<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CGeneradorEjecucionData {

    function CGeneradorEjecucionData($db) {
        $this->db = $db;
    }

    function consultarNivelUsuario($id) {
        $sql = "SELECT usu_id, per_id FROM usuario WHERE usu_id=$id";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                return $w['per_id'];
            }
        }
    }

    function consultarPassUsuario($id){
        $sql = "SELECT usu_clave FROM usuario WHERE usu_id=$id";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                return $w['usu_clave'];
            }
        }
    }
    
    function setSyncEncuesta($id, $valor){
        $tabla = "generador_encuesta";
        $campos = array('enc_sync');
        $valores = array($valor);
        $condicion = " enc_id = " . $id;
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }
    
    function getSyncEncuestas($valor){
        $respuestas = null;
        $sql = "SELECT enc_id, enc_sync FROM generador_encuesta where enc_sync = $valor";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $respuestas[$cont]['encuesta'] = $w['enc_id'];
                $respuestas[$cont]['sync'] = $w['enc_sync'];
                $cont++;
            }
        }
        return $respuestas;
    }
    
    function getSyncPlaneaciones($usuario){
        $respuestas = null;
        $sql = "SELECT pla_id FROM generador_planeacion where usu_id = $usuario";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $respuestas[$cont] = $w['enc_id']."";
                $cont++;
            }
        }
        return $respuestas;
    }
    
    function getRespuestas($id){
        $respuestas = null;
        $sql = "SELECT * FROM respuestas where idEncuesta = $id";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $respuestas[$cont]['pregunta'] = $w['idPregunta'];
                $respuestas[$cont]['encuesta'] = $w['idEncuesta'];
                $respuestas[$cont]['respuesta'] = $this->codificarString($w['respuesta']);
                $cont++;
            }
        }
        return $respuestas;
    }
    
    function codificarString($t){
        $t = str_replace('Ã¡', '00aacute00;', $t);
        $t = str_replace('Ã©', '00eacute00;', $t);
        $t = str_replace('Ã­', '00iacute00;', $t);
        $t = str_replace('Ã³', '00oacute00;', $t);
        $t = str_replace('Ãº', '00uacute00;', $t);
        $t = str_replace('Ã±', '00ntilde00;', $t);
        $t = str_replace('Ã�', '00Aacute00;', $t);
        $t = str_replace('Ã‰', '00Eacute00;', $t);
        $t = str_replace('Ã�', '00Iacute00;', $t);
        $t = str_replace('Ã“', '00Oacute00;', $t);
        $t = str_replace('Ãš', '00Uacute00;', $t);
        $t = str_replace('Ã‘', '00Ntilde00;', $t);
        //echo $t."<hr> ";
        return $t;
    }
    
    function getEncuestaEstados() {
        $preguntas = null;
        $sql = "SELECT ees_id, ees_nombre FROM encuesta_estado ";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $preguntas[$cont]['id'] = $w['ees_id'];
                $preguntas[$cont]['nombre'] = $w['ees_nombre'];
                $cont++;
            }
        }
        return $preguntas;
    }

    function getEjecucion($criterio, $excel, $admin = false) {
        $planeacion = null;

        $sql = "SELECT es.ees_nombre, g.pla_id, g.pla_numero_encuestas, b.nombre, DATEDIFF(CURDATE(),pla_fecha_fin) as dias,"
                . " i.nombre AS instrumento, c.nombre as centro_poblado, m.mun_nombre,d.dep_nombre, r.der_nombre,"
                . " g.pla_fecha_inicio, g.pla_fecha_fin, g.pla_numero_encuestas, "
                . "CONCAT(u.usu_nombre,\" \",u.usu_apellido) AS usuario, o.opc_nombre, et.enc_tipo_nombre "
                . "FROM generador_planeacion g "
                . "INNER JOIN beneficiario b ON g.ben_id=b.idBeneficiario "
                . "INNER JOIN centroPoblado c ON c.idCentroPoblado=b.idCentroPoblado "
                . "INNER JOIN municipio m ON c.mun_id=m.mun_id "
                . "INNER JOIN departamento d ON m.dep_id=d.dep_id "
                . "INNER JOIN departamento_region r ON d.der_id=r.der_id "
                . "INNER JOIN instrumentos i ON g.ins_id=i.idInstrumento "
                . "INNER JOIN usuario u ON g.usu_id=u.usu_id "
                . "INNER JOIN encuesta_estado es ON es.ees_id=g.ees_id "
                . "INNER JOIN opcion o ON i.idEncabezado=o.opc_id "
                . "INNER JOIN encuesta_tipo et ON et.enc_tipo_id=i.idTipoEncuesta "
                . "WHERE isnull(g.mun_id) AND $criterio";
        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        $estadoActualizado = null;
        $estado = null;
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $sql2 = "SELECT count(*) FROM generador_encuesta WHERE pla_id = '" . $w['pla_id'] . "' and ees_id = 1 group by ees_id";
                $r2 = $this->db->ejecutarConsulta($sql2);
//                echo $sql2;
                $w2 = mysql_fetch_array($r2);
                $planeacion[$cont]['id_element'] = $w['pla_id'];
                $planeacion[$cont]['der_nombre'] = $w['der_nombre'];
                $planeacion[$cont]['dep_nombre'] = $w['dep_nombre'];
                $planeacion[$cont]['mun_nombre'] = $w['mun_nombre'];
                $planeacion[$cont]['centro_poblado'] = $w['centro_poblado'];
                $planeacion[$cont]['beneficiario'] = $w['nombre'];
                if ($admin) {
                    $planeacion[$cont]['nivel'] = $w['opc_nombre'];
                    $planeacion[$cont]['tipo'] = $w['enc_tipo_nombre'];
                }
                $planeacion[$cont]['instrumento'] = $w['instrumento'];
                $planeacion[$cont]['pla_numero_encuestas'] = $w['pla_numero_encuestas'];
                $planeacion[$cont]['pla_fecha_inicio'] = $w['pla_fecha_inicio'];
                $planeacion[$cont]['pla_fecha_fin'] = $w['pla_fecha_fin'];
                $planeacion[$cont]['usu_nombre'] = $w['usuario'];
                $planeacion[$cont]['ees_nombre'] = $w['ees_nombre'];
                $total = round(($w2['count(*)'] * 100) / $w['pla_numero_encuestas'], 0);
                //calcular estado
                $dias = $w['dias'];
                if ($dias >= 0) {
                    $estado = '2';
                } else if ($dias < 0) {
                    $estado = '3';
                }
                if ($excel == false) {
                    if ($total <= 51) {
                        $total = '<img src=./templates/img/ico/rojo.gif>' . $total . '%';
                    } elseif ($total > 51 && $total <= 99) {
                        $total = '<img src=./templates/img/ico/amarillo.gif>' . $total . '%';
                    } else {
                        $total = '<img src=./templates/img/ico/verde.gif>' . $total . '%';
                        $estado = '1';
                    }
                } else {
                    if ($total <= 51) {
                        $total = $total . '%';
                    } elseif ($total > 51 && $total <= 99) {
                        $total = $total . '%';
                    } else {
                        $total = $total . '%';
                        $estado = '1';
                    }
                }
                $planeacion[$cont]['porcentaje_ejecucion'] = $total;
                $cont++;
                //mantener actualizado el estado
                $this->db->actualizarRegistro('planeacion', array('ees_id'), $estado, (" pla_id = " . $w['pla_id']));
            }
        }
        $sql = "SELECT es.ees_nombre, g.pla_id, g.pla_numero_encuestas, DATEDIFF(CURDATE(),pla_fecha_fin) as dias,"
                . " i.nombre AS instrumento,  m.mun_nombre,d.dep_nombre, r.der_nombre,"
                . " g.pla_fecha_inicio, g.pla_fecha_fin, g.pla_numero_encuestas, "
                . "CONCAT(u.usu_nombre,\" \",u.usu_apellido) AS usuario, o.opc_nombre, et.enc_tipo_nombre "
                . "FROM generador_planeacion g "
                . "LEFT JOIN municipio m ON g.mun_id=m.mun_id "
                . "LEFT JOIN departamento d ON m.dep_id=d.dep_id "
                . "LEFT JOIN departamento_region r ON d.der_id=r.der_id "
                . "LEFT JOIN instrumentos i ON g.ins_id=i.idInstrumento "
                . "LEFT JOIN usuario u ON g.usu_id=u.usu_id "
                . "LEFT JOIN encuesta_estado es ON es.ees_id=g.ees_id "
                . "LEFT JOIN opcion o ON i.idEncabezado=o.opc_id "
                . "LEFT JOIN encuesta_tipo et ON et.enc_tipo_id=i.idTipoEncuesta "
                . "WHERE isnull(g.ben_id) AND $criterio";
