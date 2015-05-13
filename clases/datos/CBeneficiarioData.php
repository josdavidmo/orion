<?php

/**
 * Clase Beneficiario Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2015.01.23
 * @copyright SERTIC SAS
 */
class CBeneficiarioData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    function CBeneficiarioData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene los centros poblados almacenados en la base de datos.
     * @return \CCentroPoblado
     */
    public function getCentrosPoblados($criterio = "0") {
        $centrosPoblados = null; 
        $sql = "SELECT idCentroPoblado, codigoDane, nombre "
                . "FROM centropoblado "
                . "WHERE mun_id = " . $criterio . " "
                . "ORDER BY nombre ASC ";
		if($criterio == "0"){
			$sql = "SELECT idCentroPoblado, codigoDane, nombre "
                . "FROM centropoblado "
                . "ORDER BY nombre ASC ";
		}
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $centroPoblado = new CCentroPoblado($w['idCentroPoblado'], $w['codigoDane'], $w['nombre'], null);
                $centrosPoblados[$cont] = $centroPoblado;
                $cont++;
            }
        }
        return $centrosPoblados;
    }
    
    public function getUbicacionBeneficiarioById($idBeneficiario){
        $sql = "SELECT b.idBeneficiario, re.der_id, de.dep_id, mu.mun_id,
                       c.idCentroPoblado
                FROM beneficiario b 
                INNER JOIN centropoblado c ON c.idCentroPoblado = b.idCentroPoblado 
                INNER JOIN municipio mu ON mu.mun_id = c.mun_id 
                INNER JOIN departamento de ON de.dep_id = mu.dep_id 
                INNER JOIN departamento_region re ON re.der_id = de.der_id 
                WHERE b.idBeneficiario = ".$idBeneficiario;
        $r = $this->db->ejecutarConsulta($sql);
        $ubicacion = null;
        if ($r) {
            $w = mysql_fetch_array($r);
            $ubicacion = array('region' => $w['der_id'], 
                               'departamento' => $w['dep_id'], 
                               'municipio' => $w['mun_id'], 
                               'centroPoblado' => $w['idCentroPoblado']);
        }
        return $ubicacion;
    }

    /**
     * Obtiene el centro poblado dado su id.
     * @param type $idCentroPoblado
     * @return \CCentroPoblado
     */
    public function getCentrosPobladosById($idCentroPoblado) {
        $sql = "SELECT idCentroPoblado, codigoDane, nombre "
                . "FROM centropoblado "
                . "WHERE idCentroPoblado = " . $idCentroPoblado;
        $r = $this->db->ejecutarConsulta($sql);
        $centroPoblado = null;
        if ($r) {
            $w = mysql_fetch_array($r);
            $centroPoblado = new CCentroPoblado($w['idCentroPoblado'], $w['codigoDane'], $w['nombre'], null);
        }
        return $centroPoblado;
    }
	
	/**
     * Obtiene un centro poblado dado el codigo dane.
     * @param type $codigoDane
     * @return \CCentroPoblado
     */
    public function getCentroPobladoByCodigoDane($codigoDane){
        $sql = "SELECT idCentroPoblado, codigoDane, nombre "
                . "FROM centropoblado "
                . "WHERE codigoDane = " . $codigoDane;
		$r = $this->db->ejecutarConsulta($sql);
        $centroPoblado = null;
        if ($r) {
            $w = mysql_fetch_array($r);
            $centroPoblado = new CCentroPoblado($w['idCentroPoblado'], $w['codigoDane'], $w['nombre'], null);
        }
        return $centroPoblado;
    }

    /**
     * Obtener tiene todos los beneficiarios almacenados en la base de datos.
     * @param var $criterio
     * @return type
     */
    public function getBeneficiarios($criterio = "1") {
        $beneficiarios = null;
        $sql = "SELECT b.idBeneficiario, CONCAT(re.der_nombre,'-',de.dep_nombre,'-',mu.mun_nombre,'-', c.nombre) as ubicacion, "
                . "b.codigoInterventoria, "
                . "b.codigoMintic, b.codigoOperador, b.nombre, b.fechaInicio, "
                . "b.latitudGrados, b.latitudMinutos, b.latitudSegundos, "
                . "b.S, b.longitudGrados, b.longitudMinutos, "
                . "b.longitudSegundos, b.W, m.descripcionMetaBeneficiario, "
                . "e.descripcionEstadoBeneficiario, "
                . "d.descripcionDDABeneficiario, "
                . "g.descripcionGrupoBeneficiario,"
                . "t.descripcionTipoBeneficiario, "
				. "b.observaciones "
                . "FROM beneficiario b "
                . "INNER JOIN metabeneficiario m ON m.idMetaBeneficiario = b.idMetaBeneficiario "
                . "INNER JOIN estadobeneficiario e ON e.idEstadoBeneficiario = b.idEstadoBeneficiario "
                . "INNER JOIN ddabeneficiario d ON d.idDDABeneficiario = b.idDDABeneficiario "
                . "INNER JOIN grupobeneficiario g ON g.idGrupoBeneficiario = b.idGrupoBeneficiario "
                . "INNER JOIN tipobeneficiario t ON t.idTipoBeneficiario = b.idTipoBeneficiario "
                . "INNER JOIN centropoblado c ON c.idCentroPoblado = b.idCentroPoblado "
                . "INNER JOIN municipio mu ON mu.mun_id = c.mun_id "
                . "INNER JOIN departamento de ON de.dep_id = mu.dep_id "
                . "INNER JOIN departamento_region re ON re.der_id = de.der_id "
                . "WHERE "
                . $criterio . " "
                . "ORDER BY ubicacion ASC";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $beneficiarios[$cont]['idBeneficiario'] = $w['idBeneficiario'];			
                $beneficiarios[$cont]['codigoInterventoria'] = $w['codigoInterventoria'];
                $beneficiarios[$cont]['codigoMintic'] = $w['codigoMintic'];
                $beneficiarios[$cont]['codigoOperador'] = $w['codigoOperador'];
                $beneficiarios[$cont]['centropoblado'] = $w['ubicacion'];
                $beneficiarios[$cont]['nombre'] = $w['nombre'];
                $sur = "N";
                if ($w['S']) {
                    $sur = "S";
                }
                $west = "E";
                if ($w['W']) {
                    $west = "W";
                }
                $beneficiarios[$cont]['latitud'] = $w['latitudGrados'] . "&deg;" . $w['latitudMinutos'] . "'" . $w['latitudSegundos'] . "''" . $sur;
                $beneficiarios[$cont]['longitud'] = $w['longitudGrados'] . "&deg;" . $w['longitudMinutos'] . "'" . $w['longitudSegundos'] . "''" . $west;
                $beneficiarios[$cont]['fechaInicio'] = $w['fechaInicio'];
                $beneficiarios[$cont]['estado'] = $w['descripcionEstadoBeneficiario'];
                $beneficiarios[$cont]['dda'] = $w['descripcionDDABeneficiario'];
                $beneficiarios[$cont]['grupo'] = $w['descripcionGrupoBeneficiario'];
                $beneficiarios[$cont]['tipo'] = $w['descripcionTipoBeneficiario'];
				$beneficiarios[$cont]['observaciones'] = $w['observaciones'];
                $cont++;
            }
        }
        return $beneficiarios;
    }

    /**
     * Obtener tiene todos los beneficiarios almacenados en la base de datos.
     * @param var $tipo
     * @return type
     */
    public function getBeneficiariosByTipo($tipo, $condicion) {
        $criterio = "(b.idTipoBeneficiario != 5 AND b.idTipoBeneficiario != 6)";
        if ($tipo == 'false') {
            $criterio = "(b.idTipoBeneficiario = 5 OR b.idTipoBeneficiario = 6)";
        }
        $criterio .= " AND " . $condicion;
        $beneficiarios = null;
        $sql = "SELECT b.idBeneficiario, "
                . "CONCAT(re.der_nombre,'-',de.dep_nombre,'-',mu.mun_nombre,'-', c.nombre) as ubicacion, "
                . "b.codigoInterventoria, "
                . "b.codigoMintic, b.codigoOperador, b.nombre, b.msnm, "
                . "b.latitudGrados, b.latitudMinutos, b.latitudSegundos, "
                . "b.S, b.longitudGrados, b.longitudMinutos, "
                . "b.longitudSegundos, b.W, b.fechaInicio, m.descripcionMetaBeneficiario, "
                . "e.descripcionEstadoBeneficiario, "
                . "d.descripcionDDABeneficiario, "
                . "g.descripcionGrupoBeneficiario, "
                . "t.descripcionTipoBeneficiario, "
				. "b.observaciones "
                . "FROM beneficiario b "
                . "INNER JOIN metabeneficiario m ON m.idMetaBeneficiario = b.idMetaBeneficiario "
                . "INNER JOIN estadobeneficiario e ON e.idEstadoBeneficiario = b.idEstadoBeneficiario "
                . "INNER JOIN ddabeneficiario d ON d.idDDABeneficiario = b.idDDABeneficiario "
                . "INNER JOIN grupobeneficiario g ON g.idGrupoBeneficiario = b.idGrupoBeneficiario "
                . "INNER JOIN tipobeneficiario t ON t.idTipoBeneficiario = b.idTipoBeneficiario "
                . "INNER JOIN centropoblado c ON c.idCentroPoblado = b.idCentroPoblado "
                . "INNER JOIN municipio mu ON mu.mun_id = c.mun_id "
                . "INNER JOIN departamento de ON de.dep_id = mu.dep_id "
                . "INNER JOIN departamento_region re ON re.der_id = de.der_id "
                . "WHERE "
                . $criterio . " "
                . "ORDER BY ubicacion ASC";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $beneficiarios[$cont]['idBeneficiario'] = $w['idBeneficiario'];
				$beneficiarios[$cont]['codigoInterventoria'] = $w['codigoInterventoria'];
                $beneficiarios[$cont]['codigoMintic'] = $w['codigoMintic'];
                $beneficiarios[$cont]['codigoOperador'] = $w['codigoOperador'];        
                $beneficiarios[$cont]['ubicacion'] = $w['ubicacion'];
                $beneficiarios[$cont]['nombre'] = $w['nombre'];
                $sur = "N";
                if ($w['S']) {
                    $sur = "S";
                }
                $west = "E";
                if ($w['W']) {
                    $west = "W";
                }
                $beneficiarios[$cont]['latitud'] = $w['latitudGrados'] . "&deg;" . $w['latitudMinutos'] . "'" . $w['latitudSegundos'] . "''" . $sur;
                $beneficiarios[$cont]['longitud'] = $w['longitudGrados'] . "&deg;" . $w['longitudMinutos'] . "'" . $w['longitudSegundos'] . "''" . $west;
                $beneficiarios[$cont]['fechaInicio'] = $w['fechaInicio'];
                $beneficiarios[$cont]['estado'] = $w['descripcionEstadoBeneficiario'];
                $beneficiarios[$cont]['grupo'] = $w['descripcionGrupoBeneficiario'];
                $beneficiarios[$cont]['tipo'] = $w['descripcionTipoBeneficiario'];
				$beneficiarios[$cont]['observaciones'] = $w['observaciones'];
                $cont++;
            }
        }
        return $beneficiarios;
    }

    /**
     * Obtiene la informacion de un beneficiario dado el id del mismo.
     * @param type $idBeneficiario
     * @return \CBeneficiario
     */
    public function getBeneficiarioById($idBeneficiario) {
        $beneficiario = null;
        $sql = "SELECT * FROM beneficiario "
                . "WHERE idBeneficiario = " . $idBeneficiario;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $beneficiario = new CBeneficiario($w['idBeneficiario'], $w['codigoInterventoria'], 
										      $w['codigoMintic'], $w['codigoOperador'], 
											  $w['nombre'], $w['msnm'], $w['latitudGrados'], 
											  $w['latitudMinutos'], $w['latitudSegundos'], 
											  $w['S'], $w['longitudGrados'], $w['longitudMinutos'], 
											  $w['longitudSegundos'], $w['W'], 
											  $w['fechaInicio'], $w['idMetaBeneficiario'], 
											  $w['idEstadoBeneficiario'], $w['observaciones'],
											  $w['idDDABeneficiario'], $w['idGrupoBeneficiario'], 
											  $w['idCentroPoblado'], $w['idTipoBeneficiario']);
        }
        return $beneficiario;
    }

    /**
     * Inserta un beneficiario en la base de datos.
     * @param \CBeneficiario $beneficiario
     * @return type
     */
    public function insertBeneficiario($beneficiario) {
        $tabla = "beneficiario";
        $campos = 'codigoInterventoria,codigoMintic,codigoOperador,nombre,msnm, '
                . 'latitudGrados,latitudMinutos,latitudSegundos,S, '
                . 'longitudGrados,longitudMinutos,longitudSegundos,W,fechaInicio, '
                . 'idMetaBeneficiario,idEstadoBeneficiario,idDDABeneficiario, '
                . 'idGrupoBeneficiario,idCentroPoblado,idTipoBeneficiario, observaciones';
        $valores = "'" . $beneficiario->getCodigoInterventoria() . "','"
                . $beneficiario->getCodigoMintic() . "','"
                . $beneficiario->getCodigoOperador() . "','"
                . $beneficiario->getNombre() . "','"
                . $beneficiario->getMsnm() . "','"
                . $beneficiario->getLatitudGrados() . "','"
                . $beneficiario->getLatitudMinutos() . "','"
                . $beneficiario->getLatitudSegundos() . "','"
                . $beneficiario->getSouth() . "','"
                . $beneficiario->getLongitudGrados() . "','"
                . $beneficiario->getLongitudMinutos() . "','"
                . $beneficiario->getLongitudSegundos() . "','"
                . $beneficiario->getWest() . "','"
                . $beneficiario->getFechaInicio() . "','"
                . $beneficiario->getMeta() . "','"
                . $beneficiario->getEstado() . "','"
                . $beneficiario->getDda() . "','"
                . $beneficiario->getGrupo() . "','"
                . $beneficiario->getCentroPoblado() . "','"
                . $beneficiario->getTipo() . "','"
				. $beneficiario->getObservaciones() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Actualiza un beneficiario de la base de datos.
     * @param \CBeneficiario $beneficiario
     * @return type
     */
    public function updateBeneficiario($beneficiario) {
        $tabla = "beneficiario";
        $campos = array('codigoInterventoria', 'codigoMintic',
            'codigoOperador', 'nombre', 'msnm', 'latitudGrados',
            'latitudMinutos', 'latitudSegundos', 'S', 'longitudGrados',
            'longitudMinutos', 'longitudSegundos', 'W', 'fechaInicio',
            'idMetaBeneficiario', 'idEstadoBeneficiario',
            'idDDABeneficiario', 'idGrupoBeneficiario',
            'idCentroPoblado', 'idTipoBeneficiario', 'observaciones');
        $valores = array("'" . $beneficiario->getCodigoInterventoria() . "'",
            "'" . $beneficiario->getCodigoMintic() . "'",
            "'" . $beneficiario->getCodigoOperador() . "'",
            "'" . $beneficiario->getNombre() . "'",
            "'" . $beneficiario->getMsnm() . "'",
            "'" . $beneficiario->getLatitudGrados() . "'",
            "'" . $beneficiario->getLatitudMinutos() . "'",
            "'" . $beneficiario->getLatitudSegundos() . "'",
            "'" . $beneficiario->getSouth() . "'",
            "'" . $beneficiario->getLongitudGrados() . "'",
            "'" . $beneficiario->getLongitudMinutos() . "'",
            "'" . $beneficiario->getLongitudSegundos() . "'",
            "'" . $beneficiario->getWest() . "'",
            "'" . $beneficiario->getFechaInicio() . "'",
            "'" . $beneficiario->getMeta() . "'",
            "'" . $beneficiario->getEstado() . "'",
            "'" . $beneficiario->getDda() . "'",
            "'" . $beneficiario->getGrupo() . "'",
            "'" . $beneficiario->getCentroPoblado() . "'",
            "'" . $beneficiario->getTipo() . "'",
			"'" . $beneficiario->getObservaciones() . "'");
        $condicion = "idBeneficiario = " . $beneficiario->getIdBeneficiario();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Elimina un beneficiario de la base de datos
     * @param type $idBeneficiario
     * @return type
     */
    public function deleteBodegaById($idBeneficiario) {
        $tabla = "beneficiario";
        $predicado = "idBeneficiario = " . $idBeneficiario;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Obtiene todos los historiales de cambio sobre un beneficario.
     * @return type
     */
    public function getHistorialCambiosBeneficiarios() {
        $historialcambiosbeneficiarios = null;
        $sql = "SELECT idHistorialCambiosBeneficiarios, "
                . "CONCAT(c1.nombre,' - ', t1.descripcionTipoBeneficiario, ' - ',b1.nombre) as b1, "
                . "CONCAT(c1.nombre,' - ', t2.descripcionTipoBeneficiario, ' - ',b2.nombre) as b2, "
                . "t.descripcion, "
                . "h.fecha, h.soporte, h.observaciones "
                . "FROM historialcambiosbeneficiarios h, "
                . "beneficiario b1, "
                . "beneficiario b2, "
                . "tipocambiobeneficiario t, "
				. "observaciones o, "
                . "centroPoblado c1, "
                . "centroPoblado c2, "
                . "tipoBeneficiario t1, "
                . "tipoBeneficiario t2 "
                . "WHERE b1.idBeneficiario = h.idBeneficiario1 "
                . "AND b2.idBeneficiario = h.idBeneficiario2 "
                . "AND t.idTipoCambioBeneficiario = h.idTipoCambioBeneficiario "
                . "AND c1.idCentroPoblado = b1.idCentroPoblado "
                . "AND c2.idCentroPoblado = b2.idCentroPoblado "
                . "AND t1.idTipoBeneficiario = b1.idTipoBeneficiario "
                . "AND t2.idTipoBeneficiario = b2.idTipoBeneficiario";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $historialcambiosbeneficiarios[$cont]['id'] = $w['idHistorialCambiosBeneficiarios'];
                $historialcambiosbeneficiarios[$cont]['idBeneficiario1'] = $w['b1'];
                $historialcambiosbeneficiarios[$cont]['idBeneficiario2'] = $w['b2'];
                $historialcambiosbeneficiarios[$cont]['tipoCambioBeneficiario'] = $w['descripcion'];
                $historialcambiosbeneficiarios[$cont]['fecha'] = $w['fecha'];
                $historialcambiosbeneficiarios[$cont]['soporte'] = "<a href='././soportes/historialbeneficiarios/" . $w['soporte'] . "' target='_blank'>{$w['soporte']}</a>";
                $historialcambiosbeneficiarios[$cont]['observaciones'] = $w['observaciones'];
                $cont++;
            }
        }
        return $historialcambiosbeneficiarios;
    }

    /**
     * Obtiene un historial de cambios de beneficiario dado su id.
     * @param type $idHistorial
     * @return \CHistorialCambiosBeneficiario
     */
    public function getHistorialCambiosBeneficiariosById($idHistorial) {
        $historialcambiosbeneficiario = null;
        $sql = "SELECT * FROM historialcambiosbeneficiarios "
                . "WHERE idHistorialCambiosBeneficiarios = " . $idHistorial;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $historialcambiosbeneficiario = new CHistorialCambiosBeneficiario($w['idHistorialCambiosBeneficiarios'], $w['idBeneficiario1'], $w['idBeneficiario2'], $w['idTipoCambioBeneficiario'], $w['fecha'], $w['soporte'], $w['observaciones']);
        }
        return $historialcambiosbeneficiario;
    }

    /**
     * Inserta el historial de cambios de un beneficiario.
     * @param \CHistorialCambiosBeneficiario $historialCambioBeneficiario
     * @return type
     */
    public function insertHistorialCambiosBeneficiarios($historialCambioBeneficiario) {
        $r = 'false';
        if ($this->guardarArchivo($historialCambioBeneficiario->getSoporte())) {
            $tabla = "historialcambiosbeneficiarios";
            $campos = 'idBeneficiario1,idBeneficiario2,idTipoCambioBeneficiario,'
                    . 'fecha,soporte,observaciones';
            $valores = "'" . $historialCambioBeneficiario->getBeneficiario1() . "','"
                    . $historialCambioBeneficiario->getBeneficiario2() . "','"
                    . $historialCambioBeneficiario->getTipoCambioBeneficiario() . "','"
                    . $historialCambioBeneficiario->getFecha() . "','"
                    . $historialCambioBeneficiario->getSoporte()['name'] . "','"
                    . $historialCambioBeneficiario->getObservaciones() . "'";
            $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        }
        return $r;
    }

    /**
     * Actualiza un beneficiario de la base de datos.
     * @param \CHistorialCambiosBeneficiario $historialCambioBeneficiario
     * @return type
     */
    public function updateHistorialCambiosBeneficiarios($historialCambioBeneficiario) {
        $r = true;
        $tabla = "historialcambiosbeneficiarios";
        $condicion = "idHistorialCambiosBeneficiarios = "
                . $historialCambioBeneficiario->getIdHistorialCambio();
        if ($historialCambioBeneficiario->getSoporte()['name'] != "") {
            $r = $this->guardarArchivo($historialCambioBeneficiario->getSoporte());
            if ($r) {
                $campos = array('idBeneficiario1', 'idBeneficiario2', 'idTipoCambioBeneficiario',
                    'fecha', 'soporte', 'observaciones');
                $valores = array("'" . $historialCambioBeneficiario->getBeneficiario1() . "'",
                    "'" . $historialCambioBeneficiario->getBeneficiario2() . "'",
                    "'" . $historialCambioBeneficiario->getTipoCambioBeneficiario() . "'",
                    "'" . $historialCambioBeneficiario->getFecha() . "'",
                    "'" . $historialCambioBeneficiario->getSoporte()['name'] . "'",
                    "'" . $historialCambioBeneficiario->getObservaciones() . "'");
            }
        } else {
            $campos = array('idBeneficiario1', 'idBeneficiario2', 'idTipoCambioBeneficiario',
                'fecha', 'observaciones');
            $valores = array("'" . $historialCambioBeneficiario->getBeneficiario1() . "'",
                "'" . $historialCambioBeneficiario->getBeneficiario2() . "'",
                "'" . $historialCambioBeneficiario->getTipoCambioBeneficiario() . "'",
                "'" . $historialCambioBeneficiario->getFecha() . "'",
                "'" . $historialCambioBeneficiario->getObservaciones() . "'");
        }
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Almacena un archivo en la ruta historial beneficiarios.
     * @param type $archivo
     * @return type
     */
    private function guardarArchivo($archivo) {
        $ruta = RUTA_DOCUMENTOS . "/historialbeneficiarios/";
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
        return move_uploaded_file($archivo['tmp_name'], utf8_decode($ruta_destino . $archivo['name']));
    }

    /**
     * Elimina un historial de cambios de la base de datos.
     * @param type $idHistorialCambiosBeneficiarios
     * @return type
     */
    public function deleteHistorialCambiosBeneficiariosById($idHistorialCambiosBeneficiarios) {
        $tabla = "historialcambiosbeneficiarios";
        $predicado = "idHistorialCambiosBeneficiarios = " . $idHistorialCambiosBeneficiarios;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }
	
	/**
	* Carga masivamente los datos contenidos en un archivo excel.
	* @param type $file
	* @return type $r
	*/
    public function cargaMasiva($file) {
        require_once './clases/Excel/oleread.inc';
        $daoBasica = new CBasicaData($this->db);
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');
        $data->read($file['tmp_name']);
        error_reporting(E_ALL ^ E_NOTICE);
        $r = TRUE;
        for ($i = 3; $i <= $data->sheets[0]['numRows']; $i++) {
            $id = null;
            $codigoInterventoria = $data->sheets[0]['cells'][$i][1];
            $codigoMintic = $data->sheets[0]['cells'][$i][2];
            $codigoOperador = $data->sheets[0]['cells'][$i][3];
            $nombre = $data->sheets[0]['cells'][$i][4];
            $centroPoblado = $data->sheets[0]['cells'][$i][5];
			if($centroPoblado == ""){
				continue;
			}
            $centroPoblado = $this->getCentroPobladoByCodigoDane($centroPoblado)->getIdCentroPoblado();
            $msnm = $data->sheets[0]['cells'][$i][6];
            $latitudGrados = $data->sheets[0]['cells'][$i][7];
            $latitudMinutos = $data->sheets[0]['cells'][$i][8];
            $latitudSegundos = $data->sheets[0]['cells'][$i][9];
            $south = $data->sheets[0]['cells'][$i][10];
            if($south == "S" || $south == "s"){
                $south = 1;
            } else {
                $south = 0;
            }
            $longitudGrados = $data->sheets[0]['cells'][$i][11];
            $longitudMinutos = $data->sheets[0]['cells'][$i][12];
            $longitudSegundos = $data->sheets[0]['cells'][$i][13];
            $west = $data->sheets[0]['cells'][$i][14];
            if($west == "W" || $west == "w"){
                $west = 1;
            } else {
                $west = 0;
            }
            $dt = DateTime::createFromFormat('m/d/Y', $data->sheets[0]['cells'][$i][15]);
			$fechaInicio = $dt->format('Y-m-d');
            $meta = $data->sheets[0]['cells'][$i][16];
            $meta = $daoBasica->getIdBasicasByDescripcion('metabeneficiario', $meta)->getId();
            $estado = $data->sheets[0]['cells'][$i][17];
            $estado = $daoBasica->getIdBasicasByDescripcion('estadobeneficiario', $estado)->getId();
            $dda = $data->sheets[0]['cells'][$i][18];
            $dda = $daoBasica->getIdBasicasByDescripcion('ddabeneficiario', $dda)->getId();
            $grupo = $data->sheets[0]['cells'][$i][19];
            $grupo = $daoBasica->getIdBasicasByDescripcion('grupobeneficiario', $grupo)->getId();
            $tipo = $data->sheets[0]['cells'][$i][20];
            $tipo = $daoBasica->getIdBasicasByDescripcion('tipobeneficiario', $tipo)->getId();
			$observaciones = $data->sheets[0]['cells'][$i][21];
            $beneficiario = new CBeneficiario($id, 
                                              $codigoInterventoria, 
                                              $codigoMintic, 
                                              $codigoOperador, 
                                              $nombre, 
                                              $msnm, 
                                              $latitudGrados, 
                                              $latitudMinutos, 
                                              $latitudSegundos, 
                                              $south, 
                                              $longitudGrados, 
                                              $longitudMinutos, 
                                              $longitudSegundos, 
                                              $west, 
                                              $fechaInicio, 
                                              $meta, 
                                              $estado,
											  $observaciones,
                                              $dda, 
                                              $grupo, 
                                              $centroPoblado, 
                                              $tipo);
			$r = $r && $this->insertBeneficiario($beneficiario);
        }
        return $r;
    }

}
