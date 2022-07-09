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
                    @foreach($recovery_codes as $one_code)
                        <li class="list-group-item">{{$one_code['code']}} @if($one_code['used_at'] != NULL)
                                - {{__('settings.mfa_recovery_code_used')}}
                            @endif</li>
                    @endforeach
                </ol>
                <div class="d-flex justify-content-between mb-3">
                    <div class="p-2"><a href="{{route('mfa.disablemfa')}}">{{__('settings.mfa_deactivate')}}</a></div>
                    <div class="p-2"><a
                            href="{{route('mfa.generatenewcodes')}}">{{__('settings.mfa_generate_codes')}}</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
