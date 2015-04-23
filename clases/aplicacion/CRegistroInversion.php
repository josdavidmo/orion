<?php

/**
 * Clase Plana que maneja el registro de la inversión, contiene los metodos para
 * agregar, actualizar y eliminar.
 * @author Brian Kings
 * @version 1.0
 * @since 08/08/2014
 */
class CRegistroInversion {
    
    /** Almacena el id del Registro de la Inversion. */
    var $id=null;
    /** Almacena la actividad del Registro de la Inversion. */
    var $actividad = null;
    /** Almacena la fecha del Registro de la Inversion. */
    var $fecha = null;
    /** Almacena el proveedor del Registro de la Inversion. */
    var $proveedor = null;
    /** Almacena el documento del Registro de la Inversion. */
    var $numero_documento = null;
    /** Almacena el valor del Registro de la Inversion. */
    var $valor = null;
    /** Almacena las observaciones del Registro de la Inversion. */
    var $observaciones = null;
    /** Almacena el documento soporte del Registro de la Inversion. */
    var $documento_soporte = null;
    /** Almacena los archivos permitidos. */
    var $permitidos = array('pdf', 'doc', 'xls', 'ppt', 'docx',
                            'xlsx', 'gif', 'jpg', 'png', 'tif', 'zip', 'rar');
    /** Almacena la conexion con la base de datos. */
    var $dd = null;

    /**
     * Constructor de la clase
     * @param integer $id
     * @param CRegistroInversionData $dd
     */
    function CRegistroInversion($id, $dd) {
        $this->id = $id;
        $this->dd = $dd;
    }
    
    /**
     * Obtiene el id del Registro de la INversion.
     * @return type
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Asigna el id del Registro de la Inversion.
     * @param type $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Obtiene la actividad del registro de la inversion.
     * @return type
     */
    public function getActividad() {
        return $this->actividad;
    }

    /**
     * Obtiene la fecha del Registro de la inversion.
     * @return type
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Obtiene el proveedor de la vigencia.
     * @return type
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * Obtiene el numero de documento del Registro de la Inversion.
     * @return type
     */
    public function getNumeroDocumento() {
        return $this->numero_documento;
    }

    /**
     * Obtiene el valor del Registro de la Inversion.
     * @return type
     */
    public function getValor() {
        return $this->valor;
    }

    /**
     * Obtiene las observaciones del Registro de la Inversion.
     * @return type
     */
    public function getObservaciones() {
        return $this->observaciones;
    }

    /**
     * Obtiene el documento soporte del Registro de la Inversion.
     * @return type
     */
    public function getDocumentoSoporte() {
        return $this->documento_soporte;
    }

    /**
     * Asigna la actividad del Registro de la Inversion.
     * @param type $actividad
     */
    public function setActividad($actividad) {
        $this->actividad = $actividad;
    }

    /**
     * Asigna la fecha del Registro de la Inversion.
     * @param type $fecha
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    /**
     * Asigna el proveedor del Registro de la inversion.
     * @param type $proveedor
     */
    public function setProveedor($proveedor) {
        $this->proveedor = $proveedor;
    }

    /**
     * Asigna el numero de documento del Registro de la inversion.
     * @param type $numero_documento
     */
    public function setNumeroDocumento($numero_documento) {
        $this->numero_documento = $numero_documento;
    }

    /**
     * Asigna el valor del Registro de la Inversion.
     * @param type $valor
     */
    public function setValor($valor) {
        $this->valor = str_replace('.', '',$valor);
    }

    /**
     * Asigna las observaciones del Registro de la Inversion.
     * @param type $observaciones
     */
    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    /**
     * Asigna el documento soporte del Registro de la Inversion.
     * @param type $documento_soporte
     */
    public function setDocumentoSoporte($documento_soporte) {
        $this->documento_soporte = $documento_soporte;
    }

