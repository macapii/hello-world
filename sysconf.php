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

require_once("template/session.php");
require_once("template/errorreport.php");
require_once("config/confopciones.php");
require_once("template/header.php");
?>
<!-- Custom styles for this template-->
<link href="css/test.css" rel="stylesheet">

</head>

<body id="page-top">

    <?php

    $expulsar = 0;

    //COMPROVAR SI SESSION EXISTE SINO CREARLA CON NO
    if (!isset($_SESSION['VALIDADO']) || !isset($_SESSION['KEYSECRETA'])) {
        $_SESSION['VALIDADO'] = "NO";
        $_SESSION['KEYSECRETA'] = "0";
        header("location:index.php");
        exit;
    }

    //COMPROVAR SI ES EL SUPERADMIN O ADMIN O USER CON PERMISOS
    if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2 || array_key_exists('psystemconf', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconf'] == 1) {
        $expulsar = 1;
    }

    if ($expulsar != 1) {
        header("location:index.php");
        exit;
    }

    //VALIDAMOS SESSION SINO ERROR
    if ($_SESSION['VALIDADO'] == $_SESSION['KEYSECRETA']) {
    ?>

        <!-- Page Wrapper -->
        <div id="wrapper">

            <!-- Sidebar -->
            <?php
            require_once("template/menu.php");
            ?>
            <!-- End of Sidebar -->

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">

                <!-- Main Content -->
                <div id="content">

                    <!-- Begin Page Content -->
                    <div class="container-fluid">
                        <div class="col-md-12">
                            <div class="card border-left-primary shadow h-100">
                                <div class="card-body">
                                    <div class="py-1">
                                        <div class="container">
                                            <h1 class="mb-5">System Config</h1>
                                            <div class="row">
                                                <div class="col-md-12">

                                                    <!-- Page Heading -->
                                                    <?php

                                                    //VARIABLES
                                                    $contadorarchivos = 0;
                                                    $contadorsiexiste = 0;

                                                    $recpuerto = CONFIGPUERTO;
                                                    $recram = CONFIGRAM;
                                                    $rectiposerv = CONFIGTIPOSERVER;
                                                    $recnombreserv = CONFIGNOMBRESERVER;
                                                    $reccarpmine = CONFIGDIRECTORIO;
                                                    $recarchivojar = CONFIGARCHIVOJAR;
                                                    $receulaminecraft = CONFIGEULAMINECRAFT;
                                                    $recmaxupload = CONFIGMAXUPLOAD;

                                                    $recgarbagecolector = CONFIGOPTIONGARBAGE;
                                                    $recforseupgrade = CONFIGOPTIONFORCEUPGRADE;
                                                    $recerasecache = CONFIGOPTIONERASECACHE;

                                                    $recjavaselect = CONFIGJAVASELECT;
                                                    $recjavaname = CONFIGJAVANAME;
                                                    $recjavamanual = CONFIGJAVAMANUAL;

                                                    $recbackuplimitgb = CONFIGFOLDERBACKUPSIZE;
                                                    $recminecraftlimitgb = CONFIGFOLDERMINECRAFTSIZE;

                                                    $recnumerolineaconsola = CONFIGLINEASCONSOLA;

                                                    $recbuffer = CONFIGBUFFERLIMIT;

                                                    $recshowsizefolder = CONFIGSHOWSIZEFOLDERS;

                                                    $recbootconf = CONFIGBOOTSYSTEM;

                                                    $recignoreramlimit = CONFIGIGNORERAMLIMIT;

                                                    $recmantenimiento = CONFIGMANTENIMIENTO;

                                                    //OBTENER TIPO SERVIDOR WEB
                                                    $servidorweb = $_SERVER["SERVER_SOFTWARE"];

                                                    if (substr($servidorweb, 0, 6) == "Apache") {
                                                        $servidorweb = "Apache";
                                                    } else if (substr($servidorweb, 0, 5) == "Nginx") {
                                                        $servidorweb = "Nginx";
                                                    } else {
                                                        $servidorweb = "Otros";
                                                    }

                                                    $elnombredirectorio = $reccarpmine;
                                                    $rutaarchivo = getcwd();
                                                    $rutaarchivo = trim($rutaarchivo);
                                                    $rutaarchivo .= "/" . $elnombredirectorio;

                                                    //COMPRUEVA SI LA CARPETA DEL SERVIDOR MINECRAFT EXISTE
                                                    clearstatcache();
                                                    if (!file_exists($rutaarchivo)) {
                                                        echo "<div class='alert alert-danger' role='alert'>Error: La carpeta del servidor minecraft no existe.</div>";
                                                        exit;
                                                    }

                                                    //COMPRUEBA SI LA CARPETA DEL SERVIDOR MINECRAFT SE PUEDE LEER
                                                    clearstatcache();
                                                    if (!is_readable($rutaarchivo)) {
                                                        echo "<div class='alert alert-danger' role='alert'>Error: La carpeta del servidor minecraft no tiene permisos de lectura.</div>";
                                                        exit;
                                                    }

                                                    ?>
                                                    <form id="formconf" action="function/guardasysconf.php" method="post">
                                                        <div class="form-group">
                                                            <label class="negrita" for="listadojars">Seleccione Servidor Minecraft:</label>
                                                            <select class="form-control mb-2" id="listadojars" name="listadojars">

                                                                <?php

                                                                if ($handle = opendir($rutaarchivo)) {
                                                                    while (false !== ($file = readdir($handle))) {
                                                                        $fileNameCmps = explode(".", $file);
                                                                        $fileExtension = strtolower(end($fileNameCmps));

                                                                        if ($file == $recarchivojar) {
                                                                            $contadorsiexiste = 1;
                                                                        }

                                                                        if ($fileExtension == "jar") {
                                                                            $contadorarchivos++;
                                                                        }
                                                                    }
                                                                    closedir($handle);
                                                                }

                                                                if ($contadorarchivos == 0) {
                                                                    echo '<option selected disabled hidden>No hay subido ningún servidor .jar</option>';
                                                                } else {

                                                                    if ($recarchivojar == "" || $contadorsiexiste == 0) {
                                                                        echo '<option selected disabled hidden>No hay ningún servidor seleccionado</option>';
                                                                    }


                                                                    if ($handle = opendir($rutaarchivo)) {
                                                                        while (false !== ($file = readdir($handle))) {
                                                                            $fileNameCmps = explode(".", $file);
                                                                            $fileExtension = strtolower(end($fileNameCmps));

                                                                            if ($fileExtension == "jar") {

                                                                                if ($file == $recarchivojar) {
                                                                                    echo '<option selected value="' . $file . '">' . $file . '</option>';
                                                                                } else {
                                                                                    echo '<option value="' . $file . '">' . $file . '</option>';
                                                                                }
                                                                            }
                                                                        }
                                                                        closedir($handle);
                                                                    }
                                                                }

                                                                ?>
                                                            </select>
                                                        </div>

                                                        <div class="form-row">

                                                            <?php
                                                            //PUERTO
                                                            if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfpuerto', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfpuerto'] == 1) {
                                                            ?>
                                                                <div class="form-group col-md-6">
                                                                    <label class="negrita" for="elport">Puerto:</label>
                                                                    <input type="number" class="form-control" id="elport" name="elport" required="required" min="1025" max="65535" value="<?php echo $recpuerto; ?>">
                                                                </div>

                                                            <?php
                                                            }
                                                            ?>

                                                            <?php
                                                            //MEMORIA RAM LIMITE
                                                            if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfmemoria', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfmemoria'] == 1) {
                                                            ?>
                                                                <div class="form-group col-md-6">
                                                                    <label for="elram" class="negrita">Memoria Ram Limite:</label>
                                                                    <select id="elram" name="elram" class="form-control" required="required">
                                                                        <?php

                                                                        $salida = shell_exec("free -g | grep Mem | awk '{ print $2 }'");
                                                                        $totalram = trim($salida);
                                                                        $totalram = intval($totalram);
                                                                        if ($totalram == 0) {
                                                                            echo '<option selected value="0">MEMORIA INSUFICIENTE / NO TIENES NI UN GB</option>';
                                                                        } elseif ($totalram >= 1) {
                                                                            for ($i = 1; $i <= $totalram; $i++) {
                                                                                if ($recram == $i) {
                                                                                    echo '<option selected value="' . $i . '">' . $i . ' GB</option>';
                                                                                } else {
                                                                                    echo '<option value="' . $i . '">' . $i . ' GB</option>';
                                                                                }
                                                                            }
                                                                        }

                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            <?php
                                                            }
                                                            ?>
                                                        </div>

                                                        <div class="form-row">

                                                            <?php
                                                            //TIPO SERVIDOR
                                                            if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconftipo', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconftipo'] == 1) {
                                                            ?>


                                                                <div class="form-group col-md-6">
                                                                    <label class="negrita" for="eltipserv">Tipo Servidor:</label>
                                                                    <select id="eltipserv" name="eltipserv" class="form-control" required="required">
                                                                        <?php
                                                                        $opcionesserver = array('vanilla', 'spigot', 'paper', 'forge', 'magma', 'otros');

                                                                        for ($i = 0; $i < count($opcionesserver); $i++) {

                                                                            if ($rectiposerv == $opcionesserver[$i]) {
                                                                                echo '<option selected value="' . $opcionesserver[$i] . '">' . $opcionesserver[$i] . '</option>';
                                                                            } else {
                                                                                echo '<option value="' . $opcionesserver[$i] . '">' . $opcionesserver[$i] . '</option>';
                                                                            }
                                                                        }

                                                                        ?>

                                                                    </select>
                                                                </div>

                                                            <?php
                                                            }
                                                            ?>

                                                            <?php
                                                            //LIMITE SUBIR ARCHIVO
                                                            if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfsubida', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfsubida'] == 1) {
                                                            ?>

                                                                <div class="form-group col-md-6">
                                                                    <label class="negrita" for="elmaxupload">Subida Fichero Máximo (MB):</label>
                                                                    <select id="elmaxupload" name="elmaxupload" class="form-control" required="required">
                                                                        <?php

                                                                        //COMPROBAR TIPO DE SERVIDOR WEB
                                                                        if ($servidorweb == "Apache") {
                                                                            $opcionesserver = array('128', '256', '386', '512', '640', '768', '896', '1024', '2048', '3072', '4096', '5120');

                                                                            for ($i = 0; $i < count($opcionesserver); $i++) {

                                                                                if ($recmaxupload == $opcionesserver[$i]) {
                                                                                    echo '<option selected value="' . $opcionesserver[$i] . '">' . $opcionesserver[$i] . " MB" . '</option>';
                                                                                } else {
                                                                                    echo '<option value="' . $opcionesserver[$i] . '">' . $opcionesserver[$i] . " MB" . '</option>';
                                                                                }
                                                                            }
                                                                        } else {
                                                                            $maxdeupload = ini_get("upload_max_filesize");
                                                                            $maxdeupload = substr($maxdeupload, 0, -1);
                                                                            $maxdeupload = trim($maxdeupload);
                                                                            echo '<option value="' . $maxdeupload . '">' . "Solo disponible en Apache" . '</option>';
                                                                        }

                                                                        ?>

                                                                    </select>
                                                                </div>
                                                            <?php
                                                            }
                                                            ?>
                                                        </div>

                                                        <div class="form-row">

                                                            <?php
                                                            //NOMBRE SERVIDOR
                                                            if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfnombre', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfnombre'] == 1) {
                                                            ?>

                                                                <div class="form-group col-md-6">
                                                                    <label class="negrita" for="elnomserv">Nombre Servidor:</label>
                                                                    <input type="text" class="form-control" id="elnomserv" name="elnomserv" required="required" value="<?php echo $recnombreserv; ?>">
                                                                </div>

                                                            <?php
                                                            }
                                                            ?>

                                                            <?php
                                                            //INICIAR AL ARRANCAR SERVIDOR LINUX
                                                            if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemstartonboot', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemstartonboot'] == 1) {
                                                            ?>

                                                                <div class="form-group col-md-6">
                                                                    <label class="negrita" for="elbootconf">Iniciar servidor Minecraft al arrancar linux:</label>
                                                                    <select id="elbootconf" name="elbootconf" class="form-control" required="required">
                                                                        <?php

                                                                        //COMPROBAR OPCION BOOT
                                                                        if ($recbootconf == "SI") {
                                                                            echo '<option value="NO">NO</option>';
                                                                            echo '<option selected value="SI">SI</option>';
                                                                        } else {
                                                                            echo '<option selected value="NO">NO</option>';
                                                                            echo '<option value="SI">SI</option>';
                                                                        }

                                                                        ?>

                                                                    </select>

                                                                </div>

                                                            <?php
                                                            }
                                                            ?>

                                                        </div>

                                                        <?php
                                                        //LINEAS CONSOLA
                                                        if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconflinconsole', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconflinconsole'] == 1) {
                                                        ?>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <label class="negrita" for="linconsola">Líneas Mostradas por Consola:</label>
                                                                    <input type="number" class="form-control" id="linconsola" name="linconsola" required="required" min="0" max="1000" value="<?php echo $recnumerolineaconsola; ?>">
                                                                    <label> 0 = Ilimitado</label>
                                                                </div>
                                                            </div>

                                                        <?php
                                                        }
                                                        ?>

                                                        <?php
                                                        //LINEAS BUFFER
                                                        if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfbuffer', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfbuffer'] == 1) {
                                                        ?>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <label class="negrita" for="bufferlimit">Lineas Buffer Consola:</label>
                                                                    <input type="number" class="form-control" id="bufferlimit" name="bufferlimit" required="required" min="0" max="500" value="<?php echo $recbuffer; ?>">
                                                                    <label> 0 = Desactivado</label>
                                                                </div>
                                                            </div>

                                                        <?php
                                                        }
                                                        ?>

                                                        <?php
                                                        //SELECTOR DE JAVA
                                                        if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfjavaselect', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfjavaselect'] == 1) {
                                                        ?>
                                                            <hr>
                                                            <div class="form-group">
                                                                <div class="form-row">
                                                                    <div class="form-group col-md-8">
                                                                        <label class="negrita">Selector de JAVA:</label>
                                                                        <div class="col-md-6">
                                                                            <input type="radio" id="configjavaselect0" name="configjavaselect" value="0" <?php if ($recjavaselect == "0") {
                                                                                                                                                                echo "checked";
                                                                                                                                                            } ?>>
                                                                            <label for="configjavaselect0">Usar JAVA defecto sistema</label>
                                                                            <p><?php echo exec("java -version 2>&1 | head -n 1 | awk '{ print $1 $3 }'"); ?></p>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <input type="radio" id="configjavaselect1" name="configjavaselect" value="1" <?php if ($recjavaselect == "1") {
                                                                                                                                                                echo "checked";
                                                                                                                                                            } ?>>
                                                                            <label for="configjavaselect1">Seleccionar versión JAVA</label>
                                                                            <select id="selectedjavaver" name="selectedjavaver" class="form-control">

                                                                                <?php
                                                                                $javalist = "";
                                                                                $javaruta = "";
                                                                                $javalist = shell_exec("update-java-alternatives -l | awk '{ print $1 }'");
                                                                                $javaruta = shell_exec("update-java-alternatives -l | awk '{ print $3 }'");
                                                                                $javalist = trim($javalist);
                                                                                $javaruta = trim($javaruta);
                                                                                $javalist = (explode("\n", $javalist));
                                                                                $javaruta = (explode("\n", $javaruta));

                                                                                for ($i = 0; $i < count($javalist); $i++) {

                                                                                    if ($javaruta[$i] . "/bin/java" == $recjavaname) {
                                                                                        echo '<option selected value="' . $javaruta[$i] . '">' . $javalist[$i] . '</option>';
                                                                                    } else {
                                                                                        echo '<option value="' . $javaruta[$i] . '">' . $javalist[$i] . '</option>';
                                                                                    }
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>

                                                                        <?php
                                                                        if ($_SESSION['CONFIGUSER']['rango'] == 1) {
                                                                        ?>
                                                                            <br>
                                                                            <div class="col-md-8">
                                                                                <input type="radio" id="configjavaselect2" name="configjavaselect" value="2" <?php if ($recjavaselect == "2") {
                                                                                                                                                                    echo "checked";
                                                                                                                                                                } ?>>
                                                                                <label for="configjavaselect2">Ruta manual JAVA</label>
                                                                                <p>Ejemplo: /usr/lib/jvm/java-1.11.0-openjdk-amd64</p>
                                                                                <input type="text" class="form-control" id="javamanual" name="javamanual" value="<?php echo $recjavamanual; ?>">
                                                                            </div>
                                                                        <?php
                                                                        }
                                                                        if ($_SESSION['CONFIGUSER']['rango'] == 2 && $recjavaselect == "2") {
                                                                        ?>
                                                                            <br>
                                                                            <div class="col-md-8">
                                                                                <input type="radio" id="configjavaselect2" name="configjavaselect" value="" <?php if ($recjavaselect == "2") {
                                                                                                                                                                echo "checked";
                                                                                                                                                            } ?>>
                                                                                <label for="configjavaselect2">Ruta manual JAVA (Configurado por Superusuario)</label>
                                                                                <input readonly type="text" class="form-control" value="<?php echo $recjavamanual; ?>">
                                                                            </div>
                                                                        <?php
                                                                        }
                                                                        ?>

                                                                    </div>

                                                                </div>

                                                            </div>
                                                        <?php
                                                        }
                                                        ?>

                                                        <?php

                                                        //LIMITES ALMACENAMIENTO
                                                        if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconffoldersize', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconffoldersize'] == 1) {
                                                        ?>
                                                            <hr>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label class="negrita">Limite Almacenamiento:</label>
                                                                    </div>
                                                                </div>
                                                            
                                                            <div class="">
                                                                
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <label>Carpeta Backups</label>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <label>Carpeta Minecraft</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="form-group">
                                                                                <input type="number" class="form-control  text-right" id="limitbackupgb" name="limitbackupgb" required="required" min="0" max="100" value="<?php echo $recbackuplimitgb; ?>">
                                                                                <label> 0 = Ilimitado</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-1">
                                                                            <p class="lead">GB</p>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="form-group">
                                                                                <input type="number" class="form-control text-right" id="limitminecraftgb" name="limitminecraftgb" required="required" min="0" max="100" value="<?php echo $recminecraftlimitgb; ?>">
                                                                                <label> 0 = Ilimitado</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-1">
                                                                            <p class="lead">GB</p>
                                                                        </div>
                                                                    </div>
                                                                
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>

                                                        <?php
                                                        //EXTRAS
                                                        if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2) {
                                                        ?>
                                                            <hr>
                                                            <label class="negrita">Extras:</label>
                                                            <div class="form-group">
                                                                <input id="gestorshowsizefolder" name="gestorshowsizefolder" type="checkbox" value="1" <?php if ($recshowsizefolder == "1") {
                                                                                                                                                            echo "checked";
                                                                                                                                                        } ?>>
                                                                <label class="" for="gestorshowsizefolder">Mostrar el tamaño de las carpetas y el total usado en el Gestor Archivos (Puede ralentizar el gestor archivos)</label>

                                                                <?php

                                                                //EXTRA IGNORAR RAM LIMITE
                                                                if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfignoreramlimit', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfignoreramlimit'] == 1) {
                                                                ?>
                                                                    <br>
                                                                    <input id="gestorignoreram" name="gestorignoreram" type="checkbox" value="1" <?php if ($recignoreramlimit == "1") {
                                                                                                                                                        echo "checked";
                                                                                                                                                    } ?>>
                                                                    <label class="" for="gestorignoreram">Ignorar límite Ram disponible al arrancar servidor de Minecraft.</label>
                                                                <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        <?php
                                                        }
                                                        //FIN EXTRAS

                                                        //PARAMETROS AVANZADOS
                                                        if ($_SESSION['CONFIGUSER']['rango'] == 1 || array_key_exists('psystemconfavanzados', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['psystemconfavanzados'] == 1) {
                                                        ?>
                                                            <hr>
                                                            <div class="form-group">
                                                                <label class="negrita">Parametros Avanzados:</label>
                                                                <div class="form-row">
                                                                    <div class="form-group col-md-6">
                                                                        <p>Garbage collector - Recolector de basura</p>
                                                                        <div>
                                                                            <input type="radio" id="basura0" name="recbasura" value="0" <?php if ($recgarbagecolector == "0") {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>
                                                                            <label for="basura0">Ninguno</label>
                                                                        </div>

                                                                        <div>
                                                                            <input type="radio" id="basura1" name="recbasura" value="1" <?php if ($recgarbagecolector == "1") {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>
                                                                            <label for="basura1">Usar ConcMarkSweepGC (Solo Java 8)</label>
                                                                        </div>

                                                                        <div>
                                                                            <input type="radio" id="basura2" name="recbasura" value="2" <?php if ($recgarbagecolector == "2") {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>
                                                                            <label for="basura2">Usar G1GC (Java 8/11 o superior)</label>
                                                                        </div>

                                                                    </div>

                                                                    <div class="form-group col-md-6">
                                                                        <br>
                                                                        <label>Conversion Mapa ¡PRECAUCIÓN!</label>
                                                                        <div>
                                                                            <input id="opforceupgrade" name="opforceupgrade" type="checkbox" value="1" <?php if ($recforseupgrade == "1") {
                                                                                                                                                            echo "checked";
                                                                                                                                                        } ?>>
                                                                            <label for="opforceupgrade">Usar --forceUpgrade (Requiere Versión: 1.13 o superior)</label>
                                                                        </div>

                                                                        <div>
                                                                            <input id="operasecache" name="operasecache" type="checkbox" value="1" <?php if ($recerasecache == "1") {
                                                                                                                                                        echo "checked";
                                                                                                                                                    } ?>>
                                                                            <label for="operasecache">Usar --eraseCache (Requiere Versión: 1.14 o superior)</label>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                        <hr>

                                                        <?php
                                                        //MODO MANTENIMIENTO
                                                        if ($_SESSION['CONFIGUSER']['rango'] == 1) {
                                                        ?>
                                                            <div class="form-group col-md-8">
                                                                <label class="negrita" for="modomantenimiento">Modo Mantenimiento</label>
                                                                <select id="modomantenimiento" name="modomantenimiento" class="form-control" required="required">
                                                                    <?php
                                                                    $opcmantenimiento = array('Desactivado', 'Activado');

                                                                    for ($i = 0; $i < count($opcmantenimiento); $i++) {

                                                                        if ($recmantenimiento == $opcmantenimiento[$i]) {
                                                                            echo '<option selected value="' . $opcmantenimiento[$i] . '">' . $opcmantenimiento[$i] . '</option>';
                                                                        } else {
                                                                            echo '<option value="' . $opcmantenimiento[$i] . '">' . $opcmantenimiento[$i] . '</option>';
                                                                        }
                                                                    }

                                                                    ?>

                                                                </select>
                                                                <p class="">- Se cierra la sesión de todos los admins y usuarios activos.<br>- No permitirá iniciar sesión excepto el Superusuario. <br>- Se requiere su activación para actualizar el servidor.</p>
                                                            </div>

                                                        <?php
                                                        }
                                                        ?>

                                                        <hr>
                                                        <button class="btn btn-primary btn-block" id="guardaserver" name="guardarserver">Guardar Cambios</button>
                                                        <input type="hidden" name="action" value="submit">
                                                        <br>
                                                        <div class="form-group">
                                                            <span id="result"></span>
                                                        </div>

                                                    </form>
                                                    <hr id="finpage">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.container-fluid -->
                    </div>
                    <!-- End of Main Content -->
                </div>
                <!-- End of Content Wrapper -->
            </div>
            <!-- End of Page Wrapper -->
        </div>
        <script src="js/sysconf.js"></script>
    <?php
        //FINAL VALIDAR SESSION
    } else {
        header("location:index.php");
    }
    ?>

</body>

</html>