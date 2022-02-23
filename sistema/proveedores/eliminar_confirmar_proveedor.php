<?php 
	session_start();
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] !=2)
	{
		header("location: ../index.php");
	}
	include "../../conexion.php";

	if(!empty($_POST))
	{
		if(empty($_POST['idproveedor']))
		{
			header("location: index.php");
		}

		$idproveedor = $_POST['idproveedor'];
		$query_delete = mysqli_query($conection,"UPDATE proveedor SET estatus = 0 WHERE codproveedor = $idproveedor ");
		mysqli_close($conection);
		if($query_delete){
			header("location: index.php");
		}else{
			echo "Error al eliminar";
		}
	}

	if(empty($_REQUEST['id']) )
	{
		header("location: index.php");
	}else{

		$idproveedor = $_REQUEST['id'];
		$query = mysqli_query($conection,"SELECT * FROM proveedor WHERE codproveedor = $idproveedor ");
		$result = mysqli_num_rows($query);

		if($result > 0){
			while ($data = mysqli_fetch_array($query)) {
				# code...
				$proveedor = $data['proveedor'];
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
	<title>Eliminar Proveedor</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<div class="data_delete">
			<i class="far fa-building fa-7x" style="color: #e66262"></i>
			<br><br>
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p>Nombre del Proveedor: <span><?php echo $proveedor; ?></span></p>

			<form method="post" action="">
				<input type="hidden" name="idproveedor" value="<?php echo $idproveedor; ?>">
				<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
				<a href="index.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
			</form>
		</div>
	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>