@extends('layout.app')

@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                Reservation #{{ $id }}
                            </h4>
                            <ul class="breadcrumbs">
                                <li class="nav-home">
                                    <a href="#">
                                        <i class="flaticon-home"></i>
                                    </a>
                                </li>
                                <li class="separator">
                                    <i class="flaticon-right-arrow"></i>
                                </li>
                                <li class="nav-item">
                                    <a href="#">Tables</a>
                                </li>
                                <li class="separator">
                                    <i class="flaticon-right-arrow"></i>
                                </li>
                                <li class="nav-item">
                                    <a href="#">Datatables</a>
                                </li>
                            </ul>
                        </div>
                        @if (session('reservation_message'))
                            <div class="alert alert-primary mt-2" role="alert">
                                {{ session('reservation_message') }}
                            </div>
                        @endif
                        <livewire:reservation-information :reservation="$reservation"/>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <ul class="nav nav-primary mb-3 nav-line" id="pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="pills-items-tab-nobd" data-toggle="pill"
                                                    href="#pills-items-nobd" role="tab" aria-controls="pills-items-nobd"
                                                    aria-selected="true">Items</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-status-history-tab-nobd" data-toggle="pill"
                                                    href="#pills-status-history-nobd" role="tab"
                                                    aria-controls="pills-status-history-nobd" aria-selected="false">Status
                                                    history</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content mb-3" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-items-nobd" role="tabpanel"
                                                aria-labelledby="pills-items-tab-nobd">
                                                <table class="display table table-striped table-hover w-100"
                                                    id="table-items">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th></th>
                                                            <th>ID</th>
                                                            <th>Name</th>
                                                            <th>Quantity</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th>#</th>
                                                            <th></th>
                                                            <th>ID</th>
                                                            <th>Name</th>
                                                            <th>Quantity</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </tfoot>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="pills-status-history-nobd" role="tabpanel"
                                                aria-labelledby="pills-status-history-tab-nobd">
                                                <table class="display table table-striped table-hover w-100"
                                                    id="table-statuses">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Status</th>
                                                            <th>Reason</th>
                                                            <th>Date</th>
                                                            <th>Librarian</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Status</th>
                                                            <th>Reason</th>
                                                            <th>Date</th>
                                                            <th>Librarian</th>
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

        {{-- ACCEPT RESERVATION ITEMS MODAL --}}
        <div class="modal fade pr-0" id="accept-reservation-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="accept-reservation-modal-header">
                            Reservation #
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="" id="accept-reservation-modal-id">
                        <table class="table table-hover" id="accept-reservation-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>
                            </tfoot>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="items-accept-all">
                            Accept all
                            <span class="accept-reservation-count"></span>
                        </button>
                        <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.reservations.show')
@endsection
