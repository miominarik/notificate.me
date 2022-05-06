<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Start cookieyes banner -->
    <script id="cookieyes" type="text/javascript"
        src="https://cdn-cookieyes.com/client_data/53022338a42204fb995da8b6/script.js"></script>
    <!-- End cookieyes banner -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon_io/site.webmanifest') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://lipis.github.io/bootstrap-social/bootstrap-social.css" rel="stylesheet">
</head>

<body>
    <div id="app">

        <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: #343A40;">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">{{ config('app.name', 'Laravel') }}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    @auth
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('tasks*') ? 'active' : '' }}" aria-current="page"
                                    href="{{ route('tasks.index') }}">{{ __('layout.menu_tasks') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('settings*') ? 'active' : '' }}" aria-current="page"
                                    href="{{ route('settings.index') }}">{{ __('layout.menu_settings') }}</a>
                            </li>
                        </ul>
                    @endauth
                    @guest
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('layout.menu_lang') }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                                    <li><a class="dropdown-item" href="/language/sk">{{ __('layout.lang_slovak') }}</a>
                                    </li>
                                    <li><a class="dropdown-item" href="/language/en">{{ __('layout.lang_english') }}</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    @endguest
                    @auth
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown pe-4">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarNotificationMenu" role="button"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <button type="button" class="btn btn-link btn-sm position-relative">
                                        <i class="fa-solid fa-bell fs-5 @if ($notifications['count'] < 1) text-white @endif "
                                            id="notif_bell"></i>
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $notifications['count'] }}
                                            <span class="visually-hidden">{{ __('layout.notif_comming') }}</span>
                                        </span>
                                    </button>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarNotificationMenu">
                                    <li>
                                        <h6 class="dropdown-header">{{ __('layout.notif_comming') }}</h6>
                                    </li>
                                    @forelse ($notifications['data'] as $one_notif)
                                        <li><span class="dropdown-item"
                                                style="cursor: default">{{ $one_notif['task_name'] }} -
                                                {{ $one_notif['task_next_date'] }}</span></li>
                                    @empty
                                        <li><span class="dropdown-item"
                                                style="cursor: default">{{ __('tasks.no_data') }}</span></li>
                                    @endforelse
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('layout.menu_lang') }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                                    <li><a class="dropdown-item" href="/language/sk">{{ __('layout.lang_slovak') }}</a>
                                    </li>
                                    <li><a class="dropdown-item" href="/language/en">{{ __('layout.lang_english') }}</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-dark dropdown-menu-end"
                                    aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('layout.menu_logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    @endauth
                </div>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="{{ asset('js/main.js') }}" defer></script>
</body>

</html>
