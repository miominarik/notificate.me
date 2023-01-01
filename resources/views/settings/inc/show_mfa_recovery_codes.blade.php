@php use Illuminate\Support\Facades\Crypt; @endphp
<div class="modal fade" id="show_recocery_codesModal" tabindex="-1" aria-labelledby="show_recocery_codesModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="show_recocery_codesModalLabel">{{__('settings.mfa_recovery_code')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ol class="list-group list-group-numbered">
                    @php
                        $recovery_codes = explode(',', Crypt::decrypt($mfa_info[0]->recovery_codes));
                    @endphp
                    @foreach($recovery_codes as $one_code)
                        <li class="list-group-item">{{$one_code}}</li>
                    @endforeach
                </ol>
                 <div class="d-flex justify-content-between mb-1">
                     <div class="p-2"><a href="{{route('mfa.disablemfa')}}">{{__('settings.mfa_deactivate')}}</a></div>
                 </div>
            </div>
        </div>
    </div>
</div>