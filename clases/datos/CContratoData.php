<?php

/**
 * Clase Contrato Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2015.02.07
 * @copyright SERTIC SAS
 */
class CContratoData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param \CData $db
     */
    function CContratoData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene todos los contratos almacenados en la tabla contrato.
     * @param type $idContrato
     * @return type
     */
    public function getContratoById($idContrato) {
        $contrato = null;
        $sql = "SELECT * FROM contrato WHERE idContrato = " . $idContrato;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $contrato = new CContrato($w['idContrato'], 
                                      $w['numero'], 
                                      $w['objeto'], 
                                      number_format($w['valor'], 2, ',', '.'), 
                                      $w['plazo'], 
                                      $w['fechaInicio'], 
                                      $w['fechaFin'], 
                                      $w['soporte'], 
                                      $w['idMoneda']);
        }
        return $contrato;
    }

    /**
     * Obtiene todos los contratos almacenados en la tabla contrato.
     * @param type $criterio
     * @return type
     */
    public function getContratos($criterio = "1") {
        $contratos = null;
        $sql = "SELECT c.*, m.Descripcion_Moneda as moneda, "
		. "(SELECT descripcion FROM otrosi o WHERE o.idContrato = c.idContrato AND o.idOtroSi = (SELECT MAX(idOtroSi) FROM otrosi)) as objetoContrato, "
                . "GREATEST(COALESCE((SELECT MAX(fecha) FROM otrosi o WHERE o.idContrato = c.idContrato), 0), c.fechaFin) as fechaContrato, "
                . "COALESCE((SELECT SUM(valor) FROM otrosi o WHERE o.idContrato = c.idContrato), 0) + c.valor as valorContrato, "
                . "(SELECT SUM(valor_total) FROM ordenesdepago WHERE contrato_idContrato = c.idContrato) as sumaOrdenes, "
                . "(SELECT COUNT(valor_total) FROM ordenesdepago WHERE contrato_idContrato = c.idContrato) as numeroOrdenes "
                . "FROM contrato c "
                . "INNER JOIN monedas m ON m.Id_Moneda = c.idMoneda "
                . "WHERE " . $criterio;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $contratos[$cont]['idContrato'] = $w['idContrato'];
                $contratos[$cont]['numero'] = $w['numero'];
				$contratos[$cont]['objeto'] = $w['objeto'];
				if($w['objetoContrato'] != NULL){
					$contratos[$cont]['objeto'] = $w['objetoContrato'];
				}
                $contratos[$cont]['valor'] = number_format($w['valorContrato'], 2, ',', '.');
                $contratos[$cont]['sumaOrdenes'] = number_format($w['sumaOrdenes'], 2, ',', '.');
                if($w['valor'] < $w['sumaOrdenes']){
                    $contratos[$cont]['sumaOrdenes'] = "<div style='color: red'>".number_format($w['sumaOrdenes'], 0, ',', '.')."</div>";
                }
                $contratos[$cont]['numeroOrdenes'] = $w['numeroOrdenes'];
                $contratos[$cont]['plazo'] = $w['plazo'];
                $contratos[$cont]['fechaInicio'] = $w['fechaInicio'];
                $contratos[$cont]['fechaFin'] = $w['fechaContrato'];
                $contratos[$cont]['soporte'] = "<a href='" . RUTA_DOCUMENTOS . "/soporteContrato/" . $w['soporte'] . "' >" . $w['soporte'] . "</a>";
                $contratos[$cont]['moneda'] = $w['moneda'];
                $contratos[$cont]['estado'] = '<img src=./templates/img/ico/verde.gif> Liquidado';
                if ($w['valor'] != $w['sumaOrdenes']) {
                    date_default_timezone_set('America/Bogota');
                    $fechaActual = date("Y-m-d");
                    if ($fechaActual < $w['fechaContrato']) {
                        $contratos[$cont]['estado'] = '<img src=./templates/img/ico/amarillo.gif> Vigente';
                    } else {
                        $contratos[$cont]['estado'] = '<img src=./templates/img/ico/rojo.gif> Vencido';
                    }
                }
                $cont++;
            }
        }
        return $contratos;
    }

    /**
     * Inserta un contrato en la base de datos
     * @param \CContrato $contrato
     * @return type
     */
    public function insertContrato($contrato) {
        $tabla = "contrato";
        $campos = "numero,objeto,valor,plazo,fechaInicio,fechaFin,"
                . "soporte,idMoneda";
        $valores = "'" . $contrato->getNumero() . "','"
                . $contrato->getObjeto() . "','"
                . $contrato->getValor() . "','"
                . $contrato->getPlazo() . "','"
                . $contrato->getFechaInicio() . "','"
                . $contrato->getFechaFin() . "','"
                . $contrato->getSoporte()['name'] . "','"
                . $contrato->getMoneda() . "'";
        $ruta = "soporteContrato";
        $this->db->guardarArchivo($contrato->getSoporte(), $ruta);
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Elimina un elemento de la base de datos.
     * @param type $idContrato
     * @return type
     */
    public function deleteContratoById($idContrato) {
        $tabla = "contrato";
        $predicado = "idContrato = " . $idContrato;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Actualiza un contrato de la base de datos.
     * @param \CContrato $contrato
     * @return type
     */
    public function updateContrato($contrato) {
        $tabla = "contrato";
        $campos = array("numero", "objeto", "valor", "plazo",
            "fechaInicio", "fechaFin", "idMoneda");
        $valores = array("'" . $contrato->getNumero() . "'",
            "'" . $contrato->getObjeto() . "'",
            "'" . $contrato->getValor() . "'",
            "'" . $contrato->getPlazo() . "'",
            "'" . $contrato->getFechaInicio() . "'",
            "'" . $contrato->getFechaFin() . "'",
            "'" . $contrato->getMoneda() . "'");
        $ruta = "soporteContrato";
        if ($contrato->getSoporte()['name'] != NULL && $this->db->guardarArchivo($contrato->getSoporte(), $ruta)) {
            $campos[count($campos)] = "soporte";
            $valores[count($valores)] = "'" . $contrato->getSoporte()['name'] . "'";
        }
        $condicion = "idContrato = " . $contrato->getIdContrato();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

}
