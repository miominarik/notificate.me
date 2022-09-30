@php
    use Carbon\Carbon;
    $selected_time = Carbon::parse($settings_data[0]->notification_time)->format('G');
@endphp

@extends('layouts.app')

@section('content')
    <section style="height: 100%">
        <div class="container-fluid py-5">
            <div class="row d-flex justify-content-center align-items-center">
                @if ((new \Jenssegers\Agent\Agent())->isDesktop())
                    <div class="col-12 col-md-12 col-lg-12 col-xl-9">
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


                                        <form method="POST" action="{{ route('settings.update', 1) }}"
                                              id="settings_form" class="row row-cols-lg-auto g-3 align-items-center">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                                <label
                                                    for="enable_email_notif">{{ __('settings.enable_email_notif') }}</label>
                                                <select class="form-select" name="enable_email_notif"
                                                        id="enable_email_notif">
                                                    <option
                                                        @if ($settings_data[0]->enable_email_notification == 1) selected
                                                        @endif value="1">
                                                        {{ __('settings.yes') }}</option>
                                                    <option
                                                        @if ($settings_data[0]->enable_email_notification == 0) selected
                                                        @endif value="0">
                                                        {{ __('settings.no') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                                <label
                                                    for="enable_email_notif">{{__('settings.mobile_num')}}</label>
                                                <input type="tel"
                                                       pattern="^(?:421|\(?\+421\)?\s?|421\s?)[1-79](?:[\.\-\s]?\d\d){4}$"
                                                       id="mobile_number" name="mobile_number"
                                                       class="form-control" placeholder="+421910123456"
                                                       value="{{$settings_data[0]->mobile_number}}">
                                            </div>

                                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                                <label
                                                    for="notification_time">{{ __('settings.time_when_notif') }}</label>
                                                <select class="form-select" name="notification_time"
                                                        id="notification_time" required>
                                                    @for ($i = 00; $i <= 23; $i++)
                                                        <option @if ($selected_time == $i) selected @endif
                                                        value="{{ $i }}">
                                                            {{ $i }}:00
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                                <label
                                                    for="language">{{ __('layout.menu_lang') }}</label>
                                                <select class="form-select" name="language"
                                                        id="language" required>
                                                    <option value="en"
                                                            @if (session('locale') == 'en') selected @endif>{{ __('layout.lang_english') }}</option>
                                                    <option value="sk"
                                                            @if (session('locale') == 'sk') selected @endif>{{ __('layout.lang_slovak') }}</option>
                                                </select>
                                            </div>
                                            @if($activated_modules->module_calendar)
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <label
                                                        for="public_ics_url">{{__('settings.public_ics_url')}}</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" id="public_ics_url" class="form-control"
                                                               value="{{env('APP_URL')}}/api/ics/public/{{$calendar_hash}}"
                                                               aria-label="ICS URL" aria-describedby="copy_btn_ics_url"
                                                               readonly>
                                                        <button class="btn btn-outline-secondary" type="button"
                                                                id="copy_btn_ics_url" onclick="CopyIcsUrl();">
                                                            {{__('settings.copy_btn')}}</button>
                                                    </div>

                                                </div>
                                            @endif
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <button type="submit"
                                                        class="btn btn-own-primary">{{ __('settings.send_btn') }}</button>
                                                <button type="button" class="btn btn-own-danger" data-bs-toggle="modal"
                                                        data-bs-target="#changepassModal">{{__('settings.change_pass_btn')}}
                                                </button>
                                            </div>
                                        </form>
                                        <hr class="my-4">
                                        <div class="d-grid gap-2 mb-2">
                                            @if ($apple_oauth_status[0]->apple_id == null)
                                                <a href="javascript:void(0)">
                                                    <button type="button"
                                                            disabled
                                                            class="btn btn-block btn-social"
                                                            style="background-color: #050708; color: white; width: 100%;">
                                                        <span class="fa-brands fa-apple"></span>
                                                        {{ __('auth.login_apple') }}
                                                    </button>
                                                </a>
                                            @else
                                                <a href="javascript:void(0)">
                                                    <button type="button" class="btn btn-block btn-social"
                                                            disabled
                                                            style="background-color: #050708; color: white; width: 100%;">
                                                        <span
                                                            class="fa-brands fa-apple"></span>{{ __('auth.apple_settings_setted') }}
                                                    </button>
                                                </a>
                                            @endif
                                            @if ($microsoft_oauth_status[0]->microsoft_id == null)
                                                <a href="{{ route('oauth.microsoft-login') }}">
                                                    <button type="button"
                                                            class="btn btn-block btn-social btn-microsoft"
                                                            style="width: 100%">
                                                        <span class="fa-brands fa-microsoft"></span>
                                                        {{ __('auth.login_microsoft') }}
                                                    </button>
                                                </a>
                                            @else
                                                <a href="javascript:void(0)">
                                                    <button type="button" class="btn btn-block btn-social btn-microsoft"
                                                            disabled style="width: 100%">
                                                        <span
                                                            class="fa-brands fa-microsoft"></span>{{ __('auth.microsoft_settings_setted') }}
                                                    </button>
                                                </a>
                                            @endif
                                        </div>
                                        <div class="d-grid gap-2 mb-2">
                                            @if ($google_oauth_status[0]->google_id == null)
                                                <a href="{{ route('oauth.google-login') }}">
                                                    <button type="button" class="btn btn-block btn-social btn-google"
                                                            style="width: 100%">
                                                        <span
                                                            class="fa-brands fa-google"></span>{{ __('auth.login_google') }}
                                                    </button>
                                                </a>
                                            @else
                                                <a href="javascript:void(0)">
                                                    <button type="button" class="btn btn-block btn-social btn-google"
                                                            disabled style="width: 100%">
                                                        <span
                                                            class="fa-brands fa-github"></span>{{ __('auth.google_settings_setted') }}
                                                    </button>
                                                </a>
                                            @endif
                                        </div>
                                        <hr>
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                            <h3 class="mb-5">{{__('settings.my_devices')}}</h3>
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">{{__('settings.device_name')}}</th>
                                                    <th scope="col">{{__('settings.device_last_used')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php($count = 1)
                                                @forelse($my_devices as $one_device)
                                                    <tr>
                                                        <th scope="row">{{$count}}</th>
                                                        <td>{{$one_device->device_model}}</td>
                                                        <td>{{Carbon::parse($one_device->updated_at)->format('d.m.Y H:i')}}</td>
                                                    </tr>
                                                    @php($count++)
                                                @empty
                                                    <tr>
                                                        <th scope="row">{{__('settings.device_noone')}}</th>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-own-secondary mt-3"
                                                    onclick="window.location.replace('{{route('settings.disconnect_all_devices')}}');">
                                                {{__('settings.device_disconnect_all')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
            </div>
        </div>
    </section>

    @include('settings.inc.change_pass_modal')
    @if(isset($qr_code) && $qr_code != NULL)
        @include('settings.inc.activate_two_factor')
    @endif
    @if(isset($recovery_codes) && $recovery_codes != NULL)
        @include('settings.inc.show_mfa_recovery_codes')
    @endif
@endsection
