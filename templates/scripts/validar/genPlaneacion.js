/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function validar_add_genPlaneacion() {
    if (document.getElementById('txt_region').value == '-1') {
        mostrarDiv('error_region');
        return false;
    }
    if (document.getElementById('txt_departamento').value == '-1') {
        mostrarDiv('error_departamento');
        return false;
    }
    if (document.getElementById('txt_municipio').value == '-1') {
        mostrarDiv('error_municipio');
        return false;
    }
    if (document.getElementById('txt_eje').value == '-1') {
        mostrarDiv('error_eje');
        return false;
    }
    if (document.getElementById('txt_numero_encuestas').value == '' ||
            !validarEntero(document.getElementById('txt_numero_encuestas').value)) {
        mostrarDiv('error_numero_encuestas');
        return false;
    }
    if (document.getElementById('txt_fecha_inicio').value == '') {
        mostrarDiv('error_fecha');
        return false;
    }
    if (document.getElementById('txt_fecha_fin').value == '') {
        mostrarDiv('error_fecha');
        return false;
    }
    if (document.getElementById('txt_usuario').value == '-1') {
        mostrarDiv('error_usuario');
        return false;
    }
    document.getElementById('frm_add_planeacion').action = '?mod=genPlaneacion&niv=1&task=saveAdd';
    document.getElementById('frm_add_planeacion').submit();
}