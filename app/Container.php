<?php

use Psr\Container\ContainerInterface;

abstract class Container {

    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function has($key) {
        return $this->container->has($key);
    }

    public function get($key) {
        return $this->container->get($key);
    }

    public function getLogger() {
        return $this->container->get('logger');
    }

    /**
     * @return UsuarioService
     */
    public function getUsuario() {
        if (!$this->has("service.usuario.Usuario")) {
            return null;
        }
        return $this->get("service.usuario.Usuario");
    }

    public function fetch($vista, $data = array()) {
        if (!is_null($data['APP_usuario'])) {
            throw new Exception("Template key 'APP_usuario' es una palabra reservada por el framework.");
        }
        if (!is_null($data['APP_permisos'])) {
            throw new Exception("Template key 'APP_permisos' es una palabra reservada por el framework.");
        }
        return $this->container->view->fetch($vista . '.twig', $data);
    }

    public function render($response, $vista, $data = array()) {
        if (!is_null($data['APP_usuario'])) {
            throw new Exception("Template key 'APP_usuario' es una palabra reservada por el framework.");
        }
        if (!is_null($data['APP_permisos'])) {
            throw new Exception("Template key 'APP_permisos' es una palabra reservada por el framework.");
        }
        if (!is_null($data['APP_templateName'])) {
            throw new Exception("Template key 'APP_templateName' es una palabra reservada por el framework.");
        }
        $templateName = $vista . '.twig';
        if (DEBUG) {
            $this->container->view->offsetSet("templateName", $templateName);
        }
        return $this->container->view->render($response, $templateName, $data);
    }

    public function injectScript(string $script) {
        $this->container->view->injectScript($script);
    }

    public function injectFetchedScript($twigName, $data = array()){
        $this->container->view->injectFetchedScript($twigName, $data);
    }

    protected function logException($exception) {
        $ctx = method_exists($exception, 'getMetadata') ? $exception->getMetadata() : [];
        $this->getLogger()->error($exception->getMessage(), $ctx);
    }

    public function httpGet($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->get($url, $options);
    }
    public function httpHead($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->head($url, $options);
    }
    public function httpPut($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->put($url, $options);
    }
    public function httpPost($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->post($url, $options);
    }
    public function httpPatch($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->patch($url, $options);
    }
    public function httpDelete($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->delete($url, $options);
    }

    public function httpGetAsync($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->getAsync($url, $options);
    }
    public function httpHeadAsync($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->headAsync($url, $options);
    }
    public function httpPutAsync($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->putAsync($url, $options);
    }
    public function httpPostAsync($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->postAsync($url, $options);
    }
    public function httpPatchAsync($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->patchAsync($url, $options);
    }
    public function httpDeleteAsync($url, $options = array()) {
        $client = $this->getHttpClient();
        return $client->deleteAsync($url, $options);
    }


    private function getHttpClient() {
        $timeout = defined("HTTP_CLIENT_TIMEOUT") ? HTTP_CLIENT_TIMEOUT : 5;
        if (is_null($this->httpClient)) {
            $this->httpClient = new \GuzzleHttp\Client(array(
                'connect_timeout' => $timeout, // segundos
                'timeout'         => $timeout, // segundos
                'allow_redirects' => true
            ));
        }
        return $this->httpClient;
    }
}
