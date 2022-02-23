<?php
	session_start();
	if($_SESSION['rol'] != 1)
	{
		header("location: ../index.php");
	}
	include "../../conexion.php";
	$dpi 	= '';
	$nombre = '';
	$telefono = '';
	$email  = '';
	$user   = '';
	$alert	='';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Registro Usuario</title>
</head>
<?php 
if(!empty($_POST))
{
	$dpi 	= strClean($_POST['dpi']);
	$nombre = ucwords(strClean($_POST['nombre']));
	$telefono = intval($_POST['telefono']);
	$email  = strtolower(strClean($_POST['correo']));
	$user   = strClean($_POST['usuario']);
	$clave  = md5($_POST['clave']);
	$rol    = intval($_POST['rol']);

	if(empty($_POST['dpi']) || empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['clave']) || empty($_POST['rol']))
	{
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
	}else{

		$query = mysqli_query($conection,"SELECT * FROM usuario WHERE usuario = '$user' OR correo = '$email' ");
		$result = mysqli_fetch_array($query);

		if($result > 0){
			$alert='<p class="msg_error">El correo o el usuario ya existe.</p>';
		}else{

			$query_insert = mysqli_query($conection,"INSERT INTO usuario(dpi,nombre,telefono,correo,usuario,clave,rol)
																VALUES($dpi,'$nombre',$telefono,'$email','$user','$clave','$rol')");
			if($query_insert){
				$alert  = '<p class="msg_save">Usuario creado correctamente.</p>';
				$dpi 	= '';
				$nombre = '';
				$telefono = '';
				$email  = '';
				$user   = '';
			}else{
				$alert='<p class="msg_error">Error al crear el usuario.</p>';
			}
		}
	}
}
 ?>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<div class="form_register">
			<h1><i class="fas fa-user-plus"></i> Registro de usuarios</h1>
			<a href="index.php" class="linkViewList" ><i class="far fa-list-alt"></i> Ver lista</a>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post" class="form">
				<div>
					<p>Datos del usuario, todos los campos son obligatorios.</p>
				</div>
				<div class="wd50">
					<label for="dpi"><?= IDENTIFICACION_CLIENTE ?></label>
					<input type="text" name="dpi" id="dpi" placeholder="Número de identificación" value="<?= $dpi;  ?>" required >
				</div>
				<div class="wd100">
					<label for="nombre">Nombre</label>
					<input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value="<?= $nombre; ?>" required >
				</div>
				<div class="wd50">
					<label for="telefono">Teléfono</label>
					<input type="text" name="telefono" id="telefono" placeholder="Número de teléfono" value="<?= $telefono;  ?>" required >
				</div>
				<div class="wd50">
					<label for="correo">Correo electrónico</label>
					<input type="email" name="correo" id="correo" placeholder="Correo electrónico" value="<?= $email; ?>" required >
				</div>
				<div class="wd50">
					<label for="usuario">Usuario</label>
					<input type="text" name="usuario" id="usuario" placeholder="Usuario" value="<?= $user;  ?>" required >
				</div>
				<div class="wd50">
					<label for="clave">Clave</label>
					<input type="password" name="clave" id="clave" placeholder="Clave de acceso" required >
				</div>
				<div class="wd100">
					<label for="rol">Tipo Usuario</label>
					<?php
						if($_SESSION['user'] == 'admin')
						{
							$query_rol = mysqli_query($conection,"SELECT * FROM rol");
						}else{
							$query_rol = mysqli_query($conection,"SELECT * FROM rol WHERE idrol != 1");
						}
						$result_rol = mysqli_num_rows($query_rol);
					 ?>
					<select name="rol" id="rol">
						<?php
							if($result_rol > 0)
							{
								while ($rol = mysqli_fetch_array($query_rol)) {
						?>
								<option value="<?php echo $rol["idrol"]; ?>"><?php echo $rol["rol"] ?></option>
						<?php
									# code...
								}
							}
						 ?>
					</select>
				</div>
				<button type="submit"  class="btn_save"><i class="far fa-save fa-lg"></i> Crear usuario</button>
			</form>
		</div>
	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>