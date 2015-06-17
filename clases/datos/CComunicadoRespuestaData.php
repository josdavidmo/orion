<?php

/**
 * Clase Comunicado Respuesta Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2015.04.08
 * @copyright SERTIC SAS
 */
class CComunicadoRespuestaData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    function CComunicadoRespuestaData($db) {
        $this->db = $db;
    }

    /**
     * Inserta una bitacora en la base de datos
     * @param \CComunicadoRespuesta $comunicadoRespuesta
     * @return type
     */
    public function insertComunicadoRespuesta($comunicadoRespuesta) {
        $tabla = "comunicado_respuesta";
        $campos = implode(",", $this->db->getCampos($tabla));
        $valores = "'" . $comunicadoRespuesta->getComunicado() . "','"
                . $comunicadoRespuesta->getRespuesta() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }
    
    public function deleteAsociacion($idDocumento, $idRespuesta){
        $tabla = "comunicado_respuesta";
        $predicado = "idDocumento = $idDocumento AND idRespuesta = $idRespuesta";
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    public function getRespuestasByComunicado($idComunicado) {
        $respuestas = null;
        $query = "SELECT r.idRespuesta as id, dot.dot_nombre as Area, "
                . "sd.dos_nombre as Subtema,doc_codigo_ref as CodigoReferencia, "
                . "d.doc_fecha_radicado as FechaRadicado, "
                . "d.doc_descripcion as Descripcion, dt.dti_nombre as Ruta,"
                . "d.doc_archivo as Archivo, "
                . "d.doc_fecha_respuesta as FechaLimiteRespuesta, "
                . "CONCAT(u.usu_nombre,'',u.usu_apellido) as Usuario, "
                . "d.doc_anexo as Anexo, doe.doe_id as Estado, "
                . "doe.doe_nombre as NombreEstado "
                . "FROM comunicado_respuesta r "
                . "INNER JOIN documento_comunicado d ON r.idRespuesta = d.doc_id "
                . "LEFT JOIN documento_actor ac ON d.doa_id_autor = ac.doa_id "
                . "LEFT JOIN documento_actor des ON d.doa_id_dest = des.doa_id "
                . "LEFT JOIN documento_subtema sd ON d.dos_id = sd.dos_id "
                . "LEFT JOIN usuario u ON d.usu_id = u.usu_id "
                . "LEFT JOIN documento_estado doe ON doe.doe_id=d.doe_id "
                . "LEFT JOIN documento_tipo dt ON dt.dti_id = d.dti_id "
                . "LEFT JOIN documento_tema dot ON dot.dot_id = d.dot_id "
                . "WHERE r.idDocumento = $idComunicado";
        $r = $this->db->ejecutarConsulta($query);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $respuestas[$cont]['id'] = $w['id'];
                $respuestas[$cont]['Area'] = $w['Area'];
                $respuestas[$cont]['Subtema'] = $w['Subtema'];
                $respuestas[$cont]['CodigoReferencia'] = $w['CodigoReferencia'];
                $respuestas[$cont]['FechaRadicado'] = $w['FechaRadicado'];
                $respuestas[$cont]['Descripcion'] = $w['Descripcion'];
                $respuestas[$cont]['Archivo'] = "<a href='././soportes/OPR/" . $w['Ruta'] . "/" . $w['Archivo'] . "' target='_blank'>{$w['Archivo']}</a>";
                $respuestas[$cont]['FechaLimiteRespuesta'] = NULL;
                if ($w['FechaLimiteRespuesta'] != "0000-00-00") {
                    $respuestas[$cont]['FechaLimiteRespuesta'] = $w['FechaLimiteRespuesta'];
                }
                $respuestas[$cont]['Usuario'] = $w['Usuario'];
                $respuestas[$cont]['Anexo'] = "<a href='././soportes/OPR/" . $w['Ruta'] . "/" . $w['Anexo'] . "' target='_blank'>{$w['Anexo']}</a>";
                $respuestas[$cont]['Estado'] = $w['NombreEstado'];
                if ($w['Estado'] == 2) {
                    $respuestas[$cont]['Estado'] = "<img src='templates/img/ico/verde.gif'>";
                }
                if ($w['Estado'] == 3 || $w['Estado'] == 4) {
                    $datetime1 = new DateTime("now");
                    $datetime2 = new DateTime($w['FechaLimiteRespuesta']);
                    $interval = $datetime1->diff($datetime2);
                    $dias = $interval->days + 1;
                    if ($datetime1->format("Y-m-d") <= $datetime2->format("Y-m-d")) {
                        $respuestas[$cont]['Estado'] = "<img src='templates/img/ico/amarillo.gif'> " . $dias;
                    } else {
                        $respuestas[$cont]['Estado'] = "<img src='templates/img/ico/rojo.gif'> " . $dias;
                    }
                }
                $cont++;
            }
        }
        return $respuestas;
    }
    
    public function getComunicados($criterio = "1") {
        $respuestas = null;
        $query = "SELECT d.doc_id as id, dot.dot_nombre as Area, "
                . "sd.dos_nombre as Subtema,doc_codigo_ref as CodigoReferencia, "
                . "d.doc_fecha_radicado as FechaRadicado, "
                . "d.doc_descripcion as Descripcion, dt.dti_nombre as Ruta,"
                . "d.doc_archivo as Archivo, "
                . "d.doc_fecha_respuesta as FechaLimiteRespuesta, "
                . "CONCAT(u.usu_nombre,'',u.usu_apellido) as Usuario, "
                . "d.doc_anexo as Anexo, doe.doe_id as Estado, "
                . "doe.doe_nombre as NombreEstado "
                . "FROM documento_comunicado d "
                . "LEFT JOIN documento_actor ac ON d.doa_id_autor = ac.doa_id "
                . "LEFT JOIN documento_actor des ON d.doa_id_dest = des.doa_id "
                . "LEFT JOIN documento_subtema sd ON d.dos_id = sd.dos_id "
                . "LEFT JOIN usuario u ON d.usu_id = u.usu_id "
                . "LEFT JOIN documento_estado doe ON doe.doe_id=d.doe_id "
                . "LEFT JOIN documento_tipo dt ON dt.dti_id = d.dti_id "
                . "LEFT JOIN documento_tema dot ON dot.dot_id = d.dot_id "
                . "WHERE $criterio";
        $r = $this->db->ejecutarConsulta($query);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $respuestas[$cont]['id'] = $w['id'];
                $respuestas[$cont]['Area'] = $w['Area'];
                $respuestas[$cont]['Subtema'] = $w['Subtema'];
                $respuestas[$cont]['CodigoReferencia'] = $w['CodigoReferencia'];
                $respuestas[$cont]['FechaRadicado'] = $w['FechaRadicado'];
                $respuestas[$cont]['Descripcion'] = $w['Descripcion'];
                $respuestas[$cont]['Archivo'] = "<a href='././soportes/OPR/" . $w['Ruta'] . "/" . $w['Archivo'] . "' target='_blank'>{$w['Archivo']}</a>";
                $respuestas[$cont]['FechaLimiteRespuesta'] = NULL;
                if ($w['FechaLimiteRespuesta'] != "0000-00-00") {
                    $respuestas[$cont]['FechaLimiteRespuesta'] = $w['FechaLimiteRespuesta'];
                }
                $respuestas[$cont]['Usuario'] = $w['Usuario'];
                $respuestas[$cont]['Anexo'] = "<a href='././soportes/OPR/" . $w['Ruta'] . "/" . $w['Anexo'] . "' target='_blank'>{$w['Anexo']}</a>";
                $respuestas[$cont]['Estado'] = $w['NombreEstado'];
                if ($w['Estado'] == 2) {
                    $respuestas[$cont]['Estado'] = "<img src='templates/img/ico/verde.gif'>";
                }
                if ($w['Estado'] == 3 || $w['Estado'] == 4) {
                    $datetime1 = new DateTime("now");
                    $datetime2 = new DateTime($w['FechaLimiteRespuesta']);
                    $interval = $datetime1->diff($datetime2);
                    $dias = $interval->days + 1;
                    if ($datetime1->format("Y-m-d") <= $datetime2->format("Y-m-d")) {
                        $respuestas[$cont]['Estado'] = "<img src='templates/img/ico/amarillo.gif'> " . $dias;
                    } else {
                        $respuestas[$cont]['Estado'] = "<img src='templates/img/ico/rojo.gif'> " . $dias;
                    }
                }
                $cont++;
            }
        }
        return $respuestas;
    }

}
