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

    <link rel="stylesheet" href="{{ asset('assets/landing/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/landing/css/customize-animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/landing/css/odometer.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/landing/css/owl.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/landing/css/main.css') }}"/>
    <link rel="stylesheet" href="{{asset('assets/admin/intltelinput/css/intlTelInput.css')}}">
    <link rel="icon" type="image/x-icon" href="{{\App\CentralLogics\Helpers::iconFullUrl()}}">

    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    @stack('css_or_js')
    @php($backgroundChange = \App\CentralLogics\Helpers::get_business_settings('backgroundChange')??[])
    @if (isset($backgroundChange['primary_1_hex']) && isset($backgroundChange['primary_2_hex']))
        <style>
            :root {
                --base-1: <?php echo $backgroundChange['primary_1_hex']; ?>;
                --base-rgb: <?php echo $backgroundChange['primary_1_rgb']; ?>;
                --base-2: <?php echo $backgroundChange['primary_2_hex']; ?>;
                --base-rgb-2:<?php echo $backgroundChange['primary_2_rgb']; ?>;
            }
        </style>
    @endif

    <style>
    /* ============================================================
       DESIGN TOKENS
    ============================================================ */
    :root {
        --c-bg:        #0A0A0F;
        --c-surface:   #12121A;
        --c-surface-2: #1A1A26;
        --c-border:    rgba(255,255,255,0.06);
        --c-orange:    #FF5722;
        --c-orange-2:  #FF8A50;
        --c-yellow:    #FFD166;
        --c-green:     #06D6A0;
        --c-text:      #F0EDE8;
        --c-muted:     #7A7A8C;
        --c-white:     #FFFFFF;
        --font-display: 'Syne', sans-serif;
        --font-body:    'Plus Jakarta Sans', sans-serif;
        --radius-lg:   20px;
        --radius-xl:   28px;
        --transition:  0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html { scroll-behavior: smooth; }

    body {
        background: var(--c-bg);
        color: var(--c-text);
        font-family: var(--font-body);
        font-size: 16px;
        line-height: 1.6;
        overflow-x: hidden;
    }

    /* ============================================================
       PRELOADER
    ============================================================ */
    #landing-loader {
        position: fixed;
        inset: 0;
        background: var(--c-bg);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }
    #landing-loader.hidden { opacity: 0; visibility: hidden; }
    #landing-loader::after {
        content: '';
        width: 44px;
        height: 44px;
        border: 3px solid rgba(255,87,34,0.2);
        border-top-color: var(--c-orange);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ============================================================
       HEADER / NAV
    ============================================================ */
    header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        padding: 0;
    }

    .navbar-bottom {
        background: rgba(10,10,15,0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid var(--c-border);
        transition: background var(--transition);
    }

    .navbar-bottom-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        height: 70px;
        padding: 0 4px;
    }

    .logo img {
        height: 36px;
        width: auto;
        object-fit: contain;
        filter: brightness(1.1);
    }

    .menu {
        display: flex;
        align-items: center;
        gap: 4px;
        list-style: none;
        margin: 0 auto;
    }

    .menu li a {
        display: block;
        padding: 8px 14px;
        font-size: 0.88rem;
        font-weight: 500;
        color: var(--c-muted);
        text-decoration: none;
        border-radius: 8px;
        transition: color var(--transition), background var(--transition);
    }

    .menu li a:hover,
    .menu li a.active {
        color: var(--c-text);
        background: rgba(255,255,255,0.06);
    }

    .menu li a.active { color: var(--c-orange); }

    .cmn--btn {
        background: var(--c-orange);
        color: white !important;
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 0.85rem;
        padding: 9px 20px;
        border-radius: 50px;
        text-decoration: none;
        transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
        white-space: nowrap;
    }

    .cmn--btn:hover {
        background: var(--c-orange-2);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(255,87,34,0.35);
    }

    .dropdown--btn-hover { position: relative; }

    .dropdown--btn {
        background: var(--c-surface-2);
        border: 1px solid var(--c-border) !important;
        color: var(--c-text) !important;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 9px 16px;
        border-radius: 50px;
        text-decoration: none;
        cursor: pointer;
        gap: 6px;
        white-space: nowrap;
    }

    .dropdown-list {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        list-style: none;
        padding: 8px;
        min-width: 180px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all var(--transition);
        z-index: 100;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }

    .dropdown--btn-hover:hover .dropdown-list {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-list li a {
        display: block;
        padding: 10px 14px;
        color: var(--c-text);
        text-decoration: none;
        font-size: 0.88rem;
        border-radius: 8px;
        transition: background var(--transition), color var(--transition);
    }

    .dropdown-list li a:hover {
        background: rgba(255,87,34,0.1);
        color: var(--c-orange);
    }

    .dropdown-divider { border-color: var(--c-border); margin: 4px 0; }

    /* Mobile nav toggle */
    .nav-toggle {
        width: 36px;
        height: 36px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        border-radius: 8px;
        background: var(--c-surface-2);
    }
    .nav-toggle span {
        display: block;
        width: 20px;
        height: 2px;
        background: var(--c-text);
        border-radius: 2px;
    }

    /* ============================================================
       HERO SECTION + CANVAS
    ============================================================ */
    .hero-section {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        overflow: hidden;
        padding-top: 70px;
    }

    #city-canvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            135deg,
            rgba(10,10,15,0.92) 0%,
            rgba(10,10,15,0.6) 50%,
            rgba(10,10,15,0.85) 100%
        );
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        padding: 80px 0;
    }

    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,87,34,0.12);
        border: 1px solid rgba(255,87,34,0.25);
        border-radius: 50px;
        padding: 7px 16px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--c-orange-2);
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 24px;
        animation: fadeUp 0.6s ease both;
    }

    .live-dot {
        width: 7px;
        height: 7px;
        background: var(--c-green);
        border-radius: 50%;
        box-shadow: 0 0 8px var(--c-green);
        animation: pulse-live 1.6s ease infinite;
    }

    @keyframes pulse-live {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.5); opacity: 0.6; }
    }

    .hero-title {
        font-family: var(--font-display);
        font-weight: 800;
        font-size: clamp(2.8rem, 6vw, 5.5rem);
        line-height: 1.0;
        letter-spacing: -0.03em;
        color: var(--c-white);
        margin-bottom: 24px;
        animation: fadeUp 0.6s 0.1s ease both;
    }

    .hero-title .accent { color: var(--c-orange); }
    .hero-title .accent-2 { color: var(--c-yellow); }

    .hero-sub {
        font-size: 1.1rem;
        color: var(--c-muted);
        max-width: 480px;
        line-height: 1.7;
        margin-bottom: 40px;
        animation: fadeUp 0.6s 0.2s ease both;
    }

    .hero-cta-group {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        animation: fadeUp 0.6s 0.3s ease both;
    }

    .btn-primary-hero {
        background: var(--c-orange);
        color: white;
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 1rem;
        padding: 16px 34px;
        border-radius: 50px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 8px 24px rgba(255,87,34,0.3);
    }

    .btn-primary-hero:hover {
        background: var(--c-orange-2);
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(255,87,34,0.4);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-hero {
        background: transparent;
        color: var(--c-text);
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 1rem;
        padding: 16px 34px;
        border-radius: 50px;
        text-decoration: none;
        border: 1px solid rgba(255,255,255,0.15);
        cursor: pointer;
        transition: all var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-secondary-hero:hover {
        border-color: rgba(255,87,34,0.4);
        background: rgba(255,87,34,0.07);
        color: var(--c-text);
        text-decoration: none;
        transform: translateY(-2px);
    }

    .hero-stats {
        display: flex;
        gap: 40px;
        margin-top: 60px;
        animation: fadeUp 0.6s 0.4s ease both;
    }

    .hero-stat {}
    .hero-stat .num {
        font-family: var(--font-display);
        font-weight: 800;
        font-size: 2rem;
        color: var(--c-white);
        line-height: 1;
    }
    .hero-stat .num span { color: var(--c-orange); }
    .hero-stat .lbl {
        font-size: 0.8rem;
        color: var(--c-muted);
        margin-top: 4px;
    }

    /* App store badges */
    .app-badges {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 32px;
        animation: fadeUp 0.6s 0.5s ease both;
    }

    .badge-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--c-surface-2);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        padding: 12px 20px;
        text-decoration: none;
        transition: all var(--transition);
        cursor: pointer;
    }

    .badge-btn:hover {
        border-color: rgba(255,87,34,0.3);
        background: rgba(255,87,34,0.06);
        transform: translateY(-2px);
        text-decoration: none;
    }

    .badge-btn img { height: 32px; width: auto; filter: brightness(1.2); }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ============================================================
       SECTION BASE
    ============================================================ */
    section { padding: 100px 0; }

    .section-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--c-orange-2);
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 16px;
    }

    .section-eyebrow::before {
        content: '';
        width: 24px;
        height: 2px;
        background: var(--c-orange);
        border-radius: 2px;
    }

    .section-title {
        font-family: var(--font-display);
        font-weight: 800;
        font-size: clamp(1.8rem, 3.5vw, 2.8rem);
        line-height: 1.1;
        letter-spacing: -0.02em;
        color: var(--c-white);
        margin-bottom: 16px;
    }

    .section-sub {
        font-size: 1rem;
        color: var(--c-muted);
        max-width: 540px;
        line-height: 1.7;
    }

    /* ============================================================
       FEATURES / HOW IT WORKS
    ============================================================ */
    .features-section {
        background: var(--c-surface);
        border-top: 1px solid var(--c-border);
        border-bottom: 1px solid var(--c-border);
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2px;
        background: var(--c-border);
        border-radius: var(--radius-xl);
        overflow: hidden;
        margin-top: 64px;
    }

    @media (max-width: 992px) {
        .features-grid { grid-template-columns: 1fr; }
    }

    .feature-card {
        background: var(--c-surface);
        padding: 44px 36px;
        position: relative;
        overflow: hidden;
        transition: background var(--transition);
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: var(--c-orange);
        transform: scaleX(0);
        transition: transform var(--transition);
        transform-origin: left;
    }

    .feature-card:hover { background: var(--c-surface-2); }
    .feature-card:hover::before { transform: scaleX(1); }

    .feature-icon {
        width: 56px;
        height: 56px;
        background: rgba(255,87,34,0.1);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        font-size: 1.6rem;
        transition: background var(--transition), transform var(--transition);
    }

    .feature-card:hover .feature-icon {
        background: rgba(255,87,34,0.18);
        transform: scale(1.05);
    }

    .feature-title {
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 1.2rem;
        color: var(--c-white);
        margin-bottom: 12px;
    }

    .feature-text {
        font-size: 0.92rem;
        color: var(--c-muted);
        line-height: 1.7;
    }

    .feature-num {
        position: absolute;
        top: 20px;
        right: 24px;
        font-family: var(--font-display);
        font-weight: 800;
        font-size: 4rem;
        color: rgba(255,255,255,0.03);
        line-height: 1;
        pointer-events: none;
    }

    /* ============================================================
       APP DOWNLOAD SECTION
    ============================================================ */
    .download-section {
        position: relative;
        overflow: hidden;
    }

    .download-card {
        background: linear-gradient(135deg, var(--c-surface) 0%, var(--c-surface-2) 100%);
        border: 1px solid var(--c-border);
        border-radius: var(--radius-xl);
        padding: 64px;
        position: relative;
        overflow: hidden;
    }

    .download-card::before {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,87,34,0.12) 0%, transparent 70%);
        pointer-events: none;
    }

    .download-card::after {
        content: '';
        position: absolute;
        bottom: -80px;
        left: 30%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(6,214,160,0.06) 0%, transparent 70%);
        pointer-events: none;
    }

    .app-download-btns {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 32px;
    }

    /* ============================================================
       NEWSLETTER
    ============================================================ */
    .newsletter-section {
        padding: 0;
    }

    .newsletter-wrapper {
        background: linear-gradient(135deg, var(--c-orange) 0%, #E64A19 100%);
        border-radius: var(--radius-xl);
        padding: 56px 64px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
        margin: 100px 0;
    }

    .newsletter-wrapper::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 280px;
        height: 280px;
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
    }

    .newsletter-wrapper::after {
        content: '';
        position: absolute;
        bottom: -80px;
        left: 20%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    .newsletter-content {
        position: relative;
        z-index: 2;
        flex: 1;
        min-width: 240px;
    }

    .newsletter-content .title {
        font-family: var(--font-display);
        font-weight: 800;
        font-size: clamp(1.4rem, 2.5vw, 2rem);
        color: white;
        line-height: 1.2;
        margin-bottom: 8px;
    }

    .newsletter-content .text {
        color: rgba(255,255,255,0.75);
        font-size: 0.95rem;
    }

    .newsletter-content form { margin-top: 0; }

    .input--grp {
        display: flex;
        align-items: center;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 50px;
        padding: 6px 6px 6px 20px;
        margin-top: 24px;
        max-width: 420px;
    }

    .input--grp .form-control {
        background: transparent;
        border: none;
        outline: none;
        color: white;
        font-size: 0.92rem;
        flex: 1;
        box-shadow: none !important;
        padding: 8px 0;
    }

    .input--grp .form-control::placeholder { color: rgba(255,255,255,0.55); }

    .search-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        display: flex;
        flex-shrink: 0;
        transition: transform var(--transition);
    }

    .search-btn:hover { transform: scale(1.1); }

    /* ============================================================
       FOOTER
    ============================================================ */
    footer { background: var(--c-surface); border-top: 1px solid var(--c-border); }

    .footer-bottom { padding: 64px 0 0; }

    .footer-wrapper {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 60px;
    }

    @media (max-width: 992px) {
        .footer-wrapper { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 576px) {
        .footer-wrapper { grid-template-columns: 1fr; }
    }

    .footer-logo img {
        height: 36px;
        margin-bottom: 20px;
        filter: brightness(1.1);
    }

    .footer-widget .txt {
        color: var(--c-muted);
        font-size: 0.9rem;
        line-height: 1.7;
        max-width: 280px;
    }

    .social-icon {
        display: flex;
        gap: 10px;
        list-style: none;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .social-icon li a {
        width: 40px;
        height: 40px;
        background: var(--c-surface-2);
        border: 1px solid var(--c-border);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition);
    }

    .social-icon li a:hover {
        background: rgba(255,87,34,0.12);
        border-color: rgba(255,87,34,0.3);
        transform: translateY(-2px);
    }

    .social-icon li a img { width: 18px; height: 18px; }

    .app-btn-grp {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .app-btn-grp a {
        background: var(--c-surface-2);
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 10px 16px;
        transition: all var(--transition);
        display: flex;
    }

    .app-btn-grp a:hover {
        border-color: rgba(255,87,34,0.3);
        transform: translateY(-2px);
    }

    .app-btn-grp img { height: 28px; }

    .footer-widget .subtitle {
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--c-white) !important;
        letter-spacing: 0.02em;
        margin-bottom: 20px;
    }

    .widget-links ul {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .widget-links ul li a {
        color: var(--c-muted);
        font-size: 0.9rem;
        text-decoration: none;
        transition: color var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .widget-links ul li a:hover { color: var(--c-orange-2); }

    .copyright {
        border-top: 1px solid var(--c-border);
        margin-top: 48px;
        padding: 24px 0;
        color: var(--c-muted);
        font-size: 0.85rem;
    }

    /* ============================================================
       RESPONSIVE
    ============================================================ */
    @media (max-width: 768px) {
        section { padding: 70px 0; }
        .hero-stats { gap: 24px; flex-wrap: wrap; }
        .download-card { padding: 36px 24px; }
        .newsletter-wrapper { padding: 36px 28px; }
        .menu { display: none; }
    }

    /* ============================================================
       YIELD CONTENT AREA — make sure child sections look right
    ============================================================ */
    .main-content-area { padding-top: 70px; }

    /* Scrolled state for nav */
    header.scrolled .navbar-bottom {
        background: rgba(10,10,15,0.95);
    }

    /* Floating delivery pill animation */
    .delivery-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 50px;
        padding: 10px 18px;
        font-size: 0.82rem;
        color: var(--c-text);
        position: absolute;
        animation: float 3s ease-in-out infinite;
        z-index: 3;
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    }

    .delivery-status-pill .pill-icon {
        width: 28px;
        height: 28px;
        background: var(--c-orange);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50%       { transform: translateY(-10px); }
    }

    .pill-1 { bottom: 15%; right: 8%; animation-delay: 0s; }
    .pill-2 { top: 20%; right: 5%; animation-delay: 1s; display: none; }

    @media (min-width: 992px) {
        .pill-1, .pill-2 { display: inline-flex; }
    }

    /* ============================================================
       CANVAS CITY ANIMATION - styled in JS below
    ============================================================ */
    </style>
</head>

<body>
    @php($fixed_link = \App\Models\DataSetting::where(['key'=>'fixed_link','type'=>'admin_landing_page'])->first())
    @php($fixed_link = isset($fixed_link->value)?json_decode($fixed_link->value, true):null)

    <!-- PRELOADER -->
    <div id="landing-loader"></div>

    <!-- HEADER -->
    <header id="main-header">
        <div class="navbar-bottom">
            <div class="container">
                <div class="navbar-bottom-wrapper">
                    <a href="{{route('home')}}" class="logo">
                        <img class="onerror-image" data-onerror-image="{{ asset('assets/admin/img/160x160/img2.jpg') }}"
                            src="{{ \App\CentralLogics\Helpers::logoFullUrl()}}" alt="Logo">
                    </a>

                    <ul class="menu">
                        <li><a id="home-link" href="{{route('home')}}" class="{{ Request::is('/') ? 'active' : '' }}"><span>{{ translate('messages.home') }}</span></a></li>
                        <li><a href="{{route('about-us')}}" class="{{ Request::is('about-us') ? 'active' : '' }}"><span>{{ translate('messages.about_us') }}</span></a></li>
                        <li><a href="{{route('privacy-policy')}}" class="{{ Request::is('privacy-policy') ? 'active' : '' }}"><span>{{ translate('messages.privacy_policy') }}</span></a></li>
                        <li><a href="{{route('terms-and-conditions')}}" class="{{ Request::is('terms-and-conditions') ? 'active' : '' }}"><span>{{ translate('messages.terms_and_condition') }}</span></a></li>
                        <li><a href="{{route('contact-us')}}" class="{{ Request::is('contact-us') ? 'active' : '' }}"><span>{{ translate('messages.contact_us') }}</span></a></li>
                        @if ($fixed_link && $fixed_link['web_app_url_status'])
                            <div class="me-2 d-lg-none">
                                <a class="cmn--btn me-xl-auto py-2" href="{{ $fixed_link['web_app_url'] }}" target="_blank">{{ translate('messages.browse_web') }}</a>
                            </div>
                        @endif
                    </ul>

                    <div class="nav-toggle d-lg-none ms-auto me-3">
                        <span></span><span></span><span></span>
                    </div>

                    @php($local = session()->has('landing_local')?session('landing_local'):null)
                    @php($lang = \App\CentralLogics\Helpers::get_business_settings('system_language'))
                    @if ($lang)
                        <div class="dropdown--btn-hover position-relative">
                            <a class="dropdown--btn border-0 px-3 header--btn text-capitalize d-flex align-items-center" href="javascript:void(0)">
                                @foreach($lang as $data)
                                    @if($data['code']==$local)
                                        <span class="me-1">{{$data['code']}}</span>
                                    @elseif(!$local && $data['default'] == true)
                                        <span class="me-1">{{$data['code']}}</span>
                                    @endif
                                @endforeach
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4H12.796a1.5 1.5 0 011.162 2.46l-4.796 5.48a1.5 1.5 0 01-1.915 0z" fill="#768D82"/></svg>
                            </a>
                            <ul class="dropdown-list py-0" style="min-width:120px; top:100%">
                                @foreach($lang as $data)
                                    @if($data['status']==1)
                                        <li class="py-0"><a href="{{route('lang',[$data['code']])}}">{{$data['code']}}</a></li>
                                        <li><hr class="dropdown-divider my-0"></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($fixed_link && $fixed_link['web_app_url_status'])
                        <div class="me-2 d-none d-lg-block">
                            <a class="cmn--btn me-xl-auto py-2" href="{{ $fixed_link['web_app_url'] }}" target="_blank">{{ translate('messages.browse_web') }}</a>
                        </div>
                    @endif

                    @if (isset($toggle_dm_registration) || isset($toggle_store_registration))
                        <div class="dropdown--btn-hover position-relative">
                            <a class="dropdown--btn header--btn text-capitalize d-flex align-items-center" href="javascript:void(0)">
                                <span class="me-1">{{ translate('Join us') }}</span>
                                <svg width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M5 5.5L0.5 0.5H9.5L5 5.5Z" fill="currentColor"/></svg>
                            </a>
                            <ul class="dropdown-list">
                                @if ($toggle_store_registration)
                                    <li><a href="{{ route('restaurant.create') }}">{{ translate('messages.vendor_registration') }}</a></li>
                                    @if ($toggle_dm_registration)<li><hr class="dropdown-divider"></li>@endif
                                @endif
                                @if ($toggle_dm_registration)
                                    <li><a href="{{ route('deliveryman.create') }}">{{ translate('messages.deliveryman_registration') }}</a></li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- ============================================================
         HERO SECTION WITH 2D CANVAS ANIMATION
    ============================================================ -->
    <section class="hero-section">
        <canvas id="city-canvas"></canvas>
        <div class="hero-overlay"></div>

        <!-- Floating status pills -->
        <div class="delivery-status-pill pill-1">
            <div class="pill-icon">🛵</div>
            <div>
                <div style="font-weight:600; font-size:0.82rem;">En camino</div>
                <div style="color:var(--c-muted); font-size:0.75rem;">Llega en ~8 min</div>
            </div>
        </div>

        <div class="delivery-status-pill pill-2">
            <div class="pill-icon">✅</div>
            <div>
                <div style="font-weight:600; font-size:0.82rem;">Pedido entregado</div>
                <div style="color:var(--c-green); font-size:0.75rem;">Hace 2 minutos</div>
            </div>
        </div>

        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-content">
                        <div class="hero-eyebrow">
                            <span class="live-dot"></span>
                            Delivery en tiempo real · Honduras
                        </div>

                        <h1 class="hero-title">
                            Todo lo que<br>
                            quieres, <span class="accent">en tu</span><br>
                            <span class="accent-2">puerta.</span>
                        </h1>

                        <p class="hero-sub">
                            Restaurantes, tiendas y más — rastreados en vivo, entregados rápido. La plataforma de delivery más moderna de Honduras.
                        </p>

                        <div class="hero-cta-group">
                            @php($landing_page_links = \App\Models\DataSetting::where(['type' => 'admin_landing_page','key' => 'download_user_app_links'])->first())
                            @php($landing_page_links = isset($landing_page_links->value) ? json_decode($landing_page_links->value, true) : null)
                            @if (isset($landing_page_links['playstore_url_status']) && $landing_page_links['playstore_url_status'])
                                <a href="{{ $landing_page_links['playstore_url'] ?? '#' }}" class="btn-primary-hero" target="_blank">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M3.18 23.76a2 2 0 001.26-.38l12.5-7.21L13.59 12 3.18 23.76zM20.48 10.39L17.34 8.6l-3.6 3.4 3.6 3.4 3.15-1.82a2 2 0 000-3.19zM3.18.24L13.59 12 16.94 8.6 4.44.62A2 2 0 003.18.24zM2 1.53a2 2 0 00-.23.91v19.12a2 2 0 00.23.91L12.79 12 2 1.53z"/></svg>
                                    Google Play
                                </a>
                            @endif
                            @if (isset($landing_page_links['apple_store_url_status']) && $landing_page_links['apple_store_url_status'])
                                <a href="{{ $landing_page_links['apple_store_url'] ?? '#' }}" class="btn-secondary-hero" target="_blank">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                                    App Store
                                </a>
                            @endif
                            @if ($fixed_link && $fixed_link['web_app_url_status'])
                                <a href="{{ $fixed_link['web_app_url'] }}" class="btn-secondary-hero" target="_blank">
                                    🌐 {{ translate('messages.browse_web') }}
                                </a>
                            @endif
                        </div>

                        <div class="hero-stats">
                            <div class="hero-stat">
                                <div class="num">500<span>+</span></div>
                                <div class="lbl">Restaurantes</div>
                            </div>
                            <div class="hero-stat">
                                <div class="num">30<span>k+</span></div>
                                <div class="lbl">Clientes activos</div>
                            </div>
                            <div class="hero-stat">
                                <div class="num">98<span>%</span></div>
                                <div class="lbl">Satisfacción</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         FEATURES SECTION
    ============================================================ -->
    <section class="features-section">
        <div class="container">
            <div class="text-center">
                <div class="section-eyebrow" style="justify-content:center">¿Por qué elegirnos?</div>
                <h2 class="section-title">Diseñado para Honduras</h2>
                <p class="section-sub" style="margin: 0 auto;">Velocidad, confianza y tecnología al servicio de tu antojo favorito.</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🛵</div>
                    <div class="feature-title">Rastreo en vivo</div>
                    <div class="feature-text">Sigue tu pedido en tiempo real desde que sale el restaurante hasta que llega a tu puerta. Sin sorpresas.</div>
                    <div class="feature-num">01</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <div class="feature-title">Entrega express</div>
                    <div class="feature-text">Red de repartidores certificados listos en tu ciudad. Promedio de entrega menor a 30 minutos.</div>
                    <div class="feature-num">02</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🍔</div>
                    <div class="feature-title">Cientos de opciones</div>
                    <div class="feature-text">Comida rápida, restaurantes gourmet, tiendas de conveniencia y mucho más. Todo en un solo lugar.</div>
                    <div class="feature-num">03</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💳</div>
                    <div class="feature-title">Pago seguro</div>
                    <div class="feature-text">Acepta tarjetas, efectivo y pagos digitales. Transacciones 100% encriptadas y protegidas.</div>
                    <div class="feature-num">04</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📍</div>
                    <div class="feature-title">Cobertura nacional</div>
                    <div class="feature-text">Presente en Tegucigalpa, San Pedro Sula y expandiéndose a más ciudades de Honduras.</div>
                    <div class="feature-num">05</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌟</div>
                    <div class="feature-title">Soporte 24/7</div>
                    <div class="feature-text">Equipo de atención al cliente disponible a toda hora para resolver cualquier inconveniente.</div>
                    <div class="feature-num">06</div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT (yield) -->
    <div class="main-content-area">
        @yield('content')
    </div>

    <!-- FOOTER -->
    <footer>
        @php($fixed_newsletter_title = \App\Models\DataSetting::where(['type' => 'admin_landing_page','key' => 'fixed_newsletter_title'])->first())
        @php($fixed_newsletter_title = isset($fixed_newsletter_title->value) ? $fixed_newsletter_title->value: null)
        @php($fixed_newsletter_sub_title = \App\Models\DataSetting::where(['type' => 'admin_landing_page','key' => 'fixed_newsletter_sub_title'])->first())
        @php($fixed_newsletter_sub_title = isset($fixed_newsletter_sub_title->value) ? $fixed_newsletter_sub_title->value: null)
        @php($fixed_footer_article_title = \App\Models\DataSetting::where(['type' => 'admin_landing_page','key' => 'fixed_footer_article_title'])->first())
        @php($fixed_footer_article_title = isset($fixed_footer_article_title->value) ? $fixed_footer_article_title->value: null)

        <div class="newsletter-section">
            <div class="container">
                <div class="newsletter-wrapper">
                    <div class="newsletter-content position-relative">
                        <h3 class="title">{{ $fixed_newsletter_title ?? '¡Recibe las mejores ofertas!' }}</h3>
                        <div class="text">{{ $fixed_newsletter_sub_title ?? 'Suscríbete y nunca pierdas un descuento.' }}</div>
                        <form method="post" action="{{route('newsletter.subscribe')}}">
                            @csrf
                            <div class="input--grp">
                                <input type="email" name="email" required class="form-control" placeholder="{{ translate('Enter your email address') }}">
                                <button class="search-btn" type="submit">
                                    <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="46" height="46" rx="23" fill="rgba(255,255,255,0.2)" />
                                        <path d="M25.9667 22.997L19.3001 29.2222C19.1353 29.3866 18.8556 29.6667 18.8556 29.6667C18.8556 29.6667 18.691 30.0553 18.8558 30.22L19.3803 30.7443C19.5448 30.9092 19.7648 31 19.9992 31C20.2336 31 20.4533 30.9092 20.618 30.7443L27.7448 23.6176C27.9101 23.4524 28.0006 23.2317 28 22.997C28.0006 22.7613 27.9102 22.5408 27.7448 22.3755L20.6246 15.2557C20.46 15.0908 20.2403 15 20.0057 15C19.7713 15 19.5516 15.0908 19.3868 15.2557L18.8624 15.78C18.5212 16.1212 19.0456 16.4367 19.3868 16.7778L25.9667 22.997Z" fill="white" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="footer-wrapper">
                    <div class="footer-widget">
                        <div class="footer-logo">
                            <a class="logo">
                                <img class="onerror-image" data-onerror-image="{{ asset('assets/admin/img/160x160/img2.jpg') }}"
                                    src="{{\App\CentralLogics\Helpers::logoFullUrl()}}" alt="Logo">
                            </a>
                        </div>
                        <div class="txt">{{ $fixed_footer_article_title }}</div>
                        <ul class="social-icon">
                            @php($social_media = \App\Models\SocialMedia::where('status', 1)->get())
                            @if (isset($social_media))
                                @foreach ($social_media as $social)
                                    <li>
                                        <a href="{{ $social->link }}" target="_blank">
                                            <img src="{{ asset('assets/landing/img/footer/'. $social->name.'.svg') }}" alt="">
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                        @php($landing_page_links = \App\Models\DataSetting::where(['type' => 'admin_landing_page','key' => 'download_user_app_links'])->first())
                        @php($landing_page_links = isset($landing_page_links->value) ? json_decode($landing_page_links->value, true) : null)
                        @if (isset($landing_page_links['playstore_url_status']) || isset($landing_page_links['apple_store_url_status']))
                            <div class="app-btn-grp">
                                @if (isset($landing_page_links['playstore_url_status']))
                                    <a href="{{ isset($landing_page_links['playstore_url']) ? $landing_page_links['playstore_url'] : '' }}">
                                        <img src="{{ asset('assets/landing/img/google.svg') }}" alt="Google Play">
                                    </a>
                                @endif
                                @if (isset($landing_page_links['apple_store_url_status']))
                                    <a href="{{ isset($landing_page_links['apple_store_url']) ? $landing_page_links['apple_store_url'] : '' }}">
                                        <img src="{{ asset('assets/landing/img/apple.svg') }}" alt="App Store">
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    @php($landing_data =\App\Models\DataSetting::where('type', 'admin_landing_page')->whereIn('key', ['shipping_policy_status','refund_policy_status','cancellation_policy_status'])->pluck('value','key')->toArray())
                    <div class="footer-widget widget-links">
                        <h5 class="subtitle mt-2">{{translate("messages.Suppport")}}</h5>
                        <ul>
                            <li><a href="{{route('privacy-policy')}}">{{ translate('messages.privacy_policy') }}</a></li>
                            <li><a href="{{route('terms-and-conditions')}}">{{ translate('messages.terms_and_condition') }}</a></li>
                            @if (isset($landing_data['refund_policy_status']) && $landing_data['refund_policy_status'] == 1)
                                <li><a href="{{route('refund')}}">{{ translate('messages.Refund Policy') }}</a></li>
                            @endif
                            @if (isset($landing_data['shipping_policy_status']) && $landing_data['shipping_policy_status'] == 1)
                                <li><a href="{{route('shipping-policy')}}">{{ translate('messages.Shipping Policy') }}</a></li>
                            @endif
                            @if (isset($landing_data['cancellation_policy_status']) && $landing_data['cancellation_policy_status'] == 1)
                                <li><a href="{{route('cancelation')}}">{{ translate('messages.Cancelation Policy') }}</a></li>
                            @endif
                        </ul>
                    </div>

                    <div class="footer-widget widget-links">
                        <h5 class="subtitle mt-2">{{translate("messages.Contact_Us")}}</h5>
                        <ul>
                            <li>
                                <a>
                                    <svg width="14" height="14" viewBox="0 0 12 16" fill="none"><path d="M10.238 2.74C9.267 1.064 7.542.04 5.624.001A4.5 4.5 0 005.378.001C3.46.04 1.735 1.064.764 2.74-.228 4.453-.256 6.51.692 8.244L4.659 15.506a.5.5 0 00.844 0L9.47 8.244C10.257 6.51 10.23 4.453 9.238 2.74zM5.5 7.25A2.25 2.25 0 115.5 2.75a2.25 2.25 0 010 4.5z" fill="currentColor"/></svg>
                                    {{ \App\CentralLogics\Helpers::get_settings('address') }}
                                </a>
                            </li>
                            <li>
                                <a href="/cdn-cgi/l/email-protection#d7acacf78b96a7a78b94b2b9a3a5b6bb9bb8b0beb4a48b9fb2bba7b2a5a4ededb0b2a388a4b2a3a3beb9b0a4fff0b2bab6bebb88b6b3b3a5b2a4a4f0fef7aaaa">
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M0.334 2.974A8 8 0 007.52 9.333c.32 0 .64-.067.946-.267.306-.2.534-.46.534-.933V2.974zm15.332 0l-7.18 6.093V2.974zM0 4.284V12.666A1.333 1.333 0 001.333 14h13.334A1.333 1.333 0 0016 12.666V4.284L8 9.556z" fill="currentColor"/></svg>
                                    {{ \App\CentralLogics\Helpers::get_settings('email_address') }}
                                </a>
                            </li>
                            <li>
                                <a href="tel:{{ \App\CentralLogics\Helpers::get_settings('phone') }}">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M13.604 10.275L11.65 8.321c-.698-.698-1.884-.419-2.163.488-.21.628-.907.977-1.535.837C6.557 9.298 4.673 7.484 4.324 6.018c-.21-.628.209-1.326.837-1.535.907-.279 1.186-1.465.488-2.163L3.696.366c-.558-.489-1.395-.489-1.884 0L.486 1.692C-.84 3.088.626 6.786 3.905 10.065 7.185 13.345 10.883 14.88 12.278 13.484l1.326-1.326c.489-.558.489-1.394 0-1.883z" fill="currentColor"/></svg>
                                    {{ \App\CentralLogics\Helpers::get_settings('phone') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="copyright text-center mt-3">
                    &copy; {{ \App\CentralLogics\Helpers::get_settings('footer_text') }}
                    by {{ \App\CentralLogics\Helpers::get_settings('business_name') }}
                </div>
            </div>
        </div>
    </footer>

    <!-- ============================================================
         SCRIPTS
    ============================================================ -->
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="{{ asset('assets/landing/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/landing/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/landing/js/viewport.jquery.js') }}"></script>
    <script src="{{ asset('assets/landing/js/wow.min.js') }}"></script>
    <script src="{{ asset('assets/landing/js/odometer.min.js') }}"></script>
    <script src="{{ asset('assets/landing/js/owl.min.js') }}"></script>
    <script src="{{ asset('assets/landing/js/main.js') }}"></script>
    <script src="{{ asset('assets/admin/js/toastr.js') }}"></script>
    {!! Toastr::message() !!}

    @if ($errors->any())
        <script>
            @foreach($errors->all() as $error)
            toastr.error('{{$error}}', Error, { CloseButton: true, ProgressBar: true });
            @endforeach
        </script>
    @endif

    <!-- ============================================================
         2D CITY/DELIVERY CANVAS ANIMATION
    ============================================================ -->
    <script>
    "use strict";
    (function() {
        const canvas = document.getElementById('city-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let W, H, animFrame;

        // ---- COLORS ----
        const C = {
            sky1: '#0A0A0F',
            sky2: '#0D0D18',
            ground: '#12121A',
            road:   '#1A1A26',
            roadLine: '#FF5722',
            building1: '#161622',
            building2: '#1E1E2E',
            buildingAccent: '#FF5722',
            window1: 'rgba(255,209,102,0.9)',
            window2: 'rgba(100,160,255,0.7)',
            window3: 'rgba(6,214,160,0.6)',
            starCol: 'rgba(255,255,255,0.7)',
            moto:   '#FF5722',
            motoGlow:'rgba(255,87,34,0.4)',
            car1: '#2A3A5C',
            car2: '#3A2A2A',
        };

        // ---- RESIZE ----
        function resize() {
            W = canvas.width  = canvas.offsetWidth;
            H = canvas.height = canvas.offsetHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        // ---- STARS ----
        const stars = Array.from({length: 80}, () => ({
            x: Math.random(),
            y: Math.random() * 0.5,
            r: Math.random() * 1.4 + 0.3,
            a: Math.random(),
            speed: Math.random() * 0.003 + 0.001
        }));

        // ---- BUILDINGS ----
        function makeBuilding(x, w, h, color, winRows, winCols) {
            return { x, w, h, color, winRows, winCols,
                winStates: Array.from({length: winRows * winCols}, () => Math.random() > 0.35),
                flashTimer: Math.random() * 200
            };
        }

        let buildings = [];
        function genBuildings() {
            buildings = [];
            const groundY = H * 0.62;
            let bx = -20;
            while (bx < W + 100) {
                const bw = 45 + Math.random() * 80;
                const bh = 80 + Math.random() * 220;
                const color = Math.random() > 0.5 ? C.building1 : C.building2;
                buildings.push(makeBuilding(bx, bw, bh, color, Math.floor(bh/28), Math.floor(bw/18)));
                bx += bw + 4 + Math.random() * 8;
            }
        }

        // ---- VEHICLES ----
        // Motorcycles (repartidores)
        const motos = Array.from({length: 3}, (_, i) => ({
            x: -200 - i * 420,
            y: 0,
            speed: 2.8 + i * 0.6 + Math.random() * 0.8,
            lane: i % 2 === 0 ? 0 : 1,
            pkg: Math.random() > 0.3
        }));

        // Cars (background)
        const cars = Array.from({length: 5}, (_, i) => ({
            x: -300 - i * 300,
            y: 0,
            speed: 1.2 + Math.random() * 0.8,
            lane: i % 2,
            color: i % 2 === 0 ? C.car1 : C.car2
        }));

        // ---- PARTICLES (exhaust/glow trails) ----
        const particles = [];
        function spawnParticle(x, y) {
            if (particles.length > 120) return;
            particles.push({
                x, y,
                vx: -1.5 - Math.random(),
                vy: (Math.random() - 0.5) * 0.8,
                life: 1,
                r: 3 + Math.random() * 3
            });
        }

        // ---- FLOATING FOOD ICONS ----
        const foodIcons = ['🍔','🍕','🌮','🥤','🍟','🍜','🥗','🍣'];
        const floaters = Array.from({length: 6}, (_, i) => ({
            icon: foodIcons[i],
            x: 0.1 + Math.random() * 0.8,
            y: 0.05 + Math.random() * 0.4,
            vy: -0.0002 - Math.random() * 0.0002,
            opacity: 0.08 + Math.random() * 0.1,
            size: 18 + Math.random() * 14,
            phase: Math.random() * Math.PI * 2
        }));

        let t = 0;

        // ---- DRAW ----
        function draw() {
            t++;
            ctx.clearRect(0, 0, W, H);

            const groundY = H * 0.62;
            const roadH   = H * 0.18;
            const roadY   = groundY;

            // Sky gradient
            const skyGrad = ctx.createLinearGradient(0, 0, 0, groundY);
            skyGrad.addColorStop(0, '#050508');
            skyGrad.addColorStop(0.5, '#0A0A14');
            skyGrad.addColorStop(1, '#0F0F1E');
            ctx.fillStyle = skyGrad;
            ctx.fillRect(0, 0, W, groundY);

            // Stars
            stars.forEach(s => {
                s.a += s.speed;
                const alpha = 0.3 + 0.5 * Math.abs(Math.sin(s.a));
                ctx.beginPath();
                ctx.arc(s.x * W, s.y * groundY, s.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255,255,255,${alpha})`;
                ctx.fill();
            });

            // Distant city horizon glow
            const horizGlow = ctx.createRadialGradient(W * 0.5, groundY, 0, W * 0.5, groundY, W * 0.6);
            horizGlow.addColorStop(0, 'rgba(255,87,34,0.08)');
            horizGlow.addColorStop(1, 'transparent');
            ctx.fillStyle = horizGlow;
            ctx.fillRect(0, groundY - 80, W, 80);

            // Buildings
            buildings.forEach(b => {
                const by = groundY - b.h;

                // Shadow
                ctx.fillStyle = 'rgba(0,0,0,0.4)';
                ctx.fillRect(b.x + 6, by + 6, b.w, b.h);

                // Body
                ctx.fillStyle = b.color;
                ctx.fillRect(b.x, by, b.w, b.h);

                // Top accent line
                ctx.fillStyle = C.buildingAccent;
                ctx.globalAlpha = 0.4 + 0.2 * Math.sin(t * 0.02 + b.x);
                ctx.fillRect(b.x, by, b.w, 2);
                ctx.globalAlpha = 1;

                // Windows
                b.flashTimer++;
                if (b.flashTimer > 180) {
                    b.flashTimer = 0;
                    const idx = Math.floor(Math.random() * b.winStates.length);
                    b.winStates[idx] = !b.winStates[idx];
                }

                const ww = 10, wh = 8, gap = 7;
                const startX = b.x + 8;
                const startY = by + 10;
                const cols = Math.max(1, Math.floor((b.w - 16) / (ww + gap)));
                const rows = Math.max(1, Math.floor((b.h - 20) / (wh + gap)));

                for (let r = 0; r < rows; r++) {
                    for (let c = 0; c < cols; c++) {
                        const idx = r * cols + c;
                        const on = idx < b.winStates.length ? b.winStates[idx] : true;
                        const wx = startX + c * (ww + gap);
                        const wy = startY + r * (wh + gap);
                        if (on) {
                            const winColors = [C.window1, C.window2, C.window3];
                            ctx.fillStyle = winColors[idx % 3];
                            ctx.globalAlpha = 0.7 + 0.3 * Math.sin(t * 0.01 + idx);
                            ctx.fillRect(wx, wy, ww, wh);
                            // Glow
                            ctx.shadowColor = winColors[idx % 3];
                            ctx.shadowBlur = 4;
                            ctx.fillRect(wx, wy, ww, wh);
                            ctx.shadowBlur = 0;
                        } else {
                            ctx.fillStyle = 'rgba(0,0,0,0.5)';
                            ctx.globalAlpha = 0.8;
                            ctx.fillRect(wx, wy, ww, wh);
                        }
                        ctx.globalAlpha = 1;
                    }
                }
            });

            // Ground
            const groundGrad = ctx.createLinearGradient(0, groundY, 0, H);
            groundGrad.addColorStop(0, '#181820');
            groundGrad.addColorStop(1, '#0D0D14');
            ctx.fillStyle = groundGrad;
            ctx.fillRect(0, groundY, W, H - groundY);

            // Road
            ctx.fillStyle = C.road;
            ctx.fillRect(0, roadY, W, roadH);

            // Road edge lines
            ctx.strokeStyle = 'rgba(255,87,34,0.25)';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(0, roadY + 2); ctx.lineTo(W, roadY + 2);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(0, roadY + roadH - 2); ctx.lineTo(W, roadY + roadH - 2);
            ctx.stroke();

            // Dashed center lane
            ctx.setLineDash([28, 18]);
            ctx.strokeStyle = 'rgba(255,87,34,0.15)';
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            const dashY = roadY + roadH / 2;
            ctx.moveTo(0, dashY);
            ctx.lineTo(W, dashY);
            ctx.stroke();
            ctx.setLineDash([]);

            // Road moving dashes effect
            ctx.setLineDash([20, 14]);
            ctx.strokeStyle = 'rgba(255,255,255,0.04)';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(-((t * 3) % 34), roadY + roadH * 0.3);
            ctx.lineTo(W + 34, roadY + roadH * 0.3);
            ctx.stroke();
            ctx.setLineDash([]);

            // ---- UPDATE + DRAW CARS ----
            cars.forEach(c => {
                c.x += c.speed;
                if (c.x > W + 150) c.x = -150 - Math.random() * 200;

                const laneOffset = c.lane === 0 ? roadH * 0.22 : roadH * 0.6;
                const cy = roadY + laneOffset;
                const cw = 52, ch = 22;

                // Car glow under
                ctx.shadowColor = c.color;
                ctx.shadowBlur = 10;

                // Body
                ctx.fillStyle = c.color;
                ctx.beginPath();
                ctx.roundRect(c.x, cy - ch / 2, cw, ch, 5);
                ctx.fill();
                ctx.shadowBlur = 0;

                // Wheels
                ctx.fillStyle = '#111';
                [c.x + 8, c.x + cw - 10].forEach(wx => {
                    ctx.beginPath();
                    ctx.arc(wx, cy + ch / 2 - 3, 5, 0, Math.PI * 2);
                    ctx.fill();
                });

                // Headlights
                ctx.fillStyle = 'rgba(255,230,100,0.9)';
                ctx.beginPath();
                ctx.arc(c.x + cw - 2, cy - 3, 3, 0, Math.PI * 2);
                ctx.fill();
                ctx.fillStyle = 'rgba(255,230,100,0.5)';
                ctx.beginPath();
                ctx.arc(c.x + cw - 2, cy + 3, 3, 0, Math.PI * 2);
                ctx.fill();
            });

            // ---- UPDATE + DRAW MOTOS ----
            motos.forEach((m, i) => {
                m.x += m.speed;
                if (m.x > W + 100) {
                    m.x = -150 - Math.random() * 300;
                    m.speed = 2.8 + Math.random() * 1.4;
                    m.pkg = Math.random() > 0.3;
                }

                const laneOffset = m.lane === 0 ? roadH * 0.35 : roadH * 0.72;
                const my = roadY + laneOffset;

                // Spawn trail particles
                if (t % 3 === 0) spawnParticle(m.x - 10, my + 6);

                // Glow under moto
                const glowR = ctx.createRadialGradient(m.x + 16, my + 12, 0, m.x + 16, my + 12, 30);
                glowR.addColorStop(0, C.motoGlow);
                glowR.addColorStop(1, 'transparent');
                ctx.fillStyle = glowR;
                ctx.fillRect(m.x - 14, my - 8, 60, 40);

                // Moto body
                ctx.fillStyle = C.moto;
                ctx.shadowColor = C.moto;
                ctx.shadowBlur = 8;
                ctx.beginPath();
                ctx.roundRect(m.x, my - 8, 32, 14, 3);
                ctx.fill();
                ctx.shadowBlur = 0;

                // Wheels
                ctx.fillStyle = '#222';
                ctx.strokeStyle = 'rgba(255,87,34,0.5)';
                ctx.lineWidth = 1.5;
                [m.x + 5, m.x + 26].forEach(wx => {
                    ctx.beginPath();
                    ctx.arc(wx, my + 8, 7, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.stroke();
                });

                // Rider (simple silhouette)
                ctx.fillStyle = '#1A1A2A';
                ctx.beginPath();
                ctx.ellipse(m.x + 18, my - 14, 5, 8, -0.2, 0, Math.PI * 2);
                ctx.fill();
                ctx.beginPath();
                ctx.arc(m.x + 20, my - 22, 5, 0, Math.PI * 2);
                ctx.fillStyle = '#FF8A50';
                ctx.fill();

                // Helmet highlight
                ctx.fillStyle = 'rgba(255,255,255,0.3)';
                ctx.beginPath();
                ctx.arc(m.x + 21, my - 24, 2, 0, Math.PI);
                ctx.fill();

                // Package if carrying
                if (m.pkg) {
                    ctx.fillStyle = '#FFD166';
                    ctx.shadowColor = '#FFD166';
                    ctx.shadowBlur = 6;
                    ctx.beginPath();
                    ctx.roundRect(m.x + 24, my - 14, 12, 10, 2);
                    ctx.fill();
                    ctx.shadowBlur = 0;
                    // Box lines
                    ctx.strokeStyle = 'rgba(0,0,0,0.3)';
                    ctx.lineWidth = 0.8;
                    ctx.beginPath();
                    ctx.moveTo(m.x + 30, my - 14); ctx.lineTo(m.x + 30, my - 4);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.moveTo(m.x + 24, my - 9); ctx.lineTo(m.x + 36, my - 9);
                    ctx.stroke();
                }

                // Headlight beam
                const beamGrad = ctx.createLinearGradient(m.x + 32, my, m.x + 80, my);
                beamGrad.addColorStop(0, 'rgba(255,230,100,0.4)');
                beamGrad.addColorStop(1, 'transparent');
                ctx.fillStyle = beamGrad;
                ctx.beginPath();
                ctx.moveTo(m.x + 32, my - 2);
                ctx.lineTo(m.x + 80, my - 10);
                ctx.lineTo(m.x + 80, my + 8);
                ctx.closePath();
                ctx.fill();
            });

            // ---- PARTICLES ----
            for (let i = particles.length - 1; i >= 0; i--) {
                const p = particles[i];
                p.x += p.vx;
                p.y += p.vy;
                p.life -= 0.025;
                p.r *= 0.97;
                if (p.life <= 0) { particles.splice(i, 1); continue; }
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255,87,34,${p.life * 0.25})`;
                ctx.fill();
            }

            // ---- FLOATING FOOD ICONS ----
            floaters.forEach(f => {
                f.y += f.vy;
                if (f.y < -0.05) f.y = 0.5;
                const fx = f.x * W;
                const fy = f.y * groundY;
                const wobble = Math.sin(t * 0.015 + f.phase) * 8;
                ctx.font = `${f.size}px serif`;
                ctx.globalAlpha = f.opacity + 0.03 * Math.sin(t * 0.02 + f.phase);
                ctx.fillText(f.icon, fx + wobble, fy);
                ctx.globalAlpha = 1;
            });

            // ---- PAVEMENT DASHES (foreground) ----
            const pavY = roadY + roadH + 6;
            ctx.fillStyle = '#16161E';
            ctx.fillRect(0, pavY, W, H - pavY);

            // Sidewalk texture dots
            for (let dx = (t * 0.5) % 40 - 40; dx < W + 40; dx += 40) {
                ctx.beginPath();
                ctx.arc(dx, pavY + 6, 1.5, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(255,255,255,0.03)';
                ctx.fill();
            }

            animFrame = requestAnimationFrame(draw);
        }

        // Init
        genBuildings();
        window.addEventListener('resize', () => { resize(); genBuildings(); });

        // Start
        draw();

        // Hide preloader after short delay
        setTimeout(() => {
            const loader = document.getElementById('landing-loader');
            if (loader) loader.classList.add('hidden');
        }, 700);

    })();
    </script>

    <!-- NAV SCROLL EFFECT -->
    <script>
        "use strict";
        window.addEventListener('scroll', function() {
            const header = document.getElementById('main-header');
            if (window.scrollY > 40) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>

    <!-- OWL CAROUSEL + EXISTING SCRIPTS -->
    <script>
        "use strict";
        $(".main-category-slider").owlCarousel({
            loop: true, nav: false, dots: true, items: 1, margin: 12, autoplay: true,
            rtl: {{ $landing_site_direction === 'rtl'?'true':'false' }},
        });
        $(".testimonial-slider").owlCarousel({
            loop: false, margin: 15, responsiveClass: true, nav: false, dots: false,
            autoplay: true, autoplayTimeout: 2000, autoplayHoverPause: true, items: 1,
            rtl: {{ $landing_site_direction === 'rtl'?'true':'false' }},
            responsive: { 768: { items: 2, margin: 20 }, 992: { items: 3, margin: 20 }, 1200: { items: 3, margin: 22 } },
        });
        $(".owl-prev").html('<i class="fas fa-angle-left">');
        $(".owl-next").html('<i class="fas fa-angle-right">');

        let sync1 = $("#sync1");
        let sync2 = $("#sync2");
        let thumbnailItemClass = ".owl-item";
        let slides = sync1.owlCarousel({
            items: 1, loop: false, margin: 30, mouseDrag: true, touchDrag: true,
            pullDrag: false, scrollPerPage: true, autoplayHoverPause: false, nav: false, dots: false,
            rtl: {{ $landing_site_direction === 'rtl'?'true':'false' }},
        }).on("changed.owl.carousel", syncPosition);

        function syncPosition(el) {
            let $owl_slider = $(this).data("owl.carousel");
            let loop = $owl_slider.options.loop;
            let current;
            if (loop) {
                let count = el.item.count - 1;
                current = Math.round(el.item.index - el.item.count / 2 - 0.5);
                if (current < 0) current = count;
                if (current > count) current = 0;
            } else { current = el.item.index; }
            let owl_thumbnail = sync2.data("owl.carousel");
            let itemClass = "." + owl_thumbnail.options.itemClass;
            let thumbnailCurrentItem = sync2.find(itemClass).removeClass("synced").eq(current);
            thumbnailCurrentItem.addClass("synced");
            if (!thumbnailCurrentItem.hasClass("active")) sync2.trigger("to.owl.carousel", [current, 500, true]);
        }

        let thumbs = sync2.owlCarousel({
            items: 2, loop: false, margin: 0, autoplay: false, nav: true, navText: ["",""],
            dots: false, mouseDrag: true, touchDrag: true,
            rtl: {{ $landing_site_direction === 'rtl'?'true':'false' }},
            responsive: { 400: { items: 3 }, 768: { items: 6 }, 1200: { items: 6 } },
            onInitialized: function(e) {
                $(e.target).find(thumbnailItemClass).eq(this._current).addClass("synced");
            },
        }).on("click", thumbnailItemClass, function(e) {
            e.preventDefault();
            sync1.trigger("to.owl.carousel", [$(e.target).parents(thumbnailItemClass).index(), 500, true]);
        }).on("changed.owl.carousel", function(el) {
            sync1.data("owl.carousel").to(el.item.index, 500, true);
        });
        sync1.owlCarousel();
    </script>

    <script src="{{asset('assets/admin/intltelinput/js/intlTelInput.min.js')}}"></script>
    <script>
        "use strict";
        const inputs = document.querySelectorAll('input[type="tel"]');
        inputs.forEach(input => {
            window.intlTelInput(input, {
                initialCountry: "{{$countryCode}}",
                utilsScript: "{{ asset('assets/admin/intltelinput/js/utils.js') }}",
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
    </script>

    @stack('script_2')

</body>
</html>