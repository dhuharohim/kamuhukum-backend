@extends('masterpage')

@section('page_title')
    {{ isset($section) ? 'Update Content' : 'Manage Content' }}
@endsection

@section('custom_css')
    <style>
        .preview-container {
            position: relative;
            border: 2px dashed #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .preview-container:hover {
            border-color: #2196F3;
        }

        .preview-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            color: white;
            border-radius: 6px;
        }

        .preview-container:hover .preview-overlay {
            display: flex;
        }

        .section-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .sub-section-row {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .sub-section-row:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .type-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .type-btn {
            padding: 8px 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .type-btn.active {
            background: #2196F3;
            color: white;
            border-color: #2196F3;
        }

        .ql-toolbar {
            border-radius: 5px 5px 0 0;
        }

        .ql-container {
            border-radius: 0 0 5px 5px;
            min-height: 150px;
        }

        .image-preview {
            position: relative;
            display: inline-block;
        }

        .image-preview .remove-image {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            text-align: center;
            line-height: 24px;
            cursor: pointer;
        }

        .drag-handle {
            cursor: move;
            color: #6c757d;
            margin-right: 10px;
        }

        .key-input {
            position: relative;
        }

        .key-input .key-suggestion {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
            z-index: 1000;
            display: none;
        }

        .key-input:focus-within .key-suggestion {
            display: block;
        }
    </style>
@endsection

@section('page_content')
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">{{ isset($section) ? 'Update Content' : 'Manage Content' }}</h4>
                <p class="text-muted mt-1">
                    {{ isset($section) ? 'Modify existing content section' : 'Create a new content section' }}</p>
            </div>
            <a href="{{ route('cms.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form class="nftmax-wc__form-main"
            action="{{ isset($section) ? route('cms.update', $section->id) : route('cms.store') }}" method="POST"
            enctype="multipart/form-data" id="contentForm">
            @csrf
            @if (isset($section))
                @method('PUT')
            @endif

            <div class="section-card">
                <h5 class="card-title mb-3">Basic Information</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Section Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ isset($section) ? $section->name : old('name') }}"
                                placeholder="Enter section name">
                            <small class="text-muted">This name will be used to identify the section</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" id="category">
                                <option value="" disabled {{ !isset($section) ? 'selected' : '' }}>Select Category
                                </option>
                                @foreach (['main', 'header', 'footer'] as $category)
                                    <option value="{{ $category }}"
                                        {{ (isset($section) && $section->category == $category) || old('category') == $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Choose where this section will appear</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h5 class="card-title mb-3">Preview Image</h5>
                <div class="preview-container" id="previewContainer">
                    <div id="previewImage" class="d-flex justify-content-center align-items-center"
                        style="height: 245px; background-size: cover; background-position: center; background-repeat: no-repeat;
                        {{ isset($section) && $section->preview ? 'background-image: url(' . $section->signed_preview_image . ');' : '' }}">

                        <div class="text-center {{ isset($section) && $section->preview ? 'd-none' : '' }}">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                            <p class="mt-2 text-muted">Click or drag image to upload</p>
                        </div>
                    </div>
                    <div class="preview-overlay">
                        <span><i class="fas fa-edit"></i> Change Image</span>
                    </div>
                    <input type="file" name="preview" id="preview" class="d-none" accept="image/*">
                </div>
                <small class="text-muted mt-2 d-block">Recommended size: 1200x630px. Max file size: 2MB</small>
            </div>

            <div class="section-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="card-title mb-1">Sub Sections</h5>
                        <p class="text-muted mb-0">Add multiple content blocks to this section</p>
                    </div>
                    <button type="button" class="btn btn-primary" id="addSubSection">
                        <i class="fas fa-plus"></i> Add Sub Section
                    </button>
                </div>

                <div id="subSections">
                    <!-- Sub sections will be dynamically added here -->
                </div>
            </div>

            <div class="section-card mt-3">
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        {{ isset($section) ? 'Update Content' : 'Save Content' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('custom_js')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        let countEditor = {{ isset($section) ? $section->contents->count() - 1 : -1 }};
        let quillInstances = {};

        $(document).ready(function() {
            // Make sub sections sortable
            new Sortable(document.getElementById('subSections'), {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function() {
                    updateOrder();
                }
            });

            // Initialize existing sub sections
            @if (isset($section) && $section->contents->count() > 0)
                @foreach ($section->contents as $index => $content)
                    addSubSection({
                        key: '{{ $content->key }}',
                        value: {!! json_encode($content->value) !!}
                    });
                @endforeach
            @else
                addSubSection();
            @endif

            // Add Sub Section button click handler
            $('#addSubSection').click(function() {
                addSubSection();
            });

            // Preview image handling
            $('#previewContainer').on('click', function(e) {
                if (!$(e.target).closest('input[type="file"]').length) {
                    $('#preview').click();
                }
            });

            $('#preview').change(function(e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImage').css('background-image', `url(${e.target.result})`);
                        $('#previewImage').children().addClass('d-none');
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            });

            // Key input suggestions
            $(document).on('input', 'input[name="key[]"]', function() {
                const input = $(this);
                const value = input.val().toLowerCase();
                const sanitizedValue = value.replace(/[^a-z0-9_]/g, '_').replace(/_{2,}/g, '_');
                input.val(sanitizedValue);
            });

            // Form submission
            $('#contentForm').on('submit', function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;

                // Check section name
                if (!$('#name').val()) {
                    isValid = false;
                    $('#name').addClass('is-invalid');
                }

                // Check category
                if (!$('#category').val()) {
                    isValid = false;
                    $('#category').addClass('is-invalid');
                }

                // Check sub sections
                $('.sub-section-row').each(function() {
                    const key = $(this).find('input[name="key[]"]').val();
                    const editorId = $(this).find('[id^="editor-"]').attr('id');
                    const index = editorId.split('-')[1];
                    const content = quillInstances[`editor-${index}`].root.innerHTML;

                    if (!key) {
                        isValid = false;
                        $(this).find('input[name="key[]"]').addClass('is-invalid');
                    }

                    if (content === '<p><br></p>' || !content) {
                        isValid = false;
                        $(this).find('.ql-container').addClass('is-invalid');
                    }
                });

                if (!isValid) {
                    showError('Please fill in all required fields');
                    return false;
                }

                showLoadingIndicator();
                this.submit();
            });

            // Remove invalid state on input
            $(document).on('input change', '.is-invalid', function() {
                $(this).removeClass('is-invalid');
            });
        });

        function addSubSection(data = null) {
            countEditor++;
            const index = countEditor;

            const subSection = $(`
                <div class="sub-section-row mb-4" data-index="${index}">
                    <div class="d-flex align-items-center mb-3">
                        <span class="drag-handle">
                            <i class="fas fa-grip-vertical"></i>
                        </span>
                        <div class="flex-grow-1">
                            <div class="input-group">
                                <input type="text" class="form-control" name="key[]"
                                    placeholder="Enter key (e.g., hero_title, about_text)"
                                    value="${data?.key || ''}" required>
                                ${index > 0 ? `
                                                                                <button type="button" class="btn btn-outline-danger" onclick="removeSubSection(${index})">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                            ` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="content-area">
                        <div class="editor-container">
                            <div id="editor-${index}"></div>
                            <input type="hidden" name="valueText[]" id="valueText-${index}">
                        </div>
                    </div>
                </div>
            `);

            $('#subSections').append(subSection);

            // Initialize Quill editor
            initQuillEditor(index, data?.value || '');

            // Scroll to new section
            $('html, body').animate({
                scrollTop: subSection.offset().top - 100
            }, 500);
        }

        function initQuillEditor(index, content = '') {
            const options = {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{
                            'header': 1
                        }, {
                            'header': 2
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        [{
                            'direction': 'rtl'
                        }],
                        [{
                            'size': ['small', false, 'large', 'huge']
                        }],
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        [{
                            'font': []
                        }],
                        [{
                            'align': []
                        }],
                        ['clean'],
                        ['link', 'image', 'video']
                    ]
                }
            };

            const quill = new Quill(`#editor-${index}`, options);

            // Handle image upload
            const toolbar = quill.getModule('toolbar');
            toolbar.addHandler('image', () => {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = async () => {
                    const file = input.files[0];
                    if (file) {
                        try {
                            const formData = new FormData();
                            formData.append('image', file);

                            const response = await fetch('{{ route('cms.upload-image') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                body: formData
                            });

                            const result = await response.json();

                            if (result.success) {
                                const range = quill.getSelection(true);
                                quill.insertEmbed(range.index, 'image', result.url);
                            } else {
                                throw new Error(result.message || 'Upload failed');
                            }
                        } catch (error) {
                            console.error('Upload failed:', error);
                            alert('Image upload failed: ' + error.message);
                        }
                    }
                };
            });

            if (content) {
                quill.root.innerHTML = content;
            }

            quill.on('text-change', function() {
                $(`#valueText-${index}`).val(quill.root.innerHTML);
            });

            // Set initial value
            $(`#valueText-${index}`).val(quill.root.innerHTML);

            // Store instance
            quillInstances[`editor-${index}`] = quill;
        }

        function removeSubSection(index) {
            if ($('.sub-section-row').length === 1) {
                showError('You must have at least one sub section');
                return;
            }

            if (confirm('Are you sure you want to remove this sub section?')) {
                $(`.sub-section-row[data-index="${index}"]`).fadeOut(300, function() {
                    $(this).remove();
                    updateOrder();
                });
            }
        }

        function showLoadingIndicator() {
            const submitBtn = $('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        }

        function showError(message) {
            const alert = $(`
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);

            $('.nftmax-table').prepend(alert);

            setTimeout(() => {
                alert.alert('close');
            }, 5000);
        }

        function updateOrder() {
            $('.sub-section-row').each(function(index) {
                // Optional: Update order if needed
            });
        }
    </script>
@endsection
