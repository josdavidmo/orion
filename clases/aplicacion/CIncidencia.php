<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CIncidencia{
    
    var $id;
    var $fecha;
    var $tipo;
    var $descripcion;
    var $archivo;
    var $estado;
    var $usuario;
    var $opcion;
    var $db;
    
    function CIncidencia($id, $db){
        $this->id = $id;
        $this->db = $db;
    }
    
    public function getIdIncidencia(){
        return $this->id;
    }

    public function getFechaIncidencia() {
        return $this->fecha;
    }

    public function getTipoIncidencia() {
        return $this->tipo;
    }

    public function getDescripcionIncidencia() {
        return $this->descripcion;
    }

    public function getArchivoIncidencia() {
        return $this->archivo;
    }

    public function getEstadoIncidencia() {
        return $this->estado;
    }

    public function getUsuarioIncidencia() {
        return $this->usuario;
    }

    public function getDb() {
        return $this->db;
    }
    
    public function getOpcionIncidencia() {
        return $this->opcion;
    }

    public function setIdIncidencia($id) {
        $this->id = $id;
    }

    public function setFechaIncidencia($fecha) {
        $this->fecha = $fecha;
    }

    public function setTipoIncidencia($tipo) {
        $this->tipo = $tipo;
    }

    public function setDescripcionIncidencia($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setArchivoIncidencia($archivo) {
        $this->archivo = $archivo;
    }

    public function setEstadoIncidencia($estado) {
        $this->estado = $estado;
    }

    public function setUsuarioIncidencia($usuario) {
        $this->usuario = $usuario;
    }

    public function setDbIncidencia($db) {
        $this->db = $db;
    }

    public function setOpcionIncidencia($opcion) {
        $this->opcion = $opcion;
    }

    function loadIncidencia(){
        $r = $this->db->getInicidenciaById($this->id);
        if($r!=-1){
            $this->fecha        =$r['inc_fecha'];
            $this->tipo         =$r['inc_tipo'];
            $this->descripcion  =$r['inc_desc'];
            $this->archivo      =$r['inc_archivo'];
            $this->estado       =$r['inc_estado'];
            $this->usuario      =$r['inc_usuario'];
            $this->opcion       =$r['inc_opcion'];
        }else{
            $this->fecha        ="";
            $this->tipo         ="";
            $this->descripcion  ="";
            $this->archivo      ="";
            $this->estado       ="";
            $this->usuario      ="";
            $this->opcion       ="";
        }
    }
    
    function insertNewIncidencia(){
        $r = "";
        if ($this->archivo['name'] != null) {
            if (true) {
                if ($this->archivo['size'] < MAX_SIZE_DOCUMENTOS) {
                    $nombre_compuesto = strtoupper($this->archivo['name']);
                    $ruta = (RUTA_INCIDENCIAS . "/");
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
                        $this->setArchivoIncidencia($nombre_compuesto);
                        $i = $this->db->insertIncidencia($this);
                        if ($i == "true") {
                            $r = INCIDENCIA_AGREGADA;
                        } else {
                            $r = ERROR_ADD_INCIDENCIA;
                        }
                    }
                } else {
                    $r = ERROR_SIZE_INFORME_FINANCIERO;
                }
            } else {
                $r = ERROR_FORMATO_INFORME_FINANCIERO;
            }
        } else {
            $this->setArchivoIncidencia(null);
            $i = $this->db->insertIncidencia($this);
            if ($i == "true") {
                $r = INCIDENCIA_AGREGADA;
            } else {
                $r = ERROR_ADD_INCIDENCIA;
            }
        }
        return $r;
    }
    
    function updateIncidencia(){
        $r = "";
        if ($this->archivo['name'] != null) {
            if (true) {
                if ($this->archivo['size'] < MAX_SIZE_DOCUMENTOS) {
                    $nombre_compuesto = strtoupper($this->archivo['name']);
                    $ruta = (RUTA_INCIDENCIAS . "/");
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
                        $this->setArchivoIncidencia($nombre_compuesto);
                        $i = $this->db->updateIncidencia($this);
                        if ($i == "true") {
                            $r = INCIDENCIA_ACTUALIZADA;
                        } else {
                            $r = ERROR_UPDATE_INCIDENCIA;
                        }
                                        }
                } else {
                    $r = ERROR_SIZE_INFORME_FINANCIERO;
                }
            } else {
                $r = ERROR_FORMATO_INFORME_FINANCIERO;
            }
        } else {
            $this->setArchivoIncidencia(null);
            $i = $this->db->updateIncidencia($this);
            if ($i == "true") {
                $r = INCIDENCIA_ACTUALIZADA;
            } else {
                $r = ERROR_UPDATE_INCIDENCIA;
            }
        }
        return $r;
    }
    
    function deleteIncidencia(){
        $i = $this->db->deleteIncidencia($this->id);
        if ($i == "true") {
            $r = INCIDENCIA_ELIMINADA;
        } else {
            $r = ERROR_DELETE_INCIDENCIA;
        }
        return $r;
    }
}

?>