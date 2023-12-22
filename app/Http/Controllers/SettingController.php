<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index($field = null)
    {
        if ($field == null) {
            $prefixes = \App\Models\Setting::where('field', 'prefix')->first()->holdingOptions()->pluck('value')->toArray();

            return view('settings.index')->with([
                'prefixes' => $prefixes
            ]);
        }

        $holdingOptions = Setting::where('field', $field)->first()->holdingOptions();

        return Datatables::of($holdingOptions)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';

                $html .= $this->button('fa-solid fa-xmark', $row->id, 'delete');

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
            })
            ->rawColumns(['action', 'checkbox'])
            ->make(true);
    }

    private function button($icon, $id, $className)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg btn-holding-option-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '"> <i class="' . $icon . '"></i> </button>';
    }

    public function getHoldingOptions(Request $request)
    {
        $holdingOptions = [];

        foreach($request->fields as $field){
            $setting = Setting::where('field', $field)->first()->holdingOptions();
            $holdingOptions[$field] = $setting->pluck('value')->toArray();
        }

        return response()->json($holdingOptions);
    }

    public function toggleEnableAutomaticFines($value){
        $enableAutomaticFines = \App\Models\Setting::where('field', 'enable_automatic_fines')->first();
        $enableAutomaticFines->update([
            'value' => $value
        ]);
    }
}
