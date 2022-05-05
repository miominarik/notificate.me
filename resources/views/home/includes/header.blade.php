<header id="header" class="fixed-top d-flex align-items-center  header-transparent ">
    <div class="container d-flex align-items-center justify-content-between">

        <div class="logo">
            <h1><a href="/">{{ env('APP_NAME') }}</a></h1>
        </div>

        <nav id="navbar" class="navbar">
            <ul>
                <li><a class="nav-link scrollto" href="#hero">{{ __('home_page.menu_home') }}</a></li>
                <li><a class="nav-link scrollto" href="#services">{{ __('home_page.menu_services') }}</a></li>
                <li><a class="nav-link scrollto" href="#pricing">{{ __('home_page.menu_pricing') }}</a></li>
                <li><a class="nav-link scrollto" href="#contact">{{ __('home_page.menu_contact') }}</a></li>
                <li><a class="nav-link scrollto" href="/app">{{ __('home_page.menu_login') }}</a></li>
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav><!-- .navbar -->

    </div>
</header>
