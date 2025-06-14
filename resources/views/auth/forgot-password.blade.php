@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-6 col-lg-8 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                {{-- Nested Row within Card Body --}}
                <div class="row">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Forgot Your Password?</h1>
                                <p class="mb-4">We get it, stuff happens. Just enter your email address below
                                    and we'll send you a link to reset your password!</p>
                            </div>
                            {{-- Form ini perlu disesuaikan action dan methodnya sesuai route Laravel Auth Anda --}}
                            <form class="user" method="POST" action="{{-- route('password.email') --}}">
                                @csrf
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user"
                                        name="email"
                                        placeholder="Enter Email Address...">
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Reset Password
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ url('/register') }}">Create an Account!</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="{{ url('/login') }}">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
