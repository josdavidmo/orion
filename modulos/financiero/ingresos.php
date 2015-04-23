<script src="././clases/Graficas/amcharts/amcharts.js" type="text/javascript"></script>
<script src="././clases/Graficas/amcharts/serial.js" type="text/javascript"></script>
<?php

defined('_VALID_PRY') or die('Restricted access');
$docData = new CIngresosData($db);
$task = $_REQUEST['task'];
$operador = $_REQUEST['operador'];
include "clases/libchart/libchart/classes/libchart.php";
$colores = ( array(new Color(233,163,144), new Color(140, 195, 110), new Color(194, 222, 242)));

if (empty($task))
    $task = 'list';
switch ($task) {
    /**
     * la variable list, permite cargar la pagina con los objetos 
     * ingresos
     */
    case 'list':


        $dt = new CHtmlDataTable();
        $ingresos = $docData->Obteneringresos();
        $dt->setTitleTable(TITULO_TABLA_INGRESOS);
        $titulos = array(AnO_INGRESO_TIPO, MONTO_INGRESO_B);
        $dt->setDataRows($ingresos);
        $dt->setTitleRow($titulos);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editarIngreso");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=borrarIngreso");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=AgregarIngreso");
        $dt->setSumColumns(array(2));
        $dt->setFormatRow(array(null, array(2, ',', '.')));
        $dt->setType(1);
        $dt->setPag(1);
        $dt->writeDataTable($niv);
        
        $form = new CHtmlForm();
        $form->setId("frm_list_ingresos");
        $form->setMethod('post');
				$form->setOptions('autoClean', false);
		$form->addInputButton('button', 'btn_exportar', 'btn_exportar', COMPROMISOS_EXPORTAR, 'button', 'onClick=exportar_excel_ingresos();');
        $form->writeForm();


        //obtenemos loa años de viegencias
        $Years = $docData->ObtenerYears();

        for ($i = 0; $i < count($Years); $i++) {
            $arrayear[$i] = $Years[$i]['A_Ingreso'];
        }
        //generamos loa valores y la tabla de egresos
        $vigenciaObjetivo = 2013;
        $actividadObjetivo=ACTIVIDAD_PIA;
        $dt_egresos = new CHtmlDataTable();
        $dt_egresos->setTitleTable(TITULO_TABLA_EGRESOS);
        $presupuesto_ejecutado = 0;

        $saldo=0;
        for ($i = 0; $i < count($arrayear); $i++){
            $presupuesto_ejecutado=0;
            if($arrayear[$i]==$vigenciaObjetivo){
                for($j =0 ; $j<count($arrayear); $j++){
                    
                    if($arrayear[$j]==$vigenciaObjetivo){
                        $ordenes_aprobadas = $docData->ObtenerValoresOrdenesdePago($arrayear[$j]);
                    }else{
                        $ordenes_aprobadas = $docData->ObtenerValoresOrdenesdePagoByActividad($arrayear[$j],"= $actividadObjetivo");
                    }
                    
                    if($ordenes_aprobadas[0]<0){
                        $presupuesto_ejecutado -= $ordenes_aprobadas[0];
                    }else{
                        $presupuesto_ejecutado += $ordenes_aprobadas[0];
                    }
                }
                }else{
                    $ordenes_aprobadas = $docData->ObtenerValoresOrdenesdePagoByActividad($arrayear[$i],"!= $actividadObjetivo");
                    if($ordenes_aprobadas[0]<0){
                        $presupuesto_ejecutado = $ordenes_aprobadas[0];
                    }else{
                        $presupuesto_ejecutado = $ordenes_aprobadas[0];
                    }
                }
                $utilidades_aprobadas = $docData->ObtenerValoresUtilidades($arrayear[$i]);
                $vigencias_Montos = $docData->ObtenerValoresIngresos($arrayear[$i]);
                if($utilidades_aprobadas[0] != 0){
                    $porcentaje_ejecucion = ($presupuesto_ejecutado * 100) / $utilidades_aprobadas[0];
                }else{
                    $porcentaje_ejecucion = 0;
                }

                $presupuesto_ejecutar = $utilidades_aprobadas[0]-$presupuesto_ejecutado;

                if($arrayear[$i]!=$vigenciaObjetivo){
                    $saldo+=($presupuesto_ejecutado);
                }
                $tabla_egresos[$i]['id'] =                  $i;
                $tabla_egresos[$i]['anio'] =                $arrayear[$i];
                $tabla_egresos[$i]['presAsignado'] =        $vigencias_Montos[1];
                $tabla_egresos[$i]['recAsignado'] =         $utilidades_aprobadas[0];
                $tabla_egresos[$i]['numeroUtilizaciones'] = $utilidades_aprobadas[1];
                $tabla_egresos[$i]['presEjecutado'] =       $presupuesto_ejecutado;
                $tabla_egresos[$i]['presPendiente'] =       $presupuesto_ejecutar;
                $tabla_egresos[$i]['porcentaje'] =          $porcentaje_ejecucion;
                if($arrayear[$i]==$vigenciaObjetivo){

                }
        }
        if($saldo>0){
            for ($i = 0; $i <count($tabla_egresos); $i++){
                if ($arrayear[$i] == $vigenciaObjetivo) {
                    continue;
                }
                if($tabla_egresos[$i]['recAsignado']==0){
                    $tabla_egresos[$i]['porcentaje']=0;
                    $tabla_egresos[$i]['presPendiente']=0;
                    $tabla_egresos[$i]['presEjecutado']=0;
                }
                else if($tabla_egresos[$i]['recAsignado']<=$saldo){
                    $tabla_egresos[$i]['presPendiente']=0;
                    $tabla_egresos[$i]['porcentaje']=100;
                    $tabla_egresos[$i]['presEjecutado']=$tabla_egresos[$i]['recAsignado'];
                    $saldo-=$tabla_egresos[$i]['recAsignado'];
                }else{
                    $tabla_egresos[$i]['presPendiente']=$tabla_egresos[$i]['recAsignado']-$saldo;
                    $tabla_egresos[$i]['porcentaje']=100*
                            ($tabla_egresos[$i]['recAsignado']-$tabla_egresos[$i]['presPendiente'])/$tabla_egresos[$i]['recAsignado'];
                    $tabla_egresos[$i]['presEjecutado']=$saldo;
                    $saldo=0;
                }

            }
        }

        $titulos_Egresos = array(CAMPO_ANIO,PRESUPUESTO_ASIGNADO, RECUROS_ASIGNADOS, NUMERO_UTILIZACIONES, PRESUPUESTO_EJECUTADO, PRESUPUESTO_EJECUTAR, PORCENTAJE_EJECUCION);
        $dt_egresos->setTitleRow($titulos_Egresos);
        $dt_egresos->setDataRows($tabla_egresos);
        $dt_egresos->setType(1);
        $dt_egresos->setFormatRow(array(null, array(2, ',', '.'), array(2, ',', '.'), array(0, '', '.'), array(2, ',', '.'),array(2, ',', '.'),array(4, ',', '.')));
        $dt_egresos->setSumColumns(array(2,3,4,5));
        $dt_egresos->setPag(1);
        $dt_egresos->writeDataTable($niv);



        
        $form = new CHtmlForm();
        $form->setId("form_exportar_egresos");
        $form->setMethod('post');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', GRAFICA, 'button', 'onClick=exportar_grafica_ingresos();');
		$form->addInputButton('button', 'btn_exportar', 'btn_exportar', COMPROMISOS_EXPORTAR, 'button', 'onClick=exportar_egreso_excel();');
		$form->setOptions('autoClean', false);
        $form->writeForm();

        //obtenemos los valores de desembolsos y generamos las graficas

        $dt_desembolsos = new CHtmlDataTable();
        $dt_desembolsos->setTitleTable(TITULO_TABLA_DESEMBOLSOS);
        
        for ($i = 0; $i < count($arrayear); $i++) {
            $vigencias_Montos = $docData->ObtenerValoresIngresos($arrayear[$i]);
            $informacion_desembolsos = $docData->ObtenerValoresDesembolsos($arrayear[$i]);
            $valor_pendiente_desembolsar = $vigencias_Montos[1] - $informacion_desembolsos[1];
            if($valor_pendiente_desembolsar <0){
            	$valor_pendiente_desembolsar *= (-1);
            }
            $porcentaje_desembolsado = ($informacion_desembolsos[1] / $vigencias_Montos[1]) * 100;
            $tabla_desembolsos[$i] = array(0, $vigencias_Montos[0], $informacion_desembolsos[0], 
                $informacion_desembolsos[1], $valor_pendiente_desembolsar, $porcentaje_desembolsado);
//            $dataSetDesembolo->addPoint(new Point($arrayear[$i] . "  (" . $informacion_desembolsos[0] . ")", $informacion_desembolsos[1]));
//            $dataSetDesembolo2->addPoint(new Point($arrayear[$i]. "  (" . $informacion_desembolsos[0] . ")",$valor_pendiente_desembolsar));
        }
        $titulo_desembolsos = array(DESCRIPCION_DE_VIGENCIAS, NUMERO_DESEMBOLSOS, VARLO_TOTAL_DESEMBOLSOS, VALOR_PENDIENTE_DESEMBOLSAR, PORCENTAJE_DESEMBOLSADO);
        $dt_desembolsos->setTitleRow($titulo_desembolsos);
        $dt_desembolsos->setDataRows($tabla_desembolsos);
        $dt_desembolsos->setType(1);
        $dt_desembolsos->setSumColumns(array(2, 3, 4));
        $dt_desembolsos->setFormatRow(array(null, null, array(2, ',', '.'), array(2, ',', '.'), array(4, ',', '.')));
        $dt_desembolsos->setPag(1);
        $dt_desembolsos->writeDataTable($niv);

        $form = new CHtmlForm();
        $form->setId("form_exportar_desembolsos");
        $form->setMethod('post');        
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', GRAFICA, 'button', 'onClick=exportar_grafica_desembolsos();');
		$form->addInputButton('button', 'btn_exportar', 'btn_exportar', COMPROMISOS_EXPORTAR, 'button', 'onClick=exportar_desembolsos_excel();');
		$form->setOptions('autoClean', false);

        $form->writeForm();


        //obtenemos los valores y la grafica de utilizaciones
        $dt_utilizaciones = new CHtmlDataTable();
        $dt_utilizaciones->setTitleTable(TITULO_TABLA_UTILIZACIONES);
        $dataSetUtilidades = new XYDataSet();
        $dataSetUtilidades2 = new XYDataSet();
        $totalVigencias=0;
        $totalUtilizaciones=0;
        for ($i = 0; $i < count($arrayear); $i++) {
            $vigencias_Montos = $docData->ObtenerValoresIngresos($arrayear[$i]);
            $utilidades_aprobadas = $docData->ObtenerValoresUtilidades($arrayear[$i]);
            $valor_pendiente_por_utilizaciones = $vigencias_Montos[1] - $utilidades_aprobadas[0];
            $totalVigencias += $vigencias_Montos[1];
            $totalUtilizaciones += $utilidades_aprobadas[0];
            $porcentaje_utilizaciones = ($utilidades_aprobadas[0] / $vigencias_Montos[1]) * 100;
            $tabla_utilizaciones[$i] = array(0, $vigencias_Montos[0], $utilidades_aprobadas[1], 
                $utilidades_aprobadas[0], $valor_pendiente_por_utilizaciones, $porcentaje_utilizaciones);
            $dataSetUtilidades->addPoint(new Point($arrayear[$i] . "  (" . $utilidades_aprobadas[1] . ")", $utilidades_aprobadas[0]));
            $dataSetUtilidades2->addPoint(new Point($arrayear[$i] . "  (" . $utilidades_aprobadas[1] . ")", $valor_pendiente_por_utilizaciones));
        }
        $titulo_utilizaciones = array(DESCRIPCION_DE_VIGENCIAS, NUMERO_UTILIZACIONES, VARLO_TOTAL_UTILIZACIONES, VALOR_PENDIENTE_UTILIZAR, PORCENTAJE_UTILIZACIONES);
        $dt_utilizaciones->setTitleRow($titulo_utilizaciones);
        $dt_utilizaciones->setDataRows($tabla_utilizaciones);
        $dt_utilizaciones->setFormatRow(array(null,null, array(2, ',', '.'), array(2, ',', '.'),array(4, ',', '.')));
        $dt_utilizaciones->setSumColumns(array(2, 3, 4));
        //$dt_utilizaciones->setVersusSum(array(null,null,0));
        //$dt_utilizaciones->setLabelSum(array(null,null,null,null,null,"Porcentaje Total de Ejecuci&oacute;n= ".
            //number_format($totalUtilizaciones/$totalVigencias*100,2,',','.')."%"));
        $dt_utilizaciones->setType(1);
        $dt_utilizaciones->setPag(1);
        $dt_utilizaciones->writeDataTable($niv);

        $form = new CHtmlForm();
        $form->setId("form_exportar_utilidades");
        $form->setMethod('post');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', GRAFICA, 'button', 'onClick=exportar_grafica_utilizaciones();');
		$form->addInputButton('button', 'btn_exportar', 'btn_exportar', COMPROMISOS_EXPORTAR, 'button', 'onClick=exportar_utilidades_excel();');
		$form->setOptions('autoClean', false);

        $form->writeForm();


        //obtenemos los valores y la tabla de inversion del anticipo
        $dt_inversion = new CHtmlDataTable();
        $dt_inversion->setTitleTable(TITULO_TABLA_INVSERIONES);
        $actividades_inversion = $docData->ObtenerActividaedesInversiondelAnticipo();

        for ($i = 0; $i < count($actividades_inversion); $i++) {
            $id_actividad[$i] = $actividades_inversion[$i]['Id_Actividad'];
            $descripcion_actividad[$i] = $actividades_inversion[$i]['Descripcion_Actividad'];
            $monto_actividad[$i] = $actividades_inversion[$i]['Monto_Actividad'];
            $valor_activdades_ordenes = $docData->ObtenerValoresActividadesOrdenesdepago($id_actividad[$i]);
            $actividades_ordenes[$i] = $valor_activdades_ordenes[0];
            if (!isset($actividades_ordenes[$i])) {
                $actividades_ordenes[$i] = 0;
            }
            $actividad_a_ejecutar = $monto_actividad[$i] - $actividades_ordenes[$i];
            $porcentaje_ejecucion_actividad = ($actividades_ordenes[$i] / $monto_actividad[$i]) * 100;
            $tabla_de_inversion[$i] = array($I+1, $descripcion_actividad[$i], $monto_actividad[$i], $actividades_ordenes[$i], $actividad_a_ejecutar, $porcentaje_ejecucion_actividad . "%");
        }


        $maxString=0;
        $titulo_inversion = array(REPORTE_ACTIVIDAD, REPORTE_MONTO_ACTIVIDAD, REPORTE_ORDEN_ACTIVIDAD, REPORTE_EJECUTAR, PORCENTAJE_EJECUCION_ANTICIPO);
        $dt_inversion->setTitleRow($titulo_inversion);
        $dt_inversion->setDataRows($tabla_de_inversion);
        $dt_inversion->setFormatRow(array(null, array(2, ',', '.'), array(2, ',', '.'), array(2, ',', '.'),array(4, ',', '.')));
        $dt_inversion->setSumColumns(array(2, 3, 4));
        $dt_inversion->setType(1);
        $dt_inversion->setPag(1);
        $dt_inversion->writeDataTable($niv);
        
        
        for ($i = 0; $i < count($actividades_inversion); $i++) {
            $id_actividad[$i] = $actividades_inversion[$i]['Id_Actividad'];
            $descripcion_actividad[$i] = $actividades_inversion[$i]['Descripcion_Actividad'];
            if(strlen($descripcion_actividad[$i])>$maxString){
                $maxString=strlen($descripcion_actividad[$i]);
            }
            $monto_actividad[$i] = $actividades_inversion[$i]['Monto_Actividad'];
            $valor_activdades_ordenes = $docData->ObtenerValoresActividadesOrdenesdepago($id_actividad[$i]);
            $actividades_ordenes[$i] = $valor_activdades_ordenes[0];
            if (!isset($actividades_ordenes[$i])) {
                $actividades_ordenes[$i] = 0;
            }
            $actividad_a_ejecutar = $monto_actividad[$i] - $actividades_ordenes[$i];
            $porcentaje_ejecucion_actividad = ($actividades_ordenes[$i] / $monto_actividad[$i]) * 100;
        
            
        }
        $dataSetInversion2 = new XYDataSet();
        
        for ($i = 0; $i < count($actividades_inversion); $i++) {
            $id_actividad[$i] = $actividades_inversion[$i]['Id_Actividad'];
            $descripcion_actividad[$i] = $actividades_inversion[$i]['Descripcion_Actividad'];
            if(strlen($descripcion_actividad[$i])>$maxString){
                $maxString=strlen($descripcion_actividad[$i]);
            }
            $monto_actividad[$i] = $actividades_inversion[$i]['Monto_Actividad'];
            $valor_activdades_ordenes = $docData->ObtenerValoresActividadesOrdenesdepago($id_actividad[$i]);
            $actividades_ordenes[$i] = $valor_activdades_ordenes[0];
            if (!isset($actividades_ordenes[$i])) {
                $actividades_ordenes[$i] = 0;
            }
            $actividad_a_ejecutar = $monto_actividad[$i] - $actividades_ordenes[$i];
            $porcentaje_ejecucion_actividad = ($actividades_ordenes[$i] / $monto_actividad[$i]) * 100;
           $dataSetInversion2->addPoint(new Point(($i+1),$actividades_ordenes[$i]."(".$porcentaje_ejecucion_actividad."%)"));
        }
        $dataSetInversion3 = new XYDataSet();
                  
       for ($i = 0; $i < count($actividades_inversion); $i++) {
            $id_actividad[$i] = $actividades_inversion[$i]['Id_Actividad'];
            $descripcion_actividad[$i] = $actividades_inversion[$i]['Descripcion_Actividad'];
            if(strlen($descripcion_actividad[$i])>$maxString){
                $maxString=strlen($descripcion_actividad[$i]);
            }
            $monto_actividad[$i] = $actividades_inversion[$i]['Monto_Actividad'];
            $valor_activdades_ordenes = $docData->ObtenerValoresActividadesOrdenesdepago($id_actividad[$i]);
            $actividades_ordenes[$i] = $valor_activdades_ordenes[0];
            if (!isset($actividades_ordenes[$i])) {
                $actividades_ordenes[$i] = 0;
            }
            $actividad_a_ejecutar = $monto_actividad[$i] - $actividades_ordenes[$i];
            $porcentaje_ejecucion_actividad = ($actividades_ordenes[$i] / $monto_actividad[$i]) * 100;
       
            $dataSetInversion3->addPoint(new Point(($i+1), $actividad_a_ejecutar));
        }
        if($maxString<=7){
            $maxString=0;
        }

        $form = new CHtmlForm();
        $form->setId("form_exportar_inversiones");
        $form->setMethod('post');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', GRAFICA, 'button', 'onClick=exportar_grafica_inversion();');
		$form->addInputButton('button', 'btn_exportar', 'btn_exportar', COMPROMISOS_EXPORTAR, 'button', 'onClick=exportar_invsersiones_excel();');
		$form->setOptions('autoClean', false);

        $form->writeForm();

        
        break;

    /**
     * la variable AgregarIngreso, permite cargar el formulario y los datos 
     * de un objeto Ingreso
     */
    case 'AgregarIngreso':

        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_AGREGAR_INGRESO);
        $form->setId('frm_agregar_ingreso');
        $form->setMethod('post');
        $form->addEtiqueta(AnO_INGRESO);
        $form->addInputDate('date', 'ano_ingreso', 'ano_ingreso', $ano, '%Y', '22', '22', '', 'onChange="ocultarDiv(\'error_ano\');"');
        $form->addError('error_ano', ERROR_AnO_AGREGAR);
        $form->addEtiqueta(MONTO_INGRESO);
        $form->addInputText('text', 'txt_Monto', 'txt_Monto', '15', '15', $monto, '', 'onkeypress="ocultarDiv(\'error_monto\');"');
        $form->addError('error_monto', ERROR_MONTO_AGREGAR);
        $form->addInputButton('button', 'ok', 'ok', BOTON_INSERTAR, 'button', 'onclick="Validar_agregar_ingreso(\'frm_agregar_ingreso\',\'?mod=' . $modulo . '&task=GuardarIngreso&niv=' . $niv . '\');"');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onclick="cancelarAccionIngreso(\'frm_agregar_ingreso\',\'?mod=' . $modulo . '&task=list&niv=' . $niv . '\');"');
        $form->writeForm();
        break;
    
    /**
     * la variable GuardarIngreso, permite cargar la datos de la variable AgregarIngreso 
     * y agregar a la base de datos el objeto ingreso 
     */
    case 'GuardarIngreso':

        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setId('frm_ingreso_validar');
        $form->setMethod('post');
        $form->writeForm();
        $Fecha = $_REQUEST['ano_ingreso'];
        $monto = $_REQUEST['txt_Monto'];

        $ingreso = new CIngresos('', '', '', '', $docData);

        //validamos si el ingreso que se esta ingresando es una adicion o un vigencia 
        if ($ingreso->validarano($Fecha)) {

            echo $html->generaAdvertencia(AnO_ADICION . $Fecha, '?mod=' . $modulo . '&niv=' . $niv .
                    '&task=GuardarAdicion&ano_ingreso=' . $Fecha . '&txt_Monto=' . $monto, "cancelarAccionIngreso('frm_ingreso_validar','?mod=" . $modulo . "&niv=" . $niv . "&task=AgregarIngreso" . "');");
        } else {

            echo $html->generaAdvertencia(AnO_VIGENCIA . $Fecha, '?mod=' . $modulo . '&niv=' . $niv .
                    '&task=GuardarVigencia&ano_ingreso=' . $Fecha . '&txt_Monto=' . $monto, "cancelarAccionIngreso('frm_ingreso_validar','?mod=" . $modulo . "&niv=" . $niv . "&task=AgregarIngreso" . "');");
        }


        break;
        
    //GuardarAdicion permite ingresar un adicion en la base de datos
    case 'GuardarAdicion':

        $Fecha = $_REQUEST['ano_ingreso'];
        $monto = $_REQUEST['txt_Monto'];
        $tipo = 'Adición';
        $insertarIngreso = $docData->Insertaringreso('', $Fecha, $monto, $tipo);
        echo $html->generaAviso($insertarIngreso, "?mod=" . $modulo . "&niv=" . $niv . "&task=list", "cancelarAccionIngreso('frm_agregar_ingreso','?mod=" . $modulo . "&niv=" . $niv . "');");


        break;
    
    //GuardarAdicion permite ingresar un vigencia en la base de datos
    case 'GuardarVigencia':

        $Fecha = $_REQUEST['ano_ingreso'];
        $monto = $_REQUEST['txt_Monto'];
        $tipo = 'Vigencia';
        $insertarIngreso = $docData->Insertaringreso('', $Fecha, $monto, $tipo);
        echo $html->generaAviso($insertarIngreso, "?mod=" . $modulo . "&niv=" . $niv . "&task=list", "cancelarAccionIngreso('frm_agregar_ingreso','?mod=" . $modulo . "&niv=" . $niv . "');");

        break;
    
    /**
     * la variable borrarIngreso,  cargar los datos del objeto ingreso que se 
     * va a eliminar y los envia a la variable ConfirmarBorrar 
     */
    case 'borrarIngreso':

        $id_delete = $_REQUEST['id_element'];
        $Fecha = $_REQUEST['ano_ingreso'];
        $monto = $_REQUEST['txt_Monto'];
        $form = new CHtmlForm();
        $form->setId('frm_borrar_ingreso');
        $form->setMethod('post');
        $form->addInputText('hidden', 'ano_ingreso', 'ano_ingreso', '15', '15', $Fecha, '', '');
        $form->addInputText('hidden', 'txt_nombre_proveedor', 'txt_nombre_proveedor', '15', '15', $monto, '', '');
        $form->writeForm();

        //Caragamos el objeto a eliminar y validamos si es una vigencia y si tiene adiciones o no
        $ingreso = new CIngresos($id_delete, '', '', '', $docData);
        $ingreso->Cargaringreso();
        $year_validar = $ingreso->getano();
        $tipoeliminar = $ingreso->gettipo();
        $indice = $ingreso->validarelimavigencia($year_validar);
        if ($tipoeliminar == 'Vigencia' && $indice == 1) {


            echo $html->generaAdvertencia(ALERTA_INGRESO . $Fecha, '?mod=' . $modulo . '&niv=' . $niv .
                    '&task=list', "cancelarAccionIngreso('frm_ingreso_validar','?mod=" . $modulo . "&niv=" . $niv . "&task=AgregarIngreso" . "');");
        } else {
            echo $html->generaAdvertencia(ELIMINAR_INGRESO, '?mod=' . $modulo . '&niv=' . $niv .
                    '&task=confirmaborrar&id_element=' . $id_delete . '&ano_ingreso=' . $Fecha . '&txt_Monto=' . $monto, "cancelarAccionIngreso('frm_borrar_ingreso','?mod=" . $modulo . "&niv=" . $niv . "');");
        }
        break;
    /**
     * la variable Confirmarborar, permite eliminar el objeto de la base de datos
     */
    case 'confirmaborrar':

        $id_delete = $_REQUEST['id_element'];
        $Fecha = $_REQUEST['ano_ingreso'];
        $monto = $_REQUEST['txt_Monto'];
        $ingreso = new CIngresos($id_delete, '', '', '', $docData);
        $ingreso->Cargaringreso();
        $id = $ingreso->getIdIngreso();
        $eliminado = $ingreso->EliminarIngreso($id);

        echo $html->generaAviso($eliminado, "?mod=" . $modulo . "&niv=" . $niv . "&task=list", "cancelarAccionIngreso('frm_borrar_ingreso','?mod=" . $modulo . "&niv=" . $niv . "');");

        break;

    /**
     * la variable editarIngreso, genera un formulario y carga los datos del 
     * objeto ingreso que se va a editar
     */
    case 'editarIngreso':
        $id_edit = $_REQUEST['id_element'];
        $monto = $_REQUEST['txt_Monto'];

        $ingreso = new CIngresos($id_edit, '', '', '', $docData);
        $ingreso->Cargaringreso();

        if (!isset($_REQUEST['txt_Monto_edit']) || $_REQUEST['txt_Monto_edit'] != '')
            $monto_edit = $ingreso->getmonto();
        else
            $monto_edit = $_REQUEST['txt_Monto_edit'];

        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_INGRESO);
        $form->setId('frm_editar_ingreso');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->addInputText('hidden', 'txt_id', 'txt_id', '15', '15', $ingreso->getIdIngreso(), '', '');

        $form->addEtiqueta(MONTO_INGRESO_B);
        $form->addInputText('text', 'txt_Monto_edit', 'txt_Monto_edit', '15', '15', $monto_edit, '', 'onkeypress="ocultarDiv(\'error_monto\');"');
        $form->addError('error_monto', ERROR_MONTO_AGREGAR);


        $form->addInputText('hidden', 'txt_Monto', 'txt_Monto', '15', '15', $monto, '', '');

        $form->addInputButton('button', 'ok', 'ok', BOTON_EDITAR, 'button', 'onclick="Validar_editar_ingreso();"');
        $form->addInputButton('button', 'cancel', 'cancel', BOTON_CANCELAR, 'button', 'onclick="cancelarAccionIngreso(\'frm_editar_ingreso\',\'?mod=' . $modulo . '&task=list&niv=' . $niv . '\');"');
        $form->writeForm();

        break;
    /**
     * la variable GuardarEdicion, permite guardar los atributos del objeto ingreso
     * modificados en la base de datos 
     */
    case 'GuardarEdicion':

        $id_edit = $_REQUEST['txt_id'];
        $monto_edit = $_REQUEST['txt_Monto_edit'];
        $ingreso = new CIngresos($id_edit, '', '', '', $docData);
        $ingreso->Cargaringreso();
        $id = $ingreso->getIdIngreso();
        $edicion = $ingreso->actualizarIngresos($id, $monto_edit);
        echo $html->generaAviso($edicion, "?mod=" . $modulo . "&niv=" . $niv . "&task=list");

        break;
    case 'graficaUtilizaciones':
        
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(GRAFICA_UTILIDADES);
        $form->setId('frm_agregar_ingreso');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        
        $Years = $docData->ObtenerYears();

        for ($i = 0; $i < count($Years); $i++) {
            $arrayear[$i] = $Years[$i]['A_Ingreso'];
        }
         $vigenciaObjetivo = 2013;
        $actividadObjetivo=ACTIVIDAD_PIA;
        
        $totalVigencias=0;
        $totalUtilizaciones=0;
        for ($i = 0; $i < count($arrayear); $i++) {
            $vigencias_Montos = $docData->ObtenerValoresIngresos($arrayear[$i]);
            $utilidades_aprobadas = $docData->ObtenerValoresUtilidades($arrayear[$i]);
            $valor_pendiente_por_utilizaciones = $vigencias_Montos[1] - $utilidades_aprobadas[0];
            $totalVigencias += $vigencias_Montos[1];
            $totalUtilizaciones += $utilidades_aprobadas[0];
            $porcentaje_utilizaciones = ($utilidades_aprobadas[0] / $vigencias_Montos[1]) * 100;
            $tabla_utilizaciones[$i] = array(0, $vigencias_Montos[0], $utilidades_aprobadas[1], 
                $utilidades_aprobadas[0], $valor_pendiente_por_utilizaciones, $porcentaje_utilizaciones);
//            $dataSetUtilidades->addPoint(new Point($arrayear[$i] . "  (" . $utilidades_aprobadas[1] . ")", $utilidades_aprobadas[0]));
//            $dataSetUtilidades2->addPoint(new Point($arrayear[$i] . "  (" . $utilidades_aprobadas[1] . ")", $valor_pendiente_por_utilizaciones));
        }
        $data = '[ ';
        for ($i = 0; $i < count($tabla_utilizaciones); $i++) {
			if($tabla_utilizaciones[$i][3]==null){
                $tabla_utilizaciones[$i][3]=0;
            }
                        $data.= '{ "Utilizaciones" : "' . $html->traducirTildes($tabla_utilizaciones[$i][1]) . '",'
                                . '"Recursos" : ' . ($tabla_utilizaciones[$i][4]) . ','
                                . '"temp" : ' . $tabla_utilizaciones[$i][3] . '}';
                        if ($i < (count($tabla_utilizaciones) - 1)) {
                            $data.=' , ';
                        }
                    }
        $data.=' ]';
        
           
    ?>
        <script type="text/javascript">
            var chart;
            var chartData = (<?php echo $data; ?>);
            AmCharts.ready(function() {

                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.categoryField = "Utilizaciones";
                chart.startDuration = 1;
                chart.handDrawnScatter = 3;
                chart.clustered = 0;

                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.labelRotation = 90;
                categoryAxis.gridPosition = "start";
                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.axisAlpha = 0;
                chart.addValueAxis(valueAxis);

                // GRAPHS
                // column graph
                var graph1 = new AmCharts.AmGraph();
                graph1.type = "column";
                graph1.title = "Asignado";
                graph1.lineColor = "#1e2260";
                graph1.valueField = "Recursos";
                graph1.lineAlpha = 1;
                graph1.fillAlphas = 1;
                graph1.dashLengthField = "dashLengthColumn";
                graph1.alphaField = "alpha";
                graph1.balloonText = "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>";
                chart.addGraph(graph1);

                // line
                var graph2 = new AmCharts.AmGraph();
                graph2.type = "column";
                graph2.title = "Ejecutado";
                graph2.lineColor = "#54acd2";
                graph2.valueField = "temp";
                graph2.lineAlpha = 1;
                graph2.fillAlphas = 1;
                graph2.dashLengthField = "dashLengthColumn";
                graph2.alphaField = "alpha";
                graph2.balloonText = "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>";
                graph2.clustered = false;
                graph2.columnWidth = 0.5;
                chart.addGraph(graph2);

                // LEGEND                
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);
                // WRITE
                chart.write("chartdiv");
            });
        </script>
        <div id="chartdiv" style="width: 100%; height: 400px;"></div>
        <?php
        
        break;
    
    case 'graficaDesembolsos':
        
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(GRAFICA_DESEMBOLSOS);
        $form->setId('frm_agregar_ingreso');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        
        $Years = $docData->ObtenerYears();

        for ($i = 0; $i < count($Years); $i++) {
            $arrayear[$i] = $Years[$i]['A_Ingreso'];
        }
         $vigenciaObjetivo = 2013;
        $actividadObjetivo=ACTIVIDAD_PIA;
        
        for ($i = 0; $i < count($arrayear); $i++) {
            $vigencias_Montos = $docData->ObtenerValoresIngresos($arrayear[$i]);
            $informacion_desembolsos = $docData->ObtenerValoresDesembolsos($arrayear[$i]);
            $valor_pendiente_desembolsar = $vigencias_Montos[1] - $informacion_desembolsos[1];
            if($valor_pendiente_desembolsar <0){
            	$valor_pendiente_desembolsar *= (-1);
            }
            $porcentaje_desembolsado = ($informacion_desembolsos[1] / $vigencias_Montos[1]) * 100;
            $tabla_desembolsos[$i] = array(0, $vigencias_Montos[0], $informacion_desembolsos[0], 
                $informacion_desembolsos[1], $valor_pendiente_desembolsar, $porcentaje_desembolsado);
//            $dataSetDesembolo->addPoint(new Point($arrayear[$i] . "  (" . $informacion_desembolsos[0] . ")", $informacion_desembolsos[1]));
//            $dataSetDesembolo2->addPoint(new Point($arrayear[$i]. "  (" . $informacion_desembolsos[0] . ")",$valor_pendiente_desembolsar));
        }
        $data = '[ ';
        for ($i = 0; $i < count($tabla_desembolsos); $i++) {
            if($tabla_desembolsos[$i][3]==null){
                $tabla_desembolsos[$i][3]=0;
            }
                        $data.= '{ "Ingresos" : "' . $html->traducirTildes($tabla_desembolsos[$i][1]) . '",'
                                . '"Recursos" : ' . ($tabla_desembolsos[$i][4]) . ','
                                . '"temp" : ' . $tabla_desembolsos[$i][3] . '}';
                        if ($i < (count($tabla_desembolsos) - 1)) {
                            $data.=' , ';
                        }
                    }
        $data.=' ]';
                    
    ?>
        <script type="text/javascript">
            var chart;
            var chartData = (<?php echo $data; ?>);
            AmCharts.ready(function() {

                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.categoryField = "Ingresos";
                chart.startDuration = 1;
                chart.handDrawnScatter = 3;
                chart.clustered = 0;

                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.labelRotation = 90;
                categoryAxis.gridPosition = "start";
                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.axisAlpha = 0;
                chart.addValueAxis(valueAxis);

                // GRAPHS
                // column graph
                var graph1 = new AmCharts.AmGraph();
                graph1.type = "column";
                graph1.title = "Asignado";
                graph1.lineColor = "#1e2260";
                graph1.valueField = "Recursos";
                graph1.lineAlpha = 1;
                graph1.fillAlphas = 1;
                graph1.dashLengthField = "dashLengthColumn";
                graph1.alphaField = "alpha";
                graph1.balloonText = "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>";
                chart.addGraph(graph1);

                // line
                var graph2 = new AmCharts.AmGraph();
                graph2.type = "column";
                graph2.title = "Ejecutado";
                graph2.lineColor = "#54acd2";
                graph2.valueField = "temp";
                graph2.lineAlpha = 1;
                graph2.fillAlphas = 1;
                graph2.dashLengthField = "dashLengthColumn";
                graph2.alphaField = "alpha";
                graph2.balloonText = "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>";
                graph2.clustered = false;
                graph2.columnWidth = 0.5;
                chart.addGraph(graph2);

                // LEGEND                
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);
                // WRITE
                chart.write("chartdiv");
            });
        </script>
        <div id="chartdiv" style="width: 100%; height: 400px;"></div>
        <?php
        
        break;
    case 'graficaEgresos':
        
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle("Gráfica Egresos");
        $form->setId('frm_agregar_ingreso');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        //obtenemos loa años de viegencias
        $Years = $docData->ObtenerYears();

        for ($i = 0; $i < count($Years); $i++) {
            $arrayear[$i] = $Years[$i]['A_Ingreso'];
        }
         $vigenciaObjetivo = 2013;
        $actividadObjetivo=ACTIVIDAD_PIA;
        $presupuesto_ejecutado = 0;
        $data = '[ ';
        
        $saldo=0;
        for ($i = 0; $i < count($arrayear); $i++){
            $presupuesto_ejecutado=0;
            if($arrayear[$i]==$vigenciaObjetivo){
                for($j =0 ; $j<count($arrayear); $j++){
                    
                    if($arrayear[$j]==$vigenciaObjetivo){
                        $ordenes_aprobadas = $docData->ObtenerValoresOrdenesdePago($arrayear[$j]);
                    }else{
                        $ordenes_aprobadas = $docData->ObtenerValoresOrdenesdePagoByActividad($arrayear[$j],"= $actividadObjetivo");
                    }
                    
                    if($ordenes_aprobadas[0]<0){
                        $presupuesto_ejecutado -= $ordenes_aprobadas[0];
                    }else{
                        $presupuesto_ejecutado += $ordenes_aprobadas[0];
                    }
                }
                }else{
                    $ordenes_aprobadas = $docData->ObtenerValoresOrdenesdePagoByActividad($arrayear[$i],"!= $actividadObjetivo");
                    if($ordenes_aprobadas[0]<0){
                        $presupuesto_ejecutado = $ordenes_aprobadas[0];
                    }else{
                        $presupuesto_ejecutado = $ordenes_aprobadas[0];
                    }
                }
                $utilidades_aprobadas = $docData->ObtenerValoresUtilidades($arrayear[$i]);
                $vigencias_Montos = $docData->ObtenerValoresIngresos($arrayear[$i]);
                if($utilidades_aprobadas[0] != 0){
                    $porcentaje_ejecucion = ($presupuesto_ejecutado * 100) / $utilidades_aprobadas[0];
                }else{
                    $porcentaje_ejecucion = 0;
                }

                $presupuesto_ejecutar = $utilidades_aprobadas[0]-$presupuesto_ejecutado;

                if($arrayear[$i]!=$vigenciaObjetivo){
                    $saldo+=($presupuesto_ejecutado);
                }
                $tabla_egresos[$i]['id'] =                  $i;
                $tabla_egresos[$i]['anio'] =                $arrayear[$i];
                $tabla_egresos[$i]['presAsignado'] =        $vigencias_Montos[1];
                $tabla_egresos[$i]['recAsignado'] =         $utilidades_aprobadas[0];
                $tabla_egresos[$i]['numeroUtilizaciones'] = $utilidades_aprobadas[1];
                $tabla_egresos[$i]['presEjecutado'] =       $presupuesto_ejecutado;
                $tabla_egresos[$i]['presPendiente'] =       $presupuesto_ejecutar;
                $tabla_egresos[$i]['porcentaje'] =          $porcentaje_ejecucion;
                
        }
        
        if($saldo>0){
            for ($i = 0; $i <count($tabla_egresos); $i++){
                if ($arrayear[$i] == $vigenciaObjetivo) {
                    continue;
                }
                if($tabla_egresos[$i]['recAsignado']==0){
                    $tabla_egresos[$i]['porcentaje']=0;
                    $tabla_egresos[$i]['presPendiente']=0;
                    $tabla_egresos[$i]['presEjecutado']=0;
                }
                else if($tabla_egresos[$i]['recAsignado']<=$saldo){
                    $tabla_egresos[$i]['presPendiente']=0;
                    $tabla_egresos[$i]['porcentaje']=100;
                    $tabla_egresos[$i]['presEjecutado']=$tabla_egresos[$i]['recAsignado'];
                    $saldo-=$tabla_egresos[$i]['recAsignado'];
                }else{
                    $tabla_egresos[$i]['presPendiente']=$tabla_egresos[$i]['recAsignado']-$saldo;
                    $tabla_egresos[$i]['porcentaje']=100*
                            ($tabla_egresos[$i]['recAsignado']-$tabla_egresos[$i]['presPendiente'])/$tabla_egresos[$i]['recAsignado'];
                    $tabla_egresos[$i]['presEjecutado']=$saldo;
                    $saldo=0;
                }

            }
        }
        
        for ($i = 0; $i < count($tabla_egresos); $i++) {
            if($tabla_egresos[$i]['presEjecutado']==null){
                $tabla_egresos[$i]['presEjecutado']=0;
            }
                        $data.= '{ "Ingresos" : "' . $html->traducirTildes($tabla_egresos[$i]['anio']) . '",'
                                . '"Recursos" : ' . ($tabla_egresos[$i]['presEjecutado']+$tabla_egresos[$i]['presPendiente']) . ','
                                . '"temp" : ' . $tabla_egresos[$i]['presEjecutado'] . '}';
                        if ($i < (count($tabla_egresos) - 1)) {
                            $data.=' , ';
                        }
                    }
        $data.=' ]';
        
        ?>
