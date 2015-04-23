<?php

/**
 * Clase Planeacion Autocontrol.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.11.14
 * @copyright SERTIC
 */
class CPlaneacionAutocontrol {
    
    /** Almacena el id de la planeacion autocontrol. */
    var $id;
    /** Almacena el responsable de la planeacion autocontrol. */
    var $objetivos;
    /** Almacena el responsable de la planeacion autocontrol. */
    var $responsable;
    /** Almacena el responsable PNC de la planeacion autocontrol. */
    var $responsablePNC;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $objetivos
     * @param type $responsable
     * @param type $responsablePNC
     */
    function CPlaneacionAutocontrol($id, $objetivos, $responsable, 
                                    $responsablePNC) {
        $this->id = $id;
        $this->objetivos = $objetivos;
        $this->responsable = $responsable;
        $this->responsablePNC = $responsablePNC;
    }
    
    /**
     * Obtiene el id del autocontrol.
     * @return type
     */
    function getId() {
        return $this->id;
    }

    /**
     * Obtiene los objetivos del autocontrol.
     * @return type
     */
    function getObjetivos() {
        return $this->objetivos;
    }

    /**
     * Obtiene el responsable del autocontrol.
     * @return type
     */
    function getResponsable() {
        return $this->responsable;
    }

    /**
     * Obtine el responsable PNC del autocontrol.
     * @return type
     */
    function getResponsablePNC() {
        return $this->responsablePNC;
    }

    /**
     * Asigna el id del autocontrol.
     * @param type $id
     */
    function setId($id) {
        $this->id = $id;
    }

    /**
     * Asigna los objetivos del autocontrol.
     * @param type $objetivos
     */
    function setObjetivos($objetivos) {
        $this->objetivos = $objetivos;
    }

    /**
     * Asigna el responsable del autocontrol.
     * @param type $responsable
     */
    function setResponsable($responsable) {
        $this->responsable = $responsable;
    }

    /**
     * Asigna el responsable PNC del autocontrol.
     * @param type $responsablePNC
     */
    function setResponsablePNC($responsablePNC) {
        $this->responsablePNC = $responsablePNC;
    }

}
