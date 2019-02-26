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
				$query = 'INSERT INTO candidaturas ("id_user","lista","email","nombre","motivaciones","yt","rs") values ('.$user_id.',1,"'.filtro($_POST['email_lista']).'","'.filtro($_POST['nombre_lista']).'","'.filtro($_POST['motivaciones']).'","'.filtro($_POST['yt']).'","'.filtro($_POST['rs']).'");';
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
	<p><input type="button" onClick="javascript:window.location='form-candidaturas.php?type=1'" value="Individual"> <input type="button" onClick="javascript:window.location='form-candidaturas.php?type=2'" value="Lista"></p>
<?php }else{ ?>
	<?php echo $s_error; ?>
	<form id="form-candidaturas" name="form-candidaturas" action="#" method="POST" enctype="multipart/form-data">
<?php if($type == 2){ ?>
	<p>Tus datos de inscrito en participa.masmadrid.org:</p>
<?php } ?>
	<p><b>Nombre y apellidos*:</b> <input id="nombre" name="nombre" required="required" autofocus="autofocus" type="text" value="<?php echo @$_POST['nombre']?>" /></p>
	<p><b>Documento*:</b> <select name="doc_type" id="doc_type" required="required">
			<option value="">-</option>
			<option value="1">DNI</option>
			<option value="2">NIE</option>
			<option value="3">Pasaporte</option>
		</select> <input id="doc" name="doc" required="required" type="text" value="<?php echo @$_POST['doc']?>" />
	</p>
	<p><b>Correo Electrónico*:</b> <input id="email" name="email" required="required" type="text" value="<?php echo @$_POST['email']?>" /></p>
<?php if($type == 2){ ?>
	<p>Los datos de la lista:</p>
	<p><b>Nombre*:</b> <input id="nombre_lista" name="nombre_lista" required="required" autofocus="autofocus" type="text" value="<?php echo @$_POST['nombre_lista']?>" /></p>
	<p><b>Correo Electrónico*:</b> <input id="email_lista" name="email_lista" required="required" type="text" value="<?php echo @$_POST['email_lista']?>" /></p>
<?php } ?>
	<p><b>Teléfono*:</b> <input id="telefono" name="telefono" required="required" type="text" value="<?php echo @$_POST['telefono']?>" /></p>
	<p><b>Foto*:</b> <input type="file" required="required" accept=".gif,.jpg,.jpeg,.png" id="foto" name="foto"></p>
<?php if($type == 1){ ?>
	<p><b>Circunscripción*:</b> <select name="circunscripcion" id="circunscripcion" required="required">
			<option value="">-</option>
			<option value="1">Ayuntamiento de Madrid</option>
			<option value="2">Comunidad de Madrid</option>
	</select></p>
	<p><b>Tipo de candidatura*:</b> <select name="tipo" id="tipo" required="required">
			<option value="">-</option>
			<option value="1">Lista con responsabilidades de gobierno (sólo personas que conformen equipos con candidat@ y programa)</option>
			<option value="2">Lista sin responsabilidades de gobierno</option>
	</select></p>
	<p><b>Biografía*:</b></p>
	<p><textarea id="bio" name="bio" required="required" cols="60" rows="6" maxlength="1000"><?php echo @$_POST['bio']?></textarea></p>
<?php } ?>
	<p><b>Motivaciones*:</b></p>
	<p><textarea id="motivaciones" name="motivaciones" required="required" cols="60" rows="6" maxlength="1000"><?php echo @$_POST['motivaciones']?></textarea></p>
	<p><b>Enlace a vídeo de Youtube:</b> <input id="yt" name="yt" type="text" value="<?php echo @$_POST['yt']?>" /></p>
	<p><b>Redes sociales:</b> <input id="rs" name="rs" type="text" value="<?php echo @$_POST['rs']?>" /></p>
	
<?php if($type == 2){ ?>
	<p><b>PDF:</b> <input type="file" required="required" accept=".pdf" id="pdf" name="pdf"></p>
	
	<?php for($i=1;$i<41;$i++){?>
		<p><b>Candidato <?php echo $i; ?></b></p>
		<p>Nombre y apellidos: <input type="text" id="nombre_<?php echo $i; ?>" name="nombre_<?php echo $i; ?>" value="<?php echo @$_POST['nombre_'.$i]?>" /></p>
		<p>Documento: <select name="doc_type_<?php echo $i; ?>" id="doc_type_<?php echo $i; ?>">
			<option value="">-</option>
			<option value="1">DNI</option>
			<option value="2">NIE</option>
			<option value="3">Pasaporte</option>
		</select> <input id="doc_<?php echo $i; ?>" name="doc_<?php echo $i; ?>" type="text" value="<?php echo @$_POST['doc_'.$i]?>" />
	</p>
	<input type="hidden" id="lista" name="lista" value="si" />
<?php } } ?>

	<p><input type="checkbox" name="cod_etico" value="cod_etico" id="cod_etico" required="required" /><label for="cod_etico"> Acepta el código ético</label></p>
	<p><input type="checkbox" name="cod_carta" value="cod_carta" id="cod_carta" required="required" /><label for="cod_carta"> Acepta la carta financiera</label></p>
	<?php echo $s_error; ?>
	<p><input type="submit" value="Enviar"></p>
	</form>
<?php } ?>




