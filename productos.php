<?php

	require_once "conexion.php";
/*
	print_r('<pre>');
	print_r($_SERVER);
	print_r('</pre>');
*/
	$pagina= 1;
	$cat = '';
	if(!empty($_REQUEST['pg']))
	{
		$pagina = $_REQUEST['pg'];
	}

	if(!empty($_REQUEST['cat']))
	{
		$cat = 'cat='.$_REQUEST['cat'].'&';
	}
	//echo $pagina;
	$por_pagina = 24;
	$tituloCategoria = "Todos los productos";
	$wereCat = "";
	$wereCatCount = "";

	if(!empty($_REQUEST['cat'])){

		$strCat = $_REQUEST['cat'];
		$arrCat = explode("_", $strCat);
		$tituloCategoria = $arrCat[0];
		$idCategoria = $arrCat[1];
		$wereCatCount = " categoria = ".$idCategoria." AND ";
		$wereCat = " p.categoria = ".$idCategoria." AND ";
		//$idCategoria =
	}
	//Paginador
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM producto WHERE $wereCatCount estatus = 1 ");
	$result_register = mysqli_fetch_assoc($sql_registe);
	$total_registro = $result_register['total_registro'];
	/*if(empty($_GET['pagina']))
	{
		$pagina = 1;
	}else{
		$pagina = $_GET['pagina'];
	}*/
	$desde = ($pagina-1) * $por_pagina;
	$total_paginas = ceil($total_registro / $por_pagina);

	$query = mysqli_query($conection,"SELECT p.codproducto,
											 p.producto,
											 p.descripcion,
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
									WHERE $wereCat p.estatus = 1 ORDER BY p.codproducto DESC LIMIT $desde,$por_pagina
		");

	$result = mysqli_num_rows($query);

	$cantCarrito = 0;
	if(isset($_SESSION['arrProductos'])){
		for ($i=0; $i < count($_SESSION['arrProductos']) ; $i++) {
			# code...
			$cantCarrito += $_SESSION['arrProductos'][$i]['cantidad'];
		}
	}

	//Categoría
	$sql_cat = mysqli_query($conection,"SELECT c.idcategoria, c.categoria, count(p.categoria) AS cantidad FROM producto p INNER JOIN categoria c ON p.categoria = c.idcategoria GROUP BY p.categoria, c.idcategoria");
	$numCat = mysqli_num_rows($sql_cat);
	$ulCat = "";
    if($numCat > 0){
    	$ulCat .= '<ul><li><a href="./">Todos los productos</a></li>';
        while ($categoria = mysqli_fetch_assoc($sql_cat)) {
        	$ulCat .='<li><a href="?cat='.$categoria['categoria'].'_'.$categoria['idcategoria'].'">'.$categoria['categoria'].' </a></li>';
        }
        $ulCat .= '</ul>';
    }
 ?>