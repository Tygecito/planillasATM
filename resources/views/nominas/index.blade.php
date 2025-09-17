@extends('layouts.app')

@section('title', 'Nóminas - Mi App')

@section('content')
    <h1 class="welcome-message">Gestión de Nóminas</h1>
    
    <div class="card">
        <div class="filter-section">
            <select>
                <option>Seleccionar mes</option>
                <option>Enero</option>
                <option>Febrero</option>
                <option>Marzo</option>
            </select>
            <select>
                <option>Seleccionar año</option>
                <option>2023</option>
                <option>2024</option>
                <option>2025</option>
            </select>
            <button class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            <button class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Nómina</button>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Empleado</th>
                        <th>Periodo</th>
                        <th>Sueldo Base</th>
                        <th>Bonificaciones</th>
                        <th>Descuentos</th>
                        <th>Neto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Juan Pérez</td>
                        <td>Enero 2025</td>
                        <td>S/ 3,500.00</td>
                        <td>S/ 500.00</td>
                        <td>S/ 200.00</td>
                        <td>S/ 3,800.00</td>
                        <td>
                            <button class="btn btn-secondary"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-secondary"><i class="fas fa-download"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>María Gómez</td>
                        <td>Enero 2025</td>
                        <td>S/ 4,200.00</td>
                        <td>S/ 300.00</td>
                        <td>S/ 150.00</td>
                        <td>S/ 4,350.00</td>
                        <td>
                            <button class="btn btn-secondary"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-secondary"><i class="fas fa-download"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection