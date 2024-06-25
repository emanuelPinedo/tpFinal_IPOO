<?php

include_once 'BaseDatos.php';
include_once 'Persona.php';
include_once 'Empresa.php';
include_once 'ResponsableV.php';
include_once 'Viaje.php';
include_once 'Pasajero.php';

$salir = false;

do {
  echo "***********************************\n";
  echo "|   Bienvenido                    |\n";
  echo "| Elija una sección para acceder  |\n";
  echo "|   1. Empresa                    |\n";
  echo "|   2. Responsable                |\n";
  echo "|   3. Viaje                      |\n";
  echo "|   4. Pasajero                   |\n";
  echo "|   5. Salir                      |\n";
  echo "***********************************\n";

  $opcion = trim(fgets(STDIN));

  switch ($opcion) {
    case 1:
      menuDeEmpresa();
      break;

    case 2:
      menuDeResponsableV();
      break;

    case 3:
      menuDeViaje();
      break;

    case 4:
      menuDePasajero();
      break;

    case 5:
      $salir = true;
      break;

    default:
      echo "Opción no válida \n";
      break;
  }
} while (!$salir);

echo "Usted ha salido del menú de opciones.\n";

function menuDeEmpresa(){
    do {
        echo "************************************\n";
        echo "|Usted accedió a la sección Empresa|\n";
        echo "| Elija una opción                 |\n";
        echo "|   1. Agregar                     |\n";
        echo "|   2. Modificar                   |\n";
        echo "|   3. Eliminar                    |\n";
        echo "|   4. Buscar                      |\n";
        echo "|   5. Listar                      |\n";
        echo "|   6. Volver al menú              |\n";
        echo "************************************\n";

        $opcionEmpr = trim(fgets(STDIN));
        $salirMenu = false;

        switch($opcionEmpr){
            case 1:
                echo "OPCIÓN AGREGAR EMPRESA\n";
                echo "Ingrese el nombre de la empresa: \n";
                $nombreEmp = trim(fgets(STDIN));
                echo "Ingrese la dirección de la empresa: \n";
                $direcEmp = trim(fgets(STDIN));
                //Creo el objeto
                $objEmp = new Empresa();
                
                $verificarNombre = "enombre LIKE '%" . $nombreEmp . "%'";
                //Si el nombre ingresado ya existe no podrá agregarlo.
                $colecEmp = $objEmp->listar($verificarNombre);
                if(!empty($colecEmp)){
                    echo "Este nombre ya esta en uso en otra empresa: \n";
                    foreach ($colecEmp as $emp){
                        echo $emp . "\n";//Le muestro la empresa en uso
                    }
                } else {
                    //Si el nombre no esta en uso encontes lo cargo
                    $objEmp->cargar(0, $nombreEmp, $direcEmp);
                    if($objEmp->insertar()) {
                        echo "Su Empresa ha sido agregada.\n";
                    } else {
                        echo "Ha habido un error al cargar su Empresa.\n";
                    }
                }
                break;
            case 2:
                echo "OPCIÓN MODIFICAR EMPRESA\n";
                echo "Ingrese ID de la Empresa a modificar: \n";
                $idEmp = trim(fgets(STDIN));
                $objEmp = new Empresa();
                if ($objEmp->buscar($idEmp)) {//Busco le empresa con el id ingresado
                    echo "Empresa encontrada: \n";
                    echo $objEmp . "\n";
                    echo "Ingrese el nuevo nombre para la Empresa: \n";
                    $nombreNuevo = trim(fgets(STDIN));
                    echo "Ingrese la nueva dirección para la Empresa: \n";
                    $direcNueva = trim(fgets(STDIN));

                    if(!empty($nombreNuevo)) {
                        $objEmp->setNombre($nombreNuevo);
                    }
                    if(!empty($direcNueva)) {
                        $objEmp->setDireccion($direcNueva);
                    }
                    if ($objEmp->modificar()) {
                        echo "Su Empresa ha sido modificada.\n";
                    } else {
                        echo "Ha habido un error al modificar su Empresa.\n";
                    }
                } else {
                    echo "Su Empresa no existe.\n";
                }
                break;
            case 3:
                echo "OPCIÓN ELIMINAR EMPRESA\n";
                echo "Ingrese ID de la Empresa a eliminar: \n";
                $idEmp = trim(fgets(STDIN));

                $objEmp = new Empresa();
                if($objEmp->buscar($idEmp)){
                    if($objEmp->eliminar()) {
                        echo "Su Empresa ha sido eliminada.\n";
                    } else {
                        echo "Ha habido un error al eliminar su Empresa.\n";
                    }
                }
                break;
            case 4:
                echo "OPCIÓN BUSCAR EMPRESA\n";
                echo "Ingrese ID de la Empresa a buscar: \n";
                $idEmp = trim(fgets(STDIN));

                $objEmp = new Empresa();
                if($objEmp->buscar($idEmp)){
                    echo "Su Empresa ha sido encontrada: \n";
                    echo $objEmp . "\n";
                } else {
                    echo "El ID es Incorrecto o la Empresa no existe.\n";
                }
                break;
            case 5:
                echo "OPCIÓN LISTAR EMPRESA\n";
                $objEmp = new Empresa();
                $empLista = $objEmp->listar();
                $msj = "";
                if($empLista != null){
                    foreach($empLista as $emp){
                        $msj .= $emp . "\n";
                    }
                    echo $msj;
                } else {
                    echo "No hay Empresas cargadas.\n";
                }
                break;
            case 6:
                $salirMenu = true;
                break;
            default:
            echo "Esta Opción no existe. \n";
            break;
        }
    } while (!$salirMenu);
}

