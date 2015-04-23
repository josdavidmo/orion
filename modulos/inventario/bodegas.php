<?php
/**
 * Modulo Bodegas
 * Maneja el modulo bodegas en union con CBodegaData, CBodega, CTipoBodega
 *
 * @see \CBodegaData
 * @see \CTipoBodega
 * @see \CBodegaData
 *
 * @package modulos
 * @subpackage inventarios
 * @author SERTIC SAS
 * @version 2014.09.12
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoBodega = new CBodegaData($db);
$daoRegistroProductos = new CRegistroProductosData($db);
$daoBasicas = new CBasicaData($db);
$daoBeneficiarios = new CBeneficiarioData($db);
$task = $_REQUEST['task'];
$modulo = $_REQUEST['mod'];
$niv = $_REQUEST['niv'];
if (empty($task))
    $task = 'list';

switch ($task) {
    /**
     * la variable list, permite hacer la carga la página con la lista de 
     * objetos bodegas según los parámetros de entrada
     */
    case 'list':
        $form = new CHtmlForm();
        $form->setTitle(TITULO_BODEGA);
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_BODEGA);
        $titulos = array(CODIGO_BODEGA, NOMBRE_BODEGA, TIPO_BODEGA, PADRE_BODEGA);
        $bodegas = $daoBodega->getBodegas();
        $dt->setTitleRow($titulos);
        $dt->setDataRows($bodegas);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=seeRegistroProductos");
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");
        $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=enviar", 'img' => 'enviar.jpg', 'alt' => ALT_ENVIAR);
        $dt->addOtrosLink($otros);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;
    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto bodega @see \CBodega
     */
    case 'add':
        $form = new CHtmlForm();

        $form->setTitle(TITULO_AGREGAR_BODEGA);
        $form->setId('frm_add_bodega');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('tb_add_bodega');

        $form->addEtiqueta(CODIGO_BODEGA);
        $form->addInputText('text', 'txt_codigo', 'txt_codigo', '45', '45', null, null, 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . TITLE_ALFANUMERICO . '"  required');

        $form->addEtiqueta(NOMBRE_BODEGA);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '45', '45', null, null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . TITLE_ALFANUMERICO . '"  required');

        $form->addEtiqueta(TIPO_BODEGA);
        $form->addInputText('text', 'txt_tipoBodega', 'txt_tipoBodega', '45', '45', null, null, 'list="tipoBodega" pattern="' . PATTERN_ALFANUMERICO . '" title="' . TITLE_ALFANUMERICO . '"  onchange="agregarCampoBodega(\'tb_add_bodega\',this)" required');
        $tiposBodegas = $daoBodega->getTiposBodega();
        $centrosDistribucion = $daoBodega->getCentroDistribucion();
        $zonasLogisticas = $daoBodega->getZonasLogisticas();
        ?>
        <datalist id="centrosDistribucion">
            <?php foreach ($centrosDistribucion as $centroDistribucion) { ?>
                <option value="<?= $centroDistribucion->getIdBodega(); ?>"><?= $centroDistribucion->getNombre(); ?></option>   
            <?php } ?>
        </datalist>
        <datalist id="zonasLogisticas">
            <?php foreach ($zonasLogisticas as $zonaLogistica) { ?>
                <option value="<?= $zonaLogistica->getIdBodega(); ?>"><?= $zonaLogistica->getNombre(); ?></option>   
            <?php } ?>
        </datalist>
        <datalist id="tipoBodega">
            <?php foreach ($tiposBodegas as $tipoBodega) { ?>
                <option value="<?= $tipoBodega->getIdTipoBodega(); ?>"><?= $tipoBodega->getDescripcion(); ?></option>   
            <?php } ?>
        </datalist>        
        <?php
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_manual\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto bodega en la 
     * base de datos @see \CBodega
     */
    case 'saveAdd':
        $codigo = $_REQUEST['txt_codigo'];
        $nombre = $_REQUEST['txt_nombre'];
        $tipoBodega = new CTipoBodega($_REQUEST['txt_tipoBodega'], null);
        $bodegaPadre = new CBodega($_REQUEST['txt_bodega_padre'], null, null, null, null);
        $bodega = new CBodega(null, $codigo, $nombre, $tipoBodega, $bodegaPadre);
        $r = $daoBodega->insertBodega($bodega);
        $m = ERROR_AGREGAR_BODEGA;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_BODEGA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");
        break;

    case 'enviar':
        $idBodega = $_REQUEST['id_element'];
        $form = new CHtmlForm();

        $bodega = $daoBodega->getBodegaById($idBodega);
        $productos = $daoRegistroProductos->getProductosByBodega($idBodega);
        if ($bodega->getTipoBodega()->getIdTipoBodega() != '3') {
            $dependencias = $daoBodega->getBodegasByPadre($idBodega);
        } else {
            $dependencias = $daoBeneficiarios->getBeneficiarios();
        }
        if ($productos != null && $dependencias != null) {

            $form->setTitle(TITULO_ENVIAR_PRODUCTOS);
            $form->setId('frm_enviar_producto');
            $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEnviar&idBodega=' . $idBodega);
            $form->setMethod('post');

            $opciones = null;
            if (isset($productos)) {
                foreach ($productos as $producto) {
                    if ($producto['serial'] != NULL) {
                        $opciones[count($opciones)] = array('value' => $producto['idProducto'],
                            'texto' => $producto['serial'] . " " . $producto['descripcion']);
                    }
                }
            }

            $form->addEtiqueta(DISPONIBLES_PRODUCTOS);
            $form->addSelect('select', 'sel_productos[]', 'sel_productos[]', $opciones, '', '', '', ' multiple');

            $opciones = null;
            if (isset($dependencias)) {
                foreach ($dependencias as $dependencia) {
                    if ($bodega->getTipoBodega()->getIdTipoBodega() != '3') {
                        $opciones[count($opciones)] = array('value' => $dependencia['idBodega'],
                            'texto' => $dependencia['codigo'] . " " . $dependencia['nombre']);
                    } else {
                        $opciones[count($opciones)] = array('value' => $dependencia['idBeneficiario'],
                            'texto' => $dependencia['centropoblado'] . " - " . $dependencia['tipo'] . " - " . $dependencia['nombre']);
                    }
                }
            }

            $form->addEtiqueta(ENVIAR_BODEGA);
            $form->addSelect('select', 'sel_bodega', 'sel_bodega', $opciones, '', '', '', '');

            $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
            $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_enviar_producto\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idBodega . '\');"');

            $form->writeForm();
        } else {
            $m = ADVERTENCIA_PRODUCTOS_BODEGA;
            echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");
        }

        break;

    case 'saveEnviar':
        $idBodega = $_REQUEST['idBodega'];
        $dependencia = $daoBodega->getBodegaById($idBodega);
        $idDependencia = $_REQUEST['sel_bodega'];
        $r = TRUE;
        $fechaEnvio = getdate()['year'] . '-' . getdate()['mon'] . '-' . getdate()['mday'];
        $bodega = new CBodega($idDependencia, null, null, null, null);
        $beneficiario = new CBeneficiario($idDependencia, null, NULL, null, null, null, null, null, null, null, null, null, null, null, NULL, null, null, null, NULL, null);
        if ($dependencia->getTipoBodega()->getIdTipoBodega() != '3') {
            $beneficiario->setIdBeneficiario(NULL);
        } else {
            $bodega->setIdBodega(null);
        }
        foreach ($_REQUEST['sel_productos'] as $idProducto) {
            $producto = new CProductos($idProducto, null, null, null, null, null, null);
            $historialProducto = new CHistorialProducto($producto, $bodega, $fechaEnvio, $beneficiario);
            $r = $r && $daoRegistroProductos->insertHistorialProducto($historialProducto);
        }
        $m = FRACASO_ENVIAR_PRODUCTOS;
        if ($r == 'true') {
            $m = EXITO_ENVIAR_PRODUCTOS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");
        break;

    /**
     * la variable delete, permite hacer la carga del objeto bodega 
     * y espera confirmacion de eliminarlo @see \CBodega
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_BODEGA, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1\'');
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto bodega de la 
     * base de datos @see \CBodega
     */
    case 'confirmDelete':
        $id_delete = $_REQUEST['id_element'];
        $r = $daoBodega->deleteBodegaById($id_delete);
        $m = ERROR_BORRAR_BODEGA;
        if ($r == 'true') {
            $m = EXITO_BORRAR_BODEGA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");
        break;

    /**
     * la variable edit, permite hacer la carga del objeto bodega y espera 
     * confirmacion de edicion @see \CBodega
     */
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $bodega = $daoBodega->getBodegaById($id_edit);
        $form = new CHtmlForm();

        $form->setTitle(TITULO_EDITAR_BODEGA);
        $form->setId('frm_add_bodega');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd&id_element=' . $id_edit);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('tb_add_bodega');

        $form->addEtiqueta(CODIGO_BODEGA);
        $form->addInputText('text', 'txt_codigo', 'txt_codigo', '45', '45', $bodega->getCodigo(), null, 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . TITLE_ALFANUMERICO . '"  required');

        $form->addEtiqueta(NOMBRE_BODEGA);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '45', '45', $bodega->getNombre(), null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . TITLE_ALFANUMERICO . '"  required');

        $form->addEtiqueta(TIPO_BODEGA);
        $form->addInputText('text', 'txt_tipoBodega', 'txt_tipoBodega', '1', '1', $bodega->getTipoBodega()->getIdTipoBodega(), null, 'list="tipoBodega" pattern="' . PATTERN_ALFANUMERICO . '" title="' . TITLE_ALFANUMERICO . '"  onchange="agregarCampoBodega(\'tb_add_bodega\',this)" required');

        if ($bodega->getTipoBodega()->getIdTipoBodega() != '1') {
            $label = CENTRO_DISTRIBUCION_BODEGA;
            if ($bodega->getTipoBodega()->getIdTipoBodega() == '3') {
                $label = ZONA_LOGISTICA_BODEGA;
            }
            $form->addEtiqueta($label);
            $form->addInputText('text', 'txt_bodega_padre', 'txt_bodega_padre', '1', '1', $bodega->getTipoBodega()->getIdTipoBodega(), null, 'list="tipoBodega" pattern="' . PATTERN_ALFANUMERICO . '" title="' . TITLE_ALFANUMERICO . '"  onchange="agregarCampoBodega(\'tb_add_bodega\',this)" required');
        }

        $tiposBodegas = $daoBodega->getTiposBodega();
        $centrosDistribucion = $daoBodega->getCentroDistribucion();
        $zonasLogisticas = $daoBodega->getZonasLogisticas();
        ?>
        <datalist id="centrosDistribucion">
            <?php foreach ($centrosDistribucion as $centroDistribucion) { ?>
                <option value="<?= $centroDistribucion->getIdBodega(); ?>"><?= $centroDistribucion->getNombre(); ?></option>   
            <?php } ?>
        </datalist>
        <datalist id="zonasLogisticas">
            <?php foreach ($zonasLogisticas as $zonaLogistica) { ?>
                <option value="<?= $zonaLogistica->getIdBodega(); ?>"><?= $zonaLogistica->getNombre(); ?></option>   
            <?php } ?>
        </datalist>
        <datalist id="tipoBodega">
            <?php foreach ($tiposBodegas as $tipoBodega) { ?>
                <option value="<?= $tipoBodega->getIdTipoBodega(); ?>"><?= $tipoBodega->getDescripcion(); ?></option>   
            <?php } ?>
        </datalist>        
        <?php
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_manual\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveEdit, permite actualizar el objeto bodega en la base 
     * de datos @see \CBodega
     */
    case 'saveEdit':
        $idBodega = $_REQUEST['id_element'];
        $codigo = $_REQUEST['txt_codigo'];
        $nombre = $_REQUEST['txt_nombre'];
        $tipoBodega = new CTipoBodega($_REQUEST['txt_tipoBodega'], null);
        $bodegaPadre = new CBodega($_REQUEST['txt_bodega_padre'], null, null, null, null);
        $bodega = new CBodega($idBodega, $codigo, $nombre, $tipoBodega, $bodegaPadre);
        $r = $daoBodega->updateBodega($bodega);
        $m = ERROR_EDITAR_BODEGA;
        if ($r == 'true') {
            $m = EXITO_EDITAR_BODEGA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");

        break;

    case "seeRegistroProductos":
        $form = new CHtmlForm();
        $form->setTitle(TITULO_REGISTRO_PRODUCTOS);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . "&task=seeRegistroProductos");
        $form->addEtiqueta(NUMERO_ORDEN_PAGO);
        $form->addInputText('text', 'txt_numero_orden', 'txt_numero_orden', '45', '45', $_REQUEST['txt_numero_orden'], null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . TITLE_ALFANUMERICO . '"');
        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'import', 'import', BTN_ATRAS, 'button', 'onclick="location.href=\'?mod=' . $modulo . '&niv=1\'"');
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_REGISTRO_PRODUCTOS);
        $titulos = array(DESCRIPCION_PRODUCTOS, VALOR_UNITARIO_PRODUCTOS,
            CANTIDAD_PRODUCTOS, TIPO_REGISTRO_PRODUCTOS,
            FAMILIA_PRODUCTOS, ORDEN_PAGO_PRODUCTOS, VALOR_TOTAL_PRODUCTOS);
        $criterio = "rp.servicio = 0";
        if (isset($_REQUEST['txt_numero_orden'])) {
            $criterio .= " AND op.Numero_Orden_Pago LIKE '" . $_REQUEST['txt_numero_orden'] . "%'";
        }
        $registroProductos = $daoRegistroProductos->getRegistroProductos($criterio);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($registroProductos);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=see");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->setFormatRow(array(null, array(2, ',', '.'), null, null, null, null, array(2, ',', '.')));
        $dt->setSumColumns(array(3,7));
        $dt->writeDataTable($niv);
        break;

    case "see":
        $idRegistroProducto = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_PRODUCTOS);
        $form->setAction("?mod=" . $modulo . "&niv=" . $niv . "&task=seeRegistroProductos");
        $form->setMethod("post");
        $form->setId("filtro_producto");
        $form->setOptions("autoClean", false);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->addInputButton('button', 'export', 'export', BTN_EXPORTAR, 'button', 'onclick="location.href=\'modulos/inventario/productos_excel.php?idRegistroProducto=' . $idRegistroProducto . '\'"');
        $form->addInputButton('button', 'import', 'import', BTN_IMPORTAR, 'button', 'onclick="location.href=\'?mod=' . $modulo . '&niv=1&task=cargaMasiva&idRegistroProducto=' . $idRegistroProducto . '\'"');
        $form->addInputButton('button', 'save', 'save', BTN_GUARDAR, 'button', 'onclick="guardarCambiosProductos(\'tb_productos\',\'' . $modulo . '\',\'' . $niv . '\',\'' . $idRegistroProducto . '\')"');
        $form->writeForm();
        $criterio = "p.idRegistroProducto = $idRegistroProducto";
        $productos = $daoRegistroProductos->getProductos($criterio);
        $dt = new CHtmlDataTableEditable();
        $dt->setId('tb_productos');
        $dt->setTitleTable(TITULO_PRODUCTOS);
        $titulos = array(SERIAL_PRODUCTOS, FAMILIA_PRODUCTOS, DESCRIPCION_REGISTRO_PRODUCTOS,
            DESCRIPCION_PRODUCTOS, ESTADO_PRODUCTOS, FECHA_RECEPCION_PRODUCTOS);
        $dt->setTablasAsociadas(array(NULL, NULL, NULL, NULL, 'estadoproducto', NULL));
        $dt->setDb($db);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($productos);
        $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=monitoreo&idBodega=" . $idBodega, 'img' => 'cubrimiento.gif', 'alt' => ALT_ENVIAR);
        $dt->addOtrosLink($otros);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;

    case 'monitoreo':
        $idBodega = $_REQUEST['idBodega'];
        $idProducto = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setTitle(MONITOREO_PRODUCTOS);
        $form->setAction("?mod=" . $modulo . "&niv=" . $niv . "&task=see&id_element=" . $idBodega);
        $form->setMethod("post");
        $form->setId("filtro_producto");
        $form->setOptions("autoClean", false);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        $historialProducto = $daoRegistroProductos->getHistorialProducto($idProducto);
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_PRODUCTOS);
        $titulos = array(BODEGA_PRODUCTO, FECHA_RECEPCION_PRODUCTOS);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($historialProducto);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;

    case 'saveAll':
        $idRegistroProducto = $_REQUEST['idRegistroProducto'];
        $datos = $_REQUEST['datos'];
        $r = true;
        $filas = explode(";", $datos);
        for ($i = 0; $i < count($filas) - 1; $i++) {
            $columnas = explode(",", $filas[$i]);
            $estadoProducto = new CBasica($columnas[2], null);
            $producto = new CProductos($columnas[4], $columnas[0], null, $columnas[1], $estadoProducto, $columnas[3]);
            $r = $daoRegistroProductos->updateProducto($producto);
        }
        $m = ERROR_EDITAR_PRODUCTOS;
        if ($r == "true") {
            $m = EXITO_EDITAR_PRODUCTOS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $idRegistroProducto);
        break;

    case 'cargaMasiva':
        $idRegistroProducto = $_REQUEST['idRegistroProducto'];
        $form = new CHtmlForm();
        $form->setTitle(PRODUCTOS_CARGA_MASIVA);
        $form->setId('frm_carga');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveCarga&idRegistroProducto=' . $idRegistroProducto);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(SOPORTE_CAMBIOSYTRANSFERENCIAS);
        $form->addInputFile('file', 'file_carga', 'file_carga', 25, 'file', 'required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_carga\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idRegistroProducto . '&task=see\');"');
        $form->writeForm();
        break;

    case 'saveCarga':
        $idRegistroProducto = $_REQUEST['idRegistroProducto'];
        $file = $_FILES['file_carga'];
        $r = $daoRegistroProductos->cargaMasiva($file);
        $m = ERROR_GUARDAR_BENEFICIARIOS;
        if ($r) {
            $m = EXITO_GUARDAR_BENEFICIARIOS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $idRegistroProducto);
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
