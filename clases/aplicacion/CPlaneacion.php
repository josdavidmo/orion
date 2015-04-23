<?php
/**
 * Clase destinada al manejo de Planeaciones
 * @version 1.0
 * @since 31/07/2014
 * @author Brian Kings
 */
class CPlaneacion {
    /**
     *  Identificador único interno de cada planeación
     * @var Integer 
     */
    var $id = null;
    /**
     *  Identificador de region
     * @var Integer 
     */
    var $region = null;
    /**
     *  Identificador de departamento
     * @var Integer 
     */
    var $departamento = null;
    /**
     *  Identificador de municipio
     * @var Integer 
     */
    var $municipio = null;
    /**
     *  Identificador de eje
     * @var Integer 
     */
    var $eje = null;
    /**
     *  Numero de encuestas a realizar (diligenciar)
     * @var Integer 
     */
    var $numero_encuestas = null;
    /**
     *  Identificador de estado de la planeación
     * @var Integer 
     */
    var $estado = null;
    /**
     *  Fecha que inicia la planeación
     * @var Date 
     */
    var $fecha_inicio = null;
    /**
     *  Fecha cuando finaliza la planeación
     * @var Date 
     */
    var $fecha_fin = null;
    /**
     *  Identificador del usuario encargado
     * @var Integer 
     */
    var $usuario = null;
    /**
     *  Instancia de la clase CPlaneaionData
     * @var  CPlaneaionData
     */
    var $dd = null;
    /*
     * Constructor de la clase planeación
     * @param Integer $id
     * @param CPlaneaionData $dd
     */
    function CPlaneacion($id, $dd) {
        $this->id = $id;
        $this->dd = $dd;
    }

    public function getFecha_inicio() {
        return $this->fecha_inicio;
    }

