@extends('layouts.admin.app')
@section('title', 'Multiplicadores Dinámicos')

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.multiplier-card { border-radius: 12px; border: none; transition: transform .12s; }
.multiplier-card:hover { transform: translateY(-2px); }
.multiplier-badge { font-size: 1.8rem; font-weight: 900; line-height: 1; }
.rule-row-rain     { border-left: 4px solid #0ea5e9; }
.rule-row-night    { border-left: 4px solid #6366f1; }
.rule-row-rush     { border-left: 4px solid #f59e0b; }
.rule-row-demand   { border-left: 4px solid #ef4444; }
.rule-row-weekend  { border-left: 4px solid #10b981; }
.rule-row-other    { border-left: 4px solid #6b7280; }
.weather-widget { border-radius: 12px; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff; }
.rain-active    { background: linear-gradient(135deg, #f59e0b, #d97706) !important; }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-header-title mb-0">⚡ Multiplicadores de Precio Dinámico</h1>
                <p class="page-header-text m-0 text-muted">
                    El sistema aplica automáticamente el multiplicador de mayor prioridad activo en cada momento.
                </p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.business-settings.third-party.weather-api') }}"
                   class="btn btn-sm btn-outline-secondary">
                    ⚙️ Configurar API clima
                </a>
            </div>
        </div>
    </div>

    {{-- ── Resumen visual de los 5 multiplicadores ─────────────── --}}
    @php
        $ruleMap = $rules->keyBy('rule_type');
        $weatherData = null;
        $lastCheck = \App\Models\BusinessSetting::where('key','weather_last_check')->first();
        if ($lastCheck?->value) $weatherData = json_decode($lastCheck->value, true);
        $apiKey = \App\Models\BusinessSetting::where('key','openweather_api_key')->first()?->value;
    @endphp

    <div class="row g-3 mb-4">

        {{-- Lluvia --}}
        @php $rain = $ruleMap['rain'] ?? null @endphp
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card multiplier-card text-center py-3 h-100 {{ $rainActive ? 'border-warning' : '' }}"
                 style="{{ $rainActive ? 'background:#fff8e1;' : '' }}">
                <div style="font-size:1.8rem;">🌧️</div>
                <div class="multiplier-badge text-primary mt-1">×{{ $rain?->multiplier ?? 1.4 }}</div>
                <div class="small fw-semibold mt-1">Lluvia</div>
                <div class="small text-muted">Manual / API</div>
                @if($rainActive)
                    <span class="badge badge-warning mt-1">ACTIVO</span>
                @endif
            </div>
        </div>

        {{-- Hora pico almuerzo --}}
        @php $rush = $ruleMap['rush_hour'] ?? null @endphp
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card multiplier-card text-center py-3 h-100">
                <div style="font-size:1.8rem;">🍽️</div>
                <div class="multiplier-badge text-warning mt-1">×{{ $rush?->multiplier ?? 1.3 }}</div>
                <div class="small fw-semibold mt-1">Hora pico</div>
                <div class="small text-muted">12–1pm / 7–9pm</div>
                @if($rush?->isActive())
                    <span class="badge badge-soft-warning mt-1">ACTIVO</span>
                @endif
            </div>
        </div>

        {{-- Noche --}}
        @php $night = $ruleMap['night'] ?? null @endphp
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card multiplier-card text-center py-3 h-100">
                <div style="font-size:1.8rem;">🌙</div>
                <div class="multiplier-badge text-indigo mt-1" style="color:#6366f1;">×{{ $night?->multiplier ?? 1.25 }}</div>
                <div class="small fw-semibold mt-1">Noche</div>
                <div class="small text-muted">9pm–12am</div>
                @if($night?->isActive())
                    <span class="badge badge-soft-primary mt-1">ACTIVO</span>
                @endif
            </div>
        </div>

        {{-- Alta demanda --}}
        @php $demand = $ruleMap['high_demand'] ?? null @endphp
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card multiplier-card text-center py-3 h-100">
                <div style="font-size:1.8rem;">🔥</div>
                <div class="multiplier-badge text-danger mt-1">
                    ×{{ $demand?->multiplier_min ?? 1.2 }}–{{ $demand?->multiplier_max ?? 1.5 }}
                </div>
                <div class="small fw-semibold mt-1">Alta demanda</div>
                <div class="small text-muted">Automático</div>
            </div>
        </div>

        {{-- Fin de semana --}}
        @php $weekend = $ruleMap['weekend'] ?? null @endphp
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card multiplier-card text-center py-3 h-100">
                <div style="font-size:1.8rem;">📅</div>
                <div class="multiplier-badge text-success mt-1">×{{ $weekend?->multiplier ?? 1.1 }}</div>
                <div class="small fw-semibold mt-1">Fin de semana</div>
                <div class="small text-muted">Sáb y Dom</div>
                @if($weekend?->isActive())
                    <span class="badge badge-soft-success mt-1">ACTIVO</span>
                @endif
            </div>
        </div>

        {{-- Widget clima --}}
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card multiplier-card {{ $rainActive ? 'rain-active' : 'weather-widget' }} text-white text-center py-3 h-100">
                @if($weatherData)
                    <div style="font-size:1.8rem;">
                        {{ $weatherData['is_raining'] ? '🌧️' : '☀️' }}
                    </div>
                    <div class="fw-bold mt-1">{{ $weatherData['temp'] ? round($weatherData['temp']) . '°C' : '—' }}</div>
                    <div class="small">{{ ucfirst($weatherData['description'] ?? '') }}</div>
                    <div class="small opacity-75">
                        {{ isset($weatherData['checked_at']) ? \Carbon\Carbon::parse($weatherData['checked_at'])->diffForHumans() : '' }}
                    </div>
                @else
                    <div style="font-size:1.8rem;">🌡️</div>
                    <div class="small mt-1">Sin datos</div>
                    <div class="small opacity-75">Configura API</div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">

        {{-- Control de lluvia --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">🌧️ Control de Lluvia</h5>
                </div>
                <div class="card-body">
                    {{-- Toggle manual --}}
                    <div class="p-3 rounded mb-3 {{ $rainActive ? 'bg-warning' : 'bg-light' }}" style="border-radius:10px!important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Activación manual</div>
                                <small class="text-muted">Se desactiva automáticamente en 2 horas</small>
                            </div>
                            <form action="{{ route('admin.zarpya.pricing.rain.toggle') }}" method="POST">
                                @csrf
                                <input type="hidden" name="active" value="{{ $rainActive ? 0 : 1 }}">
                                <button class="btn btn-sm btn-{{ $rainActive ? 'danger' : 'warning' }}">
                                    {{ $rainActive ? '🔴 Desactivar' : '🟢 Activar' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Estado API --}}
                    <div class="p-3 rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold small">Automatización via OpenWeatherMap</span>
                            @if($apiKey)
                                <span class="badge badge-soft-success">API configurada</span>
                            @else
                                <span class="badge badge-soft-danger">Sin API key</span>
                            @endif
                        </div>
                        @if($apiKey)
                            <div class="small text-muted mb-2">
                                El sistema verifica el clima cada <strong>15 minutos</strong> y activa/desactiva
                                el multiplicador de lluvia automáticamente.
                            </div>
                            @if($weatherData)
                            <div class="small">
                                <div>Última verificación: <strong>{{ \Carbon\Carbon::parse($weatherData['checked_at'])->format('d/m H:i') }}</strong></div>
                                <div>Condición: <strong>{{ ucfirst($weatherData['description'] ?? '—') }}</strong></div>
                                <div>Temperatura: <strong>{{ $weatherData['temp'] ? round($weatherData['temp']) . '°C' : '—' }}</strong></div>
                                <div>Humedad: <strong>{{ $weatherData['humidity'] ?? '—' }}%</strong></div>
                            </div>
                            @endif
                            <button class="btn btn-sm btn-outline-primary mt-2 w-100" id="check-weather-now">
                                🔄 Verificar ahora
                            </button>
                        @else
                            <div class="small text-muted mb-2">
                                Agrega tu API key de OpenWeatherMap para automatizar el multiplicador de lluvia.
                            </div>
                            <a href="{{ route('admin.business-settings.third-party.weather-api') }}"
                               class="btn btn-sm btn-outline-warning w-100">
                                ⚙️ Configurar API key
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla de reglas --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Reglas configuradas</h5>
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newRuleModal">
                        + Nueva regla
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Regla</th>
                                <th>Multiplicador</th>
                                <th>Horario</th>
                                <th>Días</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rules as $rule)
                            @php
                                $rowClass = match(true) {
                                    str_contains($rule->rule_type, 'rain')    => 'rule-row-rain',
                                    str_contains($rule->rule_type, 'night')   => 'rule-row-night',
                                    str_contains($rule->rule_type, 'rush')    => 'rule-row-rush',
                                    str_contains($rule->rule_type, 'demand')  => 'rule-row-demand',
                                    str_contains($rule->rule_type, 'weekend') => 'rule-row-weekend',
                                    default => 'rule-row-other',
                                };
                                $icon = match(true) {
                                    str_contains($rule->rule_type, 'rain')    => '🌧️',
                                    str_contains($rule->rule_type, 'night')   => '🌙',
                                    str_contains($rule->rule_type, 'rush')    => '⏰',
                                    str_contains($rule->rule_type, 'demand')  => '🔥',
                                    str_contains($rule->rule_type, 'weekend') => '📅',
                                    default => '⚡',
                                };
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>
                                    <span class="mr-1">{{ $icon }}</span>
                                    <span class="fw-semibold">{{ $rule->label }}</span>
                                    <br><small class="text-muted">{{ $rule->rule_type }}</small>
                                </td>
                                <td>
                                    @if($rule->multiplier_min && $rule->multiplier_max)
                                        <span class="badge badge-soft-danger">×{{ $rule->multiplier_min }}–{{ $rule->multiplier_max }}</span>
                                    @else
                                        <span class="badge badge-{{ $rule->multiplier >= 1.3 ? 'danger' : ($rule->multiplier >= 1.2 ? 'warning' : 'info') }}">
                                            ×{{ $rule->multiplier }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($rule->time_start && $rule->time_end)
                                        <span class="small">{{ substr($rule->time_start,0,5) }} – {{ substr($rule->time_end,0,5) }}</span>
                                    @else
                                        <span class="text-muted small">Todo el día</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rule->days_of_week)
                                        @php $dn = ['D','L','M','X','J','V','S'] @endphp
                                        @foreach($rule->days_of_week as $d)
                                            <span class="badge badge-soft-secondary">{{ $dn[$d] ?? $d }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">Todos</span>
                                    @endif
                                </td>
                                <td><span class="badge badge-soft-dark">{{ $rule->priority }}</span></td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm mb-0">
                                        <input type="checkbox" class="toggle-switch-input status-toggle"
                                            data-url="{{ route('admin.zarpya.pricing.rule.status') }}"
                                            data-id="{{ $rule->id }}"
                                            {{ $rule->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                    </label>
                                </td>
                                <td>
                                    <form action="{{ route('admin.zarpya.pricing.rule.destroy', $rule->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar esta regla?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-outline-danger"><i class="tio-delete"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Cómo funciona --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header border-0">
            <h5 class="card-title mb-0">📖 Cómo funciona el sistema</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light h-100">
                        <div class="fw-semibold mb-1">🌧️ Lluvia ×1.4</div>
                        <small class="text-muted">Se activa manualmente o automáticamente via OpenWeatherMap cada 15 min. TTL: 2 horas.</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light h-100">
                        <div class="fw-semibold mb-1">⏰ Hora pico ×1.3</div>
                        <small class="text-muted">Almuerzo 12–1pm y cena 7–9pm. Se activa automáticamente por horario.</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light h-100">
                        <div class="fw-semibold mb-1">🔥 Alta demanda ×1.2–1.5</div>
                        <small class="text-muted">Escala según pedidos activos en la zona. Se actualiza cada 5 min via Redis.</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light h-100">
                        <div class="fw-semibold mb-1">⚡ Prioridad</div>
                        <small class="text-muted">Si hay varios activos simultáneamente, gana el de mayor multiplicador. No se suman.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal nueva regla --}}
<div class="modal fade" id="newRuleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.zarpya.pricing.rule.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nueva regla de precio dinámico</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label">Tipo (slug único)</label>
                                <input type="text" name="rule_type" class="form-control" placeholder="rush_hour_2" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label">Etiqueta</label>
                                <input type="text" name="label" class="form-control" placeholder="Hora Pico Extra" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="input-label">Multiplicador</label>
                                <input type="number" name="multiplier" class="form-control" value="1.10" step="0.01" min="1.01" max="3.0" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="input-label">Hora inicio</label>
                                <input type="time" name="time_start" class="form-control">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="input-label">Hora fin</label>
                                <input type="time" name="time_end" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="input-label">Días de la semana (vacío = todos)</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'] as $i => $day)
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" name="days_of_week[]" value="{{ $i }}"
                                            class="custom-control-input" id="nd_{{ $i }}">
                                        <label class="custom-control-label" for="nd_{{ $i }}">{{ $day }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label">Prioridad (mayor = gana)</label>
                                <input type="number" name="priority" class="form-control" value="5" min="0" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear regla</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
// Toggle de estado
document.querySelectorAll('.status-toggle').forEach(toggle => {
    toggle.addEventListener('change', function () {
        fetch(this.dataset.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({ id: this.dataset.id, status: this.checked ? 1 : 0 })
        }).then(r => r.ok ? location.reload() : alert('Error al actualizar'));
    });
});

// Verificar clima ahora
const checkBtn = document.getElementById('check-weather-now');
if (checkBtn) {
    checkBtn.addEventListener('click', function () {
        this.disabled = true;
        this.textContent = '⏳ Verificando...';
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
                toastr.error(data.message || 'Error al verificar clima');
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
