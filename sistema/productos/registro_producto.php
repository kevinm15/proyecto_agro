<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Registro Productos</title>
</head>
<body>
<?php
	$fecha 		= date('ymd');
	$hora 		= date('Hms');
	$codebar 	= $fecha.$hora;
	//echo $codebar;
 ?>
<!-- <img src="library/barcode/barcode.php?text=<?= $codebar; ?>&size=50&orientation=horizontal&codetype=Code128&print=true&sizefactor=1"> -->
<?php
	session_start();
	include "../../conexion.php";

	$barcode 	= '';
	$producto   = '';
	$descripcion= '';
	$precio     = '';
	$cantMinima =  1;
	$cantidad   =  0;

	//Extrae Marcas
	$query_marca = mysqli_query($conection,"SELECT * FROM marca WHERE estatus = 1
																ORDER BY marca ASC");
	$result_marca = mysqli_num_rows($query_marca);
	//Extrae Categorías
	$query_cat = mysqli_query($conection,"SELECT * FROM categoria WHERE estatus = 1
																ORDER BY categoria ASC");
	$result_cat = mysqli_num_rows($query_cat);
	//Extrae Presentación
	$query_presentacion = mysqli_query($conection,"SELECT * FROM presentacion_producto WHERE estatus = 1
																ORDER BY presentacion ASC");
	$result_presentacion = mysqli_num_rows($query_presentacion);
	//Extrae Ubicación
	$query_ubicacion = mysqli_query($conection,"SELECT * FROM ubicacion WHERE status = 1
																	ORDER BY ubicacion ASC");
	$result_ubicacion = mysqli_num_rows($query_ubicacion);
	//Extrae Impuesto
	$query_impuesto = mysqli_query($conection,"SELECT * FROM impuesto WHERE status = 1
																	ORDER BY idimpuesto ASC");
	$result_impuesto = mysqli_num_rows($query_impuesto);

	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
	{
		header("location: ../index.php");
	}

	if(!empty($_POST))
	{
		$barcode   	= $_POST['codBarra'];
		$producto   = ucfirst(strClean($_POST['producto']));
		$descripcion= ucfirst(strClean($_POST['descripcion']));
		$marca  	= intval($_POST['marca']);
		$categoria  = intval($_POST['categoria']);
		$presentacion = intval($_POST['presentacion']);
		$cantMinima =  intval($_POST['cantMinima']);
		$cantidad 	=  $_POST['cantidad'];
		$precio 	=  $_POST['precio'];
		$id_ubicacion = intval($_POST['id_ubicacion']);
		$id_impuesto = intval($_POST['id_impuesto']);
		$usuario_id = intval($_SESSION['idUser']);
		$alert='';

		if( empty($_POST['producto'])  || empty($_POST['descripcion']) || empty($_POST['marca']) || empty($_POST['categoria']) || empty($_POST['presentacion']) || empty($_POST['cantMinima']) || empty($_POST['precio']) || empty($_POST['id_ubicacion']) || empty($_POST['id_impuesto']))
		{
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{
			$foto   	 	= $_FILES['foto'];
			$nombre_foto 	= $foto['name'];
			$type 		 	= $foto['type'];
			$url_temp    	= $foto['tmp_name'];
			$fecha 			= date('ymd');
			$hora 			= date('Hms');

			$imgProducto = 'img_producto.png';
			if(empty($_POST['codBarra']))
			{
				$barcode = $fecha.$hora;
			}

			if($nombre_foto != '')
			{
				$img_nombre = 'img_'.md5(date('d-m-Y H:m:s'));
				$imgProducto= $img_nombre.'.jpg';
				$arrType = explode("/", $type);
				$typeImg = $arrType[1];  
			}

			$query = mysqli_query($conection,"SELECT * FROM producto WHERE codebar = '$barcode' ");
			$result = mysqli_num_rows($query);

			if($result > 0){
				$alert='<p class="msg_error">El código de barra ya existe.</p>';
			}else{
				$query_insert = mysqli_query($conection,"INSERT INTO producto(producto,
																				descripcion,
																				categoria,
																				marca_id,
																				presentacion_id,
																				precio,
																				impuesto_id,
																				existencia,
																				existencia_minima,
																				codebar,
																				ubicacion_id,
																				usuario_id,
																				foto)
																		 VALUES('$producto',
																		 		'$descripcion',
																		 		'$categoria',
																		 		'$marca',
																		 		'$presentacion',
																		 		'$precio',
																		 		'$id_impuesto',
																		 		'$cantidad',
																		 		'$cantMinima',
																		 		'$barcode',
																		 		'$id_ubicacion',
																		 		'$usuario_id',
																		 		'$imgProducto')");
				$producto_id = mysqli_insert_id($conection);
				if($query_insert){
					//Valida si foto
					if($nombre_foto != '')
					{
						$max_ancho = 500;
				        $max_alto = 550;
						copyImage($url_temp,$typeImg,$imgProducto,$max_ancho,$max_alto);
						//move_uploaded_file($url_temp, $src);
					}
					$alert='<p class="msg_save">Producto guardado correctamente.</p>';
					$barcode 	= '';
					$producto   = '';
					$precioCompra= '';
					$precio 	= '';
					$cantidad 	= '0';
					$descripcion= '';
					$id_ubicacion= '';
					$cantMinima = 1;
				}else{
					$alert='<p class="msg_error">Error al guardar, producto ya existe.</p>';
				}
			}
		}
	}
 ?>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<div class="form_producto">
			<h1><i class="fas fa-cubes"></i> Registrar Producto</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form class="form" action="" method="post" enctype="multipart/form-data">
				<div class="form_container wd50">

					<div class="wd50">
						<label for="codBarra">Código Barra</label>
						<input type="text" name="codBarra" id="codBarra" placeholder="Código de barra" value="<?= $barcode; ?>">
					</div>
					<div class="wd50">
						<label for="producto">Producto</label>
						<input type="text" name="producto" id="producto" placeholder="Nombre del producto" value="<?= $producto; ?>" required>
					</div>
					<div class="wd100">
						<label for="descripcion">Descripción</label>
						<textarea name="descripcion" id="descripcion" rows="3" placeholder="Descripción de producto" required><?= $descripcion; ?></textarea>
					</div>
					<div class="wd30">
						<label for="marca">Marca</label>
						<select name="marca" id="marca" required>
							<option value="" >Seleccione</option>
							<?php
								if($result_marca > 0)
								{
									while ($marca = mysqli_fetch_assoc($query_marca)) {
							?>
									<option value="<?php echo $marca["idmarca"]; ?>"><?php echo $marca["marca"] ?></option>
							<?php
										# code...
									}
								}
							 ?>
						</select>
					</div>
					<div class="wd30">
						<label for="categoria">Categoría</label>
						<select name="categoria" id="categoria" required>
							<option value="" >Seleccione</option>
							<?php
								if($result_cat > 0)
								{
									while ($categoria = mysqli_fetch_assoc($query_cat)) {
							?>
									<option value="<?php echo $categoria["idcategoria"]; ?>"><?php echo $categoria["categoria"] ?></option>
							<?php
										# code...
									}
								}
							 ?>
						</select>
					</div>
					<div class="wd30">
						<label for="presentacion">Presentación</label>
						<select name="presentacion" id="presentacion" required>
							<option value="" >Seleccione</option>
							<?php
								if($result_presentacion > 0)
								{
									while ($presentacion = mysqli_fetch_assoc($query_presentacion)) {
							?>
									<option value="<?php echo $presentacion["id_presentacion"]; ?>"><?php echo $presentacion["presentacion"] ?></option>
							<?php
										# code...
									}
								}
							 ?>
						</select>
					</div>
					<div class="wd30">
						<label for="cantMinima">Stock mínimo</label>
						<input type="number" name="cantMinima" id="cantMinima" placeholder="Existencia mínima" min="1" value="<?= $cantMinima; ?>" required >
					</div>
					<div class="wd30">
						<label for="cantidad">Cantidad</label>
						<input type="text" name="cantidad" id="cantidad" placeholder="Cantidad" value="<?= $cantidad; ?>" required >
					</div>
					<div class="wd30">
						<label for="precio">Precio</label>
						<input type="text" name="precio" id="precio" value="" placeholder="Precio del producto" value="<?= $precio; ?>" required >
					</div>
					<div class="wd50">
						<label for="id_ubicacion">Ubicación:</label>
						<select name="id_ubicacion" id="id_ubicacion" required>
							<option value="" >Seleccione</option>
							<?php
								if($result_ubicacion > 0)
								{
									while ($ubicacion = mysqli_fetch_assoc($query_ubicacion)) {
							?>
									<option value="<?php echo $ubicacion["id_ubicacion"]; ?>"><?php echo $ubicacion["ubicacion"] ?></option>
							<?php
										# code...
									}
								}
							 ?>
						</select>
					</div>
					<div class="wd50">
						<label for="id_impuesto">Impuesto:</label>
						<select name="id_impuesto" id="id_impuesto" required>
							<option value="" >Seleccione</option>
							<?php
								if($result_impuesto > 0)
								{
									while ($impuesto = mysqli_fetch_assoc($query_impuesto)) {
							?>
									<option value="<?php echo $impuesto["idimpuesto"]; ?>"><?php echo $impuesto["descripcion"] ?></option>
							<?php
										# code...
									}
								}
							 ?>
						</select>
					</div>
					<div class="wd100">
						<button type="submit" class="btn_save"><i class="far fa-save fa-lg"></i> Guardar Producto</button>
					</div>
				</div>
				<div class="wd50">
					<div class="wd100">
						<div class="photo">
							<label for="foto" class="textcenter">Foto (500x550)</label>
		                    <div class="prevPhoto">
		                    	<span class="delPhoto notBlock">X</span>
		                    	<label for="foto"></label>
		                        <img id="img" src="../img/uploads/img_producto.png">
		                    </div>
		                    <div class="upimg">
		                        <input type="file" name="foto" id="foto">
		                    </div>
		                    <div id="form_alert"></div>
						</div>
					</div>
				</div>
				
			</form>
		</div>
	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>