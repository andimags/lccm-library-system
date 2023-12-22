<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patron;
use App\Models\ImportFailure;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ImportFailureController extends Controller
{
    public function index($id)
    {
        $importFailures = ImportFailure::where('import_id', $id)->select('id', 'import_id', 'values', 'errors')->orderBy('created_at', 'desc');

        if (Route::currentRouteName() == 'import.failures.archive') {
            $importFailures = $importFailures->onlyTrashed();
        }

        return Datatables::of($importFailures)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';

                if (Route::currentRouteName() == 'import.failures.index') {
                    $html .= $this->button('fa-solid fa-trash-can', $row->id, 'delete');
                }

                if (Route::currentRouteName() == 'import.failures.archive') {
                    $html .= $this->button('fa-solid fa-trash-can', $row->id, 'force-delete');
                }

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('values', function ($row) {
                return implode(' | ', array_filter($row->values, fn ($value) => $value !== null));
            })
            ->addColumn('errors', function ($row) {
                $errorMessageArray = [];

                foreach ($row->errors as $fieldName => $messages) {
                    foreach ($messages as $message) {
                        if (!empty($message)) {
                            $errorMessageArray[] = $message;
                        }
                    }
                }

                return implode(' | ', $errorMessageArray);
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
            })
            ->rawColumns(['action', 'checkbox', 'errors'])
            ->make(true);
    }

    private function button($icon, $id, $className)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg btn-import-failure-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '"> <i class="' . $icon . '"></i> </button>';
    }

    public function destroy(Request $request)
    {
        $message = 'Import failure';

        if (is_array($request->id)) {
            ImportFailure::whereIn('id', $request->id)->get()->each->delete();
            $message .= 's have ';
        } else {
            ImportFailure::findOrFail($request->id)->delete();
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'Import failure';

        if (is_array($request->id)) {
            ImportFailure::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            ImportFailure::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= ' has ';
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }
}
