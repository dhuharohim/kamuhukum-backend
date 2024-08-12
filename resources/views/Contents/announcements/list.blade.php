@extends('masterpage')

@section('page_title')
    Announcements
@endsection

@section('page_content')
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        <div class="nftmax-table__heading">
            <h3 class="nftmax-table__title mb-0">Announcement List</h3>
            <a href="{{ route('announcements.create') }}" class="btn btn-primary">Create</a>
        </div>
        <div class="table-responsive">
            <table class="table table-borderless table-hover" id="annTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="">Title</th>
                        <th class="">Published At</th>
                        <th class="">Related Edition</th>
                        <th class=" text-end">Action</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                    @foreach ($announcements as $index => $ann)
                        <tr>
                            <td class="">{{ $ann->title }}</td>
                            <td class="">
                                {{ $ann->published_date ? date('d M Y', strtotime($ann->published_date)) : '-' }}</td>
                            <td class="">
                                @if ($ann->edition)
                                    <a href="{{ route('editions.show', $ann->edition_id) }}">
                                        {{ $ann->edition->edition_name_formatted }}
                                    </a>
                                    <ul>
                                        <li>Submission deadline:
                                            {{ date('d M Y', strtotime($ann->submission_deadline_date)) }}</li>
                                        <li>Extend Until:
                                            {{ $ann->extend_submission_date ? date('d M Y', strtotime($ann->extend_submission_date)) : '-' }}
                                        </li>
                                    </ul>
                                @else
                                    Not related
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('announcements.show', $ann->id) }}"
                                        class="btn btn-sm btn-outline-primary">View</a>
                                    <a id="deleteEdition" onclick="confirmDelete('{{ $ann->id }}')"
                                        class="btn btn-outline-danger btn-sm">Delete</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('custom_js')
    <script>
        let table = new DataTable('#annTable');

        function confirmDelete(id) {
            iziToast.question({
                timeout: 0,
                close: false,
                overlay: false,
                displayMode: 'once',
                id: 'question',
                zindex: 1,
                title: 'Are you sure?',
                message: 'Do you really want to delete this announcement?',
                position: 'center',
                buttons: [
                    ['<button><b>Yes</b></button>', function(instance, toast) {
                        // Perform the delete action here
                        deleteAnn(id);

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

        function deleteAnn(id) {
            $.ajax({
                url: '/announcements/' + id,
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
