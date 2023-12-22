@extends('layout.app')
@section('title', Route::currentRouteName() == 'payments.index' ? 'Payments' : 'Payments Archive')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'payments.index')
                                    Payments
                                @else
                                    Payments Archive
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                All payments
                                            </h4>
                                            <div class="float-right">
                                                @if (auth()->user()->temp_role == 'librarian')
                                                    @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'payments.index')
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter" id="payment-delete-all"
                                                            disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Delete all
                                                            <span class="payment-count"></span>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter" id="payment-force-delete-all"
                                                            disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Force delete all
                                                            <span class="payment-count"></span>
                                                        </button>
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter" id="payment-restore-all"
                                                            disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Restore all
                                                            <span class="payment-count"></span>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-hover w-100"
                                                id="table-payments">
                                                <thead>
                                                    <tr>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th></th>
                                                        @endif
                                                        <th>#</th>
                                                        <th>Circulation ID</th>

                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th>Message</th>
                                                            <th>Borrower</th>
                                                        @else
                                                            <th>Remark</th>
                                                        @endif
                                                        <th>Status</th>
                                                        <th>Created At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th></th>
                                                        @endif
                                                        <th>#</th>
                                                        <th>Circulation ID</th>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th>Message</th>
                                                            <th>Borrower</th>
                                                        @else
                                                            <th>Remark</th>
                                                        @endif
                                                        <th>Status</th>
                                                        <th>Created At</th>
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

        <!-- PAYMENT MODAL -->
        <div class="modal fade pr-0" id="payment-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="payment-modal-header">
                            Payment #
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="payment-form" enctype="multipart/form-data">
                            <div class="form-group pt-0" id="form_group_message">
                                <label for="message">Message</label>
                                <textarea type="text" class="form-control collection-input" id="message" name="message" rows="4" disabled></textarea>
                                <small class="form-text text-muted text-danger input_msg" id="message_msg"></small>
                            </div>
                            <div class="form-group pt-0" id="form_group_remark">
                                <label for="remark">Remark</label>
                                <textarea type="text" class="form-control collection-input" id="remark" name="remark" rows="4" disabled></textarea>
                                <small class="form-text text-muted text-danger input_msg" id="remark_msg"></small>
                            </div>
                            <div id="payment-view-images"></div>
                    </div>
                    <div class="modal-footer">
                        {{-- @if (auth()->user()->temp_role == 'librarian')
                            <button type="button" class="btn btn-primary btn-border" id="payment-modal-decline">
                                Decline
                            </button>
                            <button type="button" class="btn btn-primary" id="payment-modal-accept">
                                Accept
                            </button>
                        @else
                            <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                Close
                            </button>
                        @endif --}}
                        <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.payments.index')
@endsection
