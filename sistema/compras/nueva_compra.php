<?php
	session_start();
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
	{
		header("location: ../index.php");
	}
	include "../../conexion.php";

	//Extrae proveedores
	$query_proveedor = mysqli_query($conection,"SELECT * FROM proveedor WHERE estatus = 1
																ORDER BY proveedor ASC");
	$result_proveedor = mysqli_num_rows($query_proveedor);
	//Extrae documentos
	$query_documento = mysqli_query($conection,"SELECT * FROM tipo_documento WHERE estatus = 1
																ORDER BY id_tipodocumento ASC");
	$result_documentos = mysqli_num_rows($query_documento);
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
	<title>Nueva compra</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<div class="title_page">
			<h1><i class="far fa-file-alt"></i> Nueva Compra</h1>
		</div>

		<section id="containerDataVenta">
			<div>
				<div class="containerTable" >
					<table class="tbl_venta">
						<thead>
							<tr>
								<th>Código</th>
								<th colspan="2">Descripción</th>
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
			<div>
				<div class="containerTable" style="display: none;">
					<table class="tbl_venta">
						<thead>
							<tr>
								<th width="100px">Código</th>
								<th>Descripción</th>
								<th width="100px">Cantidad</th>
								<th width="100px">Precio</th>
								<th> Acción</th>
							</tr>
							<tr>
								<td><input type="text" name="txt_cod_producto_c" id="txt_cod_producto_c">
									<input type="hidden" name="hidCodProducto" id="hidCodProducto"></td>
								<td id="txt_descripcion">-</td>
								<td><input type="text" name="txt_cant_producto_c" id="txt_cant_producto_c" value="0" min="1" disabled></td>
								<td><input type="text" name="txt_precio_c" id="txt_precio_c" value="0" min="1" disabled></td>
								<td class="textcenter"> <a href="#" id="add_product_compra" class="carAdd"><i class="fas fa-cart-plus"></a></i></td>
							</tr>
						</thead>
					</table>
				</div>
					<div>
						<input type="text" name="txtSearchPro_c" id="txtSearchPro_c" placeholder="Nombre del producto">
					</div>
				<div class="containerTable listProSearch">
					<table class="tbl_venta">
						<thead>
							<tr>
								<th>Código</th>
								<th>Descripción</th>
								<th>Marca</th>
								<th title="Precio Compra Actual">P/C/A</th>
								<th title="Precio Actual">P/A</th>
								
								<th style="width: 70px;">Cantidad</th>
								<th style="width: 80px;">Precio</th>
								<th class="textcenter">Acción</th>
							</tr>
						</thead>
						<tbody id="tbtlProSearch">
							<!-- CONTENIDO AJAX -->
						</tbody>
					</table>
				</div>
				<br>
				<div class="datos_cliente">
					<div class="action_cliente">
						<h4>Datos compra</h4>
					</div>
					<form name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos">
						<input type="hidden" name="action" value="addVenta" >
						<div class="wd20">
							<label>Documento</label>
							<select name="documento_id" id="documento_id" required>
								<?php
									if($result_documentos > 0)
									{
										while ($documento = mysqli_fetch_assoc($query_documento)) {
								?>
										<option value="<?php echo $documento["id_tipodocumento"]; ?>"><?php echo $documento["documento"] ?></option>
								<?php
											# code...
										}
									}
								?>
							</select>
						</div>
						<div class="wd20">
							<label>No. Documento</label>
							<input type="number" name="no_documento" id="no_documento" required>
						</div>
						<div class="wd10">
							<label>Serie</label>
							<input type="text" name="serie" id="serie" required>
						</div>
						<div class="wd20">
							<label>Fecha compra</label>
							<input type="date" name="fecha_compra" id="fecha_compra" required>
						</div>
						<div class="wd20">
							<label>Proveedor</label>
							<select name="proveedor_id" id="proveedor_id" required>
								<option value="" selected >Seleccione</option>
								option
								<?php
									if($result_proveedor > 0)
									{
										while ($proveedor = mysqli_fetch_assoc($query_proveedor)) {
								?>
										<option value="<?php echo $proveedor["codproveedor"]; ?>"><?php echo $proveedor["proveedor"] ?></option>
								<?php
											# code...
										}
									}
								 ?>
							</select>
						</div>
					</form>
				</div>
				<div class="datos_venta textcenter">
					<div class="datos wd100">
						<div class="wd25">
							<label class="textleft">Tipo Pago</label>
							<select name="tipopago_id" id="tipopago_id" required>
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
						<div class="wd50">
							<label>Acciones</label>
							<div id="acciones_venta">
								<a href="#" class="btn_ok textcenter" id="btn_anular_venta"><i class="fas fa-ban"></i> Anular</a>
								<a href="#" class="btn_new textcenter" id="btn_facturar_compra" ><i class="far fa-edit"></i> Procesar</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</section>
	<?php include "../includes/footer.php"; ?>

	<script type="text/javascript">
		$(document).ready(function(){
			var usuarioid = '<?php echo $_SESSION['idUser']; ?>';
			serchForDetalle(usuarioid,0);

		});
	</script>
</body>
</html>