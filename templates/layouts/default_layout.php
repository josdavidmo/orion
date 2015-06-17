<?php
/**
 * Gestion Interventoria - Gestin
 *
 * <ul>
 * <li> Redcom Ltda <www.redcom.com.co></li>
 * <li> Proyecto PNCAV</li>
 * </ul>
 */
/**
 * Default_layout
 *
 * @package  templates
 * @subpackage layouts
 * @author Redcom Ltda
 * @version 2013.01.00
 * @copyright SERTIC - MINTICS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access to this level');
set_time_limit(0);
$html = new CHtml(APP_TITLE);
$html->addEstilo('bootstrap.min.css');
$html->addEstilo('calendar/calendar-blue.css');
$html->addScript('base/jquery-latest.js');
$html->addScript('base/bootstrap.min.js');
$handle = opendir('./templates/scripts/validar/');
while ($file = readdir($handle)) {
    if ($file != "." && $file != "..") {
        //include($file);
        if (substr($file, strlen($file) - 3) == '.js') {
            $html->addScript('validar/' . $file);
        }
    }
}
$html->addScript('calendar/calendar.js');
$html->addScript('calendar/calendar-es.js');
$html->addScript('calendar/calendar-setup.js');
$html->addScript('base/jquery.tablesorter.js');
$html->addScript('base/jquery.tablesorter.pager.js');
$html->abrirHtml();
?>

<?php
?>
<div style="background-color: #1e2260; width: 101%" class="row">
    <div style="padding-left: 8%" class="col-md-4">
        <img src="./templates/img/foot-sertic.png"/>
    </div>
    <div class="col-md-4">
        <blockquote style="background-color: #1e2260">
            <h1 class="text-center" style='color:white;'><strong>PNCAV</strong></h1>
            <h6 class="text-center" style='color:white;'>Proyecto Nacional de Conectividad de Alta Velocidad</h6>
        </blockquote>
    </div>
    <div class="col-md-4">
        <img src="./templates/img/foot-mintic.png"/>
    </div>
</div>
<nav class="navbar navbar-inverse" style="background-color: #54acd2;" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex6-collapse">
            <span class="sr-only">Desplegar navegación</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#" style='color:white;'>MEN&Uacute;</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex6-collapse">
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <?php
                $cont = 0;
                while ($row = mysql_fetch_array($opciones)) {
                    if ($row["opc_variable"] != "_blank") {
                        if ($row["opn_id"] == 0) {
                            if ($cont == 1) {
                                echo "</ul>";
                                $cont--;
                            }
                            echo "<li class='dropdown'>"
                            . "<a href='#' class='dropdown-toggle' "
                            . "data-toggle='dropdown' ><b>" .
                            $html->traducirTildes($row["opc_nombre"]) .
                            "</b><b class='caret'></b></a>";
                        }
                        if ($row["opn_id"] == 1) {
                            if ($cont == 0) {
                                echo "<ul class='dropdown-menu'>";
                                $cont++;
                            }
                            echo "<li style='background-color:#54acd2;'><a href='?mod=" . $row["opc_variable"] .
                            "&niv=" . $row["pxo_nivel"] .
                            "&operador=" . $row["ope_id"] .
                            "'>" .
                            $html->traducirTildes($row["opc_nombre"])
                            . "</a>";
                        }
                    } else {
                        if ($row["opn_id"] == 0) {
                            if ($cont == 1) {
                                echo "</ul></li>";
                                $cont--;
                            }
                            echo $html->traducirTildes($row["opc_nombre"]);
                        }
                        if ($row["opn_id"] == 1) {
                            if ($cont == 0) {
                                echo "<ul>";
                                $cont++;
                            }
                            echo "<li><a href='" . $row["opc_url"] . "' target='_blank'>" . $html->traducirTildes($row["opc_nombre"]) . "</a>";
                        }
                    }
                }
                ?>
        </ul>
    </div>
</nav>

<ol class="breadcrumb">
    <?php
    $opcionesData = new COpcionData($db);
    $elementos = $opcionesData->getRutaByVar($modulo);
    foreach ($elementos as $e)
        echo "<li class='active'>" . ucwords($html->traducirTildes($e['nombre'])) . "</li>";
    ?>
    <section class="active" style="float: right;">
        Usuario: <?php echo $html->traducirTildes($nombre_usuario); ?>
        &nbsp;&nbsp;&Uacute;ltimo Ingreso: <?php echo $html->traducirTildes($fecha_ultimo_ingreso); ?>
    </section>

</ol>
<div id="wrap">
    <div class="container">
        <?php
        //echo "-".$path_modulo."- ".file_exists($path_modulo);
        if (file_exists($path_modulo))
            include( $path_modulo );
        else {
            ?>
                                                                                <!--div class="error">Error al cargar el modulo <b>'<?php echo $modulo; ?>'</b>. No existe el archivo <b>'<?php echo $conf[$modulo]['archivo']; ?>'</b></div-->
            <?php
            include('templates/html/under.html'); //die('Error al cargar el modulo <b>'.$modulo.'</b>. No existe el archivo <b>'.$conf[$modulo]['archivo'].'</b>');
        }
        ?>            
    </div>
</div>
<?php include('templates/html/footer.html'); ?>
<?php
$html->cerrarHtml();
?>
