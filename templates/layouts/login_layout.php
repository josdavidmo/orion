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
 * Login_layout
 *
 * @package  templates
 * @subpackage layouts
 * @author Redcom Ltda
 * @version 2013.01.00
 * @copyright SERTIC - MINTICS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$html = new CHtml(APP_TITLE);
$html->addEstilo('bootstrap.min.css');
$html->addScript('base/jquery.min.js');
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
$html->abrirHtml('');
$_SESSION["usuario"]="";
$_SESSION["clave"]="";
$db->cerrarConexion();
?>
<blockquote style="background-color: #1e2260">
    <h1 class="text-center" style='color:white;'><strong>PNCAV</strong></h1>
    <h6 class="text-center" style='color:white;'>Proyecto Nacional de Conectividad de Alta Velocidad</h6>
</blockquote>
<nav class="navbar navbar-inverse" style="background-color: #158cba;" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex6-collapse">
            <span class="sr-only">Desplegar navegación</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#" style='color:white;'>PNCAV</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex6-collapse">
        <form class="navbar-form navbar-right" id="frm_login" method="post">
            <div class="form-group">
                <input type="text" class="form-control" 
                       placeholder="Nombre de Usuario" 
                       pattern="[a-zA-Z]+" title="Introduce solo letras"
                       id="txt_login_session"
                       name="txt_login_session"
                       autofocus required />
            </div>
            <div class="form-group">
                <input type="password" class="form-control"
                       id="txt_password_session"
                       name="txt_password_session"
                       placeholder="Contrase&ntilde;a" required />
            </div>
            <input type="hidden" id="txt_estado" name="txt_estado" value="Activo" />
            <button type="submit" class="btn btn-default" style="margin:0px; width :70px"
                    onclick="validar_login();">Enviar</button>
        </form>
    </div>

</nav>
<div id="wrap">
    <div id="myCarousel" class="carousel slide">
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
        </ol>
        <!-- Carousel items -->
        <div class="carousel-inner">
            <div class="active item">
                <img src="./templates/img/imagen1.png" alt="banner1" />
            </div>
            <div class="item">
                <img src="./templates/img/logos_inicio.png" alt="banner2" />
            </div>
        </div>
        <!-- Carousel nav -->
        <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
        <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
    </div>
    <script>
        $(document).ready(function() {
            $('.myCarousel').carousel({
                interval: 3000
            });
        });
    </script>

</div>
<?php include('templates/html/footer.html'); ?>
<?php
$html->cerrarHtml();
?>

