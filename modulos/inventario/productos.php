<?php

/**
 * Modulo Productos
 * Maneja el modulo productos en union con CProductoData, CProductos, CBasica, 
 * CFamilia, COrdenesdepago 
 *
 * @see \CProductosDataData
 * @see \CFamilia
 * @see \CProductos
 * @see \CBasica
 * @see \COrdenesdepago
 *
 * @package modulos
 * @subpackage inventarios
 * @author SERTIC SAS
 * @version 2014.09.18
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoRegistroProductos = new CRegistroProductosData($db);
$daoOrdenesPago = new COrdenesdepagoData($db);
$daoBasicas = new CBasicaData($db);
$daoPlaneacion = new CPlaneacionData($db);
$daoRelacion = new CRelacionMunicipioOrdenPagoData($db);
$task = $_REQUEST['task'];
$modulo = $_REQUEST['mod'];
$niv = $_REQUEST['niv'];
if (empty($task)) {
    $task = 'see';
}

switch ($task) {
    /**
     * la variable list, permite hacer la carga la página con la lista de 
     * objetos productos según los parámetros de entrada
     */
    case 'list':
        $id = null;
        if (isset($_REQUEST['id_element'])) {
            $id = $_REQUEST['id_element'];
        }
        if (isset($_REQUEST['idOrden'])) {
            $id = $_REQUEST['idOrden'];
        }
        $form = new CHtmlForm();
        $form->setTitle(TITULO_REGISTRO_PRODUCTOS);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=ordenesdepago&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->addInputButton('button', 'export', 'export', BTN_EXPORTAR, 'button', 'onclick="location.href=\'modulos/inventario/registro_productos_excel.php?idOrden=' . $id . '\'"');
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_REGISTRO_PRODUCTOS);
        $titulos = array(DESCRIPCION_PRODUCTOS, VALOR_UNITARIO_PRODUCTOS,
            CANTIDAD_PRODUCTOS, TIPO_REGISTRO_PRODUCTOS,
            FAMILIA_PRODUCTOS, ORDEN_PAGO_PRODUCTOS, VALOR_TOTAL_PRODUCTOS);
        $registroProductos = $daoRegistroProductos->getRegistroProductosByOrdenPago($id);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($registroProductos);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit&idOrden=" . $id);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete&idOrden=" . $id);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add&idOrden=" . $id);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->setFormatRow(array(null, array(2, ',', '.'), null, null, null, null, array(2, ',', '.')));
        $dt->setSumColumns(array(7));
        $dt->writeDataTable($niv);

        $form = new CHtmlForm();
        $form->setTitle(TITULO_RELACION_RECURSOS_MUNICIPIO);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        //$form->addInputButton('button', 'export', 'export', BTN_EXPORTAR, 'button', 'onclick="location.href=\'modulos/inventarios/registro_productos_excel.php?idOrden=' . $id . '\'"');
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_RELACION_RECURSOS_MUNICIPIO);
        $titulos = array(VALOR_ORDEN_PAGO_MUNICIPIO, PORCENTAJE_ORDEN_PAGO_MUNICIPIO,
            DESTINACION_RECURSOS_ORDEN_PAGO_MUNICIPIO, MUNICIPIO);
        $condicion = "mop.idOrdenPago = $id";
        $registroProductos = $daoRelacion->getRelacionMunicipioOrdenPago($condicion);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($registroProductos);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editRelacion&idOrden=" . $id);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteRelacion&idOrden=" . $id);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addRelacion&idOrden=" . $id);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->setFormatRow(array(array(2, ',', '.'), null, null, null));
        $dt->setSumColumns(array(1));
        $dt->writeDataTable($niv);
        break;

    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto registro producto @see \CRegistroProductos
     */
    case 'addRelacion':
        $form = new CHtmlForm();
        $id = $_REQUEST['idOrden'];
        $form->setTitle(TITULO_AGREGAR_RELACION_RECURSOS_MUNICIPIO);
        $form->setId('frm_add_relacion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=addRelacion&idOrden=' . $id);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $regiones = $daoPlaneacion->getRegiones('der_nombre');
        $opciones = null;
        if (isset($regiones)) {
            foreach ($regiones as $region) {
                $opciones[count($opciones)] = array('value' => $region['id'],
                    'texto' => $region['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_REGION);
        $form->addSelect('select', 'sel_region', 'sel_region', $opciones, '', $_REQUEST['sel_region'], '', 'onChange="submit();" required');

        $departamentos = $daoPlaneacion->getDepartamento($_REQUEST['sel_region'], 'dep_nombre');
        $opciones = null;
        if (isset($departamentos)) {
            foreach ($departamentos as $departamento) {
                $opciones[count($opciones)] = array('value' => $departamento['id'],
                    'texto' => $departamento['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_DEPARTAMENTO);
        $form->addSelect('select', 'sel_departamento', 'sel_departamento', $opciones, '', $_REQUEST['sel_departamento'], '', 'onChange="submit();" required');

        $muncipios = $daoPlaneacion->getMunicipio($_REQUEST['sel_departamento'], 'mun_nombre');
        $opciones = null;
        if (isset($muncipios)) {
            foreach ($muncipios as $municipio) {
                $opciones[count($opciones)] = array('value' => $municipio['id'],
                    'texto' => $municipio['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_MUNICIPIO);
        $form->addSelect('select', 'sel_municipios', 'sel_municipios', $opciones, '', $_REQUEST['sel_municipios'], '', ' required');

        $form->addEtiqueta(VALOR_ORDEN_PAGO_MUNICIPIO);
        $form->addInputText('text', 'txt_valor_unitario', 'txt_valor_unitario', '19', '19', null, null, ' pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" onkeyup="formatearNumero(this);" required');

        $destinacionesRecursos = $daoBasicas->getBasicas('destinacion_recursos');
        $opciones = null;
        if (isset($destinacionesRecursos)) {
            foreach ($destinacionesRecursos as $destinacionRecursos) {
                $opciones[count($opciones)] = array('value' => $destinacionRecursos->getId(),
                    'texto' => $destinacionRecursos->getDescripcion());
            }
        }

        $form->addEtiqueta(DESTINACION_RECURSOS_ORDEN_PAGO_MUNICIPIO);
        $form->addSelect('select', 'sel_destinacion', 'sel_destinacion', $opciones, '', $_REQUEST['sel_destinacion'], '', ' required');

        $form->addInputButton('submit', 'btn_add', 'btn_add', BTN_ACEPTAR, 'button', 'onclick="cancelarAccion(\'frm_add_relacion\',\'?mod=' . $modulo . '&niv=' . $niv . '&idOrden=' . $id . '&task=saveAddRelacion\');"');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_relacion\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task=list\');"');

        $form->writeForm();

        break;

    /**
     * la variable saveAddRelacion, permite almacenar el objeto registro productos en la 
     * base de datos @see \CRelacionMunicipioOrdenPagoData
     */
    case 'saveAddRelacion':
        $valor = str_replace(".", "", $_REQUEST['txt_valor_unitario']);
        $municipio = $_REQUEST['sel_municipios'];
        $destinacionRecursos = $_REQUEST['sel_destinacion'];
        $ordenPago = $_REQUEST['idOrden'];

        $relacionMunicipioOrdenPago = new CRelacionMunicipioOrdenPago(NULL, $valor, $destinacionRecursos, $municipio, $ordenPago);
        $r = $daoRelacion->insertRelacionMunicipioOrdenPago($relacionMunicipioOrdenPago);
        $m = ERROR_AGREGAR_RELACION_RECURSOS_MUNICIPIO;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_RELACION_RECURSOS_MUNICIPIO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list&idOrden=" . $ordenPago);

        break;
        
    /**
     * la variable delete, permite hacer la carga del objeto registro producto 
     * y espera confirmacion de eliminarlo @see \CRegistroInversion
     */
    case 'deleteRelacion':
        $id_delete = $_REQUEST['id_element'];
        $idOrden = $_REQUEST['idOrden'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_RELACION_RECURSOS_MUNICIPIO, '?mod=' . $modulo . '&niv=1&task=confirmDeleteRelacion&id_element=' . $id_delete . '&idOrden=' . $idOrden, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&task=list&idOrden=' . $idOrden . '\'');
        break;
    
    /**
     * la variable confirmDelete, permite eliminar el objeto registro producto 
     * de la base de datos @see \CRegistroInversion
     */
    case 'confirmDeleteRelacion':
        $idOrden = $_REQUEST['idOrden'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoRelacion->deleteRelacionMunicipioOrdenPago($id_delete);
        $m = ERROR_BORRAR_RELACION_RECURSOS_MUNICIPIO;
        if ($r == true) {
            $m = EXITO_BORRAR_RELACION_RECURSOS_MUNICIPIO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list&idOrden=" . $idOrden);
        break;

    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto registro producto @see \CRegistroProductos
     */
    case 'editRelacion':
        $form = new CHtmlForm();
        $id_element = $_REQUEST['id_element'];
        $relacionMunicipioOrdenPago = $daoRelacion->getRelacionMunicipioOrdenPagoById($id_element);
        $ubicacion = $daoRelacion->getUbicacionMunicipioById($relacionMunicipioOrdenPago->getMunicipio());
        $id = $_REQUEST['idOrden'];
        $form->setTitle(TITULO_EDITAR_RELACION_RECURSOS_MUNICIPIO);
        $form->setId('frm_edit_relacion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=editRelacion&idOrden=' . $id . '&id_element=' . $id_element);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $regionA = $ubicacion['region'];
        if (isset($_REQUEST['sel_region'])) {
            $regionA = $_REQUEST['sel_region'];
        }

        $departamentoA = $ubicacion['departamento'];
        if (isset($_REQUEST['sel_departamento'])) {
            $departamentoA = $_REQUEST['sel_departamento'];
        }

        $muncipioA = $relacionMunicipioOrdenPago->getMunicipio();
        if (isset($_REQUEST['sel_municipios'])) {
            $muncipioA = $_REQUEST['sel_municipios'];
        }

        $valor = $relacionMunicipioOrdenPago->getValor();
        if (isset($_REQUEST['txt_valor_unitario'])) {
            $valor = $_REQUEST['txt_valor_unitario'];
        }

        $destinacionRecursosA = $relacionMunicipioOrdenPago->getDestinacionRecursos();
        if (isset($_REQUEST['sel_destinacion'])) {
            $destinacionRecursosA = $_REQUEST['sel_destinacion'];
        }

        $regiones = $daoPlaneacion->getRegiones('der_nombre');
        $opciones = null;
        if (isset($regiones)) {
            foreach ($regiones as $region) {
                $opciones[count($opciones)] = array('value' => $region['id'],
                    'texto' => $region['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_REGION);
        $form->addSelect('select', 'sel_region', 'sel_region', $opciones, '', $regionA, '', 'onChange="submit();" required');

        $departamentos = $daoPlaneacion->getDepartamento($regionA, 'dep_nombre');
        $opciones = null;
        if (isset($departamentos)) {
            foreach ($departamentos as $departamento) {
                $opciones[count($opciones)] = array('value' => $departamento['id'],
                    'texto' => $departamento['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_DEPARTAMENTO);
        $form->addSelect('select', 'sel_departamento', 'sel_departamento', $opciones, '', $departamentoA, '', 'onChange="submit();" required');

        $muncipios = $daoPlaneacion->getMunicipio($departamentoA, 'mun_nombre');
        $opciones = null;
        if (isset($muncipios)) {
            foreach ($muncipios as $municipio) {
                $opciones[count($opciones)] = array('value' => $municipio['id'],
                    'texto' => $municipio['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_MUNICIPIO);
        $form->addSelect('select', 'sel_municipios', 'sel_municipios', $opciones, '', $muncipioA, '', ' required');

        $form->addEtiqueta(VALOR_UNITARIO_PRODUCTOS);
        $form->addInputText('text', 'txt_valor_unitario', 'txt_valor_unitario', '19', '19', $valor, null, 'autofocus pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" onkeyup="formatearNumero(this);" required');

        $destinacionesRecursos = $daoBasicas->getBasicas('destinacion_recursos');
        $opciones = null;
        if (isset($destinacionesRecursos)) {
            foreach ($destinacionesRecursos as $destinacionRecursos) {
                $opciones[count($opciones)] = array('value' => $destinacionRecursos->getId(),
                    'texto' => $destinacionRecursos->getDescripcion());
            }
        }

        $form->addEtiqueta(DESTINACION_RECURSOS_ORDEN_PAGO_MUNICIPIO);
        $form->addSelect('select', 'sel_destinacion', 'sel_destinacion', $opciones, '', $destinacionRecursosA, '', ' required');

        $form->addInputButton('submit', 'btn_add', 'btn_add', BTN_ACEPTAR, 'button', 'onclick="cancelarAccion(\'frm_edit_relacion\',\'?mod=' . $modulo . '&niv=' . $niv . '&idOrden=' . $id . '&id_element=' . $id_element . '&task=saveEditRelacion\');"');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_relacion\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task=list\');"');

        $form->writeForm();

        break;

    /**
     * la variable saveAddRelacion, permite almacenar el objeto registro productos en la 
     * base de datos @see \CRelacionMunicipioOrdenPagoData
     */
    case 'saveEditRelacion':
        $id_element = $_REQUEST['id_element'];
        $valor = str_replace(".", "", $_REQUEST['txt_valor_unitario']);
        $municipio = $_REQUEST['sel_municipios'];
        $destinacionRecursos = $_REQUEST['sel_destinacion'];
        $ordenPago = $_REQUEST['idOrden'];

        $relacionMunicipioOrdenPago = new CRelacionMunicipioOrdenPago($id_element, $valor, $destinacionRecursos, $municipio, $ordenPago);
        $r = $daoRelacion->updateRelacionMunicipioOrdenPago($relacionMunicipioOrdenPago);
        $m = ERROR_EDITAR_RELACION_RECURSOS_MUNICIPIO;
        if ($r == 'true') {
            $m = EXITO_EDITAR_RELACION_RECURSOS_MUNICIPIO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list&idOrden=" . $ordenPago);

        break;

    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto registro producto @see \CRegistroProductos
     */
    case 'add':
        $form = new CHtmlForm();
        $id = $_REQUEST['idOrden'];
        $form->setTitle(TITULO_AGREGAR_REGISTRO_PRODUCTOS);
        $form->setId('frm_add_registro_producto');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd&idOrden=' . $id);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('tb_add_producto');

        $form->addEtiqueta(DESCRIPCION_PRODUCTOS);
        $form->addInputText('text', 'txt_descripcion', 'txt_descripcion', '45', '45', null, null, 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"  required');
        $opciones = null;
        $opciones[0] = array('value' => '0', 'texto' => 'Bien');
        $opciones[1] = array('value' => '1', 'texto' => 'Servicio');

        $form->addEtiqueta(TIPO_REGISTRO_PRODUCTOS);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', '', '', ' required');

        $form->addEtiqueta(VALOR_UNITARIO_PRODUCTOS);
        $form->addInputText('text', 'txt_valor_unitario', 'txt_valor_unitario', '19', '19', null, null, 'autofocus pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');

        $form->addEtiqueta(CANTIDAD_PRODUCTOS);
        $form->addInputText('text', 'txt_cantidad', 'txt_cantidad', '5', '5', null, null, 'autofocus pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" onkeyup="formatearNumero(this);" required');

        $familias = $daoBasicas->getBasicas('familias');
        $opciones = null;
        if (isset($familias)) {
            foreach ($familias as $familia) {
                $opciones[count($opciones)] = array('value' => $familia->getId(),
                    'texto' => $familia->getDescripcion());
            }
        }

        $form->addEtiqueta(FAMILIA_PRODUCTOS);
        $form->addSelect('select', 'sel_familia', 'sel_familia', $opciones, '', '', '', ' required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_registro_producto\',\'?mod=' . $modulo . '&niv=' . $niv . '&idOrden=' . $id . '&task=list\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto registro productos en la 
     * base de datos @see \CRegistroProductos
     */
    case 'saveAdd':
        $descripcion = $_REQUEST['txt_descripcion'];
        $valorUnitario = $_REQUEST['txt_valor_unitario'];
        $cantidad = str_replace(".", "", $_REQUEST['txt_cantidad']);
        $servicio = $_REQUEST['sel_tipo'];
        $familia = new CBasica($_REQUEST['sel_familia'], null);
        $ordenPago = new COrdenesdepago($_REQUEST['idOrden'], null, NULL, null, null, null, null, null, NULL, null, null, null, null, null, null, null);

        $registroProductos = new CRegistroProductos(NULL, $descripcion, $valorUnitario, $servicio, $cantidad, $familia, $ordenPago);
        $r = $daoRegistroProductos->insertRegistroProducto($registroProductos);
        $m = ERROR_AGREGAR_REGISTRO_PRODUCTOS;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_REGISTRO_PRODUCTOS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list&idOrden=" . $ordenPago->getId_ordenedepago());

        break;
        
    /**
     * la variable delete, permite hacer la carga del objeto registro producto 
     * y espera confirmacion de eliminarlo @see \CRegistroInversion
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        $idOrden = $_REQUEST['idOrden'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_REGISTRO_PRODUCTOS, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete . '&idOrden=' . $idOrden, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&idOrden=' . $idOrden . '&task=list\'');
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto registro producto 
     * de la base de datos @see \CRegistroInversion
     */
    case 'confirmDelete':
        $idOrden = $_REQUEST['idOrden'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoRegistroProductos->deleteRegistroProducto($id_delete);
        $m = ERROR_BORRAR_PRODUCTOS;
        if ($r == true) {
            $m = EXITO_BORRAR_PRODUCTOS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list&idOrden=" . $idOrden);
        break;

    /**
     * la variable edit, permite hacer la carga del objeto registro producto y espera 
     * confirmacion de edicion @see \CRegistroProductos
     */
    case 'edit':
        $id = $_REQUEST['idOrden'];
        $id_edit = $_REQUEST['id_element'];
        $registroProducto = $daoRegistroProductos->getRegistroProductoById($id_edit);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_REGISTRO_PRODUCTOS);
        $form->setId('frm_add_registro_producto');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&idOrden=' . $id . '&id=' . $registroProducto->getIdRegistroProductos());
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('tb_add_producto');

        $form->addEtiqueta(DESCRIPCION_PRODUCTOS);
        $form->addInputText('text', 'txt_descripcion', 'txt_descripcion', '45', '45', $registroProducto->getDescripcion(), null, 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"  required');
        $opciones = null;
        $opciones[0] = array('value' => '0', 'texto' => 'Bien');
        $opciones[1] = array('value' => '1', 'texto' => 'Servicio');

        $form->addEtiqueta(TIPO_REGISTRO_PRODUCTOS);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', $registroProducto->getServicio(), '', ' required');

        $form->addEtiqueta(VALOR_UNITARIO_PRODUCTOS);
        $form->addInputText('text', 'txt_valor_unitario', 'txt_valor_unitario', '19', '19', $registroProducto->getValorUnitario(), null, 'autofocus pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');

        $form->addEtiqueta(CANTIDAD_PRODUCTOS);
        $form->addInputText('text', 'txt_cantidad', 'txt_cantidad', '5', '5', $registroProducto->getCantidad(), null, 'autofocus pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" onkeyup="formatearNumero(this);" required');

        $familias = $daoBasicas->getBasicas('familias');
        $opciones = null;
        if (isset($familias)) {
            foreach ($familias as $familia) {
                $opciones[count($opciones)] = array('value' => $familia->getId(),
                    'texto' => $familia->getDescripcion());
            }
        }

        $form->addEtiqueta(FAMILIA_PRODUCTOS);
        $form->addSelect('select', 'sel_familia', 'sel_familia', $opciones, '', $registroProducto->getFamilia(), '', ' required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_registro_producto\',\'?mod=' . $modulo . '&niv=' . $niv . '&idOrden=' . $id . '&task=list\');"');

        $form->writeForm();
        break;
    /**
     * la variable saveEdit, permite actualizar el registro bodega en la base 
     * de datos @see \CRegistroProductos
     */
    case 'saveEdit':
        $idRegistro = $_REQUEST['id'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $valorUnitario = $_REQUEST['txt_valor_unitario'];
        $cantidad = str_replace(".", "", $_REQUEST['txt_cantidad']);
        $servicio = $_REQUEST['sel_tipo'];
        $familia = new CBasica($_REQUEST['sel_familia'], null);
        $ordenPago = new COrdenesdepago($_REQUEST['idOrden'], null, NULL, null, null, null, null, null, NULL, null, null, null, null, null, null, null);

        $registroProducto = new CRegistroProductos($idRegistro, $descripcion, $valorUnitario, $servicio, $cantidad, $familia, $ordenPago);
        $r = $daoRegistroProductos->updateRegistroProducto($registroProducto);
        $m = ERROR_EDITAR_REGISTRO_PRODUCTOS;
        if ($r == true) {
            $m = EXITO_EDITAR_REGISTRO_PRODUCTOS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list&idOrden=" . $_REQUEST['idOrden']);

        break;

    /**
     * en caso de que la variable task no este definida carga la página 
     * en construcción
     */
    default:
        include('templates/html/under.html');
        break;
}
?>
