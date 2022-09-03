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

    if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2 || array_key_exists('pstatusstarserver', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['pstatusstarserver'] == 1) {

        if (isset($_POST['action']) && !empty($_POST['action'])) {

            function guardareinicio($rutaelsh, $elcom, $rutaarchivlog)
            {
                $rutaelsh .= "/start.sh";

                clearstatcache();
                if (file_exists($rutaelsh)) {
                    clearstatcache();
                    if (is_writable($rutaelsh)) {
                        $file = fopen($rutaelsh, "w");
                        fwrite($file, "#!/bin/sh" . PHP_EOL);
                        fwrite($file, "rm " . $rutaarchivlog . PHP_EOL);
                        fwrite($file, $elcom . PHP_EOL);
                        fclose($file);
                    }
                } else {
                    $file = fopen($rutaelsh, "w");
                    fwrite($file, "#!/bin/sh" . PHP_EOL);
                    fwrite($file, $elcom . PHP_EOL);
                    fclose($file);
                }
            }

            $retorno = "";
            $elerror = 0;

            $reccarpmine = CONFIGDIRECTORIO;
            $recarchivojar = CONFIGARCHIVOJAR;
            $recram = CONFIGRAM;
            $rectiposerv = CONFIGTIPOSERVER;
            $receulaminecraft = CONFIGEULAMINECRAFT;
            $recpuerto = CONFIGPUERTO;

            $recgarbagecolector = CONFIGOPTIONGARBAGE;
            $recforseupgrade = CONFIGOPTIONFORCEUPGRADE;
            $recerasecache = CONFIGOPTIONERASECACHE;

            $recjavaselect = CONFIGJAVASELECT;
            $recjavaname = CONFIGJAVANAME;
            $recjavamanual = CONFIGJAVAMANUAL;

            $recignoreramlimit = CONFIGIGNORERAMLIMIT;

            $recargmanualinicio = CONFIGARGMANUALINI;
            $recargmanualfinal = CONFIGARGMANUALFINAL;

            if (!defined('CONFIGXMSRAM')) {
                $recxmsram = 1024;
            } else {
                $recxmsram = CONFIGXMSRAM;
            }

            $javaruta = "";

            $rutacarpetamine = "";

            //VARIABLE RUTA SERVIDOR MINECRAFT
            $rutacarpetamine = dirname(getcwd()) . PHP_EOL;
            $rutacarpetamine = trim($rutacarpetamine);
            $rutacarpetamine .= "/" . $reccarpmine;

            $rutaminecraffijo = $rutacarpetamine;

            //VARIABLE RUTA SERVER.PROPERTIES
            $rutaconfigproperties = $rutaminecraffijo;
            $rutaconfigproperties .= "/server.properties";

            //OBTENER PID SABER SI ESTA EN EJECUCION
            if ($elerror == 0) {
                $elcomando = "";
                $elcomando = "screen -ls | gawk '/\." . $reccarpmine . "\t/ {print strtonum($1)'}";
                $elpid = shell_exec($elcomando);

                //SI ESTA EN EJECUCION ENVIAR ERROR
                if (!$elpid == "") {
                    $elerror = 1;
                    $retorno = "yaenejecucion";
                }
            }

            //VERIFICAR CARPETA MINECRAFT
            if ($elerror == 0) {
                clearstatcache();
                if (!file_exists($rutacarpetamine)) {
                    $elerror = 1;
                    $retorno = "noexistecarpetaminecraft";
                }
            }

            //VERIFICAR SI HAY PERMISOS DE LECTURA EN EL SERVIDOR MINECRAFT
            if ($elerror == 0) {
                clearstatcache();
                if (!is_readable($rutacarpetamine)) {
                    $elerror = 1;
                    $retorno = "nolecturamine";
                }
            }

            //VERIFICAR SI HAY ESCRITURA EN EL SERVIDOR MINECRAFT
            if ($elerror == 0) {
                clearstatcache();
                if (!is_writable($rutacarpetamine)) {
                    $elerror = 1;
                    $retorno = "noescritura";
                }
            }

            //VERIFICAR SI HAY PERMISOS DE EJECUCION EN EL SERVIDOR MINECRAFT
            if ($elerror == 0) {
                clearstatcache();
                if (!is_executable($rutacarpetamine)) {
                    $elerror = 1;
                    $retorno = "noejecutable";
                }
            }

            if ($elerror == 0) {
                if ($rectiposerv == "forge") {
                    $libforge = $rutacarpetamine . "/libraries";
                    clearstatcache();
                    if (!file_exists($libforge)) {
                        $retorno = "nolibforge";
                        $elerror = 1;
                    }
                }
            }

            //VERIFICAR EULA EN CONFIG
            if ($elerror == 0) {
                if ($receulaminecraft != "1") {
                    $elerror = 1;
                    $retorno = "noeula";
                }
            }

            //CREAR EULA FORZADO
            if ($elerror == 0) {
                if ($receulaminecraft == "1") {
                    $rutaescribir = $rutacarpetamine;
                    $rutaescribir .= "/eula.txt";

                    clearstatcache();
                    if (file_exists($rutaescribir)) {
                        clearstatcache();
                        if (is_writable($rutaescribir)) {
                            $file = fopen($rutaescribir, "w");
                            fwrite($file, "eula=true" . PHP_EOL);
                            fclose($file);
                        } else {
                            $retorno = "eulanowrite";
                            $elerror = 1;
                        }
                    } else {
                        $file = fopen($rutaescribir, "w");
                        fwrite($file, "eula=true" . PHP_EOL);
                        fclose($file);
                    }

                    //PERMISO EULA.TXT
                    $elcommando = "cd " . $rutaminecraffijo . " && chmod 664 eula.txt";
                    exec($elcommando);
                }
            }

            //VERIFICAR SI HAY NOMBRE.JAR
            if ($elerror == 0) {
                if ($recarchivojar == "") {
                    $elerror = 1;
                    $retorno = "noconfjar";
                }
            }

            //VERIFICAR SI EXISTE REALMENTE
            if ($elerror == 0) {
                $rutajar = $rutacarpetamine . "/" . $recarchivojar;

                clearstatcache();
                if (!file_exists($rutajar)) {
                    $elerror = 1;
                    $retorno = "noexistejar";
                }
            }

            //COMPROBAR SI ES REALMENTE ARCHIVO JAVA
            if ($elerror == 0) {
                $tipovalido = 0;
                $eltipoapplication = mime_content_type($rutajar);

                switch ($eltipoapplication) {
                    case "application/java-archive":
                        $tipovalido = 1;
                        break;
                    case "application/zip":
                        $tipovalido = 1;
                        break;
                }

                if ($tipovalido == 0) {
                    $retorno = "notipovalido";
                    $elerror = 1;
                }
            }

            //COMPROBAR PUERTO EN USO
            if ($elerror == 0) {
                $comandopuerto = "netstat -tulpn 2>/dev/null | grep :" . $recpuerto;
                $obtener = exec($comandopuerto);
                if ($obtener != "") {
                    $elerror = 1;
                    $retorno = "puertoenuso";
                }
            }

            //COMPROBAR IGNORAR LIMITE RAM
            if ($recignoreramlimit != 1) {
                //COMPROBAR MEMORIA RAM
                if ($elerror == 0) {
                    $totalramsys = shell_exec("free -m | grep Mem | gawk '{ print $2 }'");
                    $totalramsys = trim($totalramsys);
                    $totalramsys = intval($totalramsys);

                    $getramavaliable = shell_exec("free -m | grep Mem | gawk '{ print $7 }'");
                    $getramavaliable = trim($getramavaliable);
                    $getramavaliable = intval($getramavaliable);

                    //COMPRUEBA SI AL MENOS SE TIENE 1GB
                    if ($totalramsys == 0) {
                        $elerror = 1;
                        $retorno = "rammenoragiga";
                    } elseif ($totalramsys >= 1) {

                        //COMPRUEBA QUE LA RAM SELECCIONADA NO SEA MAYOR A LA DEL SISTEMA
                        if ($recram > $totalramsys) {
                            $elerror = 1;
                            $retorno = "ramselectout";
                        }

                        //COMPROBAR SI HAY MEMORIA SUFICIENTE PARA INICIAR CON RAM DISPONIBLE
                        if ($elerror == 0) {
                            if ($recram > $getramavaliable) {
                                $elerror = 1;
                                $retorno = "ramavaliableout";
                            }
                        }
                    }
                }
            }

            //COMPROBAR ESCRITURA SERVER.PROPERTIES
            if ($elerror == 0) {
                $rutatemp = $rutaminecraffijo;
                $rutafinal = $rutaminecraffijo;
                $rutatemp .= "/serverproperties.tmp";
                $rutafinal .= "/server.properties";
                $contador = 0;
                $secuprofile = 0;

                clearstatcache();
                if (file_exists($rutafinal)) {
                    clearstatcache();
                    if (!is_writable($rutafinal)) {
                        $elerror = 1;
                        $retorno = "noescrituraservproperties";
                    }
                }
            }

            //AÑADIR PARAMETROS A SERVER.PROPERTIES
            if ($elerror == 0) {
                clearstatcache();
                if (file_exists($rutafinal)) {
                    $gestor = @fopen($rutafinal, "r");
                    $file = fopen($rutatemp, "w");

                    while (($búfer = fgets($gestor, 4096)) !== false) {
                        $str = $búfer;
                        $array = explode("=", $str);

                        if ($array[0] == "server-port") {
                            fwrite($file, 'server-port=' . $recpuerto . PHP_EOL);
                            $contador = 1;
                        } else {
                            fwrite($file, $búfer);
                        }

                        if ($array[0] == "enforce-secure-profile") {
                            $secuprofile = 1;
                        }
                    }

                    if ($contador == 0) {
                        fwrite($file, "server-port=" . $recpuerto . PHP_EOL);
                    }

                    //AÑADIR enforce-secure-profile EN FALSE SI NO EXISTE
                    if ($secuprofile == 0) {
                        fwrite($file, "enforce-secure-profile=false" . PHP_EOL);
                    }

                    fclose($gestor);
                    fclose($file);
                    rename($rutatemp, $rutafinal);

                    //PERMISO SERVER.PROPERTIES
                    $elcommando = "cd " . $rutaminecraffijo . " && chmod 664 server.properties";
                    exec($elcommando);
                } else {
                    //SI NO EXISTE POR CUALQUIER RAZON, SE GENERA UN ARCHIVO DE CONFIG MINIMA
                    $file = fopen($rutafinal, "w");
                    fwrite($file, "server-port=" . $recpuerto . PHP_EOL);
                    fwrite($file, "enforce-secure-profile=false" . PHP_EOL);
                    fclose($file);

                    //PERMISO SERVER.PROPERTIES
                    $elcommando = "cd " . $rutaminecraffijo . " && chmod 664 server.properties";
                    exec($elcommando);
                }
            }

            //INSERTAR SERVER-ICON EN CASO QUE NO EXISTA
            if ($elerror == 0) {
                $rutacarpetamine = dirname(getcwd()) . PHP_EOL;
                $rutacarpetamine = trim($rutacarpetamine);

                $rutaiconoimg = $rutacarpetamine . "/img/server-icon.png";
                $rutaiconofinal = $rutacarpetamine . "/" . $reccarpmine . "/server-icon.png";
                $rutacarpetamine .= "/" . $reccarpmine;

                //COMPROBAR SI EXISTE EN CARPETA IMG Y COPIARLA EN CASO QUE EL SERVIDOR NO LA TENGA
                clearstatcache();
                if (file_exists($rutaiconoimg)) {
                    clearstatcache();
                    if (!file_exists($rutaiconofinal)) {
                        copy($rutaiconoimg, $rutaiconofinal);
                    }
                }
            }

            //PERMISO SERVER-ICON.PNG
            if ($elerror == 0) {
                $elcommando = "cd " . $rutaminecraffijo . " && chmod 664 server-icon.png";
                exec($elcommando);
            }

            //INICIAR VARIABLE JAVARUTA Y COMPROBAR SI EXISTE
            if ($elerror == 0) {
                if ($recjavaselect == "0") {
                    $javaruta = "java";
                    //COMPROBAR SI JAVA DEFAULT EXISTE
                    $comreq = shell_exec('command -v java >/dev/null && echo "yes" || echo "no"');
                    $comreq = trim($comreq);
                    if ($comreq == "no") {
                        $retorno = "nojavadefault";
                        $elerror = 1;
                    }
                } elseif ($recjavaselect == "1") {
                    $javaruta = $recjavaname;
                    clearstatcache();
                    if (!file_exists($javaruta)) {
                        $retorno = "nojavaenruta";
                        $elerror = 1;
                    }
                } elseif ($recjavaselect == "2") {
                    $javaruta = $recjavamanual . "/bin/java";
                    clearstatcache();
                    if (!file_exists($javaruta)) {
                        $retorno = "nojavaenruta";
                        $elerror = 1;
                    }
                } else {
                    $retorno = "nojavaselect";
                    $elerror = 1;
                }
            }

            //CREAR CARPETA LOGS EN CASO QUE NO EXISTA
            if ($elerror == 0) {
                $rutacarplogs = $rutaminecraffijo . "/logs";
                clearstatcache();
                if (!file_exists($rutacarplogs)) {
                    mkdir($rutacarplogs, 0700);
                    $elcommando = "chmod 775 " . $rutacarplogs;
                    exec($elcommando);
                }
            }

            //COMPROBAR SI EXISTE SCREEN.CONF
            if ($elerror == 0) {
                $rutascreenconf = dirname(getcwd()) . PHP_EOL;
                $rutascreenconf = trim($rutascreenconf);
                $rutascreenconf .= "/config/screen.conf";

                clearstatcache();
                if (!file_exists($rutascreenconf)) {
                    $retorno = "noscreenconf";
                    $elerror = 1;
                }
            }

            //INICIAR SERVIDOR
            if ($elerror == 0) {
                $comandoserver = "";
                $cominiciostart = "";
                $larutash = "";
                $inigc = "";
                $iniforceupg = "";
                $inieracecache = "";

                $rutacarpetamine = dirname(getcwd()) . PHP_EOL;
                $rutacarpetamine = trim($rutacarpetamine);
                $larutash = $rutacarpetamine . "/" . $reccarpmine;
                $larutascrrenlog = $rutacarpetamine . "/" . $reccarpmine . "/logs/screen.log";
                $rutacarpetamine .= "/" . $reccarpmine . "/" . $recarchivojar;

                //BORRAR LOG SCREEN
                clearstatcache();
                if (file_exists($larutascrrenlog)) {
                    unlink($larutascrrenlog);
                }

                $comandoserver .= "cd .. && cd " . $reccarpmine . " && umask 002 && screen -c '" . $rutascreenconf . "' -dmS " . $reccarpmine . " -L -Logfile 'logs/screen.log' " . $javaruta . " -Xms" . $recxmsram . "M -Xmx" . $recram . "M ";

                //RECOLECTOR
                if ($recgarbagecolector == "1") {
                    $inigc = "-XX:+UseConcMarkSweepGC";
                } elseif ($recgarbagecolector == "2") {
                    $inigc = "-XX:+UseG1GC";
                }

                if ($inigc != "") {
                    $comandoserver .= $inigc . " ";
                }

                $comandoserver .= "-Dfile.encoding=UTF8 ";

                if ($recargmanualinicio != "") {
                    $comandoserver .= $recargmanualinicio . " ";
                }

                $comandoserver .= "-jar '" . $rutacarpetamine . "' ";

                //FORCEUPGRADE MAPA
                if ($recforseupgrade == "1") {
                    $iniforceupg = "--forceUpgrade";
                }

                if ($iniforceupg != "") {
                    $comandoserver .= $iniforceupg . " ";
                }

                //ERASE CACHE MAPA
                if ($recerasecache == "1") {
                    $inieracecache = "--eraseCache";
                }

                if ($inieracecache != "") {
                    $comandoserver .= $inieracecache . " ";
                }

                $comandoserver .= "nogui";

                if ($recargmanualfinal != "") {
                    $comandoserver .= " " . $recargmanualfinal;
                }

                //RESTART
                $cominiciostart = "screen -c '" . $rutascreenconf . "' -dmS " . $reccarpmine . " -L -Logfile 'logs/screen.log' " . $javaruta . " -Xms" . $recxmsram . "M -Xmx" . $recram . "M " . $inigc . " -Dfile.encoding=UTF8 " . $recargmanualinicio . " -jar '" . $rutacarpetamine . "' nogui " . $recargmanualfinal;
                if ($rectiposerv == "spigot") {
                    guardareinicio($larutash, $cominiciostart, $larutascrrenlog);
                } elseif ($rectiposerv == "paper") {
                    guardareinicio($larutash, $cominiciostart, $larutascrrenlog);
                }

                //CREAR SH
                $rutastartsh = dirname(getcwd()) . PHP_EOL;
                $rutastartsh = trim($rutastartsh);
                $startsh = $rutastartsh . "/temp";
                $startsh .= "/" . $reccarpmine . ".sh";

                $file = fopen($startsh, "w");
                fwrite($file, "#!/bin/sh" . PHP_EOL);
                fwrite($file, $comandoserver . PHP_EOL);
                fclose($file);

                $comandoperm = "chmod 744 " . $startsh;
                exec($comandoperm);
                exec("sh " . $startsh . " &");
                $retorno = "ok";
            }
            echo $retorno;
        }
    }
}
