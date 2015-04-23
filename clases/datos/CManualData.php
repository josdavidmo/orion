<?php
/**
*Gestion Interventoria - Fenix
*
*<ul>
*<li> Redcom Ltda <www.redcom.com.co></li>
*<li> Proyecto RUNT</li>
*</ul>
*/

/**
* Clase ManualData
* Usada para la definicion de todas las funciones propias del objeto MANUAL

* @package  clases
* @subpackage datos
* @author Redcom Ltda
* @version 2013.01.00
* @copyright Ministerio de Transporte
*/
Class CManualData{
    var $db = null;
	
	function CManualData($db){
		$this->db = $db;
	}
	
	function getManuales($criterio,$orden,$dirOperador){
		$manuales = null;
		$sql = "select * from manual where ". $criterio ." order by ".$orden;
		$r = $this->db->ejecutarConsulta($sql);
		if($r){
			$cont = 0;
			while($w = mysql_fetch_array($r)){
				$manuales[$cont]['id'] = $w['man_id'];
				$manuales[$cont]['nombre'] = $w['man_nombre'];
				$manuales[$cont]['tipo'] = $w['man_tipo'];
				$manuales[$cont]['archivo'] ="<a href='././soportes/".$dirOperador."Manuales/".$w['man_archivo']."' target='_blank'>{$w['man_archivo']}</a>";
				$cont++;
			}
		}
		return $manuales;
	}
	
	function insertManual($nombre,$archivo,$tipo){
		$tabla = "manual";
		$campos = "man_nombre,man_archivo,man_tipo";
		$valores = "'".$nombre."','".$archivo."','".$tipo."'";
		$r = $this->db->insertarRegistro($tabla,$campos,$valores);
		return $r;
	}

	function deleteManual($id){
		$tabla = "manual";
		$predicado = "man_id = ". $id;
		$r = $this->db->borrarRegistro($tabla,$predicado);
		return $r;
	}
	
	function updateManual($id,$nombre,$tipo){
		$tabla = "manual";
		$campos = array('man_nombre','man_tipo');
		$valores = array("'".$nombre."'","'".$tipo."'");
		$condicion = "man_id = ".$id;
		$r = $this->db->actualizarRegistro($tabla,$campos,$valores,$condicion);
		return $r;
	}
	
	function updateManualArchivo($id,$nombre,$archivo,$tipo){
		$tabla = "manual";
		$campos = array('man_nombre','man_archivo');
		$valores = array("'".$nombre."'","'".$archivo."'","'".$tipo."'");
		$condicion = "man_id = ".$id;
		$r = $this->db->actualizarRegistro($tabla,$campos,$valores,$condicion);
		return $r;
	}
	
	function getManualById($id){
		$manual = null;
		$sql = "select * 
				from manual 
				where man_id = ". $id;
		$r = $this->db->recuperarResultado($this->db->ejecutarConsulta($sql));
		if($r){
			$manual["id"] = $r["man_id"]; 
			$manual["nombre"] = $r["man_nombre"];
			$manual["archivo"] = $r["man_archivo"];
			$manual["tipo"] = $r["man_tipo"];
			return $manual; 
		}else{ 
			return -1;
		}
	}
	function getDirectorioOperador($id){		
		$tabla='operador';
		$campo='ope_sigla';
		$predicado='ope_id = '. $id;
		if(!isset($id))
		   $predicado='ope_id=1';
		$r = $this->db->recuperarCampo($tabla,$campo,$predicado);
		$r = $r."/";
		if($r) return $r; else return -1;
	}
}
?>
