<?php


class CIncidenciaData{
    var $db;
    
    function CIncidenciaData($db){
        $this->db = $db;
    }
    
    function getOpcionesModulo(){
        $sql = "SELECT opc_id, opc_nombre FROM opcion";
        $r = $this->db->ejecutarConsulta($sql);
        $cont =0;
        $opciones = null;
        while($w = mysql_fetch_array($r)){
            $opciones[$cont]['id']      = $w['opc_id'];
            $opciones[$cont]['nombre']  = $w['opc_nombre'];
            $cont++;
        }
        return $opciones;
    }
    
    function getTiposIncidencia(){
        $sql = "SELECT * FROM incidencia_tipo";
        $r = $this->db->ejecutarConsulta($sql);
        $cont =0;
        $opciones = null;
        while($w = mysql_fetch_array($r)){
            $opciones[$cont]['id']      = $w['inc_tipo_id'];
            $opciones[$cont]['nombre']  = $w['inc_tipo_nombre'];
            $cont++;
        }
        return $opciones;
    }
    
    function getEstadosIncidencia(){
        $sql = "SELECT * FROM incidencia_estado";
        $r = $this->db->ejecutarConsulta($sql);
        $cont =0;
        $opciones = null;
        while($w = mysql_fetch_array($r)){
            $opciones[$cont]['id']      = $w['inc_estado_id'];
            $opciones[$cont]['nombre']  = $w['inc_estado_nombre'];
            $cont++;
        }
        return $opciones;
    }
    
    function getIncidencias($criterio){
        $sql ="SELECT i.*, CONCAT(usu_nombre,' ',usu_apellido) as usuario, inc_estado_nombre, inc_tipo_nombre,opc_nombre "
            . "FROM incidencia i, usuario, incidencia_estado, incidencia_tipo, opcion "
            . "WHERE inc_estado = inc_estado_id "
            . "AND inc_tipo = inc_tipo_id "
            . "AND inc_usuario = usu_id "
            . "AND inc_opcion = opc_id "
            . "AND $criterio";
        $r = $this->db->ejecutarConsulta($sql);
        $cont =0;
        $incidencias = null;
        while($w = mysql_fetch_array($r)){
            $incidencias[$cont]['inc']               = $w['inc_id'];
            $incidencias[$cont]['inc_id']               = $w['inc_id'];
            $incidencias[$cont]['inc_fecha']            = $w['inc_fecha'];
            $incidencias[$cont]['inc_opcion']           = $w['opc_nombre'];
            $incidencias[$cont]['inc_tipo_nombre']      = $w['inc_tipo_nombre'];
            $incidencias[$cont]['inc_desc']             = $w['inc_desc'];
            $incidencias[$cont]['inc_archivo']          = "<a href='././".RUTA_INCIDENCIAS."/" . $w['inc_archivo'] . "' target='_blank'>{$w['inc_archivo']}</a>";
            $incidencias[$cont]['inc_usuario_nombre']   = $w['usuario'];
            $incidencias[$cont]['inc_estado']           = $w['inc_estado_nombre'];
            $cont++;
        }
        return $incidencias;
    }
    
    function getInicidenciaById($id){
        $sql = "SELECT * FROM incidencia WHERE inc_id = $id";
        $r = $this->db->ejecutarConsulta($sql);
        $incidencias = null;
        while($w = mysql_fetch_array($r)){
            $incidencias['inc_id']              = $w['inc_id'];
            $incidencias['inc_fecha']           = $w['inc_fecha'];
            $incidencias['inc_opcion']          = $w['inc_opcion'];
            $incidencias['inc_tipo']            = $w['inc_tipo'];
            $incidencias['inc_desc']            = $w['inc_desc'];
            $incidencias['inc_archivo']         = $w['inc_archivo'];
            $incidencias['inc_usuario']         = $w['inc_usuario'];
            $incidencias['inc_estado']          = $w['inc_estado'];
            return $incidencias;
        }
    }
    
    function insertIncidencia($incidencia){
        $temp="";
        if($incidencia->getArchivoIncidencia()!=null){
            $temp="'".$incidencia->getArchivoIncidencia()."'";
        }else{
            $temp="null";
        }
        $tabla = "incidencia";
        $campos =
//                "inc_id,"
//                ."inc_fecha,"
                "inc_tipo,"
                ."inc_desc,"
                ."inc_archivo,"
                ."inc_usuario,"
//                ."inc_estado,"
                ."inc_opcion";
        $valores=
//                "'','',".
                "".$incidencia->getTipoIncidencia().",".
                "'".$incidencia->getDescripcionIncidencia()."',".
                $temp.",".
//                "'',".
                "".$incidencia->getUsuarioIncidencia().",".
                "".$incidencia->getOpcionIncidencia()."";
        $r = $this->db->insertarRegistro($tabla,$campos,$valores);
	return $r;
    }
    
    function deleteIncidencia($id){
        $tabla = "incidencia";
        $predicado = "inc_id = ". $id;
        $r = $this->db->borrarRegistro($tabla,$predicado);
        return $r;
    }
    
    function updateIncidencia($incidencia){
        $condicion = "inc_id=".$incidencia->getIdIncidencia();
        if($incidencia->getArchivoIncidencia()!=null){
            $campos = array(
                "inc_fecha",
                "inc_tipo",
                "inc_desc",
                "inc_archivo",
                "inc_usuario",
                "inc_estado",
                "inc_opcion");
            $valores=array(
                "'".$incidencia->getFechaIncidencia()."'",
                "".$incidencia->getTipoIncidencia()."",
                "'".$incidencia->getDescripcionIncidencia()."'",
                "'".$incidencia->getArchivoIncidencia()."'",
                "".$incidencia->getUsuarioIncidencia()."",
                "".$incidencia->getEstadoIncidencia()."",
                "".$incidencia->getOpcionIncidencia()."",);
        }else{
            $campos = array(
                "inc_fecha",
                "inc_tipo",
                "inc_desc",
                "inc_usuario",
                "inc_estado",
                "inc_opcion");
            $valores=array(
                "'".$incidencia->getFechaIncidencia()."'",
                "".$incidencia->getTipoIncidencia()."",
                "'".$incidencia->getDescripcionIncidencia()."'",
                "".$incidencia->getUsuarioIncidencia()."",
                "".$incidencia->getEstadoIncidencia()."",
                "".$incidencia->getOpcionIncidencia()."",);
        }
        $tabla = "incidencia";

        $r = $this->db->actualizarRegistro($tabla,$campos,$valores,$condicion);
        return $r;
    }
}
?>