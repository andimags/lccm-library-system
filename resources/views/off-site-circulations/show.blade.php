@extends('layout.app')
@section('title', 'Off-Site Circulation Information')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                Off-Site Circulation Information
                            </h4>
                            <ul class="breadcrumbs">
                                <li class="nav-home">
                                    <a href="{{ route('dashboard.index') }}">
                                        <i class="flaticon-home"></i>
                                    </a>
                                </li>
                                <li class="separator">
                                    <i class="flaticon-right-arrow"></i>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('off.site.circulations.index') }}">Off-Site Circulations</a>
                                </li>
                            </ul>
                        </div>
                        @if (auth()->user()->temp_role == 'librarian')
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                You can only edit the fines' status if the circulation's status is <strong>'Checked-In'</strong> or
                                marked as
                                <strong>'Lost'</strong>.
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
                        <livewire:off-site-circulation-details :offSiteCirculation="$offSiteCirculation" />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <ul class="nav nav-primary mb-3 nav-line" id="pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="pills-fines-tab-nobd" data-toggle="pill"
                                                    href="#pills-fines-nobd" role="tab" aria-controls="pills-fines-nobd"
                                                    aria-selected="true">Fines</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-renewals-tab-nobd" data-toggle="pill"
                                                    href="#pills-renewals-nobd" role="tab"
                                                    aria-controls="pills-renewals-nobd" aria-selected="false">Renewals</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content mb-3" id="pills-items-tabContent">
                                            <div class="tab-pane fade show active" id="pills-fines-nobd" role="tabpanel"
                                                aria-labelledby="pills-fines-tab-nobd">
                                                <div class="card-header pr-3 pt-0">
                                                    <div class="d-flex justify-content-between">
                                                        <h4 class="card-title">

                                                        </h4>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                        <div class="float-right">
                                                            @if ($offSiteCirculationStatus == 'active')
                                                                <button class="btn btn-primary " data-toggle="modal"
                                                                    data-target="#exampleModalCenter"
                                                                    id="off-site-circulation-fine-delete-all" disabled>
                                                                    <i class="fa fa-trash"></i>
                                                                    Delete all
                                                                    <span class="off-site-circulation-fine-count"></span>
                                                                </button>
                                                                <button class="btn btn-primary " data-toggle="modal"
                                                                    data-target="#exampleModalCenter"
                                                                    id="off-site-circulation-fine-add">
                                                                    <i class="fa fa-plus"></i>
                                                                    Add fine
                                                                </button>
                                                            @else
                                                                <button class="btn btn-primary " data-toggle="modal"
                                                                    data-target="#exampleModalCenter"
                                                                    id="off-site-circulation-fine-force-delete-all"
                                                                    disabled>
                                                                    <i class="fa fa-trash"></i>
                                                                    Force delete all
                                                                    <span class="off-site-circulation-fine-count"></span>
                                                                </button>
                                                            @endif
                                                        </div>                                                        @endif
                                                    </div>
                                                </div>
                                                <table class="display table table-striped table-hover w-100"
                                                    id="table-off-site-circulation-fines">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>#</th>
                                                            <th>Reason</th>
                                                            <th>Note</th>
                                                            <th>Price</th>
                                                            <th>Librarian</th>
                                                            <th>Created At</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th></th>
                                                            <th>#</th>
                                                            <th>Reason</th>
                                                            <th>Note</th>
                                                            <th>Price</th>
                                                            <th>Librarian</th>
                                                            <th>Created At</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </tfoot>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="pills-renewals-nobd" role="tabpanel"
                                                aria-labelledby="pills-renewals-tab-nobd">
                                                <div class="table-responsive">
                                                    <table class="display table table-striped table-hover w-100"
                                                        id="table-renewals">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>New date due</th>
                                                                <th>Old date due</th>
                                                                <th>Librarian</th>
                                                                <th>Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tfoot>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>New date due</th>
                                                                <th>Old date due</th>
                                                                <th>Librarian</th>
                                                                <th>Date</th>
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
            </div>
        </div>


        <!-- FINES MODAL -->
        <div class="modal fade pr-0" id="off-site-circulation-fine-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="off-site-circulation-fine-modal-header">

                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="off-site-circulation-fine-form" enctype="multipart/form-data">
                            @csrf
                            <input type="text" id="off-site-circulation-fine-hidden-id" name="hidden_id" hidden>
                            <input type="text" id="off-site-circulation-fine-form-action" hidden>
                            <div class="form-group" id="form_group_reason">
                                <label for="reason">Reason</label>
                                <input type="text" class="form-control off-site-circulation-fine-input" id="reason"
                                    name="reason" />
                                <small class="form-text text-muted text-danger input_msg" id="reason_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_note">
                                <label for="note">Note</label>
                                <textarea type="text" class="form-control off-site-circulation-fines-input" id="note" name="note"></textarea>
                                <small class="form-text text-muted text-danger input_msg" id="note_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_price">
                                <label for="price">Price</label>
                                <input type="text" class="form-control off-site-circulation-fine-input" id="price"
                                name="price"/>
                                <small class="form-text text-muted text-danger input_msg" id="price_msg"></small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary"
                                    id="off-site-circulation-fine-modal-button">

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
                                    name="new_due_at" min="{{ $offSiteCirculation->due_at->addDay()->format('Y-m-d') }}"
                                    value="{{ $offSiteCirculation->due_at->addDay()->format('Y-m-d') }}" />
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
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.off-site-circulations.show')
@endsection
