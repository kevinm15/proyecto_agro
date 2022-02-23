<?php

	if(empty($_REQUEST['token']))
	{
		header('location: ./');
	}

	include_once "../conexion.php";
	$token = $_REQUEST['token'];
	$query_user = mysqli_query($conection,"SELECT * FROM usuario WHERE cod_temp = '$token'");
	$num_rows = mysqli_num_rows($query_user);
	if($num_rows > 0){
		$data = mysqli_fetch_assoc($query_user);
		$idUsuario = $data['idusuario'];
		$token = $data['cod_temp'];
	}else{
		header('location: ./');
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Reestablecer contraseña | Sistema Facturación</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<section id="container">
		<form id="formChangePassword" action="" method="post">
			<input type="hidden" id="id_us" name="id_us" value="<?php echo $idUsuario; ?>">
			<input type="hidden" id="token" name="token" value="<?php echo $token; ?>">
			<input type="hidden" id="action" name="action" value="updatePassRecovery">
			<h1>Cambiar contraseña</h1>
			<input type="password" id="pass1" name="pass1" placeholder="Nueva contraseña">
			<input type="password" id="pass2" name="pass2" placeholder="Confirmar contraseña">
			<div class="alert" style="display: none;"></div>
			<input type="submit" id="btnChangePass" value="Cambiar clave">
		</form>
	</section>
	<script src="../sistema/js/jquery.min.js"></script>
	<script src="js/functions.js"></script>
</body>
</html>