<script type="text/javascript">
            var chart;
            var chartData = (<?php echo $data; ?>);
            AmCharts.ready(function() {

                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.categoryField = "Ingresos";
                chart.startDuration = 1;
                chart.handDrawnScatter = 3;
                chart.clustered = 0;

                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                //categoryAxis.labelRotation = 90;
                categoryAxis.gridPosition = "start";
                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.axisAlpha = 0;
                chart.addValueAxis(valueAxis);

                // GRAPHS
                // column graph
                var graph1 = new AmCharts.AmGraph();
                graph1.type = "column";
                graph1.title = "Asignado";
                graph1.lineColor = "#1e2260";
                graph1.valueField = "Recursos";
                graph1.lineAlpha = 1;
                graph1.fillAlphas = 1;
                graph1.dashLengthField = "dashLengthColumn";
                graph1.alphaField = "alpha";
                graph1.balloonText = "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>";
                chart.addGraph(graph1);

                // line
                // line
                var graph2 = new AmCharts.AmGraph();
                graph2.type = "column";
                graph2.title = "Ejecutado";
                graph2.lineColor = "#54acd2";
                graph2.valueField = "temp";
                graph2.lineAlpha = 1;
                graph2.fillAlphas = 1;
                graph2.dashLengthField = "dashLengthColumn";
                graph2.alphaField = "alpha";
                graph2.balloonText = "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>";
                graph2.clustered = false;
                graph2.columnWidth = 0.5;
                chart.addGraph(graph2);

                // LEGEND                
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);
                // WRITE
                chart.write("chartdiv");
            });
        </script>
        <div id="chartdiv" style="width: 100%; height: 400px;"></div>
        <?php
        break;
        
    case 'graficaInversion':
        
        
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(GRAFICA_DE_INVERSIO);
        $form->setId('frm_agregar_ingreso');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        
        $presupuesto_ejecutado = 0;
        $data = '[ ';
        
        
        $actividades_inversion = $docData->ObtenerActividaedesInversiondelAnticipo();
        for ($i = 0; $i < count($actividades_inversion); $i++) {
            $id_actividad[$i] = $actividades_inversion[$i]['Id_Actividad'];
            $descripcion_actividad[$i] = $actividades_inversion[$i]['Descripcion_Actividad'];
            $monto_actividad[$i] = $actividades_inversion[$i]['Monto_Actividad'];
            $valor_activdades_ordenes = $docData->ObtenerValoresActividadesOrdenesdepago($id_actividad[$i]);
            $actividades_ordenes[$i] = $valor_activdades_ordenes[0];
            if (!isset($actividades_ordenes[$i])) {
                $actividades_ordenes[$i] = 0;
            }
            $actividad_a_ejecutar = $monto_actividad[$i] - $actividades_ordenes[$i];
            $porcentaje_ejecucion_actividad = ($actividades_ordenes[$i] / $monto_actividad[$i]) * 100;
            $tabla_de_inversion[$i] = array($I+1, $descripcion_actividad[$i], $monto_actividad[$i], $actividades_ordenes[$i], $actividad_a_ejecutar, $porcentaje_ejecucion_actividad . "%");
        }

        for ($i = 0; $i < count($monto_actividad); $i++) {
            if($monto_actividad[$i]==null){
                $monto_actividad[$i]=0;
            }
                        $data.= '{ "Ingresos" : "' . $html->traducirTildes($i+1) . '",'
                                . '"Recursos" : ' . ($monto_actividad[$i]) . ','
                                . '"temp" : ' .  $actividades_ordenes[$i]. '}';
                        if ($i < (count($monto_actividad) - 1)) {
                            $data.=' , ';
                        }
                    }
        $data.=' ]';
        ?>
        <script type="text/javascript">
            var chart;
            var chartData = (<?php echo $data; ?>);
            AmCharts.ready(function() {

                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.categoryField = "Ingresos";
                chart.startDuration = 1;
                chart.handDrawnScatter = 3;
                chart.clustered = 0;

                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.labelRotation = 90;
                categoryAxis.gridPosition = "start";
                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.axisAlpha = 0;
                chart.addValueAxis(valueAxis);

                // GRAPHS
                // column graph
                var graph1 = new AmCharts.AmGraph();
                graph1.type = "column";
                graph1.title = "Asignado";
                graph1.lineColor = "#1e2260";
                graph1.valueField = "Recursos";
                graph1.lineAlpha = 1;
                graph1.fillAlphas = 1;
                graph1.dashLengthField = "dashLengthColumn";
                graph1.alphaField = "alpha";
                graph1.balloonText = "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>";
                chart.addGraph(graph1);

                // line
                var graph2 = new AmCharts.AmGraph();
                graph2.type = "column";
                graph2.title = "Ejecutado";
                graph2.lineColor = "#54acd2";
                graph2.valueField = "temp";
                graph2.lineAlpha = 1;
                graph2.fillAlphas = 1;
                graph2.dashLengthField = "dashLengthColumn";
                graph2.alphaField = "alpha";
                graph2.balloonText = "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>";
                graph2.clustered = false;
                graph2.columnWidth = 0.5;
                chart.addGraph(graph2);

                // LEGEND                
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);
                // WRITE
                chart.write("chartdiv");
            });
        </script>
        <div id="chartdiv" style="width: 100%; height: 400px;"></div>
        <?php
        
        break;
    /**
     * la variable default genera un mesaje que indica que el modulo esta en construccion
     */
    default:
        include('templates/html/under.html');

        break;
}