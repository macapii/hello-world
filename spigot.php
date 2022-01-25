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

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
<!-- Custom styles for this template-->
<link href="css/test.css" rel="stylesheet">
<link href="css/consola.css" rel="stylesheet">

</head>

<body id="page-top">

    <?php

    $expulsar = 0;

    $totver = 0;

    //COMPROVAR SI SESSION EXISTE SINO CREARLA CON NO
    if (!isset($_SESSION['VALIDADO']) || !isset($_SESSION['KEYSECRETA'])) {
        $_SESSION['VALIDADO'] = "NO";
        $_SESSION['KEYSECRETA'] = "0";
        header("location:index.php");
        exit;
    }

    //COMPROVAR SI ES EL SUPERADMIN O ADMIN
    if (array_key_exists('rango', $_SESSION['CONFIGUSER'])) {
        if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2 || array_key_exists('ppagedownserver', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['ppagedownserver'] == 1) {
            $expulsar++;
        }
    }

    if (array_key_exists('rango', $_SESSION['CONFIGUSER'])) {
        if ($_SESSION['CONFIGUSER']['rango'] == 1 || $_SESSION['CONFIGUSER']['rango'] == 2 || array_key_exists('pcompilarspigot', $_SESSION['CONFIGUSER']) && $_SESSION['CONFIGUSER']['pcompilarspigot'] == 1) {
            $expulsar++;
        }
    }

    if ($expulsar != 2) {
        header("location:index.php");
        exit;
    }

    //VALIDAMOS SESSION SINO ERROR
    if ($_SESSION['VALIDADO'] == $_SESSION['KEYSECRETA']) {

        $url = "https://hub.spigotmc.org/nexus/content/repositories/snapshots/org/spigotmc/spigot-api/maven-metadata.xml";

        $context = stream_context_create(
            array(
                "http" => array(
                    "timeout" => 10,
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                )
            )
        );

        $contenido = @file_get_contents($url, false, $context);

        if ($contenido === FALSE) {
            $versiones[] = "1.17.1";
            $versiones[] = "1.17";
            $versiones[] = "1.16.5";
            $versiones[] = "1.16.4";
            $versiones[] = "1.16.3";
            $versiones[] = "1.16.2";
            $versiones[] = "1.16.1";
            $versiones[] = "1.15.2";
            $versiones[] = "1.15.1";
            $versiones[] = "1.15";
            $versiones[] = "1.14.4";
            $versiones[] = "1.14.3";
            $versiones[] = "1.14.2";
            $versiones[] = "1.14.1";
            $versiones[] = "1.14";
            $versiones[] = "1.13.2";
            $versiones[] = "1.13.1";
            $versiones[] = "1.13";
            $versiones[] = "1.12.2";
            $versiones[] = "1.12.1";
            $versiones[] = "1.12";
            $versiones[] = "1.11.2";
            $versiones[] = "1.11.1";
            $versiones[] = "1.11";
            $versiones[] = "1.10.2";
            $versiones[] = "1.10";
            $versiones[] = "1.9.4";
            $versiones[] = "1.9.2";
            $versiones[] = "1.9";
            $versiones[] = "1.8.8";
            $versiones[] = "1.8.7";
            $versiones[] = "1.8.6";
            $versiones[] = "1.8.5";
            $versiones[] = "1.8.4";
            $versiones[] = "1.8.3";
            $versiones[] = "1.8";
        } else {

            $contenido = htmlentities($contenido);

            $elarray = explode(" ", $contenido);

            for ($i = 0; $i < count($elarray); $i++) {

                $test = substr_count($elarray[$i], "R0.1-SNAPSHOT");

                if ($test >= 1) {

                    $test = substr_count($elarray[$i], "pre");

                    if ($test == 0) {

                        $test = substr_count($elarray[$i], "latest");

                        if ($test == 0) {
                            $linea = trim($elarray[$i]);
                            $linea = substr($linea, 0, -30);
                            $linea = substr($linea, 15);
                            $versiones[] = test_input(trim($linea));
                            $totver++;
                        }
                    }
                }
            }

            if ($totver > 0) {
                $versiones = array_reverse($versiones);
            } else {
                $versiones[] = "ERROR";
            }
        }

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
                                            <h1 class="mb-5">Compilar Spigot</h1>

                                            <div class="py-2">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="serselectver">Selecciona Versión Spigot:</label>
                                                            <select id="serselectver" name="serselectver" class="form-control" required="required">
                                                                <?php
                                                                for ($i = 0; $i < count($versiones); $i++) {
                                                                    echo '<option value="' . $versiones[$i] . '">Spigot '  . $versiones[$i] . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                            <br>
                                                            <?php
                                                            $elerror = 0;
                                                            $retorno = "";
                                                            $recjavaselect = CONFIGJAVASELECT;
                                                            $recjavaname = CONFIGJAVANAME;
                                                            $recjavamanual = CONFIGJAVAMANUAL;

                                                            $javaruta = "";


                                                            if ($recjavaselect == "0") {
                                                                $javaruta = "java";
                                                                //COMPROBAR SI JAVA DEFAULT EXISTE
                                                                $comreq = shell_exec('command -v java >/dev/null && echo "yes" || echo "no"');
                                                                $comreq = trim($comreq);
                                                                if ($comreq == "no") {
                                                                    $retorno = "Java no encontrado en el servidor";
                                                                    $elerror = 1;
                                                                }
                                                            } elseif ($recjavaselect == "1") {
                                                                $javaruta = $recjavaname;
                                                                clearstatcache();
                                                                if (!file_exists($javaruta)) {
                                                                    $retorno = "El archivo java no se encuentra en la ruta";
                                                                    $elerror = 1;
                                                                }
                                                            } elseif ($recjavaselect == "2") {
                                                                $javaruta = $recjavamanual . "/bin/java";
                                                                clearstatcache();
                                                                if (!file_exists($javaruta)) {
                                                                    $retorno = "El archivo java no se encuentra en la ruta";
                                                                    $elerror = 1;
                                                                }
                                                            } else {
                                                                $retorno = "No hay ningún java seleccionado en System Config";
                                                                $elerror = 1;
                                                            }

                                                            if ($elerror == 0) {
                                                                if ($totver == 0) {
                                                                    $retorno = "No se pudo obtener las versiones de la pagina de Spigot<br>Carcaga lista versiones interna";
                                                                    echo '<p>Error: ' . $retorno . '</p>';
                                                                }
                                                            }

                                                            if ($elerror == 0) {
                                                            ?>
                                                                <p>Se compilara usando JAVA: <?php echo exec($javaruta . " -version 2>&1 | head -n 1 | gawk '{ print $1 $3 }'"); ?></p>
                                                                <button class="btn btn-primary btn-block mt-2" id="compilar" name="compilar">Compilar</button>
                                                                <button class="btn btn-danger btn-block mt-2" id="killcompilar" name="killcompilar">Matar Compilar</button>
                                                            <?php
                                                            } else {
                                                                echo '<p>Error: ' . $retorno . '</p>';
                                                            }
                                                            ?>

                                                        </div>
                                                        <div class="col-md-6">
                                                            <img src="img/loading.gif" id="gifloading" alt="loading">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="py-2">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h2 class="">Consola</h2>
                                                            <textarea readonly id="laconsola" name="laconsola" class="form-control textoconsola mb-1" rows="15"></textarea>
                                                        </div>
                                                    </div>
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

                    <!-- Footer -->
                    <!-- End of Footer -->
                </div>
                <!-- End of Content Wrapper -->
            </div>
            <!-- End of Page Wrapper -->

            <script src="js/spigot.js"></script>

        </div>
    <?php
        //FINAL VALIDAR SESSION
    } else {
        header("location:index.php");
    }
    ?>

</body>

</html>