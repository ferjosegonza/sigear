<?php

//define("APP_VERSION", '1234');
define("APP_NAME", "SIGeAR");
define("LOGIN_URL", BASE_URL . "/sso/login");
define("LOGOUT_URL", BASE_URL . "/sso/logout");



define("APP_BOTONERA", array(
    array(
        'nombre' => 'SOLICITUDES',
        'permiso' => 'MesaDeEntrada',
        //'icono' => BASE_URL . '/images/icons/mesa-entrada.svg',
        'icono' => BASE_URL . '/images/icons/catalogo-formularios.svg',
        'url' => BASE_URL . '/solicitudes'
        //'url' => BASE_URL . '/mde-botonera'
    ),
   
    array(
        'nombre' => 'FONDOS Y COLECCIONES',
        'permiso' => 'Fondos',
        'icono' => BASE_URL . '/images/icons/fondos.svg',
       // 'url' => BASE_URL . '/fondos-botonera'
        'url' => BASE_URL . '/fondos/repositorio'
    ),

    array(
        'nombre' => 'ADMINISTRACION',
        'permiso' => 'Administracion',
        'icono' => BASE_URL . '/images/icons/administracion.svg',
        'url' => BASE_URL . '/administracion-botonera'
    ),
  
)
);

/*define("APP_BOTONERA_MDE", array(
    array(
        'nombre' => 'CARGA FORMULARIOS',
        'permiso' => 'MesaDeEntrada',
        'icono' => BASE_URL . '/images/icons/catalogo-formularios.svg',
        'url' => BASE_URL . '/solicitudes'
    ),
  
));
*/
/*
define("APP_BOTONERA_FONDOS", array(
   
    array(
        'nombre' => 'IMPORTAR EXCEL',
        'permiso' => 'Fondos',
        'icono' => BASE_URL . '/images/icons/catalogo-excel.svg',
        'url' => BASE_URL . '/fondos/fondosExcel',
    ),
   array(
        'nombre' => 'REPOSITORIO',
        'permiso' => 'Fondos',
        'icono' => BASE_URL . '/images/icons/catalogo-excel.svg',
        'url' => BASE_URL . '/fondos/repositorio',
    )*/
/*    array(
        'nombre' => 'PRUEBA',
        // 'permiso' => 'Fondos',
        'icono' => BASE_URL . '/images/icons/catalogo-excel.svg',
        'url' => BASE_URL . '/fondos/mostrarProbandoOCR',
    )

));*/


define("APP_BOTONERA_ADMINISTRACION", array(

    array(
        'descripcion' => 'Usuarios',
        'grupo' => array(
                array(
                    'nombre' => 'PERMISOS',
                    'permiso' => 'Permisos',
                    'icono' => BASE_URL . '/images/icons/permisos.svg',
                    'url' => BASE_URL . '/permisos'
                ),
                array(
                    'nombre' => 'ROLES',
                    'permiso' => 'Roles',
                    'icono' => BASE_URL . '/images/icons/roles.svg',
                    'url' => BASE_URL . '/roles'
                ),
                array(
                    'nombre' => 'USUARIOS',
                    'permiso' => 'Cuentas',
                    'icono' => BASE_URL . '/images/icons/usuarios.svg',
                    'url' => BASE_URL . '/cuentas'
                ),
                array(
                    'nombre' => 'ERRORES',
                    'permiso' => 'Errores',
                    'icono' => BASE_URL . '/images/icons/errores.svg',
                    'url' => BASE_URL . '/errores'
                ),
        )
    ),

    array(
        'descripcion' => 'Catalogos',
        'grupo' => array(
                array(
                    'nombre' => 'SOLICITANTES',
                    'permiso' => 'MesaDeEntrada',
                    'icono' => BASE_URL . '/images/icons/catalogo-solicitantes.svg',
                    'url' => BASE_URL . '/solicitantes'  
                ),
                array(
                        'nombre' => 'INSTITUCIONES',
                        'permiso' => 'MesaDeEntrada',
                        'icono' => BASE_URL . '/images/icons/catalogo-instituciones.svg',
                        'url' => BASE_URL . '/instituciones'  
                    ),
                    array(
                        'nombre' => 'FONDOS',
                        'permiso' => 'Fondos',
                        'icono' => BASE_URL . '/images/icons/catalogo-fondos.svg',
                        'url' => BASE_URL . '/fondos'
                    ),
        )
    ),

   /* array(
        'descripcion' => 'Fondos',
        'grupo' => array(
                    array(
                        'nombre' => 'CATALOGO FONDOS',
                        'permiso' => 'Fondos',
                        'icono' => BASE_URL . '/images/icons/catalogo-fondos.svg',
                        'url' => BASE_URL . '/fondos'
                    ),
                )
    ,)*/
    ));


?>