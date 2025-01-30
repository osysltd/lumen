@extends('home')
@section('content')

<section class="pt-2">
    <div class="container px-lg-5">
        <!-- Page Features-->
        <div class="row gx-lg-5 mb-5 justify-content-center">
            <div class="col-lg-4">
                <div class="card shadow-lg border-0 rounded-lg mt-3">
                    <div class="card-header">
                        <h3 class="text-center font-weight-light my-4">Login</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login.do') }}">
                            <input type="hidden" name="_token" value="{{ Session::token() }}" />
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" />
                                <label for="inputEmail">Email address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" />
                                <label for="inputPassword">Password</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" id="inputRememberPassword" type="checkbox" value="" />
                                <label class="form-check-label" for="inputRememberPassword">Remember Me</label>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                <a class="small" href="{{ route('recovery') }}">Forgot Password?</a>
                                <button type="submit" class="btn btn-primary">Login</button>
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
