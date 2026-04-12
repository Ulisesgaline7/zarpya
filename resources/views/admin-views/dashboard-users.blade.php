@extends('layouts.admin.app')

@section('title', \App\Models\BusinessSetting::where(['key' => 'business_name'])->first()->value ?? 'Dashboard')

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ── Tarjetas de perfil ─────────────────────────────────────── */
.profile-card {
    border-radius: 12px;
    overflow: hidden;
    transition: transform .15s, box-shadow .15s;
    border: none;
}
.profile-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.10); }

.profile-card .card-header {
    padding: 20px 20px 14px;
    border-bottom: none;
}
.profile-card .profile-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
}
.profile-card .big-number {
    font-size: 2.2rem; font-weight: 700; line-height: 1;
}
.profile-card .label { font-size: 13px; color: #6c757d; font-weight: 500; }

/* Colores por perfil */
.profile-clientes  .card-header { background: linear-gradient(135deg,#e8f5e9,#f1f8e9); }
.profile-clientes  .profile-icon { background: #28a74520; color: #28a745; }
.profile-clientes  .big-number   { color: #28a745; }

.profile-zarperos  .card-header { background: linear-gradient(135deg,#e3f2fd,#e8eaf6); }
.profile-zarperos  .profile-icon { background: #005555; color: #fff; }
.profile-zarperos  .big-number   { color: #005555; }

.profile-empleados .card-header { background: linear-gradient(135deg,#fff8e1,#fff3e0); }
.profile-empleados .profile-icon { background: #ffa80020; color: #ffa800; }
.profile-empleados .big-number   { color: #ffa800; }

/* ── Mini stat chips ────────────────────────────────────────── */
.stat-chip {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 12px;
    border-radius: 8px;
    background: #f8f9fa;
    font-size: 13px;
    text-decoration: none;
    color: inherit;
    transition: background .12s;
}
.stat-chip:hover { background: #e9ecef; color: inherit; text-decoration: none; }
.stat-chip .dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}
.stat-chip .chip-num { font-weight: 700; font-size: 15px; margin-left: auto; }

/* ── Nivel Zarpero badge ────────────────────────────────────── */
.level-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;
}
.level-standard { background:#28a74520; color:#28a745; border:1px solid #28a74540; }
.level-pro       { background:#007bff20; color:#007bff; border:1px solid #007bff40; }
.level-elite     { background:#6f42c120; color:#6f42c1; border:1px solid #6f42c140; }

/* ── Satisfacción ───────────────────────────────────────────── */
.satisfaction-bar { height: 8px; border-radius: 8px; background: #e9ecef; overflow: hidden; }
.satisfaction-bar .fill { height: 100%; border-radius: 8px; transition: width .4s; }

/* ── Mapa ───────────────────────────────────────────────────── */
.map-wrapper { border-radius: 12px; overflow: hidden; height: 280px; }
#map-canvas  { width: 100%; height: 100%; }
</style>
@endpush

@section('content')
<div class="content container-fluid">
@if(auth('admin')->user()->role_id == 1)

{{-- ── Encabezado ──────────────────────────────────────────────── --}}
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-header-title mb-0">👥 Gestión de Usuarios</h1>
            <p class="page-header-text m-0 text-muted">Resumen de clientes, Zarperos y empleados por zona</p>
        </div>
        <div class="col-auto">
            <select name="zone_id" class="form-control js-select2-custom set-filter"
                    data-url="{{ url()->full() }}" data-filter="zone_id" style="min-width:200px;">
                <option value="all">Todas las zonas</option>
                @foreach(\App\Models\Zone::orderBy('name')->get() as $zone)
                    <option value="{{ $zone->id }}" {{ $params['zone_id'] == $zone->id ? 'selected' : '' }}>
                        {{ $zone->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- ── Tres perfiles principales ───────────────────────────────── --}}
@php
    $total_customers  = $blocked_customers + $active_customers;
    $total_deliveryman = $inactive_deliveryman + $active_deliveryman + $blocked_deliveryman;
    $total_employees  = $employees->count();

    // Niveles Zarpero
    $levelCounts = [];
    if(\Illuminate\Support\Facades\Schema::hasTable('deliveryman_levels')) {
        $levelCounts = \App\Models\DeliverymanLevel::withCount('deliverymen')
            ->orderBy('sort_order')->get();
    }
@endphp

<div class="row g-3 mb-4">

    {{-- CLIENTES --}}
    <div class="col-lg-4">
        <div class="card profile-card profile-clientes h-100">
            <div class="card-header d-flex align-items-center gap-3">
                <div class="profile-icon">👤</div>
                <div>
                    <div class="big-number">{{ number_format($total_customers) }}</div>
                    <div class="label">Clientes registrados</div>
                </div>
                <a href="{{ route('admin.users.customer.list') }}" class="btn btn-sm btn-outline-success ml-auto">
                    Ver todos
                </a>
            </div>
            <div class="card-body pt-2 pb-3">
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('admin.users.customer.list', ['filter' => 'active']) }}" class="stat-chip">
                        <span class="dot" style="background:#28a745;"></span>
                        <span>Clientes activos</span>
                        <span class="chip-num text-success">{{ number_format($active_customers) }}</span>
                    </a>
                    <a href="{{ route('admin.users.customer.list', ['filter' => 'new']) }}" class="stat-chip">
                        <span class="dot" style="background:#007bff;"></span>
                        <span>Nuevos este mes</span>
                        <span class="chip-num text-primary">{{ number_format($newly_joined) }}</span>
                    </a>
                    <a href="{{ route('admin.users.customer.list', ['filter' => 'blocked']) }}" class="stat-chip">
                        <span class="dot" style="background:#dc3545;"></span>
                        <span>Bloqueados</span>
                        <span class="chip-num text-danger">{{ number_format($blocked_customers) }}</span>
                    </a>
                </div>

                {{-- Satisfacción compacta --}}
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-semibold text-muted">Satisfacción del cliente</span>
                        <span class="small text-muted">{{ $reviews }} reseñas</span>
                    </div>
                    @php
                        $pos_pct = $reviews > 0 ? round($positive_reviews / $reviews * 100) : 0;
                        $neg_pct = $reviews > 0 ? round($negative_reviews / $reviews * 100) : 0;
                    @endphp
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="small" style="width:60px;">Positivas</span>
                        <div class="satisfaction-bar flex-grow-1">
                            <div class="fill" style="width:{{ $pos_pct }}%; background:#28a745;"></div>
                        </div>
                        <span class="small fw-bold text-success" style="width:35px; text-align:right;">{{ $pos_pct }}%</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small" style="width:60px;">Negativas</span>
                        <div class="satisfaction-bar flex-grow-1">
                            <div class="fill" style="width:{{ $neg_pct }}%; background:#dc3545;"></div>
                        </div>
                        <span class="small fw-bold text-danger" style="width:35px; text-align:right;">{{ $neg_pct }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ZARPEROS (Repartidores) --}}
    <div class="col-lg-4">
        <div class="card profile-card profile-zarperos h-100">
            <div class="card-header d-flex align-items-center gap-3">
                <div class="profile-icon">🛵</div>
                <div>
                    <div class="big-number">{{ number_format($total_deliveryman) }}</div>
                    <div class="label">Zarperos registrados</div>
                </div>
                <a href="{{ route('admin.users.delivery-man.list') }}" class="btn btn-sm ml-auto"
                   style="background:#005555; color:#fff; border:none;">
                    Ver todos
                </a>
            </div>
            <div class="card-body pt-2 pb-3">
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('admin.users.delivery-man.list', ['filter' => 'active']) }}" class="stat-chip">
                        <span class="dot" style="background:#28a745;"></span>
                        <span>En línea ahora</span>
                        <span class="chip-num" style="color:#005555;">{{ number_format($active_deliveryman) }}</span>
                    </a>
                    <a href="{{ route('admin.users.delivery-man.list', ['filter' => 'new']) }}" class="stat-chip">
                        <span class="dot" style="background:#17a2b8;"></span>
                        <span>Nuevos este mes</span>
                        <span class="chip-num text-info">{{ number_format($newly_joined_deliveryman) }}</span>
                    </a>
                    <a href="{{ route('admin.users.delivery-man.list', ['filter' => 'inactive']) }}" class="stat-chip">
                        <span class="dot" style="background:#ffc107;"></span>
                        <span>Fuera de línea</span>
                        <span class="chip-num text-warning">{{ number_format($inactive_deliveryman) }}</span>
                    </a>
                    <a href="{{ route('admin.users.delivery-man.list', ['filter' => 'blocked']) }}" class="stat-chip">
                        <span class="dot" style="background:#dc3545;"></span>
                        <span>Suspendidos</span>
                        <span class="chip-num text-danger">{{ number_format($blocked_deliveryman) }}</span>
                    </a>
                </div>

                {{-- Niveles Zarpero --}}
                @if($levelCounts->count())
                <div class="mt-3 pt-3 border-top">
                    <div class="small fw-semibold text-muted mb-2">Distribución por nivel</div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($levelCounts as $lv)
                        <a href="{{ route('admin.zarpya.pricing.levels') }}"
                           class="level-pill level-{{ $lv->slug }}" style="text-decoration:none;">
                            {{ $lv->name }} · {{ $lv->deliverymen_count }}
                        </a>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.zarpya.pricing.ranking') }}"
                       class="btn btn-sm btn-outline-secondary mt-2 w-100">
                        🏆 Ver ranking semanal
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- EMPLEADOS --}}
    <div class="col-lg-4">
        <div class="card profile-card profile-empleados h-100">
            <div class="card-header d-flex align-items-center gap-3">
                <div class="profile-icon">🏢</div>
                <div>
                    <div class="big-number">{{ number_format($total_employees) }}</div>
                    <div class="label">Empleados del sistema</div>
                </div>
                <a href="{{ route('admin.users.employee.list') }}" class="btn btn-sm btn-outline-warning ml-auto">
                    Ver todos
                </a>
            </div>
            <div class="card-body pt-2 pb-3">
                <div class="d-flex flex-column gap-2">
                    @forelse($employees->take(5) as $emp)
                    <div class="stat-chip">
                        <img src="{{ $emp->image_full_url }}" class="rounded-circle"
                             width="28" height="28" style="object-fit:cover;"
                             onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'">
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:13px;">{{ $emp->f_name }} {{ $emp->l_name }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $emp->role?->name ?? 'Sin rol' }}</div>
                        </div>
                        @if($emp->is_logged_in ?? false)
                            <span class="dot" style="background:#28a745;" title="En línea"></span>
                        @endif
                    </div>
                    @empty
                    <p class="text-muted small mb-0">Sin empleados registrados.</p>
                    @endforelse
                </div>

                @if($total_employees > 5)
                <div class="mt-2 text-center">
                    <a href="{{ route('admin.users.employee.list') }}" class="small text-muted">
                        +{{ $total_employees - 5 }} empleados más →
                    </a>
                </div>
                @endif

                <div class="mt-3 pt-3 border-top">
                    <a href="{{ route('admin.users.employee.add-new') }}" class="btn btn-sm btn-warning w-100">
                        + Agregar empleado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Crecimiento de clientes + Mapa de Zarperos ──────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title mb-0">📈 Crecimiento de clientes</h5>
                <small class="text-muted">Nuevos registros por mes — {{ now()->year }}</small>
            </div>
            <div class="card-body">
                <div id="customer-growth-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header border-0 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">🗺️ Zarperos activos en el mapa</h5>
                    <small class="text-muted">Ubicación en tiempo real</small>
                </div>
                <a href="{{ route('admin.users.delivery-man.list') }}" class="btn btn-sm btn-outline-secondary">
                    Ver lista completa
                </a>
            </div>
            <div class="card-body p-0">
                <div class="px-3 pb-2">
                    <form action="javascript:" id="search-form">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="tio-search"></i></span>
                            </div>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Buscar Zarpero por nombre...">
                        </div>
                    </form>
                </div>
                <div class="map-wrapper mx-3 mb-3">
                    <div id="map-canvas"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Top Zarperos ─────────────────────────────────────────────── --}}
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">🏆 Top Zarperos del período</h5>
                <a href="{{ route('admin.zarpya.pricing.ranking') }}" class="btn btn-sm btn-warning">
                    Ver ranking completo
                </a>
            </div>
            <div class="card-body p-0" id="top-deliveryman-view">
                @include('admin-views.partials._top-deliveryman', ['top_deliveryman' => $data['top_deliveryman']])
            </div>
        </div>
    </div>
</div>

@else
{{-- Vista para empleados sin rol de admin --}}
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-header-title">Bienvenido, {{ auth('admin')->user()->f_name }}</h1>
            <p class="page-header-text">No tienes acceso al resumen completo de usuarios.</p>
        </div>
    </div>
</div>
@endif
</div>
@endsection

@push('script_2')
<script src="{{ asset('/public/assets/admin/js/apex-charts/apexcharts.js') }}"></script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key','map_api_key')->first()->value }}&callback=initialize&libraries=drawing,places,marker&v=3.61">
</script>

<script>
"use strict";
let map, infowindow, dmMarkers = [];

function initialize() {
    @php($default_location = \App\Models\BusinessSetting::where('key','default_location')->first())
    @php($default_location = $default_location?->value ? json_decode($default_location->value, true) : null)
    const center = {
        lat: {{ $default_location['lat'] ?? 14.0818 }},
        lng: {{ $default_location['lng'] ?? -87.2068 }}
    };
    const mapId = "{{ \App\Models\BusinessSetting::where('key','map_api_key')->first()?->value }}";
    map = new google.maps.Map(document.getElementById('map-canvas'), {
        zoom: 13, center, mapTypeId: 'roadmap', mapId,
    });
    infowindow = new google.maps.InfoWindow();
    const { AdvancedMarkerElement } = google.maps.marker;
    const dmbounds = new google.maps.LatLngBounds(null);
    const deliveryMen = <?php echo json_encode($deliveryMen); ?>;

    deliveryMen.forEach(dm => {
        if (!dm.lat) return;
        const point = new google.maps.LatLng(dm.lat, dm.lng);
        dmbounds.extend(point);
        const img = document.createElement('img');
        img.src = "{{ asset('public/assets/admin/img/delivery_boy_active.png') }}";
        img.style.cssText = 'width:36px;height:36px;border-radius:50%;';
        const marker = new AdvancedMarkerElement({ position: point, map, content: img });
        dmMarkers[dm.id] = marker;
        marker.addListener('click', () => {
            infowindow.setContent(`
                <div style="display:flex;gap:10px;align-items:center;padding:4px;">
                    <img src="${dm.image_link}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                    <div>
                        <strong>${dm.name}</strong><br>
                        <small>${dm.location}</small><br>
                        <small>Pedidos asignados: ${dm.assigned_order_count}</small>
                    </div>
                </div>`);
            infowindow.open(map, marker);
        });
    });
    if (!dmbounds.isEmpty()) map.fitBounds(dmbounds);
}

$('#search-form').on('submit', function(e) {
    e.preventDefault();
    const deliveryMen = <?php echo json_encode($deliveryMen); ?>;
    $.post({
        url: '{{ route('admin.users.delivery-man.active-search') }}',
        data: new FormData(this),
        cache: false, contentType: false, processData: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success(data) {
            if (!data.dm) { toastr.error('Zarpero no encontrado'); return; }
            let first = true;
            deliveryMen.forEach(item => {
                const active = data.dm.some(d => d.id === item.id);
                const marker = dmMarkers[item.id];
                if (!marker) return;
                const img = document.createElement('img');
                img.src = active
                    ? "{{ asset('public/assets/admin/img/delivery_boy_active.png') }}"
                    : "{{ asset('public/assets/admin/img/delivery_boy_map_inactive.png') }}";
                img.style.cssText = 'width:36px;height:36px;border-radius:50%;';
                marker.content = img;
                if (active && first) {
                    map.panTo(marker.position); map.setZoom(16); first = false;
                    infowindow.setContent(`<strong>${item.name}</strong><br><small>${item.location}</small>`);
                    infowindow.open(map, marker);
                }
            });
        }
    });
});

// Gráfica crecimiento de clientes
new ApexCharts(document.querySelector('#customer-growth-chart'), {
    series: [{ name: 'Nuevos clientes', data: [
        {{ $user_data[1] }}, {{ $user_data[2] }}, {{ $user_data[3] }},
        {{ $user_data[4] }}, {{ $user_data[5] }}, {{ $user_data[6] }},
        {{ $user_data[7] }}, {{ $user_data[8] }}, {{ $user_data[9] }},
        {{ $user_data[10] }}, {{ $user_data[11] }}, {{ $user_data[12] }}
    ]}],
    chart: { height: 220, type: 'area', toolbar: { show: false } },
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    colors: ['#005555'],
    fill: { type: 'gradient', colors: ['#005555'], gradient: { opacityFrom: .4, opacityTo: .05 } },
    xaxis: { categories: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'] },
    yaxis: { min: 0, tickAmount: 4, labels: { formatter: v => Math.round(v) } },
    tooltip: { y: { formatter: v => v + ' clientes' } },
    grid: { borderColor: '#f0f0f0' },
}).render();
</script>
@endpush
