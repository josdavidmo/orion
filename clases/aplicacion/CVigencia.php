<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CIngresos
 *
 * @author Personal
 */
class CVigencia {

    //put your codevar $id = null;
    var $id = null;
    var $ano = null;
    var $monto = null;
    var $tipo = null;
    var $database = null;

//definimos los atributos de la clase y creamos el constructor de la misma

    function CVigencia($id, $ano, $monto, $tipo, $database) {
        $this->id = $id;
        $this->ano = $ano;
        $this->monto = $monto;
        $this->tipo = $tipo;
        $this->database = $database;
    }

    function getIdIngreso() {
        return $this->id;
    }

    function getano() {
        return $this->ano;
    }

    function getmonto() {
        return $this->monto;
    }

    function gettipo() {
        return $this->tipo;
    }

    /**
     * Cargaringreso, permite cargar el objeto ingreso para modificarlo y eliminarlo 
     */
    function Cargaringreso() {
        $r = $this->database->obtenerVigenciaPorId($this->id);
        if ($r != -1) {
            $this->id = $r['id_Vigencia'];
            $this->ano = $r['ano_Vigencia'];
            $this->monto = $r['monto_Vigencia'];
            $this->tipo = $r['tipo_Vigencia'];
        } else {
            $this->id = '';
            $this->ano = '';
            $this->monto = '';
            $this->tipo = '';
        }
    }

    /**
     * EliminarIngreso, elimina el objeto ingreso de la base de datos
     */
    function EliminarIngreso($id) {

        $r = $this->database->eliminarVigencia($id);

        if ($r == 1) {
            $ms1 = MENSAJE_BORRAR_EXITO_INGRESO;
        } else {
            $ms1 = MENSAJE_BORRAR_INGRESO_FRACASO;
        }
        return $ms1;
    }

    /**
     * actualizarIngresos, actualiza los atributos  del objeto ingreso de la base de datos
     */
    function actualizarIngresos($id, $monto) {

        $r = $this->database->actualizaringreso($id, $monto);

        if ($r == 1) {
            $ms = MENSAJE_EDITAR_EXITO_INGRESO;
        } else {
            $ms = MENSAJE_BORRAR_FRACASO_INGRESO;
        }
        return $ms;
    }

    //valida que el año que se este ingresando sea una vigencia o una adicion
    function validarano($ano) {
        $r = $this->database->ObtenerYear($ano);
        return $r;
    }
    //me permite validad si una vigencia tiene adiciones o no
    function validarelimavigencia($year) {
        $r=$this->database->obtenerTipodeVigencia($year);
        return $r;
        
    }

}
