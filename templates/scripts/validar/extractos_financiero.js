function validar_add_extracto(form){
    if(document.getElementById('sel_cuenta').value == '-1'){
       mostrarDiv('error_cuenta');
       return false; 
    }
    if(document.getElementById('txt_mes').value == "" || 
       document.getElementById('txt_mes').value == "0000-00-00" ){
       mostrarDiv('error_mes');
       return false;
    }
    if(document.getElementById('txt_anio').value == "" || 
       document.getElementById('txt_anio').value == "0000-00-00" ){
       mostrarDiv('error_anio');
       return false;
    }
    unformatNumber('txt_saldo_inicial');
    if(document.getElementById('txt_saldo_inicial').value == "" ||
       !validarReal(document.getElementById('txt_saldo_inicial').value)){
       mostrarDiv('error_saldo_inicial');
       return false;
    }
    formatNumber('txt_saldo_inicial');
    unformatNumber('txt_incrementos');
    if(document.getElementById('txt_incrementos').value == "" ||
       !validarReal(document.getElementById('txt_incrementos').value)){
       mostrarDiv('error_incrementos');
       return false;
    }
    formatNumber('txt_incrementos');
    unformatNumber('txt_disminuciones');
    if(document.getElementById('txt_disminuciones').value == "" ||
       !validarReal(document.getElementById('txt_disminuciones').value)){
       mostrarDiv('error_disminuciones');
       return false;
    }
    formatNumber('txt_disminuciones');
    if(document.getElementById('txt_observaciones').value == ""){
       mostrarDiv('error_observaciones');
       return false;  
    }
    if(document.getElementById('file_documento_soporte').value == ""){
       mostrarDiv('error_documento_soporte');
       return false;  
    }
    if(document.getElementById('file_documento_movimientos').value == ""){
       mostrarDiv('error_documento_movimientos');
       return false;  
    }
    unformatNumber('txt_saldo_inicial');
    unformatNumber('txt_incrementos');
    unformatNumber('txt_disminuciones');
    
    document.getElementById(form).action='?mod=extractos&niv=1&task=saveAdd';
    document.getElementById(form).submit();
}

function validar_edit_extracto(form){
    if(document.getElementById('sel_cuenta').value == '-1'){
       mostrarDiv('error_cuenta');
       return false; 
    }
    if(document.getElementById('txt_mes').value == "" || 
       document.getElementById('txt_mes').value == "0000-00-00" ){
       mostrarDiv('error_mes');
       return false;
    }
    if(document.getElementById('txt_anio').value == "" || 
       document.getElementById('txt_anio').value == "0000-00-00" ){
       mostrarDiv('error_anio');
       return false;
    }
    unformatNumber('txt_saldo_inicial');
    if(document.getElementById('txt_saldo_inicial').value == "" ||
       !validarReal(document.getElementById('txt_saldo_inicial').value)){
       mostrarDiv('error_saldo_inicial');
       return false;
    }
    formatNumber('txt_saldo_inicial');
    unformatNumber('txt_incrementos');
    if(document.getElementById('txt_incrementos').value == "" ||
       !validarReal(document.getElementById('txt_incrementos').value)){
       mostrarDiv('error_incrementos');
       return false;
    }
    formatNumber('txt_incrementos');
    unformatNumber('txt_disminuciones');
    if(document.getElementById('txt_disminuciones').value == "" ||
       !validarReal(document.getElementById('txt_disminuciones').value)){
       mostrarDiv('error_disminuciones');
       return false;
    }
    formatNumber('txt_disminuciones');
    if(document.getElementById('txt_observaciones').value == ""){
       mostrarDiv('error_observaciones');
       return false;  
    }
    if(document.getElementById('file_documento_soporte').value == ""){
       mostrarDiv('error_documento_soporte');
       return false;  
    }
    if(document.getElementById('file_documento_movimientos').value == ""){
       mostrarDiv('error_documento_movimientos');
       return false;  
    }
    unformatNumber('txt_saldo_inicial');
    unformatNumber('txt_incrementos');
    unformatNumber('txt_disminuciones');
    
    document.getElementById(form).action='?mod=extractos&niv=1&task=saveEdit';
    document.getElementById(form).submit();
}

function exportar_archivo_excel_extracto(){
    document.getElementById('frm_listar_extracto').action = 'modulos/financiero/extractos_excel.php';
    document.getElementById('frm_listar_extracto').submit();
}

function exportar_archivo_excel_movimiento(){
    document.getElementById('frm_listar_movs').action = 'modulos/financiero/movimientos_excel.php';
    document.getElementById('frm_listar_movs').submit();
}

function exportar_archivo_excel_rendimiento(){
    document.getElementById('frm_listar_rendimientos').action = 'modulos/financiero/rendimientos_excel.php';
    document.getElementById('frm_listar_rendimientos').submit();
}