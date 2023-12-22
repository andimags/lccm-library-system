@extends('layout.app')

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
                                <p class="h3 mb-3">Change Password</p>
                                @if (session('message'))
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        {{ session('message') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                <form action="{{ route('forgot.password.change') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" value="{{ $token }}" name="token" hidden>
                                    <div
                                        class="form-group p-0 mb-3
                                        @error('new_password')
                                            has-error has-feedback
                                        @enderror
                                        ">
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fa fa-key"></i>
                                            </span>
                                            <input type="password" class="form-control" placeholder="New Password"
                                                name='new_password'>
                                        </div>
                                        @error('new_password')
                                            <small class="form-text text-muted text-danger">{{ $message }}</small>
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
                                            <input type="password" class="form-control" placeholder="Confirm Password"
                                                name='confirm_password'>
                                        </div>
                                        @error('confirm_password')
                                            <small class="form-text text-muted text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="btn-toolbar w-100 mb-3">
                                        <button class="btn btn-primary col-12" type="submit"><strong>Save New
                                                Password</strong></button>
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
    </div>

    {{-- SCRIPT --}}
    @include('js.auth.login')
@endsection
