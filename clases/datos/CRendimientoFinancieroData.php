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
*Usada para todas las funciones de acceso a datos referente a extractos para el modulo financiero
*
* @package  clases
* @subpackage datos
*/
Class CRendimientoFinancieroData{
    var $db = null;
	
	function __construct($db){
		$this->db = $db;
	}
	
	
	function insertRendimiento($rendimiento){
		$tabla = "rendimiento_financiero";
		$campos = "cfi_id, rfi_mes, rfi_anio, 
                    rfi_rendimiento_financiero, rfi_descuentos, rfi_rendimiento_consignado, 
                    rfi_fecha_consignacion, rfi_comprobante_consignacion, rfi_comprobante_emision,
                    rfi_observaciones";
		$valores = "'".$rendimiento->cuenta."','".$rendimiento->mes."','".$rendimiento->anio."','".
                    $rendimiento->rendimiento_financiero."','".$rendimiento->descuentos."','".$rendimiento->rendimiento_consignado."','". 
                    $rendimiento->fecha_consignacion."','". $rendimiento->comprobante_consignacion."','".$rendimiento->comprobante_emision."','".
                    $rendimiento->observaciones."'";
		$r = $this->db->insertarRegistro($tabla,$campos,$valores);
		return $r;
	}
        
        function getSaldoFinalByFecha($cuenta,$mes,$anio){
            $sql = "select efi_saldo_final as saldo
                    from rendimiento_financiero 
                    where cfi_id = ". $cuenta ." and efi_mes=". $mes . " and efi_anio = ". $anio;
            //echo $sql;
            $r = $this->db->ejecutarConsulta($sql);
            if($r){
                $w = mysql_fetch_array($r);
                return $w['saldo'];
            }else{
                return 0;
            }
        }
        
        function getSaldoConsignadoByFecha($cuenta,$mes,$anio){
            $sql = "select sum(rfi_rendimiento_consignado) as saldo
                    from rendimiento_financiero
                    where cfi_id = ". $cuenta ." and rfi_mes<=". $mes . " and rfi_anio = ". $anio;
            //echo $sql;
            $r = $this->db->ejecutarConsulta($sql);
            if($r){
                $w = mysql_fetch_array($r);
                return $w['saldo'];
            }else{
                return 0;
            }
        }
                
	function getRendimientoInterventoriaById($id){
		$rendimientos=null;
		$sql = "select r.rfi_id, r.cfi_id, c.cfi_nombre,
                                c.cfi_numero, r.rfi_mes, r.rfi_anio,
                                r.rfi_rendimiento_financiero, r.rfi_descuentos, r.rfi_rendimiento_consignado,
                                r.rfi_fecha_consignacion, r.rfi_comprobante_consignacion, r.rfi_comprobante_emision,
                                r.rfi_observaciones 
                        from rendimiento_financiero r 
                        inner join cuentas_financiero_ut c on c.cfi_id = r.cfi_id
                        where rfi_id=". $id;
//		echo ("<br>sql:".$sql);
		$r = $this->db->ejecutarConsulta($sql);
		if($r){
			$cont = 0;
			while($w = mysql_fetch_array($r)){
				$rendimientos['id'] = $w['rfi_id'];
                                $rendimientos['cuenta'] = $w['cfi_id'];
                                $rendimientos['cuenta_nombre'] = $w['cfi_nombre'];
                                $rendimientos['cuenta_numero'] = $w['cfi_numero'];
                                $rendimientos['mes'] = $w['rfi_mes'];
				$rendimientos['anio'] = $w['rfi_anio'];
				$rendimientos['rendimiento_financiero'] = $w['rfi_rendimiento_financiero'];
                                $rendimientos['descuentos'] = $w['rfi_descuentos'];
                                $rendimientos['rendimiento_consignado'] = $w['rfi_rendimiento_consignado'];
                                $rendimientos['fecha_consignacion'] = $w['rfi_fecha_consignacion'];
				$rendimientos['comprobante_consignacion'] = $w['rfi_comprobante_consignacion'];
                                $rendimientos['comprobante_emision'] = $w['rfi_comprobante_emision'];
                                $rendimientos['nombre_consignacion'] = $w['rfi_nombre_consignacion'];
                                $rendimientos['nombre_emision'] = $w['rfi_nombre_emision'];
                                $rendimientos['observaciones'] = $w['rfi_observaciones'];
				$cont++;
			}
		}
		return $rendimientos;
	}
	
	function updateRendimiento($rendimiento){
		$tabla = "rendimiento_financiero";
		$campos = array('rfi_id', 'cfi_id', 'rfi_mes', 'rfi_anio', 
                    'rfi_rendimiento_financiero', 'rfi_descuentos', 'rfi_rendimiento_consignado', 
                    'rfi_fecha_consignacion', 'rfi_comprobante_consignacion', 'rfi_comprobante_emision',
                    'rfi_nombre_consignacion','rfi_nombre_emision','rfi_observaciones');
		$montos = array("'".$rendimiento->id."'","'".$rendimiento->cuenta."'","'".$rendimiento->mes."'","'".$rendimiento->anio."'","'".
                    $rendimiento->rendimiento_financiero."'","'".$rendimiento->descuentos."'","'".$rendimiento->rendimiento_consignado."'","'". 
                    $rendimiento->fecha_consignacion."'","'". $rendimiento->comprobante_consignacion."'","'".$rendimiento->comprobante_emision."'",
                    "'".$rendimiento->nombre_consignacion."'","'".$rendimiento->nombre_emision."'","'".$rendimiento->observaciones."'");
			
		$condicion = "rfi_id = ".$rendimiento->id;
		$r = $this->db->actualizarRegistro($tabla,$campos,$montos,$condicion);
		return $r;
	}
	
	function deleteRendimiento($id){
		$tabla = "rendimiento_financiero";
		$predicado = "rfi_id = ". $id;
		$r = $this->db->borrarRegistro($tabla,$predicado);
		return $r;
	}
	
	function getRendimientosInterventoria($criterio,$orden){
		$rendimientos=null;
		$sql = "select r.rfi_id, r.cfi_id, c.cfi_nombre,
                                c.cfi_numero, r.rfi_mes, r.rfi_anio,
                                r.rfi_rendimiento_financiero, r.rfi_descuentos, r.rfi_rendimiento_consignado,
                                r.rfi_fecha_consignacion, r.rfi_comprobante_consignacion, r.rfi_comprobante_emision,rfi_nombre_consignacion,rfi_nombre_emision"
                        .", r.rfi_observaciones 
                        from rendimiento_financiero r 
                        inner join cuentas_financiero_ut c on c.cfi_id = r.cfi_id"
                        ." where ". $criterio ." order by ".$orden;
//		echo ("<br>sql:".$sql);
		$r = $this->db->ejecutarConsulta($sql);
		if($r){
			$cont = 0;
			while($w = mysql_fetch_array($r)){
				$rendimientos[$cont]['id'] = $w['rfi_id'];
                                $rendimientos[$cont]['cuenta'] = $w['cfi_id'];
                                $rendimientos[$cont]['cuenta_nombre'] = $w['cfi_nombre'];
                                $rendimientos[$cont]['cuenta_numero'] = $w['cfi_numero'];
                                $rendimientos[$cont]['mes'] = $w['rfi_mes'];
				$rendimientos[$cont]['anio'] = $w['rfi_anio'];
				$rendimientos[$cont]['rendimiento_financiero'] = $w['rfi_rendimiento_financiero'];
                                $rendimientos[$cont]['descuentos'] = $w['rfi_descuentos'];
                                $rendimientos[$cont]['rendimiento_consignado'] = $w['rfi_rendimiento_consignado'];
                                $rendimientos[$cont]['fecha_consignacion'] = $w['rfi_fecha_consignacion'];
				$rendimientos[$cont]['comprobante_consignacion'] = $w['rfi_comprobante_consignacion'];
                                $rendimientos[$cont]['comprobante_emision'] = $w['rfi_comprobante_emision'];
                                $rendimientos[$cont]['nombre_consignacion'] = $w['rfi_nombre_consignacion'];
                                $rendimientos[$cont]['nombre_emision'] = $w['rfi_nombre_emision'];
                                $rendimientos[$cont]['observaciones'] = $w['rfi_observaciones'];
				$cont++;
			}
		}
		return $rendimientos;
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
                                $cuentas[$cont]['nombre'] = $w['cfi_nombre'];
                                $cuentas[$cont]['numero'] = $w['cfi_numero'];
                                
				$cont++;
			}
		}
		return $cuentas;
	}
        
        function getEstados($criterio,$orden){
		$estados=null;
		$sql = "select * from estado_rendimiento_financiero  
                        where ". $criterio ." order by ".$orden;
		//echo ("<br>sql:".$sql);
		$r = $this->db->ejecutarConsulta($sql);
		if($r){
			$cont = 0;
			while($w = mysql_fetch_array($r)){
                                $estados[$cont]['id'] = $w['erf_id'];
                                $estados[$cont]['nombre'] = $w['erf_nombre'];
                                
				$cont++;
			}
		}
		return $estados;
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
        
        function getIntereses($id,$anio,$mes){
            $sql="SELECT SUM(d.mov_valor) as valor "
                    . "FROM extracto_movimiento_detalle_ut d , extracto_movimiento m , extractos_movimiento_tipo t, extracto_financiero e , cuentas_financiero_ut c, rendimiento_financiero i "
                    . "WHERE i.cfi_id=e.cfi_id "
                    . "AND i.rfi_id=$id "
                    . "AND d.mov_extracto = efi_id "
                    . "AND d.mov_movimiento = mov_id "
                    . "AND m.mov_tipo_id = t.mov_tipo_id "
                    . "AND e.cfi_id = c.cfi_id "
                    . "AND year(d.mov_fecha)= $anio AND month(d.mov_fecha)=$mes  "
                    . "AND t.mov_tipo_id=3";
//            echo $sql;
            $r = $this->db->ejecutarConsulta($sql);
            $movimientos = null;
            if($r){
			$cont = 0;
			while($w = mysql_fetch_array($r)){
                                return $w['valor'];
			}
		}
        }
			
}
?>