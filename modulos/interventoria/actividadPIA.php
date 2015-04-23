<?php

defined('_VALID_PRY') or die('Restricted access');

$operador = OPERADOR_DEFECTO;
$daoActividadPIA = new CActividadPIAData($db);
$task = $_REQUEST['task'];

if (empty($task)) {
    $task = 'list';
}
switch ($task) {
    //Opcion Lista
    case 'list':
        $descripcion = null;
        if (isset($_REQUEST['txt_descripcion'])) {
            $descripcion = $_REQUEST['txt_descripcion'];
        }
        // Inicio Formulario para Filtros.
        $form = new CHtmlForm();
        $form->setTitle(TABLA_ACTIVIDADES);
        $form->setId('frm_list_actividades');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->addEtiqueta(ACTIVIDADES_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 500, 2, $descripcion, '', '');
        $form->addInputButton('button', 'btn_consultar', 'btn_consultar', BTN_ACEPTAR, 'button', 'onClick=consultar_actividades();');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', ACTIVIDADES_EXPORTAR, 'button', 'onClick=exportar_excel_actividadPIA();');
        $form->writeForm();
        //Inicio Tabla de Informacion.
        $criterio = null;
        if ($descripcion) {
            $criterio = 'act_descripcion LIKE \'' . $descripcion . '%\'';
        }
        $actividades = $daoActividadPIA->getActividadPIA($criterio);
        $dt = new CHtmlDataTable();
        $titulos = array(ACTIVIDADES_DESCRIPCION, ACTIVIDADES_MONTO);
        $dt->setDataRows($actividades);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_ACTIVIDADES);
        $dt->setFormatRow(array(null,array(0, ',', '.')));
        $dt->setSumColumns(array(2));
        //OPCIONES DE GESTIÓN
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=delete");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=add");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;


    case 'add':
        //Inicio de Formulario
        $html = new CHtml('');
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_ACTIVIDADES);
        $form->setId('frm_add_actividades');
        $form->setMethod('post');
        $urlEnviar = str_replace("add", "saveAdd", $_SERVER['REQUEST_URI']);
        $form->setAction($urlEnviar);
        $form->setClassEtiquetas('td_label');
        $form->addEtiqueta(ACTIVIDADES_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 500, 2, '', '', 'pattern=\'' . PATTERN_ALFANUMERICO . '\' title=\''
                . $html->traducirTildes(TITLE_ALFANUMERICO)
                . '\' autofocus required');
        $form->addError('error_descripcion', ERROR_ACTIVIDADES_DESCRIPCION);
        $form->addEtiqueta(ACTIVIDADES_MONTO);
        $form->addInputText('text', 'txt_monto', 'txt_monto', 19, 19, '', '', 'onkeyup=\'formatearNumero(this);\' pattern=\''
                . PATTERN_NUMEROS_FINANCIEROS . '\' title=\''
                . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS)
                . '\' required');
        $form->addError('error_monto', ERROR_ACTIVIDADES_MONTO);
        $form->addInputButton('submit', 'btn_consultar', 'btn_consultar', BTN_ACEPTAR, 'button', '');
        $urlCancelar = str_replace("add", "list", $_SERVER['REQUEST_URI']);
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', BTN_CANCELAR, 'button', 'onClick=\'location.href="' . $urlCancelar . '"\'');
        $form->writeForm();
        break;

    //Almacena la actividad
    case 'saveAdd':
        $html->CHtml('');
        $descripcion = $_REQUEST['txt_descripcion'];
        $monto = str_replace(".", "", $_REQUEST['txt_monto']);
        $actividad = new CActividadPIA('', $descripcion, $monto);
        $r = $daoActividadPIA->insertActividadPIA($actividad);
        $mens = $html->traducirTildes(ACTIVIDADES_AGREGADA);
        if ($r == false) {
            $mens = $html->traducirTildes(ERROR_ADD_ACTIVIDADES);
        }
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=" . $niv . "&task=list");
        break;

    //Elimina la actividad
    case 'delete':
        $id_act = $_REQUEST['id_element'];
        $url = $_SERVER['REQUEST_URI'];
        $urlAceptar = str_replace("delete", "confirmDelete", $url);
        $urlCancelar = str_replace("delete", "list", $url);
        echo $html->generaAdvertencia(ACTIVIDADES_MSG_BORRADO, $urlAceptar, 'onclick=location.href=\''.$urlCancelar.'\'');
        break;
    /*
     * Variable confirmDelete, elimina una ActividadPIA
     */
    case 'confirmDelete':
        $id_act = $_REQUEST['id_element'];
        $r = $daoActividadPIA->deleteActividadPIA($id_act);
        $mens = ACTIVIDAD_BORRADO;
        if (r == false) {
            $mens = ERROR_DELETE_ACTIVIDADES;
        }
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=" . $niv . "&task=list");
        break;
    /*
     * Variable edit, presenta el formulario para editar una ActividadPIA
     */
    case 'edit':
        $id_act = $_REQUEST['id_element'];
        $actividadpia = $daoActividadPIA->getActividadPIAById($id_act);

        //Inicio de Formulario
        $html = new CHtml('');
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_ACTIVIDADES);
        $form->setId('frm_add_actividades');
        $form->setMethod('post');
        $urlEnviar = str_replace("edit", "saveEdit", $_SERVER['REQUEST_URI']);
        $form->setAction($urlEnviar);
        $form->setClassEtiquetas('td_label');
        $form->addEtiqueta(ACTIVIDADES_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 500, 2, $actividadpia->getDescripcion(), '', 'pattern=\'' . PATTERN_ALFANUMERICO . '\' title=\''
                . $html->traducirTildes(TITLE_ALFANUMERICO)
                . '\' autofocus required');
        $form->addError('error_descripcion', ERROR_ACTIVIDADES_DESCRIPCION);
        $form->addEtiqueta(ACTIVIDADES_MONTO);
        $form->addInputText('text', 'txt_monto', 'txt_monto', 19, 19, $actividadpia->getMonto(), '', 'onkeyup=\'formatearNumero(this);\' pattern=\''
                . PATTERN_NUMEROS_FINANCIEROS . '\' title=\''
                . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS)
                . '\' required');
        $form->addError('error_monto', ERROR_ACTIVIDADES_MONTO);
        $form->addInputButton('submit', 'btn_consultar', 'btn_consultar', BTN_ACEPTAR, 'button', '');
        $urlCancelar = str_replace("edit", "list", $_SERVER['REQUEST_URI']);
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', BTN_CANCELAR, 'button', 'onClick=\'location.href="' . $urlCancelar . '"\'');
        $form->writeForm();
        break;
    
    case 'saveEdit':
        $id_act = $_REQUEST['id_element'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $monto = str_replace(".", "", $_REQUEST['txt_monto']);
        $actividad = new CActividadPIA($id, $descripcion, $monto);
        $r = $daoActividadPIA->updateActividadPIA($id_act, $actividad);
        $mens = $html->traducirTildes(ACTIVIDADES_EDITADA);
        if ($r == false) {
            $mens = $html->traducirTildes(ERROR_EDIT_ACTIVIDADES);
        }
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=" . $niv . "&task=list");
        break;
    
    default:
        include('templates/html/under.html');
        break;
}
?>

