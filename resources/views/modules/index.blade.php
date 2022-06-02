@extends('layouts.app')

@section('content')
    <section style="height: 100%">
        <div class="container-fluid py-5">
            <div class="row d-flex justify-content-center align-items-center">
                @if ((new \Jenssegers\Agent\Agent())->isDesktop())
                    <div class="col-12 col-md-9 col-lg-6 col-xl-6">
                        @elseif((new \Jenssegers\Agent\Agent())->isMobile() || (new \Jenssegers\Agent\Agent())->isTablet())
                            <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                                @endif
                                <div class="card shadow-2-strong" style="border-radius: 1rem;">
                                    <div class="card-body p-5 text-center">
                                        <h3 class="mb-5">Moduly</h3>
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
                                        <div class="card" style="width: 100%;">
                                            <div class="card-body">
                                                <h5 class="card-title">SMS notifikácie</h5>
                                                <p class="card-text">Ak chcete byť informovaný aj pomocou SMS, tak tento
                                                    modul je priamo pre Vás.</p>
                                                @if(isset($modules_status[0]->module_sms) && $modules_status[0]->module_sms == 0)
                                                    <a href="{{route('modules.activate_modul', 'module_sms')}}"
                                                       class="btn btn-primary">Aktivovať modul</a>
                                                @else
                                                    <a href="{{route('modules.deactivate_modul', 'module_sms')}}"
                                                       class="btn btn-danger">Deaktivovať modul</a>
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
