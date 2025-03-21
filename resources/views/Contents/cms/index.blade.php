@extends('masterpage')

@section('page_title')
    Content Management System
@endsection

@section('page_content')
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Content Management System</h4>
            <a class="btn btn-primary" href="/cms/create">Create Section</a>
        </div>

        <div class="row">
            <div class="table-responsive">
                <table class="table table-borderless table-hover" id="cmsTable">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="">Section</th>
                            <th class="">Status</th>
                            <th class="">Category</th>
                            <th class=" text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px;">
                        @foreach ($sections as $section)
                            <tr>
                                <td>{{ $section->name }}</td>
                                <td>
                                    @php
                                        $badgeColor = $section->is_active ? 'success' : 'warning';
                                    @endphp
                                    <span
                                        class="badge bg-{{ $badgeColor }}">{{ $section->is_active ? 'Production' : 'Need to Adjust' }}</span>
                                </td>
                                <td>{{ ucwords($section->position) }}</td>
                                <td class="text-end">
                                    <div class="button-group">
                                        <a href="/cms/{{ $section->slug }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-tasks me-1"></i> Manage
                                        </a>
                                        @if (!$section->is_active)
                                            <button class="btn btn-danger btn-sm"
                                                onclick="deleteSection({{ $section->id }})">
                                                <i class="fas fa-trash me-1"></i> Delete
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
    </div>
@endsection

@section('custom_js')
    <script>
        let table = new DataTable('#cmsTable');

        function deleteSection(id) {
            iziToast.question({
                timeout: 0,
                close: false,
                overlay: false,
                displayMode: 'once',
                id: 'question',
                zindex: 1,
                title: 'Are you sure?',
                message: 'You won\'t be able to revert this!',
                position: 'center',
                buttons: [
                    ['<button><b>Yes, delete it!</b></button>', function(instance, toast) {
                        $.ajax({
                            url: `/cms/${id}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                window.location.reload();
                            }
                        });
                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                    }, true],
                    ['<button>No</button>', function(instance, toast) {
                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                    }]
                ]
            });
        }
    </script>
@endsection
