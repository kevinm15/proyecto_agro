<?php

	include "conexion.php";

	sort($arrCarrito);
	$cantCarrito=0;
	for ($i=0; $i < count($arrCarrito); $i++) {
		# code...
		$idProd = $arrCarrito[$i]['codproducto'];
		$queryPro = mysqli_query($conection,"SELECT producto,foto,precio FROM producto WHERE codproducto = $idProd ");
		$result_register = mysqli_fetch_assoc($queryPro);

		$arrCarrito[$i]['producto'] = $result_register['producto'];
		$arrCarrito[$i]['precio'] = $result_register['precio'];
		$arrCarrito[$i]['foto'] = $result_register['foto'];
		$cantCarrito += $arrCarrito[$i]['cantidad'];
	}

 ?>