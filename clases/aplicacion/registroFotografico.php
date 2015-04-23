<?php


class registroFotografico {
    var $idRegistroFotografico;
    var $descripcionRegistroFotografico;
    var $archivo;
    var $idActividad;
    
    function __construct($idRegistroFotografico, $descripcionRegistroFotografico, $archivo, $IdActividad) {
        $this->idRegistroFotografico = $idRegistroFotografico;
        $this->descripcionRegistroFotografico = $descripcionRegistroFotografico;
        $this->archivo = $archivo;
        $this->idActividad = $IdActividad;
    }

    function getIdRegistroFotografico() {
        return $this->idRegistroFotografico;
    }

    function getDescripcionRegistroFotografico() {
        return $this->descripcionRegistroFotografico;
    }

    function getArchivo() {
        return $this->archivo;
    }

    function getIdActividad() {
        return $this->idActividad;
    }
    
    function setIdRegistroFotografico($idRegistroFotografico) {
        $this->idRegistroFotografico = $idRegistroFotografico;
    }

    function setDescripcionRegistroFotografico($descripcionRegistroFotografico) {
        $this->descripcionRegistroFotografico = $descripcionRegistroFotografico;
    }

    function setArchivo($archivo) {
        $this->archivo = $archivo;
    }

    function setIdActividad($IdActividad) {
        $this->idActividad = $IdActividad;
    }

}
?>
