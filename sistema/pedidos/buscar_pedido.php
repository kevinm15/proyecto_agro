<?php
	session_start();
	include "../../conexion.php";

	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
	{
		header("location: ../index.php");
	}

	$busqueda	= '';
	$fecha_de 	= '';
	$fecha_a 	= '';
	$where 		= '';
	$whereCl 	= '';
	$whereCon 	= '';
	$wherePago 	= '';

	if(empty($_REQUEST['busqueda']) && empty($_REQUEST['fecha_de']) && empty($_REQUEST['fecha_a']))
	{
		header("location: index.php");
	}

	if(!empty($_REQUEST['busqueda'])){

		$busqueda = strtolower($_REQUEST['busqueda']);
		$where ="p.id_pedido = '$busqueda' ";
		$buscar = 'busqueda='.$busqueda;

		//Buscar Por contacto
		$querySerchContacto = mysqli_query($conection,"SELECT id_contacto
												FROM contacto_pedido
												WHERE (nombre LIKE '%$busqueda%' OR telefono LIKE '%$busqueda%')
												ORDER BY nombre DESC ");

		$resultSearchContacto = mysqli_num_rows($querySerchContacto);
		if($resultSearchContacto > 0){
			while ($arrSerarchContacto = mysqli_fetch_assoc($querySerchContacto)){
				$idContactoSearch = $arrSerarchContacto['id_contacto'];
				$whereCon .= ' OR p.contacto_id LIKE '.$idContactoSearch. ' ';
			}
		}
		//Buscar Por Tipo de pago
		$queryPago = mysqli_query($conection,"SELECT id_tipopago
											FROM tipo_pago
											WHERE tipo_pago LIKE '%$busqueda%' AND estatus != 10
											ORDER BY tipo_pago DESC ");
		$resultPago = mysqli_num_rows($queryPago);
		if($resultPago > 0){
			while ($arrSerarchPago = mysqli_fetch_assoc($queryPago)){
				$idPago = $arrSerarchPago['id_tipopago'];
				$wherePago .= ' OR p.tipopago_id LIKE '.$idPago. ' ';
			}
		}
	}

	if(!empty($_REQUEST['fecha_de']) && !empty($_REQUEST['fecha_a'])){
		$fecha_de = $_REQUEST['fecha_de'];
		$fecha_a = $_REQUEST['fecha_a'];

		$buscar = '';
		//$search_proveedor = $_REQUEST['proveedor'];
		if($fecha_de > $fecha_a){
			header("location: index.php");
		}else if($fecha_de == $fecha_a){

			$where = "fecha LIKE '$fecha_de%'";
			$buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
		}else{
			$where = "fecha BETWEEN '$fecha_de' AND '$fecha_a'";
			$buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";

		}
	}

	//Paginador
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM pedido as p WHERE $where $whereCon $wherePago ");
	$result_register = mysqli_fetch_assoc($sql_registe);
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

	$queryExport = "SELECT p.id_pedido,
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
						WHERE  $where $whereCon $wherePago and p.estatus != 10
					  	ORDER BY p.id_pedido DESC LIMIT $desde,$por_pagina";

	$queryPedido = "SELECT p.id_pedido,
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
						WHERE  $where $whereCon $wherePago and p.estatus != 10
					  	ORDER BY p.id_pedido DESC LIMIT $desde,$por_pagina";

	$query = mysqli_query($conection,$queryPedido);
	$result = mysqli_num_rows($query);
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Buscar pedido</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">

		<h1><i class="fas fa-user"></i> Lista de pedido</h1>
		<?php if( $result > 0  and ($_SESSION['rol'] == 1 or $_SESSION['rol'] == 2)){ ?>
		<form action="exportar.php" method="post" class="formExport" >
			<input type="hidden" name="exportFilter" id="exportFilter" value="<?php echo $queryExport; ?>">
			<button type="submit" class="bntExport">  <i class="fas fa-file-excel"></i> Exportar Filtro</button>
		</form>
		<?php } ?>
		<a href="index.php" class="btnRefresh"><i class="fas fa-sync-alt" title="Actualizar"></i></a>
		<form action="buscar_pedido.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar venta" value="<?php echo $busqueda; ?>">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
		<div>
			<h5>Buscar por Fecha</h5>
			<form action="buscar_pedido.php" method="get" class="form_search_date">
				<label>De: </label>
				<input type="date" name="fecha_de" id="fecha_de" value="<?php echo $fecha_de; ?>" required>
				<label> A </label>
				<input type="date" name="fecha_a" id="fecha_a" value="<?php echo $fecha_a; ?>" required>
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
					<li><a href="?pagina=<?php echo 1; ?>&<?php echo $buscar; ?>"><i class="fas fa-step-backward"></i></a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>&<?php echo $buscar; ?>"><i class="fas fa-backward"></i></a></li>
				<?php
					}
					for ($i=1; $i <= $total_paginas; $i++) {
						# code...
						if($i == $pagina)
						{
							echo '<li class="pageSelected">'.$i.'</li>';
						}else{
							echo '<li><a href="?pagina='.$i.'&'.$buscar.'">'.$i.'</a></li>';
						}
					}

					if($pagina != $total_paginas)
					{
				 ?>
					<li><a href="?pagina=<?php echo $pagina + 1; ?>&<?php echo $buscar; ?>"><i class="fas fa-forward"></i></a></li>
				<li><a href="?pagina=<?php echo $total_paginas; ?>&<?php echo $buscar; ?>"><i class="fas fa-step-forward"></i></a></li>
				<?php } ?>
				</ul>
			</div>
		<?php } ?>
	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>