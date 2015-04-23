<?php

/**
 * Clase Plana que maneja el informe financiero, contiene los metodos para
 * agregar, actualizar y eliminar.
 * @author Brian Kings
 * @version 1.0
 * @since 08/08/2014
 */
class CInformeFinanciero {

    /** Maneja el id del Informe Financiero. */
    var $id = null;
    /** Maneja el numero de pago del Informe Financiero. */
    var $numero_pago = null;
    /** Maneja la vigencia a la que pertence el Informe Financiero. */
    var $vigencia = null;
    /** Maneja el numero de factura del Informe Financiero. */
    var $numero_factura = null;
    /** Maneja la fecha de la factura del Informe Financiero. */
    var $fecha_factura = null;
    /** Maneja el numero de radico del ministerio del Informe Financiero. */
    var $numero_radicado_ministerio = null;
    /** Maneja el documento soporte del Informe Financiero.*/
    var $documento_soporte = null;
    /** Maneja la descripcion del Informe Financiero. */
    var $descripcion = null;
    /** Maneja le valor de la factura del Informe Financiero. */
    var $valor_factura = null;
    /** Maneja la amortizacion del Informe Financiero. */
    var $amortizacion = null;
    /** Almacena el saldo del contrato del Informe Financiero. */
    var $saldop_contrato = null;
    /** Almacena el saldo pendiente de Amortizacion del Informe Financiero. */
    var $saldop_amortizacion = null;
    /** Almacena el estado del Informe Financiero. */
    var $estado = null;
    /** Almacena la fecha de pago del Informe Financiero.*/
    var $fecha_pago = null;
    /** Almacena las observaciones del Informe Financiero. */
    var $observaciones = null;
    /** Almacena los archivos permitidos del Informe Financiero. */
    var $permitidos = array('pdf', 'doc', 'xls', 'ppt', 'docx',
                            'xlsx', 'gif', 'jpg', 'png', 'tif', 'zip', 'rar');
    /** Almacena el driver con la conexion a la base de datos. */
    var $dd = null;

    /**
     * Constructor de la clase
     * @param integer $id
     * @param \CData $dd
     */
    function CInformeFinanciero($id, $dd) {
        $this->id = $id;
        $this->dd = $dd;
    }

    /**
     * Obtiene el id del Informe Financiero.
     * @return type
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Obtiene el numero de pago del Informe Financiero.
     * @return type
     */
    public function getNumero_pago() {
        return $this->numero_pago;
    }

    /**
     * Obtiene la Vigencia del Informe Financiero.
     * @return type
     */
    public function getVigencia() {
        return $this->vigencia;
    }

    /**
     * Obtiene el numero de factura del Informe Financiero.
     * @return type
     */
    public function getNumero_factura() {
        return $this->numero_factura;
    }

    /**
     * Obtiene la fecha de factura del Informe Financiero.
     * @return type
     */
    public function getFecha_factura() {
        return $this->fecha_factura;
    }

    /**
     * Obtiene el numero de radicado del ministerios del Informe Financiero.
     * @return type
     */
    public function getNumero_radicado_ministerio() {
        return $this->numero_radicado_ministerio;
    }

    /**
     * Obtiene el documento soporte del Informe Financiero.
     * @return type
     */
    public function getDocumento_soporte() {
        return $this->documento_soporte;
    }

    /**
     * Obtiene la descripcion del Informe Financiero.
     * @return type
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Obtiene el valor de la factura del Informe Financiero.
     * @return type
     */
    public function getValor_factura() {
        return $this->valor_factura;
    }

    /**
     * Obtiene la amortizacion del Informe Financiero.
     * @return type
     */
    public function getAmortizacion() {
        return $this->amortizacion;
    }

    /**
     * Obtiene el saldo pendiente del contrato del Informe Financiero.
     * @return type
     */
    public function getSaldop_contrato() {
        return $this->saldop_contrato;
    }

    /**
     * Obtiene el saldo pendiente de amortizacion del Informe Financiero.
     * @return type
     */
    public function getSaldop_amortizacion() {
        return $this->saldop_amortizacion;
    }

    /**
     * Obtiene el estado del Informe Financiero.
     * @return type
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * Obtiene la fecha de pago del Informe Financiero.
     * @return type
     */
    public function getFecha_pago() {
        return $this->fecha_pago;
    }

    /**
     * Obtiene las observaciones del informe financiero.
     * @return type
     */
    public function getObservaciones() {
        return $this->observaciones;
    }

    /**
     * Asigna el id del informe financiero.
     * @param type $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Asigna el numero de pago del informe financiero.
     * @param type $numero_pago
     */
    public function setNumero_pago($numero_pago) {
        $this->numero_pago = $numero_pago;
    }

