<?php

/**
 * Maneja las peticiones a la base de datos que realiza el modulo informe 
 * financiero.
 * @author Brian Kings
 * @version 1.0
 * @since 08/08/2014
 */
class CInformeFinancieroData {

    /** Maneja la conexion con la base de datos. */
    var $db = null;

    /** Maneja el valor del saldo pendiente del contrato */
    static private $saldop_contrato = null;

    /** Maneja el valor del saldo pendiente de la amortización */
    static private $saldop_amortizacion = null;

    /**
     * Obtiene el valor del saldo pendiente del contrato
     * @return type
     */
    public function getSaldop_contrato() {
        return $this->saldop_contrato;
    }

    /**
     * Obtiene el valor del saldo pendiente del amortización
     * @return type
     */
    public function getSaldop_amortizacion() {
        return $this->saldop_amortizacion;
    }

    /**
     * Recibe el valor del saldo pendiente del contrato
     * @param type $saldop_contrato
     */
    public function setSaldop_contrato($saldop_contrato) {
        $this->saldop_contrato = $saldop_contrato;
    }

    /**
     * Recibe el valor del saldo pendiente del amortización
     * @param type $saldop_contrato
     */
    public function setSaldop_amortizacion($saldop_amortizacion) {
        $this->saldop_amortizacion = $saldop_amortizacion;
    }

    /**
     * Constructor de la clase.
     * @param CData $db
     */
    function CInformeFinancieroData($db) {
        $this->db = $db;
        $this->valorBaseSaldoPAmortizacion();
        $this->valorBaseSaldoPContrato();
    }
    /**
     * Obtiene todos los registros InformeFinanciero de la base de datos.
     * @param string $criterio
     * @return array
     */
    function getInformeFinanciero($criterio) {
        $resultado = null;
        $sql = "select ifi.ifi_id,ifi.ifi_numero_pago,ifi.ifi_vigencia,ifi.ifi_numero_factura,ifi.ifi_fecha_factura,"
                . "ifi.ifi_numero_radicado_ministerio,ifi.ifi_documento_soporte,ifi.ifi_descripcion,ifi.ifi_valor_factura,"
                . "ifi.ifi_amortizacion,ife.ife_nombre,ifi.ife_id, ifi.ifi_fecha_pago,ifi.ifi_observaciones"
                . " from informe_financiero ifi left join informe_financiero_estado ife on ife.ife_id = ifi.ife_id "
                . " where " . $criterio . " order by ifi.ifi_vigencia ";
        //echo $sql;
        $w = $this->db->ejecutarConsulta($sql);
        if ($w) {
            $cont = 0;
            while ($r = mysql_fetch_array($w)) {
                $resultado[$cont]['id_element'] = $r['ifi_id'];
                $resultado[$cont]['numero_pago'] = $r['ifi_numero_pago'];
                $resultado[$cont]['vigencia'] = $r['ifi_vigencia'];
                $resultado[$cont]['numero_factura'] = $r['ifi_numero_factura'];
                $resultado[$cont]['fecha_factura'] = $r['ifi_fecha_factura'];
                $resultado[$cont]['numero_radicado_ministerio'] = $r['ifi_numero_radicado_ministerio'];
                $resultado[$cont]['documento_soporte'] = "<a href='././soportes/Interventoria/Informe Financiero/" . $r['ifi_documento_soporte'] . "' target='_blank'>{$r['ifi_documento_soporte']}</a>";
                $resultado[$cont]['descripcion'] = $r['ifi_descripcion'];
                $resultado[$cont]['valor_factura'] = $r['ifi_valor_factura'];
                $resultado[$cont]['amortizacion'] = $r['ifi_amortizacion'];
                $valor_factura = 0;
                $valor_amortizacion = 0;
                if ($r['ife_id'] != '3') {
                    $valor_factura = $r['ifi_valor_factura'];
                    $valor_amortizacion = $r['ifi_amortizacion'];
                }
                $resultado[$cont]['saldop_contrato'] = $this->getSaldoPContrato($valor_factura, $valor_amortizacion);
                $resultado[$cont]['saldop_amortizacion'] = $this->getSaldoPAmortizacion($valor_amortizacion);
                $resultado[$cont]['estado'] = $r['ife_nombre'];
                $fecha_pago = null;
                if ($r['ifi_fecha_pago'] == '') {
                    $fecha_pago = 'No aplica';
                } else {
                    $fecha_pago = $r['ifi_fecha_pago'];
                }
                $resultado[$cont]['fecha_pago'] = $fecha_pago;
                $resultado[$cont]['observaciones'] = $r['ifi_observaciones'];
                $cont++;
            }
        }
        return $resultado;
    }

