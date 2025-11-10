<?php

return [

    // ... (Todas las reglas estándar de 'accepted' a 'uuid') ...

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'active_url' => 'El campo :attribute no es una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute sólo puede contener letras.',
    'alpha_dash' => 'El campo :attribute sólo puede contener letras, números y guiones.',
    'alpha_num' => 'El campo :attribute sólo puede contener letras y números.',
    'array' => 'El campo :attribute debe ser un conjunto (array).',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'numeric' => 'El campo :attribute debe ser un valor entre :min y :max.',
        'file' => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'string' => 'El campo :attribute debe contener entre :min y :max caracteres.',
        'array' => 'El campo :attribute debe contener entre :min y :max elementos.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'date_format' => 'El campo :attribute no corresponde con el formato :format.',
    'different' => 'Los campos :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions' => 'Las dimensiones de la imagen :attribute no son válidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',
    'email' => 'El campo :attribute debe ser una dirección de correo válida.',
    'exists' => 'El campo :attribute seleccionado no es válido.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute debe tener un valor.',
    'gt' => [
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'file' => 'El archivo :attribute debe pesar más de :value kilobytes.',
        'string' => 'El campo :attribute debe contener más de :value caracteres.',
        'array' => 'El campo :attribute debe contener más de :value elementos.',
    ],
    'gte' => [
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'file' => 'El archivo :attribute debe pesar :value kilobytes o más.',
        'string' => 'El campo :attribute debe contener :value caracteres o más.',
        'array' => 'El campo :attribute debe contener :value elementos o más.',
    ],
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El campo :attribute seleccionado no es válido.',
    'in_array' => 'El campo :attribute no existe en :other.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'ip' => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El campo :attribute debe ser una cadena JSON válida.',
    'lt' => [
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'file' => 'El archivo :attribute debe pesar menos de :value kilobytes.',
        'string' => 'El campo :attribute debe contener menos de :value caracteres.',
        'array' => 'El campo :attribute debe contener menos de :value elementos.',
    ],
    'lte' => [
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'file' => 'El archivo :attribute debe pesar :value kilobytes o menos.',
        'string' => 'El campo :attribute debe contener :value caracteres o menos.',
        'array' => 'El campo :attribute debe contener :value elementos o menos.',
    ],
    'max' => [
        'numeric' => 'El campo :attribute no debe ser mayor de :max.',
        'file' => 'El archivo :attribute no debe pesar más de :max kilobytes.',
        'string' => 'El campo :attribute no debe contener más de :max caracteres.',
        'array' => 'El campo :attribute no debe contener más de :max elementos.',
    ],
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'file' => 'El archivo :attribute debe pesar al menos :min kilobytes.',
        'string' => 'El campo :attribute debe contener al menos :min caracteres.',
        'array' => 'El campo :attribute debe contener al menos :min elementos.',
    ],
    'not_in' => 'El campo :attribute seleccionado no es válido.',
    'not_regex' => 'El formato del campo :attribute no es válido.',
    'numeric' => 'El campo :attribute debe ser numérico.',
    'present' => 'El campo :attribute debe estar presente.',
    'regex' => 'El formato del campo :attribute no es válido.',
    'required' => 'El campo :attribute es obligatorio.',
    'required_if' => 'El campo :attribute es obligatorio cuando el campo :other es :value.',
    'required_unless' => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with' => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all' => 'El campo :attribute es obligatorio cuando :values están presentes.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de los campos :values están presentes.',
    'same' => 'Los campos :attribute y :other deben coincidir.',
    'size' => [
        'numeric' => 'El campo :attribute debe ser :size.',
        'file' => 'El archivo :attribute debe pesar :size kilobytes.',
        'string' => 'El campo :attribute debe contener :size caracteres.',
        'array' => 'El campo :attribute debe contener :size elementos.',
    ],
    'starts_with' => 'El campo :attribute debe comenzar con uno de los siguientes valores: :values',
    'string' => 'El campo :attribute debe ser una cadena de caracteres.',
    'timezone' => 'El campo :attribute debe ser una zona horaria válida.',
    'unique' => 'El :attribute ya ha sido registrado.',
    'uploaded' => 'El campo :attribute no se pudo subir.',
    'url' => 'El formato del campo :attribute no es válido.',
    'uuid' => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'documento_identidad' => [
            'numeric' => 'El campo Documento de Identidad debe ser estrictamente numérico.',
        ],
        'password' => [
            'min' => 'La contraseña debe tener al menos :min caracteres.',
        ],
        'nit_dependiente' => [
            'numeric' => 'El NIT dependiente debe ser solo numérico.',
            'unique'  => 'El NIT dependiente ya ha sido registrado.',
        ],
        'complemento' => [
            'max'   => 'El complemento no debe tener más de :max caracteres.',
            'regex' => 'El formato del complemento no es válido (ej: 1A, E5).',
        ],
        'fecha_de_nacimiento' => [
            'after_or_equal' => 'La fecha de nacimiento no es válida (el empleado debe tener como máximo 70 años).',
            'before_or_equal' => 'La fecha de nacimiento no es válida (el empleado debe tener al menos 20 años).',
        ],
        
        // --- SECCIÓN ACTUALIZADA ---
        'fecha_ingreso' => [
            'before_or_equal' => 'La fecha de ingreso no puede ser una fecha futura.',
            'after_or_equal' => 'La fecha de ingreso es demasiado antigua (no más de 50 años).',
        ],
        // --- ---
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'nombres' => 'nombres',
        'primerapellido' => 'primer apellido',
        'segundoapellido' => 'segundo apellido',
        'sucursal' => 'sucursal',
        'fecha_ingreso' => 'fecha de ingreso',
        'cargo_laboral' => 'cargo laboral',
        'fecha_de_nacimiento' => 'fecha de nacimiento',
        'genero' => 'género',
        'estado_civil' => 'estado civil',
        'documento_identidad' => 'documento identidad',
        'telefono' => 'teléfono',
        'direccion' => 'dirección',
        'email' => 'email',
        'estado' => 'estado',
        'username' => 'username',
        'password' => 'contraseña',
        'role' => 'rol',
        'cua' => 'CUA',
        'complemento' => 'complemento',
        'nit_dependiente' => 'NIT dependiente',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'nombres' => 'nombres',
        'primerapellido' => 'primer apellido',
        // ... (Todos tus atributos existentes) ...
        'nit_dependiente' => 'NIT dependiente',

        // --- CAMBIO: AÑADIR ESTAS LÍNEAS ---
        'empleados.*.haber_basico' => 'Haber Básico',
        'empleados.*.smn' => 'SMN',
        'empleados.*.empleado_id' => 'Empleado',
        // --- FIN DEL CAMBIO ---
    ],

];