    /**
     * Asigna la vigencia del Informe Financiero.
     * @param type $vigencia
     */
    public function setVigencia($vigencia) {
        $this->vigencia = $vigencia;
    }

    /**
     * Asigna el numero de factura del Informe Financiero.
     * @param type $numero_factura
     */
    public function setNumero_factura($numero_factura) {
        $this->numero_factura = $numero_factura;
    }

    /**
     * Asigna la fecha de factura del Informe Financiero.
     * @param type $fecha_factura
     */
    public function setFecha_factura($fecha_factura) {
        $this->fecha_factura = $fecha_factura;
    }

    /**
     * Asigna el numero de radicado de ministerio del Informe Financiero.
     * @param type $numero_radicado_ministerio
     */
    public function setNumero_radicado_ministerio($numero_radicado_ministerio) {
        $this->numero_radicado_ministerio = $numero_radicado_ministerio;
    }

    /**
     * Asigna el documento soporte del Informe Ministerio.
     * @param type $documento_soporte
     */
    public function setDocumento_soporte($documento_soporte) {
        $this->documento_soporte = $documento_soporte;
    }

    /**
     * Asigna la descripcion del Informe Financiero.
     * @param type $descripcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    /**
     * Asigna el valor de la factura del Informe Financiero.
     * @param type $valor_factura
     */
    public function setValor_factura($valor_factura) {
        $this->valor_factura = str_replace('.', '', $valor_factura);
    }

    /**
     * Asigna el valor de Amortizacion del Informe Financiero.
     * @param type $amortizacion
     */
    public function setAmortizacion($amortizacion) {
        $this->amortizacion = str_replace('.', '', $amortizacion);
    }

    /**
     * Asigna el saldo pendiente de contrato del informe financiero.
     * @param type $saldop_contrato
     */
    public function setSaldop_contrato($saldop_contrato) {
        $this->saldop_contrato = $saldop_contrato;
    }

    /**
     * Asigna el saldo pendiente de amortizacion del Informe Financiero.
     * @param type $saldop_amortizacion
     */
    public function setSaldop_amortizacion($saldop_amortizacion) {
        $this->saldop_amortizacion = $saldop_amortizacion;
    }

    /**
     * Asigna el estado del Informe Financiero.
     * @param type $estado
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /**
     * Asigna la fehca de pago del Informe Financiero.
     * @param type $fecha_pago
     */
    public function setFecha_pago($fecha_pago) {
        $this->fecha_pago = $fecha_pago;
    }

