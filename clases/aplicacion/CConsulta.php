<?php

class CConsulta{
    
    var $campos=null;
    var $tablas=null;
    var $join=null;
    var $condiciones=null;
    var $order=null;
    var $group=null;
    var $sql="";
    var $titulos=null;
    var $db;
    
    function CConsulta($db=null) {
        $this->db=$db;
    }
    
    function setCampos($campos) {
        $this->campos = $campos;
    }

    function setTablas($tablas) {
        $this->tablas = $tablas;
    }

    function setCondiciones($condiciones) {
        $this->condiciones = $condiciones;
    }

    function getCampos() {
        return $this->campos;
    }

    function getTablas() {
        return $this->tablas;
    }

    function getCondiciones() {
        return $this->condiciones;
    }
    
    function getSql() {
        return $this->sql;
    }
   
    function getTitulos(){
        return $this->titulos;
    }
    
    function addCampo($valor){
        $set = true;
        for($i=0;$i<count($this->campos);$i++){
            if($this->campos[$i]==$valor){
                $set = false;
            }
        }
        if($set){
            $this->campos[count($this->campos)]=$valor;
        }
        
    }
    
    function addOrder($valor){
        $this->order[count($this->order)]=$valor;
    }
    
    function addGroup($valor){
        $this->group[count($this->group)]=$valor;
    }
    
    function addTabla($valor){
        $set = true;
        for($i=0;$i<count($this->tablas);$i++){
            if($this->tablas[$i]==$valor || (strpos($this->tablas[$i], $valor." ")!=false) || (strpos($this->tablas[$i], " ".$valor)!=false)){
                $set = false;
            }
        }
        if($set){
            $this->tablas[count($this->tablas)]=$valor;
        }
    }
    
    function addJoin($valor){
        $this->join[count($this->join)]=$valor;
        echo "link";
    }
    
    function addCondicion($valor){
        $this->condiciones[count($this->condiciones)]=$valor;
    }
    
    function prepararConsulta(){
        //-----------------CAMPOS---------------------------
        $this->sql="SELECT ";
        for($i=0;$i<count($this->campos);$i++){
            if($i!=0){
                $this->sql.=", ";
            }
            $this->sql.=$this->campos[$i];
        }
        //--------------------------------------------------
        
        //------------------TABLAS--------------------------
        //$this->tablas=null;
        for($i=0;$i<count($this->tablas);$i++){
            for($j=0;$j<count($this->join);$j++){
                $str=  explode(",", $this->join[$j]);
                if($this->tablas[$i]==$str[0] || $this->tablas[$i]==$str[1]){
                    $this->tablas[$i]="";
                }
            }
        }
        
        for($i=count($this->join)-1;$i>0;$i--){
            $str1=  explode(',', $this->join[$i]);
            for($j=$i-1;$j>=0;$j--){
                $str2=  explode(',', $this->join[$j]);
                if($str1[0]==$str2[0]||$str1[0]==$str2[1]){
                    $str1[0]="";
                }
                if($str1[1]==$str2[0]||$str1[1]==$str2[1]){
                    $str1[1]="";
                }
            }
            if($str1[1]=="" && $str1[0]!=""){
                $tem=$str1[1];
                $str1[1]=$str1[0];
                $str1[0]=$tem;
            }
            $this->join[$i]=$str1[0].','.$str1[1].','.$str1[2];
        }
        
        
        for($i=0;$i<count($this->join)-1;$i++){
            $str1=  explode(",", $this->join[$i]);
            for($j=$i+1;$j<count($this->join);$j++){
                $str2=  explode(",", $this->join[$j]);
                if($str1[0]=="" && $str1[0]!=""){
                    $tem=$this->join[$i];
                    $this->join[$i]=$this->join[$j];
                    $this->join[$j]=$tem;
                    $str1=  explode(",", $this->join[$i]);
                }
            }
        }
        
        $this->sql.=" FROM ";
        for($i=0;$i<count($this->tablas);$i++){
            if($i!=0 && $this->tablas[$i]!=""){
                $this->sql.=", ";
            }
            $this->sql.=$this->tablas[$i];
        }
        
        for($i=0;$i<count($this->join);$i++){
            $str1=  explode(",", $this->join[$i]);
            $this->sql.=$str1[0]." INNER JOIN ".$str1[1]." ON ".$str1[2]." ";
        }
        
        //--------------------------------------------------
        
        //------------------WHERE---------------------------
        $this->sql.=" WHERE 1 ";
        for($i=0;$i<count($this->condiciones);$i++){
                $this->sql.=" AND ".$this->condiciones[$i];
        }
        //--------------------------------------------------
        
        //------------------OTROS---------------------------
        if(count($this->order)>0){
            $this->sql.=" ORDER BY ";
            for($i=0;$i<count($this->order);$i++){
                    $this->sql.=$this->order[$i].", ";
            }
            $this->sql.=" 1 ";
        }
        
        if(count($this->group)>0){
            $this->sql.=" GROUP BY ";
            for($i=0;$i<count($this->group);$i++){
                $this->sql.=$this->group[$i];
                if($i<count($this->group)-1){
                    $this->sql.=", ";
                }
            }
            
        }
        //--------------------------------------------------
        
        //------------------TITULOS-------------------------
        $this->titulos=null;
        for($i=0;$i<count($this->campos);$i++){
            $pos = strpos($this->campos[$i],SEUDONIMO_TABLA);
            if($pos!=false){
                $this->titulos[$i]=substr($this->campos[$i],$pos+strlen(SEUDONIMO_TABLA));
            }else{
                $this->titulos[$i]=$this->campos[$i];
            }
        }
        //--------------------------------------------------
    }
    
    function ejecutarConsulta($db){
        $elementos = $db->ejecutarConsultaGenerada($this->sql);
        return $elementos;
    }
    
}

?>