<?php

class ResponsableV extends Persona {
    private $nroEmpleado;
    private $nroLicencia;
    private $msjOperacion; // imprime errores

    public function __construct() {
        parent::__construct();
        $this->nroEmpleado = '';
        $this->nroLicencia = '';
    }

    public function getNroEmpleado() {
        return $this->nroEmpleado;
    }

    public function setNroEmpleado($numEmpleado) {
        $this->nroEmpleado = $numEmpleado;
    }

    public function getNroLicencia() {
        return $this->nroLicencia;
    }

    public function setNroLicencia($numLicencia) {
        $this->nroLicencia = $numLicencia;
    }

    public function getMsjOperacion() {
        return $this->msjOperacion;
    }

    public function setMsjOperacion($mensajeOperacion) {
        $this->msjOperacion = $mensajeOperacion;
    }

    public function cargar($dni, $name, $apell, $numEmpleado = null, $numLicencia = null) {
        parent::cargar($dni, $name, $apell);
        $this->setNroEmpleado($numEmpleado);
        $this->setNroLicencia($numLicencia);
    }

    public function buscar($dniEmp) {
        $base = new BaseDatos();
        $consultaResponsable = "SELECT * FROM responsable WHERE rdocumento = " . $dniEmp;
        $resp = false;
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaResponsable)) {
                if ($row2 = $base->Registro()) {
                    parent::buscar($row2['rdocumento']); // buscamos por documento en la tabla persona
                    $this->setNroEmpleado($row2['rnumeroempleado']);
                    $this->setNroLicencia($row2['rnumerolicencia']);
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
/*
    public function listar($condicion = "") {
        $arregloResponsable = null;
        $base = new BaseDatos();
        $consultaResponsable = "SELECT * FROM responsable ";
        if ($condicion != "") {
            $consultaResponsable .= ' WHERE ' . $condicion;
        }
        $consultaResponsable .= " ORDER BY rnumeroempleado ";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaResponsable)) {
                $arregloResponsable = array();
                while ($row2 = $base->Registro()) {
                    $obj = new ResponsableV();
                    $obj->cargar($row2['rdocumento'], null, null, $row2['rnumeroempleado'], $row2['rnumerolicencia']);
                    array_push($arregloResponsable, $obj);
                }
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion($base->getERROR());
        }
        return $arregloResponsable;
    }
*/
    public function listar($condicion = "") { //CON INNER JOIN
        $arregloResponsable = null;
        $base = new BaseDatos();
        $consultaResponsable = "SELECT responsable.*, persona.nombre, persona.apellido FROM responsable 
                                INNER JOIN persona ON responsable.rdocumento = persona.documento";
        if ($condicion != "") {
            $consultaResponsable .= ' WHERE ' . $condicion;
        }
        $consultaResponsable .= " ORDER BY rnumeroempleado ";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaResponsable)) {
                $arregloResponsable = array();
                while ($row2 = $base->Registro()) {
                    $obj = new ResponsableV();
                    $obj->cargar($row2['rdocumento'], $row2['nombre'], $row2['apellido'], $row2['rnumeroempleado'], $row2['rnumerolicencia']);
                    array_push($arregloResponsable, $obj);
                }
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        } else {
            $this->setMsjOperacion($base->getERROR());
        }
        return $arregloResponsable;
    }
    

    public function insertar() {
        $base = new BaseDatos();
        $resp = false;
    
        // Verificar si la inserción en la tabla persona fue exitosa
        if (parent::insertar()) {
            // Verificar si el número de empleado ya existe
            $consultaVerificacion = "SELECT * FROM responsable WHERE rdocumento = " . $this->getDocumento();
            if ($base->Iniciar() && $base->Ejecutar($consultaVerificacion)) {
                if ($base->Registro()) {
                    $this->setMsjOperacion($base->getERROR());
                    return false;
                }
            }
    
            $consultaInsert = "INSERT INTO responsable (rnumeroempleado, rnumerolicencia, rdocumento) VALUES (" . $this->getNroEmpleado() . ", " . $this->getNroLicencia() . ", " . $this->getDocumento() . ")";
    
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
            $this->setMsjOperacion(parent::getMsjOperacion());
        }
    
        return $resp;
    }

    public function modificar() {
        $resp = false;
        $base = new BaseDatos();
        
        if (parent::modificar()) {
            // Verificar si el número de empleado ya existe y pertenece a otro documento
            $consultaVerificacion = "SELECT * FROM responsable WHERE rnumeroempleado = " . $this->getNroEmpleado() . " AND rdocumento != " . $this->getDocumento();
            if ($base->Iniciar() && $base->Ejecutar($consultaVerificacion)) {
                if ($base->Registro()) {
                    $this->setMsjOperacion("El número de empleado ya existe para otro documento.");
                    return false;
                }
            }
    
            $consultaUpdate = "UPDATE responsable SET rnumerolicencia='" . $this->getNroLicencia() . "', rnumeroempleado='" . $this->getNroEmpleado() . "' WHERE rdocumento=" . $this->getDocumento();
    
            if ($base->Iniciar()) {
                if ($base->Ejecutar($consultaUpdate)) {
                    $resp = true;
                } else {
                    $this->setMsjOperacion($base->getERROR());
                }
            } else {
                $this->setMsjOperacion($base->getERROR());
            }
        }
    
        return $resp;
    }

    public function eliminar() {
        $base = new BaseDatos();
        $resp = false;
        if ($base->Iniciar()) {
            $consultaDelete = "DELETE FROM responsable WHERE rdocumento=" . $this->getDocumento();
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

    public function __toString() {
        return parent::__toString() . "\nNúmero de Empleado: " . $this->getNroEmpleado() . "\nNúmero de Licencia: " . $this->getNroLicencia() . "\n";
    }
}
