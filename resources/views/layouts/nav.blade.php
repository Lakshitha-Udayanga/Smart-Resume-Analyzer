<div class="primary-menu">
    <nav class="navbar navbar-expand-lg align-items-center">
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header border-bottom">
                <div class="d-flex align-items-center">
                    <div class="">
                        <img src="" class="logo-icon" alt="logo icon">
                    </div>
                    <div class="">
                        <h4 class="logo-text">CV Analyzer</h4>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav align-items-center flex-grow-1">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            data-bs-toggle="dropdown">
                            <div class="parent-icon"><i class='bx bx-home-alt'></i>
                            </div>
                            <div class="menu-title d-flex align-items-center"
                                onclick="window.location='{{ url('dashboard') }}'">
                                Dashboard
                            </div>

                            <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            data-bs-toggle="dropdown">
                            <div class="parent-icon"><i class='bx bx-user'></i>
                            </div>
                            <div class="menu-title d-flex align-items-center">Client</div>
                            <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('registered/client') }}"><i
                                        class='bx bx-user-voice'></i>
                                    Client List</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            data-bs-toggle="dropdown">
                            <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                            </div>
                            <div class="menu-title d-flex align-items-center">Jobs</div>
                            <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li> <a class="dropdown-item" href="{{ url('jobs') }}"><i
                                        class='bx bx-layer'></i>Jobs List</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown" hidden>
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            data-bs-toggle="dropdown">
                            <div class="parent-icon"><i class='bx bx-lock'></i>
                            </div>
                            <div class="menu-title d-flex align-items-center">Authentication</div>
                            <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                        </a>
                    </li>
                    <li class="nav-item dropdown" hidden>
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            data-bs-toggle="dropdown">
                            <div class="parent-icon"><i class='bx bx-briefcase-alt'></i>
                            </div>
                            <div class="menu-title d-flex align-items-center">UI Elements</div>
                            <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li> <a class="dropdown-item" href="{{ url('widgets') }}"><i
                                        class='bx bx-wine'></i>Widgets</a></li>
                            <li class="nav-item dropend">
                                <a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"><i
                                        class='bx bx-cart'></i>eCommerce</a>
                                <ul class="dropdown-menu submenu">
                                </ul>
                            </li>
                            <li class="nav-item dropend">
                                <a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"><i
                                        class='bx bx-ghost'></i>Components</a>
                                <ul class="dropdown-menu scroll-menu">

                                </ul>
                            </li>
                            <li class="nav-item dropend">
                                <a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"><i
                                        class='bx bx-card'></i>Content</a>
                                <ul class="dropdown-menu submenu">
                                </ul>
                            </li>
                            <li class="nav-item dropend">
                                <a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"><i
                                        class='bx bx-droplet'></i>Icons</a>
                                <ul class="dropdown-menu submenu">
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown" hidden>
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            data-bs-toggle="dropdown">
                            <div class="parent-icon"><i class='bx bx-line-chart'></i>
                            </div>
                            <div class="menu-title d-flex align-items-center">Charts</div>
                            <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                        </a>
                        <ul class="dropdown-menu">
                        </ul>
                    </li>
                    <li class="nav-item dropdown" hidden>
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            data-bs-toggle="dropdown">
                            <div class="parent-icon"><i class="bx bx-grid-alt"></i>
                            </div>
                            <div class="menu-title d-flex align-items-center">Management</div>
                            <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('table-basic-table') }}"><i
                                        class='bx bx-user-check'></i>
                                    </i>User Managemnet</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
