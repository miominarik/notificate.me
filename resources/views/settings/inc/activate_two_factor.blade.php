@php use Carbon\Carbon;use Illuminate\Support\Facades\Auth; @endphp
<div class="modal fade" id="activate_two_factorModal" tabindex="-1" aria-labelledby="activate_two_factorModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activate_two_factorModalLabel">{{__('settings.mfa_activate_btn')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">
                        {{__('settings.mfa_activate_start2')}}
                        <div>
                            <a target="_blank"
                               href="https://play.google.com/store/apps/details?id=com.miucode.notificateme&pcampaignid=pcampaignidMKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1">
                                <img alt="Get it on Google Play" height="65"
                                     src="{{asset('images/apps/sk_badge_web_generic.png')}}">
                            </a>
                        </div>
                    </li>
                    <li class="list-group-item">
                        {{__('settings.mfa_activate_start3')}}
                        <br>
                        @forelse($my_devices as $one_device)
                            <div class="card mb-1">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>{{$one_device->device_model}}</div>
                                        <div>{{__('settings.device_last_used')}}: {{Carbon::parse($one_device->updated_at)->format('d.m.Y H:i')}}</div>
                                        <button type="button" class="btn btn-sm btn-own-primary send_btn"
                                                onclick="SendVerifyCode({{$one_device->id}}, {{Auth::id()}})">{{__('settings.btn_select')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="card">
                                <div class="card-body">
                                    {{__('settings.device_noone')}}
                                </div>
                            </div>
                        @endforelse
                    </li>
                    <li class="list-group-item">
                        {{__('settings.mfa_activate_start4')}}<br>
                        <form method="POST" action="{{route('mfa.checkcode')}}">
                            @csrf
                            <input type="hidden" id="device_id" name="device_id" value="0">
                            <input type="hidden" name="user_id" value="{{Auth::id()}}">
                            <div class="form-group col-3 mt-1">
                                <input type="text" class="form-control" name="mfacode" maxlength="6"
                                       placeholder="123456" required autocomplete="off">
                            </div>
                            <div class="form-group mt-2">
                                <button type="submit"
                                        class="btn btn-primary">{{__('settings.send_btn')}}</button>
                            </div>
                        </form>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>