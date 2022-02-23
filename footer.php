	<footer>
		<div class="fcontain">
			<div class="direccion">
				<?= $logoEmpresa ?><br>
				<span><?= DIRECCION_EMPRESA; ?></span>
			</div>
			<div class="footerMenu">
				<span class="titleMenuFooter">MENÚ</span>
				<ul>
					<li><a href="<?= $base_url; ?>">Inicio</a></li>
					<li><a href="<?= $base_url; ?>">Productos</a></li>
					<li><a href="<?= $base_url; ?>/carrito">Carrito</a></li>
					<!-- <li><a href="<?= $base_url; ?>/contacto">Contacto</a></li> -->
				</ul>
			</div>
			<div class="redes">
				<span>CONTACTO</span><br>
				<a href="mailto: <?= EMAIL_EMPRESA;  ?>"><?= EMAIL_EMPRESA; ?></a>
				<ul>
					<li><a href="<?= FACEBOOK; ?>" target="_blank"><i class="fab fa-facebook"></i></a></li>
					<li><a href="<?= WHATSAPP; ?>" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
					<li><a href="<?= INSTAGRAM; ?>" target="_blank"><i class="fab fa-instagram"></i></a></li>	
				</ul>
			</div>
		</div>
	</footer>
	<div class="copy">
		<p>© <?= NOMBRE_EMPESA.' - '.date('Y'); ?></p>
	</div>
	<!-- <script type="text/javascript" src="sistema/js/jquery.min.js"></script>
	<script type="text/javascript" src="sistema/js/icons.js"></script>
	<script type="text/javascript" src="sistema/js/alertify.js"></script> -->
	<script src="<?= $base_url; ?>/js/functions_store.js"></script>
	<script type="text/javascript">
		var base_url = "<?php echo $base_url; ?>";
	</script>

</body>
</html>