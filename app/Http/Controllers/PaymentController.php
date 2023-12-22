<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Payment;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    private function rules(Request $request)
    {
        $rules = [
            'message' => ['nullable', 'string'],
            'image' => ['required']
        ];

        $validator = Validator::make($request->all(), $rules);
        return $validator;
    }

    private function button($value, $id, $className)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-payment-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '">' . $value . '</button>';
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('payments.index');
        }

        $payments = Payment::with(['offSiteCirculation.borrower:id,first_name,last_name'])
            ->orderBy('created_at', 'asc')
            ->select();

        if(auth()->user()->temp_role != 'librarian'){
            $payments = $payments->where('borrower_id', auth()->user()->id);
        }

        if (Route::currentRouteName() == 'payments.archive') {
            $payments = $payments->onlyTrashed();
        }

        return Datatables::of($payments)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';
                $html .= $this->button('<i class="fa fa-eye"></i>', $row->id, 'view');

                if(auth()->user()->temp_role == 'librarian'){
                    if (Route::currentRouteName() == 'payments.index') {
                        if($row->status == 'pending'){
                            $html .= $this->button('Accept', $row->id, 'accept',);
                            $html .= $this->button('Decline', $row->id, 'decline',);    
                        }
                        $html .= $this->button('<i class="fa-solid fa-trash-can"></i>', $row->id, 'delete');
                    } else if (Route::currentRouteName() == 'payments.archive') {
                        $html .= $this->button('<i class="fa fa-undo"></i>', $row->id, 'restore');
                        $html .= $this->button('<i class="fa-solid fa-trash-can"></i>', $row->id, 'force-delete');
                    }
                }

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('status', function ($row) {
                $array = [
                    'pending' => 'count',
                    'accepted' => 'success',
                    'declined' => 'warning',
                ];

                return '<span class="badge badge-' . $array[$row->status] . '">' . $row->status . '</span>';
            })
            ->addColumn('message', function ($row) {
                return Str::limit($row->message, $limit = 30, $end = '...');
            })
            ->addColumn('remark', function ($row) {
                return Str::limit($row->remark, $limit = 30, $end = '...');
            })
            ->addColumn('borrower', function ($row) {
                return '<a href="' . route("patrons.index") . '/' . $row->borrower_id . '">' . $row->borrower->last_name . ', ' . $row->borrower->first_name . '</a>';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d g:i A');
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
            })
            ->addColumn('circulation_id', function ($row) {
                return '<a href="' . route("off.site.circulations.index") . '/' . $row->offSiteCirculation->first()->id . '">' . $row->off_site_circulation_id . '</a>';
            })
            ->rawColumns(['action', 'checkbox', 'status', 'circulation_id', 'borrower'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $paymentSubmitted = \App\Models\OffSiteCirculation::find($request->id)->payments()->where('status', 'pending')->exists();

        if($paymentSubmitted){
            return response()->json(['error' => 'Payment already submitted!']);
        }

        $payment = Payment::create([
            'message' => $request->message,
            'off_site_circulation_id' => $request->id,
            'borrower_id' => auth()->user()->id,
        ]);

        if ($request->hasFile('image')) {
            $directoryPath = 'images/payments';

            if (!Storage::disk('public')->exists($directoryPath)) {
                Storage::disk('public')->makeDirectory($directoryPath);
            }

            foreach ($request->file('image') as $image) {
                $extension = $image->getClientOriginalExtension();
                $fileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
                Storage::disk('public')->putFileAs($directoryPath, $image, $fileName);

                $payment->images()->create([
                    'file_name' => $fileName
                ]);
            }
        }

        return response()->json(['success' => 'Payment has been sent successfully!']);
    }

    public function get($id){
        $payment = Payment::with('offSiteCirculation.borrower:id,first_name,last_name')->findOrFail($id);
        $images = $payment->images()->get()->pluck('file_name')->toArray();

        $images = array_map(function ($fileName) {
            return asset('storage/images/payments/' . $fileName);
        }, $images);

        return response()->json([
            'payment' => $payment, 
            'images' => $images
        ]);
    }

    public function changeStatus(Request $request, $id){
        $payment = Payment::findOrFail($id);

        $payment->update([
            'status' => $request->status,
            'remark' => $request->remark
        ]);

        $payment->offSiteCirculation()->first()->update([
            'fines_status' => 'paid'
        ]);

        return response()->json(['success' => 'Payment has been successfully ' . ($request->status == 'accepted' ? 'accepted!' : 'declined!')]);
    }

    public function destroy(Request $request)
    {
        $message = 'Payment';

        if (is_array($request->id)) {
            Payment::whereIn('id', $request->id)->get()->each->delete();
            $message .= 's have ';
        } else {
            Payment::findOrFail($request->id)->delete();
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }

    public function restore(Request $request)
    {
        $message = 'Payment';

        if (is_array($request->id)) {
            Payment::onlyTrashed()->whereIn('id', $request->id)->get()->each->restore();
            $message .= 's have ';
        } else {
            Payment::onlyTrashed()->findOrFail($request->id)->restore();
            $message .= ' has ';
        }

        $message .= 'been successfully restored!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'Payment';

        if (is_array($request->id)) {
            Payment::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            Payment::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= ' has ';
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }
}
