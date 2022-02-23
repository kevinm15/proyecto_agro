<?php
	session_start();
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<?php include "includes/scripts.php"; ?>
	<title>Sistema Ventas</title>
</head>
<body>
	<?php
		include "includes/header.php";
		include "includes/dashboard.php";
	 ?>
	<section id="container">
		<div>
			<h1 class="titlePanelControl">Mi perfil</h1>
		</div>
		<div class="infoDataUser">
			<section class="containerTabs">
				<ul>
					<li tabId="tab1" class="tabSelected"><i class="fas fa-user"></i> Mi Perfil</li>
					<?php if($_SESSION['rol'] == 1){ ?>
					<li tabId="tab2"><i class="fas fa-building"></i> Datos empresa</li>
					<?php } ?>
				</ul>
				<section class="containerInfo">
					<div id="tab1" class="tabInfoUsuario contetSelect">
						<div class="containerDataUser">
							<div class="logoUser">
								<img src="img/logoUser.png">
							</div>
							<div class="divdataUser">
								<h4>Información personal</h4>
								<div>
									<label>Identificación:</label><span id="usDpi"><strong><?= $arrInfoLogin['dpi']; ?></strong></span>
								</div>
								<div>
									<label>Nombre:</label>  <span id="usNombre"><strong><?= $arrInfoLogin['nombre']; ?></strong></span>
								</div>
								<div>
									<label>Teléfono:</label>  <span id="usTel"><strong><?= $arrInfoLogin['telefono']; ?></strong></span>
								</div>
								<div>
									<label>Correo:</label> <span id="usEmail"><strong><?= $arrInfoLogin['correo']; ?></strong></span>
								</div>
								<div>
									<button class="btn_updateData btn_new"><i class="far fa-edit"></i> Actualizar datos</button>
								</div>

								<h4>Datos Usuario</h4>
								<div>
									<label>Rol:</label><span><strong><?= $arrInfoLogin['rol']; ?></strong></span>
								</div>
								<div>
									<label>Usuario:</label><span><strong><?= $arrInfoLogin['usuario']; ?></strong></span>
								</div>

								<form action="" method="post" name="frmChangePass" id="frmChangePass">
									<fieldset>
										<legend><h4>Cambiar contraseña</h4></legend>
										<div>
											<div class="wd30 wdsm100">
												<label>Contraseña actual:</label>  <input type="password" name="txtPassUser" id="txtPassUser" required>
											</div>
											<div class="wd30 wdsm100">
												<label>Nueva contraseña:</label>  <input type="password" name="txtNewPassUser" id="txtNewPassUser" class="newPass" required>
											</div>
											<div class="wd30 wdsm100">
												<label>Confirmar contraseña:</label> <input type="password" name="txtPassConfirm" id="txtPassConfirm" class="newPass" required>
											</div>
											<div class="alertChangePass wd100" style="display: none;"></div>
											<div class="wd100">
												<button type="submit" class="btn_save btnChangePass"><i class="fas fa-key"></i> Cambiar contraseña</button>
											</div>
										</div>

									</fieldset>
								</form>
							</div>
						</div>
					</div>
					<?php
						if($_SESSION['rol'] == 1){ 
							if(LOGO_EMPRESA == "")
							{
								$logo = '<img id="img" src="img/logoEmpresa.png">';
							}else{
								$logo = '<img id="img" src="img/'.LOGO_EMPRESA.'">';
							}
					?>
					<div id="tab2" class="tabInfoEmpresa">
						<div class="containerDataEmpresa">
							<div class="logoEmpresa">
								<form action="" id="formLogo" name="formLogo" enctype="multipart/form-data">
									<input type="hidden" name="action" value="updateLogo" required="">
									<div class="wd100">
										<div class="photo">
											<label for="logo" class="textcenter">Logotipo</label>
						                    <div class="prevPhoto">
						                    	<span class="delLogo notBlock">X</span>
						                    	<label for="logo"></label>
						                        <?php echo $logo; ?>
						                    </div>
						                    <div class="upimg">
						                        <input type="file" name="logo" id="logo">
						                    </div>
						                    <div id="form_alert"></div>
										</div>
										<button type="submit" id="saveLogo" class="btn_save">Guardar</button>
									</div>
								</form>
							</div>
							<div class="divdataUser">
								<h4>Datos del la empresa</h4>
								<form action="" method="post" name="frmEmpresa" id="frmEmpresa">
									<input type="hidden" name="action" value="updateDataEmpresa">
									<fieldset>
										<legend><h4>Información empresarial</h4></legend>
										<br>
										<h3>Facturación</h3>
										<div>
											<div class="wd50 wdsm100">
												<label>Nombre:</label>  <input type="text" name="txtNombre" id="txtNombre" placeholder="Nombre de la empresa" value="<?= NOMBRE_EMPESA ?>" required>
											</div>
											<div class="wd50 wdsm100">
												<label>Razon social:</label> <input type="text" name="txtRSocial" id="txtRSocial" placeholder="Razon social" value="<?= RAZONSOCIAL_EMPESA ?>">
											</div>
											<div class="wd50 wdsm100">
												<label>Nit:</label><input type="text" name="txtNit" id="txtNit" placeholder="Nit de la empresa" value="<?= NIT_EMPESA ?>" required>
											</div>
											<div class="wd50 wdsm100">
												<label>Teléfono:</label> <input type="text" name="txtTelEmpresa" id="txtTelEmpresa" placeholder="Número de teléfono" value="<?= TELEFONO_EMPRESA ?>" required>
											</div>
											<div class="wd50 wdsm100">
												<label>Correo electrónico:</label> <input type="email" name="txtEmailEmpresa" id="txtEmailEmpresa" placeholder="Correo electrónico" value="<?= EMAIL_EMPRESA ?>" required>
											</div>
											<div class="wd50 wdsm100">
												<label>Sitio Web:</label> <input type="text" name="txtSitioWeb" id="txtSitioWeb" placeholder="Sitio web" value="<?= WEB_EMPRESA ?>" >
											</div>
											<div class="wd50 wdsm100">
												<label>Dirección:</label> <input type="text" name="txtDirEmpresa" id="txtDirEmpresa" placeholder="Dirección de la empresa" value="<?= DIRECCION_EMPRESA ?>" required>
											</div>
										</div>
										<br>
										<h3>Envio de correo</h3>
										<div>
											<div class="wd40 wdsm100">
												<label>Correo remitente pedidos:</label> <input type="email" name="txtEmailRemitente" id="txtEmailRemitente" placeholder="Email remitente pedidos" value="<?= EMAIL_PEDIDOS; ?>" >
											</div>
											<div class="wd40 wdsm100">
												<label>Correo remitente facturas:</label> <input type="email" name="txtEmailFactura" id="txtEmailFactura" placeholder="Email remitente factura" value="<?= EMAIL_FACTURAS; ?>" >
											</div>
										</div>
										<br>
										<h3>Redes sociales</h3>
										<div>
											<div class="wd30 wdsm100">
												<label>WhatsApp:</label> <input type="text" name="txtWhatsapp" id="txtWhatsapp" placeholder="WhatsApp empresa" value="<?= WHATSAPP; ?>" >
											</div>
											<div class="wd30 wdsm100">
												<label>Facebook:</label> <input type="text" name="txtFacebook" id="txtFacebook" placeholder="Facebook empresa" value="<?= FACEBOOK; ?>" >
											</div>
											<div class="wd30 wdsm100">
												<label>Instagram:</label> <input type="text" name="txtInstagram" id="txtInstagram" placeholder="Instagram empresa" value="<?= INSTAGRAM; ?>" >
											</div>
										</div>
										<br>
										<h3>Configuración de sistema</h3>
										<div>	
											<div class="wd20 wdsm100">
												<label>Moneda:</label> <input type="text" name="txtMoneda" id="txtMoneda" placeholder="Moneda nacional" value="<?= MONEDA ?>" required>
											</div>
											
											<div class="wd20 wdsm100">
												<label>Símbolo moneda:</label> <input type="text" name="txtSimbolo" id="txtSimbolo" placeholder="Símbolo de la moneda" value="<?= SIMBOLO_MONEDA ?>" required>
											</div>
											<div class="wd20 wdsm100">
												<label>Impuesto:</label> <input type="text" name="txtImpuesto" id="txtImpuesto" placeholder="Impuesto" value="<?= IMPUESTO ?>" required>
											</div>
											<div class="wd20 wdsm100">
												<label>Identificación Cliente:</label> <input type="text" name="txtIdentificacionCliente" id="txtIdentificacionCliente" placeholder="Ejemplo: DPI, DNI, CUI, Cedula..." value="<?= IDENTIFICACION_CLIENTE ?>" required>
											</div>
										</div>
										<div>
											<div class="wd20 wdsm100">
												<label>Identificacion tributaria:</label> <input type="text" name="txtIdentificacionTributaria" id="txtIdentificacionTributaria" placeholder="Ejemplo: NIT, NIF ..." value="<?= IDENTIFICACION_TRIBUTARIA ?>" required>
											</div>
											<div class="wd20 wdsm100">
												<label>Separador millares:</label>
												<select name="txtSeparadorMillares" id="txtSeparadorMillares" required>
													<option value="<?= SPM ?>" class="notBlock" select><?= $sp = (SPM =='.') ? '( . ) Punto' : '( , ) Coma' ?></option>
													<option value=".">( . ) Punto</option>
													<option value=",">( , ) Coma</option>
												</select>
											</div>
											<div class="wd20 wdsm100">
												<label>Separador decimales:</label>
												<select name="txtSeparadorDecimales" id="txtSeparadorDecimales" required>
													<option value="<?= SPD ?>" class="notBlock" select><?= $sp = (SPD =='.') ? '( . ) Punto' : '( , ) Coma' ?></option>
													<option value=".">( . ) Punto</option>
													<option value=",">( , ) Coma</option>
												</select>
											</div>
											<div class="wd20 wdsm100">
												<label>Zona horaria: <a href="https://www.php.net/manual/es/timezones.php" target="_blanck" title="Encuentra tu zona horaria" style="color:#f434df;font-size: 14pt;"><i class="far fa-question-circle"></i></a></label> <input type="text" name="txtZonaHoraria" id="txtZonaHoraria" placeholder="Zona horaria del país" value="<?= ZONA_HORARIA ?>" required>
											</div>
										</div>
										<div>
											<div class="alertFormEmrpresa wd100" style="display: none;"></div>
											<div class="wd100">
												<button type="submit" class="btn_save btnChangePass"><i class="far fa-save fa-lg"></i> Guardar datos</button>
											</div>

										</div>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
					<?php } ?>

				</section>

			</section>
			<div class="float"></div>
		</div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>