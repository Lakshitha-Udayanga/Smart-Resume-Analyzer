<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" type="image/png" />

    <!-- Plugins CSS -->
    @yield('style')

    @include('layouts.stylesheet')

    <title> CV Analyzer - Admin Dashboard </title>
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--start header -->
        @include('layouts.header')
        <!--end header -->
        <!--navigation-->
        @include('layouts.nav')
        <!--end navigation-->
        <!--start page wrapper -->
        @yield('wrapper')
        <!--end page wrapper -->
        <!-- Search Modal -->
        @include('layouts.search-modal')
        <!-- End Search Model -->
        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i
                class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        <footer class="page-footer">
            <p class="mb-0">CV@Analyzer {{ date('Y') }}.</p>
        </footer>
    </div>
    <!--end wrapper-->
    <!--start switcher-->
    <div class="switcher-wrapper">
        <div class="switcher-btn"> <i class='bx bx-cog bx-spin'></i>
        </div>
        <div class="switcher-body">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 text-uppercase">Theme Customizer</h5>
                <button type="button" class="btn-close ms-auto close-switcher" aria-label="Close"></button>
            </div>
            <hr />
            <h6 class="mb-0">Theme Styles</h6>
            <hr />
            <div class="d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="lightmode" checked>
                    <label class="form-check-label" for="lightmode">Light</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="darkmode">
                    <label class="form-check-label" for="darkmode">Dark</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="semidark">
                    <label class="form-check-label" for="semidark">Semi Dark</label>
                </div>
            </div>
            <hr />
            <div class="form-check">
                <input class="form-check-input" type="radio" id="minimaltheme" name="flexRadioDefault">
                <label class="form-check-label" for="minimaltheme">Minimal Theme</label>
            </div>
            <hr />
            <h6 class="mb-0">Header Colors</h6>
            <hr />
            <div class="header-colors-indigators">
                <div class="row row-cols-auto g-3">
                    <div class="col">
                        <div class="indigator headercolor1" id="headercolor1"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor2" id="headercolor2"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor3" id="headercolor3"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor4" id="headercolor4"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor5" id="headercolor5"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor6" id="headercolor6"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor7" id="headercolor7"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor8" id="headercolor8"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end switcher-->

    <!-- Bootstrap JS -->
    @include('layouts.javascript')

    <script>
        $(document).ready(function() {
            // Check for saved theme preference
            const currentTheme = localStorage.getItem('theme');
            if (currentTheme) {
                $('html').attr('class', currentTheme);
                if (currentTheme === 'dark-theme') {
                    $('#darkmode').prop('checked', true);
                } else if (currentTheme === 'semi-dark') {
                    $('#semidark').prop('checked', true);
                } else if (currentTheme === 'minimal-theme') {
                    $('#minimaltheme').prop('checked', true);
                } else {
                    $('#lightmode').prop('checked', true);
                }
            }

            // Dark mode icon toggle
            $('.dark-mode-icon').on('click', function() {
                if ($('html').hasClass('dark-theme')) {
                    $('html').attr('class', 'light-theme');
                    localStorage.setItem('theme', 'light-theme');
                    $('#lightmode').prop('checked', true);
                } else {
                    $('html').attr('class', 'dark-theme');
                    localStorage.setItem('theme', 'dark-theme');
                    $('#darkmode').prop('checked', true);
                }
            });

            // Theme customizer radio buttons
            $('#lightmode').on('click', function() {
                $('html').attr('class', 'light-theme');
                localStorage.setItem('theme', 'light-theme');
            });

            $('#darkmode').on('click', function() {
                $('html').attr('class', 'dark-theme');
                localStorage.setItem('theme', 'dark-theme');
            });

            $('#semidark').on('click', function() {
                $('html').attr('class', 'semi-dark');
                localStorage.setItem('theme', 'semi-dark');
            });

            $('#minimaltheme').on('click', function() {
                $('html').attr('class', 'minimal-theme');
                localStorage.setItem('theme', 'minimal-theme');
            });

            // Switcher toggle
            $('.switcher-btn').on('click', function() {
                $('.switcher-wrapper').toggleClass('switcher-toggled');
            });

            $('.close-switcher').on('click', function() {
                $('.switcher-wrapper').removeClass('switcher-toggled');
            });

            // Header colors
            $('#headercolor1').on('click', function() {
                $('html').addClass('color-header headercolor1');
                $('html').removeClass('headercolor2 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8');
            });
            $('#headercolor2').on('click', function() {
                $('html').addClass('color-header headercolor2');
                $('html').removeClass('headercolor1 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8');
            });
            $('#headercolor3').on('click', function() {
                $('html').addClass('color-header headercolor3');
                $('html').removeClass('headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8');
            });
            $('#headercolor4').on('click', function() {
                $('html').addClass('color-header headercolor4');
                $('html').removeClass('headercolor1 headercolor2 headercolor3 headercolor5 headercolor6 headercolor7 headercolor8');
            });
            $('#headercolor5').on('click', function() {
                $('html').addClass('color-header headercolor5');
                $('html').removeClass('headercolor1 headercolor2 headercolor3 headercolor4 headercolor6 headercolor7 headercolor8');
            });
            $('#headercolor6').on('click', function() {
                $('html').addClass('color-header headercolor6');
                $('html').removeClass('headercolor1 headercolor2 headercolor3 headercolor4 headercolor5 headercolor7 headercolor8');
            });
            $('#headercolor7').on('click', function() {
                $('html').addClass('color-header headercolor7');
                $('html').removeClass('headercolor1 headercolor2 headercolor3 headercolor4 headercolor5 headercolor6 headercolor8');
            });
            $('#headercolor8').on('click', function() {
                $('html').addClass('color-header headercolor8');
                $('html').removeClass('headercolor1 headercolor2 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7');
            });
        });
    </script>

    @yield('script')
</body>

</html>
