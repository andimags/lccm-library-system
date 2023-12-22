@extends('layout.app')
@section('title', Route::currentRouteName() == 'profile' ? 'Profile' : $patron->last_name . ', ' . $patron->first_name)
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                @if (Route::currentRouteName() == 'profile')
                                    Profile
                                @else
                                    Patron Information
                                @endif
                            </h4>
                            @if (Route::currentRouteName() == 'patrons.show')
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
                            @endif
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="col-md-6" style="max-width: 600px">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                Details
                                            </h4>
                                            @if ($patron->deleted_at == null)
                                                <div class="float-right">
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="btn-patron-edit"
                                                        value="edit">
                                                        <i class="fa fa-edit"></i>
                                                        {{ Route::currentRouteName() == 'profile' ? 'Edit Profile' : 'Edit' }}
                                                    </button>
                                                    @if ($patron->id != auth()->user()->id)
                                                        @if (!($patron->checkedOutCount() > 0 || $patron->totalUnpaidFines() > 0))
                                                            <button class="btn btn-primary " data-toggle="modal"
                                                                data-target="#exampleModalCenter" id="btn-patron-delete"
                                                                value="delete">
                                                                <i class="fa-solid fa-trash-can"></i> Delete
                                                            </button>
                                                        @endif
                                                    @else
                                                        <button class="btn btn-primary " data-toggle="modal"
                                                            data-target="#change-password-modal" id="btn-change-password">
                                                            <i class="fa-solid fa-lock"></i> Change Password
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="float-right">
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="btn-patron-force-delete">
                                                        <i class="fa fa-trash"></i>
                                                        Force delete
                                                    </button>
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="btn-patron-restore">
                                                        <i class="fa fa-trash"></i>
                                                        Restore
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <livewire:patron-details :patron="$patron" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- EDIT PROFILE/UPDATE PATRON MODAL -->
        <div class="modal fade pr-0" id="patron-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="patron-modal-header">
                            @if (Route::currentRouteName() == 'profile')
                                Edit Profile
                            @else
                                Update Patron
                            @endif
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="patron-form" enctype="multipart/form-data">
                            @csrf
                            {{-- <input type="text" id="patron-form-action">
                            <input type="text" id="patron-hidden-id" name="id"> --}}
                            <div class="form-group pt-0" id="form_group_id2">
                                <label for="id2">ID</label>
                                <input type="text" class="form-control patron-input" id="id2" name="id2"
                                    maxlength=11 {{ auth()->user()->temp_role != 'librarian' ? 'readonly' : '' }}>
                                <small class="form-text text-muted text-danger input_msg" id="id2_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_first_name">
                                <label for="first_name">First name</label>
                                <input type="text" class="form-control patron-input" id="first_name" name="first_name"
                                    maxlength=30>
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
                                    name="roles" placeholder=""
                                    {{ Route::currentRouteName() == 'profile' ? 'disabled' : '' }}>
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
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHANGE PASSWORD MODAL -->
        <div class="modal fade pr-0" id="change-password-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="change-password-modal-header">
                            Change Password
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="change-password-form" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group pt-0" id="form_group_old_password">
                                <label for="old_password">Old password</label>
                                <input type="password" class="form-control patron-input" id="old_password"
                                    name="old_password">
                                <small class="form-text text-muted text-danger input_msg" id="old_password_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_new_password">
                                <label for="new_password">New password</label>
                                <input type="password" class="form-control patron-input" id="new_password"
                                    name="new_password" maxlength=30>
                                <small class="form-text text-muted text-danger input_msg" id="new_password_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_confirm_new_password">
                                <label for="confirm_new_password">Confirm new password</label>
                                <input type="password" class="form-control patron-input" id="confirm_new_password"
                                    name="confirm_new_password" maxlength=30>
                                <small class="form-text text-muted text-danger input_msg"
                                    id="confirm_new_password_msg"></small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="btn-change-password-submit">
                                    Save password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.patrons.show')
@endsection
