<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Formulario Candidaturas</title>
	<link rel="stylesheet" href="/style.css">
</head>
<body>
<?php


$dir = 'sqlite:/path/db.sqlite3';
$dir2 = 'sqlite:/path/db2.sqlite3';
$save_dir = '/path/';


$continue = 0;
if(isset($_SERVER['HTTP_REFERER'])){
	$ar=parse_url($_SERVER['HTTP_REFERER']);
	if(strpos($ar['host'], 'masmadrid.org') === false){
	}else{
		$continue = 1;
	}
}
if($continue == 0){
	header('HTTP/1.0 403 Forbidden');
	header('Location: /');
	die('Forbidden');
}


$b_guadado = false;
$s_error = '';
if(@$_GET['type'] == ''){
	$type = 0;
}else{
	$type = $_GET['type'];
	if(@$_POST['nombre'] != '' && @$_POST['email'] != ''){
		$b_encontrado = false;
		$db = new PDO($dir) or die('cannot open the database');
		$query = 'SELECT id FROM users WHERE email like "'.filtro($_POST['email']).'" AND document_vatid like "'.filtro($_POST['doc']).'" LIMIT 1;';
		foreach ($db->query($query) as $row){
			$b_encontrado = true;
			$user_id = $row['id'];
		}
		
		$db = null;
		$s_error = '';
		if($b_encontrado){
			$db = new PDO($dir2) or die('cannot open the database');
			if(@$_POST['lista'] == 'si'){
				$query = 'INSERT INTO candidaturas ("id_user","lista","tipo","email","nombre","motivaciones","yt","rs") values ('.$user_id.',1,'.filtro($_POST['tipo']).',"'.filtro($_POST['email_lista']).'","'.filtro($_POST['nombre_lista']).'","'.filtro($_POST['motivaciones']).'","'.filtro($_POST['yt']).'","'.filtro($_POST['rs']).'");';
			}else{
				$query = 'INSERT INTO candidaturas ("id_user","lista","circunscripcion","tipo","email","telefono","nombre","doc_type","doc","bio","motivaciones","yt","rs") values ('.$user_id.',0,'.filtro($_POST['circunscripcion']).','.filtro($_POST['tipo']).',"'.filtro($_POST['email']).'","'.filtro($_POST['telefono']).'","'.filtro($_POST['nombre']).'",'.filtro($_POST['doc_type']).',"'.filtro($_POST['doc']).'","'.filtro($_POST['bio']).'","'.filtro($_POST['motivaciones']).'","'.filtro($_POST['yt']).'","'.filtro($_POST['rs']).'");';
			}
			if($db->exec($query)){
				$id_lista = $db->lastInsertId();
				if($_FILES['foto']['error'] == 0){
					$info = pathinfo($_FILES['foto']['name']);
					$ext = $info['extension']; // get the extension of the file
					$s_foto = $id_lista.'.'.$ext; 
					move_uploaded_file($_FILES['foto']['tmp_name'], $save_dir.'img/'.$s_foto);
					$query = 'UPDATE candidaturas SET foto = "'.$s_foto.'" WHERE id = '.$id_lista.';';
					if(!$db->exec($query)){
						$s_error = "<p style='color:red;'><b>ERROR</b>: Hubo problemas al mandar la foto.</p>";
					}
				}
				if(@$_POST['lista'] == 'si'){
					if(@$_FILES['pdf']['error'] == 0){
						$info = pathinfo($_FILES['pdf']['name']);
						$ext = $info['extension']; // get the extension of the file
						$s_pdf = $id_lista.'.'.$ext; 
						move_uploaded_file($_FILES['pdf']['tmp_name'], $save_dir.'pdf/'.$s_pdf);
						$query = 'UPDATE candidaturas SET pdf = "'.$s_pdf.'" WHERE id = '.$id_lista.';';
						if(!$db->exec($query)){
							$s_error = "<p style='color:red;'><b>ERROR</b>: Hubo problemas al mandar el pdf.</p>";
						}
					}
					for($i=1;$i<41;$i++){
						if($_POST['nombre_'.$i] != '' && $_POST['doc_'.$i] != '' ){
							$query = 'INSERT INTO candidaturas_lista ("id_candidatura","nombre","doc_type","doc") values ("'.$id_lista.'","'.filtro($_POST['nombre_'.$i]).'","'.filtro($_POST['doc_type_'.$i]).'","'.filtro($_POST['doc_'.$i]).'");';
							//die($query);
							if(!$db->exec($query)){
								$s_error = "<p style='color:red;'><b>ERROR</b>: Hubo problemas a añadir candidatos.</p>";
							}
						}
					}
				}
				$b_guadado = true;
			}else{
				$s_error = "<p style='color:red;'><b>ERROR</b>: Hubo problemas guardar la candidatura.</p>";
			}
		}else{
			$s_error = "<p style='color:red;'><b>ERROR</b>: Tienes que poner tu correo electrónico y tu documento de instrito.</p>";
		}
		$db = null;
	}
}


if($b_guadado == true){
	die("<h1>Se han guardado los datos</h1>".$s_error);
}


function filtro($str){
	return htmlentities(strip_tags(trim($str),''), ENT_COMPAT, "UTF-8");
}


