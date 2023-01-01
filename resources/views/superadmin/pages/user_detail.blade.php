@extends('superadmin.index')

@section('superadmin_content')
    <form method="POST" action="{{route('superadmin.update_users_detail', $user_detail[0]->id)}}">
        @csrf
        <div class="form-group">
            <label for="email" class="pb-2">E-mailová adresa</label>
            <input type="email" class="form-control" name="email" id="email" required
                   value="{{$user_detail[0]->email}}">
        </div>
        <div class="form-group">
            <label for="email_verified_at" class="pb-2 pt-2">E-mail verifikovaný</label>
            <input type="text" class="form-control" id="email_verified_at" required readonly disabled
                   value="{{$user_detail[0]->email_verified_at ? $user_detail[0]->email_verified_at : 'Neverifikovaný'}}">
        </div>
        <div class="form-group">
            <label for="blocked" class="pb-2 pt-2">Stav</label>
            <input type="text" class="form-control" id="blocked" required readonly disabled
                   value="{{$user_detail[0]->blocked ? 'Zablokovaný' : 'Aktívny'}}">
        </div>
        <div class="form-group">
            <label for="superadmin" class="pb-2 pt-2">Superadmin práva</label>
            <select name="superadmin" id="superadmin" class="form-select">
                <option @if($user_detail[0]->superadmin == 0) selected @endif value="0">Nie</option>
                <option @if($user_detail[0]->superadmin == 1) selected @endif value="1">Áno</option>
            </select>
        </div>
        <div class="form-group">
            <label for="created_at" class="pb-2 pt-2">Registrácia</label>
            <input type="text" class="form-control" id="created_at" required readonly disabled
                   value="{{$user_detail[0]->created_at}}">
        </div>
        <div class="form-group">
            <label for="apple_id" class="pb-2 pt-2">Apple Auth</label>
            <div class="row">
                <div class="col-9">
                    <input type="text" class="form-control" id="apple_id" required readonly disabled
                           value="{{$user_detail[0]->apple_id == TRUE ? 'Autorizované' : 'Neautorizované'}}">
                </div>
                <div class="col-3">
                    <a href="{{route('superadmin.users_deauthorization', ['user_id' => $user_detail[0]->id, 'auth_type' => 'apple'])}}">
                        <button type="button" class="btn btn-outline-danger">Zrušenie autorizácie</button>
                    </a>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="google_id" class="pb-2 pt-2">Google Auth</label>
            <div class="row">
                <div class="col-9">
                    <input type="text" class="form-control" id="google_id" required readonly disabled
                           value="{{$user_detail[0]->google_id == TRUE ? 'Autorizované' : 'Neautorizované'}}">
                </div>
                <div class="col-3">
                    <a href="{{route('superadmin.users_deauthorization', ['user_id' => $user_detail[0]->id, 'auth_type' => 'google'])}}">
                        <button type="button" class="btn btn-outline-danger">Zrušenie autorizácie</button>
                    </a>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="microsoft_id" class="pb-2 pt-2">Microsoft Auth</label>
            <div class="row">
                <div class="col-9">
                    <input type="text" class="form-control" id="microsoft_id" required readonly disabled
                           value="{{$user_detail[0]->microsoft_id == TRUE ? 'Autorizované' : 'Neautorizované'}}">
                </div>
                <div class="col-3">
                    <a href="{{route('superadmin.users_deauthorization', ['user_id' => $user_detail[0]->id, 'auth_type' => 'microsoft'])}}">
                        <button type="button" class="btn btn-outline-danger">Zrušenie autorizácie</button>
                    </a>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="blocked" class="pb-2 pt-2">Dvojfaktorová Autentifikácia</label>
            <input type="text" class="form-control" id="mfa" required readonly disabled
                   value="{{$mfa_status > 0 ? 'Aktivovaná' : 'Neaktivovaná'}}">
        </div>
        <div class="form-group pt-2 text-start">
            <button type="submit" class="btn btn-primary">Uložiť</button>
            @if($user_detail[0]->blocked == FALSE)
                <button type="button" class="btn btn-warning"
                        onclick="document.getElementById('tooglestatus_form').submit();">Zablokovať
                    užívatela
                </button>
            @else
                <button type="button" class="btn btn-success"
                        onclick="document.getElementById('tooglestatus_form').submit();">Odblokovať
                    užívatela
                </button>
            @endif
            @if($mfa_status > 0)
                <button type="button" class="btn btn-secondary" onclick="window.location.replace('{{route('superadmin.removemfa', $user_detail[0]->id )}}')">Deaktivovať MFA</button>
            @endif
        </div>
    </form>

    <form method="POST" action="{{route('superadmin.tooglestatus', $user_detail[0]->id)}}" id="tooglestatus_form">
        @csrf
    </form>
    <hr>
    <table class="table mt-3">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Názov úlohy</th>
            <th scope="col">Typ úlohy</th>
            <th scope="col">Dátum pripomienky</th>
            <th scope="col">Stav</th>
            <th scope="col">Dátum vytvorenia</th>
        </tr>
        </thead>
        <tbody>

        @forelse($user_tasks as $one_user_task)
            <tr>
                <th scope="row">{{$one_user_task->id}}</th>
                <td>{{$one_user_task->task_name}}</td>
                <td>{{$one_user_task->task_type ? 'Opakujúca sa' : 'Jednorázová'}}</td>
                <td>{{$one_user_task->task_next_date}}</td>
                <td>{{$one_user_task->task_enabled ? 'Aktívna' : 'Neaktívna'}}</td>
                <td>{{$one_user_task->created_at}}</td>
            </tr>
        @empty
            <tr>
                <th scope="row">Žiadne dáta</th>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endforelse
        </tbody>
    </table>
    {{$user_tasks->links()}}

@endsection
