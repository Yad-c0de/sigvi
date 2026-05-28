@extends('layouts.app')

@section('title', 'Acceso Denegado')
@section('page-icon')<i class="bi bi-shield-exclamation text-danger"></i>@endsection
@section('subtitle', 'No tienes permisos para ver esta sección')

@section('content')
<div class="container" style="max-width: 550px; margin-top: 50px;">
    <div class="card shadow-sm text-center">
        <div class="card-body" style="padding: 48px 32px;">
            <i class="bi bi-shield-slash" style="font-size: 80px; color: var(--danger);"></i>
            <h2 class="mt-3 fw-bold" style="color: var(--text);">Acceso restringido</h2>
            <p class="text-muted mt-2 mb-4" style="font-size: 14px;">
                Lo sentimos, no tienes permisos para acceder a esta área.<br>
                Solo los administradores pueden ingresar aquí.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ url()->previous() }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline">
                    <i class="bi bi-house-door"></i> Ir al Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
