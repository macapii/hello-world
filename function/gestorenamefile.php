<?php

/*
This file is part of McWebPanel.
Copyright (C) 2020 Cristina Ibañez, Konata400

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

require_once("../template/session.php");
require_once("../template/errorreport.php");

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

//COMPROVAR SI SESSION EXISTE SINO CREARLA CON NO
if (!isset($_SESSION['VALIDADO']) || !isset($_SESSION['KEYSECRETA'])) {
    $_SESSION['VALIDADO'] = "NO";
    $_SESSION['KEYSECRETA'] = "0";
}

//VALIDAMOS SESSION
if ($_SESSION['VALIDADO'] == $_SESSION['KEYSECRETA']) {

    if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2 || array_key_exists('pgestorarchivosrenombrar', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['pgestorarchivosrenombrar'] == 1) {

        if (isset($_POST['action']) && !empty($_POST['action']) || isset($_POST['renombre']) && !empty($_POST['renombre'])) {

            $archivo = "";
            $renombre = "";
            $retorno = "";
            $elerror = 0;
            $nuevofile = "";
            $getinfofile = "";
            $test = 0;

            $archivo = $_POST['action'];
            $renombre = $_POST['renombre'];

            $renombre = trim($renombre);

            //COMPROVAR SI ESTA VACIO RENOMBRE
            if ($elerror == 0) {
                if ($renombre == "") {
                    $retorno = "revacio";
                    $elerror = 1;
                }
            }

            //EVITAR RENOMBRAR .htaccess
            if ($elerror == 0) {
                if ($archivo == ".htaccess") {
                    $retorno = "seguridad";
                    $elerror = 1;
                }
            }

            //EVITAR CIERTAS EXTENSIONES
            if ($elerror == 0) {

                $verificar = array('phtml', 'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'phps', 'cgi', 'exe', 'pl', 'asp', 'aspx', 'shtml', 'shtm', 'fcgi', 'fpl', 'jsp', 'htm', 'html', 'wml', 'js', 'xhtml', 'xht', 'asa', 'cer', 'asax', 'swf', 'xap', 'css', 'sh', 'ksh', 'bash', 'zsh', 'py', 'pdf');

                $fileNameCmps = explode(".", $renombre);
                $fileExtension = strtolower(end($fileNameCmps));

                for ($i = 0; $i < count($verificar); $i++) {

                    if ($fileExtension == $verificar[$i]) {
                        $retorno = "extensionnovalid";
                        $elerror = 1;
                    }
                }
            }

            //COMPROVAR SI ESTA VACIO ARCHIVO
            if ($elerror == 0) {
                if ($archivo == "") {
                    $retorno = "archvacio";
                    $elerror = 1;
                }
            }

            //COMPOBAR SI HAY ".." "..." EN ARCHIVO
            if ($elerror == 0) {

                $test = 0;

                $verificar = array('..', '...', '~', '../', './', '&&');

                for ($i = 0; $i < count($verificar); $i++) {

                    $test = substr_count($archivo, $verificar[$i]);

                    if ($test >= 1) {
                        $retorno = "novalido";
                        $elerror = 1;
                    }
                }
            }

            //COMPOBAR SI HAY ".." "..." EN RENOMBRE
            if ($elerror == 0) {

                $test = 0;

                $verificar = array('..', '...', '/.', '~', '../', './', ';', ':', '>', '<', '/', '\\', '&&', '#', "|", '$', '%', '!', '`', '&', '*', '{', '}', '?', '=', '@', "'", '"', "'\'");

                for ($i = 0; $i < count($verificar); $i++) {

                    $test = substr_count($renombre, $verificar[$i]);

                    if ($test >= 1) {
                        $retorno = "renomnovalido";
                        $elerror = 1;
                    }
                }
            }


            //AÑADIR RUTAS
            if ($elerror == 0) {
                $archivo = $_SESSION['RUTACTUAL'] . "/" . $archivo;
                $nuevofile = $_SESSION['RUTACTUAL'] . "/" . $renombre;
            }

            //COMPROVAR QUE EL INICIO DE RUTA SEA IGUAL A LA SESSION
            if ($elerror == 0) {
                if ($_SESSION['RUTALIMITE'] != substr($archivo, 0, strlen($_SESSION['RUTALIMITE']))) {
                    $retorno = "rutacambiada";
                    $elerror = 1;
                }
            }

            //COMPROVAR QUE EL INICIO DE RUTA SEA IGUAL A LA SESSION
            if ($elerror == 0) {
                if ($_SESSION['RUTALIMITE'] != substr($nuevofile, 0, strlen($_SESSION['RUTALIMITE']))) {
                    $retorno = "rutacambiada";
                    $elerror = 1;
                }
            }

            //MIRAR SI EXISTE
            if ($elerror == 0) {
                clearstatcache();
                if (!file_exists($archivo)) {
                    $retorno = "noexiste";
                    $elerror = 1;
                }
            }

            //COMPROVAR SI EL RENOMBRE NUEVO A CREAR EXISTE
            if ($elerror == 0) {
                clearstatcache();
                if (file_exists($nuevofile)) {
                    $retorno = "yaexiste";
                    $elerror = 1;
                }
            }

            //COMPROVAR SI SE PUEDE ESCRIVIR
            if ($elerror == 0) {
                clearstatcache();
                if (!is_writable($archivo)) {
                    $retorno = "nowrite";
                    $elerror = 1;
                }
            }

            //RENOMBRAR
            if ($elerror == 0) {
                $retorno = rename($archivo, $nuevofile);
            }

            echo $retorno;
        }
    }
}
