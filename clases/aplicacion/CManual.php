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
* Clase Manual
*
* @package  clases
* @subpackage aplicacion
* @author Redcom Ltda
* @version 2013.01.00
* @copyright Ministerio de Transporte
*/

class CManual{
	var $id = null;
	var $nombre = null;
	var $tipo = null;
	var $archivo = null;
	var $operador = null;
	var $dm = null;
	
	var $permitidos = array('pdf','zip','rar','7z');
/**
** Constructor de la clase CManualData
**/				
	function CManual($dm){	$this->dm = $dm;	}
	function getId(){			return $this->id;		}
	function getNombre(){		return $this->nombre;	}
	function getArchivo(){		return $this->archivo;	}
	function getOperador(){		return $this->operador;	}
	function getTipo(){		return $this->tipo;	}
	
	function setId($id){		 	 $this->id = $id;		}
	function setNombre($nombre) 	{$this->nombre = $nombre;}
	function setArchivo($archivo)	{$this->archivo = $archivo;}
	function setOperador($operador)	{$this->operador = $operador;}
	function setTipo($tipo)	{$this->tipo = $tipo;}
/**
** carga los valores de un objeto MANUAL por su id para ser editados
**/					
	function loadManual(){
		$r = $this->dm->getManualById($this->id);
		if($r != -1){
			$this->nombre = $r["nombre"];
			$this->archivo = $r["archivo"];
			$this->tipo = $r["tipo"];
		}else{
			$this->nombre = "";
			$this->archivo = "";
			$this->tipo = "";
		}
	}
/**
** almacena un objeto MANUAL y retorna un mensaje del resultado del proceso
**/		
	function saveNewManual(){
		$r = "";
		$extension = explode(".",$this->archivo['name']);
		$num = count($extension)-1;
		$noMatch = 0;
		foreach( $this->permitidos as $p ) {
			if ( strcasecmp( $extension[$num], $p ) == 0 ) $noMatch = 1;
		}
		if($this->archivo['name']!=null){
			if($noMatch==1){
				if($this->archivo['size'] < MAX_SIZE_DOCUMENTOS){
							
					$dirOperador=$this->dm->getDirectorioOperador($this->operador);
					$ruta = RUTA_DOCUMENTOS."/".$dirOperador."manuales/";
					$carpetas = explode("/",substr($ruta,0,strlen($ruta)-1));
					$cad = $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'];
					$ruta_destino = '';
					foreach($carpetas as $c){
						$ruta_destino .= "/".strtolower($c);
						if(!is_dir($ruta_destino)) {
							mkdir($ruta_destino,0777);}
						else {
							chmod($ruta_destino, 0777);
						}
					}
                                        
                                        echo utf8_decode($this->archivo['name']);
                    
					if(!move_uploaded_file($this->archivo['tmp_name'], utf8_decode(strtolower($ruta).$this->archivo['name']))){
						$r = ERROR_COPIAR_ARCHIVO;
					}else{
						$i = $this->dm->insertManual($this->nombre,$this->archivo['name'],$this->tipo);
						if($i == "true"){
							$r = MANUAL_AGREGADO;
						}else{
							$r = ERROR_ADD_MANUAL;
						}
					}
				}else{
					$r = ERROR_SIZE_ARCHIVO;
				}
			}else{
				$r = ERROR_FORMATO_ARCHIVO;
			}
		}else{
			$r = ERROR_CONFIGURACION_RUTA;
		}
		return $r;
	
	}
/**
** elimina un objeto MANUAL y retorna un mensaje del resultado del proceso
**/		
	function deleteManual(){
		$dirOperador=$this->dm->getDirectorioOperador($this->operador);
		$ruta = RUTA_DOCUMENTOS."/".$dirOperador."manuales/";
		$r = $this->dm->deleteManual($this->id);
		if($r=='true'){
			unlink(strtolower($ruta).$this->archivo);
			$msg = MANUAL_BORRADO;		
		}else{
			$msg = ERROR_DEL_MANUAL;
		}
		return $msg;
	}
/**
** actualiza un objeto MANUAL y retorna un mensaje del resultado del proceso
**/		
	function saveEditManual($archivo_anterior){
		$extension = explode(".",$this->archivo['name']);
		$num = count($extension)-1;
		
		$noMatch = 0;
		foreach( $this->permitidos as $p ) {
			if ( strcasecmp( $extension[$num], $p ) == 0 ) $noMatch = 1;
		}
		//echo("<br>archivo:".$this->archivo['name']);					
		if($this->archivo['name']!=null){
			if($noMatch==1){
				if($this->archivo['size'] < MAX_SIZE_DOCUMENTOS){
					$dirOperador=$this->dm->getDirectorioOperador($this->operador);
					$ruta = RUTA_DOCUMENTOS."/".$dirOperador."manuales/";
					$carpetas = explode("/",substr($ruta,3,strlen($ruta)-1));
					$cad = $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'];
					$ruta_destino = '';
					foreach($carpetas as $c){
						$ruta_destino .= "/".strtolower($c);
						if(!is_dir($ruta_destino)) {
							mkdir($ruta_destino,0777);}
						else {
							chmod($ruta_destino, 0777);
						}
					}
					unlink(strtolower($ruta).$archivo_anterior);
					if(!move_uploaded_file($this->archivo['tmp_name'], utf8_decode(strtolower($ruta).$this->archivo['name']))){
						$r = ERROR_COPIAR_ARCHIVO;
					}else{
						$i = $this->dm->updateManualArchivo($this->id,$this->nombre,$this->archivo['name'],$this->tipo);
						if($i == "true"){
							$r = MANUAL_EDITADO;
						}else{
							$r = ERROR_EDIT_MANUAL;
						}
					}
				}else{
					$r = ERROR_SIZE_ARCHIVO;
				}
			}else{
				$r = ERROR_FORMATO_ARCHIVO;
			}
			return $r;
		}else{
			$r = $this->dm->updateManual($this->id,$this->nombre,$this->tipo);
			if($r=='true'){
				$msg = MANUAL_EDITADO;
			}else{
				$msg = ERROR_EDIT_MANUAL;
			}
			return $msg;
		}
	}
}
?>