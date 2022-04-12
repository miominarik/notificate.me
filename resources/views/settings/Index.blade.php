@php
$selected_time = \Carbon\Carbon::parse($settings_data[0]->notification_time)->format('G');
@endphp

@extends('layouts.app')

@section('content')
    <section style="height: 100%">
        <div class="container-fluid py-5">
            <div class="row d-flex justify-content-center align-items-center">
                @if ((new \Jenssegers\Agent\Agent())->isDesktop())
                    <div class="col-9 col-md-6 col-lg-5 col-xl-4">
                    @elseif((new \Jenssegers\Agent\Agent())->isMobile() || (new \Jenssegers\Agent\Agent())->isTablet())
                        <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                @endif
                <div class="card shadow-2-strong" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">
                        <h3 class="mb-5">{{ __('settings.settings_header') }}</h3>
                        @if (session('status_success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('status_warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                {{ session('status_warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('status_danger'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('status_danger') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('settings.update', 1) }}" id="settings_form">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="enable_email_notif">{{ __('settings.enable_email_notif') }}</label>
                                    <select class="form-control" name="enable_email_notif" id="enable_email_notif">
                                        <option @if ($settings_data[0]->enable_email_notification == 1) selected @endif value="1">
                                            {{ __('settings.yes') }}</option>
                                        <option @if ($settings_data[0]->enable_email_notification == 0) selected @endif value="0">
                                            {{ __('settings.no') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="mt-2">
                                    <label for="notification_time">{{ __('settings.time_when_notif') }}</label>
                                    <select class="form-control" name="notification_time" id="notification_time" required>
                                        @for ($i = 00; $i <= 23; $i++)
                                            <option @if ($selected_time == $i) selected @endif
                                                value="{{ $i }}">
                                                {{ $i }}:00</option>
                                        @endfor
                                    </select>

                                    {{-- <input type="time" class="form-control" name="notification_time"
                                        id="notification_time" aria-describedby="time_when_notif_help"
                                        value="{{ $settings_data[0]->notification_time }}" step="3600" required> --}}
                                    <small id="time_when_notif_help"
                                        class="form-text text-muted">{{ __('settings.time_when_notif_help') }}</small>
                                </div>
                            </div>
                            <div class="form-group row mt-2">
                                <div class="col-3">
                                    <button type="submit" class="btn btn-primary">{{ __('settings.send_btn') }}</button>
                                </div>
                            </div>
                        </form>
                        <hr class="my-4">
                        <div class="d-grid gap-2 mb-2">
                            @if ($github_oauth_status[0]->github_id == null)
                                <a href="{{ route('oauth.github-login') }}">
                                    <button type="button" class="btn btn-block btn-social btn-github">
                                        <span class="fa-brands fa-github"></span>
                                        {{ __('auth.login_github') }}
                                    </button>
                                </a>
                            @else
                                <a href="javascript:void(0)">
                                    <button type="button" class="btn btn-block btn-social btn-github" disabled>
                                        <span class="fa-brands fa-github"></span>{{ __('auth.github_settings_setted') }}
                                    </button>
                                </a>
                            @endif
                        </div>
                        <div class="d-grid gap-2 mb-2">
                            @if ($google_oauth_status[0]->google_id == null)
                                <a href="{{ route('oauth.google-login') }}">
                                    <button type="button" class="btn btn-block btn-social btn-google">
                                        <span class="fa-brands fa-google"></span>{{ __('auth.login_google') }}
                                    </button>
                                </a>
                            @else
                                <a href="javascript:void(0)">
                                    <button type="button" class="btn btn-block btn-social btn-google" disabled>
                                        <span class="fa-brands fa-github"></span>{{ __('auth.google_settings_setted') }}
                                    </button>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
