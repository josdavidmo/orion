<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CPoliza{
  
    var $id = null;
    var $numContrato = null;
    var $objeto = null;
    var $plazo = null;
    var $fechaSuscripcion = null;
    var $contratante = null;
    var $contratista = null;
    var $numeroPoliza = null;
    var $aseguradora = null;
    var $tomador = null;
    var $asegurado = null;
    var $beneficiario = null;
    var $amparo = null;
    var $porcentaje = null;
    var $valorAsegurado = null;
    var $vigenciaInicio = null;
    var $vigenciaFin = null;
    var $observaciones = null;
    var $archivo;
    var $db;
    
    function CPoliza($id,$db){
        $this->db=$db;
        $this->id=$id;
    }
    public function getId() {
        return $this->id;
    }

    public function getNumContrato() {
        return $this->numContrato;
    }

    public function getObjeto() {
        return $this->objeto;
    }

    public function getPlazo() {
        return $this->plazo;
    }

    public function getFechaSuscripcion() {
        return $this->fechaSuscripcion;
    }

    public function getContratante() {
        return $this->contratante;
    }

    public function getContratista() {
        return $this->contratista;
    }

    public function getNumeroPoliza() {
        return $this->numeroPoliza;
    }

    public function getAseguradora() {
        return $this->aseguradora;
    }

    public function getTomador() {
        return $this->tomador;
    }

    public function getAsegurado() {
        return $this->asegurado;
    }

    public function getBeneficiario() {
        return $this->beneficiario;
    }

    public function getAmparo() {
        return $this->amparo;
    }

    public function getPorcentajePoliza() {
        return $this->porcentaje;
    }

    public function getValorAsegurado() {
        return $this->valorAsegurado;
    }

    public function getVigenciaInicio() {
        return $this->vigenciaInicio;
    }

    public function getVigenciaFin() {
        return $this->vigenciaFin;
    }

    public function getObservacionesPoliza() {
        return $this->observaciones;
    }

    public function getDb() {
        return $this->db;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setNumContrato($numContrato) {
        $this->numContrato = $numContrato;
    }

    public function setObjeto($objeto) {
        $this->objeto = $objeto;
    }

    public function setPlazo($plazo) {
        $this->plazo = $plazo;
    }

    public function setFechaSuscripcion($fechaSuscripcion) {
        $this->fechaSuscripcion = $fechaSuscripcion;
    }

    public function setContratante($contratante) {
        $this->contratante = $contratante;
    }

    public function setContratista($contratista) {
        $this->contratista = $contratista;
    }

    public function setNumeroPoliza($numeroPoliza) {
        $this->numeroPoliza = $numeroPoliza;
    }

    public function setAseguradora($aseguradora) {
        $this->aseguradora = $aseguradora;
    }

    public function setTomador($tomador) {
        $this->tomador = $tomador;
    }

    public function setAsegurado($asegurado) {
        $this->asegurado = $asegurado;
    }

    public function setBeneficiario($beneficiario) {
        $this->beneficiario = $beneficiario;
    }

    public function setAmparo($amparo) {
        $this->amparo = $amparo;
    }

    public function setPorcentaje($porcentaje) {
        $this->porcentaje = $porcentaje;
    }

    public function setValorAsegurado($valorAsegurado) {
        $this->valorAsegurado = $valorAsegurado;
    }

    public function setVigenciaInicio($vigenciaInicio) {
        $this->vigenciaInicio = $vigenciaInicio;
    }

    public function setVigenciaFin($vigenciaFin) {
        $this->vigenciaFin = $vigenciaFin;
    }

    public function setObservacionesPoliza($observaciones) {
        $this->observaciones = $observaciones;
    }

    public function setDb($db) {
        $this->db = $db;
    }
    
    public function getArchivoPoliza() {
        return $this->archivo;
    }

    public function setArchivoPoliza($archivo) {
        $this->archivo = $archivo;
    }

        function loadPoliza($tabla){
        $r = $this->db->getPolizaById($this->id, $tabla);
        if($r!=-1){
            $this->numContrato      = $r['pol_numero_contrato'];
            $this->objeto           = $r['pol_objeto'];
            $this->plazo            = $r['pol_plazo'];
            $this->fechaSuscripcion = $r['pol_fecha_suscripcion'];
            $this->contratante      = $r['pol_contratante'];
            $this->contratista      = $r['pol_contratista'];
            $this->numeroPoliza     = $r['pol_numero_poliza'];
            $this->aseguradora      = $r['pol_aseguradora'];
            $this->tomador          = $r['pol_tomador'];
            $this->asegurado        = $r['pol_asegurado'];
            $this->beneficiario     = $r['pol_beneficiario'];
            $this->amparo           = $r['pol_amparo'];
            $this->porcentaje       = $r['pol_porcentaje'];
            $this->valorAsegurado   = $r['pol_valor_asegurado'];
            $this->vigenciaInicio   = $r['pol_vigencia_inicio'];
            $this->vigenciaFin      = $r['pol_vigencia_fin'];
            $this->observaciones    = $r['pol_observaciones'];
            $this->archivo          = $r['pol_archivo'];
        }else{
            $this->numContrato = "";
            $this->objeto = "";
            $this->plazo = "";
            $this->fechaSuscripcion = "";
            $this->contratante = "";
            $this->contratista = "";
            $this->numeroPoliza = "";
            $this->aseguradora = "";
            $this->tomador = "";
            $this->asegurado = "";
            $this->beneficiario = "";
            $this->amparo = "";
            $this->porcentaje = "";
            $this->valorAsegurado = "";
            $this->vigenciaInicio = "";
            $this->vigenciaFin = "";
            $this->observaciones = "";
            $this->archivo = "";
        }
    }
    
    function saveNewPoliza($tabla){
		$r = "";
        if ($this->archivo['name'] != null) {
            if (true) {
                if ($this->archivo['size'] < MAX_SIZE_DOCUMENTOS) {
                    $nombre_compuesto = strtoupper($this->archivo['name']);
                    $ruta = (RUTA_POLIZAS . "/");
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
                    if (!move_uploaded_file($this->archivo['tmp_name'], utf8_decode($ruta . $nombre_compuesto))) {
                        $r = ERROR_COPIAR_ARCHIVO;
                    } else {
                        $this->setArchivoPoliza($nombre_compuesto);
                        $i = $this->db->insertPoliza($this, $tabla);
                        if ($i == "true") {
                            $r = POLIZA_AGREGADA;
                        } else {
                            $r = ERROR_ADD_POLIZA;
                        }
                    }
                } else {
                    $r = ERROR_SIZE_INFORME_FINANCIERO;
                }
            } else {
                $r = ERROR_FORMATO_INFORME_FINANCIERO;
            }
        } else {
            $this->setArchivoPoliza(null);
            $i = $this->db->insertPoliza($this, $tabla);
            if ($i == "true") {
                $r = POLIZA_AGREGADA;
            } else {
                $r = ERROR_ADD_POLIZA;
            }
        }
        return $r;
    }
    
    function deletePoliza($tabla){
        $i = $this->db->deletePoliza($this->id, $tabla);
        if ($i == "true") {
            $r = POLIZA_ELIMINADA;
        } else {
            $r = ERROR_DELETE_POLIZA;
        }
        return $r;
    }
    
    function updatePoliza($tabla){
        $r = "";
        if ($this->archivo['name'] != null) {
            if (true) {
                if ($this->archivo['size'] < MAX_SIZE_DOCUMENTOS) {
                    $nombre_compuesto = strtoupper($this->archivo['name']);
                    $ruta = (RUTA_POLIZAS . "/");
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
                    if (!move_uploaded_file($this->archivo['tmp_name'], utf8_decode($ruta . $nombre_compuesto))) {
                        $r = ERROR_COPIAR_ARCHIVO;
                    } else {
                        $this->setArchivoPoliza($nombre_compuesto);
                        $i = $this->db->updatePoliza($this, $tabla);
                        if ($i == "true") {
                            $r = POLIZA_ACTUALIZADA;
                        } else {
                            $r = ERROR_UPDATE_POLIZA;
                        }
                                        }
                } else {
                    $r = ERROR_SIZE_INFORME_FINANCIERO;
                }
            } else {
                $r = ERROR_FORMATO_INFORME_FINANCIERO;
            }
        } else {
            $this->setArchivoPoliza(null);
			$i = $this->db->updatePoliza($this, $tabla);
            if ($i == "true") {
                $r = POLIZA_ACTUALIZADA;
            } else {
                $r = ERROR_UPDATE_POLIZA;
            }
        }
        return $r;
    }

}
?>