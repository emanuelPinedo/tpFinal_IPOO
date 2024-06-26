<?php

class Viaje{
    private $idViaje; 
	private $vDestino;
    private $vCantMaxPasajeros;
	private $objEmpresa;//id empresa 
    private $objResponsable;//
    private $vImporte;
    private $colPasajeros;
    private $msjOperacion;

    public function __construct(){
        $this->idViaje = 0;
        $this->vDestino = '';
        $this->vCantMaxPasajeros = 0;
        $this->objEmpresa = new Empresa();
        $this->objResponsable = new ResponsableV();
        $this->vImporte = 0;
        $this->colPasajeros = [];
    }

	public function getIdViaje() {
		return $this->idViaje;
	}

	public function setIdViaje($id) {
		$this->idViaje = $id;
	}

	public function getVDestino() {
		return $this->vDestino;
	}

	public function setVDestino($destino) {
		$this->vDestino = $destino;
	}

	public function getVCantMaxPasajeros() {
		return $this->vCantMaxPasajeros;
	}

	public function setVCantMaxPasajeros($cantMaxPasaj) {
		$this->vCantMaxPasajeros = $cantMaxPasaj;
	}

	public function getObjEmpresa(){
		return $this->objEmpresa;
	}

	public function setObjEmpresa($objEmp) {
		$this->objEmpresa = $objEmp;
	}

	public function getObjResponsable() {
		return $this->objResponsable;
	}

	public function setObjResponsable($objResp) {
		$this->objResponsable = $objResp;
	}

	public function getVImporte() {
		return $this->vImporte;
	}

	public function setVImporte($impo) {
		$this->vImporte = $impo;
	}

	public function getColPasajeros() {
		return $this->colPasajeros;
	}

	public function setColPasajeros($colePasaj) {
		$this->colPasajeros = $colePasaj;
	}

	public function getMsjOperacion() {
		return $this->msjOperacion;
	}

	public function setMsjOperacion($mensajeOperacion) {
		$this->msjOperacion = $mensajeOperacion;
	}

    public function cargar($id, $destino, $cantMaxPasaj, $objEmp, $objResp, $impo){
        $this->setIdViaje($id);
        $this->setVDestino($destino);
        $this->setVCantMaxPasajeros($cantMaxPasaj);
        $this->setObjEmpresa($objEmp);
        $this->setObjResponsable($objResp);
        $this->setVImporte($impo);
    }

    public function pasajeDisponible() {
        $this->actualizarPasajeros(); // Asegurarse de que la colección de pasajeros esté actualizada
        $res = false;
        $cantPasajero = count($this->getColPasajeros());
        if ($this->getVCantMaxPasajeros() > $cantPasajero) {
            $res = true;
        }
        return $res;
    }
    
