<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<?php include "includes/scripts.php"; ?>
	<title>Sistema Ventas</title>
</head>
<body>
	<?php
		include "includes/header.php";
		include "includes/dashboard.php";
	 ?>
	<section id="container">
		<div>
			<h1 class="titlePanelControl">Dashboard</h1>
		</div>
		<div class="dashboard">
		<?php

			if($_SESSION['rol'] == 1)
			{
		?>
			<a href="usuarios/">
				<i class="fas fa-users"></i>
				<div>
					<h3>Usuarios</h3>
					<span><?= $data_dash['usuarios']; ?></span>
				</div>
			</a>
		<?php
		}
		 ?>
			<a href="clientes/">
				<i class="fas fa-user"></i>
				<div>
					<h3>Clientes</h3>
					<span><?= $data_dash['clientes']; ?></span>
				</div>
			</a>
			<?php
				if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
				{
			?>
			<a href="proveedores/">
				<i class="far fa-building"></i>
				<div>
					<h3>Proveedores</h3>
					<span><?= $data_dash['proveedores']; ?></span>
				</div>
			</a>
			<?php } ?>
			<a href="productos/">
				<i class="fas fa-cubes"></i>
				<div>
					<h3>Productos</h3>
					<span><?= $data_dash['productos']; ?></span>
				</div>
			</a>
			<a href="pedidos/">
				<i class="fas fa-box"></i>
				<div>
					<div>
						<h3>Pedidos</h3>
						<span><?= $data_dash['pedidos']; ?></span>
					</div>
				</div>
			</a>
			<a href="ventas/">
				<i class="far fa-file-alt"></i>
				<div>
					<div>
						<h3>Ventas</h3>
						<span><?= $data_dash['ventas_dia']; ?></span>
					</div>
				</div>
			</a>
		</div>

		<div>
			<h1 class="titlePanelControl">Estadisticas</h1>
		</div>
		<br>
		<div id="infoReportes">
			<?php
				if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){
			?>
				<div id="m_vendedores">
					<div class="divFilter">
						<h2>Vendedores del mes</h2><div class="dflex">
							<input class="date-picker vendedoresMes" name="vendedoresMes" placeholder="Mes y Año"><button type="button" class="btnVendedoresMes"><i class="fas fa-search"></i></button>
						</div>
					</div>
					<div class="vendedores">
						<table>
							<thead>
								<tr>
									<th class="textcenter">#</th>
									<th class="textcenter">Nombre</th>
									<th>Ventas</th>
									<th>Monto</th>
								</tr>
							</thead>
							<tbody id="tblEmpreadosMes">
							<?php
								if($result_v_mes > 0){
									$no=1;
									while ($vendedor = mysqli_fetch_assoc($query_v_mes)) {
							?>
								<tr>
									<td class="textcenter"><?php echo $no; ?></td>
									<td class="textleft"><img src="img/user.png" alt="<?php echo $vendedor['nombre']; ?>" class="photouser">
									<?php echo $vendedor['nombre']; ?></td>
									<td class="textcenter"><?php echo $vendedor['cant']; ?></td>
									<td class="textright"><?php echo SIMBOLO_MONEDA.'. '.formatCant($vendedor['total']); ?></td>
								</tr>
							<?php
									$no++;
									}
								}
							 ?>
							</tbody>
						</table>
					</div>
				</div>
				<div id="u_ventas">
					<div class="divFilter">
						<h2>Últimas ventas</h2>
					</div>
					<div class="ultimas_ventas">
						<table>
							<thead>
								<tr>
									<th class="textcenter">#</th>
									<th>Ticket</th>
									<th>Vendedor</th>
									<th>Pago</th>
									<th>Monto</th>
								</tr>
							</thead>
							<tbody>
							<?php
								if($result_u_ventas > 0){
									$nv=1;
									while ($venta = mysqli_fetch_assoc($query_u_ventas)) {
							?>
								<tr>
									<td class="textcenter"><?php echo $nv; ?></td>
									<td class="textcenter"><?php echo $venta['nofactura']; ?></td>
									<td><?php echo $venta['vendedor']; ?></td>
									<td><?php echo $venta['tipo_pago']; ?></td>
									<td class="textright"><?php echo SIMBOLO_MONEDA.'. '.formatCant($venta['totalfactura']); ?></td>
								</tr>
							<?php
									$nv++;
									}
								}
							 ?>
							</tbody>
						</table>
					</div>
				</div>
			<?php } ?>
				<div id="ventas_del_dia">
					<div class="divFilter">
						<h2>Ventas del día</h2>
					</div>
					<div id="graficaDia"></div>
				</div>

				<div id="ventasMesDia">
					<div class="divFilter">
						<h2>Ventas del mes</h2><div class="dflex">
							<input class="date-picker ventasMes" name="ventasMes" placeholder="Mes y Año"><button type="button" class="btnVentasMes"><i class="fas fa-search"></i></button>
						</div>
					</div>
					<div id="graficaMesAjax">
						<div id="graficaMes"></div>
					</div>
				</div>
				<div id="ventasAnioMes">
					<div class="divFilter">
						<h2>Ventas del año</h2><div class="dflex">
							<input id="ventasAnio" class="ventasAnio" name="ventasAnio" placeholder="Año" minlength="4" maxlength="4" ><button type="button" class="btnVentasAnio"><i class="fas fa-search"></i></button>
						</div>
					</div>
					<div id="graficaAnio"></div>
				</div>
			<?php
				if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
				{
			 ?>
				<div id="grIngresosEgresos">
					<div class="divFilter">
						<h2>Ingresos y egresos del año</h2><div class="dflex">
							<input class="ventasAnio" id="grIEAnio" name="grIEAnio" placeholder="Año"><button type="button" class="btnCompraVenta" obj="grafica"><i class="fas fa-search"></i></button>
						</div>
					</div>
					<div id="graficaIngresosEgresos"></div>
				</div>
				<div id="ingressosEgresos">
					<div class="divFilter">
						<h2>Ingresos y egresos del año</h2><div class="dflex">
							<input class="ventasAnio" id="tblIEAnio" name="tblIEAnio" placeholder="Año"><button type="button" class="btnCompraVenta" obj="tabla"><i class="fas fa-search"></i></button>
						</div>
					</div>
					<div id="tblIngresosEgresos">
						<table>
							<thead>
								<tr>
									<th>Año</th>
									<th>Mes</th>
									<th class="textright">Compras</th>
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
					</div>
				</div>

			<?php
				}
			?>

		</div>
	</section>
	<?php include "includes/footer.php"; ?>


	<div>


	<script type="text/javascript">
		$(function ($) {

			$('.date-picker').datepicker( {
                closeText: 'Cerrar',
				prevText: '<Ant',
				nextText: 'Sig>',
				currentText: 'Hoy',
				monthNames: ['1 -', '2 -', '3 -', '4 -', '5 -', '6 -', '7 -', '8 -', '9 -', '10 -', '11 -', '12 -'],
				monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
		        changeMonth: true,
		        changeYear: true,
		        showButtonPanel: true,
		        dateFormat: 'MM yy',
		        onClose: function(dateText, inst) {
		            $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
		        }
		    });

			$('#graficaDia').highcharts({
				title: {
					text: 'Total ventas del día: <?php echo $totalVentasDia; ?>',
					x: -20 //center
				},
				subtitle: {
					text: 'Ventas del dia en Efectivo y Tarjeta.',
					x: -20
				},
				xAxis: {
					categories: ['Ventas del día: <?php echo $totalVentasDia; ?>']
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
				series: [
						{type: 'column',name: 'Efectivo',data: [<?php echo $efectivo; ?>]},
						{type: 'column',name: 'Tarjeta',data: [<?php echo $tarjeta; ?>]},
						],
				plotOptions:{column:{dataLabels:{enabled:true}}}
			});

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
			<?php 
			if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
			{
			?>
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

			<?php 
			}
			?>
		});
	</script>
</body>
</html>