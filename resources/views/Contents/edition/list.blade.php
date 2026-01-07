@extends('masterpage')

@section('page_title')
    Editions
@endsection

@section('page_content')
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        <div class="nftmax-table__heading">
            <h3 class="nftmax-table__title mb-0">Edition List</h3>
            @if (auth()->user()->hasRole(['admin_law', 'admin_economy']))
                <a href="{{ route('editions.create') }}" class="btn btn-primary">Create</a>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-borderless table-hover" id="editionTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="">Name Edition</th>
                        <th class="">Status</th>
                        <th class="">Published At</th>
                        <th class=" text-end">Action</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                    @foreach ($editions as $index => $edition)
                        <tr>
                            <td class="">
                                <a href="{{ route('editions.show', $edition->id) }}">
                                    {{ $edition->edition_name_formatted }} - ({{ count($edition->articles) }}
                                    Articles)
                                </a>
                            </td>
                            <td class="">
                                @php
                                    $badge =
                                        $edition->status == 'Draft'
                                            ? 'warning'
                                            : ($edition->status == 'Archive'
                                                ? 'info'
                                                : 'success');
                                @endphp
                                <span class="badge bg-{{ $badge }}"> {{ $edition->status }}</span>
                            </td>
                            <td class="">
                                {{ $edition->publish_date_formatted }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('articles.index', $edition->id) }}"
                                        class="btn btn-sm btn-outline-primary">Article
                                        List</a>
                                    @if (auth()->user()->hasRole(['admin_law', 'admin_economy']))
                                        <a id="deleteEdition" onclick="confirmDelete('{{ $edition->id }}')"
                                            data-id="{{ $edition->id }}" class="btn btn-outline-danger btn-sm">Delete</a>
                                    @endif
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
        let table = new DataTable('#editionTable');

        function confirmDelete(id) {
            iziToast.question({
                timeout: 0,
                close: false,
                overlay: false,
                displayMode: 'once',
                id: 'question',
                zindex: 1,
                title: 'Are you sure?',
                message: 'Do you really want to delete this edition?',
                position: 'center',
                buttons: [
                    ['<button><b>Yes</b></button>', function(instance, toast) {
                        // Perform the delete action here
                        deleteEdition(id);

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

        function deleteEdition(id) {
            $.ajax({
                url: '/editions/' + id,
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
                    // location.reload();
                }
            });
        }
    </script>
@endsection
