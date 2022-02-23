<?php

	$subtotal 	= 0;
	$iva 	 	= 0;
	$impuesto 	= 0;
	$tl_sniva   = 0;
	$total 		= 0;
 	//print_r($arrayData); 
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Factura</title>
    <style type="text/css" media="screen">
    	p{
    		margin: 0;
    		font-size: 13px;
    	}
    	hr{
    		height: 0;
    		border: 0;
    		border-bottom: 1px solid #111;
    		border-style: dashed;
    		padding: 0px;
    		margin: 0px;
    	}
    	#datos_venta, #venta_detalle{
    		width: 300px;
    		margin: auto;
    	}
    	.btnPrint{
    		background: #26a442;
			color: #FFF;
			border: 0;
			padding: 8px 25px;
			font-size: 11pt;
			letter-spacing: 2px;
			border-radius: 5px;
			cursor: pointer;
			margin-top: 13px;
	 	}
    </style>
</head>
<body>
<div id="content_ticket">
	<table id="datos_venta">
		<tr>
			<td style="text-align: center;">
				<img style="width: 70px; margin: auto;" class="logo" src="<?php echo $base_url.'/sistema/img/'.LOGO_EMPRESA; ?>" alt="logo">				
			</td>
		</tr>
		<tr><td style="text-align: center; font-size: 15px; padding: 0; font-weight: bold;"><?php echo NOMBRE_EMPESA; ?></td></tr>
		<tr><td style="text-align: center; font-size: 12px; padding: 0; "><?php echo RAZONSOCIAL_EMPESA; ?></td></tr>
		<tr><td style="text-align: center; font-size: 12px; padding: 0; "><?php echo DIRECCION_EMPRESA; ?></td></tr>
		<tr><td style="text-align: center; font-size: 12px; padding: 0; "><?php echo IDENTIFICACION_TRIBUTARIA.': '.NIT_EMPESA; ?></td></tr>
		<tr><td style="text-align: center; font-size: 12px; padding: 0; ">Teléfono: <?php echo TELEFONO_EMPRESA; ?></td></tr>
		<tr><td style="text-align: center; font-size: 12px; padding: 0; ">Email: <?php echo EMAIL_EMPRESA; ?></td></tr>
		<tr><td style="text-align: center; font-size: 12px; padding: 0; ">Web: <?php echo WEB_EMPRESA; ?></td></tr>
		<tr><td style="text-align: center; font-size: 11px; padding: 0; ">CAI: <?php echo $factura['cai']; ?></td></tr>
		<tr><td style="text-align: center; font-size: 11px; padding: 0; ">Periodo Factura: Del<br><?php echo $factura['periodo_inicio'].' al '.$factura['periodo_fin']; ?></td></tr>
		<tr>
			<td style="text-align: center;padding: 0;font-size: 11px; ">
					No. Factura. <strong><?php echo $factura['prefijo'].'-'.formatFactura($factura['factura_serie'],$factura['ceros']); ?></strong>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;padding: 0;font-size: 11px; ">
				Rango facturación<br>Del <?php echo $factura['prefijo'].'-'.formatFactura($factura['no_inicio'],$factura['ceros']); ?> al <?php echo $factura['prefijo'].'-'.formatFactura($factura['no_fin'],$factura['ceros']); ?>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;padding: 0;font-size: 11px; ">
				Fecha: <?php echo $factura['fecha']; ?> --- Hora: <?php echo $factura['hora']; ?>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;padding: 0;font-size: 11px; ">
				Vendedor: <?php echo $factura['vendedor']; ?>
			</td>
		</tr>
		<tr>
			<td><?php echo $anulada; ?></td>
		</tr>
		<tr><td style="text-align: center; font-size: 12px; ">DATOS DEL CLIENTE</td></tr>
		<tr><td ><hr></td></tr>
		<tr><td style="font-size: 12px; padding: 0; "><?php echo IDENTIFICACION_TRIBUTARIA.': '.$factura['nit']; ?></td></tr>
		<tr><td style="font-size: 12px; padding: 0; ">Nombre: <?php echo $factura['nombre']; ?></td></tr>
		<tr><td style="font-size: 12px; padding: 0; ">Dirección: <?php echo $factura['direccion']; ?></td></tr>
		<tr><td style="font-size: 12px; padding: 0; ">Tipo de pago: <?php echo $factura['tipo_pago']; ?></td></tr>
	</table>
	<table id="venta_detalle">
			<tr>
				<td style="text-align: center; font-size: 12px; " colspan="4">DESCRIPCION DE COMPRA</td>
			</tr>
			<tr><td colspan="4"><hr></td></tr>
			<tr>
				<th width="50px" style="font-size: 12px; padding: 0; text-align: center; ">Cant.</th>
				<th width="215px" style="font-size: 12px; padding: 0; text-align: center; ">Descripción</th>
				<th width="80px" style="font-size: 12px; padding: 0; text-align: center; ">Precio</th>
				<th width="80px" style="font-size: 12px; padding: 0; text-align: right; "> Total</th>
			</tr>
			<tbody id="detalle_productos">
				<?= $detalleTabla; ?>
			</tbody>
			<tfoot id="detalle_totales">
				<tr><td colspan="4"><hr class="hr"></td></tr>
				<?= $detalleTotales; ?>
				<tr>
					<td colspan="4" style="padding-top: 10px;">
						<p style="font-size: 7pt;">TOTAL EN LETRAS: 
						<?php
						$monto = number_format($totalG,2,".","");
						$totalLetras = montoLetras("{$monto}");
						echo $totalLetras; 
						?>
						</p>
					</td>
				</tr>
				<tr>
					<td colspan="4" style="padding: 0; text-align: center; font-size: 12px; ">No. Artículos: <?php echo $cantArticulos; ?></td>
				</tr>
				<tr>
					<td colspan="4" style="padding: 0; text-align: center; font-size: 12px; ">No. filas: <?php echo $result_detalle; ?></td>
				</tr>
				<tr>
					<td colspan="4" style="font-size: 12px; padding:0px;">Producto Exento: E</td>
				</tr>
				<tr>
					<td colspan="4" style="font-size: 12px; padding:0px;">Producto Grabado: G</td>
				</tr>
				<tr>
					<td colspan="4" style="font-size: 12px; padding: 0; text-align: center; ">Si usted tiene preguntas sobre esta factura, <br>pongase en contacto con nombre, teléfono y Email</td>
				</tr>
				<tr>
					<td colspan="4" style="font-size: 12px; padding: 0; text-align: center; ">¡GRACIAS POR TU COMPRA!</td>
				</tr>
		</tfoot>
	</table>
</div>
<div style="text-align: center;">
	<button type="button" class="btnPrint" onclick="printDiv('content_ticket')"> Imprimir </button>
</div>
<script type="text/javascript">
	function printDiv(divContent) {
	    var contenido= document.getElementById(divContent).innerHTML;
	    var contenidoOriginal= document.body.innerHTML;
	    document.body.innerHTML = contenido;
	    window.print();
	    document.body.innerHTML = contenidoOriginal;
	}
</script>
</body>
</html>