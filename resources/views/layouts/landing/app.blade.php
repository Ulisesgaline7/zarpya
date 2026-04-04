
<!DOCTYPE html>
<?php
    $landing_site_direction = session()->get('landing_site_direction');
    $country= \App\CentralLogics\Helpers::get_business_settings('country')  ;
    $countryCode= strtolower($country??'auto');
   $metaData=  \App\Models\DataSetting::where('type','admin_landing_page')->whereIn('key',['meta_title','meta_description','meta_image'])->get()->keyBy('key')??[];
?>
<html dir="{{ $landing_site_direction }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>
    @include('layouts.landing._seo')

    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/customize-animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/odometer.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/owl.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/main.css') }}"/>

    <!-- External Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Funnel+Display:wght@300..800&display=swap" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=general-sans@200,300,400,500,600,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('public/assets/admin/intltelinput/css/intlTelInput.css')}}">


    <link rel="icon" type="image/x-icon" href="{{\App\CentralLogics\Helpers::iconFullUrl()}}">
    <script src="{{ asset('public/assets/landing/js/jquery-3.6.0.min.js') }}"></script>
    @stack('css_or_js')
     @php($backgroundChange = \App\CentralLogics\Helpers::get_business_settings('backgroundChange')??[])
    <style>
        :root {
            --cyan-tech: #2FB9CB;
            --black-pure: #000100;
            --deep-blue: #15263E;
            --digital-purple: #5859A3;
            --green-success: #4AB05E;
            --white: #FFFFFF;
            --font-main: 'General Sans', sans-serif;
            --font-display: 'Funnel Display', sans-serif;
            
            --base-1: var(--cyan-tech);
            --base-2: var(--deep-blue);
        }

        body {
            font-family: var(--font-main);
            background-color: var(--white);
            color: var(--deep-blue);
        }

        h1, h2, h3, h4, h5, h6, .display-font {
            font-family: var(--font-display);
            font-weight: 700;
        }

        .navbar-bottom {
            background: var(--black-pure) !important;
            padding: 15px 0;
        }

        .navbar-bottom .menu li a {
            color: var(--white) !important;
            font-weight: 500;
            font-family: var(--font-main);
        }

        .navbar-bottom .menu li a.active, .navbar-bottom .menu li a:hover {
            color: var(--cyan-tech) !important;
        }

        .header--btn {
            background: var(--cyan-tech) !important;
            color: var(--white) !important;
            border-radius: 100px !important;
            border: none !important;
        }

        footer {
            background: var(--black-pure) !important;
            color: var(--white) !important;
        }

        .cmn--btn {
            background: var(--cyan-tech) !important;
            border-radius: 100px !important;
            font-family: var(--font-main);
            font-weight: 600;
            text-transform: none !important;
            color: var(--white) !important;
        }

        .cmn--btn:hover {
            background: var(--deep-blue) !important;
            color: var(--white) !important;
        }
    </style>
</head>

<body>

    @php($fixed_link = \App\Models\DataSetting::where(['key'=>'fixed_link','type'=>'admin_landing_page'])->first())
    @php($fixed_link = isset($fixed_link->value)?json_decode($fixed_link->value, true):null)
    @php($view_keys = \App\CentralLogics\Helpers::get_view_keys())
    @php($toggle_dm_registration = $view_keys['toggle_dm_registration'] ?? 0)
    @php($toggle_store_registration = $view_keys['toggle_store_registration'] ?? 0)
    <!-- ==== Preloader ==== -->
    <div id="landing-loader"></div>
    <!-- ==== Preloader ==== -->
    <!-- ==== Header Section Starts Here ==== -->
    <header>
        <div class="navbar-bottom shadow-sm">
            <div class="container">
                <div class="navbar-bottom-wrapper d-flex align-items-center justify-content-between">

                    <a href="{{route('home')}}" class="logo">
                        <img class="onerror-image" data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                             src="{{ \App\CentralLogics\Helpers::logoFullUrl()}}"
                             alt="image" style="max-height: 40px;">
                    </a>
                    
                    <ul class="menu d-none d-lg-flex align-items-center mb-0 list-unstyled gap-4">
                        <li>
                            <a id="home-link" href="{{route('home')}}" class="{{ Request::is('/') ? 'active' : '' }}"><span>Inicio</span></a>
                        </li>
                        <li>
                            <a href="{{route('about-us')}}" class="{{ Request::is('about-us') ? 'active' : '' }}"><span>Sobre Nosotros</span></a>
                        </li>
                        <li>
                            <a href="{{route('privacy-policy')}}" class="{{ Request::is('privacy-policy') ? 'active' : '' }}"><span>Privacidad</span></a>
                        </li>
                        <li>
                            <a href="{{route('contact-us')}}" class="{{ Request::is('contact-us') ? 'active' : '' }}"><span>Contacto</span></a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-3">
                        @php( $local = session()->has('landing_local')?session('landing_local'):null)
                        @php($lang = \App\CentralLogics\Helpers::get_business_settings('system_language') )
                        @if ($lang)
                            <div class="dropdown--btn-hover position-relative">
                                <a class="dropdown--btn border-0 px-3 py-2 text-white text-capitalize d-flex align-items-center gap-1" href="javascript:void(0)" style="background: rgba(255,255,255,0.1); border-radius: 100px;">
                                    <span>ES</span>
                                    <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                                        <path d="M7.24701 11.14L2.45101 5.658C1.88501 5.013 2.34501 4 3.20401 4H12.796C12.9883 3.99984 13.1765 4.05509 13.3381 4.15914C13.4998 4.26319 13.628 4.41164 13.7075 4.58669C13.7869 4.76175 13.8142 4.956 13.7861 5.14618C13.758 5.33636 13.6757 5.51441 13.549 5.659L8.75301 11.139C8.65915 11.2464 8.5434 11.3325 8.41352 11.3915C8.28364 11.4505 8.14265 11.481 8.00001 11.481C7.85737 11.481 7.71638 11.4505 7.5865 11.3915C7.45663 11.3325 7.34087 11.2464 7.24701 11.139V11.14Z"/>
                                    </svg>
                                </a>
                                <ul class="dropdown-list py-2 shadow-lg" style="min-width:100px; top:100%; background: white; border-radius: 15px;">
                                    @foreach($lang as $key =>$data)
                                        @if($data['status']==1)
                                            <li>
                                                <a class="px-3 py-2 d-block text-dark" href="{{route('lang',[$data['code']])}}">{{$data['code']}}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (isset($toggle_dm_registration) || isset($toggle_store_registration))
                            <div class="dropdown--btn-hover position-relative">
                                <a class="cmn--btn px-4 py-2 text-white text-capitalize d-flex align-items-center gap-2" href="javascript:void(0)">
                                    <span>Únete a Zarpya</span>
                                    <svg width="10" height="6" viewBox="0 0 12 7" fill="currentColor">
                                        <path d="M6.00224 5.46105L1.33333 0.415128C1.21002 0.290383 1 0.0787335 1 0.0787335C1 0.0787335 0.708488 -0.0458817 0.584976 0.0788632L0.191805 0.475841C0.0680976 0.600389 7.43292e-08 0.766881 7.22135e-08 0.9443C7.00978e-08 1.12172 0.0680976 1.28801 0.191805 1.41266L5.53678 6.80682C5.66068 6.93196 5.82624 7.00049 6.00224 7C6.17902 7.00049 6.34439 6.93206 6.46839 6.80682L11.8082 1.41768C11.9319 1.29303 12 1.12674 12 0.949223C12 0.771804 11.9319 0.605509 11.8082 0.480765L11.415 0.0838844C11.1591 -0.174368 10.9225 0.222512 10.6667 0.480765L6.00224 5.46105Z"/>
                                    </svg>
                                </a>

                                <ul class="dropdown-list py-2 shadow-lg" style="min-width:200px; background: white; border-radius: 15px;">
                                    @if ($toggle_store_registration)
                                        <li>
                                            <a class="px-3 py-2 d-block text-dark" href="{{ route('restaurant.create') }}">
                                                Registro de Puerto (Negocio)
                                            </a>
                                        </li>
                                    @endif
                                    @if ($toggle_dm_registration)
                                        <li>
                                            <a class="px-3 py-2 d-block text-dark" href="{{ route('deliveryman.create') }}">
                                                Registro de Zarpero (Repartidor)
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        <div class="nav-toggle d-lg-none text-white">
                            <i class="fas fa-bars fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- ==== Header Section Ends Here ==== -->
    @yield('content')
    <!-- ======= Footer Section ======= -->
    <footer class="pt-5 pb-3">
        @php($fixed_footer_article_title = \App\Models\DataSetting::where(['type' => 'admin_landing_page','key' => 'fixed_footer_article_title'])->first())
        @php($fixed_footer_article_title = isset($fixed_footer_article_title->value) ? $fixed_footer_article_title->value: null)
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-4">
                    <a href="{{route('home')}}" class="footer-logo mb-4 d-block">
                        <img class="onerror-image" data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                             src="{{ \App\CentralLogics\Helpers::logoFullUrl()}}"
                             alt="image" style="max-height: 50px;">
                    </a>
                    <p class="text-white-50 mb-4 pe-lg-5">
                        {{ $fixed_footer_article_title }}
                    </p>
                    <div class="d-flex gap-3">
                        @php($social_media = \App\Models\SocialMedia::where('status', 1)->get())
                        @foreach ($social_media as $social)
                            <a href="{{ $social->link }}" target="_blank" class="text-white opacity-75 hover-opacity-100">
                                <img src="{{ asset('public/assets/landing/img/footer/'. $social->name.'.svg') }}" width="24" alt="">
                            </a>
                        @endforeach
                    </div>
                </div>
                
                <div class="col-6 col-lg-2">
                    <h5 class="text-white mb-4 display-font">Compañía</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="{{route('about-us')}}" class="text-white-50 text-decoration-none">Sobre Nosotros</a></li>
                        <li><a href="{{route('contact-us')}}" class="text-white-50 text-decoration-none">Contáctanos</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h5 class="text-white mb-4 display-font">Ayuda y Soporte</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="{{route('privacy-policy')}}" class="text-white-50 text-decoration-none">Política de Privacidad</a></li>
                        <li><a href="{{route('terms-and-conditions')}}" class="text-white-50 text-decoration-none">Términos y Condiciones</a></li>
                        <li><a href="{{route('refund')}}" class="text-white-50 text-decoration-none">Política de Reembolso</a></li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h5 class="text-white mb-4 display-font">Descarga la App</h5>
                    <div class="d-flex flex-column gap-3">
                        @php($landing_page_links = \App\Models\DataSetting::where(['type' => 'admin_landing_page','key' => 'download_user_app_links'])->first())
                        @php($landing_page_links = isset($landing_page_links->value) ? json_decode($landing_page_links->value, true) : null)
                        @if (isset($landing_page_links['playstore_url_status']))
                            <a href="{{ $landing_page_links['playstore_url'] ?? '#' }}" class="d-inline-block">
                                <img src="{{ asset('public/assets/landing/img/google.svg') }}" height="45" alt="">
                            </a>
                        @endif
                        @if (isset($landing_page_links['apple_store_url_status']))
                            <a href="{{ $landing_page_links['apple_store_url'] ?? '#' }}" class="d-inline-block">
                                <img src="{{ asset('public/assets/landing/img/apple.svg') }}" height="45" alt="">
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="my-5 border-secondary opacity-25">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <p class="text-white-50 small mb-0">
                    &copy; {{ \App\CentralLogics\Helpers::get_settings('footer_text') }}
                    by {{ \App\CentralLogics\Helpers::get_settings('business_name') }}
                </p>
                <div class="d-flex gap-4">
                    <a href="{{route('privacy-policy')}}" class="text-white-50 small text-decoration-none">{{ translate('Privacy') }}</a>
                    <a href="{{route('terms-and-conditions')}}" class="text-white-50 small text-decoration-none">{{ translate('Terms') }}</a>
                </div>
            </div>
        </div>
    </footer>
    <script src="{{ asset('public/assets/landing/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/assets/landing/js/viewport.jquery.js') }}"></script>
    <script src="{{ asset('public/assets/landing/js/wow.min.js') }}"></script>
    <script src="{{ asset('public/assets/landing/js/odometer.min.js') }}"></script>
    <script src="{{ asset('public/assets/landing/js/owl.min.js') }}"></script>
    <script src="{{ asset('public/assets/landing/js/main.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/toastr.js') }}"></script>
    {!! Toastr::message() !!}
    @if ($errors->any())
        <script>
            @foreach($errors->all() as $error)
            toastr.error('{{$error}}', Error, {
                CloseButton: true,
                ProgressBar: true
            });
            @endforeach
        </script>
    @endif


    @stack('script_2')

    <script>
        "use strict";
 $(".main-category-slider").owlCarousel({
            loop: true,
            nav: false,
            dots: true,
            items: 1,
            margin: 12,
            autoplay: true,
            rtl: {{ $landing_site_direction === 'rtl'?'true':'false' }},
        });
        $(".testimonial-slider").owlCarousel({
            loop: false,
            margin: 15,
            responsiveClass: true,
            nav: false,
            dots: false,
            autoplay: true,
            autoplayTimeout: 2000,
            autoplayHoverPause: true,
            items: 1,
            rtl: {{ $landing_site_direction === 'rtl'?'true':'false' }},
            responsive: {
                768: {
                    items: 2,
                    margin: 20,
                },
                992: {
                    items: 3,
                    margin: 20,
                },
                1200: {
                    items: 3,
                    margin: 22,
                },
            },
        });
        $(".owl-prev").html('<i class="fas fa-angle-left">');
        $(".owl-next").html('<i class="fas fa-angle-right">');
        let sync1 = $("#sync1");
         let sync2 = $("#sync2");
         let thumbnailItemClass = ".owl-item";
         let slides = sync1
            .owlCarousel({
                // startPosition: 12,
                items: 1,
                loop: false,
                margin: 30,
                mouseDrag: true,
                touchDrag: true,
                pullDrag: false,
                scrollPerPage: true,
                autoplayHoverPause: false,
                nav: false,
                dots: false,
                // center: true,
                rtl: {{ $landing_site_direction === 'rtl'?'true':'false' }},
            })
            .on("changed.owl.carousel", syncPosition);

        function syncPosition(el) {
            let  $owl_slider = $(this).data("owl.carousel");
            let loop = $owl_slider.options.loop;
            let current;
            if (loop) {
                let count = el.item.count - 1;
                 current = Math.round(
                    el.item.index - el.item.count / 2 - 0.5
                );
                if (current < 0) {
                    current = count;
                }
                if (current > count) {
                    current = 0;
                }
            } else {
                 current = el.item.index;
            }

            let owl_thumbnail = sync2.data("owl.carousel");
            let itemClass = "." + owl_thumbnail.options.itemClass;

            let thumbnailCurrentItem = sync2
                .find(itemClass)
                .removeClass("synced")
                .eq(current);
            thumbnailCurrentItem.addClass("synced");

            if (!thumbnailCurrentItem.hasClass("active")) {
                let duration = 500;
                sync2.trigger("to.owl.carousel", [current, duration, true]);
            }
        }

        let thumbs = sync2
            .owlCarousel({
                // startPosition: 12,
                items: 2,
                loop: false,
                margin: 0,
                autoplay: false,
                nav: true,
                navText: ["",""],
                dots: false,
                mouseDrag: true,
                touchDrag: true,
                rtl: {{ $landing_site_direction === 'rtl'?'true':'false' }},
                responsive: {
                    400: {
                        items: 3,
                    },
                    768: {
                        items: 6,
                    },
                    1200: {
                        items: 6,
                    },
                },
                onInitialized: function (e) {
                    let thumbnailCurrentItem = $(e.target)
                        .find(thumbnailItemClass)
                        .eq(this._current);
                    thumbnailCurrentItem.addClass("synced");
                },
            })
            .on("click", thumbnailItemClass, function (e) {
                e.preventDefault();
                let duration = 500;
                let itemIndex = $(e.target).parents(thumbnailItemClass).index();
                sync1.trigger("to.owl.carousel", [itemIndex, duration, true]);
            })
            .on("changed.owl.carousel", function (el) {
                let number = el.item.index;
                let  $owl_slider = sync1.data("owl.carousel");
                $owl_slider.to(number, 500, true);
            });
        sync1.owlCarousel();

    </script>
        <script src="{{asset('public/assets/admin/intltelinput/js/intlTelInput.min.js')}}"></script>

<script>
            "use strict";
            const inputs = document.querySelectorAll('input[type="tel"]');
            inputs.forEach(input => {
                window.intlTelInput(input, {
                    initialCountry: "{{$countryCode}}",
                    utilsScript: "{{ asset('public/assets/admin/intltelinput/js/utils.js') }}",
                    autoInsertDialCode: true,
                    nationalMode: false,
                    formatOnDisplay: false,
                });
            });


            function keepNumbersAndPlus(inputString) {
                let regex = /[0-9+]/g;
                let filteredString = inputString.match(regex);
            return filteredString ? filteredString.join('') : '';
            }

            $(document).on('keyup', 'input[type="tel"]', function () {
                $(this).val(keepNumbersAndPlus($(this).val()));
                });


</script>

</body>

</html>
