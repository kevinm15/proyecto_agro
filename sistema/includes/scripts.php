		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
		<link rel="shortcut icon" href="img/favicon.ico">
		<meta name="description" content="Tienda virtual">
		<meta name="keywords" content="Sistema administrativo">
		<meta name="author" content="Abel OSH">
		<meta name="theme-color" content="#0a4661">
	<?php
		ini_set('display_errors', 1);
	    ini_set('display_startup_errors', 1);
	    error_reporting(E_ALL);
		include_once __DIR__."/config.php";
		$stylecss = '<link rel="stylesheet" type="text/css" href="'.$base_url.'/sistema/css/style.css">';
		$responsivecss = '<link rel="stylesheet" type="text/css" href="'.$base_url.'/sistema/css/responsive.css">';
		$alertcss = '<link rel="stylesheet" type="text/css" href="'.$base_url.'/sistema/css/alert/alertify.css">';
		$semanticcss = '<link rel="stylesheet" type="text/css" href="'.$base_url.'/sistema/css/alert/themes/semantic.css">';
		$jqueryuicss = '<link rel="stylesheet" type="text/css" href="'.$base_url.'/sistema/css/jquery-ui.css">';
		//Script JS
		$jquery = '<script type="text/javascript" src="'.$base_url.'/sistema/js/jquery.min.js"></script>';
		$icons = '<script type="text/javascript" src="'.$base_url.'/sistema/js/icons.js"></script>';
		$functions = '<script type="text/javascript" src="'.$base_url.'/sistema/js/functions.js"></script>';
		$icontains = '<script type="text/javascript" src="'.$base_url.'/sistema/js/icontains.js"></script>';
		$alertify = '<script type="text/javascript" src="'.$base_url.'/sistema/js/alertify.js"></script>';
		$jqueryui = '<script type="text/javascript" src="'.$base_url.'/sistema/js/jquery-ui.min.js"></script>';
		//Script graficas
		$char = '<script type="text/javascript" src="'.$base_url.'/sistema/js/highcharts.js"></script>';
		$char2 = '<script type="text/javascript" src="'.$base_url.'/sistema/js/exporting.js"></script>';

		echo $stylecss;
		echo $responsivecss;
		echo $alertcss;
		echo $semanticcss;
		echo $jqueryuicss;

		echo $jquery;
		echo $icons;
		echo $functions;
		echo $icontains;
		echo $alertify;
		echo $jqueryui;

		echo $char;
		echo $char2;
	?>

    <script type="text/javascript">
		var base_url = "<?php echo $base_url; ?>";
	</script>
	<?php
		include_once __DIR__."/../../conexion.php";
		include_once "functions.php";
	?>