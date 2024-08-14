@extends('masterpage')

@section('page_title')
    Create Article
@endsection

@section('page_content')
    <style>
        p {
            color: black !important;
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
    </style>
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('editions.index') }}">Editions</a></li>
                <li class="breadcrumb-item"><a href="{{ route('articles.index', $edition->id) }}">Article List on
                        {{ $edition->edition_name_formatted }}</a></li>
                <li class="breadcrumb-item" aria-current="page">Create
                </li>
            </ol>
        </nav>
        <div class="card-body">
            <h4>Create New Article for {{ $edition->edition_name_formatted }}</h4>
            <form class="nftmax-wc__form-main" action="{{ route('articles.store', $edition->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="status">Status<sup>*</sup></label>
                    @php
                        $statuses = ['incomplete', 'submission', 'review', 'production'];
                    @endphp
                    <select name="status" id="status" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}"{{ old('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="prefix">Prefix</label>
                            <input type="text" name="prefix" id="prefix" value="{{ old('prefix') }}">
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="title">Title <sup>*</sup></label>
                            <input type="text" name="title" id="title" required value="{{ old('title') }}">
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
                            <input type="text" name="subtitle" id="subtitle" required value="{{ old('subtitle') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                placeholder="leave it blank to generate automaticly slug" value="{{ old('slug') }}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="abstract">Abstract<sup>*</sup></label>
                    <div id="editor"></div>
                    <input type="hidden" name="abstract" id="abstract" required value="{{ old('abstract') }}" />
                </div>
                <div class="form-group">
                    <label for="keywords">Keywords</label>
                    <select id="keywords" multiple></select>
                    <input type="hidden" name="keywords" id="keywordsHidden" value="{{ old('keywords') }}">
                </div>
                <div class="files-wrapper mt-4">
                    <div class="header">
                        <h4>Files<sup>*</sup></h4>
                    </div>
                    <div class="files-body">
                        <div class="row w-100 align-items-center">
                            <div class="col-md-4">
                                <input type="file" id="file" class="align-content-center">
                            </div>
                            <div class="col-md-8">
                                <button class="btn btn-info" id="addFile" type="button">Add File</button>
                            </div>
                        </div>
                        <div class="table-responsive mt-4">
                            <table class="table table-borderless table-hover" id="formFile">
                                <thead class="bg-info text-white">
                                    <tr>
                                        <th>File Name</th>
                                        <th>File Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px;">
                                    <tr>
                                        <td colspan="3" align="center" class="text-muted"><em>At least 1 files</em></td>
                                    </tr>
                                    {{-- <input type="file" name="files[]" class="files" multiple /> --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="contributor-wrapper mt-5">
                    <div class="header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">List of Contributors<sup>*</sup></h4>
                        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offCanvasContributor" aria-controls="offCanvasContributor"
                            id="addContributor">Add
                            Contributor</button>
                    </div>
                    <div class="contributor-form table-responsive table-striped mt-3" id="contributorList">
                        <table class="table table-borderless table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Role</th>
                                    <th>Principal Contact</th>
                                    <th>In Browse List</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;">
                                @if (old('contributors'))
                                    @foreach (old('contributors') as $index => $c)
                                        <tr>
                                            <td>{{ $c['given_name'] . ' ' . $c['family_name'] }}</td>
                                            <td>{{ $c['contact'] }}</td>
                                            <td>{{ $c['role'] }}</td>
                                            <td>{{ $c['principal_contact'] }}</td>
                                            <td>{{ $c['in_browse_list'] }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-info btn-sm editRow"
                                                        type="button">Edit</button>
                                                    <button class="btn btn-danger btn-sm deleteRow"
                                                        type="button">Delete</button>
                                                </div>
                                            </td>
                                            <input type="hidden" data-index="{{ $index }}"
                                                name="contributors[{{ $index }}][given_name]"
                                                value="{{ $c['given_name'] }}" />
                                            <input type="hidden" name="contributors[{{ $index }}][family_name]"
                                                value="{{ $c['family_name'] }}" />
                                            <input type="hidden"
                                                name="contributors[{{ $index }}][preferred_name]"
                                                value="{{ $c['preferred_name'] }}" />
                                            <input type="hidden" name="contributors[{{ $index }}][contact]"
                                                value="{{ $c['contact'] }}" />
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
                                                value="{{ $c['role'] }}" />
                                            <input type="hidden"
                                                name="contributors[{{ $index }}][principal_contact]"
                                                value="{{ $c['principal_contact'] }}" />
                                            <input type="hidden"
                                                name="contributors[{{ $index }}][in_browse_list]"
                                                value="{{ $c['in_browse_list'] }}" />
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" align="center" class="text-muted text"> <em>Article must have
                                                a
                                                contributor with role author at
                                                least 1</em></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="reference-wrapper mt-3">
                    <h4>Reference</h4>
                    <div class="reference-body">
                        <div id="editorRef"></div>
                        {{-- <input type="hidden" id="reference"> --}}
                        <button class="btn btn-dark mt-2" id="addRef" type="button">Add Reference</button>
                        <div class="table-responsive mt-2">
                            <table class="table table-borderless table-hover table-striped" id="formRef">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Reference</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px;">
                                    <tr>
                                        <td colspan="2" class="text-muted" align="center"><em>No reference</em></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="button-submit mt-4">
                    <button type="submit" class="btn btn-outline-primary w-100">Save</button>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact">Contact<sup>*</sup></label>
                                <input type="email" name="contact" id="contact" placeholder="Enter email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
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
                <div class="row container mt-4">
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
                </div>
        </div>
        <div class="offcanvas-footer mb-2">
            <div class="row container">
                <button class="btn btn-primary" type="submit" id="submitContributor">Submit</button>
            </div>
        </div>
        <input type="hidden" id="editIndex" name="editIndex" value="">
        </form>
    </div>
@endsection

@section('custom_js')
    <script>
        $('#keywords').selectize({
            create: true,
            plugins: ["restore_on_backspace", "clear_button"],
            delimiter: " - ",
            onChange: function(val) {
                $('#keywordsHidden').val(val);
            }
        });

        const quill = new Quill('#editor', {
            theme: 'snow'
        });

        var oldAbstract = '<?= old('abstract') ?>';
        quill.clipboard.dangerouslyPasteHTML(DOMPurify.sanitize(oldAbstract));


        const quillContributor = new Quill('#editorContributor', {
            theme: 'snow'
        });

        const quillRef = new Quill('#editorRef', {
            theme: 'snow'
        });

        quill.on('text-change', function() {
            $('#abstract').val(quill.root.innerHTML);
        })

        quillContributor.on('text-change', function() {
            $('#bio_statement').val(quillContributor.root.innerHTML);
        })

        quillRef.on('text-change', function() {
            $('#reference').val(quillRef.root.innerHTML);
        })

        let refCount = 0;
        $('#addRef').click(function() {
            var ref = quillRef.root.innerHTML;
            console.log(quillRef.getText() == ' ');
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
                        <input type="hidden" id="reference-${refCount}" name="references[]" />
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

        let quillInstances = {};

        function quillAddRefInit(refCount) {
            quillInstances[`editorRef-${refCount}`] = new Quill(`#editorRef-${refCount}`, {
                theme: 'snow'
            });

            $(`#reference-${refCount}`).val(quillInstances[`editorRef-${refCount}`].root.innerHTML);
            quillInstances[`editorRef-${refCount}`].on('text-change', function() {
                $(`#reference-${refCount}`).val(quillInstances[`editorRef-${refCount}`].root.innerHTML);
            });
        }

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
                        <button type="button" class="btn btn-danger deleteFile">Delete</button>
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
            var contact = formData.find(input => input.name === 'contact')?.value;
            var country = formData.find(input => input.name === 'country')?.value;
            var homepageUrl = formData.find(input => input.name === 'homepage_url')?.value;
            var role = formData.find(input => input.name === 'role')?.value;
            var bioStatement = formData.find(input => input.name === 'bio_statement')?.value;
            var principalContact = formData.find(input => input.name === 'principal_contact')?.value || 'off';
            var inBrowseList = formData.find(input => input.name === 'in_browse_list')?.value || 'off';
            var orcidId = formData.find(input => input.name === 'orcid_id')?.value || 'off';

            var name = firstName + ' ' + lastName;
            if (alias) {
                name += ' (' + alias + ')';
            }

            if (affilation) {
                name += ' - ' + affilation;
            }


            if (editIndex !== '') {
                var row = $('#contributorList tbody tr').eq(editIndex);
                //replace data table
                row.find('td').eq(0).text(name);
                row.find('td').eq(1).text(contact);
                row.find('td').eq(2).text(role);
                row.find('td').eq(3).text(principalContact);
                row.find('td').eq(4).text(inBrowseList);

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
                row.find('input[name="contributors[' + contributorIndex + '][contact]"]').val(contact);
                row.find('input[name="contributors[' + contributorIndex + '][country]"]').val(country);
                row.find('input[name="contributors[' + contributorIndex + '][homepage_url]"]').val(
                    homepageUrl);
                row.find('input[name="contributors[' + contributorIndex + '][role]"]').val(role);
                row.find('input[name="contributors[' + contributorIndex + '][bio_statement]"]').val(
                    bioStatement);
                row.find('input[name="contributors[' + contributorIndex + '][principal_contact]"]').val(
                    principalContact);
                row.find('input[name="contributors[' + contributorIndex + '][in_browse_list]"]').val(
                    inBrowseList);
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
                    <td>${principalContact}</td>
                    <td>${inBrowseList}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-info btn-sm editRow" type="button">Edit</button>
                            <button class="btn btn-danger btn-sm deleteRow" type="button">Delete</button>
                        </div>
                    </td>
                    // Form Data
                    <input type="hidden" data-index="${contributorIndex}" name="contributors[${contributorIndex}][given_name]" value="${firstName}" />
                    <input type="hidden" name="contributors[${contributorIndex}][family_name]" value="${lastName}" />
                    <input type="hidden" name="contributors[${contributorIndex}][preferred_name]" value="${alias}" />
                    <input type="hidden" name="contributors[${contributorIndex}][contact]" value="${contact}"/>
                    <input type="hidden" name="contributors[${contributorIndex}][affilation]" value="${affilation}"/>
                    <input type="hidden" name="contributors[${contributorIndex}][country]" value="${country}"/>
                    <input type="hidden" name="contributors[${contributorIndex}][homepage_url]" value="${homepageUrl}"/>
                    <input type="hidden" name="contributors[${contributorIndex}][orcid_id]" value="${orcidId}"/>
                    <input type="hidden" name="contributors[${contributorIndex}][bio_statement]" value="${bioStatement}"/>
                    <input type="hidden" name="contributors[${contributorIndex}][role]" value="${role}"/>
                    <input type="hidden" name="contributors[${contributorIndex}][principal_contact]" value="${principalContact}"/>
                    <input type="hidden" name="contributors[${contributorIndex}][in_browse_list]" value="${inBrowseList}"/>
                </tr>
                `

                // Append the new row to the table body
                $('#contributorList tbody').append(newRow);
            }

            // Remove the placeholder message if it exists
            $('#contributorList tbody').find('.text-muted.text').closest('tr').remove();
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
            $('#given_name').val(row.find('input[name="contributors[' + contributorIndex + '][given_name]"]')
                .val());
            $('#family_name').val(row.find('input[name="contributors[' + contributorIndex + '][family_name]"]')
                .val());
            $('#preferred_name').val(row.find('input[name="contributors[' + contributorIndex +
                '][preferred_name]"]').val());
            $('#affilation').val(row.find('input[name="contributors[' + contributorIndex + '][affilation]"]')
                .val());
            $('#contact').val(row.find('input[name="contributors[' + contributorIndex + '][contact]"]').val());
            $('#country').val(row.find('input[name="contributors[' + contributorIndex + '][country]"]').val());
            $('#homepage_url').val(row.find('input[name="contributors[' + contributorIndex + '][homepage_url]"]')
                .val());
            $('#bio_statement').val(row.find('input[name="contributors[' + contributorIndex + '][bio_statement]"]')
                .val());
            $('#role').val(row.find('input[name="contributors[' + contributorIndex + '][role]"]').val());
            $('#orcid_id').val(row.find('input[name="contributors[' + contributorIndex + '][orcid_id]"]').val());
            if (row.find('input[name="contributors[' + contributorIndex + '][principal_contact]"').val() == 'on') {
                $('#principal_contact').attr('checked', true);
            }

            if (row.find('input[name="contributors[' + contributorIndex + '][in_browse_list]').val() == 'on') {
                $('#in_browse_list').attr('checked', true);
            }

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
    </script>
@endsection
