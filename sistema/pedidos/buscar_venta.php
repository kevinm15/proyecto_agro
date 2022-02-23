<?php
	session_start();
	include "../../conexion.php";

	$busqueda	= '';
	$fecha_de 	= '';
	$fecha_a 	= '';
	$where 		= '';
	$whereCl 	= '';
	$whereUs 	= '';
	$wherePago 	= '';

	if(empty($_REQUEST['busqueda']) && empty($_REQUEST['fecha_de']) && empty($_REQUEST['fecha_a']))
	{
		header("location: index.php");
	}

	if(!empty($_REQUEST['busqueda'])){

		$busqueda = strtolower($_REQUEST['busqueda']);
		$where ="f.nofactura = '$busqueda' ";
		$buscar = 'busqueda='.$busqueda;

		//Buscar Por cliente
		$querySerchCliente = mysqli_query($conection,"SELECT idcliente
												FROM cliente
												WHERE (nit LIKE '%$busqueda%') AND estatus != 10
												ORDER BY nombre DESC ");
		$resultSearchCliente = mysqli_num_rows($querySerchCliente);
		if($resultSearchCliente > 0){
			while ($arrSerarchCliente = mysqli_fetch_assoc($querySerchCliente)){
				$idClienteSearch = $arrSerarchCliente['idcliente'];
				$whereCl .= ' OR f.codcliente LIKE '.$idClienteSearch. ' ';
			}
		}
		//Buscar Por vendedor
		$querySerchUsuario = mysqli_query($conection,"SELECT idusuario
												FROM usuario
												WHERE nombre LIKE '%$busqueda%' AND estatus != 10
												ORDER BY nombre DESC ");
		$resultSearchUsuario = mysqli_num_rows($querySerchUsuario);
		if($resultSearchUsuario > 0){
			while ($arrSerarchUsuario = mysqli_fetch_assoc($querySerchUsuario)){
				$idUsuarioSearch = $arrSerarchUsuario['idusuario'];
				$whereUs .= ' OR f.usuario LIKE '.$idUsuarioSearch. ' ';
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
				$wherePago .= ' OR f.tipopago_id LIKE '.$idPago. ' ';
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
			$f_de = $fecha_de.' 00:00:00';
			$f_a = $fecha_a.' 23:59:59';
			$where = "fecha BETWEEN '$f_de' AND '$f_a'";
			$buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";

		}
	}

	//Paginador
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM factura as f WHERE $where $whereCl $whereUs $wherePago ");
	$result_register = mysqli_fetch_assoc($sql_registe);
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

	$queryExport = "SELECT f.nofactura,f.factura_serie,DATE_FORMAT(f.fecha, '%d/%m/%Y') as fecha,f.totalfactura,f.codcliente,f.estatus,
											 u.nombre as vendedor,
											 cl.nit,
											 cl.nombre as cliente,
											 tp.tipo_pago
										FROM factura f
										INNER JOIN usuario u
										ON f.usuario = u.idusuario
										INNER JOIN cliente cl
										ON f.codcliente = cl.idcliente
										INNER JOIN tipo_pago tp
										ON f.tipopago_id = tp.id_tipopago
										WHERE  $where $whereCl $whereUs $wherePago and f.estatus != 10
									  	ORDER BY f.fecha DESC ";

	$query_venta = "SELECT f.nofactura,f.factura_serie,DATE_FORMAT(f.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(f.dateadd,'%H:%i:%s') as  hora,f.totalfactura,f.codcliente,f.estatus,
											 u.nombre as vendedor,
											 cl.nit,
											 cl.nombre as cliente,
											 cl.correo,
											 tp.tipo_pago
										FROM factura f
										INNER JOIN usuario u
										ON f.usuario = u.idusuario
										INNER JOIN cliente cl
										ON f.codcliente = cl.idcliente
										INNER JOIN tipo_pago tp
										ON f.tipopago_id = tp.id_tipopago
										WHERE  $where $whereCl $whereUs $wherePago and f.estatus != 10
									  	ORDER BY f.fecha DESC LIMIT $desde,$por_pagina ";
	$query = mysqli_query($conection,$query_venta);
	$result = mysqli_num_rows($query);
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Buscar venta</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">

		<h1><i class="fas fa-user"></i> Lista de ventas</h1>
		<a href="nueva_venta.php" class="btn_new"><i class="fas fa-plus"></i></i> Nueva venta</a>
		<?php if( $result > 0  and ($_SESSION['rol'] == 1 or $_SESSION['rol'] == 2)){ ?>
		<form action="exportar.php" method="post" class="formExport" >
			<input type="hidden" name="exportFilter" id="exportFilter" value="<?php echo $queryExport; ?>">
			<button type="submit" class="bntExport">  <i class="fas fa-file-excel"></i> Exportar Filtro</button>
		</form>
		<?php } ?>
		<a href="index.php" class="btnRefresh"><i class="fas fa-sync-alt" title="Actualizar"></i></a>
		<form action="buscar_venta.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar venta" value="<?php echo $busqueda; ?>">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
		<div>
			<h5>Buscar por Fecha</h5>
			<form action="buscar_venta.php" method="get" class="form_search_date">
				<label>De: </label>
				<input type="date" name="fecha_de" id="fecha_de" value="<?php echo $fecha_de; ?>" required>
				<label> A </label>
				<input type="date" name="fecha_a" id="fecha_a" value="<?php echo $fecha_a; ?>" required>
				<button type="submit" class="btn_view"><i class="fas fa-search"></i></button>
			</form>
		</div>
	<div class="containerTable">
		<table>
			<tr>
				<th>No.</th>
				<th>No. Factura</th>
				<th>Fecha / Hora</th>
				<th><?php echo IDENTIFICACION_TRIBUTARIA; ?></th>
				<th>Cliente</th>
				<th>Vendedor</th>
				<th class="textcenter">Estado</th>
				<th class="textcenter">Tipo de pago</th>
				<th class="textright">Total Factura</th>
				<th class="textcenter">Acciones</th>
			</tr>
		<?php
			if($result > 0){

				while ($data = mysqli_fetch_assoc($query)) {
					$venta_c = "venta_".$data["nofactura"];
					$idCliente = "cliente_".$data["codcliente"];
					$idventacript = encrypt($venta_c,$data["codcliente"]);
					$idclientecipt = encrypt($idCliente,$idventacript);

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
				<tr id="row_<?php echo $data["nofactura"]; ?>">
					<td><?php echo $data["nofactura"]; ?></td>
					<td><?php echo formatFactura($data["factura_serie"]); ?></td>
					<td><?php echo $data["fecha"].' - '.$data["hora"]; ?></td>
					<td><?php echo $data["nit"]; ?></td>
					<td><?php echo $data["cliente"]; ?></td>
					<td><?php echo $data["vendedor"]; ?></td>
					<td class="estado textcenter"><?php echo $estado; ?></td>
					<td class="textcenter"><?php echo $data['tipo_pago']; ?></td>
					<td class="textright totalfactura"><span><?= SIMBOLO_MONEDA ?>.</span><?php echo formatCant($data["totalfactura"]); ?></td>

					<td class="textright">
						<div class="div_acciones">
							<!--a class="link_edit" href="factura/generaFactura.php?cl=<?php //echo $data["codcliente"]; ?>&f=<?php //echo $data['nofactura'];?>" target="_blanck"><i class="fas fa-eye"></i> Ver</a-->

							<div>
								<button class="btn_print view_ticket" type="button" cl="<?php echo $idclientecipt; ?>" f="<?php echo $idventacript;?>" title="Imprimir" ><i class="fas fa-print"></i></button>
							</div>
							<div>
								<button class="btn_view view_pdf" type="button" cl="<?php echo $idclientecipt; ?>" f="<?php echo $idventacript;?>" title="Generar PDF" ><i class="far fa-file-pdf"></i></i></button>
							</div>
						<?php if($data["correo"] == ''){ ?>

							<div class="div_factura">
								<button type="button" class="btn_anular inactive" ><i class="far fa-envelope"></i></button>
							</div>
						<?php }else{ ?>
							<div>
								<button class="btn_sendEmail send_factura" type="button" cl="<?php echo $idclientecipt; ?>" f="<?php echo $idventacript;?>" email="<?php echo $data["correo"]; ?>"  title="Enviar email" ><i class="far fa-envelope"></i></button>
							</div>
						<?php } ?>
						<?php if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){
									if($data["estatus"] == 1)
									{
						?>
							<div class="div_factura">
								<button class="btn_anular anular_factura" fac="<?php echo $data["nofactura"]; ?>" type="button" title="Anular venta"><i class="fas fa-ban"></i></button>
							</div>
						<?php 		}else{  ?>
							<div class="div_factura">
								<button type="button" class="btn_anular inactive" ><i class="fas fa-ban"></i></button>
							</div>
						<?php
						}
								}
						?>
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
<?php
	}
?>

	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>