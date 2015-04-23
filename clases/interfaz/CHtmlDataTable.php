<?php
/**
 * Gestion Interventoria - Fenix
 *
 * <ul>
 * <li> Redcom Ltda <www.redcom.com.co></li>
 * <li> Proyecto RUNT</li>
 * </ul>
 */

/**
 * Clase CHtmlDataTable
 *
 * genera una tabla en base a un arreglo de datos y otro con los titulos de la tabla
 *
 * @package  clases
 * @subpackage interfaz
 * @author Redcom Ltda
 * @version 2013.01.00
 * @copyright Ministerio de Transporte
 */
class CHtmlDataTable {

    var $id = null;
    var $dataRows = null;
    var $titleRow = null;
    var $tableStyle = null;
    var $titleTable = null;
    var $editLink = null;
    var $deleteLink = null;
    var $seeLink = null;
    var $addLink = null;
    var $textAddLink = null;
    var $targetSee = null;
    var $targetEdit = null;
    var $otrosLink = null;
    var $pag = null;
    var $type = null;
    var $pag_req = null;
    var $sumColumns = null;
    var $labelSum = null;
    var $versusSum = null;
    var $labelPrincipal = null;
    var $formatRow = null;
    var $orderTable = true;

    public function setOrderTable($state) {
        $this->orderTable = $state;
    }

    function setFormatRow($formatRow) {
        $this->formatRow = $formatRow;
    }

    public function setLabelSum($labelSum) {
        $this->labelSum = $labelSum;
    }

    public function setVersusSum($versus) {
        $this->versusSum = $versus;
    }

    public function setLabelPrincipal($labelPrincipal) {
        $this->labelPrincipal = $labelPrincipal;
    }

    function setDataRows($arreglo) {
        $this->dataRows = $arreglo;
    }

    function setTitleRow($arreglo) {
        $this->titleRow = $arreglo;
    }

    function setTitleTable($t) {
        $this->titleTable = $t;
    }

    function setEditLink($l, $t = '_self') {
        $this->editLink = $l;
        $this->targetEdit = $t;
    }

    function setDeleteLink($l) {
        $this->deleteLink = $l;
    }

    function setSeeLink($l, $t = '_self') {
        $this->seeLink = $l;
        $this->targetSee = $t;
    }

    function setDigitalizationLink($l, $t = '_self') {
        $this->digitalizationLink = $l;
        $this->targetDigitalization = $t;
    }

    function setAddLink($l, $texto = BTN_AGREGAR) {
        $this->addLink = $l;
        $this->textAddLink = $texto;
    }

    function setType($t) {
        $this->type = $t;
    }

    function addOtrosLink($arreglo) {
        $this->otrosLink[count($this->otrosLink)] = $arreglo;
    }

    function setPag($t, $r = '') {
        if ($t == 1)
            $this->pag = $t;
        else
            $this->pag = 0;
        $this->pag_req = $r;
    }

    function setSumColumns($arreglo) {
        $this->sumColumns = $arreglo;
    }

    function orderData($order_column, $ordcri) {
        $filas = 0;
        if (isset($this->dataRows)) {
            foreach ($this->dataRows as $f) {
                $columnas = 1;
                foreach ($f as $c) {
                    if ($columnas == $order_column + 1) {
                        $copy_array[$filas][0] = $c;
                    }
                    //$copy_array[$filas][$columnas+1]=$c;
                    $columnas++;
                }
                $filas++;
            }
        }
        $filas = 0;
        if (isset($this->dataRows)) {
            foreach ($this->dataRows as $f) {
                $columnas = 1;
                foreach ($f as $c) {
                    $copy_array[$filas][$columnas + 1] = $c;
                    $columnas++;
                }
                $filas++;
            }
        }
        if (isset($copy_array)) {
            reset($copy_array);
            if ($ordcri == "asc") {
                sort($copy_array);
                //$this->criterio="des";
            } else {
                rsort($copy_array);
                //$this->criterio="asc";
            }
            //sort($copy_array);
            //rsort($copy_array);
            $filas = 0;
            $this->dataRows = null;
            foreach ($copy_array as $f) {
                $columnas = 0;
                foreach ($f as $c) {
                    if ($columnas > 0) {
                        $this->dataRows[$filas][$columnas - 1] = $c;
                    }
                    $columnas++;
                }
                $filas++;
            }
        }
    }

