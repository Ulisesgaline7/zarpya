@extends('layouts.admin.app')
@section('title', 'Ranking Zarperos')

@push('css_or_js')
<style>
.rank-1 { background: linear-gradient(135deg, #ffd700, #ffec6e); }
.rank-2 { background: linear-gradient(135deg, #c0c0c0, #e8e8e8); }
.rank-3 { background: linear-gradient(135deg, #cd7f32, #e8a96e); }
.level-badge-standard { background:#28a74520; color:#28a745; border:1px solid #28a745; }
.level-badge-pro      { background:#007bff20; color:#007bff; border:1px solid #007bff; }
.level-badge-elite    { background:#6f42c120; color:#6f42c1; border:1px solid #6f42c1; }
.achievement-chip { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:20px; font-size:11px; background:#f8f9fa; border:1px solid #dee2e6; }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">🏆</span>
            <span>Ranking Semanal Zarperos</span>
        </h1>
        <div class="ml-auto d-flex gap-2">
            <a href="{{ route('admin.zarpya.pricing.levels') }}" class="btn btn-sm btn-outline-secondary">
                <i class="tio-user-outlined"></i> Niveles
            </a>
        </div>
    </div>

    {{-- Podio Top 3 --}}
    @if($topDrivers->count() >= 3)
    <div class="row justify-content-center mb-4">
        {{-- 2do lugar --}}
        <div class="col-md-3 text-center mt-4">
            <div class="card rank-2 border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="mb-2" style="font-size:2rem;">🥈</div>
                    <img src="{{ $topDrivers[1]->image_full_url }}" class="rounded-circle mb-2"
                         width="60" height="60" style="object-fit:cover;"
                         onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
                    <h6 class="mb-0">{{ $topDrivers[1]->f_name }} {{ $topDrivers[1]->l_name }}</h6>
                    <small class="text-muted">{{ number_format($topDrivers[1]->stat?->xp ?? 0) }} XP</small>
                    <div class="mt-1">
                        <span class="badge level-badge-{{ $topDrivers[1]->level?->slug ?? 'standard' }}">
                            {{ $topDrivers[1]->level?->name ?? '🟢 Base' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        {{-- 1er lugar --}}
        <div class="col-md-3 text-center">
            <div class="card rank-1 border-0 shadow">
                <div class="card-body py-4">
                    <div class="mb-2" style="font-size:2.5rem;">🥇</div>
                    <img src="{{ $topDrivers[0]->image_full_url }}" class="rounded-circle mb-2"
                         width="70" height="70" style="object-fit:cover;"
                         onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
                    <h5 class="mb-0 fw-bold">{{ $topDrivers[0]->f_name }} {{ $topDrivers[0]->l_name }}</h5>
                    <small class="text-muted">{{ number_format($topDrivers[0]->stat?->xp ?? 0) }} XP</small>
                    <div class="mt-1">
                        <span class="badge level-badge-{{ $topDrivers[0]->level?->slug ?? 'standard' }}">
                            {{ $topDrivers[0]->level?->name ?? '🟢 Base' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        {{-- 3er lugar --}}
        <div class="col-md-3 text-center mt-4">
            <div class="card rank-3 border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="mb-2" style="font-size:2rem;">🥉</div>
                    <img src="{{ $topDrivers[2]->image_full_url }}" class="rounded-circle mb-2"
                         width="60" height="60" style="object-fit:cover;"
                         onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
                    <h6 class="mb-0">{{ $topDrivers[2]->f_name }} {{ $topDrivers[2]->l_name }}</h6>
                    <small class="text-muted">{{ number_format($topDrivers[2]->stat?->xp ?? 0) }} XP</small>
                    <div class="mt-1">
                        <span class="badge level-badge-{{ $topDrivers[2]->level?->slug ?? 'standard' }}">
                            {{ $topDrivers[2]->level?->name ?? '🟢 Base' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabla completa --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Tabla de Posiciones</h5>
            <span class="badge badge-soft-primary">{{ $allDrivers->total() }} Zarperos</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="thead-light">
                    <tr>
                        <th width="60">#</th>
                        <th>Zarpero</th>
                        <th>Nivel</th>
                        <th>XP</th>
                        <th>Entregas/mes</th>
                        <th>Calificación</th>
                        <th>Racha</th>
                        <th>Medallas</th>
                        <th>Bonos</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allDrivers as $i => $dm)
                    @php
                        $rank = $allDrivers->firstItem() + $i;
                        $rankIcon = match($rank) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => "#$rank" };
                        $levelSlug = $dm->level?->slug ?? 'standard';
                    @endphp
                    <tr>
                        <td class="text-center fw-bold">{{ $rankIcon }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $dm->image_full_url }}" class="rounded-circle"
                                     width="36" height="36" style="object-fit:cover;"
                                     onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
                                <div>
                                    <div class="fw-semibold">{{ $dm->f_name }} {{ $dm->l_name }}</div>
                                    <small class="text-muted">{{ $dm->phone }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge level-badge-{{ $levelSlug }} px-2 py-1">
                                {{ $dm->level?->name ?? '🟢 Zarpero Base' }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ number_format($dm->stat?->xp ?? 0) }}</div>
                            <div class="progress mt-1" style="height:4px;width:80px;">
                                <div class="progress-bar bg-warning" style="width:{{ ($dm->stat?->xp ?? 0) % 100 }}%;"></div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $dm->stat?->monthly_deliveries ?? 0 }}</span>
                        </td>
                        <td>
                            @php $r = $dm->rating->first()?->average ?? 0; @endphp
                            @if($r > 0)
                                <span class="text-warning fw-bold">★ {{ number_format($r, 1) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if(($dm->stat?->streak_days ?? 0) > 0)
                                <span class="text-danger fw-bold">🔥 {{ $dm->stat->streak_days }}d</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @php $achCount = $dm->achievements->count(); @endphp
                            @if($achCount > 0)
                                <span class="badge badge-soft-warning">🏅 {{ $achCount }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @php $pendingBonus = $dm->bonuses->where('paid', false)->sum('amount'); @endphp
                            @if($pendingBonus > 0)
                                <span class="text-success fw-bold">L {{ number_format($pendingBonus, 0) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.delivery-man.view', $dm->id) }}"
                               class="btn btn-xs btn-outline-primary">Ver perfil</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $allDrivers->links() }}</div>
    </div>
</div>
@endsection
