@extends('layouts.admin.app')

@section('title', 'Sistema de Anuncios')

@push('css_or_js')
<style>
.stat-card { border-radius: 10px; border: none; transition: transform .12s; }
.stat-card:hover { transform: translateY(-2px); }
.stat-num  { font-size: 2rem; font-weight: 800; line-height: 1; }
.status-dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; }
.credit-row:hover { background: #f8f9fa; }
.tariff-box { background: #f8f9fa; border-radius: 10px; padding: 16px 20px; }
.flow-step { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
.flow-step:last-child { border-bottom: none; }
.flow-num { width: 28px; height: 28px; border-radius: 50%; background: #005555; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    {{-- Encabezado --}}
    <div class="page-header">
        <h1 class="page-header-title mr-3">
            <span class="page-header-icon">
                <img src="{{ asset('public/assets/admin/img/business.png') }}" class="w--26" alt="">
            </span>
            <span>Configuración del Negocio</span>
        </h1>
        @include('admin-views.business-settings.partials.nav-menu')
    </div>

    {{-- ── Estadísticas de anuncios ─────────────────────────────── --}}
    @php
        $totalAds    = \App\Models\Advertisement::count();
        $pendingAds  = \App\Models\Advertisement::where('status', 'pending')->count();
        $runningAds  = \App\Models\Advertisement::valid()->count();
        $expiredAds  = \App\Models\Advertisement::expired()->count();
        $pausedAds   = \App\Models\Advertisement::where('status', 'paused')->count();
        $deniedAds   = \App\Models\Advertisement::where('status', 'denied')->count();

        $totalCredits = \App\Models\RestaurantCredit::sum('amount');
        $storesWithCredits = \App\Models\RestaurantCredit::where('amount', '>', 0)->count();

        $recentTransactions = \App\Models\CreditTransaction::with('store')
            ->latest()->take(8)->get();

        $storeCredits = \App\Models\RestaurantCredit::with('store')
            ->orderByDesc('amount')->take(10)->get();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card stat-card text-center py-3 shadow-sm">
                <div class="stat-num text-dark">{{ $totalAds }}</div>
                <small class="text-muted">Total anuncios</small>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card text-center py-3 shadow-sm" style="border-top:3px solid #ffc107;">
                <div class="stat-num text-warning">{{ $pendingAds }}</div>
                <small class="text-muted">Pendientes</small>
                @if($pendingAds > 0)
                <div class="mt-1">
                    <a href="{{ route('admin.advertisement.requestList') }}" class="badge badge-soft-warning" style="font-size:11px;">Revisar →</a>
                </div>
                @endif
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card text-center py-3 shadow-sm" style="border-top:3px solid #28a745;">
                <div class="stat-num text-success">{{ $runningAds }}</div>
                <small class="text-muted">Activos ahora</small>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card text-center py-3 shadow-sm" style="border-top:3px solid #6c757d;">
                <div class="stat-num text-muted">{{ $expiredAds }}</div>
                <small class="text-muted">Expirados</small>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card text-center py-3 shadow-sm" style="border-top:3px solid #17a2b8;">
                <div class="stat-num text-info">{{ number_format($totalCredits, 0) }}</div>
                <small class="text-muted">Créditos en sistema</small>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card text-center py-3 shadow-sm" style="border-top:3px solid #005555;">
                <div class="stat-num" style="color:#005555;">{{ $storesWithCredits }}</div>
                <small class="text-muted">Negocios con créditos</small>
            </div>
        </div>
    </div>

    {{-- ── Accesos rápidos ─────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted small fw-semibold mr-2">Gestión de anuncios:</span>
                        <a href="{{ route('admin.advertisement.index') }}" class="btn btn-sm btn-outline-primary">
                            📋 Ver todos los anuncios
                        </a>
                        @if($pendingAds > 0)
                        <a href="{{ route('admin.advertisement.requestList') }}" class="btn btn-sm btn-warning">
                            ⏳ Solicitudes pendientes
                            <span class="badge badge-light ml-1">{{ $pendingAds }}</span>
                        </a>
                        @endif
                        <a href="{{ route('admin.advertisement.create') }}" class="btn btn-sm btn-success">
                            ➕ Crear anuncio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">

        {{-- ── Configuración de tarifas ─────────────────────────── --}}
        <div class="col-lg-5">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">💰 Tarifas de Anuncios</h5>
                    <small class="text-muted">Costo que se descuenta de los créditos del negocio</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.business-settings.update-ad-surge') }}" method="POST">
                        @csrf
                        <div class="tariff-box mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold">👁️ Costo por impresión</span>
                                <small class="text-muted">Cada vez que se muestra el anuncio</small>
                            </div>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text">L</span></div>
                                <input type="number" name="ad_cost_impression" step="0.01" min="0"
                                       class="form-control"
                                       value="{{ $data['ad_cost_impression'] ?? '0.10' }}">
                                <div class="input-group-append"><span class="input-group-text">/impresión</span></div>
                            </div>
                        </div>

                        <div class="tariff-box mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold">🖱️ Costo por clic</span>
                                <small class="text-muted">Cada vez que el cliente hace clic</small>
                            </div>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text">L</span></div>
                                <input type="number" name="ad_cost_click" step="0.01" min="0"
                                       class="form-control"
                                       value="{{ $data['ad_cost_click'] ?? '0.50' }}">
                                <div class="input-group-append"><span class="input-group-text">/clic</span></div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-semibold mb-3">⚡ Multiplicadores de precio dinámico</h6>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="small fw-semibold">🌧️ Por clima</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="surge_multiplier_weather" step="0.1" min="1" max="5"
                                           class="form-control"
                                           value="{{ $data['surge_multiplier_weather'] ?? '1.50' }}">
                                    <div class="input-group-append"><span class="input-group-text">×</span></div>
                                </div>
                                <small class="text-muted">Ej: 1.5 = +50% en lluvia</small>
                            </div>
                            <div class="col-6">
                                <label class="small fw-semibold">🕐 Hora pico</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="surge_multiplier_peak_hour" step="0.1" min="1" max="5"
                                           class="form-control"
                                           value="{{ $data['surge_multiplier_peak_hour'] ?? '1.20' }}">
                                    <div class="input-group-append"><span class="input-group-text">×</span></div>
                                </div>
                                <small class="text-muted">Ej: 1.2 = +20% en pico</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn--primary btn-block">Guardar configuración</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Créditos por negocio ─────────────────────────────── --}}
        <div class="col-lg-7">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">🏪 Créditos por Negocio</h5>
                        <small class="text-muted">Saldo disponible para mostrar anuncios</small>
                    </div>
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addCreditsModal">
                        ➕ Agregar créditos
                    </button>
                </div>
                <div class="card-body p-0">
                    @if($storeCredits->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <div style="font-size:2.5rem;">💳</div>
                        <p class="mt-2 mb-0">Ningún negocio tiene créditos aún.</p>
                        <small>Agrega créditos para que puedan publicar anuncios.</small>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Negocio</th>
                                    <th class="text-right">Créditos</th>
                                    <th class="text-center">Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($storeCredits as $credit)
                                <tr class="credit-row">
                                    <td>
                                        <div class="fw-semibold">{{ $credit->store?->name ?? 'Negocio #'.$credit->store_id }}</div>
                                        <small class="text-muted">ID {{ $credit->store_id }}</small>
                                    </td>
                                    <td class="text-right">
                                        <span class="fw-bold {{ $credit->amount > 0 ? 'text-success' : 'text-danger' }}">
                                            L {{ number_format($credit->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($credit->amount > 50)
                                            <span class="badge badge-soft-success">Activo</span>
                                        @elseif($credit->amount > 0)
                                            <span class="badge badge-soft-warning">Bajo</span>
                                        @else
                                            <span class="badge badge-soft-danger">Sin créditos</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-xs btn-outline-primary add-credit-btn"
                                                data-store-id="{{ $credit->store_id }}"
                                                data-store-name="{{ $credit->store?->name }}"
                                                data-toggle="modal" data-target="#addCreditsModal">
                                            + Agregar
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Cómo funciona + Transacciones recientes ─────────────── --}}
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">📖 Cómo funciona el sistema</h5>
                </div>
                <div class="card-body">
                    <div class="flow-step">
                        <div class="flow-num">1</div>
                        <div>
                            <div class="fw-semibold small">El negocio crea un anuncio</div>
                            <div class="text-muted" style="font-size:12px;">Desde su panel de vendor, elige tipo (video o tienda), fechas y contenido.</div>
                        </div>
                    </div>
                    <div class="flow-step">
                        <div class="flow-num">2</div>
                        <div>
                            <div class="fw-semibold small">Admin aprueba o rechaza</div>
                            <div class="text-muted" style="font-size:12px;">El anuncio queda en estado "pendiente" hasta que el admin lo revise.</div>
                        </div>
                    </div>
                    <div class="flow-step">
                        <div class="flow-num">3</div>
                        <div>
                            <div class="fw-semibold small">Se muestra en la app</div>
                            <div class="text-muted" style="font-size:12px;">Solo se muestran anuncios de negocios con créditos disponibles (saldo > 0).</div>
                        </div>
                    </div>
                    <div class="flow-step">
                        <div class="flow-num">4</div>
                        <div>
                            <div class="fw-semibold small">Se descuentan créditos</div>
                            <div class="text-muted" style="font-size:12px;">
                                Cada impresión: <strong>L {{ $data['ad_cost_impression'] ?? '0.10' }}</strong><br>
                                Cada clic: <strong>L {{ $data['ad_cost_click'] ?? '0.50' }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="flow-step">
                        <div class="flow-num">5</div>
                        <div>
                            <div class="fw-semibold small">Sin créditos = sin anuncio</div>
                            <div class="text-muted" style="font-size:12px;">Cuando el saldo llega a 0, el anuncio deja de mostrarse automáticamente.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">🔄 Últimas transacciones de créditos</h5>
                </div>
                <div class="card-body p-0">
                    @if($recentTransactions->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <div style="font-size:2rem;">📭</div>
                        <p class="mt-2 mb-0">Sin transacciones aún.</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Negocio</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                    <th>Referencia</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $tx)
                                <tr>
                                    <td>{{ $tx->store?->name ?? 'Negocio #'.$tx->store_id }}</td>
                                    <td>
                                        @if($tx->type === 'add')
                                            <span class="badge badge-soft-success">+ Recarga</span>
                                        @else
                                            <span class="badge badge-soft-danger">− Consumo</span>
                                        @endif
                                    </td>
                                    <td class="{{ $tx->type === 'add' ? 'text-success' : 'text-danger' }} fw-semibold">
                                        {{ $tx->type === 'add' ? '+' : '-' }} L {{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td><small class="text-muted">{{ $tx->reference ?? '—' }}</small></td>
                                    <td><small class="text-muted">{{ $tx->created_at->format('d/m H:i') }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Modal agregar créditos ──────────────────────────────────── --}}
<div class="modal fade" id="addCreditsModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="{{ route('admin.ads.credits.add') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Agregar créditos</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="input-label">Negocio</label>
                        <select name="store_id" id="modal-store-select" class="form-control" required>
                            <option value="">Seleccionar negocio...</option>
                            @foreach(\App\Models\Store::orderBy('name')->get() as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="input-label">Monto a agregar (L)</label>
                        <input type="number" name="amount" class="form-control" min="1" step="0.01"
                               placeholder="Ej: 500.00" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label">Descripción (opcional)</label>
                        <input type="text" name="description" class="form-control"
                               placeholder="Ej: Recarga manual por admin">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm">Agregar créditos</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
// Pre-seleccionar negocio al abrir modal desde botón de fila
document.querySelectorAll('.add-credit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const storeId = this.dataset.storeId;
        document.getElementById('modal-store-select').value = storeId;
    });
});
</script>
@endpush
