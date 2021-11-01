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

    if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2 || array_key_exists('pgestorarchivos', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['pgestorarchivos'] == 1) {

        if (isset($_POST['action']) && !empty($_POST['action'])) {

            if (!isset($_SESSION['GESTARCHPROSSES'])) {
                $_SESSION['GESTARCHPROSSES'] = 0;
            }

            $retorno = "";
            $reccarpmine = CONFIGDIRECTORIO;
            $elerror = 0;
            $test = 0;

            $archivo = test_input($_POST['action']);
            $archivo = trim($archivo);

            //OBTENER RUTA RAIZ
            $dirraiz = dirname(getcwd()) . PHP_EOL;
            $dirraiz = trim($dirraiz);

            //OBTENER IDENFIFICADOR SCREEN
            $processdescomzip = $dirraiz . "/gestorarchivos";
            $processdescomzip = str_replace("/", "", $processdescomzip);

            //VER SI HAY UN PROCESO YA EN USO
            $elcomando = "screen -ls | gawk '/\." . $processdescomzip . "\t/ {print strtonum($1)'}";
            $retornodeszip = shell_exec($elcomando);


            //SELECTOR TIPO PROCESO
            if ($retornodeszip != "") {
                $retorno = "ON";
            } else {
                if ($_SESSION['GESTARCHPROSSES'] == 1) {
                    $retorno = "REFRESH";
                    $_SESSION['GESTARCHPROSSES'] = 0;
                } else {
                    $retorno = "OFF";
                }
            }
        }
        echo $retorno;
    }
}
