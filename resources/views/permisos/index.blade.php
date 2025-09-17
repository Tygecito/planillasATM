@extends('layouts.app')

@section('title', 'Vacaciones - Mi App')

@section('content')
    <h1 class="welcome-message">Gestión de Vacaciones</h1>
    
    <div class="vacation-summary">
        <div class="summary-card">
            <h3><i class="fas fa-calendar-check"></i> Días Disponibles</h3>
            <p>15</p>
        </div>
        <div class="summary-card">
            <h3><i class="fas fa-calendar-times"></i> Días Usados</h3>
            <p>5</p>
        </div>
        <div class="summary-card">
            <h3><i class="fas fa-calendar-alt"></i> Días Pendientes</h3>
            <p>3</p>
        </div>
    </div>
    
    <div class="card">
        <div class="filter-section">
            <select>
                <option>Seleccionar empleado</option>
                <option>Juan Pérez</option>
                <option>María Gómez</option>
            </select>
            <select>
                <option>Seleccionar estado</option>
                <option>Aprobado</option>
                <option>Pendiente</option>
                <option>Rechazado</option>
            </select>
            <button class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            <button class="btn btn-primary"><i class="fas fa-plus"></i> Solicitar Vacaciones</button>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Días</th>
                        <th>Estado</th>
                        <th>Comentarios</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Juan Pérez</td>
                        <td>15/01/2025</td>
                        <td>20/01/2025</td>
                        <td>5</td>
                        <td><span class="vacation-status approved">Aprobado</span></td>
                        <td>Vacaciones programadas</td>
                        <td>
                            <button class="btn btn-secondary"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>María Gómez</td>
                        <td>10/02/2025</td>
                        <td>15/02/2025</td>
                        <td>5</td>
                        <td><span class="vacation-status pending">Pendiente</span></td>
                        <td>Esperando aprobación</td>
                        <td>
                            <button class="btn btn-secondary"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Juan Pérez</td>
                        <td>01/03/2025</td>
                        <td>10/03/2025</td>
                        <td>9</td>
                        <td><span class="vacation-status rejected">Rechazado</span></td>
                        <td>Periodo de alta demanda</td>
                        <td>
                            <button class="btn btn-secondary"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection