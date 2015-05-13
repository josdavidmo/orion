<?php
/**
 * Archivo de configuracion para la aplicacion modularizada.
 * Define valores por defecto y datos para cada uno de los modulos.
*/


/**
*modulo por defecto
*/
define('MODULO_DEFECTO', 'cerrar');
/**
*layout por defecto
*/
define('LAYOUT_DEFECTO', 'default_layout.php');
/**
*ruta de los modulos
*/
define('MODULO_PATH', realpath('./modulos/'));
/**
*ruta de los layouts
*/
define('LAYOUT_PATH', realpath('./templates/layouts/'));

/**
*host de la base de datos
*/
define('DB_HOST','equipo04');
/**
*usuario de la base de datos
*@private
*/
define('DB_USER','pncav');
/**
*password del usuario de la base de datos
*@private
*/
define('DB_PASSWORD','pnc4vpr0ducc10n');
/**
*nombre de la base de datos
*/
define('DB_NAME','pncav2');
/**
*id operador por defecto
*/
define('OPERADOR_DEFECTO',1);
/**
*url del aplicativo
*/
define('WEB_PATH','http://192.168.11.6:8082/pncav2');
/**
*direccion de correo
*/
define('MAIL_ADDRESS','german.ramirez@serticsas.com.co');
/**
*validacion por ip
*/
define('VALIDAR_IP','no');
/**
*carpeta de logs
**/
define('DATA_LOG_FILE','./logs/data'.getdate()['month'].getdate()['year'].'.log');
/**
*carpeta del lenguaje
*/
define('LANG_DIR','./lang/es-co/');
$handle=opendir(LANG_DIR); 
while ($file = readdir($handle)) { 
	if ($file != "." && $file != "..") { 
    	include(LANG_DIR.$file);
	} 
}

$db = new CData(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
$db->conectar();
$sql="select * from opcion";
$result = $db->ejecutarConsulta($sql);


while($row = mysql_fetch_array($result)){
	if($row["layout"]=="")$ly = LAYOUT_DEFECTO; else $ly = $row["layout"];
        $temporal = split("&",$row["opc_variable"]);
        if(count($temporal)>1)
            $conf[$temporal[0]] = array('archivo' => $row["opc_url"], 'layout' => $ly , 'operador' => $row["ope_id"]);
        else
            $conf[$row["opc_variable"]] = array('archivo' => $row["opc_url"], 'layout' => $ly , 'operador' => $row["ope_id"]);
} 
?>
