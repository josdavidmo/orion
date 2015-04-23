<?php
/**

 */
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Consulta.xls");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
    header("Pragma: public");

    error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

    require('../../clases/datos/CConsultaData.php');
    require('../../clases/aplicacion/CConsulta.php');
    require_once '../../clases/aplicacion/CDataLog.php';
    require('../../clases/datos/CData.php');
    require('../../clases/interfaz/CHtml.php');
    // Incluimos el archivo de configuracion
    include('../../config/conf.php');
    include('../../config/constantes.php');
    require('../../lang/es-co/consulta-es.php');
    
    $conData = new CConsultaData($db);
    $consultaSerial = $_SESSION['consulta'];
    $consulta = unserialize($consultaSerial);
    
    $consulta->prepararConsulta();
    $titulos = $consulta->getTitulos();
    $elementos=$consulta->ejecutarConsulta($conData);
    
    echo "<table width='80%' border='1' align='center'>";
    //encabezado
    echo"<tr><th colspan = '4'><center></center></th></tr>";
    echo"<tr><th colspan = '4' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(TITULO_CONSULTAR) . "</center></th></tr>";


    //titulos
    echo "<tr>";
    for($i=0;$i<count($titulos);$i++){
        echo "<th>" . $html->traducirTildes($titulos[$i]) . "</th>";
    }
    echo "</tr>";
    
    for($i=0;$i<count($elementos);$i++){
        echo "<tr>";
        for($j=1;$j<count($elementos[$i]);$j++){
            "<td>" . $html->traducirTildes( $html->traducirTildes($elementos[$i][$j]) ). "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
    
    ?>