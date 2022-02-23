<?php
	include "../../conexion.php";
	include "../includes/functions.php";
	include "../includes/config.php";
	require_once '../library/html2pdf/vendor/autoload.php';
	use Spipu\Html2Pdf\Html2Pdf;
	if(empty($_REQUEST['cl']) || empty($_REQUEST['f']))
	{
		echo "No es posible generar la factura.";
	}else{

		$idCliente = $_REQUEST['cl'];
		$idVenta = $_REQUEST['f'];

		$cliente = decrypt($idCliente, $idVenta);
		$arrCliente = explode('_',$cliente);
		$codCliente = $arrCliente[1];

		$venta = decrypt($idVenta, $codCliente);
		$arrVenta = explode('_',$venta);
		$noFactura = $arrVenta[1];
		$anulado = '';
		$query = mysqli_query($conection,"SELECT f.nofactura,f.factura_serie, DATE_FORMAT(f.fecha, '%d-%m-%Y') as fecha, DATE_FORMAT(f.dateadd,'%H:%i:%s') as  hora, f.codcliente, f.descuento, f.efectivo, f.estatus, s.cai, s.prefijo,s.ceros, DATE_FORMAT(s.periodo_inicio, '%d-%m-%Y') as periodo_inicio, DATE_FORMAT(s.periodo_fin, '%d-%m-%Y') as periodo_fin, s.no_inicio,s.no_fin,
												 v.nombre as vendedor,
												 cl.nit, cl.nombre, cl.telefono,cl.direccion,
												 tp.id_tipopago,
												 tp.tipo_pago
											FROM factura f
											INNER JOIN usuario v
											ON f.usuario = v.idusuario
											INNER JOIN facturas s
											ON f.serieid = s.idserie
											INNER JOIN cliente cl
											ON f.codcliente = cl.idcliente
											INNER JOIN tipo_pago tp
											ON f.tipopago_id = tp.id_tipopago
											WHERE f.nofactura = '$noFactura' AND f.codcliente = '$codCliente'  AND f.estatus != 10 ");

		$result = mysqli_num_rows($query);
		if($result > 0){

			$factura = mysqli_fetch_assoc($query);
			$no_factura = $factura['nofactura'];
			if($factura['estatus'] == 2){
				$anulado = ' <img class="imgAnulado" src="'.$base_url.'/sistema/factura/img/anulado.png" alt="Anulado"> ';
			}

			$query_productos = mysqli_query($conection,"SELECT p.producto,p.descripcion,dt.cantidad,dt.precio_venta,(dt.cantidad * dt.precio_venta) as precio_total,i.impuesto
														FROM factura f
														INNER JOIN detallefactura dt
														ON f.nofactura = dt.nofactura
														INNER JOIN producto p
														ON dt.codproducto = p.codproducto
														INNER JOIN impuesto i
    													ON dt.impuestoid = i.idimpuesto
														WHERE f.nofactura = '$no_factura' ");
			$result_detalle = mysqli_num_rows($query_productos);

			$detalleTabla = '';
			$detalleTotales = '';
			$sub_total  = 0;
			$total 		= 0;
			$cantArticulos = 0;
			////////////////////
			$exento = 0;
			$arrayImpuestos = array();

			if($result_detalle > 0){
				$arrData = array();
				$arrGeneral = array();
				$arrImpuestos = array();
				$impuestos =  mysqli_query($conection,"SELECT * FROM impuesto WHERE status = 1 ");
				while ($dataIM = mysqli_fetch_assoc($impuestos)){
					array_push($arrayImpuestos, $dataIM);
				}
				//dep($arrayImpuestos);exit;
				while ($data = mysqli_fetch_assoc($query_productos)){
					$cantArticulos += $data['cantidad'];
					$impuesto =  mysqli_query($conection,"SELECT * FROM impuesto WHERE impuesto = {$data['impuesto']} ");
					$desc = mysqli_fetch_assoc($impuesto);
					$key = $data['impuesto'];
					if(array_key_exists($key, $arrGeneral))
					{
						$arrProductos  = $arrGeneral[$key]['productos'];
						array_push($arrProductos, $data);
						$arrGeneral[$key]['productos'] = $arrProductos;
						$total = 0;
						for ($i=0; $i < count($arrProductos) ; $i++) {
							$tlCant = $arrProductos[$i]['cantidad'] * $arrProductos[$i]['precio_venta'];
							$total = $total + $tlCant;
						}
						$imp = $total * ($data['impuesto'] / 100);
						$subTotal = $total - $imp;
						$total = $subTotal + $imp;
						$arrGeneral[$key]['d_impuesto'] = $imp;
						$arrGeneral[$key]['d_subTotal'] = $subTotal;
						$arrGeneral[$key]['d_total'] = $total;
					}else{
						$arrProd = array($data);
						$tlCant = $data['cantidad'] * $data['precio_venta'];
						$imp = $tlCant * ($data['impuesto'] / 100);
						$subTotal = $tlCant - $imp;
						$total = $subTotal + $imp;
						$arrData = array('impuesto' =>$data['impuesto'], 'descripcion' =>$desc['descripcion'], 'd_impuesto' =>$imp, 'd_subTotal' =>$subTotal, 'd_total' =>$total, 'productos' => $arrProd);
						$arrGeneral[$key] = $arrData;
					}

					$tpi = $data['impuesto'] == 0 ? 'E' : 'G';

					//ARMAR EL DETALLE EN HTML
					$sub_total 	 = $sub_total + $data['precio_total'];
					$detalleTabla .='<tr class="item_detalle">
										<td style="width: 10%" class="textcenter"><p>'.$data['cantidad'].'</p></td>
										<td style="width: 60%" class="textleft">'.$data['producto'].'</td>
										<td style="width: 15%" class="textright"><p>'.SIMBOLO_MONEDA.'.'.formatCant($data['precio_venta']).'</p></td>
										<td style="width: 13%" class="textright"><p>'.SIMBOLO_MONEDA.'.'.formatCant($data['precio_total']).'</p></td>
										<td style="width: 2%">'.$tpi.'</td>
									</tr>';
					
				}
				//Order Array
				ksort($arrGeneral);
				//dep($arrGeneral);
				//ROW EXONERADO
				$rowExento = '<tr>
						<td colspan="3" class="textright pading2"><strong>EXENTO</strong></td>
						<td class="textright pading2"><strong>'.SIMBOLO_MONEDA.'.'.formatCant(0).'</strong></td>
					</tr>';
				if(array_key_exists(0, $arrGeneral))
				{
					$importeDesc = $arrGeneral[0]['descripcion'];
					$importeExento = $arrGeneral[0]['d_total'];
					$rowExento ='
					<tr>
						<td colspan="3" class="textright pading2"><strong>'.$importeDesc.'</strong></td>
						<td class="textright pading2"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($importeExento).'</strong></td>
					</tr>';
				}
				////////////// SUBTOTALES //////////////
				$total = 0;
				$subTotalG = 0;
				$rowImportes = "";
				$rowImpuestos = "";
				for ($imp=0; $imp < count($arrayImpuestos); $imp++) { 
					# code...
					$keyArrGeneral = $arrayImpuestos[$imp]['impuesto'];
					$varImpuesto = $keyArrGeneral;
					$importeDesc = $arrayImpuestos[$imp]['descripcion'];

					if($arrayImpuestos[$imp]['impuesto'] != 0)
					{
						$varImpuesto = $arrayImpuestos[$imp]['impuesto'];
						if(array_key_exists($keyArrGeneral, $arrGeneral))
						{
							$importeImpuesto = $arrGeneral[$keyArrGeneral]['d_impuesto'];
							$rowImpuestos .='<tr>
												<td colspan="3" class="textright pading2"><strong>'.IMPUESTO.' '.$varImpuesto.'%</strong></td>
												<td class="textright pading"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($importeImpuesto).'</strong></td>
											</tr>';
							$totalImporte = $arrGeneral[$keyArrGeneral]['d_subTotal'];
							$rowImportes .='<tr>
												<td colspan="3" class="textright pading2"><strong>'.$importeDesc.'</strong></td>
												<td class="textright pading"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($totalImporte).'</strong></td>
											</tr>';
						}else{
							$rowImpuestos .='<tr>
												<td colspan="3" class="textright pading2"><strong>'.IMPUESTO.' '.$varImpuesto.'% </strong></td>
												<td class="textright pading"><strong>'.SIMBOLO_MONEDA.'.'.formatCant(0.00).'</strong></td>
											</tr>';
							$rowImportes .='<tr>
												<td colspan="3" class="textright pading2"><strong>'.$importeDesc.'</strong></td>
												<td class="textright pading"><strong>'.SIMBOLO_MONEDA.'.'.formatCant(0.00).'</strong></td>
											</tr>';
						}
					}
					//SUB TOTAL SIN IVA
					if(array_key_exists($keyArrGeneral, $arrGeneral))
					{
						$subTotalG = $subTotalG + $arrGeneral[$keyArrGeneral]['d_subTotal'];
						//TOTAL GENERAL
						$total = $total + $arrGeneral[$keyArrGeneral]['d_total'];
						$totalG = $total - $factura['descuento'];
					}
				}
				////// ROW DESCUENTO
				$rowDescuento ='<tr>
									<td colspan="3" class="textright pading2"><strong>DESCUENTO</strong></td>
									<td class="textright"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($factura['descuento']).'</strong></td>
								</tr>';
				////// ROW DETALLE TOTALES
				$detalleTotales = '<tr>
										<td colspan="3" class="textright pading2"><strong>SUBTOTAL</strong></td>
										<td class="textright pading"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($subTotalG).'</strong></td>
									</tr>'.
									$rowDescuento.
									$rowExento.
									$rowImportes.
									$rowImpuestos.
									'<tr>
										<td colspan="3" class="textright pading2"><strong>TOTAL</strong></td>
										<td class="textright pading"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($totalG).'</strong></td>
									</tr>';
				if($factura['id_tipopago'] == 1)
				{
					$detalleTotales .='
								<tr>
									<td colspan="3" class="textright pading2"><strong>EFECTIVO</strong></td>
									<td class="textright pading"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($factura['efectivo']).'</strong></td>
								</tr>
								<tr>
									<td colspan="3" class="textright pading2"><strong>CAMBIO</strong></td>
									<td class="textright pading"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($factura['efectivo'] - $totalG).'</strong></td>
								</tr>';
				}
			}
			mysqli_close($conection);
			ob_start();
		    require_once(dirname('__FILE__').'/factura.php');
		    $html = ob_get_clean();
		    $html2pdf = new Html2Pdf('p','A4','es','true','UTF-8');
		    //letter
			$html2pdf->writeHTML($html);
			$html2pdf->output('factura_'.$noFactura.'.pdf');
			exit;
		}
	}
?>

