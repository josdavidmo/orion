<?php

/**
 * Maneja las peticiones a la base de datos para la carga de los registros
 * almacenados en tablas.
 * @author Brian Kings
 * @version 1.0
 * @since 04/08/2014
 */
class CRegistroInversionData {

    /** Maneja el driver con la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase
     * @param Cdata $db
     */
    function CRegistroInversionData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene el registro de la inversion dado un criterio.
     * @param string $criterio
     * @return array
     */
    function getRegistroInversion($criterio) {
        $resultado = null;
        $sql = "select rin.rin_id ,act.act_descripcion, rin.rin_fecha, pro.nombre_prove, "
                . " rin.rin_numero_documento, rin.rin_valor, rin.rin_observaciones, rin.rin_documento_soporte"
                . " from registro_inversion rin "
                . " left join actividadpia act on rin.act_id = act.act_id "
                . " left join proveedores pro on pro.id_prove = rin.id_prove"
                . " where " . $criterio;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $resultado[$cont]['id_element'] = $w['rin_id'];
                $resultado[$cont]['actividad'] = $w['act_descripcion'];
                $resultado[$cont]['fecha'] = $w['rin_fecha'];
                $resultado[$cont]['proveedor'] = $w['nombre_prove'];
                $resultado[$cont]['numero_documento'] = $w['rin_numero_documento'];
                $resultado[$cont]['valor'] = $w['rin_valor'];
                $resultado[$cont]['observaciones'] = $w['rin_observaciones'];
                $resultado[$cont]['documento_soporte'] = "<a href='././soportes/Interventoria/Registro Inversion/" . $w['rin_documento_soporte'] . "' target='_blank'>{$w['rin_documento_soporte']}</a>";
                $cont++;
            }
        }
        return $resultado;
    }

    /*
     * Inserta un registro en la base de datos
     * @param CRegistroInversion $CRegistroInversion
     * @return boolean
     */
    function insertRegistroInversion($CRegistroInversion) {
        $tabla = 'registro_inversion';
        $campos = 'act_id, rin_fecha, id_prove,rin_numero_documento, rin_valor, rin_observaciones, rin_documento_soporte';
        $valores = "'" . $CRegistroInversion->getactividad() . "','"
                . $CRegistroInversion->getfecha() . "','"
                . $CRegistroInversion->getproveedor() . "','"
                . $CRegistroInversion->getNumeroDocumento() . "','"
                . $CRegistroInversion->getvalor() . "','"
                . $CRegistroInversion->getobservaciones() . "','"
                . $CRegistroInversion->getDocumentoSoporte() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /*
     * Elimina un registro de la base de datos dado su id.
     * @param integer $id
     * @return boolean
     */
    function deleteRegistroInversion($id) {
        $tabla = 'registro_inversion';
        $predicado = "rin_id = " . $id;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Obtiene la informacion de un registro de inversion por su id
     * @param integer $id
     * @return integer
     */
    function getRegistroInversionById($id) {
        $sql = "select rin.rin_id ,act.act_id, rin.rin_fecha, pro.id_prove, "
                . " rin.rin_numero_documento, rin.rin_valor, rin.rin_observaciones, rin.rin_documento_soporte"
                . " from registro_inversion rin "
                . " left join actividadpia act on rin.act_id = act.act_id "
                . " left join proveedores pro on pro.id_prove = rin.id_prove"
                . " where rin.rin_id = " . $id;

        $r = $this->db->recuperarResultado($this->db->ejecutarConsulta($sql));
        if ($r) {
            return $r;
        } else {
            return -1;
        }
    }

    /**
     * Actualiza los datos de un registro de inversión en la base de datos
     * @param CRegistroInversion $CRegistroInversion
     * @return boolean
     */
    function updateRegistroInversion($CRegistroInversion) {
        $tabla = 'registro_inversion';
        $campos = array('act_id', ' rin_fecha', ' id_prove', 'rin_numero_documento', ' rin_valor', ''
            . ' rin_observaciones', ' rin_documento_soporte');
        $valores = array("'" . $CRegistroInversion->getactividad() . "'",
            "'" . $CRegistroInversion->getfecha() . "'",
            "'" . $CRegistroInversion->getproveedor() . "'",
            "'" . $CRegistroInversion->getNumeroDocumento() . "'",
            "'" . $CRegistroInversion->getvalor() . "'",
            "'" . $CRegistroInversion->getobservaciones() . "'",
            "'" . $CRegistroInversion->getDocumentoSoporte() . "'");
        $condicion = " rin_id = " . $CRegistroInversion->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Obtiene las actividades almacenadas en la base de datos.
     * @return type
     */
    function getActividades() {
        $resultado = null;
        $sql = "select act_id,act_descripcion from actividadpia";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $resultado[$cont]['id'] = $w['act_id'];
                $resultado[$cont]['nombre'] = $w['act_descripcion'];
                $cont++;
            }
        }
        return $resultado;
    }

    /**
     * Obtiene los proveedores almacenados en la base de datos.
     * @return type
     */
    function getProveedores() {
        $resultado = null;
        $sql = "select Id_Prove,Nombre_Prove from proveedores";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $resultado[$cont]['id'] = $w['Id_Prove'];
                $resultado[$cont]['nombre'] = $w['Nombre_Prove'];
                $cont++;
            }
        }
        return $resultado;
    }

}

?>