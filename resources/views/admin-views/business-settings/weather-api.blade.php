@extends('layouts.admin.app')

@section('title', 'APIs de Clima')

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
#weather-map { width: 100%; height: 320px; border-radius: 10px; }
.zone-weather-card { border-radius: 10px; border: 2px solid #e9ecef; transition: border-color .15s; cursor: pointer; }
.zone-weather-card.selected { border-color: #005555; box-shadow: 0 0 0 3px rgba(0,85,85,.12); }
.zone-weather-card.raining  { border-color: #0ea5e9; background: #f0f9ff; }
.weather-icon { font-size: 2rem; line-height: 1; }
.temp-big { font-size: 1.8rem; font-weight: 800; line-height: 1; }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{ asset('public/assets/admin/img/api.png') }}" class="w--26" alt="">
            </span>
            <span>APIs de Terceros</span>
        </h1>
        @include('admin-views.business-settings.partials.third-party-links')
    </div>

    @php
        $mapKey = \App\Models\BusinessSetting::where('key','map_api_key')->first()?->value;
        $defaultLoc = json_decode(\App\Models\BusinessSetting::where('key','default_location')->first()?->value ?? '{}', true);
        $zones = \App\Models\Zone::where('status',1)->orderBy('name')->get();
    @endphp

    <div class="row g-3 mb-4">

        {{-- ── Configuración API key ─────────────────────────── --}}
        <div class="col-lg-5">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header border-0 d-flex align-items-center gap-2">
                    <span style="font-size:1.4rem;">🌧️</span>
                    <div>
                        <h5 class="card-title mb-0">OpenWeatherMap</h5>
                        <small class="text-muted">Multiplicador de lluvia ×1.4 automático</small>
                    </div>
                    @if($owKey)
                        <span class="badge badge-soft-success ml-auto">✓ Activa</span>
                    @else
                        <span class="badge badge-soft-danger ml-auto">Sin configurar</span>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.business-settings.third-party.weather-api-update') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="input-label">API Key</label>
                            <input type="text" name="openweather_api_key" class="form-control"
                                   value="{{ env('APP_MODE') !== 'demo' ? ($owKey ?? '') : '' }}"
                                   placeholder="Pega tu API key de openweathermap.org">
                            <small class="text-muted">
                                Plan gratuito en
                                <a href="https://openweathermap.org/api" target="_blank" rel="noopener">openweathermap.org/api</a>
                                — solo necesitas <strong>Current Weather Data</strong>.
                            </small>
                        </div>
                        <button type="{{ env('APP_MODE') !== 'demo' ? 'submit' : 'button' }}"
                                class="btn btn--primary btn-block call-demo">
                            Guardar API Key
                        </button>
                    </form>

                    @if($owKey)
                    <div class="alert alert-info py-2 mb-0 small mt-2">
                        <strong>⏳ ¿Error 401?</strong> Las keys nuevas tardan <strong>hasta 2 horas</strong> en activarse.
                        Cópiala desde <a href="https://home.openweathermap.org/api_keys" target="_blank">openweathermap.org/api_keys</a>.
                    </div>
                    <hr>
                    {{-- Estado actual --}}
                    @if($weatherData)
                    <div class="d-flex align-items-center gap-3 p-3 rounded bg-light mb-3">
                        <span class="weather-icon">{{ $weatherData['is_raining'] ? '🌧️' : '☀️' }}</span>
                        <div>
                            <div class="temp-big">{{ $weatherData['temp'] ? round($weatherData['temp']).'°C' : '—' }}</div>
                            <div class="small text-muted">{{ ucfirst($weatherData['description'] ?? '') }}</div>
                            <div style="font-size:11px;" class="text-muted">
                                Humedad {{ $weatherData['humidity'] ?? '—' }}% ·
                                {{ isset($weatherData['checked_at']) ? \Carbon\Carbon::parse($weatherData['checked_at'])->diffForHumans() : '' }}
                            </div>
                        </div>
                        @if($weatherData['is_raining'])
                            <span class="badge badge-soft-primary ml-auto">×1.4 ACTIVO</span>
                        @endif
                    </div>
                    @endif

                    {{-- Acciones --}}
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.zarpya.pricing.rain.toggle') }}" method="POST" class="flex-fill">
                            @csrf
                            <input type="hidden" name="active" value="{{ $rainActive ? 0 : 1 }}">
                            <button class="btn btn-sm btn-{{ $rainActive ? 'warning' : 'outline-warning' }} w-100">
                                {{ $rainActive ? '🔴 Desactivar manual' : '🟢 Activar manual' }}
                            </button>
                        </form>
                        <button class="btn btn-sm btn-outline-primary flex-fill" id="check-weather-now">
                            🔄 Verificar ahora
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Mapa + verificación por zona ─────────────────── --}}
        <div class="col-lg-7">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">🗺️ Clima por Zona</h5>
                        <small class="text-muted">Selecciona una zona para verificar su clima actual</small>
                    </div>
                    <select id="zone-selector" class="form-control form-control-sm" style="max-width:180px;">
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}"
                                    data-lat="{{ $zone->coordinates ? json_decode($zone->coordinates)?->coordinates[0][0][1] ?? $defaultLoc['lat'] : $defaultLoc['lat'] }}"
                                    data-lng="{{ $zone->coordinates ? json_decode($zone->coordinates)?->coordinates[0][0][0] ?? $defaultLoc['lng'] : $defaultLoc['lng'] }}">
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body p-0">
                    {{-- Mapa --}}
                    <div id="weather-map" class="mx-3 mt-2"></div>

                    {{-- Resultado clima por zona --}}
                    <div id="zone-weather-result" class="px-3 py-2" style="min-height:60px;">
                        @if($owKey)
                            <div class="text-muted small text-center py-2">
                                Selecciona una zona y haz clic en "Verificar zona" para ver el clima.
                            </div>
                        @else
                            <div class="text-muted small text-center py-2">
                                Configura la API key para habilitar la verificación por zona.
                            </div>
                        @endif
                    </div>

                    <div class="px-3 pb-3">
                        <button id="check-zone-weather" class="btn btn-sm btn--primary w-100"
                                {{ $owKey ? '' : 'disabled' }}>
                            🌍 Verificar clima de la zona seleccionada
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cómo funciona --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header border-0">
            <h5 class="card-title mb-0">📖 Automatización del multiplicador de lluvia</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light h-100">
                        <div class="fw-semibold mb-1">1. API key configurada</div>
                        <small class="text-muted">Regístrate gratis en openweathermap.org y pega tu key arriba.</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light h-100">
                        <div class="fw-semibold mb-1">2. Scheduler cada 15 min</div>
                        <small class="text-muted">El cron de Laravel verifica el clima de la ubicación principal automáticamente.</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light h-100">
                        <div class="fw-semibold mb-1">3. Lluvia → ×1.4</div>
                        <small class="text-muted">Si detecta lluvia, tormenta o nieve activa el multiplicador por 1 hora.</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light h-100">
                        <div class="fw-semibold mb-1">4. Cron requerido</div>
                        <small class="text-muted"><code>* * * * * php artisan schedule:run</code></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ $mapKey }}&callback=initWeatherMap&libraries=marker&v=3.61">
