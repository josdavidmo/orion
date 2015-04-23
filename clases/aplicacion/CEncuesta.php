<?php

/**
 * Clase destinada al manejo de encuestas
 * @version 1.0
 * @since 31/07/2014
 * @author Brian Kings
 */
class CEncuesta {

    /**
     *  Identificador único interno de cada encuesta
     * @var Integer 
     */
    var $id = null;

    /**
     *  Consecutivo de la encuesta, viene dado por el municipio
     * @var Integer 
     */
    var $consecutivo = null;

    /**
     *  Identificador único de planeación
     * @var Integer 
     */
    var $pla_id = null;

    /**
     *  Nombre de la encuesta digitalizada o escaneada.
     * @var String 
     */
    var $documento_soporte = null;

    /**
     *  Fecha que ingresa el usuario de toma de encuesta
     * @var Date 
     */
    var $fecha = null;

    /**
     *  Identificador único interno de cada cuestionario completo
     * @var Integer 
     */
    var $cc = null;

    /**
     *  Motivo por el cual el cuestionario esta incorrecto
     * @var String 
     */
    var $mci = null;

    /**
     *  Identificador único interno de cada resultado final de la encuesta
     * @var Integer 
     */
    var $rf = null;

    /**
     *  Identificador único interno de la inspeccion de la encuesta
     * @var Integer 
     */
    var $vi = null;

    /**
     *  Identificador único interno del resultado de la inspeccion
     * @var Integer 
     */
    var $ri = null;

    /**
     *  Motivo por el cual la encuesta esta incorrecto
     * @var String 
     */
    var $mei = null;

    /**
     *  Identificador único interno del usuario a cargo de la encuesta
     * @var Integer 
     */
    var $usuario = null;

    /**
     *  Identificador único interno del estado de la encuesta
     * @var Integer 
     */
    var $estado = null;

    /**
     *  Instancia de la clase CEjecucionData
     * @var  CEjecucionData
     */
    var $dd = null;

    /**
     *  Arreglo que contiene el tipo de extension de los documentos soporte
     * @var  array
     */
    var $permitidos = array('pdf');

    /*
     * Contructor de la clase
     * @param Integer $id
     * @param CEjecucionData $dd
     */

    function CEncuesta($id, $dd) {
        $this->id = $id;
        $this->dd = $dd;
    }

    public function getId() {
        return $this->id;
    }

    public function getConsecutivo() {
        return $this->consecutivo;
    }

    public function getPla_id() {
        return $this->pla_id;
    }

