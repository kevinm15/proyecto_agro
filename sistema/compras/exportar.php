<?php
		 session_start();
		if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
		{
			header("location: ../index.php");
		}

        $fecha = date('d-m-Y');
        $filename = 'lista_compras';
        header("Content-Disposition: attachment; filename={$filename}_{$fecha}.xls");
        header("Content-type: application/force-download");
        header("Content-type: application/vdn.ms-excel");
        header("Pragma: public");
        print "\xEF\xBB\xBF"; // UTF-8 BOM

        include "../../conexion.php";
        include "../includes/functions.php";

        $dataQuery = array();
        //Extrae la moneda
        if(!empty($_POST['exportFilter']))
        {
        	$filtro = $_POST['exportFilter'];
        	$data_query = mysqli_query($conection,$filtro);
        }else{
	        $data_query = mysqli_query($conection,"SELECT c.id_compra,
                                                            DATE_FORMAT(c.fecha_compra, '%d/%m/%Y') as fecha,
                                                            d.documento,
                                                            c.no_documento,
                                                            c.serie,
                                                            c.proveedor_id,
                                                            p.proveedor,
                                                            tp.tipo_pago,
                                                            c.total,
                                                            c.estatus
                                                    FROM compra c
                                                    INNER JOIN tipo_documento d
                                                    ON c.documento_id = d.id_tipodocumento
                                                    INNER JOIN proveedor p
                                                    ON c.proveedor_id = p.codproveedor
                                                    INNER JOIN tipo_pago tp
                                                    ON c.tipopago_id = tp.id_tipopago
                                                    WHERE c.estatus != 10
                                                    ORDER BY c.fecha_compra DESC ");
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
                <td colspan="9" style="font-size: 25pt; text-align:center;">REPORTE DE VENTAS</td>
            </tr>
            <tr>
                <th <?php echo $style_row_head;  ?> >No.</th>
                <th <?php echo $style_row_head;  ?> >Fecha</th>
                <th <?php echo $style_row_head;  ?> >Tipo Documento</th>
                <th <?php echo $style_row_head;  ?> >No. Documento</th>
                <th <?php echo $style_row_head;  ?> >Serie</th>
                <th <?php echo $style_row_head;  ?> >Proveedor</th>
                <th <?php echo $style_row_head;  ?> >Tipo pago</th>
                <th <?php echo $style_row_head;  ?> >Total</th>
                <th <?php echo $style_row_head;  ?> >Estado</th>
            </tr>
<?php
        $i=1;
        foreach ($dataQuery as $data)
        {
            $estatus = ($data['estatus'] == 1 ) ? 'Pagado' : 'Anulado';
?>
			<tr>
                <td <?php echo $style_row_data;  ?> > <?php echo $i;  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['fecha'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['documento'];  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $data['no_documento'];  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $data['serie'];  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $data['proveedor'];  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $data['tipo_pago'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo SIMBOLO_MONEDA.' '.formatCant($data['total']);  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $estatus;  ?> </td>
            </tr>
<?php
            $i++;
        }
 ?>
        </table>