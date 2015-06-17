<?php

/**
 * Clase Gastos Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.11.02
 * @copyright SERTIC
 */
class CGastoData {

    /** Almacena la conexion con la base de datos. */
    var $db;

    /** Almacena el CActividadData. */
    var $daoActividad;

    /** Almacena el CBitacoraData. */
    var $daoBitacora;

    /**
     * Constructor de la clase.
     * @type \CData $db
     */
    function CGastoData($db) {
        $this->db = $db;
        $this->daoActividad = new CActividadBitacoraData($this->db);
        $this->daoBitacora = new CBitacoraData($this->db);
    }

    /**
     * Obtiene un gasto dado el id del mismo.
     * @param type $idGasto
     * @return \CGasto
     */
    function getGastoById($idGasto) {
        $gasto = null;
        $sql = "SELECT * FROM gastos_actividad WHERE idGastosActividad = '" . $idGasto . "'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $gasto = new CGasto($w['idGastosActividad'], $w['descripcion'], number_format($w['valor'], 0, ',', '.'), $w['archivo'], $w['idTipoGasto'], $w['idActividad'], $w['estado']);
        }
        return $gasto;
    }

    /**
     * Obtiene los gastos dado el id de una Actividad.
     * @param type $idActividad
     * @return type
     */
    public function getGastosByActividad($idActividad) {
        $gastos = null;
        $sql = "SELECT idGastosActividad, gastos_actividad.descripcion, estado, "
                . "valor, archivo, tipo_gasto.descripcion as tipo, idActividad "
                . "FROM gastos_actividad "
                . "INNER JOIN tipo_gasto "
                . "ON tipo_gasto.idTipoGasto = gastos_actividad.idTipoGasto "
                . "WHERE idActividad = '" . $idActividad . "'";

        $actividad = $this->daoActividad->getActividadById($idActividad);
        $bitacora = $this->daoBitacora->getBitacoraById($actividad->getBitacora());

        $date = new DateTime($actividad->getFecha());
        $ruta = RUTA_DOCUMENTOS . "/" . RUTA_BITACORA . "/" . $bitacora->getUsuario() . "/" . $bitacora->getId() . "/" . $date->format('Y-m-d') . "/" . RUTA_GASTOS;

        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $gastos[$cont]['idGastosActividad'] = $w['idGastosActividad'];
                $gastos[$cont]['descripcion'] = $w['descripcion'];
                $gastos[$cont]['valor'] = $w['valor'];
                $gastos[$cont]['archivo'] = "<a href='" . $ruta . "/" . $w['archivo'] . "'>" . $w['archivo'] . "</a>";
                $gastos[$cont]['tipo'] = $w['tipo'];
                $estado = $w['estado'];
                $gastos[$cont]['estado'] = '<img src=./templates/img/ico/verde.gif> Aprobado';
                if ($estado == 0) {
                    $gastos[$cont]['estado'] = '<img src=./templates/img/ico/rojo.gif> No Aprobado';
                }
                $cont++;
            }
        }
        return $gastos;
    }
    
    public function getGastosSincronizar($idUsuario) {
        $gastos = null;
        $sql = "SELECT * "
                . "FROM gastos_actividad g "
                . "INNER JOIN actividad a ON a.idActividad = g.idActividad "
                . "INNER JOIN bitacora b ON b.idBitacora = a.idBitacora "
                . "WHERE g.sync AND b.idUsuario = $idUsuario";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $gastos[$cont]['idGastosActividad'] = $w['idGastosActividad'];
                $gastos[$cont]['descripcion'] = $w['descripcion'];
                $gastos[$cont]['valor'] = $w['valor'];
                $gastos[$cont]['archivo'] = $w['archivo'];
                $gastos[$cont]['idTipoGasto'] = $w['idTipoGasto'];
                $gastos[$cont]['idActividad'] = $w['idActividad'];
                $cont++;
            }
        }
        return $gastos;
    }

    /**
     * Inserta un gasto en la tabla gastos.
     * @param \CGasto $gasto
     * @return type
     */
    public function insertGasto($gasto) {
        $actividad = $this->daoActividad->getActividadById($gasto->getActividad());
        $bitacora = $this->daoBitacora->getBitacoraById($actividad->getBitacora());

        $date = new DateTime($actividad->getFecha());
        $ruta = RUTA_BITACORA . "/" . $bitacora->getUsuario() . "/" . $bitacora->getId() . "/" . $date->format('Y-m-d') . "/" . RUTA_GASTOS;

        $tabla = "gastos_actividad";
        $gasto->setId($this->daoActividad->construirId($gasto->getActividad(), $tabla, 'idGastosActividad'));
        $columnas = $this->db->getCampos($tabla);
        unset($columnas[count($columnas) - 1]);
        unset($columnas[count($columnas) - 1]);
        $campos = "";
        foreach ($columnas as $columna) {
            $campos .= $columna . ",";
        }
        $campos = substr($campos, 0, -1);
        $valores = "'" . $gasto->getId() . "','"
                . $gasto->getDescripcion() . "','"
                . $gasto->getValor() . "','"
                . $gasto->getArchivo()['name'] . "','"
                . $gasto->getTipo() . "','"
                . $gasto->getActividad() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        if ($gasto->getArchivo()['name'] != NULL) {
            $this->db->guardarArchivo($gasto->getArchivo(), $ruta);
        }
        return $r;
    }

    /**
     * Actualiza un gasto de la base de datos.
     * @param \CGasto $gasto
     * @return type
     */
    public function updateGasto($gasto) {
        $tabla = "gastos_actividad";
        $campos = $this->db->getCampos($tabla);
        if ($gasto->getArchivo()['name'] != "") {
            $actividad = $this->daoActividad->getActividadById($gasto->getActividad());
            $bitacora = $this->daoBitacora->getBitacoraById($actividad->getBitacora());

            $date = new DateTime($actividad->getFecha());
            $ruta = RUTA_BITACORA . "/" . $bitacora->getUsuario() . "/" . $bitacora->getId() . "/" . $date->format('Y-m-d') . "/" . RUTA_GASTOS;

            if ($this->db->guardarArchivo($gasto->getArchivo(), $ruta)) {
                $valores = array("'" . $gasto->getId() . "'",
                    "'" . $gasto->getDescripcion() . "'",
                    "'" . $gasto->getValor() . "'",
                    "'" . $gasto->getArchivo()['name'] . "'",
                    "'" . $gasto->getTipo() . "'",
                    "'" . $gasto->getActividad() . "'", "0", "1");
            }
        } else {
            $campos = $this->db->getCampos($tabla);
            unset($campos[3]);
            $valores = array("'" . $gasto->getId() . "'",
                "'" . $gasto->getDescripcion() . "'",
                "'" . $gasto->getValor() . "'",
                "'" . $gasto->getTipo() . "'",
                "'" . $gasto->getActividad() . "'", "0", "1");
        }
        $condicion = $campos[0] . " = '" . $gasto->getId() . "'";
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Elimina un elemento de la base de datos.
     * @param type $idGasto
     * @return type
     */
    public function deleteGastosById($idGasto) {
        $tabla = "gastos_actividad";
        $predicado = "idGastosActividad = '" . $idGasto . "'";
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Cambia el estado de un gasto tomando el valor opuesto al mostrado.
     * @param type $idGasto
     * @return type
     */
    public function cambiarEstado($idGasto) {
        $gasto = $this->getGastoById($idGasto);
        $tabla = "gastos_actividad";
        $columnas = $this->db->getCampos($tabla);
        $valores = array(0);
        if ($gasto->getEstado() == 0) {
            $valores = array(1);
        }
        $campos = array($columnas[count($columnas) - 2]);
        $condicion = $columnas[0] . " = '" . $gasto->getId() . "'";
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    public function enviarGastos($idUsuario) {
        $r = 'true';
        $gastos = $this->getGastosSincronizar($idUsuario);
        if (count($gastos) != 0) {
            require_once "./clases/nusoap-0.9.5/lib/nusoap.php";
            $cliente = new nusoap_client(DIRECCION_WEB_SERVICE_SINCRONIZACION);
            $error = $cliente->getError();
            if ($error) {
                $r = SERVIDOR_NO_DISPONIBLE;
            } else {
                $totalGastos = count($gastos);
                $exitosas = 0;
                foreach ($gastos as $gasto) {
                    $param = array("idGastosActividad" => $gasto['idGastosActividad'], 
                                   "descripcion" => utf8_decode($gasto['descripcion']), 
                                   "valor" => $gasto['valor'],
                                   "archivo" => $gasto['archivo'],
                                   "idTipoGasto" => $gasto['idTipoGasto'],
                                   "idActividad" => $gasto['idActividad']);
                    $result = $cliente->call("insertarGastos", $param);
                    if ($cliente->fault) {
                        $r = NO_EXISTE_SINCRONIZACION;
                    } else {
                        $error = $cliente->getError();
                        if ($error) {
                            $r = ERROR_CONEXION;
                        } else {
                            if ($result) {
                                $exitosas++;
                                $this->setSyncGasto($gasto['idGastosActividad'], 0);
                            } 
                        }
                    }
                }
                if (($exitosas / $totalGastos) == 1) {
                    $r = $exitosas . " " . SINCRONIZACION_RECIBIDA;
                } else {
                    $r = SINCRONIZACION_INCOMPLETA;
                }
            }
        } else {
            $r = NO_SINCRONIZAR;
        }
        return $r;
    }

    function setSyncGasto($id, $valor) {
        $tabla = "gastos_actividad";
        $campos = array('sync');
        $valores = array($valor);
        $condicion = " idGastosActividad = '$id'";
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

}
