<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Models\ShelfItem;
use Illuminate\Support\Facades\DB;


class ShelfItemController extends Controller
{
    public function index(Request $request)
    {
        $shelfItems = ShelfItem::where('borrower_id', auth()->user()->id)
            ->with(['copy:id,barcode,availability,collection_id', 'copy.collection:id,title', 'copy.collection.images:id,file_name']);

        function button($buttonName, $icon, $id)
        {
            return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg btn-shelf-item-' . $buttonName . '" data-original-title="Edit Task" data-id="' . $id . '">' . $icon . '</button>';
        }

        if ($request->ajax()) {
            return Datatables::of($shelfItems)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<td> <div class="form-button-action">';

                    $html .= button('remove', '<i class="fa-solid fa-xmark"></i>', $row->id);

                    $html .= '</div> </td>';

                    return $html;
                })
                ->addColumn('image', function ($row) {
                    $image = $row->copy()->first()->collection()->first()->images()->latest()->first();
                    $file_name = $image->file_name ?? 'default.jpg';

                    return '<div class="avatar avatar-xl"><img src="' . asset('/images/collections/' . $file_name) . '" class="avatar-img rounded"></div>';
                })
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
                })
                ->addColumn('availability', function ($row) {
                    return '<span class="badge badge-' . ($row->copy->availability == 'available' ? 'success' : 'warning') . '">' .  $row->copy->availability . '</span>';
                })
                ->addColumn('barcode', function ($row) {
                    return $row->copy()->first()->barcode;
                })
                ->addColumn('title', function ($row) {
                    return $row->copy()->first()->collection()->first()->title;
                })
                ->rawColumns(['action', 'image', 'checkbox', 'availability', 'barcode', 'title'])
                ->make(true);
        }
        return view('shelf-items.index');
    }

    public function store(Request $request)
    {
        $shelfItem = ShelfItem::where('borrower_id', auth()->user()->id)
            ->where('copy_id', $request->id)->first();

        // IF COPY IS NOT YET ADDED ON SHELF ITEMS
        if($shelfItem == null){
            ShelfItem::create([
                'borrower_id' => auth()->user()->id,
                'copy_id' => $request->id
            ]);

            return response()->json(['success' => 'Copy added to your shelf items!']);
        }
    }

    public function updateCopy(Request $request, $copy)
    {
        $copy = str_replace(",", "", $copy);
        $shelfItem = ShelfItem::findOrFail($request->id);
        $availableCopy = $shelfItem->collection()->first()->available_copy;

        if ($availableCopy >= $copy) {
            if ($copy <= 0) {
                $shelfItem->copy = 1;
            } else {
                $shelfItem->copy = $copy;
            }
            $shelfItem->save();
        } else {
            $shelfItem->copy = $availableCopy;
            $shelfItem->save();
            return response()->json(['available_copy' => $availableCopy]);
        }
    }

    public function destroy(Request $request)
    {
        if (is_array($request->id)) {
            ShelfItem::whereIn('id', $request->id)->get()->each->delete();
            // return response()->json(['success' => 'ShelfItem ' . count($request->id) == 1 ? 'item has ' : 'items have ' . 'been deleted successfully!']);
        } else {
            ShelfItem::findOrFail($request->id)->delete();
            // return response()->json(['success' => 'Shelf item has been deleted successfully!']);
        }
    }
}
