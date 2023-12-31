<?php

class SessionHandlerService extends Service {
    private $sessionKey = 'usuario';

    public function __construct($container) {
        parent::__construct($container);
    }

    public function setSesionKey($sessionKey = null) {
        if (!empty($sessionKey) && is_string($sessionKey))
            $this->sessionKey = $sessionKey;
        else
            $this->sessionKey = 'usuario';
    }

    public function existeSesion() {
        return isset($_SESSION[$this->sessionKey]);
    }

    public function getSesion() {
        if (!$this->existeSesion()) return null;
        return $_SESSION[$this->sessionKey];
    }

    public function setSesion($data) {
        $_SESSION[$this->sessionKey] = $data;
    }

    public function deleteSesion() {
        $session = $this->getSesion();
        if(!empty($session)) unset($_SESSION[$this->sessionKey]);
        return $session;
    }

    // ========================================================================
    // METADATA
    // ========================================================================
    public function getSesionMetadata($key = null) {
        $sesion = $this->getSesion();
        if (!$sesion || !isset($sesion['metadata'])) return null;

        // si no se solicito un dato especifico devolvemos todo el arreglo
        if (is_null($key)) return $sesion['metadata'];

        // si el dato no existe devolvemos null
        if (!isset($sesion['metadata'][$key])) return null;

        return $sesion['metadata'][$key];
    }

    public function setSesionMetadata($key, $content) {
        $sesion = $this->getSesion();
        if (!$sesion) return false;

        $metadata = [];
        if (isset($sesion['metadata']))
            $metadata = $sesion['metadata'];

        $metadata[$key] = $content;

        $_SESSION[$this->sessionKey]['metadata'] = $metadata;
        return true;
    }

    public function deleteSesionMetadata($key) {
        $sesion = $this->getSesion();
        if (!$sesion || !isset($sesion['metadata'])) return;

        unset($sesion['metadata'][$key]);
        $this->setSesion($sesion);
    }

    // ========================================================================
    // CACHE
    // ========================================================================
    public function getSesionCache($key = null) {
        $sesion = $this->getSesion();
        if (!$sesion || !isset($sesion['cache'])) return null;

        // si no se solicito un dato especifico devolvemos todo el arreglo
        if (is_null($key)) return $sesion['cache'];

        // si el dato no existe devolvemos null
        if (!isset($sesion['cache'][$key])) return null;

        return $sesion['cache'][$key];
    }

    public function setSesionCache($key, $content) {
        $sesion = $this->getSesion();
        if (!$sesion) return;

        $cache = [];
        if (isset($sesion['cache']))
            $cache = $sesion['cache'];

        $cache[$key] = $content;

        $_SESSION[$this->sessionKey]['cache'] = $cache;
    }

    public function deleteSesionCache($key) {
        $sesion = $this->getSesion();
        if (!$sesion || !isset($sesion['cache'])) return;

        unset($sesion['cache'][$key]);
        $this->setSesion($sesion);
    }

    public function cleanSesionCache() {
        $sesion = $this->getSesion();
        if (!$sesion || !isset($sesion['cache'])) return;

        unset($sesion['cache']);
        $this->setSesion($sesion);
    }

    // =====================================================================
    // TOKEN
    // =====================================================================

    public function getToken() {
        $sesion = $_SESSION;
        if (!$sesion || !isset($sesion['TOKEN'])) return null;
        return $sesion['TOKEN'];
    }

    public function setToken($content) {
        $_SESSION['TOKEN'] = $content;
    }

    public function cleanToken() {
        if(isset($_SESSION['TOKEN']))
            unset($_SESSION['TOKEN']);
    }
}