    public function getFecha_fin() {
        return $this->fecha_fin;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function setFecha_inicio($fecha_inicio) {
        $this->fecha_inicio = $fecha_inicio;
    }

    public function setFecha_fin($fecha_fin) {
        $this->fecha_fin = $fecha_fin;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function getId() {
        return $this->id;
    }

    public function getMunicipio() {
        return $this->municipio;
    }

    public function getEje() {
        return $this->eje;
    }

    public function getNumero_encuestas() {
        return $this->numero_encuestas;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setMunicipio($municipio) {
        $this->municipio = $municipio;
    }

    public function setEje($eje) {
        $this->eje = $eje;
    }

    public function setNumero_encuestas($numero_encuestas) {
        $this->numero_encuestas = $numero_encuestas;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function getRegion() {
        return $this->region;
    }

    public function getDepartamento() {
        return $this->departamento;
    }

    public function setRegion($region) {
        $this->region = $region;
    }

    public function setDepartamento($departamento) {
        $this->departamento = $departamento;
    }

    /*
     * Obtiene los datos de una planeacion para ser almacenados
     * @return string
     */
    function savePlaneacion() {
        if ($this->id == '') {
            $this->setId($this->dd->getUltimoIdPlaneacion() + 1);
        }
        $i = $this->dd->insertPlaneacion($this->id, $this->municipio, $this->eje, $this->numero_encuestas, $this->fecha_inicio, $this->fecha_fin, $this->usuario);
        $this->dd->getPlaneacionVerEjecucion('1', false);
        if ($i == "true") {
            $r = PLANEACION_AGREGADA;
        } else {
            $r = ERROR_ADD_PLANEACION;
        }
        return $r;
    }
    /*
     * Obtiene los datos de una planeacion para ser eliminados
     * @return string
     */
    function deletPlaneacion() {
        $r = $this->dd->deletePlaneacion($this->id);
        if ($r == 'true') {
            $msg = PLANEACION_BORRADO;
        } else {
            $msg = ERROR_DE_PLANEACION;
        }
        return $msg;
    }
    /*
     * Obtiene los datos de una planeacion para ser actualizados
     * @return string
     */
    function saveEditPlaneacion($estado) {
        $this->dd->getPlaneacionVerEjecucion('1', false);
        $i = $this->dd->updatePlaneacion($this->municipio, $this->eje, $this->numero_encuestas, $this->id, $this->fecha_inicio, $this->fecha_fin, $this->usuario);
        if ($i == 'true') {
            $msg = PLANEACION_EDITADO;
        } else {
            $msg = ERROR_DE_PLANEACION_EDIT;
        }
        return $msg;
    }
    /*
     * Carga los datos de una planeacion ya registrada segun el id
     */
    function loadPlaneacion() {
        $r = $this->dd->getPlaneacionById($this->id);
        if ($r != -1) {
            $this->region = $r['der_id'];
            $this->departamento = $r['dep_id'];
            $this->municipio = $r['mun_id'];
            $this->eje = $r['eje_id'];
            $this->numero_encuestas = $r['pla_numero_encuestas'];
            $this->fecha_inicio = $r['pla_fecha_inicio'];
            $this->fecha_fin = $r['pla_fecha_fin'];
            $this->usuario = $r['usu_id'];
        } else {
            $this->region = '';
            $this->departamento = '';
            $this->municipio = '';
            $this->eje = '';
            $this->numero_encuestas = '';
            $this->fecha_inicio = '';
            $this->fecha_fin = '';
            $this->usuario = '';
        }
    }
    /*
     * Importar planeaciones
     * @return string
     */
    function cargaMasiva($file_carga) {
        require_once './clases/Excel/reader.php';
        $data = new Spreadsheet_Excel_Reader();
        // Set output Encoding.
        $data->setOutputEncoding('CP1251');
        $data->read($file_carga['tmp_name']);
        error_reporting(E_ALL ^ E_NOTICE);
        $id_planeacion = $this->dd->getUltimoIdPlaneacion();
        for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
            $id_planeacion++;
            $this->setId($id_planeacion);
            $this->setMunicipio(($data->sheets[0]['cells'][$i][1]));
            $this->setEje($this->dd->getEjeId($data->sheets[0]['cells'][$i][2]));
            $this->setNumero_encuestas($data->sheets[0]['cells'][$i][3]);
            $this->setFecha_inicio($this->obtenerFechaFormato($data->sheets[0]['cells'][$i][4]));
            $this->setFecha_fin($this->obtenerFechaFormato($data->sheets[0]['cells'][$i][5]));
            $this->setUsuario($this->dd->getUsuarioId($data->sheets[0]['cells'][$i][6]));
            $mens = $this->savePlaneacion();
            $this->createEncuestasAndConsecutive();
        }
        return $mens;
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
     * Crear encuestas con consecutivo
     */
    function createEncuestasAndConsecutive() {
        $valores = null;
        $consecutivo = null;
        $consecutivo = ($this->dd->ultimoConsecutivoEncuesta($this->getMunicipio()));
        for ($i = 1; $i <= $this->getNumero_encuestas(); $i++) {
            $valores = $valores . "('" . ($consecutivo + $i) . "','" . $this->id . "','2')";
            if ($i < $this->getNumero_encuestas()) {
                $valores = $valores . ",";
            }
        }
        $this->dd->createEncuestas($valores);
    }
    /*
     * Crear encuestas nuevas en la edición de una planeación
     * @param Integer $numero_encuestas
     */
    function createEncuestasAndConsecutiveEdited($numero_encuestas){
        $valores = null;
        $consecutivo = null;
        $consecutivo = ($this->dd->ultimoConsecutivoEncuesta($this->getMunicipio()));
        for ($i = 1; $i <= $numero_encuestas; $i++) {
            $valores = $valores . "('" . ($consecutivo + $i) . "','" . $this->getId() . "','2')";
            if ($i < $numero_encuestas) {
                $valores = $valores . ",";
            }
        }
        $this->dd->createEncuestas($valores);
    }
    /**
     * Elimina las encuestasSincompletar
     * @param type $numero_encuestas_eliminar
     * @return string
     */
    function eliminarEncuestasSinCompletar($numero_encuestas_eliminar){
        $sinCompletar=  $this->dd->obtenerNumeroDeEncuestasSinCompletar($this->getId());
        if($numero_encuestas_eliminar<=$sinCompletar){
            $r=$this->dd->deleteEncuestasSinCompletar($this->getId(),$numero_encuestas_eliminar);
            if($r){
                $this->saveEditPlaneacion($this->getEstado());
                return 'Éxito al editar.';
            }else{
                return 'No se pudo realizar la eliminación.';
            }
        }
        else{
            return 'Solo se pueden eliminar '.$sinCompletar.' encuestas';
        }
    }
}
