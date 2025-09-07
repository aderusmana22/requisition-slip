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

                    <li class="header-apps">
                        <a aria-controls="appscanvasRights" class="d-block head-icon" data-bs-target="#appscanvasRights"
                            data-bs-toggle="offcanvas" href="#" role="button">
                            <i class="iconoir-key-command"></i>
                        </a>

                        <div aria-labelledby="appscanvasRightsLabel" class="offcanvas offcanvas-end header-apps-canvas"
                            id="appscanvasRights" tabindex="-1">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="appscanvasRightsLabel">Shortcut</h5>
                                <div class="app-dropdown flex-shrink-0">
                                    <a aria-expanded="false" class=" p-1" data-bs-auto-close="outside"
                                        data-bs-toggle="dropdown" href="#" role="button">
                                        <i class="ph-bold  ph-faders-horizontal f-s-20"></i>
                                    </a>
                                    <ul class="dropdown-menu mb-3 p-2">
                                        <li class="dropdown-item">
                                            <a href="setting.html" target="_blank">
                                                Privacy Settings
                                            </a>
                                        </li>
                                        <li class="dropdown-item">
                                            <a href="setting.html" target="_blank">
                                                Account Settings
                                            </a>
                                        </li>
                                        <li class="dropdown-item">
                                            <a href="setting.html" target="_blank">
                                                Accessibility
                                            </a>
                                        </li>
                                        <li class="dropdown-divider"></li>
                                        <li class="dropdown-item border-0">
                                            <a aria-expanded="false" data-bs-toggle="dropdown" href="#"
                                                role="button">
                                                More Settings
                                            </a>
                                            <ul class="dropdown-menu sub-menu">
                                                <li class="dropdown-item">
                                                    <a href="setting.html" target="_blank">
                                                        Backup and Restore
                                                    </a>
                                                </li>
                                                <li class="dropdown-item">
                                                    <a href="setting.html" target="_blank">
                                                        <span>Data Usage</span>
                                                    </a>
                                                </li>
                                                <li class="dropdown-item">
                                                    <a href="setting.html" target="_blank">
                                                        <span>Theme</span>
                                                    </a>
                                                </li>
                                                <li
                                                    class="dropdown-item d-flex align-items-center justify-content-between">
                                                    <a href="setting.html" target="_blank">
                                                        <p class="mb-0">Notification</p>
                                                    </a>
                                                    <div class="flex-shrink-0">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input  form-check-primary"
                                                                id="notificationSwitch" type="checkbox">
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                            <div class="offcanvas-body app-scroll">
                                <div class="row row-cols-3">
                                    <div class="d-flex-center text-center mb-3">
                                        <a href="product.html" target="_blank">
                                            <span class="text-light-info h-45 w-45 d-flex-center b-r-15">
                                                <i class="ph-duotone  ph-shopping-bag-open f-s-30"></i>
                                            </span>
                                            <p class="mb-0 f-w-500 text-info">E-shop</p>
                                        </a>
                                    </div>
                                    <div class="d-flex-center text-center mb-3">
                                        <a href="email.html" target="_blank">
                                            <span
                                                class="text-light-primary h-45 w-45 d-flex-center b-r-15 position-relative">
                                                <i class="ph-duotone  ph-envelope f-s-30"></i>
                                                <span
                                                    class="position-absolute top-space-5 start-100 translate-middle p-1 bg-primary-dark rounded-circle animate__animated animate__fadeIn animate__infinite animate__fast"></span>
                                            </span>
                                            <p class="mb-0 f-w-500 text-primary">Email</p>
                                        </a>
                                    </div>
                                    <div class="d-flex-center text-center mb-3">
                                        <a href="chat.html" target="_blank">
                                            <span
                                                class="text-light-danger h-45 w-45 d-flex-center b-r-15 position-relative">
                                                <i class="ph-duotone  ph-chat-circle-text f-s-30"></i>
                                                <span
                                                    class="position-absolute top-space-5 start-100 translate-middle badge rounded-pill bg-success badge-notification">
                                                    99+
                                                    <span class="visually-hidden">unread messages</span>
                                                </span>
                                            </span>
                                            <p class="mb-0 f-w-500 text-danger">Chat</p>
                                        </a>
                                    </div>
                                    <div class="d-flex-center text-center mb-3">
                                        <a href="project_app.html" target="_blank">
                                            <span class="text-light-warning h-45 w-45 d-flex-center b-r-15">
                                                <i class="ph-duotone ph-projector-screen-chart f-s-30"></i>
                                            </span>
                                            <p class="mb-0 f-w-500 text-warning">Project</p>
                                        </a>
                                    </div>
                                    <div class="d-flex-center text-center mb-3">
                                        <a href="invoice.html" target="_blank">
                                            <span class="text-light-secondary h-45 w-45 d-flex-center b-r-15">
                                                <i class="ph-duotone ph-scroll f-s-30"></i>
                                            </span>
                                            <p class="mb-0 f-w-500 text-secondary">Invoice</p>
                                        </a>
                                    </div>
                                    <div class="d-flex-center text-center mb-3">
                                        <a href="blog.html" target="_blank">
                                            <span class="text-light-primary h-45 w-45 d-flex-center b-r-15">
                                                <i class="ph-duotone ph-notebook f-s-30"></i>
                                            </span>
                                            <p class="mb-0 f-w-500 text-primary">Blog</p>
                                        </a>
                                    </div>
                                    <div class="d-flex-center text-center mb-3">
                                        <a href="profile.html" target="_blank">
                                            <span
                                                class="text-light-primary h-45 w-45 d-flex-center b-r-15 position-relative">
                                                <i class="ph-duotone ph-users-three f-s-30"></i>
                                                <span
                                                    class="position-absolute top-space-5 start-100 translate-middle badge rounded-pill bg-danger badge-notification">
                                                    <i class="ti ti-bell-ringing"></i>
                                                </span>
                                            </span>
                                            <p class="mb-0 f-w-500 text-primary">Profile</p>
                                        </a>
                                    </div>
                                    <div class="d-flex-center text-center mb-3">
                                        <a href="gallery.html" target="_blank">
                                            <span class="text-light-success h-45 w-45 d-flex-center b-r-15">
                                                <i class="ph-duotone ph-google-photos-logo f-s-30"></i>
                                            </span>
                                            <p class="mb-0 f-w-500 text-success">Gallery</p>
                                        </a>
                                    </div>
                                    <div class="d-flex-center text-center mb-3">
                                        <a href="kanban_board.html" target="_blank">
                                            <span class="text-light-info h-45 w-45 d-flex-center b-r-15">
                                                <i class="ph-duotone ph-selection-foreground text-info f-s-30"></i>
                                            </span>
                                            <p class="mb-0 f-w-500 text-secondary">Task </p>
                                        </a>
                                    </div>
                                    <div class="d-flex-center text-center mb-3">
                                        <a href="calendar.html" target="_blank">
                                            <span class="text-light-dark h-45 w-45 d-flex-center b-r-15">
                                                <i class="ph-duotone ph-calendar f-s-30"></i>
                                            </span>
                                            <p class="mb-0 f-w-500 text-dark">Calen..</p>
                                        </a>
                                    </div>
                                    <div class="d-flex-center text-center mb-3">
                                        <a href="filemanager.html" target="_blank">
                                            <span class="text-light-danger h-45 w-45 d-flex-center b-r-15">
                                                <i class="ph-duotone ph-folder-open f-s-30"></i>
                                            </span>
                                            <p class="mb-0 f-w-500 text-danger">File Ma..</p>
                                        </a>
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
                                            <span
                                                class="bg-secondary h-35 w-35 d-flex-center b-r-10 position-relative">
                                                <img alt="avtar" class="img-fluid b-r-10"
                                                    src="../assets/images/ai_avtar/6.jpg">
                                                <span
                                                    class="position-absolute bottom-30 end-0 p-1 bg-secondary border border-light rounded-circle notification-avtar"></span>
                                            </span>
                                        </div>
                                        <div class="message-content-box flex-grow-1 ps-2">

                                            <a class="f-s-15 text-secondary mb-0" href="read_email.html"
                                                target="_blank"><span class="f-w-500 text-secondary">Gene Hart</span>
                                                wants to
                                                edit <span class="f-w-500 text-secondary">Report.doc</span></a>
                                            <div>
                                                <a class="d-inline-block f-w-500 text-success me-1"
                                                    href="#">Approve</a>
                                                <a class="d-inline-block f-w-500 text-danger" href="#">Deny</a>
                                            </div>
                                            <span class="badge text-light-primary mt-2"> sep 23 </span>

                                        </div>
                                        <div class="align-self-start text-end">
                                            <i class="iconoir-xmark close-btn"></i>
                                        </div>
                                    </div>
                                    <div class="notification-message head-box">
                                        <div class="message-images">
                                            <span
                                                class="bg-light-dark h-35 w-35 d-flex-center b-r-10 position-relative">
                                                <i class="ph-duotone  ph-truck f-s-18"></i>
                                            </span>
                                        </div>
                                        <div class="message-content-box flex-grow-1 ps-2">
                                            <a class="f-s-15 text-secondary mb-0" href="read_email.html"
                                                target="_blank">Hey
                                                <span class="f-w-500 text-secondary">Emery McKenzie</span>,
                                                get ready: Your order from <span
                                                    class="f-w-500 text-secondary">@Shopper.com</span>
                                                is out for delivery today!</a>
                                            <span class="badge text-light-info mt-2"> sep 23 </span>

                                        </div>
                                        <div class="align-self-start text-end">
                                            <i class="iconoir-xmark close-btn"></i>
                                        </div>
                                    </div>
                                    <div class="notification-message head-box">
                                        <div class="message-images">
                                            <span
                                                class="bg-secondary h-35 w-35 d-flex-center b-r-10 position-relative">
                                                <img alt="" class="img-fluid b-r-10"
                                                    src="../assets/images/ai_avtar/2.jpg">
                                                <span
                                                    class="position-absolute  end-0 p-1 bg-secondary border border-light rounded-circle notification-avtar"></span>
                                            </span>
                                        </div>
                                        <div class="message-content-box flex-grow-1 ps-2">
                                            <a class="f-s-15 text-secondary mb-0" href="read_email.html"
                                                target="_blank"><span class="f-w-500 text-secondary">Simon
                                                    Young</span> shared
                                                a file called <span
                                                    class="f-w-500 text-secondary">Dropdown.pdf</span></a>
                                            <span class="badge text-light-success mt-2"> 30 min</span>

                                        </div>
                                        <div class="align-self-start text-end">
                                            <i class="iconoir-xmark close-btn"></i>
                                        </div>
                                    </div>
                                    <div class="notification-message head-box">
                                        <div class="message-images">
                                            <span
                                                class="bg-secondary h-35 w-35 d-flex-center b-r-10 position-relative">
                                                <img alt="" class="img-fluid b-r-10"
                                                    src="../assets/images/ai_avtar/5.jpg">
                                                <span
                                                    class="position-absolute end-0 p-1 bg-secondary border border-light rounded-circle notification-avtar"></span>
                                            </span>
                                        </div>
                                        <div class="message-content-box flex-grow-1 ps-2">
                                            <a class="f-s-15 text-secondary mb-0" href="read_email.html"
                                                target="_blank"><span class="f-w-500 text-secondary">Becky G.
                                                    Hayes</span> has
                                                added a comment to <span
                                                    class="f-w-500 text-secondary">Final_Report.pdf</span></a>
                                            <span class="badge text-light-warning mt-2"> 45 min</span>
                                        </div>
                                        <div class="align-self-start text-end">
                                            <i class="iconoir-xmark close-btn"></i>
                                        </div>
                                    </div>
                                    <div class="notification-message head-box">
                                        <div class="message-images">
                                            <span
                                                class="bg-secondary h-35 w-35 d-flex-center b-r-10 position-relative">
                                                <img alt="" class="img-fluid b-r-10"
                                                    src="../assets/images/ai_avtar/1.jpg">
                                                <span
                                                    class="position-absolute  end-0 p-1 bg-secondary border border-light rounded-circle notification-avtar"></span>
                                            </span>
                                        </div>
                                        <div class="message-content-box flex-grow-1 ps-2">
                                            <a class="f-s-15 text-secondary mb-0" href="read_email.html"
                                                target="_blank"><span class="f-w-600 text-secondary">Romaine
                                                    Nadeau</span>
                                                invited you to join a meeting
                                            </a>
                                            <div>
                                                <a class="d-inline-block f-w-500 text-success me-1"
                                                    href="#">Join</a>
                                                <a class="d-inline-block f-w-500 text-danger"
                                                    href="#">Decline</a>
                                            </div>

                                            <span class="badge text-light-secondary mt-2"> 1 hour ago </span>
                                        </div>
                                        <div class="align-self-start text-end">
                                            <i class="iconoir-xmark close-btn"></i>
                                        </div>
                                    </div>

                                    <div class="hidden-massage py-4 px-3">
                                        <img alt="" class="w-50 h-50 mb-3 mt-2"
                                            src="../assets/images/icons/bell.png">
                                        <div>
                                            <h6 class="mb-0">Notification Not Found</h6>
                                            <p class="text-secondary">When you have any notifications added
                                                here,will
                                                appear here.
                                            </p>
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
                            <img alt="avtar" class="b-r-50 h-35 w-35 bg-dark"
                                src="../assets/images/avtar/woman.jpg">
                        </a>

                        <div aria-labelledby="profilecanvasRight"
                            class="offcanvas offcanvas-end header-profile-canvas" id="profilecanvasRight"
                            tabindex="-1">
                            <div class="offcanvas-body app-scroll">
                                <ul class="">
                                    <li class="d-flex gap-3 mb-3">
                                        <div class="d-flex-center">
                                            <span class="h-45 w-45 d-flex-center b-r-10 position-relative">
                                                <img alt="" class="img-fluid b-r-10"
                                                    src="../assets/images/avtar/woman.jpg">
                                            </span>
                                        </div>
                                        <div class="text-center mt-2">
                                            <h6 class="mb-0"> Laura Monaldo <img alt="instagram-check-mark"
                                                    class="w-20 h-20" src="../assets/images/profile-app/01.png"></h6>
                                            <p class="f-s-12 mb-0 text-secondary">lauradesign@gmail.com</p>
                                        </div>
                                    </li>

                                    <li>
                                        <a class="f-w-500" href="profile.html" target="_blank">
                                            <i class="iconoir-user-love pe-1 f-s-20"></i> Profile
                                            Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="f-w-500" href="setting.html" target="_blank">
                                            <i class="iconoir-settings pe-1 f-s-20"></i> Settings
                                        </a>
                                    </li>
                                    <li class="app-divider-v dotted py-1"></li>
                                    <li>
                                        <div class="app-dropdown dropstart">
                                            <a aria-expanded="false" class="f-w-500" data-bs-toggle="dropdown"
                                                href="setting.html" role="button" target="_blank">
                                                <i class="iconoir-eye-closed pe-1 f-s-20"></i> Hide
                                                Settings
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item">Hide Comments</a></li>
                                                <li><a class="dropdown-item">Advanced comment filtering</a>
                                                </li>
                                                <li><a class="dropdown-item">Hide mssage request</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item">Separated link</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <a class="f-w-500" href="#">
                                                <i class="iconoir-bell-notification pe-1 f-s-20"></i>
                                                Notification
                                            </a>
                                            <div class="flex-shrink-0">
                                                <div class="form-check form-switch">
                                                    <input checked="" class="form-check-input form-check-primary"
                                                        id="basicSwitch" type="checkbox">
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <a class="f-w-500" href="#">
                                                    <i class="ph-duotone  ph-detective pe-1 f-s-20"></i>
                                                    Incognito
                                                </a>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input form-check-primary"
                                                        id="incognitoSwitch" type="checkbox">
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="app-divider-v dotted py-1"></li>
                                    <li>
                                        <a class="f-w-500" href="faq.html" target="_blank">
                                            <i class="iconoir-help-circle pe-1 f-s-20"></i> Help
                                        </a>
                                    </li>
                                    <li>
                                        <a class="f-w-500" href="pricing.html" target="_blank">
                                            <i class="iconoir-dollar pe-1 f-s-20"></i>
                                            Pricing
                                        </a>
                                    </li>
                                    <li>
                                        <a class="mb-0 text-secondary f-w-500" href="sign_up.html" target="_blank">
                                            <i class="iconoir-plus pe-1 f-s-20"></i> Add account
                                        </a>
                                    </li>
                                    <li>
                                        <a class="mb-0 btn btn-light-danger btn-sm justify-content-center "
                                            href="sign_in.html" role="button">
                                            <i class="ph-duotone  ph-sign-out pe-1 f-s-20"></i> Log Out
                                        </a>
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
