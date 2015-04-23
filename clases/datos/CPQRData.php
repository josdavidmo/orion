<?php

/**
 * Clase Beneficiario Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.09.21
 * @copyright SERTIC SAS
 */
class CPQRData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    public function CPQRData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene los pqr dado el id de un pqr.
     * @param type $idPQR
     * @return \CCentroPoblado
     */
    public function getPQRById($idPQR) {
        $pqr = null;
        $sql = "SELECT *, "
                . "DATE_FORMAT(fechaReporte, '%Y-%m-%d %H:%i') as fechaReporte, "
                . "DATE_FORMAT(fechaSolucion, '%Y-%m-%d %H:%i') as fechaSolucion "
                . "FROM pqr "
                . "WHERE idPQR = " . $idPQR;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $pqr = new CPQR($w['idPQR'], $w['descripcionRequerimiento'], 
                                $w['fechaReporte'], $w['fechaSolucion'], 
                                $w['diagnostico'], $w['respuesta'], 
                                $w['idBeneficiario']);
            }
        }
        return $pqr;
    }

    /**
     * Obtiene los pqr dado el id de un beneficiario.
     * @param type $idBeneficiario
     * @return \CCentroPoblado
     */
    public function getPQRByBeneficiario($idBeneficiario) {
        $pqrs = null;
        $sql = "SELECT *, "
                . "DATE_FORMAT(fechaReporte, '%Y-%m-%d %H:%i') as fechaReporte, "
                . "DATE_FORMAT(fechaSolucion, '%Y-%m-%d %H:%i') as fechaSolucion, "
                . "CONCAT(ABS(EXTRACT(hour from TIMEDIFF(fechaSolucion,fechaReporte))),':',
                   ABS(EXTRACT(minute from TIMEDIFF(fechaReporte,fechaSolucion)))) as diferencia "
                . "FROM pqr "
                . "WHERE idBeneficiario = " . $idBeneficiario;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $pqrs[$cont]['idPQR'] = $w['idPQR'];
                $pqrs[$cont]['numero'] = $w['idPQR'];
                $pqrs[$cont]['descripcionRequerimiento'] = $w['descripcionRequerimiento'];
                $pqrs[$cont]['fechaReporte'] = $w['fechaReporte'];
                $pqrs[$cont]['fechaSolucion'] = $w['fechaSolucion'];
                $pqrs[$cont]['diferencia'] = $w['diferencia'];
                $pqrs[$cont]['diagnostico'] = $w['diagnostico'];
                $pqrs[$cont]['respuesta'] = $w['respuesta'];
                $pqrs[$cont]['estado'] = '<img src=./templates/img/ico/verde.gif> Resuelto';
                if ($w['respuesta'] == NULL) {
                    $pqrs[$cont]['estado'] = '<img src=./templates/img/ico/rojo.gif> No Resuelto';
                }
                $cont++;
            }
        }
        return $pqrs;
    }

    /**
     * Inserta una pqr en la base de datos
     * @param \CPQR $pqr
     * @return type
     */
    public function insertPQR($pqr) {
        $tabla = "pqr";
        $campos = 'descripcionRequerimiento,fechaReporte,idBeneficiario';
        $valores = "'" . $pqr->getDescripcionRequerimiento() . "','"
                . $pqr->getFechaReporte() . "','"
                . $pqr->getBeneficiario() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Actualiza un pqr de la base de datos.
     * @param \CPQR $pqr
     * @return type
     */
    public function answerPQR($pqr) {
        $tabla = "pqr";
        $campos = array('fechaSolucion', 'diagnostico', 'respuesta');
        $valores = array("'" . $pqr->getFechaSolucion() . "'",
            "'" . $pqr->getDiagnostico() . "'",
            "'" . $pqr->getRespuesta() . "'");
        $condicion = "idPQR = " . $pqr->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Actualiza un pqr de la base de datos.
     * @param \CPQR $pqr
     * @return type
     */
    public function updatePQR($pqr) {
        $tabla = "pqr";
        $campos = array('descripcionRequerimiento', 'fechaReporte', 'idBeneficiario');
        $valores = array("'" . $pqr->getDescripcionRequerimiento() . "'",
            "'" . $pqr->getFechaReporte() . "'",
            "'" . $pqr->getBeneficiario() . "'");
        $condicion = "idPQR = " . $pqr->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Elimina un pqr de la base de datos
     * @param type $idPQR
     * @return type
     */
    public function deletePQRById($idPQR) {
        $tabla = "pqr";
        $predicado = "idPQR = " . $idPQR;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * 
     * @param type $idBeneficiario
     * @param type $periodo
     * @return type
     */
    public function getIndicador($idBeneficiario, $periodo) {
		$fechaInicio = substr($periodo, 0, -3)."-01";
        $año = substr($periodo, 0, 4);
		$mes = substr($periodo, 5, -3);
		$fechaFin = $año."-".$mes."-".$this->getUltimoDiaMes($año,$mes);
		$indicador = null;
        $sql = "SELECT SUM((ABS(EXTRACT(hour from TIMEDIFF(fechaReporte,fechaSolucion)))*60 + "
                . "ABS(EXTRACT(minute from TIMEDIFF(fechaReporte,fechaSolucion))))) as minutosIndisponible,"
                . "DATE_FORMAT('" . $fechaFin . "','%d')*24*60 as minutosPeriodo "
                . "FROM pqr "
                . "WHERE idBeneficiario = " . $idBeneficiario . " AND "
                . "DATE_FORMAT(fechaReporte, '%Y-%m-%d') BETWEEN '" . $fechaInicio . "' AND "
                . "'" . $fechaFin . "'";
		$r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $indicador['minutosIndisponible'] = $w['minutosIndisponible'];
            if($indicador['minutosIndisponible'] == NULL){
                $indicador['minutosIndisponible'] = 0;
            }
            $indicador['minutosDisponible'] = $w['minutosPeriodo']-$w['minutosIndisponible'];
        }
        return $indicador;
    }
    
    /**
     * 
     * @param type $idBeneficiario
     * @param type $periodo
     * @return type
     */
    public function getIndicadorGeneral($periodo) {
        $fechaInicio = substr($periodo, 0, -3)."-01";
		$año = substr($periodo, 0, 4);
		$mes = substr($periodo, 5, -3);
		$fechaFin = $año."-".$mes."-".$this->getUltimoDiaMes($año,$mes);
        $indicador = null;
        $sql = "SELECT SUM((ABS(EXTRACT(hour from TIMEDIFF(fechaReporte,fechaSolucion)))*60 + "
                . "ABS(EXTRACT(minute from TIMEDIFF(fechaReporte,fechaSolucion))))) as minutosIndisponible,"
                . "DATE_FORMAT('". $fechaFin. "','%d')*24*60 as minutosPeriodo "
                . "FROM pqr "
                . "WHERE "
                . "DATE_FORMAT(fechaReporte, '%Y-%m-%d') BETWEEN '" . $fechaInicio . "' AND "
                . "'". $fechaFin. "'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $indicador['minutosIndisponible'] = $w['minutosIndisponible'];
            if($indicador['minutosIndisponible'] == NULL){
                $indicador['minutosIndisponible'] = 0;
            }
            $indicador['minutosDisponible'] = $w['minutosPeriodo']-$w['minutosIndisponible'];
        }
        return $indicador;
    }
	
	/**
	* Obtiene el ultimo dia de un mes.
	*/
	private function getUltimoDiaMes($elAnio,$elMes) {
		return date("d",(mktime(0,0,0,$elMes+1,1,$elAnio)-1));
	}
}  