<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<link rel="shortcut icon" href="favicon-mm-84b908510880e93ccc387ac8cc0bd53f2adecd0af030ed892668344a7b3d6391.ico" type="image/png">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="vota.view.css">
<script type="text/javascript">
function otro() {    
    document.getElementById("vota").innerHTML=""
    document.getElementById("validar").innerHTML=""
    location.href =window.location.pathname;
}


    function funcion(){
        //document.getElementById("unassignObjectTitle").innerHTML=title;
		document.getElementById("nombre").innerHTML="";
        dni=document.getElementById("DNI").value;
        //alert(dni);
        $.ajax({
        		type: "POST",
        		url: 'checkDNI.php',
        		data: {"dni": dni},
        		success: function(data){
                    //alert(data);
                    formValues=JSON.parse(data);
                    //alert("datos:"+formValues.code);
                       if (formValues.code != "200") {
                           // Delete data
                           //alert(formValues.code);
                           document.getElementById("nombre").innerHTML=document.getElementById("nombre").innerHTML="<span class='label label-danger'>"+formValues.nombre+"</span>";
                           return;
                       }
                       document.getElementById("nombre").innerHTML="<span class='label label-success'>"+formValues.nombre+" "+formValues.apellidos+"</span>";
                       //document.getElementById("apellidos").innerHTML="<span class='label label-success'>"+formValues.apellidos+"</span>";
                       document.getElementById("vota").innerHTML="<a href='#' class='btn btn-primary btn-sm' onclick='vota();'><span class='glyphicon glyphicon-envelope'></span>&nbsp;Vota</a>&nbsp;&nbsp;&nbsp;"
                        if (formValues.vota=="1") {
                            document.getElementById("vota").innerHTML="<a href='#' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-envelope'></span>&nbsp;Ya ha votado</a>&nbsp;&nbsp;&nbsp;"
                        }
                       document.getElementById("validar").innerHTML="<a href='#' class='btn btn-warning btn-sm' onclick='valida();'><span class='glyphicon glyphicon-ok'></span>&nbsp;Validar DNI</a>"
                       if (formValues.valida=="1") {
                           document.getElementById("validar").innerHTML="<a href='#' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-ok'></span>&nbsp;Ya ha sido validado</a>"
                       }
        		}
    	});
    }
function vota() {
    dni=document.getElementById("DNI").value;
    //alert(dni);
    $.ajax({
            type: "POST",
            url: 'votaDNI.php',
            data: {"dni": dni},
            success: function(data){
                //alert(data);
                formValues=JSON.parse(data);
                   if (formValues.code != "200") {
                       alert("Error vota");
                       document.getElementById("vota").innerHTML="<a href='#' class='btn btn-primary btn-sm' onclick='vota();'><span class='glyphicon glyphicon-envelope'></span>&nbsp;Vota</a>&nbsp;&nbsp;&nbsp;"
                       return;
                   }
                   document.getElementById("vota").innerHTML="<a href='#' class='btn btn-dafault btn-sm''><span class='glyphicon glyphicon-envelope'></span>&nbsp;Vota</a>&nbsp;&nbsp;&nbsp;"
            }
    });
}
function valida() {
    dni=document.getElementById("DNI").value;
    //alert(dni);
    $.ajax({
            type: "POST",
            url: 'validaDNI.php',
            data: {"dni": dni},
            success: function(data){
                //alert(data);
                formValues=JSON.parse(data);
                   if (formValues.code != "200") {
                       alert("Error validando");
                       document.getElementById("validar").innerHTML="<a href='#' class='btn btn-warning btn-sm' onclick='valida();'><span class='glyphicon glyphicon-ok'></span>&nbsp;Validar</a>"
                       return;
                   }
                   document.getElementById("validar").innerHTML="<a href='#' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-ok'></span>&nbsp;Validar DNI</a>"
            }
    });
}
</script>

</head>
<body>
<div class="content-content cols">
	<div class="row">
		<div style="background-color:#004732">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<img width="256" alt="mas madrid" src="logo-masmadrid.svg" />
		</div>
	</div>
<br />

<div class="col">
    <div id="wrap">
        <div class="container">
            <table border="0">
                <tr>
                    <td><h1>Votaciones presenciales</h1></td>
                    <td width="10%" align="right"><a href="<?php echo $script; ?>?logout" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-log-out"></span> Finalizar sesi&oacute;n</a></td>
                </tr>
            </table>
            <br />
            <div class="row">
                <div id="admin">
                  <form name="DNIform" id="DNIform" method="POST" onkeypress='return event.keyCode != 13;'>
                      <h4><input type="text" name="DNI" id="DNI" autofocus maxlength="15" size="40" placeholder="NIF:(99999999A) / NIE:(A9999999A)" required></h4>
                      <!-- pattern="[0-9]{8,10}[A-Za-z]$" -->
                      <a href="#" class="btn btn-info btn-lg" onclick='funcion();'>
                        <span class="glyphicon glyphicon-play"></span>&nbsp;Comprobar DNI
                      </a>
                  </form>
              </div>
        </div>
        <div class="row">
            <!-- Datos obtenidos -->
            <table border="0">
                <tr>
                    <td align="right"><h2><span class="label label-default">Nombre</span></h2></td>
                    <td><h3><p id="nombre"><span class="label label-success"></span></p></h3></td>
                    <td width="20px">&nbsp;</td>
                    <td align="right"><h2><span id="vota"></span></h2></td>
                    <td width="20px">&nbsp;</td>
                    <td ><h2><p id="validar"></p></h2></td>
                </tr>
                <tr>
                    <td align="right"><br /></td>
                </tr>
                <tr>
                    <td><a href='#' class='btn btn-default btn-lg' onclick='otro()'><span class='glyphicon glyphicon-refresh'></span>&nbsp;Otro votante</a></td>
                </tr>
            </table>
        </div>
    </div>
	</div>
</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
</body>
</html>
