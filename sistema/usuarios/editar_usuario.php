<?php

	session_start();
	if($_SESSION['rol'] != 1)
	{
		header("location: ../index.php");
	}
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Actualizar Usuario</title>
</head>
<?php 
include "../../conexion.php";
	if(!empty($_POST))
	{
		$alert='';
		if(empty($_POST['dpi']) || empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['correo']) || empty($_POST['usuario'])  || empty($_POST['rol']))
		{
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$idUsuario = intval($_POST['id']);
			$dpi 	= strClean($_POST['dpi']);
			$nombre = ucwords(strClean($_POST['nombre']));
			$telefono = intval($_POST['telefono']);
			$email  = strtolower(strClean($_POST['correo']));
			$user   = strClean($_POST['usuario']);
			$clave  = md5($_POST['clave']);
			$rol    = intval($_POST['rol']);

			$query = mysqli_query($conection,"SELECT * FROM usuario
													   WHERE (usuario = '$user' AND idusuario != $idUsuario)
													   OR (correo = '$email' AND idusuario != $idUsuario) ");

			$result = mysqli_fetch_assoc($query);
			$result = mysqli_num_rows($query);

			if($result > 0){
				$alert='<p class="msg_error">El correo o el usuario ya existe.</p>';
			}else{

				if(empty($_POST['clave']))
				{

					$sql_update = mysqli_query($conection,"UPDATE usuario
															SET dpi= $dpi, nombre = '$nombre', telefono = $telefono, correo='$email',usuario='$user',rol='$rol'
															WHERE idusuario= $idUsuario ");
				}else{
					$sql_update = mysqli_query($conection,"UPDATE usuario
															SET dpi= $dpi, nombre = '$nombre', telefono = $telefono, correo='$email',usuario='$user',clave='$clave', rol='$rol'
															WHERE idusuario= $idUsuario ");

				}

				if($sql_update){
					$alert='<p class="msg_save">Usuario actualizado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al actualizar el usuario.</p>';
				}

			}

		}
	}

	//Mostrar Datos
	if(empty($_REQUEST['id']) || ($_REQUEST['id'] == 1 and $_SESSION['user'] != 'admin'))
	{
		header('Location: index.php');
	}
	$iduser = intval($_REQUEST['id']);

	$sql= mysqli_query($conection,"SELECT u.idusuario,u.dpi,u.nombre,u.telefono,u.correo,u.usuario, (u.rol) as idrol, (r.rol) as rol
									FROM usuario u
									INNER JOIN rol r
									on u.rol = r.idrol
									WHERE idusuario= $iduser and estatus!=10 ");
	$result_sql = mysqli_num_rows($sql);

	if($result_sql == 0){
		header('Location: index.php');
	}else{
		$option = '';
		while ($data = mysqli_fetch_assoc($sql)) {

			if($data['idrol'] == 1 && $_SESSION['user'] != 'admin' )
			{
				header('Location: index.php');
			}
			# code...
			$iduser  = $data['idusuario'];
			$dpi  	 = $data['dpi'];
			$nombre  = $data['nombre'];
			$telefono= $data['telefono'];
			$correo  = $data['correo'];
			$usuario = $data['usuario'];
			$idrol   = $data['idrol'];
			$rol     = $data['rol'];

			if($idrol == 1){
				$option = '<option value="'.$idrol.'" select>'.$rol.'</option>';
			}else if($idrol == 2){
				$option = '<option value="'.$idrol.'" select>'.$rol.'</option>';
			}else if($idrol == 3){
				$option = '<option value="'.$idrol.'" select>'.$rol.'</option>';
			}
		}
	}
 ?>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">

		<div class="form_register">
			<h1><i class="far fa-edit"></i> Actualizar usuario</h1>
			<a href="index.php" class="linkViewList" ><i class="far fa-list-alt"></i> Ver lista</a>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post" class="form">
				<input type="hidden" name="id" value="<?php echo $iduser; ?>">
				<div class="wd50">
					<label for="dpi"><?= IDENTIFICACION_CLIENTE ?></label>
					<input type="text" name="dpi" id="dpi" placeholder="Documento de Identificación" value="<?php echo $dpi; ?>" required >
				</div>
				<div class="wd100">
					<label for="nombre">Nombre</label>
					<input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value="<?php echo $nombre; ?>" required >
				</div>
				<div class="wd50">
					<label for="telefono">Teléfono</label>
					<input type="text" name="telefono" id="telefono" placeholder="Número de teléfono" value="<?php echo $telefono; ?>" required >
				</div>
				<div class="wd50">
					<label for="correo">Correo electrónico</label>
					<input type="email" name="correo" id="correo" placeholder="Correo electrónico" value="<?php echo $correo; ?>" required >
				</div>
				<div class="wd50">
					<label for="usuario">Usuario</label>
					<input type="text" name="usuario" id="usuario" placeholder="Usuario" value="<?php echo $usuario; ?>" required >
				</div>
				<div class="wd50">
					<label for="clave">Clave</label>
					<input type="password" name="clave" id="clave" placeholder="Clave de acceso">
				</div>
				<div class="wd100">
					<label for="rol">Tipo Usuario</label>
					<?php
						include "../../conexion.php";
						if($_SESSION['user'] == 'admin')
						{
							$query_rol = mysqli_query($conection,"SELECT * FROM rol");
						}else{
							$query_rol = mysqli_query($conection,"SELECT * FROM rol WHERE idrol != 1");
						}
						$result_rol = mysqli_num_rows($query_rol);

					 ?>

					<select name="rol" id="rol" class="notItemOne">
						<?php
							echo $option;
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
				<button type="submit" class="btn_save"><i class="far fa-edit"></i> Actualizar Usuario</button>
			</form>
		</div>
	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>