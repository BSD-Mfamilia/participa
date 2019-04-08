<?php

// FUNCIONES
function callGET($_url,$_param,$decode=true)
{
	$param="";
	foreach ($_param as $key => $value) {
		if ($param=="") {
			$param=$param."?".$key."=".$value;
		} else {
			$param=$param."&".$key."=".$value;
		}
	}
	//echo "cURL:".$_url.$param.PHP_EOL;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $_url.$param);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	curl_close($ch);
	if ($decode){ return json_decode($data, true); } else { return $data; }
}

function callPUT($_url,$_param,$_body,$decode=true)
{
	$param="";
	foreach ($_param as $key => $value) {
		if ($param=="") {
			$param=$param."?".$key."=".$value;
		} else {
			$param=$param."&".$key."=".$value;
		}
	}
	echo "cURL:".$_url.$param.PHP_EOL;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $_url.$param);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $_body);
	$data = curl_exec($ch);
	curl_close($ch);
	if ($decode){ return json_decode($data, true); } else { return $data; }
}



function callDELETE($_url,$_param,$decode=true)
{
	$param="";
	foreach ($_param as $key => $value) {
		if ($param=="") {
			$param=$param."?".$key."=".$value;
		} else {
			$param=$param."&".$key."=".$value;
		}
	}
	//echo "cURL:".$_url.$param.PHP_EOL;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $_url.$param);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $_body);
	$data = curl_exec($ch);
	//var_dump($data);
	curl_close($ch);
	if ($decode){ return json_decode($data, true); } else { return $data; }
}



function checkUser ($_user,$_password) {
	if ($_user==="masMadrid" && $_password =="M4dr1dM4s!")
		return true;
	return false;
}

function generateTable($tag) {
	// Genera una tabla con los datos
	global $baseApiUrl, $access_token,$connect,$tagOK;
	$tagOK=$_SESSION['tagOK'];
	$limit=100;
	$params = array('limit' => $limit,'access_token' => $access_token);
	$response = callGET($baseApiUrl . '/api/v1/tags/'.$tag.'/people',$params);
	//echo "PRIMER RESPONSE------------------------------".PHP_EOL;
	//var_dump($response);

	if (isset($response['code'])) {
		die("ERROR: (". $response['code'].")".$response['message']);
	}
	$sql = "DELETE from c1MasMadrid.People WHERE tag='".$tag."'";
	$result=$connect->query($sql);

	//$listaPersonas=array();
	foreach ($response['results'] as $persona) {
		$infoPersona['id'] = $persona['id'];
		$infoPersona['nombre'] = $persona['first_name'];
		$infoPersona['apellido'] = $persona['last_name'];
		$infoPersona['foto'] = $persona['profile_image_url_ssl'];
		$infoPersona['email'] = $persona['email'];
		$infoPersona['twitter'] = $persona['twitter_name'];

		// Check if empty data
		if ($infoPersona['email'] == "" && $infoPersona['twitter'] == "") {
			// Get name
			$infoPersona['email'] = "(".$persona['first_name'].")";
			$infoPersona['twitter'] = "(".$persona['last_name'].")";
			//var_export($persona);
			//die();
		}

// No ponemos nombre/apellido/foto
		$infoPersona['nombre'] = "";
		$infoPersona['apellido'] = "";
		$infoPersona['foto'] = "";
		//$infoPersona['email'] = $persona['email'];
		// Tag no nos envía el twitter_login
		//@$infoPersona['twitter'] = $persona['twitter_login'];
		// Check if already assist
		$userTag=$persona['tags'];
		//var_dump($persona['tags']);
//die("tag:".$tagOK);
		$alreayAssist=0;
		foreach ($userTag as $miTag) {
			if ($miTag==$tagOK)
				$alreayAssist=1;
		}
		// Insert People
		$sql="INSERT INTO c1MasMadrid.People (id, id_persona, nombre, apellido, foto, email, twitter, tag, asiste) VALUES (NULL, ".$infoPersona['id'].", '".$infoPersona['nombre']."', '".$infoPersona['apellido']."', '".$infoPersona['foto']."', '".$infoPersona['email']."', '".$infoPersona['twitter']."', '".$tag."', '".$alreayAssist."' )";

		$result=$connect->query($sql);
		if (!$result) {
			//writeLog(1,$TGCLIname."/".__FUNCTION__."/insertGroup","SQL Error:".$config['SQLCONN']->errno);
			if ($connect->errno == 1062 ) {
				// Actualizamos
				$sql2="UPDATE c1MasMadrid.People SET email = '".$infoPersona['email']."', tag = '".$tag."', asiste = '".$alreayAssist."' WHERE People.id_persona = ".$infoPersona['id']."";
				$result=$connect->query($sql2);
				if (!$result) { die("Error updating People:".$connect->errno);}

			} else {die("Error inserting People:".$connect->errno);}
		}
	}

	if (@$response['next'] != "") { $hayData=true; } else { $hayData=false; }

	//$hayData=true;
	unset($params);
	$params=array();
	while ($hayData) {
		// Next data
		$nextData=parse_url($response['next']);
		$getParam=explode("&", $nextData['query']);
		foreach ($getParam as $data) {
			$total=explode("=",$data);
			$params[$total[0]]=$total[1];
		}
		$params['access_token'] = $access_token;
		//var_dump($params);
		//$response = $client->fetch($baseApiUrl . '/api/v1/tags/'.$tag.'/people',$params);
		//echo "SIGUIENTE RESPONSE------------------------------".PHP_EOL;
		$response=callGET($baseApiUrl . '/api/v1/tags/'.$tag.'/people',$params);

		//var_dump($response);

		foreach ($response['results'] as $persona) {
			$infoPersona['id'] = $persona['id'];
			$infoPersona['nombre'] = $persona['first_name'];
			$infoPersona['apellido'] = $persona['last_name'];
			$infoPersona['foto'] = $persona['profile_image_url_ssl'];
			$infoPersona['email'] = $persona['email'];
			$infoPersona['twitter'] = $persona['twitter_name'];
			// Check if already assist
			$userTag=$persona['tags'];
			$alreayAssist=0;
			foreach ($userTag as $miTag) {
				if ($miTag==$tagOK)
					$alreayAssist=1;
			}
			// Insert People
			$sql="INSERT INTO c1MasMadrid.People (id, id_persona, nombre, apellido, foto, email, twitter, tag, asiste) VALUES (NULL, ".$infoPersona['id'].", '".$infoPersona['nombre']."', '".$infoPersona['apellido']."', '".$infoPersona['foto']."', '".$infoPersona['email']."', '".$infoPersona['twitter']."', '".$tag."', '".$alreayAssist."' )";
			$result=$connect->query($sql);
			if (!$result) {
				//writeLog(1,$TGCLIname."/".__FUNCTION__."/insertGroup","SQL Error:".$config['SQLCONN']->errno);
				die("Error inserting People:".$connect->errno);
			}
		}
		if (@$response['next'] == "") { $hayData=false; }

	}

}