</script>
<script>
var weatherMap, weatherMarker;
var defaultLat = {{ $defaultLoc['lat'] ?? 14.0818 }};
var defaultLng = {{ $defaultLoc['lng'] ?? -87.2068 }};

function initWeatherMap() {
    weatherMap = new google.maps.Map(document.getElementById('weather-map'), {
        center: { lat: defaultLat, lng: defaultLng },
        zoom: 12,
        mapTypeId: 'roadmap',
        mapId: '{{ $mapKey }}',
        disableDefaultUI: false,
        zoomControl: true,
    });

    // Marcador de ubicación actual
    weatherMarker = new google.maps.Marker({
        position: { lat: defaultLat, lng: defaultLng },
        map: weatherMap,
        title: 'Ubicación principal',
        icon: {
            url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
        }
    });

    // Al cambiar zona, mover el mapa
    document.getElementById('zone-selector').addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        var lat = parseFloat(opt.dataset.lat) || defaultLat;
        var lng = parseFloat(opt.dataset.lng) || defaultLng;
        weatherMap.panTo({ lat: lat, lng: lng });
        weatherMarker.setPosition({ lat: lat, lng: lng });
    });
}

// Verificar clima de zona seleccionada
document.getElementById('check-zone-weather').addEventListener('click', function() {
    var btn = this;
    var sel = document.getElementById('zone-selector');
    var zoneId   = sel.value;
    var zoneName = sel.options[sel.selectedIndex].text;
    var opt = sel.options[sel.selectedIndex];
    var lat = parseFloat(opt.dataset.lat) || defaultLat;
    var lng = parseFloat(opt.dataset.lng) || defaultLng;

    btn.disabled = true;
    btn.textContent = '⏳ Verificando...';

    fetch('{{ route("admin.business-settings.third-party.weather-api-zone-check") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({ zone_id: zoneId, lat: lat, lng: lng })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = '🌍 Verificar clima de la zona seleccionada';

        var result = document.getElementById('zone-weather-result');
        if (data.success) {
            var icon = data.is_raining ? '🌧️' : '☀️';
            var badge = data.is_raining
                ? '<span class="badge badge-soft-primary ml-2">×1.4 activo</span>'
                : '<span class="badge badge-soft-success ml-2">Sin lluvia</span>';
            result.innerHTML = `
                <div class="d-flex align-items-center gap-3 p-2 rounded ${data.is_raining ? 'bg-soft-info' : 'bg-light'}">
                    <span style="font-size:1.8rem;">${icon}</span>
                    <div>
                        <div class="fw-bold">${zoneName} — ${Math.round(data.temp)}°C ${badge}</div>
                        <div class="small text-muted">${data.description} · Humedad ${data.humidity}%</div>
                    </div>
                </div>`;

            // Actualizar marcador en el mapa
            if (weatherMarker) {
                weatherMarker.setPosition({ lat: lat, lng: lng });
                weatherMap.panTo({ lat: lat, lng: lng });
            }
        } else {
            // Mensaje de error con instrucciones para 401
            var msg = data.message || 'Error al verificar';
            var extra = '';
            if (msg.includes('401')) {
                extra = '<br><small>⏳ <strong>Las keys nuevas tardan hasta 2 horas en activarse.</strong> Verifica que la copiaste correctamente desde <a href="https://home.openweathermap.org/api_keys" target="_blank">openweathermap.org/api_keys</a></small>';
            }
            result.innerHTML = `<div class="alert alert-warning py-2 mb-0 small">${msg}${extra}</div>`;
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = '🌍 Verificar clima de la zona seleccionada';
        toastr.error('Error de conexión');
    });
});

// Verificar clima ahora (ubicación principal)
var checkNowBtn = document.getElementById('check-weather-now');
if (checkNowBtn) {
    checkNowBtn.addEventListener('click', function() {
        this.disabled = true;
        this.textContent = '⏳';
        fetch('{{ route("admin.zarpya.pricing.rain.check-weather") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                toastr.error(data.message || 'Error');
                this.disabled = false;
                this.textContent = '🔄 Verificar ahora';
            }
        })
        .catch(() => {
            toastr.error('Error de conexión');
            this.disabled = false;
            this.textContent = '🔄 Verificar ahora';
        });
    });
}
</script>
@endpush
