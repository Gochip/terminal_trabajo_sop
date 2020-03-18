<?php 
$dir_actual = "/";
if (isset($_COOKIE["dir-actual"])) {
    $dir_actual = htmlspecialchars($_COOKIE["dir-actual"]);
    if (!isset($dir_actual)) {
        $dir_actual = "/";
    }
}
setcookie("dir-actual", $dir_actual);
?>
<html>
<head>
    <title>Terminal de trabajo</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/2.9.3/introjs.min.css">
    <link rel="stylesheet" href="intro.js-2.9.3/themes/introjs-dark.css">

    <style type="text/css">
        .barra-superior {
            position: fixed;
            color:white;
            top: 0px;
            width: 100vw;
            padding-left: 25px;
            padding-right: 25px;
            height: 50px;
            z-index: 99;
            background-color: #1d1e1f
        }
        .barra-superior .indicador{
            margin-right: 25px;
            line-height: 50px;
            display: inline-block;
            vertical-align: middle
        }
        
        .texto-consola {
            color: white;
            font-family: "Courier New", Courier, monospace;
        }
        
        .txt-comando-a-enviar {
            height: 30px;
            margin-left: 5px;
        }
        
        /* https://stackoverflow.com/questions/6831482/contenteditable-single-line-input */
        [contenteditable="true"].single-line {
            white-space: nowrap;
            width: 900px;
            max-width: 100%;
            overflow: hidden;
        } 
        [contenteditable="true"].single-line br {
            display:none;

        }
        [contenteditable="true"].single-line * {
            display:inline;
            white-space:nowrap;
        }
        [contenteditable="true"].single-line:focus {
            outline: none;
        }
        .introjs-skipbutton {
            color: black!important;
        }
        .introjs-button {
            text-shadow: none;
        }
        .introjs-prevbutton {
            color: black!important;
            text-shadow: none;
        }
        .introjs-nextbutton {
            color: black!important;
            text-shadow: none;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/2.9.3/intro.min.js"></script>

    <script src="https://pabex.com.ar/assets/js/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            // Variables.
            var $modalInicio = $("#modal-inicio");
            var $txtComandoAEnviar = $("#txt-comando-a-enviar");
            var $divConsola = $("#div-consola");
        
            // Eventos.
            
            $divConsola.click(function(ev) {
                console.log(ev);
                if (ev.toElement.id === "div-consola") {
                    $("#txt-comando-a-enviar").focus();
                }
            });
            
            $(document).on("keypress", "#txt-comando-a-enviar", function() {
                if (event.keyCode === 13) {
                    var comando = $(this).text();
                    if (comando.length > 0) {
                        if (comando === "clear") {
                            var $shell = $(".shell:first").clone();
                            var $txtComandoAEnviar = $(".txt-comando-a-enviar:first").clone();
                            $divConsola.empty();
                            $txtComandoAEnviar.text("");
                            $divConsola.append($shell);
                            $txtComandoAEnviar.attr("contenteditable", true);
                            $txtComandoAEnviar.attr("id", "txt-comando-a-enviar");
                            $divConsola.append($txtComandoAEnviar);
                            $txtComandoAEnviar.focus();
                        } else {
                            $.ajax({
                                url: "ejecutar_comando.php",
                                type: "post",
                                data: {
                                    comando: comando
                                },
                                success: function (event) {
                                    var $p = $("<p></p>");
                                    $p.addClass("texto-consola");
                                    $p.html(event);
                                    $divConsola.append($p);
                                    comandoEjecutado();
                                }
                            });
                        }
                        $(this).val("");
                    } else {
                        var $p = $("<p></p>");
                        $p.css({
                            color: "white",
                            "margin-top": "10px",
                            "font-family": '"Courier New", Courier, monospace'
                        });
                        $p.html("&nbsp;<br/>");
                        $divConsola.append($p);
                        comandoEjecutado();
                    }
                }
            });
            
            $("#btn-ejercicios").click(function() {
                $("#modal-ejercicios").modal("show");
            });
            
            $("#btn-aceptar-ejercicios").click(function() {
                $("#modal-ejercicios").modal("hide");
                $txtComandoAEnviar.focus();
            });
            
            $("#btn-cerrar-sesion").click(function() {
                $.ajax({
                    url: "cerrar_sesion.php",
                    type: "get",
                    data: {},
                    success: function (event) {
                        location.href = "";
                    }
                });
            });
            
            $("#btn-aceptar-bienvenida").click(function() {
                $("#modal-bienvenida").modal("hide");
                empezarTutorialInteractivo();
            });
            
            $("#btn-confirmar-inicio").click(function() {
                var nombre = $("#txt-nombre").val();
                if (nombre.length > 3) {
                    $.ajax({
                        url: "crear_contenedor.php",
                        type: "post",
                        data: {
                            nombre: nombre
                        },
                        success: function (event) {
                            $modalInicio.modal("hide");
                            $("#modal-bienvenida").modal("show");
                            setNombreUsuario();
                            setIdContenedor();
                            setDirActual();
                        }
                    });
                } else {
                    alert("Complete su nombre para poder confirmar");
                }
            });
            
            
            
            // Funciones.
            function empezarTutorialInteractivo() {
                var intro = introJs();
                intro.setOptions({
                    prevLabel: "Volver",
                    nextLabel: "Siguiente",
                    skipLabel: "Salir",
                    doneLabel: "Listo"
                });
                intro.start();
            }
            
            function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for(var i = 0; i <ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return null;
            }
            
            function setNombreUsuario() {
                var nombre = getCookie("nombre");
                $(".nombre-usuario").text(nombre);
            }
            
            function setIdContenedor() {
                var idContenedor = getCookie("id-contenedor");
                idContenedor = idContenedor.substring(0, 12);
                $(".id-contenedor").text(idContenedor);
            }
            
            function setDirActual() {
                var dirActual = getCookie("dir-actual");
                $("#dir-actual").text(dirActual);
            }
            
            function comandoEjecutado() {
                var $shell = $(".shell.clone");
                var $cloneShell = $shell.clone();
                $cloneShell.removeClass("clone");
                $cloneShell.removeClass("no-limpiar");
                $(".dir-actual").removeAttr("id");
                $cloneShell.find(".dir-actual").attr("id", "dir-actual");
                $(".txt-comando-a-enviar").removeAttr("id");
                $(".txt-comando-a-enviar").attr("contenteditable", false);
                var $cloneTxtComandoAEnviar = $txtComandoAEnviar.clone();
                $cloneTxtComandoAEnviar.empty();
                $cloneTxtComandoAEnviar.attr("id", "txt-comando-a-enviar");
                $cloneTxtComandoAEnviar.attr("contenteditable", true);
                $divConsola.append($cloneShell);
                $divConsola.append($cloneTxtComandoAEnviar);
                $cloneTxtComandoAEnviar.focus()
                window.scrollTo(0, document.body.scrollHeight);
                setDirActual();
            }
            
            // Setup.
            var idContenedor = getCookie("id-contenedor");
            if (idContenedor === null) {
                $modalInicio.modal("show");
            } else {
                setNombreUsuario();
                setIdContenedor();
                setDirActual();
                $("#txt-comando-a-enviar").focus();
                empezarTutorialInteractivo();
            }
            
            $(document).ajaxStart(function() {
                $("#div-cargando").removeClass("d-none");
            });
            
            $(document).ajaxStop(function() {
                $("#div-cargando").addClass("d-none");
            });
        });
        
    </script>
