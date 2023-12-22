<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->temp_role == 'librarian') {
            return $next($request);
        }

        $paymentId = $request->route('id');
        $payment = \App\Models\Payment::find($paymentId);

        if ($payment && ($payment->borrower_id == auth()->user()->id)) {
            return $next($request);
        }

        return redirect()->route('dashboard.index');
    }
}
