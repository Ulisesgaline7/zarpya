@extends('layouts.admin.app')

@section('title', \App\Models\BusinessSetting::where(['key'=>'business_name'])->first()->value ?? translate('Dashboard'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">

    
        @php
        $mod = \App\Models\Module::find(\Illuminate\Support\Facades\Config::get("module.current_module_id"));
        $total_providers     = DB::table('service_providers')->whereNull('deleted_at')->count();
        $active_providers    = DB::table('service_providers')->whereNull('deleted_at')->where('status', 'active')->count();
        $verified_providers  = DB::table('service_providers')->whereNull('deleted_at')->where('verified', 1)->count();
        $featured_providers  = DB::table('service_providers')->whereNull('deleted_at')->where('featured', 1)->count();
        $new_providers       = DB::table('service_providers')->whereNull('deleted_at')->where('created_at', '>=', now()->subDays(30))->count();

        $total_requests      = DB::table('service_requests')->count();
        $pending_requests    = DB::table('service_requests')->where('status', 'pending')->count();
        $accepted_requests   = DB::table('service_requests')->where('status', 'accepted')->count();
        $completed_requests  = DB::table('service_requests')->where('status', 'completed')->count();
        $canceled_requests   = DB::table('service_requests')->where('status', 'canceled')->count();
        $new_requests        = DB::table('service_requests')->where('created_at', '>=', now()->subDays(30))->count();

        $total_earnings      = DB::table('service_requests')->where('status', 'completed')->where('paid', 1)->sum('platform_fee');
        $total_categories    = DB::table('categories')->where('module_id', Config::get('module.current_module_id'))->count();

        // Solicitudes por mes (año actual)
        $requests_by_month = [];
        for ($i = 1; $i <= 12; $i++) {
            $requests_by_month[$i] = DB::table('service_requests')
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->count();
        }

        // Top proveedores por trabajos completados
        $top_providers = DB::table('service_providers')
            ->whereNull('deleted_at')
            ->orderByDesc('total_jobs')
            ->limit(5)
            ->get();

        // Solicitudes recientes
        $recent_requests = DB::table('service_requests')
            ->leftJoin('users', 'service_requests.customer_id', '=', 'users.id')
            ->leftJoin('service_providers', 'service_requests.provider_id', '=', 'service_providers.id')
            ->leftJoin('categories', 'service_requests.category_id', '=', 'categories.id')
            ->select(
                'service_requests.id',
                'service_requests.status',
                'service_requests.quoted_price',
                'service_requests.final_price',
                'service_requests.scheduled_at',
                'service_requests.created_at',
                'users.f_name as customer_name',
                'service_providers.business_name as provider_name',
                'categories.name as category_name'
            )
            ->orderByDesc('service_requests.created_at')
            ->limit(8)
            ->get();
    @endphp

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center py-2">
            <div class="col-sm mb-2 mb-sm-0">
                <div class="d-flex align-items-center">
                    @if($mod && $mod->icon_full_url)
                        <img class="onerror-image" src="{{ $mod->icon_full_url }}" width="38" alt="img">
                    @else
                        <i class="tio-briefcase-outlined" style="font-size:38px; color:#005555;"></i>
                    @endif
                    <div class="w-0 flex-grow pl-2">
                        <h1 class="page-header-title mb-0">{{ translate($mod->module_name ?? 'Servicios') }} — {{ translate('Dashboard') }}</h1>
                        <p class="page-header-text m-0">{{ translate('Gestiona proveedores, solicitudes y categorías de servicios.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    @if(auth('admin')->user()->role_id == 1)

    <!-- KPIs principales -->
    <div class="row g-2 mb-3">
        <!-- Proveedores -->
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-sm avatar-soft-primary avatar-circle mr-3">
                        <span class="avatar-initials"><i class="tio-user-outlined"></i></span>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $total_providers }}</h3>
                        <small class="text-muted">{{ translate('Proveedores') }}</small>
                        <div class="text-success small">+{{ $new_providers }} {{ translate('este mes') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Solicitudes -->
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-sm avatar-soft-info avatar-circle mr-3">
                        <span class="avatar-initials"><i class="tio-receipt"></i></span>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $total_requests }}</h3>
                        <small class="text-muted">{{ translate('Solicitudes') }}</small>
                        <div class="text-info small">+{{ $new_requests }} {{ translate('este mes') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingresos plataforma -->
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-sm avatar-soft-success avatar-circle mr-3">
                        <span class="avatar-initials"><i class="tio-dollar-outlined"></i></span>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ \App\CentralLogics\Helpers::format_currency($total_earnings) }}</h3>
                        <small class="text-muted">{{ translate('Comisión Plataforma') }}</small>
                        <div class="text-success small">{{ $completed_requests }} {{ translate('completadas') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categorías -->
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-sm avatar-soft-warning avatar-circle mr-3">
                        <span class="avatar-initials"><i class="tio-category"></i></span>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $total_categories }}</h3>
                        <small class="text-muted">{{ translate('Categorías') }}</small>
                        <div class="text-warning small">{{ $active_providers }} {{ translate('proveedores activos') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End KPIs -->

    <!-- Estado de Solicitudes -->
    <div class="row g-2 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-header-title">{{ translate('Estado de Solicitudes') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{ route('admin.zarpya.services.requests') }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex m-0 align-items-center">
                                        <i class="tio-time nav-icon mr-2 text-warning"></i>
                                        <span>{{ translate('Pendientes') }}</span>
                                    </h6>
                                    <span class="card-title text-warning">{{ $pending_requests }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{ route('admin.zarpya.services.requests') }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex m-0 align-items-center">
                                        <i class="tio-checkmark-circle nav-icon mr-2 text-info"></i>
                                        <span>{{ translate('Aceptadas') }}</span>
                                    </h6>
                                    <span class="card-title text-info">{{ $accepted_requests }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{ route('admin.zarpya.services.requests') }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex m-0 align-items-center">
                                        <i class="tio-done-all nav-icon mr-2 text-success"></i>
                                        <span>{{ translate('Completadas') }}</span>
                                    </h6>
                                    <span class="card-title text-success">{{ $completed_requests }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{ route('admin.zarpya.services.requests') }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex m-0 align-items-center">
                                        <i class="tio-clear-circle nav-icon mr-2 text-danger"></i>
                                        <span>{{ translate('Canceladas') }}</span>
                                    </h6>
                                    <span class="card-title text-danger">{{ $canceled_requests }}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Estado Solicitudes -->

    <div class="row g-2">
        <!-- Gráfica solicitudes por mes -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header border-0">
                    <h5 class="card-header-title">{{ translate('Solicitudes por Mes') }} ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    <div id="requests-chart"></div>
                </div>
            </div>
        </div>

        <!-- Proveedores destacados -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header border-0">
                    <h5 class="card-header-title">{{ translate('Top Proveedores') }}</h5>
                    <a href="{{ route('admin.zarpya.services.providers') }}" class="btn btn-sm btn-white ml-auto">
                        {{ translate('Ver todos') }}
                    </a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($top_providers as $provider)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs avatar-soft-primary avatar-circle mr-2">
                                    <span class="avatar-initials">{{ strtoupper(substr($provider->business_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <div class="small font-weight-bold">{{ $provider->business_name }}</div>
                                    @if($provider->avg_rating)
                                        <div class="text-warning" style="font-size:11px;">
                                            ★ {{ number_format($provider->avg_rating, 1) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <span class="badge badge-soft-primary badge-pill">{{ $provider->total_jobs }} {{ translate('trabajos') }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted py-4">
                            {{ translate('Sin proveedores aún') }}
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Solicitudes recientes -->
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0">
                    <h5 class="card-header-title">{{ translate('Solicitudes Recientes') }}</h5>
                    <a href="{{ route('admin.zarpya.services.requests') }}" class="btn btn-sm btn-white ml-auto">
                        {{ translate('Ver todas') }}
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Cliente') }}</th>
                                <th>{{ translate('Proveedor') }}</th>
                                <th>{{ translate('Categoría') }}</th>
                                <th>{{ translate('Precio') }}</th>
                                <th>{{ translate('Estado') }}</th>
                                <th>{{ translate('Fecha') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_requests as $req)
                            <tr>
                                <td>{{ $req->id }}</td>
                                <td>{{ $req->customer_name ?? '—' }}</td>
                                <td>{{ $req->provider_name ?? '—' }}</td>
                                <td>{{ $req->category_name ?? '—' }}</td>
                                <td>{{ \App\CentralLogics\Helpers::format_currency($req->final_price ?? $req->quoted_price ?? 0) }}</td>
                                <td>
                                    @php
                                        $badge = match($req->status) {
                                            'pending'     => 'warning',
                                            'quoted'      => 'warning',
                                            'accepted'    => 'info',
                                            'in_progress' => 'primary',
                                            'completed'   => 'success',
                                            'canceled','cancelled' => 'danger',
                                            'disputed'    => 'danger',
                                            default       => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge badge-soft-{{ $badge }}">{{ ucfirst(str_replace('_',' ',$req->status)) }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($req->created_at)->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ translate('Sin solicitudes aún') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- Empleado -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">{{ translate('messages.welcome') }}, {{ auth('admin')->user()->f_name }}.</h1>
                <p class="page-header-text">{{ translate('messages.employee_welcome_message') }}</p>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('script')
    <script src="{{ asset('/public/assets/admin/js/apex-charts/apexcharts.js') }}"></script>
@endpush

@push('script_2')
<script>
"use strict";
@if(auth('admin')->user()->role_id == 1)
const requestsData = [{{ implode(',', $requests_by_month) }}];
const months = [
    '{{ translate("Ene") }}','{{ translate("Feb") }}','{{ translate("Mar") }}',
    '{{ translate("Abr") }}','{{ translate("May") }}','{{ translate("Jun") }}',
    '{{ translate("Jul") }}','{{ translate("Ago") }}','{{ translate("Sep") }}',
    '{{ translate("Oct") }}','{{ translate("Nov") }}','{{ translate("Dic") }}'
];

const chartOptions = {
    series: [{
        name: '{{ translate("Solicitudes") }}',
        data: requestsData
    }],
    chart: {
        height: 300,
        type: 'bar',
        toolbar: { show: false }
    },
    colors: ['#005555'],
    plotOptions: {
        bar: { borderRadius: 4, columnWidth: '55%' }
    },
    dataLabels: { enabled: false },
    xaxis: { categories: months },
    yaxis: { min: 0, tickAmount: 5 },
    tooltip: { y: { formatter: val => val + ' {{ translate("solicitudes") }}' } }
};

const chart = new ApexCharts(document.querySelector("#requests-chart"), chartOptions);
chart.render();
@endif
</script>
@endpush