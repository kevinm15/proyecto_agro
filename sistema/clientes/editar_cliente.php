<?php
	session_start();
	include "../../conexion.php";
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Actualizar Cliente</title>
</head>
<?php 
if(!empty($_POST))
	{
		$alert='';
		if(empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion']))
		{
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$idCliente  = intval($_POST['id']);
			$nit        = strClean($_POST['nit']);
			$nombre     = ucwords(strClean($_POST['nombre']));
			$telefono   = intval($_POST['telefono']);
			$correo  	= strtolower(strClean($_POST['correo']));
			$direccion  = strClean($_POST['direccion']);

			$result = 0;
			if($nit != 'CF' || $nit != 'cf')
			{
				$query = mysqli_query($conection,"SELECT * FROM cliente
													WHERE (nit = '$nit' AND idcliente != $idCliente) OR (correo = '$correo' AND idcliente != $idCliente)
													");
				$result = mysqli_num_rows($query);
			}

			if($result > 0){
				$alert='<p class="msg_error">El nit o el email ya existe, ingrese otro.</p>';
			}else{

				if($nit == '')
				{
					$nit = 0;
				}

				$sql_update = mysqli_query($conection,"UPDATE cliente
															SET nit = '$nit', nombre='$nombre',telefono='$telefono',correo='$correo',direccion='$direccion'
															WHERE idcliente= $idCliente ");

				if($sql_update){
					$alert='<p class="msg_save">Cliente actualizado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al actualizar el cliente.</p>';
				}
			}
		}

	}

	//Mostrar Datos
	if(empty($_REQUEST['id']))
	{
		header('Location: index.php');
	}
	$idcliente = intval($_REQUEST['id']);

	$sql= mysqli_query($conection,"SELECT *	FROM cliente WHERE idcliente= $idcliente and estatus=1 ");
	$result_sql = mysqli_num_rows($sql);

	if($result_sql == 0){
		header('Location: index.php');
	}else{

		while ($data = mysqli_fetch_array($sql)) {
			# code...
			$idcliente   = $data['idcliente'];
			$nit         = $data['nit'];
			$nombre      = $data['nombre'];
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
			<h1><i class="far fa-edit"></i> Actualizar cliente</h1>
			<a href="index.php" class="linkViewList" ><i class="far fa-list-alt"></i> Ver lista</a>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post">
				<div>
					<p>Los campos con (*) son obligatorios.</p>
				</div>
				<input type="hidden" name="id" value="<?php echo $idcliente; ?>">
				<label for="nit"><th><?= strtoupper(IDENTIFICACION_TRIBUTARIA); ?></th></label>
				<input type="text" name="nit" id="nit" placeholder="Identificación tributaria" value="<?php echo $nit; ?>" required>
				<label for="nombre">Nombre (*)</label>
				<input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value="<?php echo $nombre; ?>"  required>
				<label for="telefono">Teléfono (*)</label>
				<input type="number" name="telefono" id="telefono" placeholder="Teléfono" value="<?php echo $telefono; ?>" required>
				<label for="telefono">Correo electrónico</label>
				<input type="email" name="correo" id="correo" placeholder="Correo electrónico" value="<?php echo $correo; ?>" >
				<label for="direccion">Dirección (*)</label>
				<input type="text" name="direccion" id="direccion" placeholder="Dirección completa" value="<?php echo $direccion; ?>" required>
				<button type="submit" class="btn_save"><i class="far fa-edit"></i> Actualizar Cliente</button>
			</form>
		</div>
	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>