//        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                $sql2 = "SELECT count(*) FROM generador_encuesta WHERE pla_id = '" . $w['pla_id'] . "' and ees_id = 1 group by ees_id";
                $r2 = $this->db->ejecutarConsulta($sql2);
//                echo $sql2;
                $w2 = mysql_fetch_array($r2);
                $planeacion[$cont]['id_element'] = $w['pla_id'];
                $planeacion[$cont]['der_nombre'] = $w['der_nombre'];
                $planeacion[$cont]['dep_nombre'] = $w['dep_nombre'];
                $planeacion[$cont]['mun_nombre'] = $w['mun_nombre'];
                $planeacion[$cont]['centro_poblado'] = null;
                $planeacion[$cont]['beneficiario'] = null;
                if ($admin) {
                    $planeacion[$cont]['nivel'] = $w['opc_nombre'];
                    $planeacion[$cont]['tipo'] = $w['enc_tipo_nombre'];
                }
                $planeacion[$cont]['instrumento'] = $w['instrumento'];
                $planeacion[$cont]['pla_numero_encuestas'] = $w['pla_numero_encuestas'];
                $planeacion[$cont]['pla_fecha_inicio'] = $w['pla_fecha_inicio'];
                $planeacion[$cont]['pla_fecha_fin'] = $w['pla_fecha_fin'];
                $planeacion[$cont]['usu_nombre'] = $w['usuario'];
                $planeacion[$cont]['ees_nombre'] = $w['ees_nombre'];
                $total = round(($w2['count(*)'] * 100) / $w['pla_numero_encuestas'], 0);
                //calcular estado
                $dias = $w['dias'];
                if ($dias >= 0) {
                    $estado = '2';
                } else if ($dias < 0) {
                    $estado = '3';
                }
                if ($excel == false) {
                    if ($total <= 51) {
                        $total = '<img src=./templates/img/ico/rojo.gif>' . $total . '%';
                    } elseif ($total > 51 && $total <= 99) {
                        $total = '<img src=./templates/img/ico/amarillo.gif>' . $total . '%';
                    } else {
                        $total = '<img src=./templates/img/ico/verde.gif>' . $total . '%';
                        $estado = '1';
                    }
                } else {
                    if ($total <= 51) {
                        $total = $total . '%';
                    } elseif ($total > 51 && $total <= 99) {
                        $total = $total . '%';
                    } else {
                        $total = $total . '%';
                        $estado = '1';
                    }
                }
                $planeacion[$cont]['porcentaje_ejecucion'] = $total;
                $cont++;
                //mantener actualizado el estado
                $this->db->actualizarRegistro('planeacion', array('ees_id'), $estado, (" pla_id = " . $w['pla_id']));
            }
        }

        return $planeacion;
    }

    function getEncuestas($criterio, $excel) {
        $encuesta = null;
        $sql = "SELECT enc.enc_id, enc.enc_consecutivo,pla.pla_id, enc.enc_documento_soporte ,   
            enc.enc_fecha, ecc.ecc_nombre, 'enc.enc_motivo_cuestionario_incorrecto', erf.erf_nombre,evi.evi_nombre,eri.eri_nombre," .
                //"enc.enc_motivo_encuesta_incorrecta, ".
                "CONCAT( usu.usu_nombre,'  ',usu.usu_apellido)AS usu_nombre,ess.ees_nombre,
            pla.pla_fecha_fin,pla.pla_fecha_inicio FROM generador_encuesta enc 
            left join generador_planeacion pla on pla.pla_id = enc.pla_id 
            left join encuesta_estado ess on ess.ees_id = enc.ees_id 
            left join encuesta_cuestionario_completo ecc on ecc.ecc_id = enc.ecc_id
            left join encuesta_resultado_final erf on erf.erf_id = enc.erf_id
            left join encuesta_validar_inspeccion evi on evi.evi_id = enc.evi_id
            left join encuesta_resultado_inspeccion eri on eri.eri_id = enc.eri_id
            left join usuario usu on usu.usu_id = enc.usu_id
            WHERE " . $criterio;
//        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $encuesta[$cont]['id_element'] = $w['enc_id'];
                $encuesta[$cont]['consecutivo'] = $w['enc_consecutivo'];
                $encuesta[$cont]['documento_soporte'] = "<a href='././soportes/EJECUCION/" . $w['enc_consecutivo'] . "/" . $w['enc_documento_soporte'] . "' target='_blank'>{$w['enc_documento_soporte']}</a>";
                $encuesta[$cont]['fecha'] = $w['enc_fecha'];
                $encuesta[$cont]['ecc'] = $w['ecc_nombre'];
                //$encuesta[$cont]['motivo_ci'] = $w['enc_motivo_cuestionario_incorrecto'];
                $encuesta[$cont]['erf'] = $w['erf_nombre'];
                $encuesta[$cont]['evi'] = $w['evi_nombre'];
                $encuesta[$cont]['eri'] = $w['eri_nombre'];
                $encuesta[$cont]['motivo_ei'] = $w['enc_motivo_encuesta_incorrecta'];
                //$encuesta[$cont]['responsable'] = $w['usu_nombre'];
                if (!$excel) {
                    $encuesta[$cont]['estado'] = $this->semaforo_seguimiento($w['pla_fecha_fin'], $w['ees_nombre']);
                } else {
                    if ($w['ees_nombre'] == 'Completo') {
                        $encuesta[$cont]['estado'] = 'Completo';
                    } else {
                        $encuesta[$cont]['estado'] = $this->dias_transcurridos_entre_fechas(date('Y-m-d'), $w['pla_fecha_fin']) . ' días.';
                    }
                }
                $cont++;
            }
        }
        return $encuesta;
    }

    /*
     * Calcula los días entre dos fechas
     * @param Date $fecha_i fecha inicial
     * @param Date $fecha_f fecha final
     * @return Integer 
     */

    function getInstrumento($id) {
        $sql = "SELECT p.ins_id FROM generador_planeacion p "
                . "INNER JOIN generador_encuesta e ON e.pla_id=p.pla_id WHERE e.enc_id = $id";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                return $w['ins_id'];
            }
        }
    }

    function dias_transcurridos_entre_fechas($fecha_i, $fecha_f) {
        $dias = (strtotime($fecha_f) - strtotime($fecha_i)) / 86400;
        $dias = floor($dias);
        return $dias;
    }

    function semaforo_seguimiento($fecha_fin, $estado) {
        if ($estado == 'Completo') {
            $dias = '<img src=./templates/img/ico/verde.gif>';
            return $dias;
        } else {
            $dias = null;
            $dias = $this->dias_transcurridos_entre_fechas(date('Y-m-d'), $fecha_fin);
            if ($dias >= 0) {
                $dias = '<img src=./templates/img/ico/amarillo.gif>' . $dias . ' días.';
            }
            if ($dias < 0) {
                $dias = '<img src=./templates/img/ico/rojo.gif>' . ($dias * (-1)) . ' días.';
            }
            return $dias;
        }
    }

    function getEncuestaById($id) {
        $sql = "select * from generador_encuesta where enc_id = " . $id;
        $r = $this->db->recuperarResultado($this->db->ejecutarConsulta($sql));
        if ($r)
            return $r;
        else
            return -1;
    }

    function updateEjecucion($id, $archivo, $fecha, $cc, $mci, $rf, $vi, $ri, $mei, $usuario) {
        $tabla = "generador_encuesta";
        $campos = array('enc_documento_soporte', 'enc_fecha', 'ecc_id', 'enc_motivo_cuestionario_incorrecto',
            'erf_id', 'evi_id', 'eri_id',
            //'enc_motivo_encuesta_incorrecta',
            'usu_id');
        $valores = array("'" . $archivo . " '", "'" . $fecha . "'", "'" . $cc . "'", "'" . $mci . "'", "'" . $rf . "'", "'" . $vi . "'",
            "'" . $ri . "'",
            //"'" . $mei . "'",
            "'" . $usuario . "'");
        $condicion = "enc_id = " . $id;
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    function getTipoEncuesta($id_add) {
        return 0;
    }

    function getRDMByEncuestaId($id) {
        $opciones = null;
        $sql = "select der.der_nombre, d.dep_nombre, m.mun_nombre from generador_encuesta enc 
                left join planeacion pla on pla.pla_id = enc.pla_id 
                left join municipio m on m.mun_id = pla.mun_id
                left join departamento d on d.dep_id = m.dep_id 
                left join departamento_region der on der.der_id=d.der_id 
                where enc.enc_id = '" . $id . "'";
        $r = ($this->db->ejecutarConsulta($sql));
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $opciones[$cont]['region'] = $w['der_nombre'];
                $opciones[$cont]['departamento'] = $w['dep_nombre'];
                $opciones[$cont]['municipio'] = $w['mun_nombre'];
                $cont++;
            }
        }
        return $opciones;
    }

    function setSaveRespuestasEncuesta($valores) {
        
    }

    function getCuestionarioCompletoOptions() {
        $opciones = null;
        $sql = "SELECT  ecc_id , ecc_nombre from encuesta_cuestionario_completo ";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $opciones[$cont]['id'] = $w['ecc_id'];
                $opciones[$cont]['nombre'] = $w['ecc_nombre'];
                $cont++;
            }
        }
        return $opciones;
    }

    function getResultadoFinalOptions() {
        $opciones = null;
        $sql = "SELECT  erf_id , erf_nombre from encuesta_resultado_final ";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $opciones[$cont]['id'] = $w['erf_id'];
                $opciones[$cont]['nombre'] = $w['erf_nombre'];
                $cont++;
            }
        }
        return $opciones;
    }

    function getValidacionInspeccionOptions() {
        $opciones = null;
        $sql = "SELECT  evi_id , evi_nombre from encuesta_validar_inspeccion ";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $opciones[$cont]['id'] = $w['evi_id'];
                $opciones[$cont]['nombre'] = $w['evi_nombre'];
                $cont++;
            }
        }
        return $opciones;
    }

    /*
     * Obtiene los registros de la tabla encuesta_resultado_inspeccion
     * @return Array
     */

    function getResultadoInspeccionOptions() {
        $opciones = null;
        $sql = "SELECT  eri_id , eri_nombre from encuesta_resultado_inspeccion ";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $opciones[$cont]['id'] = $w['eri_id'];
                $opciones[$cont]['nombre'] = $w['eri_nombre'];
                $cont++;
            }
        }
        return $opciones;
    }

    function completarEncuesta($enc_id) {
        $tabla = "generador_encuesta";
        $campos = array('ees_id');
        $valores = array(1);
        $condicion = "enc_id = " . $enc_id;
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    function setEstadoEncuesta($id_add, $estado) {
        $tabla = " generador_encuesta ";
        $campos = array('ees_id');
        $valores = array($estado);
        $condicion = " enc_id = " . $id_add;
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        if ($r == 'true') {
            return "Encuesta borrada";
        } else {
            return "La encuesta no ha sido borrada";
        }
    }

    function borrarRespuestas($id) {
        $tabla = "generador_encuesta_respuestas";
        $predicado = " enc_id = " . $id;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    function insertEjecucionSync($enc_id, $enc_consecutivo, $pla_id, $usu_id){
        $tabla = 'generador_ejecucion';
        $campos = 'enc_id, enc_consecutivo, pla_id, usu_id';
        $valores = $enc_id.",".$enc_consecutivo."," . $pla_id ."";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        
        return $r;
}
    
}
