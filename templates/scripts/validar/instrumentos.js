function addRow(idTable) {
    var table = document.getElementById(idTable);
    var lastPosition = table.rows.length - 1;
    var row = table.insertRow(lastPosition);
    var cell = row.insertCell(0);
    cell.innerHTML = lastPosition;
    cell = row.insertCell(1);
    cell.innerHTML = "<textarea class='form-control' rows='4' cols='50'"
            + " id='nombreSeccion" + lastPosition + "'"
            + " name = 'nombreSeccion" + lastPosition + "'"
            + " size='45' maxlength='45'"
            + " placeholder='Escribe el nombre de la secci&oacute;n'"
            + " autofocus required></textarea>";
    var input = document.getElementById("numeroSecciones");
    input.value = lastPosition;
}

function addRowActualizar(idTable) {
    var table = document.getElementById(idTable);
    var lastPosition = table.rows.length - 1;
    var row = table.insertRow(lastPosition);
    var cell = row.insertCell(0);
    cell.innerHTML = '<div class="col-lg-6">'
            + '<div class="input-group">'
            + '<span class="input-group-addon">'
            + 'Nombre Seccion ' + lastPosition + ':'
            + '</span>'
            + '<input type="text" class="form-control" id="seccionNueva' + (lastPosition-1) + '">'
            + '</div>'
            + '</div>';
    cell = row.insertCell(1);
    cell.innerHTML = '<div class="col-lg-6">'
            + '<div class="input-group">'
            + '<span class="input-group-addon">'
            + 'N&uacute;mero:</span>'
            + '<input type="text" class="form-control" value=' + lastPosition + ' id="numeroSeccion' + (lastPosition-1) + '"> '
            + '</div></div>';
    var input = document.getElementById("nuevasSecciones");
    input.value = lastPosition;
}

function addRowPregunta(idTable) {
    var table = document.getElementById(idTable);
    var lastPosition = table.rows.length - 1;
    var row = table.insertRow(lastPosition);
    var cell = row.insertCell(0);
    cell.innerHTML = "<table id='pregunta" + lastPosition + "'><tbody>"
            + "<tr><th colspan='2'>Pregunta " + lastPosition + "</th></tr>"
            + "</tbody></table>";
    var tablePregunta = document.getElementById("pregunta" + lastPosition);
    row = tablePregunta.insertRow(1);
    cell = row.insertCell(0);
    cell.innerHTML = "N&uacute;mero:";
    cell = row.insertCell(1);
    cell.innerHTML = lastPosition;
    row = tablePregunta.insertRow(2);
    cell = row.insertCell(0);
    cell.innerHTML = "Requerido:";
    cell = row.insertCell(1);
    cell.innerHTML = '<input type="checkbox" id="requeridoPregunta' + lastPosition + '"'
            + ' name="requeridoPregunta' + lastPosition + '" />';
    row = tablePregunta.insertRow(3);
    cell = row.insertCell(0);
    cell.innerHTML = "Enunciado:";
    cell = row.insertCell(1);
    cell.innerHTML = '<textarea type="text" id="enunciadoPregunta' + lastPosition + '"'
            + ' name="enunciadoPregunta' + lastPosition + '" rows="4" cols="50"'
            + ' maxlength="200" autofocus required></textarea>';
    row = tablePregunta.insertRow(4);
    cell = row.insertCell(0);
    cell.innerHTML = "Tipo:";
    cell = row.insertCell(1);
    cell.innerHTML = '<input type="text" id="tipoPregunta' + lastPosition + '"'
            + ' name="tipoPregunta' + lastPosition + '"'
            + ' list="tipo"'
            + ' onchange="showOptions(\'pregunta' + lastPosition + '\', this)" required>';
    document.getElementById('numeroPreguntas').value = lastPosition;
}

function deleteRow(idTable, hasFoot, min) {
    var table = document.getElementById(idTable);
    var lastPosition = 0;
    if (hasFoot) {
        lastPosition = table.rows.length - 2;
    } else {
        lastPosition = table.rows.length - 1;
    }
    if (lastPosition !== min) {
        document.getElementById(idTable).deleteRow(lastPosition);
    }
}

function showOptions(idTable, select) {
    var numeroPregunta = document.getElementById('preguntas');
    var idElement = numeroPregunta.rows.length - 2;
    var table = document.getElementById(idTable);
    var valueSelect = select.value;
    var lastPosition = table.rows.length;
    if (lastPosition > 5) {
        deleteRow(idTable, false);
        deleteRow(idTable, false);
    }
    lastPosition = table.rows.length;
    var row = table.insertRow(lastPosition);
    var cell = row.insertCell(0);
    if (valueSelect === "0") {
        cell.innerHTML = "Subtipo:";
        cell = row.insertCell(1);
        cell.innerHTML = "<input type='text' list='tipoAbierta' id='subtipoPregunta"
                + idElement + "' name='subtipoPregunta"
                + idElement + "' required/>";
        row = table.insertRow(lastPosition + 1);
        cell = row.insertCell(0);
        cell.innerHTML = "Longitud:";
        cell = row.insertCell(1);
        cell.innerHTML = "<input type='number' min=0 id='longitudPregunta"
                + idElement + "' name='longitudPregunta"
                + idElement + "' required/>";
    } else {
        cell.innerHTML = "Subtipo:";
        cell = row.insertCell(1);
        cell.innerHTML = "<input type='text' list='tipoCerrada' id='subtipoPregunta"
                + idElement + "' name='subtipoPregunta"
                + idElement + "' placeholder='Seleccione uno' required/>";
        var row = table.insertRow(lastPosition + 1);
        cell = row.insertCell(0);
        cell.innerHTML = "Opciones de Respuesta:";
        cell = row.insertCell(1);
        cell.innerHTML = "<textarea id='opcRespuestaPregunta"
                + idElement + "' name='opcRespuestaPregunta"
                + idElement + "' required></textarea>";

    }
}

function guardarSecciones(idTabla, mod, niv, idInstrumento){
    var tabla = document.getElementById(idTabla);
    var lastPosition = tabla.rows.length - 2;
    var campoSeccionesActuales = document.getElementById("seccionesActuales");
    var numeroSeccionesActuales = campoSeccionesActuales.value;
    var resultadoActual = "";
    for (var i = 0; i < lastPosition; i++){
        if(i < numeroSeccionesActuales){
            var campoIdSeccionActual = document.getElementById("idSeccion"+i);
            var campoNombreSeccionActual = document.getElementById("nombreSeccion"+i);
            var campoNumeroSeccionActual = document.getElementById("numeroSeccion"+i);
            resultadoActual += campoIdSeccionActual.value + ";" 
                               + campoNombreSeccionActual.value 
                               + ";" + campoNumeroSeccionActual.value + ",";
        } else {
            var campoSeccionActual = document.getElementById("seccionNueva"+i);
            var campoNumeroSeccionActual = document.getElementById("numeroSeccion"+i);
            resultadoActual += campoSeccionActual.value + ";" 
                               + campoNumeroSeccionActual.value + ",";
        }
    }
    location.href = '?mod=' + mod + '&niv=' + niv + '&task=updateAndSaveSections' + 
            '&idInstrumento=' + idInstrumento + "&values=" + resultadoActual 
            + "&seccionesActuales=" + numeroSeccionesActuales 
            + "&secciones=" + lastPosition;
}

function actualizarPreguntas(mod, niv, task, input, idElement) {
    var seccionActual = input.value;
    location.href = '?mod=' + mod + '&niv=' + niv + '&task='
            + task + '&seccionActual=' + seccionActual
            + "&id_element=" + idElement;
}

function agregarCamposPregunta(idTable,input) {
    var table = document.getElementById(idTable);
    var length = table.rows.length;
    if(length == 6){
        table.deleteRow(3);
        table.deleteRow(3);
    }
    if (input.value == '0') {
        var lastPosition = table.rows.length - 1;
        var row = table.insertRow(lastPosition);
        var cell = row.insertCell(0);
        cell.innerHTML = 'Subtipo';
        cell = row.insertCell(1);
        cell.innerHTML = "<label id='11' ></label><input type='text'"
                + " id='txt_subtipo'"
                + " name = 'txt_subtipo'"
                + " size='1' list='tipoAbierta'" 
                + " placeholder='Seleccione uno...' required>";
        lastPosition = table.rows.length - 1;
        row = table.insertRow(lastPosition);
        cell = row.insertCell(0);
        cell.innerHTML = 'Longitud';
        cell = row.insertCell(1);
        cell.innerHTML = "<label id='12' ></label><input type='number'"
                + " id='txt_longitud'"
                + " name = 'txt_longitud'"
                + " size='2' " 
                + " required>";
    } else if(input.value == '3'){
        var lastPosition = table.rows.length - 1;
        var row = table.insertRow(lastPosition);
        var cell = row.insertCell(0);
        cell.innerHTML = 'Subtipo';
        cell = row.insertCell(1);
        cell.innerHTML = "<label id='11' ></label><input type='text'"
                + " id='txt_subtipo'"
                + " name = 'txt_subtipo'"
                + " size='1' list='tipoCerrada'" 
                + " placeholder='Seleccione uno...' required>";
        lastPosition = table.rows.length - 1;
        row = table.insertRow(lastPosition);
        cell = row.insertCell(0);
        cell.innerHTML = 'Opciones Respuestas';
        cell = row.insertCell(1);
        cell.innerHTML = "<label id='12' ></label><input type='text'"
                + " id='txt_opciones_respuestas'"
                + " name = 'txt_opciones_respuestas'"
                + " size='100' " 
                + " placeholder='Si,No,No Sabe,No Responde' required>";
    }
}