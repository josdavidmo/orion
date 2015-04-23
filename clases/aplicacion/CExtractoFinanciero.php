<?php

/**
 * PNCAV
 *
 * <ul>
 * <li> Redcom Ltda <www.redcom.com.co></li>
 * <li> Proyecto PNCAV</li>
 * </ul>
 */

/**
 * Usada para todas las funciones de negocio referente a extractos para el modulo financiero
 *
 * @package  clases
 * @subpackage datos
 */
class CExtractoFinanciero {

    var $id;
    var $cuenta;
    var $cuenta_nombre;
    var $cuenta_numero;
    var $mes;
    var $anio;
    var $observaciones;
    var $documento_soporte;
    var $data;
    
    var $permitidos_soporte = array('pdf','zip','rar','7z');
    var $permitidos_movimiento = array('xls', 'xlsx');
    
    function __construct($id,$data) {
        $this->id = $id;
        $this->data = $data;
    }
    
    function loadExtracto(){
        $r = $this->data->getExtractoById($this->id);
        if($r){
            $this->cuenta               = $r['cuenta'];
            $this->cuenta_nombre        = $r['cuenta_nombre'];
            $this->cuenta_numero        = $r['cuenta_numero'];
            $this->mes                  = $r['mes'];
            $this->anio                 = $r['anio'];
            $this->observaciones        = $r['observaciones'];
            $this->documento_soporte    = $r['documento_soporte'];
        }
    }
    
    function saveExtracto(){
        
        $tipo_cuenta = $this->data->getTipoCuenta($this->cuenta);
        $extension_soporte = explode(".", $this->documento_soporte['name']);
        $pos_soporte = count($extension_soporte) - 1;
        $valido_soporte = false;
        
        foreach ($this->permitidos_soporte as $p) {
            if (strcasecmp($extension_soporte[$pos_soporte], $p) == 0)
                $valido_soporte = true;
        }
           
        if(!$valido_soporte){
            return ERROR_DOCUMENTO_SOPORTE_NO_VALIDO;
        }
 
        if($this->documento_soporte['name']==NULL || $this->documento_soporte['name'] == ""){
            return ERROR_DOCUMENTO_SOPORTE_VACIO;
        }
        

        
        if ($this->documento_soporte['size'] > MAX_SIZE_DOCUMENTOS) {
            return ERROR_TAM_ARCHIVO_SOPORTE;
        }
        
        $dirOperador = $this->data->getDirectorioOperador(OPERADOR_DEFECTO);
        $ruta = (RUTA_EXTRACTOS_SOPORTES . "/" . $dirOperador . "/");
        
        $carpetas = explode("/", substr($ruta, 0, strlen($ruta) - 1));
        $ruta_destino = '';

        foreach ($carpetas as $c) {
            if (strlen($ruta_destino) > 0) {
                $ruta_destino .= "/" . $c;
            } else {
                $ruta_destino = $c;
            }
            //echo $ruta_destino."<br>";
            if (!is_dir($ruta_destino)) {
                mkdir($ruta_destino, 0777);
            } else {
                chmod($ruta_destino, 0777);
            }
        }
        
        $nombre_compuesto_soporte = EXTRACTO_DOCUMENTO_SOPORTE."-".$this->cuenta.$this->anio.$this->mes.".".$extension_soporte[$pos_soporte];
        
        if(!move_uploaded_file($this->documento_soporte['tmp_name'], utf8_decode($ruta . $nombre_compuesto_soporte))){
            return ERROR_COPIAR_ARCHIVO_EXTRACTO_SOPORTE;
        }
        $this->documento_soporte = $nombre_compuesto_soporte;

        
        $i = $this->data->insertExtractoInterventoria($this);
        if ($i == "true") {
            //-----------------------------------------------
            $cuenta = $this->cuenta;
            $mes = $this->mes;
            $anio = $this->anio;
            $dataRendimiento = new CRendimientoFinancieroData($this->data->db);
            
            $rendimiento = new CRendimientoFinanciero('',$dataRendimiento);
            $rendimiento->cuenta = $cuenta;
            $rendimiento->mes = $mes;
            $rendimiento->anio = $anio;
            
            $rendimiento->saveRendimiento(false);
            //-----------------------------------------------
            return EXTRACTO_AGREGADO;
        } else {
            return ERROR_ADD_EXTRACTO;
        }        
    }
    
    function updateExtracto(){
        
        $tipo_cuenta = $this->data->getTipoCuenta($this->cuenta);
        
        $dirOperador = $this->data->getDirectorioOperador(OPERADOR_DEFECTO);
        $ruta = (RUTA_EXTRACTOS_SOPORTES . "/" . $dirOperador . "/");
        $carpetas = explode("/", substr($ruta, 0, strlen($ruta) - 1));
        $ruta_destino = '';

        foreach ($carpetas as $c) {
            if (strlen($ruta_destino) > 0) {
                $ruta_destino .= "/" . $c;
            } else {
                $ruta_destino = $c;
            }
            //echo $ruta_destino."<br>";
            if (!is_dir($ruta_destino)) {
                mkdir($ruta_destino, 0777);
            } else {
                chmod($ruta_destino, 0777);
            }
        }
        
        $extension_soporte = explode(".", $this->documento_soporte['name']);
        $pos_soporte = count($extension_soporte) - 1;
        $valido_soporte = false;
        foreach ($this->permitidos_soporte as $p) {
            if (strcasecmp($extension_soporte[$pos_soporte], $p) == 0)
                $valido_soporte = true;
        }
        if(!$valido_soporte){
            return ERROR_DOCUMENTO_SOPORTE_NO_VALIDO;
        }
        if($this->documento_soporte['name']==NULL || $this->documento_soporte['name'] == ""){
            return ERROR_DOCUMENTO_SOPORTE_VACIO;
        }
        if ($this->documento_soporte['size'] > MAX_SIZE_DOCUMENTOS) {
            return ERROR_TAM_ARCHIVO_SOPORTE;
        }
        $nombre_compuesto_soporte = EXTRACTO_DOCUMENTO_SOPORTE."-".$this->cuenta.$this->anio.$this->mes.".".$extension_soporte[$pos_soporte];
        if(!move_uploaded_file($this->documento_soporte['tmp_name'], utf8_decode($ruta . $nombre_compuesto_soporte))){
            return ERROR_COPIAR_ARCHIVO_EXTRACTO_SOPORTE;
        }
       
        $this->documento_soporte = $nombre_compuesto_soporte;
        
        $i = $this->data->updateExtracto($this);
        if ($i == "true") {
            return EXTRACTO_EDITADO;
        } else {
            return ERROR_EDIT_EXTRACTO;
        }
        //die("ok");
        
    }
    
    function deleteExtracto(){
        
    }

}
