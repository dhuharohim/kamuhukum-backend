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
                        <th>
                            @if ($isAdmin)
                                Editor
                            @else
                                Assigned
                            @endif
                        </th>
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
                            <td>
                                @if ($isAdmin)
                                    <button class="btn btn-primary btn-sm seeEditorsButton"
                                        data-article-id="{{ $article->id }}">
                                        See Editors
                                    </button>
                                @elseif ($article->editors()->where('user_id', Auth::id())->exists())
                                    <small class="text-muted">
                                        {{ $article->editors()->where('user_id', Auth::id())->first()->created_at->format('d M Y H:i:s') }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('submissions.show', ['submission' => $article->id]) }}"
                                        class="btn btn-outline-dark btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if ($isAdmin)
                                        <button type="button" class="btn btn-primary btn-sm assignEditor"
                                            data-article-id="{{ $article->id }}">
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
        </div>
    @endif

    {{-- Modal for see Editors --}}
    <div class="modal fade nftmax-preview__modal" id="seeEditorsModal" tabindex="-1" aria-labelledby="seeEditorsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered nftmax-close__modal-close">
            <div class="modal-content nftmax-preview__modal-content" style="width: 100% !important;">
                <div class="modal-header">
                    <h5 class="modal-title" id="seeEditorsModalLabel">Editor List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body nftmax-modal__body modal-body nftmax-close__body p-4">
                    <!-- Add form for assigning editor here -->
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped table-hover" style="font-size: 14px;"
                            id="seeEditorsTable">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Assigned On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
    <script>
        $(function() {
            $('.seeEditorsButton').on('click', function(event) {
                event.preventDefault();
                $(this).prop('disabled', true);
                $(this).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );
                const articleId = $(this).data('article-id');
                $.ajax({
                    url: '{{ route('submissions.getEditors', ['articleId' => ':articleId']) }}'
                        .replace(':articleId', articleId),
                    method: 'GET',
                    success: function(response) {
                        $('#seeEditorsModal').modal('show');
                        $('.spinner-border').addClass('d-none');
                        $('.seeEditorsButton').prop('disabled', false);
                        $('.seeEditorsButton').html('See Editors');
                        setTimeout(() => {
                            if (response.editors.length === 0) {
                                $('#seeEditorsTable tbody').html(
                                    '<tr><td colspan="4" class="text-center">No editors assigned yet</td></tr>'
                                );
                                return;
                            }

                            $('#seeEditorsTable tbody').empty();
                            response.editors.forEach(function(editor) {
                                $('#seeEditorsTable tbody').append(
                                    '<tr><td>' + editor.username +
                                    '</td><td>' +
                                    editor.email +
                                    '</td><td>' + new Date(editor.pivot
                                        .assigned_on).toLocaleString(
                                        'en-GB') +
                                    '</td><td><button class="btn btn-sm btn-danger remove-editor" data-editor-id="' +
                                    editor.id + '" data-article-id="' +
                                    articleId +
                                    '"><i class="fas fa-trash-alt"></i></button>' +
                                    '</td></tr>');
                            });
                        }, 10);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON);
                    }
                });
            });

            $('.assignEditor').on('click', function(event) {
                event.preventDefault();
                const articleId = $(this).data('article-id');

                // Open the modal first
                $('#assignEditorModal').modal('show');
                $('#articleId').val(articleId);

                // Initialize Selectize with a loading placeholder
                setTimeout(() => {
                    let selectize = $('#editorSelect')[0].selectize;
                    if (selectize) {
                        selectize.destroy();
                    }

                    selectize = $('#editorSelect').selectize({
                        create: true,
                        sortField: 'text',
                        placeholder: 'Loading editors',
                    })[0].selectize;

                    // Fetch options via AJAX
                    $.ajax({
                        url: '{{ route('submissions.getEditorsAvail', ['articleId' => ':articleId']) }}'
                            .replace(':articleId', articleId),
                        method: 'GET',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            // Add fetched editors to Selectize
                            response.forEach(function(editor) {
                                selectize.addOption({
                                    value: editor.id,
                                    text: editor.username
                                });
                            });

                            selectize.settings.placeholder = 'Select editors';
                            selectize.updatePlaceholder();
                            selectize.refreshOptions();
                        },
                        error: function(xhr) {
                            console.error('Failed to fetch editors:', xhr.responseText);
                            alert('Error loading editors. Please try again.');
                        }
                    });
                }, 10);
            });


            // Handle the "Remove Editor" button click
            $(document).on('click', '.remove-editor', function(event) {
                event.preventDefault();
                const editorId = $(this).data('editor-id');
                const articleId = $(this).data('article-id');
                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 9000001,
                    title: 'Confirm',
                    message: 'Are you sure you want to remove this editor?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');

                            $.ajax({
                                url: '/remove-editor/' + editorId,
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    article_id: articleId
                                },
                                success: function(response) {
                                    iziToast.success({
                                        title: 'Success',
                                        message: 'Editor removed successfully',
                                        position: 'topRight'
                                    });
                                    location.reload();
                                },
                                error: function(xhr) {
                                    iziToast.error({
                                        title: 'Error',
                                        message: xhr.responseJSON
                                            .message ||
                                            'Failed to remove editor',
                                        position: 'topRight'
                                    });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                        }],
                    ]
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
