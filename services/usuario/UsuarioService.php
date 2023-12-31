<?php

class UsuarioService extends Service {
    private SessionHandlerService $authenticator;
    private $usuario;

    public function __construct($container) {
        parent::__construct($container);

        $this->authenticator = $this->get('service.authenticator.SessionHandler');
        if (!$this->authenticator) throw new Exception("UsuarioService requiere SessionHandler");

        $this->usuario = $this->authenticator->getSesion();
    }

    public function getInfo() {
        return $this->usuario;
    }

    public function getIdCuenta() {
        if (!$this->usuario) return null;
        return $this->usuario['cuenta_id'];
    }

    public function getCuil() {
        if (!$this->usuario) return null;
        return $this->usuario['cuil'];
    }

    public function getPermisos() {
        if (!$this->usuario) {
            return array();
        }
        return $this->usuario['permisos'];
    }

    public function tienePermiso($permiso) {
        if (!$this->usuario || is_null($permiso)) {
            return false;
        }
        return array_key_exists($permiso, $this->usuario['permisos']);
    }

    //==============================================================================
    // html generation
    //==============================================================================
    function dibujarUserAction($logout_url) {
        if (!$this->usuario) return '';
        $urlActualizar = BASE_URL.'/sso/actualizar';
        $iniciales = strtoupper(substr($this->usuario["nombre"], 0, 1) . substr($this->usuario["apellido"], 0, 1));
        $avatar = empty($this->usuario['imagen'])? $iniciales : $this->usuario['imagen'];
        $nombre = $this->usuario["nombre"].' '.$this->usuario["apellido"];

        return '<div class="user-menu">
            <div class="avatar avatar--border" onclick="openAccountInfo();">
                <div class="avatar-text">' . $avatar . '</div>
            </div>
            <div id="user-account-info" class="account-info">
                <div class="account-info__header">
                    <div class="account-info__avatar avatar">
                        <div class="avatar-text">' . $iniciales . '</div>
                    </div>
                    <div class="account-info__data">
                        <div class="account-info__display-name">' . $nombre . '</div>
                        <div class="account-info__email">' . $this->usuario['email'] . '</div>
                    </div>
                </div>
                <div class="account-info__footer">
                    <a class="btn-action" href="' . $urlActualizar . '">Actualizar Sesión</a>
                    <a class="btn-action" href="' . $logout_url . '">Cerrar Sesión</a>
                </div>
            </div>
        </div>';
    }

}