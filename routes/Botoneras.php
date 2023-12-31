<?php

    $app->any('/administracion-botonera',       'controller.administracion.Botonera:renderBotoneraAdministracion')->add(requierePermiso('Administracion'))
    ->add(requiereUsuario());
    $app->any('/fondos-botonera',               'controller.administracion.Botonera:renderBotoneraFondos')->add(requierePermiso('Fondos'))
    ->add(requiereUsuario());
    $app->any('/mde-botonera',               'controller.administracion.Botonera:renderBotoneraMDE')->add(requierePermiso('Permisos'))
    ->add(requiereUsuario());


