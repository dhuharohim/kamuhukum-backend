@include('Layout.Header')

<!-- NFTmax Dashboard -->
<section class="nftmax-adashboard nftmax-show">
    <div class="error-message mt-4">
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    @yield('page_content')
</section>
@include('Layout.Footer')

@yield('custom_js')
</body>

</html>
