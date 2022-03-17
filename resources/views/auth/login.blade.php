@extends('layouts.app')

@section('content')
    <section style="height: 100%">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow-2-strong" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <h3 class="mb-5">{{ __('auth.login') }}</h3>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                {!! GoogleReCaptchaV3::renderField('login_id', 'verify') !!}
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
                                        placeholder="{{ __('auth.password') }}" required
                                        autocomplete="current-password" />
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Checkbox -->
                                <div class="form-check d-flex justify-content-start mb-4">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember"
                                        {{ old('remember') ? 'checked' : '' }} />
                                    <label class="form-check-label ps-2" for="remember">
                                        {{ __('auth.remember') }}
                                    </label>
                                </div>

                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-lg"
                                        type="submit">{{ __('auth.login_btn') }}</button>
                                </div>
                            </form>

                            <hr class="my-4">

                            <div class="d-grid gap-2">
                                <a class="btn btn-lg btn-primary mb-2" style="background-color: #dd4b39;"
                                    href="{{ route('register') }}">
                                    {{ __('layout.menu_register') }}
                                </a>
                                <a class="btn btn-lg btn-primary mb-2" style="background-color: #3b5998;"
                                    href="{{ route('password.request') }}">
                                    {{ __('auth.forgot_pass') }}
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
