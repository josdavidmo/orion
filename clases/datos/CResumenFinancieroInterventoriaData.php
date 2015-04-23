<?php

/**
 * Maneja las peticiones a la base de datos para la carga de las vigencias(ingresos)
 * almacenados en tablas.
 * @author Sergio
 * @author Brian Kings
 * @version 1.0
 * @since 09/08/2014
 */
class CResumenFinancieroInterventoriaData {

    /**
     * Almacena el acceso a la base de datos.
     * @var type 
     */
    var $database = null;

    /**
     * Constructor de la clase.
     * @param \CData $db
     */
    function CResumenFinancieroInterventoriaData($database) {
        $this->database = $database;
    }

    /**
     *  Obtiene las vigencias registradas y las ordena por año y tipo
     * @return type
     */
    function ObtenerVigencias() {
        $sql = "SELECT id_Vigencia,tipo_Vigencia,ano_Vigencia,CONCAT(tipo_Vigencia ,' ', ano_Vigencia)"
                . ",monto_Vigencia from vigenciainterventoria ORDER BY ano_Vigencia, tipo_Vigencia Desc ";
        $ingresos = null;
        $r = $this->database->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {

                $ingresos[$cont]['id'] = $w["id_Vigencia"];
                $ingresos[$cont]['res_vigen'] = $w["CONCAT(tipo_Vigencia ,' ', ano_Vigencia)"];
                $ingresos[$cont]['monto'] = $w["monto_Vigencia"];
                $cont++;
            }
        }
        return $ingresos;
    }

    /**
     * Obtiene el año, la suma total de recursos y la vigencia de un año
     * @return type
     */
    function ObtenerVigenciasGraficaIngresos() {
        $sql = "SELECT ano_Vigencia, sum(monto_Vigencia) as total, monto_Vigencia
                from vigenciainterventoria group by ano_vigencia order BY ano_Vigencia ASC";
        $ingresos = null;
        $r = $this->database->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {

                $ingresos[$cont]['id'] = $w["ano_Vigencia"];
                $ingresos[$cont]['total'] = $w["total"];
                $ingresos[$cont]['monto'] = $w["monto_Vigencia"];
                $cont++;
            }
        }
        return $ingresos;
    }

    /**
     * Obtiene la suma del total de montos de las vigencias
     * @return type
     */
    function obtenerTotalVigencias() {
        $sql = "SELECT SUM(monto_Vigencia) FROM vigenciainterventoria";
        $r = $this->database->ejecutarConsulta($sql);
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                return $w['SUM(monto_Vigencia)'];
            }
        }
    }

    /**
     * Revisa si existen adiciones al anño ingresado
     * @param type $year
     * @return int
     */
    function obtenerTipodeIngreso($year) {
        $sql = "SELECT tipo_Vigencia from vigenciainterventoria where ano_Vigencia=" . $year;
        $tipoingresos = null;
        $r = $this->database->ejecutarConsulta($sql);
        $tem = 0;
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                for ($i = 0; $i < count($w) / 2; $i++) {
                    $tipoingresos[$cont][$i] = $w[$i];
                    if ($w[$i] == "Adición") {
                        $temp = 1;
                    } else {
                        $temp = 0;
                    }
                    $cont++;
                }
            }
        }
        return $temp;
    }

    /**
     * Ingresa una vigencia en la base de datos
     * @param type $id
     * @param type $Fecha
     * @param type $monto
     * @param type $tipo
     * @return string
     */
    function insertarVigencia($id, $Fecha, $monto, $tipo) {
        $tabla = "vigenciainterventoria";
        $campos = "id_Vigencia,ano_Vigencia,monto_Vigencia,tipo_Vigencia";
        $valores = "'" . $id . "','" . $Fecha . "'," . $monto . ",'" . $tipo . "'";
        $r = $this->database->insertarRegistro($tabla, $campos, $valores);
        if ($r == "true") {
            return MENSAJE_AGREGAR_EXITO_INGRESO;
        } else {
            return MENSAJE_AGREGAR_FRACASO_INGRESO;
        }
    }

    /**
     * Elimina una vigencia dependiendo de su id
     * @param type $id
     * @return int
     */
    function eliminarIngresos($id) {
        $tabla = "vigenciainterventoria";
        $predicado = "id_Vigencia=" . $id;
        $e = $this->database->borrarRegistro($tabla, $predicado);
        if ($e == "true") {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Obtiene los datos de una vigencia en especifico
     * @param type $id
     * @return \CIngresos
     */
    function obtenerIngresoPorId($id) {
        $sql = "SELECT * from vigenciainterventoria where id_Vigencia= " . $id;
        $w = $this->database->recuperarResultado($this->database->ejecutarConsulta($sql));
        $objeto = null;
        if ($w) {
            $objeto = new CIngresos($w['id_Vigencia'], $w['ano_Vigencia'], number_format($w['monto_Vigencia'], 2, ",", "."), $w['tipo_Vigencia'], $this);
        }
        return $objeto;
    }

    /**
     * Actualiza el monto de una vigencia
     * @param type $id
     * @param type $monto
     * @return int
     */
    function actualizarIngreso($id, $monto) {

        $tabla = "vigenciainterventoria";
        $campos = array('monto_Vigencia');
        $valores = array("'" . $monto . "'");

        $condicion = "id_Vigencia = " . $id;
        $r = $this->database->actualizarRegistro($tabla, $campos, $valores, $condicion);
        if ($r == "true") {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     *  Obtiene la suma de los montos de un año
     * @param type $year
     * @return type
     */
    function obtenerValoresVigencia($year) {
        $sql = "SELECT CONCAT('Vigencia ',ano_Vigencia), SUM( monto_Vigencia) FROM vigenciainterventoria WHERE ano_Vigencia =" . $year;
        $r = $this->database->recuperarResultado($this->database->ejecutarConsulta($sql));
        if ($r) {
            return $r;
        } else {
            return -1;
        }
    }

    /*
      function obtenerValoresOrdenesdePagoByActividad($year, $tipo) {
      $sql = "SELECT SUM( valor_total ) FROM ordenesdepago WHERE (Id_Estado_Orden =1
      OR Id_Estado_Orden =4 ) AND YEAR( Fecha_Orden_Pago ) = $year "
      . "AND Id_Tipo_Actividad $tipo";
      $r = $this->database->recuperarResultado($this->database->ejecutarConsulta($sql));
      if ($r) {
      return $r;
      } else {
      return -1;
      }
      }

      function ObtenerValoresOrdenesdePago($year) {
      $sql = "SELECT SUM( valor_total ) FROM ordenesdepago WHERE (Id_Estado_Orden =1
      OR Id_Estado_Orden =4 ) AND YEAR( Fecha_Orden_Pago ) =" . $year;
      $r = $this->database->recuperarResultado($this->database->ejecutarConsulta($sql));
      if ($r) {
      return $r;
      } else {
      return -1;
      }
      }

      function ObtenerValoresUtilidades($year) {
      $sql = "SELECT SUM( uti_aprobada ),Count(uti_aprobada) FROM utilidades WHERE ano_vigencia =" . $year;
      $r = $this->database->recuperarResultado($this->database->ejecutarConsulta($sql));
      if ($r) {
      return $r;
      } else {
      return -1;
      }
      }

      function ObtenerValoresUtilidadesByCriterio($year, $criterio) {
      $sql = "SELECT SUM( uti_aprobada ),Count(uti_aprobada) FROM utilidades WHERE ano_vigencia =$year AND $criterio";
      $r = $this->database->recuperarResultado($this->database->ejecutarConsulta($sql));
      if ($r) {
      return $r;
      } else {
      return -1;
      }
      }

      function ObtenerOrdenesInversiondelAnticipo() {
      $sql = "SELECT SUM( valor_total )FROM ordenesdepago WHERE Id_Tipo_Actividad =2 AND (Id_Estado_Orden =1
      OR Id_Estado_Orden =4 )";
      $r = $this->database->recuperarResultado($this->database->ejecutarConsulta($sql));
      if ($r) {
      return $r;
      } else {
      return -1;
      }
      }
     */

    /**
     * Obtenemos los años de las vigencias
     * @return type
     */
    function ObtenerYears() {
        $Years = null;
        $sql = "SELECT ano_Vigencia FROM vigenciainterventoria  GROUP BY  ano_Vigencia";
        $r = $this->database->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {

                $Years[$cont]['ano_Vigencia'] = $w["ano_Vigencia"];
                $cont++;
            }
        }
        return $Years;
    }
    
    function ObtenerYear($ano) {
        $Years = null;
        $sql = "SELECT ano_Vigencia FROM vigenciainterventoria  WHERE ano_Vigencia=$ano";
        $r = $this->database->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {

                $Years[$cont]['ano_Vigencia'] = $w["ano_Vigencia"];
                $cont++;
            }
        }
        return $Years;
    }

    /**
     * Obtenemos los registros de actividadesPIA
     * @return type
     */
    function ObtenerActividaedesInversiondelAnticipo() {
        $actividades = null;
        $sql = "SELECT * from actividadpia";
        $r = $this->database->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {

                $actividades[$cont]['Id_Actividad'] = $w["act_id"];
                $actividades[$cont]['Descripcion_Actividad'] = $w["act_descripcion"];
                $actividades[$cont]['Monto_Actividad'] = $w["act_monto"];
                $cont++;
            }
        }
        return $actividades;
    }

    /**
     * Obtiene la suma del valor de la factura menos la amortizacion de los informes financieros
     * @param type $year
     * @return type
     */
    function obtenerInformeFinanciero($year) {
        $sql = "SELECT SUM(ifi_valor_factura - ifi_amortizacion) FROM informe_financiero WHERE ifi_vigencia = $year";
        $r = $this->database->recuperarResultado($this->database->ejecutarConsulta($sql));
        if ($r) {
            return $r;
        } else {
            return -1;
        }
    }

    /**
     * Obtiene la suma de los registros de inversion de un actividad
     * @param type $id_actividad
     * @return type
     */
    function obtenerValoresActividadesRegistroInversion($id_actividad) {

        $sql = "SELECT SUM(rin_valor) from registro_inversion WHERE act_id = $id_actividad";
        $r = $this->database->recuperarResultado($this->database->ejecutarConsulta($sql));
        if ($r) {
            return $r;
        } else {
            return -1;
        }
    }

    /**
     * Obtiene el total de gastos en el registro de inversion
     * @return type
     */
    function obtenerTotalActividades() {

        $sql = "SELECT SUM(rin_valor) from registro_inversion ";
        $r = $this->database->recuperarResultado($this->database->ejecutarConsulta($sql));
        if ($r) {
            return $r;
        } else {
            return -1;
        }
    }

}

?>
