@extends('layouts.admin.app')
@section('title', 'Planes Fundadores — Zarpya Negocios')

@push('css_or_js')
<style>
/* ── Variables ─────────────────────────────────────────────── */
:root {
    --pionero: #F59E0B;
    --elite:   #7C3AED;
    --boost:   #0EA5E9;
    --std:     #6B7280;
}

/* ── Plan cards ────────────────────────────────────────────── */
.plan-card { border-radius: 16px; border: none; overflow: hidden; transition: transform .15s, box-shadow .15s; }
.plan-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.12); }

.plan-card .plan-top { padding: 28px 24px 20px; color: #fff; position: relative; }
.plan-card .plan-emoji { font-size: 2.4rem; line-height: 1; }
.plan-card .plan-name  { font-size: 1.4rem; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; margin: 6px 0 2px; }
.plan-card .plan-price { font-size: 2.2rem; font-weight: 900; line-height: 1; }
.plan-card .plan-price-sub { font-size: 12px; opacity: .8; }
.plan-card .plan-commission { font-size: 1.1rem; font-weight: 700; margin-top: 8px; }

.plan-card.pionero .plan-top { background: linear-gradient(135deg, #F59E0B, #D97706); }
.plan-card.elite   .plan-top { background: linear-gradient(135deg, #7C3AED, #5B21B6); }
.plan-card.boost   .plan-top { background: linear-gradient(135deg, #0EA5E9, #0284C7); }
.plan-card.standard .plan-top { background: linear-gradient(135deg, #6B7280, #4B5563); }

/* Slots progress */
.slots-bar { height: 8px; border-radius: 8px; background: #e9ecef; overflow: hidden; }
.slots-bar .fill { height: 100%; border-radius: 8px; transition: width .4s; }
.pionero .fill  { background: var(--pionero); }
.elite   .fill  { background: var(--elite); }
.boost   .fill  { background: var(--boost); }
.standard .fill { background: var(--std); }

/* Badge pill */
.founder-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
    border: 2px solid currentColor;
}

/* Feature list */
.feature-row { display: flex; align-items: center; gap: 8px; padding: 7px 0; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
.feature-row:last-child { border-bottom: none; }
.feature-row .icon { width: 20px; text-align: center; flex-shrink: 0; }
.check-yes { color: #22c55e; font-weight: 700; }
.check-no  { color: #d1d5db; }

/* Comparativa table */
.compare-table th { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; }
.compare-table td { font-size: 13px; vertical-align: middle; }
.badge-pionero  { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.badge-elite    { background: #ede9fe; color: #4c1d95; border: 1px solid #c4b5fd; }
.badge-boost    { background: #e0f2fe; color: #0c4a6e; border: 1px solid #7dd3fc; }
.badge-standard { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }

/* Slots cerrados */
.slots-closed { opacity: .6; }
.slots-closed .plan-top { filter: grayscale(.4); }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    {{-- Encabezado --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-header-title mb-0">🏗️ Planes Fundadores — Negocios Zarpya</h1>
                <p class="page-header-text m-0 text-muted">
                    Los primeros <strong>150 negocios</strong> entran con condiciones preferenciales para siempre.
                    Los planes fundadores cierran al completar cupos o al marcar inicio de operaciones.
                </p>
            </div>
            <div class="col-auto d-flex gap-2">
                <a href="{{ route('admin.business-settings.subscriptionackage.index') }}"
                   class="btn btn-sm btn-outline-secondary">
                    Ver todos los paquetes
                </a>
                <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#closeFounderModal">
                    🔒 Cerrar planes fundadores
                </button>
            </div>
        </div>
    </div>

    {{-- ── Resumen global ──────────────────────────────────────── --}}
    @php
        $totalSlots    = $plans->whereNotNull('max_slots')->sum('max_slots');
        $totalUsed     = $plans->whereNotNull('max_slots')->sum('used_slots');
        $totalRemaining = $totalSlots - $totalUsed;
        $globalPct     = $totalSlots > 0 ? round($totalUsed / $totalSlots * 100) : 0;
        $founderStores = \App\Models\Store::whereNotNull('founder_plan')->where('founder_active', true)->count();
    @endphp

    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg,#005555,#007777); color:#fff; border-radius:16px;">
        <div class="card-body py-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="font-size:2.5rem;">🚀</div>
                        <div>
                            <h3 class="mb-0 text-white fw-bold">Negocios Fundadores · Tegucigalpa 2026</h3>
                            <p class="mb-0 opacity-75 small">Más pedidos. Menos comisión.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-4">
                        <div>
                            <div class="h2 fw-bold text-white mb-0">{{ $totalUsed }}</div>
                            <small class="opacity-75">cupos ocupados</small>
                        </div>
                        <div>
                            <div class="h2 fw-bold text-white mb-0">{{ $totalRemaining }}</div>
                            <small class="opacity-75">cupos disponibles</small>
                        </div>
                        <div>
                            <div class="h2 fw-bold text-white mb-0">{{ $founderStores }}</div>
                            <small class="opacity-75">negocios activos</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small opacity-75">Ocupación total</span>
                        <span class="small fw-bold">{{ $globalPct }}%</span>
                    </div>
                    <div class="slots-bar" style="height:12px; background:rgba(255,255,255,.2);">
                        <div class="fill" style="width:{{ $globalPct }}%; background:#fff; border-radius:8px;"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="opacity-75">0</small>
                        <small class="opacity-75">{{ $totalSlots }} cupos totales</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Las 4 tarjetas de plan ───────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @foreach($plans as $plan)
        @php
            $typeClass = $plan->founder_type ?? 'standard';
            $pct = $plan->max_slots ? min(100, round($plan->used_slots / $plan->max_slots * 100)) : 0;
            $remaining = $plan->max_slots ? $plan->max_slots - $plan->used_slots : null;
            $isClosed = $plan->max_slots && $plan->used_slots >= $plan->max_slots;
        @endphp
        <div class="col-lg-3 col-md-6">
            <div class="card plan-card {{ $typeClass }} {{ $isClosed ? 'slots-closed' : '' }} h-100">
                <div class="plan-top">
                    <div class="plan-emoji">
                        @if($typeClass === 'pionero') ⭐
                        @elseif($typeClass === 'elite') 🏆
                        @elseif($typeClass === 'boost') 🚀
                        @else 📋
                        @endif
                    </div>
                    <div class="plan-name">{{ strtoupper(str_replace(['⭐ ','🏆 ','🚀 '], '', $plan->package_name)) }}</div>
                    <div class="plan-price">
                        @if($plan->price == 0) Gratis
                        @else L {{ number_format($plan->price, 0) }}
                        @endif
                    </div>
                    <div class="plan-price-sub">
                        @if($plan->payment_type === 'one_time') pago único · sin renovaciones
                        @elseif($plan->payment_type === 'deposit') depósito reembolsable en {{ $plan->deposit_refund_months }} meses
                        @elseif($typeClass === 'standard') plan post-lanzamiento
                        @else sin costo de entrada
                        @endif
                    </div>
                    <div class="plan-commission">{{ $plan->commission_percent }}% comisión por pedido · para siempre</div>

                    @if($plan->badge_label)
                    <div class="mt-2">
                        <span style="background:rgba(255,255,255,.25); color:#fff; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:700; letter-spacing:1px;">
                            Badge {{ $plan->badge_label }}
                        </span>
                    </div>
                    @endif
                </div>

                <div class="card-body">
                    {{-- Cupos --}}
                    @if($plan->max_slots)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-semibold">Cupos</span>
                            <span class="small {{ $isClosed ? 'text-danger fw-bold' : 'text-muted' }}">
                                {{ $isClosed ? '¡LLENO!' : $remaining . ' disponibles' }}
                            </span>
                        </div>
                        <div class="slots-bar {{ $typeClass }}">
                            <div class="fill" style="width:{{ $pct }}%;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">{{ $plan->used_slots }} ocupados</small>
                            <small class="text-muted">{{ $plan->max_slots }} total</small>
                        </div>
                    </div>
                    @else
                    <div class="mb-3">
                        <span class="badge badge-soft-secondary">Cupos ilimitados</span>
                    </div>
                    @endif

                    {{-- Beneficios --}}
                    <div class="feature-row">
                        <span class="icon">💵</span>
                        <span>Comisión <strong>{{ $plan->commission_percent }}%</strong> permanente</span>
                    </div>
                    @if($plan->banner_days)
                    <div class="feature-row">
                        <span class="icon">🖼️</span>
                        <span>Banner destacado <strong>{{ $plan->banner_days }} días</strong></span>
                    </div>
                    @endif
                    @if($plan->promo_credits > 0)
                    <div class="feature-row">
                        <span class="icon">💳</span>
                        <span><strong>L {{ number_format($plan->promo_credits, 0) }}</strong> en créditos de app</span>
                    </div>
                    @endif
                    @if($plan->payment_type === 'deposit')
                    <div class="feature-row">
                        <span class="icon">🔄</span>
                        <span>Depósito reembolsable en <strong>{{ $plan->deposit_refund_months }} meses</strong></span>
                    </div>
                    @endif
                    @if($plan->vip_support)
                    <div class="feature-row">
                        <span class="icon">🛡️</span>
                        <span>Soporte <strong>VIP</strong> prioritario</span>
                    </div>
                    @endif
                    @if($plan->badge_label)
                    <div class="feature-row">
                        <span class="icon">🏷️</span>
                        <span>Badge <strong>{{ $plan->badge_label }}</strong> visible en la app</span>
                    </div>
                    @endif
                    @if($typeClass === 'standard')
                    <div class="feature-row">
                        <span class="icon check-no">✗</span>
                        <span class="text-muted">Sin beneficios especiales</span>
                    </div>
                    @endif
                </div>

                <div class="card-footer border-0 bg-transparent pb-3 px-3">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.business-settings.subscriptionackage.edit', $plan->id) }}"
                           class="btn btn-sm btn-outline-secondary flex-fill">Editar</a>
                        <form action="{{ route('admin.founder-plans.toggle-slots', $plan->id) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sm w-100 {{ $plan->slots_open ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                {{ $plan->slots_open ? '🔒 Cerrar' : '🔓 Abrir' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Tabla comparativa ───────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0">
            <h5 class="card-title mb-0">📊 Comparativa de Planes</h5>
        </div>
        <div class="table-responsive">
            <table class="table compare-table mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width:200px;">Beneficio</th>
                        <th class="text-center">⭐ Pionero</th>
                        <th class="text-center">🏆 Elite</th>
                        <th class="text-center">🚀 Boost</th>
                        <th class="text-center">Estándar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-semibold">Costo de entrada</td>
                        <td class="text-center text-success fw-bold">Gratis</td>
                        <td class="text-center fw-bold" style="color:#7C3AED;">L 1,500 único</td>
                        <td class="text-center fw-bold" style="color:#0EA5E9;">L 2,500 depósito</td>
                        <td class="text-center text-muted">Gratis</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Comisión por pedido</td>
                        <td class="text-center"><span class="badge badge-pionero px-2">14%</span></td>
                        <td class="text-center"><span class="badge badge-elite px-2">10%</span></td>
                        <td class="text-center"><span class="badge badge-boost px-2">12%</span></td>
                        <td class="text-center"><span class="badge badge-standard px-2">18%</span></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Comisión permanente</td>
                        <td class="text-center check-yes">✓</td>
                        <td class="text-center check-yes">✓</td>
                        <td class="text-center check-yes">✓</td>
                        <td class="text-center check-no">—</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Badge en la app</td>
                        <td class="text-center"><span class="badge badge-pionero">Pionero</span></td>
                        <td class="text-center"><span class="badge badge-elite">Elite</span></td>
                        <td class="text-center"><span class="badge badge-boost">Boost</span></td>
                        <td class="text-center check-no">—</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Banner pantalla inicio</td>
                        <td class="text-center check-no">—</td>
                        <td class="text-center check-yes">90 días</td>
                        <td class="text-center check-no">—</td>
                        <td class="text-center check-no">—</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Créditos de promoción</td>
                        <td class="text-center check-no">—</td>
                        <td class="text-center check-no">—</td>
                        <td class="text-center check-yes fw-bold" style="color:#0EA5E9;">L 3,750</td>
                        <td class="text-center check-no">—</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Depósito reembolsable</td>
                        <td class="text-center check-no">—</td>
                        <td class="text-center check-no">—</td>
                        <td class="text-center check-yes">en 12 meses</td>
                        <td class="text-center check-no">—</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Soporte prioritario</td>
                        <td class="text-center check-no">—</td>
                        <td class="text-center check-yes">VIP</td>
                        <td class="text-center check-no">—</td>
                        <td class="text-center check-no">—</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Cupos disponibles</td>
                        <td class="text-center fw-bold" style="color:#F59E0B;">100</td>
                        <td class="text-center fw-bold" style="color:#7C3AED;">30</td>
                        <td class="text-center fw-bold" style="color:#0EA5E9;">20</td>
                        <td class="text-center text-muted">Ilimitado</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer border-0 bg-light">
            <small class="text-muted">
                💡 <strong>Ejemplo real:</strong> Un pedido de L 300 con plan Pionero → Zarpya descuenta L 42 → el negocio recibe L 258.
                Con el plan Estándar descontaría L 54 por el mismo pedido.
            </small>
        </div>
    </div>

    {{-- ── Negocios fundadores activos ─────────────────────────── --}}
    @php
        $founderStoresList = \App\Models\Store::whereNotNull('founder_plan')
            ->where('founder_active', true)
            ->with('vendor')
            ->latest()
            ->take(20)
            ->get();
    @endphp
    @if($founderStoresList->count())
    <div class="card border-0 shadow-sm">
        <div class="card-header border-0">
            <h5 class="card-title mb-0">🏪 Negocios Fundadores Activos</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Negocio</th>
                        <th>Plan</th>
                        <th>Comisión</th>
                        <th>Zona</th>
                        <th>Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($founderStoresList as $store)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $store->name }}</div>
                            <small class="text-muted">{{ $store->vendor?->email }}</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $store->founder_plan }} px-2">
                                {{ ucfirst($store->founder_plan) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $planComm = ['pionero'=>14,'elite'=>10,'boost'=>12,'standard'=>18];
                                $comm = $planComm[$store->founder_plan] ?? $store->comission;
                            @endphp
                            <span class="fw-bold text-success">{{ $comm }}%</span>
                        </td>
                        <td><small>{{ $store->zone?->name ?? '—' }}</small></td>
                        <td><small class="text-muted">{{ $store->created_at->format('d/m/Y') }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- Modal cerrar todos los planes fundadores --}}
<div class="modal fade" id="closeFounderModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="{{ route('admin.founder-plans.close-all') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-danger">🔒 Cerrar planes fundadores</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-0">
                        Esto cerrará los cupos de <strong>todos</strong> los planes fundadores (Pionero, Elite, Boost).
                        Los negocios ya registrados conservan sus beneficios. Esta acción marca el inicio de operaciones.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger btn-sm">Confirmar cierre</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
