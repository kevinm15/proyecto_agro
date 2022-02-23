<?php
	session_start();
	if(!empty($_SESSION['active']))
	{
		header('location: ../sistema/dashboard.php');
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Login | Sistema Facturación</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<div class="loading" style="display: none;"><img src="../sistema/img/loading.gif" alt="Loading"></div>
	<div class="modalLogin" style="display: none;">
		<div class="bodyModalLogin">
			<div class="closeModal">X</div>
			<div>
				<div>
					<span class="titleModalLogin">Recuperar cuenta</span>
				</div>
				<div>
					<p>Ingresa tu correo electrónico y se te enviara tus datos de acceso.</p>
					<br>
					<input type="email" id="txtEmail" name="txtEmail" placeholder="Correo electrónico" required>
				</div>
				<div class="alertSolicitud" style="display: none;"></div><br>
				<div>
					<button type="button" id="btnRecoveryPass" class="btnActionForm">Solicitar accesos</button>
				</div>
			</div>
		</div>
	</div>
	<section id="container">
		<form id="formLogin" action="" method="post">
			<h1>L o g i n</h1>
			<img src="../sistema/img/user.png" alt="Login">

			<input type="text" id="usuario" name="usuario" placeholder="Usuario" required >
			<input type="password" id="clave" name="clave" placeholder="Contraseña" required >
			<div class="alert"></div>
			<input type="submit" value="Iniciar sesión">
			<div class="divRecoveryPass">
				<a href="#" id="linkRecoveryPass">Recuperar cuenta</a>
			</div>
		</form>
	</section>
	<script src="../sistema/js/jquery.min.js"></script>
	<script src="js/functions.js"></script>

</body>
</html>