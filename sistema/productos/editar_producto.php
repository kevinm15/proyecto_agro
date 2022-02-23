<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Editar Productos</title>
</head>
<body>
<?php
	session_start();
	include "../../conexion.php";
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
	{
		header("location: ../index.php");
	}

	if(!empty($_POST))
	{
		$alert='';
		if( empty($_POST['codBarra']) || empty($_POST['producto'])  || empty($_POST['descripcion']) || empty($_POST['marca']) || empty($_POST['categoria']) || empty($_POST['presentacion']) || empty($_POST['cantMinima']) || empty($_POST['precio']) || empty($_POST['id_impuesto']))
		{
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{
			$codproducto = ucfirst($_POST['id']);
			$barcode   	 = $_POST['codBarra'];
			$marca   	 = intval($_POST['marca']);
			$categoria   = intval($_POST['categoria']);
			$presentacion_id = intval($_POST['presentacion']);
			$producto    = ucfirst(strClean($_POST['producto']));
			$descripcion = ucfirst(strClean($_POST['descripcion']));
			$precio      = $_POST['precio'];
			$exist      = $_POST['existencia'];
			$cantMinima  = $_POST['cantMinima'];
			$imgProducto = $_POST['foto_actual'];
			$imgRemove   = $_POST['foto_remove'];
			$id_ubicacion =  intval($_POST['id_ubicacion']);
			$id_impuesto = intval($_POST['id_impuesto']);
			$cantCategory 	= 0;
			$foto   	 = $_FILES['foto'];
			$nombre_foto = $foto['name'];
			$type 		 = $foto['type'];
			$url_temp    = $foto['tmp_name'];

			if(empty($_POST['codBarra']))
			{
				$fecha 			= date('ymd');
				$hora 			= date('Hms');
				$barcode = $fecha.$hora;
			}

			if($nombre_foto == "")
			{
				if($_POST['foto_actual'] === $_POST['foto_remove']){
					$img_nombre = $_POST['foto_actual'];
				}else{
					$img_nombre	 = 'img_producto.png';
				}
			}else{
				$img_nombre = 'img_'.md5(date('d-m-Y H:m:s'));
				$img_nombre= $img_nombre.'.jpg';
				$arrType = explode("/", $type);
				$typeImg = $arrType[1]; 
			}
			$query = mysqli_query($conection,"SELECT * FROM producto WHERE codebar = '$barcode' AND codproducto != $codproducto");
			$result = mysqli_num_rows($query);

			if($result > 0){
				$alert='<p class="msg_error">El código de barra ya existe.</p>';
			}else{
				$query_update = mysqli_query($conection,"UPDATE producto
																SET producto = '$producto',
																	descripcion = '$descripcion',
																	categoria 	= $categoria,
																	marca_id 	=  $marca,
																	presentacion_id = $presentacion_id,
																	precio 		=  $precio,
																	impuesto_id = $id_impuesto,
																	existencia_minima = $cantMinima,
																	codebar 	= $barcode,
																	existencia = $exist,
																	ubicacion_id = $id_ubicacion,
																	foto 		=  '$img_nombre'
														WHERE codproducto = $codproducto");
				if($query_update){
					//Valida Foto
					if(($nombre_foto != '' && ($_POST['foto_actual'] != 'img_producto.png')) || ($_POST['foto_actual'] != $_POST['foto_remove']))
					{
						//Definir tamaño máximo y mínimo
						unlink('../img/uploads/'.$_POST['foto_actual']);
					}
					if($nombre_foto != '')
					{
				        $max_ancho = 500;
				        $max_alto = 550;
						copyImage($url_temp,$typeImg,$img_nombre,$max_ancho,$max_alto);
						//move_uploaded_file($url_temp, $src);
					}
					$alert='<p class="msg_save">Producto actualizado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al actualizar el producto.</p>';
				}
			}
		}
	}

	//Consultar producto
	if(empty($_REQUEST['id']) )
	{
		header("location: index.php");
	}else{
		//Extrae categorías
		$query_cat = mysqli_query($conection,"SELECT * FROM categoria WHERE estatus = 1
																	ORDER BY categoria ASC");
		$result_cat = mysqli_num_rows($query_cat);
		//Extrae marca
		$query_marcas = mysqli_query($conection,"SELECT * FROM marca WHERE estatus = 1
																ORDER BY marca ASC");
		$result_marcas = mysqli_num_rows($query_marcas);
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
		//Extrae infor del producto
		$idproducto = intval($_REQUEST['id']);
		$query = mysqli_query($conection,"SELECT p.codproducto,p.producto,p.descripcion,p.precio,p.existencia,p.existencia_minima,p.codebar,p.ubicacion_id,p.presentacion_id,p.foto,p.impuesto_id,i.descripcion as desc_impuesto,mr.idmarca,c.idcategoria,c.categoria, mr.marca,pr.presentacion,u.ubicacion
											FROM producto p
											INNER JOIN marca mr
											ON p.marca_id = mr.idmarca
											INNER JOIN categoria c
											ON p.categoria = c.idcategoria
											INNER JOIN presentacion_producto pr
											ON p.presentacion_id = pr.id_presentacion
											INNER JOIN ubicacion u
											ON p.ubicacion_id = u.id_ubicacion
											INNER JOIN impuesto i
											ON p.impuesto_id = i.idimpuesto
											WHERE p.codproducto = $idproducto AND p.estatus = 1");
		$result = mysqli_num_rows($query);

		$foto 		 = '<img id="img" src="'.$base_url.'/sistema/img/uploads/img_producto.png">';
		$classRemove = 'notBlock';
		if($result > 0){
			$data = mysqli_fetch_assoc($query);
		}else{
			header("location: index.php");
		}
	}
 ?>
	<?php include "../includes/header.php"; ?>
	<section id="container">
		<div class="form_producto">
			<h1><i class="fas fa-cubes"></i> Editar Producto</h1>
			<a href="index.php" class="linkViewList" ><i class="far fa-list-alt"></i> Ver lista</a>	
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
			<form class="form" action="" method="post" enctype="multipart/form-data">
				<input type="hidden" name="id" value="<?php echo $idproducto; ?>">
				<input type="hidden" id="foto_actual" name="foto_actual" value="<?php echo $data['foto']; ?>">
				<input type="hidden" id="foto_remove" name="foto_remove" value="<?php echo $data['foto']; ?>">
				<div class="form_container wd50">
					
					<div class="wd50">
						<label for="codBarra">Código Barra</label>
						<input type="text" name="codBarra" id="codBarra" placeholder="Código de barra" value="<?= $data['codebar']; ?>">
					</div>
					<div class="wd50">
						<label for="producto">Producto</label>
						<input type="text" name="producto" id="producto" value="<?php echo $data['producto']; ?>" placeholder="Nombre del producto">
					</div>
					<div class="wd100">
						<label for="descripcion">Descripción</label>
						<textarea name="descripcion" id="descripcion" rows="3" placeholder="Descripción de producto" required><?php echo $data['descripcion']; ?></textarea>
					</div>
					<div class="wd30">
						<label for="marca">Marca</label>
						<select name="marca" id="marca" class="notItemOne">
							<option value="<?php  echo $data["idmarca"]; ?>" select><?php  echo $data["marca"]; ?></option>
							<?php
								//Valida y Crear imagen prducto
								if($data['foto'] !='img_producto.png')
								{
									$classRemove = '';
									$foto 		 = '<img id="img" src="'.$base_url.'/sistema/img/uploads/'.$data['foto'].'">';
								}
								if($result_marcas > 0)
								{
									while ($marca = mysqli_fetch_array($query_marcas)) {
							?>
									<option value="<?php echo $marca["idmarca"]; ?>"><?php echo $marca["marca"] ?></option>
							<?php
									}
								}
							 ?>
						</select>
					</div>
					<div class="wd30">
						<label for="categoria">Categoría</label>
						<select name="categoria" id="categoria" class="notItemOne">
							<option value="<?php  echo $data["idcategoria"]; ?>" select><?php  echo $data["categoria"]; ?></option>
							<?php
								if($result_cat > 0)
								{
									while ($categoria = mysqli_fetch_array($query_cat)) {
							?>
									<option value="<?php echo $categoria["idcategoria"]; ?>"><?php echo $categoria["categoria"] ?></option>
							<?php
									}
								}
							 ?>
						</select>
					</div>
					<div class="wd30">
						<label for="presentacion">Presentación</label>
						<select name="presentacion" id="presentacion" class="notItemOne" required>
							<option value="<?php echo $data["presentacion_id"]; ?>" select><?php echo $data["presentacion"] ?></option>
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
						<label for="cantidad">Stock mínimo</label>
						<input type="number" name="cantMinima" id="cantMinima" placeholder="Cantidad mínima en existencia" min="1" value="<?= $data['existencia_minima']; ?>">
					</div>
					<div class="wd30">
						<label for="precio">Precio actual</label>
						<input type="text" name="precio" id="precio" value="<?php echo $data['precio']; ?>" placeholder="Precio del producto">
					</div>
					<div class="wd30">
						<label for="cantidad">Existencia </label>
						<input type="text" name="existencia" id="existencia" value="<?php echo $data['existencia']; ?>" placeholder="Existencia Actual">
					</div>
					<!--<div class="wd30">
						<label for="cantidad">Existencia: <strong><br><?= $data['existencia']; ?></strong></label>
					</div> -->
					<div class="wd50">
						<label for="id_ubicacion">Ubicación:</label>
						<select name="id_ubicacion" id="id_ubicacion" class="notItemOne" required>
							<option value="<?php echo $data["ubicacion_id"]; ?>" select><?php echo $data["ubicacion"] ?></option>
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
						<select name="id_impuesto" id="id_impuesto" class="notItemOne" required>
							<option value="<?php echo $data["impuesto_id"]; ?>" select><?php echo $data["desc_impuesto"] ?></option>
							<?php
								if($result_ubicacion > 0)
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
						<button type="submit" class="btn_save"><i class="far fa-save fa-lg"></i> Actualizar Producto</button>
					</div>
				</div>
				<div class="wd50">
					<div class="photo">
						<label for="foto" class="textcenter">Foto (500x550)</label>
	                    <div class="prevPhoto">
	                    	<span class="delPhoto <?php echo $classRemove; ?> ">X</span>
	                    	<label for="foto"></label>
	                    	<?php echo $foto; ?>
	                    </div>
	                    <div class="upimg">
	                        <input type="file" name="foto" id="foto">
	                    </div>
	                    <div id="form_alert"></div>
					</div>
				</div>
			</form>
		</div>
	</section>
	<?php include "../includes/footer.php"; ?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('.labelListCategorias').click(function(event) {
				$('.divListCategorias').toggle('fast');
			});

		});
	</script>
</body>
</html>