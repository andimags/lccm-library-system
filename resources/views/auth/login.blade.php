@extends('layout.app')
@section('title', 'Login')
@section('content')
    <div class="" id="login-section">
        <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
            <div class="container pb-0">
                <div class="card login-card mb-0">
                    <div class="row no-gutters">
                        <div class="col-md-7">
                            <img src="{{ asset('images/layout/laco-school.jpg') }}" alt="login" class="login-card-img">
                        </div>
                        <div class="col-md-5">
                            <div class="card-body">
                                <div class="brand-wrapper d-flex align-items-center">
                                    <img src="{{ asset('images/layout/laco-logo-icon-256.ico') }}" alt="logo"
                                        class="logo mr-3">
                                    <h2 class='font-weight-bold mb-0'>LCCM Library System</h2>
                                </div>
                                <p class="login-card-description mb-2">Sign into your account</p>
                                <form action="{{ route('login.patron') }}" method="POST">
                                    @csrf
                                    @if (session('message'))
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            {{ session('message') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                    <div
                                        class="form-group p-0 mb-3
                                            @error('id')
                                                has-error has-feedback
                                            @enderror
                                        ">
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fa fa-user"></i>
                                            </span>
                                            <input type="text" class="form-control" placeholder="Email or ID"
                                                name='id'>
                                        </div>
                                        @error('id')
                                            <small class="form-text text-muted text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                    <div
                                        class="form-group p-0 mb-3
                                        @error('password')
                                            has-error has-feedback
                                        @enderror
                                        ">
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fa fa-key"></i>
                                            </span>
                                            <input type="password" class="form-control" placeholder="Password"
                                                name='password'>
                                        </div>
                                        @error('password')
                                            <small class="form-text text-muted text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <input name="login" class="btn btn-primary btn-block mb-3 color-9 font-weight-bold"
                                        type="submit" value="Login">
                                </form>
                                <input class="btn btn-outline-primary btn-block mb-4 color-9 font-weight-bold" type="button"
                                    value="Create an Account" data-toggle="modal" data-target="#registration-modal"
                                    id="btn-create-an-account">
                                {{-- <a href="#!" class="forgot-password-link d-inline-block">Forgot password?</a> --}}
                                <nav class="login-card-footer-nav mb-3 d-flex justify-content-between">
                                    <a href="" id="btn-forgot-password" data-toggle="modal"
                                        data-target="#forgot-password-modal">Forgot
                                        password?</a>
                                    <a href="{{ route('collections.index') }}">View Collections</a>
                                </nav>
                                {{-- <p class="login-card-footer-text">Don't have an account? <a href="#!" class="text-reset">Register here</a></p> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- REGISTRATION MODAL --}}
        <div class="modal fade pr-0" id="registration-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="patron-modal-header">
                            Create an Account
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="registration-form" enctype="multipart/form-data" method="POST"
                            action="{{ route('registrations.store') }}">
                            @csrf
                            <div class="form-group pt-0 form_group_id2">
                                <label for="id2">ID</label>
                                <input type="text" class="form-control patron-input id2" name="id2" maxlength=11
                                    placeholder="ID">
                                <small class="form-text text-muted text-danger input_msg id2_msg"></small>
                            </div>
                            <div class="form-row p-2">
                                <div class="form-group col-md-6 form_group_first_name">
                                    <label for="first_name">First name</label>
                                    <input type="text" class="form-control patron-input first_name"
                                        placeholder="First name" name="first_name">
                                    <small class="form-text text-muted text-danger input_msg first_name_msg"></small>
                                </div>
                                <div class="form-group col-md-6 form_group_last_name">
                                    <label for="last_name patron-input">Last name</label>
                                    <input type="text" class="form-control last_name" placeholder="Last name"
                                        name="last_name">
                                    <small class="form-text text-muted text-danger input_msg last_name_msg"></small>
                                </div>
                            </div>
                            <div class="form-group form_group_email">
                                <label for="email">Email</label>
                                <input type="text" class="form-control patron-input email" name="email"
                                    placeholder="Email">
                                <small class="form-text text-muted text-danger input_msg email_msg"></small>
                            </div>
                            <div class="form-group form_group_role">
                                <label for="role">Role select</label>
                                <select class="form-control patron-input role" name="role">
                                    <option value="student">Student</option>
                                    <option value="employee">Employee</option>
                                    <option value="faculty">Faculty</option>
                                </select>
                                <small class="form-text text-muted text-danger input_msg role_msg"></small>
                            </div>
                            <div class="form-row p-2">
                                <div class="form-group col-md-6 form_group_password">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control password" placeholder="Password"
                                        name="password">
                                    <small class="form-text text-muted text-danger input_msg password_msg"></small>
                                </div>
                                <div class="form-group col-md-6 form_group_confirm_password">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" class="form-control confirm_password"
                                        placeholder="Confirm Password" name="confirm_password">
                                    <small class="form-text text-muted text-danger input_msg confirm_password_msg"></small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="btn-registration-submit">
                                    Create an account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORGOT PASSWORD --}}
        <div class="modal fade pr-0" id="forgot-password-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="patron-modal-header">
                            Forgot Password
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="forgot-password-form" enctype="multipart/form-data" method="POST"
                            action="{{ route('forgot.password.store') }}">
                            @csrf
                            <div class="form-group form_group_email">
                                <label for="email">Email</label>
                                <input type="text" class="form-control patron-input email" name="email"
                                    placeholder="Email">
                                <small class="form-text text-muted text-danger input_msg email_msg"></small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="btn-forgot-password-submit">
                                    Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    @include('js.auth.login')
@endsection
