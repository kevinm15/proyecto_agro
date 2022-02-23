<?php 
	session_start();

	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
	{
		header("location: ../index.php");
	}

	include "../../conexion.php";
	$nit   = '';
	$proveedor   = '';
	$contacto    = '';
	$telefono    = '';
	$correo    	 = '';
	$direccion   = '';
	$alert 		 = '';
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Registro Proveedor</title>
</head>
<?php 
if(!empty($_POST))
{
	$nit   = strClean($_POST['nit']);
	$proveedor   = ucwords(strClean($_POST['proveedor']));
	$contacto    = ucwords(strClean($_POST['contacto']));
	$telefono    = intval($_POST['telefono']);
	$correo    	 = strtolower(strClean($_POST['correo']));
	$direccion   = strClean($_POST['direccion']);
	$usuario_id  = intval($_SESSION['idUser']);

	if(empty($_POST['nit']) || empty($_POST['proveedor']) || empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['correo']) || empty($_POST['direccion']))
	{
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
	}else{
		$query_insert = mysqli_query($conection,"INSERT INTO proveedor(nit,proveedor,contacto,telefono,correo,direccion,usuario_id)
													VALUES('$nit','$proveedor','$contacto','$telefono','$correo','$direccion','$usuario_id')");

		if($query_insert){
			$alert='<p class="msg_save">Proveedor guardado correctamente.</p>';
			$nit   		 = '';
			$proveedor   = '';
			$contacto    = '';
			$telefono    = '';
			$correo    	 = '';
			$direccion   = '';
		}else{
			$alert='<p class="msg_error">Error al guardar el Proveedor.</p>';
		}
	}
}
?>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<div class="form_register">
			<h1><i class="far fa-building"></i> Registro Proveedor</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post" class="form">
				<div class="wd100">
					<label for="nit"><?= strtoupper(IDENTIFICACION_TRIBUTARIA); ?></label>
					<input type="text" name="nit" id="nit" value="<?= $nit;  ?>" placeholder="Identificación tributaria" required>
				</div>
				<div class="wd100">	
					<label for="proveedor">Proveedor</label>
					<input type="text" name="proveedor" id="proveedor" value="<?= $proveedor;  ?>" placeholder="Nombre del Proveedor" required>
				</div>
				<div class="wd100">
					<label for="contacto">Contacto</label>
					<input type="text" name="contacto" id="contacto" value="<?= $contacto;  ?>" placeholder="Nombre completo del contacto" required>
				</div>
				<div class="wd50">
					<label for="telefono">Teléfono</label>
					<input type="number" name="telefono" id="telefono" value="<?= $telefono;  ?>" placeholder="Teléfono" required>
				</div>
				<div class="wd50">
					<label for="telefono">Correo</label>
					<input type="email" name="correo" id="correo" value="<?= $correo;  ?>" placeholder="Correo electrónico" required>
				</div>
				<div class="wd100">
					<label for="direccion">Dirección</label>
					<input type="text" name="direccion" id="direccion" value="<?= $direccion;  ?>" placeholder="Dirección completa" required>
				</div>
				<button type="submit" class="btn_save"><i class="far fa-save fa-lg"></i> Guardar Proveedor</button>
			</form>
		</div>
	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>