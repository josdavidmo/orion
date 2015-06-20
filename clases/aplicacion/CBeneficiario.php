<?php

/**
 * Clase CBeneficiario
 * Clase plana o entidad de Beneficiario, contiene la definicion y el manejo
 * de los atributos del beneficiario a traves de la definicion de los mismos
 * y los metodos de set y get.
 * @see beneficiarios.php (@package modulos,@subpackage beneficiarios)
 * @see CBeneficiarioData.php (@package Clases,@subpackage datos)
 * @package clases
 * @subpackage aplicacion
 * @access public
 * @author SERTIC SAS
 * @since @version 2015.01.23
 * @copyright SERTIC SAS
 */
class CBeneficiario {

    /**
     * Almacena el id del beneficiario.
     * @var Integer Id del Beneficiario 
     * @access public.
     */
    var $idBeneficiario = null;

    /**
     * Almacena el codigo interventoria del beneficiario.
     * @var Integer Codigo de Interventoria.
     * @access public.
     */
    var $codigoInterventoria = null;

    /**
     * Almacena el codigo Mintic del beneficiario.
     * @var Integer Codigo Mintic.
     * @access public.
     */
    var $codigoMintic = null;

    /**
     * Almacena el codigo del operador del beneficiario.
     * @var Integer Codigo Operador.
     * @access public.
     */
    var $codigoOperador = null;

    /**
     * Almacena el nombre del beneficiario.
     * @var string Nombre del Beneficiario .
     * @access public.
     */
    var $nombre = null;

    /**
     * Almacena el msns del beneficiario..
     * @var string Msns del Beneficiario.
     * @access public.
     */
    var $msnm = null;

    /**
     * Almacena la latitud en grados.
     * @var Integer Latidud en grados de la ubicacion del Beneficiario.
     * @access public.
     */
    var $latitudGrados = null;

    /**
     * Almacena la latitud en minutos.
     * @var Integer Latidud en minutos de la ubicacion del Beneficiario.
     * @access public.
     */
    var $latitudMinutos = null;

    /**
     * Almacena la latitud en segundos.
     * @var Integer Latidud en segundos de la ubicacion del Beneficiario.
     * @access public.
     */
    var $latitudSegundos = null;

    /**
     * Almacena la posicion en norte o sur.
     * @var Integer South de la ubicacion del Beneficiario.
     * @access public.
     */
    var $south = null;

    /**
     * Almacena la longitud en grados.
     * @var Integer Longitud en grados de la ubicacion del Beneficiario.
     * @access public.
     */
    var $longitudGrados = null;

    /**
     * Almacena la longitud en minutos.
     * @var Integer Longitud en minutos de la ubicacion del Beneficiario.
     * @access public.
     */
    var $longitudMinutos = null;

    /**
     * Almacena la longitud en minutos.
     * @var Integer Longitud en segundos de la ubicacion del Beneficiario.
     * @access public.
     */
    var $longitudSegundos = null;

    /**
     * Almacena la posicion en este u oeste.
     * @var Integer West de la ubicacion del Beneficiario.
     * @access public.
     */
    var $west = null;

    /**
     * Almacena la fecha inicio del benficiario.
     * @var string Fecha Inicio del Beneficiario .
     * @access public.
     */
    var $fechaInicio = null;

    /**
     * Almacena la meta del beneficiario.
     * @var string Meta del Beneficiario .
     * @access public.
     */
    var $meta = null;

    /**
     * Almacena el estado del beneficiario.
     * @var CBasica Estado del del Beneficiario .
     * @access public.
     */
    var $estado = null;

    /**
     * Almacena el dda del beneficiario.
     * @var CBasica Dda del Beneficiario .
     * @access public.
     */
    var $dda = null;

    /**
     * Almacena el grupo del beneficiario.
     * @var CBasica Grupo del Beneficiario .
     * @access public.
     */
    var $grupo = null;

    /**
     * Almacena el centro poblado del beneficiario.
     * @var CPoblado Centro Poblado del Beneficiario .
     * @access public.
     */
    var $centroPoblado = null;

    /**
     * Almacena el tipo de beneficiario.
     * @var CBasica  Tipo de Beneficiario .
     * @access public.
     */
    var $tipo = null;

