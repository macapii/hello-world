<?php
/*
This file is part of McWebPanel.
Copyright (C) 2020-2022 Cristina Ibañez, Konata400

    McWebPanel is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    McWebPanel is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with McWebPanel.  If not, see <https://www.gnu.org/licenses/>.
*/
header("Content-Security-Policy: default-src 'none'; style-src 'self'; img-src 'self'; script-src 'self'; form-action 'self'; base-uri 'none'; connect-src 'self'; frame-ancestors 'none'");
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");
header('Permissions-Policy: geolocation=(), microphone=()');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once("../template/session.php");
require_once("../template/errorreport.php");

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

//COMPROBAR SI SESSION EXISTE SINO CREARLA CON NO
if (!isset($_SESSION['VALIDADO']) || !isset($_SESSION['KEYSECRETA'])) {
    $_SESSION['VALIDADO'] = "NO";
    $_SESSION['KEYSECRETA'] = "0";
}

//VALIDAMOS SESSION
if ($_SESSION['VALIDADO'] == $_SESSION['KEYSECRETA']) {

    if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2 || array_key_exists('pbackupsdescargar', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['pbackupsdescargar'] == 1) {

        if (isset($_GET['action']) && !empty($_GET['action'])) {

            $retorno = "";
            $verificarex = "";
            $test = 0;

            $archivo = test_input($_GET['action']);

            //Evitar poder ir a una ruta hacia atras
            if (strpos($archivo, '..') !== false || strpos($archivo, '*.*') !== false || strpos($archivo, '*/*.*') !== false) {
                exit;
            }

            //EVITAR Descargar .htaccess
            if ($archivo == ".htaccess") {
                exit;
            }

            //COMPROBAR SI HAY ".." "..."
            if ($elerror == 0) {

                $verificar = array('..', '...', '/.', '~', '../', './', '&&');

                for ($i = 0; $i < count($verificar); $i++) {

                    $test = substr_count($archivo, $verificar[$i]);

                    if ($test >= 1) {
                        exit;
                    }
                }
            }

            //VERIFICAR EXTENSION
            $verificarex = substr($archivo, -7);
            if ($verificarex != ".tar.gz") {
                exit;
            }

            $dirconfig = "";
            $dirconfig = dirname(getcwd()) . PHP_EOL;
            $dirconfig = trim($dirconfig);
            $dirconfig .= "/backups";

            $dirconfig = $dirconfig . "/" . $archivo;

            session_write_close();
            //COMPROBAR SI EXISTE
            clearstatcache();
            if (file_exists($dirconfig)) {
                //COMPROBAR SI SE PUEDE LEER
                clearstatcache();
                if (is_readable($dirconfig)) {
                    //COMPROBAR SI NO ES UN DIRECTORIO
                    clearstatcache();
                    if (is_dir($dirconfig)) {
                        exit;
                    } else {
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename="' . basename($dirconfig) . '"');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($dirconfig));
                        readfile($dirconfig);
                        exit;
                    }
                } else {
                    echo ('<!doctype html><html lang="es"><head><title>Backups</title><link rel="stylesheet" href="../css/bootstrap.min.css"></head><body>');
                    echo '<div class="alert alert-danger" role="alert">Error: El backup no tiene permisos de lectura.</div>';
                    echo ('</body></html>');
                }
            }
        }
    }
}
