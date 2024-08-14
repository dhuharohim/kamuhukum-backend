@extends('masterpage')

@section('page_title')
    User Create
@endsection

@section('page_content')
    <style>
        p {
            color: black !important;
        }
    </style>
    <div class="nftmax-table welcome-cta d-block mg-top-40">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users-access.index') }}">User List</a></li>
                <li class="breadcrumb-item" aria-current="page">Create
                </li>
            </ol>
        </nav>
        <div class="card-body">
            <form action="{{ route('users-access.store') }}" method="POST" class="nftmax-wc__form-main">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Username<sup>*</sup></label>
                            <input type="text" class="form-control" id="username" name="username" required
                                value="{{ old('username') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email<sup>*</sup></label>
                            <input type="email" class="form-control" id="email" name="email" required
                                value="{{ old('email') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" required>
                        <option value="">Select Role</option>
                        @foreach ($roles as $role)
                            @php
                                $r = $role == 'editor_' . $for ? 'Editor' : 'Author';
                            @endphp
                            <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                {{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="forAuthor" class="mt-4" style="display:none;">
                    <h4>Profile Author</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="given_name">First Name<sup>*</sup></label>
                                <input type="text" name="given_name" id="given_name" value="{{ old('given_name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="family_name">Last Name<sup>*</sup></label>
                                <input type="text" name="family_name" id="family_name" value="{{ old('family_name') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="preferred_name">Preferred Name</label>
                                <input type="text" name="preferred_name" id="preferred_name"
                                    value="{{ old('preferred_name') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="affilation">Affilation<sup>*</sup></label>
                                <input type="text" name="affilation" id="affilation" value="{{ old('affilation') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country">Country<sup>*</sup></label>
                                <input type="text" name="country" id="country" value="{{ old('country') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="homepage_url">Homepage Url</label>
                                <input type="url" name="homepage_url" id="homepage_url"
                                    value="{{ old('homepage_url') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="orcid_id">ORCID-ID</label>
                                <input type="text" name="orcid_id" id="orcid_id" value="{{ old('orcid_id') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bio_statement">Bio Statement</label>
                        <div id="editor">{!! old('bio_statement') !!}</div>
                        <input type="hidden" name="bio_statement" id="bio_statement"
                            value="{{ old('bio_statement') }}">
                    </div>
                </div>
                <div class="row mt-4 mb-0">
                    <button class="btn btn-outline-primary" type="submit">Save</button>
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
            $('#bio_statement').val(quill.root.innerHTML);
        })

        $(document).ready(function() {
            $('#role').change(function() {
                var role = $(this).val();
                if (role === 'author_' + '{{ $for }}') {
                    $('#forAuthor').slideDown();
                } else {
                    $('#forAuthor').slideUp();
                }
            });
        });
    </script>
@endsection