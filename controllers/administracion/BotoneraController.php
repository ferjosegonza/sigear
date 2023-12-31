<?php

class BotoneraController extends Controller {

    public function __construct($container) {
        parent::__construct($container);
    }

    public function renderBotoneraAdministracion(Request $request, Response $response, $args) {
        $data = array(
            'portada' => false,
            'botonera' => $this->filtrarBotonera(APP_BOTONERA_ADMINISTRACION),
            'solapaNombre'=>'ADMINISTRACION',
        );
        $this->render($response, 'layout/botonera', $data);
    }

    public function renderBotoneraFondos(Request $request, Response $response, $args) {
        $data = array(
            'portada' => false,
            'botonera' => $this->filtrarBotonera(APP_BOTONERA),
            'solapaNombre'=>'FONDOS',
        );
        $this->render($response, 'layout/botonera', $data);
    }

    public function renderBotoneraMDE(Request $request, Response $response, $args) {
        $data = array(
            'portada' => false,
            'botonera' => $this->filtrarBotonera(APP_BOTONERA),
            'solapaNombre'=>'MESA DE ENTRADA',
        );
       // $data['botonera'][0]['botones'] = $this->filtrarBotonera(APP_BOTONERA)[0]['botones'];

        $this->render($response, 'layout/botonera', $data);
    }
/*
    public function renderBotoneraFormularios(Request $request, Response $response, $args) {
        $data = array(
            'portada' => false,
            'botonera' => $this->filtrarBotonera(APP_BOTONERA_FORMULARIOS),
            'solapaNombre'=>'FORMULARIOS DE PEDIDOS',
        );
        $this->render($response, 'layout/botonera', $data);
    } */

}
