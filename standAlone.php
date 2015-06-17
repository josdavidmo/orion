<?php

$username = 'pncav';
$password = 'pnc4vpr0ducc10n';
$database = 'pncav2';
$host = 'equipo04';

// Opens a connection to a MySQL server
$connection = mysql_connect($host, $username, $password);
if (!$connection) {
    die('Not connected : ' . mysql_error());
} else {
    $db_selected = mysql_select_db($database, $connection);
    // Set the active MySQL database
    if (!$db_selected) {
        die('Can\'t use db : ' . mysql_error());
    } else {
        require_once "./clases/nusoap-0.9.5/lib/nusoap.php";

        function writeLog($query) {
            $file = fopen('logs/data' . getdate()['month'] . getdate()['year'] . '.log', "a");
            fwrite($file, date('Y-m-d H:i:s') . "|standAlone|" . $query . PHP_EOL);
            fclose($file);
        }

        function insertarRespuesta($idPregunta, $idEncuesta, $respuesta) {
            $query = "REPLACE INTO respuestas "
                    . "(idPregunta,idEncuesta,respuesta) "
                    . "VALUES ('$idPregunta','$idEncuesta','$respuesta')";
            $result = mysql_query($query);
            writeLog($query);
            return $result;
        }

        function insertarActividad($idActividad, $idBitacora, $fecha, $fechaFin, $descripcionActividadEjecutada, $condicionesClimaticas, $condicionesTopologicas, $observaciones, $idEstadoSalud, $numCuadrillas, $totalPersonas, $totalPersonasContratadas, $cumplimientoParafiscales, $cumplimientoSenalizacion, $cumplimientoEPP, $cumplimientoCertificaciones, $estado) {
            if ($numCuadrillas == "") {
                $numCuadrillas = "NULL";
            }
            if ($totalPersonas == "") {
                $totalPersonas = "NULL";
            }
            if ($totalPersonasContratadas == "") {
                $totalPersonasContratadas = "NULL";
            }
            $auxObject = "'$idActividad','$idBitacora','$fecha','$fechaFin',"
                    . "'$descripcionActividadEjecutada','$condicionesClimaticas',"
                    . "'$condicionesTopologicas','$observaciones','$idEstadoSalud',"
                    . "$numCuadrillas,$totalPersonas,$totalPersonasContratadas,"
                    . "'$cumplimientoParafiscales','$cumplimientoSenalizacion',"
                    . "'$cumplimientoEPP','$cumplimientoCertificaciones','$estado',0";
            $query = "REPLACE INTO actividad VALUES ($auxObject)";
            writeLog($query);
            $result = mysql_query($query);
            return $result;
        }

        function insertarGastos($idGastosActividad, $descripcion, $valor, $archivo, $idTipoGasto, $idActividad) {
            $auxObject = "'$idGastosActividad','$descripcion','$valor','$archivo',"
                    . "'$idTipoGasto','$idActividad',0,0";
            $query = "REPLACE INTO gastos_actividad VALUES ($auxObject)";
            writeLog($query);
            $result = mysql_query($query);
            return $result;
        }

        function insertarRegistroFotografico($idRegistroFotografico, $descripcionRegistroFotografico, $archivo, $idActividad) {
            $auxObject = "'$idRegistroFotografico','$descripcionRegistroFotografico',"
                    . "'$archivo','$idActividad',0";
            $query = "REPLACE INTO registroFotografico VALUES ($auxObject)";
            writeLog($query);
            $result = mysql_query($query);
            return $result;
        }

        function insertarHallazgo($idHallazgosPendientes, $observacion, $archivo, $idTipo, $idActividad, $idClasficiacion, $fechaRespuesta, $observacionRespuesta, $archivoRespuesta) {
            if($fechaRespuesta == ""){
                $fechaRespuesta = "NULL";
            }
            if($observacionRespuesta == ""){
                $observacionRespuesta = "NULL";
            }
            if($archivoRespuesta == ""){
                $archivoRespuesta = "NULL";
            }
            $auxObject = "'$idHallazgosPendientes','$observacion','$archivo','$idTipo',"
                    . "'$idActividad',$idClasficiacion,$fechaRespuesta,$observacionRespuesta,$archivoRespuesta,0";
            $query = "REPLACE INTO hallazgospendientes VALUES ($auxObject)";
            writeLog($query);
            $result = mysql_query($query);
            return $result;
        }

        function getObjetivosBitacora($idUsuario) {
            $bitacoras = NULL;
            $query = "SELECT * FROM bitacora WHERE idUsuario = '$idUsuario' AND bitacora_sync";
            writeLog($query);
            $result = mysql_query($query);
            $contador = 0;
            while ($row = mysql_fetch_assoc($result)) {
                $bitacoras[$contador]['id'] = $row['idBitacora'];
                $bitacoras[$contador]['usuario'] = $row['idUsuario'];
                $bitacoras[$contador]['beneficiario'] = $row['idBeneficiario'];
                $bitacoras[$contador]['descripcionActividad'] = $row['descripcionActividad'];
                $bitacoras[$contador]['fechaInicio'] = $row['fechaInicio'];
                $bitacoras[$contador]['fechaFin'] = $row['fechaFin'];
                $contador++;
            }
            $query = "UPDATE bitacora SET bitacora_sync = 0 WHERE idUsuario = '$idUsuario'";
            writeLog($query);
            mysql_query($query);
            return $bitacoras;
        }

        function getPlaneacionesNuevas($idUsuario) {
            $planeaciones = NULL;
            $query = "SELECT * "
                    . "FROM generador_planeacion "
                    . "WHERE sync AND usu_id = '$idUsuario'";
            $result = mysql_query($query);
            $contador = 0;
            while ($row = mysql_fetch_assoc($result)) {
                $planeaciones[$contador]['id'] = $row['pla_id'];
                $planeaciones[$contador]['beneficiario'] = $row['ben_id'];
                $planeaciones[$contador]['instrumento'] = $row['ins_id'];
                $planeaciones[$contador]['numeroEncuestas'] = $row['pla_numero_encuestas'];
                $planeaciones[$contador]['fechaInicio'] = $row['pla_fecha_inicio'];
                $planeaciones[$contador]['fechaFin'] = $row['pla_fecha_fin'];
                $planeaciones[$contador]['usuario'] = $row['usu_id'];
                $contador++;
            }
            $query = "UPDATE generador_planeacion SET sync = 0 WHERE usu_id = '$idUsuario'";
            writeLog($query);
            mysql_query($query);
            return $planeaciones;
        }

        $server = new soap_server();
        $server->configureWSDL("standAlone", "urn:standAlone");

        $server->wsdl->addComplexType("columns", "complexType", "array", "", "SOAP-ENC:Array", array());
        $server->wsdl->addComplexType("rows", "complexType", "array", "", "SOAP-ENC:Array", array(), array(array("ref" => "SOAP:ENC:arrayType",
                "wsdl:arrayType" => "tns:columns[]")), "tns:columns");

        $server->register("insertarRespuesta", array("idPregunta" => "xsd:string", "idEncuesta" => "xsd:string", "respuesta" => "xsd:string"), array("return" => "xsd:boolean"), "urn:standAlone", "urn:standAlone#insertarRespuesta", "rpc", "encoded", "Usado para la sincronizacion de Respuestas");

        $server->register("getPlaneacionesNuevas", array("idUsuario" => "xsd:string"), array("return" => "tns:rows"), "urn:standAlone", "urn:standAlone#getPlaneacionesNuevas", "rpc", "encoded", "Obtiene nuevas planeaciones agregadas por el servidor");

        $server->register("getObjetivosBitacora", array("idUsuario" => "xsd:string"), array("return" => "tns:rows"), "urn:standAlone", "urn:standAlone#getObjetivosBitacora", "rpc", "encoded", "Obtiene nuevas bitacoras agregadas por el servidor");

        $server->register("insertarActividad", array("idActividad" => "xsd:string",
            "idBitacora" => "xsd:string",
            "fecha" => "xsd:string",
            "fechaFin" => "xsd:string",
            "descripcionActividadEjecutada" => "xsd:string",
            "condicionesClimaticas" => "xsd:string",
            "condicionesTopologicas" => "xsd:string",
            "observaciones" => "xsd:string",
            "idEstadoSalud" => "xsd:string",
            "numCuadrillas" => "xsd:string",
            "totalPersonas" => "xsd:string",
            "totalPersonasContratadas" => "xsd:string",
            "cumplimientoParafiscales" => "xsd:string",
            "cumplimientoSenalizacion" => "xsd:string",
            "cumplimientoEPP" => "xsd:string",
            "cumplimientoCertificaciones" => "xsd:string",
            "estado" => "xsd:string"), array("return" => "xsd:boolean"), "urn:standAlone", "urn:standAlone#insertarActividad", "rpc", "encoded", "Agregar nuevas actividades enviadas por los clientes");
        
        $server->register("insertarHallazgo", array("idHallazgosPendientes" => "xsd:string",
            "observacion" => "xsd:string",
            "archivo" => "xsd:string",
            "idTipo" => "xsd:string",
            "idActividad" => "xsd:string",
            "idClasificacion" => "xsd:string",
            "fechaRespuesta" => "xsd:string",
            "observacionRespuesta" => "xsd:string",
            "archivoRespuesta" => "xsd:string"), array("return" => "xsd:boolean"), "urn:standAlone", "urn:standAlone#insertarHallazgo", "rpc", "encoded", "Agregar nuevos hallazgos enviados por los clientes");

        $server->register("insertarRegistroFotografico", array("idRegistroFotografico" => "xsd:string",
            "descripcionRegistroFotografico" => "xsd:string",
            "archivo" => "xsd:string",
            "idActividad" => "xsd:string"), array("return" => "xsd:boolean"), "urn:standAlone", "urn:standAlone#insertarRegistroFotografico", "rpc", "encoded", "Agregar nuevos registros fotograficos enviados por los clientes");

        $server->register("insertarGastos", array("idGastosActividad" => "xsd:string",
            "descripcion" => "xsd:string",
            "valor" => "xsd:string",
            "archivo" => "xsd:string",
            "idTipoGasto" => "xsd:string",
            "idActividad" => "xsd:string"), array("return" => "xsd:boolean"), "urn:standAlone", "urn:standAlone#insertarGastos", "rpc", "encoded", "Agregar nuevos gastos enviados por los clientes");
        
        $post = file_get_contents('php://input');
        $server->service($post);
    }
}
?>