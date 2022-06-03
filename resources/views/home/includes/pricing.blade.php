<section id="pricing" class="pricing">
    <div class="container">

        <div class="section-title" data-aos="zoom-out">
            <h2>{{ __('home_page.pricing') }}</h2>
            <p>{{ __('home_page.pricing_sub') }}</p>
        </div>

        <div class="row">

            <div class="d-flex justify-content-center bd-highlight">
                <div class="p-2 bd-highlight">
                    <div class="col-lg-12 col-md-12">

                        <div class="box" data-aos="zoom-in">
                            <h3>{{ __('home_page.price_free') }}</h3>
                            <h4><sup>€</sup>0<span> / {{ __('home_page.month') }}</span></h4>
                            <ul>
                                <li>{{ __('home_page.free_task') }}</li>
                                <li>{{ __('home_page.free_notif') }}</li>
                                <li>{{ __('home_page.free_notif_sms') }}</li>
                                <li>{{ __('home_page.free_history') }}</li>
                                <li class="na">{{ __('home_page.free_export') }}</li>
                            </ul>
                            <div class="btn-wrap">
                                <a href="{{ route('register') }}"
                                   class="btn-buy">{{ __('home_page.free_activate') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
