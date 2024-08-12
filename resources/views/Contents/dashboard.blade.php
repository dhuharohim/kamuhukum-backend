@extends('masterpage')

@section('page_title')
    Dashboard
@endsection

@section('page_content')
    <!-- NFTmax Dashboard -->
    <div class="container">
        <div class="row">
            <div class="col-xxl-9 col-12 nftmax-main__column">
                <div class="nftmax-body">
                    <!-- Dashboard Inner -->
                    <div class="nftmax-dsinner">
                        <!-- Welcome CTA -->
                        <div class="welcome-cta mg-top-40">
                            <div class="welcome-cta__heading">
                                <h2 class="welcome-cta__title">
                                    @php
                                        $hour = date('H');
                                        $greeting = '';

                                        if ($hour >= 5 && $hour < 12) {
                                            $greeting = 'Good Morning';
                                        } elseif ($hour >= 12 && $hour < 17) {
                                            $greeting = 'Good Afternoon';
                                        } elseif ($hour >= 17 && $hour < 21) {
                                            $greeting = 'Good Evening';
                                        } else {
                                            $greeting = 'Good Night';
                                        }

                                        $formattedUsername = ucwords(str_replace('_', ' ', $user->username));

                                        // Append the formatted username to the greeting
                                        $greeting .= ' ' . $formattedUsername;
                                    @endphp
                                    {{ $greeting }}
                                </h2>
                                <p class="welcome-cta__text nftmax-lspacing">
                                    Create a new edition for {{ $journal }}
                                </p>
                            </div>
                            <div class="welcome-cta__button">
                                <a href="{{ route('editions.create') }}"
                                    class="nftmax-btn nftmax-btn__bordered bg radius">Create Edition</a>
                                <a href="{{ route('submissions.index') }}" class="nftmax-btn trs-white bl-color">View
                                    Submissions</a>
                            </div>
                        </div>
                        <!-- End Welcome CTA -->
                    </div>
                    <!-- End Dashboard Inner -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
    <script></script>
@endsection
