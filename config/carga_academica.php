<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hojas institucionales procesables
    |--------------------------------------------------------------------------
    |
    | El archivo Excel institucional puede traer hojas auxiliares. Para SIGAT-FICA
    | solo se procesan las hojas confirmadas por Coordinación.
    |
    */

    'hojas_permitidas' => [
        'VIRTUALES Y SETACO',
        'INFO',
        'CCAA',
    ],

    /*
    |--------------------------------------------------------------------------
    | Encabezados obligatorios
    |--------------------------------------------------------------------------
    |
    | El importador buscará estas columnas por encabezado normalizado, no por
    | posición. Si cambia el orden de columnas, la importación debe seguir
    | funcionando.
    |
    */

    'columnas_obligatorias' => [
        'codigo_materia',
        'nombre_materia',
        'numero_seccion',
        'tipo',
        'docente_titular',
    ],

    /*
    |--------------------------------------------------------------------------
    | Columnas opcionales
    |--------------------------------------------------------------------------
    |
    | Si existen, se importan. Si no existen, no se bloquea la carga.
    |
    */

    'columnas_opcionales' => [
        'codigo_catedra',
        'horas',
        'dias',
        'codigo_docente',
        'categoria_docente',
        'correo_institucional',
        'correo_personal',
        'observaciones',
        'aula',
        'capacidad',
    ],

    /*
    |--------------------------------------------------------------------------
    | Alias de encabezados
    |--------------------------------------------------------------------------
    |
    | Todos los encabezados se normalizarán antes de comparar:
    | - mayúsculas/minúsculas
    | - acentos
    | - saltos de línea
    | - espacios múltiples
    |
    | Esto permite tolerar cambios menores en el Excel institucional.
    |
    */

    'alias_columnas' => [
        'codigo_catedra' => [
            'COD CATEDRA',
            'COD CÁTEDRA',
            'CODIGO CATEDRA',
            'CÓDIGO CÁTEDRA',
        ],

        'codigo_materia' => [
            'COD ASIG',
            'CODIGO ASIG',
            'CÓDIGO ASIG',
            'CODIGO ASIGNATURA',
            'CÓDIGO ASIGNATURA',
            'COD MATERIA',
            'CODIGO MATERIA',
        ],

        'nombre_materia' => [
            'ASIGNATURA',
            'MATERIA',
            'NOMBRE ASIGNATURA',
            'NOMBRE DE ASIGNATURA',
            'NOMBRE MATERIA',
        ],

        'numero_seccion' => [
            'SEC',
            'SECCION',
            'SECCIÓN',
            'NUMERO SECCION',
            'NÚMERO SECCIÓN',
            'NUM SECCION',
        ],

        'horas' => [
            'HORAS',
            'HORA',
            'HORARIO',
        ],

        'dias' => [
            'DIAS',
            'DÍAS',
            'DIA',
            'DÍA',
        ],

        'tipo' => [
            'TIPO',
            'MODALIDAD',
        ],

        'docente_titular' => [
            'DOCENTE',
            'DOCENTE TITULAR',
            'NOMBRE DOCENTE',
        ],

        'codigo_docente' => [
            'CODIGO DOCENTE',
            'CÓDIGO DOCENTE',
            'COD DOCENTE',
            'COD. DOCENTE',
        ],

        'categoria_docente' => [
            'CAT DOC',
            'CATEGORIA DOC',
            'CATEGORÍA DOC',
            'CATEGORIA DOCENTE',
            'CATEGORÍA DOCENTE',
        ],

        'correo_institucional' => [
            'CORREO INSTITUCIONAL',
            'EMAIL INSTITUCIONAL',
            'CORREO UTEC',
        ],

        'correo_personal' => [
            'CORREO PERSONAL',
            'EMAIL PERSONAL',
        ],

        'observaciones' => [
            'OBSERVACIONES',
            'OBS',
        ],

        'aula' => [
            'AULA',
            'SALON',
            'SALÓN',
        ],

        'capacidad' => [
            'CAPACIDAD',
            'CUPO',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Modalidades desde Excel institucional
    |--------------------------------------------------------------------------
    |
    | Confirmado por Coordinación:
    | P    = presencial
    | EL   = en línea
    | E.L. = en línea
    | V    = virtual
    | Par  = no se ocupa
    |
    */

    'modalidades' => [
        'P' => [
            'modalidad' => 'presencial',
            'requiere_tutor' => true,
            'importar' => true,
            'requiere_horario' => true,
        ],

        'EL' => [
            'modalidad' => 'en_linea',
            'requiere_tutor' => true,
            'importar' => true,
            'requiere_horario' => true,
        ],

        'E.L.' => [
            'modalidad' => 'en_linea',
            'requiere_tutor' => true,
            'importar' => true,
            'requiere_horario' => true,
        ],

        'V' => [
            'modalidad' => 'virtual',
            'requiere_tutor' => true,
            'importar' => true,
            'requiere_horario' => false,
        ],

        'PAR' => [
            'modalidad' => null,
            'requiere_tutor' => false,
            'importar' => false,
            'requiere_horario' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Días de la semana
    |--------------------------------------------------------------------------
    |
    | El Excel puede traer combinaciones como:
    | Lu-Mie
    | Ma-Jue
    | Sab
    | Lu-Ma-Mie-Jue-Vie-Sab-Dom
    |
    */

    'dias_semana' => [
        'LU' => 1,
        'LUN' => 1,
        'LUNES' => 1,

        'MA' => 2,
        'MAR' => 2,
        'MARTES' => 2,

        'MI' => 3,
        'MIE' => 3,
        'MIERCOLES' => 3,
        'MIÉRCOLES' => 3,

        'JU' => 4,
        'JUE' => 4,
        'JUEVES' => 4,

        'VI' => 5,
        'VIE' => 5,
        'VIERNES' => 5,

        'SA' => 6,
        'SAB' => 6,
        'SÁB' => 6,
        'SABADO' => 6,
        'SÁBADO' => 6,

        'DO' => 7,
        'DOM' => 7,
        'DOMINGO' => 7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reglas de negocio de importación
    |--------------------------------------------------------------------------
    */

    'reglas' => [
        /*
         | Si una materia no existe, se crea automáticamente porque el Excel
         | institucional es la fuente principal de la carga académica.
         */
        'crear_materias_si_no_existen' => true,

        /*
         | Las materias creadas automáticamente quedan pendientes de revisión
         | porque el Excel no trae ciclo del plan y los créditos no se usan para
         | asignación de tutores.
         */
        'materia_nueva_requiere_revision' => true,

        /*
         | No se debe inventar ciclo del plan. Si no viene de catálogo, queda null.
         */
        'ciclo_plan_por_defecto' => null,

        /*
         | Créditos no se usan en el proceso de tutorías. Se conserva default 3.
         */
        'creditos_por_defecto' => 3,

        /*
         | Las materias nuevas no se marcan automáticamente como gestionadas por
         | Coordinación. La coordinadora debe validarlo desde el CRUD de materias.
         */
        'materia_nueva_gestionada_por_coordinacion' => false,

        /*
         | Si se reimporta carga académica corregida, se actualiza la sección y
         | se reemplazan los horarios de esa sección importada.
         */
        'reemplazar_horarios_en_reimportacion' => true,

        /*
         | Si una sección existente no aparece en el Excel nuevo, no se elimina
         | ni se desactiva automáticamente.
         */
        'eliminar_secciones_ausentes' => false,

        /*
         | Las secciones virtuales son asincrónicas. No se debe guardar horario
         | 00:00-23:59 porque rompería la validación de choques de horario.
         */
        'guardar_horarios_virtuales' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Encabezados y filas
    |--------------------------------------------------------------------------
    */

    'lectura' => [
        /*
         | Cantidad máxima de filas a revisar para encontrar encabezados.
         | El archivo institucional trae títulos arriba de la tabla.
         */
        'max_filas_busqueda_encabezado' => 25,

        /*
         | Fila mínima válida con datos. Se usa después de detectar encabezados.
         */
        'ignorar_filas_vacias' => true,
    ],

];
