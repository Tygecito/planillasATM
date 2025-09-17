@extends('layouts.app')

@section('title', 'Dashboard - Mi App')

@section('content')
    <h1 class="welcome-message">Bienvenido, {{ Auth::user()->username }}</h1>
    <div class="card">
        <h2><i class="fas fa-chart-line"></i> Resumen Estadístico</h2>
        <p>Aquí puedes mostrar gráficos o resúmenes importantes de tu aplicación.</p>
    </div>
    <div class="card">
        <h2><i class="fas fa-bell"></i> Notificaciones Recientes</h2>
        <p>Listado de notificaciones o actividades recientes.</p>
    </div>
    <div class="card">
        <h2><i class="fas fa-tasks"></i> Tareas Pendientes</h2>
        <p>Listado de tareas o acciones requeridas.</p>
    </div>
@endsection