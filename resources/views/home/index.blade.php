<!DOCTYPE html>
<html lang="sk">

<head>
    @if(env('APP_DEBUG') == FALSE)
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
          content="Notificate.me Vám pomôže s evidenciou Vašich úloh. Vďaka aplikácii Notificate.me môžete jednoduchšie plánovať a organizovať svoje úlohy."/>
    <meta name="keywords" content="Úlohy, Evidencia, Task Manager, Plánovanie, Upozornenia, Evidencia Vašich úloh">
    <meta name="apple-mobile-web-app-title" content="Notificate.me">
    <meta name="application-name" content="Notificate.me"/>
    <meta name="msapplication-tooltip" content="Notificate.me">
    <meta name="msapplication-starturl" content="/"/>
    <meta name="robots" content="index, follow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="revisit-after" content="30 days">
    <meta name="author" content="Miroslav Minárik">

    <title>{{ config('app.name', 'Laravel') }}</title>


    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('home_page_assets/vendor/animate.css/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('home_page_assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('home_page_assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('home_page_assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('home_page_assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('home_page_assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('home_page_assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('home_page_assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('home_page_assets/css/styles.mini.css') }}" rel="stylesheet">
</head>

<body>

<!-- ======= Header ======= -->
@include('home.includes.header')
<!-- End Header -->

<!-- ======= Hero Section ======= -->
@include('home.includes.slider')
<!-- End Hero -->

<main id="main">

    @if (isset($page))

        @if ($page == 'gdpr')
            @include('home.page.gdpr')
        @elseif($page == 'cookies')
            @include('home.page.cookies')
        @elseif($page == 'unsubscribe')
            @include('home.page.unsubscribe')
        @endif
    @else
        <!-- ======= Services Section ======= -->
        @include('home.includes.info')
        <!-- End Services Section -->

        <!-- ======= Services Section ======= -->
        @include('home.includes.services')
        <!-- End Services Section -->

        <!-- ======= Services Section ======= -->
        @include('home.includes.app')
        <!-- End Services Section -->

        <!-- ======= Services Section ======= -->
        @include('home.includes.screenshots')
        <!-- End Services Section -->

        {{--<!-- ======= Pricing Section ======= -->
        @include('home.includes.pricing')
        <!-- End Pricing Section -->--}}

        <!-- ======= Contact Section ======= -->
        @include('home.includes.contact')
        <!-- End Contact Section -->
    @endif


</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer">
    <div class="container">
        <h3>Notificate.me</h3>
        <div class="route">
            <ul class="list-inline">
                <li class="list-inline-item"><a href="/gdpr">{{__('home_page.gdpr')}}</a></li>
                <li class="list-inline-item"><a href="/cookies">{{__('home_page.cookies')}}</a></li>
            </ul>
        </div>
        <div class="copyright">
            &copy; Copyright @php
                echo date('Y');
            @endphp <strong><span><a href="https://miucode.com" target="_blank"
                                     style="text-decoration: none;">Miucode.com</a></span></strong>. All Rights
            Reserved
        </div>
        <hr>
        <small>
            This site is protected by reCAPTCHA and the Google
            <a href="https://policies.google.com/privacy">Privacy Policy</a> and
            <a href="https://policies.google.com/terms">Terms of Service</a> apply.
        </small>
    </div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="{{ asset('home_page_assets/vendor/aos/aos.js') }}"></script>
<script src="{{ asset('home_page_assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('home_page_assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
<script src="{{ asset('home_page_assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
<script src="{{ asset('home_page_assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

<!-- Template Main JS File -->
<script src="{{ asset('home_page_assets/js/main.js') }}"></script>

<script src="https://www.google.com/recaptcha/api.js?render={{env('RECAPTCHA_V3_SITE_KEY')}}"></script>
<script>
    function submitPost() {
        grecaptcha.ready(function () {
            grecaptcha.execute('{{env('RECAPTCHA_V3_SITE_KEY')}}', {action: 'validate_captcha'}).then(function (token) {
                document.getElementById('g-recaptcha-response').value = token;
                document.getElementById('contact_form_submit_btn').click();
            });
        });
    }
</script>
</body>

</html>
