<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    private function rules(Request $request)
    {
        $rules = [
            'title' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'min:2', 'max:30'],
            'content' => ['nullable', 'string'],
            'visibility' => ['required', 'string', 'in:all,librarian,faculty,employee,student'],
            'start_at' => ['required', 'date', 'after_or_equal:today'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
        ];


        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames(['id2' => 'id']); // Replace 'id2' with 'id' in the attribute names

        return $validator;
    }

    private function button($icon, $id, $className)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg btn-announcement-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '"> <i class="' . $icon . '"></i> </button>';
    }


    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('announcements.index');
        }

        $announcements = \App\Models\Announcement::with(['images:file_name,imageable_id,imageable_type', 'librarian:id,first_name,last_name'])
            ->select('id', 'title', 'content', 'start_at', 'end_at', 'librarian_id', 'visibility')
            ->orderBy('created_at', 'desc');

        if (Route::currentRouteName() == 'announcements.archive') {
            $announcements = $announcements->onlyTrashed();
        }

        return Datatables::of($announcements)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';
                $html = $this->button('fa fa-eye', $row->id, 'view');

                if (Route::currentRouteName() == 'announcements.index') {
                    $html .= $this->button('fa fa-edit', $row->id, 'edit');
                    $html .= $this->button('fa-solid fa-trash-can', $row->id, 'delete');
                } else if (Route::currentRouteName() == 'announcements.archive') {
                    $html .= $this->button('fa fa-undo', $row->id, 'restore');
                    $html .= $this->button('fa-solid fa-trash-can', $row->id, 'force-delete');
                }

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
            })
            ->addColumn('librarian', function ($row) {
                return '<a href="' . route("patrons.index") . '/' . $row->librarian_id . '">' . $row->librarian->last_name . ', ' . $row->librarian->first_name . '</a>';
            })
            ->filterColumn('librarian', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('librarian', function ($subquery) use ($keyword) {
                        $subquery->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%');
                    });
                });
            })
            ->addColumn('end_at', function ($row) {
                return $row->end_at->format('Y-m-d');
            })
            ->filterColumn('end_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(end_at, '%Y-%m-%d %h:%i %p') like ?", ["%$keyword%"]);
            })
            ->addColumn('start_at', function ($row) {
                return $row->start_at->format('Y-m-d');
            })
            ->filterColumn('start_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(start_at, '%Y-%m-%d %h:%i %p') like ?", ["%$keyword%"]);
            })
            ->addColumn('visibility', function ($row) {
                return Str::title($row->visibility);
            })
            ->filterColumn('visibility', function ($query, $keyword) {
                $query->where('visibility', 'like', '%' . $keyword . '%');
            })
            ->rawColumns(['action', 'checkbox', 'librarian'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $announcement = Announcement::create([
            'title' => Str::title($request->title),
            'content' => $request->content,
            'visibility' => $request->visibility,
            'librarian_id' => auth()->user()->id,
            'start_at' => Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay()->toDateTimeString(),
            'end_at' => Carbon::createFromFormat('Y-m-d', $request->end_at)->startOfDay()->toDateTimeString(),
        ]);

        if ($request->hasFile('image')) {
            $directoryPath = 'images/announcements';

            if (!Storage::disk('public')->exists($directoryPath)) {
                Storage::disk('public')->makeDirectory($directoryPath);
            }

            foreach ($request->file('image') as $image) {
                $extension = $image->getClientOriginalExtension();
                $fileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
                Storage::disk('public')->putFileAs($directoryPath, $image, $fileName);

                $announcement->images()->create([
                    'file_name' => $fileName
                ]);
            }
        }

        return response()->json(['success' => 'Announcement has been added successfully!']);
    }

    public function edit($id)
    {
        $announcement = Announcement::with(['images:file_name,imageable_id,imageable_type'])->withTrashed()->find($id);
        $startAt = $announcement->start_at->format('Y-m-d');
        $endAt = $announcement->end_at->format('Y-m-d');

        $images = $announcement->images->pluck('file_name')->toArray();

        $images = array_map(function ($fileName) {
            return asset('storage/images/announcements/' . $fileName);
        }, $images);

        return response()->json([
            'announcement' => $announcement,
            'images' => $images,
            'start_at' => $startAt,
            'end_at' => $endAt
        ]);
    }

    public function update(Request $request, $id)
    {
        // return response()->json('laravel: ' . $request->file('image')[0]->getClientOriginalExtension());

        $announcement = Announcement::find($id);

        $validated = $this->rules($request, $announcement);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $announcement->update([
            'title' => Str::title($request->title),
            'content' => $request->content,
            'visibility' => $request->visibility,
            'librarian_id' => auth()->user()->id,
            'start_at' => Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay()->toDateTimeString(),
            'end_at' => Carbon::createFromFormat('Y-m-d', $request->end_at)->startOfDay()->toDateTimeString(),
        ]);

        $announcement->images()->each(function ($image) {
            $image->delete();
        });

        if ($request->hasFile('image')) {
            $directoryPath = 'images/announcements';

            if (!Storage::disk('public')->exists($directoryPath)) {
                Storage::disk('public')->makeDirectory($directoryPath);
            }

            foreach ($request->file('image') as $image) {
                $extension = $image->getClientOriginalExtension();
                $fileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
                Storage::disk('public')->putFileAs($directoryPath, $image, $fileName);

                $announcement->images()->create([
                    'file_name' => $fileName
                ]);
            }
        }

        return response()->json(['success' => 'Announcement has been updated successfully!']);
    }

    public function destroy(Request $request)
    {
        $message = 'Announcement';

        if (is_array($request->id)) {
            Announcement::whereIn('id', $request->id)->get()->each->delete();
            $message .= 's have ';
        } else {
            Announcement::findOrFail($request->id)->delete();
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }


    public function restore(Request $request)
    {
        $message = 'Announcement';

        if (is_array($request->id)) {
            Announcement::onlyTrashed()->whereIn('id', $request->id)->get()->each->restore();
            $message .= 's have ';
        } else {
            Announcement::onlyTrashed()->findOrFail($request->id)->restore();
            $message .= ' has ';
        }

        $message .= 'been successfully restored!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'Announcement';

        if (is_array($request->id)) {
            Announcement::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            Announcement::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= ' has ';
        }

        $message .= 'been successfully deleted permanently!';
        return response()->json(['success' => $message]);
    }
}