function menuDeViaje(){
    do {
        echo "************************************\n";
        echo "| Usted accedió a la sección Viaje |\n";
        echo "| Elija una opción                 |\n";
        echo "|   1. Agregar                     |\n";
        echo "|   2. Modificar                   |\n";
        echo "|   3. Eliminar                    |\n";
        echo "|   4. Buscar                      |\n";
        echo "|   5. Listar viajes               |\n";
        echo "|   6. Listar pasajeros del viaje  |\n";
        echo "|   7. Volver al menú              |\n";
        echo "************************************\n";

        $opcionViaje = trim(fgets(STDIN));
        $salirMenu = false;

        switch($opcionViaje){
            case 1:
                echo "OPCIÓN AGREGAR Viaje\n";
                echo "Ingrese el destino del Viaje: \n";
                $destinoViaje = trim(fgets(STDIN));
                echo "Ingrese la capacidad max. de pasajeros del Viaje: \n";
                $cantMax = trim(fgets(STDIN));
                echo "Ingrese ID de la Empresa: \n";
                $idEmp = trim(fgets(STDIN));
                //Creo el objeto de la empresa para el id y lo busco
                $objEmp = new Empresa();
                if(!$objEmp->buscar($idEmp)){
                    echo "Esta Empresa no ha sido encontrada.\n";
                    $objEmp = null;
                }
                echo "Ingrese el DNI de Empleado del Responsable del Viaje: \n";
                $dniEmp = trim(fgets(STDIN));

                // Creo el objeto ResponsableV y lo busco
                $objRespV = new ResponsableV();
                if (!$objRespV->buscar($dniEmp)) {
                    echo "Este Responsable no ha sido encontrado.\n";
                    $objRespV = null; // Reinicializar el objeto si no se encuentra
                }
                echo "Ingrese el Importe a pagar en el Viaje: \n";
                $importe = trim(fgets(STDIN));
                //Creo el objeto viaje
                $objViaje = new Viaje();
                
                $objViaje->cargar(0, $destinoViaje, $cantMax, $objEmp, $objRespV, $importe, []);
                if($objViaje->insertar()) {
                    echo "Su Viaje ha sido agregada.\n";
                } else {
                    echo "Ha habido un error al cargar su Viaje.\n";
                }
                break;
            case 2:
                echo "OPCIÓN MODIFICAR Viaje\n";
                echo "Ingrese ID de la Viaje a modificar: \n";
                $idViaje = trim(fgets(STDIN));

                $objViaje = new Viaje();
                if ($objViaje->buscar($idViaje)) {//Busco le Viaje con el id ingresado
                    echo "Viaje encontrado: \n";
                    echo "Ingrese el nuevo destino de viaje: \n";
                    $destinoViaje = trim(fgets(STDIN));
                    //Evaluamos que si ingresa un destino como vacio, el destino seguirá siendo el mismo
                    if($destinoViaje == ''){
                        $destinoViaje = $objViaje->getVDestino();
                    }

                    echo "Ingrese la nueva cantidad max. de Pasajeros en el Viaje: \n";
                    $cantMax = trim(fgets(STDIN));
                    //Hago lo mismo que hice con el destino :v

                    echo "Ingrese el nuevo ID de Empresa: \n";
                    $idEmp = trim(fgets(STDIN));
                    $objEmp = new Empresa;
                    //Evaluamos que exista la Empresa
                    if($idEmp != "" && !$objEmp->buscar($idEmp)){
                        echo "Esta Empresa no ha sido encontrada. Es probable que no exista.\n";
                    }
                    if($idEmp == ""){
                        $objEmp = $objViaje->getObjEmpresa();
                    }

                    echo "Ingrese el nuevo N° del Responsable del Viaje: \n";
                    $respV = trim(fgets(STDIN));
                    $objRespV = new ResponsableV();
                    //Hago lo mismo que con el id de la empresa
                    if ($respV != "" && !$objRespV->Buscar($respV)) {
                        echo "Esta Responsable no ha sido encontrado. Es probable que no exista.\n";
                        $objRespV = $objViaje->getObjResponsable();
                    }

                    echo "Ingrese el nuevo Importe del Viaje: \n";
                    $importe = trim(fgets(STDIN));
                    if($importe == ""){
                        $importe = $objViaje->getVImporte();
                    }

                    //Cargamos todos los datoides
                    $objViaje->cargar($idViaje, $destinoViaje, $cantMax, $objEmp, $objRespV, $importe);

                    if ($objViaje->modificar()) {
                        echo "Su Viaje ha sido modificado.\n";
                    } else {
                        echo "Ha habido un error al modificar su Viaje.\n";
                    }
                } else {
                    echo "Su Viaje no existe.\n";
                }
                break;
            case 3:
                echo "OPCIÓN ELIMINAR Viaje\n";
                echo "Ingrese ID de la Viaje a eliminar: \n";
                $idViaje = trim(fgets(STDIN));

                $objViaje = new Viaje();
                if($objViaje->buscar($idViaje)){
                    if($objViaje->eliminar()) {
                        echo "Su Viaje ha sido eliminada.\n";
                    } else {
                        echo "Ha habido un error al eliminar su Viaje.\n";
                    }
                }
                break;
            case 4:
                echo "OPCIÓN BUSCAR Viaje\n";
                echo "Ingrese ID de la Viaje a buscar: \n";
                $idViaje = trim(fgets(STDIN));

                $objViaje = new Viaje();
                if($objViaje->buscar($idViaje)){
                    echo "Su Viaje ha sido encontrada: \n";
                    echo $objViaje . "\n";
                } else {
                    echo "El ID es Incorrecto o el Viaje no existe.\n";
                }
                break;
            case 5:
                echo "OPCIÓN LISTAR Viaje\n";
                $objViaje = new Viaje();
                $viajeLista = $objViaje->listar();
                $msj = "";
                if($viajeLista != null){
                    foreach($viajeLista as $viaje){
                        $msj .= $viaje . "\n";
                    }
                    echo $msj;
                } else {
                    echo "No hay Viajes cargados.\n";
                }
                break;
            case 6:
                echo "OPCIÓN LISTAR Pasajeros del Viaje\n";
                echo "Ingrese ID del viaje en el que quiera ver los Pasajeros: \n";
                $idViaje = trim(fgets(STDIN));
                $objViaje = new Viaje();

                if($objViaje->buscar($idViaje)){
                    $objPasaj = new Pasajero();
                    $colecPasajeros = $objPasaj->listar();

                    echo "Viaje con ID: " . $idViaje . "\nLista de Pasajeros: \n";
                    $msj = "";
                    foreach($colecPasajeros as $pasaj){
                        $msj .= "\n" . $pasaj . "\n";
                    }
                    echo $msj;
                } else {
                    echo "Este Viaje no existe o el ID es el incorrecto.";
                }
                break;
            case 7:
                $salirMenu = true;
                break;
            default:
            echo "Esta Opción no existe. \n";
            break;
        }
    } while (!$salirMenu);
}

