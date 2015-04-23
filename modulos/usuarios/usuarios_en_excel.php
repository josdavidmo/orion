<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte_usuarios.xls");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

require('../../clases/datos/CUsuarioData.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
// Incluimos el archivo de configuracion
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/usuarios-es.php');

$html = new CHtml('');

$userData = new CUserData($db);

$operador = OPERADOR_DEFECTO;
$usuarios = $userData->getUsersExcel();
//Primera tabla
echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '27'><center></center></th></tr>";
echo"<tr><th colspan = '27' bgcolor='#CCCCCC'><center>" . $html->traducirTildes('Reporte Usuarios') . "</center></th></tr>";

//titulos
echo "<tr>";
echo "<th>" . $html->traducirTildes(USUARIO_LOGIN) . "</th>
	<th>" . $html->traducirTildes(USUARIO_PERFIL) . "</th>
	<th>" . $html->traducirTildes(USUARIO_DOCUMENTO) . "</th>
	<th>" . $html->traducirTildes(USUARIO_NOMBRE) . "</th>
        <th>" . $html->traducirTildes(USUARIO_APELLIDO) . "</th>
        <th>" . $html->traducirTildes(USUARIO_RH) . "</th>
        <th>" . $html->traducirTildes(USUARIO_FECHA_INGRESO) . "</th>
        <th>" . $html->traducirTildes(USUARIO_REGIONAL) . "</th>
        <th>" . $html->traducirTildes(USUARIO_CARGO) . "</th>
	<th>" . $html->traducirTildes(USUARIO_CORREO_CORPORATIVO) . "</th>
	<th>" . $html->traducirTildes(USUARIO_CUENTA_BANCO) . "</th>
        <th>" . $html->traducirTildes(USUARIO_CELULAR) . "</th>
        <th>" . $html->traducirTildes(USUARIO_CELULAR_CORPORATIVO) . "</th>
        <th>" . $html->traducirTildes(USUARIO_TELEFONO) . "</th>
        <th>" . $html->traducirTildes(USUARIO_CORREO) . "</th>
        <th>" . $html->traducirTildes(USUARIO_CIUDAD) . "</th>
	<th>" . $html->traducirTildes(USUARIO_DIRECCION) . "</th>
	<th>" . $html->traducirTildes(USUARIO_FECHA_DE_NACIMIENTO) . "</th>
        <th>" . $html->traducirTildes(USUARIO_CONTACTO_EMERGENCIA) . "</th>
        <th>" . $html->traducirTildes(USUARIO_CONTACTO_EMERGENCIA_CELULAR) . "</th>
        <th>" . $html->traducirTildes(USUARIO_FECHA_APROBACION) . "</th>
        <th>" . $html->traducirTildes(USUARIO_ARL) . "</th>
        <th>" . $html->traducirTildes(USUARIO_EPS) . "</th>
	<th>" . $html->traducirTildes(USUARIO_ALERGIA) . "</th>
	<th>" . $html->traducirTildes(USUARIO_ANTECEDENTES_ENFERMEDAD) . "</th>
        <th>" . $html->traducirTildes(USUARIO_MEDICAMENTOS) . "</th>
        <th>" . $html->traducirTildes(USUARIO_ESTADO) . "</th>";
echo "</tr>";
//datos 
$contador = 0;
$cont = count($usuarios);

while ($contador < $cont) {
    echo "<tr>";
    echo "<td>" . $html->traducirTildes($usuarios[$contador]['login']). "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['perfil']). "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['documento'] ). "</td>
	<td>" . $html->traducirTildes( $usuarios[$contador]['nombre']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['apellido']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['rh']) . "</td>
        <td>" . $html->traducirTildes($usuarios[$contador]['fecha_ingreso']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['regional']). "</td>  
        <td>" . $html->traducirTildes( $usuarios[$contador]['cargo']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['correo_corporativo']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['cuenta_banco']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['celular']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['celular_corporativo']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['telefono']) . "</td>
        <td>" . $html->traducirTildes($usuarios[$contador]['correo']) . "</td>  
        <td>" . $html->traducirTildes( $usuarios[$contador]['ciudad']) . "</td>
        <td>" . $html->traducirTildes($usuarios[$contador]['direccion']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['fecha_nacimiento']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['contacto_emergencia']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['telefono_contacto']) . "</td>
        <td>" . $html->traducirTildes($usuarios[$contador]['fecha_aprobacion']) . "</td>
        <td>" . $html->traducirTildes( $usuarios[$contador]['arl']) . "</td>        
        <td>" . $html->traducirTildes($usuarios[$contador]['eps']) . "</td>        
        <td>" . $html->traducirTildes( $usuarios[$contador]['alergia']) . "</td>        
        <td>" . $html->traducirTildes($usuarios[$contador]['antecedentes_enfermedad']) . "</td>        
        <td>" . $html->traducirTildes($usuarios[$contador]['medicamentos']) . "</td>        
        <td>" . $html->traducirTildes($usuarios[$contador]['estado']) . "</td>";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>
