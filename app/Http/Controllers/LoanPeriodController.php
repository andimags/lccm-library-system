<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoaningPeriod;
use Spatie\Permission\Models\Role;

class LoanPeriodController extends Controller
{
    public function store(Request $request){
        // UNIT, VALUE, ROLE
        $role = Role::findByName($request->role);
        $holdingOption = \App\Models\HoldingOption::where('value', 'prefix')->first();
        
        $loaningPeriod = LoaningPeriod::firstOrCreate([
            'holding_option_id' => $holdingOption,
            'role_id' => $role->id
        ]);

        $loaningPeriod->update([
            'unit' => $request->unit,
            'value' => $request->value
        ]);
    }
}
