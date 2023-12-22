@extends('layout.app')
@section('title', 'Import Information')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                Import Information
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
                                    <a href="{{ route('imports.index') }}">Imports</a>
                                </li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                            </h4>
                                            <div class="float-right">
                                                @if ($import->deleted_at == null)
                                                    <div class="float-right">
                                                        <button class="btn btn-primary" id="btn-import-delete"
                                                            value="delete">
                                                            <i class="fa-solid fa-trash-can"></i> Delete
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="float-right">
                                                        <button class="btn btn-primary " id="btn-import-force-delete">
                                                            <i class="fa fa-trash"></i>
                                                            Force delete
                                                        </button>
                                                        <button class="btn btn-primary" id="btn-import-restore">
                                                            <i class="fa fa-trash"></i>
                                                            Restore
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-condensed table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>Import #</td>
                                                    <td><b>{{ $import->id }}</b></td>
                                                    <td>Table</td>
                                                    <td><b>{{ $import->table }}</b></td>
                                                </tr>
                                                <tr>
                                                    <td>Success count</td>
                                                    <td><b>{{ $import->success_count }}</b></td>
                                                    <td>Failed count</td>
                                                    <td><b>{{ $import->failed_count }}</b></td>
                                                </tr>
                                                <tr>
                                                    <td>Librarian name</td>
                                                    <td><strong><a
                                                                href="{{ route('patrons.show', ['id' => $import->librarian->id]) }}">{{ $import->librarian->last_name . ', ' . $import->librarian->first_name }}</a></strong>
                                                    </td>
                                                    <td>Created at</td>
                                                    <td><b>{{ $import->created_at->format('F j, Y g:i A') }}</b></td>
                                                </tr>
                                                @if ($import->deleted_at)
                                                    <tr>
                                                        <td>Deleted at</td>
                                                        <td class="text-danger">
                                                            <b>{{ $import->deleted_at->format('F j, Y g:i A') }}</b>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                <b class="">Import Failures: Records That Failed to Insert</b>
                                            </h4>
                                            @if (!$import->deleted_at)
                                                <div class="float-right">
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="import-failure-delete-all"
                                                        disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Delete all
                                                        <span class="import-failure-count"></span>
                                                    </button>
                                                </div>
                                            @else
                                                <button class="btn btn-primary " data-toggle="modal"
                                                    data-target="#exampleModalCenter" id="import-failure-force-delete-all" disabled>
                                                    <i class="fa fa-trash"></i>
                                                    Force delete all
                                                    <span class="import-failure-count"></span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-hover w-100 dataTable"
                                                id="table-import-failures">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>Values</th>
                                                        <th>Errors</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>Values</th>
                                                        <th>Errors</th>
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
    @include('js.imports.show')
@endsection
