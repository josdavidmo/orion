<?php

/**
 * Clase Productos Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.09.12
 * @copyright SERTIC SAS
 */
class CRegistroProductosData {

    /** Manejador de la base de datos. */
    var $db = null;

    /** Maneja las peticiones a tablas basicas. */
    var $daoBasica = null;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    function CRegistroProductosData($db) {
        $this->db = $db;
        $this->daoBasica = new CBasicaData($db);
    }
    
    /**
     * Obtiene los registros de los productos por orden de pago.
     * @param type $criterio
     * @return type
     */
    public function getRegistroProductos($criterio = "1") {
        $registroProducto = null;
        $sql = "SELECT rp.idRegistroProducto, rp.descripcion, rp.valorUnitario, "
                . "rp.cantidad, rp.servicio, f.descripcionFamilia, "
                . "op.Numero_Orden_Pago "
                . "FROM registroproductos rp "
                . "INNER JOIN familias f ON rp.idFamilia = f.idFamilia "
                . "INNER JOIN ordenesdepago op ON op.Id_Orden_Pago = rp.Id_Orden_Pago "
                . "WHERE $criterio";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $registroProducto[$cont]['idRegistroProducto'] = $w['idRegistroProducto'];
                $registroProducto[$cont]['descripcion'] = $w['descripcion'];
                $registroProducto[$cont]['valorUnitario'] = $w['valorUnitario'];
                $registroProducto[$cont]['cantidad'] = $w['cantidad'];
                $registroProducto[$cont]['servicio'] = 'Servicio';
                if ($w['servicio'] == '0') {
                    $registroProducto[$cont]['servicio'] = 'Bien';
                }
                $registroProducto[$cont]['descripcionFamilia'] = $w['descripcionFamilia'];
                $registroProducto[$cont]['Numero_Orden_Pago'] = $w['Numero_Orden_Pago'];
                $registroProducto[$cont]['valorTotal'] = $w['valorUnitario'] * $w['cantidad'];
                $cont++;
            }
        }
        return $registroProducto;
    }

    /**
     * Obtiene los registros de los productos por orden de pago.
     * @param type $idOrdenPago
     * @return type
     */
    public function getRegistroProductosByOrdenPago($idOrdenPago) {
        $registroProducto = null;
        $sql = "SELECT rp.idRegistroProducto, rp.descripcion, rp.valorUnitario, "
                . "rp.cantidad, rp.servicio, f.descripcionFamilia, "
                . "op.Numero_Orden_Pago "
                . "FROM registroproductos rp, familias f, ordenesdepago op "
                . "WHERE rp.idFamilia = f.idFamilia AND "
                . "rp.Id_Orden_Pago = op.Id_Orden_Pago AND "
                . "rp.Id_Orden_Pago = " . $idOrdenPago;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $registroProducto[$cont]['idRegistroProducto'] = $w['idRegistroProducto'];
                $registroProducto[$cont]['descripcion'] = $w['descripcion'];
                $registroProducto[$cont]['valorUnitario'] = $w['valorUnitario'];
                $registroProducto[$cont]['cantidad'] = $w['cantidad'];
                $registroProducto[$cont]['servicio'] = 'Servicio';
                if ($w['servicio'] == '0') {
                    $registroProducto[$cont]['servicio'] = 'Bien';
                }
                $registroProducto[$cont]['descripcionFamilia'] = $w['descripcionFamilia'];
                $registroProducto[$cont]['Numero_Orden_Pago'] = $w['Numero_Orden_Pago'];
                $registroProducto[$cont]['valorTotal'] = $w['valorUnitario'] * $w['cantidad'];
                $cont++;
            }
        }
        return $registroProducto;
    }

    /**
     * Obtiene el Registro Producto al que pertenece un producto.
     * @param type $idProducto
     * @return type
     */
    public function getRegistroProductoByIdProducto($idProducto) {
        $sql = "SELECT rp.idRegistroProducto "
                . "FROM productos p, registroproductos rp "
                . "WHERE p.idProducto = " . $idProducto . " AND "
                . "rp.idRegistroProducto = p.idRegistroProducto";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                $idRegistroProducto = $w['idRegistroProducto'];
                return $this->getRegistroProductoById($idRegistroProducto);
            }
        }
        return null;
    }

    /**
     * Obtiene un producto dado el id del mismo.
     * @param type $idProducto
     * @return \CProductos
     */
    public function getProductoById($idProducto) {
        $producto = null;
        $sql = "SELECT * FROM productos p "
                . "WHERE p.idProducto = " . $idProducto;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $producto = new CProductos($w['idProducto'], $w['serial'], $w['idRegistroProducto'], $w['descripcion'], $w['idEstadoProducto'], $w['idSubgrupo'], $w['idEje']);
        }
        return $producto;
    }

    /**
     * Obtiene los productos almacenados en la base de datos.
     * @param type $criterio
     * @return type
     */
    public function getProductos($criterio = "1") {
        $producto = null;
        $sql = "SELECT p.*, e.descripcionEstadoProducto as estado,"
                . "rp.descripcion as registroProducto, f.descripcionFamilia "
                . "FROM productos p "
                . "INNER JOIN registroProductos rp ON rp.idRegistroProducto = p.idRegistroProducto "
                . "INNER JOIN familias f ON rp.idFamilia = f.idFamilia "
                . "LEFT JOIN estadoproducto e ON e.idEstadoProducto = p.idEstadoProducto "
                . "WHERE $criterio";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $producto[$cont]['idProducto'] = $w['idProducto'];
                $producto[$cont]['serial'] = $w['serial'];
                $producto[$cont]['familia'] = $w['descripcionFamilia'];
                $producto[$cont]['registroproducto'] = $w['registroProducto'];
                $producto[$cont]['descripcion'] = $w['descripcion'];
                $producto[$cont]['estado'] = $w['estado'];
                $producto[$cont]['fechaEnvio'] = $w['fechaEnvio'];
                $cont++;
            }
        }
        return $producto;
    }

    /**
     * Obtiene un registro producto dado el id del mismo.
     * @param type $idRegistroProducto
     * @return \CRegistroProductos
     */
    public function getRegistroProductoById($idRegistroProducto) {
        $registroProducto = null;
        $sql = "SELECT * FROM registroproductos WHERE idRegistroProducto = " . $idRegistroProducto;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $registroProducto = new CRegistroProductos($w['idRegistroProducto'], $w['descripcion'], $w['valorUnitario'], $w['servicio'], $w['cantidad'], $w['idFamilia'], $w['Id_Orden_Pago']);
        }
        return $registroProducto;
    }

    /**
     * Inserta un nuevo producto en la base de datos.
     * @param \CRegistroProductos $registroProducto
     */
    public function insertRegistroProducto($registroProducto) {
        $tabla = "registroproductos";
        $campos = "descripcion,valorUnitario,cantidad,"
                . "servicio,idFamilia,Id_Orden_Pago";
        $valores = "'" . $registroProducto->getDescripcion() . "','"
                . $registroProducto->getValorUnitario() . "','"
                . $registroProducto->getCantidad() . "','"
                . $registroProducto->getServicio() . "','"
                . $registroProducto->getFamilia()->getId() . "','"
                . $registroProducto->getOrdenPago()->getId_ordenedepago() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Inserta el historial de un producto en la base de datos.
     * @param \CHistorialProducto $historialProducto
     * @return type
     */
    public function insertHistorialProducto($historialProducto) {
        if ($historialProducto->getBeneficiario()->getIdBeneficiario() == null) {
            $this->updateHistorialProducto($historialProducto);
        }
        $tabla = "historialproductos";
        $campos = "idProducto,idBodega,idBeneficiario,fechaEnvio";
        $valores = "'" . $historialProducto->getProducto()->getIdProducto() . "','"
                . $historialProducto->getBodega()->getIdBodega() . "','"
                . $historialProducto->getBeneficiario()->getIdBeneficiario() . "','"
                . $historialProducto->getFechaEnvio() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Actualiza la ubicacion de un producto dejando verdadero si el producto se
     * encuentra en esa posicion y falso en caso de que no se encuentra.
     * @param \CHistorialProducto $historialProducto
     * @return type
     */
    public function updateHistorialProducto($historialProducto) {
        $tabla = "historialproductos";
        $campos = array('estado');
        $valores = array('0');
        $condicion = "idProducto = " . $historialProducto->getProducto()->getIdProducto();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Actualiza un producto de la base de datos.
     * @param CProductos $producto
     * @return type
     */
    public function updateProducto($producto) {
        $tabla = "productos";
        $campos = array('serial', 'descripcion',
            'idEstadoProducto', 'fechaEnvio');
        $valores = array("'" . $producto->getSerial() . "'",
            "'" . $producto->getDescripcion() . "'",
            "'" . $producto->getEstadoProducto()->getId() . "'",
            "'" . $producto->getFechaEnvio() . "'");
        $condicion = "idProducto = " . $producto->getIdProducto();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Actualiza un registro producto de la base de datos.
     * @param \CRegistroProductos $registroProducto
     * @return type
     */
    public function updateRegistroProducto($registroProducto) {
        $tabla = "registroproductos";
        $campos = array('descripcion', 'valorUnitario', 'cantidad', 'servicio', 'idFamilia');
        $valores = array("'" . $registroProducto->getDescripcion() . "'",
            "'" . $registroProducto->getValorUnitario() . "'",
            "'" . $registroProducto->getCantidad() . "'",
            "'" . $registroProducto->getServicio() . "'",
            "'" . $registroProducto->getFamilia()->getId() . "'");
        $condicion = "idRegistroProducto = " . $registroProducto->getIdRegistroProductos();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Elimina un registro producto de la base de datos.
     * @param type $idRegistroProducto
     * @return type
     */
    public function deleteRegistroProducto($idRegistroProducto) {
        $tabla = "registroproductos";
        $predicado = "idRegistroProducto = " . $idRegistroProducto;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }
    
    /**
     * Actualiza todos los productos segun el archivo ingresado.
     * @param type $file
     * @return type
     */
    public function cargaMasiva($file){
        require_once './clases/Excel/oleread.inc';
        $daoBasica = new CBasicaData($this->db);
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('UTF-8');
        $data->read($file['tmp_name']);
        error_reporting(E_ALL ^ E_NOTICE);
        $r = TRUE;
        for ($i = 3; $i <= $data->sheets[0]['numRows']; $i++) {
            $idProducto = $data->sheets[0]['cells'][$i][1];
            $serial = $data->sheets[0]['cells'][$i][2];
            $descripcion = utf8_encode($data->sheets[0]['cells'][$i][4]);
            $estado = $daoBasica->getIdBasicasByDescripcion('estadoproducto', utf8_encode($data->sheets[0]['cells'][$i][5]));
			$fecha = $data->sheets[0]['cells'][$i][6];
			$fechaEnvio = NULL;
			if($fecha != NULL){
				$fecha = explode("/", $data->sheets[0]['cells'][$i][6]);
				$fechaEnvio = "$fecha[2]-$fecha[1]-$fecha[0]";
			}
            $producto = new CProductos($idProducto, $serial, null, $descripcion, $estado, $fechaEnvio);
            $r = $r && $this->updateProducto($producto);
        }
        return $r;
    }

    /**
     * Obtiene el historial de un producto.
     * @param type $idProducto
     * @return type
     */
    public function getHistorialProducto($idProducto) {
        $historialProductos = null;
        $sql = "SELECT * FROM historialproductos WHERE idProducto = " . $idProducto;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $historialProductos[$cont]['idProducto'] = $w['idRegistroProducto'];
                $daoBeneficiario = new CBeneficiarioData($this->db);
                $beneficiario = $daoBeneficiario->getBeneficiarioById($w['idBeneficiario']);
                $daoBodega = new CBodegaData($this->db);
                $bodega = $daoBodega->getBodegaById($w['idBodega']);
                if ($beneficiario != null) {
                    $centroPoblado = $daoBeneficiario->getCentrosPobladosById($beneficiario->getCentroPoblado());
                    $tipoBeneficiario = $this->daoBasica->getBasicasById("tipobeneficiario", "idTipoBeneficiario = " . $beneficiario->getTipo());
                    $historialProductos[$cont]['idBodega'] = $centroPoblado->getNombre() . " - " . $tipoBeneficiario->getDescripcion() . " - " . $beneficiario->getNombre();
                } else {
                    $historialProductos[$cont]['idBodega'] = $bodega->getNombre();
                }
                $historialProductos[$cont]['fechaEnvio'] = $w['fechaEnvio'];
                $cont++;
            }
        }
        return $historialProductos;
    }

}
