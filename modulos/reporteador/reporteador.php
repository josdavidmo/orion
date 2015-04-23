<?php

/**
 * PNCAV
 *
 * <ul>
 * <li> Redcom Ltda <www.redcom.com.co></li>
 * <li> Proyecto PNCAV</li>
 * </ul>
 */
/**
 * Modulo Incidencias
 * maneja el modulo INCIDENCIAS en union con CIncidencia y CIncidenciaData
 *
 * @see CIncidencia
 * @see CIncidenciaData
 *
 * @package  modulos
 * @subpackage incidencias
 * @author Redcom Ltda
 * @version 2014.09.07
 * @copyright SERTIC - MINTICS
 */

defined('_VALID_PRY') or die('Restricted access');

$conData = new CConsultaData($db);

if (empty($_REQUEST['task'])){
    $task = 'list';
    $_SESSION['consulta']=null;
    $_SESSION['id_reporte']=null;
}else{
    $task = $_REQUEST['task'];
}

if($task=='list') $_SESSION['consulta']=null;

$consultaSerial = $_SESSION['consulta'];
if (empty($consultaSerial)){
    $consulta = new CConsulta();
}else{
    $consulta = unserialize($consultaSerial);
}



switch($task){
    
    case 'list':
        $elementosReportes = $conData->getReportes("1");
        $titulos = Array(NOMBRE_REPORTE,OPCION_NIVEL);
        $dt = new CHtmlDataTable();
        $dt->setDataRows($elementosReportes);
        $dt->setTitleRow($titulos);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->setTitleTable(CAMPO_REPORTE);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=consulta");
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editconsulta");
        $dt->setSeeLink("?mod=consultarReporte&niv=" . $niv . "");
        $dt->writeDataTable($niv);
        break;
    
    case 'editconsulta':
        $id_reporte=$_REQUEST['id_element'];
        $_SESSION['id_reporte'] = $id_reporte;
        $reporte = $conData->getReporteById($id_reporte);
        $consulta=  unserialize($reporte['consulta']);
        
        if(!empty($_REQUEST['num_columnas'])){
            $cont=$_REQUEST['num_columnas'];
            $tabla = $_REQUEST['sel_tabla'];
            for($i=0;$i<$cont;$i++){
                $sql="";
                $funcion = $_REQUEST[$i."_funcion"];
                $detalle = $_REQUEST[$i."_detalle"];
                $alias = $_REQUEST[$i."_alias"];
                $columna = $_REQUEST[$i."_columna"];
                if($funcion!=-1){
                    if($detalle!=''){
                        $sql=$detalle;
                    }else{
                        $detalle = $conData->getDetalleFuncion($funcion);
                        $sql=  str_replace("$#", $columna, $detalle);
                    }
                }else{
                    $sql=$columna;
                }
                if($alias!=''){
                    $sql.=" ".SEUDONIMO_TABLA." ".$alias;
                }
                $consulta->addCampo($sql);
            }
            $consulta->addTabla($tabla);
        }
        //----------------------------------------------------------------------
        
        //-----------------------AGREGAR LINKS----------------------------------
        if(!empty($_REQUEST['sel_columnas_llave'])){
            $tabla = $_REQUEST['sel_tabla'];
            $columnaPrincipal = $_REQUEST['sel_columnas'];
            $tablaLlave = $_REQUEST['sel_tabla_llave'];
            $columnaLlave = $_REQUEST['sel_columnas_llave'];
            $sql = "$tabla,$tablaLlave,$tabla.$columnaPrincipal=$tablaLlave.$columnaLlave";
            $consulta->addJoin($sql);
        }
        //----------------------------------------------------------------------
        
        //-----------------------AGREGAR CONDICIONES----------------------------
        if(!empty($_REQUEST['txt_valor_cond'])){
            $tabla          = $_REQUEST['sel_tabla'];
            $columnaRef     = $_REQUEST['sel_columnas'];
            $operador       = $_REQUEST['sel_operador'];
            $valorCondicion = $_REQUEST['txt_valor_cond'];
            $sql="$tabla.$columnaRef $operador '$valorCondicion'";
            $consulta->addCondicion($sql);
        }
        //----------------------------------------------------------------------
        
        //-----------------------AGREGAR OTROS----------------------------------
        if(!empty($_REQUEST['sel_operador_otros'])){
            $tabla          = $_REQUEST['sel_tabla_otros'];
            $columnaRef     = $_REQUEST['sel_columnas_otros'];
            $operador       = $_REQUEST['sel_operador_otros'];
            if($operador==1){
                $consulta->addOrder("$tabla.$columnaRef");
            }else if($operador==2){
                $consulta->addGroup("$tabla.$columnaRef");
            }
        }
        //----------------------------------------------------------------------
        
        $_SESSION['consulta']=  serialize($consulta);
        
        $form = new CHtmlForm();
        $form->setId('frm_consulta');
        $form->setAction("?mod=$modulo&niv=$niv&task=consulta");
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setTitle(TITULO_REPORTEADOR);
        $form->addInputText('hidden', 'confirmacion_consulta', 'confirmacion_consulta', 0, '', '', '', '');
        $form->addInputButton('button', 'consultar', 'consultar', TITULO_CONSULTAR, '', 'onclick="consultar_reporteador();"');
        $form->addInputButton('button', 'add_columna', 'add_columna', TITULO_AGREGAR_COLUMNAS, '', 'onclick="agregar_columnas();"');
        $form->addInputButton('button', 'add_link', 'add_link', TITULO_AGREGAR_LINK, '', 'onclick="agregar_link();"');
        $form->addInputButton('button', 'add_condicion', 'add_condicion', TITULO_AGREGAR_CONDICION, '', 'onclick="agregar_condicion();"');
        $form->addInputButton('button', 'add_otros', 'add_otros', TITULO_AGREGAR_OTROS, '', 'onclick="agregar_otros();"');
        $form->writeForm();
        
        
//        echo "SQL: ".$consulta->getSql();
        $consulta->prepararConsulta();
        $titulos = $consulta->getTitulos();
        $elementos = $_REQUEST['confirmacion_consulta'];
        if($elementos!=''){
            $consulta->prepararConsulta();
            $elementos=$consulta->ejecutarConsulta($conData);
        }
        $dt = new CHtmlDataTable();
        $dt->setDataRows($elementos);
        $dt->setTitleRow($titulos);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->setTitleTable(CONSULTA);
        $dt->writeDataTable($niv);
        
        
        $form = new CHtmlForm();
        $form->setId('frm_consulta2');
        $form->setAction("");
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->addTitleText('title', "<B>Sentencia: "."</B>".$consulta->getSql());
        $form->addInputButton('button', 'guardar', 'guardar', BTN_ATRAS, '', 'onclick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=list\"");
        $form->addInputButton('button', 'guardar', 'guardar', BTN_GUARDAR, '', 'onclick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=guardarReporte\"");
        $form->addInputButton('button', 'exportar', 'exportar', COMPROMISOS_EXPORTAR, '', 'onclick="exportar_reporteador();"');
//        $form->addInputButton('button', 'reiniciar', 'reiniciar', REINICIAR_CONSULTA, '', 'onclick="reiniciar_consulta();"');
        $form->addInputButton('button', 'add_columna', 'add_columna', GRAFICA, '', 'onclick="agregar_graficas();"');
        $form->writeForm();
        
        $columnas=null;
        $temp=$consulta->getCampos();
        for($i=0;$i<count($temp);$i++){
            $columnas[$i]['id']=$i;
            $columnas[$i]['columna']=$temp[$i];
        }
        
        $dt2 = new CHtmlDataTable();
        $dt2->setDataRows($columnas);
        $dt2->setTitleRow(Array(CAMPO_FIELD));
        $dt2->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteColumna");
        $dt2->setType(1);
        $pag_crit = "";
        $dt2->setPag(1, $pag_crit);
        $dt2->setTitleTable(CAMPO_FIELDS);
        $dt2->writeDataTable($niv);
        
        $condiciones=null;
        $temp=$consulta->getCondiciones();
        for($i=0;$i<count($temp);$i++){
            $condiciones[$i]['id']=$i;
            $condiciones[$i]['columna']=$temp[$i];
        }
        
        $dt3 = new CHtmlDataTable();
        $dt3->setDataRows($condiciones);
        $dt3->setTitleRow(Array(CAMPO_CONDICION));
        $dt3->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteCondicion");
        $dt3->setType(1);
        $pag_crit = "";
        $dt3->setPag(1, $pag_crit);
        $dt3->setTitleTable(CAMPO_CONDICION);
        $dt3->writeDataTable($niv);
        break;
    
    case 'guardarReporte':
        $id_reporte = $_SESSION['id_reporte'];
        $reporte = $conData->getReporteById($id_reporte);
        $form = new CHtmlForm();
        $form->setTitle(GUARDAR_REPORTE);
        $form->setId('frm_guardar_consulta');
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=confirmGuardar');
        $form->setOptions('autoClean', false);
        $opcionesNivel=$conData->getEncabezados();
        $form->addEtiqueta(NOMBRE_REPORTE);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '', '', $reporte['nombre'], '', '');
        $form->addEtiqueta(OPCION_NIVEL);
        $form->addSelect('select', 'sel_nivel', 'sel_nivel', $opcionesNivel," un módulo" , $reporte['opcion'], '', '');
        $form->addInputButton('submit', 'aceptar', 'aceptar', BTN_ACEPTAR, '', '');
        $form->addInputButton('button', 'atras', 'atras', BTN_ATRAS, '', 'onclick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=consulta\"");
        $form->writeForm();
        
        break;
    case 'confirmGuardar':
        $nombre = $_REQUEST['txt_nombre'];
        $opcionNivel = $_REQUEST['sel_nivel'];
        $consulta = unserialize($_SESSION['consulta']);
        $consulta->titulos=null;
        $consulta->sql="";
        $_SESSION['consulta']=  serialize($consulta);
        if(empty($_SESSION['id_reporte'])){
            $m=$conData->insertReporte($nombre, $opcionNivel);
        }else{
            $m=$conData->editReporte($nombre, $opcionNivel);
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=" . $niv . "&task=consulta");
        break;
    case 'consulta':
        //-----------------------AGREGAR CAMPOS---------------------------------
        if(!empty($_REQUEST['num_columnas'])){
            $cont=$_REQUEST['num_columnas'];
            $tabla = $_REQUEST['sel_tabla'];
            for($i=0;$i<$cont;$i++){
                $sql="";
                $funcion = $_REQUEST[$i."_funcion"];
                $detalle = $_REQUEST[$i."_detalle"];
                $alias = $_REQUEST[$i."_alias"];
                $columna = $_REQUEST[$i."_columna"];
                if($funcion!=-1){
                    if($detalle!=''){
                        $sql=$detalle;
                    }else{
                        $detalle = $conData->getDetalleFuncion($funcion);
                        $sql=  str_replace("$#", $columna, $detalle);
                    }
                }else{
                    $sql=$columna;
                }
                if($alias!=''){
                    $sql.=" ".SEUDONIMO_TABLA." ".$alias;
                }
                $consulta->addCampo($sql);
            }
            $consulta->addTabla($tabla);
        }
        //----------------------------------------------------------------------
        
        //-----------------------AGREGAR LINKS----------------------------------
        if(!empty($_REQUEST['sel_columnas_llave'])){
            $tabla = $_REQUEST['sel_tabla'];
            $columnaPrincipal = $_REQUEST['sel_columnas'];
            $tablaLlave = $_REQUEST['sel_tabla_llave'];
            $columnaLlave = $_REQUEST['sel_columnas_llave'];
            $sql = "$tabla,$tablaLlave,$tabla.$columnaPrincipal=$tablaLlave.$columnaLlave";
            $consulta->addJoin($sql);
        }
        //----------------------------------------------------------------------
        
        //-----------------------AGREGAR CONDICIONES----------------------------
        if(!empty($_REQUEST['txt_valor_cond'])){
            $tabla          = $_REQUEST['sel_tabla'];
            $columnaRef     = $_REQUEST['sel_columnas'];
            $operador       = $_REQUEST['sel_operador'];
            $valorCondicion = $_REQUEST['txt_valor_cond'];
            $sql="$tabla.$columnaRef $operador '$valorCondicion'";
            $consulta->addCondicion($sql);
        }
        //----------------------------------------------------------------------
        
        //-----------------------AGREGAR OTROS----------------------------------
        if(!empty($_REQUEST['sel_operador_otros'])){
            $tabla          = $_REQUEST['sel_tabla_otros'];
            $columnaRef     = $_REQUEST['sel_columnas_otros'];
            $operador       = $_REQUEST['sel_operador_otros'];
            if($operador==1){
                $consulta->addOrder("$tabla.$columnaRef");
            }else if($operador==2){
                $consulta->addGroup("$tabla.$columnaRef");
            }
        }
        //----------------------------------------------------------------------
        
        $_SESSION['consulta']=  serialize($consulta);
        
        $form = new CHtmlForm();
        $form->setId('frm_consulta');
        $form->setAction("?mod=$modulo&niv=$niv&task=consulta");
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setTitle(TITULO_REPORTEADOR);
        $form->addInputText('hidden', 'confirmacion_consulta', 'confirmacion_consulta', 0, '', '', '', '');
        $form->addInputButton('button', 'consultar', 'consultar', TITULO_CONSULTAR, '', 'onclick="consultar_reporteador();"');
        $form->addInputButton('button', 'add_columna', 'add_columna', TITULO_AGREGAR_COLUMNAS, '', 'onclick="agregar_columnas();"');
        $form->addInputButton('button', 'add_link', 'add_link', TITULO_AGREGAR_LINK, '', 'onclick="agregar_link();"');
        $form->addInputButton('button', 'add_condicion', 'add_condicion', TITULO_AGREGAR_CONDICION, '', 'onclick="agregar_condicion();"');
        $form->addInputButton('button', 'add_otros', 'add_otros', TITULO_AGREGAR_OTROS, '', 'onclick="agregar_otros();"');
        $form->writeForm();
        
        
//        echo "SQL: ".$consulta->getSql();
        $consulta->prepararConsulta();
        $titulos = $consulta->getTitulos();
        $elementos = $_REQUEST['confirmacion_consulta'];
        if($elementos!=''){
            $consulta->prepararConsulta();
            $elementos=$consulta->ejecutarConsulta($conData);
        }
        $dt = new CHtmlDataTable();
        $dt->setDataRows($elementos);
        $dt->setTitleRow($titulos);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->setTitleTable(CONSULTA);
        $dt->writeDataTable($niv);
        
        
        $form = new CHtmlForm();
        $form->setId('frm_consulta2');
        $form->setAction("");
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->addTitleText('title', "<B>Sentencia: "."</B>".$consulta->getSql());
        $form->addInputButton('button', 'guardar', 'guardar', BTN_ATRAS, '', 'onclick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=list\"");
        $form->addInputButton('button', 'guardar', 'guardar', BTN_GUARDAR, '', 'onclick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=guardarReporte\"");
        $form->addInputButton('button', 'exportar', 'exportar', COMPROMISOS_EXPORTAR, '', 'onclick="exportar_reporteador();"');
//        $form->addInputButton('button', 'reiniciar', 'reiniciar', REINICIAR_CONSULTA, '', 'onclick="reiniciar_consulta();"');
        $form->addInputButton('button', 'add_columna', 'add_columna', GRAFICA, '', 'onclick="agregar_graficas();"');
        $form->writeForm();
        
        $columnas=null;
        $temp=$consulta->getCampos();
        for($i=0;$i<count($temp);$i++){
            $columnas[$i]['id']=$i;
            $columnas[$i]['columna']=$temp[$i];
        }
        
        $dt2 = new CHtmlDataTable();
        $dt2->setDataRows($columnas);
        $dt2->setTitleRow(Array(CAMPO_FIELD));
        $dt2->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteColumna");
        $dt2->setType(1);
        $pag_crit = "";
        $dt2->setPag(1, $pag_crit);
        $dt2->setTitleTable(CAMPO_FIELDS);
        $dt2->writeDataTable($niv);
        
        $condiciones=null;
        $temp=$consulta->getCondiciones();
        for($i=0;$i<count($temp);$i++){
            $condiciones[$i]['id']=$i;
            $condiciones[$i]['columna']=$temp[$i];
        }
        
        $dt3 = new CHtmlDataTable();
        $dt3->setDataRows($condiciones);
        $dt3->setTitleRow(Array(CAMPO_CONDICION));
        $dt3->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteCondicion");
        $dt3->setType(1);
        $pag_crit = "";
        $dt3->setPag(1, $pag_crit);
        $dt3->setTitleTable(CAMPO_CONDICION);
        $dt3->writeDataTable($niv);
        
        break;
    case 'deleteColumna':
        $consultaSerial = $_SESSION['consulta'];
        if (empty($consultaSerial)){
            $consulta = new CConsulta();
        }else{
            $consulta = unserialize($consultaSerial);
        }
        $id_delete = $_REQUEST['id_element'];
        
        $form = new CHtmlForm();
        $form->setId('frm_delete_columna');
        $form->setMethod('post');
        $form->writeForm();
        
        $temp = $consulta->getCampos();
        
        echo $html->generaAdvertencia(COLUMNA_MSG_BORRADO.$temp[$id_delete]." \" ?",
                '?mod=' . $modulo . '&niv=' . $niv . '&task=confirmDeleteColumna&id_element=' . $id_delete,
                '"onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=consulta");
        break;    
    case 'deleteCondicion':
        $consultaSerial = $_SESSION['consulta'];
        if (empty($consultaSerial)){
            $consulta = new CConsulta();
        }else{
            $consulta = unserialize($consultaSerial);
        }
        
         $id_delete = $_REQUEST['id_element'];
        
        $form = new CHtmlForm();
        $form->setId('frm_delete_condicion');
        $form->setMethod('post');
        $form->writeForm();
        
        $temp = $consulta->getCondiciones();
        
        echo $html->generaAdvertencia(CONDICION_MSG_BORRADO.$temp[$id_delete]." \" ?",
                '?mod=' . $modulo . '&niv=' . $niv . '&task=confirmDeleteCondicion&id_element=' . $id_delete,
                '"onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=consulta");
        break;
    case 'confirmDeleteColumna':
        $consulta=  unserialize($_SESSION['consulta']);
        $id_delete = $_REQUEST['id_element'];
        
        $temp = $consulta->getCampos();
        $newCampos=null;
        for($i=0;$i<count($temp);$i++){
            if($i==$id_delete) continue;
            $newCampos[count($newCampos)]=$temp[$i];
        }
        $consulta->setCampos($newCampos);
        $_SESSION['consulta']=  serialize($consulta);

        $msg = COLUMNA_BORRADA;
      
        echo $html->generaAviso($msg, "?mod=" . $modulo . "&niv=" . $niv . "&task=consulta");
        
        break;
    case 'confirmDeleteCondicion':
        $consulta=  unserialize($_SESSION['consulta']);
        $id_delete = $_REQUEST['id_element'];
        
        $temp = $consulta->getCondiciones();
        $newCampos=null;
        for($i=0;$i<count($temp);$i++){
            if($i==$id_delete) continue;
            $newCampos[count($newCampos)]=$temp[$i];
        }
        $consulta->setCondiciones($newCampos);
        $_SESSION['consulta']=  serialize($consulta);

        $msg = CONDICION_BORRADA;
      
        echo $html->generaAviso($msg, "?mod=" . $modulo . "&niv=" . $niv . "&task=consulta");
        
        break;
    case 'columnas':
        
        $tabla = $_REQUEST['sel_tabla'];
       
        $_SESSION['consulta']=  serialize($consulta);
        
        $formSelect = new CHtmlForm();
        $formSelect->setId('frm_select');
        $formSelect->setAction("?mod=$modulo&niv=$niv&task=tratar_columnas");
        $formSelect->setMethod('post');
        $formSelect->setOptions('autoClean', false);
        $formSelect->setTitle(TITULO_AGREGAR_COLUMNAS);
        
        $tablas = $conData->consultarTablas();
        
        $formSelect->addEtiqueta(CAMPO_TABLAS);
        $formSelect->addSelect('select', 'sel_tabla', 'sel_tabla', $tablas, '', $tabla, '', 'onChange="actualizar_campos();" required');
        
        $columnas = $conData->consultarCampos($tabla);        
        $formSelect->addEtiqueta(CAMPO_FIELDS);
        $formSelect->addSelect('select', 'sel_columnas[]', 'sel_columnas[]', $columnas, '', $columnaPrincipal, '', ' multiple');
        
        $formSelect->addInputButton('submit', 'add_campos', 'add_campos', AGREGAR_COLUMNAS, '', '');
        $formSelect->addInputButton('button', 'atras', 'atras', BTN_ATRAS, '', 'onClick=location.href="?mod='.$modulo.'&niv='.$niv.'&task=consulta"');
        $formSelect->writeForm();
        
        break;
        
    case 'tratar_columnas':
        $columnas=null;
        if(!empty($_REQUEST['sel_columnas'])&&!empty($_REQUEST['sel_tabla'])){
            $tabla = $_REQUEST['sel_tabla'];
            $cont=0;
            foreach($_REQUEST['sel_columnas'] as $columna){
                $columnas[count($columnas)]=("$tabla.$columna");
                $cont++;
            }
        }
        
        $formSelect = new CHtmlForm();
        $formSelect->setId('frm_select');
        $formSelect->setAction("?mod=$modulo&niv=$niv&task=consulta");
        $formSelect->setMethod('post');
        $formSelect->setOptions('autoClean', false);
        $formSelect->setTitle(TITULO_AGREGAR_COLUMNAS);
        $formSelect->addInputText('hidden', 'num_columnas', 'num_columnas', '', '', $cont, '', '');
        $formSelect->addInputText('hidden', 'sel_tabla', 'sel_tabla', '', '', $tabla, '', '');
        $funciones = $conData->consultarFunciones();
        for($i=0;$i<$cont;$i++){
            $formSelect->addTitleText('title', "<B>".CAMPO_FIELD." ".$columnas[$i]."</B>");
            $formSelect->addInputText('hidden', $i."_columna", $i."_columna", '', '', $columnas[$i], '', '');
            
            $formSelect->addEtiqueta(CAMPO_FUNCION);
            $formSelect->addSelect('select', $i."_funcion", $i."_funcion", $funciones, '', '', '', '');
            
            $formSelect->addEtiqueta(CAMPO_DETALLE);
            $formSelect->addInputText('text', $i."_detalle", $i."_detalle", '', '', '', '', '');
            
            $formSelect->addEtiqueta(CAMPO_ALIAS);
            $formSelect->addInputText('text', $i."_alias", $i."_alias", '', '', '', '', '');
            $formSelect->addTitleText('title', " ");
            
        }

        $formSelect->addInputButton('submit', 'add_campos', 'add_campos', AGREGAR_COLUMNAS, '', '');
        $formSelect->addInputButton('button', 'atras', 'atras', BTN_ATRAS, '', 'onClick=location.href="?mod='.$modulo.'&niv='.$niv.'&task=consulta"');
        $formSelect->writeForm();
        
        break;
    case 'link':
        
        $tabla = $_REQUEST['sel_tabla'];
        $columnaPrincipal = $_REQUEST['sel_columnas'];
        $tablaLlave = $_REQUEST['sel_tabla_llave'];
        $columnaLlave = $_REQUEST['sel_columnas_llave'];
        
        $formSelect = new CHtmlForm();
        $formSelect->setId('frm_link');
        $formSelect->setAction("?mod=$modulo&niv=$niv&task=link");
        $formSelect->setMethod('post');
        $formSelect->setOptions('autoClean', false);
        $formSelect->setTitle(TITULO_AGREGAR_LINK);
        
        $tablas = $conData->consultarTablas();
        
        $formSelect->addEtiqueta(CAMPO_TABLAS);
        $formSelect->addSelect('select', 'sel_tabla', 'sel_tabla', $tablas, '', $tabla, '', 'onChange="submit();"');
        
        $columnas = $conData->consultarCampos($tabla);        
        $formSelect->addEtiqueta(CAMPO_FIELDS);
        $formSelect->addSelect('select', 'sel_columnas', 'sel_columnas', $columnas, '', $columnaPrincipal, '', 'onChange="submit();"');
        
        $tablasLlave = $conData->consultarTablasAlt($tabla,$columnaPrincipal);
        
        $formSelect->addEtiqueta(CAMPO_TABLAS);
        $formSelect->addSelect('select', 'sel_tabla_llave', 'sel_tabla_llave', $tablasLlave, '', $tablaLlave, '', 'onChange="submit();"');
        
        $columnasLlave = $conData->consultarCamposAlt($tabla,$columnaPrincipal,$tablaLlave);
        
        $formSelect->addEtiqueta(CAMPO_FIELDS);
        $formSelect->addSelect('select', 'sel_columnas_llave', 'sel_columnas_llave', $columnasLlave, '',$columnaLlave, '', ' ');
        
        $formSelect->addInputButton('button', 'add_campos', 'add_campos', TITULO_AGREGAR_LINK, '', 'onclick="set_link();"');
        $formSelect->addInputButton('button', 'atras', 'atras', BTN_ATRAS, '', 'onClick=location.href="?mod='.$modulo.'&niv='.$niv.'&task=consulta"');
        $formSelect->writeForm();
        
        break;
        
    case 'condicion':
        
        $tabla = $_REQUEST['sel_tabla'];
        $columnaPrincipal = $_REQUEST['sel_columnas'];
        $operador = $_REQUEST['sel_operador'];
        $valorCondicion = $_REQUEST['txt_valor_cond'];
        $formSelect = new CHtmlForm();
        $formSelect->setId('frm_condicion');
        $formSelect->setAction("?mod=$modulo&niv=$niv&task=condicion");
        $formSelect->setMethod('post');
        $formSelect->setOptions('autoClean', false);
        $formSelect->setTitle(TITULO_AGREGAR_CONDICION);
        
        $tablas = $conData->consultarTablas();
        
        $formSelect->addEtiqueta(CAMPO_TABLAS);
        $formSelect->addSelect('select', 'sel_tabla', 'sel_tabla', $tablas, '', $tabla, '', 'onChange="submit();"');
        
        $columnas = $conData->consultarCampos($tabla);        
        $formSelect->addEtiqueta(CAMPO_FIELDS);
        $formSelect->addSelect('select', 'sel_columnas', 'sel_columnas', $columnas, '', $columnaPrincipal, '', '');
        
        $comparaciones= $conData->consultarComparaciones();        
        $formSelect->addEtiqueta(CAMPO_FUNCION);
        $formSelect->addSelect('select', "sel_operador", "sel_operador", $comparaciones, '', '', '', '');
        
        $formSelect->addEtiqueta(CAMPO_CONDICION);
        $formSelect->addInputText('text', 'txt_valor_cond', 'txt_valor_cond', 15, 15, '', '', '');
        
        $formSelect->addInputButton('button', 'add_campos', 'add_campos', TITULO_AGREGAR_CONDICION, '', 'onclick="set_condicion();"');
        $formSelect->addInputButton('button', 'atras', 'atras', BTN_ATRAS, '', 'onClick=location.href="?mod='.$modulo.'&niv='.$niv.'&task=consulta"');
        $formSelect->writeForm();
        break;
    case 'otros':
        
        $tabla = $_REQUEST['sel_tabla_otros'];
        $operador = $_REQUEST['sel_operador_otros'];
        $formSelect = new CHtmlForm();
        $formSelect->setId('frm_otrps');
        $formSelect->setAction("?mod=$modulo&niv=$niv&task=otros");
        $formSelect->setMethod('post');
        $formSelect->setOptions('autoClean', false);
        $formSelect->setTitle(TITULO_AGREGAR_COLUMNAS);
        
        $tablas = $conData->consultarTablas();
        
        $comparaciones= $conData->consultarOtros();        
        $formSelect->addEtiqueta(CAMPO_FUNCION);
        $formSelect->addSelect('select', "sel_operador_otros", "sel_operador_otros", $comparaciones, '', $operador, '', '');
        
        $formSelect->addEtiqueta(CAMPO_TABLAS);
        $formSelect->addSelect('select', 'sel_tabla_otros', 'sel_tabla_otros', $tablas, '', $tabla, '', 'onChange="submit();"');
        
        $columnas = $conData->consultarCampos($tabla);        
        $formSelect->addEtiqueta(CAMPO_FIELDS);
        $formSelect->addSelect('select', 'sel_columnas_otros', 'sel_columnas_otros', $columnas, '', $columnaPrincipal, '', '');
        
        $formSelect->addInputButton('button', 'add_campos', 'add_campos', TITULO_AGREGAR_LINK, '', 'onclick="set_otros();"');
        $formSelect->addInputButton('button', 'atras', 'atras', BTN_ATRAS, '', 'onClick=location.href="?mod='.$modulo.'&niv='.$niv.'&task=consulta"');
        $formSelect->writeForm();
        
        break;
}

?>