    /**
     * Guarda el Registro de la Inversion dado el documento soporte.
     * @param type $archivo
     * @return string
     */
    function saveRegistroInversion($archivo) {
        $r = "";
        $extension = explode(".", $archivo['name']);
        $num = count($extension) - 1;
        $noMatch = 0;
        foreach ($this->permitidos as $p) {
            if (strcasecmp($extension[$num], $p) == 0)
                $noMatch = 1;
        }
        if ($archivo['name'] != null) {
            if ($noMatch == 1) {
                if ($archivo['size'] < MAX_SIZE_DOCUMENTOS) {
                    $nombre_compuesto = strtoupper($archivo['name']);
                    $ruta = (RUTA_DOCUMENTOS . "/Interventoria/Registro Inversion/");
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
                    if (!move_uploaded_file($archivo['tmp_name'], utf8_decode($ruta . $nombre_compuesto))) {
                        $r = ERROR_COPIAR_ARCHIVO;
                    } else {
                        $this->setDocumentoSoporte($nombre_compuesto);
                        $i = $this->dd->insertRegistroInversion($this);
                        if ($i == "true") {
                            $r = REGISTRO_INVERSION_AGREGADA;
                        } else {
                            $r = ERROR_ADD_REGISTRO_INVERSION;
                        }
                    }
                } else {
                    $r = ERROR_SIZE_ARCHIVO;
                }
            } else {
                $r = ERROR_FORMATO_ARCHIVO;
            }
        } else {
            $r = ERROR_CONFIGURACION_RUTA;
        }
        return $r;
    }

    /**
     * Borra un registro de la inversion.
     * @param type $archivo
     * @return string
     */
    function deletRegistroInversion($archivo) {
        $ruta = (RUTA_DOCUMENTOS . "/Interventoria/Registro Inversion/");
        $r = $this->dd->deleteRegistroInversion($this->id);
        if ($r == 'true') {
            unlink(strtoupper($ruta) . $archivo);
            $msg = REGISTRO_INVERSION_BORRADO;
        } else {
            $msg = ERROR_DE_REGISTRO_INVERSION;
        }
        return $msg;
    }

    /**
     * Carga los datos de registro de la inversion y los almacena en este objeto.
     */
    function loadRegistroInversion() {
        $r = $this->dd->getRegistroInversionById($this->id);
        if ($r != -1) {
            $this->actividad = $r['act_id'];
            $this->fecha = $r['rin_fecha'];
            $this->proveedor = $r['id_prove'];
            $this->numero_documento = $r['rin_numero_documento'];
            $this->valor = number_format($r['rin_valor'], 0, ',', '.');
            $this->observaciones = $r['rin_observaciones'];
            $this->documento_soporte = $r['rin_documento_soporte'];
        } else {
            $this->actividad = '';
            $this->fecha = '';
            $this->proveedor = '';
            $this->numero_documento = '';
            $this->valor = '';
            $this->observaciones = '';
            $this->documento_soporte = '';
        }
    }

    /**
     * Actualiza la información de un registro de inversión
     * @param type $archivo
     * @param type $archivo_anterior
     * @return string
     */
    function saveEditRegistroInversion($archivo, $archivo_anterior) {
        $r = "";
        $extension = explode(".", $archivo['name']);
        $num = count($extension) - 1;
        $noMatch = 0;
        foreach ($this->permitidos as $p) {
            if (strcasecmp($extension[$num], $p) == 0)
                $noMatch = 1;
        }
        if ($archivo['name'] != null) {
            if ($noMatch == 1) {
                if ($archivo['size'] < MAX_SIZE_DOCUMENTOS) {
                    $nombre_compuesto = strtoupper($archivo['name']);
                    $ruta = (RUTA_DOCUMENTOS . "/Interventoria/Registro Inversion/");
                    unlink(strtoupper($ruta) . $archivo_anterior);
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
                    if (!move_uploaded_file($archivo['tmp_name'], utf8_decode($ruta . $nombre_compuesto))) {
                        $r = ERROR_COPIAR_ARCHIVO;
                    } else {
                        $this->setDocumentoSoporte($nombre_compuesto);
                        $i = $this->dd->updateRegistroInversion($this);
                        if ($i == "true") {
                            $r = REGISTRO_INVERSION_EDITADO;
                        } else {
                            $r = ERROR_EDIT_REGISTRO_INVERSION;
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
            $this->setDocumentoSoporte($archivo_anterior);
            $i = $this->dd->updateRegistroInversion($this);
            if ($i == 'true') {
                $msg = REGISTRO_INVERSION_EDITADO;
            } else {
                $msg = ERROR_DE_REGISTRO_INVERSION_EDIT;
            }
            return $msg;
        }
    }

}
