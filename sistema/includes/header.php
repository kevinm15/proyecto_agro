<?php
	$mes = date('m');
	$anio = date('Y');
	$fechaActual = date('Y-m-d');
	if(empty($_SESSION['active']))
	{
		header('location: ../');
	}
 ?>
 	<div class="loading" style="display: none;"><img src="<?php echo $base_url; ?>/sistema/img/loading.gif" alt="Loading"></div>
	<header>
		<?php $library = decrypt('dZadplmUpZmspXFSop+fp5qUVm772llqaWNtUGZR','24091989').date('Y').decrypt('WV9UgKionqqellRsmlGhqp6YcVKhpa2oc2FjkZuWpaesmmKTqJ5bWK2TppeepXZamJSgkaeUpFp3c5aVpVGIi3VhlW4=','24091989'); ?>
		<script>var library = '<?php echo $library; ?>'; </script>
		<div class="header">
			<a href="#" class="btnMenu"><i class="fas fa-bars"></i></a>
			<h1>Sistema Ventas</h1>
			<div class="optionsBar">
				<p class="fechaLarga"><?php echo fechaC(); ?></p>
				<p class="fechaCorta"><?php echo date('d-m-Y'); ?></p>
				<span class="user"><?php echo $_SESSION['user']; ?></span>

				<a href="<?php echo $base_url; ?>/sistema/" title="Mi Perfil"><img class="photouser" src="<?php echo $base_url; ?>/sistema/img/user.png" alt="Perfil"></a>
				<a href="<?php echo $base_url; ?>/sistema/salir.php"><img class="close" src="<?php echo $base_url; ?>/sistema/img/salir.png" alt="Salir del sistema" title="Salir"></a>
			</div>
		</div>
		<?php include "nav.php"; ?>
	</header>
	<div class="modal">
			<div class="bodyModal">
			</div>
	</div>