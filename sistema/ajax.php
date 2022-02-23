<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	include "../conexion.php";
	include "includes/functions.php";
	include "includes/config.php";
	session_start();
	//print_r($_POST);exit;
	if(!empty($_POST))
	{
		$arrMeses = fntMeses();
		// Extraer producto
		if($_POST['action'] == 'infoProducto')
		{
			$procucto_id = $_POST['producto'];
			$query = mysqli_query($conection,"SELECT p.codproducto,p.producto,p.descripcion,p.precio,p.existencia,p.existencia_minima,p.codebar,DATE_FORMAT(p.date_add, '%d/%m/%Y') as fecha,p.foto,mr.idmarca, mr.marca,p.presentacion_id,c.categoria,pr.presentacion
											FROM producto p
											INNER JOIN marca mr
											ON p.marca_id = mr.idmarca
											INNER JOIN categoria c
											ON p.categoria = c.idcategoria
											INNER JOIN presentacion_producto pr
											ON p.presentacion_id = pr.id_presentacion
											WHERE p.codproducto = '$procucto_id' AND p.estatus = 1");
			mysqli_close($conection);
			$result = mysqli_num_rows($query);
			if($result > 0){
				$data = mysqli_fetch_assoc($query);
				$_SESSION['updProductiId'] = $data['codproducto'];
				//$data['var_session']= $_SESSION['updProductiId'];
				if($data['foto'] != 'img_producto.png'){
					$data['foto'] = 'img/uploads/'.$data['foto'];
				}else{
					$data['foto'] = 'img/'.$data['foto'];
				}
				if($data['existencia']  <=  $data['existencia_minima']){
						$data['estado'] = '<span class="textorange">Reserva</span>';
				}else{
						$data['estado'] = '<span class="textgreen">Activo</span>';
				}

				if($data['existencia']  <= 0){
					$data['estado'] = '<span class="textred">Agotado</span>';
				}
				echo json_encode($data,JSON_UNESCAPED_UNICODE);
				exit;
			}

			echo "error";
			exit;
		}

		// Extraer producto para agregar a la venta
		if($_POST['action'] == 'infoProductoCod')
		{
			$barCode = $_POST['barCode'];
			$query = mysqli_query($conection,"SELECT p.codproducto,p.producto,p.descripcion,p.precio,p.existencia,p.existencia_minima,p.codebar,mr.idmarca, mr.marca
											FROM producto p
											INNER JOIN marca mr
											ON p.marca_id = mr.idmarca
											WHERE p.codebar = '$barCode' AND p.estatus = 1");
			mysqli_close($conection);

			$result = mysqli_num_rows($query);
			if($result > 0){

				$data = mysqli_fetch_assoc($query);

				if($data['existencia']  <= 0){
					echo "error";
					exit;
				}

				echo json_encode($data,JSON_UNESCAPED_UNICODE);
				exit;
			}

			echo "error";
			exit;
		}

		// Extraer producto para agregar a la COMPRA
		if($_POST['action'] == 'infoProductoCodCompra')
		{
			$barCode = $_POST['barCode'];
			$query = mysqli_query($conection,"SELECT p.codproducto,p.producto,p.descripcion,p.precio,p.existencia,p.existencia_minima,p.codebar,mr.idmarca, mr.marca
											FROM producto p
											INNER JOIN marca mr
											ON p.marca_id = mr.idmarca
											WHERE p.codebar = '$barCode' AND p.estatus = 1");
			mysqli_close($conection);

			$result = mysqli_num_rows($query);
			if($result > 0){

				$data = mysqli_fetch_assoc($query);
				echo json_encode($data,JSON_UNESCAPED_UNICODE);
				exit;
			}

			echo "error";
			exit;
		}

		// Extrae producto por nombre,marca,cateroria
		if($_POST['action'] == 'infoProductoSearch')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['searchText']))
				{
					$busqueda = strtolower($_POST['searchText']);
					$arrProductos = array();
					$htmlProd = '';

					$querySerchMarca = mysqli_query($conection,"SELECT idmarca,marca
																	FROM marca
																	WHERE marca LIKE '%$busqueda%' AND estatus != 10
																	ORDER BY marca DESC ");
					$resultSearchMarca = mysqli_num_rows($querySerchMarca);

					$whereMr = '';
					if($resultSearchMarca > 0){
						while ($arrSerarchPro = mysqli_fetch_assoc($querySerchMarca)){
							$idMrSearch = $arrSerarchPro['idmarca'];
							$whereMr .= ' OR p.marca_id LIKE '.$idMrSearch. ' ';
						}
					}

					$querySearchPro = mysqli_query($conection,"SELECT p.codproducto, p.producto, p.descripcion, p.precio, p.existencia , mr.marca,p.codebar
													FROM producto p
													INNER JOIN marca mr
													ON p.marca_id = mr.idmarca
													WHERE
													p.codebar LIKE '%$busqueda%' OR
													(p.descripcion LIKE '%$busqueda%' AND p.estatus = 1)
													$whereMr
													ORDER BY p.codproducto DESC ");
					$resultSearch = mysqli_num_rows($querySearchPro);
					if($resultSearch > 0){

						while ($arrSerarch = mysqli_fetch_assoc($querySearchPro)) {

							$inputCantidad = '<p style="color:red"><strong>Agotado</strong></p>';
							$btnCarrito = '';
							if( $arrSerarch['existencia'] > 0)
							{
								$inputCantidad = '<input type="text" name="txtCantProd" class="txtCantProd" value="1" min="1" onkeyup="cantProductoSearch('.$arrSerarch['codproducto'].');" >';
								$btnCarrito = '<a href="#" class="carAdd" onclick="event.preventDefault(); addProductVenta('.$arrSerarch['codproducto'].');"><i class="fas fa-cart-plus"></a></i>';
							}

							$htmlProd .= '
								<tr id="prodSrcAll_'.$arrSerarch['codproducto'].'">
									<td>'.$arrSerarch['codebar'].'</td>
									<td>'.$arrSerarch['producto'].'</td>
									<td>'.$arrSerarch['marca'].'</td>
									<td class="textright">'.SIMBOLO_MONEDA.'.'.formatCant($arrSerarch['precio']).'</td>
									<td class="textcenter existSR">'.$arrSerarch['existencia'].'</td>
									<td class="textright">'.$inputCantidad.'</td>
									<td class="textcenter">'.$btnCarrito.'</td>
								</tr>
							';
						}
						echo json_encode($htmlProd,JSON_UNESCAPED_UNICODE);
						exit;
					}

				}
				echo 'error';
			}
			exit;
		}

		// Extrae producto por nombre,marca,cateroria - COMPRA
		if($_POST['action'] == 'infoProductoSearch_v')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['searchText']))
				{
					$busqueda = strtolower($_POST['searchText']);
					$arrProductos = array();
					$htmlProd = '';
					$querySerchMarca = mysqli_query($conection,"SELECT idmarca,marca
																	FROM marca
																	WHERE marca LIKE '%$busqueda%' AND estatus != 10
																	ORDER BY marca DESC ");
					$resultSearchMarca = mysqli_num_rows($querySerchMarca);
					$whereMr = '';
					if($resultSearchMarca > 0){
						while ($arrSerarchPro = mysqli_fetch_assoc($querySerchMarca)){
							$idMrSearch = $arrSerarchPro['idmarca'];
							$whereMr .= ' OR p.marca_id LIKE '.$idMrSearch. ' ';
						}
					}

					$querySearchPro = mysqli_query($conection,"SELECT p.codproducto,p.producto,p.descripcion,p.precio_compra, p.precio, p.existencia , mr.marca,p.codebar
													FROM producto p
													INNER JOIN marca mr
													ON p.marca_id = mr.idmarca
													WHERE
													p.codebar LIKE '%$busqueda%' OR
													(p.descripcion LIKE '%$busqueda%' AND p.estatus = 1)
													$whereMr
													ORDER BY p.codproducto DESC ");
					$resultSearch = mysqli_num_rows($querySearchPro);
					if($resultSearch > 0){

						while ($arrSerarch = mysqli_fetch_assoc($querySearchPro)) {
							$htmlProd .= '
								<tr id="prodSrcAll_'.$arrSerarch['codproducto'].'">
									<td>'.$arrSerarch['codebar'].'</td>
									<td>'.$arrSerarch['producto'].'</td>
									<td>'.$arrSerarch['marca'].'</td>
									<td>'.SIMBOLO_MONEDA.'. '.formatCant($arrSerarch['precio_compra']).'</td>
									<td>'.SIMBOLO_MONEDA.'. '.formatCant($arrSerarch['precio']).'</td>
									<td><input type="text" name="txtCantProd_c" class="txtCantProd_c" value="1" min="1" onkeyup="fntActionPress('.$arrSerarch['codproducto'].')"></td>
									<td><input type="text" name="txtPreProd_c" class="txtPreProd_c" value="'.$arrSerarch['precio'].'" min="1" onkeyup="fntActionPress('.$arrSerarch['codproducto'].')"></td>
									<td class="textcenter"> <a href="#" class="carAdd" onclick="event.preventDefault(); addProductCompra('.$arrSerarch['codproducto'].');"><i class="fas fa-cart-plus"></a></i></td>
								</tr>
							';
						}
						echo json_encode($htmlProd,JSON_UNESCAPED_UNICODE);
						exit;
					}

				}
				echo 'error';
			}
			exit;
		}

		// Agregar Productos
		if($_POST['action'] == 'addProduct')
		{

			if(!empty($_POST['cantidad']) || !empty($_POST['precio']) || !empty($_POST['producto_id']))
			{

				$cantidad  	= $_POST['cantidad'];
				$precio 	= $_POST['precio'];
				$producto_id= $_POST['producto_id'];
				$usuario_id = intval($_SESSION['idUser']);

				$query_insert = mysqli_query($conection,"INSERT INTO entradas(codproducto,cantidad,precio_compra,usuario_id)
													 	 VALUES($producto_id,$cantidad,$precio,$usuario_id)");
				if($query_insert){
					$idEntrada = mysqli_insert_id($conection);
					// Actualizar Precio y Existencia
					$query_upd 	= mysqli_query($conection,"CALL actualizar_precio_producto($cantidad,$precio,$producto_id,$idEntrada)");

					$result = mysqli_num_rows($query_upd);
					if($result > 0){

						$data	= mysqli_fetch_assoc($query_upd);
						echo json_encode($data,JSON_UNESCAPED_UNICODE);
					}

				}else{
					echo "error";
				}
				mysqli_close($conection);
			}else{
				echo "error";
			}
			exit;
		}

		// Eliminar Productos
		if($_POST['action'] == 'delProduct')
		{

			if(!empty($_POST['producto_id']))
			{
				$producto_id= $_POST['producto_id'];

				$query_delete = mysqli_query($conection,"UPDATE producto SET estatus = 10 WHERE codproducto = $producto_id ");
				mysqli_close($conection);
				if($query_delete){
					echo 'ok';
				}else{
					echo "error";
				}
			}else{
				echo "error";
			}
			exit;
		}

		// Buscar cliente
		if($_POST['action'] == 'searchCliente')
		{
			if(!empty($_POST['cliente'])){
				$nit = $_POST['cliente'];


				$query = mysqli_query($conection,"SELECT * FROM cliente WHERE nit LIKE '$nit' and estatus = 1 ");
				mysqli_close($conection);
				$result = mysqli_num_rows($query);

				$data = '';
				if($result > 0){
					$data = mysqli_fetch_assoc($query);
				}else{
					$data = 0;
				}
				echo json_encode($data,JSON_UNESCAPED_UNICODE);
			}
			exit;
		}

		// Registrar Cliente
		if($_POST['action'] == 'addCliente'){

			$nit       = $_POST['nit_cliente'];
			$nombre    = $_POST['nom_cliente'];
			$telefono  = $_POST['tel_cliente'];
			$direccion = $_POST['dir_cliente'];
			$usuario_id = $_SESSION['idUser'];

			$query_insert = mysqli_query($conection,"INSERT INTO cliente(nit,nombre,telefono,direccion,usuario_id)
														VALUES('$nit','$nombre','$telefono','$direccion','$usuario_id')");

			if($query_insert){
				$codCliente = mysqli_insert_id($conection);
				$msg = $codCliente;
			}else{
				$msg='error';
			}
			echo $msg;
			exit;
		}
		// Agregar producto al detalle temporal
		/*
		if($_POST['action'] == 'addProductoDetalle'){
			$codproducto = $_POST['producto'];
			$cantidad 	 = $_POST['cantidad'];
			$token 		 = md5($_SESSION['idUser']);

			if(empty($_POST['producto']) || empty($_POST['cantidad']))
			{
				echo 'error';
			}else{
				// Agrega al detalle temporal y actualiza existencia del producto
				$query_detalle_temp 	= mysqli_query($conection,"CALL add_detalle_temp($codproducto,$cantidad,'$token')");
				$result = mysqli_num_rows($query_detalle_temp);
				$detalleTabla = '';
				$detalleTotales = '';
				$sub_total  = 0;
				$total 		= 0;
				////////////////////
				$exento = 0;
				$arrayData = array();
				if(!empty($query_detalle_temp) > 0){
					$arrData = array();
					$arrGeneral = array();
					include "../conexion.php";
					while ($data = mysqli_fetch_assoc($query_detalle_temp)){
						$impuesto =  mysqli_query($conection,"SELECT * FROM impuesto WHERE impuesto = {$data['impuesto']}");
						$desc = mysqli_fetch_assoc($impuesto);
						$key = $data['impuesto'];
						//dep($data);
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

						//ARMAR EL DETALLE EN HTML
						$precioTotal = $data['cantidad'] * $data['precio_venta'];
						$sub_total 	 = $sub_total + $precioTotal;

						$detalleTabla .='
						<tr>
							<td>'.$data['codebar'].'</td>
							<td colspan="2">'.$data['producto'].'</td>
							<td class="textcenter">'.$data['cantidad'].'</td>
							<td class="textright">'.formatCant($data['precio_venta']).'</td>
							<td class="textright">'.formatCant($precioTotal).'</td>
							<td class="textcenter">
								<a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].',1);"><i class="far fa-trash-alt"></i></a>
							</td>
						</tr>';
					}
					//Order Array
					ksort($arrGeneral);
					//ROW EXONERADO
					$rowExento = "";
					if(array_key_exists(0, $arrGeneral))
					{
						$importeDesc = $arrGeneral[0]['descripcion'];
						$importeExento = $arrGeneral[0]['d_total'];
						$rowExento .='
						<tr>
							<td colspan="5" class="textright">'.$importeDesc.' '.SIMBOLO_MONEDA.'.</td>
							<td class="textright">'.formatCant($importeExento).'</td>
						</tr>';
					}

					$total = 0;
					$subTotalG = 0;
					$rowImportes = "";
					$rowImpuestos = "";
					
					foreach ($arrGeneral as $key => $arrIMP) {
						# code...
						//dep($arrIMP);
						if($key != 0)
						{
							$importeDesc = $arrGeneral[$key]['descripcion'];
							$totalImporte = $arrGeneral[$key]['d_subTotal'];
							$rowImportes .='
							<tr>
								<td colspan="5" class="textright">'.$importeDesc.' '.SIMBOLO_MONEDA.'.</td>
								<td class="textright">'.formatCant($totalImporte).'</td>
							</tr>';
							
							$varImpuesto = $arrGeneral[$key]['impuesto'];
							$importeImpuesto = $arrGeneral[$key]['d_impuesto'];
							$rowImpuestos .='
							<tr>
								<td colspan="5" class="textright">'.IMPUESTO.' '.$varImpuesto.'% '.SIMBOLO_MONEDA.'.</td>
								<td class="textright">'.formatCant($importeImpuesto).'</td>
							</tr>';
						}

						//SUB TOTAL SIN IVA
						$subTotalG = $subTotalG + $arrIMP['d_subTotal'];
						//TOTAL GENERAL
						$total = $total + $arrIMP['d_total'];	
						//break;
					}

					$detalleTotales = '<tr>
											<td colspan="5" class="textright">SUBTOTAL '.SIMBOLO_MONEDA.'.</td>
											<td class="textright">'.formatCant($subTotalG).'</td>
										</tr>'.
										$rowExento.
										$rowImportes.
										$rowImpuestos.
										'<tr>
											<td colspan="5" class="textright">TOTAL '.SIMBOLO_MONEDA.'.</td>
											<td class="textright"><input type="hidden" name="hidTotalPagar" id="hidTotalPagar" value="'.$total.'" required disabled placeholder="Total a pagar"><span id="importeTotal">'.formatCant($total).'</span></td>
										</tr>';
					$arrayData['total'] = round($total,2);				
					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
				}else{
					echo 'error';
				}
				mysqli_close($conection);
			}
			exit;
		}
		*/
		// Agregar producto al detalle temporal
		if($_POST['action'] == 'addProductoDetalle'){
			$codproducto = intval($_POST['producto']);
			$cantidad 	 = $_POST['cantidad'];
			$token 		 = md5($_SESSION['idUser']);
			$operacion 	 = 1;

			if(empty($_POST['producto']) || empty($_POST['cantidad']))
			{
				echo 'error';
			}else{
				//Busca en el detalle si existe el producto
				$queryProducto = mysqli_query($conection,"SELECT precio,existencia,impuesto_id FROM producto WHERE codproducto = $codproducto;");
				$dataProducto = mysqli_fetch_assoc($queryProducto);
				$cant_actual = $dataProducto['existencia'];
				$precio_actual = $dataProducto['precio'];
				$impuesto_id = $dataProducto['impuesto_id'];

				//Busca en el detalle si existe el producto
				$queryDetalleProducto = mysqli_query($conection,"SELECT IFNULL(SUM(cantidad),0) as cantcarrito FROM detalle_temp WHERE codproducto = $codproducto AND operacion = 1");
				$num_rowsDetalleProducto = mysqli_num_rows($queryDetalleProducto);

				if($num_rowsDetalleProducto > 0)
				{
					$dataDetalleProducto = mysqli_fetch_assoc($queryDetalleProducto);
					$newcantcarrito = $dataDetalleProducto['cantcarrito'] + $cantidad;
				}

				if($newcantcarrito <= $cant_actual)
				{
					$queryDetalleProd = mysqli_query($conection,"SELECT correlativo,cantidad FROM detalle_temp WHERE token_user = '$token' AND codproducto = $codproducto AND operacion = 1");
					$num_rowsDetallePro = mysqli_num_rows($queryDetalleProd);
					if($num_rowsDetallePro > 0)
					{
						//Actualizar
						$dataDetalleProd = mysqli_fetch_assoc($queryDetalleProd);
						$cantTemp = $dataDetalleProd['cantidad'];
						$correlativo = $dataDetalleProd['correlativo'];
						$nueva_cantidad = $cantidad + $cantTemp;
						$queryUpdateTemp = mysqli_query($conection,"UPDATE detalle_temp SET cantidad = $nueva_cantidad WHERE correlativo = $correlativo ");
					}else{
						// Agrega al detalle temporal para compras
						$queryInsert = mysqli_query($conection,"INSERT INTO detalle_temp(token_user,codproducto,cantidad,precio_venta,impuestoid) VALUES('$token',$codproducto,$cantidad,$precio_actual,$impuesto_id);");
						$dtalleId = mysqli_insert_id($conection);
					}

					$query_detalle_temp = mysqli_query($conection,"SELECT tm.correlativo,
																				tm.codproducto,
																				tm.cantidad,
																				tm.precio_venta,
																				p.codebar,
																				p.producto,
																				i.impuesto,
																				p.descripcion
																			FROM detalle_temp tm
																			INNER JOIN producto p
																			ON tm.codproducto = p.codproducto
																			INNER JOIN impuesto i
																			ON p.impuesto_id = i.idimpuesto
																			WHERE tm.token_user = '$token' AND tm.operacion = 1 ");
					$rows_detalle_temp = mysqli_num_rows($query_detalle_temp);
					$detalleTabla = '';
					$detalleTotales = '';
					$sub_total  = 0;
					$total 		= 0;
					$exento = 0;
					$arrayData = array();
					if(!empty($rows_detalle_temp) > 0){
						$arrData = array();
						$arrGeneral = array();

						while ($data = mysqli_fetch_assoc($query_detalle_temp)){
							$impuesto =  mysqli_query($conection,"SELECT * FROM impuesto WHERE impuesto = {$data['impuesto']}");
							$desc = mysqli_fetch_assoc($impuesto);
							$key = $data['impuesto'];
							//dep($data);
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

							//ARMAR EL DETALLE EN HTML
							$precioTotal = $data['cantidad'] * $data['precio_venta'];
							$sub_total 	 = $sub_total + $precioTotal;

							$detalleTabla .='
							<tr>
								<td>'.$data['codebar'].'</td>
								<td colspan="2">'.$data['producto'].'</td>
								<td class="textcenter">'.$data['cantidad'].'</td>
								<td class="textright">'.formatCant($data['precio_venta']).'</td>
								<td class="textright">'.formatCant($precioTotal).'</td>
								<td class="textcenter">
									<a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].',1);"><i class="far fa-trash-alt"></i></a>
								</td>
							</tr>';
						}
						//Order Array
						ksort($arrGeneral);
						//ROW EXONERADO
						$rowExento = "";
						if(array_key_exists(0, $arrGeneral))
						{
							$importeDesc = $arrGeneral[0]['descripcion'];
							$importeExento = $arrGeneral[0]['d_total'];
							$rowExento .='
							<tr>
								<td colspan="5" class="textright">'.$importeDesc.' '.SIMBOLO_MONEDA.'.</td>
								<td class="textright">'.formatCant($importeExento).'</td>
							</tr>';
						}

						$total = 0;
						$subTotalG = 0;
						$rowImportes = "";
						$rowImpuestos = "";
						
						foreach ($arrGeneral as $key => $arrIMP) {
							# code...
							//dep($arrIMP);
							if($key != 0)
							{
								$importeDesc = $arrGeneral[$key]['descripcion'];
								$totalImporte = $arrGeneral[$key]['d_subTotal'];
								$rowImportes .='
								<tr>
									<td colspan="5" class="textright">'.$importeDesc.' '.SIMBOLO_MONEDA.'.</td>
									<td class="textright">'.formatCant($totalImporte).'</td>
								</tr>';
								
								$varImpuesto = $arrGeneral[$key]['impuesto'];
								$importeImpuesto = $arrGeneral[$key]['d_impuesto'];
								$rowImpuestos .='
								<tr>
									<td colspan="5" class="textright">'.IMPUESTO.' '.$varImpuesto.'% '.SIMBOLO_MONEDA.'.</td>
									<td class="textright">'.formatCant($importeImpuesto).'</td>
								</tr>';
							}

							//SUB TOTAL SIN IVA
							$subTotalG = $subTotalG + $arrIMP['d_subTotal'];
							//TOTAL GENERAL
							$total = $total + $arrIMP['d_total'];	
							//break;
						}

						$detalleTotales = '<tr>
												<td colspan="5" class="textright">SUBTOTAL '.SIMBOLO_MONEDA.'.</td>
												<td class="textright">'.formatCant($subTotalG).'</td>
											</tr>'.
											$rowExento.
											$rowImportes.
											$rowImpuestos.
											'<tr>
												<td colspan="5" class="textright">TOTAL '.SIMBOLO_MONEDA.'.</td>
												<td class="textright"><input type="hidden" name="hidTotalPagar" id="hidTotalPagar" value="'.$total.'" required disabled placeholder="Total a pagar"><span id="importeTotal">'.formatCant($total).'</span></td>
											</tr>';
						$arrayData['total'] = round($total,2);				
						$arrayData['detalle'] = $detalleTabla;
						$arrayData['totales'] = $detalleTotales;

						echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
					}else{
						echo 'error';
					}
				}else{
					//Cantidad superada
					echo "errorCantidad";
				}
				mysqli_close($conection);
			}
			exit;
		}


		// Agregar producto al detalle temporal - Compra
		if($_POST['action'] == 'addProductoDetalleCompra'){
			$codproducto = intval($_POST['producto']);
			$cantidad 	 = $_POST['cantidad'];
			$precio 	 = $_POST['precio'];
			$operacion 	 = 0;
			$token 		 = md5($_SESSION['idUser']);

			if(empty($_POST['producto']) || empty($_POST['cantidad']) || empty($_POST['precio']) )
			{
				echo 'error';
			}else{
                //Busca en el detalle si existe el producto
				$queryDetalle = mysqli_query($conection,"SELECT correlativo,cantidad FROM detalle_temp WHERE token_user = '$token' AND codproducto = $codproducto AND operacion = $operacion");
				$num_rowsDetalle = mysqli_num_rows($queryDetalle);
				$dataDetalle = mysqli_fetch_assoc($queryDetalle);
				$query_insert = false;
				$query_update = false;

				if($num_rowsDetalle > 0){
					$correlativo = $dataDetalle['correlativo'];
					$cantidad = $cantidad + $dataDetalle['cantidad'];
				}

				$queryProduct = mysqli_query($conection,"SELECT precio,existencia,impuesto_id FROM producto WHERE codproducto = $codproducto ");
				$dataProduct  = mysqli_fetch_assoc($queryProduct);
				$nueva_existencia = $dataProduct['existencia'] + $cantidad;
                $nuevo_total = ($dataProduct['existencia'] *  $dataProduct['precio']) + ($cantidad  * $precio);
                $precio_venta = $nuevo_total / $nueva_existencia;
                $impuesto = $dataProduct['impuesto_id'];
				if($num_rowsDetalle > 0){
					// Actualiza detalle producto
					$query_update = mysqli_query($conection,"UPDATE detalle_temp SET cantidad = $cantidad, precio_compra = $precio, precio_venta = $precio_venta  WHERE correlativo = $correlativo ");
				}else{
					// Agrega al detalle temporal para compras
					$query_insert = mysqli_query($conection,"INSERT INTO detalle_temp(token_user,codproducto,cantidad,precio_compra,precio_venta,impuestoid,operacion) VALUES('$token',$codproducto,$cantidad,$precio,$precio_venta,$impuesto,$operacion)");
					$dtalleId = mysqli_insert_id($conection);
				}

				if($query_insert or $query_update){

					$query_productos_compra = mysqli_query($conection,"SELECT tm.correlativo,
																				tm.token_user,
																				tm.codproducto,
																				tm.cantidad,
																				tm.precio_compra,
																				p.codebar,
																				p.producto,
																				i.impuesto,
																				p.descripcion
																			FROM detalle_temp tm
																			INNER JOIN producto p
																			ON tm.codproducto = p.codproducto
																			INNER JOIN impuesto i
																			ON p.impuesto_id = i.idimpuesto
																			WHERE tm.token_user = '$token' AND tm.operacion = 0 ");
					$result_producto_compra = mysqli_num_rows($query_productos_compra);
					$detalleTabla = '';
					$detalleTotales = '';
					$sub_total  = 0;
					$total 		= 0;
					$exento = 0;
					$arrayData = array();
					if($result_producto_compra > 0){

						$arrData = array();
						$arrGeneral = array();
						include "../conexion.php";

						while ($data = mysqli_fetch_assoc($query_productos_compra)){
							$impuesto =  mysqli_query($conection,"SELECT * FROM impuesto WHERE impuesto = {$data['impuesto']}");
							$desc = mysqli_fetch_assoc($impuesto);
							$key = $data['impuesto'];
							//dep($data);
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

							//ARMAR EL DETALLE EN HTML
							$precioTotal = $data['cantidad'] * $data['precio_compra'];
							$sub_total 	 = $sub_total + $precioTotal;

							$detalleTabla .='
							<tr>
								<td>'.$data['codebar'].'</td>
								<td colspan="2">'.$data['producto'].'</td>
								<td class="textcenter">'.$data['cantidad'].'</td>
								<td class="textright">'.formatCant($data['precio_compra']).'</td>
								<td class="textright">'.formatCant($precioTotal).'</td>
								<td class="textcenter">
									<a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].',0);"><i class="far fa-trash-alt"></i></a>
								</td>
							</tr>';
						}
						//Order Array
						ksort($arrGeneral);
						//ROW EXONERADO
						$rowExento = "";
						if(array_key_exists(0, $arrGeneral))
						{
							$importeDesc = $arrGeneral[0]['descripcion'];
							$importeExento = $arrGeneral[0]['d_total'];
							$rowExento .='
							<tr>
								<td colspan="5" class="textright">'.$importeDesc.' '.SIMBOLO_MONEDA.'.</td>
								<td class="textright">'.formatCant($importeExento).'</td>
							</tr>';
						}

						$total = 0;
						$subTotalG = 0;
						$rowImportes = "";
						$rowImpuestos = "";
						
						foreach ($arrGeneral as $key => $arrIMP) {
							# code...
							//dep($arrIMP);
							if($key != 0)
							{
								$importeDesc = $arrGeneral[$key]['descripcion'];
								$totalImporte = $arrGeneral[$key]['d_subTotal'];
								$rowImportes .='
								<tr>
									<td colspan="5" class="textright">'.$importeDesc.' '.SIMBOLO_MONEDA.'.</td>
									<td class="textright">'.formatCant($totalImporte).'</td>
								</tr>';
								
								$varImpuesto = $arrGeneral[$key]['impuesto'];
								$importeImpuesto = $arrGeneral[$key]['d_impuesto'];
								$rowImpuestos .='
								<tr>
									<td colspan="5" class="textright">'.IMPUESTO.' '.$varImpuesto.'% '.SIMBOLO_MONEDA.'.</td>
									<td class="textright">'.formatCant($importeImpuesto).'</td>
								</tr>';
							}
							//SUB TOTAL SIN IVA
							$subTotalG = $subTotalG + $arrIMP['d_subTotal'];
							//TOTAL GENERAL
							$total = $total + $arrIMP['d_total'];	
							//break;
						}

						$detalleTotales = '<tr>
												<td colspan="5" class="textright">SUBTOTAL '.SIMBOLO_MONEDA.'.</td>
												<td class="textright">'.formatCant($subTotalG).'</td>
											</tr>'.
											$rowExento.
											$rowImportes.
											$rowImpuestos.
											'<tr>
												<td colspan="5" class="textright">TOTAL '.SIMBOLO_MONEDA.'.</td>
												<td class="textright"><input type="hidden" name="hidTotalPagar" id="hidTotalPagar" value="'.$total.'" required disabled placeholder="Total a pagar"><span id="importeTotal">'.formatCant($total).'</span></td>
											</tr>';
						$arrayData['total'] = round($total,2);				
						$arrayData['detalle'] = $detalleTabla;
						$arrayData['totales'] = $detalleTotales;

						echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
					}else{
						echo 'error1';
					}
					mysqli_close($conection);
				}else{
					echo 'error2';
				}
			}
			exit;
		}

		if($_POST['action'] == 'serchForDetalle'){
			if(!empty($_POST['user'])){
				$token = md5($_POST['user']);
				$operacion = $_POST['operacion'];

				$op = ($operacion == 0) ? 'tmp.precio_compra' : 'tmp.precio_venta';

				$query = mysqli_query($conection,"SELECT tmp.correlativo,
														 tmp.token_user,
														 tmp.cantidad,
														 $op,
														 p.codProducto,
														 p.producto,
														 p.codebar,
														 i.impuesto
													FROM detalle_temp tmp
													INNER JOIN producto p
													ON tmp.codproducto = p.codproducto
													INNER JOIN impuesto i
													ON p.impuesto_id = i.idimpuesto
													WHERE token_user = '$token' AND operacion = $operacion ");
				$result = mysqli_num_rows($query);

				$detalleTabla = '';
				$detalleTotales = '';
				$sub_total  = 0;
				$total 		= 0;
				///////
				$exento = 0;
				$arrayData = array();

				if($result > 0){
					$arrData = array();
					$arrGeneral = array();

					while ($data = mysqli_fetch_assoc($query)){
						$impuesto =  mysqli_query($conection,"SELECT * FROM impuesto WHERE impuesto = {$data['impuesto']} ");
						$desc = mysqli_fetch_assoc($impuesto);
						$key = $data['impuesto'];
						$precio = ($operacion == 0) ? $data['precio_compra'] : $data['precio_venta'];
						//dep($data);
						if(array_key_exists($key, $arrGeneral))
						{
							$arrProductos  = $arrGeneral[$key]['productos'];
							array_push($arrProductos, $data);
							$arrGeneral[$key]['productos'] = $arrProductos;
							$total = 0;
							for ($i=0; $i < count($arrProductos) ; $i++) {

								$fltPrecio = ($operacion == 0) ? $data['precio_compra'] : $arrProductos[$i]['precio_venta'];
								$tlCant = $arrProductos[$i]['cantidad'] * $fltPrecio;

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
							$tlCant = $data['cantidad'] * $precio;
							$imp = $tlCant * ($data['impuesto'] / 100);
							$subTotal = $tlCant - $imp;
							$total = $subTotal + $imp;
							$arrData = array('impuesto' =>$data['impuesto'], 'descripcion' =>$desc['descripcion'], 'd_impuesto' =>$imp, 'd_subTotal' =>$subTotal, 'd_total' =>$total, 'productos' => $arrProd);
							$arrGeneral[$key] = $arrData;
						}

						//ARMAR EL DETALLE EN HTML
						$precioTbl = ($operacion == 0) ? $data['precio_compra'] : $precio;

						$precioTotal = $data['cantidad'] * $precio;
						$sub_total 	 = $sub_total + $precioTotal;

						$detalleTabla .='
						<tr>
							<td>'.$data['codebar'].'</td>
							<td colspan="2">'.$data['producto'].'</td>
							<td class="textcenter">'.$data['cantidad'].'</td>
							<td class="textright">'.formatCant($precioTbl).'</td>
							<td class="textright">'.formatCant($precioTotal).'</td>
							<td class="textcenter">
								<a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].','.$operacion.');"><i class="far fa-trash-alt"></i></a>
							</td>
						</tr>';
					}
					//Order Array
					ksort($arrGeneral);
					//dep($arrGeneral);
					//ROW EXONERADO
					$rowExento = "";
					if(array_key_exists(0, $arrGeneral))
					{
						$importeDesc = $arrGeneral[0]['descripcion'];
						$importeExento = $arrGeneral[0]['d_total'];
						$rowExento .='
						<tr>
							<td colspan="5" class="textright">'.$importeDesc.' '.SIMBOLO_MONEDA.'.</td>
							<td class="textright">'.formatCant($importeExento).'</td>
						</tr>';
					}

					$total = 0;
					$subTotalG = 0;
					$rowImportes = "";
					$rowImpuestos = "";
					
					foreach ($arrGeneral as $key => $arrIMP) {
						# code...
						//dep($arrIMP);
						if($key != 0)
						{
							$importeDesc = $arrGeneral[$key]['descripcion'];
							$totalImporte = $arrGeneral[$key]['d_subTotal'];
							$rowImportes .='
							<tr>
								<td colspan="5" class="textright">'.$importeDesc.' '.SIMBOLO_MONEDA.'.</td>
								<td class="textright">'.formatCant($totalImporte).'</td>
							</tr>';
							
							$varImpuesto = $arrGeneral[$key]['impuesto'];
							$importeImpuesto = $arrGeneral[$key]['d_impuesto'];
							$rowImpuestos .='
							<tr>
								<td colspan="5" class="textright">'.IMPUESTO.' '.$varImpuesto.'% '.SIMBOLO_MONEDA.'.</td>
								<td class="textright">'.formatCant($importeImpuesto).'</td>
							</tr>';
						}

						//SUB TOTAL SIN IVA
						$subTotalG = $subTotalG + $arrIMP['d_subTotal'];
						//TOTAL GENERAL
						$total = $total + $arrIMP['d_total'];	
						//break;
					}

					$detalleTotales = '<tr>
											<td colspan="5" class="textright">SUBTOTAL '.SIMBOLO_MONEDA.'.</td>
											<td class="textright">'.formatCant($subTotalG).'</td>
										</tr>'.
										$rowExento.
										$rowImportes.
										$rowImpuestos.
										'<tr>
											<td colspan="5" class="textright">TOTAL '.SIMBOLO_MONEDA.'.</td>
											<td class="textright"><input type="hidden" name="hidTotalPagar" id="hidTotalPagar" value="'.$total.'" required disabled placeholder="Total a pagar"><span id="importeTotal">'.formatCant($total).'</span></td>
										</tr>';
					$arrayData['total'] = round($total,2);
					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
				}else{
					echo 'error';
				}
				mysqli_close($conection);
			}
			exit;
		}

		// Eliminar producto del detalle temporal
		if($_POST['action'] == 'delProductoDetalle'){
			$id_detalle 	= $_POST['id_detalle'];
			$token 		 	= md5($_SESSION['idUser']);
			$operacion  	= $_POST['operacion'];

			if(empty($_POST['id_detalle']))
			{
				echo 'error';
			}else{
				$query_detalle_temp 	= mysqli_query($conection,"CALL del_detalle_temp($id_detalle,'$token',$operacion)");
				$result = mysqli_num_rows($query_detalle_temp);

				$detalleTabla = '';
				$detalleTotales = '';
				$sub_total  = 0;
				$total 		= 0;
				///////
				$exento = 0;
				$arrayData = array();

				if($result > 0){
					$arrData = array();
					$arrGeneral = array();
					include "../conexion.php";
					while ($data = mysqli_fetch_assoc($query_detalle_temp)){
						$impuesto =  mysqli_query($conection,"SELECT * FROM impuesto WHERE impuesto = {$data['impuesto']} ");
						$desc = mysqli_fetch_assoc($impuesto);
						$key = $data['impuesto'];
						//dep($data);
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

						//ARMAR EL DETALLE EN HTML
						$precio = ($operacion == 0) ? $data['precio_compra'] : $data['precio_venta'];
						$precioTbl = ($operacion == 0) ? $data['precio_compra'] : $data['precio_venta'];

						$precioTotal = $data['cantidad'] * $precio;
						$sub_total 	 = $sub_total + $precioTotal;

						$detalleTabla .='
						<tr>
							<td>'.$data['codebar'].'</td>
							<td colspan="2">'.$data['producto'].'</td>
							<td class="textcenter">'.$data['cantidad'].'</td>
							<td class="textright">'.formatCant($precioTbl).'</td>
							<td class="textright">'.formatCant($precioTotal).'</td>
							<td class="textcenter">
								<a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].','.$operacion.');"><i class="far fa-trash-alt"></i></a>
							</td>
						</tr>';
					}
					//Order Array
					ksort($arrGeneral);
					//ROW EXONERADO
					$rowExento = "";
					if(array_key_exists(0, $arrGeneral))
					{
						$importeDesc = $arrGeneral[0]['descripcion'];
						$importeExento = $arrGeneral[0]['d_total'];
						$rowExento .='
						<tr>
							<td colspan="5" class="textright">'.$importeDesc.' '.SIMBOLO_MONEDA.'.</td>
							<td class="textright">'.formatCant($importeExento).'</td>
						</tr>';
					}

					$total = 0;
					$subTotalG = 0;
					$rowImportes = "";
					$rowImpuestos = "";
					
					foreach ($arrGeneral as $key => $arrIMP) {
						# code...
						if($key != 0)
						{
							$importeDesc = $arrGeneral[$key]['descripcion'];
							$totalImporte = $arrGeneral[$key]['d_subTotal'];
							$rowImportes .='
							<tr>
								<td colspan="5" class="textright">'.$importeDesc.' '.SIMBOLO_MONEDA.'.</td>
								<td class="textright">'.formatCant($totalImporte).'</td>
							</tr>';
							
							$varImpuesto = $arrGeneral[$key]['impuesto'];
							$importeImpuesto = $arrGeneral[$key]['d_impuesto'];
							$rowImpuestos .='
							<tr>
								<td colspan="5" class="textright">'.IMPUESTO.' '.$varImpuesto.'% '.SIMBOLO_MONEDA.'.</td>
								<td class="textright">'.formatCant($importeImpuesto).'</td>
							</tr>';
						}
						//SUB TOTAL SIN IVA
						$subTotalG = $subTotalG + $arrIMP['d_subTotal'];
						//TOTAL GENERAL
						$total = $total + $arrIMP['d_total'];
					}

					$detalleTotales = '<tr>
											<td colspan="5" class="textright">SUBTOTAL '.SIMBOLO_MONEDA.'.</td>
											<td class="textright">'.formatCant($subTotalG).'</td>
										</tr>'.
										$rowExento.
										$rowImportes.
										$rowImpuestos.
										'<tr>
											<td colspan="5" class="textright">TOTAL '.SIMBOLO_MONEDA.'.</td>
											<td class="textright"><input type="hidden" name="hidTotalPagar" id="hidTotalPagar" value="'.$total.'" required disabled placeholder="Total a pagar"><span id="importeTotal">'.formatCant($total).'</span></td>
										</tr>';

					$arrayData['total'] = round($total,2);
					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
				}else{
					echo 'error';
				}
				mysqli_close($conection);
			}
			exit;
		}

		// Anular Venta
		if($_POST['action'] == 'anularVenta'){

			$token 		 = md5($_SESSION['idUser']);

			$query_del = mysqli_query($conection,"DELETE FROM detalle_temp WHERE token_user = '$token' ");
			mysqli_close($conection);
			if($query_del){
				echo 'ok';
			}else{
				echo 'error';
			}
			exit;
		}

		// Procesa pago
		if($_POST['action'] == 'procesarPago'){

			$fecha = date('Y-m-d');
			$arrResponse = array();
			if($_SESSION['active'])
			{
				if(empty($_POST['codcliente'])){
					$codcliente = 1;
				}else{
					$codcliente = $_POST['codcliente'];
				}

				$descuento = $_POST['descuento'] == "" ? 0 : $_POST['descuento'];

				$token 		= md5($_SESSION['idUser']);
				$tipoPago 	= intval($_POST['tipoPago']);
				$efectivo 	= $_POST['efectivo'];
				$usuario 	= $_SESSION['idUser'];
				$idserie	= $_POST['idserie'];

				//Serie
				$query_serie = mysqli_query($conection,"SELECT * FROM facturas WHERE idserie = {$idserie} ");
				$infoSerie = mysqli_fetch_assoc($query_serie);
				if(empty($infoSerie))
				{
					$arrResponse = array('status' => false, 'msg' => "Serie de facturación no definida." );
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);exit;
				}
				//Extrae la ultima factura
				$queryFactura = mysqli_query($conection,"SELECT factura_serie FROM factura WHERE serieid = {$idserie} ORDER BY factura_serie DESC LIMIT 1");
				$infoFAc = mysqli_fetch_assoc($queryFactura);

				if(empty($infoFAc))
				{
					$noFacturaSerie = $infoSerie['no_inicio'];
				}else{
					$noFacturaSerie = $infoFAc['factura_serie'] + 1;
					if($noFacturaSerie > $infoSerie['no_fin'])
					{
						$arrResponse = array('status' => false, 'msg' => "No hay facturas disponibles para la serie." );
						echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);exit;
					}
				}

				$query = mysqli_query($conection,"SELECT * FROM detalle_temp WHERE token_user = '$token' ");
				$result = mysqli_num_rows($query);

				if($result > 0)
				{
					$query_procesar = mysqli_query($conection,"CALL procesar_venta($usuario,$codcliente,'$token',$tipoPago,$efectivo,$descuento,'$fecha',$idserie,$noFacturaSerie)");
					$result_detalle = mysqli_num_rows($query_procesar);
					if($result_detalle > 0){

						$data	= mysqli_fetch_assoc($query_procesar);
						$venta_c = "venta_".$data["nofactura"];
						$idCliente = "cliente_".$data["codcliente"];
						$idventacript = encrypt($venta_c,$data["codcliente"]);
						$idclientecipt = encrypt($idCliente,$idventacript);
						$data["status"] = true;
						$data["nofactura"] = $idventacript;
						$data["codcliente"] = $idclientecipt;
						echo json_encode($data,JSON_UNESCAPED_UNICODE);
						exit;
					}else{
						$arrResponse = array('status' => false, 'msg' => "Error al procesar la venta." );
					}
				}else{
					$arrResponse = array('status' => false, 'msg' => "Productos no definidos en el detalle." );
				}
				mysqli_close($conection);
			}else{
				$arrResponse = array('status' => false, 'msg' => "Usuario no autorizado." );
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);exit;
			exit;
		}

		// Info Factura
		if($_POST['action'] == 'infoFactura'){
			if(!empty($_POST['nofactura'])){


				$nofactura = $_POST['nofactura'];

				$query = mysqli_query($conection,"SELECT * FROM factura WHERE nofactura = '$nofactura' AND estatus = 1");
				mysqli_close($conection);

				$result = mysqli_num_rows($query);
				if($result > 0){

					$data = mysqli_fetch_assoc($query);
					$arrFecha = explode('-',$data['fecha']);
					$fecha = $arrFecha[2].'/'.$arrFecha[1].'/'.$arrFecha[0];
					$data['fecha'] = $fecha;
					$data['totalfactura'] = SIMBOLO_MONEDA.'. '.formatCant($data['totalfactura']);
					echo json_encode($data,JSON_UNESCAPED_UNICODE);
					exit;
				}
			}
			echo "error";
			exit;
		}

		// Anular Factura
		if($_POST['action'] == 'anularFactura'){

			if(!empty($_POST['noFactura']))
			{
				$noFactura = $_POST['noFactura'];
				$query_anular 	= mysqli_query($conection,"CALL anular_factura($noFactura)");

				$result = mysqli_num_rows($query_anular);
				if($result > 0){
					$data	= mysqli_fetch_assoc($query_anular);
					echo json_encode($data,JSON_UNESCAPED_UNICODE);
					exit;
				}
			}
			echo "error";
			exit;
		}

		// Anular Factura
		if($_POST['action'] == 'login'){
			$code 		= '';
			$msg    	= '';
			$arrData 	= array();

			if(empty($_POST['usuario']) or empty($_POST['clave']))
			{
				$code = '1';
				$msg = "Ingrese usuario y contraseña";
			}else{
				$user = mysqli_real_escape_string($conection,$_POST['usuario']);
				$pass = md5(mysqli_real_escape_string($conection,$_POST['clave']));

				$query_login = mysqli_query($conection,"SELECT u.idusuario,u.dpi,u.nombre,u.telefono,u.correo,u.usuario,u.estatus,
																r.idRol,r.rol
																FROM usuario u
																INNER JOIN rol r
																ON u.rol = r.idrol
																WHERE u.usuario= '$user' AND clave = '$pass'");
				mysqli_close($conection);
				$result = mysqli_num_rows($query_login);

				if($result > 0)
				{
					$dataUser = mysqli_fetch_assoc($query_login);
					if($dataUser['estatus'] == 1 ){
						$_SESSION['active'] = true;
						$_SESSION['idUser'] = $dataUser['idusuario'];
						$_SESSION['user']   = $dataUser['usuario'];
						$_SESSION['vendedor']   = $dataUser['nombre'];
						$_SESSION['rol']    = $dataUser['idRol'];
						$code = '00';
						$msg = "Bienvenido...";
					}else{
						$code = '2';
						$msg = "Usuario inactivo, consulte al administrador.";
					}
				}else{
					$code = '3';
					$msg = "El usuario o la clave es incorrecto.";
				}
			}

			$arrData = array('cod' => $code, 'msg' => $msg);
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Cambiar Password
		if($_POST['action'] == 'changePassword'){
			if(!empty($_POST['passActual']) && !empty($_POST['passNuevo']))
			{
				$password = md5($_POST['passActual']);
				$newPass  = md5($_POST['passNuevo']);
				$idUser   = $_SESSION['idUser'];

				$code 		= '';
				$msg    	= '';
				$arrData 	= array();

				$query_login = mysqli_query($conection,"SELECT * FROM usuario WHERE clave = '$password' and idusuario = $idUser ");
				$result 	 = mysqli_num_rows($query_login);
				if($result > 0)
				{
					$query_update = mysqli_query($conection,"UPDATE usuario SET clave = '$newPass' WHERE idusuario = $idUser ");
					mysqli_close($conection);
					if($query_update){
						$code = '00';
						$msg = "Su contraseña se ha actualizado con éxito.";
					}else{
						$code = '2';
						$msg = "No es posible cambiar su contraseña.";
					}
				}else{
					$code = '1';
					$msg = "La contraseña actual es incorrecta.";
				}
			}
			$arrData = array('cod' => $code, 'msg' => $msg);
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Cambiar Password
		if($_POST['action'] == 'updatePassRecovery'){
			if(!empty($_POST['id_us']) || !empty($_POST['token']) || !empty($_POST['pass1']) )
			{
				$idUser 	= intval($_POST['id_us']);
				$token  	= $_POST['token'];
				$newPass    = md5($_POST['pass1']);
				$code 		= '01';
				$msg    	= 'Acción no válida';
				$arrData 	= array();

				$query_login = mysqli_query($conection,"SELECT * FROM usuario WHERE cod_temp = '$token' ");
				$result 	 = mysqli_num_rows($query_login);
				if($result > 0)
				{
					$query_update = mysqli_query($conection,"UPDATE usuario SET clave = '$newPass', cod_temp = '' WHERE idusuario = $idUser ");
					mysqli_close($conection);
					if($query_update){
						$code = '00';
						$msg = "Su contraseña se ha actualizado con éxito.";
					}else{
						$code = '2';
						$msg = "No es posible cambiar su contraseña.";
					}
				}else{
					$code = '1';
					$msg = "Acción no válida.";
				}
			}
			$arrData = array('cod' => $code, 'msg' => $msg);
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Recuperar accesos
		if($_POST['action'] == 'recoveryPass'){			
			if(empty($_POST['email'])){
				echo "errorData";
			}
			
			$strEmail = strtolower(strClean($_POST['email']));
			$query_user = mysqli_query($conection,"SELECT * FROM usuario WHERE correo = '$strEmail'");
			$result = mysqli_num_rows($query_user);

				if($result > 0)
				{
					$dataUser = mysqli_fetch_assoc($query_user);
					if($dataUser['estatus'] == 1 ){
						$token = md5(date('Y-m-dd h:m:s'));
						$idUser = $dataUser['idusuario'];
						$nombreUsuario = $dataUser['nombre'];
						$usuario = $dataUser['usuario'];
						$query_update = mysqli_query($conection,"UPDATE usuario SET cod_temp = '$token' WHERE idusuario = $idUser ");
						if($query_update){
							$code = '00';
							$msg = "Se te ha enviado un email con los datos de acceso.";
							//ASUNTO							
							if($nombreUsuario != ''){
								$url_recovery = $base_url.'/admin/cambiar_password.php?token='.$token;
								//ENVIO DE CORREO
								$emailRemitente = EMAIL_EMPRESA;
								//Data email Cliente
								$dataUsuario = array('nombreUsuario' => $nombreUsuario,'usuario' => $usuario, 'emailDestino' => $strEmail,'emailRemitente' => $emailRemitente,'asunto' => 'Recuperar cuenta - Tienda Virtual','url_recovery' => $url_recovery);
								sendEmail($dataUsuario,'email_recuperar_usuario');
							}
						}else{
							$code = '1';
							$msg = "No es posible procesar la solicitud.";
						}
						
					}else{
						$code = '2';
						$msg = "Usuario inactivo, consulte al administrador.";
					}
				}else{
					$code = '3';
					$msg = "Correo no registrado.";
				}

			$arrData = array('cod' => $code, 'msg' => $msg);
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Extraer usuario
		if($_POST['action'] == 'infoUsuario')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idUser'])){
					$iduser = intval($_POST['idUser']);
				}else{
					$iduser = intval($_SESSION['idUser']);
				}
				$queryUser= mysqli_query($conection,"SELECT u.idusuario,u.dpi,u.nombre,u.telefono,u.correo,u.usuario, (u.rol) as idrol, (r.rol) as rol, u.estatus
									FROM usuario u
									INNER JOIN rol r
									on u.rol = r.idrol
									WHERE idusuario= $iduser and estatus !=10 ");
				mysqli_close($conection);

				$result = mysqli_num_rows($queryUser);
				if($result > 0){
					$arrDataUser = mysqli_fetch_assoc($queryUser);
					echo json_encode($arrDataUser,JSON_UNESCAPED_UNICODE);
					exit;
				}
			}
			echo "error";
			exit;
		}

		// Cambiar Estado Usuario
		if($_POST['action'] == 'changeEstadoUser')
		{
			if(!empty($_POST['idUser']))
			{
				$iduser= intval($_POST['idUser']);
				$queryUser= mysqli_query($conection,"SELECT estatus FROM usuario WHERE idusuario= $iduser and idusuario != 1");
				$numRow = mysqli_num_rows($queryUser);
				if($numRow > 0){
					$dataUser = mysqli_fetch_assoc($queryUser);
					if($dataUser['estatus'] == 1){
						$newEstatus = 0;
					}else{
						$newEstatus = 1;
					}
					$queryUpdEstatus = mysqli_query($conection,"UPDATE usuario SET estatus = $newEstatus WHERE idusuario= $iduser ");
					mysqli_close($conection);
					if($queryUpdEstatus){
						echo $newEstatus;
						exit;
					}else{
						echo "error";
						exit;
					}
				}
			}else{
				echo "error";
			}
			exit;
		}

		// Actualizar Usuario Logeado
		if($_POST['action'] == 'updateUser')
		{
			if($_SESSION['active'])
			{
				if(empty($_POST['dpi']) || empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['correo']))
				{
					$code = '1';
					$msg = "Todos los campos son obligatorios.";
				}else{
					$intIdUser 	= intval($_SESSION['idUser']);
					$intDpi 	= $_POST['dpi'];
					$strNombre 	= $_POST['nombre'];
					$intTel 	= intval($_POST['telefono']);
					$strEmail 	= $_POST['correo'];

					$querySearch = mysqli_query($conection,"SELECT * FROM usuario
													   WHERE (correo = '$strEmail' AND idusuario != $intIdUser) ");

					$rows   = mysqli_num_rows($querySearch);
					if($rows > 0){
						$code 	= '1';
						$msg 	= "El correo electrónico ya existe, ingrese otro.";
					}else{
							$queryUpd = mysqli_query($conection,"UPDATE usuario SET dpi = '$intDpi', nombre= '$strNombre', telefono=$intTel, correo = '$strEmail'  WHERE idusuario= $intIdUser ");
							mysqli_close($conection);
							if($queryUpd){
								$code = '00';
								$msg = "Datos actualizados correctamente.";
							}else{
								$code = '2';
								$msg = "Error al actualizar los datos.";
							}
						}
				}
				$arrData = array('cod' => $code, 'msg' => $msg);
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
				exit;
			}
			echo "error";
			exit;
		}

		// Actualizar Datos Empresa
		if($_POST['action'] == 'updateDataEmpresa')
		{
			if($_SESSION['active'] and $_SESSION['rol'] == 1)
			{
				if(empty($_POST['txtNit']) || empty($_POST['txtNombre']) || empty($_POST['txtRSocial']) || empty($_POST['txtTelEmpresa']) || empty($_POST['txtEmailEmpresa']) || empty($_POST['txtEmailRemitente']) || empty($_POST['txtDirEmpresa']) || empty($_POST['txtImpuesto']) || empty($_POST['txtMoneda']) || empty($_POST['txtSimbolo']) || empty($_POST['txtZonaHoraria']) || empty($_POST['txtIdentificacionCliente']) || empty($_POST['txtIdentificacionTributaria']) || empty($_POST['txtSeparadorMillares']) || empty($_POST['txtSeparadorDecimales']))
				{
					$code = '1';
					$msg = "Todos los campos son obligatorios.";
				}else{

					$intNit 	= intval($_POST['txtNit']);
					$strNombre 	= $_POST['txtNombre'];
					$strRSocial = $_POST['txtRSocial'];
					$intTel 	= intval($_POST['txtTelEmpresa']);
					$strEmail 	= $_POST['txtEmailEmpresa'];
					$sitioWeb = $_POST['txtSitioWeb'];
					$strDir 	= $_POST['txtDirEmpresa'];
					$strImpuesto= $_POST['txtImpuesto'];
					$strMoneda 	= $_POST['txtMoneda'];
					$strSMoneda	= $_POST['txtSimbolo'];
					$zonaHoraria = $_POST['txtZonaHoraria'];
					$idCliente = $_POST['txtIdentificacionCliente'];
					$idTributaria = $_POST['txtIdentificacionTributaria'];
					$spMillares = $_POST['txtSeparadorMillares'];
					$spDecimales = $_POST['txtSeparadorDecimales'];
					$emailPedidos = $_POST['txtEmailRemitente'];
					$emailFactura = $_POST['txtEmailFactura'];
					$whatsapp = $_POST['txtWhatsapp'];
					$facebook = $_POST['txtFacebook'];
					$instagram = $_POST['txtInstagram'];

					$queryUpd = mysqli_query($conection,"UPDATE configuracion SET nit 	= $intNit,
																			nombre	= '$strNombre',
																			razon_social='$strRSocial',
																			telefono = $intTel,
																			whatsapp = '$whatsapp',
																			email 	= '$strEmail',
																			direccion = '$strDir',
																			impuesto = '$strImpuesto',
																			moneda 	= '$strMoneda',
																			simbolo_moneda = '$strSMoneda',
																			zona_horaria = '$zonaHoraria',
																			sitio_web = '$sitioWeb',
																			email_factura = '$emailFactura',
																			email_pedidos = '$emailPedidos',
																			facebook = '$facebook',
																			instagram = '$instagram',
																			identificacion_cliente = '$idCliente',
																			identificacion_tributaria = '$idTributaria',
																			separador_millares = '$spMillares',
																			separador_decimales = '$spDecimales'
																		WHERE id = 1 ");
					mysqli_close($conection);
					if($queryUpd){
						$code = '00';
						$msg = "Datos actualizados correctamente.";
					}else{
						$code = '2';
						$msg = "Error al actualizar los datos.";
					}
				}
				$arrData = array('cod' => $code, 'msg' => $msg);
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
				exit;
			}
			echo "error";
			exit;
		}
		//Actualizar logo
		if($_POST['action'] == 'updateLogo'){
			if(!empty($_FILES['logo']['name']))
			{
				$foto   	 	= $_FILES['logo'];
				$nombre_foto 	= $foto['name'];
				$type 		 	= $foto['type'];
				$url_temp    	= $foto['tmp_name'];

				$img_nombre = 'logo_empresa.jpg';
				$destino    = 'img/'.$img_nombre;
				move_uploaded_file($url_temp, $destino);
				$queryUpd = mysqli_query($conection,"UPDATE configuracion SET logotipo = '$img_nombre'");
				if($queryUpd){
					echo "ok";
				}else{
					echo "error";
				}
			}
			exit;
			
		}
		// Extraer cliente
		if($_POST['action'] == 'infoCliente')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idCliente'])){
					$idCliente = intval($_POST['idCliente']);

					$queryCliente= mysqli_query($conection,"SELECT * FROM cliente WHERE idcliente= $idCliente and estatus !=10 ");
					mysqli_close($conection);

					$result = mysqli_num_rows($queryCliente);
					if($result > 0){
						$arrDataCliente = mysqli_fetch_assoc($queryCliente);
						echo json_encode($arrDataCliente,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}

		// Extraer proveedor
		if($_POST['action'] == 'infoProveedor')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idProveedor'])){
					$idProveedor = intval($_POST['idProveedor']);

					$queryProveedor= mysqli_query($conection,"SELECT codproveedor,nit,proveedor,contacto,telefono,correo,direccion, DATE_FORMAT(date_add, '%d/%m/%Y') as fecha FROM proveedor WHERE codproveedor= $idProveedor and estatus !=10 ");
					mysqli_close($conection);

					$result = mysqli_num_rows($queryProveedor);
					if($result > 0){
						$arrDataProveedor = mysqli_fetch_assoc($queryProveedor);
						echo json_encode($arrDataProveedor,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}

		// Crear categoria
		if($_POST['action'] == 'newCategoria'){

			if($_SESSION['active'])
			{
				if(empty($_POST['txtCategoria']) || empty($_POST['txtDescripcionCat']))
				{
					$msg='error';
				}else{
					$strCategoria 	= ucwords(strClean($_POST['txtCategoria']));
					$strDescipcion  = ucwords(strClean($_POST['txtDescripcionCat']));
					$usuario_id = intval($_SESSION['idUser']);
					$query_insert = mysqli_query($conection,"INSERT INTO categoria(categoria,descripcion,usuarioid)
																VALUES('$strCategoria','$strDescipcion',$usuario_id)");

					if($query_insert){
						$msg = 'ok';
					}else{
						$msg='error';
					}

				}
				echo $msg;
				exit;
			}

			echo 'error';
			exit;
		}

		// Update categoria
		if($_POST['action'] == 'updCategoria'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtCategoria']) || empty($_POST['txtDescripcionCat']))
				{
					$msg='error';
				}else{
					$intIdCategoria = intval($_POST['txtIdCategoria']);
					$strCategoria 	= ucwords(strClean($_POST['txtCategoria']));
					$strDescipcion  = ucwords(strClean($_POST['txtDescripcionCat']));

					$query_update = mysqli_query($conection,"UPDATE categoria SET categoria = '$strCategoria', descripcion = '$strDescipcion' WHERE idcategoria = $intIdCategoria ");

					if($query_update){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}

			echo 'error';
			exit;
		}

		// Extraer Categorias y subcategorias
		if($_POST['action'] == 'infoCategorias')
		{
			if($_SESSION['active'])
			{
				$arrayDataCategorias = array();

				$queryCategoria= mysqli_query($conection,"SELECT (idcategoria) as id,(categoria) as title,descripcion,parent_categoria FROM categoria WHERE parent_categoria = 0 and estatus !=10 ");

				$resultCategoria = mysqli_num_rows($queryCategoria);

				if($resultCategoria > 0){
					while ($arrCategorias = mysqli_fetch_assoc($queryCategoria)) {
						$arrCategorias['value'] = $arrCategorias['id'];
						$arrCicloCat = "arrayCat".$arrCategorias['id'];
						$arrCicloCat = array();
						$idCategoria = intval($arrCategorias['id']);

						$querySubCat= mysqli_query($conection,"SELECT (idcategoria) as id,(categoria) as title,descripcion,parent_categoria FROM categoria WHERE parent_categoria = $idCategoria and estatus !=10 ");
						$resultSub = mysqli_num_rows($querySubCat);

						if($resultSub > 0){

							while ( $subCat = mysqli_fetch_assoc($querySubCat)) {
								$arrCicloCat2 = "arrayCat".$subCat['id'];
								$arrCicloCat2 = array();
								$intParentCat = intval($subCat['id']);

								$querySubCt = mysqli_query($conection,"SELECT (idcategoria) as id,(categoria) as title,descripcion,parent_categoria FROM categoria WHERE parent_categoria = $intParentCat and estatus !=10 ");
								$resultSubCt = mysqli_num_rows($querySubCt);

								if($resultSubCt > 0){
									while ( $arrSubCat = mysqli_fetch_assoc($querySubCt)) {
										array_push($arrCicloCat2,$arrSubCat);
										$subCat['subs'] = $arrCicloCat2;
									}
								}

								array_push($arrCicloCat,$subCat);
								$arrCategorias['subs'] = $arrCicloCat;
							}
						}

						array_push($arrayDataCategorias,$arrCategorias);
					}
					//print_r($arrayDataCategorias);
				}
				echo json_encode($arrayDataCategorias,JSON_UNESCAPED_UNICODE);
				exit;
			}
			echo "error";
			exit;
		}

		// Extraer Categorias y subcategorias
		if($_POST['action'] == 'infoAllCategorias')
		{
			if($_SESSION['active'])
			{
				$arrayDataCategorias = array();

				$queryCategoria= mysqli_query($conection,"SELECT (idcategoria) as id,(categoria) as title,descripcion,parent_categoria FROM categoria WHERE estatus !=10 ");

				$resultCategoria = mysqli_num_rows($queryCategoria);

				if($resultCategoria > 0){
					while ($arrCategorias = mysqli_fetch_assoc($queryCategoria)) {
						$arrCategorias['value'] = $arrCategorias['id'];
						$arrCicloCat = "arrayCat".$arrCategorias['id'];
						array_push($arrayDataCategorias,$arrCategorias);
					}
					//print_r($arrayDataCategorias);
				}
				echo json_encode($arrayDataCategorias,JSON_UNESCAPED_UNICODE);
				exit;
			}
			echo "error";
			exit;
		}

		// Extraer una categoria
		if($_POST['action'] == 'infoCategory')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idCategoria'])){
					$idCategoria = intval($_POST['idCategoria']);

					$queryCategoria= mysqli_query($conection,"SELECT idcategoria,categoria,descripcion,estatus FROM categoria WHERE idcategoria= $idCategoria and estatus !=10 ");

					$resultCategoria = mysqli_num_rows($queryCategoria);

					if($resultCategoria > 0){
						$dataCategory = mysqli_fetch_assoc($queryCategoria);
						echo json_encode($dataCategory,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}

		// Cambiar Estado Marca
		if($_POST['action'] == 'changeEstadoCategoria')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idCategoria']))
				{
					$idcategoria= intval($_POST['idCategoria']);
					$queryCategoria= mysqli_query($conection,"SELECT estatus FROM categoria WHERE idcategoria= $idcategoria");
					$numRow = mysqli_num_rows($queryCategoria);
					if($numRow > 0){

						$dataCategoria = mysqli_fetch_assoc($queryCategoria);
						if($dataCategoria['estatus'] == 1){
							$newEstatus = 0;
						}else{
							$newEstatus = 1;
						}
						$queryUpdEstatus = mysqli_query($conection,"UPDATE categoria SET estatus = $newEstatus WHERE idcategoria= $idcategoria ");
						mysqli_close($conection);
						if($queryUpdEstatus){
							echo $newEstatus;
							exit;
						}else{
							echo "error";
							exit;
						}
					}
				}else{
					echo "error";
				}
			}
			exit;
		}

		// Extraer para eliminar
		if($_POST['action'] == 'infoCategoryDel')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idCategoria'])){
					$idCategoria = intval($_POST['idCategoria']);

					$queryCatPro= mysqli_query($conection,"SELECT * FROM categoria_producto WHERE categoria_id = $idCategoria ");
					$resultCatPro = mysqli_num_rows($queryCatPro);
					if($resultCatPro > 0)
					{
						echo "exist";exit;
					}else{
						$queryCategoria= mysqli_query($conection,"SELECT idcategoria,categoria,descripcion FROM categoria WHERE idcategoria= $idCategoria and estatus !=10 ");

						$resultCategoria = mysqli_num_rows($queryCategoria);
						if($resultCategoria > 0){
							$dataCategory = mysqli_fetch_assoc($queryCategoria);
							echo json_encode($dataCategory,JSON_UNESCAPED_UNICODE);
							exit;
						}
					}

				}
			}
			echo "error";
			exit;
		}

		// Eliminar Categoria
		if($_POST['action'] == 'delCategoria')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['categoria_id']))
				{
					$categoria_id= $_POST['categoria_id'];

					$query_delete = mysqli_query($conection,"UPDATE categoria SET estatus = 10 WHERE idcategoria = $categoria_id ");
					mysqli_close($conection);
					if($query_delete){
						echo 'ok';exit;
					}else{
						echo "error";exit;
					}
				}else{
					echo "error";exit;
				}
			}
			echo "error";
			exit;
		}

		// Extraer una categoria
		if($_POST['action'] == 'infoCategoria')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idCategoria'])){
					$idCategoria = intval($_POST['idCategoria']);
					$arrayDataSub  = array();
					$arrayDataSubCt  = array();

					$queryCategoria= mysqli_query($conection,"SELECT (idcategoria) as id,(categoria) as title,descripcion FROM categoria WHERE idcategoria= $idCategoria and estatus !=10 ");

					$resultCategoria = mysqli_num_rows($queryCategoria);
					if($resultCategoria > 0){
						$querySubCat= mysqli_query($conection,"SELECT (idcategoria) as id,(categoria) as title,descripcion FROM categoria WHERE parent_categoria = $idCategoria and estatus !=10 ");
						$resultSub = mysqli_num_rows($querySubCat);

						if($resultSub > 0){
							while ( $subCat = mysqli_fetch_assoc($querySubCat)) {
								# code...
								$intCatId = intval($subCat['id']);
								$querySubCt = mysqli_query($conection,"SELECT (idcategoria) as id,(categoria) as title,descripcion FROM categoria WHERE parent_categoria = $intCatId and estatus !=10 ");
								$resultSubCt = mysqli_num_rows($querySubCt);

								if($resultSubCt > 0){
									while ( $arrSubCat = mysqli_fetch_assoc($querySubCt)) {
										array_push($arrayDataSubCt,$arrSubCat);
									}
								}
								$subCat['subs'] = $arrayDataSubCt;
								array_push($arrayDataSub,$subCat);
								$arrDataCategoria['subs'] = $arrayDataSub;
							}
						}

						$arrDataCategoria = mysqli_fetch_assoc($queryCategoria);
						$arrDataCategoria['subs'] = $arrayDataSub;

						print_r($arrDataCategoria);

						//echo json_encode($arrDataCategoria,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}

		// Crear marca
		if($_POST['action'] == 'newMarca'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtMarca']) || empty($_POST['txtDescripcionM']))
				{
					$msg='error';
				}else{
					$strMarca 	= ucwords(strClean($_POST['txtMarca']));
					$strDescipcion 	= ucwords(strClean($_POST['txtDescripcionM']));
					$usuario_id = intval($_SESSION['idUser']);
					$query_insert = mysqli_query($conection,"INSERT INTO marca(marca,descripcion,usuarioid)
																VALUES('$strMarca','$strDescipcion',$usuario_id)");

					if($query_insert){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}

			echo 'error';
			exit;
		}

		// Extraer una marca
		if($_POST['action'] == 'infoMarca')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idMarca'])){
					$idMarca = intval($_POST['idMarca']);

					$queryMarca= mysqli_query($conection,"SELECT idmarca,marca,descripcion,estatus FROM marca WHERE idmarca= $idMarca and estatus !=10 ");

					$resultMarca = mysqli_num_rows($queryMarca);

					if($resultMarca > 0){
						$dataMarca = mysqli_fetch_assoc($queryMarca);
						echo json_encode($dataMarca,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}

		// Crear Ubicación
		if($_POST['action'] == 'newUbicacion'){

			if($_SESSION['active'])
			{
				if(empty($_POST['txtUbicacion']) || empty($_POST['txtDescripcionU']))
				{
					$msg='error';
				}else{
					$strUbicacion 	= ucwords(strClean($_POST['txtUbicacion']));
					$strDescipcion 	= ucwords(strClean($_POST['txtDescripcionU']));
					$usuario_id = intval($_SESSION['idUser']);
					$query_insert = mysqli_query($conection,"INSERT INTO ubicacion(ubicacion,descripcion,usuario_id)
																VALUES('$strUbicacion','$strDescipcion',$usuario_id)");

					if($query_insert){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}

			echo 'error';
			exit;
		}

		// Extraer una ubicacion
		if($_POST['action'] == 'infoUbicacion')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idUbicacion'])){
					$idUbicacion = intval($_POST['idUbicacion']);
					$queryUbicacion= mysqli_query($conection,"SELECT id_ubicacion,ubicacion,descripcion,status FROM ubicacion WHERE id_ubicacion= $idUbicacion and status !=10 ");
					$resultUbicacion = mysqli_num_rows($queryUbicacion);
					if($resultUbicacion > 0){
						$dataUbicacion = mysqli_fetch_assoc($queryUbicacion);
						echo json_encode($dataUbicacion,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}

		// Cambiar Estado Ubicacion
		if($_POST['action'] == 'changeEstadoUbicacion')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idUbicacion']))
				{
					$idUbicacion= intval($_POST['idUbicacion']);
					$queryUbicacion= mysqli_query($conection,"SELECT status FROM ubicacion WHERE id_ubicacion= $idUbicacion");
					$numRow = mysqli_num_rows($queryUbicacion);
					if($numRow > 0){

						$dataUbicacion = mysqli_fetch_assoc($queryUbicacion);
						if($dataUbicacion['status'] == 1){
							$newEstatus = 0;
						}else{
							$newEstatus = 1;
						}
						$queryUpdEstatus = mysqli_query($conection,"UPDATE ubicacion SET status = $newEstatus WHERE id_ubicacion= $idUbicacion ");
						mysqli_close($conection);
						if($queryUpdEstatus){
							echo $newEstatus;
							exit;
						}else{
							echo "error";
							exit;
						}
					}
				}else{
					echo "error";
				}
			}
			exit;
		}

		// Update Ubicación
		if($_POST['action'] == 'updUbicacion'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtUbicacion']) || empty($_POST['txtDescripcionU']))
				{
					$msg='error';
				}else{
					$intIdUbicacion = intval($_POST['txtIdUbicacion']);
					$strUbicacion 	= ucwords(strClean($_POST['txtUbicacion']));
					$strDescipcion  = ucwords(strClean($_POST['txtDescripcionU']));
					$query_update = mysqli_query($conection,"UPDATE ubicacion SET ubicacion = '$strUbicacion', descripcion = '$strDescipcion' WHERE id_ubicacion = $intIdUbicacion ");

					if($query_update){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}
			echo 'error';
			exit;
		}

		// Extraer para eliminar ubicación
		if($_POST['action'] == 'infoUbicacionDel')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idUbicacion'])){
					$idUbicacion = intval($_POST['idUbicacion']);
					$queryMrPro= mysqli_query($conection,"SELECT * FROM producto WHERE ubicacion_id = $idUbicacion ");
					$resultMrPro = mysqli_num_rows($queryMrPro);
					if($resultMrPro > 0)
					{
						echo "exist";exit;
					}else{
						$queryUbicacion= mysqli_query($conection,"SELECT id_ubicacion,ubicacion,descripcion FROM ubicacion WHERE id_ubicacion= $idUbicacion and status !=10 ");

						$resultUbicacion = mysqli_num_rows($queryUbicacion);
						if($resultUbicacion > 0){
							$dataUbicacion = mysqli_fetch_assoc($queryUbicacion);
							echo json_encode($dataUbicacion,JSON_UNESCAPED_UNICODE);
							exit;
						}
					}

				}
			}
			echo "error";
			exit;
		}

		// Eliminar Ubicación
		if($_POST['action'] == 'delUbicacion')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['ubicacion_id']))
				{
					$ubicacion_id= $_POST['ubicacion_id'];

					$query_delete = mysqli_query($conection,"UPDATE ubicacion SET status = 10 WHERE id_ubicacion = $ubicacion_id ");
					mysqli_close($conection);
					if($query_delete){
						echo 'ok';exit;
					}else{
						echo "error";exit;
					}
				}else{
					echo "error";exit;
				}
			}
			echo "error";
			exit;
		}

		// Update categoria
		if($_POST['action'] == 'updMarca'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtMarca']) || empty($_POST['txtDescripcionM']))
				{
					$msg='error';
				}else{
					$intIdMarca = intval($_POST['txtIdMarca']);
					$strMarca 	= ucwords(strClean($_POST['txtMarca']));
					$strDescipcion  = ucwords(strClean($_POST['txtDescripcionM']));
					$query_update = mysqli_query($conection,"UPDATE marca SET marca = '$strMarca', descripcion = '$strDescipcion' WHERE idmarca = $intIdMarca ");

					if($query_update){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}

			echo 'error';
			exit;
		}

		// Extraer para eliminar marca
		if($_POST['action'] == 'infoMarcaDel')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idMarca'])){
					$idMarca = intval($_POST['idMarca']);

					$queryMrPro= mysqli_query($conection,"SELECT * FROM producto WHERE marca_id = $idMarca ");
					$resultMrPro = mysqli_num_rows($queryMrPro);
					if($resultMrPro > 0)
					{
						echo "exist";exit;
					}else{
						$queryMarca= mysqli_query($conection,"SELECT idmarca,marca,descripcion FROM marca WHERE idmarca= $idMarca and estatus !=10 ");

						$resultMarca = mysqli_num_rows($queryMarca);
						if($resultMarca > 0){
							$dataMarca = mysqli_fetch_assoc($queryMarca);
							echo json_encode($dataMarca,JSON_UNESCAPED_UNICODE);
							exit;
						}
					}

				}
			}
			echo "error";
			exit;
		}

		// Eliminar Marca
		if($_POST['action'] == 'delMarca')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['marca_id']))
				{
					$marca_id= $_POST['marca_id'];

					$query_delete = mysqli_query($conection,"UPDATE marca SET estatus = 10 WHERE idmarca = $marca_id ");
					mysqli_close($conection);
					if($query_delete){
						echo 'ok';exit;
					}else{
						echo "error";exit;
					}
				}else{
					echo "error";exit;
				}
			}
			echo "error";
			exit;
		}

		// Cambiar Estado Marca
		if($_POST['action'] == 'changeEstadoMarca')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idMarca']))
				{
					$idmarca= intval($_POST['idMarca']);
					$queryMarca= mysqli_query($conection,"SELECT estatus FROM marca WHERE idmarca= $idmarca");
					$numRow = mysqli_num_rows($queryMarca);
					if($numRow > 0){

						$dataMarca = mysqli_fetch_assoc($queryMarca);
						if($dataMarca['estatus'] == 1){
							$newEstatus = 0;
						}else{
							$newEstatus = 1;
						}
						$queryUpdEstatus = mysqli_query($conection,"UPDATE marca SET estatus = $newEstatus WHERE idmarca= $idmarca ");
						mysqli_close($conection);
						if($queryUpdEstatus){
							echo $newEstatus;
							exit;
						}else{
							echo "error";
							exit;
						}
					}
				}else{
					echo "error";
				}
			}
			exit;
		}

		// Procesa Compra
		if($_POST['action'] == 'procesarCompra'){

			if($_SESSION['active'])
			{
				$documento 	= intval($_POST['documento']);
				$noDocumento= intval($_POST['noDocumento']);
				$serie 		= $_POST['serie'];
				$fechaCompra= $_POST['fechaCompra'];
				$proveedor 	= intval($_POST['proveedor']);
				$tipoPago 	= intval($_POST['tipoPago']);
				$usuario 	= intval($_SESSION['idUser']);
				$token 		= md5($_SESSION['idUser']);
				$total 		= 0;

				$queryProductoCompra = mysqli_query($conection,"SELECT * FROM detalle_temp WHERE token_user = '$token' AND operacion =  0 ");
				$result = mysqli_num_rows($queryProductoCompra);

				$arrProdut = mysqli_fetch_assoc($queryProductoCompra);

				if($result > 0)
				{
					//CREAR COMPRA
					$insertCompra = mysqli_query($conection,"INSERT INTO compra(
																				documento_id,
																				no_documento,
																				serie,
																				fecha_compra,
																				proveedor_id,
																				tipopago_id,
																				total,
																				usuario)
																	 	 VALUES($documento,
																	 	 		$noDocumento,
																	 	 		'$serie',
																	 	 		'$fechaCompra',
																	 	 		$proveedor,
																	 	 		$tipoPago,
																	 	 		$total,
																	 	 		$usuario)");
					if($insertCompra){
						$compraId = mysqli_insert_id($conection);

						$query_procesar = mysqli_query($conection,"CALL procesar_compra($usuario,$proveedor,$compraId,'$token')");
						$result_detalle = mysqli_num_rows($query_procesar);
						mysqli_close($conection);

						if($result_detalle > 0){
							//echo "ok";
							$data	= mysqli_fetch_assoc($query_procesar);
							$arrResponse = array('status' => true, 'compra_id' =>$data['id_compra']);
							echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
							exit;
						}else{
							$arrResponse = array('status' => false);
							echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
						}
					}
				}
			}else{
				$arrResponse = array('status' => false);
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}

			mysqli_close($conection);
			exit;
		}
		// Crear documento
		if($_POST['action'] == 'newDocumento'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtDocumento']) || empty($_POST['txtDescripcion']))
				{
					$msg='error';
				}else{
					$strDocumento 	= ucwords(strClean($_POST['txtDocumento']));
					$strDescipcion  = ucwords(strClean($_POST['txtDescripcion']));
					$usuario_id = intval($_SESSION['idUser']);
					$query_insert = mysqli_query($conection,"INSERT INTO tipo_documento(documento,descripcion,usuario_id)
																VALUES('$strDocumento','$strDescipcion',$usuario_id)");

					if($query_insert){
						$msg = 'ok';
					}else{
						$msg='error';
					}

				}
				echo $msg;
				exit;
			}

			echo 'error';
			exit;
		}
		// Extraer un documento
		if($_POST['action'] == 'infoDocumento')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idDocumento'])){
					$idCategoria = intval($_POST['idDocumento']);

					$queryCategoria= mysqli_query($conection,"SELECT (id_tipodocumento) as idDocumento,documento,descripcion,estatus FROM tipo_documento WHERE id_tipodocumento= $idCategoria and estatus !=10 ");

					$resultCategoria = mysqli_num_rows($queryCategoria);
					if($resultCategoria > 0){
						$dataCategory = mysqli_fetch_assoc($queryCategoria);
						echo json_encode($dataCategory,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}
		// Update Documento
		if($_POST['action'] == 'updDocumento'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtDocumento']) || empty($_POST['txtDescripcion']) || empty($_POST['txtIdDocumento']))
				{
					$msg='error';
				}else{
					$intIdDocumento = intval($_POST['txtIdDocumento']);
					$strDocumento 	= ucwords(strClean($_POST['txtDocumento']));
					$strDescipcion  = ucwords(strClean($_POST['txtDescripcion']));

					$query_update = mysqli_query($conection,"UPDATE tipo_documento SET documento = '$strDocumento', descripcion = '$strDescipcion' WHERE id_tipodocumento = $intIdDocumento ");
					if($query_update){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}

			echo 'error';
			exit;
		}
		// Extraer para eliminar
		if($_POST['action'] == 'infoDocumentoDel')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idDocumento'])){
					$idDocumento = intval($_POST['idDocumento']);

					$queryDoc= mysqli_query($conection,"SELECT * FROM compra WHERE documento_id = $idDocumento ");
					$resultDoc = mysqli_num_rows($queryDoc);
					if($resultDoc > 0)
					{
						echo "exist";exit;
					}else{
						$queryDocumento= mysqli_query($conection,"SELECT (id_tipodocumento) as iddocumento,documento,descripcion FROM tipo_documento WHERE id_tipodocumento= $idDocumento and estatus !=10 ");

						$resultDocumento = mysqli_num_rows($queryDocumento);
						if($resultDocumento > 0){
							$dataDocumento = mysqli_fetch_assoc($queryDocumento);
							echo json_encode($dataDocumento,JSON_UNESCAPED_UNICODE);
							exit;
						}
					}

				}
			}
			echo "error";
			exit;
		}
		// Eliminar documento
		if($_POST['action'] == 'delDocumento')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['documento_id']))
				{
					$documento_id= $_POST['documento_id'];

					$query_delete = mysqli_query($conection,"UPDATE tipo_documento SET estatus = 10 WHERE id_tipodocumento = $documento_id ");
					mysqli_close($conection);
					if($query_delete){
						echo 'ok';exit;
					}else{
						echo "error";exit;
					}
				}else{
					echo "error";exit;
				}
			}
			echo "error";
			exit;
		}
		// Cambiar Estado Documento
		if($_POST['action'] == 'changeEstadoDocumento')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idDocumento']))
				{
					$idDocumento= intval($_POST['idDocumento']);
					$queryDoc= mysqli_query($conection,"SELECT estatus FROM tipo_documento WHERE id_tipodocumento= $idDocumento");
					$numRow = mysqli_num_rows($queryDoc);
					if($numRow > 0){

						$dataDoc = mysqli_fetch_assoc($queryDoc);
						if($dataDoc['estatus'] == 1){
							$newEstatus = 0;
						}else{
							$newEstatus = 1;
						}
						$queryUpdEstatus = mysqli_query($conection,"UPDATE tipo_documento SET estatus = $newEstatus WHERE id_tipodocumento= $idDocumento ");
						mysqli_close($conection);
						if($queryUpdEstatus){
							echo $newEstatus;
							exit;
						}else{
							echo "error";
							exit;
						}
					}
				}else{
					echo "error";
				}
			}
			exit;
		}
		// Update Documento
		if($_POST['action'] == 'updFormaPago'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtFormaPago']) || empty($_POST['txtDescripcion']) || empty($_POST['txtIdFormaPago']))
				{
					$msg='error';
				}else{
					$intIdFormaPago = intval($_POST['txtIdFormaPago']);
					$strFormaPago 	= $_POST['txtFormaPago'];
					$strDescipcion  = $_POST['txtDescripcion'];
					$query_update = mysqli_query($conection,"UPDATE tipo_pago SET tipo_pago = '$strFormaPago', descripcion = '$strDescipcion' WHERE id_tipopago = $intIdFormaPago ");
					if($query_update){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}

			echo 'error';
			exit;
		}
		// Crear Forma Pago
		if($_POST['action'] == 'newFormaPago'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtFormaPago']) || empty($_POST['txtDescripcion']))
				{
					$msg='error';
				}else{
					$strFormaPago 	= $_POST['txtFormaPago'];
					$strDescipcion  = $_POST['txtDescripcion'];
					$query_insert = mysqli_query($conection,"INSERT INTO tipo_pago(tipo_pago,descripcion)
																VALUES('$strFormaPago','$strDescipcion')");
					if($query_insert){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}
			echo 'error';
			exit;
		}
		// Extraer Forma pago
		if($_POST['action'] == 'infoFormaPago')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idFormaPago'])){
					$idFormaPago = intval($_POST['idFormaPago']);

					$queryFormaPago= mysqli_query($conection,"SELECT (id_tipopago) as idformapago,tipo_pago,descripcion,estatus FROM tipo_pago WHERE id_tipopago= $idFormaPago and estatus !=10 ");
					$resultFormaPago = mysqli_num_rows($queryFormaPago);
					if($resultFormaPago > 0){
						$dataCategory = mysqli_fetch_assoc($queryFormaPago);
						echo json_encode($dataCategory,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}
		// Crear Impuesto
		if($_POST['action'] == 'newImpuesto'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtImpuesto']) || empty($_POST['txtDescripcion']))
				{
					$msg='error';
				}else{
					$strImpuesto = strClean($_POST['txtImpuesto']);
					$strDescipcion = strClean($_POST['txtDescripcion']);
					
					$queryImpuesto= mysqli_query($conection,"SELECT * FROM impuesto WHERE impuesto= $strImpuesto ");
					$resultImpuesto = mysqli_num_rows($queryImpuesto);
					if($resultImpuesto > 0){
						$msg="exist";
					}else{
						$query_insert = mysqli_query($conection,"INSERT INTO impuesto(impuesto,descripcion)
																	VALUES('$strImpuesto','$strDescipcion')");
						if($query_insert){
							$msg = 'ok';
						}else{
							$msg='error';
						}
					}


				}
				echo $msg;
				exit;
			}
			echo 'error';
			exit;
		}
		// Extraer Impuesto
		if($_POST['action'] == 'infoImpuesto')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idImpuesto'])){
					$idImpuesto = intval($_POST['idImpuesto']);

					$queryImpuesto= mysqli_query($conection,"SELECT idimpuesto,impuesto,descripcion,status FROM impuesto WHERE idimpuesto= $idImpuesto and status !=10 ");
					$resultImpuesto = mysqli_num_rows($queryImpuesto);
					if($resultImpuesto > 0){
						$dataImpuesto = mysqli_fetch_assoc($queryImpuesto);
						echo json_encode($dataImpuesto,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}
		// Update Impuesto
		if($_POST['action'] == 'updImpuesto'){
			if($_SESSION['active'])
			{
				$impuesto = intval($_POST['txtImpuesto']);
				if($impuesto < 0 || empty($_POST['txtDescripcion']) || empty($_POST['txtIdImpuesto']))
				{
					$msg='error';
				}else{
					$intIdImpuesto = intval($_POST['txtIdImpuesto']);
					$strImpuesto = strClean($_POST['txtImpuesto']);
					$strDescipcion = strClean($_POST['txtDescripcion']);

					$query = mysqli_query($conection,"SELECT * FROM impuesto
													   WHERE (impuesto = '$strImpuesto' AND idimpuesto != $intIdImpuesto)");
					$result = mysqli_num_rows($query);
					if($result > 0)
					{
						$msg='exist';
					}else{
						$query_update = mysqli_query($conection,"UPDATE impuesto SET impuesto = '$strImpuesto', descripcion = '$strDescipcion' WHERE idimpuesto = $intIdImpuesto ");
						if($query_update){
							$msg = 'ok';
						}else{
							$msg='error';
						}
					}
				}
				echo $msg;
				exit;
			}
			echo 'error';
			exit;
		}
		// Cambiar Estado Impuesto
		if($_POST['action'] == 'changeEstadoImpuesto')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idImpuesto']))
				{
					$idImpuesto= intval($_POST['idImpuesto']);
					$queryImpuesto= mysqli_query($conection,"SELECT status FROM impuesto WHERE idimpuesto= $idImpuesto");
					$numRow = mysqli_num_rows($queryImpuesto);
					if($numRow > 0){
						$dataDoc = mysqli_fetch_assoc($queryImpuesto);
						if($dataDoc['status'] == 1){
							$newEstatus = 0;
						}else{
							$newEstatus = 1;
						}
						$queryUpdEstatus = mysqli_query($conection,"UPDATE impuesto SET status = $newEstatus WHERE idimpuesto= $idImpuesto ");
						mysqli_close($conection);
						if($queryUpdEstatus){
							echo $newEstatus;
							exit;
						}else{
							echo "error";
							exit;
						}
					}
				}else{
					echo "error";
				}
			}
			exit;
		}
		// Extraer para eliminar impuesto
		if($_POST['action'] == 'infoImpuestoDel')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idImpuesto'])){
					$idimpuesto = intval($_POST['idImpuesto']);
					$query= mysqli_query($conection,"SELECT * FROM producto WHERE impuesto_id = $idimpuesto ");
					$result = mysqli_num_rows($query);
					if($result > 0)
					{
						echo "exist";exit;
					}else{
						$queryImpuesto= mysqli_query($conection,"SELECT idimpuesto,impuesto,descripcion FROM impuesto WHERE idimpuesto = $idimpuesto and status !=10 ");
						$resultImpuesto = mysqli_num_rows($queryImpuesto);
						if($resultImpuesto > 0){
							$data = mysqli_fetch_assoc($queryImpuesto);
							echo json_encode($data,JSON_UNESCAPED_UNICODE);
							exit;
						}
					}
				}
			}
			echo "error";
			exit;
		}
		// Eliminar impuesto
		if($_POST['action'] == 'delImpuesto')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idimpuesto']))
				{
					$idimpuesto= $_POST['idimpuesto'];
					$query_delete = mysqli_query($conection,"UPDATE impuesto SET status = 10 WHERE idimpuesto = $idimpuesto ");
					mysqli_close($conection);
					if($query_delete){
						echo 'ok';exit;
					}else{
						echo "error";exit;
					}
				}else{
					echo "error";exit;
				}
			}
			echo "error";
			exit;
		}
		// Crear serie
		if($_POST['action'] == 'newSerie'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtCai']) || empty($_POST['txtPrefijoFactura']) || empty($_POST['txtPeriodoInicio']) || empty($_POST['txtPeriodoFin']) || empty($_POST['txtRango']) )
				{
					$msg='error';
				}else{
					$arrRango = explode("-", $_POST['txtRango']);
					$noInicio = $arrRango[0];
					$noFin = $arrRango[1];
					$strCai 	= $_POST['txtCai'];
					$strPrefijo  = $_POST['txtPrefijoFactura'];
					$strFechaInicio  = $_POST['txtPeriodoInicio'];
					$strFechaFin  = $_POST['txtPeriodoFin'];					
					$usuario_id = intval($_SESSION['idUser']);
					$ceros = intval($_POST['txtCeros']);
					$query_insert = mysqli_query($conection,"INSERT INTO facturas(cai,prefijo,periodo_inicio,periodo_fin,no_inicio,no_fin,ceros,usuarioid,status)
																VALUES('$strCai','$strPrefijo','$strFechaInicio','$strFechaFin',$noInicio,$noFin,$ceros,$usuario_id,'0')");

					if($query_insert){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}
			echo 'error';
			exit;
		}
		// Cambiar Estado Serie
		if($_POST['action'] == 'changeEstadoSerie')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idSerie']))
				{
					$idSerie= intval($_POST['idSerie']);
					$querySerie= mysqli_query($conection,"SELECT status FROM facturas WHERE idserie= $idSerie");
					$numRow = mysqli_num_rows($querySerie);
					if($numRow > 0){
						$dataDoc = mysqli_fetch_assoc($querySerie);
						if($dataDoc['status'] == 1){
							$newEstatus = 0;
						}else{
							$newEstatus = 1;
						}
						$queryUpdEstatus = mysqli_query($conection,"UPDATE facturas SET status = $newEstatus WHERE idserie= $idSerie ");
						mysqli_close($conection);
						if($queryUpdEstatus){
							echo $newEstatus;
							exit;
						}else{
							echo "error";
							exit;
						}
					}
				}else{
					echo "error";
				}
			}
			exit;
		}
		// Extraer Serie
		if($_POST['action'] == 'infoSerie')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idSerie'])){
					$idSerie = intval($_POST['idSerie']);
					$querySerie= mysqli_query($conection,"SELECT idserie,cai,prefijo,periodo_inicio,periodo_fin,no_inicio,no_fin,ceros,status FROM facturas WHERE idserie= $idSerie and status !=10 ");
					$resultSerie = mysqli_num_rows($querySerie);
					if($resultSerie > 0){
						$dataSerie = mysqli_fetch_assoc($querySerie);
						echo json_encode($dataSerie,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}
		// Extraer para eliminar
		if($_POST['action'] == 'infoSerieDel')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idSerie'])){
					$idSerie = intval($_POST['idSerie']);

					$queryFac= mysqli_query($conection,"SELECT * FROM factura WHERE serieid = $idSerie ");
					$resultFac = mysqli_num_rows($queryFac);
					if($resultFac > 0)
					{
						echo "exist";exit;
					}else{
						$querySerie= mysqli_query($conection,"SELECT idserie,cai,DATE_FORMAT(periodo_inicio, '%d/%m/%Y') as fecha_inicio,DATE_FORMAT(periodo_fin, '%d/%m/%Y') as fecha_fin,no_inicio,no_fin FROM facturas WHERE idserie= $idSerie and status !=10 ");
						$resultSerie = mysqli_num_rows($querySerie);
						if($resultSerie > 0){
							$dataSerie = mysqli_fetch_assoc($querySerie);
							echo json_encode($dataSerie,JSON_UNESCAPED_UNICODE);
							exit;
						}
					}

				}
			}
			echo "error";
			exit;
		}
		// Eliminar Serie
		if($_POST['action'] == 'delSerie')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['serie_id']))
				{
					$serie_id= $_POST['serie_id'];

					$query_delete = mysqli_query($conection,"UPDATE facturas SET status = 10 WHERE idserie = $serie_id ");
					mysqli_close($conection);
					if($query_delete){
						echo 'ok';exit;
					}else{
						echo "error";exit;
					}
				}else{
					echo "error";exit;
				}
			}
			echo "error";
			exit;
		}
		
		// Update Serie
		if($_POST['action'] == 'updSerie'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtIdSerie']) || empty($_POST['txtCai']) || empty($_POST['txtPrefijoFactura']) || empty($_POST['txtPeriodoInicio']) || empty($_POST['txtPeriodoFin']) || empty($_POST['txtRango']))
				{
					$msg='error';
				}else{
					$intIdSerie = intval($_POST['txtIdSerie']);
					$strCai 	= $_POST['txtCai'];
					$strPrefijo  = $_POST['txtPrefijoFactura'];
					$strPeriodoInicio  = $_POST['txtPeriodoInicio'];
					$strPeriodoFin  = $_POST['txtPeriodoFin'];
					$strPrefijo  = $_POST['txtPrefijoFactura'];
					$arrRango = explode("-", $_POST['txtRango']);
					$noInicio = $arrRango[0];
					$noFin = $arrRango[1];
					$intCero = intval($_POST['txtCeros']);
					$query_update = mysqli_query($conection,"UPDATE facturas SET cai = '$strCai', prefijo = '$strPrefijo', periodo_inicio = '$strPeriodoInicio', periodo_fin = '$strPeriodoFin', no_inicio = $noInicio, no_fin = $noFin, ceros = $intCero WHERE idserie = $intIdSerie ");
					if($query_update){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}

			echo 'error';
			exit;
		}


		// Crear forma de pago
		if($_POST['action'] == 'newTipoPago'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtTipoPago']) || empty($_POST['txtDescripcion']))
				{
					$msg='error';
				}else{
					$strTipoPago 	= ucwords(strClean($_POST['txtTipoPago']));
					$strDescipcion  = ucwords(strClean($_POST['txtDescripcion']));
					$query_insert = mysqli_query($conection,"INSERT INTO tipo_pago(tipo_pago,descripcion)
																VALUES('$strTipoPago','$strDescipcion')");

					if($query_insert){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}
			echo 'error';
			exit;
		}
		// Extraer una forma de pago
		if($_POST['action'] == 'infoTipoPago')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idTipoPago'])){
					$idTipoPago = intval($_POST['idTipoPago']);

					$queryTipoPago= mysqli_query($conection,"SELECT id_tipopago,tipo_pago,descripcion,estatus FROM tipo_pago WHERE id_tipopago= $idTipoPago and estatus !=10 ");
					$result = mysqli_num_rows($queryTipoPago);
					if($result > 0){
						$data = mysqli_fetch_assoc($queryTipoPago);
						echo json_encode($data,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}
		// Update forma pago
		if($_POST['action'] == 'updTipoPago'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtTipoPago']) || empty($_POST['txtDescripcion']) || empty($_POST['txtIdTipoPago']))
				{
					$msg='error';
				}else{
					$intIdTipoPago = intval($_POST['txtIdTipoPago']);
					$strTipoPago 	= ucwords(strClean($_POST['txtTipoPago']));
					$strDescipcion  = ucwords(strClean($_POST['txtDescripcion']));

					$query_update = mysqli_query($conection,"UPDATE tipo_pago SET tipo_pago = '$strTipoPago', descripcion = '$strDescipcion' WHERE id_tipopago = $intIdTipoPago ");
					if($query_update){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}
			echo 'error';
			exit;
		}
		// Extraer para eliminar tipo pago
		if($_POST['action'] == 'infoTipoPagoDel')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idFormaPago'])){
					$idTipoPago = intval($_POST['idFormaPago']);
					$query= mysqli_query($conection,"SELECT * FROM factura WHERE tipopago_id = $idTipoPago ");
					$result = mysqli_num_rows($query);
					if($result > 0)
					{
						echo "exist";exit;
					}else{
						$queryTipoPago= mysqli_query($conection,"SELECT id_tipopago,tipo_pago,descripcion FROM tipo_pago WHERE id_tipopago = $idTipoPago and estatus !=10 ");

						$resultTipoPago = mysqli_num_rows($queryTipoPago);
						if($resultTipoPago > 0){
							$data = mysqli_fetch_assoc($queryTipoPago);
							echo json_encode($data,JSON_UNESCAPED_UNICODE);
							exit;
						}
					}
				}
			}
			echo "error";
			exit;
		}
		// Cambiar Estado Documento
		if($_POST['action'] == 'changeEstadoFormaPago')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idFormaPago']))
				{
					$idTipoPago= intval($_POST['idFormaPago']);
					$queryDoc= mysqli_query($conection,"SELECT estatus FROM tipo_pago WHERE id_tipopago= $idTipoPago");
					$numRow = mysqli_num_rows($queryDoc);
					if($numRow > 0){

						$dataDoc = mysqli_fetch_assoc($queryDoc);
						if($dataDoc['estatus'] == 1){
							$newEstatus = 0;
						}else{
							$newEstatus = 1;
						}
						$queryUpdEstatus = mysqli_query($conection,"UPDATE tipo_pago SET estatus = $newEstatus WHERE id_tipopago= $idTipoPago ");
						mysqli_close($conection);
						if($queryUpdEstatus){
							echo $newEstatus;
							exit;
						}else{
							echo "error";
							exit;
						}
					}
				}else{
					echo "error";
				}
			}
			exit;
		}
		// Eliminar documento
		if($_POST['action'] == 'delFormaPago')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['formapago_id']))
				{
					$id_tipopago= $_POST['formapago_id'];

					$query_delete = mysqli_query($conection,"UPDATE tipo_pago SET estatus = 10 WHERE id_tipopago = $id_tipopago ");
					mysqli_close($conection);
					if($query_delete){
						echo 'ok';exit;
					}else{
						echo "error";exit;
					}
				}else{
					echo "error";exit;
				}
			}
			echo "error";
			exit;
		}
		// Crear Presentación
		if($_POST['action'] == 'newPresentacion'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtPresentacion']) || empty($_POST['txtDescripcion']))
				{
					$msg='error';
				}else{
					$strPresentacion = ucwords(strClean($_POST['txtPresentacion']));
					$strDescipcion  = ucwords(strClean($_POST['txtDescripcion']));
					$usuario_id = intval($_SESSION['idUser']);
					$query_insert = mysqli_query($conection,"INSERT INTO presentacion_producto(presentacion,descripcion,usuarioid)
																VALUES('$strPresentacion','$strDescipcion',$usuario_id)");

					if($query_insert){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}

			echo 'error';
			exit;
		}

		// Extraer una presetnación
		if($_POST['action'] == 'infoPresentacion')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idPresentacion'])){
					$idPresentacion = intval($_POST['idPresentacion']);

					$queryPresentacion= mysqli_query($conection,"SELECT (id_presentacion) as idPresentacion,presentacion,descripcion,estatus FROM presentacion_producto WHERE id_presentacion= $idPresentacion and estatus !=10 ");

					$resultPresentacion = mysqli_num_rows($queryPresentacion);
					if($resultPresentacion > 0){
						$dataPresentacion = mysqli_fetch_assoc($queryPresentacion);
						echo json_encode($dataPresentacion,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}
		// Update presentación
		if($_POST['action'] == 'updPresentacion'){
			if($_SESSION['active'])
			{
				if(empty($_POST['txtPresentacion']) || empty($_POST['txtDescripcion']) || empty($_POST['txtIdPresentacion']))
				{
					$msg='error';
				}else{
					$intIdPresentacion = intval($_POST['txtIdPresentacion']);
					$strPresentacion = ucwords(strClean($_POST['txtPresentacion']));
					$strDescipcion  = ucwords(strClean($_POST['txtDescripcion']));

					$query_update = mysqli_query($conection,"UPDATE presentacion_producto SET presentacion = '$strPresentacion', descripcion = '$strDescipcion' WHERE id_presentacion = $intIdPresentacion ");
					if($query_update){
						$msg = 'ok';
					}else{
						$msg='error';
					}
				}
				echo $msg;
				exit;
			}

			echo 'error';
			exit;
		}
		// Extraer para eliminar
		if($_POST['action'] == 'infoPresentacionDel')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idPresentacion'])){
					$idPresentacion = intval($_POST['idPresentacion']);

					$queryDoc= mysqli_query($conection,"SELECT * FROM producto WHERE presentacion_id = $idPresentacion ");
					$resultDoc = mysqli_num_rows($queryDoc);
					if($resultDoc > 0)
					{
						echo "exist";exit;
					}else{
						$queryPresentacion= mysqli_query($conection,"SELECT (id_presentacion) as idpresentacion,presentacion,descripcion FROM presentacion_producto WHERE id_presentacion= $idPresentacion and estatus !=10 ");

						$resultPresentacion = mysqli_num_rows($queryPresentacion);
						if($resultPresentacion > 0){
							$dataPresentacion = mysqli_fetch_assoc($queryPresentacion);
							echo json_encode($dataPresentacion,JSON_UNESCAPED_UNICODE);
							exit;
						}
					}

				}
			}
			echo "error";
			exit;
		}
		// Eliminar presentación
		if($_POST['action'] == 'delPresentacion')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['presentacion_id']))
				{
					$presentacion_id= $_POST['presentacion_id'];

					$query_delete = mysqli_query($conection,"UPDATE presentacion_producto SET estatus = 10 WHERE id_presentacion = $presentacion_id ");
					mysqli_close($conection);
					if($query_delete){
						echo 'ok';exit;
					}else{
						echo "error";exit;
					}
				}else{
					echo "error";exit;
				}
			}
			echo "error";
			exit;
		}
		// Extraer un presentacion
		if($_POST['action'] == 'infoPreentacion')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idPresentacion'])){
					$idPresentacion = intval($_POST['idPresentacion']);

					$queryPresentacion= mysqli_query($conection,"SELECT (id_presentacion) as idPresentacion,presentacion,descripcion,estatus FROM presentacion_producto WHERE id_presentacion= $idPresentacion and estatus !=10 ");

					$resultPresentacion = mysqli_num_rows($queryPresentacion);
					if($resultPresentacion > 0){
						$dataPresentacion = mysqli_fetch_assoc($queryPresentacion);
						echo json_encode($dataPresentacion,JSON_UNESCAPED_UNICODE);
						exit;
					}
				}
			}
			echo "error";
			exit;
		}
		// Cambiar Estado Documento
		if($_POST['action'] == 'changeEstadoPresentacion')
		{
			if($_SESSION['active'])
			{
				if(!empty($_POST['idPresentacion']))
				{
					$idPresentacion= intval($_POST['idPresentacion']);
					$queryPre= mysqli_query($conection,"SELECT estatus FROM presentacion_producto WHERE id_presentacion= $idPresentacion");
					$numRow = mysqli_num_rows($queryPre);
					if($numRow > 0){

						$dataPre = mysqli_fetch_assoc($queryPre);
						if($dataPre['estatus'] == 1){
							$newEstatus = 0;
						}else{
							$newEstatus = 1;
						}
						$queryUpdEstatus = mysqli_query($conection,"UPDATE presentacion_producto SET estatus = $newEstatus WHERE id_presentacion= $idPresentacion ");
						mysqli_close($conection);
						if($queryUpdEstatus){
							echo $newEstatus;
							exit;
						}else{
							echo "error";
							exit;
						}
					}
				}else{
					echo "error";
				}
			}
			exit;
		}

		//Vendedores del mes
		if($_POST['action'] == 'vendedoresMes')
		{
			$nFecha = str_replace(" ","",$_POST['fecha']);
			$arrFecha = explode('-',$nFecha);
			$mes = $arrFecha[0];
			$anio = $arrFecha[1];
			$content ='';

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

			if($result_v_mes > 0){
					$no=1;
					while ($vendedor = mysqli_fetch_assoc($query_v_mes)) {
			$content .= '
				<tr>
					<td class="textcenter">'.$no.'</td>
					<td class="textleft"><img src="img/user.png" alt="'.$vendedor['nombre'].'" class="photouser">'.$vendedor['nombre'].'</td>
					<td class="textcenter">'.$vendedor['cant'].'</td>
					<td class="textright">'.SIMBOLO_MONEDA.'. '.$vendedor['total'].'</td>
				</tr>';
					$no++;
					}
				}

			echo json_encode($content,JSON_UNESCAPED_UNICODE);
			exit;
		}
		//Ventas del mes
		if($_POST['action'] == 'ventasMes')
		{
			ini_set('display_errors', 1);
		    ini_set('display_startup_errors', 1);
		    error_reporting(E_ALL);

			$nFecha = str_replace(" ","",$_POST['fecha']);
			$arrFecha = explode('-',$nFecha);
			$mes = $arrFecha[0];
			$anio = $arrFecha[1];
			$idUser   = $_SESSION['idUser'];
			$content ='';
			$contentScript = '';
			$grafica ='ventasMesDia';

			if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
			{
				$were_gmes = '';
			}else{
				$were_gmes = ' AND usuario = '.$idUser;

			}

			$dias = cal_days_in_month(CAL_GREGORIAN,$mes, $anio); // 31
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

			ob_start();
		    include('includes/grafica.php');
		    $htmlGrafica = ob_get_clean();
		    echo $htmlGrafica;
			exit;
		}
		//Ventas del año por mes
		if($_POST['action'] == 'ventasAnio')
		{
			$anio = $_POST['anio'];
			$mes = date('m');
			$idUser = $_SESSION['idUser'];
			$grafica ='ventasAnioMes';

			$arrVentaMeses = array();
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

			ob_start();
		    include('includes/grafica.php');
		    $htmlGrafica = ob_get_clean();
		    echo $htmlGrafica;
			exit;
		}

		//Compra Ventas del año por mes
		if($_POST['action'] == 'grCompraVentaAnio')
		{
			
			$anio = $_POST['anio'];
			$mes = date('m');
			$idUser = $_SESSION['idUser'];	
			$objeto = $_POST['objeto'];

			if($objeto == 'tabla'){
				$grafica ='tableVentasComprasAnioMes';		
			}else{
				$grafica ='ventasComprasAnioMes';
			}

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

			ob_start();
		    include('includes/grafica.php');
		    $htmlGrafica = ob_get_clean();

		    print_r($htmlGrafica);exit;
		    echo $htmlGrafica;
			exit;
		}

		//Tabla Compra Ventas del año por mes
		if($_POST['action'] == 'grCompraVentaAnio')
		{
			$anio = $_POST['anio'];
			$mes = date('m');
			$idUser = $_SESSION['idUser'];	
			$grafica ='ventasComprasAnioMes';


			exit;

		}

		//Enviar factura por correo
		if($_POST['action'] == 'ajaxSendEmail'){
			//dep($_POST);exit;
			if(!empty($_POST['cliente']) and !empty($_POST['factura']) and !empty($_POST['urlVenta'])){
				$idCliente = $_POST['cliente'];
				$idVenta = $_POST['factura'];
				$urlPDF = $_POST['urlVenta'];

				$cliente = decrypt($idCliente, $idVenta);
				$arrCliente = explode('_',$cliente);
				$codCliente = $arrCliente[1];

				$venta = decrypt($idVenta, $codCliente);
				$arrVenta = explode('_',$venta);
				$noFactura = $arrVenta[1];

				$querySelect= mysqli_query($conection,"SELECT * FROM cliente WHERE idcliente = '$codCliente' ");
				$numRow = mysqli_num_rows($querySelect);
				if($numRow > 0){
					$infoCliente = mysqli_fetch_assoc($querySelect);
					$nombreCliente = $infoCliente['nombre'];
					$emailCliente = $infoCliente['correo'];
					if($emailCliente != ''){
						//ENVIO DE CORREO
						$emailRemitente = EMAIL_FACTURAS;
						//Data email Cliente
						$dataPedidoCliente = array('nombreCliente' => $nombreCliente,'emailDestino' => $emailCliente,'emailRemitente' => $emailRemitente,'asunto' => 'Comprobante electrónico','urlPDF' => $urlPDF);
						sendEmail($dataPedidoCliente,'email_factura_cliente');
						echo 'send';
					}else{
						echo 'noEmail';
					}
				}
				//echo "Cliente: ".$codCliente.' - Factura: '.$noFactura.' - URL: '.$urlPDF;
				exit;
			}

			exit;
		}// End Send factura

		//Agrega al carrito
		if($_POST['action'] == 'addCarrito'){

			$codProducto = intval($_POST['coproducto']);
			$cantidad = intval($_POST['cantidad']);
			$precio = intval($_POST['precio']);
			$queryProducto= mysqli_query($conection,"SELECT precio,impuesto_id FROM producto WHERE codproducto = '$codProducto' ");
			$infoProducto = mysqli_fetch_assoc($queryProducto);
			$arrProducto = array('codproducto' =>$codProducto,'cantidad' => $cantidad,'precio' => $infoProducto['precio'],'impuestoid' => $infoProducto['impuesto_id']);
			$arrProductos = array();

			//Crear variable
			if(isset($_SESSION['arrProductos']))
			{
				$on = true;
				$arrProductos = $_SESSION['arrProductos'];
				for ($pr=0; $pr < count($arrProductos); $pr++) {
					if($arrProductos[$pr]['codproducto'] == $codProducto)
					{
						$arrProductos[$pr]['cantidad'] = $arrProductos[$pr]['cantidad'] + $cantidad;
						$on = false;
					}
				}
				if($on)
				{
					array_push($arrProductos,$arrProducto);
				}
				$_SESSION['arrProductos'] = $arrProductos;
			}else{
				array_push($arrProductos,$arrProducto);
				$_SESSION['arrProductos'] = $arrProductos;
			}
			echo json_encode($_SESSION['arrProductos']);
			exit;
		}// end add carrito

		//Actualizar carrito
		if($_POST['action'] == 'updCantidadCarrito'){

			$codProducto = $_POST['coproducto'];
			$cantidad = $_POST['cantidad'];
			//$precio = $_POST['precio'];
			$arrProductos = array();
			//Crear variable
			if(isset($_SESSION['arrProductos']))
			{
				$on = true;
				$subTotal = 0;
				$total = 0;
				$arrProductos = $_SESSION['arrProductos'];
				for ($pr=0; $pr < count($arrProductos); $pr++) {
					if($arrProductos[$pr]['codproducto'] == $codProducto)
					{
						$arrProductos[$pr]['cantidad'] = $cantidad;
						$subTotal = $arrProductos[$pr]['precio'] * $cantidad;
						$on = false;
					}
				}
				$_SESSION['arrProductos'] = $arrProductos;
				for ($prs=0; $prs < count($arrProductos); $prs++) {
					$total = $total + ($arrProductos[$prs]['precio'] * $arrProductos[$prs]['cantidad']);
				}
				$total = SIMBOLO_MONEDA.'. '.formatCant($total);
				$subTotal = SIMBOLO_MONEDA.'. '.formatCant($subTotal);
				$arrData = array('productos' => $_SESSION['arrProductos'],'subtotal' => $subTotal, 'total' => $total);
				//echo json_encode($_SESSION['arrProductos']);
				echo json_encode($arrData);

			}else{
				echo "errorData";
			}
			exit;
		}// end add carrito

		//Borra del carrito
		if($_POST['action'] == 'delItemCarrito'){

			$codProducto = $_POST['codproducto'];
			$cantProd = 0;
			//Crear variable
			if(isset($_SESSION['arrProductos']))
			{
				$arrProductos = $_SESSION['arrProductos'];
				for ($pr=0; $pr < count($arrProductos); $pr++) {
					if($arrProductos[$pr]['codproducto'] == $codProducto)
					{
						unset($arrProductos[$pr]);
					}
				}
				sort($arrProductos);
				for ($i=0; $i < count($arrProductos); $i++) {
					# code...
					$idProd = $arrProductos[$i]['codproducto'];
					$queryPro = mysqli_query($conection,"SELECT producto,foto,precio FROM producto WHERE codproducto = $idProd ");
					$result_register = mysqli_fetch_assoc($queryPro);
					$arrProductos[$i]['producto'] = $result_register['producto'];
					$arrProductos[$i]['precio'] = $result_register['precio'];
					$arrProductos[$i]['foto'] = $result_register['foto'];
					$cantProd += $arrProductos[$i]['cantidad'];
				}
			}

			$_SESSION['arrProductos'] = $arrProductos;
			$htmlTable = '';
			if(count($arrProductos) > 0)
			{
				$htmlTable = '
							<table class="table">
			                <thead>
			                    <tr>
			                        <th scope="col"></th>
			                        <th scope="col" colspan="2">Producto</th>
			                        <th class="textcenter" scope="col">Precio</th>
			                        <th class="textcenter" scope="col">Cantidad</th>
			                        <th class="textright" scope="col">Total</th>
			                    </tr>
			                </thead>
			                <tbody id="detalleCarrito">';
			                        $montoTotal = 0;
			                        for ($p=0; $p < count($arrProductos); $p++) {
			                        	# code...
			                            $total = $arrProductos[$p]['precio'] * $arrProductos[$p]['cantidad'];
			                            $montoTotal += $total;
			    $htmlTable .= '<tr id="row_'.$arrProductos[$p]['codproducto'].'">
			                        <th scope="row" class="textcenter"><span class="btnDelDetalle" onclick="delProdCarrito('.$arrProductos[$p]['codproducto'].');"  ><i class="far fa-trash-alt" ></i></span></th>
			                        <td><img class="imgDetalleCarrito" src="'.$base_url.'/sistema/img/uploads/'.$arrProductos[$p]['foto'].'" alt="'.$arrProductos[$p]['producto'].'"></td>
			                        <td>'.$arrProductos[$p]['producto'].'</td>
			                        <td class="textcenter">'.SIMBOLO_MONEDA.'. '. $arrProductos[$p]['precio'].'</td>
			                        <td class="textcenter">
										<input type="number" min="1" name="cantProducto" id="prod_'.$arrProductos[$p]['codproducto'].'" class="cantProducto" value="'.$arrProductos[$p]['cantidad'].'" producto_id="'.$arrProductos[$p]['codproducto'].'" onkeypress="return controlTag(event);" required>
			                        </td>
			                        <td class="textright subTotal">'.SIMBOLO_MONEDA.'. '. $total.'</td>
			                    </tr>';
			                    	}
			    $htmlTable .= '<tr>
			                        <td class="textright" colspan="5">Monto Total:</td>
			                        <td id="totalCarrito" class="textright">'.SIMBOLO_MONEDA.'. '. $montoTotal.'</td>
			                    </tr>
			                </tbody>
			            </table>
			            <br>
			            <div class="containerBtnPago textright">
			                <button type="button" id="btnCotizar" class="btn btn-primary" onclick="sendPedido();"><i class="fas fa-box"></i>&nbsp;&nbsp; Realiza pedido &nbsp;&nbsp;</button>
			            </div>';
			}else{
				$htmlTable = '<p>No hay productos en el carrito, <a href="index.php">Ver Poroductos</a></p>';
			}

			$arrData = array('cant' => $cantProd, 'htmlTable' => $htmlTable);

			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			exit;
		}// end add carrito

		//Enviar Pedido
		if($_POST['action'] == 'sendPedido'){

			if(empty($_POST['nombre_cliente']) || empty($_POST['tel_cliente']) || empty($_POST['email_cliente']) || empty($_POST['nit']) || empty($_POST['nombrefiscal']) || empty($_POST['direccion']) || empty($_SESSION['arrProductos']))
			{
				echo "errorData";exit;
			}

			$strNombre = strClean($_POST['nombre_cliente']);
			$intTelefono = strClean($_POST['tel_cliente']);
			$strEmail = strClean($_POST['email_cliente']);
			$strNit = strClean($_POST['nit']);
			$strNombreFiscal = strClean($_POST['nombrefiscal']);
			$strDireccion = strClean($_POST['direccion']);
			$intTipoPago = strClean($_POST['tipopago']);
			$fecha = date('Y-m-d');
			$totalPedido = 0;
			$arrProductos = $_SESSION['arrProductos'];

			$insertContacto = mysqli_query($conection,"INSERT INTO
														contacto_pedido(nombre,
																telefono,
																email,
																nit,
																nombre_fiscal,
																direccion)
												 	VALUES('$strNombre',
												 	 		$intTelefono,
												 	 		'$strEmail',
												 	 		'$strNit',
												 	 		'$strNombreFiscal',
												 	 		'$strDireccion')"
										);
			if($insertContacto){
				$idContacto = mysqli_insert_id($conection);
				$insertPedido = mysqli_query($conection,"INSERT INTO
														pedido(fecha,
																contacto_id,
																tipopago_id)
												 	VALUES('$fecha',
												 	 		$idContacto,
												 	 		$intTipoPago)"
										);
				if($insertPedido)
				{
					$totalPedido = 0;
					$idPedido = mysqli_insert_id($conection);
					//Insertar productos
					$insert = "INSERT INTO detalle_pedido(pedido_id,codproducto,cantidad,precio_venta,impuestoid) VALUES";
					foreach ($arrProductos as $producto) {
						$codProducto = $producto['codproducto'];
						$cantidad = $producto['cantidad'];
						$precio = $producto['precio'];
						$impuestoid = $producto['impuestoid'];
						$subtotal = $cantidad * $precio;
						$totalPedido = $totalPedido + $subtotal;
						$insert .= "({$idPedido},{$codProducto},{$cantidad},{$precio},{$impuestoid}),";
					}
					$insert = substr($insert, 0, -1);
					$insertDetalle = mysqli_query($conection,$insert);
					if($insertDetalle)
					{
						$pedido = "pedido_".$idPedido;
						$key = $idPedido.'_24091989';
						$keyPedido = md5($key);
						$emailDestinoEmpresa = EMAIL_PEDIDOS;
						$emailRemitente = EMAIL_PEDIDOS;
						$urlPedido = $base_url."/sistema/pedidos/pedido.php?p={$idPedido}&c={$keyPedido}";
						$updatePedido = mysqli_query($conection,"UPDATE pedido SET total = $totalPedido WHERE id_pedido = $idPedido");
						//Data email Cliente
						$dataPedidoCliente = array('pedido' => $idPedido,'nombreContacto' => $strNombre = $strNombre,'emailDestino' => $strEmail,'emailRemitente' => $emailRemitente,'asunto' => 'Pedido realizado','urlPedido' => $urlPedido);
						//Data email Empresa
						$dataPedidoEmpresa = array('pedido' => $idPedido,'nombre' => $strNombre = $strNombre,'email' => $strEmail,'telefono' => $intTelefono,'direccion' => $strDireccion,'urlPedido' => $urlPedido, 'asunto' => "Nuevo pedido",'emailDestino' => $emailDestinoEmpresa,'emailRemitente' => $emailRemitente);
						sendEmail($dataPedidoCliente,'email_pedido_cliente');
						sendEmail($dataPedidoEmpresa,'email_pedido_empresa');
						unset($_SESSION['arrProductos']);
						echo $idPedido;
					}else{
						echo "errorDetalle";
					}
				}else{
					echo "errorPedido";
				}

			}else{
				echo "errorContacto";
			}
			mysqli_close($conection);
			exit;
		}// end save pedido

		// Info Pedido
		if($_POST['action'] == 'infoPedido'){
			if(!empty($_POST['idpedido'])){
				$nopedido = $_POST['idpedido'];
				$query = mysqli_query($conection,"SELECT * FROM pedido WHERE id_pedido = '$nopedido' AND estatus != 10");
				mysqli_close($conection);
				$result = mysqli_num_rows($query);

				if($result > 0){

					$data = mysqli_fetch_assoc($query);

					$arrFecha = explode('-',$data['fecha']);
					$fecha = $arrFecha[2].'/'.$arrFecha[1].'/'.$arrFecha[0];
					$data['fecha'] = $fecha;
					$data['total'] = SIMBOLO_MONEDA.'. '.formatCant($data['total']);
					echo json_encode($data,JSON_UNESCAPED_UNICODE);
					exit;
				}
			}
			echo "error";
			exit;
		}
		// Anular Pedido
		if($_POST['action'] == 'updPedido'){
			if(!empty($_POST['noPedido']) || !empty($_POST['estado']))
			{
				$estado = intval($_POST['estado']);
				$nopedido = $_POST['noPedido'];
				$query_anular 	= mysqli_query($conection,"UPDATE pedido SET estatus = $estado WHERE id_pedido = '$nopedido' ");
				if($query_anular){
					echo "ok";
					exit;
				}
			}
			echo "error";
			exit;
		}
	} //End Post
	exit;



 ?>