<?php
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
//Variable de operador y task
$operador = OPERADOR_DEFECTO;
$task = $_REQUEST['task'];
if (empty($task)) {
    $task = 'list';
}
$niv = $_REQUEST['niv'];
$modulo = $_REQUEST['mod'];
//DAO utilizado para la clase
$daoInstrumentos = new CInstrumentoData($db);
$planData = new CGeneradorPlaneacionData($db);
switch ($task) {

    case 'list':
        $form = new CHtmlForm();
        $form->setTitle(TITULO_INSTRUMENTOS);
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $titulos = array(CODIGO_INSTRUMENTO, NOMBRE_INSTRUMENTO, ENCABEZADO_INTRUMENTO,
            TIPO_INSTRUMENTO, SECCIONES_INSTRUMENTO, PREGUNTAS_INSTRUMENTO);
        $instrumentos = $daoInstrumentos->getInstrumentos();
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TITULO_INSTRUMENTOS);
        $dt->setDataRows($instrumentos);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=see");
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $dt->setType(1);
        $dt->setPag(1);
        $dt->writeDataTable($niv);
        break;

    case 'see':
        $idInstrumento = $_REQUEST['id_element'];
        $instrumento = $daoInstrumentos->getInstrumentoById($idInstrumento);
        $secciones = $daoInstrumentos->getSecciones($instrumento);
        $numeroSecciones = count($secciones);
        $seccionActual = 0;
        $pagina = $_REQUEST['pagina'];
        if (isset($_REQUEST['seccionActual'])) {
            $seccionActual = $_REQUEST['seccionActual'];
        }
        $preguntas = $daoInstrumentos->getPreguntas($secciones[$seccionActual]);
        $numeroPaginas = ceil($numeroSecciones / PAGINAS);
        ?>
        <h1><?php echo $instrumento->getCodigo() . " " . $instrumento->getNombreInstrumento(); ?></h1>
        <nav>
            <ul class="pagination pagination-lg">
                <li>
                    <a href="?mod=instrumentos&task=see&seccionActual=0&id_element=<?= $idInstrumento ?>&niv=<?= $niv ?>&pagina=0" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($j = 0; $j < $numeroPaginas; $j++) { ?>
                    <?php if ($j == $pagina) { ?>
                        <li class="active">
                        <?php } else { ?>
                        <li>
                        <?php } ?>
                        <a href="?mod=instrumentos&task=see&seccionActual=<?= ($j * PAGINAS) ?>&id_element=<?= $idInstrumento ?>&niv=<?= $niv ?>&pagina=<?= ($j) ?>"><?= ($j + 1) ?></a>
                    </li>
                <?php } ?>
                <li>
                    <a href="?mod=instrumentos&task=see&seccionActual=<?= ($numeroSecciones - 1) ?>&id_element=<?= $idInstrumento ?>&niv=<?= $niv ?>&pagina=<?= ($numeroPaginas - 1) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <ul class="nav nav-tabs nav-justified">
            <?php
            $maximo = PAGINAS;
            if ($maximo * $numeroPaginas > $numeroSecciones && $pagina == ($numeroPaginas - 1)) {
                $maximo = PAGINAS - ($numeroPaginas * PAGINAS - $numeroSecciones);
            }
            for ($i = 0; $i < $maximo; $i++) {
                $numeroSeccion = (PAGINAS * $pagina) + $i;
                $seccion = $secciones[$numeroSeccion];
                ?>
                <li <?php
                if ($seccionActual == $numeroSeccion) {
                    echo "class='active' style='background: rgba(54, 25, 25, .5)'";
                }
                ?>>
                    <a href="?mod=instrumentos&task=see&seccionActual=<?= $numeroSeccion ?>&id_element=<?= $idInstrumento ?>&niv=<?= $niv ?>&pagina=<?= ($pagina) ?>">	
                        <?php
                        if ($seccionActual == $numeroSeccion) {
                            echo "<strong>";
                        }
                        echo $seccion->getNumero() . ". " . $seccion->getNombreSeccion();
                        if ($seccionActual == $numeroSeccion) {
                            echo "</strong>";
                        }
                        ?>
                    </a>
                </li>      
            <?php } ?>
        </ul>
        <form>
            <?php foreach ($preguntas as $pregunta) { ?> 
                <fieldset>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <?php echo $pregunta->getEnunciado(); ?>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="input-group">
                                <?php if ($pregunta->getTipoPregunta() == 0) { ?>
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <?php echo $daoInstrumentos->construirInput($pregunta); ?>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    $opcionesRespuesta = split(",", $pregunta->getOpcionRespuesta());
                                    $entro = false;
                                    ?>
                                    <div class="row">
                                        <?php if ($pregunta->getTipoPregunta() == 4) { ?>
                                            <div class="col-lg-12">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Respuesta Pregunta <?= $pregunta->getNumero() ?>.</span>
                                                    <select class="form-control" <?php
                                                    if ($pregunta->isRequerido()) {
                                                        echo 'required';
                                                    }
                                                    ?>>
                                                        <option value="">Seleccione uno</option>
                                                        <?php for ($i = 0; $i < count($opcionesRespuesta); $i++) { ?>
                                                            <option value="<?= $opcionesRespuesta[$i] ?>"><?= $opcionesRespuesta[$i] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } else if ($pregunta->getTipoPregunta() != 7) { ?>
                                            <?php for ($i = 0; $i < count($opcionesRespuesta); $i++) { ?>
                                                <div class="col-lg-6">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" <?php
                                                        if ($pregunta->isRequerido()) {
                                                            echo 'id="inputSeleccionado"';
                                                        }
                                                        ?>>
                                                                  <?php if ($pregunta->getTipoPregunta() == 1) { ?>
                                                                <input type="radio" name="unico" value="<?php echo $opcionesRespuesta[$i] ?>" <?php
                                                                if ($pregunta->isRequerido() && !$entro) {
                                                                    echo "required";
                                                                    $entro = true;
                                                                }
                                                                ?>>
                                                                   <?php } else if ($pregunta->getTipoPregunta() == 2) { ?>
                                                                <input type="checkbox" value="<?php echo $opcionesRespuesta[$i] ?>" <?php
                                                                if ($pregunta->isRequerido() && !$entro) {
                                                                    echo "required";
                                                                    $entro = true;
                                                                }
                                                                ?>>
                                                                   <?php } ?>
                                                        </span>
                                                        <input type="text" class="form-control" value="<?php echo $opcionesRespuesta[$i] ?>" readonly>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <div class="col-lg-12">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Respuesta Pregunta <?= $pregunta->getNumero() ?>.</span>
                                                    <input type="text" class="form-control" value="<?= $opcionesRespuesta[0] ?>" readonly>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php }
                                ?>
                            </div>
                        </div>
                    </div>
                </fieldset>
            <?php } ?>
            <input type="button" value="Atras" onclick="location.href = '?mod=<?php echo $modulo ?>&niv=<?php echo $niv ?>'">
        </form>

        <?php
        break;

    case 'add':
        $codigo = $_REQUEST['codigoInstrumento'];
        $r = 'true';
        if (isset($_REQUEST['nombreInstrumento'])) {
            $nombreInstrumento = $_REQUEST['nombreInstrumento'];
            $codigo = $_REQUEST['codigoInstrumento'];
            $tipoInstrumento = $_REQUEST['tipoInstrumento'];
            $idNivel = $_REQUEST['idNivel'];
            $instrumento = new CInstrumento('', $nombreInstrumento, $codigo, $tipoInstrumento, $idNivel);
            $r = $daoInstrumentos->insertarInstrumento($instrumento);
        }
        if ($r == 'false') {
            $html->generaAviso(ENCUESTA_AGREGADA_FRACASO, "?mod=" . $modulo . "&niv=" . $niv
                    . "&task=list" . "&operador=" . $operador);
        } else {
            if (isset($_REQUEST['numeroSecciones']) && !isset($_REQUEST['seccionActual'])) {
                $codigo = $_REQUEST['codigoInstrumento'];
                $numeroSecciones = $_REQUEST['numeroSecciones'];
                $instrumento = $daoInstrumentos->getInstrumentoByCodigo($codigo);
                for ($i = 0; $i < $numeroSecciones; $i++) {
                    $numeroSeccion = ($i + 1);
                    $nombreSeccion = $_REQUEST['nombreSeccion' . ($i + 1)];
                    $seccion = new CSeccion($idSeccion, $nombreSeccion, $numeroSeccion, $instrumento);
                    $r = $r && $daoInstrumentos->insertarSeccion($seccion);
                }
            }
            $session = 0;
            if (isset($_REQUEST['seccion'])) {
                $seccion = $_REQUEST['seccion'];
            }
            $seccion += 0;
            if ($seccion != 3) {
                ?>
                <ul class="nav nav-tabs nav-justified">
                    <li <?php
                    if ($seccion === 0) {
                        echo 'class="active"';
                    }
                    if ($seccion > 0) {
                        echo 'class="disabled"';
                    }
                    ?>><a href="#">Instrumento</a></li>
                    <li <?php
                    if ($seccion === 1) {
                        echo 'class="active"';
                    }
                    if ($seccion > 1) {
                        echo 'class="disabled"';
                    }
                    ?>><a href="#">Secciones</a></li>
                    <li <?php
                    if ($seccion === 2) {
                        echo 'class="active"';
                    }
                    if ($seccion > 2) {
                        echo 'class="disabled"';
                    }
                    ?>><a href="#">Preguntas</a></li>
                </ul>
                <?php
            }
            switch ($seccion) {
                case 0:
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <strong>Datos B&aacute;sicos Instrumento</strong>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" action="index.php?mod=<?php echo $_REQUEST['mod']; ?>&niv=<?php echo $_REQUEST['niv']; ?>&operador=<?php echo $_REQUEST['operador']; ?>&seccion=1&task=add" method="post">
                                <div class="form-group">
                                    <label for="codigoInstrumento" class="col-lg-2 control-label">C&oacute;digo Instrumento:</label>
                                    <div class="col-lg-10">
                                        <input type="text" class="form-control"
                                               id="codigoInstrumento" name="codigoInstrumento"
                                               placeholder="Escribe el c&oacute;digo &uacute;nico del Intrumento"
                                               autofocus required/>
                                    </div>
                                    <label for="nombreIntrumento" class="col-lg-2 control-label">Nombre Instrumento:</label>
                                    <div class="col-lg-10">
                                        <input type="text" class="form-control"
                                               id="nombreInstrumento" name="nombreInstrumento"
                                               placeholder="Escribe el nombre del Intrumento"
                                               autofocus required/>
                                    </div>
                                    <label for="tipoInstrumento" class="col-lg-2 control-label">Tipo de Instrumento:</label>
                                    <datalist id="tipos"><?php
                                        $tiposIns = $planData->getTipoInstrumentos('enc_tipo_nombre');
                                        if (isset($tiposIns)) {
                                            foreach ($tiposIns as $t) {
                                                echo "<option value=" . $t['id'] . ">" . $t['nombre'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </datalist>
                                    <div class="col-lg-10">
                                        <input type="text" class="form-control"
                                               id="tipoInstrumento" name="tipoInstrumento"
                                               placeholder="Selecciona uno"
                                               list="tipos" autofocus required/>
                                    </div>
                                    <label for="idNivel" class="col-lg-2 control-label">Modulo Asociado:</label>
                                    <div class="col-lg-10">
                                        <datalist id="niveles"><?php
                                            $tiposIns = $planData->getEncabezados();
                                            if (isset($tiposIns)) {
                                                foreach ($tiposIns as $t) {
                                                    echo "<option value=" . $t['id'] . ">" . $t['nombre'] . "</option>";
                                                }
                                            }
                                            ?>
                                        </datalist>
                                        <input type="text" class="form-control"
                                               id="idNivel" name="idNivel"
                                               placeholder="Selecciona uno"
                                               list="niveles" autofocus required/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-10 col-lg-10">
                                        <button type="submit" class="btn btn-default">Siguiente</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                    break;

                case 1:
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <strong>Secciones</strong>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" action="index.php?mod=<?php echo $_REQUEST['mod']; ?>&niv=<?php echo $_REQUEST['niv']; ?>&operador=<?php echo $_REQUEST['operador']; ?>&seccion=2&codigoInstrumento=<?php echo $_REQUEST['codigoInstrumento'] ?>&task=add" method="post">
                                <table id="secciones">
                                    <thead>
                                        <tr>
                                            <th>N&uacute;mero</th>
                                            <th>Nombre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <textarea class="form-control" rows="4" cols="50"
                                                          id="nombreSeccion1" name = "nombreSeccion1"
                                                          size="45" maxlength="45"
                                                          placeholder="Escribe el nombre de la secci&oacute;n"
                                                          autofocus required ></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><button type="button" onclick="addRow('secciones')" class="btn btn-default">Agregar</button></td>
                                            <td><button type="button" onclick="deleteRow('secciones', true)" class="btn btn-default">Eliminar</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <input type="hidden" id="numeroSecciones" name="numeroSecciones" value="1"/>
                                <input type="hidden" id="codigoInstrumento" name="codigoInstrumento" value="<?= $codigo ?>"/>
                                <div class="form-group">
                                    <div class="col-lg-offset-10 col-lg-10">
                                        <button type="submit" class="btn btn-default">Siguiente</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                    break;

                case 2:
                    $codigo = $_REQUEST['codigoInstrumento'];
                    $numeroSecciones = $_REQUEST['numeroSecciones'];
                    $seccionActual = 0;
                    if (isset($_REQUEST['seccionActual'])) {
                        $seccionActual = $_REQUEST['seccionActual'];
                    }
                    $seccionActual += 0;
                    if ($seccionActual !== 0) {
                        $numeroPreguntas = $_REQUEST['numeroPreguntas'];
                        $instrumento = $daoInstrumentos->getInstrumentoByCodigo($codigo);
                        $seccion = $daoInstrumentos->getSeccionByCodigoInstrumentoAndNumero($instrumento, $seccionActual);
                        for ($index = 0; $index < $numeroPreguntas; $index++) {
                            $numeroPregunta = ($index + 1);
                            $requeridoPregunta = "off";
                            if (isset($_REQUEST['requeridoPregunta' . ($index + 1)])) {
                                $requeridoPregunta = $_REQUEST['requeridoPregunta' . ($index + 1)];
                            }
                            if ($requeridoPregunta == "on") {
                                $requeridoPregunta = 1;
                            } else {
                                $requeridoPregunta = 0;
                            }
                            $enunciadoPregunta = $_REQUEST['enunciadoPregunta' . ($index + 1)];
                            $tipoPregunta = $_REQUEST['tipoPregunta' . ($index + 1)];
                            if ($tipoPregunta == 3) {
                                $tipoPregunta = $_REQUEST['subtipoPregunta' . ($index + 1)];
                            }
                            $descripcion = null;
                            if (isset($_REQUEST['longitudPregunta' . ($index + 1)])) {
                                $descripcion = $_REQUEST['subtipoPregunta' . ($index + 1)]
                                        . "," . $_REQUEST['longitudPregunta' . ($index + 1)];
                            }
                            $opcionRespuesta = null;
                            if (isset($_REQUEST['opcRespuestaPregunta' . ($index + 1)])) {
                                $opcionRespuesta = $_REQUEST['opcRespuestaPregunta' . ($index + 1)];
                            }
                            $pregunta = new CPregunta('', $seccion, $tipoPregunta, $numeroPregunta, $requeridoPregunta, $enunciadoPregunta, $descripcion, $opcionRespuesta);
                            $daoInstrumentos->insertarPregunta($pregunta);
                        }
                    }
                    ?>
                    <ul class="nav nav-tabs nav-justified">
                        <?php
                        if ($seccionActual != $numeroSecciones) {
                            for ($index = 0; $index < $numeroSecciones; $index++) {
                                ?>
                                <li <?php
                                if ($seccionActual === $index) {
                                    echo 'class="active"';
                                }
                                if ($seccionActual < $index) {
                                    echo 'class="disabled"';
                                }
                                ?>><a href="#">Secci&oacute;n <?php echo ($index + 1) ?></a></li>
                                    <?php
                                }
                            }
                            ?>
                    </ul>
                    <datalist id="tipo">
                        <option value="3">Cerrada</option>
                        <option value="0">Abierta</option>
                    </datalist>
                    <datalist id="tipoCerrada">
                        <option value="1">Opci&oacute;n Multipe &Uacute;nica Respuesta (Radio Bot&oacute;n)</opcion>
                        <option value="2">Opci&oacute;n Multipe Multiple Respuesta</opcion>
                        <option value="4">Opci&oacute;n Multipe &Uacute;nica Respuesta (Selecci&oacute;n)</opcion>
                        <option value="7">F&oacute;rmula</opcion>
                    </datalist>
                    <datalist id="tipoAbierta">
                        <option value="0">Texto</option>
                        <option value="1">&Aacute;rea de Texto</option>
                        <option value="2">Tel&eacute;fono</option>
                        <option value="3">Email</option>
                        <option value="4">N&uacute;mero</option>
                        <option value="5">Fecha</option>
                        <option value="6">Archivo</option>
                    </datalist>
                    <?php
                    if ($seccionActual == $numeroSecciones) {
                        $html = new CHtml('');
                        $html->generaAviso($html->traducirTildes(ENCUESTA_AGREGADA_EXITO), "?mod=" . $modulo . "&niv=" . $niv
                                . "&task=list" . "&operador=" . $operador);
                    } else {
                        ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <strong>Preguntas</strong>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <form class="form-horizontal" action="index.php?mod=<?php echo $_REQUEST['mod']; ?>&niv=<?php echo $_REQUEST['niv']; ?>&operador=<?php echo $_REQUEST['operador']; ?>&seccion=2&seccionActual=<?php echo ($seccionActual + 1) ?>&numeroSecciones=<?php echo $numeroSecciones ?>&codigoInstrumento=<?php echo $codigo ?>&task=add" method="post">
                                    <table id="preguntas">
                                        <thead>
                                            <tr>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table id="pregunta1">
                                                        <tbody>
                                                            <tr>
                                                                <th colspan="2">Pregunta 1</th>
                                                            </tr>
                                                            <tr>
                                                                <td>N&uacute;mero:</td>
                                                                <td>1</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Requerido:</td>
                                                                <td><input type="checkbox"
                                                                           id="requeridoPregunta1"
                                                                           name="requeridoPregunta1"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Enunciado:</td>
                                                                <td>
                                                                    <textarea rows="4" cols="50"
                                                                              id="enunciadoPregunta1"
                                                                              name="enunciadoPregunta1"
                                                                              maxlength="200"
                                                                              autofocus
                                                                              required></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Tipo:</td>
                                                                <td>
                                                                    <input type="text"
                                                                           list="tipo"
                                                                           id="tipoPregunta1"
                                                                           name="tipoPregunta1"
                                                                           onchange="showOptions('pregunta1', this)"
                                                                           placeholder="Seleccione uno"
                                                                           required/>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><button type="button" onclick="addRowPregunta('preguntas')" class="btn btn-default">Agregar</button></td>
                                                <td><button type="button" onclick="deleteRow('preguntas', true)" class="btn btn-default">Eliminar</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" id="numeroPreguntas" name="numeroPreguntas" value="1"/>
                                    <div class="form-group">
                                        <div class="col-lg-offset-10 col-lg-10">
                                            <button type="submit" class="btn btn-default">Siguiente</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php
                    }
                    break;

                case 3:
                    $html->generaAviso($html->traducirTildes(ENCUESTA_AGREGADA_EXITO), "?mod=" . $modulo . "&niv=" . $niv
                            . "&task=list" . "&operador=" . $operador);
                    break;
            }
        }
        ?>

        <?php
        break;

    case 'edit':
        $idInstrumento = $_REQUEST['id_element'];
        $instrumento = $daoInstrumentos->getInstrumentoById($idInstrumento);
		$secciones = $daoInstrumentos->getSecciones($instrumento);
        $numeroSecciones = count($secciones);
        $seccionActual = 0;
        if (isset($_REQUEST['seccionActual'])) {
            $seccionActual = $_REQUEST['seccionActual'];
        }
        $preguntas = $daoInstrumentos->getPreguntas($secciones[$seccionActual]);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_INSTRUMENTO);
        $form->setOptions("autoClean", false);
        $form->setAction("?mod=" . $modulo . "&niv=" . $niv);
        $form->setMethod('post');
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ATRAS, 'button', '');
        $form->writeForm();
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <strong>Instrumento</strong>
                </h3>
            </div>
            <form action="?mod=<?php echo $modulo; ?>&niv=<?php echo $niv; ?>&idInstrumento=<?php echo $idInstrumento; ?>&task=updateInstrumento" method="post">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    Nombre Instrumento:
                                </span>
                                <input name="nombreInstrumento" id="nombreInstrumento" type='text' class="form-control" value='<?php echo $instrumento->getNombreInstrumento(); ?>'>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    C&oacute;digo:
                                </span>
                                <input name="codigoInstrumento" id="codigoInstrumento" type='text' class="form-control" value='<?php echo $instrumento->getCodigo(); ?>'>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    Encabezado:
                                </span>
                                <select class="form-control" name="encabezadoInstrumento" id="encabezadoInstrumento">
                                <?php
                                $tiposIns = $planData->getEncabezados();
                                if (isset($tiposIns)) {
                                    foreach ($tiposIns as $t) {
                                        if($t['id'] == $instrumento->getNivel()){
                                            echo "<option value=" . $t['id'] . " selected>" . $t['nombre'] . "</option>";
                                        } else {
                                            echo "<option value=" . $t['id'] . ">" . $t['nombre'] . "</option>";
                                        }
                                        
                                    }
                                }
                                ?>
                                </select>
                            </div>                      
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    Tipo:
                                </span>
                                <select class="form-control" name="tipoInstrumento" id="tipoInstrumento">
                                <?php
                                $tiposIns = $planData->getTipoInstrumentos('enc_tipo_id');
                                if (isset($tiposIns)) {
                                    foreach ($tiposIns as $t) {
                                        if($t['id'] == $instrumento->getTipo()){
                                            echo "<option value=" . $t['id'] . " selected>" . $t['nombre'] . "</option>";
                                        } else {
                                            echo "<option value=" . $t['id'] . ">" . $t['nombre'] . "</option>";
                                        }
                                        
                                    }
                                }
                                ?>
                                </select>
                            </div>                      
                        </div>
                        <div class="col-lg-2">
                            <button type='submit' class="form-control" value='Guardar'>Guardar</button>
                        </div>
                    </div>        
                </div>
            </form>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <strong>Secciones</strong>
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <table id="newSections">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        <input type="hidden" value="<?= count($secciones) ?>" id="seccionesActuales" />
                        <?php for ($i = 0; $i < count($secciones); $i++) { ?>
                            <tr>
                                <td>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                Nombre Seccion <?php echo ($i + 1); ?>:
                                            </span>
                                            <input type='text' class="form-control" value='<?php echo $secciones[$i]->getNombreSeccion(); ?>' id="nombreSeccion<?= $i ?>">
                                            <input type='hidden' class="form-control" value='<?php echo $secciones[$i]->getIdSeccion(); ?>' id="idSeccion<?= $i ?>">
                                        </div>
                                    </div> 
                                </td>
                                <td>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                N&uacute;mero:
                                            </span>
                                            <input type='text' class="form-control" value='<?php echo ($i + 1) ?>' id="numeroSeccion<?= $i ?>">
                                            <span class="input-group-addon"><a href="?mod=<?php echo $modulo; ?>&niv=<?php echo $niv; ?>&task=deleteSection&id_element=<?php echo $idInstrumento; ?>&idSeccion=<?php echo $secciones[$i]->getIdSeccion(); ?>"><img src="./templates/img/ico/borrar.gif" width="15" alt="<?php echo ALT_BORRAR ?>"/></a></span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <button type='button' class="form-control" value='Agregar' onclick="addRowActualizar('newSections')">Agregar</button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <button type='button' class="form-control" value='Eliminar' onclick="deleteRow('newSections', true, <?php echo count($secciones); ?>)">Eliminar</button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <button type='button' class="form-control" value='Guardar' onclick="guardarSecciones('newSections', '<?= $modulo ?>', '<?= $niv ?>', '<?= $idInstrumento ?>');">Guardar</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <input type="hidden" value="0" id="nuevasSecciones" />
                </div>   
                </table>
            </div>
        </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <strong>Preguntas</strong>
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?php
                    $form = new CHtmlForm();
                    $opciones = null;
                    if (isset($secciones)) {
                        $i = 0;
                        foreach ($secciones as $seccion) {
                            $opciones[count($opciones)] = array('value' => $i,
                                'texto' => $seccion->getNombreSeccion());
                            $i++;
                        }
                    }

                    $form->addEtiqueta(SECCIONES_INSTRUMENTO);
                    $form->addSelect('select', 'seccionActual', 'seccionActual', $opciones, '', $seccionActual, '', 'onchange="actualizarPreguntas(\'' . $modulo . '\',\'' . $niv . '\',\'edit\',this,\'' . $idInstrumento . '\');"');
                    $form->writeForm();

                    $dt = new CHtmlDataTable();
                    $dt->setTitleTable(PREGUNTAS_INSTRUMENTO);
                    $titulos = array(PREGUNTAS_INSTRUMENTO, ENUNCIADO_PREGUNTA);

                    $datos = null;
                    for ($i = 0; $i < count($preguntas); $i++) {
                        $datos[$i]['idPregunta'] = $preguntas[$i]->getIdPregunta();
                        $datos[$i]['pregunta'] = "Pregunta " . ($i + 1);
                        $datos[$i]['enunciado'] = $preguntas[$i]->getEnunciado();
                    }

                    $dt->setTitleRow($titulos);
                    $dt->setDataRows($datos);
                    $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editPregunta&idSeccion=" . $secciones[$seccionActual]->getIdSeccion() . "&idInstrumento=" . $instrumento->getId());
                    $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deletePregunta&idSeccion=" . $secciones[$seccionActual]->getIdSeccion() . "&idInstrumento=" . $instrumento->getId());
                    $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addPregunta&idSeccion=" . $secciones[$seccionActual]->getIdSeccion() . "&idInstrumento=" . $instrumento->getId());
                    $dt->setType(1);
                    $pag_crit = "";
                    $dt->setPag(1, $pag_crit);
                    $dt->writeDataTable($niv);
                    ?>
                </div>        
            </div>
        </div>
        <?php
        break;

    case 'addPregunta':
        $idInstrumento = $_REQUEST['idInstrumento'];
        $idSeccion = $_REQUEST['idSeccion'];

        $form = new CHtmlForm();

        $form->setTitle(TITULO_AGREGAR_PREGUNTA);
        $form->setId('frm_add_pregunta');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAddPregunta&idInstrumento=' . $idInstrumento . '&idSeccion=' . $idSeccion);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('tb_add_pregunta');
        ?>
        <datalist id="requerido">
            <option value="1">Si</option>
            <option value="0">No</option>
        </datalist>
        <datalist id="tipo">
            <option value="3">Cerrada</option>
            <option value="0">Abierta</option>
        </datalist>
        <datalist id="tipoCerrada">
            <option value="1">Opci&oacute;n Multipe &Uacute;nica Respuesta (Radio Bot&oacute;n)</opcion>
            <option value="2">Opci&oacute;n Multipe Multiple Respuesta</opcion>
            <option value="4">Opci&oacute;n Multipe &Uacute;nica Respuesta (Selecci&oacute;n)</opcion>
            <option value="7">F&oacute;rmula</opcion>
        </datalist>
        <datalist id="tipoAbierta">
            <option value="0">Texto</option>
            <option value="1">&Aacute;rea de Texto</option>
            <option value="2">Tel&eacute;fono</option>
            <option value="3">Email</option>
            <option value="4">N&uacute;mero</option>
            <option value="5">Fecha</option>
            <option value="6">Archivo</option>
        </datalist>        
        <?php
        $form->addEtiqueta(REQUERIDO_PREGUNTA);
        $form->addInputText('text', 'txt_requerido', 'txt_requerido', '1', '1', null, null, 'autofocus pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"  list="requerido" placeholder="Seleccione uno.." required');

        $form->addEtiqueta(ENUNCIADO_PREGUNTA);
        $form->addTextArea('textarea', 'txt_enunciado', 'txt_enunciado', '45', '45', null, null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"  required');

        $form->addEtiqueta(TIPO_PREGUNTA);
        $form->addInputText('text', 'txt_tipoPregunta', 'txt_tipoPregunta', '1', '1', null, null, 'list="tipo" pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"  onchange="agregarCamposPregunta(\'tb_add_pregunta\',this)" placeholder="Seleccione uno..." required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_pregunta\',\'?mod=' . $modulo . '&niv=' . $niv . '&task=edit&id_element=' . $idInstrumento . '\');"');

        $form->writeForm();
        break;

    case 'saveAddPregunta':
        $instrumento = new CInstrumento($_REQUEST['idInstrumento'], null, null);
        $seccion = new CSeccion($_REQUEST['idSeccion'], null, null, $instrumento);
        $tipoPregunta = $_REQUEST['txt_tipoPregunta'];
        if ($tipoPregunta == '3') {
            $tipoPregunta = $_REQUEST['txt_subtipo'];
        }
        $numero = $daoInstrumentos->getCantidadPreguntas($seccion) + 1;
        $requerido = $_REQUEST['txt_requerido'];
        $enunciado = $_REQUEST['txt_enunciado'];
        $descripcion = null;
        if ($tipoPregunta == '0') {
            $descripcion = $_REQUEST['txt_subtipo'] . ',' . $_REQUEST['txt_longitud'];
        }
        $opcionRespuesta = null;
        if ($tipoPregunta != '3') {
            $opcionRespuesta = $_REQUEST['txt_opciones_respuestas'];
        }
        $pregunta = new CPregunta(null, $seccion, $tipoPregunta, $numero, $requerido, $enunciado, $descripcion, $opcionRespuesta);
        $r = $daoInstrumentos->insertarPregunta($pregunta);
        $m = PREGUNTA_AGREGADA_ERROR;
        if ($r == TRUE) {
            $m = PREGUNTA_AGREGADA_EXITO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=edit&id_element=" . $instrumento->getId());
        break;

    case 'editPregunta':
        $idInstrumento = $_REQUEST['idInstrumento'];
        $idSeccion = $_REQUEST['idSeccion'];
        $idPregunta = $_REQUEST['id_element'];
        $pregunta = $daoInstrumentos->getPreguntaById($idPregunta);
        $form = new CHtmlForm();

        $form->setTitle(TITULO_EDITAR_PREGUNTA);
        $form->setId('frm_edit_pregunta');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEditPregunta&idInstrumento=' . $idInstrumento . '&idSeccion=' . $idSeccion . '&idPregunta=' . $idPregunta);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('tb_edit_pregunta');
        ?>
        <datalist id="requerido">
            <option value="1">Si</option>
            <option value="0">No</option>
        </datalist>
        <datalist id="tipo">
            <option value="3">Cerrada</option>
            <option value="0">Abierta</option>
        </datalist>
        <datalist id="tipoCerrada">
            <option value="1">Opci&oacute;n Multipe &Uacute;nica Respuesta (Radio Bot&oacute;n)</opcion>
            <option value="2">Opci&oacute;n Multipe Multiple Respuesta</opcion>
            <option value="4">Opci&oacute;n Multipe &Uacute;nica Respuesta (Selecci&oacute;n)</opcion>
            <option value="7">F&oacute;rmula</opcion>
        </datalist>
        <datalist id="tipoAbierta">
            <option value="0">Texto</option>
            <option value="1">&Aacute;rea de Texto</option>
            <option value="2">Tel&eacute;fono</option>
            <option value="3">Email</option>
            <option value="4">N&uacute;mero</option>
            <option value="5">Fecha</option>
            <option value="6">Archivo</option>
        </datalist>        
        <?php
        $form->addEtiqueta(REQUERIDO_PREGUNTA);
        $form->addInputText('text', 'txt_requerido', 'txt_requerido', '1', '1', $pregunta->isRequerido(), null, 'autofocus pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"  list="requerido" placeholder="Seleccione uno.." required');

        $form->addEtiqueta(ENUNCIADO_PREGUNTA);
        $form->addTextArea('textarea', 'txt_enunciado', 'txt_enunciado', '45', '45', $pregunta->getEnunciado(), null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"  required');

        if ($pregunta->getTipoPregunta() == '0') {
            $form->addEtiqueta(TIPO_PREGUNTA);
            $form->addInputText('text', 'txt_tipoPregunta', 'txt_tipoPregunta', '1', '1', '0', null, 'list="tipo" pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"  onchange="agregarCamposPregunta(\'tb_edit_pregunta\',this)" placeholder="Seleccione uno..." required');

            $subtipo = split(",", $pregunta->getDescripcion())[0];

            $form->addEtiqueta(SUBTIPO_PREGUNTA);
            $form->addTextArea('text', 'txt_subtipo', 'txt_subtipo', '1', '1', $subtipo, null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"  list="tipoAbierta" required');

            $longitud = split(",", $pregunta->getDescripcion())[1];

            $form->addEtiqueta(LONGITUD_PREGUNTA);
            $form->addTextArea('text', 'txt_longitud', 'txt_longitud', '2', '2', $longitud, null, 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"  required');
        } else {
            $form->addEtiqueta(TIPO_PREGUNTA);
            $form->addInputText('text', 'txt_tipoPregunta', 'txt_tipoPregunta', '1', '1', '3', null, 'list="tipo" pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"  onchange="agregarCamposPregunta(\'tb_edit_pregunta\',this)" placeholder="Seleccione uno..." required');

            $form->addEtiqueta(SUBTIPO_PREGUNTA);
            $form->addTextArea('text', 'txt_subtipo', 'txt_subtipo', '1', '1', $pregunta->getTipoPregunta(), null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"  list="tipoCerrada" required');

            $form->addEtiqueta(OPCIONES_RESPUESTAS_PREGUNTA);
            $form->addTextArea('text', 'txt_opciones_respuestas', 'txt_opciones_respuestas', '100', '100', $pregunta->getOpcionRespuesta(), null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" placeholder="Si,No,No Sabe,No Responde" required');
        }

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_pregunta\',\'?mod=' . $modulo . '&niv=' . $niv . '&task=edit&id_element=' . $idInstrumento . '\');"');

        $form->writeForm();
        break;

    case 'saveEditPregunta':
        $instrumento = new CInstrumento($_REQUEST['idInstrumento'], null, null);
        $seccion = new CSeccion($_REQUEST['idSeccion'], null, null, $instrumento);
        $idPregunta = $_REQUEST['idPregunta'];
        $tipoPregunta = $_REQUEST['txt_tipoPregunta'];
        if ($tipoPregunta == '3') {
            $tipoPregunta = $_REQUEST['txt_subtipo'];
        }
        $numero = $daoInstrumentos->getPreguntaById($idPregunta)->getNumero();
        $requerido = $_REQUEST['txt_requerido'];
        $enunciado = $_REQUEST['txt_enunciado'];
        $descripcion = null;
        if ($tipoPregunta == '0') {
            $descripcion = $_REQUEST['txt_subtipo'] . ',' . $_REQUEST['txt_longitud'];
        }
        $opcionRespuesta = null;
        if ($tipoPregunta != '3') {
            $opcionRespuesta = $_REQUEST['txt_opciones_respuestas'];
        }
        $pregunta = new CPregunta($idPregunta, $seccion, $tipoPregunta, $numero, $requerido, $enunciado, $descripcion, $opcionRespuesta);
        $r = $daoInstrumentos->updatePregunta($pregunta);
        $m = ERROR_ACTUALIZAR_PREGUNTA;
        if ($r == TRUE) {
            $m = EXITO_ACTUALIZAR_PREGUNTA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=edit&id_element=" . $instrumento->getId());
        break;

    case "delete":
        $id_element = $_REQUEST['id_element'];
        $html = new CHtml('');
        echo $html->generaAdvertencia(BORRAR_INSTRUMENTO, '?mod=' . $modulo .
                '&niv=' . $niv . '&task=confirmDelete&id_element=' . $id_element, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1\'');
        break;

    case 'confirmDelete':
        $id_element = $_REQUEST['id_element'];
        $r = $daoInstrumentos->borrarInstrumento($id_element);
        $mens = ERROR_BORRAR_INSTRUMENTO;
        if ($r == TRUE) {
            $mens = EXITO_BORRAR_INSTRUMENTO;
        }
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=" . $niv
                . "&task=list");
        break;

    case 'deleteSection':
        $url = $_SERVER['REQUEST_URI'];
        $urlAceptar = str_replace("deleteSection", "confirmDeleteSection", $url);
        $urlCancelar = str_replace("deleteSection", "edit", $url);
        $html = new CHtml('');
        echo $html->generaAdvertencia(BORRAR_SECCION, $urlAceptar, 'onclick=location.href=\'' . $urlCancelar . '\'');
        break;

    case 'confirmDeleteSection':
        $urlAceptar = str_replace("confirmDeleteSection", "edit", $_SERVER['REQUEST_URI']);
        $id_element = $_REQUEST['idSeccion'];
        $r = $daoInstrumentos->borrarSeccion($id_element);
        $mens = ERROR_BORRAR_SECCION;
        if ($r == TRUE) {
            $mens = EXITO_BORRAR_SECCION;
        }
        echo $html->generaAviso($mens, $urlAceptar);
        break;

    case 'deletePregunta':
        $url = $_SERVER['REQUEST_URI'];
        $urlAceptar = str_replace("deletePregunta", "confirmDeletePregunta", $url);
        $urlCancelar = str_replace("deletePregunta", "edit", $url);
        $urlCancelar = str_replace("id_element", "", $urlCancelar);
        $urlCancelar = str_replace("idInstrumento", "id_element", $urlCancelar);
        echo $html->generaAdvertencia(BORRAR_PREGUNTA, $urlAceptar, 'onclick=location.href=\'' . $urlCancelar . '\'');
        break;

    case 'confirmDeletePregunta':
        $id_element = $_REQUEST['id_element'];
        $idInstrumento = $_REQUEST['idInstrumento'];
        $r = $daoInstrumentos->borrarPregunta($id_element);
        $mens = ERROR_BORRAR_PREGUNTA;
        if ($r == TRUE) {
            $mens = EXITO_BORRAR_PREGUNTA;
        }
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=" . $niv . "&id_element=" . $idInstrumento . "&task=edit");
        break;

    case 'updateInstrumento':
        $idInstrumento = $_REQUEST['idInstrumento'];
        $nombreInstrumento = $_REQUEST['nombreInstrumento'];
        $codigoInstrumento = $_REQUEST['codigoInstrumento'];
        $encabezadoInstrumento = $_REQUEST['encabezadoInstrumento'];
        $instrumento = new CInstrumento($idInstrumento, $nombreInstrumento, $codigoInstrumento, 1 , $encabezadoInstrumento);
        $r = $daoInstrumentos->updateInstrumento($instrumento);
        $mens = ERROR_ACTUALIZAR_INSTRUMENTO;
        if ($r == TRUE) {
            $mens = EXITO_ACTUALIZAR_INSTRUMENTO;
        }
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=" . $niv . "&id_element=" . $idInstrumento . "&task=edit");
        break;

    case 'updateAndSaveSections':
        $idInstrumento = $_REQUEST['idInstrumento'];
        $instrumento = $daoInstrumentos->getInstrumentoById($idInstrumento);
        $values = $_REQUEST['values'];
        $numeroSeccionesActuales = $_REQUEST['seccionesActuales'];
        $numeroSecciones = $_REQUEST['secciones'];
        $values = split(",", $values);
        $r = TRUE;
        for ($i = 0; $i < (count($values) - 1); $i++) {
            $value = split(";", $values[$i]);
            if ($i < $numeroSeccionesActuales) {
                $idSeccion = $value[0];
                $nombreSeccion = $value[1];
                $numero = $value[2];
                $seccion = new CSeccion($idSeccion, $nombreSeccion, $numero, $instrumento);
                $r = $r && $daoInstrumentos->updateSeccion($seccion);
            } else {
                $nombreSeccion = $value[0];
                $numero = $value[1];
                $seccion = new CSeccion($idSeccion, $nombreSeccion, $numero, $instrumento);
                $r = $r && $daoInstrumentos->insertarSeccion($seccion);
            }
        }
        $m = ERROR_ACTUALIZAR_INSTRUMENTO;
        if ($r == TRUE) {
            $m = EXITO_ACTUALIZAR_INSTRUMENTO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=" . $niv . "&id_element=" . $idInstrumento . "&task=edit");
        break;

    default:
        include('templates/html/under.html');
        break;
}
