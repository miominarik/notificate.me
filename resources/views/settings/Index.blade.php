@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('status_success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status_success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('status_warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('status_warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('status_danger'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('status_danger') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">{{ __('settings.settings_header') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.update', 1) }}" id="settings_form">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <div class="col-lg-4 col-md-12 col-sm-12 col-12">
                                    <label for="enable_email_notif">{{ __('settings.enable_email_notif') }}</label>
                                    <select class="form-control" name="enable_email_notif" id="enable_email_notif"
                                        required>
                                        <option value="1" @if ($settings_data[0]->enable_email_notification == 1) selected @endif>
                                            {{ __('settings.yes') }}</option>
                                        <option value="0" @if ($settings_data[0]->enable_email_notification == 0) selected @endif>
                                            {{ __('settings.no') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-4 col-md-12 col-sm-12 col-12 mt-2">
                                    <label for="notification_time">{{ __('settings.time_when_notif') }}</label>
                                    <input type="time" class="form-control" name="notification_time"
                                        id="notification_time" aria-describedby="time_when_notif_help"
                                        value="{{ $settings_data[0]->notification_time }}" step="3600" required>
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
                    </div>
                </div>
            </div>
        </div>
    @endsection
