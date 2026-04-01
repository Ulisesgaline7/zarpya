<!DOCTYPE html>
<?php
    $log_email_succ = session()->get('log_email_succ');
?>
<html dir="{{ $site_direction }}" lang="{{ $locale }}" class="{{ $site_direction === 'rtl'?'active':'' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{translate('messages.login')}}</title>
    <link rel="shortcut icon" href="{{asset('public/favicon.ico')}}">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{asset('assets/admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">

    <style>
        :root {
            --brand-orange: #FF4D00;
            --brand-orange-light: #FF7A3D;
            --brand-dark: #0D0D0D;
            --brand-dark-2: #161616;
            --brand-dark-3: #1E1E1E;
            --brand-gray: #2A2A2A;
            --brand-muted: #6B6B6B;
            --brand-white: #F5F0EB;
            --brand-accent: #FFD166;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--brand-dark);
            font-family: 'DM Sans', sans-serif;
            color: var(--brand-white);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ---- BACKGROUND ---- */
        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(255,77,0,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,77,0,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }
        .bg-glow {
            position: fixed;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,77,0,0.12) 0%, transparent 70%);
            top: -100px;
            right: -100px;
            pointer-events: none;
            z-index: 0;
        }
        .bg-glow-2 {
            position: fixed;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,209,102,0.06) 0%, transparent 70%);
            bottom: -50px;
            left: 20%;
            pointer-events: none;
            z-index: 0;
        }

        /* ---- LAYOUT ---- */
        .auth-shell {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        /* ---- LEFT PANEL ---- */
        .panel-left {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 56px;
            overflow: hidden;
            background: var(--brand-dark-2);
            border-right: 1px solid rgba(255,77,0,0.1);
        }

        .panel-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='100' cy='100' r='80' fill='none' stroke='rgba(255,77,0,0.06)' stroke-width='1'/%3E%3Ccircle cx='100' cy='100' r='55' fill='none' stroke='rgba(255,77,0,0.04)' stroke-width='1'/%3E%3Ccircle cx='100' cy='100' r='30' fill='none' stroke='rgba(255,77,0,0.08)' stroke-width='1'/%3E%3C/svg%3E") center/cover no-repeat;
            opacity: 0.5;
            pointer-events: none;
        }

        /* Delivery illustration */
        .delivery-visual {
            position: absolute;
            bottom: 60px;
            right: -30px;
            width: 340px;
            opacity: 0.12;
            pointer-events: none;
        }

        .panel-brand {
            position: relative;
            z-index: 2;
        }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 56px;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: var(--brand-orange);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 24px rgba(255,77,0,0.4);
            flex-shrink: 0;
        }

        .brand-icon svg { width: 26px; height: 26px; fill: white; }

        .brand-img {
            height: 36px;
            width: auto;
            object-fit: contain;
        }

        .panel-headline {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(2.2rem, 3.5vw, 3rem);
            line-height: 1.1;
            letter-spacing: -0.02em;
            color: var(--brand-white);
        }

        .panel-headline span {
            display: block;
            color: var(--brand-orange);
        }

        .panel-sub {
            margin-top: 20px;
            font-size: 0.95rem;
            color: var(--brand-muted);
            line-height: 1.6;
            max-width: 300px;
        }

        /* Stat chips */
        .stat-chips {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .stat-chip {
            background: var(--brand-gray);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 50px;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            animation: fadeUp 0.6s ease both;
        }

        .stat-chip:nth-child(2) { animation-delay: 0.1s; }
        .stat-chip:nth-child(3) { animation-delay: 0.2s; }

        .stat-dot {
            width: 8px;
            height: 8px;
            background: var(--brand-orange);
            border-radius: 50%;
            box-shadow: 0 0 8px rgba(255,77,0,0.7);
            animation: pulse-dot 1.8s ease infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.7; }
        }

        /* ---- RIGHT PANEL ---- */
        .panel-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 56px;
        }

        .form-card {
            width: 100%;
            max-width: 420px;
            animation: fadeUp 0.5s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,77,0,0.1);
            border: 1px solid rgba(255,77,0,0.2);
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--brand-orange-light);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .form-eyebrow::before {
            content: '';
            width: 6px;
            height: 6px;
            background: var(--brand-orange);
            border-radius: 50%;
        }

        .form-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.9rem;
            letter-spacing: -0.02em;
            color: var(--brand-white);
            margin-bottom: 6px;
        }

        .form-desc {
            font-size: 0.9rem;
            color: var(--brand-muted);
            margin-bottom: 36px;
        }

        /* ---- FORM CONTROLS ---- */
        .field-group {
            margin-bottom: 20px;
        }

        .field-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #999;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .field-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--brand-muted);
            font-size: 1.1rem;
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            background: var(--brand-dark-3) !important;
            border: 1px solid rgba(255,255,255,0.07) !important;
            border-radius: 12px !important;
            color: var(--brand-white) !important;
            font-family: 'DM Sans', sans-serif !important;
            font-size: 0.95rem !important;
            padding: 14px 16px 14px 46px !important;
            outline: none !important;
            transition: border-color 0.2s, box-shadow 0.2s !important;
            height: auto !important;
        }

        .field-input::placeholder { color: #444 !important; }

        .field-input:focus {
            border-color: rgba(255,77,0,0.5) !important;
            box-shadow: 0 0 0 3px rgba(255,77,0,0.08) !important;
            background: var(--brand-dark-3) !important;
        }

        /* Password field suffix */
        .field-suffix {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            padding: 0 16px;
        }

        .field-suffix a {
            color: var(--brand-muted) !important;
            transition: color 0.2s;
        }

        .field-suffix a:hover { color: var(--brand-orange) !important; }

        /* ---- CAPTCHA ---- */
        #reload-captcha {
            background: var(--brand-dark-3);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 20px;
        }

        #custome_recaptcha {
            background: transparent !important;
            border: none !important;
            color: var(--brand-white) !important;
            font-size: 0.9rem !important;
            padding: 4px 8px !important;
        }

        /* ---- BOTTOM BAR ---- */
        .form-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            color: #888;
        }

        .remember-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--brand-orange);
            cursor: pointer;
        }

        .forget-link {
            font-size: 0.85rem;
            color: var(--brand-orange-light) !important;
            text-decoration: none !important;
            transition: opacity 0.2s;
        }

        .forget-link:hover { opacity: 0.75; }

        /* ---- SUBMIT BUTTON ---- */
        .btn-submit {
            width: 100%;
            background: var(--brand-orange) !important;
            color: white !important;
            border: none !important;
            border-radius: 12px !important;
            font-family: 'Syne', sans-serif !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            letter-spacing: 0.02em !important;
            padding: 15px !important;
            cursor: pointer;
            transition: all 0.25s !important;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.25s;
        }

        .btn-submit:hover {
            background: var(--brand-orange-light) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 28px rgba(255,77,0,0.35) !important;
        }

        .btn-submit:hover::before { opacity: 1; }
        .btn-submit:active { transform: translateY(0px) !important; }

        /* ---- DEMO CARD ---- */
        .demo-card {
            margin-top: 20px;
            background: var(--brand-dark-3);
            border: 1px dashed rgba(255,77,0,0.25);
            border-radius: 12px;
            padding: 16px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .demo-card span {
            font-size: 0.82rem;
            color: #888;
            line-height: 1.7;
        }

        .demo-card strong { color: var(--brand-white); }

        .btn-copy-demo {
            background: rgba(255,77,0,0.15) !important;
            border: 1px solid rgba(255,77,0,0.3) !important;
            color: var(--brand-orange) !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-size: 0.85rem !important;
            cursor: pointer;
            transition: background 0.2s !important;
        }

        .btn-copy-demo:hover { background: rgba(255,77,0,0.25) !important; }

        /* ---- MODALS ---- */
        .modal-content {
            background: var(--brand-dark-2) !important;
            border: 1px solid rgba(255,255,255,0.08) !important;
            border-radius: 20px !important;
            color: var(--brand-white) !important;
        }

        .modal-header {
            border-bottom: 1px solid rgba(255,255,255,0.05) !important;
        }

        .close-modal-icon {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .close-modal-icon:hover { background: rgba(255,77,0,0.15); }

        .forget-pass-content {
            text-align: center;
            padding: 12px;
        }

        .forget-pass-content img { height: 80px; margin-bottom: 20px; }
        .forget-pass-content h4 { font-family: 'Syne', sans-serif; font-weight: 700; margin-bottom: 10px; }
        .forget-pass-content p { color: var(--brand-muted); font-size: 0.9rem; line-height: 1.6; }

        .forget-pass-content .form-control {
            background: var(--brand-dark-3) !important;
            border: 1px solid rgba(255,255,255,0.08) !important;
            border-radius: 10px !important;
            color: var(--brand-white) !important;
            margin-top: 16px;
            padding: 12px 16px !important;
        }

        /* ---- RESPONSIVE ---- */
        @media (max-width: 900px) {
            .auth-shell { grid-template-columns: 1fr; }
            .panel-left { display: none; }
            .panel-right { padding: 32px 24px; }
        }
    </style>
</head>

<body>
    <div class="bg-grid"></div>
    <div class="bg-glow"></div>
    <div class="bg-glow-2"></div>

    <main id="content" role="main">
        <div class="auth-shell">

            <!-- LEFT -->
            <div class="panel-left">
                <div class="panel-brand">
                    <div class="brand-logo-wrap">
                        <div class="brand-icon">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                            </svg>
                        </div>
                        @php($store_logo = \App\Models\BusinessSetting::where(['key' => 'logo'])->first())
                        <img class="brand-img onerror-image"
                            data-onerror-image="{{asset('assets/admin/img/favicon.png')}}"
                            src="{{\App\CentralLogics\Helpers::get_full_url('business', $store_logo?->value?? '', $store_logo?->storage[0]?->value ?? 'public','favicon')}}"
                            alt="Logo">
                    </div>

                    <h1 class="panel-headline">
                        Tu servicio
                        <span>de delivery</span>
                        en Honduras.
                    </h1>
                    <p class="panel-sub">Administra pedidos, restaurantes y repartidores desde un solo lugar.</p>
                </div>

                <div class="stat-chips">
                    <div class="stat-chip">
                        <span class="stat-dot"></span>
                        <span>Pedidos en tiempo real</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-dot"></span>
                        <span>Rastreo GPS</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-dot"></span>
                        <span>Panel unificado</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="panel-right">
                <div class="form-card">
                    <div class="form-eyebrow">Panel de acceso</div>
                    <h2 class="form-title">{{ translate($role) }} {{translate('messages.login')}}</h2>
                    <p class="form-desc">{{translate('messages.welcome_back_login_to_your_panel')}}.</p>

                    <form action="{{route('login_post')}}" method="post" id="form-id">
                        @csrf
                        <input type="hidden" name="role" value="{{  $role ?? null }}">

                        <!-- Email -->
                        <div class="field-group">
                            <label class="field-label" for="signinSrEmail">{{translate('messages.your_email')}}</label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="tio-email-outlined"></i></span>
                                <input type="email" class="field-input js-form-message" name="email" id="signinSrEmail"
                                    tabindex="1" placeholder="correo@empresa.com" value="{{ $email ?? '' }}"
                                    required data-msg="{{ translate('Please_enter_a_valid_email_address.') }}">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="field-group">
                            <label class="field-label" for="signupSrPassword">{{translate('messages.password')}}</label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="tio-lock-outlined"></i></span>
                                <input type="password" class="field-input js-toggle-password" name="password" id="signupSrPassword"
                                    placeholder="{{translate('messages.password_length_placeholder',['length'=>'6+'])}}"
                                    value="{{ $password ?? '' }}" required
                                    data-msg="{{translate('messages.invalid_password_warning')}}"
                                    data-hs-toggle-password-options='{
                                        "target": "#changePassTarget",
                                        "defaultClass": "tio-hidden-outlined",
                                        "showClass": "tio-visible-outlined",
                                        "classChangeTarget": "#changePassIcon"
                                    }'>
                                <div id="changePassTarget" class="field-suffix">
                                    <a href="javascript:">
                                        <i id="changePassIcon" class="tio-visible-outlined"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Meta bar -->
                        <div class="form-meta">
                            <label class="remember-label">
                                <input type="checkbox" name="remember" {{ $password ? 'checked' : '' }}>
                                {{translate('messages.remember_me')}}
                            </label>

                            <div id="forget-password" style="display: {{ $role == 'admin' ? '' : 'none' }};">
                                <span type="button" class="forget-link" data-toggle="modal" data-target="#forgetPassModal">
                                    {{ translate('Forget Password') }}?
                                </span>
                            </div>
                            <div id="forget-password1" style="display: {{ $role == 'vendor' ? '' : 'none' }};">
                                <span type="button" class="forget-link" data-toggle="modal" data-target="#forgetPassModal1">
                                    {{ translate('messages.Forget Password') }}?
                                </span>
                            </div>
                        </div>

                        <!-- Captcha -->
                        @php($recaptcha = \App\CentralLogics\Helpers::get_business_settings('recaptcha'))
                        @if(isset($recaptcha) && $recaptcha['status'] == 1)
                            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                            <input type="hidden" name="set_default_captcha" id="set_default_captcha_value" value="0">
                            <div class="row p-2 d-none" id="reload-captcha">
                                <div class="col-6 pr-0">
                                    <input type="text" class="form-control" name="custome_recaptcha" id="custome_recaptcha"
                                        required placeholder="{{translate('Enter recaptcha value')}}" autocomplete="off"
                                        value="{{env('APP_MODE')=='dev'? session('six_captcha'):''}}">
                                </div>
                                <div class="col-6 bg-white rounded d-flex">
                                    <img src="<?php echo $custome_recaptcha->inline(); ?>" class="rounded w-100" />
                                    <div class="p-3 pr-0 capcha-spin reloadCaptcha"><i class="tio-cached"></i></div>
                                </div>
                            </div>
                        @else
                            <div class="row p-2" id="reload-captcha">
                                <div class="col-6 pr-0">
                                    <input type="text" class="form-control" name="custome_recaptcha" id="custome_recaptcha"
                                        required placeholder="{{translate('Enter recaptcha value')}}" autocomplete="off"
                                        value="{{env('APP_MODE')=='dev'? session('six_captcha'):''}}">
                                </div>
                                <div class="col-6 bg-white rounded d-flex">
                                    <img src="<?php echo $custome_recaptcha->inline(); ?>" class="rounded w-100" />
                                    <div class="p-3 pr-0 capcha-spin reloadCaptcha"><i class="tio-cached"></i></div>
                                </div>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-submit" id="signInBtn">
                            {{translate('messages.login')}}
                        </button>
                    </form>

                    <!-- Demo credentials -->
                    @if(env('APP_MODE') == 'demo')
                        @if (isset($role) && $role == 'admin')
                            <div class="demo-card">
                                <div>
                                    <span><strong>Email</strong> : admin@admin.com</span><br>
                                    <span><strong>Contraseña</strong> : 12345678</span>
                                </div>
                                <button class="btn btn-copy-demo copy_cred"><i class="tio-copy"></i></button>
                            </div>
                        @endif
                        @if (isset($role) && $role == 'vendor')
                            <div class="demo-card">
                                <div>
                                    <span><strong>Email</strong> : test.restaurant@gmail.com</span><br>
                                    <span><strong>Contraseña</strong> : 12345678</span>
                                </div>
                                <button class="btn btn-copy-demo copy_cred2"><i class="tio-copy"></i></button>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL: Admin forget pass -->
    <div class="modal fade" id="forgetPassModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-end">
                    <span type="button" class="close-modal-icon" data-dismiss="modal"><i class="tio-clear"></i></span>
                </div>
                <div class="modal-body">
                    <div class="forget-pass-content">
                        <img src="{{asset('assets/admin/img/send-mail.svg')}}" alt="">
                        <h4>{{ translate('Send_Mail_to_Your_Email') }}?</h4>
                        <p>
                            {{ translate('A mail will be send to your registered email') }}
                            {{ isset($role) && $role == 'admin' ? \App\Models\Admin::where('role_id',1)->first()?->masked_email : '' }}
                            {{ translate('with a link to change passowrd') }}
                        </p>
                        <a class="btn btn-submit mt-3 d-block" href="{{route('reset-password')}}">
                            {{ translate('Send Mail') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: Vendor forget pass -->
    <div class="modal fade" id="forgetPassModal1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-end">
                    <span type="button" class="close-modal-icon" data-dismiss="modal"><i class="tio-clear"></i></span>
                </div>
                <div class="modal-body">
                    <div class="forget-pass-content">
                        <img src="{{asset('assets/admin/img/send-mail.svg')}}" alt="">
                        <h4>{{ translate('messages.Send_Mail_to_Your_Email') }}?</h4>
                        <form action="{{ route('vendor-reset-password') }}" method="post">
                            @csrf
                            <input type="email" name="email" class="form-control"
                                placeholder="{{ translate('messages.plesae_enter_your_registerd_email') }}" required>
                            <button type="submit" class="btn btn-submit mt-3 w-100">{{ translate('messages.Send Mail') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: Success mail -->
    <div class="modal fade" id="successMailModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-end">
                    <span type="button" class="close-modal-icon" data-dismiss="modal"><i class="tio-clear"></i></span>
                </div>
                <div class="modal-body">
                    <div class="forget-pass-content">
                        <img src="{{asset('assets/admin/img/sent-mail.svg')}}" alt="">
                        <h4>{{ translate('A mail has been sent to your registered email') }}!</h4>
                        <p>{{ translate('Click the link in the mail description to change password') }}</p>
                        <button class="btn btn-submit mt-3 w-100" data-dismiss="modal">{{ translate('Got_It') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>
    <script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
    <script src="{{asset('assets/admin')}}/js/toastr.js"></script>
    {!! Toastr::message() !!}

    @if ($errors->any())
        <script>
            "use strict";
            @foreach($errors->all() as $error)
            toastr.error('{{translate($error)}}', Error, { CloseButton: true, ProgressBar: true });
            @endforeach
        </script>
    @endif

    @if ($log_email_succ)
        @php(session()->forget('log_email_succ'))
        <script>"use strict"; $('#successMailModal').modal('show');</script>
    @endif

    <script>
        "use strict";
        $("#role-select").change(function() {
            var v = $(this).val();
            $("#forget-password").toggle(v === "admin");
            $("#forget-password1").toggle(v === "vendor");
        });

        $(document).on('ready', function () {
            $('.js-toggle-password').each(function () { new HSTogglePassword(this).init(); });
            $('.js-validate').each(function () { $.HSCore.components.HSValidation.init($(this)); });
        });

        $(document).on('click', '.reloadCaptcha', function () {
            $.ajax({
                url: "{{ route('reload-captcha') }}", type: "GET", dataType: 'json',
                beforeSend: function () { $('.capcha-spin').addClass('active'); },
                success: function (data) { $('#reload-captcha').html(data.view); },
                complete: function () { $('.capcha-spin').removeClass('active'); }
            });
        });

        $(document).ready(function () {
            $('.onerror-image').on('error', function () {
                $(this).attr('src', $(this).data('onerror-image'));
            });
        });
    </script>

    @if(isset($recaptcha) && $recaptcha['status'] == 1)
        <script src="https://www.google.com/recaptcha/api.js?render={{$recaptcha['site_key']}}"></script>
        <script>
            $(document).ready(function () {
                $('#signInBtn').click(function (e) {
                    if ($('#set_default_captcha_value').val() == 1) { $('#form-id').submit(); return true; }
                    e.preventDefault();
                    if (typeof grecaptcha === 'undefined') {
                        toastr.error('Invalid recaptcha key provided.');
                        $('#reload-captcha').removeClass('d-none');
                        $('#set_default_captcha_value').val('1');
                        return;
                    }
                    grecaptcha.ready(function () {
                        grecaptcha.execute('{{$recaptcha["site_key"]}}', { action: 'submit' }).then(function (token) {
                            $('#g-recaptcha-response').value = token;
                            $('#form-id').submit();
                        });
                    });
                    window.onerror = function (message) {
                        var err = 'An unexpected error occurred. Please check the recaptcha configuration';
                        if (message.includes('Invalid site key')) err = 'Invalid site key provided.';
                        else if (message.includes('not loaded')) err = 'reCAPTCHA API could not be loaded.';
                        $('#reload-captcha').removeClass('d-none');
                        $('#set_default_captcha_value').val('1');
                        toastr.error(err);
                        return true;
                    };
                });
            });
        </script>
    @endif

    @if(env('APP_MODE') == 'demo')
        <script>
            "use strict";
            $('.copy_cred').on('click', function () {
                $('#signinSrEmail').val('admin@admin.com');
                $('#signupSrPassword').val('12345678');
                toastr.success('¡Copiado exitosamente!', '¡Éxito!', { CloseButton: true, ProgressBar: true });
            });
            $('.copy_cred2').on('click', function () {
                $('#signinSrEmail').val('test.restaurant@gmail.com');
                $('#signupSrPassword').val('12345678');
                toastr.success('¡Copiado exitosamente!', '¡Éxito!', { CloseButton: true, ProgressBar: true });
            });
        </script>
    @endif

    <script>
        if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent))
            document.write('<script src="{{asset("assets/admin")}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
    </script>
</body>
</html>