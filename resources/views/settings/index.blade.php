@extends('layout.app')
@section('title', 'Settings')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                Settings
                            </h4>
                            <ul class="breadcrumbs">
                                <li class="nav-home">
                                    <a href="{{ route('dashboard.index') }}">
                                        <i class="flaticon-home"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <ul class="nav nav-primary mb-3 nav-line" id="pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="pills-information-tab-nobd"
                                                    data-toggle="pill" href="#pills-information-nobd" role="tab"
                                                    aria-controls="pills-information-nobd" aria-selected="true">Holding
                                                    Options</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-loan-periods-history-tab-nobd"
                                                    data-toggle="pill" href="#pills-loan-periods-history-nobd"
                                                    role="tab" aria-controls="pills-loan-periods-history-nobd"
                                                    aria-selected="false">Loan Periods</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-grace-periods-history-tab-nobd"
                                                    data-toggle="pill" href="#pills-grace-periods-history-nobd"
                                                    role="tab" aria-controls="pills-grace-periods-history-nobd"
                                                    aria-selected="false">Grace Periods</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content mb-3" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-information-nobd"
                                                role="tabpanel" aria-labelledby="pills-information-tab-nobd">
                                                <div class="d-flex justify-content-between mb-3">
                                                    <h4 class="card-title">

                                                    </h4>
                                                    <div class="float-right">
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter" id="holding-option-delete-all"
                                                            disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Delete all
                                                            <span class="holding-option-count"></span>
                                                        </button>
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter" id="holding-option-add">
                                                            <i class="fa fa-plus"></i>
                                                            Add value
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-2">
                                                    <label for="field">Select Field</label>
                                                    <select class="form-control input-square" id="field">
                                                        <option value="location">Location</option>
                                                        <option value="format">Format</option>
                                                        <option value="vendor">Vendor</option>
                                                        <option value="fund">Fund</option>
                                                        <option value="prefix">Prefix</option>
                                                        <option value="group">Group</option>
                                                        <option value="cutter">Cutter</option>
                                                    </select>
                                                </div>
                                                <table class="display table table-striped table-hover w-100"
                                                    id="table-holding-options">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>#</th>
                                                            <th>Value</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th></th>
                                                            <th>#</th>
                                                            <th>Value</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </tfoot>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="pills-loan-periods-history-nobd" role="tabpanel"
                                                aria-labelledby="pills-loan-periods-history-tab-nobd">
                                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                                    Please be informed that library materials with a loan period set to
                                                    <strong>0 days</strong> are designated for room use only. These items
                                                    should
                                                    not be taken outside the library premises.
                                                    <button type="button" class="close" data-dismiss="alert"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col"></th>
                                                            <th scope="col">Student</th>
                                                            <th scope="col">Faculty</th>
                                                            <th scope="col">Employee</th>
                                                            <th scope="col">Librarian</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <form action="">
                                                            @foreach ($prefixes as $prefix)
                                                                <tr>
                                                                    <th>{{ $prefix }}</th>
                                                                    @php
                                                                        $roles = ['Student', 'Faculty', 'Employee', 'Librarian'];
                                                                    @endphp
                                                                    @for ($i = 0; $i <= 3; $i++)
                                                                        @php
                                                                            $holdingOption = \App\Models\HoldingOption::where('value', $prefix)->first();
                                                                            $role = \Spatie\Permission\Models\Role::findByName($roles[$i]);
                                                                            $loaningPeriod = \App\Models\LoaningPeriod::where('role_id', $role->id)
                                                                                ->where('holding_option_id', $holdingOption->id)
                                                                                ->first();

                                                                            $no_of_days = $loaningPeriod ? $loaningPeriod->no_of_days : 1;
                                                                        @endphp
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <div class="input-group mb-3">
                                                                                    <input type="text"
                                                                                        class="form-control loaning-period-input"
                                                                                        value="{{ $no_of_days }}"
                                                                                        name="{{ strtolower($roles[$i]) . '_' . strtolower($prefix) }}">
                                                                                    <div class="input-group-append">
                                                                                        <span class="input-group-text"
                                                                                            id="basic-addon2">days</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            @if ($no_of_days == 0)
                                                                                <span class="text-muted">Room Use
                                                                                    Only</span>
                                                                            @endif
                                                                        </td>
                                                                    @endfor
                                                                </tr>
                                                            @endforeach
                                                        </form>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="pills-grace-periods-history-nobd" role="tabpanel"
                                                aria-labelledby="pills-grace-periods-history-tab-nobd">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col"></th>
                                                            <th scope="col">Student</th>
                                                            <th scope="col">Faculty</th>
                                                            <th scope="col">Employee</th>
                                                            <th scope="col">Librarian</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <form action="">
                                                            @foreach ($prefixes as $prefix)
                                                                <tr>
                                                                    <th>{{ $prefix }}</th>
                                                                    @php
                                                                        $roles = ['Student', 'Faculty', 'Employee', 'Librarian'];
                                                                    @endphp
                                                                    @for ($i = 0; $i <= 3; $i++)
                                                                        @php
                                                                            $holdingOption = \App\Models\HoldingOption::where('value', $prefix)->first();
                                                                            $role = \Spatie\Permission\Models\Role::findByName($roles[$i]);
                                                                            $loaningPeriod = \App\Models\LoaningPeriod::where('role_id', $role->id)
                                                                                ->where('holding_option_id', $holdingOption->id)
                                                                                ->first();

                                                                            $grace_period_days = $loaningPeriod ? $loaningPeriod->grace_period_days : 0;
                                                                        @endphp
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <div class="input-group mb-3">
                                                                                    <input type="text"
                                                                                        class="form-control grace-period-input"
                                                                                        value="{{ $grace_period_days }}"
                                                                                        name="{{ strtolower($roles[$i]) . '_' . strtolower($prefix) . '_value' }}">
                                                                                    <div class="input-group-append">
                                                                                        <span class="input-group-text"
                                                                                            id="basic-addon2">days</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            @if ($no_of_days == 0)
                                                                                <span class="text-muted">Room Use
                                                                                    Only</span>
                                                                            @endif
                                                                        </td>
                                                                    @endfor
                                                                </tr>
                                                            @endforeach
                                                        </form>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- HOLDING OPTIONS MODAL -->
        <div class="modal fade pr-0" id="holding-option-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="holding-option-modal-header">

                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="holding-option-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="holding-option-form-action">
                            <input type="hidden" id="holding-option-hidden-id" name="old_id">
                            <div class="form-group" id="form_group_value">
                                <label for="value">Value</label>
                                <input type="text" class="form-control holding-option-input" id="value"
                                    name="value">
                                <small class="form-text text-muted text-danger input_msg" id="value_msg"></small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="holding-option-modal-button">

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.settings.index')
@endsection