    /**
     * Inserta un registro InformeFinanciero en la base de datos.
     * @param CInformeFinanciero $CInformeFinanciero
     * @return boolean
     */
    function insertInformeFinanciero($CInformeFinanciero) {
        $tabla = 'informe_financiero';
        $campos = " ifi_numero_pago, ifi_vigencia, ifi_numero_factura, ifi_fecha_factura,"
                . " ifi_numero_radicado_ministerio, ifi_documento_soporte, ifi_descripcion, ifi_valor_factura,"
                . " ifi_amortizacion, ife_id,  ifi_fecha_pago, ifi_observaciones";

        $valores = "'" . $CInformeFinanciero->getnumero_pago() . "','"
                . $CInformeFinanciero->getvigencia() . "','"
                . $CInformeFinanciero->getnumero_factura() . "','"
                . $CInformeFinanciero->getfecha_factura() . "','"
                . $CInformeFinanciero->getnumero_radicado_ministerio() . "','"
                . $CInformeFinanciero->getDocumento_soporte() . "','"
                . $CInformeFinanciero->getdescripcion() . "','"
                . $CInformeFinanciero->getvalor_factura() . "','"
                . $CInformeFinanciero->getamortizacion() . "','"
                . $CInformeFinanciero->getestado() . "','"
                . $CInformeFinanciero->getfecha_pago() . "','"
                . $CInformeFinanciero->getobservaciones() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Eliminar un registro de informe financiero dado su id.
     * @param integer $id
     * @return boolean
     */
    function deleteInformeFinanciero($id) {
        $tabla = 'informe_financiero';
        $predicado = "ifi_id = " . $id;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Obtiene un Informe Financiero dado su id.
     * @param integer $id
     * @return integer
     */
    function getInformeFinancieroById($id) {
        $sql = "select ifi_numero_pago, ifi_vigencia, ifi_numero_factura, ifi_fecha_factura,"
                . " ifi_numero_radicado_ministerio, ifi_documento_soporte, ifi_descripcion, ifi_valor_factura,"
                . " ifi_amortizacion,ife_id,ifi_fecha_pago, ifi_observaciones"
                . " from informe_financiero "
                . " where ifi_id = " . $id;
        //echo $sql;
        $r = $this->db->recuperarResultado($this->db->ejecutarConsulta($sql));

        if ($r)
            return $r;
        else
            return -1;
    }

    /**
     * Actualiza un InformeFinanciero dado su Id
     * @param CInformeFinanciero $CInformeFinanciero
     * @return boolean
     */
    function updateInformeFinanciero($CInformeFinanciero) {
        $tabla = 'informe_financiero';
        $campos = array('ifi_numero_pago', ' ifi_vigencia', ' ifi_numero_factura', ' ifi_fecha_factura', '
                  ifi_numero_radicado_ministerio', ' ifi_documento_soporte', ' ifi_descripcion', ' ifi_valor_factura', '
                  ifi_amortizacion', 'ife_id', '  ifi_fecha_pago', ' ifi_observaciones');
        $valores = array("'" . $CInformeFinanciero->getnumero_pago() . "'",
            "'" . $CInformeFinanciero->getvigencia() . "'",
            "'" . $CInformeFinanciero->getnumero_factura() . "'",
            "'" . $CInformeFinanciero->getfecha_factura() . "'",
            "'" . $CInformeFinanciero->getnumero_radicado_ministerio() . "'",
            "'" . $CInformeFinanciero->getDocumento_Soporte() . "'",
            "'" . $CInformeFinanciero->getdescripcion() . "'",
            "'" . $CInformeFinanciero->getvalor_factura() . "'",
            "'" . $CInformeFinanciero->getamortizacion() . "'",
            "'" . $CInformeFinanciero->getestado() . "'",
            "'" . $CInformeFinanciero->getfecha_pago() . "'",
            "'" . $CInformeFinanciero->getobservaciones() . "'");
        $condicion = " ifi_id = " . $CInformeFinanciero->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Obtiene el valor de las vigencias y adiciones de la base de datos
     */
    function valorBaseSaldoPContrato() {
        $sql = "select sum(monto_vigencia)as suma from vigenciainterventoria";
        $r = $this->db->ejecutarConsulta($sql);
        $w = mysql_fetch_array($r);
        $this->setSaldop_contrato($w['suma']);
    }

    /**
     * Obtiene el valor de las vigencias y adiciones del año 2013 de la base de datos
     */
    function valorBaseSaldoPAmortizacion() {
        $sql = "select sum(monto_vigencia)as suma from vigenciainterventoria where ano_vigencia = 2013 ";
        $r = $this->db->ejecutarConsulta($sql);
        $w = mysql_fetch_array($r);
        $this->setSaldop_amortizacion($w['suma']);
    }

    /**
     * Calcula el saldo pendiente del contrato
     * @param integer $valor_factura
     * @param integer $amortizacion
     * @return integer
     */
    function getSaldoPContrato($valor_factura, $amortizacion) {
        $this->setSaldop_contrato($this->getSaldop_contrato() - $valor_factura - $amortizacion);
        return $this->getSaldop_contrato();
    }

    /**
     * Calcula el saldo pendiente de la amortizacion
     * @param integer $valor_factura
     * @return integer
     */
    function getSaldoPAmortizacion($amortizacion) {
        $this->setSaldop_amortizacion($this->getSaldop_amortizacion() - $amortizacion);
        return $this->getSaldop_amortizacion();
    }

    /**
     * Obtiene los posibles estados de un informe financiero
     * @return array
     */
    function getEstadosInformeFinanciero() {
        $opciones = null;
        $sql = "select ife_id,ife_nombre from informe_financiero_estado";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $opciones[$cont]['id'] = $w['ife_id'];
                $opciones[$cont]['nombre'] = $w['ife_nombre'];
                $cont++;
            }
        }
        return $opciones;
    }

}
