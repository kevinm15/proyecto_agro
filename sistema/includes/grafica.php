<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Reportes</title>
</head>
<body>
	<?php 
		if($grafica == 'ventasMesDia'){
	 ?>
	<script>
		$('#graficaMes').highcharts({
				title: {
					text: 'Total Ventas del mes <?php echo SIMBOLO_MONEDA.'. '.$totalVentasMes; ?>',
					x: -20 //center
				},
				subtitle: {
					text: 'Estadistica de las ventas por dia.',
					x: -20
				},
				xAxis: {
					categories: [
					<?php for ($i=0; $i < count($arrVentaDias); $i++) {  ?>

						<?php echo "'".$arrVentaDias[$i]['dia']."',"; ?>
					<?php } ?>
					]
				},
				yAxis: {
					title: {
					text: ''
				},
				plotLines: [{
					value: 0,
					width: 1,
					color: '#CCC'
				}]
				},
				tooltip: {
					valueSuffix: ''
				},
				legend: {
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle',
					borderWidth: 0
				},
				series: [{
					name: '',
					type: 'line',
					color: '#0aa617',
					data: [
						<?php for ($i=0; $i < count($arrVentaDias); $i++) {  ?>
							<?php echo $arrVentaDias[$i]['total'].","; ?>
						<?php } ?>
					]
				}],
				plotOptions:{column:{dataLabels:{enabled:true}}}
			});
	</script>
	<?php 
		}

		if($grafica == 'ventasAnioMes'){
	 ?>
	 <script>
	 	$('#graficaAnio').highcharts({
			title: {
				text: 'Total ventas del año: <?php echo SIMBOLO_MONEDA.'. '.$totalVentasAnio; ?>',
				x: -20 //center
			},
			subtitle: {
				text: 'Estadistica de las ventas por mes.',
				x: -20
			},
			xAxis: {
				categories: [
				<?php for ($i=0; $i < count($arrVentaMeses) ; $i++) {  ?>

					<?php echo "'".$arrVentaMeses[$i]['mes']."',"; ?>
				<?php } ?>
				]
			},
			yAxis: {
				title: {
				text: ''
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
			},
			tooltip: {
				valueSuffix: ''
			},
			legend: {
				layout: 'vertical',
				align: 'right',
				verticalAlign: 'middle',
				borderWidth: 0
			},
			series: [{
				name: '',
				type: 'column',
				data: [
					<?php for ($i=0; $i < count($arrVentaMeses) ; $i++) {
						echo $arrVentaMeses[$i]['total'].","; 
					 } ?>
				]
			}],
			plotOptions:{column:{dataLabels:{enabled:true}}}
		});
	 </script>
	 <?php 
		}
		if($grafica == 'ventasComprasAnioMes'){
	?>

	<script>
		$('#graficaIngresosEgresos').highcharts({
		  chart: {
		    type: 'column'
		  },
		  title: {
		    text: 'Ingresos y egresos del año <?= $anio; ?>'
		  },
		  subtitle: {
		    text: 'Estadística de compras y ventas por mes'
		  },
		  xAxis: {
		    categories: [
		      'Ene',
		      'Feb',
		      'Mar',
		      'Abr',
		      'May',
		      'Jun',
		      'Jul',
		      'Ags',
		      'Sep',
		      'Oct',
		      'Nov',
		      'Dic'
		    ],
		    crosshair: true
		  },
		  yAxis: {
		    min: 0,
		    title: {
		      text: ''
		    }
		  },
		  tooltip: {
	
		    shared: true,
		    useHTML: true
		  },
		  plotOptions: {
		    column: {
		      pointPadding: 0.2,
		      borderWidth: 0
		    }
		  },
		  series: [{
		    name: 'Ventas',
		    data: [
		    	<?php for ($ie=0; $ie < count($arrVentasCompras) ; $ie++) {
					echo $arrVentasCompras[$ie]['venta'].","; 
				} ?>
		    ]
		  }, {
		    name: 'Compras',
		    data: [
		    	<?php for ($ei=0; $ei < count($arrVentasCompras) ; $ei++) {
					echo $arrVentasCompras[$ei]['compra'].","; 
				} ?>
		    ]

		  }]
		});
	</script>
	<?php
		}

		if($grafica == 'tableVentasComprasAnioMes'){
	?>
		<table>
			<thead>
				<tr>
					<th>Año</th>
					<th>Mes</th>
					<th class="textright">Copras</th>
					<th class="textright">Ventas</th>
				</tr>
			</thead>
			<tbody id="bodyTableIE">
				<?php
					$totalCompras = 0;
					$totalVentas = 0;

					for ($cv=0; $cv < count($arrVentasCompras) ; $cv++) {
						$totalCompras += $arrVentasCompras[$cv]['compra'];
						$totalVentas += $arrVentasCompras[$cv]['venta'];
				 ?>
				<tr>
					<td><?= $arrVentasCompras[$cv]['anio'] ?></td>
					<td><?= $arrVentasCompras[$cv]['mes'] ?></td>
					<td class="textright"><?= SIMBOLO_MONEDA.'. '.formatCant($arrVentasCompras[$cv]['compra']); ?></td>
					<td class="textright"><?= SIMBOLO_MONEDA.'. '.formatCant($arrVentasCompras[$cv]['venta']); ?></td>
				</tr>
				<?php
					}
				 ?>
			</tbody>
			<tfoot id="totalesIE">
				<tr>
					<td colspan="2" class="textright"><strong>Total:</strong></td>
					<td class="textright"><strong><?= SIMBOLO_MONEDA.'. '.formatCant($totalCompras); ?></strong></td>
					<td class="textright"><strong><?= SIMBOLO_MONEDA.'. '.formatCant($totalVentas); ?></strong></td>
				</tr>
			</tfoot>
		</table>

	<?php 
		}
	?>
</body>
</html>