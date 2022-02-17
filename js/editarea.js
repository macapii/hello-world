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

$(function () {

    document.getElementById("elcomando").disabled = true;

    if (document.getElementById('selectodashoras') !== null) {
        $("#selectodashoras").click(function () {
            for (var i = 0; i <= 23; i++) {
                checkhoras = document.getElementById('h' + i);
                checkhoras.checked = true;
            }
        });
    }

    if (document.getElementById('deselecionarhoras') !== null) {
        $("#deselecionarhoras").click(function () {
            for (var i = 0; i <= 23; i++) {
                checkhoras = document.getElementById('h' + i);
                checkhoras.checked = false;
            }
        });
    }

    if (document.getElementById('selectodosminutos') !== null) {
        $("#selectodosminutos").click(function () {
            for (var i = 0; i <= 59; i++) {
                checkminutos = document.getElementById('m' + i);
                checkminutos.checked = true;
            }
            if (document.getElementById('marcahoras') !== null) {
                $('#marcahoras').append('<option selected disabled hidden>Elige una opción</option>');
            }
        });
    }

    if (document.getElementById('deselecionarminutos') !== null) {
        $("#deselecionarminutos").click(function () {
            for (var i = 0; i <= 59; i++) {
                checkminutos = document.getElementById('m' + i);
                checkminutos.checked = false;
            }
            if (document.getElementById('marcahoras') !== null) {
                $('#marcahoras').append('<option selected disabled hidden>Elige una opción</option>');
            }
        });
    }

    if (document.getElementById('marcahoras') !== null) {
        $("#marcahoras").change(function () {
            marcado = parseInt(this.value);
            for (var i = 0; i <= 59; i++) {
                checkminutos = document.getElementById('m' + i);
                checkminutos.checked = false;
            }
            for (var i = 0; i <= 59; i = i + marcado) {
                checkminutos = document.getElementById('m' + i);
                checkminutos.checked = true;
            }
        });
    }

    if (document.getElementById("laaccion").value == "acc4") {
        document.getElementById("elcomando").disabled = false;
    } else {
        document.getElementById("elcomando").disabled = true;
        document.getElementById("elcomando").value = "";
    }


    $("#laaccion").change(function () {
        if (document.getElementById("laaccion").value == "acc4") {
            document.getElementById("elcomando").disabled = false;
        } else {
            document.getElementById("elcomando").disabled = true;
            document.getElementById("elcomando").value = "";
        }
    });

    $("#edittarea").click(function () {

        var eldata = $("#formtarea :input").serializeArray();
        $.post($("#formtarea").attr("action"), eldata, function (data) {

            if (data == "errnosession") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: No se ha encontrado la sessión.</div>";
            } else if (data == "errnombre") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: La tarea tiene que tener un nombre.</div>";
            } else if (data == "errlaaccion") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: La tarea tiene que tener una acción.</div>";
            } else if (data == "errmes") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: Tienes que seleccionar como mínimo un mes.</div>";
            } else if (data == "errsemana") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: Tienes que seleccionar como mínimo una semana.</div>";
            } else if (data == "errhora") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: Tienes que seleccionar como mínimo una hora.</div>";
            } else if (data == "errminuto") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: Tienes que seleccionar como mínimo un minuto.</div>";
            } else if (data == "errarchnoconfig") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: La carpeta config no existe.</div>";
            } else if (data == "errconfignoread") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: La carpeta config no tiene permisos de lectura .</div>";
            } else if (data == "errconfignowrite") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: La carpeta config no tiene permisos de escritura.</div>";
            } else if (data == "errjsonnoread") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: El archivo json no tiene permisos de lectura.</div>";
            } else if (data == "errjsonnowrite") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: Hay que introducir un comando.</div>";
            } else if (data == "nocomando") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: El archivo json no tiene permisos de escritura.</div>";
            } else if (data == "badchars") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: El comando tiene caracteres no válidos.</div>";
            } else if (data == "lenmax") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: El comando supera los 4096 caracteres.</div>";
            } else if (data == "nofilexist") {
                document.getElementById("textotarearetorno").innerHTML = "<div class='alert alert-danger' role='alert'>Error: No existe ninguna tarea para editar.</div>";
            } else if (data == "OK") {
                location.href = "tareas.php";
            }

        });
    });

    $("#cancelar").click(function () {
        location.href = "tareas.php";
    });

    $("#formtarea").submit(function () {

        return false;
    });

    function sessionTimer() {

        $.ajax({
            url: 'function/salirsession.php',
            data: {
                action: 'status'
            },
            type: 'POST',
            success: function (data) {
                if (data == "SALIR") {
                    location.href = "index.php";
                }


            }
        });
    }

    setInterval(sessionTimer, 1000);

});