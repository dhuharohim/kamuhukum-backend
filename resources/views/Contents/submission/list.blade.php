@extends('masterpage')

@section('page_title')
    Submissions
@endsection

@section('page_content')
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        <h4>Submission List</h4>
        <div class="table-responsive">
            <table class="table table-borderless table-striped table-hover" id="articleTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Article</th>
                        <th>Related Edition</th>
                        <th>Status</th>
                        @if ($isAdmin)
                            <th>Editor</th>
                        @endif
                        <th>Assigned On</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articles as $article)
                        <tr>
                            <td>
                                <p class="mb-0 text-truncate" style="max-width: 400px;">
                                    {{ \Illuminate\Support\Str::title($article->title) }}</p>
                                <ul class="list-unstyled mt-1">
                                    @foreach ($article->authors as $author)
                                        <li class="text-muted" style="font-size: 12px;">
                                            <span class="badge bg-secondary">{{ ucwords($author->contributor_role) }}</span>
                                            {{ $author->given_name }} {{ $author->family_name }}
                                            - {{ $author->affilation }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                @if ($article->edition)
                                    <span class="badge bg-primary">{{ $article->edition->name_edition }}</span>
                                @else
                                    <span class="badge bg-danger">No Edition</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badge = '';
                                    switch ($article->status):
                                        case 'incomplete':
                                            $badge = 'danger';
                                            break;
                                        case 'submission':
                                            $badge = 'warning';
                                            break;
                                        case 'review':
                                            $badge = 'info';
                                            break;
                                        default:
                                            break;
                                    endswitch;
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucwords($article->status) }}</span>
                            </td>
                            @if ($isAdmin)
                                <td>
                                    <small>

                                    </small>
                                </td>
                            @endif
                            <td>
                                {{-- <small>
                                    @if ($article->assigned_on)
                                        {{ !empty($article->assigned_on) ? date('d M Y h:i:s', strtotime($article->assigned_on)) : '' }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </small> --}}
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('submissions.show', ['submission' => $article->id]) }}"
                                        class="btn btn-outline-dark btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if ($isAdmin)
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#assignEditorModal" data-article-id="{{ $article->id }}">
                                            <i class="fas fa-user-plus"></i> Assign
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if ($isAdmin)
        <!-- Modal -->
        <div class="modal fade nftmax-preview__modal" id="assignEditorModal" tabindex="-1"
            aria-labelledby="assignEditorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered nftmax-close__modal-close">
                <div class="modal-content nftmax-preview__modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignEditorModalLabel">Assign Editor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body nftmax-modal__body modal-body nftmax-close__body p-4">
                        <!-- Add form for assigning editor here -->
                        <form id="assignEditorForm">
                            <input type="hidden" id="articleId" name="articleId">
                            <div class="mb-3">
                                <label for="editorSelect" class="form-label">Select Editor (Multiple)</label>
                                <select class="selectize" id="editorSelect" name="editorId[]" multiple required>
                                    <option value="">Select Editor</option>
                                    <!-- Populate this dropdown with available editors -->
                                    @foreach ($editors as $editor)
                                        <option value="{{ $editor->id }}">{{ $editor->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="notifyEmail" name="notifyEmail" checked>
                                <label class="form-check-label" for="notifyEmail">Notify via email</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="nftmax__item-button--group">
                            <button type="button"
                                class="text-black nftmax__item-button--single nftmax-btn nftmax-btn__bordered--plus radius"
                                data-bs-dismiss="modal">Close</button>
                            <button type="button"
                                class="text-black btn btn-primary nftmax__item-button--single nftmax-btn nftmax-btn__bordered--plus radius"
                                id="saveAssignment">
                                <span class="spinner-border spinner-border-sm d-none" role="status"
                                    aria-hidden="true"></span>
                                Save changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif
@endsection

@section('custom_js')
    <script>
        $(function() {
            // Handle the "Assign Editor" modal
            $('#assignEditorModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const articleId = button.data('article-id');
                $('#articleId').val(articleId);

                // Reset form fields
                $('#editorSelect').val('');
                $('#notifyEmail').prop('checked', true);
                // Reset selectize by destroying and reinitializing
                $('#editorSelect').selectize()[0].selectize.destroy();
                $('#editorSelect').selectize({
                    plugins: ['remove_button'],
                    delimiter: ',',
                    persist: false,
                    create: false
                });
            });

            // Handle the "Save changes" button click
            $('#saveAssignment').click(function() {
                var articleId = $('#articleId').val();
                var editorId = $('#editorSelect').val();
                var notifyEmail = $('#notifyEmail').is(':checked');

                // Show loading spinner
                $('#saveAssignment .spinner-border').removeClass('d-none');
                $('#saveAssignment').prop('disabled', true);

                $.ajax({
                    url: '{{ route('submissions.assignEditor') }}',
                    method: 'POST',
                    data: {
                        articleId: articleId,
                        editorId: editorId,
                        notifyEmail: notifyEmail ? true : false,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        var modal = bootstrap.Modal.getInstance(document.getElementById(
                            'assignEditorModal'));
                        modal.hide();
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topRight'
                        });
                        location.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON);
                        iziToast.error({
                            title: 'Error',
                            message: xhr.responseJSON.error ||
                                'An error occurred while assigning the editor.',
                            position: 'topRight'
                        });
                    },
                    complete: function() {
                        // Hide loading spinner
                        $('#saveAssignment .spinner-border').addClass('d-none');
                        $('#saveAssignment').prop('disabled', false);
                    }
                });
            });
        })
        let table = new DataTable('#articleTable');

        function confirmDelete(editionId, id) {
            iziToast.question({
                timeout: 0,
                close: false,
                overlay: false,
                displayMode: 'once',
                id: 'question',
                zindex: 1,
                title: 'Are you sure?',
                message: 'Do you really want to delete this article?',
                position: 'center',
                buttons: [
                    ['<button><b>Yes</b></button>', function(instance, toast) {
                        // Perform the delete action here
                        deleteArticle(editionId, id);

                        // Close the iziToast notification
                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                    }, true],
                    ['<button>No</button>', function(instance, toast) {
                        // Just close the iziToast notification
                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                    }]
                ]
            });
        }

        function deleteArticle(editionId, id) {
            $.ajax({
                url: '/articles/' + editionId + '/' + id,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    iziToast.success({
                        title: 'Deleted',
                        message: 'The item has been deleted successfully.',
                        position: 'topRight'
                    });
                    location.reload();
                },
                error: function(xhr, status, error) {
                    iziToast.error({
                        title: 'Error',
                        message: 'There was an error deleting the item.',
                        position: 'topRight'
                    });
                    location.reload();
                }
            });
        }
    </script>
@endsection