function DBconnect() {
global $config;
	$servername = "localhost";
	$username = "c1MasMadrid";
	$password = "masmadrid_1230";
	$database = "c1MasMadrid";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $database);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	else{
		return $conn;
	}
} // END function DBconnect


function valida_nif_cif_nie($cif) {
//Returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok, -1 = NIF bad, -2 = CIF bad, -3 = NIE bad, 0 = ??? bad
$cif = strtoupper($cif);
for ($i = 0; $i < 9; $i ++)
{
	$num[$i] = substr($cif, $i, 1);
}
//si no tiene un formato valido devuelve error
if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/', $cif)) {return 0;}
echo "formato valido\n";
//comprobacion de NIFs estandar
if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/', $cif)){
	if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 0, 8) % 23, 1)) {return 1;
} else {return -1;} }
//algoritmo para comprobacion de codigos tipo CIF
$suma = $num[2] + $num[4] + $num[6];
for ($i = 1; $i < 8; $i += 2) {
	$suma += substr((2 * $num[$i]),0,1) + substr((2 * $num[$i]), 1, 1);
}
$n = 10 - substr($suma,strlen($suma)-1,1);

//comprobacion de NIFs especiales (se calculan como CIFs o como NIFs)
if (preg_match('/^[KLM]{1}/', $cif)) {
	if ($num[8] == chr(64 + $n) || $num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 1, 8) % 23, 1)) { return 1;  } else { return -1; }
}
//comprobacion de CIFs
if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $cif))
{
	if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1)) { return 2; } else { return -2; }
}
//comprobacion de NIEs
if (preg_match('/^[XYZ]{1}/', $cif)) {
	if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X','Y','Z'), array('0','1','2'), $cif), 0, 8) % 23, 1)) { return 3; } else { return -3; }
}
//si todavia no se ha verificado devuelve error
return 0;
}


function valida_dato($dni) {
	//echo "DATO PASADO:".$dni."\n";
if (ctype_digit(substr($dni,0,1))) {
	// es NIF
	// echo "es nif\n";
	if (ctype_digit(substr($dni,0,8))) {
		// echo "los 8 son digitos\n";
		// echo "ultima:".substr($dni,8,1)."\n";
		if (ctype_alpha(substr($dni,8,1))) {
			// echo "el 1 es letra\n";
			return true;
		}
	}
} else {
	// es NIE
	// A9999999A
	if (ctype_digit(substr($dni,1,7))) {
		// echo "los 7 son digitos\n";
		// echo "ultima:".substr($dni,8,1)."\n";
		if (ctype_alpha(substr($dni,8,1))) {
			// echo "el 1 es letra\n";
			return true;
		}
	}
}
return false;
}
