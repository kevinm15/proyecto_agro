<?php
	session_start();
	$arrCarrito = array();
	if(isset($_SESSION['arrProductos']))
	{
		$arrCarrito = $_SESSION['arrProductos'];
		//print_r($arrCarrito);
	}

	if(!empty($_REQUEST['art'])){
		$arrData = array();
		$strCat = $_REQUEST['art'];
		$arrCat = explode("_", $strCat);
		$producto = $arrCat[0];
		$idProducto = $arrCat[1];

		require_once "conexion.php";
		$query = mysqli_query($conection,"SELECT p.codproducto,
											 p.producto,
											 p.descripcion,
											 p.categoria AS id_categoria,
											 c.categoria,
											 pr.presentacion,
											 p.precio,
											 p.existencia,
											 p.existencia_minima,
											 m.marca,
											 p.codebar,
											 p.foto
									FROM producto p
									INNER JOIN marca m
									ON p.marca_id = m.idmarca
									INNER JOIN categoria c
									ON p.categoria = c.idcategoria
									INNER JOIN presentacion_producto pr
									ON p.presentacion_id = pr.id_presentacion
									WHERE p.codproducto = $idProducto and  p.estatus = 1
		");

		$numRow = mysqli_num_rows($query);
		if($numRow == 0)
		{
			header("Location: index.php");
		}else{
			$arrData = mysqli_fetch_assoc($query);
		}

		$query_ref = mysqli_query($conection,"SELECT codproducto,
											 producto,
											 categoria,
											 precio,
											 foto
									FROM producto
									WHERE categoria =  ".$arrData['id_categoria']." AND estatus = 1 ORDER BY RAND() LIMIT 4
		");

		$result_ref = mysqli_num_rows($query_ref);

	}
 ?>
<?php require_once ("header.php"); ?>
	<section class="containerPage">
        	<section id="info_productos">
				<h3>INFORMACIÓN DEL PRODUCTO</h3>
				<section id="detalles_producto">
					<div class="foto_rincipal">
						<img src="<?php echo $base_url.'/sistema/img/uploads/'.$arrData['foto'];?>" alt="Producto">
					</div>
					<div class="descripcion">
						<p><?php echo strtoupper($arrData['producto']); ?></p>
						<br>
						<span>MARCA: <?php echo $arrData['marca']; ?></span><br>
						<span>PRESENTACIÓN: <?php echo $arrData['presentacion']; ?></span><br>
						<span>CATEGORÍA: <?php echo $arrData['categoria']; ?></span>
						<br><br><br>
						<span>Acerca del producto</span>
						<br><br>
						<p><?php echo $arrData['descripcion']; ?></p>
					</div>
					<div class="prod_car">
						<div class="compra_pro">
							<p>Precio</p>
							<p><span class="precio"><?php echo SIMBOLO_MONEDA. formatCant($arrData['precio']); ?></span></p>
							<br>
							<p>CANT.</p>
							<div class="divCant">
								<span class="mas" onclick="restaCant();">◄</span><input type="text" value="1" id="txtCantidad" name="txtCantidad" onkeypress="return controlTag(event);"><span class="menos" onclick="sumaCant();" >►</span>
							</div>
							<div class="btn_action_car">
								<button id="btnCotizar" type="button" onclick="fntAddCarProd('<?php echo $arrData['codproducto']; ?>','<?php echo $arrData['precio']; ?>',1)"><i class="fas fa-shopping-cart"></i> Agregar</button>
							</div>
						</div>
					</div>
				</section>
			</section>
	</section>


	<section class="group_p">
        <h3>PRODUCTOS RELACIONADOS</h3>
        <div>
			<?php
	            if($result_ref > 0){
	                while ($producto = mysqli_fetch_array($query_ref)) {
	        ?>
	            <article class="item_prod">
	                <a href="producto.php?art=<?php echo $producto['producto'].'_'.$producto['codproducto']; ?>">
	                    <div class="img_prod">
	                        <img src="<?php echo $base_url.'/sistema/img/uploads/'.$producto['foto'];?>" alt="Producto">
	                    </div>
	                </a>
	                <div class="info_pro">
	                    <p><?php echo $producto['producto']; ?></p>
	                    <label for="" class="precio"><?php echo SIMBOLO_MONEDA. formatCant($producto['precio']); ?></label>
	                </div>
	                <div class="add_car">
	                    <span type="button" class="btnAddCarrito" onclick="fntAddCarProd(<?= $producto['codproducto'].','.$producto['precio'].',0'; ?>)">Agregar <i class="fas fa-cart-plus"></i></span>
	                </div>
	            </article>
	        <?php
	                }//end While
	            }//end if
	        ?>
		</div>
    </section>
<?php require_once ("footer.php"); ?>