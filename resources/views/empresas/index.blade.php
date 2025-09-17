@extends('layouts.app')

@section('title', 'Empresas - Mi App')

@section('content')
    <h1 class="welcome-message">Gestión de Empresas</h1>
    
    <div class="card">
        <div class="search-bar">
            <input type="text" placeholder="Buscar empresa...">
            <button class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <a href="{{ route('empresas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Empresa
            </a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>NIT</th>
                        <th>Ciudad</th>
                        <th>Número Patronal</th>
                        <th>Gestión</th>
                        <th>Nro Empleador Mín Trab</th>
                        <th>Tipo de Empresa</th>
                        <th>Representante</th>
                        <th>Dirección</th>
                        <th>CI Representante</th>
                        <th>Mes</th>
                        <th>Salario Mínimo Nacional</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresas as $empresa)
                    <tr>
                        <td>{{ $empresa->id }}</td>
                        <td>{{ $empresa->nombre }}</td>
                        <td>{{ $empresa->nit }}</td>
                        <td>{{ $empresa->ciudad }}</td>
                        <td>{{ $empresa->numero_patronal }}</td>
                        <td>{{ $empresa->gestion }}</td>
                        <td>{{ $empresa->nro_empleador_min_trab }}</td>
                        <td>{{ $empresa->tipo_empresa }}</td>
                        <td>{{ $empresa->representante_legal }}</td>
                        <td>{{ $empresa->direccion }}</td>
                        <td>{{ $empresa->ci_representante_legal }}</td>
                        <td>{{ $empresa->mes }}</td>
                        <td>{{ $empresa->salario_minimo_nacional }}</td>
                        <td>{{ $empresa->email }}</td>
                        <td>{{ $empresa->telefono }}</td>
                        <td>
                            <a href="{{ route('empresas.edit', $empresa->id) }}" class="btn btn-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('empresas.destroy', $empresa->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary" onclick="return confirm('¿Estás seguro?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection