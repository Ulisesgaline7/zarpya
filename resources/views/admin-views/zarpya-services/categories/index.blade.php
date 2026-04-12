@extends('layouts.admin.app')
@section('title', 'Categorías de Servicios')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-tool" style="font-size:22px"></i></span>
            <span>Módulo Servicios — Categorías</span>
        </h1>
        <div class="ml-auto d-flex gap-2">
            <a href="{{ route('admin.zarpya.services.providers') }}" class="btn btn-sm btn-outline-primary">Proveedores</a>
            <a href="{{ route('admin.zarpya.services.requests') }}" class="btn btn-sm btn-outline-secondary">Solicitudes</a>
        </div>
    </div>

    <div class="row g-3">
        {{-- FORM --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Nueva categoría</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.zarpya.services.category.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="input-label">Slug</label>
                            <input type="text" name="slug" class="form-control" placeholder="electricidad" required>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Nombre</label>
                            <input type="text" name="name" class="form-control" placeholder="Electricidad" required>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Ícono (emoji)</label>
                            <input type="text" name="icon" class="form-control" placeholder="⚡" maxlength="10">
                        </div>
                        <div class="form-group">
                            <label class="input-label">Comisión Zarpya (%)</label>
                            <input type="number" name="platform_commission" class="form-control" value="15" step="0.01" min="0" max="100" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Crear categoría</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- CARDS --}}
        <div class="col-lg-8">
            <div class="row g-3">
                @foreach($categories as $cat)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center py-3">
                            <div style="font-size:2rem">{{ $cat->icon }}</div>
                            <h6 class="mb-1 mt-1">{{ $cat->name }}</h6>
                            <small class="text-muted">Comisión: <strong>{{ $cat->platform_commission }}%</strong></small>
                            <br><small class="text-muted">{{ $cat->providers_count }} proveedores</small>
                            <div class="mt-2 d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-xs btn-outline-primary edit-cat-btn"
                                    data-id="{{ $cat->id }}"
                                    data-name="{{ $cat->name }}"
                                    data-icon="{{ $cat->icon }}"
                                    data-commission="{{ $cat->platform_commission }}">Editar</button>
                                <label class="toggle-switch toggle-switch-sm mb-0">
                                    <input type="checkbox" class="toggle-switch-input status-toggle"
                                        data-url="{{ route('admin.zarpya.services.category.status') }}"
                                        data-id="{{ $cat->id }}"
                                        {{ $cat->status ? 'checked' : '' }}>
                                    <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Modal editar --}}
<div class="modal fade" id="editCatModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form id="editCatForm" method="POST">
            @csrf @method('PATCH')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Editar categoría</h5></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="name" id="editCatName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Ícono</label>
                        <input type="text" name="icon" id="editCatIcon" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Comisión (%)</label>
                        <input type="number" name="platform_commission" id="editCatCommission" class="form-control" step="0.01" min="0" max="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script>
document.querySelectorAll('.edit-cat-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('editCatName').value = this.dataset.name;
        document.getElementById('editCatIcon').value = this.dataset.icon;
        document.getElementById('editCatCommission').value = this.dataset.commission;
        document.getElementById('editCatForm').action = `/admin/zarpya/services/categories/${id}`;
        $('#editCatModal').modal('show');
    });
});
document.querySelectorAll('.status-toggle').forEach(toggle => {
    toggle.addEventListener('change', function () {
        fetch(this.dataset.url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            body: JSON.stringify({ id: this.dataset.id, status: this.checked ? 1 : 0 })
        }).then(r => r.ok ? location.reload() : alert('Error'));
    });
});
</script>
@endpush
