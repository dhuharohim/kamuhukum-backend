    <div class="nftmax-sidebar mg-top-40">
        <div class="row">
            <div class="col-xxl-12 col-xl-6 col-12 nftmax-sidebar__widget">
                <!-- NFTMax Single Sidebar -->
                <div class="nftmax-sidebar__single">
                    <!-- Sidebar Heading -->
                    <div class="nftmax-sidebar__heading">
                        <h4 class="nftmax-sidebar__title">Statistics</h4>
                        <ul class="nav nav-tabs nftmax-dropdown__list" id="nav-tab" role="tablist">
                            <li class="nav-item dropdown">
                                <a class="nftmax-sidebar_btn nftmax-heading__tabs nav-link dropdown-toggle"
                                    data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Today
                                    <span class="nftmax-sidebar__arrow--icon"><svg width="13" height="6"
                                            viewBox="0 0 13 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.7"
                                                d="M12.4124 0.247421C12.3327 0.169022 12.2379 0.106794 12.1335 0.0643287C12.0291 0.0218632 11.917 0 11.8039 0C11.6908 0 11.5787 0.0218632 11.4743 0.0643287C11.3699 0.106794 11.2751 0.169022 11.1954 0.247421L7.27012 4.07837C7.19045 4.15677 7.09566 4.219 6.99122 4.26146C6.88678 4.30393 6.77476 4.32579 6.66162 4.32579C6.54848 4.32579 6.43646 4.30393 6.33202 4.26146C6.22758 4.219 6.13279 4.15677 6.05312 4.07837L2.12785 0.247421C2.04818 0.169022 1.95338 0.106794 1.84895 0.0643287C1.74451 0.0218632 1.63249 0 1.51935 0C1.40621 0 1.29419 0.0218632 1.18975 0.0643287C1.08531 0.106794 0.990517 0.169022 0.910844 0.247421C0.751218 0.404141 0.661621 0.616141 0.661621 0.837119C0.661621 1.0581 0.751218 1.2701 0.910844 1.42682L4.84468 5.26613C5.32677 5.73605 5.98027 6 6.66162 6C7.34297 6 7.99647 5.73605 8.47856 5.26613L12.4124 1.42682C12.572 1.2701 12.6616 1.0581 12.6616 0.837119C12.6616 0.616141 12.572 0.404141 12.4124 0.247421Z"
                                                fill="#374557" fill-opacity="0.6"></path>
                                        </svg></span></a>
                                <ul class="dropdown-menu nftmax-sidebar_dropdown">
                                    <a class="list-group-item active" data-bs-toggle="list" data-bs-target="#side__one"
                                        role="tab">Daily</a>
                                    <a class="list-group-item" data-bs-toggle="list" data-bs-target="#side__one_weekly"
                                        role="tab">Weekly</a>
                                    <a class="list-group-item" data-bs-toggle="list"data-bs-target="#side__one_monthly"
                                        role="tab">Monthly</a>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="nav-tabContent">
                        <!-- Single Tab -->
                        <div class="tab-pane fade show active" id="side__one" role="tabpanel"
                            aria-labelledby="side__one">
                            <!-- Platform List -->
                            <div class="nftmax-sidebar__charts nftmax-sidebar__charts--v1">
                                <div class="nftmax-sidebar__cside-one">
                                    <canvas id="myChart_Side_One"></canvas>
                                    <div class="nftmax-sidebar__cicon"><img src="assets/img/charts-middle.png"
                                            alt="#"></div>
                                </div>
                                <div class="nftmax-sidebar__cstatics">
                                    <h4 class="nftmax-sidebar__cstatics-title">Daily Artwork Statistics</h4>
                                    <ul class="nftmax-sidebar__clist">
                                        <li><span class="nftmax-sidebar__ccolor nftmax-sidebar__ccolor-one"></span>Total
                                            Sold : $Statistics['TotalSold'] </li>
                                        <li><span class="nftmax-sidebar__ccolor nftmax-sidebar__ccolor-two"></span>Total
                                            Cancel : $Statistics['TotalCancel'] </li>
                                        <li><span
                                                class="nftmax-sidebar__ccolor nftmax-sidebar__ccolor-three"></span>Total
                                            Panding : $Statistics['TotalPanding'] </li>
                                    </ul>

                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="side__one_weekly" role="tabpanel"
                            aria-labelledby="side__one">
                            <!-- Platform List -->
                            <div class="nftmax-sidebar__charts nftmax-sidebar__charts--v1">
                                <div class="nftmax-sidebar__cside-one">
                                    <canvas id="myChart_Side_One_weekly"></canvas>
                                    <div class="nftmax-sidebar__cicon"><img src="assets/img/charts-middle.png"
                                            alt="#"></div>
                                </div>
                                <div class="nftmax-sidebar__cstatics">
                                    <h4 class="nftmax-sidebar__cstatics-title">Weekly Artwork Statistics</h4>
                                    <ul class="nftmax-sidebar__clist">
                                        <li><span class="nftmax-sidebar__ccolor nftmax-sidebar__ccolor-one"></span>Total
                                            Sold : $Statistics_weekly['TotalSold'] </li>
                                        <li><span class="nftmax-sidebar__ccolor nftmax-sidebar__ccolor-two"></span>Total
                                            Cancel : $Statistics_weekly['TotalCancel'] </li>
                                        <li><span
                                                class="nftmax-sidebar__ccolor nftmax-sidebar__ccolor-three"></span>Total
                                            Panding : $Statistics_weekly['TotalPanding'] </li>
                                    </ul>

                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="side__one_monthly" role="tabpanel"
                            aria-labelledby="side__one">
                            <!-- Platform List -->
                            <div class="nftmax-sidebar__charts nftmax-sidebar__charts--v1">
                                <div class="nftmax-sidebar__cside-one">
                                    <canvas id="myChart_Side_One_monthly"></canvas>
                                    <div class="nftmax-sidebar__cicon"><img src="assets/img/charts-middle.png"
                                            alt="#"></div>
                                </div>
                                <div class="nftmax-sidebar__cstatics">
                                    <h4 class="nftmax-sidebar__cstatics-title">Monthly Artwork Statistics</h4>
                                    <ul class="nftmax-sidebar__clist">
                                        <li><span class="nftmax-sidebar__ccolor nftmax-sidebar__ccolor-one"></span>Total
                                            Sold : $Statistics_monthly['TotalSold'] </li>
                                        <li><span class="nftmax-sidebar__ccolor nftmax-sidebar__ccolor-two"></span>Total
                                            Cancel : $Statistics_monthly['TotalCancel'] </li>
                                        <li><span
                                                class="nftmax-sidebar__ccolor nftmax-sidebar__ccolor-three"></span>Total
                                            Panding : $Statistics_monthly['TotalPanding'] </li>
                                    </ul>

                                </div>
                            </div>
                        </div>


                        <!-- End Single Tab -->
                    </div>
                </div>
                <!-- End NFTMax Single Sidebar -->
            </div>

            <div class="col-xxl-12 col-xl-6 col-12 nftmax-sidebar__widget">
                <!-- NFTMax Single Sidebar -->
                <div class="nftmax-sidebar__single">
                    <ul class="nav nav-tabs nftmax-dropdown__list" id="nav-tab" role="tablist">
                        <li class="nav-item dropdown nftmax-multiple__adropdownn">
                            <a class="nftmax-heading__amount-dropdown nav-link  dropdown-toggle"
                                data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                                <div class="nftmax__amount-dropdown"><img src="assets/img/eth-icon.png"
                                        alt="#">ETH rate<span class="nftmax-sidebar__arrow--icon"><svg
                                            width="14" height="7" viewBox="0 0 14 7" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M13.7092 0.288658C13.6163 0.197192 13.5057 0.124593 13.3839 0.0750502C13.262 0.025507 13.1313 0 12.9993 0C12.8673 0 12.7366 0.025507 12.6148 0.0750502C12.4929 0.124593 12.3824 0.197192 12.2894 0.288658L7.70992 4.7581C7.61697 4.84956 7.50638 4.92216 7.38453 4.9717C7.26269 5.02125 7.132 5.04676 7 5.04676C6.868 5.04676 6.73731 5.02125 6.61547 4.9717C6.49362 4.92216 6.38303 4.84956 6.29008 4.7581L1.7106 0.288658C1.61765 0.197192 1.50706 0.124593 1.38521 0.0750502C1.26337 0.025507 1.13268 0 1.00068 0C0.868682 0 0.737991 0.025507 0.616146 0.0750502C0.4943 0.124593 0.383712 0.197192 0.29076 0.288658C0.10453 0.471497 0 0.718831 0 0.976639C0 1.23445 0.10453 1.48178 0.29076 1.66462L4.88024 6.14382C5.44268 6.69206 6.20509 7 7 7C7.79491 7 8.55732 6.69206 9.11976 6.14382L13.7092 1.66462C13.8955 1.48178 14 1.23445 14 0.976639C14 0.718831 13.8955 0.471497 13.7092 0.288658Z"
                                                fill="#374557"></path>
                                        </svg></span></div>
                            </a>
                            <ul class="dropdown-menu nftmax-sidebar_dropdown">
                                <a class="list-group-item" data-bs-toggle="tab" data-bs-target="#side__two"
                                    role="tab">
                                    <div class="nftmax__amount-dropdown"><img src="assets/img/eth-icon.png"
                                            alt="#">ETH rate</div>
                                </a>
                                <a class="list-group-item" data-bs-toggle="tab" data-bs-target="#side__two_BTC"
                                    role="tab">
                                    <div class="nftmax__amount-dropdown"><img src="assets/img/btc-icon.png"
                                            alt="#">BTC rate</div>
                                </a>
                            </ul>
                        </li>
                    </ul>

                    <div class="tab-content" id="nav-tabContent">
                        <!-- Single Tab -->
                        <div class="tab-pane fade show active" id="side__two" role="tabpanel"
                            aria-labelledby="side__two">
                            <div class="nftmax-amount__statics">
                                <h4 class="nftmax-amount__statics__title">$ $transectionRateData['usd'] USD</h4>
                                <p class="nftmax-amount__statics__text">+ $transectionRateData['growth'] (
                                    $transectionRateData['growth_percentage'] %)</p>
                            </div>

                            <div class="nftmax-sidebar__cside-one">
                                <canvas id="myChart_Side_Two"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="side__two_BTC" role="tabpanel"
                            aria-labelledby="side__two">
                            <div class="nftmax-amount__statics">
                                <h4 class="nftmax-amount__statics__title">$ $transectionRateBTCData['usd'] USD</h4>
                                <p class="nftmax-amount__statics__text">+ $transectionRateBTCData['growth'] (
                                    $transectionRateBTCData['growth_percentage'] %)</p>
                            </div>

                            <div class="nftmax-sidebar__cside-one">
                                <canvas id="myChart_Side_Two_BTC"></canvas>
                            </div>
                        </div>
                        <!-- End Single Tab -->
                    </div>
                </div>
                <!-- End NFTMax Single Sidebar -->
            </div>

            <div class="col-xxl-12 col-xl-6 col-12 nftmax-sidebar__widget">
                <!-- NFTMax Single Sidebar -->
                <div class="nftmax-sidebar__single">
                    <!-- Sidebar Heading -->
                    <div class="nftmax-sidebar__heading">
                        <h4 class="nftmax-sidebar__title">Top Creators</h4>
                        <ul class="nav nav-tabs nftmax-dropdown__list" id="nav-tab" role="tablist">
                            <li class="nav-item dropdown">
                                <a class="nftmax-sidebar_btn nftmax-heading__tabs nav-link dropdown-toggle"
                                    data-bs-toggle="dropdown" href="#" role="button"
                                    aria-expanded="false">View All <svg width="13" height="6"
                                        viewBox="0 0 13 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.7"
                                            d="M12.4124 0.247421C12.3327 0.169022 12.2379 0.106794 12.1335 0.0643287C12.0291 0.0218632 11.917 0 11.8039 0C11.6908 0 11.5787 0.0218632 11.4743 0.0643287C11.3699 0.106794 11.2751 0.169022 11.1954 0.247421L7.27012 4.07837C7.19045 4.15677 7.09566 4.219 6.99122 4.26146C6.88678 4.30393 6.77476 4.32579 6.66162 4.32579C6.54848 4.32579 6.43646 4.30393 6.33202 4.26146C6.22758 4.219 6.13279 4.15677 6.05312 4.07837L2.12785 0.247421C2.04818 0.169022 1.95338 0.106794 1.84895 0.0643287C1.74451 0.0218632 1.63249 0 1.51935 0C1.40621 0 1.29419 0.0218632 1.18975 0.0643287C1.08531 0.106794 0.990517 0.169022 0.910844 0.247421C0.751218 0.404141 0.661621 0.616141 0.661621 0.837119C0.661621 1.0581 0.751218 1.2701 0.910844 1.42682L4.84468 5.26613C5.32677 5.73605 5.98027 6 6.66162 6C7.34297 6 7.99647 5.73605 8.47856 5.26613L12.4124 1.42682C12.572 1.2701 12.6616 1.0581 12.6616 0.837119C12.6616 0.616141 12.572 0.404141 12.4124 0.247421Z"
                                            fill="#374557" fill-opacity="0.6"></path>
                                    </svg></a>
                                <ul class="dropdown-menu nftmax-sidebar_dropdown">
                                    <a class="list-group-item" data-bs-toggle="tab" data-bs-target="#daily"
                                        role="tab">Daily</a>
                                    <a class="list-group-item" data-bs-toggle="tab" data-bs-target="#weekly"
                                        role="tab">Weekly</a>
                                    <a class="list-group-item" data-bs-toggle="tab" data-bs-target="#monthly"
                                        role="tab">Monthly</a>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!-- Sidebar Creator Lists -->
                    <div class="nftmax-sidebar__creators">
                        <div class="tab-content" id="nav-tabContent">
                            <!-- Single Tab -->
                            <div class="tab-pane fade show active" id="daily" role="tabpanel"
                                aria-labelledby="nav-home-tab">
                                <ul class="nftmax-sidebar__creatorlist">
                                    {{-- @foreach ($TopCreators as $TopCreators) --}}
                                    <li>
                                        <div class="nftmax-sidebar__creator">
                                            <img src=" asset($TopCreators['0']) " alt="#">
                                            <a href="#"><b class="nftmax-sidebar__creator-name">
                                                    $TopCreators['1'] <span class="nftmax-sidebar__creator-link">
                                                        $TopCreators['2'] </span></b></a>
                                        </div>
                                        <div class="nftmax-sidebar__button">
                                            <a href="#"
                                                class="nftmax-sidebar__button-btn nftmax-request_request"><svg
                                                    width="13" height="12" viewBox="0 0 13 12" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.1351 5.4852H11.1378V4.4879C11.1378 4.2125 10.9145 3.98926 10.6391 3.98926C10.3637 3.98926 10.1405 4.2125 10.1405 4.4879V5.4852H9.14317C8.86778 5.4852 8.64453 5.70845 8.64453 5.98384C8.64453 6.25924 8.86778 6.48248 9.14317 6.48248H10.1405V7.47979C10.1405 7.75518 10.3637 7.97843 10.6391 7.97843C10.9145 7.97843 11.1378 7.75518 11.1378 7.47979V6.48248H12.1351C12.4105 6.48248 12.6337 6.25924 12.6337 5.98384C12.6337 5.70845 12.4105 5.4852 12.1351 5.4852Z"
                                                        fill="white"></path>
                                                    <path
                                                        d="M5.15595 5.98378C6.80833 5.98378 8.14784 4.64426 8.14784 2.99189C8.14784 1.33951 6.80833 0 5.15595 0C3.50358 0 2.16406 1.33951 2.16406 2.99189C2.16406 4.64426 3.50358 5.98378 5.15595 5.98378Z"
                                                        fill="white"></path>
                                                    <path
                                                        d="M5.1558 6.98096C2.67838 6.98372 0.670727 8.99137 0.667969 11.4688C0.667969 11.7442 0.891215 11.9674 1.16661 11.9674H9.14497C9.42037 11.9674 9.64361 11.7442 9.64361 11.4688C9.64088 8.99137 7.63323 6.98369 5.1558 6.98096Z"
                                                        fill="white"></path>
                                                </svg></a>
                                            <a href="#"
                                                class="nftmax-sidebar__button-btn nftmax-request_close"><svg
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"></path>
                                                </svg></a>
                                        </div>
                                    </li>
                                    {{-- @endforeach --}}
                                </ul>
                            </div>
                            <!-- Single Tab -->
                            <div class="tab-pane fade show" id="weekly" role="tabpanel"
                                aria-labelledby="nav-profile-tab">
                                <ul class="nftmax-sidebar__creatorlist">
                                    {{-- @foreach ($TopCreatorsWeekly as $TopCreatorsWeekly) --}}
                                    <li>
                                        <div class="nftmax-sidebar__creator">
                                            <img src=" asset($TopCreatorsWeekly['0']) " alt="#">
                                            <a href="#"><b class="nftmax-sidebar__creator-name">
                                                    $TopCreatorsWeekly['1'] <span class="nftmax-sidebar__creator-link">
                                                        $TopCreatorsWeekly['2'] </span></b></a>
                                        </div>
                                        <div class="nftmax-sidebar__button">
                                            <a href="#"
                                                class="nftmax-sidebar__button-btn nftmax-request_request"><svg
                                                    width="13" height="12" viewBox="0 0 13 12" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.1351 5.4852H11.1378V4.4879C11.1378 4.2125 10.9145 3.98926 10.6391 3.98926C10.3637 3.98926 10.1405 4.2125 10.1405 4.4879V5.4852H9.14317C8.86778 5.4852 8.64453 5.70845 8.64453 5.98384C8.64453 6.25924 8.86778 6.48248 9.14317 6.48248H10.1405V7.47979C10.1405 7.75518 10.3637 7.97843 10.6391 7.97843C10.9145 7.97843 11.1378 7.75518 11.1378 7.47979V6.48248H12.1351C12.4105 6.48248 12.6337 6.25924 12.6337 5.98384C12.6337 5.70845 12.4105 5.4852 12.1351 5.4852Z"
                                                        fill="white"></path>
                                                    <path
                                                        d="M5.15595 5.98378C6.80833 5.98378 8.14784 4.64426 8.14784 2.99189C8.14784 1.33951 6.80833 0 5.15595 0C3.50358 0 2.16406 1.33951 2.16406 2.99189C2.16406 4.64426 3.50358 5.98378 5.15595 5.98378Z"
                                                        fill="white"></path>
                                                    <path
                                                        d="M5.1558 6.98096C2.67838 6.98372 0.670727 8.99137 0.667969 11.4688C0.667969 11.7442 0.891215 11.9674 1.16661 11.9674H9.14497C9.42037 11.9674 9.64361 11.7442 9.64361 11.4688C9.64088 8.99137 7.63323 6.98369 5.1558 6.98096Z"
                                                        fill="white"></path>
                                                </svg></a>
                                            <a href="#"
                                                class="nftmax-sidebar__button-btn nftmax-request_close"><svg
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"></path>
                                                </svg></a>
                                        </div>
                                    </li>
                                    {{-- @endforeach --}}
                                </ul>
                            </div>
                            <div class="tab-pane fade show" id="monthly" role="tabpanel"
                                aria-labelledby="nav-profile-tab">
                                <ul class="nftmax-sidebar__creatorlist">
                                    {{-- @foreach ($TopCreatorsMonthly as $TopCreatorsMonthly) --}}
                                    <li>
                                        <div class="nftmax-sidebar__creator">
                                            <img src=" asset($TopCreatorsMonthly['0']) " alt="#">
                                            <a href="#"><b class="nftmax-sidebar__creator-name">
                                                    $TopCreatorsMonthly['1'] <span
                                                        class="nftmax-sidebar__creator-link"> $TopCreatorsMonthly['2']
                                                    </span></b></a>
                                        </div>
                                        <div class="nftmax-sidebar__button">
                                            <a href="#"
                                                class="nftmax-sidebar__button-btn nftmax-request_request"><svg
                                                    width="13" height="12" viewBox="0 0 13 12" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.1351 5.4852H11.1378V4.4879C11.1378 4.2125 10.9145 3.98926 10.6391 3.98926C10.3637 3.98926 10.1405 4.2125 10.1405 4.4879V5.4852H9.14317C8.86778 5.4852 8.64453 5.70845 8.64453 5.98384C8.64453 6.25924 8.86778 6.48248 9.14317 6.48248H10.1405V7.47979C10.1405 7.75518 10.3637 7.97843 10.6391 7.97843C10.9145 7.97843 11.1378 7.75518 11.1378 7.47979V6.48248H12.1351C12.4105 6.48248 12.6337 6.25924 12.6337 5.98384C12.6337 5.70845 12.4105 5.4852 12.1351 5.4852Z"
                                                        fill="white"></path>
                                                    <path
                                                        d="M5.15595 5.98378C6.80833 5.98378 8.14784 4.64426 8.14784 2.99189C8.14784 1.33951 6.80833 0 5.15595 0C3.50358 0 2.16406 1.33951 2.16406 2.99189C2.16406 4.64426 3.50358 5.98378 5.15595 5.98378Z"
                                                        fill="white"></path>
                                                    <path
                                                        d="M5.1558 6.98096C2.67838 6.98372 0.670727 8.99137 0.667969 11.4688C0.667969 11.7442 0.891215 11.9674 1.16661 11.9674H9.14497C9.42037 11.9674 9.64361 11.7442 9.64361 11.4688C9.64088 8.99137 7.63323 6.98369 5.1558 6.98096Z"
                                                        fill="white"></path>
                                                </svg></a>
                                            <a href="#"
                                                class="nftmax-sidebar__button-btn nftmax-request_close"><svg
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"></path>
                                                </svg></a>
                                        </div>
                                    </li>
                                    {{-- @endforeach --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- End Sidebar Creator Lists -->
                </div>
                <!-- End NFTMax Single Sidebar -->
            </div>

            <div class="col-xxl-12 col-xl-6 col-12 nftmax-sidebar__widget">
                <!-- NFTMax Single Sidebar -->
                <div class="nftmax-sidebar__single">
                    <!-- Sidebar Heading -->
                    <div class="nftmax-sidebar__heading">
                        <h4 class="nftmax-sidebar__title">Top Flatform</h4>
                        <a href="#" class="nftmax-sidebar_btn">View All</a>
                    </div>
                    <!-- Platform List -->
                    <div class="nftmax-sidebar__platform">
                        <ul class="nftmax-sidebar__list">
                            {{-- @foreach ($TopPlatform as $TopPlatform) --}}
                            <li>
                                <a href="#"><img src="  $TopPlatform['0']  " alt="#"> $TopPlatform['1']
                                </a>
                            </li>
                            {{-- @endforeach --}}
                        </ul>
                    </div>
                </div>
                <!-- End NFTMax Single Sidebar -->
            </div>
        </div>
    </div>

    <script></script>
