<?php

session_start(); // Starting Session
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
}

if (isset($_GET['dni'])) {
    $_POST['dni']=$_GET['dni'];
}

include_once "config.php";
include_once "functions.php";

if (!isset($_POST['dni'])) {
    $data['code']="500";
    $data['msg']="Sin DNI";
    echo json_encode($data);
    die();
}

try {
    $file_db = new PDO('sqlite:votaciones.sqlite3');
} catch(PDOException $e) {
    $data['code']="500";
    $data['msg']="Error creando BBDD";
    echo json_encode($data);
    die();
}

$dni=strtoupper($_POST['dni']);

$sql="replace into users_validated(dni,valida) VALUES ('".$dni."',1)";

try {
    $result = $file_db->exec($sql);
} catch(PDOException $e) {
    $data['code']="500";
    $data['msg']="DBError Replace:".$e->getMessage();
    echo json_encode($data);
    die();
}

//var_dump($file_db->errorInfo());

$data['code']="200";
$data['msg']="OK";
echo json_encode($data);

// try {
//     $result = $file_db->query('SELECT * FROM users_validated');
// } catch(PDOException $e) {
//     die("Error");
// }
// // Solo puede haber uno
//
// foreach ($result as $result) {
//     print $result['dni'];
//     print $result['vota'];
// }