    public function getDocumento_soporte() {
        return $this->documento_soporte;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function getCc() {
        return $this->cc;
    }

    public function getMci() {
        return $this->mci;
    }

    public function getRf() {
        return $this->rf;
    }

    public function getVi() {
        return $this->vi;
    }

    public function getRi() {
        return $this->ri;
    }

    public function getMei() {
        return $this->mei;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setConsecutivo($consecutivo) {
        $this->consecutivo = $consecutivo;
    }

    public function setPla_id($pla_id) {
        $this->pla_id = $pla_id;
    }

    public function setDocumento_soporte($documento_soporte) {
        $this->documento_soporte = $documento_soporte;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function setCc($cc) {
        $this->cc = $cc;
    }

    public function setMci($mci) {
        $this->mci = $mci;
    }

    public function setRf($rf) {
        $this->rf = $rf;
    }

    public function setVi($vi) {
        $this->vi = $vi;
    }

    public function setRi($ri) {
        $this->ri = $ri;
    }

    public function setMei($mei) {
        $this->mei = $mei;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /*
     * Eliminar una encuestas
     * @return string
     */

    function deletEncuesta() {
        $r = $this->dd->deleteEncuesta($this->id);
        if ($r == 'true') {
            unlink(strtolower((RUTA_DOCUMENTOS . "/EJECUCION/" . $this->getConsecutivo() . "/")) . $this->documento_soporte);
            $msg = EJECUCION_BORRADO;
        } else {
            $msg = ERROR_DE_EJECUCION;
        }
        return $msg;
    }

    /*
     * Eliminar las respuestas de una encuesta
     * @return string
     */

    function deletEncuestaRespuestas() {
        $r = $this->dd->eliminarRespuestas($this->id);
        if ($r == TRUE) {
            $msg = EJECUCION_BORRADO;
        } else {
            $msg = ERROR_DE_EJECUCION;
        }
        return $msg;
    }

    /*
     * Almacena los datos de una encuesta en especifico en la clase
     */

    function loadEncuesta() {
        $r = $this->dd->getEncuestaById($this->getId());
        if ($r != -1) {
            $this->consecutivo = $r['enc_consecutivo'];
            $this->setDocumento_soporte($r['enc_documento_soporte']);
            $this->fecha = $r['enc_fecha'];
            $this->cc = $r['ecc_id'];
            $this->mci = $r['enc_motivo_cuestionario_incorrecto'];
            $this->rf = $r['erf_id'];
            $this->vi = $r['evi_id'];
            $this->ri = $r['eri_id'];
            $this->mei = $r['enc_motivo_encuesta_incorrecta'];
            $this->usuario = $r['usu_id'];
            $this->estado = $r['ees_id'];
        } else {
            $this->consecutivo = '';
            $this->documento_soporte = '';
            $this->fecha = '';
            $this->cc = '';
            $this->mci = '';
            $this->rf = '';
            $this->vi = '';
            $this->ri = '';
            $this->mei = '';
            $this->usuario = '';
            $this->estado = '';
        }
    }

    /*
     * Guarda los datos de una encuesta, sea por primera vez.
     * @return string
     */

    function saveEditEncuesta($archivo, $archivo_anterior, $pla_id) {
        $r = "";
        $extension = explode(".", $archivo['name']);
        $num = count($extension) - 1;
        $noMatch = 0;
        foreach ($this->permitidos as $p) {
            if (strcasecmp($extension[$num], $p) == 0)
                $noMatch = 1;
        }
        if ($archivo['name'] != null) {
            if ($archivo_anterior != NULL) {
                unlink(strtolower((RUTA_DOCUMENTOS . "/EJECUCION/" . $this->getConsecutivo() . "/")) . $archivo_anterior);
            }
            if ($noMatch == 1) {
                if ($archivo['size'] < MAX_SIZE_DOCUMENTOS) {
                    $ruta = (RUTA_DOCUMENTOS . "/EJECUCION/" . $this->getConsecutivo() . "/");
                    $carpetas = explode("/", substr($ruta, 0, strlen($ruta) - 1));
                    $cad = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'];
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
                    if (!move_uploaded_file($archivo['tmp_name'], utf8_decode(($ruta) . $archivo['name']))) {
                        $r = ERROR_COPIAR_ARCHIVO;
                    } else {
                        $this->documento_soporte = ($archivo['name']);
                        $i = $this->dd->updateEjecucion($this->id, $this->documento_soporte, $this->fecha, $this->cc, $this->mci, $this->rf, $this->vi, $this->ri, $this->mei, $this->usuario, $this->estado);
                        if ($i == "true") {
                            $r = DOCUMENTO_EDITADO;
                        } else {
                            $r = ERROR_EDIT_DOCUMENTO;
                        }
                    }
                } else {
                    $r = ERROR_SIZE_ARCHIVO;
                }
            } else {
                $r = ERROR_FORMATO_ARCHIVO;
            }
            return $r;
        } else {
            $r = $this->dd->updateEjecucion($this->id, $archivo_anterior, $this->fecha, $this->cc, $this->mci, $this->rf, $this->vi, $this->ri, $this->mei, $this->usuario);
            if ($r == 'true') {
                $msg = DOCUMENTO_EDITADO;
            } else {
                $msg = ERROR_EDIT_DOCUMENTO;
            }
            return $msg;
        }
    }

    /*
     * Guardar respuestas de una encuesta
     * @param array $arreglo
     */

    function saveRespuestasEncuesta($arreglo) {
        $this->dd->eliminarRespuestas($this->getId());
        $valores = $valores . "'" . $this->getId() . "','" . $arreglo[0]['id'] . "','" . $arreglo[0]['respuesta'] . "'),";
        for ($i = 1; $i < (count($arreglo) - 1); $i++) {
            $valores = $valores . "('" . $this->getId() . "','" . $arreglo[$i]['id'] . "','" . $arreglo[$i]['respuesta'] . "'),";
        }
        $valores = $valores . "('" . $this->getId() . "','" . $arreglo[(count($arreglo) - 1)]['id'] . "','" . $arreglo[(count($arreglo) - 1)]['respuesta'] . "'";
        $r = $this->dd->setSaveRespuestasEncuesta($valores);
        return $r;
    }

    /*
     * Convierte el formato de la fecha 01/02/2014 a Y-m-d
     * @return string
     */

    function obtenerFechaFormato($fechaC) {
        $fecha = explode('/', $fechaC);
        return $fecha[2] . '-' . $fecha[1] . '-' . $fecha[0];
    }

    /*
     * Almacenar Consecutivo, Region, departamento y municipio por defecto
     */

    function saveRDM() { 
        $tipo_encuesta = 0;
        $tipo_encuesta = $this->dd->getTipoEncuesta($this->getId());
        $arreglo = $this->dd->getRDMByEncuestaId($this->getId());
        if ($tipo_encuesta == 1) {
            $valores = $valores . "'" . $this->getId() . "','" . '1' . "','" . $this->getConsecutivo() . "'),";
            $valores = $valores . "('" . $this->getId() . "','" . '3' . "','" . $arreglo[0]['region'] . "'),";
            $valores = $valores . "('" . $this->getId() . "','" . '4' . "','" . $arreglo[0]['departamento'] . "'),";
            $valores = $valores . "('" . $this->getId() . "','" . '5' . "','" . $arreglo[0]['municipio'] . "'";
        } else if ($tipo_encuesta == 2) {
            $valores = $valores . "'" . $this->getId() . "','" . '152' . "','" . $this->getConsecutivo()  . "'),";
            $valores = $valores . "('" . $this->getId() . "','" . '154' . "','" . $arreglo[0]['region'] . "'),";
            $valores = $valores . "('" . $this->getId() . "','" . '155' . "','" . $arreglo[0]['departamento'] . "'),";
            $valores = $valores . "('" . $this->getId() . "','" . '156' . "','" . $arreglo[0]['municipio'] . "'";
        }
        $r = $this->dd->setSaveRespuestasEncuesta($valores);
    }

    /*
     * Importar encuestas
     * @return String
     */

    function cargaMasiva($file_carga, $pla_id) {
        $file = fopen($file_carga['tmp_name'], "r");
        if ($file) {
            while (!feof($file)) {
                $lineas[] = fgets($file);
            }
            fclose($file);
            if ((int) $lineas[2] == (int) $pla_id) {
                $this->setId($this->dd->getEncuestaIdByConsecutivo($lineas[3]));
				if($this->getId()==0){
						$this->setId($lineas[3]);
					}
                $tipoEncuesta = $this->dd->getTipoEncuesta($this->getId());
                $secciones = $this->dd->getSecciones($tipoEncuesta);
                $cont = 0;
                $correct = 1;
                $respuestas = null;
                foreach ($secciones as $s) {
                    $preguntas_base = $this->dd->getPreguntasBaseBySeccion($s['id']);
                    foreach ($preguntas_base as $pb) {
                        $respuestas[$cont]['id'] = $pb['id'];
                        $cont++;
                    }
                }
                for ($j = 4; $j < count($lineas) - 1; $j+=2) {				
                    $this->setId($this->dd->getEncuestaIdByConsecutivo($lineas[$j-1]));
					if($this->getId()==0){
						$this->setId($lineas[$j-1]);
					}
                    $arrayResp = explode('$%$', $lineas[$j]);
                    for ($h = 0; $h < count($arrayResp) - 1; $h++) {
                        $respuestas[$h]['respuesta'] = $arrayResp[$h];
                    }
                    $this->loadEncuesta();
                    if($this->getDocumento_soporte()!='') {
                        $msg = $this->saveRespuestasEncuesta($respuestas);
                    }
                    if ($msg == true && $respuestas[(count($arrayResp) - 2)]['respuesta'] != '') {
                        $this->dd->setEstadoEncuesta($this->getId(), '1');
                        $correct = $correct * 10;
                    }
                }
                if ($correct > 1) {
                    return ENCUESTAS_IMPORTACION_EXITOSA;
                } else {
                    return ERROR_ENCUESTAS_RESPUESTAS;
                }
            }else{
                return ERROR_ENCUESTA_PLA_ID;
            }
        } else {
            return ERROR_ENCUESTAS_IMPORTACION;
        }
    }

}

?>