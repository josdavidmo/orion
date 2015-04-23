<?php
/**
 * Clase destinada a la gestio de datos referentes a planeaciones
 * @version 1.0
 * @since 31/07/2014
 * @author Brian Kings
 */
class CPlaneacionData {
    /**
     *  Instancia de la clase que conecta la base de datos
     * @var CData
     */
    var $db = null;
    /**
     * Constructor de la clase
     * @param CData $db
     */
    function CPlaneacionData($db) {
        $this->db = $db;
    }
    /*
     * Obtienes las regiones y las organiza segun el criterio ingresado
     * @param String $orden 
     * @return Array
     */
    function getRegiones($orden) {
        $sql = "select * from departamento_region order by " . $orden;
        $r = $this->db->ejecutarConsulta($sql);
        $regiones = null;
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $regiones[$cont]['id'] = $w['der_id'];
                $regiones[$cont]['nombre'] = $w['der_nombre'];
                $cont++;
            }
        }
        return $regiones;
    }
    /*
     * Obtiene los departamentos dependiendo de la region ingresada como criterio
     * @param String $criterio 
     * @param String $orden 
     * @return Array
     */
    function getDepartamento($criterio = "0", $orden = "dep_nombre") {
        $regiones = null;
        $sql = "SELECT * FROM departamento where der_id = " . $criterio . "  order by " . $orden;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $regiones[$cont]['id'] = $w['dep_id'];
                $regiones[$cont]['nombre'] = $w['dep_nombre'];
                $cont++;
            }
        }
        return $regiones;
    }
    /*
     * Obtiene los municipios dependiendo del departamento ingresado como criterio
     * @param String $criterio 
     * @param String $orden 
     * @return Array
     */
    function getMunicipio($criterio = "0", $orden = "mun_nombre") {
        $regiones = null;
        $sql = "SELECT * FROM municipio where dep_id = " . $criterio . "  order by " . $orden;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $regiones[$cont]['id'] = $w['mun_id'];
                $regiones[$cont]['nombre'] = $w['mun_nombre'];
                $cont++;
            }
        }
        return $regiones;
    }
    /*
     * Obtiene los ejes y los ordena segun el criterio ingresado
     * @param String $orden 
     * @return Array
     */
    function getEjes($orden) {
        $ejes = null;
        $sql = " select * from eje order by " . $orden;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $ejes[$cont]['id'] = $w['eje_id'];
                $ejes[$cont]['nombre'] = $w['eje_nombre'];
                $cont++;
            }
        }
        return $ejes;
    }
    /*
     * Obtiene las planeaciones ingresadas en la base de datos, que cumplan el criterio ingresado.
     * @param String $criterio 
     * @param String $orden 
     * @return Array
     */
    function getPlaneacion($criterio, $orden) {
        $planeacion = null;
        $sql = "SELECT p.pla_id, der.der_nombre,d.dep_nombre,m.mun_nombre,e.eje_nombre, "
                . "p.pla_numero_encuestas, t.ins_nombre, p.pla_fecha_inicio, p.pla_fecha_fin, CONCAT( u.usu_nombre,'  ',u.usu_apellido)AS usu_nombre "
                . "FROM planeacion p left join eje e on e.eje_id = p.eje_id "
                . "left join municipio m on m.mun_id = p.mun_id "
                . "left join departamento d on d.dep_id = m.dep_id "
                . "left join departamento_region der on der.der_id=d.der_id "
                . "left join instrumento t on t.ins_id = e.ins_id "
                . "left join usuario u on u.usu_id =p.usu_id where " . $criterio . " order by " . $orden;

        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $planeacion[$cont]['id_element'] = $w['pla_id'];
                $planeacion[$cont]['der_nombre'] = $w['der_nombre'];
                $planeacion[$cont]['dep_nombre'] = $w['dep_nombre'];
                $planeacion[$cont]['mun_nombre'] = $w['mun_nombre'];
                $planeacion[$cont]['eje_nombre'] = $w['eje_nombre'];
                $planeacion[$cont]['numero_encuestas'] = $w['pla_numero_encuestas'];
                $planeacion[$cont]['ins_nombre'] = $w['ins_nombre'];
                $planeacion[$cont]['pla_fecha_inicio'] = $w['pla_fecha_inicio'];
                $planeacion[$cont]['pla_fecha_fin'] = $w['pla_fecha_fin'];
                $planeacion[$cont]['usu_nombre'] = $w['usu_nombre'];
                $cont++;
            }
        }
        return $planeacion;
    }
    /*
     * Obtiene las planeaciones para completar la tabla para ser vista desde el modulo ejecucion
     * @param Boolean $excel 
     * @param String $orden 
     * @return Array
     */
    function getPlaneacionVerEjecucion($criterio, $excel) {
        $planeacion = null;
        $sql = "SELECT p.pla_id, der.der_nombre,d.dep_nombre,m.mun_nombre,e.eje_nombre, "
                . "p.pla_numero_encuestas, t.ins_nombre, p.pla_fecha_inicio, p.pla_fecha_fin, CONCAT( u.usu_nombre,'  ',u.usu_apellido)AS usu_nombre, ees.ees_nombre "
                . "FROM planeacion p left join eje e on e.eje_id = p.eje_id "
                . "left join municipio m on m.mun_id = p.mun_id "
                . "left join departamento d on d.dep_id = m.dep_id "
                . "left join departamento_region der on der.der_id=d.der_id "
                . "left join instrumento t on t.ins_id = e.ins_id "
                . "left join encuesta_estado ees on  ees.ees_id = p.ees_id "
                . "left join usuario u on u.usu_id = p.usu_id  where " . $criterio . " order by pla_id";
        $r = $this->db->ejecutarConsulta($sql);
        $estadoActualizado = null;
        $estado = null;
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $sql2 = "SELECT count(*) FROM encuesta WHERE pla_id = '" . $w['pla_id'] . "' and ees_id = 1 group by ees_id";
                $r2 = $this->db->ejecutarConsulta($sql2);
                $w2 = mysql_fetch_array($r2);
                $planeacion[$cont]['id_element'] = $w['pla_id'];
                $planeacion[$cont]['der_nombre'] = $w['der_nombre'];
                $planeacion[$cont]['dep_nombre'] = $w['dep_nombre'];
                $planeacion[$cont]['mun_nombre'] = $w['mun_nombre'];
                $planeacion[$cont]['eje_nombre'] = $w['eje_nombre'];
                $planeacion[$cont]['numero_encuestas'] = $w['pla_numero_encuestas'];
                $planeacion[$cont]['ins_nombre'] = $w['ins_nombre'];
                $planeacion[$cont]['pla_fecha_inicio'] = $w['pla_fecha_inicio'];
                $planeacion[$cont]['pla_fecha_fin'] = $w['pla_fecha_fin'];
                $planeacion[$cont]['usu_nombre'] = $w['usu_nombre'];
                $planeacion[$cont]['ees_nombre'] = $w['ees_nombre'];
                $total = round(($w2['count(*)'] * 100) / $w['pla_numero_encuestas'], 0);
                //calcular estado
                $dias = $this->dias_transcurridos_entre_fechas(date('Y-m-d'), $w['pla_fecha_fin']);
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
    /*
     * Calcula los días entre dos fechas
     * @param Date $fecha_i fecha inicial
     * @param Date $fecha_f fecha final
     * @return Integer 
     */
    function dias_transcurridos_entre_fechas($fecha_i, $fecha_f) {
        $dias = (strtotime($fecha_f) - strtotime($fecha_i)) / 86400;
        $dias = floor($dias);
        return $dias;
    }
    /*
     * Obtiene el numero de ejes existenes
     * @return Integer
     */
    function getNumeroEjes() {
        $sql = "select count(*) from eje " ;
        $r = ($this->db->ejecutarConsulta($sql));
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['count(*)'];
        }
    }
    /*
     * Obtiene el resumen de planeacion
     * @param String $criterio 
     * @return Array
     */
    function getResumen($criterio) {
        $contadorEje = 1;
        $arreglo = null;
        $resumen = null;
        $total = null;
        $numeroEjes=null;
        $numeroEjes=  $this->getNumeroEjes();
        while ($contadorEje <= $numeroEjes) {  // indica los 4 ejes existentes
            $sql = "SELECT p.eje_id,e.eje_nombre, count(*), SUM(pla_numero_encuestas) FROM planeacion p 
                    inner join eje e on e.eje_id = p.eje_id 
                    left join municipio m on m.mun_id = p.mun_id 
                    left join departamento d on d.dep_id = m.dep_id 
                    left join departamento_region der on der.der_id=d.der_id 
                    left join instrumento t on t.ins_id = e.ins_id 
                    left join usuario u on u.usu_id =p.usu_id
                    where p.eje_id =" . $contadorEje . " and " . $criterio
                    . " group by eje_id";
            //echo $sql;
            $r = $this->db->ejecutarConsulta($sql);
            $w = mysql_fetch_array($r);
            if ($w['count(*)'] == '') {
                $resumen[$contadorEje - 1] = 0;
            } else {
                $resumen[$contadorEje - 1] = $w['SUM(pla_numero_encuestas)'];
            }
            $total+=$w['SUM(pla_numero_encuestas)'];
            $contadorEje++;
        }
        $resumen[$contadorEje - 1] = $total;
        return $resumen;
    }
    /*
     * Ingresa los datos que recibe en la tabla planeaciond e la base de datos
     * @param Integer $id identificador de la planeacion
     * @param Integer $municipio
     * @param Integer $eje
     * @param Integer $numero_encuestas
     * @param Date $fecha_inicio
     * @param Date $fecha_fin
     * @param Integer $usuario
     * @return String
     */
    function insertPlaneacion($id, $municipio, $eje, $numero_encuestas, $fecha_inicio, $fecha_fin, $usuario) {
        $tabla = 'planeacion';
        $campos = 'pla_id,  mun_id,eje_id,pla_numero_encuestas, '
                . 'pla_fecha_inicio,pla_fecha_fin, usu_id';
        $valores = $id . ",'" . $municipio . "','" . $eje . "','"
                . $numero_encuestas . "','" . $fecha_inicio . "','" . $fecha_fin . "','" . $usuario . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }
    /*
     * Actualiza los datos que recibe en la tabla planeacion de la base de datos segun el id
     * @param Integer $municipio
     * @param Integer $eje
     * @param Integer $numero_encuestas
     * @param Date $fecha_inicio
     * @param Date $fecha_fin
     * @param Integer $usuario
     * @return String
     */
    function updatePlaneacion($municipio, $eje, $numero_encuestas, $id, $fecha_inicio, $fecha_fin, $usuario) {
        $tabla = 'planeacion';
        $campos = array(' mun_id', 'eje_id', 'pla_numero_encuestas', 'pla_fecha_inicio', 'pla_fecha_fin', 'usu_id');
        $valores = array("'" . $municipio . "'", "'" . $eje . "'", "'"
            . $numero_encuestas . "'", "'" . $fecha_inicio . "'", "'" . $fecha_fin . "'", "'" . $usuario . "'");
        $condicion = " pla_id = " . $id;
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }
    /*
     * Elimina los datos de un planeacion segun el id que recibe en la base de datos
     * @param Integer $id
     * @return String
     */
    function deletePlaneacion($id) {
        $tabla = "planeacion";
        $predicado = "pla_id = " . $id;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }
    /*
     * Obtiene los datos de una planeacion en especifico dependiendo del id ingresado
     * @param Integer $id
     * @return Array
     */
    function getPlaneacionById($id) {
        $plan = null;
        $sql = "SELECT p.pla_id,  d.der_id,d.dep_id,m.mun_id,e.eje_id, p.pla_numero_encuestas,p.pla_fecha_inicio,p.pla_fecha_fin,p.usu_id "
                . "FROM planeacion p left join eje e on e.eje_id = p.eje_id "
                . "left join municipio m on m.mun_id = p.mun_id "
                . "left join departamento d on d.dep_id = m.dep_id where p.pla_id = " . $id;
        $r = $this->db->recuperarResultado($this->db->ejecutarConsulta($sql));
        if ($r)
            return $r;
        else
            return -1;
    }
    /*
     * obtiene el id y nombre de los usuarios registrados en la tabla usuario de la base de datos
     * @param String $orden 
     * @return Array
     */
    function getUsuarios($orden) {
        $usuarios = null;
        $sql = "SELECT  usu_id , CONCAT( usu_nombre,'  ',usu_apellido)AS usu_nombre from usuario order by " . $orden;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $usuarios[$cont]['id'] = $w['usu_id'];
                $usuarios[$cont]['nombre'] = $w['usu_nombre'];
                $cont++;
            }
        }
        return $usuarios;
    }
    /*
     * Obtiene el id del municipio a partir de su nombre
     * @param Integer $municipio 
     * @return Integer
     */
    function getMunicipioId($municipio) {
        $sql = "SELECT  mun_id from municipio where mun_nombre = '" . strtoupper($municipio) . "'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['mun_id'];
        }
    }
    /*
     * Obtiene el id del eje a partir de su nombre
     * @param String $eje 
     * @return Integer
     */
    function getEjeId($eje) {
        $sql = "SELECT  eje_id from eje where eje_nombre = '" . strtoupper($eje) . "'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['eje_id'];
        }
    }
    /*
     * obtiene el id del usuario a partir de su documento de identificacion
     * @param Integer $documento 
     * @return Integer
     */
    function getUsuarioId($documento) {
        $sql = "SELECT  usu_id from usuario where usu_documento = '" . $documento . "'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['usu_id'];
        }
    }
    /*
     * ingresa los registros de las nuevas encuestas en la base de datos
     * @param Array $valores
     */
    function createEncuestas($valores) {
        $tabla = 'encuesta';
        $campos = ' enc_consecutivo, pla_id, ees_id ';
        $this->db->insertarVariosRegistros($tabla, $campos, $valores);
    }
    /*
     * Elimina los registros de las encuestas que tienen el identificador de la 
     * planeacion asignado
     * @param Integer $id
     * @return String
     */
    function deleteEncuestas($id) {
        $this->deleteRespuestasPlaId($id);
        $tabla = "encuesta";
        $predicado = "pla_id= '" . $id . "'";
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }
    /*
     * Obtener el ultimo consecutivo para determinado municipio
     * @param Integer $id_municipio
     * @return Integer
     */
    function ultimoConsecutivoEncuesta($id_municipio) {
        $sql = "SELECT  max(enc_consecutivo)as mayor from encuesta where enc_consecutivo LIKE '" . $id_municipio . "%'";
        //echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        $w = mysql_fetch_array($r);
        if ($w['mayor']<=0) {
            return ($id_municipio * 1000);
        } else {
            return $w['mayor'];
        }
    }
    /*
     * Obtiene el id del ultimo registro ingresado
     * @return Integer
     */
    function getUltimoIdPlaneacion() {
        $sql = "SELECT  MAX(pla_id) as mayor from planeacion";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['mayor'];
        }
    }
    /*
     * Obtiene el array de ids de las encuestas asociadas
     * @param Integer $id
     * * @return Array
     */
    function getIdsEncuestas($id) {
        $encuestas=null;
        $sql = "SELECT  enc_id from encuesta where pla_id = ".$id;
        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $encuestas[$cont]['enc_id'] = $w['enc_id'];
                $cont++;
            }
        }
        return $encuestas;
    }
    /*
     * Elimina las respuestas de una encuesta
     * @param Integer $id 
     */
    function deleteRespuestasPlaId($id){
        $encuestas=$this->getIdsEncuestas($id);
        $cont=0;
        $predicado=' 0 ';
        while($cont<count($encuestas)){
            $predicado=$predicado.' or enc_id ='.$encuestas[$cont]['enc_id'];
            $cont++;
        }
        $r = $this->db->borrarRegistro('instrumento_respuestas',$predicado);
    }
    /**
     * Obtiene el numero de encuestas en estado sin completar
     * @param type $id
     * @return type
     */
    function obtenerNumeroDeEncuestasSinCompletar($id){
        $sql="SELECT count(*)as mayor FROM encuesta WHERE pla_id = ".$id." and ees_id!=1";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['mayor'];
        }
    }
    /**
     * Eliminar las encuestas sin completar de una planeacion
     * @param type $id
     */
    function deleteEncuestasSinCompletar($id,$numero_encuestas_eliminar){
        $condicional= $id.' and ees_id!=1 order by enc_id';
        $encuestas=$this->getIdsEncuestas($condicional);
        $cont=0;
        $predicado=' 0 ';
        while($cont<$numero_encuestas_eliminar){
            $predicado=$predicado.' or enc_id ='.$encuestas[$cont]['enc_id'];
            $cont++;
        }
        $r = $this->db->borrarRegistro('instrumento_respuestas',$predicado);
        return $this->db->borrarRegistro('encuesta',$predicado);
        // 2337 2319
    }
}
?>
