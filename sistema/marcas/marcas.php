<?php
	session_start();
	if($_SESSION['rol'] != 1)
	{
		header("location: ../index.php");
	}

	include "../../conexion.php";
	//Paginador
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM marca WHERE estatus != 10 ");
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

	$query = mysqli_query($conection,"SELECT * FROM marca WHERE estatus != 10 ORDER BY marca ASC LIMIT $desde,$por_pagina
		");
	$result = mysqli_num_rows($query);
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Lista de marcas</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<h1> <i class="fab fa-bandcamp"></i> Lista de marcas</h1>
		<a href="#" class="btn_new btnNewMarca"><i class="fas fa-plus"></i> Crear marca</a>
		<a href="index.php" class="btnRefresh"><i class="fas fa-sync-alt" title="Actualizar"></i></a>
		<form action="buscar_marca.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
	<div class="containerTable">
		<table>
			<tr>
				<th>ID</th>
				<th>Marca</th>
				<th>Descripción</th>
				<th class="textcenter">Estado</th>
				<th class="textright">Acciones</th>
			</tr>
		<?php
			if($result > 0){
				while ($data = mysqli_fetch_array($query)) {

					if($data["estatus"] == 1){
						$estatusMarca = '<span title="Desactivar Marca" class="pagada activeUser" estado="'.$data["estatus"].'" onclick="modalActiveMarca('.$data["idmarca"].');">Activo</span>';
					}else{
						$estatusMarca = '<span title="Activar Marca" class="anulada activeUser" estado="'.$data["estatus"].'" onclick="modalActiveMarca('.$data["idmarca"].');">Inactivo</span>';
					}

			?>
				<tr id="item_<?php echo $data["idmarca"]; ?>" >
					<td><?php echo $data["idmarca"]; ?></td>
					<td class="rowMarca"><?php echo $data["marca"]; ?></td>
					<td class="rowDescripcion"><?php echo  $data["descripcion"]; ?></td>
					<td class="rowEstado textcenter"><?php echo $estatusMarca; ?></td>
					<td class="textright">
						<div class="div_acciones">
							<div>
								<a class="btn_edit btnEditMarca" href="#"><i class="far fa-edit"></i> </a>
							</div>
							<div>
								<a class="btn_del btnDelMarca" href="#" title="Eliminar"><i class="far fa-trash-alt"></i></a>
							</div>
						</div>
					</td>
				</tr>
		<?php
				}

			}else{
				echo '<tr><td colspan="5" align="center"><p><strong>No hay datos para mostrar</strong></p></td></tr>';
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