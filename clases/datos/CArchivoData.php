<?php

/**
 * Clase Archivo Data
 * Usado para la obtencion de directorios y archivos.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2015.03.11
 * @copyright SERTIC
 */
class CArchivoData {

    function getArchivos($ruta = "./") {
        $directorio = opendir($ruta);
        $directorios = null;
        $cont = 0;
        while ($archivo = readdir($directorio)) {
            if ($cont != 0) {
                $directorios[$cont]['id_element'] = $ruta . $archivo;
                $directorios[$cont]['nombre'] = "<a href='?mod=archivo&niv=1&task=download&operador=1&file=$ruta$archivo' download='$archivo'>" . $archivo . "</a>";
                $directorios[$cont]['tamano'] = filesize($ruta . $archivo) / 1000;
                if (is_dir($ruta . $archivo)) {
                    $directorios[$cont]['nombre'] = "<a href='?mod=archivo&niv=1&operador=1&ruta=$ruta$archivo'>" . $archivo . "</a>";
                    if ($cont != 1) {
                        $directorios[$cont]['tamano'] = $this->dirSize($ruta . $archivo) / 1000;
                    }
                }
                $directorios[$cont]['fechaModificacion'] = date('Y-m-d', filectime($ruta . $archivo));
                $directorios[$cont]['tipo'] = filetype($ruta . $archivo);
            }
            $cont++;
        }
        closedir($directorio);
        return $directorios;
    }

    function dirSize($directory) {
        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            $size+=$file->getSize();
        }
        return $size;
    }

    function insertArchivo($file, $ruta) {
        $ruta .= "/";
        $carpetas = explode("/", substr($ruta, 0, strlen($ruta) - 1));
        $ruta_destino = "";
        foreach ($carpetas as $c) {
            if (strlen($ruta_destino) > 0) {
                $ruta_destino .= "/" . $c;
            } else {
                $ruta_destino = $c;
            }
            if (!is_dir($ruta_destino)) {
                mkdir($ruta_destino, 0777);
            } else {
                chmod($ruta_destino, 0777);
            }
        }
        $ruta_destino .= "/";
        return move_uploaded_file($file['tmp_name'], utf8_decode($ruta_destino . $file['name']));
    }

    function deleteArchivo($filename) {
        return unlink($filename);
    }

    function updateArchivo($file, $ruta, $fileToErase) {
        return $this->deleteArchivo($fileToErase) && $this->insertArchivo($file, $ruta);
    }

}
