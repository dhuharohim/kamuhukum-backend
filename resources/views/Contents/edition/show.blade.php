@extends('masterpage')

@section('page_title')
    {{ $edition->name_edition }}
@endsection

@section('page_content')
    <div class="alert alert-warning" role="alert">
        <span class="nftmax-menu-icon nftmax-svg-icon__v9 me-1">
            <svg class="nftmax-svg-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M18.458 3.11A1 1 0 0 1 19 4v16a1 1 0 0 1-1.581.814L12 16.944V7.056l5.419-3.87a1 1 0 0 1 1.039-.076ZM22 12c0 1.48-.804 2.773-2 3.465v-6.93c1.196.692 2 1.984 2 3.465ZM10 8H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6V8Zm0 9H5v3a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-3Z"
                    clip-rule="evenodd" />
            </svg>
        </span>
        @if ($edition->announcement)
            <a class="alert-link fw-bold" href="{{ route('announcements.show', $edition->announcement->id) }}">
                <u>{{ $edition->announcement->title }}</u> </a>

            <span>: Submission deadline on
                {{ date('d M Y', strtotime($edition->announcement->submission_deadline_date)) }}</span>
            @if ($edition->announcement->extend_submission_date)
                <span>, and extended until
                    {{ date('d M Y', strtotime($edition->announcement->extend_submission_date)) }}</span>
            @endif
        @endif
    </div>
    <div class="welcome-cta mg-top-40 d-block">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('editions.index') }}">Edition List</a></li>
                <li class="breadcrumb-item" aria-current="page">View {{ $edition->edition_name_formatted }}
                </li>
            </ol>
        </nav>
        <div class="card-body">
            <h4>View {{ $edition->edition_name_formatted }}</h4>
            <form class="nftmax-wc__form-main" action="{{ route('editions.update', $edition->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group" id="coverRequired"
                    style="{{ $edition->status == 'Published' ? '' : 'display: none;' }}">
                    <div id="coverImage" class="card"
                        style="height:245px; width:175px; background-size:cover; background-repeat:no-repeat; background-image: url('{{ $edition->signed_edition_image }}');">
                    </div>
                    <label for="cover">Cover<sup>*</sup></label>
                    <input type="file" name="cover_img" id="cover" class="align-content-center" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="status">Status<sup>*</sup></label>
                    <select name="status" id="status" required>
                        <option value="Draft" {{ $edition->status == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Archive" {{ $edition->status == 'Archive' ? 'selected' : '' }}>Archive</option>
                        <option value="Published" {{ $edition->status == 'Published' ? 'selected' : '' }}>Published
                        </option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="volume">Volume <sup>*</sup></label>
                            <input type="number" name="volume" id="volume"
                                value="{{ old('volume', $edition->volume) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="issue">Issue <sup>*</sup></label>
                            <input type="number" name="issue" id="issue" value="{{ old('issue', $edition->issue) }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="year">Year <sup>*</sup></label>
                            @php
                                $years = range(date('Y') + 5, date('Y') - 5);
                                $currentYear = date('Y');
                            @endphp
                            <select name="year" id="year" required>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                        {{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="theme">Theme Edition <sup>*</sup></label>
                            <input type="text" class="form-control" id="theme" name="name_edition"
                                placeholder="Enter Theme edition" value="{{ old('name_edition', $edition->name_edition) }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                placeholder="leave it blank to generate automatically slug"
                                value="{{ old('slug', $edition->slug) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <div id="editor">{!! old('description', $edition->description) !!}</div>
                    <input type="hidden" name="description" id="description"
                        value="{{ old('description', $edition->description) }}" />
                </div>

                <div class="button-submit mt-4">
                    <button type="submit" class="btn btn-outline-primary w-100">Save</button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection

@section('custom_js')
    <script>
        const quill = new Quill('#editor', {
            theme: 'snow'
        });

        quill.on('text-change', function() {
            $('#description').val(quill.root.innerHTML);
        })

        $('#status').change(function() {
            if ($(this).val() == 'Published') {
                $('#coverRequired').slideDown();
                $('#cover').attr('required', true);
            } else {
                $('#coverRequired').slideUp();
                $('#cover').attr('required', false);
            }
        })

        $('#cover').change(function() {
            var file = $(this)[0].files[0];
            var reader = new FileReader();
            reader.onloadend = function() {
                $('#coverImage').css('background-image', 'url(' + reader.result + ')');
            }
            reader.readAsDataURL(file);
        })
    </script>
@endsection
