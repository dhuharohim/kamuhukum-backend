<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Site keywords here">
    <meta name="description" content="#">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Site Title -->
    <title>KIB Journal - @yield('page_title')</title>

    <!-- Fav Icon -->
    <link rel="icon" href="assets/img/favicon.png">


    <!-- NFTMax Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome-all.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets/css/charts.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets/css/slickslider.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets/css/reset.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }} ">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css"
        integrity="sha512-pTaEn+6gF1IeWv3W1+7X7eM60TFu/agjgoHmYhAfLEU8Phuf6JKiiE8YmsNC0aCgQv4192s4Vai8YZ6VNM6vyQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.datatables.net/v/bs5/dt-2.1.3/datatables.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="body-bg h-100 overflow-auto" style="background-image:url('{{ asset('assets/img/body-bg.jpg') }}')">
        <!-- NFTMax Admin Menu -->
        <div class="nftmax-smenu">
            <!-- Admin Menu -->
            <div class="admin-menu">
                <!-- Logo -->
                <div class="logo">
                    <a href="/" class="fs-2">
                        KIB Journal
                    </a>
                    <div class="nftmax__sicon close-icon"><img src="{{ asset('assets/img/menu-toggle.svg') }}"
                            alt="#"></div>
                </div>
                <!-- Author Details -->
                <div class="admin-menu__one">
                    <h4 class="admin-menu__title nftmax-scolor">Menu</h4>
                    <!-- Nav Menu -->
                    <div class="menu-bar">
                        <ul class="menu-bar__one">
                            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><a
                                    href="{{ route('dashboard') }}"><span class="menu-bar__text"><span
                                            class="nftmax-menu-icon nftmax-svg-icon__v1"><svg class="nftmax-svg-icon"
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M0.800781 2.60005V7.40005H7.40078V0.800049H2.60078C2.12339 0.800049 1.66555 0.989691 1.32799 1.32726C0.990424 1.66482 0.800781 2.12266 0.800781 2.60005H0.800781Z">
                                                </path>
                                                <path
                                                    d="M13.4016 0.800049H8.60156V7.40005H15.2016V2.60005C15.2016 2.12266 15.0119 1.66482 14.6744 1.32726C14.3368 0.989691 13.879 0.800049 13.4016 0.800049V0.800049Z">
                                                </path>
                                                <path
                                                    d="M0.800781 13.4001C0.800781 13.8775 0.990424 14.3353 1.32799 14.6729C1.66555 15.0105 2.12339 15.2001 2.60078 15.2001H7.40078V8.6001H0.800781V13.4001Z">
                                                </path>
                                                <path
                                                    d="M8.60156 15.2001H13.4016C13.879 15.2001 14.3368 15.0105 14.6744 14.6729C15.0119 14.3353 15.2016 13.8775 15.2016 13.4001V8.6001H8.60156V15.2001Z">
                                                </path>
                                            </svg></span><span class="menu-bar__name">Dashboard</span></span></a></li>
                            <li class="{{ request()->routeIs(['editions.*', 'articles.*']) ? 'active' : '' }}">
                                <a href="{{ route('editions.index') }}"><span class="menu-bar__text">
                                        <span class="nftmax-menu-icon nftmax-svg-icon__v9">
                                            <svg class="nftmax-svg-icon" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 103.19 122.88">
                                                <path
                                                    d="M17.16 0h82.72a3.32 3.32 0 013.31 3.31v92.32c-.15 2.58-3.48 2.64-7.08 2.48H15.94c-4.98 0-9.05 4.07-9.05 9.05s4.07 9.05 9.05 9.05h80.17v-9.63h7.08v12.24c0 2.23-1.82 4.05-4.05 4.05H16.29C7.33 122.88 0 115.55 0 106.59V17.16C0 7.72 7.72 0 17.16 0zm3.19 13.4h2.86c1.46 0 2.66.97 2.66 2.15v67.47c0 1.18-1.2 2.15-2.66 2.15h-2.86c-1.46 0-2.66-.97-2.66-2.15V15.55c.01-1.19 1.2-2.15 2.66-2.15z"
                                                    fill-rule="evenodd" clip-rule="evenodd" />
                                            </svg>

                                        </span>
                                        <span class="menu-bar__name">Editions</span></span>
                                </a>
                            </li>
                            <li class="{{ request()->routeIs(['submissions.*']) ? 'active' : '' }}">
                                <a href="{{ route('submissions.index') }}"><span class="menu-bar__text">
                                        <span class="nftmax-menu-icon nftmax-svg-icon__v9">
                                            <svg class="nftmax-svg-icon" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd"
                                                    d="M3.559 4.544c.355-.35.834-.544 1.33-.544H19.11c.496 0 .975.194 1.33.544.356.35.559.829.559 1.331v9.25c0 .502-.203.981-.559 1.331-.355.35-.834.544-1.33.544H15.5l-2.7 3.6a1 1 0 0 1-1.6 0L8.5 17H4.889c-.496 0-.975-.194-1.33-.544A1.868 1.868 0 0 1 3 15.125v-9.25c0-.502.203-.981.559-1.331ZM7.556 7.5a1 1 0 1 0 0 2h8a1 1 0 0 0 0-2h-8Zm0 3.5a1 1 0 1 0 0 2H12a1 1 0 1 0 0-2H7.556Z"
                                                    clip-rule="evenodd" />
                                            </svg>

                                        </span>
                                        <span class="menu-bar__name">Submissions</span></span>
                                </a>
                            </li>
                            @if (auth()->user()->hasRole(['admin_law', 'admin_economy']))
                                <li class="{{ request()->routeIs(['announcements.*']) ? 'active' : '' }}">
                                    <a href="{{ route('announcements.index') }}"><span class="menu-bar__text">
                                            <span class="nftmax-menu-icon nftmax-svg-icon__v9">
                                                <svg class="nftmax-svg-icon" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd"
                                                        d="M18.458 3.11A1 1 0 0 1 19 4v16a1 1 0 0 1-1.581.814L12 16.944V7.056l5.419-3.87a1 1 0 0 1 1.039-.076ZM22 12c0 1.48-.804 2.773-2 3.465v-6.93c1.196.692 2 1.984 2 3.465ZM10 8H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6V8Zm0 9H5v3a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-3Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            <span class="menu-bar__name">Announcements</span></span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <!-- End Nav Menu -->
                </div>
                @if (auth()->user()->hasRole(['admin_law', 'admin_economy']))
                    <div class="admin-menu__two mg-top-50">
                        <h4 class="admin-menu__title nftmax-scolor">Settings</h4>
                        <!-- Nav Menu -->
                        <div class="menu-bar">
                            <ul class="menu-bar__one">
                                <li class="{{ request()->routeIs(['users-access.*']) ? 'active' : '' }}"><a
                                        href="{{ route('users-access.index') }}"><span class="menu-bar__text"><span
                                                class="nftmax-menu-icon nftmax-svg-icon__v10"><svg
                                                    class="nftmax-svg-icon" viewBox="0 0 15 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10.8692 11.6667H4.13085C3.03569 11.668 1.98576 12.1036 1.21136 12.878C0.436961 13.6524 0.00132319 14.7023 0 15.7975V20H15.0001V15.7975C14.9987 14.7023 14.5631 13.6524 13.7887 12.878C13.0143 12.1036 11.9644 11.668 10.8692 11.6667Z">
                                                    </path>
                                                    <path
                                                        d="M7.49953 10C10.261 10 12.4995 7.76145 12.4995 5.00002C12.4995 2.23858 10.261 0 7.49953 0C4.7381 0 2.49951 2.23858 2.49951 5.00002C2.49951 7.76145 4.7381 10 7.49953 10Z">
                                                    </path>
                                                </svg></span><span class="menu-bar__name">Users</span> </span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Logout Button -->
                <div class="logout-button">
                    <a class="nftmax-btn primary" data-bs-toggle="modal" data-bs-target="#logout_modal">
                        <div class="logo-button__icon"><img src="{{ asset('assets/img/logout.png') }}"
                                alt="#">
                        </div><span class="menu-bar__name">Signout</span>
                    </a>
                </div>
            </div>
            <!-- End Admin Menu -->
        </div>
        <!-- End NFTMax Admin Menu -->

        <!-- Logout Modal  -->
        <div class="nftmax-preview__modal modal fade" id="logout_modal" tabindex="-1" aria-labelledby="logoutmodal"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered nftmax-close__modal-close">
                <div class="modal-content nftmax-preview__modal-content">
                    <div class="modal-header nftmax__modal__header">
                        <h4 class="modal-title nftmax-preview__modal-title" id="logoutmodal">Confirm</h4>
                        <button type="button" class="nftmax-preview__modal--close btn-close" data-bs-dismiss="modal"
                            aria-label="Close"><svg width="36" height="36" viewBox="0 0 36 36"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M36 16.16C36 17.4399 36 18.7199 36 20.0001C35.7911 20.0709 35.8636 20.2554 35.8385 20.4001C34.5321 27.9453 30.246 32.9248 22.9603 35.2822C21.9006 35.6251 20.7753 35.7657 19.6802 35.9997C18.4003 35.9997 17.1204 35.9997 15.8401 35.9997C15.5896 35.7086 15.2189 35.7732 14.9034 35.7093C7.77231 34.2621 3.08728 30.0725 0.769671 23.187C0.435002 22.1926 0.445997 21.1199 0 20.1599C0 18.7198 0 17.2798 0 15.8398C0.291376 15.6195 0.214408 15.2656 0.270759 14.9808C1.71321 7.69774 6.02611 2.99691 13.0428 0.700951C14.0118 0.383805 15.0509 0.386897 15.9999 0C17.2265 0 18.4532 0 19.6799 0C19.7156 0.124041 19.8125 0.136067 19.9225 0.146719C27.3 0.868973 33.5322 6.21922 35.3801 13.427C35.6121 14.3313 35.7945 15.2484 36 16.16ZM33.011 18.0787C33.0433 9.77105 26.3423 3.00309 18.077 2.9945C9.78479 2.98626 3.00344 9.658 2.98523 17.8426C2.96667 26.1633 9.58859 32.9601 17.7602 33.0079C26.197 33.0577 32.9787 26.4186 33.011 18.0787Z"
                                    fill="#374557" fill-opacity="0.6"></path>
                                <path
                                    d="M15.9309 18.023C13.9329 16.037 12.007 14.1207 10.0787 12.2072C9.60071 11.733 9.26398 11.2162 9.51996 10.506C9.945 9.32677 11.1954 9.0811 12.1437 10.0174C13.9067 11.7585 15.6766 13.494 17.385 15.2879C17.9108 15.8401 18.1633 15.7487 18.6375 15.258C20.3586 13.4761 22.1199 11.7327 23.8822 9.99096C24.8175 9.06632 26.1095 9.33639 26.4967 10.517C26.7286 11.2241 26.3919 11.7413 25.9133 12.2178C24.1757 13.9472 22.4477 15.6855 20.7104 17.4148C20.5228 17.6018 20.2964 17.7495 20.0466 17.9485C22.0831 19.974 24.0372 21.8992 25.9689 23.8468C26.9262 24.8119 26.6489 26.1101 25.4336 26.4987C24.712 26.7292 24.2131 26.3441 23.7455 25.8757C21.9945 24.1227 20.2232 22.3892 18.5045 20.6049C18.0698 20.1534 17.8716 20.2269 17.4802 20.6282C15.732 22.4215 13.9493 24.1807 12.1777 25.951C11.7022 26.4262 11.193 26.7471 10.4738 26.4537C9.31345 25.9798 9.06881 24.8398 9.98589 23.8952C11.285 22.5576 12.6138 21.2484 13.9387 19.9355C14.5792 19.3005 15.2399 18.6852 15.9309 18.023Z"
                                    fill="#374557" fill-opacity="0.6"></path>
                            </svg></button>
                    </div>
                    <div class="modal-body nftmax-modal__body modal-body nftmax-close__body">
                        <div class="nftmax-preview__close">
                            <div class="nftmax-preview__close-img"><img src="{{ asset('assets/img/close.png') }}"
                                    alt="#">
                            </div>
                            <h2 class="nftmax-preview__close-title">Are you sure you want to Logout?</h2>
                            <div class="nftmax__item-button--group">
                                <form action="{{ route('auth-backbone.logout') }}" method="POST">
                                    @csrf
                                    <button
                                        class="nftmax__item-button--single nftmax-btn nftmax-btn__bordered bg radius "
                                        type="submit">Yes Logout
                                    </button>
                                </form>
                                <button
                                    class="nftmax__item-button--single nftmax-btn nftmax-btn__bordered--plus radius"
                                    data-bs-dismiss="modal"><span class="ntfmax__btn-textgr">Not Now</span> </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Logout Modal -->

        <!-- Start Header -->
        <header class="nftmax-header">
            <div class="container-fluid">
                <!-- Dashboard Header -->
                <div class="nftmax-header__inner">
                    <div class="nftmax__sicon close-icon d-xl-none"><img
                            src="{{ asset('assets/img/menu-toggle.svg') }}" alt="#"></div>
                    <div class="nftmax-header__left" style="">
                        @php
                            $domain = auth()
                                ->user()
                                ->hasRole(['admin_law', 'editor_law'])
                                ? 'legisinsightjournal.com'
                                : 'oeajournal.com';
                        @endphp
                        <h4 style="text-indent: 1rem;" class="mb-0">{{ $domain }}</h4>
                    </div>
                </div>
            </div>
        </header>
