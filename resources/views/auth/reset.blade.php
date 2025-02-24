@extends('home')
@section('content')

<section class="pt-2">
    <div class="container px-lg-5">
        <!-- Page Features-->
        <div class="row gx-lg-5 mb-5 justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header">
                        <h3 class="text-center font-weight-light my-4">Password Reset</h3>
                    </div>
                    <div class="card-body">
                        <div class="small mb-3 text-muted">Enter your new password.</div>
                        <form method="POST" action="{{ route('password.request') }}">
                            <input type="hidden" name="_token" value="{{ Session::token() }}" />
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}" />

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Create a password" required/>
                                        <label for="inputPassword">New Password</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputPasswordConfirmation" name="password_confirmation" type="password" placeholder="Confirm password" required/>
                                        <label for="inputPasswordConfirmation">Confirm new Password</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                <a class="small" href="{{ route('login') }}">Return to Login</a>
                                <button type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </form>
                    </div>
                </div>



                <div class="card-footer text-center py-3">
                    <div class="small"><a href="{{ route('register') }}">Need an Account? Sign Up!</a></div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

@endsection
