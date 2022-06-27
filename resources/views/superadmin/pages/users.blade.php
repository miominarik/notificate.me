@extends('superadmin.index')

@section('superadmin_content')
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">E-mail</th>
                <th scope="col">E-mail verifikovaný</th>
                <th scope="col">Stav</th>
                <th scope="col">Registrácia</th>
            </tr>
            </thead>
            <tbody>
            @forelse($all_users as $one_user)
                <tr onclick="show_user_details({{$one_user->id}})" style="cursor: pointer;">
                    <th scope="row">{{$one_user->id}}</th>
                    <td>{{$one_user->email}}</td>
                    <td>{{$one_user->email_verified_at ? $one_user->email_verified_at : 'Neverifikovaný'}}</td>
                    <td>{{$one_user->blocked ? 'Zablokovaný' : 'Aktívny'}}</td>
                    <td>{{$one_user->created_at}}</td>
                </tr>
            @empty
                <tr>
                    <th scope="row">Žiadne dáta</th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforelse
            </tbody>
        </table>
        {{ $all_users->links() }}
    </div>

    <script>
        function show_user_details(user_id) {
            if (user_id) {
                let url = '{{ route("superadmin.user_detail", ":user_id") }}';
                url = url.replace(':user_id', user_id);
                window.location.href = url;
            }
        }
    </script>
@endsection
