<?php

/**
 * Clase CPlaneacionData
 * Usada para establecer la conexion con la base de datos, ejecucion de consultas 
 * y  la implementacion de operaciones Crud(Create, Read, Update and Delete) sobre
 * la informacion referente a los beneficiarios y los historiales de cambios.
 * @see beneficiarios.php (@package modulos,@subpackage beneficiarios)
 * @see beneficiariosCambiosTransferencias.php(@package modulos,@subpackage beneficiarios)
 * @package clases
 * @subpackage datos
 * @access public
 * @author SERTIC SAS
 * @since @version 2015.01.23
 * @copyright SERTIC SAS
 */
class CPlaneacionData {

    /**
     * @var CData variable de clase de manejo y gestion de la base de datos. 
     */
    var $db = null;

    /**
     * Constructor de la clase CBeneficiarioData.
     * @param CData $db, Variable de conexion de la base de datos
     */
    function CPlaneacionData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene las regiones y las organiza segun el parametro de orden seleccionado.
     * @param string $orden, parametro utilizado para ordenar las regiones obtenidas.
     * @return array $regiones, retorna un Arreglo con las regiones obtenidas.
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

    /**
     * Obtiene los departamentos de una region en particular que se pasa como
     * paramtero, junto con el criterio de orden del arreglo de salida.
     * @param string $orden, parametro utilizado para ordenar los departamentos 
     * obtenidos.
     * @param string $criterio, parametro de condicion de consulta.  
     * @return array $regiones, retorna un arreglo con los departamentos  obtenidos.
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

    /**
     * Obtiene los municipios de un departamento en particular que se pasa como 
     * paramtero, junto con el criterio de orden del arreglo de salida.
     * @param string $orden, parametro utilizado para ordenar los departamentos 
     * obtenidos.
     * @param string $criterio, parametro de condicion de consulta. 
     * @return array $regiones, retorna un arreglo con los municipios obtenidos.
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

    /**
     * Obtiene los ejes y los ordena segun el criterio de orden ingresdo.
     * @param string $orden, parametro utilizado para ordenar los ejes obtenidos.
     * @return array $ejes, retorna un arreglo con los ejes obtenidos.
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

    /**
     * Obtiene las planeaciones almacenadas en la base de datos mediante un 
     * criterio y un parametro de orden.
     * @param string $orden, parametro utilizado para ordenar las planeaciones obtenidas.
     * @param string $criterio, parametro de condicion de consulta. 
     * @return array $planeacion, retorna un arreglo con las planeaciones obtenidas.
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

    
    /**
     * Obtiene las planeaciones almacenadas en la base con el fin de completar la 
     * tabla que se visualiza desde el modulo de ejecucion. 
     * @param string $orden, parametro utilizado para ordenar las planeaciones obtenidas.
     * @param string $exel,?. 
     * @return array $planeacion, petorna un Arreglo con las planeaciones obtenidas.
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
    
    
    /**
     * Calcula la cantidad de dias entre dos fechas. 
     * @param Date $fecha_i fecha inicial, parametro que especifica la fecha de inicio.
     * @param Date $fecha_f fecha final, parametro que especifica la fecha final.
     * @return integer $dias, retorna un numero con la cantidad de dias calculados.
     */
    
    function dias_transcurridos_entre_fechas($fecha_i, $fecha_f) {
        $dias = (strtotime($fecha_f) - strtotime($fecha_i)) / 86400;
        $dias = floor($dias);
        return $dias;
    }

    /**
     * Obtiene el numero de ejes existentes.
     * @param no recibe paramtero alguno.
     * @return integer $w, retorna el numero de posiciones del arreglo obtenido
     * al ejecutar la consulta.
     */

