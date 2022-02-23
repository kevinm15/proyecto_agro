<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
		<title>Nuevo Pedido</title>
		<style type="text/css">
			p{
				font-family: arial;
				letter-spacing: 1px;
				color: #7f7f7f;
				font-size: 15px;
			}
			a{
				color: #3b74d7;
				font-family: arial;
				text-decoration: none;
				text-align: center;
				display: block;
				font-size: 18px;
			}
			.x_sgwrap p{
				font-size: 30px;
			    line-height: 32px;
			    color: #244180;
			    font-family: arial;
			    text-align: center;
			}
			.x_title_gray {
			    color: #d7d7d7;
			    padding: 5px 0;
			    font-size: 15px;
			    background: #3064a8;
			    text-transform: uppercase;
			}
			.x_title_blue {
			    padding: 08px 0;
			    line-height: 25px;
			    background: #47b1e1;
			    text-transform: uppercase;
			}
			.x_title_blue p{
				color: #FFF;
				font-size: 20px;
			}
			.x_bluetext {
			    color: #244180 !important;
			}
			.x_title_gray a{
				text-align: center;
				padding: 10px;
				margin: auto;
				color: #FFF;
			}
			.x_button_link {
			    width: 470px;
			    height: 40px;
			    display: block;
			    color: #FFF;
			    margin: 20px auto;
			    line-height: 40px;
			    text-transform: uppercase;
			    font-family: Arial Black,Arial Bold,Gadget,sans-serif;
			}
			.x_link_blue {
			    background-color: #27ca67;
			}
			.x_textwhite {
			    background-color: rgb(50, 67, 128);
			    color: #ffffff;
			    padding: 10px;
			    font-size: 15px;
			    line-height: 20px;
			}
			table tr td{
				border: 1px solid #CCC;
			}
			@media screen and (max-width: 700px) {
				a{
					font-size: 14px;
				}
				.x_sgwrap p{
					font-size: 20px;
				}
				.x_button_link{
					width: 205px;
				}
				table{
					width: 100%!important;
				}
			}
		</style>
	</head>
	<body>
		<div class="x_sgwrap x_title_blue">
			<p><?= NOMBRE_EMPESA; ?></p>
		</div>
		<br>
		<table align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="text-align:center; width: 500px;">
			<tbody>
				<tr>
					<td colspan="2">
						<p>Se ha realizado un nuevo pedido.</p>
						<p>Datos del contacto.</p>
					</td>
				</tr>
				<tr>
					<td style="width: 50%; text-align: right;"><p>No. Pedido:</p></td>
					<td style="width: 50%; text-align: left;"><p>&nbsp;<strong><?= $data['pedido']; ?></strong></p></td>
				</tr>
				<tr>
					<td style="width: 50%; text-align: right;"><p>Nombre:</p></td>
					<td style="width: 50%; text-align: left;"><p>&nbsp;<strong><?= $data['nombre']; ?></strong></p></td>
				</tr>
				<tr>
					<td style="width: 50%; text-align: right;"><p>Teléfono:</p></td>
					<td style="width: 50%; text-align: left;"><p>&nbsp;<strong><?= $data['telefono']; ?></strong></p></td>
				</tr>
				<tr>
					<td style="width: 50%; text-align: right;"><p>Email:</p></td>
					<td style="width: 50%; text-align: left;"><p>&nbsp;<strong><?= $data['email']; ?></strong></p></td>
				</tr>
				<tr>
					<td colspan="2" style="width: 100%; text-align: center;"><p><strong><a href="<?= $data['urlPedido']; ?>" target="_blanck" style="text-align: center;"><?= $data['urlPedido']; ?></a></strong></p></td>
				</tr>
			</tbody>
		</table>
		<br>
		<div>
			<p class="x_title_gray"><a href="<?= WEB_EMPRESA; ?>" target="_blanck" style="text-transform: lowercase;"><?= WEB_EMPRESA; ?></a></p>
		</div>
	</body>
	</html>