function menuDeResponsableV() {
    do {
        echo "****************************************\n";
        echo "|Usted accedió a la sección Responsable|\n";
        echo "| Elija una opción                     |\n";
        echo "|   1. Agregar                         |\n";
        echo "|   2. Modificar                       |\n";
        echo "|   3. Eliminar                        |\n";
        echo "|   4. Buscar                          |\n";
        echo "|   5. Listar                          |\n";
        echo "|   6. Volver al menú                  |\n";
        echo "****************************************\n";

        $opcionResp = trim(fgets(STDIN));
        $salirMenu = false;

        switch ($opcionResp) {
            case 1:
                echo "OPCIÓN AGREGAR Responsable\n";
                echo "Ingrese el Nombre del Responsable: \n";
                $nombre = trim(fgets(STDIN));
                echo "Ingrese el Apellido del Responsable: \n";
                $apellido = trim(fgets(STDIN));
                echo "Ingrese el N° de Documento del Responsable: \n";
                $dni = trim(fgets(STDIN));
                echo "Ingrese N° de Licencia: \n";
                $numLic = trim(fgets(STDIN));
                echo "Ingrese N° de Empleado: \n";
                $numEmpleado = trim(fgets(STDIN));
                $objRespV = new ResponsableV();

                $objRespV->cargar($dni, $nombre, $apellido, $numEmpleado, $numLic);
                if ($nombre != "" && $apellido != "" && $numLic != 0) {
                    if ($objRespV->insertar()) {
                        echo "El Responsable ha sido agregado.\n";
                    } else {
                        echo "El Responsable no se pudo agregar.\n";
                        echo "Error al insertar en persona: " . $objRespV->getMsjOperacion() . "\n";
                    }
                } else {
                    echo "Faltan datos.\n";
                }
                break;

            case 2:
                echo "OPCIÓN MODIFICAR Responsable\n";
                echo "Ingrese el DNI de Empleado del Responsable a modificar: \n";
                $dni = trim(fgets(STDIN));
                $objRespV = new ResponsableV();
                if ($objRespV->buscar($dni)) {
                    echo "Ingrese el nuevo Nombre del Responsable (actual: " . $objRespV->getNombre() . "): \n";
                    $nombre = trim(fgets(STDIN));
                    echo "Ingrese el nuevo Apellido del Responsable (actual: " . $objRespV->getApellido() . "): \n";
                    $apellido = trim(fgets(STDIN));
                    echo "Ingrese el nuevo N° de Licencia (actual: " . $objRespV->getNroLicencia() . "): \n";
                    $numLic = trim(fgets(STDIN));
                    echo "Ingrese el nuevo N° de Empleado (actual: " . $objRespV->getNroEmpleado() . "): \n";
                    $numEmpleado = trim(fgets(STDIN));
                    
                    $objRespV->cargar($dni, $nombre, $apellido, $numEmpleado, $numLic);
                    if ($objRespV->modificar()) {
                        echo "El Responsable ha sido modificado.\n";
                    } else {
                        echo "El Responsable no se pudo modificar.\n";
                        echo "Error al modificar en persona: " . $objRespV->getMsjOperacion() . "\n";
                    }
                } else {
                    echo "No se encontró un Responsable con ese N° de Documento.\n";
                }
                break;

            case 3:
                echo "OPCIÓN ELIMINAR Responsable\n";
                echo "Ingrese el N° de Documento del Responsable a eliminar: \n";
                $dni = trim(fgets(STDIN));
                $objRespV = new ResponsableV();
                if ($objRespV->buscar($dni)) {
                    if ($objRespV->eliminar()) {
                        echo "El Responsable ha sido eliminado.\n";
                    } else {
                        echo "El Responsable no se pudo eliminar.\n";
                        echo "Error al eliminar en persona: " . $objRespV->getMsjOperacion() . "\n";
                    }
                } else {
                    echo "No se encontró un Responsable con ese N° de Documento.\n";
                }
                break;

            case 4:
                echo "OPCIÓN BUSCAR Responsable\n";
                echo "Ingrese el N° de Documento del Responsable a buscar: \n";
                $dni = trim(fgets(STDIN));
                $objRespV = new ResponsableV();
                if ($objRespV->buscar($dni)) {
                    echo $objRespV;
                } else {
                    echo "No se encontró un Responsable con ese N° de Documento.\n";
                }
                break;

            case 5:
                echo "OPCIÓN LISTAR Responsables\n";
                $objRespV = new ResponsableV();
                $colResponsables = $objRespV->listar();

                if (count($colResponsables) > 0) {
                    foreach ($colResponsables as $respV) {
                        echo $respV . "\n";
                    }
                } else {
                    echo "No hay Responsables para listar.\n";
                }
                break;

            case 6:
                $salirMenu = true;
                break;

            default:
                echo "Esta Opción no existe. \n";
                break;
        }
    } while (!$salirMenu);
}