    /**
     * Constructor de la clase, encargado de inicializar los atributos del 
     * beneficiario y asignarles los valores de los parametros recibidos.
     * @param Integer $idBeneficiario
     * @param Integer $codigoInterventoria
     * @param Integer $codigoMintic
     * @param Integer $codigoOperador
     * @param string $nombre
     * @param string $msnm
     * @param Integer $latitudGrados
     * @param Integer $latitudMinutos
     * @param Integer $latitudSegundos
     * @param Integer $south
     * @param Integer $longitudGrados
     * @param Integer $longitudMinutos
     * @param Integer $longitudSegundos
     * @param Integer $west
     * @param string $fechaInicio
     * @param CBasica $meta
     * @param CBasica $estado
     * @param CBasica $dda
     * @param CBasica $grupo
     * @param CPoblado $centroPoblado
     * @param CBasica $tipo
     */
    function CBeneficiario($idBeneficiario, $codigoInterventoria, $codigoMintic, 
                           $codigoOperador, $nombre, $msnm, $latitudGrados, 
                           $latitudMinutos, $latitudSegundos, $south, 
                           $longitudGrados, $longitudMinutos, $longitudSegundos, 
                           $west, $fechaInicio, $meta, $estado, $dda, $grupo, 
                           $centroPoblado, $tipo) {
        $this->idBeneficiario = $idBeneficiario;
        $this->codigoInterventoria = $codigoInterventoria;
        $this->codigoMintic = $codigoMintic;
        $this->codigoOperador = $codigoOperador;
        $this->nombre = $nombre;
        $this->msnm = $msnm;
        $this->latitudGrados = $latitudGrados;
        $this->latitudMinutos = $latitudMinutos;
        $this->latitudSegundos = $latitudSegundos;
        $this->south = $south;
        $this->longitudGrados = $longitudGrados;
        $this->longitudMinutos = $longitudMinutos;
        $this->longitudSegundos = $longitudSegundos;
        $this->west = $west;
        $this->fechaInicio = $fechaInicio;
        $this->meta = $meta;
        $this->estado = $estado;
        $this->dda = $dda;
        $this->grupo = $grupo;
        $this->centroPoblado = $centroPoblado;
        $this->tipo = $tipo;
        $this->latitudMinutos = $latitudMinutos;
        $this->longitudSegundos = $longitudSegundos;
    }

    /**
     * Obtiene el Id del beneficiario.
     * @return Integer  Id del beneficiario.
     */
    public function getIdBeneficiario() {
        return $this->idBeneficiario;
    }

    /**
     * Obtiene el Codigo de Interventoria del beneficiario.
     * @return Integer Codigo de Interventoria del beneficiario.
     */
    public function getCodigoInterventoria() {
        return $this->codigoInterventoria;
    }

    /**
     * Obtiene el Codigo de Mintic del beneficiario.
     * @return Integer Codigo de Mintic del beneficiario.
     */
    public function getCodigoMintic() {
        return $this->codigoMintic;
    }

    /**
     * Obtiene el Codigo de Operador del beneficiario.
     * @return Integer Codigo de Operador del beneficiario.
     */
    public function getCodigoOperador() {
        return $this->codigoOperador;
    }

    /**
     * Obtiene el Nombre del beneficiario.
     * @return string Nombre del beneficiario.
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Obtiene el Msnm del beneficiario.
     * @return string Msnm del beneficiario.
     */
    public function getMsnm() {
        return $this->msnm;
    }

    /**
     * Obtiene la Latitud en Grados del beneficiario.
     * @return Integer Latitud en Grados del beneficiario.
     */
    public function getLatitudGrados() {
        return $this->latitudGrados;
    }

    /**
     * Obtiene la Latitud en Segundos del beneficiario.
     * @return Integer Latitud en Segundos del beneficiario.
     */
    public function getLatitudSegundos() {
        return $this->latitudSegundos;
    }

    /**
     * Obtiene el South  del beneficiario.
     * @return Integer South del beneficiario.
     */
    public function getSouth() {
        return $this->south;
    }

    /**
     * Obtiene la Longitud en Grados del beneficiario.
     * @return Integer Longitud en Grados del beneficiario.
     */
    public function getLongitudGrados() {
        return $this->longitudGrados;
    }

    /**
     * Obtiene la Longitud en Minutos del beneficiario.
     * @return Integer Longitud en Minutos del beneficiario.
     */
    public function getLongitudMinutos() {
        return $this->longitudMinutos;
    }

    /**
     * Obtiene el West del beneficiario.
     * @return Integer West del beneficiario.
     */
    public function getWest() {
        return $this->west;
    }

    /**
     * Obtiene la meta del beneficiario.
     * @return \CBasica
     */
    public function getMeta() {
        return $this->meta;
    }

    /**
     * Obtiene el estado del beneficiario.
     * @return \CBasica
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * Obtiene el dda del beneficiario.
     * @return \CBasica
     */
    public function getDda() {
        return $this->dda;
    }

    /**
     * Obtiene el grupo del beneficiario.
     * @return \CBasica
     */
    public function getGrupo() {
        return $this->grupo;
    }

    /**
     * Obtiene el centro poblado del beneficiario.
     * @return \CPoblado.
     */
    public function getCentroPoblado() {
        return $this->centroPoblado;
    }

    /**
     * Obtiene el tipo de beneficiario.
     * @return \CBasica
     */
    public function getTipo() {
        return $this->tipo;
    }

    /**
     * Asigna el Id del beneficiario.
     * @param Integer $idBeneficiario Id del beneficiario.
     */
    public function setIdBeneficiario($idBeneficiario) {
        $this->idBeneficiario = $idBeneficiario;
    }

