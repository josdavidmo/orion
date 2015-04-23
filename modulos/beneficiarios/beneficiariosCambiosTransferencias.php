<?php
/**
 * Modulo Cambios y Transferencias
 * Maneja el modulo cambios y transferencias beneficiarios 
 * en union con CBeneficiario, CHistorialCambiosBeneficiario, CBeneficiarioData
 *
 * @see \CBeneficiario
 * @see \CHistorialCambiosBeneficiario
 * @see \CBeneficiarioData
 *
 * @package modulos
 * @subpackage beneficiarios
 * @author SERTIC SAS
 * @version 2014.10.26
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoBeneficiarios = new CBeneficiarioData($db);
$daoBasicas = new CBasicaData($db);
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
        $titulo = TITULO_CAMBIOSYTRANSFERENCIAS;
        
        $form = new CHtmlForm();
        $form->setId('frm_filtrar_beneficiarios');
        $form->setTitle($titulo);
        //$form->setAction("?mod=" . $modulo . "&niv=" . $niv . "&tipo=" . $tipoPagina);
        //$form->setMethod("post");
        $form->setOptions("autoClean", false);
        //$form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_EXPORTAR, 'button', '');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable($titulo);
        $titulos = array(BENEFICIARIO1_CAMBIOSYTRANSFERENCIAS,
            BENEFICIARIO2_CAMBIOSYTRANSFERENCIAS,
            TIPO_CAMBIOSYTRANSFERENCIAS,
            FECHA_CAMBIOSYTRANSFERENCIAS,
            SOPORTE_CAMBIOSYTRANSFERENCIAS,
            OBSERVACIONES_CAMBIOSYTRANSFERENCIAS);
        $historial = $daoBeneficiarios->getHistorialCambiosBeneficiarios();
        $dt->setTitleRow($titulos);
        $dt->setDataRows($historial);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;
    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto historial cambios beneficiarios 
     * @see \CHistorialCambiosBeneficiario
     */
    case 'add':
        $form = new CHtmlForm();

        $opciones = null;

        $form->setTitle(TITULO_AGREGAR_CAMBIOSYTRANSFERENCIAS);
        $form->setId('frm_add_historial');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd');
        $form->setMethod('post');

        $beneficiarios = $daoBeneficiarios->getBeneficiarios();
        $opciones = null;
        if (isset($beneficiarios)) {
            foreach ($beneficiarios as $beneficiario) {
                $opciones[count($opciones)] = array('value' => $beneficiario['idBeneficiario'],
                    'texto' => $beneficiario['centropoblado']." - ".$beneficiario['tipo']." - ".$beneficiario['nombre']);
            }
        }

        $form->addEtiqueta(BENEFICIARIO1_CAMBIOSYTRANSFERENCIAS);
        $form->addSelect('select', 'sel_beneficiario1', 'sel_beneficiario1', $opciones, '', '', '', ' required');

        $form->addEtiqueta(BENEFICIARIO2_CAMBIOSYTRANSFERENCIAS);
        $form->addSelect('select', 'sel_beneficiario2', 'sel_beneficiario2', $opciones, '', '', '', ' required');
        
        $tipos = $daoBasicas->getBasicas("tipocambiobeneficiario");
        $opciones = null;
        if (isset($tipos)) {
            foreach ($tipos as $tipo) {
                $opciones[count($opciones)] = array('value' => $tipo->getId(),
                    'texto' => $tipo->getDescripcion());
            }
        }

        $form->addEtiqueta(TIPO_CAMBIOSYTRANSFERENCIAS);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', '', '', ' required');
        
        $form->addEtiqueta(FECHA_CAMBIOSYTRANSFERENCIAS);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', '', '%Y-%m-%d', '16', '16', '', ' required');
        
        $form->addEtiqueta(SOPORTE_CAMBIOSYTRANSFERENCIAS);
        $form->addInputFile('file','file_archivo','file_archivo','25','file',' required');
			
        $form->addEtiqueta(OBSERVACIONES_CAMBIOSYTRANSFERENCIAS);
        $form->addTextArea('textarea','txt_observaciones','txt_observaciones','30','5','','','');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_historial\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto historial cambios 
     * y transferencias en la base de datos @see \CHistorialCambiosBeneficiario
     */
    case 'saveAdd':
        $beneficiario1 = $_REQUEST['sel_beneficiario1'];
        $beneficiario2 = $_REQUEST['sel_beneficiario2'];
        $tipo = $_REQUEST['sel_tipo'];
        $fecha = $_REQUEST['txt_fecha'];
        $soporte = $_FILES['file_archivo'];
        $observaciones = $_REQUEST['txt_observaciones'];
        
        $historial = new CHistorialCambiosBeneficiario(NULL, 
                                                       $beneficiario1, 
                                                       $beneficiario2, 
                                                       $tipo, 
                                                       $fecha, 
                                                       $soporte, 
                                                       $observaciones);
        
        $r = $daoBeneficiarios->insertHistorialCambiosBeneficiarios($historial);
        $m = ERROR_AGREGAR_CAMBIOSYTRANSFERENCIAS;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_CAMBIOSYTRANSFERENCIAS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");

        break;
    /**
     * la variable delete, permite hacer la carga del objeto historial beneficiario 
     * y espera confirmacion de eliminarlo @see \CHistorialCambiosBeneficiario
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_BENEFICIARIOS, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1\'');
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto historial 
     * beneficiario de la base de datos @see \CHistorialCambiosBeneficiario
     */
    case 'confirmDelete':
        $id_delete = $_REQUEST['id_element'];
        $r = $daoBeneficiarios->deleteHistorialCambiosBeneficiariosById($id_delete);
        $m = ERROR_BORRAR_CAMBIOSYTRANSFERENCIAS;
        if ($r == 'true') {
            $m = EXITO_BORRAR_CAMBIOSYTRANSFERENCIAS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");
        break;

    /**
     * la variable edit, permite hacer la carga del objeto historial 
     * beneficiario y espera confirmacion de edicion @see \CHistorialCambiosBeneficiario
     */
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $historial = $daoBeneficiarios->getHistorialCambiosBeneficiariosById($id_edit);
        
        $form = new CHtmlForm();

        $opciones = null;

        $form->setTitle(TITULO_AGREGAR_CAMBIOSYTRANSFERENCIAS);
        $form->setId('frm_edit_historial');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&id_element='.$id_edit);
        $form->setMethod('post');

        $beneficiarios = $daoBeneficiarios->getBeneficiarios();
        $opciones = null;
        if (isset($beneficiarios)) {
            foreach ($beneficiarios as $beneficiario) {
                $opciones[count($opciones)] = array('value' => $beneficiario['idBeneficiario'],
                    'texto' => $beneficiario['centropoblado']." - ".$beneficiario['tipo']." - ".$beneficiario['nombre']);
            }
        }

        $form->addEtiqueta(BENEFICIARIO1_CAMBIOSYTRANSFERENCIAS);
        $form->addSelect('select', 'sel_beneficiario1', 'sel_beneficiario1', $opciones, '', $historial->getBeneficiario1(), '', ' required');

        $form->addEtiqueta(BENEFICIARIO2_CAMBIOSYTRANSFERENCIAS);
        $form->addSelect('select', 'sel_beneficiario2', 'sel_beneficiario2', $opciones, '', $historial->getBeneficiario2(), '', ' required');
        
        $tipos = $daoBasicas->getBasicas("tipocambiobeneficiario");
        $opciones = null;
        if (isset($tipos)) {
            foreach ($tipos as $tipo) {
                $opciones[count($opciones)] = array('value' => $tipo->getId(),
                    'texto' => $tipo->getDescripcion());
            }
        }

        $form->addEtiqueta(TIPO_CAMBIOSYTRANSFERENCIAS);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', $historial->getTipoCambioBeneficiario(), '', ' required');
        
        $form->addEtiqueta(FECHA_CAMBIOSYTRANSFERENCIAS);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', $historial->getFecha(), '%Y-%m-%d', '16', '16', '', '');
        
        $form->addEtiqueta(SOPORTE_CAMBIOSYTRANSFERENCIAS);
        $form->addInputFile('file','file_archivo','file_archivo','25','file','');
			
        $form->addEtiqueta(OBSERVACIONES_CAMBIOSYTRANSFERENCIAS);
        $form->addTextArea('textarea','txt_observaciones','txt_observaciones','30','5',$historial->getObservaciones(),'','');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_historial\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;

    /**
     * la variable saveEdit, permite actualizar el objeto historial beneficiario
     * en la base de datos @see \CHistorialCambiosBeneficiario
     */
    case 'saveEdit':
        $idHistorial = $_REQUEST['id_element'];
        $beneficiario1 = $_REQUEST['sel_beneficiario1'];
        $beneficiario2 = $_REQUEST['sel_beneficiario2'];
        $tipo = $_REQUEST['sel_tipo'];
        $fecha = $_REQUEST['txt_fecha'];
        $soporte = $_FILES['file_archivo'];
        $observaciones = $_REQUEST['txt_observaciones'];
        
        $historial = new CHistorialCambiosBeneficiario($idHistorial, 
                                                       $beneficiario1, 
                                                       $beneficiario2, 
                                                       $tipo, 
                                                       $fecha, 
                                                       $soporte, 
                                                       $observaciones);
        
        $r = $daoBeneficiarios->updateHistorialCambiosBeneficiarios($historial);
        $m = ERROR_EDITAR_CAMBIOSYTRANSFERENCIAS;
        if ($r == 'true') {
            $m = EXITO_EDITAR_CAMBIOSYTRANSFERENCIAS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");

        break;

    case 'see':
        $idBeneficiario = $_REQUEST['id_element'];
        
        $tipo = $_REQUEST['tipo'];
        $form = new CHtmlForm();

        $opciones = null;

        $form->setTitle(TITULO_UBICACION);
        $form->setId('frm_volver_beneficiario');
        $form->setAction('?mod=' . $modulo . '&niv=1&tipo='.$tipo);
        $form->setMethod('post');
        $form->setOptions("autoClean", false);
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ATRAS, 'button', '');
        $form->writeForm();
        ?>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
        <script type="text/javascript">
            //<![CDATA[

            var customIcons = {
                1: {
                    icon: 'http://labs.google.com/ridefinder/images/mm_20_green.png'
                },
                2: {
                    icon: 'http://labs.google.com/ridefinder/images/mm_20_red.png'
                }
            };

            function load() {
				var map = new google.maps.Map(document.getElementById("map"), {
                            center: new google.maps.LatLng(4.08972222, -72.9619444),
                            zoom: 5,
                            mapTypeId: 'terrain'
                        });
                var infoWindow = new google.maps.InfoWindow;
                // Change this depending on the name of your PHP file
                downloadUrl("modulos/beneficiarios/MapsBeneficiario.php?idBeneficiario=<?= $idBeneficiario ?>&tipo=<?= $tipo?>", function(data) {
					var xml = data.responseXML;
                    var markers = xml.documentElement.getElementsByTagName("marker");
                    for (var i = 0; i < markers.length; i++) {
                        var name = markers[i].getAttribute("name");
                        var address = markers[i].getAttribute("address");
                        var type = markers[i].getAttribute("type");
						var point = new google.maps.LatLng(
                                parseFloat(markers[i].getAttribute("lat")),
                                parseFloat(markers[i].getAttribute("lng")));
                        var html = "<b>" + name + "</b> <br/>" + address;
                        var icon = customIcons[type] || {};
                        var marker = new google.maps.Marker({
                            map: map,
                            position: point,
                            icon: icon.icon
                        });
                        bindInfoWindow(marker, map, infoWindow, html);
                    }
                });
            }

            function bindInfoWindow(marker, map, infoWindow, html) {
                google.maps.event.addListener(marker, 'click', function() {
                    infoWindow.setContent(html);
                    infoWindow.open(map, marker);
                });
            }

            function downloadUrl(url, callback) {
                var request = window.ActiveXObject ?
                        new ActiveXObject('Microsoft.XMLHTTP') :
                        new XMLHttpRequest;

                request.onreadystatechange = function() {
                    if (request.readyState == 4) {
                        request.onreadystatechange = doNothing;
                        callback(request, request.status);
                    }
                };

                request.open('GET', url, true);
                request.send(null);
            }

            function doNothing() {
            }
			

            //]]>
        </script>
        <center><div id="map" style="width: 1200px; height: 600px"></div></center>
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


