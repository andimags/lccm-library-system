@extends('layout.app')
@section('title', 'Edit Profile')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                Profile
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
                        <div class="row">
                            <div class="col-md-6" style="max-width: 600px">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                Edit profile
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <form id="patron-form" enctype="multipart/form-data">
                                            @csrf
                                            <input type="text" id="patron-form-action" hidden>
                                            <input type="text" id="patron-hidden-id" name="old_id" hidden>
                                            <div class="form-group pt-0" id="form_group_id">
                                                <label for="id">ID</label>
                                                <input type="text" class="form-control patron-input" id="id"
                                                    name="id" maxlength=11 value="{{ $patron->id }}" disabled>
                                                <small class="form-text text-muted text-danger input_msg"
                                                    id="id_msg"></small>
                                            </div>
                                            <div class="form-group" id="form_group_first_name">
                                                <label for="first_name">First name</label>
                                                <input type="text" class="form-control patron-input" id="first_name"
                                                    name="first_name" maxlength=30 value="{{ $patron->first_name }}">
                                                <small class="form-text text-muted text-danger input_msg"
                                                    id="first_name_msg"></small>
                                            </div>
                                            <div class="form-group" id="form_group_last_name">
                                                <label for="last_name">Last name</label>
                                                <input type="text" class="form-control patron-input" id="last_name"
                                                    name="last_name" maxlength=20 value="{{ $patron->last_name }}">
                                                <small class="form-text text-muted text-danger input_msg"
                                                    id="last_name_msg"></small>
                                            </div>
                                            <div class="form-group" id="form_group_email">
                                                <label for="email">Email address</label>
                                                <input type="text" class="form-control patron-input" id="email"
                                                    name="email" value="{{ $patron->email }}">
                                                <small class="form-text text-muted text-danger input_msg"
                                                    id="email_msg"></small>
                                            </div>
                                            {{-- <div class='form-group patron-input' id="form_group_image">
                                                <label for="image">Displayed picture
                                                    <span class="badge badge-count">Optional</span></label>
                                                </label>
                                                <input type="file" class="form-control-file" id="image"
                                                    name="image">
                                                <div id="image-container"></div>
                                                <small class="form-text text-muted text-danger input_msg"
                                                    id="image_msg"></small>
                                            </div> --}}
                                            <input type="file" class="my-pond" name="filepond" multiple/>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary btn-border"
                                                    data-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit" class="btn btn-primary" id="patron-modal-button">
                                                    Save
                                                </button>
                                            </div>
                                        </form>
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
    @include('js.patrons.edit-profile')
@endsection
