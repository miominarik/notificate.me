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
                                        <h3 class="mb-5">{{__('modules.modules')}}</h3>
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
                                        <div class="card card-sub" style="width: 100%;">
                                            <div class="card-body card-sub-body">
                                                <h5 class="card-title card-sub-card-title">{{__('modules.module_sms')}}</h5>
                                                <p class="card-text card-sub-card-text">{{__('modules.module_sms_sub')}}</p>
                                                @if(isset($modules_status[0]->module_sms) && $modules_status[0]->module_sms == 0)
                                                    <a href="{{route('modules.activate_modul', 'module_sms')}}"
                                                       class="btn btn-own-primary">{{__('modules.module_sms_activate')}}</a>
                                                @else
                                                    <a href="{{route('modules.deactivate_modul', 'module_sms')}}"
                                                       class="btn btn-own-danger">{{__('modules.module_sms_deactivate')}}</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card card-sub mt-2" style="width: 100%;">
                                            <div class="card-body card-sub-body">
                                                <h5 class="card-title card-sub-card-title">{{__('modules.module_calendar')}}</h5>
                                                <p class="card-text card-sub-card-text">{{__('modules.module_calendar_sub')}}</p>
                                                @if(isset($modules_status[0]->module_calendar) && $modules_status[0]->module_calendar == 0)
                                                    <a href="{{route('modules.activate_modul', 'module_calendar')}}"
                                                       class="btn btn-own-primary">{{__('modules.module_calendar_activate')}}</a>
                                                @else
                                                    <a href="{{route('modules.deactivate_modul', 'module_calendar')}}"
                                                       class="btn btn-own-danger">{{__('modules.module_calendar_deactivate')}}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
            </div>
        </div>
    </section>
@endsection