function menuDePasajero() {
    do {
        echo "*************************************\n";
        echo "|Usted accedió a la sección Pasajero|\n";
        echo "| Elija una opción                  |\n";
        echo "|   1. Agregar                      |\n";
        echo "|   2. Modificar                    |\n";
        echo "|   3. Eliminar                     |\n";
        echo "|   4. Buscar                       |\n";
        echo "|   5. Listar                       |\n";
        echo "|   6. Volver al menú               |\n";
        echo "*************************************\n";

        $opcionPasaj = trim(fgets(STDIN));
        $salirMenu = false;

        switch($opcionPasaj) {
            case 1:
                echo "OPCIÓN AGREGAR Pasajero\n";
                echo "Ingrese el documento del Pasajero: \n";
                $documento = trim(fgets(STDIN));

                // Verificar si el pasajero ya existe
                $objPasajero = new Pasajero();
                if ($objPasajero->buscar($documento)) {
                    echo "El Pasajero con documento $documento ya existe.\n";
                    break;
                }

                echo "Ingrese el nombre del Pasajero: \n";
                $nombre = trim(fgets(STDIN));
                echo "Ingrese el apellido del Pasajero: \n";
                $apellido = trim(fgets(STDIN));
                echo "Ingrese el teléfono del Pasajero: \n";
                $telefono = trim(fgets(STDIN));
                echo "Ingrese el ID del Viaje al que pertenece el Pasajero: \n";
                $idViaje = trim(fgets(STDIN));

                // Verificar si el viaje existe
                $objViaje = new Viaje();
                if (!$objViaje->buscar($idViaje)) {
                    echo "El Viaje con ID $idViaje no existe.\n";
                    break;
                } elseif(!$objViaje->pasajeDisponible()) {
                    echo "No hay más pasajes disponibles\n";
                    break;
                } else {
                    // Crear el objeto Pasajero y cargar los datos
                $objPasajero->cargar($documento, $nombre, $apellido, $idViaje, $telefono);

                // Insertar el pasajero en la base de datos
                if ($objPasajero->insertar()) {
                    echo "El Pasajero ha sido agregado correctamente.\n";
                } else {
                    echo "Error al agregar el Pasajero.\n";
                    echo "Mensaje de error: " . $objPasajero->getMsjOperacion() . "\n";
                }
                break;
                }

            case 2:
                echo "OPCIÓN MODIFICAR Pasajero\n";
                echo "Ingrese el documento del Pasajero que desea modificar: \n";
                $documento = trim(fgets(STDIN));

                // Verificar si el pasajero existe
                $objPasajero = new Pasajero();
                if (!$objPasajero->buscar($documento)) {
                    echo "El Pasajero con documento $documento no existe.\n";
                    break;
                }

                echo "Ingrese el nuevo nombre del Pasajero (dejar en blanco para mantener el actual): \n";
                $nombre = trim(fgets(STDIN));
                echo "Ingrese el nuevo apellido del Pasajero (dejar en blanco para mantener el actual): \n";
                $apellido = trim(fgets(STDIN));
                echo "Ingrese el nuevo teléfono del Pasajero (dejar en blanco para mantener el actual): \n";
                $telefono = trim(fgets(STDIN));

                // Modificar el pasajero en la base de datos
                $objPasajero->setNombre($nombre);
                $objPasajero->setApellido($apellido);
                $objPasajero->setTelefono($telefono);

                if ($objPasajero->modificar()) {
                    echo "El Pasajero ha sido modificado correctamente.\n";
                } else {
                    echo "Error al modificar el Pasajero.\n";
                    echo "Mensaje de error: " . $objPasajero->getMsjOperacion() . "\n";
                }
                break;

            case 3:
                echo "OPCIÓN ELIMINAR Pasajero\n";
                echo "Ingrese el documento del Pasajero que desea eliminar: \n";
                $documento = trim(fgets(STDIN));

                // Verificar si el pasajero existe
                $objPasajero = new Pasajero();
                if (!$objPasajero->buscar($documento)) {
                    echo "El Pasajero con documento $documento no existe.\n";
                    break;
                }

                // Eliminar el pasajero de la base de datos
                if ($objPasajero->eliminar()) {
                    echo "El Pasajero ha sido eliminado correctamente.\n";
                } else {
                    echo "Error al eliminar el Pasajero.\n";
                    echo "Mensaje de error: " . $objPasajero->getMsjOperacion() . "\n";
                }
                break;

            case 4:
                echo "OPCIÓN BUSCAR Pasajero\n";
                echo "Ingrese el documento del Pasajero que desea buscar: \n";
                $documento = trim(fgets(STDIN));

                // Buscar el pasajero en la base de datos
                $objPasajero = new Pasajero();
                if ($objPasajero->buscar($documento)) {
                    echo "Información del Pasajero:\n";
                    echo $objPasajero;
                } else {
                    echo "El Pasajero con documento $documento no fue encontrado.\n";
                }
                break;

            case 5:
                echo "OPCIÓN LISTAR Pasajeros\n";

                // Listar todos los pasajeros
                $objPasajero = new Pasajero();
                $listaPasajeros = $objPasajero->listar();

                if (!empty($listaPasajeros)) {
                    echo "Lista de Pasajeros:\n";
                    foreach ($listaPasajeros as $pasajero) {
                        echo $pasajero;
                        echo "----------------\n";
                    }
                } else {
                    echo "No se encontraron pasajeros.\n";
                }
                break;

            case 6:
                $salirMenu = true;
                break;

            default:
                echo "Esta Opción no existe. \n";
                break;
        }

    } while (!$salirMenu);
}