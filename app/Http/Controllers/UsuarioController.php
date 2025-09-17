<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
             
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Debes iniciar sesión.');
            }

            // Verifica el rol del usuario autenticado
            if (Auth::user()->role === 'admin') {
                return $next($request);
            }

            // Si el rol no es admin, redirige al dashboard con un mensaje de error
            return redirect()->route('dashboard')->with('error', 'No tienes acceso a esta sección.');
        }); 
    }

    public function index()
    {
        $usuarios = Usuario::with('empleado')->get(); // Eager loading para obtener los empleados
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $empleados = Empleado::all(); // Obtener todos los empleados
        return view('usuarios.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:usuarios',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'empleado_id' => 'nullable|exists:empleados,id',
        ]);

        try {
            Usuario::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'empleado_id' => $request->empleado_id,
            ]);

            return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ocurrió un error al crear el usuario.'])->withInput();
        }
    }

    public function edit(Usuario $usuario)
    {
        $empleados = Empleado::all(); // Obtener todos los empleados
        return view('usuarios.edit', compact('usuario', 'empleados'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:usuarios,username,' . $usuario->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'empleado_id' => 'nullable|exists:empleados,id',
        ]);

        try {
            $usuario->username = $request->username;
            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->password);
            }
            $usuario->role = $request->role;
            $usuario->empleado_id = $request->empleado_id;
            $usuario->save();

            return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ocurrió un error al actualizar el usuario.'])->withInput();
        }
    }

    public function destroy(Usuario $usuario)
    {
        try {
            $usuario->delete();
            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ocurrió un error al eliminar el usuario.']);
        }
    }
}