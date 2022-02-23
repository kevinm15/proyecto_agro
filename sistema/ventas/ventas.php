<?php
	session_start();
	include "../../conexion.php";

	//Paginador
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM factura");
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
	//factura_serie = número de factura por serie
	$query = mysqli_query($conection,"SELECT f.nofactura,f.factura_serie,
											 DATE_FORMAT(f.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(f.dateadd,'%H:%i:%s') as  hora,
											 f.totalfactura,
											 f.codcliente,
											 f.estatus,
											 u.nombre as vendedor,
											 cl.nit,
											 cl.correo,
											 cl.nombre as cliente,
											 tp.tipo_pago,
											 s.prefijo,s.ceros
										FROM factura f
										INNER JOIN usuario u
										ON f.usuario = u.idusuario
										INNER JOIN cliente cl
										ON f.codcliente = cl.idcliente
										INNER JOIN tipo_pago tp
										ON f.tipopago_id = tp.id_tipopago
										INNER JOIN facturas s
										ON f.serieid = s.idserie
										WHERE f.estatus != 10
									  	ORDER BY f.nofactura DESC LIMIT $desde,$por_pagina
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

		<h1><i class="far fa-file-alt"></i> Lista de ventas</h1>
		<a href="nueva_venta.php" class="btn_new"><i class="fas fa-plus"></i></i> Nueva venta</a>
		<?php if( $result > 0  and ($_SESSION['rol'] == 1 or $_SESSION['rol'] == 2)){ ?>
		<form action="exportar.php" method="post" class="formExport" >
			<button type="submit" class="bntExport"> <i class="fas fa-file-excel"></i> Exportar todo</button>
		</form>
		<?php } ?>
		<a href="index.php" class="btnRefresh"><i class="fas fa-sync-alt" title="Actualizar"></i></a>
		<form action="buscar_venta.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar venta">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
		<div>
			<h5>Buscar por Fecha</h5>
			<form action="buscar_venta.php" method="get" class="form_search_date">
				<label>De: </label>
				<input type="date" name="fecha_de" id="fecha_de" required>
				<label> A </label>
				<input type="date" name="fecha_a" id="fecha_a" required>
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

				while ($data = mysqli_fetch_array($query)) {
					$venta_c = "venta_".$data["nofactura"];
					$idCliente = "cliente_".$data["codcliente"];
					$idventacript = encrypt($venta_c,$data["codcliente"]);
					$idclientecipt = encrypt($idCliente,$idventacript);

					if($data["estatus"] == 1){
						$estado = '<span class="pagada">Pagado</span>';
					}else{
						$estado = '<span class="anulada">Anulado</span>';
					}
			?>
				<tr id="row_<?php echo $data["nofactura"]; ?>">
					<td><?php echo $data["nofactura"]; ?></td>
					<td><?php echo $data["prefijo"].'-'.formatFactura($data["factura_serie"],$data["ceros"]); ?></td>
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
						<?php if($data["correo"] == '' || $data["correo"] == '-'){ ?>

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