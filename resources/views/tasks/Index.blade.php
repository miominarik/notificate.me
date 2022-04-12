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
                                    <button class="btn btn-dark btn-sm" type="button" data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvasAddTask"
                                        aria-controls="offcanvasWithBackdrop">{{ __('tasks.menu_task_create') }}</button>
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

                            @if ((new \Jenssegers\Agent\Agent())->isDesktop())
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
                                                    <tr style="cursor: pointer;">
                                                        <td scope="row" onclick="getDataToEditTaskForm({{ $item->id }})">
                                                            {{ $item->task_name }}</td>
                                                        <td scope="row" onclick="getDataToEditTaskForm({{ $item->id }})">
                                                            {{ $item->task_note }}</td>
                                                        <td scope="row" onclick="getDataToEditTaskForm({{ $item->id }})">
                                                            {{ \Carbon\Carbon::parse($item->task_next_date)->format('d.m.Y') }}
                                                        </td>
                                                        <td scope="row" onclick="getDataToEditTaskForm({{ $item->id }})">
                                                            {{ $item->task_repeat_value }}
                                                            {{ $item->task_repeat_type }}
                                                        </td>
                                                        <td scope="row">
                                                            <span data-bs-toggle="modal" data-bs-target="#completeModal"
                                                                onclick="document.getElementById('form_complete_task').setAttribute('action', '{{ route('tasks.complete', $item->id) }}')"><i
                                                                    class="fa-solid fa-check succes_icon"></i></span>
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
                            @elseif ((new \Jenssegers\Agent\Agent())->isMobile() == true || (new \Jenssegers\Agent\Agent())->isTable() == true)
                                @isset($all_enabled_tasks)
                                    @forelse ($all_enabled_tasks as $item)
                                        @php
                                            $item->task_repeat_type = match ($item->task_repeat_type) { 1 => trans_choice('tasks.day', $item->task_repeat_value),  2 => trans_choice('tasks.week', $item->task_repeat_value),  3 => trans_choice('tasks.month', $item->task_repeat_value),  4 => trans_choice('tasks.year', $item->task_repeat_value),  default => __('tasks.none') };
                                        @endphp
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="card mb-3" style="width: auto;">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{ $item->task_name }}</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted">{{ $item->task_note }}</h6>
                                                        <p class="card-text">
                                                            {{ __('tasks.task_date') }}:
                                                            {{ \Carbon\Carbon::parse($item->task_next_date)->format('d.m.Y') }}
                                                            <br>
                                                            {{ __('tasks.task_repeat') }}: {{ $item->task_repeat_value }}
                                                            {{ $item->task_repeat_type }}
                                                        </p>
                                                        <span class="card-link" style="color: green;"
                                                            data-bs-toggle="modal" data-bs-target="#completeModal"
                                                            onclick="document.getElementById('form_complete_task').setAttribute('action', '{{ route('tasks.complete', $item->id) }}')">{{ __('tasks.complete_btn') }}</span>
                                                        <span class="card-link" style="color: blue;"
                                                            onclick="getDataToEditTaskForm({{ $item->id }})">{{ __('tasks.edit_btn') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="card mb-3" style="width: auto;">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{ __('tasks.no_data') }}</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted"></h6>
                                                        <p class="card-text"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                @endisset
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Offcanvas to add task --}}

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddTask" aria-labelledby="offcanvasAddTaskLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasWithBackdropLabel">{{ __('tasks.add_new_task') }}</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form method="POST" action="{{ route('tasks.store') }}" id="offcanvasAddTaskForm">
                @csrf
                @method('POST')
                <div class="form-group row">
                    <label for="task_name" class="col-sm-1-12 col-form-label">{{ __('tasks.task_name') }}</label>
                    <div class="col-sm-1-12">
                        <input type="text" class="form-control" name="task_name" id="task_name" required max="50">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="task_note" class="col-sm-1-12 col-form-label">{{ __('tasks.task_note') }}</label>
                    <div class="col-sm-1-12">
                        <input type="text" class="form-control" name="task_note" id="task_note" max="50">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="task_next_date"
                        class="col-sm-1-12 col-form-label">{{ __('tasks.task_next_date') }}</label>
                    <div class="col-sm-1-12">
                        <input type="date" class="form-control" name="task_next_date" id="task_next_date" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6">
                        <label for="task_repeat_value"
                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_repeat_value') }}</label>
                        <div class="col-sm-1-12">
                            <input type="number" class="form-control" name="task_repeat_value" id="task_repeat_value"
                                min="1" max="16777215" placeholder="{{ __('tasks.amount') }}" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <label for="task_repeat_type"
                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_repeat_type') }}</label>
                        <div class="col-sm-1-12">
                            <select class="form-control" name="task_repeat_type" id="task_repeat_type">
                                <option value="1">{{ __('tasks.one_day') }}</option>
                                <option value="2">{{ __('tasks.one_week') }}</option>
                                <option value="3">{{ __('tasks.one_month') }}</option>
                                <option value="4">{{ __('tasks.one_year') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6">
                        <label for="task_notification_value"
                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_notification_value') }}</label>
                        <div class="col-sm-1-12">
                            <input type="number" class="form-control" name="task_notification_value"
                                id="task_notification_value" min="1" max="16777215"
                                placeholder="{{ __('tasks.amount') }}" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <label for="task_notification_type"
                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_notification_type') }}</label>
                        <div class="col-sm-1-12">
                            <select class="form-control" name="task_notification_type" id="task_notification_type">
                                <option value="1">{{ __('tasks.one_day') }}</option>
                                <option value="2">{{ __('tasks.one_week') }}</option>
                                <option value="3">{{ __('tasks.one_month') }}</option>
                                <option value="4">{{ __('tasks.one_year') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="offset mt-3">
                        <button type="submit" class="btn btn-primary">{{ __('tasks.save_button') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Offcanvas to edit task --}}

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditTask" aria-labelledby="offcanvasEditTaskLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasWithBackdropLabel">{{ __('tasks.edit_task') }}</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form method="POST" action="" id="offcanvasEditForm">
                @csrf
                @method('PUT')
                <div class="form-group row">
                    <label for="task_name" class="col-sm-1-12 col-form-label">{{ __('tasks.task_name') }}</label>
                    <div class="col-sm-1-12">
                        <input type="text" class="form-control" name="task_name" id="task_name_edit" required max="50">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="task_note" class="col-sm-1-12 col-form-label">{{ __('tasks.task_note') }}</label>
                    <div class="col-sm-1-12">
                        <input type="text" class="form-control" name="task_note" id="task_note_edit" max="50">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="task_next_date"
                        class="col-sm-1-12 col-form-label">{{ __('tasks.task_next_date') }}</label>
                    <div class="col-sm-1-12">
                        <input type="date" class="form-control" name="task_next_date" id="task_next_date_edit" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6">
                        <label for="task_repeat_value"
                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_repeat_value') }}</label>
                        <div class="col-sm-1-12">
                            <input type="number" class="form-control" name="task_repeat_value" id="task_repeat_value_edit"
                                min="1" max="16777215" placeholder="{{ __('tasks.amount') }}" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <label for="task_repeat_type"
                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_repeat_type') }}</label>
                        <div class="col-sm-1-12">
                            <select class="form-control" name="task_repeat_type" id="task_repeat_type_edit">
                                <option value="1">{{ __('tasks.one_day') }}</option>
                                <option value="2">{{ __('tasks.one_week') }}</option>
                                <option value="3">{{ __('tasks.one_month') }}</option>
                                <option value="4">{{ __('tasks.one_year') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6">
                        <label for="task_notification_value"
                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_notification_value') }}</label>
                        <div class="col-sm-1-12">
                            <input type="number" class="form-control" name="task_notification_value"
                                id="task_notification_value_edit" min="1" max="16777215"
                                placeholder="{{ __('tasks.amount') }}" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <label for="task_notification_type"
                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_notification_type') }}</label>
                        <div class="col-sm-1-12">
                            <select class="form-control" name="task_notification_type" id="task_notification_type_edit">
                                <option value="1">{{ __('tasks.one_day') }}</option>
                                <option value="2">{{ __('tasks.one_week') }}</option>
                                <option value="3">{{ __('tasks.one_month') }}</option>
                                <option value="4">{{ __('tasks.one_year') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="offset mt-3">
                        <button type="submit" class="btn btn-primary">{{ __('tasks.save_button') }}</button>
                        <button type="button" class="btn btn-danger"
                            onclick="delete_task();">{{ __('tasks.remove_button') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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

    {{-- Reset Add form if offcanvas is hidden --}}
    <script>
        let offcanvasAddTask = document.getElementById('offcanvasAddTask')
        offcanvasAddTask.addEventListener('hidden.bs.offcanvas', function() {
            document.getElementById("offcanvasAddTaskForm").reset();
        })
    </script>

    {{-- Ajax for editTaskForm --}}
    <script>
        function getDataToEditTaskForm(task_id) {
            axios.post('/tasks/' + task_id + '/edit')
                .then(function(response) {
                    document.getElementById('task_name_edit').value = response.data[0].task_name;
                    document.getElementById('task_note_edit').value = response.data[0].task_note;
                    document.getElementById('task_next_date_edit').value = response.data[0].task_next_date;
                    document.getElementById('task_repeat_value_edit').value = response.data[0].task_repeat_value;
                    document.getElementById('task_repeat_type_edit').value = response.data[0].task_repeat_type;
                    document.getElementById('task_notification_value_edit').value = response.data[0]
                        .task_notification_value;
                    document.getElementById('task_notification_type_edit').value = response.data[0]
                        .task_notification_type;

                    document.getElementById('delete_form').action = 'tasks/' + task_id;
                    document.getElementById('offcanvasEditForm').action = 'tasks/' + task_id;

                    const offcanvasEditTask = new bootstrap.Offcanvas(document.getElementById(
                        'offcanvasEditTask'));
                    offcanvasEditTask.show();
                })
                .catch(function(error) {
                    //console.log(error);
                });
        }
    </script>

    {{-- Delete task --}}
    <form method="POST" action="" id="delete_form">
        @method('DELETE')
        @csrf
    </form>

    <script>
        function delete_task() {
            event.preventDefault();
            document.getElementById('delete_form').submit();
        }
    </script>
@endsection
