<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi App')</title>
    
    <!-- Incluir el CSS común -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- CSS específico por página -->
    @stack('styles')
</head>
<body>
    <header class="header">
        <div class="logo">Mi Aplicación</div>
        <div class="user-info">
            @if (Auth::check())
                @php
                    $user = Auth::user();
                @endphp

                @if ($user->empleado)
                    <span>{{ $user->full_name }}</span> <!-- Mostrará el nombre completo -->
                @else
                    <span>No hay empleado asociado</span>
                @endif
            @else
                <span>Invitado</span>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button>
            </form>
        </div>
    </header>
    
    <div class="main-container">
        <aside class="sidebar">
            <ul class="sidebar-nav">
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('empresas.index') }}" class="{{ request()->routeIs('empresas.*') ? 'active' : '' }}">
                        <i class="fas fa-clinic-medical"></i> Empresas
                    </a>
                </li>
                <li>
                    <a href="{{ route('empleados.index') }}" class="{{ request()->routeIs('empleados.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Empleados
                    </a>
                </li>
                <li>
                    <a href="{{ route('nominas.index') }}" class="{{ request()->routeIs('nominas.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i> Nóminas
                    </a>
                </li>
                <li>
                    <a href="{{ route('planillas.index') }}" class="{{ request()->routeIs('planillas.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> Planillas
                    </a>
                </li>
                <li>
                    <a href="{{ route('asistencias.index') }}" class="{{ request()->routeIs('asistencias.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> Asistencia
                    </a>
                </li>
                <li>
                    <a href="{{ route('permisos.index') }}" class="{{ request()->routeIs('permisos.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar"></i> Permisos
                    </a>
                </li>
                <!-- Agregar enlace a Usuarios -->
                <li>
                    <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> Usuarios
                    </a>
                </li>
            </ul>
        </aside>
        
        <main class="content">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    
    <footer class="footer">
        <p>&copy; 2025 Mi Aplicación Laravel. Versión 1.0.0</p>
    </footer>

    <!-- Scripts específicos por página -->
    @stack('scripts')
</body>
</html>