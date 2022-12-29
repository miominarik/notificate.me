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
                                    <h3 class="mb-5">{{ __('files.all_files_head') }}</h3>
                                </div>
                                <div class="bd-highlight">
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
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>{{ __('files.file_name') }}</th>
                                            <th>{{ __('tasks.task_name') }}</th>
                                            <th>{{ __('files.size') }}</th>
                                            <th>{{ __('files.created_at') }}</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @isset($all_files)
                                            @forelse ($all_files as $file)
                                                <tr>
                                                    <td scope="row">
                                                        {{ $file->file_name }}</td>
                                                    <td scope="row">
                                                        {{ $file->task_name }}</td>
                                                    <td scope="row">
                                                        {{ $file->file_size }}
                                                    </td>
                                                    <td scope="row">
                                                        {{ $file->created_at}}
                                                    </td>
                                                    <td class="text-start"><i class="fa-solid fa-download"
                                                                              onclick="window.location.replace('{{route('files.download_file', $file->id)}}')"
                                                                              style="cursor: pointer;"></i>
                                                        <i class="fa-solid fa-trash text-danger"
                                                           onclick="window.location.replace('{{route('files.delete', $file->id)}}')"
                                                           style="cursor: pointer;"></i>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td scope="row">{{ __('files.no_files') }}</td>
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
                            @elseif ((new \Jenssegers\Agent\Agent())->isMobile() == TRUE || (new \Jenssegers\Agent\Agent())->isTable() == TRUE)
                                @isset($all_files)
                                    @forelse ($all_files as $file)
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="card mb-3" style="width: auto;">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{ $file->file_name }}</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted">{{ $file->task_name }}</h6>
                                                        <p class="card-text">
                                                            {{ __('files.size') }}:
                                                            {{ $file->file_size }}
                                                            <br>
                                                            {{ __('files.created_at') }}
                                                            {{ $file->created_at }}
                                                        </p>
                                                        <div class="d-flex justify-content-between">
                                                            <i class="fa-solid fa-download"
                                                               onclick="window.location.replace('{{route('files.download_file', $file->id)}}')"
                                                               style="cursor: pointer;"></i>
                                                            <i class="fa-solid fa-trash text-danger"
                                                               onclick="window.location.replace('{{route('files.delete', $file->id)}}')"
                                                               style="cursor: pointer;"></i>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="card mb-3" style="width: auto;">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{ __('files.no_files') }}</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted"></h6>
                                                        <p class="card-text"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                @endisset
                            @endif
                            {{ $all_files->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