    function sumData() {
        if (isset($this->dataRows)) {
            $temp = null;
            for ($i = 0; $i < count($this->dataRows[0]); $i++) {
                $temp[$i] = 0;
            }
            $filas = 0;
            foreach ($this->dataRows as $f) {
                $columnas = 0;
                foreach ($f as $c) {
                    if (in_array($columnas, $this->sumColumns)) {
                        $temp[$columnas] += $c;
                    }

                    $columnas++;
                }
                $filas++;
            }
        }
        $cont = 0;
        for ($i = 0; $i < count($temp); $i++) {
            if ($temp[$i] != 0) {
                $temp[$i] = $this->labelPrincipal[$cont] . number_format($temp[$i], 2, ',', '.');
                $cont++;
            }
        }
        return $temp;
    }

    function sumInfo() {
        if (isset($this->dataRows)) {
            $temp = null;
            for ($i = 0; $i < count($this->dataRows[0]); $i++) {
                $temp[$i] = null;
            }
            $filas = 0;
            foreach ($this->dataRows as $f) {
                $columnas = 0;
                foreach ($f as $c) {
                    if (in_array($columnas, $this->sumColumns)) {
                        $temp[$columnas] += $c;
                    } else {
                        $temp[$columnas] = null;
                    }
                    $columnas++;
                }
                $filas++;
            }
        }
        $cont = 0;
        for ($i = 0; $i < count($temp); $i++) {
            if (isset($this->labelSum[$i])) {
                if (isset($temp[$i])) {
                    $temp[$i] = $this->labelSum[$i] . number_format(($this->versusSum[$cont] - $temp[$i]), 2, ',', '.');
                    $cont++;
                } else {
                    $temp[$i] = $this->labelSum[$i];
                    $cont++;
                }
            } else {
                $temp[$i] = null;
            }
        }
        return $temp;
    }

    function writeDataTable($nivel) {
        $this->id = rand(0, 100);
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#myTable<?= $this->id ?>").tablesorter(
                        {widthFixed: true, headers: {
                                0: {sorter: false},
                                1: {sorter: false},
        <?= count($this->titleRow) + 2 ?>: {sorter: false}
                            }}
                );
                $("#myTable<?= $this->id ?>").tablesorterPager({container: $("#pager<?= $this->id ?>"),
                    positionFixed: false,
                    seperator: " de ",
                    size: <?= PAG_CANT ?>});
            });
        </script>
        <?php
        $html = new Chtml('');
        if (!isset($this->pag))
            $this->pag = 0;
        ?>
        <div class="row-fluid">
            <div class="span12"><?php
                switch ($this->type) {
                    case 1:
                        ?>
                        <table id="myTable<?= $this->id ?>" class="table table-bordered table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th colspan="<?php echo count($this->titleRow) + 2 ?>" class="titledatatable">
                            <div id="pager<?= $this->id ?>">
                                <form>
                                    <?= $html->traducirTildes($this->titleTable) ?>
                                    <img src="./templates/img/ico/first.png" class="first">
                                    <img src="./templates/img/ico/prev.png" class="prev">
                                    <input type="text" class="pagedisplay" style="background-color: #54acd2; text-align: center">
                                    <img src="./templates/img/ico/next.png" class="next">
                                    <img src="./templates/img/ico/last.png" class="last">
                                    <input type="hidden" class="pagesize" style="color: black;" value="<?= $html->traducirTildes(PAG_CANT) ?>">
                                </form>
                            </div>
                            </th>
                            </tr>
                            <tr>
                                <th style="font-size: 12px">#</th>
                                <?php foreach ($this->titleRow as $t) { ?>
                                    <th class="order" style="text-align:left; vertical-align: middle; padding-right: 20px; padding-left: 5px">
                                        <a href="#">
                                            <div style="font-size: 12px; color: white">
                                                <?php echo $html->traducirTildes($t); ?>
                                            </div>
                                        </a>
                                    </th>
                                <?php } ?>
                                <?php if (isset($this->addLink) || isset($this->editLink) || isset($this->seeLink) || isset($this->deleteLink) || isset($this->otrosLink)) { ?>
                                    <th style='font-size: 12px; color: white'><?php echo $html->traducirTildes(OPCIONES); ?></th>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                if (isset($this->dataRows)) {
                                    foreach ($this->dataRows as $row) {
                                        $i++;
                                        ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <?php $j = 0; ?>
                                            <?php $id = 0; ?>
                                            <?php foreach ($row as $cell) { ?>
                                                <?php
                                                if ($j != 0) {
                                                    if (isset($this->formatRow)) {
                                                        ?>
                                                        <?php
                                                        if (isset($this->formatRow[$j - 1])) {
                                                            $cell = number_format($cell, $this->formatRow[$j - 1][0], $this->formatRow[$j - 1][1], $this->formatRow[$j - 1][2]);
                                                        }
                                                    }
                                                    ?>
                                                    <td><?= $html->traducirTildes($cell) ?></td>
                                                <?php } else { ?>
                                                    <?php $id = $cell; ?>
                                                <?php } ?>
                                                <?php $j++; ?>
                                            <?php } ?>
                                            <?php if (isset($this->addLink) || isset($this->seeLink) || isset($this->editLink) || isset($this->deleteLink) || isset($this->otrosLink)) { ?>
                                                <td nowrap>
                                                    <?php if (!empty($this->seeLink)) { ?>
                                                        <a href="<?= $this->seeLink ?>&id_element=<?= $id ?>" target="<?= $this->targetSee; ?>"><img src="./templates/img/ico/ver.gif" border="0" width="20" alt="<?= ALT_VER ?>"/></a>
                                                    <?php } ?>
                                                    <?php if ($nivel == 1) { ?>
                                                        <?php if (!empty($this->digitalizationLink)) { ?>
                                                            <a href="<?= $this->digitalizationLink ?>&id_element=<?= $id ?>" target="<?= $this->targetDigitalization; ?>"><img src="./templates/img/ico/agregar.gif" border="0" width="20" alt="<?= BTN_AGREGAR ?>"/></a>
                                                        <?php } ?>
                                                        <?php if (!empty($this->editLink)) { ?>
                                                            <a href="<?= $this->editLink ?>&id_element=<?= $id ?>" target="<?= $this->targetEdit; ?>"><img src="./templates/img/ico/editar.gif" border="0" width="20" alt="<?= ALT_EDITAR ?>"/></a>
                                                        <?php } ?>
                                                        <?php if (!empty($this->deleteLink)) { ?>
                                                            <a href="<?= $this->deleteLink ?>&id_element=<?= $id ?>"><img src="./templates/img/ico/borrar.gif" border="0" width="20" alt="<?= ALT_BORRAR ?>"/></a>
                                                        <?php } ?>
                                                        <?php if (isset($this->otrosLink)) { ?>
                                                            <?php foreach ($this->otrosLink as $o) { ?>
                                                                <a href="<?= $o['link']; ?>&id_element=<?= $id ?>"><img src="./templates/img/ico/<?= $o['img']; ?>" border="0" width="20" alt="<?= $o['alt'] ?>"/></a>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                            <?php if (is_array($this->sumColumns)) { ?>
                                <tr class = "suma">
                                    <?php
                                    $sumas = $this->sumData();
                                    if (isset($sumas)) {
                                        foreach ($sumas as $s) {
                                            if ($s != '0') {
                                                echo "<td class='sumdatatable'>" . $s . "</td>";
                                            } else {
                                                echo "<td>&nbsp;</td>";
                                            }
                                        }
                                    }
                                    ?>
                                </tr>
                                <?php if (isset($this->versusSum)) { ?>
                                    <tr class = "suma">
                                        <?php
                                        $sumas = $this->sumInfo();
                                        if (isset($sumas)) {
                                            foreach ($sumas as $s) {
                                                if (isset($s) && $s != '0') {
                                                    echo "<td class='sumdatatable'>" . $s . "</td>";
                                                } else {
                                                    echo "<td>&nbsp;</td>";
                                                }
                                            }
                                        }
                                        ?>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                            <?php if ((!empty($this->addLink) && $nivel == 1)) { ?>
                                <tr class ="add">
                                    <td colspan="<?php echo count($this->titleRow) + 2 ?>" class="celladddatatable">
                                        <?php if ((!empty($this->addLink) && $nivel == 1)) { ?>
                                            <a href="<?php echo $this->addLink ?>"><img src="./templates/img/ico/agregar.gif" border="0" width="20" align="absmiddle" /><?php echo $html->traducirTildes($this->textAddLink); ?></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                        <?php
                        break;
                    case 2:
                        ?>
                        <table class="table table-bordered table-hover table-condensed">
                            <tr>
                                <th colspan="2" style="font-size: 12px; color: white; background-color: #54acd2; text-align: center"><?php echo $html->traducirTildes($this->titleTable) ?></td>
                            </tr>
                            <?php $cont_rows = 0; ?>
                            <?php foreach ($this->dataRows as $r) { ?>
                                <tr>
                                    <td style="text-align: left"><?php echo $html->traducirTildes($this->titleRow[$cont_rows]); ?></td>
                                    <td style="text-align: right"><?php echo $html->traducirTildes($r); ?></td>
                                </tr>
                                <?php $cont_rows++; ?>
                            <?php } ?>
                        </table>
                        <?php
                        break;
                }
                ?>
            </div>
        </div>
        <?php
    }

    function countPag() {
        $cant = intval(count($this->dataRows) / PAG_CANT);
        if (count($this->dataRows) % PAG_CANT > 0)
            $cant+=1;
        if ($cant == 0)
            $cant = 1;
        return $cant;
    }

}
?>