</head>
<body >
<div class="barra-superior">
    <h6 class="indicador" style="font-family: monospace">
        <span class="d-none d-sm-block">Terminal de trabajo | UTN - FRC | Sistemas Operativos</span>
    </h6>
    <button type="button" id="btn-cerrar-sesion" class="btn btn-danger" style="float: right; margin-top: 5px; margin-left: 5px">Cerrar sesión</button>
    <button type="button" id="btn-ejercicios" class="btn btn-primary" style="float: right; margin-top: 5px">Tutorial</button>
</div>
<div class="container-fluid">
    <div class="row">
        <div id="div-consola" class="col-sm-12" style="background-color: rgba(0,0,0,0.8); height: 90%; padding-top: 50px; padding-bottom: 38px; min-height: 100vh;">
            <div style="display: inline-block" class="shell clone">                
                <span class="texto-consola nombre-usuario" data-step="1" data-intro="Acá está el nombre de usuario.">gochi</span>
                <span class="texto-consola separador" data-step="3" data-intro="Esto es simplemente un separador.">@</span>
                <span class="texto-consola id-contenedor" data-step="2" data-intro="Esto es el nombre del host, es decir, la computadora a la que estás conectado.">sop</span>
                <span class="texto-consola separador">:</span>
                <span class="texto-consola dir-actual" id="dir-actual">/</span>
                <span class="texto-consola prompt">$</span>
            </div>
            <div class="texto-consola txt-comando-a-enviar single-line clone " id="txt-comando-a-enviar" contenteditable="true" style="display: inline-block; line-height: 30px; vertical-align: middle;"></div>
        </div>
    </div>
</div>

<div id="div-cargando" class="d-none" style="height: 100%; width: 100%; background-color: rgba(100,100,100,0.4); position: fixed; z-index: 2000; top: 0px; left: 0px">
    <div class="spinner-border text-light" style="left: 20px; position: absolute; top: 20px;"></div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal-inicio" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Bienvenido/a</h5>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="txt-nombre">Ingrese su nombre completo</label>
            <input type="text" class="form-control" id="txt-nombre" aria-describedby="nombre">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-confirmar-inicio">Confirmar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal-bienvenida" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Éxito</h5>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <p>
                Se te ha creado con éxito una sesión de trabajo para que practiques algunos comandos. En la barra superior tenés un botón para ver algunos ejercicios.
            </p>
            <p>
                Atención: Si no usas la sesión de trabajo durante 20 minutos, la misma se desactivará. Asimismo, cuando dejes de usarla hacé clic en "Cerrar sesión" así sabemos que ya terminaste de probar y podemos liberar ese espacio.
            </p>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-aceptar-bienvenida">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal-ejercicios" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tutorial</h5>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <p>En Linux, al igual que Windows, los archivos se organizan en carpetas, solo que aquí se le llaman directorios.</p>
            <p>Para listar los archivos se debe ejecutar el comando: <pre>ls</pre> esto significa que tienes que escribir ls y pulsar enter.</p>
            <p>¡Inténtalo ahora!</p>
            <p>Deberías haber visto una lista de archivos que podrían ser directorios.</p>
            <p>Ahora prueba: <pre>ls -l</pre></p>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-aceptar-ejercicios">Aceptar</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>
