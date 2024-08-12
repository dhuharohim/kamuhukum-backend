@extends('masterpage')

@section('page_title')
    Article List
@endsection

@section('page_content')
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('editions.index') }}">Editions</a></li>
                <li class="breadcrumb-item active" aria-current="page">Article List on {{ $edition->edition_name_formatted }}
                </li>
            </ol>
        </nav>
        <div class="nftmax-table__heading">
            <h3 class="nftmax-table__title mb-0">Article List on {{ $edition->edition_name_formatted }}</h3>
            <a href="{{ route('articles.create', $edition->id) }}" class="btn btn-primary">Add Article</a>
        </div>
        <div class="table-responsive">
            <table class="table table-borderless table-striped table-hover" id="articleTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="">Name Article</th>
                        <th>Status</th>
                        <th class="">Published</th>
                        <th class=" text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articles as $article)
                        <tr>
                            <td width="30%">
                                <p class="mb-0 text-truncate" style="max-width: 600px;">
                                    {{ \Illuminate\Support\Str::title($article->title) }}</p>
                                <ul>
                                    @foreach ($article->authors as $author)
                                        <li style="font-size: 12px;">{{ $author->given_name }} {{ $author->family_name }}
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
                                {{ !empty($article->published_date) ? date('d M Y', strtotime($article->published_date)) : '' }}
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('articles.show', ['editionId' => $edition->id, 'article' => $article->id]) }}"
                                        class="btn btn-outline-dark btn-sm">View</a>
                                    <a onclick="confirmDelete('{{ $edition->id }}', '{{ $article->id }}')"
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
