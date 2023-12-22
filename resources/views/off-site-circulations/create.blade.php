@extends('layout.app')
@section('title', 'Check-Out')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                Check-Out
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
                        @if (session('success'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-xl-4">
                                <div class="card">
                                    <form action="{{ route('off.site.circulations.store') }}" method="POST">
                                        <div class="card-header">
                                            <h4 class="card-title">
                                                Patron information
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            @csrf
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa-solid fa-magnifying-glass"></i>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control p-0" placeholder="Search patron"
                                                    id="search-patron">
                                            </div>
                                            <input type="text" class="form-control" id="id2" name="id2"
                                                value="" hidden>
                                            <table class="table text-muted">
                                                <tbody>
                                                    <tr>
                                                        <th>ID</th>
                                                        <td id="id_td"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Name</th>
                                                        <td id="name_td"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Roles</th>
                                                        <td id="roles_td"></td>
                                                    </tr>
                                                    <tr>
                                                        <th># of on loan items</th>
                                                        <td id="total_on_loan_items_td"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="card-action pl-0">
                                                <button class="btn btn-primary" id="btn-submit" type='submit'
                                                    disabled>Submit</button>
                                                <button class="btn btn-outline-primary"
                                                    id="btn-remove-all" disabled>Remove all items</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-block justify-content-between">
                                            <h4 class="card-title">
                                                Items
                                            </h4>
                                            @if (session('transaction_error'))
                                                <div class="alert alert-primary mt-2" role="alert">
                                                    {{ session('transaction_error') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </span>
                                            </div>
                                            <input id="search-copy" type="text" class="form-control"
                                                placeholder="Search copies" disabled>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-hover"
                                                id="table-temp-check-out-items">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Barcode</th>
                                                        <th>Title</th>
                                                        <th>Availability</th>
                                                        <th>Date due</th>
                                                        <th>Grace Period Days</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Barcode</th>
                                                        <th>Title</th>
                                                        <th>Availability</th>
                                                        <th>Date due</th>
                                                        <th>Grace Period Days</th>
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
    @include('js.off-site-circulations.create')
@endsection
