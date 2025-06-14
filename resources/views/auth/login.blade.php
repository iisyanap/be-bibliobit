@extends('layouts.guest')

@section('title', 'Admin Login')

@section('content')

{{-- Custom Styles for Login Page --}}
<style>
    /* Override body background dari guest layout */
    body {
        background-color: #F1F8E8 !important;
    }

    /* Custom login card styling */
    .card-login {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
    }

    /* Styling panel kiri dengan logo dan pesan selamat datang */
    .card-login-left {
        background-color: #55AD9B;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem;
    }

    .card-login-left img {
        width: 80px; /* Sesuaikan ukuran jika perlu */
        height: auto;
        margin-bottom: 1.5rem;
    }

    .card-login-left h2 {
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .card-login-left p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
    }

    /* Styling panel kanan (form) */
    .card-login-right {
        background-color: #fff;
    }

    /* Custom styling input form */
    .form-control-user {
        border-radius: 50rem !important;
        padding: 1.5rem 1rem !important;
        font-size: 0.9rem !important;
        border: 1px solid #D8EFD3;
        transition: all 0.3s;
    }
    .form-control-user:focus {
        border-color: #55AD9B;
        box-shadow: 0 0 0 0.2rem rgba(85, 173, 155, 0.25);
    }

    /* Custom styling tombol */
    .btn-user {
        border-radius: 50rem !important;
        padding: .75rem 1rem !important;
        background-color: #55AD9B !important;
        border-color: #55AD9B !important;
        font-weight: 700 !important;
        letter-spacing: .05rem;
        transition: background-color 0.3s;
    }
    .btn-user:hover {
        background-color: #357569 !important;
        border-color: #357569 !important;
    }

    /* Menyesuaikan posisi pesan error */
    .invalid-feedback {
        text-align: left;
        padding-left: 1.25rem;
        font-size: 0.8rem;
    }

</style>


<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            {{-- Menambahkan class custom untuk styling --}}
            <div class="card card-login o-hidden shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row g-0"> {{-- Menggunakan g-0 untuk menghilangkan jarak antar kolom --}}
                        {{-- Panel Kiri --}}
                        <div class="col-lg-6 d-none d-lg-flex card-login-left">
                           <img src="{{ asset('img/bibliobit_aries.png') }}" alt="Bibliobit Logo">
                           <h2>Admin Panel</h2>
                           <p class="text-center">Selamat datang kembali! Silakan masuk untuk mengelola aplikasi Bibliobit.</p>
                        </div>

                        {{-- Panel Kanan --}}
                        <div class="col-lg-6 card-login-right">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                </div>

                                <form class="user" method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Alamat Email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-user btn-block mt-4">
                                        Login
                                    </button>
                                </form>
                                <hr>
                                {{-- Link Lupa Password dan Buat Akun dihapus sesuai permintaan sebelumnya --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
