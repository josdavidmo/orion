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
                . "WHERE idActividad = '" . $idActividad."'";

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
        unset($campos[count($campos) - 1]);
        unset($campos[count($campos) - 1]);
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
                    "'" . $gasto->getActividad() . "'","");
            }
        } else {
            $campos = $this->db->getCampos($tabla);
            unset($campos[3]);
            $valores = array("'" . $gasto->getId() . "'",
                "'" . $gasto->getDescripcion() . "'",
                "'" . $gasto->getValor() . "'",
                "'" . $gasto->getTipo() . "'",
                "'" . $gasto->getActividad() . "'","");
        }
        $condicion = $campos[0] . " = '" . $gasto->getId() ."'";
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
    
    function getGastosSincronizacion($usuario,$html) {
        $gastos = null;
        $sql = "SELECT gastos_actividad.* FROM gastos_actividad inner join "
                . "actividad on gastos_actividad.idActividad = actividad.idActividad "
                . "inner join bitacora on actividad.idBitacora = bitacora.idBitacora "
                . "WHERE gastos_actividad.sync = 1 AND bitacora.idUsuario = " . $usuario;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                $gastos[count($gastos)] = new gasto($w['idGastosActividad'], $html->traducirTildes($w['descripcion']), 
                        $w['valor'], $w['archivo'], $w['idTipoGasto'], 
                        $w['idActividad'], $w['estado']);
            }
        }
        return $gastos;
    }

}
