<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Patron;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Avatar;
use Illuminate\Support\Facades\Storage;

class PatronController extends Controller
{
    private function rules(Request $request, Patron $patron = null)
    {
        $rules = [
            'id2' => ['required', 'numeric', Rule::unique('patrons')->ignore($patron)],
            'first_name' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'min:2', 'max:30'],
            'last_name' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'min:2', 'max:20'],
            'email' => ['required', 'email', Rule::unique('patrons')->ignore($patron)],
            'image' => ['nullable', 'sometimes', 'mimes:jpeg,png,webp,jpg', 'max:1000'],
            'groups' => [new \App\Rules\MultivaluedMax(5)],
            'roles' => ['required', new \App\Rules\MultivaluedMax(2)]
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames(['id2' => 'id']); // Replace 'id2' with 'id' in the attribute names

        return $validator;
    }

    private function button($icon, $id, $className)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg btn-patron-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '"> <i class="' . $icon . '"></i> </button>';
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            $registrationsCount = \App\Models\Patron::where('registration_status', 'pending')->whereNotNull('email_verified_at')->count();

            return view('patrons.index')->with([
                'registrationsCount' => $registrationsCount
            ]);
        }

        $patrons = Patron::with(['groups:id,group', 'roles', 'images' => function ($query) {
            $query->select('file_name', 'imageable_type', 'imageable_id')->latest('created_at')->take(1);
        }])->select('id', 'id2', 'first_name', 'last_name')
            ->where('id', '<>', auth()->user()->id)
            ->where('registration_status', 'accepted')
            ->orderBy('updated_at', 'desc');

        if (Route::currentRouteName() == 'patrons.archive') {
            $patrons = $patrons->onlyTrashed();
        }

        return Datatables::of($patrons)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action"> 
                    <a href="' . url('patrons') . '/' . $row->id . '">' . $this->button('fa fa-eye', $row->id, 'view') . '</a>';

                if (Route::currentRouteName() == 'patrons.index') {
                    $html .= $this->button('fa fa-edit', $row->id, 'edit');
                    if(!($row->checkedOutCount() > 0 || $row->totalUnpaidFines() > 0)){
                        $html .= $this->button('fa-solid fa-trash-can', $row->id, 'delete');
                    }
                } else if (Route::currentRouteName() == 'patrons.archive') {
                    $html .= $this->button('fa fa-undo', $row->id, 'restore');
                    $html .= $this->button('fa-solid fa-trash-can', $row->id, 'force-delete');
                }

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('roles', function ($row) {
                $roles = $row->roles->pluck('name')->map(function ($role) {
                    return Str::title($role);
                })->implode(', ');

                return $roles;
            })
            ->filterColumn('roles', function ($query, $keyword) {
                $query->whereHas('roles', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->addColumn('groups', function ($row) {
                return $row->groups->pluck('group')->implode(', ');
            })
            ->filterColumn('groups', function ($query, $keyword) {
                $query->whereHas('groups', function ($q) use ($keyword) {
                    $q->where('group', 'like', '%' . $keyword . '%');
                });
            })
            ->addColumn('image', function ($row) {
                $image = $row->images()->first();

                if ($image) {
                    return '<div class = "avatar avatar-sm" ><img src = "' . asset('storage/images/patrons/' . $image->file_name) . '" class = "avatar-img rounded-circle" ></div>';
                } else {
                    return '<div class = "avatar avatar-sm" ><img src = "' . (Avatar::create($row->first_name . ', ' . $row->last_name)->setFontFamily('Lato')->toBase64()) . '" class = "avatar-img rounded-circle" ></div>';
                }
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '" hidden>';
            })
            ->addColumn('name', function ($row) {
                return $row->last_name . ', ' . $row->first_name;
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('first_name', 'like', '%' . $keyword . '%')
                    ->orWhere('last_name', 'like', '%' . $keyword . '%');
            })
            ->addColumn('disabled', function ($row) {
                return $row->checkedOutCount() > 0 || $row->totalUnpaidFines() > 0 ? true : false;
            })
            ->rawColumns(['action', 'image', 'checkbox', 'groups'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $patron = Patron::create([
            'id2' => $request->id2,
            'first_name' => Str::title($request->first_name),
            'last_name' => Str::title($request->last_name),
            'email' => strtolower($request->email),
            'librarian_id' => auth()->user()->id
        ]);

        // ADD GROUPS TO A PATRON
        if (!empty($request->groups)) {
            $groups = json_decode($request->groups, true); //

            foreach ($groups as $group) {
                $model = \App\Models\Group::firstOrCreate(['group' => Str::title($group)]);
                $patron->groups()->attach($model);
            }
        }

        // ADD ROLES
        $roles = array_map(function ($role) {
            return strtolower($role);
        }, json_decode($request->roles, true));

        $patron->syncRoles($roles);

        // ADD IMAGE
        if ($request->hasFile('image')) {
            $directoryPath = 'images/patrons';

            if (!Storage::disk('public')->exists($directoryPath)) {
                Storage::disk('public')->makeDirectory($directoryPath);
            }

            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            Storage::disk('public')->putFileAs($directoryPath, $request->file('image'), $fileName);

            $patron->images()->create([
                'file_name' => $fileName
            ]);
        }

        return response()->json(['success' => 'Patron has been added successfully!']);
    }

    public function edit($id)
    {
        $patron = Patron::with(['roles'])->findOrFail($id);
        $image = $patron->images()->latest()->first();
        $groups = $patron->groups()->pluck('group')->toArray();
        $roles = $patron->roles->pluck('name')->toArray();
        $roles = array_map(function ($role) {
            return Str::title($role);
        }, $roles);

        if ($image != null) {
            $image = Storage::url('images/patrons/' . $image->file_name);
        }

        return response()->json([
            'patron' => $patron,
            'image' => $image,
            'groups' => $groups,
            'roles' => $roles
        ]);
    }

    public function update(Request $request, $id)
    {
        $patron = Patron::find($id);
        $validated = $this->rules($request, $patron);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $patron->update([
            'id2' => $request->id2,
            'first_name' => Str::title($request->first_name),
            'last_name' => Str::title($request->last_name),
            'email' => strtolower($request->email),
        ]);

        // ADD GROUPS TO A PATRON
        if (!empty($request->groups)) {
            $groups = json_decode($request->groups, true); //

            $groupIds = [];
            foreach ($groups as $group) {
                $groupModel = \App\Models\Group::firstOrCreate(['group' => Str::title($group)]);
                $groupIds[] = $groupModel->id;
            }

            $patron->groups()->sync($groupIds);
        }

        // UPDATE ROLES
        $roles = array_map(function ($role) {
            return strtolower($role);
        }, json_decode($request->roles, true));

        $patron->syncRoles($roles);

        //  ADD IMAGE
        $patron->images()->delete();

        if ($request->hasFile('image')) {
            $directoryPath = 'images/patrons';

            if (!Storage::disk('public')->exists($directoryPath)) {
                Storage::disk('public')->makeDirectory($directoryPath);
            }

            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            Storage::disk('public')->putFileAs($directoryPath, $request->file('image'), $fileName);

            $patron->images()->create([
                'file_name' => $fileName
            ]);
        }

        return response()->json(['success' => 'Patron has been updated successfully!']);
    }

    public function destroy(Request $request)
    {
        $message = 'Patron';

        if (is_array($request->id)) {
            Patron::whereIn('id', $request->id)->get()->each->delete();
            $message .= 's have ';
        } else {
            Patron::findOrFail($request->id)->delete();
            $message .= ' has ';

            if (!$request->ajax()) {
                return redirect()->route('patrons.index')->with('success', 'Patron has been successfully deleted!');
            }
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }

    public function restore(Request $request)
    {
        $message = 'Patron';

        if (is_array($request->id)) {
            Patron::onlyTrashed()->whereIn('id', $request->id)->get()->each->restore();
            $message .= 's have ';
        } else {
            Patron::onlyTrashed()->findOrFail($request->id)->restore();
            $message .= ' has ';
        }

        $message .= 'been successfully restored!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'Patron';

        if (is_array($request->id)) {
            Patron::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            Patron::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= ' has ';
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }

    public function search($id)
    {
        $patrons = \App\Models\Patron::with(['roles:name'])
            ->select('id', 'id2', 'first_name', 'last_name')
            ->where('id2', 'LIKE', $id . '%')
            ->take(50)
            ->withCount(['offSiteCirculations' => function ($query) {
                $query->whereNull('checked_in_at');
            }])
            ->get();


        return response()->json($patrons);
    }

    public function show($id)
    {
        $patron = \App\Models\Patron::withTrashed()
            ->with('groups:group', 'roles', 'librarian:id,first_name,last_name')
            ->find($id);
        $fullName = Str::title($patron->last_name . ", " . $patron->first_name);

        return view('patrons.show')->with([
            'patron' => $patron,
            'fullName' => $fullName,
            'patronStatus' => $patron->deleted_at == null ? 'active' : 'archived'
        ]);
    }

    public function checkUniqueness(Request $request)
    {
        $isUnique = true;

        if ($request->action == 'add') {
            $isUnique = !Patron::where($request->field, $request->value)
                ->where('registration_status', 'accepted')
                ->exists();
        } else if ($request->action == 'edit') {
            $currentPatron = Patron::find($request->patron_id);
            
            if ($currentPatron->{$request->field} != $request->value) {
                $isUnique = !Patron::where($request->field, $request->value)
                    ->where('registration_status', 'accepted')
                    ->where('id', '<>', $currentPatron->id)
                    ->exists();
            }
        }

        return response()->json($isUnique);
    }
}
