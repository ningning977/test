<?php
include('config.core.php');
include('functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();

$ID=$_POST['pid'];

require("addPicker.php");


?>