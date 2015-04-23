<?php


defined('_VALID_PRY') or die('Restricted access');

$operador = OPERADOR_DEFECTO;
$planData = new CGeneradorPlaneacionData($db);
$task = $_REQUEST['task'];

if (empty($task)) {
    $task = 'list';
}
switch ($task) {
    /**
     * La variable list, permite hacer la carga la página con la lista de 
     * objetos PLANEACION según los parámetros de entrada.
     */
    case 'list':
        //Variables
        
        $region = $_REQUEST['txt_region'];
        $departamento = $_REQUEST['txt_departamento'];
        $municipio = $_REQUEST['txt_municipio'];
        
        //-------------------------------criterios---------------------------
        $criterio = " 1";

        if (isset($region) && $region != -1 && $region != '') {
            $criterio .= " and r.der_id = " . $region;
        }
        if (isset($departamento) && $departamento != -1 && $departamento != '') {
            $criterio .= " and d.dep_id = " . $departamento;
        }
        if (isset($municipio) && $municipio != -1 && $municipio != '') {
            $criterio .= " and m.mun_id = " . $municipio;
        }
        
        //Inicio Formulario
        $form = new CHtmlForm();
        $form->setTitle(TABLA_PLANEACION);
        $form->setId('frm_list_centros');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        //Regiones
        
        $form->addEtiqueta(PLANEACION_REGION);
        $opciones = null;
        $regiones = $planData->getRegiones(' der_nombre');
        if (isset($regiones)) {
            foreach ($regiones as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_region', 'txt_region', $opciones, PLANEACION_REGION, $region, '', 'onChange=submit();');

        //Departamentos
        $opciones = null;
        $form->addEtiqueta(PLANEACION_DEPARTAMENTO);
        $departamentos = $planData->getDepartamento($region, ' dep_nombre');
        if (isset($departamentos)) {
            foreach ($departamentos as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_departamento', 'txt_departamento', $opciones, PLANEACION_DEPARTAMENTO, $departamento, '', 'onChange=submit();');

        //Municipios
//        $form->addEtiqueta(PLANEACION_MUNICIPIO);
//        $opciones = null;
//        $municipios = $planData->getMunicipio($departamento, ' mun_nombre');
//        if (isset($municipios)) {
//            foreach ($municipios as $t) {
//                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
//            }
//        }
//        $form->addSelect('select', 'txt_municipio', 'txt_municipio', $opciones, PLANEACION_MUNICIPIO, $municipio, '', 'onChange=submit();');

        //Botones Formulario
        $form->addInputText('hidden', 'txt_criterio', 'txt_criterio', '5', '5', '', '', '');

        $form->writeForm();
        //Carga filtro de planeaciones
        $planeaciones = $planData->getMunicipios($criterio);
        //Inicio Tabla
        $dt = new CHtmlDataTable();
        $titulos = array(
            //NOMBRE_CENTRO_POBLADO, 
            PLANEACION_MUNICIPIO, PLANEACION_DEPARTAMENTO, PLANEACION_REGION );
        $dt->setDataRows($planeaciones);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_PLANEACION);

        //OPCIONES DE GESTIÓN
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=centros");
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=planeacionMunicipio");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;
        
    case 'centros':
        //Variables
        
        $municipio = $_REQUEST['id_element'];
        
        //-------------------------------criterios---------------------------
        $criterio = " m.mun_id = $municipio";

        
        //Inicio Formulario
        $form = new CHtmlForm();
        $form->setTitle(TABLA_PLANEACION);
        $form->setId('frm_list_centros');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');


        //Botones Formulario
        $form->addInputText('hidden', 'txt_criterio', 'txt_criterio', '5', '5', '', '', '');
        $form->addInputButton('button', 'atras', 'atras', BTN_ATRAS, '', 
                'onclick=location.href="?mod=' . $modulo . '&niv=' . $niv . '&task=list"');
        $form->writeForm();
        //Carga filtro de planeaciones
        $planeaciones = $planData->getCentroPoblado($criterio);
        //Inicio Tabla
        $dt = new CHtmlDataTable();
        $titulos = array(NOMBRE_CENTRO_POBLADO, PLANEACION_MUNICIPIO
            //, PLANEACION_DEPARTAMENTO, PLANEACION_REGION 
            );
        $dt->setDataRows($planeaciones);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_PLANEACION);

        //OPCIONES DE GESTIÓN
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=beneficiarios");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;
    
    case 'beneficiarios':
        $id = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_BENEFICIARIOS);
        $form->setId('frm_list_beneficiario');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->addInputText('hidden', 'txt_centro_poblado', 'txt_centro_poblado', 0, 0, $id, '', '');
        $form->writeForm();
        $criterio = " idCentroPoblado = $id";
        $planeaciones = $planData->getBeneficiarios($criterio);
        $dt = new CHtmlDataTable();
        $titulos = array(NOMBRE_BENEFICIARIO);
        $dt->setDataRows($planeaciones);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_PLANEACION);

        //OPCIONES DE GESTIÓN
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=planeacion&CP=$id");
        
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        
        $form = new CHtmlForm();
        $form->setId('frm_list_beneficiario');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setOptions('autoClean', false);
        $form->addInputButton('button', 'cancelar', 'cancelar', BTN_ATRAS, 'button',
                'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '&task=list"');
        $form->writeForm();
        
        break;
    
    case 'planeacion':
        $beneficiario   = $_REQUEST['id_element'];
        $centro_poblado =$_REQUEST['CP'];
        $form = new CHtmlForm();
        $form->addInputText('hidden', 'txt_centro_poblado', 'txt_centro_poblado', 0, 0, $centro_poblado, '', '');
        $form->addInputText('hidden', 'txt_beneficiario', 'txt_beneficiario', 0, 0, $beneficiario, '', '');
        $form->setTitle(PLANEACION." para ".$planData->getNombreBeneficiario($beneficiario));
        $form->setId('frm_list_planeacion');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $criterio = "ben_id = $beneficiario";
        $form->writeForm();
        $planeaciones = $planData->getPlaneacion($criterio);
        $dt = new CHtmlDataTable();
        $titulos = array(INSTRUMENTO, PLANEACION_FECHA_INICIO,
            PLANEACION_FECHA_FIN, PLANEACION_NUMERO_ENCUESTAS, USUARIO_LOGIN);
        $dt->setDataRows($planeaciones);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_PLANEACION);

        //OPCIONES DE GESTIÓN
        
        //$dt->setEditLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=editPlaneacion&ben=$beneficiario&CP=$centro_poblado");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=deletePlaneacion&ben=$beneficiario&CP=$centro_poblado");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=addPlaneacion&ben=$beneficiario&CP=$centro_poblado");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable(1);
        
        $form = new CHtmlForm();
        $form->setId('frm_list_planeacion');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setOptions('autoClean', false);
        $form->addInputButton('button', 'cancelar', 'cancelar', BTN_ATRAS, 'button',
                'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '&task=beneficiarios&id_element='.$centro_poblado.'"');
        $form->writeForm();
        break;
    
    case 'planeacionMunicipio':
        $municipio   = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->addInputText('hidden', 'txt_beneficiario', 'txt_beneficiario', 0, 0, $municipio, '', '');
        $form->setTitle(PLANEACION." para ".$planData->getNombreMunicipio($municipio)."(Municipio)");
        $form->setId('frm_list_planeacion_municipio');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $criterio = " mun_id = $municipio";
        $form->writeForm();
        $planeaciones = $planData->getPlaneacion($criterio);
        $dt = new CHtmlDataTable();
        $titulos = array(INSTRUMENTO, PLANEACION_FECHA_INICIO,
            PLANEACION_FECHA_FIN, PLANEACION_NUMERO_ENCUESTAS, USUARIO_LOGIN);
        $dt->setDataRows($planeaciones);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_PLANEACION);

        //OPCIONES DE GESTIÓN
        
        //$dt->setEditLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=editPlaneacionMunicipio&mun=$municipio");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=deletePlaneacionMunicipio&mun=$municipio");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=addPlaneacionMunicipio&mun=$municipio");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        
        $form = new CHtmlForm();
        $form->setId('frm_list_planeacion');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setOptions('autoClean', false);
        $form->addInputButton('button', 'cancelar', 'cancelar', BTN_ATRAS, 'button',
                'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '&task=list"');
        $form->writeForm();
        break;
    
    case 'addPlaneacion':
        $beneficiario   = $_REQUEST['ben'];
        $centro_poblado =$_REQUEST['CP'];
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(PLANEACION);
        $form->setId('frm_add_planeacion');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=savePlaneacion');
        
        $instrumentosConsulta = $planData->getInstrumentos(' nombre');
        $instrumentos = null;
        if (isset($instrumentosConsulta)) {
            foreach ($instrumentosConsulta as $c) {
                $instrumentos[count($instrumentos)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        $usuariosConsulta = $planData->getUsuarios(' usu_nombre');
        $usuarios= null;
        if (isset($usuariosConsulta)) {
            foreach ($usuariosConsulta as $c) {
                $usuarios[count($usuarios)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        
        $form->addInputText('hidden', 'ben', 'ben', '', '', $beneficiario, '', '');
        $form->addInputText('hidden', 'CP', 'CP', '', '', $centro_poblado, '', '');
        
        $form->addEtiqueta(INSTRUMENTO);
        $form->addSelect('select', 'sel_ins', 'sel_ins', $instrumentos, '', '', '', ' required');
        
        $form->addEtiqueta(PLANEACION_FECHA_INICIO);
        $form->addInputDate('date', 'txt_inicio', 'txt_inicio', '', '%Y-%m-%d', '18', '18', '', 'pattern="' . 
                PATTERN_FECHA. '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PLANEACION_FECHA_FIN);
        $form->addInputDate('date', 'txt_fin', 'txt_fin', '', '%Y-%m-%d', '18', '18', '', 'pattern="' . 
                PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(PLANEACION_NUMERO_ENCUESTAS);
        $form->addInputText('text', 'txt_numero', 'txt_numero', 25, 25, '','',' pattern="' 
                . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" required');
        
        $form->addEtiqueta(USUARIO_LOGIN);
        $form->addSelect('select', 'sel_usu', 'sel_usu', $usuarios, '', '', '', ' required');

        $form->addInputButton('submit', 'ok', 'ok', BOTON_INSERTAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button',
                'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=planeacion&id_element=$beneficiario&CP=$centro_poblado\"");
        $form->writeForm();
        break;
        
    case 'addPlaneacionMunicipio':
        $municipio   = $_REQUEST['mun'];
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(PLANEACION);
        $form->setId('frm_add_planeacion');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=savePlaneacionMunicipio');
        
        $instrumentosConsulta = $planData->getInstrumentos(' nombre');
        $instrumentos = null;
        if (isset($instrumentosConsulta)) {
            foreach ($instrumentosConsulta as $c) {
                $instrumentos[count($instrumentos)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        $usuariosConsulta = $planData->getUsuarios(' usu_id');
        $usuarios= null;
        if (isset($usuariosConsulta)) {
            foreach ($usuariosConsulta as $c) {
                $usuarios[count($usuarios)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        
        $form->addInputText('hidden', 'mun', 'mun', '', '', $municipio, '', '');
        
        $form->addEtiqueta(INSTRUMENTO);
        $form->addSelect('select', 'sel_ins', 'sel_ins', $instrumentos, '', '', '', ' required');
        
        $form->addEtiqueta(PLANEACION_FECHA_INICIO);
        $form->addInputDate('date', 'txt_inicio', 'txt_inicio', '', '%Y-%m-%d', '18', '18', '', 'pattern="' . 
                PATTERN_FECHA. '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PLANEACION_FECHA_FIN);
        $form->addInputDate('date', 'txt_fin', 'txt_fin', '', '%Y-%m-%d', '18', '18', '', 'pattern="' . 
                PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(PLANEACION_NUMERO_ENCUESTAS);
        $form->addInputText('text', 'txt_numero', 'txt_numero', 25, 25, '','',' pattern="' 
                . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" required');
        
        $form->addEtiqueta(USUARIO_LOGIN);
        $form->addSelect('select', 'sel_usu', 'sel_usu', $usuarios, '', '', '', ' required');

        $form->addInputButton('submit', 'ok', 'ok', BOTON_INSERTAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button',
                'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=planeacionMunicipio&id_element=$municipio\"");
        $form->writeForm();
        break;
        
    case 'savePlaneacion':
        $beneficiario   = $_REQUEST['ben'];
        $centro_poblado =$_REQUEST['CP'];

        $instrumento = $_REQUEST['sel_ins'];
        $usuario = $_REQUEST['sel_usu'];
        $inicio = $_REQUEST['txt_inicio'];
        $fin = $_REQUEST['txt_fin'];
        $numero = $_REQUEST['txt_numero'];
        
        $r = $planData->insertPlaneacion($beneficiario, $instrumento, $inicio, $fin, $numero, $usuario);
        if($r=='true'){
            $m=PLANEACION_AGREGADA;
        }else{
            $m=ERROR_ADD_PLANEACION;
        }
        
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=" . $niv . "&task=planeacion&id_element=$beneficiario&CP=$centro_poblado");
        break;
        
    case 'savePlaneacionMunicipio':
        $municipio   = $_REQUEST['mun'];

        $instrumento = $_REQUEST['sel_ins'];
        $usuario = $_REQUEST['sel_usu'];
        $inicio = $_REQUEST['txt_inicio'];
        $fin = $_REQUEST['txt_fin'];
        $numero = $_REQUEST['txt_numero'];
        
        $r = $planData->insertPlaneacionMun($municipio, $instrumento, $inicio, $fin, $numero, $usuario);
        if($r=='true'){
            $m=PLANEACION_AGREGADA;
        }else{
            $m=ERROR_ADD_PLANEACION;
        }
        
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=" . $niv . "&task=planeacionMunicipio&id_element=$municipio");
        break;
        
    case 'editPlaneacion':
        $beneficiario   = $_REQUEST['ben'];
        $centro_poblado =$_REQUEST['CP'];
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(PLANEACION);
        $form->setId('frm_add_planeacion');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=savePlaneacion');
        
        $instrumentosConsulta = $planData->getInstrumentos(' nombre');
        $instrumentos = null;
        if (isset($instrumentosConsulta)) {
            foreach ($instrumentosConsulta as $c) {
                $instrumentos[count($instrumentos)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        $usuariosConsulta = $planData->getUsuarios(' usu_id');
        $usuarios= null;
        if (isset($usuariosConsulta)) {
            foreach ($usuariosConsulta as $c) {
                $usuarios[count($usuarios)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        
        $form->addInputText('hidden', 'ben', 'ben', '', '', $beneficiario, '', '');
        $form->addInputText('hidden', 'CP', 'CP', '', '', $centro_poblado, '', '');
        
        $form->addEtiqueta(INSTRUMENTO);
        $form->addSelect('select', 'sel_ins', 'sel_ins', $instrumentos, '', '', '', ' required');
        
        $form->addEtiqueta(PLANEACION_FECHA_INICIO);
        $form->addInputDate('date', 'txt_inicio', 'txt_inicio', '', '%Y-%m-%d', '18', '18', '', 'pattern="' . 
                PATTERN_FECHA. '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PLANEACION_FECHA_FIN);
        $form->addInputDate('date', 'txt_fin', 'txt_fin', '', '%Y-%m-%d', '18', '18', '', 'pattern="' . 
                PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(PLANEACION_NUMERO_ENCUESTAS);
        $form->addInputText('text', 'txt_numero', 'txt_numero', 25, 25, '','',' pattern="' 
                . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" required');
        
        $form->addEtiqueta(USUARIO_LOGIN);
        $form->addSelect('select', 'sel_usu', 'sel_usu', $usuarios, '', '', '', ' required');

        $form->addInputButton('submit', 'ok', 'ok', BOTON_INSERTAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button',
                'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=planeacion&id_element=$beneficiario&CP=$centro_poblado\"");
        $form->writeForm();
        break;
        
    case 'editPlaneacionMunicipio':
        $beneficiario   = $_REQUEST['ben'];
        $centro_poblado =$_REQUEST['CP'];
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(PLANEACION);
        $form->setId('frm_add_planeacion');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=savePlaneacion');
        
        $instrumentosConsulta = $planData->getInstrumentos(' nombre');
        $instrumentos = null;
        if (isset($instrumentosConsulta)) {
            foreach ($instrumentosConsulta as $c) {
                $instrumentos[count($instrumentos)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        $usuariosConsulta = $planData->getUsuarios(' usu_id');
        $usuarios= null;
        if (isset($usuariosConsulta)) {
            foreach ($usuariosConsulta as $c) {
                $usuarios[count($usuarios)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        
        $form->addInputText('hidden', 'ben', 'ben', '', '', $beneficiario, '', '');
        $form->addInputText('hidden', 'CP', 'CP', '', '', $centro_poblado, '', '');
        
        $form->addEtiqueta(INSTRUMENTO);
        $form->addSelect('select', 'sel_ins', 'sel_ins', $instrumentos, '', '', '', ' required');
        
        $form->addEtiqueta(PLANEACION_FECHA_INICIO);
        $form->addInputDate('date', 'txt_inicio', 'txt_inicio', '', '%Y-%m-%d', '18', '18', '', 'pattern="' . 
                PATTERN_FECHA. '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PLANEACION_FECHA_FIN);
        $form->addInputDate('date', 'txt_fin', 'txt_fin', '', '%Y-%m-%d', '18', '18', '', 'pattern="' . 
                PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(PLANEACION_NUMERO_ENCUESTAS);
        $form->addInputText('text', 'txt_numero', 'txt_numero', 25, 25, '','',' pattern="' 
                . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" required');
        
        $form->addEtiqueta(USUARIO_LOGIN);
        $form->addSelect('select', 'sel_usu', 'sel_usu', $usuarios, '', '', '', ' required');

        $form->addInputButton('submit', 'ok', 'ok', BOTON_INSERTAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button',
                'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=planeacion&id_element=$beneficiario&CP=$centro_poblado\"");
        $form->writeForm();
        break;
        
    case 'deletePlaneacion':
        $beneficiario   = $_REQUEST['ben'];
        $centro_poblado =$_REQUEST['CP'];
        $pla_id = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(PLANEACION_MSG_BORRADO, '?mod=' . $modulo . '&niv='
                . $niv . "&task=confirmDelete&id_element=$pla_id&ben=$beneficiario&CP=$centro_poblado", '"onClick=location.href="?mod='.$modulo.'&niv='.$niv);
        break;
    
    case 'confirmDelete':
        $pla_id = $_REQUEST['id_element'];
        $beneficiario   = $_REQUEST['ben'];
        $centro_poblado =$_REQUEST['CP'];
        $planData->deleteEncuestas($pla_id);
        $r = $planData->deletePlaneacion($pla_id);
        if($r=='true'){
            $mens = PLANEACION_BORRADO;
        }else{
            $mens = ERROR_DE_PLANEACION;
        }
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=1&task=planeacion&id_element=$beneficiario&CP=$centro_poblado");
        break;
    
    case 'deletePlaneacionMunicipio':
        $municipio   = $_REQUEST['mun'];
        $pla_id = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(PLANEACION_MSG_BORRADO, '?mod=' . $modulo . '&niv='
                . $niv . "&task=confirmDeleteMunicipio&id_element=$pla_id&mun=$municipio", '"onClick=location.href="?mod='.$modulo.'&niv='.$niv);
        break;
    
    case 'confirmDeleteMunicipio':
        $pla_id = $_REQUEST['id_element'];
        $municipio= $_REQUEST['mun'];
        
        $planData->deleteEncuestas($pla_id);
        $r = $planData->deletePlaneacion($pla_id);
        
        if($r=='true'){
            $mens = PLANEACION_BORRADO;
        }else{
            $mens = ERROR_DE_PLANEACION;
        }
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=1&task=planeacionMunicipio&id_element=$municipio");
        break;
}
?>
