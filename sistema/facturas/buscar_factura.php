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
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM facturas
															WHERE ( idserie LIKE '%$busqueda%' OR
																cai LIKE '%$busqueda%' OR
																prefijo LIKE '%$busqueda%'
														   	) AND status != 10 ORDER BY idserie ASC ");

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

	$query = mysqli_query($conection,"SELECT * FROM facturas
												WHERE
													( idserie LIKE '%$busqueda%' OR
													  cai LIKE '%$busqueda%' OR
													  prefijo LIKE '%$busqueda%' )
													AND
													status != 10 ORDER BY idserie ASC LIMIT $desde,$por_pagina
									");
	$result = mysqli_num_rows($query);

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Lista facturas</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<h1> <i class="fas fa-file-alt"></i> Lista facturas</h1>
		<a href="#" class="btn_new btnNewSerie"><i class="fas fa-plus"></i> Nuevo </a>
		<a href="index.php" class="btnRefresh"><i class="fas fa-sync-alt" title="Actualizar"></i></a>
		<form action="buscar_factura.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" value="<?php echo $busqueda; ?>" placeholder="Buscar">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
	<div class="containerTable">
		<table>
			<tr>
				<th class="textcenter">No.</th>
				<th>CAI</th>
				<th>Prefijo Factura</th>
				<th>Periodo</th>
				<th>Rango factura</th>
				<th class="textcenter">Estado</th>
				<th class="textright">Acciones</th>
			</tr>
		<?php
			if($result > 0){

				while ($data = mysqli_fetch_array($query)) {
					if($data["status"] == 1){
						$status = '<span title="Desactivar Serie" class="pagada activeUser" estado="'.$data["status"].'" onclick="modalActiveSerie('.$data["idserie"].');">Activo</span>';
					}else{
						$status = '<span title="Activar Serie" class="anulada activeUser" estado="'.$data["status"].'" onclick="modalActiveSerie('.$data["idserie"].');">Inactivo</span>';
					}
			?>
				<tr id="item_<?php echo $data["idserie"]; ?>" >
					<td class="textcenter"><?php echo $data["idserie"]; ?></td>
					<td class="rowCai"><?php echo $data["cai"]; ?></td>
					<td class="rowPrefijo"><?php echo $data["prefijo"]; ?></td>
					<td class="rowPeriodo"><?php echo $data["periodo_inicio"].' - '.$data["periodo_fin"]; ?></td>
					<td class="rowRango"><?php echo $data["no_inicio"].' - '.$data["no_fin"]; ?></td>
					<td class="rowEstado textcenter"><?php echo $status; ?></td>
					<td class="textright">
						<div class="div_acciones">
							<div>
								<a class="btn_edit btnEditSerie" href="#"><i class="far fa-edit"></i> </a>
							</div>
							<div>
								<a class="btn_del btnDelSerie" href="#" title="Eliminar"><i class="far fa-trash-alt"></i></a>
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