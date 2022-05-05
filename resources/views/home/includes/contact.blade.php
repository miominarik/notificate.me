<section id="contact" class="contact">
    <div class="container">

        <div class="section-title" data-aos="zoom-out">
            <h2>{{ __('home_page.contact') }}</h2>
            <p>{{ __('home_page.contact_sub') }}</p>
        </div>

        <div class="row mt-5">

            <div class="col-lg-4" data-aos="fade-right">
                <div class="info">

                    <div class="email">
                        <i class="bi bi-envelope"></i>
                        <h4>E-mail:</h4>
                        <p><a href="mailto:info@notificate.me">info@notificate.me</a></p>
                    </div>

                </div>

            </div>

            <div class="col-lg-8 mt-5 mt-lg-0" data-aos="fade-left">

                <form method="POST" action="{{ route('contact.send_mail') }}" class="php-email-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <input type="text" name="name" class="form-control" id="name"
                                placeholder="{{ __('home_page.contact_name') }}" required>
                        </div>
                        <div class="col-md-6 form-group mt-3 mt-md-0">
                            <input type="email" class="form-control" name="email" id="email"
                                placeholder="{{ __('home_page.contact_email') }}" required>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" name="subject" id="subject"
                            placeholder="{{ __('home_page.contact_subject') }}" required>
                    </div>
                    <div class="form-group mt-3">
                        <textarea class="form-control" name="text_message" rows="5" placeholder="{{ __('home_page.contact_message') }}"
                            required></textarea>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gdpr" value="1" name="gdpr" required>
                            <label class="form-check-label" for="gdpr">Súhlasím so <a
                                    href="{{ route('gdpr') }}" target="_blank">spracovaním osobných
                                    údajov</a>.</label>
                        </div>
                    </div>
                    <div class="text-center"><button type="submit">{{ __('home_page.contact_send') }}</button>
                    </div>
                </form>

            </div>

        </div>

    </div>
</section>
