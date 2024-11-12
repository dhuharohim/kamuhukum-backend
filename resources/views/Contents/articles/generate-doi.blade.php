@extends('masterpage')

@section('page_title')
    Generate DOI Links for {{ $edition->edition_name_formatted }}
@endsection

@section('page_content')
    <div class="nftmax-table welcome-cta mg-top-40 d-block">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('editions.index') }}">Editions</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ route('articles.index', $edition->id) }}">Article
                        List on
                        {{ $edition->edition_name_formatted }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Generate DOI Links</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    Note: Only articles with "Production" status and published dates and without DOI links are displayed
                    here.
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-borderless table-striped table-hover" id="articleTable">
                <thead class="bg-info text-white">
                    <tr>
                        <th class="">Name Article</th>
                        <th class="">Published</th>
                        <th>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label text-white" for="selectAll">Select All</label>
                            </div>
                        </th>
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
                                        <li style="font-size: 12px;">
                                            <span class="badge bg-secondary">{{ ucwords($author->contributor_role) }}</span>
                                            {{ $author->given_name }} {{ $author->family_name }}
                                            - {{ $author->affilation }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                {{ !empty($article->published_date) ? date('d M Y', strtotime($article->published_date)) : '' }}
                            </td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="selected_articles[]"
                                        value="{{ $article->id }}">
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" align="right">
                            <button class="btn btn-primary" id="generateDoi">
                                <i class="fa fa-spinner fa-spin d-none" id="generateDoiLoader"></i>
                                Generate DOI for selected articles
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('custom_js')
    <script>
        $(function() {
            $('#selectAll').on('click', function() {
                $('input[name="selected_articles[]"]').prop('checked', this.checked);
            });

            $('#generateDoi').on('click', function() {
                const selectedArticles = $('input[name="selected_articles[]"]:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedArticles.length === 0) {
                    iziToast.info({
                        title: 'Info',
                        message: 'No articles selected, please select at least one article',
                    });
                    return;
                }

                iziToast.question({
                    timeout: 0,
                    close: false,
                    overlay: false,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 1,
                    title: 'Are you sure?',
                    message: 'Do you want to generate DOI for selected articles?',
                    position: 'center',
                    buttons: [
                        ['<button><b>Yes</b></button>', function(instance, toast) {
                            // Close the iziToast notification
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            generateDoi(selectedArticles);
                        }, true],
                        ['<button>No</button>', function(instance, toast) {
                            // Just close the iziToast notification
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                        }]
                    ]
                });
            });
        });

        function generateDoi(selectedArticles) {
            $('#generateDoi').prop('disabled', true);
            $('#generateDoiLoader').removeClass('d-none');
            $.ajax({
                url: "{{ route('articles.generateDoiForSelectedArticles', $edition->id) }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    articles: selectedArticles
                },
                success: function(response) {
                    iziToast.success({
                        title: 'Success',
                        message: 'DOI generated successfully',
                        position: 'topRight'
                    });
                    window.location.href = "{{ route('articles.index', $edition->id) }}";
                },
                error: function(error) {
                    iziToast.error({
                        title: 'Error',
                        message: 'Failed to generate DOI',
                        position: 'topRight'
                    });
                    window.location.reload();
                }
            });
            $('#generateDoi').prop('disabled', false);
            $('#generateDoiLoader').addClass('d-none');
        }
    </script>
@endsection
