@extends('layouts.app')

@section('content')
    <section style="height: 100%">
        <div class="container-fluid py-5">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                    <div class="list-group">
                        <a href="{{route('superadmin.users')}}"
                           class="list-group-item list-group-item-action {{request()->is('superadmin/users') || request()->is('superadmin/users/*') ? 'active' : ''}}">Užívatelia</a>
                        <a href="{{route('superadmin.users_modules')}}"
                           class="list-group-item list-group-item-action {{request()->is('superadmin/users_modules') ? 'active' : ''}}">Stav
                            modulov</a>
                        <a href="{{route('superadmin.logs')}}"
                           class="list-group-item list-group-item-action {{request()->is('superadmin/logs') ? 'active' : ''}}">Logy</a>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-9">
                    <div class="card">
                        <div class="card-body">
                            @yield('superadmin_content')
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
