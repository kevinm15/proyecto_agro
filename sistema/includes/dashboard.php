<?php
		$idUser = $_SESSION['idUser'];
		//Datos usuario
		$query_login = mysqli_query($conection,"SELECT u.idusuario,u.dpi,u.nombre,u.telefono,u.correo,u.usuario,u.estatus,
																r.idRol,r.rol
																FROM usuario u
																INNER JOIN rol r
																ON u.rol = r.idrol
																WHERE u.idusuario=".$_SESSION['idUser']);
		$arrInfoLogin = mysqli_fetch_assoc($query_login);
		//Datos DASHBOARD
		if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
		{
			$intUser = 0;
		}else{
			$intUser = $idUser;
		}
		$data_dash = array();
		$query_dash = mysqli_query($conection,"CALL data_dashboard('$fechaActual','$intUser');");
		$result_das = mysqli_num_rows($query_dash);
		if($result_das > 0){
			$data_dash	= mysqli_fetch_assoc($query_dash);
			mysqli_close($conection);
		}

		//Ventas del día
		$efectivo = isset($data_dash['venta_dia_efectivo']) ? $data_dash['venta_dia_efectivo'] : '0';
		$tarjeta = isset($data_dash['venta_dia_tarjeta']) ? $data_dash['venta_dia_tarjeta'] : '0';
		$totalVentasDia = SIMBOLO_MONEDA.' '.round($efectivo + $tarjeta, 2);

		include "../conexion.php";
		//Ventas del año por mes
		$arrVentaMeses = array();
		$arrMeses = fntMeses();
		if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
		{
			$query_vmeses 	= mysqli_query($conection,"CALL ventas_mensual($anio,$mes);");
		}else{
			$query_vmeses 	= mysqli_query($conection,"CALL ventas_mensual_user($anio,$mes,$idUser);");
		}
		$result_vmeses = mysqli_num_rows($query_vmeses);
		if($result_vmeses > 0){
			$i = 0;
			while ($vnt_mes = mysqli_fetch_assoc($query_vmeses)) {
				# code...
				$vnt_mes['mes']= $arrMeses[$i];
				if($vnt_mes['cant_ventas'] == 0)
				{
					$vnt_mes['anio']= $anio;
					$vnt_mes['total']= 0;
				}
				array_push($arrVentaMeses,$vnt_mes);
				$i++;
			}
			$totalVentasAnio = 0;
			for ($tl=0; $tl < count($arrVentaMeses) ; $tl++) {
				$totalVentasAnio += $arrVentaMeses[$tl]['total'];
			}
		}

		//Ventas del mes por día
		include "../conexion.php";
		if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
		{
			$were_gmes = '';
		}else{
			$were_gmes = ' AND usuario = '.$idUser;

		}
		$dias = cal_days_in_month(CAL_GREGORIAN,$mes, 2019); // 31
		//$fechaInicio = $anio.'-'.$mes.'-1';
		//$fechaFinal = $anio.'-'.$mes.'-'.$dias;
		$totalVentasMes = 0;
		$arrVentaDias = array();
		$n_dia = 1;
		for ($dd=0; $dd < $dias ; $dd++) { 
			//echo $dd.'<br>';
			$fechaVenta = $anio.'-'.$mes.'-'.$n_dia;
			$queryVentasMes = "SELECT DAY(fecha) AS dia,COUNT(nofactura) AS cantidad,SUM(totalfactura) AS total FROM factura WHERE fecha = '$fechaVenta' AND estatus=1 $were_gmes ";
			$query_vdias = mysqli_query($conection,$queryVentasMes);
			$vnt_dia 	 = mysqli_fetch_assoc($query_vdias);
			$vnt_dia['dia'] = $n_dia;
			$vnt_dia['total'] = ($vnt_dia['total'] == '') ? 0 : $vnt_dia['total'];
			$totalVentasMes += $vnt_dia['total'];
			array_push($arrVentaDias, $vnt_dia);
			$n_dia++;
		}

		//Vendedores del mes
		$query_v_mes = "SELECT f.usuario,u.nombre,COUNT(f.nofactura) as cant, SUM(f.totalfactura) AS total
								FROM factura f
								INNER JOIN usuario u
								ON
								f.usuario = u.idusuario
								WHERE MONTH(f.fecha) = $mes and YEAR(f.fecha) = $anio and f.estatus = 1
								GROUP BY f.usuario
								ORDER BY total DESC
								LIMIT 0,10 ";
		$query_v_mes  = mysqli_query($conection,$query_v_mes);
		$result_v_mes = mysqli_num_rows($query_v_mes);

		//últimas ventas
		$query_ventas = "SELECT f.nofactura,
							 f.fecha,
							 f.totalfactura,
							 f.codcliente,
							 f.estatus,
							 u.nombre as vendedor,
							 cl.nombre as cliente,
							 tp.tipo_pago
						FROM factura f
						INNER JOIN usuario u
						ON f.usuario = u.idusuario
						INNER JOIN cliente cl
						ON f.codcliente = cl.idcliente
						INNER JOIN tipo_pago tp
						ON f.tipopago_id = tp.id_tipopago
						WHERE f.estatus = 1
					  	ORDER BY f.nofactura DESC LIMIT 0,10";
		$query_u_ventas  = mysqli_query($conection,$query_ventas);
		$result_u_ventas = mysqli_num_rows($query_u_ventas);

		//Compras del año por mes
		$arrVentasCompras = array();
		for ($q=1; $q <= 12 ; $q++) {
			# code...
			$arrData = array('anio'=>'','no_mes'=>'','mes'=>'','compra'=>'','venta'=>'');
			$queryCompras = "SELECT {$anio} as anio, {$q} as mes, SUM(total) as compra FROM compra where MONTH(fecha_compra) = {$q} AND YEAR(fecha_compra) = {$anio} AND estatus = 1 GROUP by MONTH(fecha_compra)";
			$query_compra  = mysqli_query($conection,$queryCompras);
			$numCompras = mysqli_num_rows($query_compra);

			$queryVentas = "SELECT {$anio} as anio, {$q} as mes, SUM(totalfactura) as venta FROM factura where MONTH(fecha) = {$q} AND YEAR(fecha) = {$anio} AND estatus = 1 GROUP by MONTH(fecha)";
			$query_venta  = mysqli_query($conection,$queryVentas);
			$numVentas = mysqli_num_rows($query_venta);

			$arrCompra = mysqli_fetch_assoc($query_compra);
			$arrDataVentas = mysqli_fetch_assoc($query_venta);

			$arrData['mes']= $arrMeses[$q-1];
			if($numCompras == 0)
			{
				$arrData['anio'] = $anio;
				$arrData['no_mes'] = $q;
				$arrData['compra'] = 0;
			}else{
				$arrData['anio'] = $arrCompra['anio'];
				$arrData['no_mes'] = $arrCompra['mes'];
				$arrData['compra'] = $arrCompra['compra'];
			}

			if($numVentas == 0)
			{
				$arrData['venta'] = 0;
			}else{
				$arrData['venta'] = $arrDataVentas['venta'];
			}
			array_push($arrVentasCompras,$arrData);
		}
?>