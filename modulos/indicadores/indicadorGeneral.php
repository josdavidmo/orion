<?php

/**
 * Modulo PQR
 * Maneja el modulo pqr en union con CPQR, CPQRData
 *
 * @see \CPQR
 * @see \CPQRData
 *
 * @package modulos
 * @subpackage indicadores
 * @author SERTIC SAS
 * @version 2014.11.26
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoBeneficiarios = new CBeneficiarioData($db);
$daoBasicas = new CBasicaData($db);
$daoPQR = new CPQRData($db);
$task = $_REQUEST['task'];
$modulo = $_REQUEST['mod'];
$niv = $_REQUEST['niv'];
if (empty($task))
    $task = 'list';

switch ($task) {
    
    /**
     * la variable list, permite hacer la carga la página con la lista de
     * objetos beneficiarios según los parámetros de entrada
     */
    case 'list':
        $periodo = date('Y-m').'-00';
        if(isset($_REQUEST['txt_periodo'])){
            $periodo = $_REQUEST['txt_periodo'].'-00';
        }
        $indicador = $daoPQR->getIndicadorGeneral($periodo);
        $periodo = substr($periodo, 0, -3);
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_GRAFICA_INDISPONIBILIDAD_GENERAL);
        $form->setId('frm_indicador_ingreso');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id_element . '&task=list');
        $form->addEtiqueta(PERIODO_PARAFISCALES);
        $form->addInputText('month', 'txt_periodo', 'txt_periodo', '50', '50', 
                            $periodo, '', '');
        $form->addInputButton('button', 'btn_ver', 'btn_ver', BTN_VOLVER, 'button', 'onclick="location.href=\'?mod=' . $modulo . '&niv=' . $niv . '&task=seePQRs&id_element='. $id_element . '\'"');
        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->writeForm();
        ?>
        <script src="././clases/Graficas/amcharts/amcharts.js" type="text/javascript"></script>
        <script type="text/javascript" src="././clases/Graficas/amcharts/pie.js"></script>
        <script type="text/javascript" src="h././clases/Graficas/amcharts/themes/light.js"></script>
        <script>
            var chart = AmCharts.makeChart("chartdiv", {
                "type": "pie",
                "theme": "none",
                "dataProvider": [
                    {
                        "label": "Minutos Disponible",
                        "minutos": <?= $indicador['minutosDisponible'] ?>
                    },
                    {
                        "label": "Minutos Indisponible",
                        "minutos": <?= $indicador['minutosIndisponible'] ?>
                    }],
                "valueField": "minutos",
                "titleField": "label",
                "radius": "35%",
                "labelRadius": 2,
                "colors": ["#54acd2", "#fcd202"],
                "labelText": "[[percents]]%"
            });
            
            
        </script>
        <div id="chartdiv" style="width: 100%; height: 400px;"></div>
        <?php

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


