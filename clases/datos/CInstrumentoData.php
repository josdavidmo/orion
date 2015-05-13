<?php

/**
 * DAO para el modulo instrumentos.
 * @package clases.
 * @subpackage datos.
 * @author Jose David Moreno Posada
 * @version 1.0
 * @since 04/08/2014
 * @copyright SERTIC S.A.S
 */
class CInstrumentoData {

    /** Maneja la conexion con la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase inicializa la conexion con la base de datos.
     * @param \CData $db
     */
    public function CInstrumentoData($db) {
        $this->db = $db;
    }

    /**
     * Inserta un nuevo instrumento en la base de datos.
     * @param \CInstrumento $instrumento
     * @return boolean
     */
    public function insertarInstrumento($instrumento) {
        $tabla = 'instrumentos';
        $campos = 'nombre, codigo, idEncabezado, idTipoEncuesta';
        $valores = "'" . $instrumento->getNombreInstrumento() . "'"
                . ", '" . $instrumento->getCodigo() . "'"
                . ", '" . $instrumento->getNivel() . "'"
                . ", '" . $instrumento->getTipo() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        $this->actualizarOpcion($instrumento->getNivel());
        return $r;
    }

    public function actualizarOpcion($idEncabezado) {
        $tabla = "opcion";
        $campos = array('opc_variable');
        $valores = array("'genEjecucion&ref=$idEncabezado'");
        $condicion = "opc_id = '$idEncabezado'";
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
    }

    /**
     * Inserta una nueva seccion en la base de datos.
     * @param \CSeccion $seccion
     * @return boolean
     */
    public function insertarSeccion($seccion) {
        $tabla = 'seccion';
        $campos = 'nombre, numero, idInstrumento';
        $valores = "'" . $seccion->getNombreSeccion() . "'"
                . ", '" . $seccion->getNumero() . "'"
                . ", " . $seccion->getInstrumento()->getId() . "";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Inserta una nueva pregunta en la base de datos.
     * @param \CPregunta $pregunta
     * @return boolean
     */
    public function insertarPregunta($pregunta) {
        $tabla = 'pregunta';
        $campos = 'idSeccion, tipo, numero, enunciado, requerido, '
                . 'descripcion, opcionRespuesta';
        $valores = "" . $pregunta->getSeccion()->getIdSeccion() . ""
                . ", " . $pregunta->getTipoPregunta() . ""
                . ", " . $pregunta->getNumero() . ""
                . ", '" . $pregunta->getEnunciado() . "'"
                . ", " . $pregunta->isRequerido() . "";
        if ($pregunta->getDescripcion() != null) {
            $valores .= ", '" . $pregunta->getDescripcion() . "'";
        } else {
            $valores .= ", null";
        }
        if ($pregunta->getOpcionRespuesta() != null) {
            $valores .= ", '" . $pregunta->getOpcionRespuesta() . "'";
        } else {
            $valores .= ", null";
        }
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Obtiene todos los instrumentos almacenados en la tabla instrumentos.
     * @return array
     */
    public function getInstrumentos() {
        $instrumentos = null;
        $sql = "SELECT i.idInstrumento,i.nombre,i.codigo, "
                . "(SELECT COUNT(idSeccion) "
                . "FROM seccion s  "
                . "WHERE s.idInstrumento = i.idInstrumento) as secciones, "
                . "(SELECT count(p.idPregunta) as preguntas "
                . "FROM pregunta p "
                . "INNER JOIN seccion s ON s.idSeccion = p.idSeccion "
                . "WHERE s.idInstrumento = i.idInstrumento) as preguntas, "
                . "o.opc_nombre, "
                . "t.enc_tipo_nombre "
                . "FROM pncav2.instrumentos i "
                . "INNER JOIN opcion o ON o.opc_id = i.idEncabezado "
                . "INNER JOIN encuesta_tipo t ON t.enc_tipo_id = i.idTipoEncuesta";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $instrumentos[$cont]['idInstrumento'] = $w['idInstrumento'];
                $instrumentos[$cont]['codigo'] = $w['codigo'];
                $instrumentos[$cont]['nombre'] = $w['nombre'];
                $instrumentos[$cont]['modulo'] = $w['opc_nombre'];
                $instrumentos[$cont]['tipo'] = $w['enc_tipo_nombre'];
                $instrumentos[$cont]['secciones'] = $w['secciones'];
                $instrumentos[$cont]['preguntas'] = $w['preguntas'];
                $cont++;
            }
        }
        return $instrumentos;
    }

