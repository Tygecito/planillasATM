@extends('layouts.app')

@section('title', 'Generación de Planillas - Mi App')

@section('content')

<h1 class="welcome-message">Generación de Planillas</h1>
<div class="card p-4">
    <div class="card-header">
        <h2>Filtro de Periodo y Reporte</h2>
    </div>

    <div class="card-body">
        
        {{-- Muestra errores de validación de formulario --}}
        @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p><strong>Por favor, corrija los siguientes errores:</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Muestra errores generales devueltos por el controlador (como 'No hay datos') --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="GET" id="filter-form" onsubmit="return false;">
            
            <div class="form-row filter-grid">
                {{-- Filtro Mes --}}
                <div class="form-group">
                    <label for="mes">Seleccionar Mes *</label>
                    <select class="form-control" id="mes" name="mes" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($meses as $mes)
                            <option value="{{ $mes }}" {{ old('mes') == $mes ? 'selected' : '' }}>{{ $mes }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Filtro Año --}}
                <div class="form-group">
                    <label for="anio">Seleccionar Año *</label>
                    <select class="form-control" id="anio" name="anio" required>
                        <option value="">-- Seleccionar --</option>
                        @for($year = date('Y') + 1; $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ old('anio', date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div></div> 
            </div>

        </form>

        <h3 style="margin-top: 2rem;">Tipos de Planilla</h3>
        <div class="report-actions-grid">
            
            {{-- Planilla Mensual --}}
            <div class="report-card">
                <h4>1. Planilla Mensual</h4>
                <p>Reporte detallado de sueldos y salarios por periodo.</p>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="generarReporte('mensual', 'pdf')">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                    <button class="btn btn-secondary" onclick="generarReporte('mensual', 'xlsx')">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                </div>
            </div>

            {{-- Planilla RC IVA --}}
            <div class="report-card">
                <h4>2. Planilla RC IVA</h4>
                <p>Declaración jurada de retenciones por RC IVA.</p>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="generarReporte('rc_iva', 'pdf')">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                    <button class="btn btn-secondary" onclick="generarReporte('rc_iva', 'xlsx')">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                </div>
            </div>
            
            {{-- Planilla Gestora --}}
            <div class="report-card">
                <h4>3. Planilla Gestora</h4>
                <p>Declaración de aportes a la Gestora Pública.</p>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="generarReporte('gestora', 'pdf')">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                    <button class="btn btn-secondary" onclick="generarReporte('gestora', 'xlsx')">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>

<style>
/* Estilos adicionales para la vista Index de Planillas */
.filter-grid { 
    display: grid; 
    grid-template-columns: repeat(3, 1fr); 
    gap: 1.5rem; 
    align-items: flex-end;
}
.report-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 1.5rem;
}
.report-card {
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.report-card h4 {
    color: #942044;
    margin-bottom: 0.5rem;
}
.action-buttons {
    margin-top: 1rem;
    display: flex;
    gap: 10px;
}
.action-buttons .btn {
    flex: 1;
}
.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
}
@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function generarReporte(tipo, formato) {
    const mes = document.getElementById('mes').value;
    const anio = document.getElementById('anio').value;

    if (!mes || !anio) {
        // Muestra una alerta simple si faltan datos
        alert('Debe seleccionar el Mes y el Año para generar la planilla.'); 
        return;
    }

    // Construye la URL de descarga.
    const url = `/planillas/generar/${tipo}/${formato}?mes=${mes}&anio=${anio}`;
    
    window.location.href = url;
}
</script>

@endsection