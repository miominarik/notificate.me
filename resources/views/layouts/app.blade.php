<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @if(env('APP_DEBUG') == false)
        @if((new \Jenssegers\Agent\Agent())->isDesktop())
            <!-- Start cookieyes banner -->
            <script id="cookieyes" type="text/javascript"
                    src="https://cdn-cookieyes.com/client_data/53022338a42204fb995da8b6/script.js"></script>
            <!-- End cookieyes banner -->
        @endif
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-0EK8WTBP0C"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());

            gtag('config', 'G-0EK8WTBP0C');
        </script>
    @endif
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon_io/site.webmanifest') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
          content="Notificate.me Vám pomôže s evidenciou Vaších úloh. Vďaka aplikácii Notificate.me môžete jednoduchšie plánovať a organizovať svoje úlohy."/>
    <meta name="keywords" content="Úlohy, Evidencia, Task Manager, Plánovanie, Upozornenia, Evidencia Vaších úloh">
    <meta name="author" content="Miroslav Minárik"/>
    <meta name="robots" content="noindex, nofollow"/>
    <meta name="apple-mobile-web-app-title" content="Notificate.me">
    <meta name="application-name" content="Notificate.me"/>
    <meta name="msapplication-tooltip" content="Notificate.me">
    <meta name="msapplication-starturl" content="/"/>

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"
          integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://lipis.github.io/bootstrap-social/bootstrap-social.css" rel="stylesheet">

    @if(request()->is('calendar*'))
        <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
        <link href='{{asset('css/fullcalendar.css')}}' rel='stylesheet'/>
        <script src='{{asset('js/fullcalendar.js')}}'></script>
        <script src='{{asset('js/fullcalendar-sk.js')}}'></script>
        <script src='https://unpkg.com/popper.js/dist/umd/popper.min.js'></script>
        <script src='https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js'></script>
    @endif

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>

<body>
<div id="app">
    @if((new \Jenssegers\Agent\Agent())->isDesktop())
        <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="/app">{{ config('app.name', 'Laravel') }}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    @auth
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->is('tasks*') ? 'active' : '' }}"
                                   aria-current="page"
                                   href="{{ route('tasks.index') }}">{{ __('layout.menu_tasks') }}</a>
                            </li>
                            @if($activated_modules->module_calendar)
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->is('calendar*') ? 'active' : '' }}"
                                       aria-current="page"
                                       href="{{ route('calendar.index') }}">{{__('layout.menu_calendar')}}</a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->is('modules*') ? 'active' : '' }}"
                                   aria-current="page"
                                   href="{{ route('modules.index') }}">{{ __('layout.menu_modules') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->is('settings*') ? 'active' : '' }}"
                                   aria-current="page"
                                   href="{{ route('settings.index') }}">{{ __('layout.menu_settings') }}</a>
                            </li>
                            @if(session()->get('user_superadmin') === 1)
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->is('superadmin*') ? 'active' : '' }}"
                                       aria-current="page"
                                       href="{{ route('superadmin.index') }}">Administrácia</a>
                                </li>
                            @endif
                        </ul>
                    @endauth
                    @guest
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link text-white" aria-current="page"
                                   href="/">{{ __('layout.menu_index') }}</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                                   role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('layout.menu_lang') }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end"
                                    aria-labelledby="navbarDropdownMenuLink">
                                    <li><a class="dropdown-item"
                                           href="/language/sk">{{ __('layout.lang_slovak') }}</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                           href="/language/en">{{ __('layout.lang_english') }}</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    @endguest
                    @auth
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown pe-4">
                                <a class="nav-link dropdown-toggle text-white"
                                   href="#"
                                   id="navbarNotificationMenu"
                                   role="button"
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
                                <ul class="dropdown-menu dropdown-menu-end"
                                    aria-labelledby="navbarNotificationMenu">
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
                                <a id="navbarDropdown"
                                   class="nav-link dropdown-toggle text-white"
                                   href="#"
                                   role="button"
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->email }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end"
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
    @endif
    <main>
        @yield('content')
    </main>
    @auth
        @if((new \Jenssegers\Agent\Agent())->isMobile() ||(new \Jenssegers\Agent\Agent())->isTablet())
            <div class="sticky-footer">
                <a href="{{ route('tasks.index') }}"
                   class="sticky-footer mb-1 item {{ request()->is('tasks*') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i>
                    {{ __('layout.menu_tasks') }}
                </a>
                @if($activated_modules->module_calendar)
                    <a href="{{ route('calendar.index') }}"
                       class="sticky-footer mb-1 item {{ request()->is('calendar*') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-days"></i>
                        {{__('layout.menu_calendar')}}
                    </a>
                @endif
                <a href="{{ route('modules.index') }}"
                   class="sticky-footer mb-1 item {{ request()->is('modules*') ? 'active' : '' }}">
                    <i class="fa-solid fa-puzzle-piece"></i>
                    {{ __('layout.menu_modules') }}
                </a>
                <a href="{{ route('settings.index') }}"
                   class="sticky-footer mb-1 item {{ request()->is('settings*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i>
                    {{ __('layout.menu_settings') }}
                </a>
            </div>
        @endif
    @endauth
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.27.2/axios.min.js"
        integrity="sha512-odNmoc1XJy5x1TMVMdC7EMs3IVdItLPlCeL5vSUPN2llYKMJ2eByTTAIiiuqLg+GdNr9hF6z81p27DArRFKT7A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('js/main.js') }}" defer></script>
</body>

</html>
