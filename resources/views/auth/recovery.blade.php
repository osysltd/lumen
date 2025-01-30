@extends('home')
@section('content')

<section class="pt-2">
    <div class="container px-lg-5">
        <!-- Page Features-->
        <div class="row gx-lg-5 mb-5 justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg mt-3">
                    <div class="card-header">
                        <h3 class="text-center font-weight-light my-4">Password Recovery</h3>
                    </div>
                    <div class="card-body">
                        <div class="small mb-3 text-muted">Enter your email address and we will send you a link to reset your password.</div>
                        <form method="POST" action="{{ route('recovery.do') }}">
                            <input type="hidden" name="_token" value="{{ Session::token() }}" />
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" />
                                <label for="inputEmail">Email address</label>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                <a class="small" href="{{ route('login') }}">Return to login</a>
                                <button type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <div class="small"><a href="{{ route('register') }}">Need an account? Sign up!</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
