@extends('layout.app')
@section('title', Route::currentRouteName() == 'in.house.circulations.index' ? 'In-House Circulations' : 'In-House Circulations Archive')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                @if (Route::currentRouteName() === 'in.house.circulations.index')
                                    In-House Circulations
                                @elseif (Route::currentRouteName() === 'in.house.circulations.archive')
                                    In-House Circulations Archive
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
                                                All In-House Circulations
                                            </h4>
                                            <div class="float-right">
                                                @if (Route::currentRouteName() === 'in.house.circulations.index')
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter"
                                                        id="in-house-circulation-delete-all" disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Delete all
                                                        <span class="in-house-circulation-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        id="btn-in-house-circulation-check-in">
                                                        <i class="fa fa-plus"></i>
                                                        Add in-house
                                                    </button>
                                                @elseif (Route::currentRouteName() === 'in.house.circulations.archive')
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter"
                                                        id="in-house-circulation-force-delete-all" disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Force delete all
                                                        <span class="in-house-circulation-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter"
                                                        id="in-house-circulation-restore-all" disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Restore all
                                                        <span class="in-house-circulation-count"></span>
                                                    </button>
                                                @endif

                                                {{-- <div class="btn-group dropdown">
                                                    <button class="btn btn-primary dropdown-toggle " type="button"
                                                        id="dropdownMenu1" data-toggle="dropdown">
                                                        <i class="fa fa-filter"></i>
                                                        Filter by roles
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
                                                        <a class="dropdown-item filter-item" data-val=""
                                                            href="#">Show
                                                            all</a>
                                                        <a class="dropdown-item filter-item" data-val="admin"
                                                            href="#">Admin</a>
                                                        <a class="dropdown-item filter-item" data-val="librarian"
                                                            href="#">Librarian</a>
                                                        <a class="dropdown-item filter-item" data-val="faculty"
                                                            href="#">Faculty</a>
                                                        <a class="dropdown-item filter-item" data-val="student"
                                                            href="#">Student</a>
                                                    </ul>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-hover w-100"
                                                id="table-in-house-circulations">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Barcode</th>
                                                        <th>Title</th>
                                                        <th>Librarian</th>
                                                        <th>Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th>Barcode</th>
                                                        <th>Title</th>
                                                        <th>Librarian</th>
                                                        <th>Date</th>
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
                            <input id="hidden_id" name="hidden_id" hidden>
                            <div class="form-group" id="form_group_barcode">
                                <label for="barcode">Barcode</label>
                                <input type="text" class="form-control in-house-circulation-input" id="barcode"
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

    </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.in-house-circulations.index')
@endsection
