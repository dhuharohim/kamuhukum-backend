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
        <div class="row justify-content-between align-items-center">
            <h3 class="nftmax-table__title mb-0 col-md-6">Article List on {{ $edition->edition_name_formatted }}</h3>
            @if (auth()->user()->hasRole(['admin_law', 'admin_economy']))
                <div class="d-flex gap-2 col-md-6 justify-content-end">
                    <a href="{{ route('articles.create', $edition->id) }}" class="btn btn-primary">Add Article</a>
                    <a href="{{ route('articles.generateDoi', $edition->id) }}" class="btn btn-outline-success">Generate
                        DOI
                        Links</a>
                </div>
            @endif
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
                                <div class="d-flex justify-content-between">
                                    <ul>
                                        @foreach ($article->authors as $author)
                                            <li style="font-size: 12px;">
                                                <span
                                                    class="badge bg-secondary">{{ ucwords($author->contributor_role) }}</span>
                                                {{ $author->given_name }} {{ $author->family_name }}
                                                - {{ $author->affilation }}
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if ($article->status == 'production' && !empty($article->doi_link))
                                        <a href="{{ $article->doi_link_formatted }}" target="_blank"
                                            class="badge bg-success text-sm mb-0 align-content-center"><i
                                                class="fa fa-link"></i>
                                            {{ $article->doi_link }}</a>
                                    @endif
                                </div>
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
                                    @if (
                                        $article->editors()->where('user_id', Auth::id())->exists() ||
                                            auth()->user()->hasRole(['admin_law', 'admin_economy']))
                                        <a href="{{ route('submissions.show', ['submission' => $article->id]) }}"
                                            class="btn btn-outline-dark btn-sm"><i class="fa fa-eye"></i></a>
                                    @endif
                                    @if (auth()->user()->hasRole(['admin_law', 'admin_economy']))
                                        <a onclick="confirmDelete('{{ $edition->id }}', '{{ $article->id }}')"
                                            class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i></a>
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
