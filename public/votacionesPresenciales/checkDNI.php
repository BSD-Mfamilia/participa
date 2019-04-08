<?php

session_start(); // Starting Session
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
}

//$_POST['dni']=$_GET['dni'];

include_once "config.php";
include_once "functions.php";

if (!isset($_POST['dni'])) {
    $data['code']="500";
    $data['nombre']="Sin DNI";
    $data['apellidos']="";
    echo json_encode($data);
    die();
}


try {
//    $file_db = new PDO('sqlite:/var/www/vhosts/participa.masmadrid.org/participa/db/development.sqlite3');
    $file_db = new PDO('sqlite:/var/www/vhosts/participa.masmadrid.org/participa/db/production.sqlite3');
} catch(PDOException $e) {
    //echo $e->getMessage();
    $data['code']="500";
    $data['nombre']="DBError Open:".$e->getMessage();
    $data['apellidos']="";
    echo json_encode($data);
    die();
}


// $result = $file_db->query('SELECT * FROM users');
// foreach ($result as $result) {
//     print $result['id'];
// }

// $db->exec(
// "CREATE TABLE IF NOT EXISTS myTable (
//     id INTEGER PRIMARY KEY,
//     title TEXT,
//     value TEXT)"
// );



$data['code']="404";
$data['nombre']="NIF/NIE incorrecto";
$data['apellidos']="";

$dni=strtoupper($_POST['dni']);

if (valida_dato(substr($dni,0,9))) {
    // nif ok
    // if ($_POST['dni'] == "12345678A") {
    //     $data['code']="200";
    //     $data['nombre']="JuanMa NIF";
    //     $data['apellidos']="González";
    // }
    try {
        $result = $file_db->query('SELECT * FROM users where document_vatid="'.$dni.'"');
    } catch(PDOException $e) {
        $data['code']="500";
        $data['nombre']="DBError Search:".$e->getMessage();
        $data['apellidos']="";
        echo json_encode($data);
        die();
    }
    // Solo puede haber uno
    foreach ($result as $result) {
        $data['code']="200";
        $data['nombre']=$result['first_name'];
        $data['apellidos']=$result['last_name'];
        $data['user_id']=$result['id'];
    }
    // foreach ($result as $result) {
    //     print $result['document_vatid']."\n";
    // }
// comprobar si ha votado
    try {
        $file_db1 = new PDO('sqlite:votaciones.sqlite3');
    } catch(PDOException $e) {
        //echo $e->getMessage();
        $data['code']="500";
        $data['nombre']="DBError Open (votaciones):".$e->getMessage();
        $data['apellidos']=" No se puede establecer si ya ha votado";
        echo json_encode($data);
        die();
    }
    try {
        $result2 = $file_db1->query('SELECT * FROM users_validated where dni="'.$dni.'"');
    } catch(PDOException $e) {
        $data['code']="500";
        $data['nombre']="DBError Search:".$e->getMessage();
        $data['apellidos']=" No se puede establecer si ya ha votado";
        echo json_encode($data);
        die();
    }
    foreach ($result2 as $result2) {
        $data['vota']=$result2['vota'];
		$data['valida']=$result2['valida'];
    }





} // Valida DNI






echo json_encode($data);
?>
