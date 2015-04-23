<?php

class CPolizaData{
  
    var $db = null;
    
    function CPolizaData($db){
        $this->db=$db;
    }
    
    function getPolizaById($id, $tabla){
        $polizas = null;
        $sql="SELECT * FROM " . $tabla . " WHERE pol_id = $id";
        $r = $this->db->ejecutarConsulta($sql);
        while($w = mysql_fetch_array($r)){
            $polizas['pol_id']                  = $w['pol_id'];
            $polizas['pol_numero_contrato']     = $w['pol_numero_contrato'];
            $polizas['pol_objeto']              = $w['pol_objeto'];
            $polizas['pol_plazo']               = $w['pol_plazo'];
            $polizas['pol_fecha_suscripcion']   = $w['pol_fecha_suscripcion'];
            $polizas['pol_contratante']         = $w['pol_contratante'];
            $polizas['pol_contratista']         = $w['pol_contratista'];
            $polizas['pol_numero_poliza']       = $w['pol_numero_poliza'];
            $polizas['pol_aseguradora']         = $w['pol_aseguradora'];
            $polizas['pol_tomador']             = $w['pol_tomador'];
            $polizas['pol_asegurado']           = $w['pol_asegurado'];
            $polizas['pol_beneficiario']        = $w['pol_beneficiario'];
            $polizas['pol_amparo']              = $w['pol_amparo'];
            $polizas['pol_porcentaje']          = $w['pol_porcentaje'];
            $polizas['pol_valor_asegurado']     = $w['pol_valor_asegurado'];
            $polizas['pol_vigencia_inicio']     = $w['pol_vigencia_inicio'];
            $polizas['pol_vigencia_fin']        = $w['pol_vigencia_fin'];
            $polizas['pol_observaciones']       = $w['pol_observaciones'];
            $polizas['pol_archivo']             = $w['pol_archivo'];
            return $polizas;
        }
    }
    
    function getPolizas($condicion, $tabla){
        $polizas=null;
        $sql="SELECT * FROM " . $tabla ." WHERE $condicion";
        $r = $this->db->ejecutarConsulta($sql);
        $cont=0;
        while($w = mysql_fetch_array($r)){
            $polizas[$cont]['pol_id']               = $w['pol_id'];
            $polizas[$cont]['pol_numero_contrato']  = $w['pol_numero_contrato'];
            $polizas[$cont]['pol_objeto']           = $w['pol_objeto'];
            $polizas[$cont]['pol_plazo']            = $w['pol_plazo'];
            $polizas[$cont]['pol_fecha_suscripcion']= $w['pol_fecha_suscripcion'];
            $polizas[$cont]['pol_contratante']      = $w['pol_contratante'];
            $polizas[$cont]['pol_contratista']      = $w['pol_contratista'];
            $polizas[$cont]['pol_numero_poliza']    = $w['pol_numero_poliza'];
            $polizas[$cont]['pol_aseguradora']      = $w['pol_aseguradora'];
            $polizas[$cont]['pol_tomador']          = $w['pol_tomador'];
            $polizas[$cont]['pol_asegurado']        = $w['pol_asegurado'];
            $polizas[$cont]['pol_beneficiario']     = $w['pol_beneficiario'];
            $polizas[$cont]['pol_amparo']           = $w['pol_amparo'];
			$polizas[$cont]['pol_porcentaje']       = $w['pol_porcentaje']*100;
			if($tabla == 'poliza'){
				$polizas[$cont]['pol_porcentaje']       = $w['pol_porcentaje'];
			}
            $polizas[$cont]['pol_valor_asegurado']  = $w['pol_valor_asegurado'];
            $polizas[$cont]['pol_vigencia_inicio']  = $w['pol_vigencia_inicio'];
            $polizas[$cont]['pol_vigencia_fin']     = $w['pol_vigencia_fin'];
            $polizas[$cont]['pol_observaciones']    = $w['pol_observaciones'];
            $polizas[$cont]['pol_archivo']          = "<a href='././".RUTA_POLIZAS."/" . $w['pol_archivo'] . "' target='_blank'>{$w['pol_archivo']}</a>";
            $cont++;
        }
        return $polizas;
    }
    
