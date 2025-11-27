<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Gestión de Personal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Definición de variables de color y tipografía moderna */
        :root {
            --primary-color: #942044; /* Tu color principal */
            --primary-dark: #7a1a38;
            --background-color: #e6e6e6;
            --text-color: #333;
        }

        body {
            font-family: 'Poppins', sans-serif; /* Usando fuente más profesional */
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        .login-container {
            background-color: white;
            padding: 2.5rem; /* Más espacio interior */
            border-radius: 12px; /* Más redondeado */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); /* Sombra más profunda */
            width: 100%;
            max-width: 420px;
        }

        /* --- Cabecera: Logo y Títulos --- */
        .app-logo { text-align: center; margin-bottom: 1rem; }
        /* RUTA CORREGIDA: Asumiendo public/img/logo.png */
        .logo-img { width: 180px; height: auto; }

        .project-title {
            color: var(--text-color);
            font-size: 1.25rem; 
            font-weight: 600; 
            text-align: center;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        .login-header {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
            font-weight: 700;
        }
        .login-subheader {
            text-align: center;
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            font-weight: 300;
        }

        /* --- Formulario y Efectos de Interacción (Clave para mejorar) --- */
        .form-group { margin-bottom: 1.5rem; } /* Más espacio entre campos */
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--text-color); font-weight: 600; font-size: 0.95rem; }
        .form-group input {
            width: 100%;
            padding: 0.8rem; /* Inputs más grandes */
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        /* Efecto de foco: se ilumina con el color principal */
        .form-group input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(148, 32, 68, 0.2);
        }

        /* --- Estilos de Botón --- */
        .btn-login {
            width: 100%;
            padding: 0.9rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            box-shadow: 0 4px 10px rgba(148, 32, 68, 0.4);
        }
        /* Efecto de hover: más oscuro y ligero movimiento */
        .btn-login:hover { 
            background-color: var(--primary-dark); 
            box-shadow: 0 6px 15px rgba(148, 32, 68, 0.5);
            transform: translateY(-2px);
        }
        .error { color: red; font-size: 0.85rem; margin-top: 0.2rem; display: block; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="app-logo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo de la Empresa" class="logo-img">
        </div>
        
        <h2 class="project-title">SISTEMA WEB PARA LA GESTIÓN DE PERSONAL Y NÓMINAS</h2>
        <h1 class="login-header">Iniciar Sesión</h1>
        <p class="login-subheader">Por favor, introduce tu usuario y contraseña.</p>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Tu nombre de usuario" required>
                @error('username') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Tu contraseña" required>
                @error('password') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <button type="submit" class="btn-login">Ingresar al Sistema</button>
        </form>
    </div>
</body>
</html>