if($type == 0){ ?>
	<p><input type="button" class="btn-candidaturas" onClick="javascript:window.location='form-candidaturas.php?type=1'" value="Individual"> <input type="button" class="btn-candidaturas" onClick="javascript:window.location='form-candidaturas.php?type=2'" value="Lista"></p>
<?php }else{ ?>
	<?php echo $s_error; ?>
			<form id="form-candidaturas" name="form-candidaturas" action="#" method="POST" enctype="multipart/form-data">
		<?php if($type == 2){ ?>
			<legend>Tus datos de inscrito en participa.masmadrid.org:</legend>
		<?php } ?>
			<div class="form-group"><label>Nombre y apellidos*:</label> <input id="nombre" name="nombre" required="required" autofocus="autofocus" type="text" value="<?php echo @$_POST['nombre']?>" /></div>
			<div class="form-group"><label>Documento*:</label> <select name="doc_type" id="doc_type" required="required">
					<option value="">-</option>
					<option value="1">DNI</option>
					<option value="2">NIE</option>
					<option value="3">Pasaporte</option>
				</select> <input id="doc" name="doc" required="required" type="text" value="<?php echo @$_POST['doc']?>" />
			</div>
			<div class="form-group"><label>Correo Electrónico*:</label> <input id="email" name="email" required="required" type="text" value="<?php echo @$_POST['email']?>" /></div>
		<?php if($type == 2){ ?>
			<legend>Los datos de la lista:</legend>
			<div class="form-group"><label>Nombre*:</label> <input id="nombre_lista" name="nombre_lista" required="required" autofocus="autofocus" type="text" value="<?php echo @$_POST['nombre_lista']?>" /></div>
			<div class="form-group"><label>Correo Electrónico*:</label> <input id="email_lista" name="email_lista" required="required" type="text" value="<?php echo @$_POST['email_lista']?>" /></div>
		<?php } ?>
			<div class="form-group"><label>Teléfono*:</label> <input id="telefono" name="telefono" required="required" type="text" value="<?php echo @$_POST['telefono']?>" /></div>
			<div class="form-group"><label>Foto*:</label> <input type="file" required="required" accept=".gif,.jpg,.jpeg,.png" id="foto" name="foto"></div>
			<div class="form-group"><label>Tipo de candidatura*:</label> <select name="tipo" id="tipo" required="required">
					<option value="">-</option>
					<option value="1">Lista con responsabilidades de gobierno (sólo personas que conformen equipos con candidat@ y programa)</option>
					<option value="2">Lista sin responsabilidades de gobierno</option>
			</select></div>
		<?php if($type == 1){ ?>
			<div class="form-group"><label>Circunscripción*:</label> <select name="circunscripcion" id="circunscripcion" required="required">
					<option value="">-</option>
					<option value="1">Ayuntamiento de Madrid</option>
					<option value="2">Comunidad de Madrid</option>
			</select></div>
			<div class="form-group"><label>Biografía*:</label></div>
			<div class="form-group"><textarea id="bio" name="bio" required="required" cols="60" rows="6" maxlength="1000"><?php echo @$_POST['bio']?></textarea></div>
		<?php } ?>
			<div class="form-group"><label>Motivaciones*:</label></div>
			<div class="form-group"><textarea id="motivaciones" name="motivaciones" required="required" cols="60" rows="6" maxlength="1000"><?php echo @$_POST['motivaciones']?></textarea></div>
			<div class="form-group"><label>Enlace a vídeo de Youtube:</label> <input id="yt" name="yt" type="text" value="<?php echo @$_POST['yt']?>" /></div>
			<div class="form-group"><label>Redes sociales:</label> <input id="rs" name="rs" type="text" value="<?php echo @$_POST['rs']?>" /></div>
			
		<?php if($type == 2){ ?>
			<div class="form-group"><label>PDF:</label> <input type="file" required="required" accept=".pdf" id="pdf" name="pdf"></div>
			
			<?php for($i=1;$i<41;$i++){?>
				<div class="form-group"><legend class="candidato">Candidato <?php echo $i; ?></legend></div>
				<div class="form-group"><label>Nombre y apellidos: </label><input type="text" id="nombre_<?php echo $i; ?>" name="nombre_<?php echo $i; ?>" value="<?php echo @$_POST['nombre_'.$i]?>" /></div>
				<div class="form-group"><label>Documento:</label> <select name="doc_type_<?php echo $i; ?>" id="doc_type_<?php echo $i; ?>">
					<option value="">-</option>
					<option value="1">DNI</option>
					<option value="2">NIE</option>
					<option value="3">Pasaporte</option>
				</select> <input id="doc_<?php echo $i; ?>" name="doc_<?php echo $i; ?>" type="text" value="<?php echo @$_POST['doc_'.$i]?>" />
			</div>
			<input type="hidden" id="lista" name="lista" value="si" />
		<?php } } ?>

			<div class="form-group"><input type="checkbox" name="cod_etico" value="cod_etico" id="cod_etico" required="required" /><label class="check" for="cod_etico"> <a href="https://assets.nationbuilder.com/masmadrid/pages/182/attachments/original/1551220335/C%C3%B3digo-%C3%A9tico_MM.pdf?1551220335" target="_blank">Acepta el código ético</a></label></div>
			<div class="form-group"><input type="checkbox" name="cod_carta" value="cod_carta" id="cod_carta" required="required" /><label class="check" for="cod_carta"> <a href="https://assets.nationbuilder.com/masmadrid/pages/182/attachments/original/1551220347/Carta-financiera_MM.pdf?1551220347" target="_blank">Acepta la carta financiera</a></label></div>
			<?php echo $s_error; ?>
			<div class="form-group"><input type="submit" value="Enviar"></div>
			</form>
		<?php } ?>
	</body>
</html>

