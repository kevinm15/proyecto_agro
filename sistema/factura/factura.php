<?php

	$subtotal 	= 0;
	$iva 	 	= 0;
	$impuesto 	= 0;
	$tl_sniva   = 0;
	$total 		= 0;
	//print_r($configuracion); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Factura</title>
    <style type="text/css" media="screen">
    	*{
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		table{
			width: 100%;
			display: block;
			border-collapse: collapse;
			margin-bottom: 15px;
		}
		thead{
			width: 100%;
			display: block;
		}
		table td div{
			display: block;
		}
		table tr{
			width: 100%;
			display: block;
		}
		p, label, span, table{
			font-family: 'arial';
			font-size: 9px;
		}
		.h2{
			font-family: 'arial';
			font-size: 16pt;
		}
		.h3{
			font-family: 'arial';
			font-size: 12pt;
			display: block;
			border-radius: 5px 5px 0 0;
			background: #0a4661;
			color: #FFF;
			text-align: center;
			padding: 3px;
			margin-bottom: 5px;
			border: 2px solid #0a4661;
		}
		.logo_factura{
			width: 30%;
		}
		.logo_factura img{
			width: 150px;
		}
		.info_empresa{
			width: 40%;
			text-align: center;
			vertical-align: top;
		}
		.info_factura{
			width: 30%;
			vertical-align: top;
		}
		.info_factura div{
			padding-bottom: 5px;
		}
		.logo_factura div, .info_factura div{
			display: block;
			width: 100%;
		}
		.info_cliente{
			width: 100%;
		}
		.datos_cliente{
			width: 100%;
		}
		.datos_cliente tr td{
			width: 50%;
		}
		.datos_cliente{
			padding: 10px 10px 0 0;
		}
		.datos_cliente p{
			display: inline-block;
		}

		.textright{
			text-align: right;
		}
		.textleft{
			text-align: left;
		}
		.textcenter{
			text-align: center;
		}
		.round{
			border-radius: 10px;
			border: 1px solid #0a4661;
		}
		.round p{
			padding: 0 15px;
		}
		#header_table th {
		    background: #058167;
		    color: #FFF;
		    padding: 5px;
		}
		#factura_detalle{
			border-collapse: collapse;
		}
		#factura_detalle tr{
			background: #CCC;
			color: #FFF;
		}
		#detalle_productos tr.item_detalle td{
			border-bottom: 1px solid #CCC;
			padding: 5px;
		}
		#detalle_totales span{
			font-family: 'arial';
		}
		.nota{
			font-size: 8px;
		}
		.pading2{
			padding: 3px;
		}
		.label_gracias{
			font-family: arial;
			font-weight: bold;
			font-style: italic;
			text-align: center;
			margin-top: 20px;
		}
		.imgAnulado{
			width: 400px;
			float: right; position: fixed; top: 0; left: 0; z-index: 9999; margin-right: 20%; margin-top: -60%;
		}
    </style>
</head>
<body>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<img style="width: 70px;" class="logo" src="<?php echo $base_url.'/sistema/img/'.LOGO_EMPRESA; ?>" alt="logo">
			</td>
			<td class="info_empresa">
					<span class="h2"><?php echo NOMBRE_EMPESA; ?></span>
					<p><?php echo RAZONSOCIAL_EMPESA; ?></p>
					<p><?php echo DIRECCION_EMPRESA; ?></p>
					<p><?php echo IDENTIFICACION_TRIBUTARIA.': '.NIT_EMPESA; ?></p>
					<p>Teléfono: <?php echo TELEFONO_EMPRESA; ?></p>
					<p>Email: <?php echo EMAIL_EMPRESA; ?></p>
					<p>Web: <?php echo WEB_EMPRESA; ?></p>
					<p>CAI: <?php echo $factura['cai']; ?> </p>
					<p>Periodo Factura: Del <?php echo $factura['periodo_inicio'].' al '.$factura['periodo_fin']; ?> </p>
					<p>Rango facturación <br>Del <?php echo $factura['prefijo'].'-'.formatFactura($factura['no_inicio'],$factura['ceros']); ?> al <?php echo $factura['prefijo'].'-'.formatFactura($factura['no_fin'],$factura['ceros']); ?></p>
			</td>
			<td class="info_factura">
				<div class="round">
					<div class="h3">Documento</div>
					<p>No. Factura: <strong><?php echo $factura['prefijo'].'-'.formatFactura($factura['factura_serie'],$factura['ceros']); ?></strong></p>
					<p>Fecha: <strong><?php echo $factura['fecha']; ?></strong></p>
					<p>Hora: <strong><?php echo $factura['hora']; ?></strong></p>
					<p>Vendedor: <strong><?php echo $factura['vendedor']; ?></strong></p>
					<p>Tipo de pago: <strong><?php echo $factura['tipo_pago']; ?></strong></p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<div class="h3">Cliente</div>
					<table class="datos_cliente">
						<tr>
							<td><p><strong><?php echo IDENTIFICACION_TRIBUTARIA ?>:</strong> <?php echo $factura['nit']; ?></p></td>
							<td><p><strong>Teléfono:</strong> <?php echo $factura['telefono']; ?></p></td>

						</tr>
						<tr>
							<td><p><strong>Nombre:</strong> <?php echo $factura['nombre']; ?></p></td>
							<td><p><strong>Dirección:</strong> <?php echo $factura['direccion']; ?></p></td>
						</tr>
					</table>
				</div>
			</td>

		</tr>
	</table>
	<table style="margin-bottom: 2px;">
		<tr id="header_table">
			<th style="width: 10%;" class="textcenter"><p>Cant.</p></th>
			<th style="width: 60%;" class="textleft"><p>Descripción</p></th>
			<th style="width: 15%;" class="textright"><p>Precio</p></th>
			<th style="width: 13%;" class="textright"><p>Total</p></th>
			<th style="width: 2%;"></th>
		</tr>
	</table>

	<table id="detalle_productos">
			<?= $detalleTabla; ?>
			<?= $detalleTotales; ?>
	</table>
	<div>
		<p>TOTAL EN LETRAS: 
		<?php
		$monto = number_format($totalG,2,".","");
		$totalLetras = montoLetras("{$monto}");
		echo $totalLetras; 
		?>
		</p>
	</div>
	<table border="1">
		<tr>
			<td colspan="6" style="padding: 5px;">
				<p class="textleft">Producto Exento: E</p>
				<p class="textleft">Producto Grabado: G</p>
			</td>
			<td style="padding: 5px;">
				<p>No. Artículos: <?php echo $cantArticulos; ?></p>
				<p>No. filas: <?php echo $result_detalle; ?></p>
			</td>
		</tr>
	</table>
	<div>
		<p class="nota">Si usted tiene preguntas sobre esta factura, <br>pongase en contacto con nombre, teléfono y Email</p>
		<h4 class="label_gracias">¡Gracias por su compra!</h4>
	</div>
	<?php echo $anulado; ?>
</div>
</body>
</html>
<!-- 
<tr>
	<td><div class="textcenter" style="width: 10%"><p><?php //echo $row['cantidad']; ?></p></div></td>
	<td><div style="width: 60%"><?php //echo $row['descripcion']; ?></div></td>
	<td><div class="textright" style="width: 15%"><p><?php //echo $moneda.'.'.$row['precio_venta']; ?></p></div></td>
	<td><div class="textright" style="width: 15%"><p><?php //echo $moneda.'.'.$row['precio_total']; ?></p></div></td>
</tr> -->