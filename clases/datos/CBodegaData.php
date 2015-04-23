<?php

/**
 * Clase Bodega Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.09.12
 * @copyright SERTIC SAS
 */
class CBodegaData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    function CBodegaData($db) {
        $this->db = $db;
    }

    /**
     * Obtener tiene todas las bodegas almacenadas en la base de datos.
     * @return type
     */
    public function getBodegas() {
        $bodegas = null;
        $sql = "SELECT b.idBodega, b.codigo, b.nombre, t.descripcion, "
                . "b.Bodega_idBodega "
                . "FROM bodega b, tipobodega t "
                . "WHERE b.TipoBodega_idTipoBodega = t.idTipoBodega";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $bodegaPadre = $this->getBodegaById($w['Bodega_idBodega']);
                $bodegas[$cont]['idBodega'] = $w['idBodega'];
                $bodegas[$cont]['codigo'] = $w['codigo'];
                $bodegas[$cont]['nombre'] = $w['nombre'];
                $bodegas[$cont]['descripcion'] = $w['descripcion'];
                $bodegas[$cont]['padreBodega'] = 'No tiene';
                if($bodegaPadre != NULL){
                    $bodegas[$cont]['padreBodega'] = $bodegaPadre->getNombre();
                }
                $cont++;
            }
        }
        return $bodegas;
    }
    
    /**
     * Obtiene los predecesores de una bodega.
     * @param type $idBodega
     * @return type
     */
    public function getBodegasByPadre($idBodega){
        $bodegas = null;
        $sql = "SELECT b.idBodega, b.codigo, b.nombre, t.descripcion, "
                . "b.Bodega_idBodega "
                . "FROM bodega b, tipobodega t "
                . "WHERE b.TipoBodega_idTipoBodega = t.idTipoBodega AND "
                . "b.Bodega_idBodega = ".$idBodega;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $bodegaPadre = $this->getBodegaById($w['Bodega_idBodega']);
                $bodegas[$cont]['idBodega'] = $w['idBodega'];
                $bodegas[$cont]['codigo'] = $w['codigo'];
                $bodegas[$cont]['nombre'] = $w['nombre'];
                $bodegas[$cont]['descripcion'] = $w['descripcion'];
                $bodegas[$cont]['padreBodega'] = 'No tiene';
                if($bodegaPadre != NULL){
                    $bodegas[$cont]['padreBodega'] = $bodegaPadre->getNombre();
                }
                $cont++;
            }
        }
        return $bodegas;
    }

    /**
     * Obtiene los tipos de bodega almacenados en la base de datos.
     * @return type
     */
    public function getTiposBodega() {
        $tiposBodega = null;
        $sql = "SELECT * FROM tipobodega";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $tipoBodega = new CTipoBodega($w['idTipoBodega'], $w['descripcion']);
                $tiposBodega[$cont] = $tipoBodega;
                $cont++;
            }
        }
        return $tiposBodega;
    }

    /**
     * Obtiene el tipo de una bodega dado su id.
     * @param type $idTipoBodega
     * @return \CTipoBodega
     */
    public function getTipoBodegaById($idTipoBodega) {
        $tipoBodega = null;
        $sql = "SELECT * FROM tipobodega WHERE idTipoBodega = " . $idTipoBodega;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $tipoBodega = new CTipoBodega($w['idTipoBodega'], $w['descripcion']);
        }
        return $tipoBodega;
    }

    /**
     * Obtiene los centros de distribucion almacenados en la base de datos.
     * @return \CBodega
     */
    public function getCentroDistribucion() {
        $bodegas = null;
        $sql = "SELECT * FROM bodega WHERE TipoBodega_idTipoBodega = 1";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $tipoBodega = $this->getTipoBodegaById($w['TipoBodega_idTipoBodega']);
                $bodega = new CBodega($w['idBodega'], $w['codigo'], $w['nombre'], $tipoBodega, null);
                $bodegas[$cont] = $bodega;
                $cont++;
            }
        }
        return $bodegas;
    }

    /**
     * Obtiene los centros de distribucion almacenados en la base de datos.
     * @return \CBodega
     */
    public function getZonasLogisticas() {
        $bodegas = null;
        $sql = "SELECT * FROM bodega WHERE TipoBodega_idTipoBodega = 2";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $tipoBodega = $this->getTipoBodegaById($w['TipoBodega_idTipoBodega']);
                $centroDistribucion = $this->getBodegaById($w['Bodega_idBodega']);
                $bodega = new CBodega($w['idBodega'], $w['codigo'], $w['nombre'], $tipoBodega, $centroDistribucion);
                $bodegas[$cont] = $bodega;
                $cont++;
            }
        }
        return $bodegas;
    }

    public function getBodegaById($idBodega) {
        $bodega = NULL;
        if ($idBodega != '') {
            $sql = "SELECT * FROM bodega WHERE idBodega = " . $idBodega;
            $r = $this->db->ejecutarConsulta($sql);
            if ($r) {
                $w = mysql_fetch_array($r);
                $tipoBodega = $this->getTipoBodegaById($w['TipoBodega_idTipoBodega']);
                $bodegaPadre = NULL;
                if ($w['Bodega_idBodega'] != NULL) {
                    $bodegaPadre = $this->getBodegaById($w['Bodega_idBodega']);
                }
                $bodega = new CBodega($w['idBodega'], $w['codigo'], $w['nombre'], $tipoBodega, $bodegaPadre);
            }
        }
        return $bodega;
    }

    /**
     * Inserta una nueva bodega en la base de datos.
     * @param \CBodega $bodega
     * @return type
     */
    public function insertBodega($bodega) {
        $tabla = "bodega";
        $campos = "codigo,nombre,TipoBodega_idTipoBodega,Bodega_idBodega";
        $valores = "'" . $bodega->getCodigo() . "','"
                . $bodega->getNombre() . "','"
                . $bodega->getTipoBodega()->getIdTipoBodega() . "','"
                . $bodega->getBodegaPadre()->getIdBodega() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }
    
    /**
     * Actualiza una bodega de la base de datos.
     * @param \CBodega $bodega
     * @return type
     */
    public function updateBodega($bodega){
        $tabla = "bodega";
        $campos = array('codigo','nombre','TipoBodega_idTipoBodega','Bodega_idBodega');
        $valores = array("'" . $bodega->getCodigo() . "'",
                         "'" . $bodega->getNombre() . "'",
                         "'" . $bodega->getTipoBodega()->getIdTipoBodega() . "'",
                         "'" . $bodega->getBodegaPadre()->getIdBodega() . "'");
        $condicion = "idBodega = " . $bodega->getIdBodega();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
        
    }
    
    /**
     * Elimina una bodega de la base de datos
     * @param type $idBodega
     * @return type
     */
    public function deleteBodegaById($idBodega){
        $tabla = "bodega";
        $predicado = "idBodega = " . $idBodega;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

}
