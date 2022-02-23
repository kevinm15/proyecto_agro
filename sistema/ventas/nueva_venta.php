<?php
	session_start();
	include "../../conexion.php";
	//Serie
	$query_facturas = mysqli_query($conection,"SELECT * FROM facturas WHERE status = 1 ");
	$infoFacturas = mysqli_fetch_assoc($query_facturas);
	//Tipo Pago
	$query_tipopago = mysqli_query($conection,"SELECT * FROM tipo_pago WHERE estatus = 1
																ORDER BY id_tipopago ASC");
	$result_tipopago = mysqli_num_rows($query_tipopago);
 ?>

<?php //echo md5('1'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Nueva venta</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<?php 

		if(empty($infoFacturas))
		{
			echo "<div class='textcenter' style='color:red;'><p><strong>Active la serie de facturación para procesar ventas.</strong><p></div>";die();
		}else{

			$fecha_inicio = $infoFacturas['periodo_inicio']; 
			$fecha_fin = $infoFacturas['periodo_fin'];
			$fecha = date('Y-m-d');

			if(!check_range($fecha_inicio, $fecha_fin, $fecha))
			{
				echo "<div class='textcenter' style='color:red;'><p><strong>La fecha {$fecha} no está en el periodo de facturación.</strong><p></div>";die();
			}
		}

		?>
		<div class="title_page">
			<h1><i class="far fa-file-alt"></i> Nueva Venta</h1>
		</div>


		<section id="containerDataVenta">
			<div>
				<div class="containerTable" style="display: none;">
					<table class="tbl_venta">
						<thead>
							<tr>
								<th width="100px">Código</th>
								<th>Producto</th>
								<th>Existencia</th>
								<th width="100px">Cantidad</th>
								<th class="textright">Precio</th>
								<th class="textright">Precio Total</th>
								<th> Acción</th>
							</tr>
							<tr>
								<td><input type="text" name="txt_cod_producto" id="txt_cod_producto"><input type="hidden" name="hidCodProducto" id="hidCodProducto"></td>
								<td id="txt_descripcion">-</td>
								<td id="txt_existencia">-</td>
								<td><input type="text" name="txt_cant_producto" id="txt_cant_producto" value="0" min="1" disabled></td>
								<td id="txt_precio" class="textright">0.00</td>
								<td id="txt_precio_total" class="textright">0.00</td>
								<td class="textcenter"> <a href="#" id="add_product_venta" class="carAdd"><i class="fas fa-cart-plus"></a></i></td>
							</tr>
						</thead>
					</table>
				</div>
				<div>
					<input type="text" name="txtSearchPro" id="txtSearchPro" placeholder="Nombre del producto">
				</div>
				<div class="containerTable listProSearch">
					<table class="tbl_venta">
						<thead>
							<tr>
								<th>Código</th>
								<th>Producto</th>
								<th>Marca</th>
								<th>Precio</th>
								<th>Existencia</th>
								<th width="70px">Cantidad</th>
								<th class="textcenter">Acción</th>
							</tr>
						</thead>
						<tbody id="tbtlProSearch">
							<!-- CONTENIDO AJAX -->
						</tbody>
					</table>
				</div><br>
				<div class="datos_cliente">
					<div class="action_cliente">
						<h4>Datos del Cliente</h4>
						<a href="#" class="btn_new btn_new_cliente"><i class="fas fa-user-plus"></i> Nuevo cliente</a>
					</div>
					<form name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos">
						<input type="hidden" name="action" value="addCliente" >
						<input type="hidden" id="idcliente" name="idcliente" value="" required>
						<div class="wd15">
							<label><?php echo IDENTIFICACION_TRIBUTARIA; ?></label>
							<input type="text" name="nit_cliente" id="nit_cliente">
						</div>
						<div class="wd35">
							<label>Nombre</label>
							<input type="text" name="nom_cliente" id="nom_cliente" disabled required>
						</div>
						<div class="wd15">
							<label>Teléfono</label>
							<input type="number" name="tel_cliente" id="tel_cliente" disabled required>
						</div>
						<div class="wd40">
							<label>Dirección</label>
							<input type="text" name="dir_cliente" id="dir_cliente" disabled required>
						</div>
						<div id="div_registro_cliente" class="wd100">
							<button type="submit" class="btn_save"><i class="far fa-save fa-lg"></i> Guardar</button>
						</div>
					</form>
				</div>
				<div class="datos_venta textcenter">
					<input type="hidden" name="hddidserie" id="hddidserie" value="<?= $infoFacturas['idserie'];  ?>">
					<div class="datos wd100">
						<div class="wd30">
							<label class="textleft">Forma Pago</label>
							<select name="tipo_pago" id="tipo_pago" required>
								<?php
									if($result_tipopago > 0)
									{
										while ($pago = mysqli_fetch_assoc($query_tipopago)) {
								?>
										<option value="<?php echo $pago["id_tipopago"]; ?>"><?php echo $pago["tipo_pago"] ?></option>
								<?php
											# code...
										}
									}
								?>
							</select>
						</div>
						<div class="dflex wd20">
							<div class="divDescuento">
								<label>Descuento</label>
								<input type="text" name="txtDescuento" id="txtDescuento" class="textcenter">
							</div>
						</div>
						<div class="dflex wd20">
							<div class="divDescuento">
								<label>Total</label>
								<input type="text" name="txtTotal" id="txtTotal" class="textcenter" disabled readonly>
							</div>
						</div>
						<div class="dflex divTipoPago wd30">
							<div class="divEfectivo wd60">
								<label>Monto</label>
								<input type="text" name="txtPagoEfectivo" id="txtPagoEfectivo" class="textcenter">
							</div>
							<div class="divCambio wd40">
								<label>Cambio</label>
								<span class="spnCambio"></span>
							</div>
						</div>


						<div class="wd100">
							<br>
							<div id="acciones_venta">
								<a href="#" class="btn_ok textcenter" id="btn_anular_venta"><i class="fas fa-ban"></i> Anular</a>
								<a href="#" class="btn_new textcenter" id="btn_facturar_venta" style="display: none;"><i class="far fa-edit"></i> Procesar</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div>
				<div class="containerTable">
					<table class="tbl_venta">
						<thead>
							<tr>
								<th>Código</th>
								<th colspan="2">Producto</th>
								<th>Cantidad</th>
								<th class="textright">Precio</th>
								<th class="textright">Precio Total</th>
								<th class="textcenter"> Acción</th>
							</tr>
						</thead>
						<tbody id="detalle_venta">
							<!-- CONTENIDO AJAX -->
						</tbody>
						<tfoot id="detalle_totales">
							<!-- CONTENIDO AJAX -->
						</tfoot>
					</table>
				</div>
			</div>
		</section>
	</section>
	<?php include "../includes/footer.php"; ?>

	<script type="text/javascript">
		$(document).ready(function(){
			var usuarioid = '<?php echo $_SESSION['idUser']; ?>';
			serchForDetalle(usuarioid,1);

		});
	</script>
</body>
</html>