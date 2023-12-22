@extends('layout.app')
@section('title', 'OTP Verification')
@section('content')
    <div class="" id="login-section">
        <main class="d-flex align-items-center min-vh-30 py-3 py-md-0">
            <div class="container pb-0" style="max-width: 500px">
                <div class="card login-card mb-0">
                    <div class="row no-gutters">
                        <div class="col-md-12">
                            <div class="card-body">
                                <div class="brand-wrapper d-flex align-items-center">
                                    <img src="{{ asset('images/layout/laco-logo-icon-256.ico') }}" alt="logo" class="logo mr-3">
                                    <h2 class='font-weight-bold mb-0'>LCCM Library System</h2>
                                </div>
                                <p class="h3 mb-3">{{ $title }}</p>
                                <p>OTP code has been sent to <strong>{{ $email }}</strong></p>
                                @if (session('message'))
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        {{ session('message') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                <form action="{{ route('otp.verification.verify') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" value="{{ $token }}" name="token" hidden>
                                    <div
                                        class="form-group p-0 mb-3
                                            @error('otp')
                                                has-error has-feedback
                                            @enderror
                                        ">
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fa-solid fa-shield-halved"></i>
                                            </span>
                                            <input type="text" class="form-control" placeholder="OTP code"
                                                name='otp'>
                                        </div>
                                        @error('otp')
                                            <small class="form-text text-muted text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                    <div class="btn-toolbar w-100 mb-3">
                                        <button class="btn btn-primary col-12"
                                            type="submit"><strong>Submit</strong></button>
                                    </div>
                                </form>
                                <form action="{{ route('otp.verification.resend') }}" method="POST">
                                    @csrf
                                    <input type="text" value="{{ $token }}" name="token" hidden>
                                    <div class="btn-toolbar w-100 mb-3">
                                        <button class="btn btn-primary btn-border col-12"><strong>Resend
                                                OTP</strong></button>
                                        <p class="text-danger mt-3">Your OTP code will expire in 5 minutes</p>
                                    </div>
                                </form>
                                <nav class="login-card-footer-nav mb-3">
                                    <a href="">Back to login</a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- CREATE AN ACCOUNT --}}
        <!-- Modal -->
        <div class="modal fade pr-0" id="create-an-account-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="patron-modal-header">
                            Create an account
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="create-an-account-form" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group pt-0" id="form_group_id2">
                                <label for="id2">ID</label>
                                <input type="text" class="form-control patron-input" id="id2" name="id2"
                                    maxlength=11 placeholder="ID">
                                <small class="form-text text-muted text-danger input_msg" id="id2_msg"></small>
                            </div>
                            <div class="form-row p-2">
                                <div class="form-group col-md-6">
                                    <label for="first_name">First name</label>
                                    <input type="text" class="form-control" id="first_name" placeholder="First name"
                                        name="first_name">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="last_name">Last name</label>
                                    <input type="text" class="form-control" id="last_name" placeholder="Last name"
                                        name="last_name">
                                </div>
                            </div>
                            <div class="form-group" id="form_group_email">
                                <label for="email">Email</label>
                                <input type="text" class="form-control patron-input" id="email" name="email"
                                    placeholder="Email">
                                <small class="form-text text-muted text-danger input_msg" id="email_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_role">
                                <label for="role">Role select</label>
                                <select class="form-control patron-input" id="role" name="role">
                                    <option value="student">Student</option>
                                    <option value="employee">Employee</option>
                                    <option value="faculty">Faculty</option>
                                </select>
                                <small class="form-text text-muted text-danger input_msg" id="role_msg"></small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="patron-modal-button">
                                    Create an account
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
