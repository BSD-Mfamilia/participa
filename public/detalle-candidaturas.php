<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Candidaturas Más Madrid</title>
	<link rel="stylesheet" href="/style.css?v=5">
</head>
<body>
<?php


$dir = 'sqlite:/var/www/vhosts/participa.masmadrid.org/candidaturas/db/candidaturas.sqlite3';
$save_dir = '/var/www/html/candidaturas/db';


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



$id = @$_GET['id'];

if(is_numeric($id)){
	$db = new PDO($dir) or die('cannot open the database');
	
	$query = 'SELECT * FROM candidaturas WHERE id = '.filtro($id).';';
	
	
	foreach ($db->query($query) as $row){
		if($row['foto'] == ''){
			$row['foto'] = 'anon.png';
		}
		$candidatura = $row;
	}
	
	$a_candidatos = array();
	//$query = 'SELECT * FROM candidaturas WHERE circunscripcion = '.$candidatura['circunscripcion'].' AND doc IN (SELECT doc FROM candidaturas_lista WHERE id_candidatura = '.filtro($id).');';
	$query = 'SELECT c.id,c.nombre,c.foto FROM candidaturas c INNER JOIN candidaturas_lista cl ON c.doc = cl.doc WHERE c.circunscripcion = '.$candidatura['circunscripcion'].' AND cl.id_candidatura = '.filtro($id).' ORDER BY cl.id ASC;';
	foreach ($db->query($query) as $row){
		if($row['foto'] == ''){
			$row['foto'] = 'anon.png';
		}
		$a_candidatos[] = $row;
	}
}


$db = null;

function filtro($str){
	return htmlentities(strip_tags(trim($str),''), ENT_COMPAT, "UTF-8");
}


if($id == ''){ 
	date_default_timezone_set('Europe/Madrid');
	$ahora = strtotime(date("d-m-Y H:i:s",time()));
	$fecha = strtotime("11-03-2019 20:00:00");
	if($ahora > $fecha){
		echo '<p><a class="btn-doc" href="https://participa.masmadrid.org/doc/MasMadrid-Programa-definitivo.pdf" target="_blank">Consulta las Lineas Estratégicas para el programa de Más Madrid</a></p>';
	}	
	?>

	<a href="" target="_blank">Consulta las Lineas Estratégicas para el programa de MásMadrid</a>
	<p><input type="button" class="btn-candidaturas" onClick="javascript:window.location='ver-candidaturas.php?circ=1'" value="Ayto. de Madrid"> <input type="button" class="btn-candidaturas" onClick="javascript:window.location='ver-candidaturas.php?circ=2'" value="Comunidad de Madrid"></p>
<?php }else{ ?>
	<div class="contenedor">
		<h2 class="title-candidato"><?php echo $candidatura['nombre']; ?></h2>
		<div class="pag-candidato">
			<div class="img-candidato">
				<img src="/files-candidaturas/img/<?php echo $candidatura['foto']; ?>" alt="candidato"/>
			</div>
			<div class="candidato-text">
				<section>
					<h2 class="title-lista">Biografía</h2>
					<p><?php echo nl2br($candidatura['bio']); ?></p>
				</section>
				<section>
					<h2 class="title-lista">Motivación</h2>
					<p><?php echo nl2br($candidatura['motivaciones']); ?></p>
				</section>
				<section><p>&nbsp;</p><p>
				<?php if($candidatura['pdf'] != '' || $candidatura['yt'] != '') { ?>
					<b>Enlaces:</b>
				<?php } ?>
				<?php if($candidatura['pdf'] != '') { ?>
					&nbsp; <a href="/files-candidaturas/pdf/<?php echo $candidatura['pdf']; ?>" target="_blank">Áreas temáticas municipales</a> 
				<?php } ?>
				<?php if($candidatura['yt'] != '') { ?>
					&nbsp; <a href="<?php echo $candidatura['yt']; ?>" target="_blank">Vídeo</a> 
				<?php } ?>
				</p>
				<?php if($candidatura['rs'] != '') { ?>
					<p><b>Redes sociales:</b> <?php echo $candidatura['rs']; ?></p>
				<?php } ?>
				</section>
			</div>
		</div>
	</div>
	
	<?php if(count($a_candidatos) >= 1){?>
	<div class="contenedor">
		<h2 class="title-lista"></h2>
		<div class="rowlista">
		<?php foreach($a_candidatos as $cand){?>
			<div class="candidato" id="candidato1">
				<a href="detalle-candidaturas.php?id=<?php echo $cand['id']; ?>">
					<div class="img-candidato">
						<img src="/files-candidaturas/img/<?php echo $cand['foto']; ?>" alt="candidato"/>
					</div>
					<div class="name-candidato">
						<span><?php echo $cand['nombre']; ?></span>
					</div>
				</a>
			</div>
		<?php } ?>
		</div>
	</div>
	<?php } ?>
	<p><input type="button" class="btn-candidaturas" onClick="javascript:window.history.back();" value="Volver"></p>
<?php } ?>
	</body>
</html>

