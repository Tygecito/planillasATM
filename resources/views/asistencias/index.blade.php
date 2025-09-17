@extends('layouts.app')

@section('title', 'Asistencia - Mi App')

@section('content')
    <h1 class="welcome-message">Registro de Asistencia</h1>
    
    <div class="card">
        <div class="filter-section">
            <select>
                <option>Seleccionar empleado</option>
                <option>Juan Pérez</option>
                <option>María Gómez</option>
            </select>
            <select>
                <option>Seleccionar mes</option>
                <option>Enero 2025</option>
                <option>Febrero 2025</option>
            </select>
            <button class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            <button class="btn btn-primary"><i class="fas fa-plus"></i> Registrar Asistencia</button>
        </div>
        
        <div class="calendar">
            <div class="calendar-header">Lun</div>
            <div class="calendar-header">Mar</div>
            <div class="calendar-header">Mié</div>
            <div class="calendar-header">Jue</div>
            <div class="calendar-header">Vie</div>
            <div class="calendar-header">Sáb</div>
            <div class="calendar-header">Dom</div>
            
            <!-- Ejemplo de días del calendario -->
            <div class="calendar-day">
                <div class="calendar-date">1</div>
                <div class="attendance-mark"><span class="attendance-status present"></span> Presente</div>
            </div>
            <div class="calendar-day">
                <div class="calendar-date">2</div>
                <div class="attendance-mark"><span class="attendance-status present"></span> Presente</div>
            </div>
            <div class="calendar-day">
                <div class="calendar-date">3</div>
                <div class="attendance-mark"><span class="attendance-status late"></span> Tardanza</div>
            </div>
            <div class="calendar-day">
                <div class="calendar-date">4</div>
                <div class="attendance-mark"><span class="attendance-status absent"></span> Ausente</div>
            </div>
            <div class="calendar-day">
                <div class="calendar-date">5</div>
                <div class="attendance-mark"><span class="attendance-status present"></span> Presente</div>
            </div>
            <div class="calendar-day">
                <div class="calendar-date">6</div>
                <div class="attendance-mark">Fin de semana</div>
            </div>
            <div class="calendar-day">
                <div class="calendar-date">7</div>
                <div class="attendance-mark">Fin de semana</div>
            </div>
        </div>
        
        <div class="table-container" style="margin-top: 2rem;">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Hora de Entrada</th>
                        <th>Hora de Salida</th>
                        <th>Horas Trabajadas</th>
                        <th>Comentarios</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01/01/2025</td>
                        <td><span class="attendance-status present"></span> Presente</td>
                        <td>08:00 AM</td>
                        <td>05:00 PM</td>
                        <td>9 horas</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>02/01/2025</td>
                        <td><span class="attendance-status present"></span> Presente</td>
                        <td>08:15 AM</td>
                        <td>05:10 PM</td>
                        <td>8.9 horas</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>03/01/2025</td>
                        <td><span class="attendance-status late"></span> Tardanza</td>
                        <td>09:30 AM</td>
                        <td>06:00 PM</td>
                        <td>8.5 horas</td>
                        <td>Tráfico</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection