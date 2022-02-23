<?php
	session_start();
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
	{
		header("location: ./");
	}
	include "../../conexion.php";

	$busqueda 	= '';
	$fecha_de 	= '';
	$fecha_a 	= '';
	$where 		= '';
	$whereDoc 	= '';
	$whereProv 	= '';
	$wherePago 	= '';

	if(empty($_REQUEST['busqueda']) && empty($_REQUEST['fecha_de']) && empty($_REQUEST['fecha_a']))
	{
		header("location: index.php");
	}

	//Si se busca por input
	if(!empty($_REQUEST['busqueda'])){

		$busqueda = strtolower($_REQUEST['busqueda']);
		$where ="(c.id_compra LIKE '%$busqueda%' OR c.no_documento LIKE '%$busqueda%') ";
		$buscar = 'busqueda='.$busqueda;

		//Buscar Por tipo documento
		$querySerchDoc = mysqli_query($conection,"SELECT id_tipodocumento
												FROM tipo_documento
												WHERE documento LIKE '%$busqueda%' AND estatus != 10
												ORDER BY documento DESC ");
		$resultSearchDoc = mysqli_num_rows($querySerchDoc);
		if($resultSearchDoc > 0){
			while ($arrSerarchDoc = mysqli_fetch_assoc($querySerchDoc)){
				$idDocSearch = $arrSerarchDoc['id_tipodocumento'];
				$whereDoc .= ' OR c.documento_id LIKE '.$idDocSearch. ' ';
			}
		}
		//Buscar Por Proveedor
		$queryProv = mysqli_query($conection,"SELECT codproveedor
											FROM proveedor
											WHERE proveedor LIKE '%$busqueda%' AND estatus != 10
											ORDER BY proveedor DESC ");
		$resultProv = mysqli_num_rows($queryProv);
		if($resultProv > 0){
			while ($arrSerarchPro = mysqli_fetch_assoc($queryProv)){
				$idProv = $arrSerarchPro['codproveedor'];
				$whereProv .= ' OR c.proveedor_id LIKE '.$idProv. ' ';
			}
		}
		//Buscar Por Tipo de pago
		$queryPago = mysqli_query($conection,"SELECT id_tipopago
											FROM tipo_pago
											WHERE tipo_pago LIKE '%$busqueda%' AND estatus != 10
											ORDER BY tipo_pago DESC ");
		$resultPago = mysqli_num_rows($queryPago);
		if($resultPago > 0){
			while ($arrSerarchPago = mysqli_fetch_assoc($queryPago)){
				$idPago = $arrSerarchPago['id_tipopago'];
				$wherePago .= ' OR c.tipopago_id LIKE '.$idPago. ' ';
			}
		}
	}

	if(!empty($_REQUEST['fecha_de']) && !empty($_REQUEST['fecha_a'])){
		$fecha_de = $_REQUEST['fecha_de'];
		$fecha_a = $_REQUEST['fecha_a'];

		$buscar = '';
		//$search_proveedor = $_REQUEST['proveedor'];
		if($fecha_de > $fecha_a){
			header("location: index.php");
		}else if($fecha_de == $fecha_a){

			$where = "fecha_compra LIKE '$fecha_de%'";
			$buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
		}else{
			$f_de = $fecha_de.' 00:00:00';
			$f_a = $fecha_a.' 23:59:59';
			$where = "fecha_compra BETWEEN '$f_de' AND '$f_a'";
			$buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
		}
	}

	//Paginador
	$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM compra as c WHERE $where $whereDoc $whereProv $wherePago AND c.estatus != 10 ");
	$result_register = mysqli_fetch_assoc($sql_registe);
	$total_registro = $result_register['total_registro'];

	$por_pagina = 200;

	if(empty($_GET['pagina']))
	{
		$pagina = 1;
	}else{
		$pagina = $_GET['pagina'];
	}

	$desde = ($pagina-1) * $por_pagina;
	$total_paginas = ceil($total_registro / $por_pagina);

	$queryExport = "SELECT c.id_compra,DATE_FORMAT(c.fecha_compra, '%d/%m/%Y') as fecha,d.documento,c.serie,c.no_documento,c.proveedor_id,p.proveedor,tp.tipo_pago,c.total,c.estatus
										FROM compra c
										INNER JOIN tipo_documento d
										ON c.documento_id = d.id_tipodocumento
										INNER JOIN proveedor p
										ON c.proveedor_id = p.codproveedor
										INNER JOIN tipo_pago tp
										ON c.tipopago_id = tp.id_tipopago
										WHERE $where $whereDoc $whereProv $wherePago and c.estatus != 10
									  	ORDER BY c.id_compra DESC ";

	$query_compra = "SELECT c.id_compra,DATE_FORMAT(c.fecha_compra, '%d/%m/%Y') as fecha,d.documento,c.no_documento,c.proveedor_id,p.proveedor,tp.tipo_pago,c.total,c.estatus
										FROM compra c
										INNER JOIN tipo_documento d
										ON c.documento_id = d.id_tipodocumento
										INNER JOIN proveedor p
										ON c.proveedor_id = p.codproveedor
										INNER JOIN tipo_pago tp
										ON c.tipopago_id = tp.id_tipopago
										WHERE $where $whereDoc $whereProv $wherePago and c.estatus != 10
									  	ORDER BY c.id_compra DESC LIMIT $desde,$por_pagina ";

	$query = mysqli_query($conection,$query_compra);
	$result = mysqli_num_rows($query);
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "../includes/scripts.php"; ?>
	<title>Lista de compras</title>
</head>
<body>
	<?php include "../includes/header.php"; ?>
	<section id="container">

		<h1><i class="far fa-file-alt"></i> Lista de compras</h1>
		<a href="nueva_compra.php" class="btn_new"><i class="fas fa-plus"></i></i> Nueva compra</a>
		<?php
			if($result > 0){
		 ?>
		<form action="exportar.php" method="post" class="formExport" >
			<input type="hidden" name="exportFilter" id="exportFilter" value="<?php echo $queryExport; ?>">
			<button type="submit" class="bntExport">  <i class="fas fa-file-excel"></i> Exportar Filtro</button>
		</form>
		<?php } ?>
		<a href="index.php" class="btnRefresh"><i class="fas fa-sync-alt" title="Actualizar"></i></a>
		<form action="buscar_compra.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" value="<?php echo $busqueda; ?>">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
		<div>
			<h5>Buscar por Fecha</h5>
			<form action="buscar_compra.php" method="get" class="form_search_date">
				<label>De: </label>
				<input type="date" name="fecha_de" id="fecha_de" value="<?php echo $fecha_de; ?>" required>
				<label> A </label>
				<input type="date" name="fecha_a" id="fecha_a" value="<?php echo $fecha_a; ?>" required>
				<button type="submit" class="btn_view"><i class="fas fa-search"></i></button>
			</form>
		</div>
	<div class="containerTable">
		<table>
			<tr>
				<th>No.</th>
				<th>Fecha</th>
				<th>Tipo Doc.</th>
				<th>No. Documento</th>
				<th>Proveedor</th>
				<th class="textcenter">Tipo de pago</th>
				<th class="textright">Total</th>
				<th class="textcenter">Estado</th>
				<th class="textcenter">Acciones</th>
			</tr>
		<?php
			if($result > 0){
				while ($data = mysqli_fetch_array($query)) {
					if($data["estatus"] == 1){
						$estado = '<span class="pagada">Pagado</span>';
					}else{
						$estado = '<span class="anulada">Anulado</span>';
					}
			?>
				<tr id="row_<?php echo $data["id_compra"]; ?>">
					<td><?php echo $data["id_compra"]; ?></td>
					<td><?php echo $data["fecha"]; ?></td>
					<td><?php echo $data["documento"]; ?></td>
					<td><?php echo $data["no_documento"]; ?></td>
					<td><?php echo $data["proveedor"]; ?></td>
					<td class="textcenter"><?php echo $data['tipo_pago']; ?></td>
					<td class="textright totalfactura"><span><?= SIMBOLO_MONEDA ?>.</span><?php echo formatCant($data["total"]); ?></td>
					<td class="estado textcenter"><?php echo $estado; ?></td>
					<td class="textcenter">
						<button title="Ver compra" class="btn_view view_compra" type="button" onClick="viewCompra(<?php echo $data["id_compra"];?>)" ><i class="fas fa-eye"></i></button>
					</td>
				</tr>
		<?php
				}
			}else{
				echo '<tr><td colspan="9" align="center"><p><strong>No hay datos para mostrar</strong></p></td></tr>';
			}
		 ?>
		</table>
	</div>
<?php
	if($total_registro != 0)
	{
 ?>
		<div class="paginador">
			<ul>
			<?php
				if($pagina != 1)
				{
			 ?>
				<li><a href="?pagina=<?php echo 1; ?>&<?php echo $buscar; ?>"><i class="fas fa-step-backward"></i></a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>&<?php echo $buscar; ?>"><i class="fas fa-backward"></i></a></li>
			<?php
				}
				for ($i=1; $i <= $total_paginas; $i++) {
					# code...
					if($i == $pagina)
					{
						echo '<li class="pageSelected">'.$i.'</li>';
					}else{
						echo '<li><a href="?pagina='.$i.'&'.$buscar.'">'.$i.'</a></li>';
					}
				}

				if($pagina != $total_paginas)
				{
			 ?>
				<li><a href="?pagina=<?php echo $pagina + 1; ?>&<?php echo $buscar; ?>"><i class="fas fa-forward"></i></a></li>
				<li><a href="?pagina=<?php echo $total_paginas; ?>&<?php echo $buscar; ?>"><i class="fas fa-step-forward"></i></a></li>
			<?php } ?>
			</ul>
		</div>
<?php } ?>

	</section>
	<?php include "../includes/footer.php"; ?>
</body>
</html>