<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Candidaturas Más Madrid</title>
	<link rel="stylesheet" href="/style.css?v=3">
</head>
<body>
	<div class="contenedor">
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



$circ = @$_GET['circ'];

if($circ == 1 || $circ == 2){
	$db = new PDO($dir) or die('cannot open the database');
	
	$query = 'SELECT * FROM candidaturas WHERE circunscripcion = '.filtro($circ).' AND (lista = 1 OR doc NOT IN (SELECT doc FROM candidaturas_lista));';
	
	$a_candidatos = array();
	$a_candidatos['con'] = array();
	$a_candidatos['sin'] = array();
	$a_candidatos['no'] = array();
	foreach ($db->query($query) as $row){
		if($row['foto'] == ''){
			$row['foto'] = 'anon.png';
		}
		if($row['lista'] == '1'){//lista
			if($row['tipo'] == '1'){//con responsabilidad
				$a_candidatos['con'][] = $row;
			}else{//sin responsabilidad
				$a_candidatos['sin'][] = $row;
			}
		}else{//candidatos no agrupados
			$a_candidatos['no'][] = $row;
		}
		//echo "<h1>Ejemplo:</h1><pre>";print_r($row);die();
		//echo "<h1>Ejemplo:</h1><pre>";print_r($a_candidatos);die();
	}
	
	if($circ == 1){
		$s_titulo = 'Candidaturas para el Ayuntamiento de Madrid';
	}else{
		$s_titulo = 'Candidaturas para la Comunidad de Madrid';
	}
}


$db = null;

function filtro($str){
	return htmlentities(strip_tags(trim($str),''), ENT_COMPAT, "UTF-8");
}


if($circ == ''){ ?>
	<p><input type="button" class="btn-candidaturas" onClick="javascript:window.location='ver-candidaturas.php?circ=1'" value="Ayto. de Madrid"> <input type="button" class="btn-candidaturas" onClick="javascript:window.location='ver-candidaturas.php?circ=2'" value="Comunidad de Madrid"></p>
<?php }else{ ?>
	<h1><?php echo $s_titulo; ?></h1>
	
	<?php if(count($a_candidatos['con']) >= 1){?>
		<h2 class="title-lista">Candidaturas con responsabilidades de gobierno</h2>
		<div class="rowlista">
		<?php foreach($a_candidatos['con'] as $cand){?>
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
	<?php } ?>
	
	<?php if(count($a_candidatos['sin']) >= 1){?>
		<h2 class="title-lista">Candidaturas sin responsabilidades de gobierno</h2>
		<div class="rowlista">
		<?php foreach($a_candidatos['sin'] as $cand){?>
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
	<?php } ?>
	
	<?php if(count($a_candidatos['no']) >= 1){?>
		<h2 class="title-lista">Candidaturas no agrupadas</h2>
		<div class="rowlista">
		<?php foreach($a_candidatos['no'] as $cand){?>
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
	<?php } ?>
	<p><input type="button" class="btn-candidaturas" onClick="javascript:window.history.back();" value="Volver"></p>
<?php } ?>
	</body>
</html>

