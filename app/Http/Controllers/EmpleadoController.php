<?php

namespace App\Http\Controllers;

use App\Models\Empleado; 
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    // ELIMINA ESTE CONSTRUCTOR COMPLETAMENTE
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         // Restringir create y store solo para admin
    //         $routeName = $request->route()->getName();
            
    //         if (in_array($routeName, ['empleados.create', 'empleados.store']) && 
    //             Auth::user()->role !== 'admin') {
    //             return redirect()->route('empleados.index')
    //                 ->with('error', 'No tienes permisos para crear empleados');
    //         }
            
    //         return $next($request);
    //     });
    // }

    public function index(Request $request)
    {
        $query = Empleado::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'LIKE', "%{$search}%")
                  ->orWhere('primerapellido', 'LIKE', "%{$search}%")
                  ->orWhere('segundoapellido', 'LIKE', "%{$search}%")
                  ->orWhere('documento_identidad', 'LIKE', "%{$search}%")
                  ->orWhere('cargo_laboral', 'LIKE', "%{$search}%");
            });
        }

        $empleados = $query->get();
        $usuarios = Usuario::with('empleado')->get();

        return view('empleados.index', compact('empleados', 'usuarios'));
    }

    public function create()
    {
        return view('empleados.create');
    }

    public function store(Request $request)
    {
        $validationRules = [
            // Validaciones para empleado
            'nombres' => 'required|string|max:100',
            'primerapellido' => 'required|string|max:100',
            'segundoapellido' => 'nullable|string|max:100',
            'sucursal' => 'required|boolean',
            'fecha_ingreso' => 'required|date',
            'caja_de_salud' => 'nullable|in:Caja Nacional de Salud,Caja Bancaria Estatal de Salud,Caja de Salud de la Banca Privada,Caja Petrolera de Salud',
            'tipo_de_contrato' => 'nullable|in:Contrato escrito,Contrato verbal',
            'modalidad_contrato' => 'nullable|in:Contrato por tiempo indefinido,Contrato a plazo fijo,Contrato por temporada,Contrato por obra o servicio,Contrato de teletrabajo',
            'cargo_laboral' => 'required|string|max:100',
            'fecha_de_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F',
            'estado_civil' => 'nullable|in:Soltero,Casado,Divorciado,Viudo,Unión libre',
            'documento_identidad' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'foto' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
        ];

        // Solo validar campos de usuario si es admin
        if (Auth::user()->role === 'admin') {
            $validationRules['username'] = 'required|string|max:50|unique:usuarios';
            $validationRules['password'] = 'required|string|min:8|confirmed';
            $validationRules['role'] = 'required|in:admin,user';
        }

        $request->validate($validationRules);

        try {
            // Crear el empleado
            $empleado = Empleado::create($request->all());

            // Crear el usuario asociado
            if (Auth::user()->role === 'admin') {
                // Si es admin, usar los datos del formulario
                Usuario::create([
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                    'empleado_id' => $empleado->id,
                ]);
            } else {
                // Si no es admin, crear usuario con valores por defecto
                $username = 'user_' . $empleado->documento_identidad; // Usar documento como username
                Usuario::create([
                    'username' => $username,
                    'password' => Hash::make('password123'), // Contraseña por defecto
                    'role' => 'user',
                    'empleado_id' => $empleado->id,
                ]);
            }

            return redirect()->route('empleados.index')->with('success', 'Empleado y usuario creados correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear empleado y usuario: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Empleado $empleado)
    {
        $usuario = $empleado->usuario;
        $empleados = Empleado::all();
        return view('empleados.edit', compact('empleado', 'usuario', 'empleados'));
    }

            public function update(Request $request, Empleado $empleado)
{
    $validationRules = [
        'nombres' => 'required|string|max:100',
        'primerapellido' => 'required|string|max:100',
        'segundoapellido' => 'nullable|string|max:100',
        'sucursal' => 'required|boolean',
        'fecha_ingreso' => 'required|date',
        'caja_de_salud' => 'nullable|in:Caja Nacional de Salud,Caja Bancaria Estatal de Salud,Caja de Salud de la Banca Privada,Caja Petrolera de Salud',
        'tipo_de_contrato' => 'nullable|in:Contrato escrito,Contrato verbal',
        'modalidad_contrato' => 'nullable|in:Contrato por tiempo indefinido,Contrato a plazo fijo,Contrato por temporada,Contrato por obra o servicio,Contrato de teletrabajo',
        'cargo_laboral' => 'required|string|max:100',
        'fecha_de_nacimiento' => 'required|date',
        'genero' => 'required|in:M,F',
        'estado_civil' => 'nullable|in:Soltero,Casado,Divorciado,Viudo,Unión libre',
        'documento_identidad' => 'required|string|max:20',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:100',
        'email' => 'nullable|email|max:100',
        'foto' => 'nullable|string|max:255',
        'estado' => 'required|boolean',
    ];

    // Obtener el usuario asociado
    $usuario = $empleado->usuario;

    // Solo validar campos de usuario si es admin y existe usuario
    if (Auth::user()->role === 'admin' && $usuario) {
        $validationRules['username'] = 'required|string|max:255|unique:usuarios,username,' . $usuario->id;
        $validationRules['password'] = 'nullable|string|min:6|confirmed';
        $validationRules['role'] = 'required|in:admin,user';
    }

    $request->validate($validationRules);

    // Actualiza el empleado
    $empleado->update($request->except('username', 'password', 'role'));

    // Verificar si el usuario asociado existe
    if ($usuario) {
        if (Auth::user()->role === 'admin') {
            // Si es admin, actualizar todos los campos del usuario
            $usuario->username = $request->username;

            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->password);
            }

            $usuario->role = $request->role;
        }
        // Si no es admin, no se modifican los datos del usuario
        $usuario->save();
    }

    return redirect()->route('empleados.index')->with('success', 'Empleado y usuario actualizados correctamente');
}

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();
        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente');
    }
}