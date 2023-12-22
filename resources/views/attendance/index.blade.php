@extends('layout.app')
@section('title', Route::currentRouteName() == 'attendance.index' ? 'Attendance' : 'Attendance Archive')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                @if (Route::currentRouteName() == 'attendance.index')
                                    Attendance
                                @else
                                    Attendance Archive
                                @endif
                            </h4>
                            <ul class="breadcrumbs">
                                <li class="nav-home">
                                    <a href="{{ route('dashboard.index') }}">
                                        <i class="flaticon-home"></i>
                                    </a>
                                </li>
                                @if (Route::currentRouteName() != 'attendance.archive' && auth()->user()->temp_role == 'librarian')
                                    <li class="separator">
                                        <i class="flaticon-right-arrow"></i>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('patrons.index') }}">Patrons</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="row">
                            @if (Route::currentRouteName() == 'attendance.index' && auth()->user()->temp_role == 'librarian')
                                <div class="col-md-12">
                                    <div style="max-width: 800px" class="m-auto">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    ID
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="Enter ID"
                                                id="id">
                                        </div>
                                        <div class="alert alert-primary" role="alert">
                                            <div class="row m-l-0 m-r-0">
                                                <div class="col-sm-4 bg-c-lite-green user-profile">
                                                    <div class="card-block text-center text-white">
                                                        <div class="m-b-25">
                                                            <div class="avatar avatar-xxl">
                                                                <img src="{{ asset('/storage/images/patrons/guest.jpg') }}" alt="User image" class="avatar-img rounded-circle" id="displayed_picture"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="card-block">
                                                        <h6 class="m-b-20 p-b-5 b-b-default font-weight-bold">Information
                                                        </h6>
                                                        <div class="row">
                                                            <table class="table table-condensed">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="height: 40px"><strong>ID</strong></td>
                                                                        <td style="height: 40px" id="td_id"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="height: 40px"><strong>Full Name</strong>
                                                                        </td>
                                                                        <td style="height: 40px" id="td_full_name"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="height: 40px"><strong>Roles</strong></td>
                                                                        <td style="height: 40px" id="td_roles"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="height: 40px"><strong>Time-in</strong>
                                                                        </td>
                                                                        <td style="height: 40px" id="td_created_at"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="hide-attendance-records">
                                            <label class="form-check-label" for="hide-attendance-records">
                                                Hide attendance records
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-12" id="attendance-records">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                All Attendance
                                            </h4>
                                            <div class="float-right">
                                                @if (auth()->user()->temp_role == 'librarian')
                                                    @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'attendance.index')
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter" id="attendance-delete-all"
                                                            disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Delete all
                                                            <span class="attendance-count"></span>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter"
                                                            id="attendance-force-delete-all" disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Force delete all
                                                            <span class="attendance-count"></span>
                                                        </button>
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#exampleModalCenter" id="attendance-restore-all"
                                                            disabled>
                                                            <i class="fa fa-trash"></i>
                                                            Restore all
                                                            <span class="attendance-count"></span>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-hover w-100 nowrap"
                                                id="table-attendance">
                                                <thead>
                                                    <tr>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th></th>
                                                        @endif
                                                        <th>#</th>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th></th>
                                                            <th>ID</th>
                                                            <th>Full Name</th>
                                                            <th>Roles</th>
                                                        @endif
                                                        <th>Time-in</th>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th>Action</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th></th>
                                                        @endif
                                                        <th>#</th>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th></th>
                                                            <th>ID</th>
                                                            <th>Full Name</th>
                                                            <th>Roles</th>
                                                        @endif
                                                        <th>Time-in</th>
                                                        @if (auth()->user()->temp_role == 'librarian')
                                                            <th>Action</th>
                                                        @endif
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
        <div class="modal fade pr-0" id="patron-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="patron-modal-header">

                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="patron-form" enctype="multipart/form-data">
                            @csrf
                            <input type="text" id="patron-form-action" hidden>
                            <input type="text" id="patron-hidden-id" name="id" hidden>
                            <div class="form-group pt-0" id="form_group_id2">
                                <label for="id2">ID</label>
                                <input type="text" class="form-control patron-input" id="id2" name="id2"
                                    maxlength=11>
                                <small class="form-text text-muted text-danger input_msg" id="id2_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_first_name">
                                <label for="first_name">First name</label>
                                <input type="text" class="form-control patron-input" id="first_name"
                                    name="first_name" maxlength=30>
                                <small class="form-text text-muted text-danger input_msg" id="first_name_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_last_name">
                                <label for="last_name">Last name</label>
                                <input type="text" class="form-control patron-input" id="last_name" name="last_name"
                                    maxlength=20>
                                <small class="form-text text-muted text-danger input_msg" id="last_name_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_email">
                                <label for="email">Email address</label>
                                <input type="text" class="form-control patron-input" id="email" name="email">
                                <small class="form-text text-muted text-danger input_msg" id="email_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_groups">
                                <label for="groups">Groups</label>
                                <input type="text" class="form-control patron-input p-0" id="groups"
                                    name="groups" placeholder="">
                                <small class="form-text text-muted text-danger input_msg" id="groups_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_roles">
                                <label for="roles">Roles</label>
                                <input type="text" class="form-control patron-input p-0" id="roles"
                                    name="roles" placeholder="">
                                <small class="form-text text-muted text-danger input_msg" id="roles_msg"></small>
                            </div>
                            <div class='form-group patron-input' id="form_group_image">
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
                                <button type="submit" class="btn btn-primary" id="patron-modal-button">

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.attendance.index')
@endsection
