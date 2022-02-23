<?php
	include "../../conexion.php";
	include "../includes/functions.php";
	include "../includes/config.php";
	require_once '../library/html2pdf/vendor/autoload.php';
	use Spipu\Html2Pdf\Html2Pdf;
	if(empty($_REQUEST['p']) || empty($_REQUEST['c']))
	{
		echo "Información no encontrada.";exit;
	}else{
		$pedido_id = intval($_REQUEST['p']);
		$strKey = $_REQUEST['c'];
		$keyGenerate = $pedido_id.'_24091989';
		$key = md5($keyGenerate);		 
		if($_REQUEST['c'] != $key)
		{
			echo "Información no encontrada";
			exit;
		}
		/*$strPedido = decrypt($strPedido,$strKey);
		$arrPedido = explode('_',$strPedido);
		$pedido_id = $arrPedido[1];*/
		$imgEstado = '';
		$query = mysqli_query($conection,"SELECT p.id_pedido, p.fecha, tp.tipo_pago, p.total, p.estatus, c.id_contacto, c.nombre, c.telefono, c.email, c.nit, c.nombre_fiscal, c.direccion
											FROM pedido p
											INNER JOIN contacto_pedido c
											ON p.contacto_id = c.id_contacto
											INNER JOIN tipo_pago tp
											ON p.tipopago_id = tp.id_tipopago
											WHERE p.id_pedido = {$pedido_id} AND p.estatus != 10 ");
		$result = mysqli_num_rows($query);
		if($result > 0){
			$pedido = mysqli_fetch_assoc($query);
			$id_pedido = $pedido['id_pedido'];
			$txtEstado ="<span style='color:blue;'>EN PROCESO</span>";
			if($pedido['estatus'] == 4){
				$imgEstado = '<img class="imgAnulado" src="'.$base_url.'/sistema/img/anulado.png" alt="Anulado"> ';
				$txtEstado ="<span style='color:red;'>ANULADO</span>";
			}else if($pedido['estatus'] == 3){
				$imgEstado = '<img class="imgAnulado" src="'.$base_url.'/sistema/img/entregado.png" alt="Entregado"> ';
				$txtEstado ="<span style='color:green;'>ENTREGADO</span>";
			}
			$query_productos = mysqli_query($conection,"SELECT p.id_pedido,pr.codproducto,pr.producto,dp.id_detalle,dp.cantidad,dp.precio_venta,(dp.cantidad * dp.precio_venta) as precio_total,i.impuesto
														FROM pedido p
														INNER JOIN detalle_pedido dp
														ON p.id_pedido = dp.pedido_id
														INNER JOIN producto pr
														ON dp.codproducto = pr.codproducto
														INNER JOIN impuesto i
    													ON dp.impuestoid = i.idimpuesto
														WHERE p.id_pedido = '$id_pedido' ");
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
					}
				}
				////// ROW DETALLE TOTALES
				$detalleTotales = '<tr>
										<td colspan="3" class="textright pading2"><strong>SUBTOTAL</strong></td>
										<td class="textright pading"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($subTotalG).'</strong></td>
									</tr>'.
									$rowExento.
									$rowImportes.
									$rowImpuestos.
									'<tr>
										<td colspan="3" class="textright pading2"><strong>TOTAL</strong></td>
										<td class="textright pading"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($total).'</strong></td>
									</tr>';
			}
			mysqli_close($conection);
			ob_start();
		    require_once(dirname('__FILE__').'/pedidoPDF.php');
		    $html = ob_get_clean();
		    $html2pdf = new Html2Pdf('p','A4','es','true','UTF-8');
		    //letter
			$html2pdf->writeHTML($html);
			$html2pdf->output('pedido_'.$pedido_id.'.pdf');
			exit;
		}
	}

 ?>