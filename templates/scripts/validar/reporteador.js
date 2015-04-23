
function actualizar_campos(){
    document.getElementById('frm_select').action = '?mod=reporteador&niv=1&task=columnas';
    document.getElementById('frm_select').submit();
}

function agregar_columnas(){
    document.getElementById('frm_consulta').action = '?mod=reporteador&niv=1&task=columnas';
    document.getElementById('frm_consulta').submit();
}

function agregar_link(){
    document.getElementById('frm_consulta').action = '?mod=reporteador&niv=1&task=link';
    document.getElementById('frm_consulta').submit();
}

function agregar_otros(){
    document.getElementById('frm_consulta').action = '?mod=reporteador&niv=1&task=otros';
    document.getElementById('frm_consulta').submit();
}

function set_link(){
    document.getElementById('frm_link').action = '?mod=reporteador&niv=1&task=consulta';
    document.getElementById('frm_link').submit();
}

function set_otros(){
    document.getElementById('frm_otrps').action = '?mod=reporteador&niv=1&task=consulta';
    document.getElementById('frm_otrps').submit();
}

function agregar_condicion(){
    document.getElementById('frm_consulta').action = '?mod=reporteador&niv=1&task=condicion';
    document.getElementById('frm_consulta').submit();
}

function consultar_reporteador(){
    document.getElementById('confirmacion_consulta').value='consultar';
    document.getElementById('frm_consulta').action = '?mod=reporteador&niv=1&task=consulta';
    document.getElementById('frm_consulta').submit();
}

function set_condicion(){
    document.getElementById('frm_condicion').action = '?mod=reporteador&niv=1&task=consulta';
    document.getElementById('frm_condicion').submit();
}

function exportar_reporteador(){
    document.getElementById('frm_consulta2').action = 'modulos/reporteador/reporteador_a_excel.php';
    document.getElementById('frm_consulta2').submit();
}

function reiniciar_consulta(){
    document.getElementById('frm_consulta2').action = '';
    document.getElementById('frm_consulta2').submit();
}