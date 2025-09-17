@extends('layouts.app')

@section('title', 'Planillas - Mi App')

@section('content')
    <h1 class="welcome-message">Planillas y Reportes</h1>
    
    <div class="stats-cards">
        <div class="stat-card">
            <h3><i class="fas fa-users"></i> Total Empleados</h3>
            <p>42</p>
        </div>
        <div class="stat-card">
            <h3><i class="fas fa-money-bill-wave"></i> Nómina Mensual</h3>
            <p>S/ 125,000</p>
        </div>
        <div class="stat-card">
            <h3><i class="fas fa-calendar-check"></i> Asistencia Promedio</h3>
            <p>95%</p>
        </div>
    </div>
    
    <div class="card">
        <div class="filter-section">
            <select>
                <option>Seleccionar reporte</option>
                <option>Planilla general</option>
                <option>Reporte de impuestos</option>
                <option>Reporte de beneficios</option>
            </select>
            <select>
                <option>Seleccionar periodo</option>
                <option>Enero 2025</option>
                <option>Febrero 2025</option>
            </select>
            <button class="btn btn-primary"><i class="fas fa-download"></i> Generar Reporte</button>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tipo de Reporte</th>
                        <th>Periodo</th>
                        <th>Generado el</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Planilla general</td>
                        <td>Diciembre 2024</td>
                        <td>05/01/2025</td>
                        <td>
                            <button class="btn btn-secondary"><i class="fas fa-download"></i> Descargar</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Reporte de impuestos</td>
                        <td>Diciembre 2024</td>
                        <td>05/01/2025</td>
                        <td>
                            <button class="btn btn-secondary"><i class="fas fa-download"></i> Descargar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection