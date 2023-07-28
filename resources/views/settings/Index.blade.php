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
                                                @if(isset($mfa_info) && $mfa_info->count() == 0)
                                                    <button type="button" class="btn btn-own-purple"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#activate_two_factorModal">{{__('settings.mfa_activate_btn')}}
                                                    </button>
                                                @endif
                                                @if(isset($mfa_info) && $mfa_info->count() > 0)
                                                    <button type="button" class="btn btn-own-purple"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#show_recocery_codesModal">{{__('settings.mfa_activated_btn')}}
                                                    </button>
                                                @endif
                                            </div>
                                        </form>
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
                                            <button type="button" class="btn btn-own-success mt-3"
                                                    onclick="Notification.requestPermission().then(function(permission) {});YR">
                                                {{__('settings.btn_allow_notif')}}
                                            </button>
                                        </div>
                                        <hr>
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                            <h3 class="mb-5">{{__('settings.ics_sources')}}</h3>
                                            <form class="row g-2 align-items-start" method="POST"
                                                  action="{{route('settings.add_ics_source')}}">
                                                @csrf
                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                                                    <input type="text" class="form-control" name="ics_name"
                                                           placeholder="{{__('settings.ics_add_name')}}">
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                                                    <input type="text" class="form-control" name="ics_url"
                                                           placeholder="{{__('settings.ics_add_url')}}">
                                                </div>
                                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
                                                    <select name="ics_notif" class="form-select">
                                                        <option value="0">{{__('settings.ics_add_notif_no')}}</option>
                                                        <option value="1">{{__('settings.ics_add_notif_yes')}}</option>
                                                    </select>
                                                </div>
                                                <div class="col-xl-1 col-lg-1 col-md-1 col-sm-12 col-12">
                                                    <button type="submit"
                                                            class="btn btn-own-primary btn-sm">{{__('settings.ics_add_submit')}}
                                                    </button>
                                                </div>
                                            </form>
                                            <div class="table-responsive">

                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">{{__('settings.ics_name')}}</th>
                                                        <th scope="col">{{__('settings.ics_url')}}</th>
                                                        <th scope="col">{{__('settings.allow_notif')}}</th>
                                                        <th scope="col">{{__('settings.ics_created_at')}}</th>
                                                        <th scope="col">{{__('settings.ics_remove')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @php($count = 1)
                                                    @forelse($ics_sources as $one_ics)
                                                        <tr>
                                                            <th scope="row">{{$count}}</th>
                                                            <td>{{$one_ics->name}}</td>
                                                            <td>{{$one_ics->ics_url}}</td>
                                                            <td>{{$one_ics->allow_notif == TRUE ? __('settings.yes') : __('settings.no')}}</td>
                                                            <td>{{Carbon::parse($one_ics->created_at)->format('d.m.Y H:i')}}</td>
                                                            <td>
                                                                <a href="{{route("settings.remove_ics_source", $one_ics->id)}}"><i
                                                                        class="fa-solid fa-trash text-danger"
                                                                        style="cursor: pointer;"></i></a></td>
                                                        </tr>
                                                        @php($count++)
                                                    @empty
                                                        <tr>
                                                            <th scope="row">{{__('settings.ics_none')}}</th>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                    @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
            </div>
        </div>
    </section>

    @include('settings.inc.change_pass_modal')
    @include('settings.inc.activate_two_factor')
    @if(isset($mfa_info) && $mfa_info->count() > 0)
        @include('settings.inc.show_mfa_recovery_codes')
    @endif
@endsection
