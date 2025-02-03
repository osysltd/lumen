@extends('home')
@section('content')

<div class="row justify-content-center mt-5">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Verify Your Email Address</div>
            <div class="card-body">
                Before proceeding, please check your email for a verification link. If you did not receive the email,
                <form class="d-inline" method="POST" action="{{ route('verification.verify') }}">
                    <input type="hidden" name="_token" value="{{ Session::token() }}" />
                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">click here to request another</button>.
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
