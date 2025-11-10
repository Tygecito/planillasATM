@extends('layouts.app')

@section('title', 'Editar Subsidio - Mi App')

@section('content')
    <h1 class="welcome-message">Editar Subsidio</h1>

    <div class="card">
        <div class="card-header">
            <h4>Empleado: {{ $empleado->nombres }} {{ $empleado->primerapellido }}</h4>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <p><strong>Por favor, corrija los siguientes errores:</strong></p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('subsidios.update', $subsidio->id) }}" method="POST" id="edit-subsidio-form">
                @csrf
                @method('PUT') 

                <div class="form-grid-empleado">
                    {{-- Tipo de Subsidio --}}
                    <div class="form-group">
                        <label for="tipo_subsidio">Tipo de Subsidio *</label>
                        <select name="tipo_subsidio" id="tipo_subsidio" class="form-control @error('tipo_subsidio') is-invalid @enderror" required>
                            <option value="">Seleccione...</option>
                            <option value="PRENATAL" {{ old('tipo_subsidio', $subsidio->tipo_subsidio) == 'PRENATAL' ? 'selected' : '' }}>PRENATAL</option>
                            <option value="NATALIDAD" {{ old('tipo_subsidio', $subsidio->tipo_subsidio) == 'NATALIDAD' ? 'selected' : '' }}>NATALIDAD</option>
                            <option value="LACTANCIA" {{ old('tipo_subsidio', $subsidio->tipo_subsidio) == 'LACTANCIA' ? 'selected' : '' }}>LACTANCIA</option>
                        </select>
                        @error('tipo_subsidio') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Monto --}}
                    <div class="form-group">
                        <label for="monto">Monto (Bs) *</label>
                        <input type="number" name="monto" id="monto" class="form-control @error('monto') is-invalid @enderror" 
                               value="{{ old('monto', $subsidio->monto) }}" required step="0.01" 
                               min="2500" 
                               placeholder="Mínimo 2500.00">
                        @error('monto') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Mes --}}
                    <div class="form-group">
                        <label for="mes">Mes *</label>
                        <select class="form-control @error('mes') is-invalid @enderror" id="mes" name="mes" required>
                            <option value="">Seleccionar mes</option>
                            @php
                                $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                            @endphp
                            @foreach ($meses as $mesNombre)
                                <option value="{{ $mesNombre }}" {{ old('mes', $subsidio->mes) == $mesNombre ? 'selected' : '' }}>{{ $mesNombre }}</option>
                            @endforeach
                        </select>
                        @error('mes') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    
                    {{-- Año --}}
                    <div class="form-group">
                        <label for="anio">Año *</label>
                        <select class="form-control @error('anio') is-invalid @enderror" id="anio" name="anio" required>
                            <option value="">Seleccionar año</option>
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}" {{ old('anio', $subsidio->anio) == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                        @error('anio') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Estado --}}
                    <div class="form-group">
                        <label for="estado">Estado *</label>
                        <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                            <option value="POR PAGAR" {{ old('estado', $subsidio->estado) == 'POR PAGAR' ? 'selected' : '' }}>POR PAGAR</option>
                            <option value="PAGADO" {{ old('estado', $subsidio->estado) == 'PAGADO' ? 'selected' : '' }}>PAGADO</option>
                        </select>
                        @error('estado') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-primary" onclick="showCustomConfirm()">
                        <i class="fas fa-save"></i> Actualizar Subsidio
                    </button>
                    <a href="{{ route('subsidios.reporte', $empleado->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de Confirmación --}}
    <div id="custom-confirm-modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 400px; border-radius: 8px;">
            <p><strong>¿Estás seguro de actualizar este subsidio?</strong></p>
            <button onclick="submitForm()" style="background-color: #942044; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">Sí, Actualizar</button>
            <button onclick="hideCustomConfirm()" style="background-color: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
        </div>
    </div>

    {{-- Estilos y Script --}}
    <style>
        .form-grid-empleado {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-group { display: flex; flex-direction: column; }
        .form-control { width: 100%; }
        .form-actions {
            margin-top: 2rem;
            border-top: 1px solid #ddd;
            padding-top: 1.5rem;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        .text-danger { color: #dc3545 !important; font-size: 0.85rem; margin-top: 5px; }
        #custom-confirm-modal > div { text-align: center; }
        @media (max-width: 768px) {
            .form-grid-empleado { grid-template-columns: 1fr; }
        }
    </style>

    <script>
        function showCustomConfirm() { document.getElementById('custom-confirm-modal').style.display = 'block'; }
        function hideCustomConfirm() { document.getElementById('custom-confirm-modal').style.display = 'none'; }
        
        function submitForm() { 
            hideCustomConfirm(); 
            document.getElementById('edit-subsidio-form').submit(); 
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('custom-confirm-modal');
            if (event.target === modal) { modal.style.display = 'none'; }
        }
    </script>
@endsection