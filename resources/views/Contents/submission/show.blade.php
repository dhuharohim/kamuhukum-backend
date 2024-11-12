@extends('masterpage')

@section('page_title')
    Edit Article
@endsection

@section('page_content')
    <style>
        p {
            color: black !important;
            margin-bottom: 0 !important;
        }

        .selectize-input {
            border-radius: 36px;
            height: 3rem;
            align-items: center;
            display: flex;
            padding: 1rem !important;
        }

        @media(max-width:768px) {
            .offcanvas-end {
                width: 100% !important;
            }
        }

        .offcanvas-header {
            padding: 20px !important;
        }

        .offcanvas-header .btn-close {
            top: 25px !important;
        }
    </style>
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        @if (in_array($article->status, ['submission', 'incomplete']))
            <a class="btn btn-outline-primary mb-2" href="{{ route('submissions.index') }}"><- Submission List</a>
        @endif
        <nav aria-label="breadcrumb">
            @if ($article->edition)
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('editions.index') }}">Editions</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('articles.index', $article->edition->id) }}">Article List on
                            {{ $article->edition->edition_name_formatted }}</a></li>
                    <li class="breadcrumb-item text-truncate w-50" aria-current="page">View
                        {{ \Illuminate\Support\Str::title($article->title) }}
                    </li>
                </ol>
            @endif
        </nav>
        <div class="card-body">
            <!-- Assuming $article contains the article data to be edited -->
            <div class="discussions">
                <h4>Discussions</h4>
                <button class="btn btn-warning btn-sm" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasComments" aria-controls="offcanvasComments">List of Discussions</button>
            </div>
            <form class="nftmax-wc__form-main"
                @if ($article->edition) action="{{ route('articles.update', ['editionId' => $article->edition->id, 'article' => $article->id]) }}"
                @else
                action="{{ route('submissions.update', ['submission' => $article->id]) }}" @endif
                method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edition">Edition</label>
                            <select name="edition" id="edition">
                                <option value="">Select Edition</option>
                                @foreach ($editions as $edition)
                                    <option value="{{ $edition->id }}"
                                        {{ old('edition', $article->edition->id ?? '') == $edition->id ? 'selected' : '' }}>
                                        {{ $edition->edition_name_formatted }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status<sup>*</sup></label>
                            @php
                                $statuses = ['incomplete', 'submission', 'review', 'production'];
                            @endphp
                            <select name="status" id="status" required>
                                @foreach ($statuses as $status)
                                    <option
                                        value="{{ $status }}"{{ old('status', $article->status) == $status ? ' selected' : '' }}>
                                        {{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="prefix">Prefix</label>
                            <input type="text" name="prefix" id="prefix"
                                value="{{ old('prefix', $article->prefix) }}">
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="title">Title <sup>*</sup></label>
                            <input type="text" name="title" id="title" required
                                value="{{ old('title', $article->title) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="section">Section<sup>*</sup></label>
                            @php
                                $sections = ['article' => 'Article', 'general_article' => 'General'];
                            @endphp
                            <select name="section" id="section" required>
                                @foreach ($sections as $key => $section)
                                    <option value="{{ $key }}" {{ old('section') == $key ? 'selected' : '' }}>
                                        {{ $section }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="subtitle">Subtitle</label>
                            <input type="text" name="subtitle" id="subtitle"
                                value="{{ old('subtitle', $article->subtitle) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">URL Custom (For SEO and Google Scholar Support)</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                placeholder="leave it blank to generate automaticly"
                                value="{{ old('slug', $article->slug) }}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="abstract">Abstract<sup>*</sup></label>
                    <div id="editor"></div>
                    <input type="hidden" name="abstract" id="abstract" required
                        value="{{ old('abstract', $article->abstract) }}" />
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="keywords">Keywords</label>
                                @php
                                    $keywordsVal = $article->keywords->pluck('keyword')->implode(', ');
                                @endphp
                                <select id="keywords" multiple>
                                    @foreach ($article->keywords as $keyword)
                                        <option value="{{ $keyword->keyword }}" selected>{{ $keyword->keyword }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="keywords" id="keywordsHidden"
                                    value="{{ old('keywords', $keywordsVal) }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if ($article->status == 'production')
                            <div class="form-group">
                                <label for="doi_link">DOI Link</label>
                                <input type="text" name="doi_link" id="doi_link"
                                    value="{{ old('doi_link', $article->doi_link) }}" disabled>
                            </div>
                        @endif
                    </div>
                </div>


                <!-- File upload section -->
                <div class="files-wrapper mt-4">
                    <div class="header">
                        <h4>Files<sup>*</sup></h4>
                    </div>
                    {{-- <div class="row w-100 align-items-center">
                        <div class="col-md-4">
                            <input type="file" id="file" class="align-content-center">
                        </div>
                        <div class="col-md-8">
                            <button class="btn btn-info" id="addFile" type="button">Add File</button>
                        </div>
                    </div> --}}
                    <div class="table-responsive mt-4">
                        <table class="table table-borderless table-hover table-striped" id="formFile">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th>File Name</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;">
                                @php
                                    $typeFiles = [
                                        'Article Text',
                                        'Plagiarism Report',
                                        'Research Instrument',
                                        'Research Result',
                                        'Transcripts',
                                        'Data Analysis',
                                        'Data Set',
                                        'Source Texts',
                                        'Other',
                                    ];
                                @endphp
                                @foreach ($article->files as $indexFile => $file)
                                    <tr data-index="{{ $indexFile }}">
                                        <td width="60%">
                                            <a>{{ $file->file_name }}</a>
                                        </td>
                                        <td>
                                            <select name="file_type[]" id="fileType" class="form-control">
                                                @foreach ($typeFiles as $type)
                                                    <option value="{{ $type }}"
                                                        {{ $file->type == $type ? 'selected' : '' }}>
                                                        {{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <a href="{{ $file->signed_file_path }}" target="_blank" type="button"
                                                class="btn btn-info viewFile btn-sm">View</a>
                                        </td>
                                        <td style="display: none">
                                            <input type="hidden" name="existing_files[]" value="{{ $file->id }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="contributor-wrapper mt-5">
                    <div class="header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">List of Contributors<sup>*</sup></h4>
                        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" id="addContributor"
                            data-bs-target="#offCanvasContributor" aria-controls="offCanvasContributor">Add
                            Contributor</button>
                    </div>
                    <div class="contributor-form table-responsive table-striped mt-3" id="contributorList">
                        <table class="table table-borderless table-hover" id="contributorList">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Name</th>
                                    <th>Contact (Email - Phone)</th>
                                    <th>Role</th>
                                    {{-- <th>Principal Contact</th> --}}
                                    {{-- <th>In Browse List</th> --}}
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;">
                                @foreach ($article->authors as $index => $c)
                                    <tr>
                                        <td>{{ $c->name_formatted }}</td>
                                        <td>{{ $c->email }} - {{ $c->phone ? $c->phone : 'No Phone' }}</td>
                                        <td>{{ ucfirst($c->contributor_role) }}</td>
                                        {{-- <td>{{ $c->principal_contact == '1' ? 'on' : 'off' }}</td>
                                        <td>{{ $c->in_browse_list == '1' ? 'on' : 'off' }}</td> --}}
                                        <td class="text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button class="btn btn-info btn-sm editRow" type="button">Edit</button>
                                                <button class="btn btn-danger btn-sm deleteRow"
                                                    type="button">Delete</button>
                                            </div>
                                        </td>
                                        <input type="hidden" data-index="{{ $index }}"
                                            name="contributors[{{ $index }}][given_name]"
                                            value="{{ $c['given_name'] }}" />
                                        <input type="hidden" name="contributors[{{ $index }}][family_name]"
                                            value="{{ $c['family_name'] }}" />
                                        <input type="hidden" name="contributors[{{ $index }}][preferred_name]"
                                            value="{{ $c['preferred_name'] }}" />
                                        <input type="hidden" name="contributors[{{ $index }}][email]"
                                            value="{{ $c['email'] }}" />
                                        <input type="hidden" name="contributors[{{ $index }}][phone]"
                                            value="{{ $c['phone'] }}" />
                                        <input type="hidden" name="contributors[{{ $index }}][affilation]"
                                            value="{{ $c['affilation'] }}" />
                                        <input type="hidden" name="contributors[{{ $index }}][country]"
                                            value="{{ $c['country'] }}" />
                                        <input type="hidden" name="contributors[{{ $index }}][homepage_url]"
                                            value="{{ $c['homepage_url'] }}" />
                                        <input type="hidden" name="contributors[{{ $index }}][orcid_id]"
                                            value="{{ $c['orcid_id'] }}" />
                                        <input type="hidden" name="contributors[{{ $index }}][bio_statement]"
                                            value="{{ $c['bio_statement'] }}" />
                                        <input type="hidden" name="contributors[{{ $index }}][role]"
                                            value="{{ $c['contributor_role'] }}" />
                                        {{-- <input type="hidden" name="contributors[{{ $index }}][principal_contact]"
                                            value="{{ $c['principal_contact'] }}" />
                                        <input type="hidden" name="contributors[{{ $index }}][in_browse_list]"
                                            value="{{ $c['in_browse_list'] }}" /> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="reference-wrapper mt-3">
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <h4 class="mb-0">Reference</h4>
                        <button class="btn btn-dark" id="addRef" type="button">Add Reference</button>
                    </div>
                    <div class="reference-body">
                        <div id="editorRef"></div>
                        <div class="table-responsive mt-2">
                            <table class="table table-borderless table-hover table-striped" id="formRef">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Reference</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px;">
                                    @forelse ($article->references as $idx => $r)
                                        <tr>
                                            <td>
                                                <div id="editorRef-{{ $idx }}">{!! $r->reference !!}
                                                </div>
                                                <input type="hidden" id="reference-{{ $idx }}"
                                                    name="references[]" value="{{ $r->reference }}">
                                            </td>
                                            <td>
                                                <button type="button" class="deleteRef btn btn-danger">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" align="center" class="text-muted"><em>No reference</em>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 mb-4">
                    <button type="submit" class="btn btn-outline-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Contributor canvas --}}
    <div class="offcanvas offcanvas-end" style="z-index: 9999; width:50%;" tabindex="-1" id="offCanvasContributor"
        aria-labelledby="offCanvasContributorLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offCanvasContributorLabel">Add Contributor</h5>
            <button type="button" class="btn-close text-reset" id="closeCanvas" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="contributorForm" action="#">
                <div class="container nftmax-wc__form-main">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="given_name">First Name<sup>*</sup></label>
                                <input type="text" name="given_name" id="given_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="family_name">Last Name<sup>*</sup></label>
                                <input type="text" name="family_name" id="family_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="preferred_name">Alias</label>
                                <input type="text" name="preferred_name" id="preferred_name">
                                <sub>How do you prefer to be addressed? Salutations, middle names and suffixes can be added
                                    here
                                    if you would like.</sub>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="affilation">Affilation<sup>*</sup></label>
                                <input type="text" name="affilation" id="affilation" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email<sup>*</sup></label>
                                <input type="email" name="email" id="email" placeholder="Enter email" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" id="phone" placeholder="Enter phone">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="country">Country<sup>*</sup></label>
                                <input type="text" name="country" id="country" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="homepage_url">Homepage URL</label>
                                <input type="url" name="homepage_url" id="homepage_url" placeholder="Enter url">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="orcid_id">ORCID-ID</label>
                                <input type="text" name="orcid_id" id="orcid_id">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bio_statement">Bio Statement</label>
                        <div id="editorContributor"></div>
                        <input type="hidden" name="bio_statement" id="bio_statement">
                    </div>
                    <div class="form-group">
                        <label for="role">Role<sup>*</sup></label>
                        <select name="role" id="role" required>
                            <option value="">Select Role</option>
                            <option value="author">Author</option>
                            <option value="translator">Translator</option>
                        </select>
                    </div>
                </div>
                {{-- <div class="row container mt-4">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="principal_contact"
                                name="principal_contact">
                            <label class="form-check-label" for="principal_contact">Principal Contact</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="in_browse_list"
                                name="in_browse_list">
                            <label class="form-check-label" for="in_browse_list">In Browse List</label>
                        </div>
                    </div>
                </div> --}}
        </div>
        <div class="offcanvas-footer mb-2">
            <div class="row container">
                <button class="btn btn-primary" type="submit" id="submitContributor">Submit</button>
            </div>
        </div>
        <input type="hidden" id="editIndex" name="editIndex" value="">
        </form>
    </div>
    {{-- Comments canvas --}}
    <div class="offcanvas offcanvas-end" style="z-index: 9999; width:50%;" tabindex="-1" id="offcanvasComments"
        aria-labelledby="offcanvasCommentsLabel">
        <div class="offcanvas-header bg-warning">
            <h5 class="offcanvas-title text-white" id="offcanvasCommentsLabel">Discussions</h5>
            <button type="button" class="btn-close text-white" id="closeCanvas" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body" id="comments">
            @forelse($article->comments as $comment)
                @if (in_array($comment->user->role_name, ['author_law', 'author_economy']))
                    <!-- User Comment (Right-aligned) -->
                    <div class="d-block text-start w-100 mt-3">
                        <div class="d-flex justify-content-start flex-column">
                            <small class="fw-bold">{{ $comment->user->username }}</small>
                            <div class="card bg-light text-black p-2" style="width: fit-content; max-width: 50%;">
                                <div>{!! $comment->comments !!}</div>
                            </div>
                        </div>
                        @if ($comment->attachments->count() > 0)
                            <div class="d-flex flex-wrap mt-2">
                                @foreach ($comment->attachments as $attachment)
                                    <div class="card me-2 mb-2" style="max-width: 150px;">
                                        <div class="card-body p-2">
                                            <a href="{{ $attachment->signed_file_path }}" target="_blank"
                                                class="text-decoration-none">
                                                <small class="text-truncate d-flex"
                                                    style="max-width: 130px;">{{ $attachment->file_name }}</small>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <small class="text-muted"><em>{{ $comment->commented_at }}</em></small>
                    </div>
                @endif

                @if (in_array($comment->user->role_name, ['editor_law', 'admin_law', 'editor_economy', 'admin_economy']))
                    <!-- Reviewer Comment (Left-aligned) -->
                    <div class="d-block text-end w-100 mt-3">
                        <div class="d-flex justify-content-end">
                            <div class="card bg-warning text-black p-2" style="width: fit-content; max-width: 50%;">
                                <div>{!! $comment->comments !!}</div>
                            </div>
                        </div>
                        @if ($comment->attachments->count() > 0)
                            <div class="d-flex flex-wrap mt-2 justify-content-end">
                                @foreach ($comment->attachments as $attachment)
                                    <div class="card mb-2" style="max-width: 150px;">
                                        <div class="card-body p-2">
                                            <a href="{{ $attachment->signed_file_path }}" target="_blank"
                                                class="text-decoration-none">
                                                <small class="text-truncate d-flex"
                                                    style="max-width: 130px;">{{ $attachment->file_name }}</small>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <small class="text-muted"><em>{{ $comment->commented_at }}</em></small>
                    </div>
                @endif
            @empty
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                No comments yet. Be the first to leave a comment!
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        <div class="offcanvas-footer pb-2 pt-4 bg-light">
            <div class="row container">
                <div id="editorComment"></div>
                <div class="mt-2">
                    <input type="file" id="attachments" name="attachments[]" multiple class="form-control">
                </div>
                <div id="fileList" class="mt-2"></div>
                <a class="btn btn-warning mt-2" onclick="sendComment()">Send</a>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
    <script>
        var oldAbstract = `<?= old('abstract', $article->abstract) ?>`;
        const quill = new Quill('#editor', {
            theme: 'snow'
        });

        var editorContributor = new Quill('#editorContributor', {
            theme: 'snow'
        });

        const quillComment = new Quill('#editorComment', {
            theme: 'snow',
        });

        const quillRef = new Quill('#editorRef', {
            theme: 'snow'
        });

        let quillInstances = {};
        quill.clipboard.dangerouslyPasteHTML(DOMPurify.sanitize(oldAbstract));
        quill.format('color', 'black');

        $(document).ready(function() {
            // Handle file selection
            $('#attachments').on('change', function() {
                const files = this.files;
                let fileListHtml = $('#fileList').html() || '<ul class="list-group">';

                for (let i = 0; i < files.length; i++) {
                    const newIndex = $('#fileList .list-group-item').length + i;
                    const fileUrl = URL.createObjectURL(files[i]);
                    fileListHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="${fileUrl}" target="_blank">${files[i].name}</a>
                            <button type="button" class="btn btn-sm btn-danger remove-file" data-index="${newIndex}">Remove</button>
                        </li>`;
                }

                if (!$('#fileList').html()) {
                    fileListHtml += '</ul>';
                }

                $('#fileList').html(fileListHtml);
            });

            // Handle file removal
            $('#fileList').on('click', '.remove-file', function() {
                const index = $(this).data('index');
                const dt = new DataTransfer();
                const input = document.getElementById('attachments');
                const {
                    files
                } = input;

                for (let i = 0; i < files.length; i++) {
                    if (index !== i)
                        dt.items.add(files[i]);
                }

                input.files = dt.files;
                $(this).parent().remove();
            });

            // Modify sendComment function to include file attachments
            window.sendComment = function() {
                var comment = quillComment.root.innerHTML;
                var strippedComment = quillComment.root.innerText.trim();
                var attachments = $('#attachments')[0].files;

                if (strippedComment.length === 0 && attachments.length === 0) {
                    iziToast.error({
                        title: 'Error',
                        message: 'Comment is empty and no files attached',
                        position: 'topRight'
                    });
                    return;
                }

                var articleId = '{{ $article->id }}';
                var formData = new FormData();
                formData.append('comment', comment);

                for (let i = 0; i < attachments.length; i++) {
                    formData.append('attachments[]', attachments[i]);
                }

                $.ajax({
                    url: '/articles/' + articleId + '/send-comment',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        quillComment.setContents('');
                        $('#attachments').val('');
                        $('#fileList').empty();
                        $('#comments').append(`
                        <div class="row justify-content-end w-100">
                            <div class="col-auto">
                                <div class="card bg-warning text-end mt-3 p-2">
                                    <div>${response.comment}</div>
                                    ${response.attachments ? `<div class="mt-2"><strong>Attachments:</strong> ${response.attachments}</div>` : ''}
                                </div>
                            </div>
                            <small class="text-end text-muted"><em>${response.commented_at}</em></small>
                        </div>
                    `);
                    },
                    error: function(response) {
                        console.log(response);
                        iziToast.error({
                            title: 'Error',
                            message: 'Failed to send comment',
                            position: 'topRight'
                        });
                    }
                });
            };
            $('#keywords').selectize({
                create: true,
                plugins: ["restore_on_backspace", "clear_button"],
                onChange: function(val) {
                    $('#keywordsHidden').val(val);
                }
            });
            $('#status').change(function() {
                var status = $(this).val();
                var editionField = $('#edition');

                if (status !== 'submission' && status !== 'incomplete' && status !== 'review') {
                    editionField.prop('required', true);
                    editionField.closest('.form-group').find('label').append('<sup>*</sup>');
                } else {
                    editionField.prop('required', false);
                    editionField.closest('.form-group').find('label sup').remove();
                }
            });

            // Trigger the change event on page load to set initial state
            $('#status').trigger('change');

            $('#addFile').click(function() {
                const fileInput = $('#file')[0];
                const file = $('#file')[0].files[0];
                if (!file) {
                    iziToast.error({
                        title: 'Error',
                        message: 'File not selected',
                        position: 'topRight'
                    });
                    return;
                }


                // Check for duplicate file names
                let isDuplicate = false;
                $('#formFile tbody tr').each(function() {
                    const existingFileName = $(this).find('td').first().text();
                    if (existingFileName == file.name) {
                        isDuplicate = true;
                        return false; // Exit the loop early
                    }
                });

                if (isDuplicate) {
                    iziToast.error({
                        title: 'Error',
                        message: 'Duplicate file detected! Please select a different file.',
                        position: 'topRight'
                    });
                    return;
                }

                $('#formFile tbody').find('.text-muted').closest('tr').remove();
                var row = $('#formFile tbody tr:last');
                let fileIndex = 0;
                if (row.length == 0) {
                    fileIndex = 0;
                } else {
                    fileIndex = parseInt(row.attr('data-index')) + 1;
                }

                var newRowFile = `
                <tr data-index="${fileIndex}">
                    <td width="60%">${file.name}</td>
                    <td>
                        <select name="article_files[${fileIndex}][type]" id="fileType" class="fileType">
                            <option value="Article Text">Article Text</option>
                            <option value="Plagiarism Report">Plagiarism Report</option>
                            <option value="Research Instrument">Research Instrument</option>
                            <option value="Research Materials">Research Materials</option>
                            <option value="Research Result">Research Result</option>
                            <option value="Transcripts">Transcripts</option>
                            <option value="Data Analysis">Data Analysis</option>
                            <option value="Data Set">Data Set</option>
                            <option value="Source Texts">Source Texts</option>
                            <option value="Other">Source Texts</option>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger viewFile">viewFile</button>
                    </td>
                    <td style="display:none">
                        <input type="file" name="article_files[${fileIndex}][file]" style="display:none;" data-filename="${file.name}" />
                    </td>
                </tr>
                `
                $('#formFile tbody').append(newRowFile);
                setTimeout(() => {
                    const hiddenFileInput = $('#formFile tbody tr:last-child input[type="file"]');
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    hiddenFileInput[0].files = dataTransfer.files;
                    $('#file').val('');
                }, 100);
            });

            $('#formFile').on('click', '.deleteFile', function() {
                $(this).closest('tr').remove();
                if ($('#formFile tbody').find('tr').length == 0) {
                    $('#formFile tbody').append(`
                    <tr>
                        <td colspan="3" align="center" class="text-muted"><em>At least 1 files</em></td>
                    </tr>
                `)
                }
            });

            $('#contributorForm').on('submit', function(e) {
                e.preventDefault();
                if (!this.checkValidity()) {
                    return;
                }

                var editIndex = $('#editIndex').val()
                var formData = $(this).serializeArray();
                // Extract the form data
                var firstName = formData.find(input => input.name === 'given_name')?.value;
                var lastName = formData.find(input => input.name === 'family_name')?.value;
                var alias = formData.find(input => input.name === 'preferred_name')?.value;
                var affilation = formData.find(input => input.name === 'affilation')?.value;
                var email = formData.find(input => input.name === 'email')?.value;
                var phone = formData.find(input => input.name === 'phone')?.value;
                var country = formData.find(input => input.name === 'country')?.value;
                var homepageUrl = formData.find(input => input.name === 'homepage_url')?.value;
                var role = formData.find(input => input.name === 'role')?.value;
                var bioStatement = formData.find(input => input.name === 'bio_statement')?.value;
                var orcidId = formData.find(input => input.name === 'orcid_id')?.value;

                var name = firstName + ' ' + lastName;
                if (alias) {
                    name += ' (' + alias + ')';
                }

                if (affilation) {
                    name += ' - ' + affilation;
                }

                var contact = email + (phone ? ' - ' + phone : '');

                if (editIndex !== '') {
                    var row = $('#contributorList tbody tr').eq(editIndex);
                    //replace data table
                    row.find('td').eq(0).text(name);
                    row.find('td').eq(1).text(contact);
                    row.find('td').eq(2).text(role);

                    let contributorIndex = row.find('input:first').data('index');
                    // replace data input
                    row.find('input[name="contributors[' + contributorIndex + '][given_name]"]').val(
                        firstName);
                    row.find('input[name="contributors[' + contributorIndex + '][family_name]"]').val(
                        lastName);
                    row.find('input[name="contributors[' + contributorIndex + '][preferred_name]"]').val(
                        alias);
                    row.find('input[name="contributors[' + contributorIndex + '][affilation]"]').val(
                        affilation);
                    row.find('input[name="contributors[' + contributorIndex + '][email]"]').val(email);
                    row.find('input[name="contributors[' + contributorIndex + '][phone]"]').val(phone);
                    row.find('input[name="contributors[' + contributorIndex + '][country]"]').val(country);
                    row.find('input[name="contributors[' + contributorIndex + '][homepage_url]"]').val(
                        homepageUrl);
                    row.find('input[name="contributors[' + contributorIndex + '][role]"]').val(role);
                    row.find('input[name="contributors[' + contributorIndex + '][bio_statement]"]').val(
                        bioStatement);
                    row.find('input[name="contributors[' + contributorIndex + '][orcid_id]"]').val(orcidId);
                } else {
                    var row = $('#contributorList tbody tr:last');
                    let contributorIndex = 0;
                    if (row.length == 0) {
                        contributorIndex = 0;
                    } else if (row.find('.text-muted').length == 0) {
                        contributorIndex = parseInt(row.find('input:first').data('index')) + 1;
                    }

                    var newRow = `
                    <tr>
                        <td>${name}</td>
                        <td>${contact}</td>
                        <td>${role}</td>
                        <td class="text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-info btn-sm editRow" type="button">Edit</button>
                                <button class="btn btn-danger btn-sm deleteRow" type="button">Delete</button>
                            </div>
                        </td>
                        <input type="hidden" data-index="${contributorIndex}" name="contributors[${contributorIndex}][given_name]" value="${firstName}" />
                        <input type="hidden" name="contributors[${contributorIndex}][family_name]" value="${lastName}" />
                        <input type="hidden" name="contributors[${contributorIndex}][preferred_name]" value="${alias}" />
                        <input type="hidden" name="contributors[${contributorIndex}][email]" value="${email}"/>
                        <input type="hidden" name="contributors[${contributorIndex}][phone]" value="${phone}"/>
                        <input type="hidden" name="contributors[${contributorIndex}][affilation]" value="${affilation}"/>
                        <input type="hidden" name="contributors[${contributorIndex}][country]" value="${country}"/>
                        <input type="hidden" name="contributors[${contributorIndex}][homepage_url]" value="${homepageUrl}"/>
                        <input type="hidden" name="contributors[${contributorIndex}][orcid_id]" value="${orcidId}"/>
                        <input type="hidden" name="contributors[${contributorIndex}][bio_statement]" value="${bioStatement}"/>
                        <input type="hidden" name="contributors[${contributorIndex}][role]" value="${role}"/>
                    </tr>
                    `

                    // Append the new row to the table body
                    $('#contributorList tbody').append(newRow);
                }

                // Remove the placeholder message if it exists
                $('#contributorList tbody').find('.text-muted').closest('tr').remove();
                $('#editIndex').val('');
                $('#closeCanvas').click();
                // Optionally, clear the form fields after submission
                this.reset();
            });

            $('#contributorList').on('click', '.editRow', function() {
                var row = $(this).closest('tr');
                var rowIndex = row.index();
                var contributorIndex = row.find('input:first').data('index');

                // Populate the form fields with the row's data
                $('#given_name').val(row.find('input[name="contributors[' + contributorIndex +
                    '][given_name]"]').val() || '');
                $('#family_name').val(row.find('input[name="contributors[' + contributorIndex +
                    '][family_name]"]').val() || '');
                $('#preferred_name').val(row.find('input[name="contributors[' + contributorIndex +
                    '][preferred_name]"]').val() || '');
                $('#affilation').val(row.find('input[name="contributors[' + contributorIndex +
                    '][affilation]"]').val() || '');
                $('#email').val(row.find('input[name="contributors[' + contributorIndex + '][email]"]')
                    .val() || '');
                $('#phone').val(row.find('input[name="contributors[' + contributorIndex + '][phone]"]')
                    .val() || '');
                $('#country').val(row.find('input[name="contributors[' + contributorIndex + '][country]"]')
                    .val() || '');
                $('#homepage_url').val(row.find('input[name="contributors[' + contributorIndex +
                    '][homepage_url]"]').val() || '');
                $('#bio_statement').val(row.find('input[name="contributors[' + contributorIndex +
                    '][bio_statement]"]').val() || '');
                $('#role').val(row.find('input[name="contributors[' + contributorIndex + '][role]"]')
                    .val() || '');
                $('#orcid_id').val(row.find('input[name="contributors[' + contributorIndex +
                    '][orcid_id]"]').val() || '');

                // Set the edit index
                $('#editIndex').val(rowIndex);

                // Show the offcanvas form
                var offCanvas = new bootstrap.Offcanvas('#offCanvasContributor');
                offCanvas.show();
            });

            $('#contributorList').on('click', '.deleteRow', function() {
                var row = $(this).closest('tr');
                // Remove the row from the table body
                row.remove();
            });

            $('#addContributor').click(function() {
                $('#contributorForm').trigger('reset');
                $('#editIndex').val('');
            })

            let refCount = 0;
            $('#addRef').click(function() {
                var ref = quillRef.root.innerHTML;
                if (quillRef.getText() == '') {
                    iziToast.error({
                        title: 'Error',
                        message: 'Cant add reference on empty',
                        position: 'topRight'
                    });

                    return;
                }

                $('#formRef tbody').find('.text-muted').closest('tr').remove();
                var refSanitize = DOMPurify.sanitize(ref);
                var newRef = `
                <tr>
                    <td width="90%">
                        <div id="editorRef-${refCount}">${refSanitize}</div>
                        <input type="hidden" id="reference-${refCount}" name="references[]" value="" />
                    </td>
                    <td>
                        <button type="button" class="deleteRef btn btn-danger">Delete</button>
                    </td>
                </tr>
            `
                $('#formRef tbody').append(newRef);
                quillAddRefInit(refCount);
                quillRef.setContents('');
                refCount++;
            });

            $('#formRef').on('click', '.deleteRef', function() {
                $(this).closest('tr').remove();
                if ($('#formRef tbody').find('tr').length == 0) {
                    $('#formRef tbody').append(`
                    <tr>
                        <td colspan="2" align="center" class="text-muted"><em>No reference</em></td>
                    </tr>
                `)
                }
            });
        });

        @php
            foreach ($article->references as $key => $reference) {
                echo "quillAddRefInit($key);";
            }
        @endphp

        function quillAddRefInit(refCount) {
            quillInstances[`editorRef-${refCount}`] = new Quill(`#editorRef-${refCount}`, {
                theme: 'snow'
            });

            $(`#reference-${refCount}`).val(quillInstances[`editorRef-${refCount}`].root.innerHTML);

            quillInstances[`editorRef-${refCount}`].on('text-change', function() {
                $(`#reference-${refCount}`).val(quillInstances[`editorRef-${refCount}`].root.innerHTML);
            });
        }
    </script>
@endsection
