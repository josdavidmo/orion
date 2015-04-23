<?php

/**
 * DAO para el modulo Parafiscales.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.09.14
 * @copyright SERTIC
 */
class CParafiscalesData {

    /** Almacena la conexion con la base de datos. */
    var $db = null;

    /**
     * Construtor de la clase.
     * @param \CData $db
     */
    function CParafiscalesData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene los estados de los parafiscales.
     * @return \CEstadoParafiscales
     */
    public function getEstadoParafiscales() {
        $estadosParafiscales = null;
        $sql = "SELECT * FROM estadoParafiscales";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $estadoParafiscal = new CEstadoParafiscales($w['idEstado'], $w['descripcion']);
                $estadosParafiscales[$cont] = $estadoParafiscal;
                $cont++;
            }
        }
        return $estadosParafiscales;
    }

    /**
     * Obtiene los estados de los parafiscales.
     * @return \CEstadoParafiscales
     */
    public function getEstadoParafiscalesById($idEstado) {
        $estadoParafiscal = null;
        $sql = "SELECT * FROM estadoParafiscales WHERE idEstado = " . $idEstado;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $estadoParafiscal = new CEstadoParafiscales($w['idEstado'], $w['descripcion']);
        }
        return $estadoParafiscal;
    }

    /**
     * Obtiene los usuarios pertenecientes al area juridica.
     * @return \CUsuario
     */
    public function getUsuariosJuridico() {
        $usuarios = null;
        $sql = "SELECT usu_id, usu_apellido, usu_nombre "
                . "FROM usuario "
                . "WHERE per_id = 8 "
                . "ORDER BY usu_apellido";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $usuario = new CUsuario($w['usu_id'], null);
                $usuario->setNombre($w['usu_nombre']);
                $usuario->setApellido($w['usu_apellido']);
                $usuarios[$cont] = $usuario;
                $cont++;
            }
        }
        return $usuarios;
    }

    /**
     * Obtiene los parafiscales almacenados en la base de datos.
     * @return type
     */
    public function getParafiscales() {
        $parafiscales = null;
        $sql = "SELECT p.idParafiscales, DATE_FORMAT(p.periodo, '%Y-%m') as periodo, "
                . "p.fechaRealizacionComunicado, p.comunicadoEntregaSoportes, "
                . "ec.descripcion as evCont, er.descripcion as evRe, "
                . "CONCAT(u.usu_nombre, ' ',u.usu_apellido) as usuario, "
                . "p.fechaComunicadoInterventoria, "
                . "p.comunicadoConceptoInterventoria, "
                . "p.observaciones "
                . "FROM parafiscales p, estadoParafiscales ec, "
                . "estadoParafiscales er, usuario u "
                . "WHERE p.evaluacionContenidoDocumento = ec.idEstado AND "
                . "p.evaluacionRevisorFiscal = er.idEstado AND "
                . "p.usuario = u.usu_id";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $parafiscales[$cont]['id'] = $w['idParafiscales'];
                $parafiscales[$cont]['periodo'] = $w['periodo'];
                $parafiscales[$cont]['fechaRealizacionComunicado'] = $w['fechaRealizacionComunicado'];
                $parafiscales[$cont]['comunicadoEntregaSoportes'] = $w['comunicadoEntregaSoportes'];
                $parafiscales[$cont]['evaluacionContenido'] = $w['evCont'];
                $parafiscales[$cont]['evaluacionRevisor'] = $w['evRe'];
                $conceptoFinal = 'No Cumple';
                if ($w['evCont'] == 'Cumple' AND $w['evRe'] == 'Cumple') {
                    $conceptoFinal = 'Cumple';
                }
                $parafiscales[$cont]['evaluacionFinal'] = $conceptoFinal;
                $parafiscales[$cont]['usuario'] = $w['usuario'];
                $parafiscales[$cont]['fechaComunicadoInterventoria'] = $w['fechaComunicadoInterventoria'];
                $parafiscales[$cont]['comunicadoConceptoInterventoria'] = $w['comunicadoConceptoInterventoria'];
                $parafiscales[$cont]['observaciones'] = $w['observaciones'];
                $cont++;
            }
        }
        return $parafiscales;
    }
    
    /**
     * Obtiene los parafiscales por periodo almacenados en la base de datos.
     * @param type $periodo
     * @return type
     */
    public function getParafiscalesByPeriodo($periodo) {
        $parafiscales = null;
        $sql = "SELECT p.idParafiscales, DATE_FORMAT(p.periodo, '%Y-%m') as periodo, "
                . "p.fechaRealizacionComunicado, p.comunicadoEntregaSoportes, "
                . "ec.descripcion as evCont, er.descripcion as evRe, "
                . "CONCAT(u.usu_nombre, ' ',u.usu_apellido) as usuario, "
                . "p.fechaComunicadoInterventoria, "
                . "p.comunicadoConceptoInterventoria, "
                . "p.observaciones "
                . "FROM parafiscales p, estadoParafiscales ec, "
                . "estadoParafiscales er, usuario u "
                . "WHERE p.evaluacionContenidoDocumento = ec.idEstado AND "
                . "p.evaluacionRevisorFiscal = er.idEstado AND "
                . "p.usuario = u.usu_id AND "
                . "DATE_FORMAT(p.periodo, '%Y-%m') = '".$periodo."'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $parafiscales[$cont]['id'] = $w['idParafiscales'];
                $parafiscales[$cont]['periodo'] = $w['periodo'];
                $parafiscales[$cont]['fechaRealizacionComunicado'] = $w['fechaRealizacionComunicado'];
                $parafiscales[$cont]['comunicadoEntregaSoportes'] = $w['comunicadoEntregaSoportes'];
                $parafiscales[$cont]['evaluacionContenido'] = $w['evCont'];
                $parafiscales[$cont]['evaluacionRevisor'] = $w['evRe'];
                $conceptoFinal = 'No Cumple';
                if ($w['evCont'] == 'Cumple' AND $w['evRe'] == 'Cumple') {
                    $conceptoFinal = 'Cumple';
                }
                $parafiscales[$cont]['evaluacionFinal'] = $conceptoFinal;
                $parafiscales[$cont]['usuario'] = $w['usuario'];
                $parafiscales[$cont]['fechaComunicadoInterventoria'] = $w['fechaComunicadoInterventoria'];
                $parafiscales[$cont]['comunicadoConceptoInterventoria'] = $w['comunicadoConceptoInterventoria'];
                $parafiscales[$cont]['observaciones'] = $w['observaciones'];
                $cont++;
            }
        }
        return $parafiscales;
    }

    /**
     * Obtiene un parafiscal dado su id.
     * @param type $idParafiscal
     * @return \CParafiscales
     */
    public function getParafiscalById($idParafiscal) {
        $parafiscal = null;
        $sql = "SELECT * FROM parafiscales WHERE idParafiscales = " . $idParafiscal;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $periodo = substr($w['periodo'], 0, -3);
            $evaluacionContenidoDocumento = $this->getEstadoParafiscalesById($w['evaluacionContenidoDocumento']);
            $evaluacionRevisorFiscal = $this->getEstadoParafiscalesById($w['evaluacionRevisorFiscal']);
            $usuario = new CUsuario($w['usuario'], NULL);
            $parafiscal = new CParafiscales($w['idParafiscal'], $periodo, $w['fechaRealizacionComunicado'], $w['comunicadoEntregaSoportes'], $evaluacionContenidoDocumento, $evaluacionRevisorFiscal, $usuario, $w['fechaComunicadoInterventoria'], $w['comunicadoConceptoInterventoria'], $w['observaciones']);
        }
        return $parafiscal;
    }

    /**
     * Inserta un parafiscal en la tabla parafiscales.
     * @param \CParafiscales $parafiscal
     * @return type
     */
    public function insertParafiscal($parafiscal) {
        $tabla = "parafiscales";
        $campos = "periodo,fechaRealizacionComunicado,comunicadoEntregaSoportes,"
                . "evaluacionContenidoDocumento,evaluacionRevisorFiscal,usuario,"
                . "fechaComunicadoInterventoria,comunicadoConceptoInterventoria,"
                . "observaciones";
        $valores = "'" . $parafiscal->getPeriodo() . "',"
                . "'" . $parafiscal->getFechaRealizacionComunicado() . "',"
                . "'" . $parafiscal->getComunicadoEntregaSoportes() . "',"
                . "'" . $parafiscal->getEvaluacionContenidoDocumento()->getIdEstadoParasfiscales() . "',"
                . "'" . $parafiscal->getEvaluacionRevisorFiscal()->getIdEstadoParasfiscales() . "',"
                . "'" . $parafiscal->getUsuario()->getId() . "',"
                . "'" . $parafiscal->getFechaComunicadoInterventoria() . "',"
                . "'" . $parafiscal->getComunicadoConceptoInterventoria() . "',"
                . "'" . $parafiscal->getObservaciones() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Actualiza un parafiscal de la base de datos.
     * @param \CParafiscales $parafiscal
     * @return type
     */
    public function updateParafiscal($parafiscal) {
        $tabla = "parafiscales";
        $campos = array('periodo', 'fechaRealizacionComunicado',
            'comunicadoEntregaSoportes',
            'evaluacionContenidoDocumento',
            'evaluacionRevisorFiscal', 'usuario',
            'fechaComunicadoInterventoria',
            'comunicadoConceptoInterventoria', 'observaciones');
        $valores = array("'" . $parafiscal->getPeriodo() . "'",
                         "'" . $parafiscal->getFechaRealizacionComunicado() . "'",
                         "'" . $parafiscal->getComunicadoEntregaSoportes() . "'",
                         "'" . $parafiscal->getEvaluacionContenidoDocumento()->getIdEstadoParasfiscales() . "'",
                         "'" . $parafiscal->getEvaluacionRevisorFiscal()->getIdEstadoParasfiscales() . "'",
                         "'" . $parafiscal->getUsuario()->getId() . "'",
                         "'" . $parafiscal->getFechaComunicadoInterventoria() . "'",
                         "'" . $parafiscal->getComunicadoConceptoInterventoria() . "'",
                         "'" . $parafiscal->getObservaciones() . "'");
        $condicion = "idParafiscales = " . $parafiscal->getIdParafiscales();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }
    
    /**
     * Borra un parafiscal de la base de datos dado su id.
     * @param type $idParafiscal
     * @return type
     */
    public function deleteParafiscalById($idParafiscal) {
        $tabla = "parafiscales";
        $predicado = "idParafiscales = " . $idParafiscal;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

}
