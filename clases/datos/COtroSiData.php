<?php

/**
 * Clase Hallazgos Pendientes Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2015.04.30
 * @copyright SERTIC SAS
 */
class COtroSiData {

    /** Almacena la conexion con la base de datos. */
    var $db;

    /**
     * Constructor de la clase.
     * @param \CData $db
     */
    function COtroSiData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene un otro si dado el id del mismo.
     * @param type $idOtrosSi
     * @return \COtroSi
     */
    function getOtrosSiById($idOtrosSi) {
        $otrosSi = null;
        $sql = "SELECT * FROM otrosi WHERE idOtroSi = " . $idOtrosSi;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $otrosSi = new COtroSi($w['idOtroSi'], 
                                   $w['descripcion'], 
                                   number_format($w['valor'], 2, ',', '.'), 
                                   $w['fecha'],
                                   $w['documentoSoporte'],
                                   $w['observaciones'],
                                   $w['idContrato']);
        }
        return $otrosSi;
    }

    /**
     * Obtiene los otros si almacenados en la base de datos.
     * @param type $criterio
     * @return type
     */
    function getOtrosSi($criterio = "1") {
        $otrosSi = null;
        $sql = "SELECT * FROM otrosi WHERE " . $criterio;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $otrosSi[$cont]['idOtroSi'] = $w['idOtroSi'];
                $otrosSi[$cont]['descripcion'] = $w['descripcion'];
                $otrosSi[$cont]['valor'] = $w['valor'];
                $otrosSi[$cont]['fecha'] = $w['fecha'];
                $otrosSi[$cont]['soporte'] = "<a href='" . RUTA_DOCUMENTOS . "/soporteContrato/" . $w['documentoSoporte'] . "' >" . $w['documentoSoporte'] . "</a>";
                $otrosSi[$cont]['observaciones'] = $w['observaciones'];
                $cont++;
            }
        }
        return $otrosSi;
    }

    /**
     * Inserta un otro si en la base de datos.
     * @param \COtroSi $otroSi
     * @return type
     */
    public function insertOtroSi($otroSi) {
        $tabla = "otrosi";
        $columnas = $this->db->getCampos($tabla);
        $campos = "";
        foreach ($columnas as $columna) {
            $campos .= $columna . ",";
        }
        $campos = substr($campos, 0, -1);
        $valores = "'" . $otroSi->getId() . "','"
                . $otroSi->getDescripcion() . "','"
                . $otroSi->getValor() . "','"
                . $otroSi->getFecha() . "','"
                . $otroSi->getObservaciones() . "','"
                . $otroSi->getDocumentoSoporte()['name'] . "','"
                . $otroSi->getContrato() . "'";
        $ruta = "soporteContrato";
        $this->db->guardarArchivo($otroSi->getDocumentoSoporte(), $ruta);
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Actualiza un otro si de la base de datos.
     * @param \COtroSi $otroSi
     * @return type
     */
    public function updateOtroSi($otroSi) {
        $tabla = "otrosi";
        $campos = array("descripcion", "valor", "fecha", "observaciones");
        $valores = array("'" . $otroSi->getDescripcion() . "'",
            "'" . $otroSi->getValor() . "'",
            "'" . $otroSi->getFecha() . "'",
            "'" . $otroSi->getObservaciones() . "'");
        $ruta = "soporteContrato";
        if (isset($otroSi->getDocumentoSoporte()['name']) 
                && $this->db->guardarArchivo($otroSi->getDocumentoSoporte(), $ruta)) {
            $campos[count($campos)] = "documentoSoporte";
            $valores[count($valores)] = "'" . $otroSi->getDocumentoSoporte()['name'] . "'";
        }
        $condicion = "idOtroSi = " . $otroSi->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }
    
    /**
     * Elimina un elemento de la base de datos.
     * @param type $idOtroSi
     * @return type
     */
    public function deleteOtroSiById($idOtroSi) {
        $tabla = "otrosi";
        $predicado = "idOtroSi = " . $idOtroSi;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

}
