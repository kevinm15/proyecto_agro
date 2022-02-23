<?php
	session_start();
	include "../../conexion.php";
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
	{
		header("location: ../index.php");
	}

	//Paginador
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM pedido");
	$result_register = mysqli_fetch_array($sql_registe);
	$total_registro = $result_register['total_registro'];
	$por_pagina = 200;

	if(empty($_GET['pagina']))
	{
		$pagina = 1;
	}else{
		$pagina = $_GET['pagina'];
	}

	$desde = ($pagina-1) * $por_pagina;
	$total_paginas = ceil($total_registro / $por_pagina);
	$query = mysqli_query($conection,"SELECT p.id_pedido,
											 p.fecha,
											 p.total,
											 c.id_contacto,
											 p.estatus,
											 c.nombre as contacto,
											 c.telefono,
											 tp.tipo_pago
										FROM pedido p
										INNER JOIN contacto_pedido c
										ON p.contacto_id = c.id_contacto
										INNER JOIN tipo_pago tp
										ON p.tipopago_id = tp.id_tipopago
										WHERE p.estatus != 10
									  	ORDER BY p.id_pedido DESC LIMIT $desde,$por_pagina
		");

	$result = mysqli_num_rows($query);
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Lista de ventas</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<h1><i class="fas fa-box"></i> Pedidos </h1>
		<?php if( $result > 0  and ($_SESSION['rol'] == 1 or $_SESSION['rol'] == 2)){ ?>
		<form action="exportar.php" method="post" class="formExport" >
			<button type="submit" class="bntExport"> <i class="fas fa-file-excel"></i> Exportar todo</button>
		</form>
		<?php } ?>
		<a href="index.php" class="btnRefresh"><i class="fas fa-sync-alt" title="Actualizar"></i></a>
		<form action="buscar_pedido.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar pedido">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
		<div>
			<h5>Buscar por Fecha</h5>
			<form action="buscar_pedido.php" method="get" class="form_search_date">
				<label>De: </label>
				<input type="date" name="fecha_de" id="fecha_de" required>
				<label> A </label>
				<input type="date" name="fecha_a" id="fecha_a" required>
				<button type="submit" class="btn_view"><i class="fas fa-search"></i></button>
			</form>
		</div>
		<div class="containerTable">
			<table id="tblPedidos">
				<tr>
					<th>No.</th>
					<th>Fecha</th>
					<th>Contacto</th>
					<th>Teléfono</th>
					<th class="textcenter">Tipo de pago</th>
					<th class="textcenter">Estado</th>
					<th class="textright">Total</th>
					<th class="textright">Acciones</th>
				</tr>
			<?php
				if($result > 0){
					while ($data = mysqli_fetch_assoc($query)) {					
						$key = $data["id_pedido"].'_24091989';
						$keyPedido = md5($key);
						$arrFecha = explode('-',$data['fecha']);
						$fecha = $arrFecha[2].' / '.$arrFecha[1].' / '.$arrFecha[0];

						if($data["estatus"] == 1){
							$estado = '<span class="bkA">Activo</span>';
						}else if($data["estatus"] == 2){
							$estado = '<span class="bkY">En proceso</span>';
						}else if($data["estatus"] == 3){
							$estado = '<span class="bkG">Entregado</span>';
						}else{
							$estado = '<span class="bkR">Anulado</span>';
						}
				?>
					<tr id="row_<?php echo $data["id_pedido"]; ?>">
						<td><?php echo $data["id_pedido"]; ?></td>
						<td><?php echo $fecha; ?></td>
						<td><?php echo $data["contacto"]; ?></td>
						<td><?php echo $data["telefono"]; ?></td>
						<td class="textcenter"><?php echo $data['tipo_pago']; ?></td>
						<td class="estado textcenter"><?php echo $estado; ?></td>
						<td class="textright totalpedido"><span><?= SIMBOLO_MONEDA ?>.</span><?php echo formatCant($data["total"]); ?></td>
						<td class="textright">
							<div class="div_acciones">
								<div>
									<button title="Procesar pedido" class="btn_add estado_pedido" type="button" p="<?php echo $data["id_pedido"]; ?>" ><i class="fas fa-reply-all"></i></button>
								</div>
								<div>
									<button class="btn_view view_pedido_pdf" type="button" c="<?php echo $keyPedido; ?>" p="<?php echo $data["id_pedido"]; ?>" title="Generar PDF" ><i class="far fa-file-pdf"></i></i></button>
								</div>
								<?php 
									if($data["estatus"] == 1 || $data["estatus"] == 2)
									{
								 ?>
								<!-- <div>
									<button style="display: none;" title="Procesar pedido" class="btn_add view_pedido" type="button" cp=<?php echo $cripPedido; ?> ><i class="fas fa-reply-all"></i></button>
								</div> -->
								<?php } ?>
								<?php if($data["estatus"] == 4){ ?>

									<!-- <div class="div_pedido">
										<button type="button" class="btn_anular inactive" ><i class="fas fa-ban"></i></button>
									</div> -->
								<?php }else{ ?>
									<!-- <div class="div_pedido">
										<button class="btn_anular anular_pedido" p="<?php echo $data["id_pedido"]; ?>" type="button" title="Anular pedido"><i class="fas fa-ban"></i></button>
									</div> -->
								<?php } ?>
							</div>
						</td>
					</tr>
			<?php
					}
				}else{
					echo '<tr><td colspan="9" align="center"><p><strong>No hay datos para mostrar</strong></p></td></tr>';
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