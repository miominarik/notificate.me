@extends('layouts.app')

@section('content')
    <section style="height: 100%">
        <div class="container-fluid py-5">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-12 col-md-12 col-lg-12 col-xl-9">
                    <div class="card shadow-2-strong" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">

                            <div class="d-flex justify-content-between bd-highlight mb-3">
                                <div class="bd-highlight"></div>
                                <div class="ps-5 bd-highlight">
                                    <h3 class="mb-5">{{ __('tasks.all_active_tasks') }}</h3>
                                </div>
                                <div class="bd-highlight">
                                    <a href="{{ route('tasks.create') }}"><button type="button"
                                            class="btn btn-dark btn-sm">{{ __('tasks.menu_task_create') }}</button></a>
                                </div>
                            </div>

                            @if (session('status_success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status_success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('status_warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{ session('status_warning') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('status_danger'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('status_danger') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('tasks.task_name') }}</th>
                                            <th>{{ __('tasks.task_note') }}</th>
                                            <th>{{ __('tasks.task_date') }}</th>
                                            <th>{{ __('tasks.task_repeat') }}</th>
                                            <th>{{ __('tasks.task_action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($all_enabled_tasks)
                                            @forelse ($all_enabled_tasks as $item)
                                                @php
                                                    $item->task_repeat_type = match ($item->task_repeat_type) { 1 => trans_choice('tasks.day', $item->task_repeat_value),  2 => trans_choice('tasks.week', $item->task_repeat_value),  3 => trans_choice('tasks.month', $item->task_repeat_value),  4 => trans_choice('tasks.year', $item->task_repeat_value),  default => __('tasks.none') };
                                                @endphp
                                                <tr>
                                                    <td scope="row">{{ $item->task_name }}</td>
                                                    <td scope="row">{{ $item->task_note }}</td>
                                                    <td scope="row">
                                                        {{ \Carbon\Carbon::parse($item->task_next_date)->format('d.m.Y') }}
                                                    </td>
                                                    <td scope="row">{{ $item->task_repeat_value }}
                                                        {{ $item->task_repeat_type }}
                                                    </td>
                                                    <td scope="row">
                                                        <span data-bs-toggle="modal" data-bs-target="#completeModal"
                                                            onclick="document.getElementById('form_complete_task').setAttribute('action', '{{ route('tasks.complete', $item->id) }}')"><i
                                                                class="fa-solid fa-check succes_icon"></i></span>
                                                        |
                                                        <a href="{{ route('tasks.edit', $item->id) }}"><i
                                                                class="fa-solid fa-pen"></i></a </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td scope="row">{{ __('tasks.no_data') }}</td>
                                                    <td scope="row"></td>
                                                    <td scope="row"></td>
                                                    <td scope="row"></td>
                                                    <td scope="row"></td>
                                                </tr>
                                            @endforelse
                                        @endisset
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal to complete tasks -->
    <div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="completeModalLabel">{{ __('tasks.check_task_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="form_complete_task">
                        @csrf
                        @method('PUT')
                        <div class="form-group row">
                            <label for="complete_date"
                                class="col-sm-1-12 col-form-label">{{ __('tasks.complete_date') }}</label>
                            <div class="col-sm-1-12">
                                <input type="date" class="form-control" name="complete_date" id="complete_date" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('tasks.close_button') }}</button>
                    <button type="button" class="btn btn-success"
                        onclick="document.getElementById('form_complete_task').submit();">{{ __('tasks.save_button') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {

            let today = new Date();
            let dd = String(today.getDate()).padStart(2, '0');
            let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            let yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            document.getElementById('complete_date').value = today
        });
    </script>
@endsection
