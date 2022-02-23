<?php
		 session_start();
		if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
        {
			header("location: ../index.php");
		}

		$fecha = date('d-m-Y');
        $filename = 'lista_proveedores';
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
	        $data_query = mysqli_query($conection,"SELECT 
                                                        nit,
                                                        proveedor,
	        											contacto,
	        											telefono,
	        											correo,
                                                        direccion,
	        											DATE_FORMAT(date_add, '%d/%m/%Y') as fecha_registro,
	        											estatus
		        									FROM proveedor
		        									WHERE estatus = 1
		        									ORDER BY codproveedor ASC");
        }

		$numRow = mysqli_num_rows($data_query);
		if($numRow > 0){
			while ($info = mysqli_fetch_assoc($data_query)) {
				# code...
				array_push($dataQuery,$info);
			}
		}

        $style_row_head = 'style="border: 1px solid #CCC;background-color:#5890cc;color:white;"';
        $style_row_data = 'style="border: 1px solid #CCC; color:#555;"';

        $dataHtml = '';
?>
        <table>
            <tr>
                <td colspan="9" style="font-size: 25pt; text-align:center;">LISTA DE PROVEEDORES</td>
            </tr>
            <tr>
                <th <?php echo $style_row_head;  ?> >No.</th>
                <th <?php echo $style_row_head;  ?> ><?= strtoupper(IDENTIFICACION_TRIBUTARIA); ?></th>
                <th <?php echo $style_row_head;  ?> >Nombre</th>
                <th <?php echo $style_row_head;  ?> >Contacto</th>
                <th <?php echo $style_row_head;  ?> >Teléfono</th>
                <th <?php echo $style_row_head;  ?> >Correo</th>
                <th <?php echo $style_row_head;  ?> >Dirección</th>
                <th <?php echo $style_row_head;  ?> >Fecha registro</th>
                <th <?php echo $style_row_head;  ?> >Estatus</th>
            </tr>
<?php
        $i=1;
        foreach ($dataQuery as $data)
        {
            $estatus = ($data['estatus'] == "1" ) ? "Activo" : "Inactivo" ;
?>
				<tr>
                    <td <?php echo $style_row_data;  ?> > <?php echo $i;  ?> </td>
                    <td <?php echo $style_row_data;  ?> > <?php echo $data['nit'];  ?> </td>
                    <td <?php echo $style_row_data;  ?> > <?php echo $data['proveedor'];  ?> </td>
                    <td <?php echo $style_row_data;  ?> > <?php echo $data['contacto'];  ?> </td>
                    <td <?php echo $style_row_data;  ?> > <?php echo $data['telefono'];  ?> </td>
                    <td <?php echo $style_row_data;  ?> > <?php echo $data['correo'];  ?> </td>
                    <td <?php echo $style_row_data;  ?> > <?php echo $data['direccion'];  ?> </td>
                    <td <?php echo $style_row_data;  ?> > <?php echo $data['fecha_registro'];  ?> </td>
                    <td <?php echo $style_row_data;  ?> > <?php echo $estatus;  ?> </td>
                </tr>
<?php
            $i++;
        }
 ?>
        </table>