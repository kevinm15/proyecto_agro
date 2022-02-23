<?php
	session_start();
	include "../../conexion.php";

	//Extrae la moneda
	$sbMoneda = '';
	$query_moneda = mysqli_query($conection,"SELECT simbolo_moneda FROM configuracion WHERE id = 1");
	$result_moneda = mysqli_num_rows($query_moneda);
	if($result_moneda > 0)
	{
		$moneda = mysqli_fetch_assoc($query_moneda);
		$sbMoneda = $moneda['simbolo_moneda'];
	}
	//Extrae marca
	$query_marca = mysqli_query($conection,"SELECT * FROM marca WHERE estatus != 10 ORDER BY marca ASC");
	$result_marca = mysqli_num_rows($query_marca);

	//Paginador
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM producto WHERE estatus = 1 ");
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

	$query = mysqli_query($conection,"SELECT p.codproducto,
											 p.producto,
											 p.descripcion,
											 c.categoria,
											 pr.presentacion,
											 p.precio,
											 p.existencia,
											 p.existencia_minima,
											 m.marca,
											 p.codebar,
											 u.ubicacion,
											 p.foto
									FROM producto p
									INNER JOIN marca m
									ON p.marca_id = m.idmarca
									INNER JOIN categoria c
									ON p.categoria = c.idcategoria
									INNER JOIN presentacion_producto pr
									ON p.presentacion_id = pr.id_presentacion
									INNER JOIN ubicacion u
									ON p.ubicacion_id = u.id_ubicacion
									WHERE p.estatus = 1 ORDER BY p.codproducto DESC LIMIT $desde,$por_pagina
		");

	$result = mysqli_num_rows($query);
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Lista de productos</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<h1><i class="fas fa-cube"></i> Lista de productos</h1>
		<?php
			if($_SESSION['rol'] == 1 or $_SESSION['rol'] == 2){
		 ?>
		<a href="registro_producto.php" class="btn_new"><i class="fas fa-plus"></i> Nuevo </a>
		<?php
		}
		if($result> 0 and ($_SESSION['rol'] == 1 or $_SESSION['rol'] == 2)){
		 ?>
		<form action="exportar.php" method="post" class="formExport" >
			<button type="submit" class="bntExport"> <i class="fas fa-file-excel"></i> Exportar todo</button>
		</form>
		<?php } ?>
		<a href="index.php" class="btnRefresh"><i class="fas fa-sync-alt" title="Actualizar"></i></a>

		<form action="buscar_productos.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
	<div class="containerTable">
		<table id="tblProductos">
			<tr>
				<th>Código</th>
				<th>Producto</th>
				<th>Ubicación</th>
				<th class="textcenter">Presentación</th>
				<th>Categoría</th>
				<th>Marca</th>
				<th class="textright" width="128px">Precio</th>
				<th class="textcenter">Existencia</th>
				<th class="textcenter">Estado</th>
				<!-- <th class="textcenter">Foto</th> -->
				<th class="textright">Acciones</th>
			</tr>
		<?php
			if($result > 0){

				while ($data = mysqli_fetch_array($query)) {
					if($data['foto'] != 'img_producto.png'){
						$foto = $base_url.'/img/uploads/'.$data['foto'];
					}else{
						$foto = $base_url.'/img/'.$data['foto'];
					}
					$bkRowClass = '';
					$estadoExistencia = '<span style="color: #60a756;"><strong>Activo</strong></span>';
					if($data['existencia']  <=  $data['existencia_minima']){
						$estadoExistencia = '<span>Reserva</span>';
						$bkRowClass = 'reserva';
					}
					if($data['existencia']  <= 0){
						$estadoExistencia = '<span class="anulada">Agotado</span>';
						$bkRowClass = 'agotado';
					}
			?>
				<tr class="row<?php echo $data["codproducto"]; ?>" id="row_<?php echo $data["codproducto"]; ?>">
					<td ><?php echo $data["codebar"]; ?></td>
					<td class="nameProduct"><?php echo $data["producto"]; ?> <div class="descriptionProduct"><?php echo $data["descripcion"]; ?></div></td>
					<td class="textcenter"><?php echo $data["ubicacion"]; ?></td>
					<td class="textcenter"><?php echo $data["presentacion"]; ?></td>
					<td ><?php echo $data["categoria"]; ?></td>
					<td class="textcenter"><?php echo $data["marca"]; ?></td>
					<td class="celPrecio textright"><?php echo SIMBOLO_MONEDA.'. '.formatCant($data["precio"]); ?></td>
					<td class="celExistencia textcenter"><?php echo $data["existencia"]; ?></td>
					<td class="celEstado <?= $bkRowClass; ?> textcenter" style="width: 50px;"><?php echo $estadoExistencia; ?></td>
					<!-- <td class="img_producto textcenter"><img src="<?php echo $foto; ?>" alt="<?php echo $data["descripcion"]; ?>"></td> -->
					<td class="textright">
						<div class="div_acciones">
							<div>
								<a class="btn_add add_product" product = "<?php echo $data["codproducto"]; ?>" href="#" title="Agregar al carrito"><i class="fas fa-cart-plus"></i></a>
							</div>
							<div>
								<a href="#" class="btn_view btnInfoProducto" title="Ver datos"><i class="fas fa-eye"></i></a>
							</div>
					<?php if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){ ?>
							<div>
								<a class="btn_edit" href="editar_producto.php?id=<?php echo $data["codproducto"]; ?>" title="Editar"><i class="far fa-edit"></i></a>
							</div>
					<?php }
							if($_SESSION['rol'] == 1){
					?>
							<div>
								<a class="btn_del del_product" product = "<?php echo $data["codproducto"]; ?>" href="#" title="Eliminar"><i class="far fa-trash-alt"></i> </a>
							</div>
					<?php } ?>
						</div>
					</td>
				</tr>
		<?php
				}
			}else{
				echo '<tr><td colspan="10" align="center"><p><strong>No hay datos para mostrar</strong></p></td></tr>';
			}
			mysqli_close($conection);
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