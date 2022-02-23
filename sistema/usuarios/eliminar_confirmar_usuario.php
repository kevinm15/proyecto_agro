<?php 
	session_start();
	if($_SESSION['rol'] != 1)
	{
		header("location: ../index.php");
	}
	include "../../conexion.php";

	if(!empty($_POST))
	{
		if($_POST['idusuario'] == 1){
			header("location: index.php");
			exit;
		}
		$idusuario = $_POST['idusuario'];

		$query_delete = mysqli_query($conection,"UPDATE usuario SET estatus = 10 WHERE idusuario = $idusuario ");
		mysqli_close($conection);
		if($query_delete){
			header("location: index.php");
		}else{
			echo "Error al eliminar";
		}

	}

	if(empty($_REQUEST['id']) || $_REQUEST['id'] == 1 )
	{
		header("location: index.php");
	}else{

		$idusuario = $_REQUEST['id'];

		$query = mysqli_query($conection,"SELECT u.nombre,u.usuario,u.rol as idrol,r.rol
												FROM usuario u
												INNER JOIN
												rol r
												ON u.rol = r.idrol
												WHERE u.idusuario = $idusuario ");
		$result = mysqli_num_rows($query);

		if($result > 0){
			while ($data = mysqli_fetch_array($query)) {
				# code...
				if($data['idrol'] == 1 && $_SESSION['user'] != 'admin' )
				{
					header('Location: index.php');
				}

				$nombre = $data['nombre'];
				$usuario = $data['usuario'];
				$rol     = $data['rol'];
			}
		}else{
			header("location: index.php");
		}
	}


 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Eliminar Usuario</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<div class="data_delete">
			<i class="fas fa-user-times fa-7x" style="color: #e66262"></i>
			<br>
			<br>
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p>Nombre: <span><?php echo $nombre; ?></span></p>
			<p>usuario: <span><?php echo $usuario; ?></span></p>
			<p>Tipo Usuario: <span><?php echo $rol; ?></span></p>

			<form method="post" action="">
				<input type="hidden" name="idusuario" value="<?php echo $idusuario; ?>">
				<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
				<a href="index.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
			</form>
		</div>
	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>