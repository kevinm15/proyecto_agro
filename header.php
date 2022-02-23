<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="shortcut icon" href="sistema/img/favicon.ico">
	<meta name="description" content="Tienda virtual">
	<meta name="keywords" content="Variedad de artículos">
	<meta name="author" content="Abel OSH">
	<meta name="theme-color" content="#142247">
	<?php include __DIR__."/sistema/includes/scripts.php"; ?>
	<link rel="stylesheet" type="text/css" href="<?= $base_url; ?>/css/styles.css">
	<title>Tienda Virtual</title>
</head>
<body>
	<?php include_once "productos.php"; ?>
    <div class="modalStore" style="display: none;">
        <div class="bodyModalStore">
        </div>
    </div>
	<header>
		<?php 
			$telefono = "";
			$wp = "";
			$emailContact = "";
			$logoEmpresa = "";

			if(!empty(TELEFONO_EMPRESA))
			{
				$telefono = '<a href="tel://'.TELEFONO_EMPRESA.'"><i class="fas fa-phone" data-fa-transform="rotate-90"></i> '.TELEFONO_EMPRESA.'</a><span class="spsp">|</span>';
			}
			if(!empty(WHATSAPP))
			{
				$wp = '<a href="https://api.whatsapp.com/send?phone='.WHATSAPP.'&amp;text=Hola!%20Me%20pueden%20apoyar" target="_self"><i class="fab fa-whatsapp fa-lg"></i> '.WHATSAPP.'</a><span class="spsp">|</span>';
			}
			if(!empty(EMAIL_EMPRESA))
			{
				$emailContact = '<a href="mailto: '.EMAIL_EMPRESA.'" target="_self"><i class="fas fa-envelope"></i> '.EMAIL_EMPRESA.'</a><span class="spsp">|</span>';
			}
			if(!empty(LOGO_EMPRESA))
			{
				$logoEmpresa = '<a href="'.$base_url.'" class="imgLogo"><img src="'.$base_url.'/sistema/img/'.LOGO_EMPRESA.'" alt="logo"></a>';
			}
		 ?>
	    <div class="varinfo">
	      <div class="medios_contacto">
	        <?php 
	        	echo $emailContact;
	        	echo $telefono;
	        	echo $wp;
	         ?>
	        <div class="barHeaderCarrito">
	            <a href="<?= $base_url; ?>/carrito">
	                <span><i class="fas fa-shopping-cart"></i></span>
	                <span class="cantCarrito"><?= $cantCarrito; ?></span>
	            </a>
	        </div>
	      </div>
	    </div>
	    <div class="headerNav">
	      <?= $logoEmpresa; ?>
	      <div class="icon_menu"><i class="fas fa-bars fa-2x" ></i></div>
	        <nav id="menu_principal">
	            <ul>
	            	<li><a href="<?= $base_url; ?>">Productos</a></li>
	            	<li><a href="<?= $base_url; ?>/carrito">Carrito</a></li>
	            	<!-- <li><a href="<?= $base_url; ?>/contacto">Contacto</a></li> -->
	            </ul>
	        </nav>
	    </div>
  	</header>