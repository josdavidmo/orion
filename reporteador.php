<!DOCTYPE html>
<?php
include "clases/datos/CData.php";
include "clases/aplicacion/CDataLog.php";
include "clases/datos/CConsultaData.php";
include('config/conf.php');
include('config/constantes.php');
$db = new CData(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$db->conectar();
$conData = new CConsultaData($db);

$selectedTable = $_REQUEST['select_tabla'];
$campos = $_REQUEST['select_columna'];
$data = "0";
if ($campos != NULL) {
    $columnas = implode(",", $campos);
    $query = "SELECT $columnas FROM $selectedTable";
    $elementos = $conData->ejecutarConsultaGenerada($query);
    $data = json_encode($elementos, JSON_NUMERIC_CHECK);
}
$infoCampos = array();
foreach ($campos as $campo) {
    $info = $conData->getTipoColumna($selectedTable, $campo);
    switch ($info) {
        case "int":
            $info = "number";
            break;
        
        case "double":
            $info = "number";
            break;
        
        case "datetime":
            $info = "date";
            break;
        
        case "date":
            $info = "date";
            break;
        
        default:
            $info = "string";
            break;
    }
    $aux = array("id" => $campo, "type" => $info);
    $infoCampos[] = $aux;
}
$fields = json_encode($infoCampos);
?>
<html lang="es">
    <head>
        <script>
            data = <?= $data?>;
            fields = <?= $fields?>;
        </script>
        <meta charset="utf-8" />
        <title>Reporteador</title>
        <!-- you do not have to use bootstrap but we use it by default -->
        <link rel="stylesheet" href="templates/report/vendor/bootstrap/3.2.0/css/bootstrap.css">
        <!-- vendor css -->
        <link href="templates/report/vendor/leaflet/0.7.3/leaflet.css" rel="stylesheet">
        <link href="templates/report/vendor/leaflet.markercluster/MarkerCluster.css" rel="stylesheet">
        <link href="templates/report/vendor/leaflet.markercluster/MarkerCluster.Default.css" rel="stylesheet">
        <link rel="stylesheet" href="templates/report/vendor/slickgrid/2.2/slick.grid.css">

        <!-- recline css -->
        <link href="templates/report/css/map.css" rel="stylesheet">

        <link href="templates/report/css/multiview.css" rel="stylesheet">
        <link href="templates/report/css/slickgrid.css"rel="stylesheet">
        <link href="templates/report/css/flot.css" rel="stylesheet">

        <!-- Vendor JS - general dependencies -->
        <script src="templates/report/vendor/jquery/1.7.1/jquery.js" type="text/javascript"></script>
        <script src="templates/report/vendor/underscore/1.4.4/underscore.js" type="text/javascript"></script>
        <script src="templates/report/vendor/backbone/1.0.0/backbone.js" type="text/javascript"></script>
        <script src="templates/report/vendor/mustache/0.5.0-dev/mustache.js" type="text/javascript"></script>
        <script src="templates/report/vendor/bootstrap/3.2.0/js/bootstrap.js" type="text/javascript"></script>

        <!-- Vendor JS - view dependencies -->
        <script src="templates/report/vendor/leaflet/0.7.3/leaflet.js" type="text/javascript"></script>
        <script src="templates/report/vendor/leaflet.markercluster/leaflet.markercluster.js" type="text/javascript"></script>
        <script type="text/javascript" src="templates/report/vendor/flot/jquery.flot.js"></script>
        <script type="text/javascript" src="templates/report/vendor/flot/jquery.flot.time.js"></script>
        <script type="text/javascript" src="templates/report/vendor/moment/2.0.0/moment.js"></script>
        <script src="templates/report/vendor/slickgrid/2.2/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="templates/report/vendor/slickgrid/2.2/jquery.event.drag-2.2.js"></script>
        <script src="templates/report/vendor/slickgrid/2.2/jquery.event.drop-2.2.js"></script>
        <script src="templates/report/vendor/slickgrid/2.2/slick.core.js"></script>
        <script src="templates/report/vendor/slickgrid/2.2/slick.formatters.js"></script>
        <script src="templates/report/vendor/slickgrid/2.2/slick.editors.js"></script>
        <script src="templates/report/vendor/slickgrid/2.2/slick.grid.js"></script>
        <script src="templates/report/vendor/slickgrid/2.2/plugins/slick.rowselectionmodel.js" type="text/javascript"></script>
        <script src="templates/report/vendor/slickgrid/2.2/plugins/slick.rowmovemanager.js" type="text/javascript"></script>

        <!-- Recline JS (combined distribution, all views) -->
        <script src="templates/report/dist/recline.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="container">
            <style type="text/css">
                .recline-slickgrid {
                    height: 300px;
                }

                .changelog {
                    display: none;
                    border-bottom: 1px solid #ccc;
                    margin-bottom: 10px;
                }
            </style>

            <form lass="form-horizontal" role="form" action="reporteador.php" method="post">
                <div class="form-group">
                    <label for="select_tabla" class="col-lg-2 control-label">Tabla</label>
                    <div class="col-lg-10">
                        <select class="form-control" id="select_tabla" name="select_tabla" onchange="this.form.submit()" required>
                            <option value="">Seleccione una</option>
                            <?php $tablas = $conData->consultarTablas(); ?>
                            <?php foreach ($tablas as $tabla) { ?>
                                <?php if ($tabla['value'] == $selectedTable) { ?>
                                    <option value="<?= $tabla['value'] ?>" selected><?= $tabla['texto'] ?></option>
                                <?php } else { ?>
                                    <option value="<?= $tabla['value'] ?>"><?= $tabla['texto'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="select_columna" class="col-lg-2 control-label">Columnas</label>
                    <div class="col-lg-10">
                        <select class="form-control" id="select_columna" name="select_columna[]" multiple required>
                            <?php $columnas = $conData->consultarCampos($selectedTable); ?>
                            <?php foreach ($columnas as $columna) { ?>
                                <option value="<?= $columna['value'] ?>"><?= $columna['texto'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        <button type="submit" class="btn btn-default">Generar Reporte</button>
                    </div>
                </div>
            </form>

            <div class="data-explorer-here"></div>
            <div style="clear: both;"></div>

            <script src="templates/report/app.js" type="text/javascript"></script>
        </div>
    </body>
</html>
