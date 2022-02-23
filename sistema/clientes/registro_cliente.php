<?php
	session_start();
	include "../../conexion.php";

	$nit 		= '';
	$nombre 	= '';
	$telefono 	= '';
	$correo  	= '';
	$direccion  = '';
	$alert 		= '';
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Registro Cliente</title>
</head>
<?php 
if(!empty($_POST))
{
	$nit 		= strClean($_POST['nit']);
	$nombre 	= ucwords(strClean($_POST['nombre']));
	$telefono 	= intval($_POST['telefono']);
	$correo  	= strtolower(strClean($_POST['correo']));
	$direccion  = strClean($_POST['direccion']);
	$usuario_id = intval($_SESSION['idUser']);

	if(empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion']))
	{
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
	}else{
		$result = 0;
		$nit 	= $_POST['nit'];
		$query  = mysqli_query($conection,"SELECT * FROM cliente WHERE nit = '$nit' OR correo = '$correo' ");
		$result = mysqli_fetch_array($query);

		if($result > 0){
			$alert='<p class="msg_error">El número de NIT ya existe o el email, ingrese otro.</p>';
		}else{
			$query_insert = mysqli_query($conection,"INSERT INTO cliente(nit,nombre,telefono,correo,direccion,usuario_id)
													VALUES('$nit','$nombre','$telefono','$correo','$direccion','$usuario_id')");

			if($query_insert){
				$alert='<p class="msg_save">Cliente guardado correctamente.</p>';
				$nit 		= '';
				$nombre 	= '';
				$telefono 	= '';
				$correo  	= '';
				$direccion  = '';
				$usuario_id = '';
			}else{
				$alert='<p class="msg_error">Error al guardar el cliente.</p>';
			}
		}
	}
}
 ?>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">

		<div class="form_register">
			<h1><i class="fas fa-user-plus"></i> Registro cliente</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post">
				<div>
					<p>Los campos con (*) son obligatorios.</p>
				</div>
				<label for="nit"><th><?= strtoupper(IDENTIFICACION_TRIBUTARIA); ?></th> (*)</label>
				<input type="text" name="nit" id="nit" placeholder="Identificación tributaria" value="<?= $nit;  ?>" required>
				<label for="nombre">Nombre (*)</label>
				<input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value="<?= $nombre;  ?>" required>
				<label for="telefono">Teléfono (*)</label>
				<input type="number" name="telefono" id="telefono" placeholder="Teléfono" value="<?= $telefono;  ?>" required>
				<label for="telefono">Correo electrónico</label>
				<input type="email" name="correo" id="correo" placeholder="Correo electrónico" value="<?= $correo;  ?>" >
				<label for="direccion">Dirección (*)</label>
				<input type="text" name="direccion" id="direccion" placeholder="Dirección completa" value="<?= $direccion;  ?>" required>
				<button type="submit" class="btn_save"><i class="far fa-save fa-lg"></i> Guardar Cliente</button>
			</form>
		</div>

	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>