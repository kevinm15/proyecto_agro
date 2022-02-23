<?php
	session_start();
	if($_SESSION['rol'] != 1)
	{
		header("location: ../index.php");
	}
	$busqueda = strtolower($_REQUEST['busqueda']);
	if(empty($busqueda))
	{
		header("location: index.php");
	}
	include "../../conexion.php";

	//Paginador
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM categoria
														WHERE ( idcategoria LIKE '%$busqueda%' OR
																categoria LIKE '%$busqueda%' OR
																descripcion LIKE '%$busqueda%'
														   )
														AND estatus != 10 ORDER BY categoria ASC ");
	$result_register = mysqli_fetch_array($sql_registe);
	$total_registro = $result_register['total_registro'];

	$por_pagina = 100;

	if(empty($_GET['pagina']))
	{
		$pagina = 1;
	}else{
		$pagina = $_GET['pagina'];
	}

	$desde = ($pagina-1) * $por_pagina;
	$total_paginas = ceil($total_registro / $por_pagina);
	$query = mysqli_query($conection,"SELECT * FROM categoria WHERE
								( idcategoria LIKE '%$busqueda%' OR
									categoria LIKE '%$busqueda%' OR
									descripcion LIKE '%$busqueda%' )
								AND
								estatus != 10 ORDER BY categoria ASC LIMIT $desde,$por_pagina
		");

	$result = mysqli_num_rows($query);
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Buscar categoría</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<h1> <i class="fas fa-window-restore"></i> Lista de categorías</h1>
		<a href="#" class="btn_new btnNewCategory"><i class="fas fa-plus"></i> Crear categoria</a>
		<a href="index.php" class="btnRefresh"><i class="fas fa-sync-alt" title="Actualizar"></i></a>
		<form action="buscar_categoria.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda; ?>">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
	<div class="containerTable">
		<table>
			<tr>
				<th>No.</th>
				<th>Categoría</th>
				<th>Descripción</th>
				<th class="textcenter">Estado</th>
				<th class="textright">Acciones</th>
			</tr>
		<?php
			if($result > 0){
				$c = 1;
				while ($data = mysqli_fetch_array($query)) {
					if($data["estatus"] == 1){
						$estatusCategoria = '<span title="Desactivar Marca" class="pagada activeUser" estado="'.$data["estatus"].'" onclick="modalActiveCategoria('.$data["idcategoria"].');">Activo</span>';
					}else{
						$estatusCategoria = '<span title="Activar Marca" class="anulada activeUser" estado="'.$data["estatus"].'" onclick="modalActiveCategoria('.$data["idcategoria"].');">Inactivo</span>';
					}
			?>
				<tr id="item_<?php echo $data["idcategoria"]; ?>" >
					<td><?php echo $c; ?></td>
					<td class="rowCategoria"><?php echo $data["categoria"]; ?></td>
					<td class="rowDescripcion"><?php echo $data["descripcion"]; ?></td>
					<td class="rowEstado textcenter"><?php echo $estatusCategoria; ?></td>
					<td class="textright">
						<div class="div_acciones">
							<div>
								<a class="btn_edit btnEditCategoria" href="#"><i class="far fa-edit"></i> </a>
							</div>
							<div>
								<a class="btn_del btnDelCategoria" href="#" title="Eliminar"><i class="far fa-trash-alt"></i></a>
							</div>
						</div>
					</td>
				</tr>
		<?php
					$c++;
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