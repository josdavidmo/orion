<?php

/**
 * Clase Relacion Municipio Orden Pago Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.02.27
 * @copyright SERTIC SAS
 */
class CRelacionMunicipioOrdenPagoData {
    
    /** Almacena la conexion de la clase. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param \CData $db
     */
    function CRelacionMunicipioOrdenPagoData($db) {
        $this->db = $db;
    }
    
    /**
     * Obtiene una relacion municipio orden pago dado su id.
     * @param type $idRelacionMunicipioOrdenPago
     * @return \CRelacionMunicipioOrdenPago
     */
    function getRelacionMunicipioOrdenPagoById($idRelacionMunicipioOrdenPago) {
        $relacionMunicipioOrdenPago = null;
        $sql = "SELECT * FROM municipio_orden_pago "
                . "WHERE idValorOrdenMunicipio = " . $idRelacionMunicipioOrdenPago;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $relacionMunicipioOrdenPago = new CRelacionMunicipioOrdenPago($w['idValorOrdenMunicipio'], 
                                                                          number_format($w['valor'], 0, ',', '.'), 
                                                                          $w['idDestinacionRecursos'], 
                                                                          $w['idMunicipio'], 
                                                                          $w['idOrdenPago']);
        }
        return $relacionMunicipioOrdenPago;
    }
    
    /**
     * Obtiene todas las relaciones municipio orden de pago almacenadas en 
     * la base de datos.
     * @param type $condicion
     * @return type
     */
    function getRelacionMunicipioOrdenPago($condicion = "1") {
        $relacionesMunicipioOrdenPago = null;
        $sql = "SELECT mop.idValorOrdenMunicipio, mop.valor, "
                . "d.descripcionDestinacionRecursos, m.mun_nombre,"
                . "ROUND(((mop.valor*100)/(o.valor_total*o.Tasa_Orden)), 3) as porcentaje "
                . "FROM municipio_orden_pago mop "
                . "INNER JOIN destinacion_recursos d ON d.idDestinacionRecursos = mop.idDestinacionRecursos "
                . "INNER JOIN municipio m ON m.mun_id = mop.idMunicipio "
                . "INNER JOIN ordenesdepago o ON o.Id_Orden_Pago = mop.idOrdenPago "
                . "WHERE $condicion";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $relacionesMunicipioOrdenPago[$cont]['idValorOrdenMunicipio'] = $w['idValorOrdenMunicipio'];
                $relacionesMunicipioOrdenPago[$cont]['valor'] = $w['valor'];
                $relacionesMunicipioOrdenPago[$cont]['porcentaje'] = $w['porcentaje'];
                $relacionesMunicipioOrdenPago[$cont]['descripcionDestinacionRecursos'] = $w['descripcionDestinacionRecursos'];
                $relacionesMunicipioOrdenPago[$cont]['mun_nombre'] = $w['mun_nombre'];
                $cont++;
            }
        }
        return $relacionesMunicipioOrdenPago;
    }
    
    /**
     * Inserta una relacion municipio orden de pago en la base de datos.
     * @param \CRelacionMunicipioOrdenPago $relacionMunicipioOrdenPago
     * @return type
     */
    public function insertRelacionMunicipioOrdenPago($relacionMunicipioOrdenPago) {
        $r = "false";
        $tabla = "municipio_orden_pago";
        $columnas = $this->db->getCampos($tabla);
        $campos = "";
        foreach ($columnas as $columna) {
            $campos .= $columna . ",";
        }
        $campos = substr($campos, 0, -1);
        $valores = "'" . $relacionMunicipioOrdenPago->getId() . "','"
                   . $relacionMunicipioOrdenPago->getValor() . "','"
                   . $relacionMunicipioOrdenPago->getDestinacionRecursos() . "','"
                   . $relacionMunicipioOrdenPago->getMunicipio() . "','"
                   . $relacionMunicipioOrdenPago->getOrdenPago() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }
    
    /**
     * Elimina una relacion municipio orden de pago dado el su id.
     * @param type $idRelacionMunicipioOrdenPago
     * @return type
     */
    public function deleteRelacionMunicipioOrdenPago($idRelacionMunicipioOrdenPago) {
        $tabla = "municipio_orden_pago";
        $predicado = "idValorOrdenMunicipio = " . $idRelacionMunicipioOrdenPago;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }
    
    /**
     * Actualiza una relacion municipio orden de pago en la base de datos.
     * @param \CRelacionMunicipioOrdenPago $relacionMunicipioOrdenPago
     * @return type
     */
    public function updateRelacionMunicipioOrdenPago($relacionMunicipioOrdenPago) {
        $tabla = "municipio_orden_pago";
        $campos = $this->db->getCampos($tabla);
        $condicion = $campos[0] . " = " . $relacionMunicipioOrdenPago->getId();
        $valores = array("'" . $relacionMunicipioOrdenPago->getId() . "'",
                    "'" . $relacionMunicipioOrdenPago->getValor() . "'",
                    "'" . $relacionMunicipioOrdenPago->getDestinacionRecursos() . "'",
                    "'" . $relacionMunicipioOrdenPago->getMunicipio() . "'",
                    "'" . $relacionMunicipioOrdenPago->getOrdenPago() . "'");
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }
    
    /**
     * Obtiene la ubicacion de un municipio.
     * @param type $idMunicipio
     * @return type
     */
    public function getUbicacionMunicipioById($idMunicipio) {
        $sql = "SELECT re.der_id, de.dep_id, mu.mun_id "
               ."FROM municipio mu "
               ."INNER JOIN departamento de ON de.dep_id = mu.dep_id "
               ."INNER JOIN departamento_region re ON re.der_id = de.der_id "
               ."WHERE mu.mun_id = $idMunicipio";
        $r = $this->db->ejecutarConsulta($sql);
        $ubicacion = null;
        if ($r) {
            $w = mysql_fetch_array($r);
            $ubicacion = array('region' => $w['der_id'],
                'departamento' => $w['dep_id'],
                'municipio' => $w['mun_id']);
        }
        return $ubicacion;
    }
    
}
