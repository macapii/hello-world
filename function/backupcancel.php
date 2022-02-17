<?php

/*
This file is part of McWebPanel.
Copyright (C) 2020 Cristina Iba�ez, Konata400

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

//COMPROBAR SI SESSION EXISTE SINO CREARLA CON NO
if (!isset($_SESSION['VALIDADO']) || !isset($_SESSION['KEYSECRETA'])) {
    $_SESSION['VALIDADO'] = "NO";
    $_SESSION['KEYSECRETA'] = "0";
}

//VALIDAMOS SESSION
if ($_SESSION['VALIDADO'] == $_SESSION['KEYSECRETA']) {

    if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2 || array_key_exists('pbackupscrear', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['pbackupscrear'] == 1) {

        if (isset($_POST['action']) && !empty($_POST['action'])) {

            $retorno = "";
            $reccarpmine = CONFIGDIRECTORIO;
            $elerror = 0;
            $test = 0;

            $archivo = test_input($_POST['action']);

            //OBTENER RUTA RAIZ
            $dirraiz = dirname(getcwd()) . PHP_EOL;
            $dirraiz = trim($dirraiz);

            //OBTENER IDENFIFICADOR SCREEN
            $nombrescreen = $dirraiz . "/losbackups";
            $nombrescreen = str_replace("/", "", $nombrescreen);

            //VER SI HAY UN PROCESO YA EN BACKUP
            if ($elerror == 0) {
                $elcomando = "screen -ls | gawk '/\." . $nombrescreen . "\t/ {print strtonum($1)'}";
                $elpid = shell_exec($elcomando);

                if ($elpid == "") {
                    $retorno = "backupnoenjecucion";
                    $elerror = 1;
                }
            }


            if ($elerror == 0) {

                //MATAR SCREEN BACKUP
                $elcomando = "screen -S " . $nombrescreen . " -X quit";
                $retorno = shell_exec($elcomando);
                $retorno = "ok";
            }
        }
        echo $retorno;
    }
}
