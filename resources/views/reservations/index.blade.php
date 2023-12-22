@extends('layout.app')
@section('title', Route::currentRouteName() == 'reservations.index' ? 'Reservations' : 'Reservations Archive')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                @if (Route::currentRouteName() === 'reservations.index')
                                    Reservations
                                @elseif (Route::currentRouteName() === 'reservations.archive')
                                    Reservations Archive
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                All reservations
                                            </h4>
                                            @if (auth()->user()->temp_role == 'librarian')
                                                <div class="float-right">
                                                    @if (Route::currentRouteName() === 'reservations.index')
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter" id="reservation-delete-all"
                                                            disabled>
                                                            <i class="fa-solid fa-trash-can"></i>
                                                            Delete all
                                                            <span class="reservation-count"></span>
                                                        </button>
                                                    @elseif (Route::currentRouteName() === 'reservations.archive')
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter"
                                                            id="reservation-force-delete-all" disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Force delete all
                                                            <span class="reservation-count"></span>
                                                        </button>
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter" id="reservation-restore-all"
                                                            disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Restore all
                                                            <span class="reservation-count"></span>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-hover w-100"
                                                id="table-reservations">
                                                <thead>
                                                    <tr>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th></th>
                                                        @endif
                                                        <th>#</th>
                                                        <th></th>
                                                        <th>Copy barcode</th>
                                                        <th>Title</th>
                                                        <th>Check-out before</th>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th>Borrower</th>
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
                                                        <th></th>
                                                        <th>Copy barcode</th>
                                                        <th>Title</th>
                                                        <th>Check-out before</th>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th>Borrower</th>
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
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.reservations.index')
@endsection