    /**
     * Obtiene el numero de preguntas de un instrumento
     * @param type $idInstrumento
     * @return type
     */
    public function getNumeroPreguntasByInstrumento($idInstrumento) {
        $preguntas = null;
        $sql = "SELECT count(p.idPregunta) as preguntas FROM pregunta p "
                . "INNER JOIN seccion s ON s.idSeccion = p.idSeccion "
                . "INNER JOIN instrumentos i ON i.idInstrumento = s.idInstrumento "
                . "WHERE i.idInstrumento = $idInstrumento AND p.requerido = 1";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $preguntas = $w['preguntas'];
        }
        return $preguntas;
    }

    /**
     * Obtiene un instrumento por el id del mismo
     * @param type $idInstrumento
     * @return \CInstrumento
     */
    public function getInstrumentoById($idInstrumento) {
        $sql = "SELECT * FROM instrumentos WHERE idInstrumento = "
                . $idInstrumento;
        $r = $this->db->ejecutarConsulta($sql);
        $instrumento = null;
        if ($r) {
            $w = mysql_fetch_array($r);
            $instrumento = new CInstrumento($w['idInstrumento'], $w['nombre'], $w['codigo'], $w['idTipoEncuesta'], $w['idEncabezado']);
        }
        return $instrumento;
    }

    /**
     * Obtiene un instrumento por el codigo del mismo.
     * @param String $codigo
     * @return \CInstrumento
     */
    public function getInstrumentoByCodigo($codigo) {
        $sql = "SELECT * FROM instrumentos WHERE codigo = '" . $codigo . "'";
        $r = $this->db->ejecutarConsulta($sql);
        $instrumento = null;
        if ($r) {
            $w = mysql_fetch_array($r);
            $instrumento = new CInstrumento($w['idInstrumento'], $w['nombre'], $w['codigo'], $w['idTipoEncuesta'], $w['idEncabezado']);
        }
        return $instrumento;
    }

    /**
     * Obtiene la cantidad de preguntas almacenadas en una seccion.
     * @param \CSeccion $seccion
     * @return type
     */
    public function getCantidadPreguntas($seccion) {
        $sql = "SELECT COUNT(*) FROM pregunta WHERE idSeccion = '" .
                $seccion->getIdSeccion() . "'";
        $r = $this->db->ejecutarConsulta($sql);
        $cantidad = 0;
        if ($r) {
            $w = mysql_fetch_array($r);
            $cantidad = $w[0];
        }
        return $cantidad;
    }

    /**
     * Obtiene las secciones de un instrumento.
     * @param \CInstrumento $instrumento
     * @return type $secciones
     */
    public function getSecciones($instrumento) {
        $sql = "SELECT * FROM seccion WHERE idInstrumento = "
                . $instrumento->getId() . " ORDER BY numero";
        $r = $this->db->ejecutarConsulta($sql);
        $secciones = null;
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $seccion = new CSeccion($w['idSeccion'], $w['nombre'], $w['numero'], $instrumento);
                $secciones[$cont] = $seccion;
                $cont++;
            }
        }
        return $secciones;
    }

    /**
     * Obtiene las preguntas dada una seccion
     * @param \CSeccion $seccion
     * @return \CPregunta
     */
    public function getPreguntas($seccion) {
        $sql = "SELECT * FROM pregunta WHERE idSeccion = "
                . $seccion->getIdSeccion() . " ORDER BY numero";
        $r = $this->db->ejecutarConsulta($sql);
        $preguntas = null;
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $pregunta = new CPregunta($w['idPregunta'], $seccion, $w['tipo'], $w['numero'], $w['requerido'], $w['enunciado'], $w['descripcion'], $w['opcionRespuesta']);
                $preguntas[$cont] = $pregunta;
                $cont++;
            }
        }
        return $preguntas;
    }

    /**
     * Obtiene las preguntas dada una seccion
     * @param type $idPregunta
     * @return \CPregunta
     */
    public function getPreguntaById($idPregunta) {
        $sql = "SELECT * FROM pregunta WHERE idPregunta = " . $idPregunta;
        $r = $this->db->ejecutarConsulta($sql);
        $pregunta = null;
        if ($r) {
            $w = mysql_fetch_array($r);
            $pregunta = new CPregunta($w['idPregunta'], $this->getSeccionById($w['idSeccion']), $w['tipo'], $w['numero'], $w['requerido'], $w['enunciado'], $w['descripcion'], $w['opcionRespuesta']);
        }
        return $pregunta;
    }

    /**
     * Obtiene una seccion por el id del mismo.
     * @param type $idSeccion
     * @return \CSeccion
     */
    public function getSeccionById($idSeccion) {
        $sql = "SELECT * FROM seccion WHERE idSeccion = " . $idSeccion;
        $seccion = null;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $seccion = new CSeccion($w['idSeccion'], $this->getInstrumentoById($w['idInstrumento']), $w['nombre'], $w['numero']);
        }
        return $seccion;
    }

    /**
     * Obtiene una seccion por codigo instrumento y numero.
     * @param \CInstrumento $instrumento
     * @param type $numero
     * @return \CSeccion
     */
    public function getSeccionByCodigoInstrumentoAndNumero($instrumento, $numero) {
        $sql = "SELECT * FROM seccion WHERE idInstrumento = "
                . $instrumento->getId() . " AND numero = " . $numero;
        $seccion = null;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $seccion = new CSeccion($w['idSeccion'], $w['idInstrumento'], $w['nombre'], $w['numero']);
        }
        return $seccion;
    }

    /**
     * Borra un instrumento de la base de datos.
     * @param type $idInstrumento
     */
    public function borrarInstrumento($idInstrumento) {
        $resultado = $this->borrarSecciones($idInstrumento);
        $tabla = "instrumentos";
        $predicado = " idInstrumento = " . $idInstrumento;
        return $resultado && $this->db->borrarRegistro($tabla, $predicado);
    }

    /**
     * Borra las secciones de un instrumento.
     * @param type $idInstrumento
     * @return string
     */
    public function borrarSecciones($idInstrumento) {
        $tabla = "seccion";
        $sql = "SELECT idSeccion FROM seccion "
                . "WHERE idInstrumento = " . $idInstrumento;
        $r = $this->db->ejecutarConsulta($sql);
        $resultado = true;
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                $idSeccion = $w['idSeccion'];
                $this->borrarPreguntas($idSeccion);
                $predicado = " idSeccion = " . $w['idSeccion'];
                $resultado = $resultado && $this->db->borrarRegistro($tabla, $predicado);
            }
        }
        return resultado;
    }

    /**
     * Borra las preguntas pertenecientes a la seccion dada.
     * @param type $idSeccion
     * @return type
     */
    public function borrarPreguntas($idSeccion) {
        $tabla = "pregunta";
        $sql = "SELECT idPregunta FROM pregunta WHERE idSeccion = " . $idSeccion;
        $r = $this->db->ejecutarConsulta($sql);
        $resultado = true;
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                $predicado = " idPregunta = " . $w['idPregunta'];
                $resultado = $resultado && $this->db->borrarRegistro($tabla, $predicado);
            }
        }
        return $resultado;
    }

    /**
     * Borra una seccion de un instrumento dado el id de la seccion.
     * @param type $idSeccion
     * @return type
     */
    public function borrarSeccion($idSeccion) {
        $r = $this->borrarPreguntas($idSeccion);
        $tabla = 'seccion';
        $predicado = ' idSeccion = ' . $idSeccion;
        $r = $r && $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Borra una pregunta de un instrumento dado el id de la pregunta.
     * @param type $idPregunta
     * @return type
     */
    public function borrarPregunta($idPregunta) {
        $tabla = 'pregunta';
        $predicado = ' idPregunta = ' . $idPregunta;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Determina la mejor forma de mostrar una pregunta abierta.
     * @param type $pregunta
     * @param type $numero
     * @param \CRespuesta $respuesta
     * @return string
     */
    public function construirInput($pregunta, $numero, $respuesta) {
        $tipo = split(',', $pregunta->getDescripcion())[0];
        $longitud = split(',', $pregunta->getDescripcion())[1];
        $resultado .= "<span class='input-group-addon'>Respuesta Pregunta " . $pregunta->getNumero() . ".</span>";
        $resultado .= "<";
        switch ($tipo) {
            case '0':
                $resultado .= "input type='text' placeholder='Escribe letras y n&uacute;meros' pattern='{0," . $longitud . "}'";
                break;

            case '1':
                $resultado .= "textarea ";
                break;

            case '2':
                $resultado .= "input type='tel' placeholder='Escribe un n&uacute;mero de t&eacute;lefono' pattern='[0-9]{0," . $longitud . "}' title='Ingresa solo n&uacute;meros'";
                break;

            case '3':
                $resultado .= "input type='email' placeholder='Escribe un correo electr&oacute;nico' ";
                break;

            case '4':
                $resultado .= "input type='number' pattern='[0-9]{0," . $longitud . "}' placeholder='Escribe solo numeros' ";
                break;

            case '5':
                $resultado .= "input type='date' placeholder='Escribe solo numeros' ";
                break;

            case '6':
                $resultado .= "input type='file' ";
                break;

            default:
                break;
        }
        if ($respuesta != null && $tipo != '1') {
            $resultado .= "value='" . $respuesta->getRespuesta() . "'";
        }
        $resultado .= "class='form-control' ";
        $resultado .= "id='pregunta" . $numero . "' "
                . "name='pregunta" . $numero . "' ";
        if ($pregunta->isRequerido()) {
            $resultado .= " required ";
        }
        $resultado .= " />";
        if ($tipo == '1') {
            if ($respuesta != null) {
                $resultado .= $respuesta->getRespuesta();
            }
            $resultado .= "</textarea>";
        }
        return $resultado;
    }

    /**
     * Actualiza un instrumento dado el objeto del mismo.
     * @param \CInstrumento $instrumento
     * @return boolean
     */
    public function updateInstrumento($instrumento) {
        $tabla = 'instrumentos';
        $campos = array('codigo', 'nombre', 'idEncabezado');
        $valores = array("'" . $instrumento->getCodigo() . "'",
            "'" . $instrumento->getNombreInstrumento() . "'",
            "'" . $instrumento->getNivel() . "'");
        $condicion = " idInstrumento = " . $instrumento->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
		$this->actualizarOpcion($instrumento->getNivel());
        return $r;
    }

    /**
     * Actualiza una seccion dado el objeto del mismo.
     * @param \CSeccion $seccion
     * @return boolean
     */
    public function updateSeccion($seccion) {
        $tabla = 'seccion';
        $campos = array('nombre', 'numero');
        $valores = array("'" . $seccion->getNombreSeccion() . "'",
            "'" . $seccion->getNumero() . "'");
        $condicion = " idSeccion = " . $seccion->getIdSeccion();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Actualiza una pregunta dado el objeto del mismo.
     * @param \CPregunta $pregunta
     * @return boolean
     */
    public function updatePregunta($pregunta) {
        $tabla = 'pregunta';
        $campos = array('idSeccion', 'tipo', 'numero', 'enunciado', 'requerido', 'descripcion', 'opcionRespuesta');
        $valores = array("'" . $pregunta->getSeccion()->getIdSeccion() . "'",
            "'" . $pregunta->getTipoPregunta() . "'",
            "'" . $pregunta->getNumero() . "'",
            "'" . $pregunta->getEnunciado() . "'",
            "'" . $pregunta->isRequerido() . "'",
            "'" . $pregunta->getDescripcion() . "'",
            "'" . $pregunta->getOpcionRespuesta() . "'");
        $condicion = " idPregunta = " . $pregunta->getIdPregunta();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Borra una encuesta de la base de datos
     * @param \CRespuesta $respuesta
     * @return type
     */
    public function deleteRespuesta($respuesta) {
        $tabla = 'respuestas';
        $predicado = ' idPregunta = ' . $respuesta->getIdPregunta() .
                ' AND idEncuesta = ' . $respuesta->getIdEncuesta();
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Almacena las respuestas de una encuesta en la base de datos
     * @param \CRespuesta $respuesta
     * @return type
     */
    public function insertRespuesta($respuesta) {
        $r = $this->deleteRespuesta($respuesta);
        $tabla = 'respuestas';
        $campos = 'idPregunta, idEncuesta, respuesta';
        $valores = "'" . $respuesta->getIdPregunta() . "','"
                . "" . $respuesta->getIdEncuesta() . "','"
                . "" . $respuesta->getRespuesta() . "'";
        $r = $r && $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Obtiene las respuestas de un instrumento.
     * @param type $idEncuesta
     * @param type $idPregunta
     * @return \CRespuesta
     */
    public function getRespuestaByIdPreguntaAndIdEncuesta($idEncuesta, $idPregunta) {
        $sql = "SELECT * FROM respuestas WHERE idPregunta = "
                . $idPregunta . " AND idEncuesta = " . $idEncuesta;
        $respuesta = new CRespuesta(null, null, null);
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $respuesta = new CRespuesta($w['idPregunta'], $w['idEncuesta'], $w['respuesta']);
        }
        return $respuesta;
    }

    /**
     * Obtiene el numero de respuestas dadas en una encuesta.
     * @param type $idEncuesta
     * @return type
     */
    public function getNumeroRespuestasByEncuesta($idEncuesta) {
        $sql = "SELECT COUNT(*) FROM respuestas WHERE idEncuesta = " . $idEncuesta;
        $r = $this->db->ejecutarConsulta($sql);
        $numero = 0;
        if ($r) {
            $w = mysql_fetch_array($r);
            $numero = $w[0];
        }
        return $numero;
    }

    /**
     * Almacena un archivo en la base de datos.
     * @param type $archivo
     * @return type
     */
    public function guardarArchivo($archivo, $ruta) {
        $ruta = RUTA_DOCUMENTOS . "/soporteEncuestas/" . $ruta;
        $carpetas = explode("/", substr($ruta, 0, strlen($ruta) - 1));
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
        $ruta_destino .= "/";
        return utf8_decode(move_uploaded_file($archivo['tmp_name'], $ruta_destino . $archivo['name']));
    }

}
