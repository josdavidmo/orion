<?php
/**
*Gestion Interventoria - Gestin
*
*<ul>
*<li> Redcom Ltda <www.redcom.com.co></li>
*<li> Proyecto PNCAV</li>
*</ul>
*/

/**
* carga la ventana la vista inicial del sistema
*
* @package  modulos
* @author Redcom Ltda
* @version 2013.01.00
* @copyright SERTIC - MINTICS
*/
//no permite el acceso directo
    defined('_VALID_PRY') or die('Restricted access to this option');

	$fecha = date("Y-m-d");
	$du->updateUserFecha($id_usuario,$fecha);
        
        
?>

</div>
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
<div class="container">