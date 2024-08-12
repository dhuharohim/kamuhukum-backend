@extends('masterpage')

@section('page_title')
    View Announcement
@endsection

@section('page_content')
    <style>
        p {
            color: black !important;
        }
    </style>
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('announcements.index') }}">Announcement List</a></li>
                <li class="breadcrumb-item" aria-current="page">View {{ $announcement->title }}
                </li>
            </ol>
        </nav>

        <div class="card-body">
            <form class="" action="{{ route('announcements.update', $announcement->id) }}" method="POST">
                @method('PUT')
                @csrf
                <div class="nftmax-wc__form-main">
                    <div class="form-group">
                        <label for="edition">Related Edition</label>
                        <select name="edition" id="edition">
                            <option value="">Select an Edition</option>
                            @foreach ($editions as $edition)
                                <option value="{{ $edition->id }}"
                                    {{ old('edition', $announcement->edition_id) == $edition->id ? 'selected' : '' }}>
                                    {{ $edition->edition_name_formatted }}</option>
                            @endforeach
                        </select>
                        <sub>Not mandatory, you can just create a general announcement</sub>
                    </div>
                    <div class="row" id="relatedEdition" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="submission_deadline">Submission Deadline<sup>*</sup></label>
                                <input type="date" name="submission_deadline" id="submission_deadline"
                                    value="{{ old('submission_deadline', $announcement->submission_deadline_date) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="extend_submission_date">Extend Deadline</label>
                                <input type="date" name="extend_submission_date" id="extend_submission_date"
                                    value="{{ old('extend_submission_date', $announcement->extend_submission_date) }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">Title<sup>*</sup></label>
                                <input type="text" name="title" id="title" required
                                    value="{{ old('title', $announcement->title) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="slug">Slug</label>
                                <input type="text" name="slug" id="slug"
                                    placeholder="leave it blank to generate automaticly slug"
                                    value="{{ old('slug', $announcement->slug) }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="Description">Description</label>
                        <div id="editor"></div>
                        <input type="hidden" name="description" id="description"
                            value="{{ old('description', $announcement->description) }}">
                    </div>
                </div>

                <div class="form-group mt-4">
                    @if (empty($announcement->published_date))
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="published" role="switch" id="published">
                            <label class="form-check-label" for="published">Publish now?</label>
                        </div>
                    @else
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="takedown" role="switch" id="takedown">
                            <label class="form-check-label" for="takedown">Take down?</label>
                        </div>
                        <p>Published at: {{ date('d M Y', strtotime($announcement->published_date)) }}</p>
                    @endif
                </div>
                <div class="row mt-4">
                    <button class="btn btn-outline-primary" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('custom_js')
    <script>
        const quill = new Quill('#editor', {
            theme: 'snow'
        });

        var description = `<?= old('description', $announcement->description) ?>`;
        $(document).ready(function() {
            quill.clipboard.dangerouslyPasteHTML(DOMPurify.sanitize(description));
            quill.on('text-change', function() {
                $('#description').val(quill.root.innerHTML);
            })

            $('#edition').change(function() {
                var thisEl = $(this);
                if (thisEl.val() !== '') {
                    $('#relatedEdition').slideDown();
                    $('#submission_deadline').attr('required', true);
                } else {
                    $('#relatedEdition').slideUp();
                    $('#submission_deadline').attr('required', false);
                }
            });

            setTimeout(() => {
                $('#edition').trigger('change');
            }, 100);
        })
    </script>
@endsection
