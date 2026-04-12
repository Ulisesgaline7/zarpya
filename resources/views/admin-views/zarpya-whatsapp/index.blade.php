@extends('layouts.admin.app')
@section('title', 'WhatsApp - Logs de Notificaciones')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-chat-outlined" style="font-size:22px"></i></span>
            <span>WhatsApp — Logs de Notificaciones</span>
        </h1>
        <small class="text-muted">Twilio WhatsApp API</small>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div class="h3 mb-0">{{ $stats['total'] }}</div>
                <small class="text-muted">Total</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-info">{{ $stats['sent'] }}</div>
                <small class="text-muted">Enviados</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-success">{{ $stats['delivered'] }}</div>
                <small class="text-muted">Entregados</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-danger">{{ $stats['failed'] }}</div>
                <small class="text-muted">Fallidos</small>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Filtros --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-2">
                        <div class="col-md-3">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">Todos los estados</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="sent" {{ $status == 'sent' ? 'selected' : '' }}>Enviado</option>
                                <option value="delivered" {{ $status == 'delivered' ? 'selected' : '' }}>Entregado</option>
                                <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>Fallido</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="template" class="form-control" onchange="this.form.submit()">
                                <option value="">Todas las plantillas</option>
                                @foreach($templates as $t)
                                    <option value="{{ $t }}" {{ $template == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Buscar teléfono..." value="{{ $search }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Teléfono</th>
                                <th>Plantilla</th>
                                <th>Estado</th>
                                <th>Twilio SID</th>
                                <th>Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->to_phone }}</td>
                                <td><span class="badge badge-soft-info">{{ $log->template_name }}</span></td>
                                <td>
                                    @php $colors = ['pending'=>'warning','sent'=>'info','delivered'=>'success','failed'=>'danger','read'=>'success'] @endphp
                                    <span class="badge badge-{{ $colors[$log->status] ?? 'secondary' }}">{{ ucfirst($log->status) }}</span>
                                </td>
                                <td><small class="text-muted">{{ $log->twilio_sid ?? '—' }}</small></td>
                                <td>{{ $log->created_at->format('d/m H:i') }}</td>
                                <td>
                                    @if($log->status === 'failed')
                                        <a href="{{ route('admin.zarpya.whatsapp.retry', $log->id) }}"
                                            class="btn btn-sm btn-outline-warning" title="Reenviar">
                                            <i class="tio-refresh"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection