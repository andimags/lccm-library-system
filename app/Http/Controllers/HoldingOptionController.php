<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\HoldingOption;
use Illuminate\Support\Str;

class HoldingOptionController extends Controller
{
    private function rules(Request $request, HoldingOption $holdingOption = null)
    {
        $rules = [
            'value' => ['required', 'string', Rule::unique('holding_options')->ignore($holdingOption)],
        ];

        $validator = Validator::make($request->all(), $rules);

        return $validator;
    }

    public function store(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $setting = \App\Models\Setting::where('field', $request->field)->first();

        $setting->holdingOptions()->create([
            'value' => Str::title($request->value)
        ]);

        return response()->json(['success' => 'Holding option added successfully!']);
    }

    public function destroy(Request $request)
    {
        $message = 'Holding option';

        if (is_array($request->id)) {
            HoldingOption::whereIn('id', $request->id)->get()->each->delete();
            $message .= 's have ';
        } else {
            HoldingOption::findOrFail($request->id)->delete();
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }
}
