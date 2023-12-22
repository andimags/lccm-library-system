@extends('layout.app')
@section('title', 'Login As')
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
                                    <img src="{{ asset('images/layout/laco-logo-icon-256.ico') }}" alt="logo" class="logo mr-3">
                                    <h2 class='font-weight-bold mb-0'>LCCM Library System</h2>
                                </div>
                                <p class="login-card-description mb-2">Login as...</p>
                                <form action="{{ route('select.temp.role') }}" method="POST">
                                    @csrf
                                    @if(session('message'))                                        
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            {{ session('message') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                    <div class="form-group px-0">
                                        <div class="selectgroup w-100">
                                            @foreach ($roles as $key => $role)
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="temp_role" value="{{ $role }}"
                                                        class="selectgroup-input"
                                                        @if ($key === 0) checked @endif>
                                                    <span class="selectgroup-button">{{ Str::title($role) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <input name="continue" class="btn btn-primary btn-block mb-4 color-9 font-weight-bold"
                                        type="submit" value="Continue">

                                </form>
                                <nav class="login-card-footer-nav">
                                    <a href="#!">Terms of use.</a>
                                    <a href="#!">Privacy policy</a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="card login-card">
                          <img src="assets/images/login.jpg" alt="login" class="login-card-img">
                          <div class="card-body">
                            <h2 class="login-card-title">Login</h2>
                            <p class="login-card-description">Sign in to your account to continue.</p>
                            <form action="#!">
                              <div class="form-group">
                                <label for="email" class="sr-only">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                              </div>
                              <div class="form-group">
                                <label for="password" class="sr-only">Password</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                              </div>
                              <div class="form-prompt-wrapper">
                                <div class="custom-control custom-checkbox login-card-check-box">
                                  <input type="checkbox" class="custom-control-input" id="customCheck1">
                                  <label class="custom-control-label" for="customCheck1">Remember me</label>
                                </div>
                                <a href="#!" class="text-reset">Forgot password?</a>
                              </div>
                              <input name="login" id="login" class="btn btn-block login-btn mb-4" type="button" value="Login">
                            </form>
                            <p class="login-card-footer-text">Don't have an account? <a href="#!" class="text-reset">Register here</a></p>
                          </div>
                        </div> -->
            </div>
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
@endsection
