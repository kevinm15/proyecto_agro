<?php
	session_start();
	if($_SESSION['rol'] != 1)
	{
		header("location: ../index.php");
	}

	include "../../conexion.php";
	//Paginador
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM usuario WHERE estatus != 10 ");
	$result_register = mysqli_fetch_array($sql_registe);
	$total_registro = $result_register['total_registro'];

	$por_pagina = 150;

	if(empty($_GET['pagina']))
	{
		$pagina = 1;
	}else{
		$pagina = $_GET['pagina'];
	}

	$desde = ($pagina-1) * $por_pagina;
	$total_paginas = ceil($total_registro / $por_pagina);

	$whereAdmin = "";
	if($_SESSION['user'] != 'admin')
	{
		$whereAdmin = " and u.rol != 1 ";
	}

	$query = mysqli_query($conection,"SELECT u.idusuario, u.nombre, u.correo, u.usuario, u.estatus, u.rol as idRol, r.rol FROM usuario u INNER JOIN rol r ON u.rol = r.idrol WHERE estatus != 10 {$whereAdmin} ORDER BY u.idusuario DESC LIMIT $desde,$por_pagina
			");

	$result = mysqli_num_rows($query);

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Lista de usuarios</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<h1> <i class="fas fa-users"></i> Lista de usuarios</h1>
		<a href="registro_usuario.php" class="btn_new"><i class="fas fa-user-plus"></i> Nuevo</a>

		<?php
			if($result > 0)
			{
		?>
		<form action="exportar.php" method="post" class="formExport" >
			<button type="submit" class="bntExport">  <i class="fas fa-file-excel"></i> Exportar todo</button>
		</form>
		<?php } ?>
		<a href="index.php" class="btnRefresh"><i class="fas fa-sync-alt" title="Actualizar"></i></a>
		<form action="buscar_usuario.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
		<div class="containerTable">
			<table>
				<tr>
					<th>ID</th>
					<th>Nombre</th>
					<th>Correo</th>
					<th>Usuario</th>
					<th>Rol</th>
					<th class="textcenter">Estado</th>
					<th class="textright">Acciones</th>
				</tr>
			<?php
				if($result > 0){

					while ($data = mysqli_fetch_array($query)) {
						$estado= '';
						if($data["idusuario"] != 1)
						{
							if($data["estatus"] == 0){
								$estado = '<span title="Activar usuario" class="anulada activeUser" estado="'.$data["estatus"].'" onclick="modalActiveUser('.$data["idusuario"].');">Inactivo</span>';
							}else{
								$estado = '<span title="Desactivar usuario" class="pagada activeUser" estado="'.$data["estatus"].'" onclick="modalActiveUser('.$data["idusuario"].');">Activo</span>';
							}
						}
				?>
					<tr id="item_<?php echo $data["idusuario"]; ?>" >
						<td><?php echo $data["idusuario"]; ?></td>
						<td><?php echo $data["nombre"]; ?></td>
						<td><?php echo $data["correo"]; ?></td>
						<td><?php echo $data["usuario"]; ?></td>
						<td><?php echo $data['rol'] ?></td>

						<td class="estado textcenter"><?php echo $estado; ?></td>

						<td class="textright">
							<div class="div_acciones">
								<div>
									<a href="#" class="btn_view btnInfoUser" title="Ver datos"><i class="fas fa-eye"></i></a>
								</div>
								<div>
									<a class="btn_edit" href="editar_usuario.php?id=<?php echo $data["idusuario"]; ?>" title="Editar"><i class="far fa-edit"></i> </a>
								</div>

							<?php if($data["idusuario"] != 1){ ?>
								<div>
									<a class="btn_del" href="eliminar_confirmar_usuario.php?id=<?php echo $data["idusuario"]; ?>" title="Eliminar"><i class="far fa-trash-alt"></i></a>
								</div>
							<?php } ?>
							</div>
						</td>
					</tr>
			<?php
					}

				}else{
					echo '<tr><td colspan="7" align="center"><p><strong>No hay datos para mostrar</strong></p></td></tr>';
				}
			 ?>


			</table>
		</div>
<?php

	if($total_registro != 0)
	{
 ?>
		<div class="paginador">
			<ul>
			<?php 
				if($pagina != 1)
				{
			 ?>
				<li><a href="?pagina=<?php echo 1; ?>"><i class="fas fa-step-backward"></i></a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>"><i class="fas fa-backward"></i></a></li>
			<?php 
				}
				for ($i=1; $i <= $total_paginas; $i++) { 
					# code...
					if($i == $pagina)
					{
						echo '<li class="pageSelected">'.$i.'</li>';
					}else{
						echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
					}
				}

				if($pagina != $total_paginas)
				{
			 ?>
				<li><a href="?pagina=<?php echo $pagina + 1; ?>"><i class="fas fa-forward"></i></a></li>
				<li><a href="?pagina=<?php echo $total_paginas; ?> "><i class="fas fa-step-forward"></i></a></li>
			<?php } ?>
			</ul>
		</div>
<?php } ?>
	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>