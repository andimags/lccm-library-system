@extends('layout.app')
@section('title', 'Registrations')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'registrations.index')
                                    Registrations
                                @else
                                    Registrations archive
                                @endif
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
                                    <a href="{{ route('patrons.index') }}">Patrons</a>
                                </li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                All registrations
                                            </h4>
                                            <div class="float-right">
                                                @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'registrations.index')
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="registration-decline-all" disabled>
                                                        <i class="fa-solid fa-x"></i>
                                                         Decline all
                                                        <span class="registration-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="registration-accept-all" disabled>
                                                        <i class="fa-solid fa-check"></i>
                                                         Accept all
                                                        <span class="registration-count"></span>
                                                    </button>
                                                @else
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="registration-force-delete-all"
                                                        disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Force delete all
                                                        <span class="registration-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="registration-restore-all" disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Restore all
                                                        <span class="registration-count"></span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-hover w-100" id="table-registrations">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>ID</th>
                                                        <th>Last name</th>
                                                        <th>First name</th>
                                                        <th>Role</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>ID</th>
                                                        <th>Last name</th>
                                                        <th>First name</th>
                                                        <th>Role</th>
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

        <!-- Modal -->
        <div class="modal fade pr-0" id="registration-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="registration-modal-header">

                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="registration-form" enctype="multipart/form-data">
                            @csrf
                            <input type="text" id="registration-form-action">
                            <input type="text" id="registration-hidden-id" name="id">
                            <div class="form-group pt-0" id="form_group_id2">
                                <label for="id2">ID</label>
                                <input type="text" class="form-control registration-input" id="id2" name="id2"
                                    maxlength=11>
                                <small class="form-text text-muted text-danger input_msg" id="id2_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_first_name">
                                <label for="first_name">First name</label>
                                <input type="text" class="form-control registration-input" id="first_name"
                                    name="first_name" maxlength=30>
                                <small class="form-text text-muted text-danger input_msg" id="first_name_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_last_name">
                                <label for="last_name">Last name</label>
                                <input type="text" class="form-control registration-input" id="last_name" name="last_name"
                                    maxlength=20>
                                <small class="form-text text-muted text-danger input_msg" id="last_name_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_email">
                                <label for="email">Email address</label>
                                <input type="text" class="form-control registration-input" id="email" name="email">
                                <small class="form-text text-muted text-danger input_msg" id="email_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_groups">
                                <label for="groups">Groups</label>
                                <input type="text" class="form-control registration-input p-0" id="groups"
                                    name="groups" placeholder="">
                                <small class="form-text text-muted text-danger input_msg" id="groups_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_roles">
                                <label for="roles">Roles</label>
                                <input type="text" class="form-control registration-input p-0" id="roles"
                                    name="roles" placeholder="">
                                <small class="form-text text-muted text-danger input_msg" id="roles_msg"></small>
                            </div>
                            <div class='form-group registration-input' id="form_group_image">
                                <label for="image">Displayed picture
                                    <span class="badge badge-count">Optional</span></label>
                                </label>
                                <input type="file" class="form-control-file" id="image" name="image">
                                <div id="image-container"></div>
                                <small class="form-text text-muted text-danger input_msg" id="image_msg"></small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="registration-modal-button">

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.registrations.index')
@endsection
