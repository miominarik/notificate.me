@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('tasks.add_new_task') }}</div>

                    <div class="card-body">
                        <div class="container">
                            <form method="POST" action="{{ route('tasks.store') }}">
                                @csrf
                                @method('POST')
                                <div class="form-group row">
                                    <label for="task_name"
                                        class="col-sm-1-12 col-form-label">{{ __('tasks.task_name') }}</label>
                                    <div class="col-sm-1-12">
                                        <input type="text" class="form-control" name="task_name" id="task_name" required
                                            max="50">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="task_note"
                                        class="col-sm-1-12 col-form-label">{{ __('tasks.task_note') }}</label>
                                    <div class="col-sm-1-12">
                                        <input type="text" class="form-control" name="task_note" id="task_note" max="50">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="task_next_date"
                                        class="col-sm-1-12 col-form-label">{{ __('tasks.task_next_date') }}</label>
                                    <div class="col-sm-1-12">
                                        <input type="date" class="form-control" name="task_next_date" id="task_next_date"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-6">
                                        <label for="task_repeat_value"
                                            class="col-sm-1-12 col-form-label">{{ __('tasks.task_repeat_value') }}</label>
                                        <div class="col-sm-1-12">
                                            <input type="number" class="form-control" name="task_repeat_value"
                                                id="task_repeat_value" min="1" max="16777215" placeholder="{{ __('tasks.amount') }}" required>
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
                                        <button type="submit"
                                            class="btn btn-primary">{{ __('tasks.save_button') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
