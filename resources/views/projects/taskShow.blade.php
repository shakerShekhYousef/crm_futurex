@php
    // $logo=\App\Models\Utility::get_file('users-avatar/');
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
@endphp
<div class="modal-body">
    @if ($currentWorkspace && $task)
        <div class="p-2">
            <div class="form-control-label">{{ __('Description') }}:</div>
            <p class="text-muted mb-4">
                {{ $task->description }}
            </p>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="form-control-label">{{ __('Create Date') }}</div>
                    <p class="mt-1">{{ \App\Models\Utility::dateFormat($task->created_at) }}</p>
                </div>
                <div class="col-md-3">
                    <div class="form-control-label">{{ __('Due Date') }}</div>
                    <p class="mt-1">{{ \App\Models\Utility::dateFormat($task->due_date) }}</p>
                </div>
                <div class="col-md-3">
                    <div class="form-control-label">{{ __('Assigned') }}</div>
                    @if ($users = $task->users())
                        @foreach ($users as $user)
                            <img @if ($user->avatar) src="{{ asset($logo . $user->avatar) }}" @else avatar="{{ $user->name }}" @endif
                                class="rounded-circle mt-1 w-20">
                        @endforeach
                    @endif
                </div>
                <div class="col-md-3">
                    <div class="form-control-label">{{ __('Milestone') }}</div>
                    @php($milestone = $task->milestone())
                    <p class="mt-1">
                        @if ($milestone) {{ $milestone->title }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs  bordar_styless mb-3" id="myTab" role="tablist">
            <li>
                <a class=" active" id="comments-tab" data-toggle="tab" href="#comments-data" role="tab"
                    aria-controls="home" aria-selected="false"> {{ __('Comments') }} </a>
            </li>
            <li class="annual-billing">
                <a id="file-tab" data-toggle="tab" href="#file-data" role="tab" aria-controls="profile"
                    aria-selected="false"> {{ __('Files') }} </a>
            </li>
            <li class="annual-billing">
                <a id="sub-task-tab" data-toggle="tab" href="#sub-task-data" role="tab" aria-controls="contact"
                    aria-selected="true"> {{ __('Sub Task') }} </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            {{-- for sending comments in task --}}
            <div class="tab-pane fade active show" id="comments-data" role="tabpanel" aria-labelledby="home-tab">
                <form method="post" id="form-comment"
                    data-action="{{ route($client_keyword . 'comment.store', [$currentWorkspace->slug, $task->project_id, $task->id, $clientID]) }}">
                    @if ($currentWorkspace->is_chagpt_enable())
                        <div class="row text-end pb-3">
                            <div class="col">
                                <a href="#" data-size="md" class="btn btn-primary btn-icon btn-sm"
                                    data-ajax-popup-over="true" id="grammarCheck"
                                    data-url="{{ route('grammar', ['grammar']) }}" data-bs-placement="top"
                                    data-title="{{ __('Grammar check with AI') }}">
                                    <i class="ti ti-rotate"></i> <span>{{ __('Grammar check with AI') }}</span></a>
                            </div>
                            <div class="col-auto">
                                <a href="#" data-size="lg" data-ajax-popup-over="true"
                                    class="btn btn-sm btn-primary" data-url="{{ route('generate', ['task show']) }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="{{ __('Generate with AI') }}"
                                    data-title="{{ __('Generate Task Massage') }}">
                                    <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}</a>
                            </div>
                        </div>
                    @endif
                    <textarea class="form-control form-control-light mb-2 grammer_textarea" name="comment"
                        placeholder="{{ __('Write message') }}" id="example-textarea" rows="3" required></textarea>
                    <div class="text-end">
                        <div class="btn-group mb-2 ml-2 d-sm-inline-block">
                            <button type="button" class="btn btn btn-primary">{{ __('Submit') }}</button>
                        </div>
                    </div>
                </form>
                <ul class="list-unstyled list-unstyled-border mt-3" id="task-comments">
                    @foreach ($task->comments as $comment)
                        <li class="media border-bottom mb-3">
                            <img class="mr-3 avatar-sm rounded-circle img-thumbnail" width=""
                                style="max-width: 30px; max-height: 30px;"
                                @if ($comment->user_type != 'Client') @if ($comment->user->avatar) src="{{ asset($logo . $comment->user->avatar) }}" @else avatar="{{ $comment->user->name }}" @endif
                            alt="{{ $comment->user->name }}" @else avatar="{{ $comment->client->name }}"
                                alt="{{ $comment->client->name }}" @endif />
                            <div class="media-body mb-2">
                                <div class="float-left">
                                    <h5 class="mt-0 mb-1 form-control-label">
                                        @if ($comment->user_type != 'Client')
                                            {{ $comment->user->name }}
                                        @else
                                            {{ $comment->client->name }} @endif
                                    </h5>
                                    {{ $comment->comment }}
                                </div>
                                @if (Auth::user()->id == $comment->created_by && $comment->user_type == 'Client')
                                    <div class="text-end">
                                        <a href="#" class=" btn-danger  btn btn-sm delete-comment"
                                            data-url="{{ route($client_keyword . 'comment.destroy', [$currentWorkspace->slug, $task->project_id, $task->id, $comment->id]) }}">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                @else
                                    @auth('web')
                                        <div class="text-end">
                                            <a href="#" class=" btn-danger  btn btn-sm delete-comment"
                                                data-url="{{ route('comment.destroy', [$currentWorkspace->slug, $task->project_id, $task->id, $comment->id]) }}">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                        </div>
                                    @endauth
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            {{-- end --}}

            {{-- for sending files in task --}}
            <div class="tab-pane fade" id="file-data" role="tabpanel" aria-labelledby="profile-tab">
                <div class="form-group m-0">
                    <form method="post" id="form-file" enctype="multipart/form-data"
                        data-url="{{ route($client_keyword . 'comment.store.file', [$currentWorkspace->slug, $task->project_id, $task->id, $clientID]) }}">

                        @csrf

                        <div class="choose-file mt-3">
                            <label for="file" class="">
                                <div class="logo-content">
                                    <img src="{{ asset($logo_tasks . 'sample.jpg') }}" class="preview_img_size"
                                        id="task_file" />
                                </div>
                                <div class=" bg-primary"> <i
                                        class="ti ti-upload px-1"></i>{{ __('Choose file here') }}</div>
                                <input type="file" class="form-control choose_file_custom" name="file"
                                    id="file" data-filename="file_create">
                                <span class="invalid-feedback" id="file-error" role="alert">
                                    <strong></strong>
                                </span>
                            </label>
                            <!--  <p class="file_create"></p> -->
                        </div>

                        <div class="text-end">
                            <div class="">
                                <button type="submit" class="btn btn-primary">{{ __('Upload') }}</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="comments-file" class="mt-3">
                    @foreach ($task->taskFiles as $file)
                        <div class="card pb-0 mb-1 shadow-none border">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar-sm">
                                            @if (in_array($file->extension, ['.jpg', '.jpeg', '.png', '.gif', '.bmp']))
                                                <span class="avatar-title rounded">
                                                    <img src="{{ asset($logo_tasks . $file->file) }}"
                                                        class="preview_img_size" id="" />
                                                    {{-- {{$file->extension}} --}}
                                                </span>
                                            @else
                                                <span class="avatar-title rounded">
                                                    <img src="{{ asset($logo_tasks . 'sample-file.png') }}"
                                                        class="preview_img_size" id="" />
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col pl-0">
                                        <a href="#"
                                            class="text-muted form-control-label">{{ $file->name }}</a>
                                        <p class="mb-0">{{ $file->file_size }}</p>
                                    </div>
                                    <div class="col-auto">
                                        <!-- Button -->
                                        <a download href="{{ $logo_tasks . $file->file }}"
                                            class="btn-primary  btn btn-sm">
                                            <i class="ti ti-download" data-toggle="tooltip"
                                                title="{{ __('Download') }}"></i>
                                        </a>

                                        <a class="btn-secondary  btn btn-sm" href="{{ $logo_tasks . $file->file }}"
                                            target="_blank">
                                            <i class="ti ti-crosshair text-white" data-toggle="tooltip"
                                                title="{{ __('Preview') }}"></i>
                                        </a>

                                        @auth('web')
                                            <a href="#" class="btn-danger  btn btn-sm delete-comment-file"
                                                data-url="{{ route('comment.destroy.file', [$currentWorkspace->slug, $task->project_id, $task->id, $file->id]) }}">
                                                <i class="ti ti-trash" data-toggle="tooltip"
                                                    title="{{ __('Delete') }}"></i>
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- end --}}

            {{-- subtask --}}
            <div class="tab-pane fade mt-3" id="sub-task-data" role="tabpanel" aria-labelledby="contact-tab">

                <div class="text-end mb-3">
                    <a href="#" class="btn btn-sm btn-primary" data-toggle="collapse"
                        data-target="#form-subtask"> <i class="ti ti-plus"></i></a>
                </div>
                <form method="post" id="form-subtask" class="collapse"
                    data-action="{{ route($client_keyword . 'subtask.store', [$currentWorkspace->slug, $task->project_id, $task->id, $clientID]) }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">{{ __('Name') }}</label>
                                    <input type="text" name="name" class="form-control" required
                                        placeholder="{{ __('Sub Task Name') }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">{{ __('Due Date') }}</label>
                                    <input class="form-control datepicker2" type="text" id="due_date"
                                        name="due_date" autocomplete="off" required="required">
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="btn-group mb-2 ml-2 d-sm-inline-block">
                                <button type="submit"
                                    class="btn btn-primary create-subtask">{{ __('Add Subtask') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <ul class="list-group mt-3" id="subtasks">
                    @foreach ($task->sub_tasks as $subTask)
                        <li class="list-group-item py-3">
                            <div class="form-check form-switch d-inline-block">
                                <input type="checkbox" class="form-check-input" name="option"
                                    id="option{{ $subTask->id }}" @if ($subTask->status) checked @endif
                                    data-url="{{ route($client_keyword . 'subtask.update', [$currentWorkspace->slug, $task->project_id, $subTask->id]) }}">
                                <label class="custom-control-label form-control-label"
                                    for="option{{ $subTask->id }}">{{ $subTask->name }}</label>
                            </div>
                            <div class="text-end row_line_style">
                                <a href="#" class="btn-danger  btn btn-sm delete-subtask"
                                    data-url="{{ route($client_keyword . 'subtask.destroy', [$currentWorkspace->slug, $task->project_id, $subTask->id]) }}">
                                    <i class="ti ti-trash"></i>
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            {{-- end --}}
        </div>
    @else
        <div class="container mt-5">
            <div class="card">
                <div class="card-body p-4">
                    <div class="page-error">
                        <div class="page-inner">
                            <h1>404</h1>
                            <div class="page-description">
                                {{ __('Page Not Found') }}
                            </div>
                            <div class="page-search">
                                <p class="text-muted mt-3">
                                    {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
                                </p>
                                <div class="mt-3">
                                    <a class="btn-return-home badge-blue" href="{{ route('home') }}"><i
                                            class="fas fa-reply"></i> {{ __('Return Home') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    (function() {
        const d_week = new Datepicker(document.querySelector('.datepicker2'), {
            buttonClass: 'btn',
            todayBtn: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
        });
    })();
</script>



<script type="text/javascript">
    $('#file').change(function() {
        let fileInput = this;

        // Check if the file is an image
        if (fileInput.files && fileInput.files[0]) {
            let fileType = fileInput.files[0].type;

            if (fileType.startsWith('image/')) {
                // If it's an image, load and display it
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#task_file').attr('src', e.target.result);
                }
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                // If it's not an image, show the default image
                $('#task_file').attr('src', '{{ asset($logo_tasks . 'sample-file.png') }}');
            }
        }
    });
</script>
