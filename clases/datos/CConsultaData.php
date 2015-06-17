<?php

class CConsultaData {

    var $db = null;

    function __construct($db) {
        $this->db = $db;
    }

    function getDb() {
        return $this->db;
    }

    function ejecutarConsultaGenerada($sql) {
        $elementos = array();
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            while ($w = mysql_fetch_assoc($r)) {
                $elementos[] = $w;
            }
        }
        return $elementos;
    }

    function consultarTablas($criterio = "1") {
        $sql = "SHOW TABLES FROM " . DATA_BASE_NAME . " WHERE " . $criterio;
        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        $elementos = null;
        if ($r) {
            $contador = 0;
            while ($w = mysql_fetch_array($r)) {
                $elementos[$contador]['value'] = $w[0];
                $elementos[$contador]['texto'] = $w[0];
                $contador++;
            }
        }
        return $elementos;
    }

    function consultarTablasAlt($tablaOrigen, $campoOrigen) {
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS set1 " .
                "WHERE set1.TABLE_SCHEMA = '" . DATA_BASE_NAME . "' AND EXISTS " .
                "(SELECT * FROM INFORMATION_SCHEMA.COLUMNS set2 " .
                "WHERE set2.TABLE_SCHEMA = '" . DATA_BASE_NAME . "' " .
                "AND set2.TABLE_NAME = '$tablaOrigen' " .
                "AND set2.COLUMN_NAME= '$campoOrigen' " .
                "AND set1.DATA_TYPE=set2.DATA_TYPE " .
                "AND (set1.CHARACTER_MAXIMUM_LENGTH=set2.CHARACTER_MAXIMUM_LENGTH OR (ISNULL(set1.CHARACTER_MAXIMUM_LENGTH) AND ISNULL(set2.CHARACTER_MAXIMUM_LENGTH))) AND (set1.NUMERIC_PRECISION=set2.NUMERIC_PRECISION OR (ISNULL(set1.NUMERIC_PRECISION) AND ISNULL(set2.NUMERIC_PRECISION))) AND (set1.NUMERIC_SCALE=set2.NUMERIC_SCALE OR (ISNULL(set1.NUMERIC_SCALE) AND ISNULL(set2.NUMERIC_SCALE)))) GROUP BY set1.TABLE_NAME";
        //echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        $elementos = null;
        if ($r) {
            $contador = 0;
            while ($w = mysql_fetch_array($r)) {
                $elementos[$contador]['value'] = $w['TABLE_NAME'];
                $elementos[$contador]['texto'] = $w['TABLE_NAME'];
                $contador++;
            }
        }
        return $elementos;
    }

    function consultarCampos($tabla, $criterio = "1") {
        $sql = "SHOW FIELDS FROM " . $tabla . " WHERE " . $criterio;
        $r = $this->db->ejecutarConsulta($sql);
        $elementos = null;
        if ($r) {
            $contador = 0;
            while ($w = mysql_fetch_array($r)) {
                $elementos[$contador]['value'] = $w[0];
                $elementos[$contador]['texto'] = $w[0];
                $contador++;
            }
        }
        return $elementos;
    }

    function consultarCamposAlt($tablaOrigen, $campoOrigen, $tablaDestino, $criterio = "1") {
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS set1 " .
                "WHERE set1.TABLE_SCHEMA = '" . DATA_BASE_NAME . "' AND set1.TABLE_NAME = '$tablaDestino' AND EXISTS " .
                "(SELECT * FROM INFORMATION_SCHEMA.COLUMNS set2 " .
                "WHERE set2.TABLE_SCHEMA = '" . DATA_BASE_NAME . "' " .
                "AND set2.TABLE_NAME = '$tablaOrigen' " .
                "AND set2.COLUMN_NAME= '$campoOrigen' " .
                "AND set1.DATA_TYPE=set2.DATA_TYPE " .
                "AND (set1.CHARACTER_MAXIMUM_LENGTH=set2.CHARACTER_MAXIMUM_LENGTH OR (ISNULL(set1.CHARACTER_MAXIMUM_LENGTH) AND ISNULL(set2.CHARACTER_MAXIMUM_LENGTH))) AND (set1.NUMERIC_PRECISION=set2.NUMERIC_PRECISION OR (ISNULL(set1.NUMERIC_PRECISION) AND ISNULL(set2.NUMERIC_PRECISION))) AND (set1.NUMERIC_SCALE=set2.NUMERIC_SCALE OR (ISNULL(set1.NUMERIC_SCALE) AND ISNULL(set2.NUMERIC_SCALE))))";
        $r = $this->db->ejecutarConsulta($sql);
        $elementos = null;
        if ($r) {
            $contador = 0;
            while ($w = mysql_fetch_array($r)) {
                $elementos[$contador]['value'] = $w['COLUMN_NAME'];
                $elementos[$contador]['texto'] = $w['COLUMN_NAME'];
                $contador++;
            }
        }
        return $elementos;
    }

    function getTipoColumna($tabla, $columna) {
        $sql = "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . DATA_BASE_NAME . "' AND TABLE_NAME='$tabla' AND COLUMN_NAME='$columna'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            return $w[0];
        }
        return null;
    }

    function getLongitudColumna($tabla, $columna) {
        $sql = "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . DATA_BASE_NAME . "' WHERE TABLE_NAME='$columna' AND COLUMN_NAME='$columna'";
        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                return $w[0];
            }
        }
        return null;
    }

    function consultarFunciones() {
        $sql = "SELECT * FROM reporteador_funciones ORDER BY rep_funcion_nombre";
//        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        $elementos = null;
        if ($r) {
            $contador = 0;
            while ($w = mysql_fetch_array($r)) {
                $elementos[$contador]['value'] = $w['rep_funcion_id'];
                $elementos[$contador]['texto'] = $w['rep_funcion_nombre'];
                $contador++;
            }
        }
        return $elementos;
    }

    function consultarComparaciones() {
        $sql = "SELECT * FROM reporteador_comparaciones";
//        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        $elementos = null;
        if ($r) {
            $contador = 0;
            while ($w = mysql_fetch_array($r)) {
                $elementos[$contador]['value'] = $w['rep_comparacion_valor'];
                $elementos[$contador]['texto'] = $w['rep_comparacion_valor'];
                $contador++;
            }
        }
        return $elementos;
    }

    function consultarOtros() {
        $sql = "SELECT * FROM reporteador_OTROS";
//        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        $elementos = null;
        if ($r) {
            $contador = 0;
            while ($w = mysql_fetch_array($r)) {
                $elementos[$contador]['value'] = $w['rep_otros_id'];
                $elementos[$contador]['texto'] = $w['rep_otros_def'];
                $contador++;
            }
        }
        return $elementos;
    }

    function getDetalleFuncion($id) {
        $sql = "SELECT rep_funcion_detalle FROM reporteador_funciones WHERE rep_funcion_id=$id";
//        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        $elementos = null;
        if ($r) {
            $contador = 0;
            while ($w = mysql_fetch_array($r)) {
                return $w['rep_funcion_detalle'];
                $contador++;
            }
        }
        return null;
    }

    function getReportes($criterio) {
        $sql = "SELECT rep_id, rep_nombre, opc_nombre FROM reporteador INNER JOIN opcion ON rep_opcion = opc_id WHERE $criterio";
//        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        $elementos = null;
        if ($r) {
            $contador = 0;
            while ($w = mysql_fetch_array($r)) {
                $elementos[$contador]['id'] = $w['rep_id'];
                $elementos[$contador]['nombre'] = $w['rep_nombre'];
                $elementos[$contador]['opcion'] = $w['opc_nombre'];
                $contador++;
            }
        }
        return $elementos;
    }

    function getReporteById($id) {
        $sql = "SELECT * FROM reporteador WHERE rep_id=$id";
//        echo $sql;
        $r = $this->db->ejecutarConsulta($sql);
        $elementos = null;
        if ($r) {
            $contador = 0;
            while ($w = mysql_fetch_array($r)) {
                $elementos['id'] = $w['rep_id'];
                $elementos['nombre'] = $w['rep_nombre'];
                $elementos['consulta'] = str_replace("\'", "'", $w['rep_consulta']);
                $elementos['opcion'] = $w['rep_opcion'];
                return $elementos;
            }
        }
        return $elementos;
    }

    function getEncabezados() {
        $instrumentos = null;
        $sql = " select * from opcion where opn_id = 0 order by opc_nombre";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $instrumentos[$cont]['value'] = $w['opc_id'];
                $instrumentos[$cont]['texto'] = $w['opc_nombre'];
                $cont++;
            }
        }
        return $instrumentos;
    }

    function insertReporte($nombre, $opcionNivel) {
        $tabla = 'reporteador';
        $consulta = $_SESSION['consulta'];
        $consulta = str_replace("'", "\'", $consulta);
        $campos = 'rep_id,  rep_nombre,rep_consulta,rep_opcion';
        $valores = "'','" . $nombre . "','" . $consulta . "'," . $opcionNivel;
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        if ($r == 'true') {
            return REPORTE_GUARDADO;
        }
        return ERROR_REPORTE_GUARDADO;
    }

    function editReporte($nombre, $opcionNivel) {
        $tabla = 'reporteador';
        $id_reporte = $_SESSION['id_reporte'];
        $consulta = $_SESSION['consulta'];
        $consulta = str_replace("'", "\'", $consulta);
        $condicion = "rep_id=$id_reporte";
        $campos = array('rep_nombre', 'rep_consulta', 'rep_opcion');
        $valores = array("'" . $nombre . "'", "'" . $consulta . "'", $opcionNivel);
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        if ($r == 'true') {
            return REPORTE_GUARDADO;
        }
        return ERROR_REPORTE_GUARDADO;
    }

}

?>
