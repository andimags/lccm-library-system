<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoaningPeriod;
use Spatie\Permission\Models\Role;

class LoaningPeriodController extends Controller
{
    public function store(Request $request)
    {
        $role = Role::findByName($request->role);
        $holdingOption = \App\Models\HoldingOption::where('value', $request->prefix)->first();

        $loaningPeriod = LoaningPeriod::where('holding_option_id', $holdingOption->id)
            ->where('role_id', $role->id)
            ->first();
        
        $field = $request->field; //no_of_days or grace_period_days

        if ($loaningPeriod) {
            $loaningPeriod->update([
                $field => $request->input,
            ]);
        }
        else{
            LoaningPeriod::create([
                'holding_option_id' => $holdingOption->id,
                'role_id' => $role->id,
                $field => $request->input,
            ]);
        }
    }
}
