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
require_once("../config/confopciones.php");

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

    if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2 || array_key_exists('pgestorarchivosexcludefiles', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['pgestorarchivosexcludefiles'] == 1) {

        if (isset($_POST['action']) && !empty($_POST['action'])) {

            $archivo = "";
            $rutadesdemine = "";
            $retorno = "";
            $elerror = 0;
            $test = 0;
            $reccarpmine = CONFIGDIRECTORIO;
            $rutaconfig = trim(dirname(getcwd()) . "/config" . PHP_EOL);
            $rutaarchivo = trim(dirname(getcwd()) . "/config" . "/excludeback.json" . PHP_EOL);

            $archivo = test_input($_POST['action']);

            //COMPROBAR SI ESTA VACIO
            if ($elerror == 0) {
                if ($archivo == "") {
                    $retorno = "nada";
                    $elerror = 1;
                }
            }

            //EVITAR .htaccess
            if ($elerror == 0) {
                if ($archivo == ".htaccess") {
                    $retorno = "seguridad";
                    $elerror = 1;
                }
            }

            //AÑADIR RUTA ACTUAL AL ARCHIVO
            if ($elerror == 0) {
                $archivo = $_SESSION['RUTACTUAL'] . "/" . $archivo;
            }


            //COMPROVAR QUE EL INICIO DE RUTA SEA IGUAL A LA SESSION
            if ($elerror == 0) {
                if ($_SESSION['RUTALIMITE'] != substr($archivo, 0, strlen($_SESSION['RUTALIMITE']))) {
                    $retorno = "rutacambiada";
                    $elerror = 1;
                }
            }

            //COMPOBAR SI HAY ".." "..."
            if ($elerror == 0) {

                $verificar = array('..', '...', '~', '../', './', '&&');

                for ($i = 0; $i < count($verificar); $i++) {

                    $test = substr_count($archivo, $verificar[$i]);

                    if ($test >= 1) {
                        $retorno = "novalido";
                        $elerror = 1;
                    }
                }
            }

            //COMPROVAR SI ARCHIVO EXISTE
            if ($elerror == 0) {
                clearstatcache();
                if (!file_exists($archivo)) {
                    $retorno = "noexiste";
                    $elerror = 1;
                }
            }

            //LIMPIAR RUTA
            if ($elerror == 0) {
                $rutacarpetamine = dirname(getcwd()) . "/" . $reccarpmine . "/" . PHP_EOL;
                $rutacarpetamine = trim($rutacarpetamine);
                $rutadesdemine = "./" . substr($archivo, strlen($rutacarpetamine));
            }

            //COMPROBAR SI YA EXISTE EL EXCLUIDO
            if ($elerror == 0) {
                clearstatcache();
                if (file_exists($rutaarchivo)) {
                    clearstatcache();
                    if (is_readable($rutaarchivo)) {
                        //OBTENER ARRAY
                        $buscaarray = file_get_contents($rutaarchivo);
                        $buscaexcluidos = unserialize($buscaarray);
                        $contador = count($buscaexcluidos);

                        for ($i = 0; $i < $contador; $i++) {
                            if ($buscaexcluidos[$i]['excluido'] == $rutadesdemine) {
                                $elerror = 1;
                                $retorno = "duplicado";
                            }
                        }

                        //BUSCAR SI YA ESTA AÑADIDO
                    } else {
                        $elerror = 1;
                        $retorno = "noread";
                    }
                }
            }

            //COMPROVAR SI SE PUEDE ESCRIBIR EN CONFIG
            if ($elerror == 0) {
                clearstatcache();
                if (!is_writable($rutaconfig)) {
                    $retorno = "nowriteconfig";
                    $elerror = 1;
                }
            }

            //AÑADIR EXCLUSION
            if ($elerror == 0) {
                clearstatcache();
                if (!file_exists($rutaarchivo)) {
                    $array[0]['excluido'] = $rutadesdemine;
                    $array[0]['completa'] = $archivo;
                    $serialized = serialize($array);
                    file_put_contents($rutaarchivo, $serialized);
                    $retorno = "ok";
                } else {
                    clearstatcache();
                    if (!is_writable($rutaarchivo)) {
                        $retorno = "nowritearchivo";
                        $elerror = 1;
                    } else {
                        $getarray = file_get_contents($rutaarchivo);
                        $arrayobtenido = unserialize($getarray);
                        $elindice = count($arrayobtenido);
                        $arrayobtenido[$elindice]['excluido'] = $rutadesdemine;
                        $arrayobtenido[$elindice]['completa'] = $archivo;
                        $serialized = serialize($arrayobtenido);
                        file_put_contents($rutaarchivo, $serialized);
                        $retorno = "ok";
                    }
                }
            }

            echo $retorno;
        }
    }
}
