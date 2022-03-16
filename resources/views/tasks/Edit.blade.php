@extends('layouts.app')

@section('content')
    <section style="height: 100%">
        <div class="container-fluid py-5">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-12 col-md-12 col-lg-12 col-xl-9">
                    <div class="card shadow-2-strong" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <h3 class="mb-5">{{ __('tasks.edit_task') }}</h3>

                            <form method="POST" action="{{ route('tasks.update', $task_data[0]->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group row">
                                    <label for="task_name"
                                        class="col-sm-1-12 col-form-label">{{ __('tasks.task_name') }}</label>
                                    <div class="col-sm-1-12">
                                        <input type="text" class="form-control" name="task_name" id="task_name" required
                                            max="50" value="{{ $task_data[0]->task_name }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="task_note"
                                        class="col-sm-1-12 col-form-label">{{ __('tasks.task_note') }}</label>
                                    <div class="col-sm-1-12">
                                        <input type="text" class="form-control" name="task_note" id="task_note" max="50"
                                            value="{{ $task_data[0]->task_note }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="task_next_date"
                                        class="col-sm-1-12 col-form-label">{{ __('tasks.task_next_date') }}</label>
                                    <div class="col-sm-1-12">
                                        <input type="date" class="form-control" required readonly disabled
                                            value="{{ $task_data[0]->task_next_date }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-6">
                                        <label for="task_repeat_value"
                                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_repeat_value') }}</label>
                                        <div class="col-sm-1-12">
                                            <input type="number" class="form-control" name="task_repeat_value"
                                                id="task_repeat_value" min="1" max="16777215" required
                                                value="{{ $task_data[0]->task_repeat_value }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label for="task_repeat_type"
                                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_repeat_type') }}</label>
                                        <div class="col-sm-1-12">
                                            <select class="form-control" name="task_repeat_type" id="task_repeat_type">
                                                <option @if ($task_data[0]->task_repeat_type == 1) selected @endif value="1">
                                                    {{ __('tasks.one_day') }}</option>
                                                <option @if ($task_data[0]->task_repeat_type == 2) selected @endif value="2">
                                                    {{ __('tasks.one_week') }}</option>
                                                <option @if ($task_data[0]->task_repeat_type == 3) selected @endif value="3">
                                                    {{ __('tasks.one_month') }}</option>
                                                <option @if ($task_data[0]->task_repeat_type == 4) selected @endif value="4">
                                                    {{ __('tasks.one_year') }}</option>
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
                                                value="{{ $task_data[0]->task_notification_value }}" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label for="task_notification_type"
                                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_notification_type') }}</label>
                                        <div class="col-sm-1-12">
                                            <select class="form-control" name="task_notification_type"
                                                id="task_notification_type">
                                                <option @if ($task_data[0]->task_notification_type == 1) selected @endif value="1">
                                                    {{ __('tasks.one_day') }}</option>
                                                <option @if ($task_data[0]->task_notification_type == 2) selected @endif value="2">
                                                    {{ __('tasks.one_week') }}</option>
                                                <option @if ($task_data[0]->task_notification_type == 3) selected @endif value="3">
                                                    {{ __('tasks.one_month') }}</option>
                                                <option @if ($task_data[0]->task_notification_type == 4) selected @endif value="4">
                                                    {{ __('tasks.one_year') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset mt-3">
                                        <button type="submit"
                                            class="btn btn-primary">{{ __('tasks.save_button') }}</button>
                                        <button type="button" class="btn btn-danger"
                                            onclick="delete_task();">{{ __('tasks.remove_button') }}</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <form method="POST" action="{{ route('tasks.destroy', $task_data[0]->id) }}" id="delete_form">
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
