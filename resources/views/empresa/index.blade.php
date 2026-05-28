@extends('layouts.app')

@section('title', 'Datos de Empresa')
@section('page-icon')<i class="bi bi-building-fill-gear text-primary"></i>@endsection
@section('subtitle', 'Configuración de Install D')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-building text-primary"></i> Información de la Empresa</h3>
            </div>
            <form method="POST" action="{{ route('empresa.store') }}">
                @csrf
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-5">
                            <div class="form-group mb-0">
                                <label class="form-label">RUC <span class="req">*</span></label>
                                <input type="text" name="ruc" class="form-control @error('ruc') is-invalid @enderror"
                                       value="{{ old('ruc', $empresa?->ruc) }}" maxlength="11" required placeholder="20xxxxxxxxx">
                                @error('ruc')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group mb-0">
                                <label class="form-label">Razón Social <span class="req">*</span></label>
                                <input type="text" name="razon_social" class="form-control @error('razon_social') is-invalid @enderror"
                                       value="{{ old('razon_social', $empresa?->razon_social) }}" required>
                                @error('razon_social')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label class="form-label">Dirección <span class="req">*</span></label>
                                <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                                       value="{{ old('direccion', $empresa?->direccion) }}" required>
                                @error('direccion')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-label">Distrito <span class="req">*</span></label>
                                <input type="text" name="distrito" class="form-control @error('distrito') is-invalid @enderror"
                                       value="{{ old('distrito', $empresa?->distrito) }}" required>
                                @error('distrito')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-label">Ciudad <span class="req">*</span></label>
                                <input type="text" name="ciudad" class="form-control @error('ciudad') is-invalid @enderror"
                                       value="{{ old('ciudad', $empresa?->ciudad ?? 'Trujillo') }}" required>
                                @error('ciudad')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="border-top:1px solid var(--border);display:flex;justify-content:flex-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy2-fill"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
