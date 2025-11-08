<?php

namespace App\Http\Controllers;

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
                    ->orWhere('cargo_laboral', 'LIKE', "%{$search}%")
                    ->orWhere('cua', 'LIKE', "%{$search}%"); // AÑADIDO: búsqueda por CUA
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
            
            'documento_identidad' => 'required|numeric|digits_between:5,20|unique:empleados,documento_identidad',
            'telefono' => ['nullable', 'regex:/^[0-9]{5,20}$/'],
            
            'direccion' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100|unique:empleados,email', 
            
            // CAMBIO: foto por cua con validación numérica de 8-10 dígitos
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
            
            'documento_identidad' => [
                'required',
                'numeric',
                'digits_between:5,20',
                Rule::unique('empleados', 'documento_identidad')->ignore($empleado->id),
            ],
            'telefono' => ['nullable', 'regex:/^[0-9]{5,20}$/'],
            
            'direccion' => 'nullable|string|max:100',
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('empleados', 'email')->ignore($empleado->id),
            ],
            
            // CAMBIO: foto por cua con validación numérica de 8-10 dígitos e ignorando el empleado actual
            'cua' => [
                'nullable',
                'numeric',
                'digits_between:8,10',
                Rule::unique('empleados', 'cua')->ignore($empleado->id),
            ],
            
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