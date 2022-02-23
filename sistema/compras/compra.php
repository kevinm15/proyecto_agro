<?php
	$subtotal 	= 0;
	$impuesto 	= 0;
	$tl_sniva   = 0;
	$total 		= 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Compra</title>
    <style type="text/css" media="screen">
    	*{
    		box-sizing: border-box;
    		margin: 0;
    		padding: 0;
    	}
    	table{
    		margin: 10px auto;
    		border: 0;
			border-collapse: collapse;
    	}
    	table th{
    		color: #FFF;
    		background-color: #06234F;
			text-align: center;
			border: 1px solid #CCC;
			padding: 5px;
			font-family: 'arial';
			font-size: 11pt;
			font-weight: bold;
			border-left: 0;
			border-right: 0;
			border-top: 0;
			text-align: center;
    	}
    	table td{
    		border: 1px solid #CCC;
    		padding: 7px;
    	}
    	table label{
    		font-size: 10pt;
    		font-family: 'arial';
    		text-align: right;
    		display: block;
    		font-weight: bold;
    	}
    	table h2{
			color: #FFF;
			font-size: 11pt;
			font-family: 'arial';
			letter-spacing: 1px;
	   	}
    	table p{
    		font-size: 10pt;
    		font-family: 'arial';
    	}
    	.titleTable{
    		background: #080c48;
			text-align: center;
    		padding: 5px;
    	}
    	#detalle_compra td{
    		font-size: 9pt;
    		font-family: 'arial';
    	}
    	.textcenter{
    		text-align: center;
    	}
    	.textright{
    		text-align: right;
    	}
    	.borderLB{
    		border-left: 0;
    		border-bottom: 0;
    		border-top: 0;
    	}
    	.borderB{
    		border-bottom: 1px solid #CCC;
    	}
    	.btnClose{
    		cursor: pointer;
		    color: #FFF;
		    background: #ff4545;
		    border: 0;
		    padding: 10px 25px;
		    border-radius: 5px;
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

<div id="page_compra">
	<table id="datos_compra" width="900" border="1">
		<tr class="titleTable">
			<td colspan="10" style="text-align: center;"><h2>DETALLE DE COMPRA</h2></td>
		</tr>
		<tr>
			<td width="120" style="text-align: right;"><label>Proveedor:</label></td>
			<td colspan="5" style="text-align: left;"><p><?php echo $compra['proveedor']; ?></p></td>
			<td width="100" style="text-align: right;"><label>Teléfono:</label></td>
			<td width="120" style="text-align: left;"><p><?php echo $compra['telefono']; ?></p></td>
			<td width="50" style="text-align: right;"><label><?= IDENTIFICACION_TRIBUTARIA ?>:</label></td>
			<td width="110" style="text-align: left;"><p><?php echo $compra['nit']; ?></p></td>
		</tr>
		<tr>
			<td style="text-align: right;"><label>Dirección:</label></td>
			<td colspan="5" style="text-align: left;"><p><?php echo $compra['direccion']; ?></p></td>
			<td style="text-align: right;"><label>Contacto:</label></td>
			<td colspan="3" style="text-align: left;"><p><?php echo $compra['contacto']; ?></p></td>
		</tr>
		<tr>
			<td style="text-align: right;"><label>No. compra:</label></td>
			<td width="100" style="text-align: left;"><p><?php echo $compra['id_compra']; ?></p></td>
			<td width="130" style="text-align: right;"><label>Fecha compra:</label></td>
			<td width="100" colspan="2" style="text-align: left;"><p><?php echo $compra['fecha']; ?></p></td>

			<td width="100" colspan="2" style="text-align: right;"><label>Tipo Documento:</label></td>
			<td width="100" colspan="3" style="text-align: left;"><p><?php echo $compra['documento']; ?></p></td>
		</tr>
		<tr>
			<td width="130" style="text-align: right;"><label>No. Documento:</label></td>
			<td width="100" style="text-align: left;"><p><?php echo $compra['no_documento']; ?></p></td>
			<td width="100" style="text-align: right;"><label>Serie:</label></td>
			<td width="100" style="text-align: left;"><p><?php echo $compra['serie']; ?></p></td>
			<td width="100" colspan="3" style="text-align: right;"><label>Tipo pago:</label></td>
			<td colspan="3" style="text-align: left;"><p><?php echo $compra['tipo_pago']; ?></p></td>
		</tr>
	</table>

	<table style="margin-bottom: 2px;">
		<tr id="header_table">
			<th style="width: 70px; text-align: center;" ><p>Cant.</p></th>
			<th style="width: 580px;" class="textleft"><p>Descripción</p></th>
			<th style="width: 120px; text-align: right;" ><p>Precio</p></th>
			<th style="width: 120px; text-align: right;" ><p>Total</p></th>
			<th style="width: 10px;"></th>
		</tr>
	</table>

	<table id="detalle_productos">
			<?= $detalleTabla; ?>
			<?= $detalleTotales; ?>
	</table>
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
</div>
<div class="textcenter">
	<button type="button" class="btnClose" onclick="colseWindow('page_compra')">&nbsp; Cerrar &nbsp;</button>
	<button type="button" class="btnPrint" onclick="printDiv('page_compra')"> Imprimir </button>
</div>
</body>
<script type="text/javascript">
	function printDiv(divContent) {
	    /*var contenido= document.getElementById(divContent).innerHTML;
	    var contenidoOriginal= document.body.innerHTML;
	    document.body.innerHTML = contenido;
	    window.print();
	    document.body.innerHTML = contenidoOriginal;*/

	    var ficha = document.getElementById(divContent);
		var ventimp = window.open(' ', 'popimpr');
		ventimp.document.write( ficha.innerHTML );
		ventimp.document.close();
		ventimp.print( );
		ventimp.close();
	}

	function colseWindow(divContent) {

		  /*var ficha = document.getElementById(divContent);
		  var ventimp = window.open(' ', 'popimpr');
		  ventimp.document.write( ficha.innerHTML );
		  ventimp.document.close();
		  ventimp.print( );
		  ventimp.close();*/

	     /*var contenido= document.getElementById(divContent).innerHTML;
	     var contenidoOriginal= document.body.innerHTML;
	     document.body.innerHTML = contenido;
	     window.print();
	     document.body.innerHTML = contenidoOriginal;*/
	    window.close();
	}
</script>
</html>