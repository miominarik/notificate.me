<div class="modal fade" id="activate_two_factorModal" tabindex="-1" aria-labelledby="activate_two_factorModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activate_two_factorModalLabel">{{__('settings.mfa_activate_start1')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">
                        {{__('settings.mfa_activate_start2')}}
                        <div>
                            <a target="_blank" href="https://apps.apple.com/us/app/google-authenticator/id388497605">
                                <img class="me-2"
                                     src="{{asset('images/apps/applestore.svg')}}" height="45">
                            </a>
                            <a target="_blank"
                               href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">
                                <img alt="Get it on Google Play" height="65"
                                     src="{{asset('images/apps/googleplay.png')}}">
                            </a>
                        </div>
                    </li>
                    <li class="list-group-item">
                        {{__('settings.mfa_activate_start3')}}
                        <string class="text-primary">@php echo $string; @endphp</string>
                        <br>
                        @php echo $qr_code; @endphp
                    </li>
                    <li class="list-group-item">
                        {{__('settings.mfa_activate_start4')}}<br>
                        <form method="POST" action="{{route('mfa.checkcode')}}">
                            @csrf
                            <div class="form-group col-3 mt-1">
                                <input type="text" class="form-control" name="mfacode" maxlength="6"
                                       placeholder="123456" required autocomplete="off">
                            </div>
                            <div class="form-group mt-2">
                                <button type="submit"
                                        class="btn btn-own-primary">{{__('settings.mfa_activate_save_btn')}}</button>
                            </div>
                        </form>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
