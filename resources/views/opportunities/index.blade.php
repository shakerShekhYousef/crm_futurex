@extends('layouts.admin')

@section('page-title')
    {{ __('Opportunities') }}
@endsection

@section('links')
    <li class="breadcrumb-item"><a href="#">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('opportunities') }}</li>
@endsection

@section('action-button')
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-primary filter" data-bs-toggle="tooltip" title="{{ __('Filter') }}">
            <i class="ti ti-filter"></i>
        </button>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">
            <i class="ti ti-upload"></i> {{ __('Import Excel') }}
        </button>
        <!-- New Create Button -->
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="ti ti-plus"></i> {{ __('Create New') }}
        </button>
        <!-- Download Example Button -->
        <a href="{{ route('opportunities.download.example', $currentWorkspace) }}" class="btn btn-sm btn-secondary">
            <i class="ti ti-download"></i> {{ __('Example') }}
        </a>
    </div>
@endsection

@push('css-page')
    <style>
        .action-buttons .btn {
            margin: 0 2px;
            padding: 5px 8px;
        }

        .progress-bar {
            transition: width 0.3s ease;
        }

        .progress-bar-striped {
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-text {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">{{ __('Create New Opportunity') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('candidateclients.create', $currentWorkspace) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Name') }}</label>
                                <input name="name" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Phone') }}</label>
                                <input name="phone" type="tel" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Company') }}</label>
                                <input name="company_name" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Email') }}</label>
                                <input name="email" type="email" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Followup History Modal -->
    <div class="modal fade" id="followupHistoryModal" tabindex="-1" aria-labelledby="followupHistoryModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="followupHistoryModalLabel">{{ __('Follow-up History') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Method') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Notes') }}</th>
                                <th>{{ __('Future Notes') }}</th>
                                <th>{{ __('Next Follow-up') }}</th>
                            </tr>
                            </thead>
                            <tbody id="followupHistoryBody">
                            <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Excel Upload Modal -->
    <!-- Excel Upload Modal -->
    <div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadExcelModalLabel">{{ __('Import Excel File') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="excelUploadForm"
                      action="{{ route('candidateclients.upload') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-4">
                            <input type="hidden" name="workspace" value="{{ $currentWorkspace->slug }}">
                            <label for="excelFile" class="form-label">{{ __('Select Excel File') }}</label>
                            <input class="form-control form-control-lg"
                                   type="file"
                                   id="excelFile"
                                   name="file"
                                   accept=".xlsx,.xls,.csv"
                                   required>
                            <div class="form-text">{{ __('Maximum file size: 256MB') }}</div>
                        </div>

                        <!-- Progress Container -->
                        <div class="upload-progress" style="display: none;">
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped bg-success"
                                     role="progressbar"
                                     style="width: 0%">
                                    <span class="progress-text">0%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Error Container -->
                        <div id="uploadErrors" class="d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="uploadButton">
                            <i class="ti ti-upload me-2"></i>{{ __('Upload') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Follow-up Modal -->
    <div class="modal fade" id="followupModal" tabindex="-1" aria-labelledby="followupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="followupModalLabel">{{ __('Add New Follow-up') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="followupForm" action="{{ route('opportunities.store', $currentWorkspace) }}" method="POST">
                    @csrf
                    <!-- Remove @method("POST") as it's redundant when using method="post" -->

                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Client Information -->
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Client') }}</label>
                                <!-- Add hidden input for client ID -->
                                <input type="hidden" name="candidate_client_id" id="candidate_client_id">
                                <input type="text" class="form-control"
                                       id="name" name="name" readonly>
                            </div>

                            <!-- Follow-up Date -->
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Follow-up Date') }}</label>
                                <div class="input-group">
                                    <input type="date" class="form-control"
                                           name="follow_up_date" required>
                                </div>
                            </div>

                            <!-- Communication Method -->
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Communication Method') }}</label>
                                <select class="form-select communication-method"
                                        name="contact_method" required>
                                    <option value="">{{ __('Select Method') }}</option>
                                    <option value="phone">{{ __('Phone Call') }}</option>
                                    <option value="email">{{ __('Email') }}</option>
                                    <option value="meeting">{{ __('In-person Meeting') }}</option>
                                    <option value="whatsapp">{{ __('Whatsapp') }}</option>
                                    <option value="other">{{ __('Other') }}</option>
                                </select>
                                <div class="other-input-container mt-2" style="display: none;">
                                    <input type="text" class="form-control other-method-input"
                                           name="other_contact_method"
                                           placeholder="{{ __('Specify communication method') }}">
                                </div>
                            </div>

                            <!-- Client Status -->
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Client Status') }}</label>
                                <select class="form-select client-status"
                                        name="status" required>
                                    <option value="">{{ __('Select Status') }}</option>
                                    <option value="wait offer">{{ __('Wait Offer') }}</option>
                                    <option value="not interested">{{ __('Not Interested') }}</option>
                                    <option value="follow up">{{ __('Follow Up') }}</option>
                                    <!-- Fix typo: need_metting → need_meeting -->
                                    <option value="need meeting">{{ __('Need a Meeting') }}</option>
                                    <option value="other">{{ __('Other') }}</option>
                                </select>
                                <div class="other-input-container mt-2" style="display: none;">
                                    <input type="text" class="form-control other-status-input"
                                           name="other_status"
                                           placeholder="{{ __('Specify client status') }}">
                                </div>
                            </div>

                            <!-- Notes Sections -->
                            <div class="col-12">
                                <label class="form-label">{{ __('Administrative Notes') }}</label>
                                <textarea class="form-control"
                                          name="current_notes" rows="3" required></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ __('Next Follow-up Notes') }}</label>
                                <textarea class="form-control"
                                          name="future_notes" rows="3" required></textarea>
                            </div>

                            <!-- Next Follow-up Date -->
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Next Follow-up Date') }}</label>
                                <input type="date" class="form-control"
                                       name="next_follow_up_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Follow-up') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">{{ __('Edit Opportunity') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post" action="{{route('opportunities.update',$currentWorkspace)}}">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <input name="id" type="hidden" id="editId">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Name') }}</label>
                                <input readonly name="name" type="text" class="form-control" id="editName" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Phone') }}</label>
                                <input name="phone" type="tel" class="form-control" id="editPhone" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Company') }}</label>
                                <input name="company_name" type="text" class="form-control" id="editCompany" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Email') }}</label>
                                <input name="email" type="email" class="form-control" id="editEmail" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Main Content -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="opportunities-table">
                    <thead class="table-light">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Company') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Sample Data -->
                    @foreach($candidateclients as $candidateclient)
                        <tr>
                            <td>{{$candidateclient->name}}</td>
                            <td>{{$candidateclient->phone}}</td>
                            <td>{{$candidateclient->company_name}}</td>
                            <td>{{$candidateclient->email}}</td>
                            <td>
                                <div class="action-buttons d-flex">
                                    <button class="btn btn-sm btn-icon btn-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#followupHistoryModal"
                                            data-client-id="{{ $candidateclient->id }}"
                                            data-workspace="{{ $currentWorkspace->name }}"
                                            title="{{ __('View History') }}">

                                        <i class="ti ti-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-info edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $candidateclient->id }}"
                                            data-name="{{ $candidateclient->name }}"
                                            data-phone="{{ $candidateclient->phone }}"
                                            data-company="{{ $candidateclient->company_name }}"
                                            data-email="{{ $candidateclient->email }}"
                                            title="{{ __('Edit') }}">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon"
                                            style="color: aliceblue;background-color: #801c47;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#followupModal"
                                            data-name="{{ $candidateclient->name }}"
                                            data-client-id="{{ $candidateclient->id }}"
                                            title="{{ __('Add Follow-up') }}">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
   <script>
       document.addEventListener('DOMContentLoaded', function () {
           const form = document.getElementById('excelUploadForm');
           const progressContainer = document.querySelector('.upload-progress');
           const progressBar = document.querySelector('.progress-bar');
           const progressText = document.querySelector('.progress-text');
           const uploadButton = document.getElementById('uploadButton');
           const errorContainer = document.getElementById('uploadErrors');

           form.addEventListener('submit', async function (e) {
               e.preventDefault();
               const formData = new FormData(form);
               const xhr = new XMLHttpRequest();

               // Reset UI state
               errorContainer.classList.add('d-none');
               errorContainer.innerHTML = '';
               progressContainer.style.display = 'block';
               uploadButton.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status"></span>
            {{ __('Uploading...') }}
               `;
               uploadButton.disabled = true;

               xhr.upload.addEventListener('progress', function (e) {
                   if (e.lengthComputable) {
                       const percent = Math.round((e.loaded / e.total) * 100);
                       progressBar.style.width = percent + '%';
                       progressText.textContent = percent + '%';
                       if (percent >= 95) {
                           progressBar.style.transition = 'width 1s ease';
                       }
                   }
               });

               xhr.addEventListener('load', function () {
                   try {
                       const response = JSON.parse(xhr.responseText);
                       if (xhr.status >= 200 && xhr.status < 300) {
                           handleSuccess(response);
                       } else {
                           handleError({
                               status: xhr.status,
                               responseText: xhr.responseText
                           });
                       }
                   } catch (e) {
                       handleError({
                           status: xhr.status,
                           responseText: JSON.stringify({
                               message: 'Invalid server response format'
                           })
                       });
                   }
               });

               xhr.addEventListener('error', function () {
                   handleError({
                       status: 0,
                       responseText: JSON.stringify({
                           message: 'Network error - failed to connect to server'
                       })
                   });
               });

               xhr.open('POST', form.action);
               xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
               xhr.setRequestHeader('Accept', 'application/json');
               xhr.send(formData);
           });

           function handleSuccess(response) {
               const successHtml = `
            <div class="alert alert-success mt-3">
                ${response.message}
                <div class="mt-2">
                    <strong>${response.stats.imported}</strong> {{ __('records imported') }}<br>
                    ${response.stats.skipped ? `<strong>${response.stats.skipped}</strong> {{ __('records skipped') }}` : ''}
                </div>
            </div>
        `;

               errorContainer.innerHTML = successHtml;
               errorContainer.classList.remove('d-none', 'alert-danger');
               errorContainer.classList.add('alert-success');

               setTimeout(() => {
                   $('#uploadExcelModal').modal('hide');
                   window.location.reload();
               }, 2000);
           }

           function handleError(error) {
               let errorMessage = '{{ __("An error occurred during upload") }}';
               let errorDetails = '';

               // Handle different error types
               switch (error.status) {
                   case 0:
                       errorMessage = '{{ __("Network Error") }}';
                       errorDetails = '{{ __("Please check your internet connection") }}';
                       break;
                   case 404:
                       errorMessage = '{{ __("Resource Not Found") }}';
                       errorDetails = '{{ __("The upload endpoint could not be found") }}';
                       break;
                   case 413:
                       errorMessage = '{{ __("File Too Large") }}';
                       errorDetails = '{{ __("Maximum file size is 256MB") }}';
                       break;
                   case 422:
                       try {
                           const response = JSON.parse(error.responseText);
                           errorMessage = response.message || errorMessage;
                           if (response.errors) {
                               errorDetails = '<ul class="mb-0">';
                               Object.values(response.errors).forEach(errors => {
                                   errors.forEach(error => errorDetails += `<li>${error}</li>`);
                               });
                               errorDetails += '</ul>';
                           }
                       } catch (e) {
                           errorDetails = '{{ __("Invalid file format or structure") }}';
                       }
                       break;
                   case 500:
                       errorMessage = '{{ __("Server Error") }}';
                       errorDetails = '{{ __("Please try again later") }}';
                       break;
               }

               const errorHtml = `
            <div class="alert alert-danger">
                <strong>${errorMessage}</strong>
                ${errorDetails}
            </div>
        `;

               errorContainer.innerHTML = errorHtml;
               errorContainer.classList.remove('d-none');
               progressContainer.style.display = 'none';
               uploadButton.disabled = false;
               uploadButton.innerHTML = `<i class="ti ti-upload me-2"></i>{{ __('Upload') }}`;
               progressBar.style.width = '0%';
               progressText.textContent = '0%';
           }

           $('#uploadExcelModal').on('hidden.bs.modal', function () {
               form.reset();
               progressBar.style.width = '0%';
               progressText.textContent = '0%';
               errorContainer.classList.add('d-none');
               errorContainer.innerHTML = '';
               uploadButton.disabled = false;
               uploadButton.innerHTML = `<i class="ti ti-upload me-2"></i>{{ __('Upload') }}`;
           });
       });
   </script>

    <script>

        $('#followupHistoryModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const clientId = button.data('client-id');

        });
    </script>

    <script>
        $('#editModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const clientId = button.data('client-id');
        });

        $('#editModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const modal = $(this);

            // Extract data from button attributes
            const id = button.data('id');
            modal.find('#editId').val(id);
            modal.find('#editName').val(button.data('name'));
            modal.find('#editPhone').val(button.data('phone'));
            modal.find('#editCompany').val(button.data('company'));
            modal.find('#editEmail').val(button.data('email'));

            // Update form action
            const action = "{{ route('opportunities.update', [$currentWorkspace->name, 'id']) }}".replace('id', id);
            modal.find('#editForm').attr('action', action);
        });
    </script>

    <!-- Required JavaScript -->
    <script>
        $(document).ready(function () {
            // Handle modal show event
            $('#followupModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const modal = $(this);

                // Set client information
                modal.find('#name').val(button.data('name'));
                modal.find('#candidate_client_id').val(button.data('client-id'));

                // Update form action if needed
                const action = "{{ route('opportunities.store', $currentWorkspace) }}";
                modal.find('#followupForm').attr('action', action);
            });

            // Handle communication method changes
            $('.communication-method').change(function () {
                const container = $(this).siblings('.other-input-container');
                const input = container.find('input');
                container.toggle($(this).val() === 'other');
                input.prop('required', $(this).val() === 'other');
            });

            // Handle client status changes
            $('.client-status').change(function () {
                const container = $(this).siblings('.other-input-container');
                const input = container.find('input');
                container.toggle($(this).val() === 'other');
                input.prop('required', $(this).val() === 'other');
            });
        });
    </script>

    <script>
        document.getElementById('followupHistoryModal').addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const clientId = button.getAttribute('data-client-id');
            const modal = this;

            // Get slug from current URL path
            const slug = window.location.pathname.split('/')[1];

            // Clear previous content
            modal.querySelector('#followupHistoryBody').innerHTML = '<tr><td colspan="5" class="text-center">Loading...</td></tr>';

            // Fetch follow-up history using the opportunities.show route
            fetch(`/${slug}/opportunities/${clientId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let html = '';
                    data.forEach(followup => {
                        html += `
                    <tr>
                        <td>${followup.follow_up_date}</td>
                        <td><span class="badge bg-info">${followup.contact_method}</span></td>
                        <td>${followup.status}</td>
                        <td>${followup.current_notes}</td>
                        <td>${followup.future_notes}</td>
                        <td>${followup.next_follow_up_date}</td>
                    </tr>
                `;
                    });

                    modal.querySelector('#followupHistoryBody').innerHTML = html ||
                        '<tr><td colspan="5" class="text-center">No follow-up history found</td></tr>';
                })
                .catch(error => {
                    console.error('Error:', error);
                    modal.querySelector('#followupHistoryBody').innerHTML =
                        '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
                });
        });
    </script>
@endpush()
