@extends('superadmin.index')

@section('superadmin_content')
    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
            <tr>
                <th scope="col">Uživateľ</th>
                <th scope="col">Log typ</th>
                <th scope="col">Task</th>
                <th scope="col">IP</th>
                <th scope="col">Dátum</th>
            </tr>
            </thead>
            <tbody>
            @forelse($all_logs as $one_log)
                <tr>
                    <th scope="row">{{$one_log->email}}</th>
                    <th scope="row">{{$one_log->log_type}}</th>
                    <th scope="row">{{$one_log->task_name}}</th>
                    <th scope="row">{{$one_log->ip_address}}</th>
                    <th scope="row">{{$one_log->created_at}}</th>
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
        {{ $all_logs->links() }}
    </div>
@endsection