    function getNumeroEjes() {
        $sql = "select count(*) from eje ";
        $r = ($this->db->ejecutarConsulta($sql));
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['count(*)'];
        }
    }

    /**
     * Obtiene el resumen de la planeacion. 
     * @param string $criterio, parametro de condicion de consulta. 
     * @return array $resumen, retorna un arreglo con el resumen obtenido.
     */

    function getResumen($criterio) {
        $contadorEje = 1;
        $arreglo = null;
        $resumen = null;
        $total = null;
        $numeroEjes = null;
        $numeroEjes = $this->getNumeroEjes();
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

    /**
     * Inserta una planeacion y sus atributos dentro la base de datos.
     * @param Integer $id, id de la planeacion insertada.
     * @param string $municipio, municipio de la planeacion.
     * @param string $eje, eje de la planeacion.
     * @param Integer $numero_encuestas, numero de encuestas de la planeacion.
     * @param Date $fecha_inicio, fecha de inicio de la planeacion.
     * @param Date $fecha_fin, fecha de finalizacion de la planeacion.
     * @param string $usuario, usuario de la planeacion.
     * @return string, retorna "true" si la inserccion fue exitosa.
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

    /**
     * Actualiza una planeacion existente en la base de datos a traves de su id.
     * @param Integer $id, id de la planeacion insertada.
     * @param string $municipio, municipio de la planeacion.
     * @param string $eje, eje de la planeacion.
     * @param Integer $numero_encuestas, numero de encuestas de la planeacion.
     * @param Date $fecha_inicio, fecha de inicio de la planeacion.
     * @param Date $fecha_fin, fecha de finalizacion de la planeacion.
     * @param string $usuario, usuario de la planeacion.
     * @return string, retorna "true" si la actualizacion fue  exitosa.
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

   /**
    * Elimina una planeacion de la base de datos.
    * @param Integer $id, Id de la planeacion que se eliminara.
    * @return string, retorna "true" si la eliminacion fue exitosa.
    */

    function deletePlaneacion($id) {
        $tabla = "planeacion";
        $predicado = "pla_id = " . $id;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Obtiene los datos de una planeacion a traves de su Id desde la base da datos.
     * @param Integer $id, Id de la planeacion consultada.
     * @return array $r, retorna un arreglo con los datos de la planeacion si el
     * proceso es exitoso, en caso contrario retorna -1.
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

    /**
     * Obtiene los usuarios de la base de datos y los organiza segun el parametro 
     * de orden.
     * @param string $orden, parameto de orden del arreglo de salida.
     * @return array $usuarios, retorna un arreglo con los usuarios obtenidos.
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

   /**
     * Obtiene el Id de un municipio a traves de su nombre desde la base da datos.
     * @param string $municipio, nombre del municipio consultado.
     * @return Integer $w, retorna el numero almacenado en el arreglo obtenido a 
     * traves de la consulta.
     */
    function getMunicipioId($municipio) {
        $sql = "SELECT  mun_id from municipio where mun_nombre = '" . strtoupper($municipio) . "'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['mun_id'];
        }
    }

    /**
     * Obtiene el Id de un eje a traves de su nombre desde la base da datos.
     * @param string $eje, nombre del eje consultado.
     * @return Integer $w, retorna el numero almacenado en el arreglo obtenido a 
     * traves de la consulta.
     */
    function getEjeId($eje) {
        $sql = "SELECT  eje_id from eje where eje_nombre = '" . strtoupper($eje) . "'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['eje_id'];
        }
    }

    /**
     * Obtiene el Id de un usuario a traves de su documento desde la base da datos.
     * @param Integer $documento, documento de identidad del usuario consultado.
     * @return Integer $w, retorna el numero almacenado en el arreglo obtenido a 
     * traves de la consulta.
     */

    function getUsuarioId($documento) {
        $sql = "SELECT  usu_id from usuario where usu_documento = '" . $documento . "'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['usu_id'];
        }
    }

    /**
     * Inserta los registros de las nuevas encuestas en la base de datos.
     * @param array $valores, arreglo con los valores de los atributos 
     * de las encuestas.
     */

    function createEncuestas($valores) {
        $tabla = 'encuesta';
        $campos = ' enc_consecutivo, pla_id, ees_id ';
        $this->db->insertarVariosRegistros($tabla, $campos, $valores);
    }

      
    /**
     * Elimina los registros de las encuestas que tienen el identificador de la 
     * planeacion asignado.
     * @param Integer $i, Id de la planeacion.
     * @return string, retorna "true" si la eliminacion fue exitosa.
     */

    function deleteEncuestas($id) {
        $this->deleteRespuestasPlaId($id);
        $tabla = "encuesta";
        $predicado = "pla_id= '" . $id . "'";
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Obtiene el ultimo consecutivo asignado a determinado municipio.
     * @param Integer $id_municipio, Id del municipo.
     * @return Integer $w, retorna el numero almacenado en el arreglo obtenido a 
     * traves de la consulta.
     */

    function ultimoConsecutivoEncuesta($id_municipio) {
        $sql = "SELECT  max(enc_consecutivo)as mayor from encuesta where enc_consecutivo LIKE '" . $id_municipio . "%'";
        //echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        $w = mysql_fetch_array($r);
        if ($w['mayor'] <= 0) {
            return ($id_municipio * 1000);
        } else {
            return $w['mayor'];
        }
    }

  
   /**
     * Obtiene el id del ultimo registro ingresado.
     * @param no recibe parametro alguno.
     * @return Integer $w, retorna el numero almacenado en el arreglo obtenido a 
     * traves de la consulta.
     */

    function getUltimoIdPlaneacion() {
        $sql = "SELECT  MAX(pla_id) as mayor from planeacion";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['mayor'];
        }
    }

    /**
     * Obtiene un array con los Ids de las encuestas asociadas en una planeacion.
     * @param Integer $id, Id de la planeacion. 
     * @return array $encuestas, retorna un arreglo con los Ids de las encuestas.
     */

    function getIdsEncuestas($id) {
        $encuestas = null;
        $sql = "SELECT  enc_id from encuesta where pla_id = " . $id;
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

    /**
     * Elimina las respuestas de una encuesta de una planeacion de la base de datos.
     * @param Integer $id, Id de la encuesta.
     */

    function deleteRespuestasPlaId($id) {
        $encuestas = $this->getIdsEncuestas($id);
        $cont = 0;
        $predicado = ' 0 ';
        while ($cont < count($encuestas)) {
            $predicado = $predicado . ' or enc_id =' . $encuestas[$cont]['enc_id'];
            $cont++;
        }
        $r = $this->db->borrarRegistro('instrumento_respuestas', $predicado);
    }

    /**
     * Obtiene el numero de encuestas que se encuentran en estado "sin completar".
     * @param Integer $id, Id de la planeacion donde las encuestas estan asignadas.
     * @return Integer $w, retorna el numero almacenado en el arreglo obtenido a 
     * traves de la consulta.
     */
    function obtenerNumeroDeEncuestasSinCompletar($id) {
        $sql = "SELECT count(*)as mayor FROM encuesta WHERE pla_id = " . $id . " and ees_id!=1";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w['mayor'];
        }
    }

    /**
     * Elimina las encuestas en estado "sin completar", de una planeacion.
     * @param Integer $id, Id de la planeacion de la cual se eliminaran las
     * encuestas.
     * @param Integer $numero_encuestas_eliminar, numero de encuestas a eliminar.
     * @return string, retorna "true" si la eliminacion fue exitosa.
     */
    function deleteEncuestasSinCompletar($id, $numero_encuestas_eliminar) {
        $condicional = $id . ' and ees_id!=1 order by enc_id';
        $encuestas = $this->getIdsEncuestas($condicional);
        $cont = 0;
        $predicado = ' 0 ';
        while ($cont < $numero_encuestas_eliminar) {
            $predicado = $predicado . ' or enc_id =' . $encuestas[$cont]['enc_id'];
            $cont++;
        }
        $r = $this->db->borrarRegistro('instrumento_respuestas', $predicado);
        return $this->db->borrarRegistro('encuesta', $predicado);
        // 2337 2319
    }

}

?>
