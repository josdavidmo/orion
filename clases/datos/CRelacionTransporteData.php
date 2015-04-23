<?php

/**
 * Clase Relacion Trasnporte Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.10.31
 * @copyright SERTIC SAS
 */
class CRelacionTransporteData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    function CRelacionTransporteData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene la bitacora de un usuario dado su id.
     * @param type $idUsuario
     * @return type
     */
    public function getRelacionTransporteByUsuario($idUsuario) {
        $relacionTransporte = null;
        $sql = "SELECT r.idrelacionTransporte, d1.dep_nombre as origen, "
                    . "d2.dep_nombre as destino, r.valor, r.fecha "
                . "FROM relaciontransporte r, departamento d1, departamento d2 "
                . "WHERE r.origen = d1.dep_id "
                    . "AND r.destino = d2.dep_id "
                    . "AND idUsuario = " . $idUsuario;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $relacionTransporte[$cont]['idrelacionTransporte'] = $w['idrelacionTransporte'];
                $relacionTransporte[$cont]['fecha'] = $w['fecha'];
                $relacionTransporte[$cont]['origen'] = $w['origen'];
                $relacionTransporte[$cont]['destino'] = $w['destino'];
                $relacionTransporte[$cont]['valor'] = $w['valor'];
                $cont++;
            }
        }
        return $relacionTransporte;
    }

    /**
     * Obtiene una relacion transporte dado el id del mismo.
     * @param type $idRelacionTransporte
     * @return \CRelacionTransporte
     */
    public function getRelacionTransporteById($idRelacionTransporte) {
        $relacionTransporte = null;
        $sql = "SELECT * "
                . "FROM relaciontransporte "
                . "WHERE idrelacionTransporte = " . $idRelacionTransporte;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $relacionTransporte = new CRelacionTransporte($w['idrelacionTransporte'], 
                                                          $w['idUsuario'], 
                                                          $w['origen'], 
                                                          $w['destino'], 
                                                          number_format(($w['valor']), 2, ',', '.'), 
                                                          $w['fecha']);
        }
        return $relacionTransporte;
    }

    /**
     * Inserta una relacion transporte en la base de datos
     * @param \CRelacionTransporte $relacionTransporte
     * @return type
     */
    public function insertRelacionTransporte($relacionTransporte) {
        $tabla = "relaciontransporte";
        $campos = "idUsuario,origen,destino,valor,fecha";
        $valores = "'" . $relacionTransporte->getUsuario() . "','"
                . $relacionTransporte->getOrigen() . "','"
                . $relacionTransporte->getDestino() . "','"
                . $relacionTransporte->getValor() . "','"
                . $relacionTransporte->getFecha() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }
    
    /**
     * Actualiza la relacion transporte de la base de datos
     * @param \CRelacionTransporte $relacionTransporte
     * @return type
     */
    public function updateRelacionTransporte($relacionTransporte){
        $tabla = "relaciontransporte";
        $campos = array('idUsuario','origen','destino',
                        'valor','fecha');
        $valores = array("'" . $relacionTransporte->getUsuario() . "'",
                         "'" . $relacionTransporte->getOrigen() . "'",
                         "'" . $relacionTransporte->getDestino() . "'",
                         "'" . $relacionTransporte->getValor() . "'",
                         "'" . $relacionTransporte->getFecha() . "'");
        $condicion = "idrelacionTransporte = " . $relacionTransporte->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }
    
    /**
     * Elimina una relacion transporte de la base de datos dado su id.
     * @param type $idRelacionTransporte
     * @return type
     */
    public function deleteRelacionTransporteById($idRelacionTransporte){
        $tabla = "relaciontransporte";
        $predicado = "idrelacionTransporte = " . $idRelacionTransporte;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

}
