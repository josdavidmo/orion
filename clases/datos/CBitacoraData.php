<?php

/**
 * Clase Bitacora Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.10.30
 * @copyright SERTIC SAS
 */
class CBitacoraData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    function CBitacoraData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene la bitacora de un usuario dado su id.
     * @param type $idUsuario
     * @return type
     */
    public function getBitacorasByUsuario($idUsuario) {
        $bitacora = null;
        $sql = "SELECT b.idBitacora, "
                . "CONCAT(re.der_nombre,'-',de.dep_nombre,'-',mu.mun_nombre,'-',c.nombre,'-',be.nombre) as beneficiario, "
                . "b.descripcionActividad, "
                . "b.fechaInicio, b.fechaFin, "
                . "(SELECT SUM(a.estado)/COUNT(a.estado) FROM actividad a WHERE a.idBitacora = b.idBitacora) as estado "
                . "FROM bitacora b "
                . "INNER JOIN beneficiario be ON b.idBeneficiario = be.idBeneficiario "
                . "INNER JOIN centropoblado c ON c.idCentroPoblado = be.idCentroPoblado "
                . "INNER JOIN municipio mu ON mu.mun_id = c.mun_id "
                . "INNER JOIN departamento de ON de.dep_id = mu.dep_id "
                . "INNER JOIN departamento_region re ON re.der_id = de.der_id "
                . "WHERE idUsuario = " . $idUsuario;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $bitacora[$cont]['idBitacora'] = $w['idBitacora'];
                $bitacora[$cont]['beneficiario'] = $w['beneficiario'];
                $bitacora[$cont]['descripcionActividad'] = $w['descripcionActividad'];
                $bitacora[$cont]['fechaInicio'] = $w['fechaInicio'];
                $bitacora[$cont]['fechaFin'] = $w['fechaFin'];
                $fechaActual = strtotime(getdate()['year'] . '-' . getdate()['mon'] . '-' . getdate()['mday']);
                $fechaFin = strtotime($bitacora[$cont]['fechaFin']);
                if ($w['estado'] == 1) {
                    $bitacora[$cont]['imagen'] = '<img src=./templates/img/ico/verde.gif> Completado';
                } else {
                    $bitacora[$cont]['imagen'] = '<img src=./templates/img/ico/rojo.gif> Vencido';
                    if ($fechaActual < $fechaFin) {
                        $bitacora[$cont]['imagen'] = '<img src=./templates/img/ico/amarillo.gif> Vigente';
                    }
                }
                $cont++;
            }
        }
        return $bitacora;
    }

    /**
     * Inserta una bitacora en la base de datos
     * @param \CBitacora $bitacora
     * @return type
     */
    public function insertBitacora($bitacora) {
        $tabla = "bitacora";
        $columnas = $this->db->getCampos($tabla);
        $campos = "idUsuario,idBeneficiario,descripcionActividad,fechaInicio,fechaFin";
        $valores = "'" . $bitacora->getUsuario() . "','"
                . $bitacora->getBeneficiario() . "','"
                . $bitacora->getDescripcionActividad() . "','"
                . $bitacora->getFechaInicio() . "','"
                . $bitacora->getFechaFin() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }
    
    public function insertBitacoraSync($bitacora) {
        $tabla = "bitacora";
        $columnas = $this->db->getCampos($tabla);
        $campos = "idBitacora,idUsuario,idBeneficiario,descripcionActividad,fechaInicio,fechaFin";
        $valores = "'" . $bitacora->getId() . "','"
                . $bitacora->getUsuario() . "','"
                . $bitacora->getBeneficiario() . "','"
                . $bitacora->getDescripcionActividad() . "','"
                . $bitacora->getFechaInicio() . "','"
                . $bitacora->getFechaFin() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Obtiene la bitacora dado su el id de la bitacora.
     * @param type $idBitacora
     * @return \CBitacora
     */
    public function getBitacoraById($idBitacora) {
        $bitacora = null;
        $sql = "SELECT * FROM bitacora WHERE idBitacora = " . $idBitacora;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $bitacora = new CBitacora($w['idBitacora'], $w['idUsuario'], $w['idBeneficiario'], $w['descripcionActividad'], $w['fechaInicio'], $w['fechaFin']);
        }
        return $bitacora;
    }

    /**
     * Actualiza una bitacora de la base de datos.
     * @param \CBitacora $bitacora
     * @return type
     */
    public function updateBitacora($bitacora) {
        $tabla = "bitacora";
        $campos = $this->db->getCampos($tabla);
        unset($campos[count($campos) - 1]);
        $valores = array("'" . $bitacora->getId() . "'",
            "'" . $bitacora->getUsuario() . "'",
            "'" . $bitacora->getBeneficiario() . "'",
            "'" . $bitacora->getDescripcionActividad() . "'",
            "'" . $bitacora->getFechaInicio() . "'",
            "'" . $bitacora->getFechaFin() . "'");
        $condicion = $campos[0] . " = " . $bitacora->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Elimina una bitacora de la base de datos dado su id.
     * @param type $idBitacora
     * @return type
     */
    public function deleteBitacoraById($idBitacora) {
        $tabla = "bitacora";
        $predicado = "idBitacora = " . $idBitacora;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }
    
    public function getBitacorasSincronizacion($usuario){
        $resultado = $this->getBitacorasByUsuario($usuario);
        $bitacoras = null;
        for($i =0;$i<count($resultado);$i++){
            $bitacoras[count($bitacoras)]= new bitacora($resultado[$i]['idBitacora'],$usuario,
                    $resultado[$i]['beneficiario'],$resultado[$i]['descripcionActividad'],
                    $resultado[$i]['fechaInicio'],$resultado[$i]['fechaFin']);
        }
        return $bitacoras;
    }
    
    public function setSync($tabla,$campo,$valorId,$sync){
        $condicion = $campo." = '".$valorId."'";
        $r = $this->db->actualizarRegistro($tabla, array('sync'), array($sync), $condicion);
    }

}
