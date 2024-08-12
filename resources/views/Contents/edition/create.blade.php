@extends('masterpage')

@section('page_title')
    Create Edition
@endsection

@section('page_content')
    <div class="card mg-top-40">
        <div class="card-body">
            <h4>Create New Edition</h4>
            <form class="nftmax-wc__form-main" action="{{ route('editions.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="form-group" style="display: none;" id="coverRequired">
                    <div id="coverImage" class="card"
                        style="height:245px; width:175px; background-size:cover; background-repeat:no-repeat;">
                    </div>
                    <label for="cover">Cover<sup>*</sup></label>
                    <input type="file" name="cover_img" id="cover" class="align-content-center" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="status">Status<sup>*</sup></label>
                    <select name="status" id="status" required>
                        <option value="Draft">Draft</option>
                        <option value="Archive">Archive</option>
                        <option value="Published">Published</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="volume">Volume <sup>*</sup></label>
                            <input type="number" name="volume" id="volume" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="issue">Issue <sup>*</sup></label>
                            <input type="number" name="issue" id="issue" required>
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
                                placeholder="Enter Theme edition" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                placeholder="leave it blank to generate automaticly slug">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <div id="editor"></div>
                    <input type="hidden" name="description" id="description" />
                </div>
                <div class="button-submit mt-4">
                    <button type="submit" class="btn btn-outline-primary w-100">Save</button>
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
