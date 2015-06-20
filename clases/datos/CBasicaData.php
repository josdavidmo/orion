<?php

/**
 * Clase CBásicaData
 * Usada para establecer la conexion con la base de datos, ejecucion de consultas 
 * y  la implementacion de operaciones Crud(Create, Read, Update and Delete)sobre 
 * la informacion referente a las tablas basicas y a los registros de las mismas.
 * @see beneficiarios.php (@package modulos,@subpackage beneficiarios)
 * @see beneficiariosCambiosTransferencias.php(@package modulos,@subpackage beneficiarios)
 * @package clases
 * @subpackage datos
 * @access public
 * @author SERTIC SAS
 * @since @version 2015.01.23
 * @copyright SERTIC SAS
 */
class CBasicaData {

    /** 
    * @var CData variable de clase de manejo y gestion de la base de datos. 
    */
    var $db = null;

    /**
     * Constructor de la clase CBasicaData.
     * @param CData $db, Variable de conexion de la  base de datos.
     */
    function CBasicaData($db) {
        $this->db = $db;
    }

    
    /**
    * Obtiene los centros poblados almacenados dentro de 
    * la base de datos, aplicando un filtro por mun_id.
    * @param string $criterio, Criterio de filtro de la
    * consulta, valor default "0".
    * @return array $centrosPoblados, retorna un arreglo
    * cn los objetos de tipo /CCentroPoblado y sus respectivos 
    * atributos. 
    */
    public function getBasicas($tabla, $criterio = "1") {
        $basicas = null;
        $sql = "SELECT * FROM " . $tabla . " WHERE " . $criterio;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $basica = new CBasica($w[0], $w[1]);
                $basicas[$cont] = $basica;
                $cont++;
            }
        }
        return $basicas;
    }

    /**
     * Obtiene un objeto de tipo basico
     * @param type $tabla
     * @param type $condicion
     * @return \CBasica
     */
    public function getBasicasById($tabla, $condicion) {
        $basica = null;
        $sql = "SELECT * FROM " . $tabla . " WHERE " . $condicion;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $basica = new CBasica($w[0], $w[1]);
        }
        return $basica;
    }

    /**
     * Obtiene el id de un registro dado la descripcion.
     * @param type $tabla
     * @param type $descripcion
     * @return \CBasica
     */
    public function getIdBasicasByDescripcion($tabla, $descripcion) {
        $basica = null;
        $campos = $this->db->getCampos($tabla);
        $sql = "SELECT " . $campos[0] 
                . " FROM " . $tabla 
                . " WHERE LOWER(" . $campos[1] . ") = LOWER('" . $descripcion . "')";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $basica = new CBasica($w[0], $w[1]);
        }
        return $basica;
    }

}
