<?php

abstract class Controller extends Container {
    public function __construct($container) {
        parent::__construct($container);

        $view = $this->container->view;

        // usuario
        $usuario = $this->getUsuario();
        $view->offsetSet("userAction", $usuario->dibujarUserAction(LOGOUT_URL));

        $view->offsetSet("APP_usuario", $usuario->getInfo());
        $view->offsetSet("APP_permisos", $usuario->getPermisos());

        // botonera
        $view->offsetSet("appBotonera", defined('APP_BOTONERA') ? $this->filtrarBotonera(APP_BOTONERA) : array());
        //organismo
        $view->offsetSet("APP_organismo",array(
            "nombre" => "SIGeAr",
            "descripcion" => "",
            "logo" => BASE_URL."/images/logo-blanco.svg",
            "logoEstilo" => ""
        ));
 
    }

    public function filtrarBotonera($botonera = array()) {
        $usuario = $this->getUsuario();

        $botoneraMapeada = array_map(function($item) use ($usuario) {
            if (array_key_exists("grupo", $item)) {
                $item['grupo'] = $this->filtrarBotonera($item['grupo']);
                if (empty($item['grupo'])) {
                    return null;
                }
                return $item;
            }

            if (!array_key_exists("permiso", $item)) {
                return $item; // si no se definio permiso, se asume publico
            }
            $permisoRequerido = trim($item["permiso"]);
            if ($permisoRequerido === "") {
                return $item; // si permiso vacio, se asume publico
            }
            if ($usuario->tienePermiso($permisoRequerido)) {
                return $item;
            }
            return null;
        }, $botonera);

        $botoneraFiltrada = array_filter($botoneraMapeada);
        usort($botoneraFiltrada, function($a, $b) {
            $aIsGroup = array_key_exists("grupo", $a);
            $bIsGroup = array_key_exists("grupo", $b);
            if ($aIsGroup && !$bIsGroup) return 1;
            if (!$aIsGroup && $bIsGroup) return -1;
            return 0;
        });

        return $botoneraFiltrada;
    }

}
