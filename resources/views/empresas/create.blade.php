@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="welcome-message">Registrar Nueva Empresa</h1>
    
    <div class="card">
        <!-- Mostrar errores de validación -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('empresas.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="nombre">Nombre de la Empresa:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nit">NIT:</label>
                    <input type="text" id="nit" name="nit" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="ciudad">Ciudad:</label>
                    <select id="ciudad" name="ciudad" class="form-control" required>
                        <option value="La Paz">La Paz</option>
                        <option value="Santa Cruz">Santa Cruz</option>
                        <option value="Cochabamba">Cochabamba</option>
                        <option value="Oruro">Oruro</option>
                        <option value="Potosí">Potosí</option>
                        <option value="Chuquisaca">Chuquisaca</option>
                        <option value="Tarija">Tarija</option>
                        <option value="Beni">Beni</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="numero_patronal">Número Patronal:</label>
                    <input type="text" id="numero_patronal" name="numero_patronal" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="nro_empleador_min_trab">Nro Empleador Mín Trab:</label>
                    <input type="text" id="nro_empleador_min_trab" name="nro_empleador_min_trab" class="form-control" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="gestion">Gestión:</label>
                    <input type="number" id="gestion" name="gestion" min="2000" max="2100" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="tipo_empresa">Tipo de Empresa:</label>
                    <select id="tipo_empresa" name="tipo_empresa" class="form-control" required>
                        <option value="Empresa Unipersonal">Empresa Unipersonal</option>
                        <option value="Sociedad de Responsabilidad Limitada (S.R.L.)">S.R.L.</option>
                        <option value="Sociedad Anónima (S.A.)">S.A.</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="representante_legal">Representante Legal:</label>
                    <input type="text" id="representante_legal" name="representante_legal" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="ci_representante_legal">CI Representante:</label>
                    <input type="text" id="ci_representante_legal" name="ci_representante_legal" class="form-control" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" class="form-control" required>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="salario_minimo_nacional">Salario Mínimo Nacional:</label>
                    <input type="number" step="0.01" id="salario_minimo_nacional" name="salario_minimo_nacional" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="mes">Mes:</label>
                    <input type="text" id="mes" name="mes" class="form-control" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" class="form-control" required>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Empresa
                </button>
                <a href="{{ route('empresas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection