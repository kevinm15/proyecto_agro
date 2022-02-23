<?php
	session_start();
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
	{
		header("location: ../index.php");
	}
	include "../../conexion.php";
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Actualizar Proveedor</title>
</head>
<?php 
if(!empty($_POST))
{
	$alert='';
	if(empty($_POST['nit']) || empty($_POST['proveedor']) || empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['correo']) || empty($_POST['direccion']))
	{
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
	}else{
		$idproveedor  = intval($_POST['id']);
		$nit    	  = strClean($_POST['nit']);
		$proveedor    = ucwords(strClean($_POST['proveedor']));
		$contacto     = ucwords(strClean($_POST['contacto']));
		$telefono     = intval($_POST['telefono']);
		$correo       = strtolower(strClean($_POST['correo']));
		$direccion    = strClean($_POST['direccion']);
		$sql_update = mysqli_query($conection,"UPDATE proveedor
														SET nit = '$nit' ,proveedor = '$proveedor', contacto='$contacto',telefono='$telefono',correo = '$correo',direccion='$direccion'
														WHERE codproveedor= $idproveedor ");
		if($sql_update){
			$alert='<p class="msg_save">Proveedor actualizado correctamente.</p>';
		}else{
			$alert='<p class="msg_error">Error al actualizar el Proveedor.</p>';
		}
	}
}

//Mostrar Datos
if(empty($_REQUEST['id']))
{
	header('Location: index.php');
}
$idproveedor = intval($_REQUEST['id']);

$sql= mysqli_query($conection,"SELECT *	FROM proveedor WHERE codproveedor= $idproveedor and estatus=1");
$result_sql = mysqli_num_rows($sql);

if($result_sql == 0){
	header('Location: index.php');
}else{

	while ($data = mysqli_fetch_array($sql)) {
		# code...
		$idproveedor = $data['codproveedor'];
		$nit 		 = $data['nit'];
		$proveedor   = $data['proveedor'];
		$contacto    = $data['contacto'];
		$telefono    = $data['telefono'];
		$correo    	 = $data['correo'];
		$direccion   = $data['direccion'];
	}
}

?>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">

		<div class="form_register">
			<h1><i class="far fa-edit"></i> Actualizar proveedor</h1>
			<a href="index.php" class="linkViewList" ><i class="far fa-list-alt"></i> Ver lista</a>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post" class="form">
				<input type="hidden" name="id" value="<?php echo $idproveedor; ?>">
				<div class="wd100">
					<label for="nit"><?= strtoupper(IDENTIFICACION_TRIBUTARIA); ?></label>
					<input type="text" name="nit" id="nit" value="<?= $nit;  ?>" placeholder="Identificación tributaria" required>
				</div>
				<div class="wd100">
					<label for="proveedor">Proveedor</label>
					<input type="text" name="proveedor" id="proveedor" placeholder="Nombre del Proveedor" value="<?php echo $proveedor; ?>" required>
				</div>
				<div class="wd100">
					<label for="contacto">Contacto</label>
					<input type="text" name="contacto" id="contacto" placeholder="Nombre completo del contacto" value="<?php echo $contacto; ?>" required>
				</div>
				<div class="wd50">
					<label for="telefono">Teléfono</label>
					<input type="number" name="telefono" id="telefono" placeholder="Teléfono" value="<?php echo $telefono; ?>" required>
				</div>
				<div class="wd50">
					<label for="telefono">Correo</label>
					<input type="email" name="correo" id="correo" placeholder="Correo electrónico" value="<?= $correo; ?>" required>
				</div>
				<div class="wd100">
					<label for="direccion">Dirección</label>
					<input type="text" name="direccion" id="direccion" placeholder="Dirección completa" value="<?php echo $direccion; ?>" required>
				</div>
				<button type="submit" class="btn_save"><i class="far fa-edit"></i> Actualizar Proveedor</button>
			</form>
		</div>
	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>