<?php
		 session_start();
		if($_SESSION['rol'] != 1)
		{
			header("location: ../index.php");
		}

		$fecha = date('d-m-Y');
        $filename = 'lista_usuarios';
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
        	$dataUsers = mysqli_query($conection,$filtro);
        }else{
            $whereExport = '';
            if($_SESSION['user'] != 'admin')
            {
                $whereExport = " AND u.rol != 1 ";
            }

	        $dataUsers = mysqli_query($conection,"SELECT u.idusuario,
	        											u.dpi,
	        											u.nombre,
	        											u.telefono,
	        											u.correo,
	        											u.usuario,
	        											r.rol,
	        											DATE_FORMAT(u.dateadd, '%d/%m/%Y') as fecha_registro,
	        											u.estatus
		        									FROM usuario u
		        									INNER JOIN rol r
		        									ON u.rol = r.idrol
		        									WHERE estatus != 10 {$whereExport}
		        									ORDER BY u.idusuario ASC");
        }

		$numRow = mysqli_num_rows($dataUsers);
		if($numRow > 0){
			while ($info = mysqli_fetch_assoc($dataUsers)) {
				# code...
				array_push($dataQuery,$info);
			}
		}

        $style_row_head = 'style="border:1px solid #CCC;background-color:#5890cc;color:white;"';
        $style_row_data = 'style="border:1px solid #CCC; color:#555;"';

        $dataHtml = '';
?>

        <table>
            <tr>
                <td colspan="8" style="font-size: 25pt; text-align:center;">LISTA DE USUARIOS</td>
            </tr>
            <tr>
                <th <?php echo $style_row_head;  ?> >ID</th>
                <th <?php echo $style_row_head;  ?> ><?= strtoupper(IDENTIFICACION_CLIENTE); ?></th>
                <th <?php echo $style_row_head;  ?> >Nombre</th>
                <th <?php echo $style_row_head;  ?> >Teléfono</th>
                <th <?php echo $style_row_head;  ?> >Correo</th>
                <th <?php echo $style_row_head;  ?> >Usuario</th>
                <th <?php echo $style_row_head;  ?> >Rol</th>
                <th <?php echo $style_row_head;  ?> >Fecha registro</th>
                <th <?php echo $style_row_head;  ?> >Estatus</th>
            </tr>
<?php
        foreach ($dataQuery as $data)
        {

            $estatus = ($data['estatus'] == "1" ? "Activo" : ($data['estatus'] == "0" ? "Inactivo" : "Eliminado"));
?>
			<tr>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['idusuario'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['dpi'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['nombre'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['telefono'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['correo'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['usuario'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['rol'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $data['fecha_registro'];  ?> </td>
                <td <?php echo $style_row_data;  ?> > <?php echo $estatus;  ?> </td>
            </tr>
<?php
        }
 ?>
        </table>