    public function actualizarPasajeros() {
        $base = new BaseDatos();
        $consultaPasajeros = "SELECT * FROM pasajero WHERE idViaje = " . $this->getIdViaje();
        $this->colPasajeros = [];
    
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaPasajeros)) {
                while ($row = $base->Registro()) {
                    $pasajero = new Pasajero();
                    $documento = isset($row['pdocumento']) ? $row['pdocumento'] : '';
                    $nombre = isset($row['nombre']) ? $row['nombre'] : '';
                    $apellido = isset($row['apellido']) ? $row['apellido'] : '';
                    $telefono = isset($row['ptelefono']) ? $row['ptelefono'] : '';
    
                    $pasajero->cargar($documento, $nombre, $apellido, $this->getIdViaje(), $telefono);
                    array_push($this->colPasajeros, $pasajero);
                }
            }
        }
    }
    
    //Funcion para realizar Consultas
    public function buscar($id) {
        $base = new BaseDatos();
        $consultaViaje = "SELECT * FROM viaje WHERE idViaje = " . $id;
        $resp = false;
        if($base->Iniciar()){
            if($base->Ejecutar($consultaViaje)){
                if($row2 = $base->Registro()){
                    $this->setIdViaje($id);
                    $this->setVDestino($row2['vdestino']);
                    $this->setVCantMaxPasajeros($row2['vcantmaxpasajeros']);
                    
                    // Buscar y asignar el objeto Empresa
                    $objEmpresa = new Empresa();
                    $objEmpresa->buscar($row2['idempresa']);
                    $this->setObjEmpresa($objEmpresa);
                    
                    // Buscar y asignar el objeto Responsable
                    $objResponsable = new ResponsableV();
                    if ($objResponsable->buscar($row2['rdocumento'])) {
                        $this->setObjResponsable($objResponsable);
                    } else {
                        $this->setMsjOperacion("Este Responsable no ha sido encontrado.");
                    }
                    
                    $this->setVImporte($row2['vimporte']);
                    
                    // Cargar la colección de pasajeros del viaje
                    $this->cargarPasajeros();
                    
                    $resp = true;
                } else {
                    $this->setMsjOperacion("No se encontró ningún viaje con el ID especificado.");
                }
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion($base->getERROR());
        }
        return $resp;
    }
    
    // Método para cargar la colección de pasajeros de un viaje
    private function cargarPasajeros() {
        $base = new BaseDatos();
        $consultaPasajeros = "SELECT p.pdocumento, per.nombre, per.apellido, p.ptelefono 
                              FROM pasajero AS p
                              INNER JOIN persona AS per ON p.pdocumento = per.documento
                              WHERE p.idviaje = " . $this->getIdViaje();
        
        if($base->Iniciar()){
            if($base->Ejecutar($consultaPasajeros)){
                $pasajeros = array();
                while($row = $base->Registro()){
                    $objPasajero = new Pasajero();
                    $objPasajero->cargar($row['pdocumento'], $row['nombre'], $row['apellido'], $this->getIdViaje(), $row['ptelefono']);
                    array_push($pasajeros, $objPasajero);
                }
                $this->setColPasajeros($pasajeros);
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion($base->getERROR());
        }
    }
    
    public function listar($condicion = "") {
        $arregloViaje = null;
        $base = new BaseDatos();
        $consultaViaje = "SELECT * FROM viaje ";
        if ($condicion != "") {
            $consultaViaje .= ' WHERE ' . $condicion;
        }
        $consultaViaje .= " ORDER BY vdestino ";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaViaje)) {
                $arregloViaje = array();
                while ($row2 = $base->Registro()) {
                    $IdViaje = $row2['idviaje'];
                    $Destino = $row2['vdestino'];
                    $CantMaxPas = $row2['vcantmaxpasajeros'];
                    $IdEmpresa = $row2['idempresa'];
                    $RNumeroEmpleado = $row2['rdocumento'];
                    $VImporte = $row2['vimporte'];
    
                    // Crear objetos Empresa y ResponsableV
                    $objEmpresa = new Empresa();
                    $objEmpresa->buscar($IdEmpresa);
                    
                    $objResponsable = new ResponsableV();
                    $objResponsable->buscar($RNumeroEmpleado);
    
                    $viaje = new Viaje();
                    $viaje->cargar($IdViaje, $Destino, $CantMaxPas, $objEmpresa, $objResponsable, $VImporte);
                    array_push($arregloViaje, $viaje);
                }
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion($base->getERROR());
        }
        return $arregloViaje;
    }    

    //Funcion para añadir datos
    public function insertar() {
        $base = new BaseDatos();
        $resp = false;
    
        // Verifica que los objetos estén correctamente inicializados
        if ($this->getObjEmpresa() != null && $this->getObjResponsable() != null) {
            // Verifica también la disponibilidad de pasajes antes de insertar
            if (!$this->pasajeDisponible()) {
                $this->setMsjOperacion("No hay más pasajes disponibles para este viaje.");
                return false;
            }
    
            $consultaInsert = "INSERT INTO viaje(vdestino, vcantmaxpasajeros, idempresa, rdocumento, vimporte) 
                               VALUES ('" . $this->getVDestino() . "', " . $this->getVCantMaxPasajeros() . ", " . $this->getObjEmpresa()->getIdEmpresa() . ", " . $this->getObjResponsable()->getDocumento() . ", " . $this->getVImporte() . ")";
            if ($base->Iniciar()) {
                if ($base->Ejecutar($consultaInsert)) {
                    $resp = true;
                } else {
                    $this->setMsjOperacion($base->getERROR());
                }
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion("Los objetos Empresa o Responsable no están inicializados correctamente.");
        }
    
        return $resp;
    }
    

    //Funcion para modificar la BD según el id del viaje
    public function modificar(){
        $resp = false;
        $base = new BaseDatos();
        $consultaUpdate = "UPDATE viaje SET vdestino='" . $this->getVDestino() . "', vcantmaxpasajeros='" . $this->getVCantMaxPasajeros() . "', idempresa='" . $this->getObjEmpresa()->getIdEmpresa() . "', rdocumento='" . $this->getObjResponsable()->getDocumento() . "', vimporte='" . $this->getVImporte() . "' WHERE idviaje=" . $this->getIdViaje();
        
        if($base->Iniciar()){
            if($base->Ejecutar($consultaUpdate)){
                $resp = true;
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion($base->getERROR());
        }
        return $resp;
    }

    //Funcion para eliminar un viaje de la BD según el id del viaje
    public function eliminar(){
		$base=new BaseDatos();
		$resp=false;
		if($base->Iniciar()){
				$consultaDelete="DELETE FROM viaje WHERE idviaje=".$this->getIdViaje();
				if($base->Ejecutar($consultaDelete)){
				    $resp=  true;
				}else{
						$this->setMsjOperacion($base->getERROR());
					
				}
		}else{
				$this->setMsjOperacion($base->getERROR());
			
		}
		return $resp; 
	}

    public function recorrerArray($array){
        $msj = "";
        foreach($array as $obj){
            $msj = $msj . " " . $obj . "\n";
        }
        return $msj;
    }

    public function __toString(){
       return "Id de Viaje: " . $this->getIdViaje() . 
       "\nDestino: " . $this->getVDestino() . 
       "\nCantidad Maxima de Pasajeros: " . $this->getVCantMaxPasajeros() . 
       "\nEmpresa: " . $this->getObjEmpresa() . 
       "\nResponsable: " . $this->getObjResponsable() . 
       "\nPasajeros: " . $this->recorrerArray($this->getColPasajeros()) . "\n";
    }
}