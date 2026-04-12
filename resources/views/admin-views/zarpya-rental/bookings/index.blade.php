@extends('layouts.admin.app')
@section('title', 'Reservas de Renta')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-calendar" style="font-size:22px"></i></span>
            <span>Reservas de Renta</span>
        </h1>
        <a href="{{ route('admin.zarpya.rental.vehicles') }}" class="btn btn-sm btn-outline-secondary ml-auto">
            <i class="tio-arrow-backward"></i> Flota
        </a>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        @foreach(['total' => ['Totales','dark'], 'pending' => ['Pendientes','secondary'], 'active' => ['Activas','primary'], 'completed' => ['Completadas','success']] as $key => [$label, $color])
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-{{ $color }}">
                    {{ $key === 'revenue' ? 'L' . number_format($stats[$key], 2) : number_format($stats[$key]) }}
                </div>
                <small class="text-muted">{{ $label }}</small>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Lista de reservas</h5>
            <form method="GET" class="ml-auto d-flex gap-2">
                <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach(['pending','confirmed','active','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Vehículo</th>
                        <th>Cliente</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Total</th>
                        <th>Pago</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $b)
                    <tr>
                        <td>#{{ $b->id }}</td>
                        <td>{{ $b->vehicle?->brand }} {{ $b->vehicle?->model }}</td>
                        <td>{{ $b->customer?->f_name }} {{ $b->customer?->l_name }}</td>
                        <td><small>{{ $b->start_at?->format('d/m/Y H:i') }}</small></td>
                        <td><small>{{ $b->end_at?->format('d/m/Y H:i') }}</small></td>
                        <td class="text-success font-weight-bold">L{{ number_format($b->total_price, 2) }}</td>
                        <td><span class="badge badge-soft-info">{{ $b->payment_method ?? '—' }}</span></td>
                        <td>
                            @php $statusColors = ['pending'=>'secondary','confirmed'=>'info','active'=>'primary','completed'=>'success','cancelled'=>'danger'] @endphp
                            <span class="badge badge-{{ $statusColors[$b->status] ?? 'secondary' }}">{{ ucfirst($b->status) }}</span>
                        </td>
                        <td>
                            <form action="{{ route('admin.zarpya.rental.booking.status', $b->id) }}" method="POST" class="d-flex gap-1">
                                @csrf
                                <select name="status" class="form-control form-control-sm" style="width:110px">
                                    @foreach(['pending','confirmed','active','completed','cancelled'] as $s)
                                        <option value="{{ $s }}" {{ $b->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-xs btn-primary">OK</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $bookings->links() }}</div>
    </div>
</div>
@endsection
