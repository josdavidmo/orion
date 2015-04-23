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
class CAtencionMesaAyudaData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    public function CAtencionMesaAyudaData($db) {
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
                . "FROM atencionmesaayuda "
                . "WHERE idAtencionMesaAyuda = " . $idPQR;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $pqr = new CPQR($w['idAtencionMesaAyuda'], $w['descripcionRequerimiento'], 
                                $w['fechaReporte'], $w['fechaSolucion'], 
                                $w['diagnostico'], $w['respuesta'], 
                                null);
            }
        }
        return $pqr;
    }

    /**
     * Obtiene los pqr dado el id de un beneficiario.
     * @return \CCentroPoblado
     */
    public function getPQRByBeneficiario() {
        $pqrs = null;
        $sql = "SELECT *, "
                . "DATE_FORMAT(fechaReporte, '%Y-%m-%d %H:%i') as fechaReporte, "
                . "DATE_FORMAT(fechaSolucion, '%Y-%m-%d %H:%i') as fechaSolucion, "
                . "CONCAT(ABS(EXTRACT(hour from TIMEDIFF(fechaSolucion,fechaReporte))),':',
                   ABS(EXTRACT(minute from TIMEDIFF(fechaReporte,fechaSolucion)))) as diferencia "
                . "FROM atencionmesaayuda ";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $pqrs[$cont]['idAtencionMesaAyuda'] = $w['idAtencionMesaAyuda'];
                $pqrs[$cont]['numero'] = $w['idAtencionMesaAyuda'];
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
        $tabla = "atencionmesaayuda";
        $campos = 'descripcionRequerimiento,fechaReporte';
        $valores = "'" . $pqr->getDescripcionRequerimiento() . "','"
                . $pqr->getFechaReporte() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Actualiza un pqr de la base de datos.
     * @param \CPQR $pqr
     * @return type
     */
    public function answerPQR($pqr) {
        $tabla = "atencionmesaayuda";
        $campos = array('fechaSolucion', 'diagnostico', 'respuesta');
        $valores = array("'" . $pqr->getFechaSolucion() . "'",
            "'" . $pqr->getDiagnostico() . "'",
            "'" . $pqr->getRespuesta() . "'");
        $condicion = "idAtencionMesaAyuda = " . $pqr->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Actualiza un pqr de la base de datos.
     * @param \CPQR $pqr
     * @return type
     */
    public function updatePQR($pqr) {
        $tabla = "atencionmesaayuda";
        $campos = array('descripcionRequerimiento', 'fechaReporte');
        $valores = array("'" . $pqr->getDescripcionRequerimiento() . "'",
            "'" . $pqr->getFechaReporte() . "'");
        $condicion = "idAtencionMesaAyuda = " . $pqr->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Elimina un pqr de la base de datos
     * @param type $idPQR
     * @return type
     */
    public function deletePQRById($idPQR) {
        $tabla = "atencionmesaayuda";
        $predicado = "idAtencionMesaAyuda = " . $idPQR;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }
}  