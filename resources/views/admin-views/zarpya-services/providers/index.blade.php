@extends('layouts.admin.app')
@section('title', 'Proveedores de Servicios')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-user-star-outlined" style="font-size:22px"></i></span>
            <span>Proveedores de Servicios</span>
        </h1>
        <div class="ml-auto d-flex gap-2">
            <a href="{{ route('admin.zarpya.services.categories') }}" class="btn btn-sm btn-outline-secondary">Categorías</a>
            <a href="{{ route('admin.zarpya.services.requests') }}" class="btn btn-sm btn-outline-secondary">Solicitudes</a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-warning">{{ $stats['pending'] }}</div>
                <small class="text-muted">Pendientes</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-success">{{ $stats['active'] }}</div>
                <small class="text-muted">Activos</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-danger">{{ $stats['suspended'] }}</div>
                <small class="text-muted">Suspendidos</small>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <form method="GET" class="row g-2 flex-grow-1">
                        <div class="col-md-2">
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Todos</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspendido</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">Todas las categorías</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ $categoryId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar negocio..." value="{{ $search }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary">Buscar</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Negocio</th>
                                <th>Categoría</th>
                                <th>Usuario</th>
                                <th>Zona</th>
                                <th>Estado</th>
                                <th>Verificado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($providers as $p)
                            <tr>
                                <td>
                                    <span class="font-weight-bold">{{ $p->business_name }}</span>
                                    @if($p->featured)<span class="badge badge-warning ml-1">★</span>@endif
                                </td>
                                <td>{{ $p->category?->name ?? '—' }}</td>
                                <td>{{ $p->user ? $p->user->f_name . ' ' . $p->user->l_name : '—' }}</td>
                                <td>{{ $p->zone?->name ?? '—' }}</td>
                                <td>
                                    @php $colors = ['pending'=>'warning','active'=>'success','suspended'=>'danger','inactive'=>'secondary'] @endphp
                                    <span class="badge badge-{{ $colors[$p->status] ?? 'secondary' }}">{{ ucfirst($p->status) }}</span>
                                </td>
                                <td>
                                    @if($p->verified)
                                        <span class="badge badge-soft-success"><i class="tio-checkmark-circle"></i> Verificado</span>
                                    @else
                                        <span class="badge badge-soft-warning">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.zarpya.services.provider.show', $p->id) }}"
                                        class="btn btn-sm btn-outline-primary"><i class="tio-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $providers->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection