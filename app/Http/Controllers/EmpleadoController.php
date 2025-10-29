<?php

namespace App\Http\Controllers;

use App\Models\Empleado; 
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Importado para usar validaciones 'unique' en update

class EmpleadoController extends Controller
{
    // Bloque de Middleware COMENTADO, según tu solicitud
    /*
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Restringir create y store solo para admin
            $routeName = $request->route()->getName();
            
            if (in_array($routeName, ['empleados.create', 'empleados.store']) && 
                Auth::user()->role !== 'admin') {
                return redirect()->route('empleados.index')
                    ->with('error', 'No tienes permisos para crear empleados');
            }
            
            return $next($request);
        });
    }
    */

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
            // REGLAS ESTRICTAS PARA NOMBRES Y APELLIDOS: Solo letras, espacios y guiones
            // El regex /^[\pL\s\-]+$/u permite todas las letras unicode (incluyendo Ñ y acentos)
            'nombres' => ['required', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'primerapellido' => ['required', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'segundoapellido' => ['nullable', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            
            'sucursal' => 'required|boolean',
            'fecha_ingreso' => 'required|date',
            'caja_de_salud' => 'nullable|in:Caja Nacional de Salud,Caja Bancaria Estatal de Salud,Caja de Salud de la Banca Privada,Caja Petrolera de Salud',
            'tipo_de_contrato' => 'nullable|in:Contrato escrito,Contrato verbal',
            'modalidad_contrato' => 'nullable|in:Contrato por tiempo indefinido,Contrato a plazo fijo,Contrato por temporada,Contrato por obra o servicio,Contrato de teletrabajo',
            'cargo_laboral' => 'required|string|max:100',
            'fecha_de_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F',
            'estado_civil' => 'nullable|in:Soltero,Casado,Divorciado,Viudo,Unión libre',
            
            // VALIDACIÓN NUMÉRICA ESTRICTA y UNICIDAD
            'documento_identidad' => 'required|numeric|digits_between:5,20|unique:empleados,documento_identidad',
            // VALIDACIÓN ESTRICTA PARA TELÉFONO: Solo dígitos, longitud entre 5 y 20
            'telefono' => ['nullable', 'regex:/^[0-9]{5,20}$/'],
            
            'direccion' => 'nullable|string|max:100',
            // VALIDACIÓN DE FORMATO EMAIL y UNICIDAD
            'email' => 'nullable|email|max:100|unique:empleados,email', 
            
            'foto' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
        ];

        // Solo validar campos de usuario si es admin Y se proporcionaron datos de usuario
        if (Auth::user()->role === 'admin') {
            // ... (Lógica de validación de usuario)
            if ($request->filled('username') || $request->filled('password') || $request->filled('role')) {
                $validationRules['username'] = 'required|string|max:50|unique:usuarios';
                $validationRules['password'] = 'required|string|min:8|confirmed';
                $validationRules['role'] = 'required|in:admin,user';
            } else {
                // Si no se proporcionaron datos de usuario, hacerlos opcionales
                $validationRules['username'] = 'nullable|string|max:50|unique:usuarios';
                $validationRules['password'] = 'nullable|string|min:8|confirmed';
                $validationRules['role'] = 'nullable|in:admin,user';
            }
        }

        $request->validate($validationRules);

        try {
            // Crear el empleado
            $empleado = Empleado::create($request->all());

            // Crear el usuario asociado SOLO si se proporcionaron datos de usuario o si no es admin
            if (Auth::user()->role === 'admin') {
                // Si es admin y proporcionó datos de usuario, crear usuario con esos datos
                if ($request->filled('username') && $request->filled('password') && $request->filled('role')) {
                    Usuario::create([
                        'username' => $request->username,
                        'password' => Hash::make($request->password),
                        'role' => $request->role,
                        'empleado_id' => $empleado->id,
                    ]);
                }
                // Si es admin pero NO proporcionó datos de usuario, NO crear usuario
            } else {
                // Si no es admin, crear usuario con valores por defecto
                $username = 'user_' . $empleado->documento_identidad;
                Usuario::create([
                    'username' => $username,
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'empleado_id' => $empleado->id,
                ]);
            }

            return redirect()->route('empleados.index')->with('success', 'Empleado creado correctamente' . (($request->filled('username')) ? ' con usuario' : ' sin usuario'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear empleado: ' . $e->getMessage()])->withInput();
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
            // REGLAS ESTRICTAS PARA NOMBRES Y APELLIDOS: Solo letras, espacios y guiones
            'nombres' => ['required', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'primerapellido' => ['required', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'segundoapellido' => ['nullable', 'max:100', 'regex:/^[\pL\s\-]+$/u'],

            'sucursal' => 'required|boolean',
            'fecha_ingreso' => 'required|date',
            'caja_de_salud' => 'nullable|in:Caja Nacional de Salud,Caja Bancaria Estatal de Salud,Caja de Salud de la Banca Privada,Caja Petrolera de Salud',
            'tipo_de_contrato' => 'nullable|in:Contrato escrito,Contrato verbal',
            'modalidad_contrato' => 'nullable|in:Contrato por tiempo indefinido,Contrato a plazo fijo,Contrato por temporada,Contrato por obra o servicio,Contrato de teletrabajo',
            'cargo_laboral' => 'required|string|max:100',
            'fecha_de_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F',
            'estado_civil' => 'nullable|in:Soltero,Casado,Divorciado,Viudo,Unión libre',
            
            // VALIDACIÓN NUMÉRICA ESTRICTA, IGNORANDO EL EMPLEADO ACTUAL
            'documento_identidad' => [
                'required',
                'numeric',
                'digits_between:5,20',
                Rule::unique('empleados', 'documento_identidad')->ignore($empleado->id),
            ],
            // VALIDACIÓN ESTRICTA PARA TELÉFONO: Solo dígitos, longitud entre 5 y 20
            'telefono' => ['nullable', 'regex:/^[0-9]{5,20}$/'],
            
            'direccion' => 'nullable|string|max:100',
            // VALIDACIÓN DE FORMATO EMAIL, IGNORANDO EL EMPLEADO ACTUAL
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('empleados', 'email')->ignore($empleado->id),
            ],
            
            'foto' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
        ];

        // Obtener el usuario asociado
        $usuario = $empleado->usuario;

        // Validaciones condicionales según el tipo de usuario
        if ($usuario) {
            if (Auth::user()->role === 'admin') {
                // Caso 1: Admin editando cualquier usuario
                $validationRules['username'] = 'required|string|max:255|unique:usuarios,username,' . $usuario->id;
                $validationRules['password'] = 'nullable|string|min:6|confirmed';
                $validationRules['role'] = 'required|in:admin,user';
            } elseif (Auth::user()->role === 'user' && Auth::user()->id == $usuario->id) {
                // Caso 2: Usuario normal editándose a sí mismo - solo contraseña
                $validationRules['password'] = 'nullable|string|min:6|confirmed';
            }
        }

        $request->validate($validationRules);

        // Actualiza el empleado (excluir campos de usuario)
        $empleado->update($request->except('username', 'password', 'password_confirmation', 'role'));

        // Actualizar usuario si existe
        if ($usuario) {
            if (Auth::user()->role === 'admin') {
                // Caso 1: Admin - actualizar todos los campos
                $usuario->username = $request->username;
                $usuario->role = $request->role;
                
                if ($request->filled('password')) {
                    $usuario->password = Hash::make($request->password);
                }
            } elseif (Auth::user()->role === 'user' && Auth::user()->id == $usuario->id) {
                // Caso 2: Usuario normal editándose a sí mismo - solo contraseña
                if ($request->filled('password')) {
                    $usuario->password = Hash::make($request->password);
                }
            }
            
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
