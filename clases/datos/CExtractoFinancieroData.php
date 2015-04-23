<?php
/**
*Redcom Ltda 
*<ul> Desarrollado por
*<li> Camaleon Multimedia ltda <www.camaleon.com.co></li>
*<li> Copyright Redcom</li>
*<li> Redcom Ltda</li>
*</ul>
*/

/**
*Usada para todas las funciones de acceso a datos referente a extractos para el modulo de interventoría
*
* @package  clases
* @subpackage datos
*/
Class CExtractoFinancieroData{
    var $db = null;

	
	function __construct($db){
		$this->db = $db;
	}
	
	
	function insertExtractoInterventoria($extracto){
		$tabla = "extracto_financiero";
		$campos = "cfi_id,efi_mes, efi_anio, efi_observaciones, efi_documento_soporte";
		$valores = "'".$extracto->cuenta."','".$extracto->mes."','".$extracto->anio."','".
                               $extracto->observaciones."','".$extracto->documento_soporte."'";
		$r = $this->db->insertarRegistro($tabla,$campos,$valores);
		return $r;
	}
        
        function getSaldoInicialByFecha($cuenta,$mes,$anio){
            $sql = "select SUM(mov_valor * t.mov_tipo_factor) as saldo
                    from extracto_movimiento_detalle_ut, extracto_movimiento m, extractos_movimiento_tipo t, extracto_financiero 
                    where mov_fecha<'$anio-$mes-01' AND mov_movimiento = m.mov_id AND m.mov_tipo_id = t.mov_tipo_id AND mov_extracto = efi_id "
                    . "AND cfi_id =$cuenta";
//            echo $sql;
            $r = $this->db->ejecutarConsulta($sql);
            if($r){
                $w = mysql_fetch_array($r);
                return $w['saldo'];
            }else{
                return 0;
            }
        }
        
        function getSaldoFinalByFecha($cuenta,$mes,$anio){
            $sql = "select SUM(mov_valor * t.mov_tipo_factor) as saldo
                    from extracto_movimiento_detalle_ut, extracto_movimiento m, extractos_movimiento_tipo t, extracto_financiero 
                    where mov_fecha < DATE_ADD('$anio-$mes-01', INTERVAL 1 MONTH) AND mov_movimiento = m.mov_id AND m.mov_tipo_id = t.mov_tipo_id AND mov_extracto = efi_id "
                    . "AND cfi_id =$cuenta";
//            echo $sql."<br>";
            $r = $this->db->ejecutarConsulta($sql);
            if($r){
                $w = mysql_fetch_array($r);
                return $w['saldo'];
            }else{
                return 0;
            }
        }
                
	function getExtractoById($id){
		$extractos=null;
		$sql = "select e.efi_id, c.cfi_id, c.cfi_numero, c.cfi_nombre, 
                               e.efi_mes, e.efi_anio, e.efi_observaciones, 
                               e.efi_documento_soporte 
                        from extracto_financiero e 
                        inner join cuentas_financiero_ut c on c.cfi_id = e.cfi_id  
                        where efi_id=". $id;
//		echo ("<br>sql:".$sql);
		$r = $this->db->ejecutarConsulta($sql);
		if($r){
			$cont = 0;
			while($w = mysql_fetch_array($r)){
				$extractos['id'] = $w['efi_id'];
                                $extractos['cuenta'] = $w['cfi_id'];
                                $extractos['cuenta_nombre'] = $w['cfi_nombre'];
                                $extractos['cuenta_numero'] = $w['cfi_numero'];
                                $extractos['mes'] = $w['efi_mes'];
				$extractos['anio'] = $w['efi_anio'];
                                $extractos['observaciones'] = $w['efi_observaciones'];
				$extractos['documento_soporte'] = $w['efi_documento_soporte'];
				$cont++;
			}
		}
		return $extractos;
	}
	
	function updateExtracto($extracto){
		$tabla = "extracto_financiero";
		$campos = array('efi_id', 'cfi_id', 'efi_mes',
                    'efi_anio', 'efi_observaciones', 'efi_documento_soporte');
		$montos = array("'".$extracto->id."'","'".$extracto->cuenta."'","'".$extracto->mes."'",
                    "'".$extracto->anio."'","'".$extracto->observaciones."'","'".$extracto->documento_soporte."'");
			
		$condicion = "efi_id = ".$extracto->id;
		$r = $this->db->actualizarRegistro($tabla,$campos,$montos,$condicion);
		return $r;
	}
	
	function deleteExtracto($id){
		$tabla = "extracto_financiero";
		$predicado = "efi_id = ". $id;
		$r = $this->db->borrarRegistro($tabla,$predicado);
		return $r;
	}
	
	function getExtractos($criterio,$orden){
		$extractos=null;
		$sql = "select e.efi_id, c.cfi_id, c.cfi_numero, c.cfi_nombre, 
                               e.efi_mes, e.efi_anio, e.efi_observaciones, 
                               e.efi_documento_soporte 
                        from extracto_financiero e 
                        inner join cuentas_financiero_ut c on c.cfi_id = e.cfi_id  
                        where ". $criterio ." order by ".$orden;
		//echo ("<br>sql:".$sql);
		$r = $this->db->ejecutarConsulta($sql);
		if($r){
			$cont = 0;
			while($w = mysql_fetch_array($r)){
				$extractos[$cont]['id'] = $w['efi_id'];
                                $extractos[$cont]['cuenta'] = $w['cfi_id'];
                                $extractos[$cont]['cuenta_nombre'] = $w['cfi_nombre'];
                                $extractos[$cont]['cuenta_numero'] = $w['cfi_numero'];
                                $extractos[$cont]['mes'] = $w['efi_mes'];
				$extractos[$cont]['anio'] = $w['efi_anio'];
				$extractos[$cont]['saldo_inicial'] = $w['saldo_inicial'];
                                $extractos[$cont]['saldo_final'] = $w['saldo_final'];
                                $extractos[$cont]['observaciones'] = $w['efi_observaciones'];
				$extractos[$cont]['documento_soporte'] = $w['efi_documento_soporte'];
				$cont++;
			}
		}
		return $extractos;
	}
        
        function getCuentas($criterio,$orden){
		$cuentas=null;
		$sql = "select * from cuentas_financiero_ut  
                        where ". $criterio ." order by ".$orden;
		//echo ("<br>sql:".$sql);
		$r = $this->db->ejecutarConsulta($sql);
		if($r){
			$cont = 0;
			while($w = mysql_fetch_array($r)){
                                $cuentas[$cont]['id'] = $w['cfi_id'];
                                $cuentas[$cont]['nombre'] = $w['cfi_nombre']." (".$w['cfi_numero'].")";
                                $cuentas[$cont]['numero'] = $w['cfi_numero'];
                                
				$cont++;
			}
		}
		return $cuentas;
	}
        
        function getDirectorioOperador($id) {
            $tabla = 'operador';
            $campo = 'ope_sigla';
            $predicado = 'ope_id = ' . $id;
            if (!isset($id))
                $predicado = 'ope_id=1';
            $r = $this->db->recuperarCampo($tabla, $campo, $predicado);
            $r = $r . "/";
            if ($r)
                return $r;
            else
                return -1;
        }
        
        function getTipoCuenta($cuenta) {
            $tabla = 'cuentas_financiero_ut';
            $campo = 'cft_id';
            $predicado = 'cfi_id = ' . $cuenta;
            $r = $this->db->recuperarCampo($tabla, $campo, $predicado);
            if ($r)
                return $r;
            else
                return -1;
        }
        
        function getMovimientosByExtracto($extracto){
            $sql="SELECT d.*, m.*, t.* , (d.mov_valor*t.mov_tipo_factor) as valor, mov_fecha as fecha  "
                    . "FROM extracto_movimiento_detalle_ut d , extracto_movimiento m , extractos_movimiento_tipo t "
                    . "WHERE d.mov_extracto = $extracto AND d.mov_movimiento = mov_id AND m.mov_tipo_id = t.mov_tipo_id ORDER BY mov_fecha, mov_detalle_id";
//            echo $sql;
            $r = $this->db->ejecutarConsulta($sql);
            $movimientos = null;
            if($r){
			$cont = 0;
			while($w = mysql_fetch_array($r)){
                                $movimientos[$cont]['id'] = $w['mov_detalle_id'];
                                $movimientos[$cont]['fecha'] = $w['fecha'];
                                $movimientos[$cont]['descripcion'] = $w['mov_descripcion'];
                                $movimientos[$cont]['valor'] = $w['valor'];
                                
				$cont++;
			}
		}
		return $movimientos;
        }
        
        function getMovimientos(){
            $sql = "SELECT * FROM extracto_movimiento order by mov_descripcion";
            $r = $this->db->ejecutarConsulta($sql);
            $movimientos = null;
            if($r){
			$cont = 0;
			while($w = mysql_fetch_array($r)){
                                $movimientos[$cont]['id'] = $w['mov_id'];
                                $movimientos[$cont]['nombre'] = $w['mov_descripcion'];
				$cont++;
			}
		}
		return $movimientos;
        }
        
        function saveMovimiento($extracto,$movimiento,$fecha,$valor){
            $tabla = "extracto_movimiento_detalle_ut";
		$campos = "mov_detalle_id, mov_extracto, mov_movimiento, mov_valor, mov_fecha";
		$valores = "'',".$extracto.",".$movimiento.",".$valor.",'".$fecha."'";
		$r = $this->db->insertarRegistro($tabla,$campos,$valores);
		return $r;
        }
        
        function deleteMovimiento($id){
            $tabla = "extracto_movimiento_detalle_ut";
            $predicado = "mov_detalle_id = ". $id;
            $r = $this->db->borrarRegistro($tabla,$predicado);
            return $r;
        }
        
        function getMovimientosGeneral($criterio){
            $sql="SELECT (d.mov_valor) as valor, mov_detalle_id, mov_fecha as fecha, mov_descripcion, c.cfi_numero "
                    . "FROM extracto_movimiento_detalle_ut d , extracto_movimiento m , extractos_movimiento_tipo t, extracto_financiero e , cuentas_financiero_ut c "
                    . "WHERE d.mov_extracto = efi_id "
                    . "AND d.mov_movimiento = mov_id "
                    . "AND m.mov_tipo_id = t.mov_tipo_id "
                    . "AND e.cfi_id = c.cfi_id "
                    . "AND $criterio "
                    . "ORDER BY fecha, c.cfi_id";
//            echo $sql;
            $r = $this->db->ejecutarConsulta($sql);
            $movimientos = null;
            if($r){
			$cont = 0;
			while($w = mysql_fetch_array($r)){
                                $movimientos[$cont]['id'] = $w['mov_detalle_id'];
                                $movimientos[$cont]['cuenta'] = $w['cfi_numero'];
                                $movimientos[$cont]['fecha'] = $w['fecha'];
                                $movimientos[$cont]['descripcion'] = $w['mov_descripcion'];
                                $movimientos[$cont]['valor'] = $w['valor'];
                                
				$cont++;
			}
		}
		return $movimientos;
        }
        
        function editMovimiento($id,$extracto,$movimiento,$fecha,$valor){

            $tabla = "extracto_movimiento_detalle_ut";
            $campos = array(" mov_extracto"," mov_movimiento"," mov_valor"," mov_fecha");
            $valores = array($extracto,$movimiento,$valor,"'$fecha'");
            $condicion = "mov_detalle_id = ".$id;
            $r = $this->db->actualizarRegistro($tabla,$campos,$valores,$condicion);
            return $r;
        }
        
        function getValorMovimiento($id_mov){
            $sql="SELECT mov_valor AS mov FROM extracto_movimiento_detalle_ut WHERE mov_detalle_id = $id_mov";
            $r=$this->db->ejecutarConsulta($sql);
            if($r){
			while($w = mysql_fetch_array($r)){
                                return $w['mov'];
			}
		}
        }
        
        function getDiaMovimiento($id_mov){
            $sql="SELECT DAY(mov_fecha) AS dia FROM extracto_movimiento_detalle_ut WHERE mov_detalle_id = $id_mov";
            $r=$this->db->ejecutarConsulta($sql);
            if($r){
			while($w = mysql_fetch_array($r)){
                                return $w['dia'];
			}
		}
        }
        
        function getTipoMovimiento($id_mov){
            $sql="SELECT mov_movimiento AS mov FROM extracto_movimiento_detalle_ut WHERE mov_detalle_id = $id_mov";
            $r=$this->db->ejecutarConsulta($sql);
            if($r){
			while($w = mysql_fetch_array($r)){
                                return $w['mov'];
			}
		}
        }
}
?>