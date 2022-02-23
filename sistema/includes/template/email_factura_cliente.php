<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Factura electrónica</title>
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
		    background: #0a4661;
		    text-transform: uppercase;
		}
		.x_title_blue {
		    padding: 08px 0;
		    line-height: 25px;
		    background: #0a4661;
		    text-transform: uppercase;
		}
		.x_title_blue p{
			color: #ffffff;
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
		    background-color: #058167;
		}
		.x_textwhite {
		    background-color: rgb(50, 67, 128);
		    color: #ffffff;
		    padding: 10px;
		    font-size: 15px;
		    line-height: 20px;
		}
	</style>
</head>
<body>
	<table align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="text-align:center;">
		<tbody>
			<tr>
				<td>
					<div class="x_sgwrap x_title_blue">
						<p><?= NOMBRE_EMPESA; ?></p>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="x_sgwrap">
						<p>Hola <?= $data['nombreCliente']; ?></p>
					</div>
					<p>Gracias por tu compra.</p>
					<p>A continuación encontrará el enlace del comprobante de tu compra. </p>
					<a href="<?= $data['urlPDF']; ?>" target="_blank" rel="noopener noreferrer" class="x_button_link x_link_blue" style="color:#FFF;">Ver compra </a>
					<br>
					<p>Si no te funciona el botón puedes copiar y pegar la siguiente dirección en tu navegador.</p>
					<p><strong><?= $data['urlPDF']; ?></strong></p>
					<p class="x_title_gray"><a href="<?= WEB_EMPRESA; ?>" target="_blanck"><?= WEB_EMPRESA; ?></a></p>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>