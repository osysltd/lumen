@extends('home')
@section('content')

<section class="pt-2">
    <div class="container px-lg-5">
        @if (!$user->hasVerifiedEmail())
        <div class="row justify-content-center mt-3">
            <div class="col-lg-7">
                <div class="card text-center">
                    <div class="card-header">Verify Your Email Address</div>
                    <div class="card-body">
                        <p class="card-text">Before proceeding, please check your email for a verification link. If you did not receive the email, click the button to request another.</p>
                        <form method="POST" action="{{ route('verification.verify') }}">
                            <input type="hidden" name="_token" value="{{ Session::token() }}" />
                            <div class="text-end text-center">
                                <button class="btn btn-primary" type="submit" id="verify-button">Verify</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="container px-lg-5">
            <!-- Page Features-->
            <div class="row gx-lg-5 mb-5 justify-content-center">
                <div class="col-lg-7">
                    <div class="card shadow-lg border-0 rounded-lg mt-5">
                        <div class="card-header">
                            <h3 class="text-center font-weight-light my-4">Account Profile</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.do') }}">
                                @method('patch')
                                <input type="hidden" name="_token" value="{{ Session::token() }}" />
                                <div class="form-floating mb-3">
                                    <input class="form-control" id="inputName" name="name" type="text" placeholder="Enter your name" value="{{ $user->name }}" />
                                    <label for="inputEmail">Name</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" value="{{ $user->email }}" />
                                    <label for="inputEmail">Email Address</label>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3 mb-md-0">
                                            <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Create a password" />
                                            <label for="inputPassword">New Password</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3 mb-md-0">
                                            <input class="form-control" id="inputPasswordConfirmation" name="password_confirmation" type="password" placeholder="Confirm password" />
                                            <label for="inputPasswordConfirmation">Confirm new Password</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 mb-0 text-end">
                                    <button type="submit" class="btn btn-primary">Update Account</button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center py-3">
                            <div class="small">Please make sure your account is verified and the information is correct before updating your profile</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