    /**
     * Asigna el Codigo Interventoria del beneficiario.
     * @param Integer $codigoInterventoria Codigo Interventoria del beneficiario.
     */
    public function setCodigoInterventoria($codigoInterventoria) {
        $this->codigoInterventoria = $codigoInterventoria;
    }

    /**
     * Asigna el Codigo Mintic del beneficiario.
     * @param Integer $codigoMintic Codigo Mintic del beneficiario.
     */
    public function setCodigoMintic($codigoMintic) {
        $this->codigoMintic = $codigoMintic;
    }

    /**
     * Asigna el Codigo Operador del beneficiario.
     * @param Integer $codigoOperador Codigo Operador del beneficiario.
     */
    public function setCodigoOperador($codigoOperador) {
        $this->codigoOperador = $codigoOperador;
    }

    /**
     * Asigna el Nombre del beneficiario.
     * @param string $nombre Nombre del beneficiario.
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    /**
     * Asigna el Msnm del beneficiario.
     * @param string $msnm Msnm del beneficiario.
     */
    public function setMsnm($msnm) {
        $this->msnm = $msnm;
    }

    /**
     * Asigna la Latitud en Grados de la ubicacion del beneficiario.
     * @param Integer $latitudGrados Latitud en Grados del beneficiario.
     */
    public function setLatitudGrados($latitudGrados) {
        $this->latitudGrados = $latitudGrados;
    }

    /**
     * Asigna la Latitud en Segundos de la ubicacion del beneficiario.
     * @param Integer $latitudSegundos Latitud en Segundos del beneficiario.
     */
    public function setLatitudSegundos($latitudSegundos) {
        $this->latitudSegundos = $latitudSegundos;
    }

    /**
     * Asigna South al beneficiario.
     * @param Integer $south South del beneficiario.
     */
    public function setSouth($south) {
        $this->south = $south;
    }

    /**
     * Asigna la Longitud en Grados de la ubicacion del beneficiario.
     * @param Integer $longitudGrados Longitud en Grados del beneficiario.
     */
    public function setLongitudGrados($longitudGrados) {
        $this->longitudGrados = $longitudGrados;
    }

    /**
     * Asigna la Longitud en Minutos de la ubicacion del beneficiario.
     * @param Integer $longitudMinutos Longitud en Minutos del beneficiario.
     */
    public function setLongitudMinutos($longitudMinutos) {
        $this->longitudMinutos = $longitudMinutos;
    }

    /**
     * Asigna West al beneficiario.
     * @param Integer $west West del beneficiario.
     */
    public function setWest($west) {
        $this->west = $west;
    }

    /**
     * Asigna la Meta del beneficiario.
     * @param CBasica $meta Meta del beneficiario.
     */
    public function setMeta($meta) {
        $this->meta = $meta;
    }

    /**
     * Asigna el Estado del beneficiario.
     * @param CBasica $estado Estado del beneficiario.
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /**
     * Asigna el Dda del beneficiario.
     * @param CBasica $dda Dda del beneficiario.
     */
    public function setDda($dda) {
        $this->dda = $dda;
    }

    /**
     * Asigna el Grupo del beneficiario.
     * @param CBasica $grupo Grupo del beneficiario.
     */
    public function setGrupo($grupo) {
        $this->grupo = $grupo;
    }

    /**
     * Asigna el Centro Poblado del beneficiario.
     * @param CPoblado $centroPoblado Centro Poblado del beneficiario.
     */
    public function setCentroPoblado($centroPoblado) {
        $this->centroPoblado = $centroPoblado;
    }

    /**
     * Asigna el Tipo del beneficiario.
     * @param CBasica $tipo Tipo del beneficiario.
     */
    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    /**
     * Obtiene la Latitud en Minutos del beneficiario.
     * @return Integer Latitud en Minutos del beneficiario.
     */
    function getLatitudMinutos() {
        return $this->latitudMinutos;
    }

    /**
     * Asigna la Latitud en Minutos de la ubicacion del beneficiario.
     * @param Integer $latitudMinutos Latitud en Minutos del beneficiario.
     */
    function setLatitudMinutos($latitudMinutos) {
        $this->latitudMinutos = $latitudMinutos;
    }

    /**
     * Obtiene la Longitud en Segundos del beneficiario.
     * @return Integer Longitud en Segundos del beneficiario.
     */
    function getLongitudSegundos() {
        return $this->longitudSegundos;
    }

    /**
     * Asigna la Longitud en Segundos de la ubicacion del beneficiario.
     * @param Integer $longitudSegundos Longitud en Segundos del beneficiario.
     */
    function setLongitudSegundos($longitudSegundos) {
        $this->longitudSegundos = $longitudSegundos;
    }

    /**
     * Asigna la Fecha Inicio del beneficiario.
     * @param string $fechaInicio Fecha Inicio del beneficiario.
     */
    function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;
    }

    /**
     * Obtiene la Fecha Inicio del beneficiario.
     * @return string Fecha Inicio del beneficiario.
     */
    function getFechaInicio() {
        return $this->fechaInicio;
    }

}
