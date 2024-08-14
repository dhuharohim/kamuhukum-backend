@extends('masterpage')

@section('page_title')
    Submissions
@endsection

@section('page_content')
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        <h4>Submission List</h4>
        <div class="table-responsive">
            <table class="table table-borderless table-striped table-hover" id="articleTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="">Name Article</th>
                        <th>Status</th>
                        <th class=" text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($editions as $edition)
                        @foreach ($edition->articles as $article)
                            @if (in_array($article->status, ['submission', 'incomplete']))
                                <tr>
                                    <td width="70%">
                                        <p class="mb-0 text-truncate" style="max-width: 600px;">
                                            {{ \Illuminate\Support\Str::title($article->title) }}</p>
                                        <ul>
                                            @foreach ($article->authors as $author)
                                                <li style="font-size: 12px;">{{ $author->given_name }}
                                                    {{ $author->family_name }}
                                                    ({{ ucwords($author->contributor_role) }})
                                                    - {{ $author->affilation }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        @php
                                            $badge = '';
                                            switch ($article->status):
                                                case 'incomplete':
                                                    $badge = 'danger';
                                                    break;
                                                case 'submission':
                                                    $badge = 'warning';
                                                    break;
                                                case 'review':
                                                    $badge = 'info';
                                                    break;
                                                case 'production':
                                                    $badge = 'success';
                                                    break;
                                                default:
                                                    break;
                                            endswitch;
                                        @endphp
                                        <span class="badge bg-{{ $badge }}">{{ ucwords($article->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('articles.show', ['editionId' => $edition->id, 'article' => $article->id]) }}"
                                                class="btn btn-outline-dark btn-sm">View</a>
                                            @if (auth()->user()->hasRole(['admin_law', 'admin_economy']))
                                                <a onclick="confirmDelete('{{ $edition->id }}', '{{ $article->id }}')"
                                                    class="btn btn-outline-danger btn-sm">Delete</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('custom_js')
    <script>
        let table = new DataTable('#articleTable');

        function confirmDelete(editionId, id) {
            iziToast.question({
                timeout: 0,
                close: false,
                overlay: false,
                displayMode: 'once',
                id: 'question',
                zindex: 1,
                title: 'Are you sure?',
                message: 'Do you really want to delete this article?',
                position: 'center',
                buttons: [
                    ['<button><b>Yes</b></button>', function(instance, toast) {
                        // Perform the delete action here
                        deleteArticle(editionId, id);

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

        function deleteArticle(editionId, id) {
            $.ajax({
                url: '/articles/' + editionId + '/' + id,
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
