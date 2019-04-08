<?php
session_start(); // Starting Session

//if(!isset($_SESSION['login_userID'])){header("location: index.php");}

//include "../php/connection.php";
include_once "config.php";
include_once "functions.php";
$script="index.php";

if (!isset($_SESSION['action'])) {
	if (isset($_POST['action'])) {
		if ($_POST['action']=="LOGIN") {
			// Check User Login
			if (!checkUser($_POST['user'],$_POST['password']))
				die("Usuario incorrecto");
			//session_start();
			$_SESSION['user']=$_POST['user'];
			$_SESSION['action']="PROCESO";
			//$_SESSION['tag']=$tag;
			header("Location: ".$script);
			die();
		}
	} else {
		if (!isset($_SESSION['action'])) {
			// Hacemos login
			include "login.php";
			die();
		}
	}
}

if (isset($_GET['logout'])) {
	session_destroy();
	header("Location: ".$script);
	die();
}



include "vota.view.php";
