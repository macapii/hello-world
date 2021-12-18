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

$retorno = "";

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

if ($_SESSION['VALIDADO'] == $_SESSION['KEYSECRETA']) {

  if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2 || array_key_exists('psystemconf', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconf'] == 1) {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      if (isset($_POST['action']) && $_POST['action'] === 'submit') {

        $elerror = 0;
        $test = 0;

        $reccarpmine = CONFIGDIRECTORIO;

        //VARIABLE RUTA SERVIDOR MINECRAFT
        $rutacarpetamine = dirname(getcwd()) . PHP_EOL;
        $rutacarpetamine = trim($rutacarpetamine);
        $rutacarpetamine .= "/" . $reccarpmine;


        //INPUT LISTADO JARS
        if (isset($_POST["listadojars"])) {
          $ellistadojars = test_input($_POST["listadojars"]);

          //COMPOBAR SI HAY ".." "..."
          $verificar = array('..', '...', '/.', '~', '../', './', ';', ':', '>', '<', '/', '\\', '&&', '#', "|", '$', '%', '!', '`', '&', '*', '{', '}', '?', '=', '@', "'", '"', "'\'");

          for ($i = 0; $i < count($verificar); $i++) {

            $test = substr_count($ellistadojars, $verificar[$i]);

            if ($test >= 1) {
              $retorno = "novalidoname";
              $elerror = 1;
            }
          }

          //VERIFICAR SI EXISTE REALMENTE
          if ($elerror == 0) {
            $rutajar = $rutacarpetamine . "/" . $ellistadojars;

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
        } else {
          $ellistadojars = "";
        }

        //INPUT PUERTO
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfpuerto', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfpuerto'] == 1) {

            if (isset($_POST["elport"])) {
              $elpuerto = test_input($_POST["elport"]);

              //ES NUMERICO
              if ($elerror == 0) {
                if (!is_numeric($elpuerto)) {
                  $retorno = "portnonumerico";
                  $elerror = 1;
                }
              }

              //RANGO
              if ($elerror == 0) {
                if ($elpuerto < 1024 || $elpuerto > 65535) {
                  $retorno = "portoutrango";
                  $elerror = 1;
                }
              }
            } else {
              $retorno = "portvacio";
              $elerror = 1;
            }
          } else {
            $elpuerto = CONFIGPUERTO;
          }
        }

        //INPUT RAM
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfmemoria', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfmemoria'] == 1) {
            if (isset($_POST["elram"])) {
              $laram = test_input($_POST["elram"]);

              //ES NUMERICO
              if ($elerror == 0) {
                if (!is_numeric($laram)) {
                  $retorno = "ramnonumerico";
                  $elerror = 1;
                }
              }

              if ($elerror == 0) {
                $salida = shell_exec("free -g | grep Mem | gawk '{ print $2 }'");
                $totalram = trim($salida);
                $totalram = intval($totalram);

                if ($totalram <= 0) {
                  $retorno = "raminsuficiente";
                  $elerror = 1;
                } else {
                  if ($laram > $totalram) {
                    $retorno = "ramoutrange";
                    $elerror = 1;
                  }
                }
              }
            } else {
              $retorno = "ramvacia";
              $elerror = 1;
            }
          } else {
            $laram = CONFIGRAM;
          }
        }

        //INPUT TIPO SERVIDOR
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconftipo', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconftipo'] == 1) {
            if (isset($_POST["eltipserv"])) {
              $eltiposerver = test_input($_POST["eltipserv"]);
              $opcionesserver = array('vanilla', 'spigot', 'paper', 'forge', 'magma', 'otros');

              if ($elerror == 0) {
                if (!in_array($eltiposerver, $opcionesserver)) {
                  $retorno = "badtipserv";
                  $elerror = 1;
                }
              }
            } else {
              $retorno = "tipservvacio";
              $elerror = 1;
            }
          } else {
            $eltiposerver = CONFIGTIPOSERVER;
          }
        }

        //INPUT SUBIDA MAXIMA FICHEROS
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfsubida', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfsubida'] == 1) {
            if (isset($_POST["elmaxupload"])) {
              $eluploadmax = test_input($_POST["elmaxupload"]);
              $opcionesserver = array('128', '256', '386', '512', '640', '768', '896', '1024', '2048', '3072', '4096', '5120');
              if ($elerror == 0) {
                if (!in_array($eluploadmax, $opcionesserver)) {
                  $retorno = "badmaxupload";
                  $elerror = 1;
                }
              }
            } else {
              $retorno = "maxuploadvacio";
              $elerror = 1;
            }
          } else {
            $eluploadmax = CONFIGMAXUPLOAD;
          }
        }

        //INPUT NOMBRE SERVIDOR
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfnombre', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfnombre'] == 1) {
            if (isset($_POST["elnomserv"])) {
              $elnombreservidor = test_input($_POST["elnomserv"]);
            } else {
              $retorno = "nomservvacio";
              $elerror = 1;
            }
          } else {
            $elnombreservidor = CONFIGNOMBRESERVER;
          }
        }

        //INPUT BOOTCONFIG
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemstartonboot', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemstartonboot'] == 1) {

            if (isset($_POST["elbootconf"])) {
              $elbootconfig = test_input($_POST["elbootconf"]);
              if ($elbootconfig != "SI") {
                $elbootconfig = "NO";
              }
            } else {
              $retorno = "bootconfvacio";
              $elerror = 1;
            }
          } else {
            $elbootconfig = CONFIGBOOTSYSTEM;
          }
        }

        //LINEAS CONSOLA
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconflinconsole', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconflinconsole'] == 1) {
            if (isset($_POST["linconsola"])) {
              $elnumerolineaconsola = test_input($_POST["linconsola"]);

              //ES NUMERICO
              if ($elerror == 0) {
                if (!is_numeric($elnumerolineaconsola)) {
                  $retorno = "lineasconsolanonumerico";
                  $elerror = 1;
                }
              }

              //RANGO
              if ($elerror == 0) {
                if ($elnumerolineaconsola < 0 || $elnumerolineaconsola > 1000) {
                  $retorno = "lineasconsolaoutrango";
                  $elerror = 1;
                }
              }
            } else {
              $retorno = "linconsolavacio";
              $elerror = 1;
            }
          } else {
            $elnumerolineaconsola = CONFIGLINEASCONSOLA;
          }
        }

        //LINEAS BUFFER
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfbuffer', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfbuffer'] == 1) {
            if (isset($_POST["bufferlimit"])) {
              $elbufferlimit = test_input($_POST["bufferlimit"]);

              //ES NUMERICO
              if ($elerror == 0) {
                if (!is_numeric($elbufferlimit)) {
                  $retorno = "buffernonumerico";
                  $elerror = 1;
                }
              }

              //RANGO
              if ($elerror == 0) {
                if ($elbufferlimit < 0 || $elbufferlimit > 500) {
                  $retorno = "bufferoutrango";
                  $elerror = 1;
                }
              }
            } else {
              $retorno = "buffervacio";
              $elerror = 1;
            }
          } else {
            $elbufferlimit = CONFIGBUFFERLIMIT;
          }
        }

        //TIPO CONSOLA
        if ($elerror == 0) {

          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconftypeconsole', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconftypeconsole'] == 1) {
            if (isset($_POST["eltipoconsola"])) {
              $eltypeconsola = test_input($_POST["eltipoconsola"]);

              //ES NUMERICO
              if ($elerror == 0) {
                if (!is_numeric($eltypeconsola)) {
                  $retorno = "typenonumero";
                  $elerror = 1;
                }
              }

              //RANGO
              if ($elerror == 0) {
                if ($eltypeconsola < 0 || $eltypeconsola > 2) {
                  $retorno = "typeoutrango";
                  $elerror = 1;
                }
              }
            } else {
              $retorno = "typeconsolavacio";
              $elerror = 1;
            }
          } else {
            if (!defined('CONFIGCONSOLETYPE')) {
              $eltypeconsola = 2;
            } else {
              $eltypeconsola = CONFIGCONSOLETYPE;
            }
          }
        }

        //EXTRAS TAMAÑO CARPETAS
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2) {

            if (isset($_POST['gestorshowsizefolder'])) {
              $elmostrarsizecarpeta = test_input($_POST["gestorshowsizefolder"]);

              if ($elmostrarsizecarpeta != 1) {
                $elmostrarsizecarpeta = "";
              }
            } else {
              $elmostrarsizecarpeta = "";
            }
          } else {
            $elmostrarsizecarpeta = CONFIGLINEASCONSOLA;
          }
        }

        //EXTRA IGNORAR LIMITE RAM
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfignoreramlimit', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfignoreramlimit'] == 1) {

            if (isset($_POST['gestorignoreram'])) {
              $elignorarlimitram = test_input($_POST["gestorignoreram"]);

              if ($elignorarlimitram != 1) {
                $elignorarlimitram = "";
              }
            } else {
              $elignorarlimitram = "";
            }
          } else {
            $elignorarlimitram = CONFIGLINEASCONSOLA;
          }
        }

        //ARGUMENTOS JAVA
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemcustomarg', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemcustomarg'] == 1) {
            $cogercheck = 0;
            $checkarg = array('..', '...', '~', '../', './', ';', '>', '<', '\\', '&&', '#', "|", '$', '%', '!', '`', '&', '*', '{', '}', '?', '@', "'", '"', "'\'", '-Xms', '-Xmx', '-port', 'Dfile.encoding=UTF8', '-jar', 'java', 'cd ..');

            //ARGUMENTO INICIO
            if (isset($_POST['argmanualinicio'])) {
              $elargmanualinicio = $_POST["argmanualinicio"];
              $elargmanualinicio = addslashes($elargmanualinicio);
              $elargmanualinicio = test_input($elargmanualinicio);

              for ($i = 0; $i < count($checkarg); $i++) {

                $cogercheck = substr_count(strtolower($elargmanualinicio), strtolower($checkarg[$i]));

                if ($cogercheck >= 1) {
                  $retorno = "elargmanuininovalid";
                  $elerror = 1;
                }
              }
            } else {
              $elargmanualinicio = CONFIGARGMANUALINI;
            }

            if ($elerror == 0) {
              $cogercheck = 0;
              //ARGUMENTO FINAL
              if (isset($_POST["argmanualfinal"])) {
                $elargmanualfinal = $_POST["argmanualfinal"];
                $elargmanualfinal = addslashes($elargmanualfinal);
                $elargmanualfinal = test_input($elargmanualfinal);

                for ($i = 0; $i < count($checkarg); $i++) {

                  $cogercheck = substr_count(strtolower($elargmanualfinal), strtolower($checkarg[$i]));

                  if ($cogercheck >= 1) {
                    $retorno = "elargmanufinalnovalid";
                    $elerror = 1;
                  }
                }
              } else {
                $elargmanualfinal = CONFIGARGMANUALFINAL;
              }
            }
          } else {
            $elargmanualinicio = CONFIGARGMANUALINI;
            $elargmanualfinal = CONFIGARGMANUALFINAL;
          }
        }

        //MODO MANTENIMIENTO
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1) {

            if (isset($_POST['modomantenimiento'])) {
              $elmantenimiento = test_input($_POST["modomantenimiento"]);
              $opcmantenimiento = array('Desactivado', 'Activado');

              if (!in_array($elmantenimiento, $opcmantenimiento)) {
                $elmantenimiento = CONFIGMANTENIMIENTO;
              }
            } else {
              $elmantenimiento = CONFIGMANTENIMIENTO;
            }
          } else {
            $elmantenimiento = CONFIGMANTENIMIENTO;
          }
        }

        //RECOLECTOR DE BASURA
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfavanzados', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfavanzados'] == 1) {
            if (isset($_POST['recbasura'])) {
              $elgarbagecolector = test_input($_POST["recbasura"]);
            } else {
              $elgarbagecolector = CONFIGOPTIONGARBAGE;
            }

            if (isset($_POST['opforceupgrade'])) {
              $elforseupgrade = test_input($_POST["opforceupgrade"]);
            } else {
              $elforseupgrade = 0;
            }

            if (isset($_POST['operasecache'])) {
              $elerasecache = test_input($_POST["operasecache"]);
            } else {
              $elerasecache = 0;
            }
          } else {
            $elgarbagecolector = CONFIGOPTIONGARBAGE;
            $elforseupgrade = 0;
            $elerasecache = 0;
          }
        }

        //SELECTOR JAVA
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfjavaselect', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfjavaselect'] == 1) {
            $eljavaname = "0";
            $eljavamanual = "";
            $eljavaselect = "";

            if (isset($_POST['configjavaselect'])) {
              $eljavaselect = test_input($_POST["configjavaselect"]);
            }

            if ($eljavaselect == "0") {
              //PRIMERA OPCION
              $eljavaname = "0";
              $eljavamanual = "";
            } elseif ($eljavaselect == "1") {
              //SEGUNDA OPCION
              if (isset($_POST['selectedjavaver'])) {
                $eljavaname = test_input($_POST["selectedjavaver"]);

                //OBTENER DIRECTORIOS JAVA
                $existjavaruta = shell_exec("update-java-alternatives -l | gawk '{ print $3 }'");
                $existjavaruta = trim($existjavaruta);
                $existjavaruta = (explode("\n", $existjavaruta));
                $sijavaexist = 0;

                //COMPROBAR SI EXISTA LA RUTA INTRODUCIDA
                for ($i = 0; $i < count($existjavaruta); $i++) {
                  if ($existjavaruta[$i] == $eljavaname) {
                    $sijavaexist = 1;
                  }
                }

                //SI EXISTE COMPROBAR SI ESTA JAVA DENTRO
                if ($sijavaexist == 1) {
                  $eljavaname .= "/bin/java";
                  clearstatcache();
                  if (!file_exists($eljavaname)) {
                    $retorno = "nojavaenruta";
                    $elerror = 1;
                  }
                } else {
                  $retorno = "nojavaencontrado";
                  $elerror = 1;
                }
              }
            } elseif ($eljavaselect == "2") {
              //TERCERA OPCION
              if ($_SESSION['CONFIGUSER']['rango'] == 1) {
                if (isset($_POST['javamanual'])) {
                  $eljavamanual = test_input($_POST["javamanual"]);
                  $existjavaruta = trim($eljavamanual);
                  $existjavaruta .= "/bin/java";
                  $test = 0;

                  //COMPOBAR SI HAY ".." "..."
                  if ($elerror == 0) {

                    $verificar = array('..', '...', '/.', '~', '../', './', ';', ':', '>', '<', '\\', '&&', '#', "|", '$', '%', '!', '`', '&', '*', '{', '}', '?', '=', '@', "'", '"', "'\'");

                    for ($i = 0; $i < count($verificar); $i++) {

                      $test = substr_count($existjavaruta, $verificar[$i]);

                      if ($test >= 1) {
                        $retorno = "novalido";
                        $elerror = 1;
                      }
                    }
                  }

                  //COMPROBAR QUE NO ESTE DENTRO DE LA CARPETA RAIZ
                  if ($elerror == 0) {
                    $rutacheck = trim(dirname(getcwd()));
                    $rutajavacheck = substr($existjavaruta, 0, strlen($rutacheck));

                    if ($rutajavacheck == $rutacheck) {
                      $elerror = 1;
                      $retorno = "inpanel";
                    }
                  }

                  //COMPROBAR SI ESTA JAVA EN LA RUTA
                  clearstatcache();
                  if ($elerror == 0) {
                    if (!file_exists($existjavaruta)) {
                      $retorno = "nojavaenruta";
                      $elerror = 1;
                    }
                  }
                }
              } else {
                $eljavaselect = "0";
                $eljavaname = "0";
                $eljavamanual = "";
              }
            } else {
              $eljavaselect = "2";
              $eljavaname = "0";
              $eljavamanual = CONFIGJAVAMANUAL;
            }
          } else {
            //SI NO TIENE PERMISOS SE ASIGNA LOS QUE YA TIENE
            $eljavaselect = CONFIGJAVASELECT;
            $eljavaname = CONFIGJAVANAME;
            $eljavamanual = CONFIGJAVAMANUAL;
          }
        }

        //LIMITE ALMACENAMIENTO
        if ($elerror == 0) {
          if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconffoldersize', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconffoldersize'] == 1) {
            if (isset($_POST["limitbackupgb"])) {
              //OBTENER INPUT LIMITE BACKUPS GIGAS
              $ellimitebackupgb = test_input($_POST["limitbackupgb"]);

              //MIRAR SI ES NUMERICO
              if (is_numeric($ellimitebackupgb)) {
                //MIRAR SI SUPERA EL LIMITE PERMITIDO
                if ($ellimitebackupgb > 100) {
                  $elerror = 1;
                  $retorno = "datolimitebacksuperior";
                }
              } else {
                $elerror = 1;
                $retorno = "valornonumerico";
              }
            } else {
              $ellimitebackupgb = CONFIGFOLDERBACKUPSIZE;
            }

            if (isset($_POST["limitminecraftgb"])) {
              //OBTENER INPUT LIMITE MINECRAF GIGAS
              $ellimiteminecraftgb = test_input($_POST["limitminecraftgb"]);

              //MIRAR SI ES NUMERICO
              if (is_numeric($ellimiteminecraftgb)) {
                //MIRAR SI SUPERA EL LIMITE PERMITIDO
                if ($ellimiteminecraftgb > 100) {
                  $elerror = 1;
                  $retorno = "datolimiteminesuperior";
                }
              } else {
                $elerror = 1;
                $retorno = "valornonumerico";
              }
            } else {
              $ellimiteminecraftgb = CONFIGFOLDERMINECRAFTSIZE;
            }
          } else {
            $ellimitebackupgb = CONFIGFOLDERBACKUPSIZE;
            $ellimiteminecraftgb = CONFIGFOLDERMINECRAFTSIZE;
          }
        }

        //OPCIONES QUE NO SE CAMBIAN DESDE GUARDARSYSCONF
        $lakey = CONFIGSESSIONKEY;
        $eldirectorio = CONFIGDIRECTORIO;
        $elpostmax = "";
        $eleulaminecraft = CONFIGEULAMINECRAFT;

        //OBTENER RUTA DONDE TIENE QUE ESTAR LA CARPETA CONFIG
        $dirconfig = "";
        $dirconfig = dirname(getcwd()) . PHP_EOL;
        $dirconfig = trim($dirconfig);
        $dirconfig .= "/config";

        //OBTENER RUTA RAIZ
        $rutaraiz = dirname(getcwd()) . PHP_EOL;
        $rutaraiz = trim($rutaraiz);

        //COMPROVAR SI EXISTE CARPETA CONF
        if ($elerror == 0) {
          clearstatcache();
          if (!file_exists($dirconfig)) {
            $retorno = "nocarpetaconf";
            $elerror = 1;
          }
        }

        //COMPROVAR SI SE PUEDE ESCRIVIR CARPETA CONF
        if ($elerror == 0) {
          clearstatcache();
          if (!is_writable($dirconfig)) {
            $retorno = "nowriteconf";
            $elerror = 1;
          }
        }

        //COMPROVAR SI SE PUEDE ESCRIVIR ARCHIVO .htaccess de la raiz
        if ($elerror == 0) {
          $rutaescrivir = $rutaraiz;
          $rutaescrivir .= "/.htaccess";

          clearstatcache();
          if (file_exists($rutaescrivir)) {
            clearstatcache();
            if (!is_writable($rutaescrivir)) {
              $retorno = "nowritehtaccess";
              $elerror = 1;
            }
          }
        }

        if ($elerror == 0) {
          //CREAR RUTA FICHERO .htaccess en config
          $rutaescrivir = $dirconfig;
          $rutaescrivir .= "/.htaccess";

          //GUARDAR FICHERO .htaccess en config
          $file = fopen($rutaescrivir, "w");
          fwrite($file, "deny from all" . PHP_EOL);
          fclose($file);

          //CREAR RUTA FICHERO CONFOPCIONES.PHP
          $rutaescrivir = $dirconfig;
          $rutaescrivir .= "/confopciones.php";

          //GUARDAR FICHERO CONFOPCIONES.PHP
          $file = fopen($rutaescrivir, "w");
          fwrite($file, "<?php " . PHP_EOL);
          fwrite($file, 'define("CONFIGSESSIONKEY", "' . $lakey . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGNOMBRESERVER", "' . $elnombreservidor . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGDIRECTORIO", "' . $eldirectorio . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGPUERTO", "' . $elpuerto . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGRAM", "' . $laram . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGTIPOSERVER", "' . $eltiposerver . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGARCHIVOJAR", "' . $ellistadojars . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGEULAMINECRAFT", "' . $eleulaminecraft . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGMAXUPLOAD", "' . $eluploadmax . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGOPTIONGARBAGE", "' . $elgarbagecolector . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGOPTIONFORCEUPGRADE", "' . $elforseupgrade . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGOPTIONERASECACHE", "' . $elerasecache . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGJAVASELECT", "' . $eljavaselect . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGJAVANAME", "' . $eljavaname . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGJAVAMANUAL", "' . $eljavamanual . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGFOLDERBACKUPSIZE", "' . $ellimitebackupgb . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGFOLDERMINECRAFTSIZE", "' . $ellimiteminecraftgb . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGLINEASCONSOLA", "' . $elnumerolineaconsola . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGSHOWSIZEFOLDERS", "' . $elmostrarsizecarpeta . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGBOOTSYSTEM", "' . $elbootconfig . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGIGNORERAMLIMIT", "' . $elignorarlimitram . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGMANTENIMIENTO", "' . $elmantenimiento . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGBUFFERLIMIT", "' . $elbufferlimit . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGARGMANUALINI", "' . $elargmanualinicio . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGARGMANUALFINAL", "' . $elargmanualfinal . '");' . PHP_EOL);
          fwrite($file, 'define("CONFIGCONSOLETYPE", "' . $eltypeconsola . '");' . PHP_EOL);
          fwrite($file, "?>" . PHP_EOL);
          fclose($file);

          $rutaescrivir = $rutaraiz;
          $rutaescrivir .= "/.htaccess";

          $elpostmax = $eluploadmax + 1;

          $linea1 = "php_value upload_max_filesize " . $eluploadmax . "M";
          $linea2 = "php_value post_max_size " . $elpostmax . "M";
          $linea3 = "php_value max_file_uploads 1";

          //GUARDAR FICHERO .HTACCESS EN RAIZ
          $file = fopen($rutaescrivir, "w");
          fwrite($file, "<IfModule mod_php7.c>" . PHP_EOL);
          fwrite($file, $linea1 . PHP_EOL);
          fwrite($file, $linea2 . PHP_EOL);
          fwrite($file, $linea3 . PHP_EOL);
          fwrite($file, "php_value max_execution_time 600" . PHP_EOL);
          fwrite($file, "php_value max_input_time 600" . PHP_EOL);
          fwrite($file, "</IfModule>" . PHP_EOL);
          fwrite($file, "<IfModule mod_php8.c>" . PHP_EOL);
          fwrite($file, $linea1 . PHP_EOL);
          fwrite($file, $linea2 . PHP_EOL);
          fwrite($file, $linea3 . PHP_EOL);
          fwrite($file, "php_value max_execution_time 600" . PHP_EOL);
          fwrite($file, "php_value max_input_time 600" . PHP_EOL);
          fwrite($file, "</IfModule>" . PHP_EOL);
          fclose($file);
          sleep(2);
          $retorno = "saveconf";
        }
        echo $retorno;
      }
    }
  }
}
