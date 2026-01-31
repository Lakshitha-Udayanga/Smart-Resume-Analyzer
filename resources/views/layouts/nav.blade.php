<div class="primary-menu">
    <nav class="navbar navbar-expand-lg align-items-center">
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header border-bottom">
                <div class="d-flex align-items-center">
                    <div class="">
                        <img src="assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
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
                            <div class="menu-title d-flex align-items-center" href="{{ url('index') }}">Dashboard</div>
                            <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            data-bs-toggle="dropdown">
                            <div class="parent-icon"><i class='bx bx-cube'></i>
                            </div>
                            <div class="menu-title d-flex align-items-center">Contacts</div>
                            <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('app-chat-box') }}"><i
                                        class='bx bx-conversation'></i>Chat Box</a></li>
                            <li><a class="dropdown-item" href="{{ url('app-file-manager') }}"><i
                                        class='bx bx-file'></i>File Manager</a></li>
                            <li><a class="dropdown-item" href="{{ url('app-contact-list') }}"><i
                                        class='bx bx-microphone'></i>Contacts List</a></li>
                            <li><a class="dropdown-item" href="{{ url('app-to-do') }}"><i
                                        class='bx bx-check-shield'></i>Todo</a></li>
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

                            <li> <a class="dropdown-item" href="{{ url('form-layouts') }}"><i
                                        class='bx bx-layer'></i>Jobs List</a>
                            </li>
                            <li> <a class="dropdown-item" href="{{ url('form-text-editor') }}"><i
                                        class='bx bx-edit'></i>Text Editor</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            data-bs-toggle="dropdown">
                            <div class="parent-icon"><i class='bx bx-lock'></i>
                            </div>
                            <div class="menu-title d-flex align-items-center">Authentication</div>
                            <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="nav-item dropend">
                                <a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret"
                                    href="javascript:;"><i class='bx bx-receipt'></i>Basic</a>
                                <ul class="dropdown-menu submenu">
                                    <li><a class="dropdown-item" href="{{ url('auth-basic-signin') }}"><i
                                                class='bx bx-radio-circle'></i>Sign In</a></li>
                                    <li><a class="dropdown-item" href="{{ url('auth-basic-signup') }}"><i
                                                class='bx bx-radio-circle'></i>Sign Up</a></li>
                                    <li><a class="dropdown-item" href="{{ url('auth-basic-forgot-password') }}"><i
                                                class='bx bx-radio-circle'></i>Forgot Password</a></li>
                                    <li><a class="dropdown-item" href="{{ url('auth-basic-reset-password') }}"><i
                                                class='bx bx-radio-circle'></i>Reset Password</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropend">
                                <a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret"
                                    href="javascript:;"><i class='bx bx-cylinder'></i>Cover</a>
                                <ul class="dropdown-menu submenu">
                                    <li><a class="dropdown-item" href="{{ url('auth-cover-signin') }}"><i
                                                class='bx bx-radio-circle'></i>Sign In</a></li>
                                    <li><a class="dropdown-item" href="{{ url('auth-cover-signup') }}"><i
                                                class='bx bx-radio-circle'></i>Sign Up</a></li>
                                    <li><a class="dropdown-item" href="{{ url('auth-cover-forgot-password') }}"><i
                                                class='bx bx-radio-circle'></i>Forgot Password</a></li>
                                    <li><a class="dropdown-item" href="{{ url('auth-cover-reset-password') }}"><i
                                                class='bx bx-radio-circle'></i>Reset Password</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropend">
                                <a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret"
                                    href="javascript:;"><i class='bx bx-aperture'></i>Header & Footer</a>
                                <ul class="dropdown-menu submenu">
                                    <li><a class="dropdown-item" href="{{ url('auth-header-footer-signin') }}"><i
                                                class='bx bx-radio-circle'></i>Sign In</a></li>
                                    <li><a class="dropdown-item" href="{{ url('auth-header-footer-signup') }}"><i
                                                class='bx bx-radio-circle'></i>Sign Up</a></li>
                                    <li><a class="dropdown-item"
                                            href="{{ url('auth-header-footer-forgot-password') }}"><i
                                                class='bx bx-radio-circle'></i>Forgot Password</a></li>
                                    <li><a class="dropdown-item"
                                            href="{{ url('auth-header-footer-reset-password') }}"><i
                                                class='bx bx-radio-circle'></i>Reset Password</a></li>
                                </ul>
                            </li>
                        </ul>
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
                                <a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret"
                                    href="javascript:;"><i class='bx bx-cart'></i>eCommerce</a>
                                <ul class="dropdown-menu submenu">

                                </ul>
                            </li>
                            <li class="nav-item dropend">
                                <a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret"
                                    href="javascript:;"><i class='bx bx-ghost'></i>Components</a>
                                <ul class="dropdown-menu scroll-menu">

                                </ul>
                            </li>
                            <li class="nav-item dropend">
                                <a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret"
                                    href="javascript:;"><i class='bx bx-card'></i>Content</a>
                                <ul class="dropdown-menu submenu">
                                </ul>
                            </li>
                            <li class="nav-item dropend">
                                <a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret"
                                    href="javascript:;"><i class='bx bx-droplet'></i>Icons</a>
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            data-bs-toggle="dropdown">
                            <div class="parent-icon"><i class="bx bx-grid-alt"></i>
                            </div>
                            <div class="menu-title d-flex align-items-center">Management</div>
                            <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('table-basic-table') }}"><i
                                        class='bx bx-table'></i>User Managemnet</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
