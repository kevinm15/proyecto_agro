<?php
		 session_start();
		if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
		{
			header("location: ../index.php");
		}

        $fecha = date('d-m-Y');
        $filename = 'lista_ventas';
        header("Content-Disposition: attachment; filename={$filename}_{$fecha}.xls");
        header("Content-type: application/force-download");
        header("Content-type: application/vdn.ms-excel");
        header("Pragma: public");
        print "\xEF\xBB\xBF"; // UTF-8 BOM

        include "../../conexion.php";
        include "../includes/functions.php";

        $dataQuery = array();
        if(!empty($_POST['exportFilter']))
        {
        	$filtro = $_POST['exportFilter'];
        	$data_query = mysqli_query($conection,$filtro);
        }else{
            $data_query = mysqli_query($conection,"SELECT p.id_pedido,
                                             p.fecha,
                                             p.total,
                                             c.id_contacto,
                                             p.estatus,
                                             c.nombre as contacto,
                                             c.telefono,
                                             tp.tipo_pago
                                        FROM pedido p
                                        INNER JOIN contacto_pedido c
                                        ON p.contacto_id = c.id_contacto
                                        INNER JOIN tipo_pago tp
                                        ON p.tipopago_id = tp.id_tipopago
                                        WHERE p.estatus != 10
                                        ORDER BY p.id_pedido ASC ");
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
                <td colspan="7" style="font-size: 25pt; text-align:center;">REPORTE DE PEDIDOS</td>
            </tr>
            <tr>
                <th <?php echo $style_row_head;  ?> >No.</th>
                <th <?php echo $style_row_head;  ?> >Fecha</th>
                <th <?php echo $style_row_head;  ?> >Contacto</th>
                <th <?php echo $style_row_head;  ?> >Teléfono</th>
                <th <?php echo $style_row_head;  ?> >Tipo pago</th>
                <th <?php echo $style_row_head;  ?> >Estado</th>
                <th <?php echo $style_row_head;  ?> >Total</th>
            </tr>
<?php
        $i=1;
        $total= 0;
        foreach ($dataQuery as $data)
        {
            if($data["estatus"] == 1){
                $estado = '<p style="color:#51b9b9;">Activo</p>';
            }else if($data["estatus"] == 2){
                $estado = '<p style="color:#ffab00;">En proceso</p>';
            }else if($data["estatus"] == 3){
                $estado = '<p style="color:green;">Entregado</p>';
            }else{
                $estado = '<p style="color:red;">Anulado</p>';
            }
?>
			<tr>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['id_pedido'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['fecha'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['contacto'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['telefono'];  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $data['tipo_pago'];  ?> </td>
                <td <?php echo $style_center;  ?> > <?php echo $estado;  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['total'];  ?> </td>
            </tr>
<?php
            $total += $data['total'];
            $i++;
        }
 ?>
            <tr>
                <td colspan="6">Total:</td>
                <td><?= $total; ?></td>
            </tr>
        </table>