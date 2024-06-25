<?php

class Pasajero extends Persona{
	private $telefono; 
	private $objViaje;
	private $msjOperacion;//imprime errores

	public function __construct() {
		parent::__construct();
		$this->telefono = '';
		$this->objViaje = null;
	}

	public function getTelefono() {
		return $this->telefono;
	}

	public function setTelefono($tel) {
		$this->telefono = $tel;
	}

	public function getObjViaje() {
		return $this->objViaje;
	}

	public function setObjViaje($viaje) {
		$this->objViaje = $viaje;
	}

	public function getMsjOperacion() {
		return $this->msjOperacion;
	}

	public function setMsjOperacion($mensajeOperacion) {
		$this->msjOperacion = $mensajeOperacion;
	}

	public function cargar($dni, $name, $apell, $viaje = null, $tel = null)
	{
		parent::cargar($dni,$name,$apell);
		$this->setObjViaje($viaje);
		$this->setTelefono($tel);
	}

	//Funcion para realizar Consultas
	public function buscar($dni) {
		$base = new BaseDatos();
		$consultaPasajero = "SELECT * FROM pasajero WHERE pdocumento = " . $dni;
		$resp = false;
		if ($base->Iniciar()) {
			if ($base->Ejecutar($consultaPasajero)) {
				if ($row2 = $base->Registro()) {
					parent::buscar($dni);
					$this->setTelefono($row2['ptelefono']);//con los [] accedemos a los datos
					$this->setObjViaje($row2['idviaje']);
					$resp = true;
				}
			} else {
				$this->setMsjOperacion($base->getERROR());
			}
		} else {
			$this->setMsjOperacion($base->getERROR());
		}
		return $resp;
	}

    public function listar($condicion = "") {
        $arregloPasajero = null;
        $base = new BaseDatos();
        $consultaPasajero = "SELECT * FROM pasajero";
        if ($condicion != "") {
            $consultaPasajero .= " WHERE " . $condicion;
        }
        $consultaPasajero .= " ORDER BY pdocumento";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaPasajero)) {
                $arregloPasajero = array();
                while ($row2 = $base->Registro()) {
                    /*$PerDocumento = $row2['pdocumento'];
                    $Nombre = $row2['nombre'];
                    $Apellido = $row2['apellido'];
                    $PTelefono = $row2['ptelefono'];
                    $IdViaje = $row2['idviaje'];

                    $pasaj = new Pasajero();
                    $pasaj->cargar($PerDocumento,$Nombre,$Apellido,$ptelefono,$idviaje);
                    array_push($arregloPasajero,$pasaj);*/
                    $obj = new Pasajero();
                    $obj->cargar($row2['pdocumento'], null, null, $row2['idviaje'], $row2['ptelefono']);
                    array_push($arregloPasajero, $obj);
                }
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion($base->getERROR());
        }
        return $arregloPasajero;
    }

    /*public function listar($condicion = "") {//CON INNER JOIN
        $arregloPasajero = null;
        $base = new BaseDatos();
        $consultaPasajero = "SELECT p.*, per.nombre, per.apellido 
                             FROM pasajero p 
                             INNER JOIN persona per ON p.pdocumento = per.documento";
        if ($condicion != "") {
            $consultaPasajero .= " WHERE " . $condicion;
        }
        $consultaPasajero .= " ORDER BY p.pdocumento";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaPasajero)) {
                $arregloPasajero = array();
                while ($row2 = $base->Registro()) {
                    $obj = new Pasajero();
                    $obj->cargar($row2['pdocumento'], $row2['nombre'], $row2['apellido'], $row2['idviaje'], $row2['ptelefono']);
                    array_push($arregloPasajero, $obj);
                }
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion($base->getERROR());
        }
        return $arregloPasajero;
    }*/

	//Funcion para añadir datos
    public function insertar() {
        $base = new BaseDatos();
        $resp = false;
        if (parent::insertar()) {
            $consultaInsert = "INSERT INTO pasajero(pdocumento, ptelefono, idviaje) VALUES 
            (" . $this->getDocumento() . ", '" . $this->getTelefono() . "', " . $this->getObjViaje() . ")";
        }
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaInsert)) {
                $resp = true;
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion($base->getERROR());
        }
        return $resp;
    }

	//Funcion para modificar la BD según el documento de la persona
    public function modificar() {
        $resp = false;
        $base = new BaseDatos();
        if (parent::modificar()) {
            $consultaUpdate = "UPDATE pasajero SET ptelefono = '" . $this->getTelefono() . "', idviaje = '" . $this->getObjViaje() . "' WHERE pdocumento = '" . $this->getDocumento() . "'";
        }
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaUpdate)) {
                $resp = true;
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion($base->getERROR());
        }
        return $resp;
    }

	//Funcion para eliminar un viaje de la BD según el documento de la persona
    public function eliminar() {
        $base = new BaseDatos();
        $resp = false;
        if ($base->Iniciar()) {
            $consultaDelete = "DELETE FROM pasajero WHERE pdocumento = " . $this->getDocumento();
            if ($base->Ejecutar($consultaDelete)) {
                if (parent::eliminar()) {
                    $resp = true;
                }
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion($base->getERROR());
        }
        return $resp;
    }

	public function __toString(){
		return /*parent::__toString() .*/  
		"\nTelefono: " . $this->getTelefono() . 
		"\nId del Viaje: " . $this->getObjViaje() . "\n";
	}

}