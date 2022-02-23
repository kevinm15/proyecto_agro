<?php
	session_start();
	include "../../conexion.php";
	$busqueda = strtolower($_REQUEST['busqueda']);
	if(empty($busqueda))
	{
		header("location: index.php");
	}
	//Paginador
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM cliente
														WHERE ( idcliente LIKE '%$busqueda%' OR
																nit LIKE '%$busqueda%' OR
																nombre LIKE '%$busqueda%' OR
																telefono LIKE '%$busqueda%' OR
																correo LIKE '%$busqueda%'
														   )
														AND estatus = 1  ");

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

	$queryExport = "SELECT idcliente,
							nit,
							nombre,
							telefono,
							correo,
                            direccion,
							DATE_FORMAT(dateadd, '%d/%m/%Y') as fecha_registro,
							estatus FROM cliente WHERE
								( idcliente LIKE '%$busqueda%' OR
									nit LIKE '%$busqueda%' OR
									nombre LIKE '%$busqueda%' OR
									telefono LIKE '%$busqueda%' OR
									correo LIKE  '%$busqueda%' ) AND
							estatus = 1 ORDER BY idcliente ";
	$queryFiltro = "SELECT idcliente,
							nit,
							nombre,
							telefono,
							correo,
                            direccion,
							DATE_FORMAT(dateadd, '%d/%m/%Y') as fecha_registro,
							estatus FROM cliente WHERE
								( idcliente LIKE '%$busqueda%' OR
									nit LIKE '%$busqueda%' OR
									nombre LIKE '%$busqueda%' OR
									telefono LIKE '%$busqueda%' OR
									correo LIKE  '%$busqueda%' )
								AND
								estatus = 1 ORDER BY idcliente ASC LIMIT $desde,$por_pagina";

	$query = mysqli_query($conection,$queryFiltro);
	$result = mysqli_num_rows($query);

 ?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Buscar cliente</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<h1> <i class="fas fa-users"></i> Buscar de clientes</h1>
		<a href="registro_cliente.php" class="btn_new"><i class="fas fa-user-plus"></i> Nuevo </a>
		<?php
			if($result > 0 and ($_SESSION['rol'] == 1 or $_SESSION['rol'] == 2)){
		?>
		<form action="exportar.php" method="post" class="formExport" >
			<input type="hidden" name="exportFilter" id="exportFilter" value="<?php echo $queryExport; ?>">
			<button type="submit" class="bntExport">  <i class="fas fa-file-excel"></i> Exportar Filtro</button>
		</form>
		<?php } ?>
		<a href="index.php" class="btnRefresh"><i class="fas fa-sync-alt" title="Actualizar"></i></a>
		<form action="buscar_cliente.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda; ?>">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
	<div class="containerTable">
		<table>
			<tr>
				<th>ID</th>
				<th><?= strtoupper(IDENTIFICACION_TRIBUTARIA); ?></th>
				<th>Nombre</th>
				<th>Teléfono</th>
				<th>Email</th>
				<th>Fecha</th>
				<th class="textright">Acciones</th>
			</tr>
		<?php
			if($result > 0){

				while ($data = mysqli_fetch_array($query)) {
			?>
				<tr id="item_<?php echo $data["idcliente"]; ?>">
					<td><?php echo $data["idcliente"]; ?></td>
					<td><?php echo $data["nit"]; ?></td>
					<td><?php echo $data["nombre"]; ?></td>
					<td><?php echo ($data["telefono"] != 0) ? $data["telefono"] : '-'; ?></td>
					<td><?php echo $data["correo"]; ?></td>
					<td><?php echo $data["fecha_registro"]; ?></td>
					<td class="textright">
						<div class="div_acciones">
							<div>
								<a href="#" class="btn_view btnInfoCliente" title="Ver datos"><i class="fas fa-eye"></i></a>
							</div>
							<div>
								<a class="btn_edit" href="editar_cliente.php?id=<?php echo $data["idcliente"]; ?>" title="Editar"><i class="far fa-edit"></i></a>
							</div>
							<?php if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){ ?>
							<div>
								<a class="btn_del" href="eliminar_confirmar_cliente.php?id=<?php echo $data["idcliente"]; ?>" title="Eliminar"><i class="far fa-trash-alt"></i></a>
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
				<li><a href="?pagina=<?php echo 1; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fas fa-step-backward"></i></a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fas fa-backward"></i></a></li>
			<?php
				}
				for ($i=1; $i <= $total_paginas; $i++) {
					# code...
					if($i == $pagina)
					{
						echo '<li class="pageSelected">'.$i.'</li>';
					}else{
						echo '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>';
					}
				}

				if($pagina != $total_paginas)
				{
			 ?>
				<li><a href="?pagina=<?php echo $pagina + 1; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fas fa-forward"></i></a></li>
				<li><a href="?pagina=<?php echo $total_paginas; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fas fa-step-forward"></i></a></li>
			<?php } ?>
			</ul>
		</div>
<?php } ?>
	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>