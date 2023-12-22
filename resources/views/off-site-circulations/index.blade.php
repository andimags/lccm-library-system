@extends('layout.app')
@section('title',
    Route::currentRouteName() == 'off.site.circulations.index'
    ? 'Off-Site Circulations'
    : 'Off-Site
    Circulations Archive')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                @if (Route::currentRouteName() === 'off.site.circulations.index')
                                    Off-Site Circulations
                                @elseif (Route::currentRouteName() === 'off.site.circulations.archive')
                                    Off-Site Circulations Archive
                                @endif
                            </h4>
                            <ul class="breadcrumbs">
                                <li class="nav-home">
                                    <a href="{{ route('dashboard.index') }}">
                                        <i class="flaticon-home"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @if (session('message'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if (auth()->user()->temp_role != 'librarian')
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                You can only send payments when the circulation's status is <strong>'Checked-In'</strong> or marked as <strong>'Lost'</strong>.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            If the circulation's status is <strong>'Checked-In'</strong> or
                            marked as
                            <strong>'Lost'</strong>, automatic overdue penalty will be discontinued.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                All Off-Site Circulations
                                            </h4>
                                            <div class="float-right">
                                                @if (auth()->user()->temp_role == 'librarian')
                                                    @if (Route::currentRouteName() === 'off.site.circulations.index')
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter"
                                                            id="off-site-circulation-delete-all" disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Delete all
                                                            <span class="off-site-circulation-count"></span>
                                                        </button>
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            id="btn-off-site-circulation-create">
                                                            <i class="fa fa-minus"></i>
                                                            Check-out
                                                        </button>
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            id="btn-off-site-circulation-check-in">
                                                            <i class="fa fa-plus"></i>
                                                            Check-in
                                                        </button>
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            id="btn-off-site-circulation-renew">
                                                            <i class="fa-solid fa-calendar-days"></i>
                                                            Renew
                                                        </button>
                                                    @elseif (Route::currentRouteName() === 'off.site.circulations.archive')
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter"
                                                            id="off-site-circulation-force-delete-all" disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Force delete all
                                                            <span class="off-site-circulation-count"></span>
                                                        </button>
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter"
                                                            id="off-site-circulation-restore-all" disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Restore all
                                                            <span class="off-site-circulation-count"></span>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if (auth()->user()->temp_role == 'librarian')
                                            <div class="ml-4">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="enable-automatic-fines"
                                                    {{ $enableAutomaticFines == 'yes' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable-automatic-fines">
                                                    Enable automatic fines <span
                                                        class="text-muted font-italic">(automatically fine overdue
                                                        circulations)</span>
                                                </label>
                                            </div>
                                        @endif
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-hover w-100 nowrap"
                                                id="table-off-site-circulations">
                                                <thead>
                                                    <tr>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th></th>
                                                        @endif
                                                        <th>Barcode</th>
                                                        <th>Checked-Out</th>
                                                        <th>Checked-In</th>
                                                        <th>Date Due</th>
                                                        <th>Grace Period Days</th>
                                                        <th>Total Fines</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th></th>
                                                        @endif
                                                        <th>Barcode</th>
                                                        <th>Checked-Out</th>
                                                        <th>Checked-In</th>
                                                        <th>Date Due</th>
                                                        <th>Grace Period Days</th>
                                                        <th>Total Fines</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>

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


        <!-- BARCODE MODAL -->
        <div class="modal fade pr-0" id="barcode-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="barcode-modal-header">

                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="barcode-form" enctype="multipart/form-data">
                            @csrf
                            <input type="text" id="barcode_form_action" hidden>
                            <div class="form-group" id="form_group_barcode">
                                <label for="barcode">Barcode</label>
                                <input type="text" class="form-control off-site-circulation-input" id="barcode"
                                    name="barcode" />
                                <small class="form-text text-muted text-danger input_msg" id="barcode_msg"></small>
                            </div>
                            <div class="alert alert-primary" role="alert" id="alert_msg" hidden>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="barcode-modal-button">

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- RENEWAL MODAL --}}
        <div class="modal fade pr-0" id="circulation-renewal-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="circulation-renewal-modal-header">
                            Circulation Renewal
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="circulation-renewal-form" enctype="multipart/form-data">
                            @csrf
                            <input id="hidden_barcode" name="hidden_barcode" hidden>
                            <div class="form-group" id="form_group_new_due_at">
                                <label for="new_due_at">New date due</label>
                                <input type="date" class="form-control off-site-circulation-input" id="new_due_at"
                                    name="new_due_at" min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" />
                                <small class="form-text text-muted text-danger input_msg" id="new_due_at_msg"></small>
                            </div>
                            <div class="alert alert-primary" role="alert" id="alert_msg" hidden>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="off-site-circulation-modal-button">
                                    Renew
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- PAYMENT MODAL -->
        <div class="modal fade pr-0" id="payment-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="payment-modal-header">
                            Send Payment
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="payment-form" enctype="multipart/form-data">
                            @csrf
                            <input type="text" id="off-site-circulation-hidden-id" name="id" hidden>

                            <div class="form-group pt-0" id="form_group_message">
                                <label for="message">Message</label>
                                <textarea type="text" class="form-control collection-input" id="message" name="message" rows="4"></textarea>
                                <small class="form-text text-muted text-danger input_msg" id="message_msg"></small>
                            </div>

                            <div class='form-group' id="form_group_image">
                                <label for="image">Image
                                    <span class="badge badge-count">Max: 3</span></label>
                                </label>
                                <input type="file" class="form-control-file" id="image" name="image">
                                <small class="form-text text-muted text-danger input_msg" id="image_msg"></small>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary" id="payment-modal-button">
                            Send Payment
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.off-site-circulations.index')
@endsection
