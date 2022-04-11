@extends('layouts.app')

@section('content')
    <section style="height: 100%">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow-2-strong" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <h3 class="mb-5">{{ __('auth.register') }}</h3>
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                {!! GoogleReCaptchaV3::renderField('register_id', 'verify') !!}
                                <div class="form-outline mb-4">
                                    <input type="text" id="name" name="name"
                                        class="form-control form-control-lg @error('name') is-invalid @enderror"
                                        placeholder="{{ __('auth.name') }}" value="{{ old('name') }}" required
                                        autocomplete="name" />
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-outline mb-4">
                                    <input type="email" id="email" name="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        placeholder="{{ __('auth.email') }}" value="{{ old('email') }}" required
                                        autocomplete="email" />
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-outline mb-4">
                                    <input type="password" id="password" name="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        placeholder="{{ __('auth.password') }}" required autocomplete="new-password" />
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-outline mb-4">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control form-control-lg"
                                        placeholder="{{ __('auth.password_second') }}" required
                                        autocomplete="new-password" />
                                </div>

                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-lg"
                                        type="submit">{{ __('auth.register_btn') }}</button>
                                </div>
                            </form>

                            <div class="d-flex justify-content-between bd-highlight mb-3 mt-2">
                                <div class="p-2 bd-highlight"><a
                                        href="{{ route('login') }}">{{ __('layout.menu_login') }}</a></div>
                                <div class="p-2 bd-highlight"></div>
                                <div class="p-2 bd-highlight"><a
                                        href="{{ route('password.request') }}">{{ __('auth.forgot_pass') }}</a></div>
                            </div>

                            <hr class="my-4">
                            <div class="d-grid gap-2 mb-2">
                                <a href="{{route('oauth.github-login')}}" class="btn btn-block btn-social btn-github">
                                    <span class="fa-brands fa-github"></span> {{ __('auth.register_github') }}
                                </a>
                            </div>
                            <div class="d-grid gap-2 mb-2">
                                <a href="{{route('oauth.google-login')}}" class="btn btn-block btn-social btn-google">
                                    <span class="fa-brands fa-google"></span> {{ __('auth.register_google') }}
                                </a>
                            </div>
                            <small>
                                This site is protected by reCAPTCHA and the Google
                                <a href="https://policies.google.com/privacy">Privacy Policy</a> and
                                <a href="https://policies.google.com/terms">Terms of Service</a> apply.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {!! GoogleReCaptchaV3::init() !!}
@endsection
