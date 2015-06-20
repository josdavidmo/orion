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

$numerotablas = $_REQUEST['numeroTablas'];
if ($numerotablas == NULL) {
    $numerotablas = 1;
}
/** 
 * En estos arreglos se almacenan las tablas y columnas seleccionadas,
 * la idea es construir el query y colocarlo en el area de texto 'query'.
 * Finalmente cuando el usuario de clic en el boton Generar reporte el programa
 * hara el resto.
 */
$tablasSeleccionadas = array();
$camposSeleccionados = array();
for ($i = 0; $i < $numerotablas; $i++) {
    $tablasSeleccionadas[] = $_REQUEST["select_tabla_$i"];
    $camposSeleccionados[] = $_REQUEST["select_columna_$i"];
}

$data = "0";
$fields = "0";
$query = $_REQUEST['query'];
if ($query != NULL) {
    $elementos = $conData->ejecutarConsultaGenerada($query);
    $data = json_encode($elementos, JSON_NUMERIC_CHECK);
}
?>
<html lang="es">
    <head>
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

        <script>
            function agregarTabla() {
                var form = document.getElementById("form_tabla");
                form.action = "reporteador.php?numeroTablas=<?= ($numerotablas + 1) ?>";
                form.submit();
            }

            data = <?= $data ?>;
            fields = <?= $fields ?>;
        </script>
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
            <form id="form_query" class="form-horizontal" role="form" action="reporteador.php" method="post"> 
                <div class="form-group">
                    <label for="query" class="col-lg-2 control-label">Query</label>
                    <div class="col-lg-10">
                        <textarea id="query" name="query" class="form-control">SELECT * FROM actividad</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        <button type="submit" class="btn btn-default">Generar Reporte</button>
                    </div>
                </div>
            </form>
            <form id="form_tabla" class="form-horizontal" role="form" action="reporteador.php?numeroTablas=<?= $numerotablas ?>" method="post">
                <?php for ($i = 0; $i < $numerotablas; $i++) { ?>
                    <div class="form-group">
                        <label for="select_tabla_<?= $i ?>" class="col-lg-2 control-label">Tabla</label>
                        <div class="col-lg-10">
                            <select class="form-control" id="select_tabla" name="select_tabla_<?= $i ?>" onchange="this.form.submit()">
                                <option value="">Seleccione una</option>
                                <?php $tablas = $conData->consultarTablas(); ?>
                                <?php foreach ($tablas as $tabla) { ?>
                                    <?php if ($tabla['value'] == $tablasSeleccionadas[$i]) { ?>
                                        <option value="<?= $tabla['value'] ?>" selected><?= $tabla['texto'] ?></option>
                                    <?php } else { ?>
                                        <option value="<?= $tabla['value'] ?>"><?= $tabla['texto'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select_columna_<?= $i ?>" class="col-lg-2 control-label">Columnas</label>
                        <div class="col-lg-10">
                            <select class="form-control" id="select_columna" name="select_columna_<?= $i ?>[]" multiple>
                                <?php $columnas = $conData->consultarCampos($tablasSeleccionadas[$i]); ?>
                                <?php foreach ($columnas as $columna) { ?>
                                    <?php if (in_array($columna['value'], $camposSeleccionados[$i])) { ?>
                                        <option value="<?= $columna['value'] ?>" selected><?= $columna['texto'] ?></option>
                                    <?php } else { ?>
                                        <option value="<?= $columna['value'] ?>"><?= $columna['texto'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        <button onclick="agregarTabla()" class="btn btn-default">Agregar Tabla</button>
                    </div>
                </div>
            </form>

            <div class="data-explorer-here"></div>
            <div style="clear: both;"></div>

            <script src="templates/report/app.js" type="text/javascript"></script>
        </div>
    </body>
</html>
