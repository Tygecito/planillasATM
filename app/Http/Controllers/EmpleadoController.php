<?php

namespace App\Http\Controllers; // <-- CORREGIDO

use App\Models\Empleado; 
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; 

class EmpleadoController extends Controller
{
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
                    ->orWhere('complemento', 'LIKE', "%{$search}%") 
                    ->orWhere('nit_dependiente', 'LIKE', "%{$search}%") 
                    ->orWhere('cargo_laboral', 'LIKE', "%{$search}%")
                    ->orWhere('cua', 'LIKE', "%{$search}%");
            });
        }
        $query->orderBy('nombres', 'asc');
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
        // --- FECHAS DINÁMICAS PARA VALIDACIÓN ---
        $today = now()->toDateString();
        $minBirthDate = now()->subYears(70)->toDateString(); // Fecha mínima (hace 70 años)
        $maxBirthDate = now()->subYears(20)->toDateString(); // Fecha máxima (hace 20 años)
        
        // Definimos una fecha de ingreso "más antigua" razonable (ej: 50 años atrás)
        $oldestHireDate = now()->subYears(50)->toDateString(); 
        // --- ---

        $validationRules = [
            'nombres' => ['required', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'primerapellido' => ['required', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'segundoapellido' => ['nullable', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'sucursal' => 'required|boolean',
            
            'fecha_ingreso' => [
                'required', 
                'date', 
                'before_or_equal:' . $today, // No puede ser fecha futura
                'after_or_equal:' . $oldestHireDate // No puede ser una fecha irrazonablemente antigua
            ],
            
            'caja_de_salud' => 'nullable|in:Caja Nacional de Salud,Caja Bancaria Estatal de Salud,Caja de Salud de la Banca Privada,Caja Petrolera de Salud',
            'tipo_de_contrato' => 'nullable|in:Contrato escrito,Contrato verbal',
            'modalidad_contrato' => 'nullable|in:Contrato por tiempo indefinido,Contrato a plazo fijo,Contrato por temporada,Contrato por obra o servicio,Contrato de teletrabajo',
            
            // --- CORREGIDO ---
            'cargo_laboral' => ['required', Rule::in(Empleado::CARGOS_LABORALES)], 
            
            'fecha_de_nacimiento' => [
                'required', 
                'date', 
                'after_or_equal:' . $minBirthDate,  // Debe ser DESPUÉS de (max 70 años)
                'before_or_equal:' . $maxBirthDate // Debe ser ANTES de (min 20 años)
            ],

            'genero' => 'required|in:M,F',
            'estado_civil' => 'nullable|in:Soltero,Casado,Divorciado,Viudo,Unión libre',
            'documento_identidad' => 'required|numeric|digits_between:5,20|unique:empleados,documento_identidad',
            'complemento' => 'nullable|string|max:2|regex:/^[A-Z0-9]{1,2}$/i', 
            'nit_dependiente' => 'nullable|numeric|unique:empleados,nit_dependiente', 
            'telefono' => ['nullable', 'regex:/^[0-9]{5,20}$/'],
            'direccion' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100|unique:empleados,email', 
            'cua' => 'nullable|numeric|digits_between:8,10|unique:empleados,cua',
            'estado' => 'required|boolean',
        ];

        if (Auth::user()->role === 'admin') {
            if ($request->filled('username') || $request->filled('password') || $request->filled('role')) {
                $validationRules['username'] = 'required|string|max:50|unique:usuarios';
                $validationRules['password'] = 'required|string|min:8|confirmed';
                $validationRules['role'] = 'required|in:admin,user';
            } else {
                $validationRules['username'] = 'nullable|string|max:50|unique:usuarios';
                $validationRules['password'] = 'nullable|string|min:8|confirmed';
                $validationRules['role'] = 'nullable|in:admin,user';
            }
        }

        $request->validate($validationRules);

        try {
            $empleado = Empleado::create($request->all());

            if (Auth::user()->role === 'admin') {
                if ($request->filled('username') && $request->filled('password') && $request->filled('role')) {
                    Usuario::create([
                        'username' => $request->username,
                        'password' => Hash::make($request->password),
                        'role' => $request->role,
                        'empleado_id' => $empleado->id,
                    ]);
                }
            } else {
                // Si no es admin, o si el admin no llenó los campos, se crea un usuario 'user' por defecto
                // (Comentado - puedes activar esta lógica si la necesitas)
                /*
                $username = 'user_' . $empleado->documento_identidad;
                Usuario::create([
                    'username' => $username,
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'empleado_id' => $empleado->id,
                ]);
                */
            }

            return redirect()->route('empleados.index')->with('success', 'Empleado creado correctamente' . (($request->filled('username')) ? ' con usuario' : ' sin usuario'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear empleado: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Empleado $empleado)
    {
        $usuario = $empleado->usuario;
        $empleados = Empleado::all(); // Esta línea parece no usarse en la vista edit, pero la mantenemos
        return view('empleados.edit', compact('empleado', 'usuario', 'empleados'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        // --- FECHAS DINÁMICAS PARA VALIDACIÓN ---
        $today = now()->toDateString();
        $minBirthDate = now()->subYears(70)->toDateString(); 
        $maxBirthDate = now()->subYears(20)->toDateString(); 
        $oldestHireDate = now()->subYears(50)->toDateString(); 
        // --- ---

        $validationRules = [
            'nombres' => ['required', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'primerapellido' => ['required', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'segundoapellido' => ['nullable', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'sucursal' => 'required|boolean',

            'fecha_ingreso' => [
                'required', 
                'date', 
                'before_or_equal:' . $today, 
                'after_or_equal:' . $oldestHireDate 
            ],

            'caja_de_salud' => 'nullable|in:Caja Nacional de Salud,Caja Bancaria Estatal de Salud,Caja de Salud de la Banca Privada,Caja Petrolera de Salud',
            'tipo_de_contrato' => 'nullable|in:Contrato escrito,Contrato verbal',
            'modalidad_contrato' => 'nullable|in:Contrato por tiempo indefinido,Contrato a plazo fijo,Contrato por temporada,Contrato por obra o servicio,Contrato de teletrabajo',
            
            // --- CORREGIDO ---
            'cargo_laboral' => ['required', Rule::in(Empleado::CARGOS_LABORALES)],

            'fecha_de_nacimiento' => [
                'required', 
                'date', 
                'after_or_equal:' . $minBirthDate,  
                'before_or_equal:' . $maxBirthDate 
            ],

            'genero' => 'required|in:M,F',
            'estado_civil' => 'nullable|in:Soltero,Casado,Divorciado,Viudo,Unión libre',
            'documento_identidad' => ['required', 'numeric', 'digits_between:5,20', Rule::unique('empleados', 'documento_identidad')->ignore($empleado->id)],
            'complemento' => 'nullable|string|max:2|regex:/^[A-Z0-9]{1,2}$/i', 
            'nit_dependiente' => ['nullable', 'numeric', Rule::unique('empleados', 'nit_dependiente')->ignore($empleado->id)],
            'telefono' => ['nullable', 'regex:/^[0-9]{5,20}$/'],
            'direccion' => 'nullable|string|max:100',
            'email' => ['nullable', 'email', 'max:100', Rule::unique('empleados', 'email')->ignore($empleado->id)],
            'cua' => ['nullable', 'numeric', 'digits_between:8,10', Rule::unique('empleados', 'cua')->ignore($empleado->id)],
            'estado' => 'required|boolean',
        ];

        $usuario = $empleado->usuario;

        if ($usuario) {
            if (Auth::user()->role === 'admin') {
                $validationRules['username'] = 'required|string|max:255|unique:usuarios,username,' . $usuario->id;
                $validationRules['password'] = 'nullable|string|min:6|confirmed';
                $validationRules['role'] = 'required|in:admin,user';
            } elseif (Auth::user()->role === 'user' && Auth::user()->id == $usuario->id) {
                $validationRules['password'] = 'nullable|string|min:6|confirmed';
            }
        }

        $request->validate($validationRules);

        $empleado->update($request->except('username', 'password', 'password_confirmation', 'role'));

        if ($usuario) {
            if (Auth::user()->role === 'admin') {
                $usuario->username = $request->username;
                $usuario->role = $request->role;
                
                if ($request->filled('password')) {
                    $usuario->password = Hash::make($request->password);
                }
            } elseif (Auth::user()->role === 'user' && Auth::user()->id == $usuario->id) {
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