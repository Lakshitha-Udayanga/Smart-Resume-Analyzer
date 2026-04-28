    <!--start header wrapper-->
    <div class="header-wrapper">
        <header>
            <div class="topbar d-flex align-items-center">
                <nav class="navbar navbar-expand gap-3">
                    <div class="topbar-logo-header d-none d-lg-flex">
                        <div class="">
                            <img src="{{ asset('assets/images/logo.jpg') }}" class="logo-icon" alt="logo icon">
                        </div>
                        <div class="">
                            <h4 class="logo-text">CV Analyzer</h4>
                        </div>
                    </div>
                    <div class="mobile-toggle-menu d-block d-lg-none" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasNavbar"><i class='bx bx-menu'></i></div>
                    <div class="top-menu ms-auto">
                        <ul class="navbar-nav align-items-center gap-1">
                            <li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal"
                                data-bs-target="#SearchModal">
                                <a class="nav-link" href="avascript:;"><i class='bx bx-search'></i>
                                </a>
                            </li>
                            <li class="nav-item dropdown dropdown-laungauge d-none d-sm-flex">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="avascript:;"
                                    data-bs-toggle="dropdown"><img src="" width="22"
                                        alt="">
                                </a>
                            </li>
                            <li class="nav-item dark-mode d-none d-sm-flex">
                                <a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
                                </a>
                            </li>

                            <li class="nav-item dropdown dropdown-app" hidden>
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown"
                                    href="javascript:;"><i class='bx bx-grid-alt'></i></a>
                                <div class="dropdown-menu dropdown-menu-end p-0">
                                    <div class="app-container p-2 my-2">
                                    </div>
                                </div>
                            </li>

                            <li class="nav-item dropdown dropdown-large">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative"
                                    href="#" data-bs-toggle="dropdown">
                                    @if($unreadCount > 0)
                                        <span class="alert-count">{{ $unreadCount }}</span>
                                    @endif
                                    <i class='bx bx-bell'></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:;">
                                        <div class="msg-header">
                                            <p class="msg-header-title">Notifications</p>
                                            <p class="msg-header-badge">{{ $unreadCount }} New</p>
                                        </div>
                                    </a>
                                    <div class="header-notifications-list">
                                        @forelse($unreadNotifications as $notification)
                                            <a class="dropdown-item notification-item" href="javascript:;" 
                                               onclick="markAsRead('{{ $notification->id }}', this)">
                                                <div class="d-flex align-items-center">
                                                    <div class="notify bg-light-primary text-primary">
                                                        <i class='bx {{ $notification->type == "client" ? "bx-user" : "bx-briefcase" }}'></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="msg-name">{{ $notification->title }}<span
                                                                class="msg-time float-end">{{ $notification->created_at->diffForHumans() }}</span></h6>
                                                        <p class="msg-info">{{ $notification->message }}</p>
                                                    </div>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="text-center p-3">
                                                <p class="mb-0">No new notifications</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    <a href="javascript:;">
                                        <div class="text-center msg-footer">
                                            <button class="btn btn-primary w-100">View All Notifications</button>
                                        </div>
                                    </a>
                                </div>
                            </li>

                            <script>
                                function markAsRead(id, element) {
                                    fetch(`/notifications/mark-as-read/${id}`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success') {
                                            // Remove the item from list
                                            element.remove();
                                            
                                            // Update counts
                                            let countElements = document.querySelectorAll('.alert-count, .msg-header-badge');
                                            countElements.forEach(el => {
                                                let currentCount = parseInt(el.innerText);
                                                if (!isNaN(currentCount) && currentCount > 0) {
                                                    let newCount = currentCount - 1;
                                                    el.innerText = newCount > 0 ? (el.classList.contains('msg-header-badge') ? newCount + ' New' : newCount) : '';
                                                    if (newCount === 0 && el.classList.contains('alert-count')) {
                                                        el.style.display = 'none';
                                                    }
                                                }
                                            });
                                        }
                                    })
                                    .catch(error => console.error('Error:', error));
                                }
                            </script>
                            <li class="nav-item dropdown dropdown-large" hidden>
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative"
                                    href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="alert-count">8</span>
                                    <i class='bx bx-shopping-bag'></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:;">
                                        <div class="msg-header">
                                            <p class="msg-header-title">My Cart</p>
                                            <p class="msg-header-badge">10 Items</p>
                                        </div>
                                    </a>
                                    <div class="header-message-list">
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="user-box dropdown px-3">
                        <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('assets/images/avatar.png') }}" class="user-img" alt="user avatar">
                            <div class="user-info">
                                <p class="user-name mb-0">{{ auth()->user()->name }}</p>
                                <p class="designattion mb-0">Developer</p>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item d-flex align-items-center" href="{{ route('client.show', auth()->id()) }}"><i
                                        class="bx bx-user fs-5"></i><span>Profile</span></a>
                            </li>
                            <li>
                                <div class="dropdown-divider mb-0"></div>
                            </li>
                            <a class="dropdown-item d-flex align-items-center" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bx bx-log-out-circle"></i>
                                <span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
    </div>
    <!-- Page wrapper end -->
