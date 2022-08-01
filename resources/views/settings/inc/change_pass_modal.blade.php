<div class="modal fade" id="changepassModal" tabindex="-1" aria-labelledby="changepassModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changepassModalLabel">{{__('settings.change_pass_btn')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{route('settings.change_password')}}">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="oldpass">{{__('settings.old_pass')}}</label>
                        <input type="password" class="form-control" id="oldpass" name="oldpass" required>
                    </div>
                    <div class="form-group mt-2">
                        <label for="newpass1">{{__('settings.new_pass')}}</label>
                        <input type="password" class="form-control" id="newpass1" name="newpass1" minlength="8"
                               required>
                        <div id="emailHelp" class="form-text">{{__('settings.new_pass_help')}}</div>
                    </div>
                    <div class="form-group mt-2">
                        <label for="newpass2">{{__('settings.new_pass2')}}</label>
                        <input type="password" class="form-control" id="newpass2" name="newpass2" minlength="8"
                               required>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="logout_everywhere" name="logout_everywhere">
                        <label class="form-check-label" for="logout_everywhere">
                            {{__('settings.log_out_everywhere')}}
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-own-secondary"
                            data-bs-dismiss="modal">{{__('settings.close')}}</button>
                    <button type="submit" class="btn btn-own-primary">{{__('settings.change_it')}}</button>
                </div>
            </form>

        </div>
    </div>
</div>
