function validar_add_plandeaccion() {
    if (document.getElementById('sel_subtema_add').value == '-1') {
        mostrarDiv('error_subtema');
        return false;
    }
    if (document.getElementById('txt_actividad').value == '') {
        mostrarDiv('error_actividad');
        return false;
    }
    if (document.getElementById('txt_consecutivo').value == '') {
        mostrarDiv('error_consecutivo');
        return false;
    }
    if (document.getElementById('sel_responsable_add').value == '-1') {
        mostrarDiv('error_responsable');
        return false;
    }
    if (document.getElementById('txt_fecha_entrega').value == '' || document.getElementById('txt_fecha_entrega').value == '0000-00-00') {
        mostrarDiv('error_fecha_entrega');
        return false;
    }
    if (document.getElementById('txt_fecha_limite_add').value == '' || document.getElementById('txt_fecha_limite_add').value == '0000-00-00') {
        mostrarDiv('error_fecha_limite');
        return false;
    }
//    if (document.getElementById('sel_estado_add').value == '-1') {
//        mostrarDiv('error_estado');
//        return false;
//    }
    if (document.getElementById('txt_observaciones').value == '') {
        mostrarDiv('error_observaciones');
        return false;
    }

    document.getElementById('frm_add_plandeaccion').action = '?mod=planAccion&niv=1&task=saveAdd';
    document.getElementById('frm_add_plandeaccion').submit();
}

function validar_edit_planaccion() {
    if (document.getElementById('sel_subtema_edit').value == '-1') {
        mostrarDiv('error_subtema');
        return false;
    }
    if (document.getElementById('txt_actividad_edit').value == '') {
        mostrarDiv('error_actividad');
        return false;
    }
    if (document.getElementById('txt_consecutivo_edit').value == '') {
        mostrarDiv('error_consecutivo');
        return false;
    }
    if (document.getElementById('sel_subtema_edit').value == '-1') {
        mostrarDiv('error_subtema');
        return false;
    }
    if (document.getElementById('txt_fecha_entrega_edit').value == '' || document.getElementById('txt_fecha_entrega_edit').value == '0000-00-00') {
        mostrarDiv('error_fecha_entrega');
        return false;
    }
    if (document.getElementById('txt_fecha_limite_edit').value == '' || document.getElementById('txt_fecha_limite_edit').value == '0000-00-00') {
        mostrarDiv('error_fecha_limite');
        return false;
    }
//    if (document.getElementById('sel_estado_edit').value == '-1') {
//        mostrarDiv('error_estado');
//        return false;
//    }
    if (document.getElementById('txt_observaciones_edit').value == '') {
        mostrarDiv('error_observaciones');
        return false;
    }
    document.getElementById('frm_edit_compromiso').action = '?mod=planAccion&niv=1&task=saveEdit';
    document.getElementById('frm_edit_compromiso').submit();
}

function consultar_planaccion() {

    if (document.getElementById('txt_criterio').value == "")
        document.getElementById('txt_criterio').value = "1";
    document.getElementById('frm_list_compromisos').action = '?mod=planAccion&niv=1';
    document.getElementById('frm_list_compromisos').submit();
}

function cancelar_busqueda_planaccion() {
    document.getElementById('txt_fecha_inicio').value = '';
    document.getElementById('txt_fecha_fin').value = '';
    document.getElementById('sel_responsable').value = '-1';
    document.getElementById('sel_estado').value = '-1';
    document.getElementById('txt_actividad').value = '';
    document.getElementById('txt_criterio').value = '';
    document.getElementById('frm_list_compromisos').action = '?mod=planAccion&niv=1';
    document.getElementById('frm_list_compromisos').submit();
}