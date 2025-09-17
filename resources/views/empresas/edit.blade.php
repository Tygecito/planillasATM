@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="welcome-message">Editar Empresa: {{ $empresa->nombre }}</h1>
    
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
        
        <form id="editForm" action="{{ route('empresas.update', $empresa->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="nombre">Nombre de la Empresa:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre', $empresa->nombre) }}" required>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nit">NIT:</label>
                    <input type="text" id="nit" name="nit" class="form-control" value="{{ old('nit', $empresa->nit) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="ciudad">Ciudad:</label>
                    <select id="ciudad" name="ciudad" class="form-control" required>
                        @foreach(['La Paz', 'Santa Cruz', 'Cochabamba', 'Oruro', 'Potosí', 'Chuquisaca', 'Tarija', 'Beni'] as $ciudad)
                            <option value="{{ $ciudad }}" {{ old('ciudad', $empresa->ciudad) == $ciudad ? 'selected' : '' }}>{{ $ciudad }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="numero_patronal">Número Patronal:</label>
                    <input type="text" id="numero_patronal" name="numero_patronal" class="form-control" value="{{ old('numero_patronal', $empresa->numero_patronal) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="nro_empleador_min_trab">Nro Empleador Mín Trab:</label>
                    <input type="text" id="nro_empleador_min_trab" name="nro_empleador_min_trab" class="form-control" value="{{ old('nro_empleador_min_trab', $empresa->nro_empleador_min_trab) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="gestion">Gestión:</label>
                    <input type="number" id="gestion" name="gestion" min="2000" max="2100" class="form-control" value="{{ old('gestion', $empresa->gestion) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="tipo_empresa">Tipo de Empresa:</label>
                    <select id="tipo_empresa" name="tipo_empresa" class="form-control" required>
                        <option value="Empresa Unipersonal" {{ old('tipo_empresa', $empresa->tipo_empresa) == 'Empresa Unipersonal' ? 'selected' : '' }}>Empresa Unipersonal</option>
                        <option value="Sociedad de Responsabilidad Limitada (S.R.L.)" {{ old('tipo_empresa', $empresa->tipo_empresa) == 'Sociedad de Responsabilidad Limitada (S.R.L.)' ? 'selected' : '' }}>S.R.L.</option>
                        <option value="Sociedad Anónima (S.A.)" {{ old('tipo_empresa', $empresa->tipo_empresa) == 'Sociedad Anónima (S.A.)' ? 'selected' : '' }}>S.A.</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="representante_legal">Representante Legal:</label>
                    <input type="text" id="representante_legal" name="representante_legal" class="form-control" value="{{ old('representante_legal', $empresa->representante_legal) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="ci_representante_legal">CI Representante:</label>
                    <input type="text" id="ci_representante_legal" name="ci_representante_legal" class="form-control" value="{{ old('ci_representante_legal', $empresa->ci_representante_legal) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" class="form-control" value="{{ old('direccion', $empresa->direccion) }}" required>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="salario_minimo_nacional">Salario Mínimo Nacional:</label>
                    <input type="number" step="0.01" id="salario_minimo_nacional" name="salario_minimo_nacional" class="form-control" value="{{ old('salario_minimo_nacional', $empresa->salario_minimo_nacional) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="mes">Mes:</label>
                    <input type="text" id="mes" name="mes" class="form-control" value="{{ old('mes', $empresa->mes) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $empresa->email) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" class="form-control" value="{{ old('telefono', $empresa->telefono) }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <button type="button" class="btn btn-primary" id="confirmButton">
                    <i class="fas fa-save"></i> Actualizar Empresa
                </button>
                <a href="{{ route('empresas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('confirmButton').addEventListener('click', function() {
        if (confirm('¿Estás seguro de que quieres guardar los cambios?')) {
            document.getElementById('editForm').submit();
        }
    });
</script>
@endsection