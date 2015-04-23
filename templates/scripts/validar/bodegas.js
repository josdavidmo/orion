function agregarCampoBodega(idTable, input) {
    var table = document.getElementById(idTable);
    var length = table.rows.length;
    if (length == 5) {
        table.deleteRow(3);
    }
    if (input.value == '2') {
        var lastPosition = table.rows.length - 1;
        var row = table.insertRow(lastPosition);
        var cell = row.insertCell(0);
        cell.innerHTML = 'Centro de Distribuci&oacute;n';
        cell = row.insertCell(1);
        cell.innerHTML = "<label id='11' ></label><input type='text>'"
                + " id='txt_bodega_padre'"
                + " name = 'txt_bodega_padre'"
                + " size='1' list='centrosDistribucion'"
                + " placeholder='Seleccione uno...' required>";
    } else if (input.value == '3') {
        var table = document.getElementById(idTable);
        var lastPosition = table.rows.length - 1;
        var row = table.insertRow(lastPosition);
        var cell = row.insertCell(0);
        cell.innerHTML = 'Zona Log&iacute;stica';
        cell = row.insertCell(1);
        cell.innerHTML = "<label id='11' ></label><input type='text>'"
                + " id='txt_bodega_padre'"
                + " name = 'txt_bodega_padre'"
                + " size='1' list='zonasLogisticas'"
                + " placeholder='Seleccione uno...' required>";
    }
}

function guardarCambiosProductos(idTable, modulo, niv, registroProducto) {
    var text = document.getElementById("pagedisplay" + idTable).value;
    var pagina = Number(text.substring(7, text.indexOf(" ", 8))) - 1;
    var aux = "";
    var table = document.getElementById("myTable" + idTable);
    var rows = table.rows.length;
    for (var i = 1; i < (rows - 1); i++) {
        if (document.getElementById("cell" + (i + pagina * 15) + "1").value !== ""
                && document.getElementById("cell" + (i + pagina * 15) + "4").value !== ""
                && document.getElementById("cell" + (i + pagina * 15) + "5").value !== ""
                && document.getElementById("cell" + (i + pagina * 15) + "6").value !== "") {
            aux += document.getElementById("cell" + (i + pagina * 15) + "1").value + ",";
            aux += document.getElementById("cell" + (i + pagina * 15) + "4").value + ",";
            aux += document.getElementById("cell" + (i + pagina * 15) + "5").value + ",";
            aux += document.getElementById("cell" + (i + pagina * 15) + "6").value + ",";
            aux += document.getElementById("cell" + (i + pagina * 15) + "7").value;
            aux += ";";
        }
    }
    location.href = "?mod=" + modulo + "&niv=" + niv + "&task=saveAll&datos=" + aux + "&idRegistroProducto=" + registroProducto;
}