    function insertPoliza($poliza, $tabla){
        $temp="";
        if($poliza->getArchivoPoliza()!=null){
            $temp="'".$poliza->getArchivoPoliza()."'";
        }else{
            $temp="null";
        }
        $campos = "pol_id,"
                ."pol_numero_contrato,"
                ."pol_objeto,"
                ."pol_plazo,"
                ."pol_fecha_suscripcion,"
                ."pol_contratante,"
                ."pol_contratista,"
                ."pol_numero_poliza,"
                ."pol_aseguradora,"
                ."pol_tomador,"
                ."pol_asegurado,"
                ."pol_beneficiario,"
                ."pol_amparo,"
                ."pol_porcentaje,"
                ."pol_valor_asegurado,"
                ."pol_vigencia_inicio,"
                ."pol_vigencia_fin,"
                ."pol_observaciones,"
                ."pol_archivo";
        $valores="'',".
                "'".$poliza->getNumContrato()."',".
                "'".$poliza->getObjeto()."',".
                "'".$poliza->getPlazo()."',".
                "'".$poliza->getFechaSuscripcion()."',".
                "'".$poliza->getContratante()."',".
                "'".$poliza->getContratista()."',".
                "'".$poliza->getNumeroPoliza()."',".
                "'".$poliza->getAseguradora()."',".
                "'".$poliza->getTomador()."',".
                "'".$poliza->getAsegurado()."',".
                "'".$poliza->getBeneficiario()."',".
                "'".$poliza->getAmparo()."',".
                "'".$poliza->getPorcentajePoliza()."',".
                "".$poliza->getValorAsegurado().",".
                "'".$poliza->getVigenciaInicio()."',".
                "'".$poliza->getVigenciaFin()."',";
        if($poliza->getObservacionesPoliza()!=null){
            $valores=$valores."'".$poliza->getObservacionesPoliza()."'";
        }else{
            $valores=$valores."null";
        }
        $valores=$valores.",".$temp;
        $r = $this->db->insertarRegistro($tabla,$campos,$valores);
	return $r;
    }
    
    function deletePoliza($id, $tabla){
        $predicado = "pol_id = ". $id;
        $r = $this->db->borrarRegistro($tabla,$predicado);
        return $r;
    }
    function updatePoliza($poliza, $tabla){
        $condicion = "pol_id=".$poliza->getId();
        $temp=null;
        if($poliza->getObservacionesPoliza()==null){
            $temp="null";
        }else{
            $temp="'".$poliza->getObservacionesPoliza()."'";
        }
        $temp2="";
        if($poliza->getArchivoPoliza()!=null){
            $campos = array(
                "pol_numero_contrato",
                "pol_objeto",
                "pol_plazo",
                "pol_fecha_suscripcion",
                "pol_contratante",
                "pol_contratista",
                "pol_numero_poliza",
                "pol_aseguradora",
                "pol_tomador",
                "pol_asegurado",
                "pol_beneficiario",
                "pol_amparo",
                "pol_porcentaje",
                "pol_valor_asegurado",
                "pol_vigencia_inicio",
                "pol_vigencia_fin",
                "pol_observaciones",
                "pol_archivo");
            $temp2="'".$poliza->getArchivoPoliza()."'";
            $valores=array(
                "'".$poliza->getNumContrato()."'",
                "'".$poliza->getObjeto()."'",
                "'".$poliza->getPlazo()."'",
                "'".$poliza->getFechaSuscripcion()."'",
                "'".$poliza->getContratante()."'",
                "'".$poliza->getContratista()."'",
                "'".$poliza->getNumeroPoliza()."'",
                "'".$poliza->getAseguradora()."'",
                "'".$poliza->getTomador()."'",
                "'".$poliza->getAsegurado()."'",
                "'".$poliza->getBeneficiario()."'",
                "'".$poliza->getAmparo()."'",
                "'".$poliza->getPorcentajePoliza()."'",
                "".$poliza->getValorAsegurado()."",
                "'".$poliza->getVigenciaInicio()."'",
                "'".$poliza->getVigenciaFin()."'",
                $temp,$temp2);
        }else{
            $campos = array(
            "pol_numero_contrato",
            "pol_objeto",
            "pol_plazo",
            "pol_fecha_suscripcion",
            "pol_contratante",
            "pol_contratista",
            "pol_numero_poliza",
            "pol_aseguradora",
            "pol_tomador",
            "pol_asegurado",
            "pol_beneficiario",
            "pol_amparo",
            "pol_porcentaje",
            "pol_valor_asegurado",
            "pol_vigencia_inicio",
            "pol_vigencia_fin",
            "pol_observaciones");
            $valores=array(
                "'".$poliza->getNumContrato()."'",
                "'".$poliza->getObjeto()."'",
                "'".$poliza->getPlazo()."'",
                "'".$poliza->getFechaSuscripcion()."'",
                "'".$poliza->getContratante()."'",
                "'".$poliza->getContratista()."'",
                "'".$poliza->getNumeroPoliza()."'",
                "'".$poliza->getAseguradora()."'",
                "'".$poliza->getTomador()."'",
                "'".$poliza->getAsegurado()."'",
                "'".$poliza->getBeneficiario()."'",
                "'".$poliza->getAmparo()."'",
                "'".$poliza->getPorcentajePoliza()."'",
                "".$poliza->getValorAsegurado()."",
                "'".$poliza->getVigenciaInicio()."'",
                "'".$poliza->getVigenciaFin()."'",
                $temp);
            
        }
        $r = $this->db->actualizarRegistro($tabla,$campos,$valores,$condicion);
        return $r;
    }
    
}

?>