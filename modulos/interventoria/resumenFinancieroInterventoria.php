<script src="././clases/Graficas/amcharts/amcharts.js" type="text/javascript"></script>
<script src="././clases/Graficas/amcharts/serial.js" type="text/javascript"></script>
<?php
defined('_VALID_PRY') or die('Restricted access');
$docData = new CResumenFinancieroInterventoriaData($db);
$html = new CHtml();
$task = $_REQUEST['task'];
$operador = $_REQUEST['operador'];

if (empty($task))
    $task = 'list';
switch ($task) {
    /**
     * la variable list, permite cargar la pagina con los objetos 
     * ingresos
     */
    case 'list':
        
        $form = new CHtmlForm();
        $form->setTitle(TITULO_RESUMEN_FINANCIERO_INTERVENTORIA);
        $form->writeForm();
        //Tabla Ingresos
        $dt = new CHtmlDataTable();
        $ingresos = $docData->ObtenerVigencias();
        $dt->setTitleTable(TITULO_TABLA_INGRESOS);
        $titulos = array(AnO_INGRESO_TIPO, MONTO_INGRESO_B);
        $dt->setDataRows($ingresos);
        $dt->setTitleRow($titulos);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editarVigencia");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=borrarVigencia");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=agregarVigencia");
        $dt->setSumColumns(array(2));
        $dt->setFormatRow(array(null, array(0, ',', '.')));
        $dt->setType(1);
        $dt->setPag(1);
        $dt->writeDataTable($niv);

        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setId('frm_list_ingresos');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', COMPROMISOS_EXPORTAR, 'button', 'onClick=exportar_excel_ingresos_interventoria();');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', GRAFICA, 'button', 'onClick=exportar_grafica_ingresos_interventoria();');
        $form->setOptions('autoClean', false);
        $form->setMethod('post');
        $form->writeForm();

        //obtenemos loa años de viegencias
        $Years = $docData->ObtenerYears();

        for ($i = 0; $i < count($Years); $i++) {
            $arrayear[$i] = $Years[$i]['ano_Vigencia'];
        }
        //generamos loa valores y la tabla de egresos
        $vigenciaObjetivo = 2013;
        $actividadObjetivo = ACTIVIDAD_PIA;
        $dt_egresos = new CHtmlDataTable();
        $dt_egresos->setTitleTable(TITULO_TABLA_INFORME_FINANCIERO);
        $presupuesto_ejecutado = 0;
        $saldo = 0;
        for ($i = 0; $i < count($arrayear); $i++) {
            if ($arrayear[$i] == $vigenciaObjetivo) {
                $presupuesto_ejecutado = $docData->obtenerTotalActividades();
            } else {
                $presupuesto_ejecutado = $docData->obtenerInformeFinanciero($arrayear[$i]);
            }
            $vigencias_Montos = $docData->obtenerValoresVigencia($arrayear[$i]);
            if (isset($presupuesto_ejecutado[0])) {
                $presupuesto_ejecutado = $presupuesto_ejecutado[0];
            } else {
                $presupuesto_ejecutado = 0;
            }
            $presupuesto_ejecutar = $vigencias_Montos[1] - $presupuesto_ejecutado;

            $porcentaje_ejecucion = $presupuesto_ejecutado / $vigencias_Montos[1] * 100;
            $tabla_egresos[$i]['id'] = $i;
            $tabla_egresos[$i]['anio'] = $arrayear[$i];
            $tabla_egresos[$i]['presAsignado'] = $vigencias_Montos[1];
            $tabla_egresos[$i]['presEjecutado'] = $presupuesto_ejecutado;
            $tabla_egresos[$i]['presPendiente'] = $presupuesto_ejecutar;
            //$tabla_egresos[$i]['porcentaje'] = $porcentaje_ejecucion;
        }

        $titulos_Egresos = array(CAMPO_ANIO, PRESUPUESTO_ASIGNADO, PRESUPUESTO_EJECUTADO, PRESUPUESTO_EJECUTAR); //, PORCENTAJE_EJECUCION);
        $dt_egresos->setTitleRow($titulos_Egresos);
        $dt_egresos->setDataRows($tabla_egresos);
        $dt_egresos->setType(1);
        $dt_egresos->setFormatRow(array(null, array(2, ',', '.'), array(2, ',', '.'), array(2, ',', '.'))); //array(4, ',', '.')));
        $dt_egresos->setSumColumns(array(2, 3, 4));
        $dt_egresos->setPag(1);
        $dt_egresos->writeDataTable($niv);

        //Generamos la grafica de egresos
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setId('frm_list_ingresos');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', COMPROMISOS_EXPORTAR, 'button', 'onClick=exportar_excel_egresos_interventoria();');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', GRAFICA, 'button', 'onClick=exportar_grafica_egresos_interventoria();');
        $form->setOptions('autoClean', false);
        $form->setMethod('post');
        $form->writeForm();

        //Tabla  Anticipo
        //obtenemos los valores y la tabla de inversion del anticipo
        $dt_inversion = new CHtmlDataTable();
        $dt_inversion->setTitleTable(TITULO_TABLA_INVSERIONES);
        $actividades_inversion = $docData->ObtenerActividaedesInversiondelAnticipo();
        for ($i = 0; $i < count($actividades_inversion); $i++) {
            $id_actividad[$i] = $actividades_inversion[$i]['Id_Actividad'];
            $descripcion_actividad[$i] = $actividades_inversion[$i]['Descripcion_Actividad'];
            $monto_actividad[$i] = $actividades_inversion[$i]['Monto_Actividad'];
            $valor_activdades_ordenes = $docData->obtenerValoresActividadesRegistroInversion($id_actividad[$i]);
            $actividades_ordenes[$i] = $valor_activdades_ordenes[0];
            if (!isset($actividades_ordenes[$i])) {
                $actividades_ordenes[$i] = 0;
            }
            $actividad_a_ejecutar = $monto_actividad[$i] - $actividades_ordenes[$i];
            $porcentaje_ejecucion_actividad = ($actividades_ordenes[$i] / $monto_actividad[$i]) * 100;
            $tabla_de_inversion[$i] = array($I + 1, $descripcion_actividad[$i],
                $monto_actividad[$i], $actividades_ordenes[$i], $actividad_a_ejecutar); //, $porcentaje_ejecucion_actividad . "%");
        }
        $maxString = 0;
        $titulo_inversion = array(REPORTE_ACTIVIDAD, REPORTE_MONTO_ACTIVIDAD, REPORTE_ORDEN_ACTIVIDAD, REPORTE_EJECUTAR); //, PORCENTAJE_EJECUCION_ANTICIPO);
        $dt_inversion->setTitleRow($titulo_inversion);
        $dt_inversion->setDataRows($tabla_de_inversion);
        $dt_inversion->setFormatRow(array(null, array(2, ',', '.'), array(2, ',', '.'), array(2, ',', '.'))); //, array(4, ',', '.')));
        $dt_inversion->setSumColumns(array(2, 3, 4));
        $dt_inversion->setType(1);
        $dt_inversion->setPag(1);
        $dt_inversion->writeDataTable($niv);

        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setId('frm_list_ingresos');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', COMPROMISOS_EXPORTAR, 'button', 'onClick=exportar_excel_ria_interventoria();');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', GRAFICA, 'button', 'onClick=exportar_grafica_ria_interventoria();');
        $form->setOptions('autoClean', false);
        $form->setMethod('post');
        $form->writeForm();
        break;

    /**
     * la variable AgregarIngreso, permite cargar el formulario y los datos 
     * de un objeto Ingreso
     */
    case 'agregarVigencia':
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_AGREGAR_INGRESO);
        $form->setId('frm_agregar_ingreso');
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=guardarIngreso');
        $form->addEtiqueta(AnO_INGRESO);
        $form->addInputDate('date', 'ano_ingreso', 'ano_ingreso', $ano, '%Y', '22', '22', '', 'pattern="' . PATTERN_AÑO . '" title="' . $html->traducirTildes(TITLE_AÑO) . '" required');
        $form->addEtiqueta(MONTO_INGRESO);
        $form->addInputText('text', 'txt_Monto', 'txt_Monto', 15, 19, $monto, '', 'onkeyup="formatearNumero(this);" pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');
        $form->addInputButton('submit', 'ok', 'ok', BOTON_INSERTAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '"');
        $form->writeForm();
        break;

    /**
     * la variable GuardarIngreso, permite cargar la datos de la variable AgregarIngreso 
     * y agregar a la base de datos el objeto ingreso 
     */
    case 'guardarIngreso':

        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setId('frm_ingreso_validar');
        $form->setMethod('post');
        $form->writeForm();
        $Fecha = $_REQUEST['ano_ingreso'];
        $monto = $_REQUEST['txt_Monto'];

        $ingreso = new CVigencia('', '', '', '', new CResumenFinancieroInterventoriaData($db));

        //validamos si el ingreso que se esta ingresando es una adicion o un vigencia 
        if ($ingreso->validarano($Fecha)) {

            echo $html->generaAdvertencia(AnO_ADICION . $Fecha, '?mod=' . $modulo . '&niv=' . $niv .
                    '&task=guardarAdicion&ano_ingreso=' . $Fecha . '&txt_Monto=' . $monto, "cancelarAccionIngreso('frm_ingreso_validar','?mod=" . $modulo . "&niv=" . $niv . "&task=agregarVigencia" . "');");
        } else {

            echo $html->generaAdvertencia(AnO_VIGENCIA . $Fecha, '?mod=' . $modulo . '&niv=' . $niv .
                    '&task=guardarVigencia&ano_ingreso=' . $Fecha . '&txt_Monto=' . $monto, "cancelarAccionIngreso('frm_ingreso_validar','?mod=" . $modulo . "&niv=" . $niv . "&task=agregarVigencia" . "');");
        }


        break;

    //GuardarAdicion permite ingresar un adicion en la base de datos
    case 'guardarAdicion':

        $Fecha = $_REQUEST['ano_ingreso'];
        $monto = str_replace(".", "", $_REQUEST['txt_Monto']);
        $tipo = 'Adición';
        $insertarIngreso = $docData->insertarVigencia('', $Fecha, $monto, $tipo);
        echo $html->generaAviso($insertarIngreso, "?mod=" . $modulo . "&niv=" . $niv . "&task=list", "cancelarAccionIngreso('frm_agregar_ingreso','?mod=" . $modulo . "&niv=" . $niv . "');");


        break;

    //GuardarAdicion permite ingresar un vigencia en la base de datos
    case 'guardarVigencia':

        $Fecha = $_REQUEST['ano_ingreso'];
        $monto = str_replace(".", "", $_REQUEST['txt_Monto']);
        $tipo = 'Vigencia';
        $insertarIngreso = $docData->insertarVigencia('', $Fecha, $monto, $tipo);
        echo $html->generaAviso($insertarIngreso, "?mod=" . $modulo . "&niv=" . $niv . "&task=list", "cancelarAccionIngreso('frm_agregar_ingreso','?mod=" . $modulo . "&niv=" . $niv . "');");

        break;

    /**
     * la variable borrarIngreso,  cargar los datos del objeto ingreso que se 
     * va a eliminar y los envia a la variable ConfirmarBorrar 
     */
    case 'borrarVigencia':

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
        $objIngreso = $docData->obtenerIngresoPorId($id_delete);
        $indice = $objIngreso->validarelimavigencia($objIngreso->getano());
        if ($objIngreso->gettipo() == 'Vigencia' && $indice == 1) {
            echo $html->generaAviso(ALERTA_INGRESO . $Fecha, '?mod=' . $modulo . '&niv=' . $niv .
                    '&task=list');
        } else {
            echo $html->generaAdvertencia(ELIMINAR_INGRESO, '?mod=' . $modulo . '&niv=' . $niv .
                    '&task=confirmarBorrar&id_element=' . $id_delete . '&ano_ingreso=' . $Fecha . '&txt_Monto=' . $monto, "cancelarAccionIngreso('frm_borrar_ingreso','?mod=" . $modulo . "&niv=" . $niv . "');");
        }
        break;
    /**
     * la variable Confirmarborar, permite eliminar el objeto de la base de datos
     */
    case 'confirmarBorrar':

        $id_delete = $_REQUEST['id_element'];
        $Fecha = $_REQUEST['ano_ingreso'];
        $monto = $_REQUEST['txt_Monto'];
        $ingreso = new CIngresos($id_delete, '', '', '', $docData);
        $eliminado = $ingreso->EliminarIngreso($id_delete);

        echo $html->generaAviso($eliminado, "?mod=" . $modulo . "&niv=" . $niv . "&task=list", "cancelarAccionIngreso('frm_borrar_ingreso','?mod=" . $modulo . "&niv=" . $niv . "');");

        break;

    /**
     * la variable editarIngreso, genera un formulario y carga los datos del 
     * objeto ingreso que se va a editar
     */
    case 'editarVigencia':
        $id_edit = $_REQUEST['id_element'];
        $monto = $_REQUEST['txt_Monto'];

        $ingreso = $docData->obtenerIngresoPorId($id_edit);

        if (!isset($_REQUEST['txt_Monto_edit']) || $_REQUEST['txt_Monto_edit'] != '') {
            $monto_edit = $ingreso->getmonto();
        } else {
            $monto_edit = $_REQUEST['txt_Monto_edit'];
        }
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_INGRESO);
        $form->setId('frm_editar_ingreso');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->addInputText('hidden', 'txt_id', 'txt_id', '15', '15', $ingreso->getIdIngreso(), '', '');

        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=GuardarEdicion');
        $form->addEtiqueta(MONTO_INGRESO);
        $form->addInputText('text', 'txt_Monto', 'txt_Monto', 15, 19, $monto_edit, '', 'onkeyup="formatearNumero(this);" pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');
        $form->addInputButton('submit', 'ok', 'ok', BOTON_EDITAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '"');
        $form->writeForm();

        break;
    /**
     * la variable GuardarEdicion, permite guardar los atributos del objeto ingreso
     * modificados en la base de datos 
     */
    case 'GuardarEdicion':
        $id_edit = $_REQUEST['txt_id'];
        $monto_edit = str_replace(".", "", ($_REQUEST['txt_Monto']));
        $ingreso = $docData->obtenerIngresoPorId($id_edit);
        $id = $ingreso->getIdIngreso();
        $edicion = $ingreso->actualizarIngresos($id, $monto_edit);
        echo $html->generaAviso($edicion, "?mod=" . $modulo . "&niv=" . $niv . "&task=list");
        break;
    case 'GraficasIngresos':
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_INGRESOS);
        $form->setId('frm_agregar_ingreso');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();

        $temp = $docData->ObtenerVigenciasGraficaIngresos();

        $data = '[ ';
        for ($i = 0; $i < count($temp); $i++) {
            $data.= '{ "Ingresos" : "' . $html->traducirTildes($temp[$i]['id']) . '",'
                    . '"Recursos" : ' . $temp[$i]['total'] . ','
                    . '"temp" : ' . $temp[$i]['monto'] . '}';
            if ($i < (count($temp) - 1)) {
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
                graph1.title = "Recursos";
                graph1.lineColor = "#54acd2";
                graph1.valueField = "Recursos";
                graph1.lineAlpha = 1;
                graph1.fillAlphas = 1;
                graph1.dashLengthField = "dashLengthColumn";
                graph1.alphaField = "alpha";
                graph1.balloonText = "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>";
                chart.addGraph(graph1);

                // line
                var graph2 = new AmCharts.AmGraph();
                graph2.type = "line";
                graph2.title = "Vigencia";
                graph2.lineColor = "#fcd202";
                graph2.valueField = "temp";
                graph2.lineThickness = 3;
                graph2.bullet = "round";
                graph2.bulletBorderThickness = 3;
                graph2.bulletBorderColor = "#fcd202";
                graph2.bulletBorderAlpha = 1;
                graph2.bulletColor = "#ffffff";
                graph2.dashLengthField = "dashLengthLine";
                graph2.balloonText = "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>";
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
    case 'GraficasEgresos':
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(GRAFICA_EGRESOS);
        $form->setId('frm_agregar_ingreso');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        //obtenemos loa años de viegencias
        $Years = $docData->ObtenerYears();

        for ($i = 0; $i < count($Years); $i++) {
            $arrayear[$i] = $Years[$i]['ano_Vigencia'];
        }
//generamos loa valores y la tabla de egresos
        $vigenciaObjetivo = 2013;
        $actividadObjetivo = ACTIVIDAD_PIA;
        $presupuesto_ejecutado = 0;
        $saldo = 0;
        for ($i = 0; $i < count($arrayear); $i++) {
            if ($arrayear[$i] == $vigenciaObjetivo) {
                $presupuesto_ejecutado = $docData->obtenerTotalActividades();
            } else {
                $presupuesto_ejecutado = $docData->obtenerInformeFinanciero($arrayear[$i]);
            }
            $vigencias_Montos = $docData->obtenerValoresVigencia($arrayear[$i]);
            if (isset($presupuesto_ejecutado[0])) {
                $presupuesto_ejecutado = $presupuesto_ejecutado[0];
            } else {
                $presupuesto_ejecutado = 0;
            }
            $presupuesto_ejecutar = $vigencias_Montos[1] - $presupuesto_ejecutado;

            $porcentaje_ejecucion = $presupuesto_ejecutado / $vigencias_Montos[1] * 100;
            $tabla_egresos[$i]['id'] = $i;
            $tabla_egresos[$i]['anio'] = $arrayear[$i];
            $tabla_egresos[$i]['presAsignado'] = $vigencias_Montos[1];
            $tabla_egresos[$i]['presEjecutado'] = $presupuesto_ejecutado;
            $tabla_egresos[$i]['presPendiente'] = $presupuesto_ejecutar;
            //$tabla_egresos[$i]['porcentaje'] = $porcentaje_ejecucion;
        }
        $temp = $tabla_egresos;

        $data = '[ ';
        for ($i = 0; $i < count($temp); $i++) {
            echo $html->traducirTildes($temp[$i]['res_vigen']);
            $data.= '{ "Ingresos" : "' . $html->traducirTildes($temp[$i]['anio']) . '",'
                    . '"Recursos" : ' . $temp[$i]['presAsignado'] . ','
                    . '"temp" : ' . $temp[$i]['presEjecutado'] . '}';
            if ($i < (count($temp) - 1)) {
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
    case 'GraficasRIA':
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(GRAFICA_INVERSION_DEL_ANTICIPO);
        $form->setId('frm_agregar_ingreso');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        $actividades_inversion = $docData->ObtenerActividaedesInversiondelAnticipo();
        for ($i = 0; $i < count($actividades_inversion); $i++) {
            $id_actividad[$i] = $actividades_inversion[$i]['Id_Actividad'];
            $descripcion_actividad[$i] = $actividades_inversion[$i]['Descripcion_Actividad'];
            $monto_actividad[$i] = $actividades_inversion[$i]['Monto_Actividad'];
            $valor_activdades_ordenes = $docData->obtenerValoresActividadesRegistroInversion($id_actividad[$i]);
            $actividades_ordenes[$i] = $valor_activdades_ordenes[0];
            if (!isset($actividades_ordenes[$i])) {
                $actividades_ordenes[$i] = 0;
            }
            $actividad_a_ejecutar = $monto_actividad[$i] - $actividades_ordenes[$i];
            $porcentaje_ejecucion_actividad = ($actividades_ordenes[$i] / $monto_actividad[$i]) * 100;
            $tabla_de_inversion[$i] = array($i + 1, $descripcion_actividad[$i],
                $monto_actividad[$i], $actividades_ordenes[$i], $actividad_a_ejecutar); //, $porcentaje_ejecucion_actividad . "%");
        }
        $temp = $tabla_de_inversion;

        $data = '[ ';
        for ($i = 0; $i < count($temp); $i++) {
            echo $html->traducirTildes($temp[$i]['res_vigen']);
            $data.= '{ "Ingresos" : "' . $html->traducirTildes($temp[$i][0]) . '",'
                    . '"Recursos" : ' . $temp[$i][2] . ','
                    . '"temp" : ' . $temp[$i][3] . '}';
            if ($i < (count($temp) - 1)) {
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