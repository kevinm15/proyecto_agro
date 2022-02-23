<?php
	//print_r($_REQUEST);
	//exit;
	//echo base64_encode('2');
	//exit;
	session_start();
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
	{
		header("location: ../index.php");
	}
	include "../../conexion.php";
	include "../includes/functions.php";

	if(empty($_REQUEST['cmp']))
	{
		echo "No disponible";
	}else{
		$id_compra = intval($_REQUEST['cmp']);
		$anulada = '';
		$query = mysqli_query($conection,"SELECT c.id_compra,DATE_FORMAT(c.fecha_compra, '%d/%m/%Y') as fecha,d.documento,c.no_documento,c.proveedor_id,c.serie,p.proveedor,p.contacto,p.nit,p.telefono,p.direccion,tp.tipo_pago,c.total,c.estatus
												FROM compra c
												INNER JOIN tipo_documento d
												ON c.documento_id = d.id_tipodocumento
												INNER JOIN proveedor p
												ON c.proveedor_id = p.codproveedor
												INNER JOIN tipo_pago tp
												ON c.tipopago_id = tp.id_tipopago
												WHERE c.id_compra = '$id_compra' and c.estatus != 10");
		$result = mysqli_num_rows($query);
		if($result > 0){

			$compra = mysqli_fetch_assoc($query);
			if($compra['estatus'] == 2){
				$anulada = '<h1>COMPRA ANULADA</H1>';
			}

			$query_productos = mysqli_query($conection,"SELECT p.codebar,p.producto,p.descripcion,m.marca,e.cantidad,e.precio_compra,(e.cantidad * e.precio_compra) as precio_total,i.impuesto
														FROM compra cp
														INNER JOIN entradas e
														ON cp.id_compra = e.compra_id
														INNER JOIN producto p
														ON e.codproducto = p.codproducto
														INNER JOIN marca m
														ON p.marca_id = m.idmarca
														INNER JOIN impuesto i
    													ON e.impuestoid = i.idimpuesto
														WHERE cp.id_compra = '$id_compra' ");
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
							$tlCant = $arrProductos[$i]['cantidad'] * $arrProductos[$i]['precio_compra'];
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
						$tlCant = $data['cantidad'] * $data['precio_compra'];
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
										<td style="width: 50px; text-align: center;"><p>'.$data['cantidad'].'</p></td>
										<td class="textleft" style="width: 580px">'.$data['producto'].'</td>
										<td style="width: 120px; text-align: right;" ><p>'.SIMBOLO_MONEDA.'.'.formatCant($data['precio_compra']).'</p></td>
										<td style="width: 120px; text-align: right;" ><p>'.SIMBOLO_MONEDA.'.'.formatCant($tlCant).'</p></td>
										<td style="width: 10px; text-align: right;" >'.$tpi.'</td>
									</tr>';
					
				}
				//Order Array
				ksort($arrGeneral);
				//dep($arrGeneral);
				//ROW EXONERADO
				$rowExento = '<tr>
						<td colspan="3" style="text-align: right;"><strong>EXENTO</strong></td>
						<td class="textright pading2" style="text-align: right;"><strong>'.SIMBOLO_MONEDA.'.'.formatCant(0).'</strong></td>
					</tr>';
				if(array_key_exists(0, $arrGeneral))
				{
					$importeDesc = $arrGeneral[0]['descripcion'];
					$importeExento = $arrGeneral[0]['d_total'];
					$rowExento ='
					<tr>
						<td colspan="3" class="pading2" style="text-align: right;"><strong>'.$importeDesc.'</strong></td>
						<td class="pading2" style="text-align: right;"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($importeExento).'</strong></td>
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
												<td colspan="3" class="pading2" style="text-align: right;"><strong>'.IMPUESTO.' '.$varImpuesto.'%</strong></td>
												<td class="pading" style="text-align: right;"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($importeImpuesto).'</strong></td>
											</tr>';
							$totalImporte = $arrGeneral[$keyArrGeneral]['d_subTotal'];
							$rowImportes .='<tr>
												<td colspan="3" class="pading2" style="text-align: right;"><strong>'.$importeDesc.'</strong></td>
												<td class="pading" style="text-align: right;"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($totalImporte).'</strong></td>
											</tr>';
						}else{
							$rowImpuestos .='<tr>
												<td colspan="3" class="pading2" style="text-align: right;"><strong>'.IMPUESTO.' '.$varImpuesto.'% </strong></td>
												<td class="pading" style="text-align: right;"><strong>'.SIMBOLO_MONEDA.'.'.formatCant(0.00).'</strong></td>
											</tr>';
							$rowImportes .='<tr>
												<td colspan="3" class="pading2" style="text-align: right;"><strong>'.$importeDesc.'</strong></td>
												<td class="pading" style="text-align: right;"><strong>'.SIMBOLO_MONEDA.'.'.formatCant(0.00).'</strong></td>
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
										<td colspan="3" class="pading2" style="text-align: right;"><strong>SUBTOTAL</strong></td>
										<td class="pading" style="text-align: right;"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($subTotalG).'</strong></td>
									</tr>'.
									$rowExento.
									$rowImportes.
									$rowImpuestos.
									'<tr>
										<td colspan="3" class="pading2" style="text-align: right;"><strong>TOTAL</strong></td>
										<td class="pading" style="text-align: right;"><strong>'.SIMBOLO_MONEDA.'.'.formatCant($total).'</strong></td>
									</tr>';
			}
			mysqli_close($conection);
			ob_start();
		    include(dirname('__FILE__').'/compra.php');
		    echo ob_get_clean();
			exit;
		}
	}

?>

