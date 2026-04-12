<div id="sidebarMain" class="d-none">
    <aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered">
        <div class="navbar-vertical-container">
            <div class="navbar-brand-wrapper justify-content-between">
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}" aria-label="Zarpya Admin">
                    <img class="navbar-brand-logo initial--36" src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}" alt="Logo">
                    <img class="navbar-brand-logo-mini initial--36" src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}" alt="Logo">
                </a>
                <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                    <i class="tio-clear tio-lg"></i>
                </button>
            </div>
            <div class="navbar-vertical-content bg--005555" id="navbar-vertical-content">
                <form class="sidebar--search-form">
                    <div class="search--form-group">
                        <button type="button" class="btn"><i class="tio-search"></i></button>
                        <input type="text" class="form-control form--control" placeholder="Buscar...">
                    </div>
                </form>
                <ul class="navbar-nav navbar-nav-lg nav-tabs">
                    <li class="nav-item">
                        <small class="nav-subtitle">Zarpya Módulos</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <!-- Pricing -->
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/zarpya/pricing*') ? 'show active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                            <i class="tio-tag-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Precios Delivery</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display:{{ Request::is('admin/zarpya/pricing*') ? 'block' : 'none' }}">
                            <li class="nav-item {{ Request::is('admin/zarpya/pricing/categories') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.zarpya.pricing.categories') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Categorías</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/zarpya/pricing/dynamic-rules') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.zarpya.pricing.rules') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Multiplicadores</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/zarpya/pricing/levels') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.zarpya.pricing.levels') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Niveles Repartidor</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Taxi -->
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/zarpya/taxi*') ? 'show active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                            <i class="tio-car nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Taxi</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display:{{ Request::is('admin/zarpya/taxi*') ? 'block' : 'none' }}">
                            <li class="nav-item {{ Request::is('admin/zarpya/taxi/rates') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.zarpya.taxi.rates') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Tarifas</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/zarpya/taxi/rides') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.zarpya.taxi.rides') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Viajes</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Rental -->
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/zarpya/rental*') ? 'show active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                            <i class="tio-car-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Renta de Vehículos</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display:{{ Request::is('admin/zarpya/rental*') ? 'block' : 'none' }}">
                            <li class="nav-item {{ Request::is('admin/zarpya/rental/vehicles') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.zarpya.rental.vehicles') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Flota</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/zarpya/rental/bookings') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.zarpya.rental.bookings') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Reservas</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Services -->
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/zarpya/services*') ? 'show active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                            <i class="tio-tool nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Servicios</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display:{{ Request::is('admin/zarpya/services*') ? 'block' : 'none' }}">
                            <li class="nav-item {{ Request::is('admin/zarpya/services/categories') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.zarpya.services.categories') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Categorías</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/zarpya/services/providers') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.zarpya.services.providers') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Proveedores</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/zarpya/services/requests') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.zarpya.services.requests') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Solicitudes</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- WhatsApp -->
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/zarpya/whatsapp*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.zarpya.whatsapp.logs') }}">
                            <i class="tio-chat-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">WhatsApp</span>
                        </a>
                    </li>

                    <li class="nav-item py-5"></li>
                </ul>
            </div>
        </div>
    </aside>
</div>