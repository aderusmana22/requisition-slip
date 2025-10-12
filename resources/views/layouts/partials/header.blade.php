<header class="header-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6 col-sm-4 d-flex align-items-center header-left p-0">
                <span class="header-toggle me-3">
                    <i class="iconoir-view-grid"></i>
                </span>
            </div>

            <div class="col-6 col-sm-8 d-flex align-items-center justify-content-end header-right p-0">

                <ul class="d-flex align-items-center">

                    <li class="header-cloud">
                        <a aria-controls="cloudoffcanvasTops" class="head-icon" data-bs-target="#cloudoffcanvasTops"
                            data-bs-toggle="offcanvas" href="#" role="button">
                            <i class="iconoir-dew-point text-primary f-s-26 me-1"></i>
                            <span id="current-temp" class="f-w-600">-- <sup class="f-s-10">°C</sup></span>
                        </a>

                        <div aria-labelledby="cloudoffcanvasTops" class="offcanvas offcanvas-end header-cloud-canvas"
                            id="cloudoffcanvasTops" tabindex="-1">
                            <div class="offcanvas-body p-0">
                                <div class="cloud-body">
                                    <div id="forecast-box" class="cloud-content-box">
                                        <!-- isi forecast akan di-generate JS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>



                    <li class="header-dark">
                        <div class="sun-logo head-icon">
                            <i class="iconoir-sun-light"></i>
                        </div>
                        <div class="moon-logo head-icon">
                            <i class="iconoir-half-moon"></i>
                        </div>
                    </li>

                    <li class="header-notification">
                        <a aria-controls="notificationcanvasRight" class="d-block head-icon position-relative"
                            data-bs-target="#notificationcanvasRight" data-bs-toggle="offcanvas" href="#"
                            role="button">
                            <i class="iconoir-bell"></i>
                            <span
                                class="position-absolute translate-middle p-1 bg-success border border-light rounded-circle animate__animated animate__fadeIn animate__infinite animate__slower"></span>
                        </a>
                        <div aria-labelledby="notificationcanvasRightLabel"
                            class="offcanvas offcanvas-end header-notification-canvas" id="notificationcanvasRight"
                            tabindex="-1">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="notificationcanvasRightLabel">
                                    Notification</h5>
                                <button aria-label="Close" class="btn-close" data-bs-dismiss="offcanvas"
                                    type="button"></button>
                            </div>
                            <div class="offcanvas-body notification-offcanvas-body app-scroll p-0">
                                <div class="head-container notification-head-container">
                                    <div class="notification-message head-box">
                                        <div class="message-images">
                                            <span class="bg-secondary h-35 w-35 d-flex-center b-r-10 position-relative">
                                                <i class="iconoir-document"></i>
                                            </span>
                                        </div>
                                        <div class="message-content-box flex-grow-1 ps-2">
                                            <a class="f-s-15 text-secondary mb-0" href="#" target="_blank">
                                                <span class="f-w-500 text-secondary">Your requisition slip #RS-1023</span> has been <span class="text-success">approved</span> by <span class="f-w-500 text-secondary">Manager John</span>.
                                            </a>
                                            <span class="badge text-light-success mt-2"> 5 min ago </span>
                                        </div>
                                        <div class="align-self-start text-end">
                                            <i class="iconoir-xmark close-btn"></i>
                                        </div>
                                    </div>
                                    <div class="notification-message head-box">
                                        <div class="message-images">
                                            <span class="bg-warning h-35 w-35 d-flex-center b-r-10 position-relative">
                                                <i class="iconoir-clock"></i>
                                            </span>
                                        </div>
                                        <div class="message-content-box flex-grow-1 ps-2">
                                            <a class="f-s-15 text-secondary mb-0" href="#" target="_blank">
                                                <span class="f-w-500 text-secondary">Requisition slip #RS-1024</span> is <span class="text-warning">waiting for your approval</span>.
                                            </a>
                                            <div>
                                                <a class="d-inline-block f-w-500 text-success me-1" href="#">Approve</a>
                                                <a class="d-inline-block f-w-500 text-danger" href="#">Reject</a>
                                            </div>
                                            <span class="badge text-light-warning mt-2"> 10 min ago </span>
                                        </div>
                                        <div class="align-self-start text-end">
                                            <i class="iconoir-xmark close-btn"></i>
                                        </div>
                                    </div>
                                    <div class="notification-message head-box">
                                        <div class="message-images">
                                            <span class="bg-danger h-35 w-35 d-flex-center b-r-10 position-relative">
                                                <i class="iconoir-cancel"></i>
                                            </span>
                                        </div>
                                        <div class="message-content-box flex-grow-1 ps-2">
                                            <a class="f-s-15 text-secondary mb-0" href="#" target="_blank">
                                                <span class="f-w-500 text-secondary">Requisition slip #RS-1022</span> has been <span class="text-danger">rejected</span> by <span class="f-w-500 text-secondary">Manager Lisa</span>.
                                            </a>
                                            <span class="badge text-light-danger mt-2"> 30 min ago </span>
                                        </div>
                                        <div class="align-self-start text-end">
                                            <i class="iconoir-xmark close-btn"></i>
                                        </div>
                                    </div>
                                    <div class="notification-message head-box">
                                        <div class="message-images">
                                            <span class="bg-info h-35 w-35 d-flex-center b-r-10 position-relative">
                                                <i class="iconoir-truck"></i>
                                            </span>
                                        </div>
                                        <div class="message-content-box flex-grow-1 ps-2">
                                            <a class="f-s-15 text-secondary mb-0" href="#" target="_blank">
                                                <span class="f-w-500 text-secondary">Tracking update:</span> Your requisition slip #RS-1023 is <span class="text-info">being processed</span>.
                                            </a>
                                            <span class="badge text-light-info mt-2"> 1 hour ago </span>
                                        </div>
                                        <div class="align-self-start text-end">
                                            <i class="iconoir-xmark close-btn"></i>
                                        </div>
                                    </div>
                                    <div class="notification-message head-box">
                                        <div class="message-images">
                                            <span class="bg-primary h-35 w-35 d-flex-center b-r-10 position-relative">
                                                <i class="iconoir-plus"></i>
                                            </span>
                                        </div>
                                        <div class="message-content-box flex-grow-1 ps-2">
                                            <a class="f-s-15 text-secondary mb-0" href="#" target="_blank">
                                                <span class="f-w-500 text-secondary">You have created a new requisition slip</span> #RS-1025.
                                            </a>
                                            <span class="badge text-light-primary mt-2"> just now </span>
                                        </div>
                                        <div class="align-self-start text-end">
                                            <i class="iconoir-xmark close-btn"></i>
                                        </div>
                                    </div>
                                    <div class="hidden-massage py-4 px-3">
                                        <img alt="" class="w-50 h-50 mb-3 mt-2"
                                            src="../assets/images/icons/bell.png">
                                        <div>
                                            <h6 class="mb-0">No Notifications</h6>
                                            <p class="text-secondary">When you have any requisition slip notifications, they will appear here.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="header-profile">
                        <a aria-controls="profilecanvasRight" class="d-block head-icon"
                            data-bs-target="#profilecanvasRight" data-bs-toggle="offcanvas" href="#"
                            role="button">
                            @if(Auth::user()->avatar)
                                <img alt="avtar" class="b-r-50 h-35 w-35 bg-dark" src="{{ asset(Auth::user()->avatar) }}">
                            @else
                                <img alt="avtar" class="b-r-50 h-35 w-35 bg-dark" src="{{ asset('assets/images/logo/sinarmeadow.png') }}">
                            @endif
                        </a>

                        <div aria-labelledby="profilecanvasRight"
                            class="offcanvas offcanvas-end header-profile-canvas" id="profilecanvasRight"
                            tabindex="-1">
                            <div class="offcanvas-body app-scroll">
                                <ul class="">
                                    <li class="d-flex gap-3 mb-3">
                                        <div class="d-flex-center">
                                            <span class="h-45 w-45 d-flex-center b-r-10 position-relative">
                                                @if(Auth::user()->avatar)
                                                    <img alt="" class="img-fluid b-r-10" src="{{ asset(Auth::user()->avatar) }}">
                                                @else
                                                    <img alt="" class="img-fluid b-r-10" src="{{ asset('assets/images/logo/sinarmeadow.png') }}">
                                                @endif
                                            </span>
                                        </div>
                                        <div class="text-center mt-2">
                                            <h6 class="mb-0"> {{ Auth::user()->name }}
                                                </h6>
                                            <p class="f-s-12 mb-0 text-secondary">{{ Auth::user()->email  }}</p>
                                        </div>
                                    </li>

                                    <li>
                                        <a class="f-w-500" href="{{ route('profile.edit') }}" target="_blank">
                                            <i class="iconoir-user-love pe-1 f-s-20"></i> Profile
                                            Details
                                        </a>
                                    </li>
                                    <!-- Authentication -->
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item f-w-500 bg-transparent border-0 p-0">
                                                <i class="ph-duotone ph-sign-out pe-1 f-s-20"></i> {{ __('Log Out') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
