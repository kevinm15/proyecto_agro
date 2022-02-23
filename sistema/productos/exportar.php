<?php
		 session_start();
		if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
		{
			header("location: ../index.php");
		}

        $fecha = date('d-m-Y');
        $filename = 'lista_productos';
        header("Content-Disposition: attachment; filename={$filename}_{$fecha}.xls");
        header("Content-type: application/force-download");
        header("Content-type: application/vdn.ms-excel");
        header("Pragma: public");
        print "\xEF\xBB\xBF"; // UTF-8 BOM

        include "../../conexion.php";

        $dataQuery = array();

        //Extrae la moneda
        $sbMoneda = '';
        $query_moneda = mysqli_query($conection,"SELECT simbolo_moneda FROM configuracion WHERE id = 1");
        $result_moneda = mysqli_num_rows($query_moneda);
        if($result_moneda > 0)
        {
            $moneda = mysqli_fetch_assoc($query_moneda);
            $sbMoneda = $moneda['simbolo_moneda'];
        }

        if(!empty($_POST['exportFilter']))
        {
        	$filtro = $_POST['exportFilter'];
        	$data_query = mysqli_query($conection,$filtro);
        }else{
	        $data_query = mysqli_query($conection,"SELECT
                                                     p.producto,
                                                     c.categoria,
                                                     pr.presentacion,
                                                     p.precio,
                                                     p.existencia,
                                                     p.existencia_minima,
                                                     m.marca,
                                                     p.codebar
                                            FROM producto p
                                            INNER JOIN marca m
                                            ON p.marca_id = m.idmarca
                                            INNER JOIN categoria c
                                            ON p.categoria = c.idcategoria
                                            INNER JOIN presentacion_producto pr
                                            ON p.presentacion_id = pr.id_presentacion
                                            WHERE p.estatus = 1 ORDER BY p.producto ASC ");
        }

		$numRow = mysqli_num_rows($data_query);
		if($numRow > 0){
			while ($info = mysqli_fetch_assoc($data_query)) {
				# code...
				array_push($dataQuery,$info);
			}
		}

        $style_row_head = 'style="border:1px solid #CCC;background-color:#5890cc;color:white;"';
        $style_row_data = 'style="border:1px solid #CCC; color:#555;"';
        $style_center = 'style="border:1px solid #CCC; color:#555; text-align:center;"';

        $dataHtml = '';
?>
        <table>
            <tr>
                <td colspan="9" style="font-size: 25pt; text-align:center;">LISTA DE PRODUCTOS</td>
            </tr>
            <tr>
                <th <?php echo $style_row_head;  ?> >No.</th>
                <th <?php echo $style_row_head;  ?> >Código.</th>
                <th <?php echo $style_row_head;  ?> >Producto</th>
                <th <?php echo $style_row_head;  ?> >Marca</th>
                <th <?php echo $style_row_head;  ?> >Categoría</th>
                <th <?php echo $style_row_head;  ?> >Presentación</th>
                <th <?php echo $style_row_head;  ?> >Existencia mínima</th>
                <th <?php echo $style_row_head;  ?> >Existencia</th>
                <th <?php echo $style_row_head;  ?> >Precio</th>
            </tr>
<?php
        $i=1;
        foreach ($dataQuery as $data)
        {
?>
			<tr>
                <td <?php echo $style_row_data;  ?> > <?php echo $i;  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['codebar'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['producto'];  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $data['marca'];  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $data['categoria'];  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $data['presentacion'];  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $data['existencia_minima'];  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $data['existencia'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['precio'];  ?> </td>
            </tr>
<?php
            $i++;
        }
 ?>
        </table>