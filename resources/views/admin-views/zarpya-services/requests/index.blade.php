@extends('layouts.admin.app')
@section('title', 'Solicitudes de Servicios')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-clipboard-list-outlined" style="font-size:22px"></i></span>
            <span>Solicitudes de Servicios</span>
        </h1>
        <div class="ml-auto d-flex gap-2">
            <a href="{{ route('admin.zarpya.services.categories') }}" class="btn btn-sm btn-outline-secondary">Categorías</a>
            <a href="{{ route('admin.zarpya.services.providers') }}" class="btn btn-sm btn-outline-secondary">Proveedores</a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-info">{{ $stats['open'] }}</div>
                <small class="text-muted">Abiertas</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-success">{{ $stats['completed'] }}</div>
                <small class="text-muted">Completadas</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-warning">L{{ number_format($stats['revenue'], 0) }}</div>
                <small class="text-muted">Ingresos</small>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <form method="GET" class="row g-2 flex-grow-1">
                        <div class="col-md-3">
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">Todos los estados</option>
                                <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Abierta</option>
                                <option value="quoted" {{ $status === 'quoted' ? 'selected' : '' }}>Con cotización</option>
                                <option value="accepted" {{ $status === 'accepted' ? 'selected' : '' }}>Aceptada</option>
                                <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>En proceso</option>
                                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completada</option>
                                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Categoría</th>
                                <th>Proveedor</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                            <tr>
                                <td>#{{ $req->id }}</td>
                                <td>{{ $req->customer ? $req->customer->f_name . ' ' . $req->customer->l_name : '—' }}</td>
                                <td>{{ $req->category?->name ?? '—' }}</td>
                                <td>{{ $req->provider?->business_name ?? '—' }}</td>
                                <td>{{ Str::limit($req->description, 40) }}</td>
                                <td>
                                    @php $colors = ['open'=>'info','quoted'=>'warning','accepted'=>'success','in_progress'=>'primary','completed'=>'success','cancelled'=>'danger','disputed'=>'warning'] @endphp
                                    <span class="badge badge-{{ $colors[$req->status] ?? 'secondary' }}">{{ ucfirst($req->status) }}</span>
                                </td>
                                <td>L{{ number_format($req->quoted_price ?? 0, 2) }}</td>
                                <td>{{ $req->created_at->format('d/m H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $requests->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection