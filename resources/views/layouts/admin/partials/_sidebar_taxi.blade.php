@php($store_logo = \App\Models\BusinessSetting::where(['key' => 'logo'])->first())
@php($mod_id = Config::get('module.current_module_id') ?? 46)

<div id="sidebarMain" class="d-none">
    <aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered">
        <div class="navbar-vertical-container">
            <div class="navbar-brand-wrapper justify-content-between">
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}?module_id={{ $mod_id }}">
                    <img class="navbar-brand-logo initial--36 onerror-image"
                         data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                         src="{{ \App\CentralLogics\Helpers::get_full_url('business', $store_logo?->value ?? '', $store_logo?->storage[0]?->value ?? 'public', 'favicon') }}"
                         alt="Logo">
                    <img class="navbar-brand-logo-mini initial--36 onerror-image"
                         data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                         src="{{ \App\CentralLogics\Helpers::get_full_url('business', $store_logo?->value ?? '', $store_logo?->storage[0]?->value ?? 'public', 'favicon') }}"
                         alt="Logo">
                </a>
                <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                    <i class="tio-clear tio-lg"></i>
                </button>
                <div class="navbar-nav-wrap-content-left">
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align"></i>
                    </button>
                </div>
            </div>

            <div class="navbar-vertical-content bg--005555" id="navbar-vertical-content">
                <ul class="navbar-nav navbar-nav-lg nav-tabs">

                    {{-- Dashboard --}}
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin') && Request::get('module_id') == $mod_id ? 'show active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                           href="{{ route('admin.dashboard') }}?module_id={{ $mod_id }}">
                            <i class="tio-home-vs-1-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Dashboard</span>
                        </a>
                    </li>

                    {{-- Viajes --}}
                    <li class="nav-item">
                        <small class="nav-subtitle">Viajes</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/zarpya/taxi/rides*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                           href="{{ route('admin.zarpya.taxi.rides') }}">
                            <i class="tio-car nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Todos los viajes</span>
                        </a>
                    </li>

                    {{-- Conductores --}}
                    <li class="nav-item">
                        <small class="nav-subtitle">Conductores de Taxi</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/zarpya/taxi/drivers') && !Request::has('status') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                           href="{{ route('admin.zarpya.taxi.drivers') }}">
                            <i class="tio-group-senior nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Lista de conductores</span>
                        </a>
                    </li>

                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/zarpya/taxi/drivers') && Request::get('status') === 'pending' ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                           href="{{ route('admin.zarpya.taxi.drivers') }}?status=pending">
                            <i class="tio-user-add nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Solicitudes pendientes</span>
                        </a>
                    </li>

                    {{-- Tarifas --}}
                    <li class="nav-item">
                        <small class="nav-subtitle">Configuración</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/zarpya/taxi/rates*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                           href="{{ route('admin.zarpya.taxi.rates') }}">
                            <i class="tio-dollar-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Tarifas por zona</span>
                        </a>
                    </li>

                    <li class="nav-item py-5"></li>
                    @includeIf('layouts.admin.partials._logout_modal')
                </ul>
            </div>
        </div>
    </aside>
</div>
<div id="sidebarCompact" class="d-none"></div>

@push('script_2')
<script>
$(window).on('load', function () {
    if ($(".navbar-vertical-content li.active").length) {
        $('.navbar-vertical-content').animate({
            scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
        }, 10);
    }
});
</script>
@endpush
