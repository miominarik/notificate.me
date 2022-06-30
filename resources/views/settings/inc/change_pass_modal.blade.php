<div class="modal fade" id="changepassModal" tabindex="-1" aria-labelledby="changepassModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changepassModalLabel">Zmena hesla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{route('settings.change_password')}}">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="oldpass">Pôvodne heslo</label>
                        <input type="password" class="form-control" id="oldpass" name="oldpass" required>
                    </div>
                    <div class="form-group mt-2">
                        <label for="newpass1">Nové heslo</label>
                        <input type="password" class="form-control" id="newpass1" name="newpass1" minlength="8"
                               required>
                        <div id="emailHelp" class="form-text">Heslo musí obsahovať minimálne 8 znakov</div>
                    </div>
                    <div class="form-group mt-2">
                        <label for="newpass2">Zopakujte nové heslo</label>
                        <input type="password" class="form-control" id="newpass2" name="newpass2" minlength="8"
                               required>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="logout_everywhere" name="logout_everywhere">
                        <label class="form-check-label" for="logout_everywhere">
                            Odhlásiť ma na všetkých zariadeniach
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
                    <button type="submit" class="btn btn-primary">Zmeniť heslo</button>
                </div>
            </form>

        </div>
    </div>
</div>
