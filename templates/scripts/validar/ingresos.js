function Validar_agregar_ingreso(form, accion) {

    if (document.getElementById('ano_ingreso').value == '') {
        mostrarDiv('error_ano');
        return false;
    }


    if (document.getElementById('txt_Monto').value == '' ||  document.getElementById('txt_Monto').value < '0' || !validaFloat(document.getElementById('txt_Monto').value)) {
        mostrarDiv('error_monto');
        return false;
    }
   


    document.getElementById(form).action = accion;
    document.getElementById(form).submit();
}
function Validar_editar_ingreso() {


    if (document.getElementById('txt_Monto_edit').value == '' ||  document.getElementById('txt_Monto_edit').value<'0' || !validaFloat(document.getElementById('txt_Monto_edit').value)) {
        mostrarDiv('error_monto');
        return false;
    }

    document.getElementById('frm_editar_ingreso').action = '?mod=ingresos&niv=1&task=GuardarEdicion';
    document.getElementById('frm_editar_ingreso').submit();
}

function cancelarAccionIngreso(form, accion) {
    document.getElementById(form).action = accion;
    document.getElementById(form).submit();
}


function Filtrar_year() {
    document.getElementById('frm_list_resumen_financiero').action = '?mod=ingresos&niv=1';
    document.getElementById('frm_list_resumen_financiero').submit();
}


function exportar_excel_ingresos() {
    document.getElementById('frm_list_ingresos').action = 'modulos/financiero/resumenfinanciero_a_excel.php';
    document.getElementById('frm_list_ingresos').submit();
}

function exportar_egreso_excel() {
    document.getElementById('form_exportar_egresos').action = 'modulos/financiero/egresos_a_excel.php';
    document.getElementById('form_exportar_egresos').submit();
}
function exportar_desembolsos_excel() {
    document.getElementById('form_exportar_desembolsos').action = 'modulos/financiero/desembolsos_a_excel.php';
    document.getElementById('form_exportar_desembolsos').submit();
}
function exportar_utilidades_excel() {
    document.getElementById('form_exportar_utilidades').action = 'modulos/financiero/utilizaciones_a_excel.php';
    document.getElementById('form_exportar_utilidades').submit();
}
function exportar_invsersiones_excel() {
    document.getElementById('form_exportar_inversiones').action = 'modulos/financiero/inversiondelanticipo_a_excel.php';
    document.getElementById('form_exportar_inversiones').submit();
}
function exportar_grafica_ingresos(){
    document.getElementById('form_exportar_egresos').action = '?mod=ingresos&niv=1&operador=1&task=graficaEgresos';
    document.getElementById('form_exportar_egresos').submit();
}
function exportar_grafica_desembolsos(){
    document.getElementById('form_exportar_desembolsos').action = '?mod=ingresos&niv=1&operador=1&task=graficaDesembolsos';
    document.getElementById('form_exportar_desembolsos').submit();
}
function exportar_grafica_utilizaciones(){
    document.getElementById('form_exportar_utilidades').action = '?mod=ingresos&niv=1&operador=1&task=graficaUtilizaciones';
    document.getElementById('form_exportar_utilidades').submit();
}
function exportar_grafica_inversion(){
    document.getElementById('form_exportar_inversiones').action = '?mod=ingresos&niv=1&operador=1&task=graficaInversion';
    document.getElementById('form_exportar_inversiones').submit();
}