    /**
     * Asigna las observaciones del Informe Financiero.
     * @param type $observaciones
     */
    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }
    
    /**
     * Almacena la informacion del informe financiero.
     * Copiamos el documento soporte a la ruta correspondiente y enviamos 
     * los campos necesarios para almacenar un Informe Financiero.
     * @param string $archivo
     * @return string
     */
    function saveInformeFinanciero($archivo) {
        $r = "";
        $extension = explode(".", $archivo['name']);
        $num = count($extension) - 1;
        $noMatch = 0;
        foreach ($this->permitidos as $p) {
            if (strcasecmp($extension[$num], $p) == 0)
                $noMatch = 1;
        }
        if ($archivo['name'] != null) {
            if ($noMatch == 1) {
                if ($archivo['size'] < MAX_SIZE_DOCUMENTOS) {
                    $nombre_compuesto = strtoupper($archivo['name']);
                    $ruta = (RUTA_DOCUMENTOS . "/Interventoria/Informe Financiero/");
                    $carpetas = explode("/", substr($ruta, 0, strlen($ruta) - 1));
                    $cad = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'];
                    $ruta_destino = '';
                    foreach ($carpetas as $c) {
                        if (strlen($ruta_destino) > 0) {
                            $ruta_destino .= "/" . $c;
                        } else {
                            $ruta_destino = $c;
                        }
                        if (!is_dir($ruta_destino)) {
                            mkdir($ruta_destino, 0777);
                        } else {
                            chmod($ruta_destino, 0777);
                        }
                    }
                    if (!move_uploaded_file($archivo['tmp_name'], utf8_decode($ruta . $nombre_compuesto))) {
                        $r = ERROR_COPIAR_ARCHIVO;
                    } else {
                        $this->setDocumento_soporte($nombre_compuesto);
                        $i = $this->dd->insertInformeFinanciero($this);

                        if ($i == "true") {
                            $r = INFORME_FINANCIERO_ADD;
                        } else {
                            $r = ERROR_ADD_INFORME_FINANCIERO;
                        }
                    }
                } else {
                    $r = ERROR_SIZE_INFORME_FINANCIERO;
                }
            } else {
                $r = ERROR_FORMATO_INFORME_FINANCIERO;
            }
        } else {
            $r = ERROR_CONFIGURACION_RUTA;
        }
        return $r;
    }

    /**
     * Elimina el informe financiero dado el id del mismo.
     * @return string
     */
    function deletInformeFinanciero() {
        $ruta = (RUTA_DOCUMENTOS . "/Interventoria/Informe Financiero/");
        $r = $this->dd->deleteInformeFinanciero($this->id);
        if ($r == 'true') {
            unlink(strtoupper($ruta) . ($this->getDocumento_soporte()));
            $msg = INFORME_FINANCIERO_BORRADO;
        } else {
            $msg = ERROR_DE_INFORME_FINANCIERO;
        }
        return $msg;
    }

    /**
     * Carga la informacion del informe financiero y lo almacena en este objeto.
     */
    function loadInformeFinanciero() {
        $r = $this->dd->getInformeFinancieroById($this->id);
        if ($r != -1) {
            $this->numero_pago = $r['ifi_numero_pago'];
            $this->vigencia = $r['ifi_vigencia'];
            $this->numero_factura = $r['ifi_numero_factura'];
            $this->fecha_factura = $r['ifi_fecha_factura'];
            $this->numero_radicado_ministerio = $r['ifi_numero_radicado_ministerio'];
            $this->documento_soporte = $r['ifi_documento_soporte'];
            $this->descripcion = $r['ifi_descripcion'];
            $this->valor_factura = number_format($r['ifi_valor_factura'], 0, ',', '.');
            $this->amortizacion = number_format($r['ifi_amortizacion'], 0, ',', '.');
            $this->estado = $r['ife_id'];
            $this->fecha_pago = $r['ifi_fecha_pago'];
            $this->observaciones = $r['ifi_observaciones'];
        } else {
            $this->numero_pago = '';
            $this->vigencia = '';
            $this->numero_factura = '';
            $this->fecha_factura = '';
            $this->numero_radicado_ministerio = '';
            $this->documento_soporte = '';
            $this->descripcion = '';
            $this->valor_factura = '';
            $this->amortizacion = '';
            $this->estado = '';
            $this->fecha_pago = '';
            $this->observaciones = '';
        }
    }

    /**
     * Actualiza un informe financiero.
     * @param type $archivo
     * @param type $archivo_anterior
     * @return string
     */
    function saveEditInformeFinanciero($archivo, $archivo_anterior) {
        $r = "";
        $extension = explode(".", $archivo['name']);
        $num = count($extension) - 1;
        $noMatch = 0;
        foreach ($this->permitidos as $p) {
            if (strcasecmp($extension[$num], $p) == 0)
                $noMatch = 1;
        }
        if ($archivo['name'] != null) {
            if ($noMatch == 1) {
                if ($archivo['size'] < MAX_SIZE_DOCUMENTOS) {
                    $nombre_compuesto = strtoupper($archivo['name']);
                    $ruta = (RUTA_DOCUMENTOS . "/Interventoria/Informe Financiero/");
                    unlink(strtoupper($ruta) . $archivo_anterior);
                    $carpetas = explode("/", substr($ruta, 0, strlen($ruta) - 1));
                    $cad = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'];
                    $ruta_destino = '';
                    foreach ($carpetas as $c) {
                        if (strlen($ruta_destino) > 0) {
                            $ruta_destino .= "/" . $c;
                        } else {
                            $ruta_destino = $c;
                        }
                        if (!is_dir($ruta_destino)) {
                            mkdir($ruta_destino, 0777);
                        } else {
                            chmod($ruta_destino, 0777);
                        }
                    } 
                    echo 'H'.$ruta . $nombre_compuesto;
                    if (!move_uploaded_file($archivo['tmp_name'], utf8_decode($ruta . $nombre_compuesto))) {
                        $r = ERROR_COPIAR_ARCHIVO;
                    } else {
                        $this->documento_soporte = $nombre_compuesto;
                        $i = $this->dd->updateInformeFinanciero($this);

                        if ($i == "true") {
                            $r = INFORME_FINANCIERO_EDIT;
                        } else {
                            $r = ERROR_EDIT_INFORME_FINANCIERO;
                        }
                    }
                } else {
                    $r = ERROR_SIZE_INFORME_FINANCIERO;
                }
            } else {
                $r = ERROR_FORMATO_INFORME_FINANCIERO;
            }
            return $r;
        } else {
            $this->setDocumento_soporte($archivo_anterior);
            $i = $this->dd->updateInformeFinanciero($this);
            if ($i == 'true') {
                $msg = INFORME_FINANCIERO_EDIT;
            } else {
                $msg = ERROR_EDIT_INFORME_FINANCIERO;
            }
            return $msg;
        }
    }
}
