@extends('superadmin.index')

@section('superadmin_content')
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">E-mail</th>
                <th scope="col">Zľava</th>
                <th scope="col">Modul SMS</th>
                <th scope="col">Modul Kalendár</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users_modules as $one_user_module)
                <tr>
                    <th scope="row">{{$one_user_module->email}}</th>
                    <th scope="row">{{$one_user_module->discount_percent}} %</th>
                    <td class="ps-4 {{$one_user_module->module_sms == 1 ? 'text-success' : 'text-danger'}}">{{$one_user_module->module_sms == 1 ? html_entity_decode('&#10003;') : html_entity_decode('&#10005;')}}</td>
                    <td class="ps-4 {{$one_user_module->module_calendar == 1 ? 'text-success' : 'text-danger'}}">{{$one_user_module->module_calendar == 1 ? html_entity_decode('&#10003;') : html_entity_decode('&#10005;')}}</td>
                </tr>
            @empty
                <tr>
                    <th scope="row">Žiadne dáta</th>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforelse
            </tbody>
        </table>
        {{ $users_modules->links() }}
    </div>
@endsection
