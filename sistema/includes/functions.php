<?php
	//date_default_timezone_set('America/Guatemala');
	//Datos empresa
	$query_empresa = mysqli_query($conection,"SELECT * FROM configuracion");
	$num_rows = mysqli_num_rows($query_empresa);

	if($num_rows > 0)
	{
		$arrInfoEmpresa = mysqli_fetch_assoc($query_empresa);
		//dep($arrInfoEmpresa);
		define("NIT_EMPESA", $arrInfoEmpresa['nit']);
		define("NOMBRE_EMPESA", $arrInfoEmpresa['nombre']);
		define("RAZONSOCIAL_EMPESA", $arrInfoEmpresa['razon_social']);
		define("LOGO_EMPRESA", $arrInfoEmpresa['logotipo']);
		define("TELEFONO_EMPRESA", $arrInfoEmpresa['telefono']);
		define("WHATSAPP", $arrInfoEmpresa['whatsapp']);
		define("FACEBOOK", $arrInfoEmpresa['facebook']);
		define("INSTAGRAM", $arrInfoEmpresa['instagram']);
		define("EMAIL_EMPRESA", $arrInfoEmpresa['email']);
		define("DIRECCION_EMPRESA", $arrInfoEmpresa['direccion']);
		define("IMPUESTO", $arrInfoEmpresa['impuesto']);
		define("MONEDA", $arrInfoEmpresa['moneda']);
		define("SIMBOLO_MONEDA", $arrInfoEmpresa['simbolo_moneda']);
		define("WEB_EMPRESA", $arrInfoEmpresa['sitio_web']);
		define("EMAIL_PEDIDOS", $arrInfoEmpresa['email_pedidos']);
		define("EMAIL_FACTURAS", $arrInfoEmpresa['email_factura']);
		define("ZONA_HORARIA", $arrInfoEmpresa['zona_horaria']);
		define("IDENTIFICACION_CLIENTE", $arrInfoEmpresa['identificacion_cliente']);
		define("IDENTIFICACION_TRIBUTARIA", $arrInfoEmpresa['identificacion_tributaria']);
		define("SPM", $arrInfoEmpresa['separador_millares']);
		define("SPD", $arrInfoEmpresa['separador_decimales']);
	}else{
		define("NIT_EMPESA", '');
		define("NOMBRE_EMPESA", '');
		define("RAZONSOCIAL_EMPESA", '');
		define("LOGO_EMPRESA",'');
		define("TELEFONO_EMPRESA", '');
		define("WHATSAPP", '');
		define("FACEBOOK", '');
		define("INSTAGRAM", '');
		define("EMAIL_EMPRESA", '');
		define("DIRECCION_EMPRESA", '');
		define("IMPUESTO", '');
		define("MONEDA", '');
		define("SIMBOLO_MONEDA", '');
		define("WEB_EMPRESA", '');
		define("EMAIL_PEDIDOS",'');
		define("EMAIL_FACTURAS",'');
		define("ZONA_HORARIA", '');
		define("IDENTIFICACION_CLIENTE", '');
		define("IDENTIFICACION_TRIBUTARIA", '');
		define("SPM", ",");
		define("SPD", ".");
	}
	date_default_timezone_set(ZONA_HORARIA);
	function fechaC(){
		$mes = array("","Enero", 
					  "Febrero", 
					  "Marzo", 
					  "Abril", 
					  "Mayo", 
					  "Junio", 
					  "Julio", 
					  "Agosto", 
					  "Septiembre", 
					  "Octubre", 
					  "Noviembre", 
					  "Diciembre");
		return date('d')." de ". $mes[date('n')] . " de " . date('Y');
	}

	function fntMeses(){
		$meses = array("Enero", 
					  "Febrero", 
					  "Marzo", 
					  "Abril", 
					  "Mayo", 
					  "Junio", 
					  "Julio", 
					  "Agosto", 
					  "Septiembre", 
					  "Octubre", 
					  "Noviembre", 
					  "Diciembre");
		return $meses;
	}
	//Formato Factura
	function formatFactura($factura,$ceros){
		$intFactura = str_pad($factura,$ceros,'0',STR_PAD_LEFT);
		return $intFactura;
	}
	//Elimina exceso de espacios entre palabras
    function strClean($strCadena){
        $string = preg_replace(['/\s+/','/^\s|\s$/'],[' ',''], $strCadena);
        $string = trim($string); //Elimina espacios en blanco al inicio y al final
        $string = stripslashes($string); // Elimina las \ invertidas
        $string = str_ireplace("<script>","",$string);
        $string = str_ireplace("</script>","",$string);
        $string = str_ireplace("<script src>","",$string);
        $string = str_ireplace("<script type=>","",$string);
        $string = str_ireplace("SELECT * FROM","",$string);
        $string = str_ireplace("DELETE FROM","",$string);
        $string = str_ireplace("INSERT INTO","",$string);
        $string = str_ireplace("SELECT COUNT(*) FROM","",$string);
        $string = str_ireplace("DROP TABLE","",$string);
        $string = str_ireplace("OR '1'='1","",$string);
        $string = str_ireplace('OR "1"="1"',"",$string);
        $string = str_ireplace('OR ´1´=´1´',"",$string);
        $string = str_ireplace("is NULL; --","",$string);
        $string = str_ireplace("is NULL; --","",$string);
        $string = str_ireplace("LIKE '","",$string);
        $string = str_ireplace('LIKE "',"",$string);
        $string = str_ireplace("LIKE ´","",$string);
        $string = str_ireplace("OR 'a'='a","",$string);
        $string = str_ireplace('OR "a"="a',"",$string);
        $string = str_ireplace("OR ´a´=´a","",$string);
        $string = str_ireplace("OR ´a´=´a","",$string);
        $string = str_ireplace("--","",$string);
        $string = str_ireplace("^","",$string);
        $string = str_ireplace("[","",$string);
        $string = str_ireplace("]","",$string);
        $string = str_ireplace("==","",$string);
        return $string;
    }

	function encrypt($string, $key) {
	   $result = '';
	   for($i=0; $i<strlen($string); $i++) {
	      $char = substr($string, $i, 1);
	      $keychar = substr($key, ($i % strlen($key))-1, 1);
	      $char = chr(ord($char)+ord($keychar));
	      $result.=$char;
	   }
	   return base64_encode($result);
	}

	function decrypt($string, $key) {
	   $result = '';
	   $string = base64_decode($string);
	   for($i=0; $i<strlen($string); $i++) {
	      $char = substr($string, $i, 1);
	      $keychar = substr($key, ($i % strlen($key))-1, 1);
	      $char = chr(ord($char)-ord($keychar));
	      $result.=$char;
	   }
	   return $result;
	}

	function dep($infoarray){
		print_r('<pre>');
		print_r($infoarray);
		print_r('</pre>');

	}

	function formatCant($cantidad){
		$cantidad = number_format($cantidad,2,SPD,SPM);
		return $cantidad;
	}

	function sendEmail($data,$template)
	{
		$asunto = $data['asunto'];
		$emailDestino = $data['emailDestino'];
		$empresa = NOMBRE_EMPESA;
		$remitente = $data['emailRemitente'];
		$from = "From: {$empresa} <{$remitente}>";
		//ENVIO DE CORREO
		$de = "MIME-Version: 1.0\n";
		$de .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$de .= "Content-type: text/html; charset=UTF-8\r\n";
		$de .= $from."\r\n";
		//$de .= "From: Nuevo pedido <info@abelosh.com>\r\n";
		ob_start();
	    require_once('template/'.$template.'.php');
	    $mensaje = ob_get_clean();
		$send = mail($emailDestino, $asunto, $mensaje, $de) or die('Hubo un error');
		return $send;
	}
	//Copia una imágen a una ruta determinada
	function copyImage($url_temp,$typeImg,$img_nombre,$max_ancho,$max_alto){
        //Ruta de la original
		//Crear variable de imagen a partir de la original
        if($typeImg == "jpg" || $typeImg == "jpeg" || $typeImg == "pjpeg"){
            $original = @imagecreatefromjpeg($url_temp);
        }else if($typeImg == "png"){
            $original = @imagecreatefrompng($url_temp);
        }else if($typeImg == "gif"){
            $original = @imagecreatefromgif($url_temp);
        }
        //Recoger ancho y alto de la original
        list($ancho,$alto)=getimagesize($url_temp);
        //Calcular proporción ancho y alto
        $x_ratio = $max_ancho / $ancho;
        $y_ratio = $max_alto / $alto;

        if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){
        //Si es más pequeña que el máximo no redimensionamos
            $ancho_final = $ancho;
            $alto_final = $alto;
        }
        //si no calculamos si es más alta o más ancha y redimensionamos
        elseif (($x_ratio * $alto) < $max_alto){
            $alto_final = ceil($x_ratio * $alto);
            $ancho_final = $max_ancho;
        }else{
            $ancho_final = ceil($y_ratio * $ancho);
            $alto_final = $max_alto;
        }
        //Crear lienzo en blanco con proporciones
        $lienzo=imagecreatetruecolor($ancho_final,$alto_final);
        //Copiar $original sobre la imagen que acabamos de crear en blanco ($tmp)
        imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final,$alto_final,$ancho,$alto);
        //Limpiar memoria
        imagedestroy($original);
        //Definimos la calidad de la imagen final
        $cal=75;
        //Se crea la imagen final en el directorio indicado
        $dataimg = imagejpeg($lienzo,"../img/uploads/".$img_nombre,$cal);
		//move_uploaded_file($url_temp, $src);
        return $dataimg;
    }
	function check_range($fecha_inicio, $fecha_fin, $fecha){
	    $fecha_inicio = strtotime($fecha_inicio);
	    $fecha_fin = strtotime($fecha_fin);
	    $fecha = strtotime($fecha);

	    if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
	        return true;
	    } else {
	        return false;
	    }
	}
	function unidad($numuero){
	switch ($numuero)
	{
	case 9:
	{
	$numu = "NUEVE";
	break;
	}
	case 8:
	{
	$numu = "OCHO";
	break;
	}
	case 7:
	{
	$numu = "SIETE";
	break;
	}
	case 6:
	{
	$numu = "SEIS";
	break;
	}
	case 5:
	{
	$numu = "CINCO";
	break;
	}
	case 4:
	{
	$numu = "CUATRO";
	break;
	}
	case 3:
	{
	$numu = "TRES";
	break;
	}
	case 2:
	{
	$numu = "DOS";
	break;
	}
	case 1:
	{
	$numu = "UNO";
	break;
	}
	case 0:
	{
	$numu = "";
	break;
	}
	}
	return $numu;
	}

	function decena($numdero){

	if ($numdero >= 90 && $numdero <= 99)
	{
	$numd = "NOVENTA ";
	if ($numdero > 90)
	$numd = $numd."Y ".(unidad($numdero - 90));
	}
	else if ($numdero >= 80 && $numdero <= 89)
	{
	$numd = "OCHENTA ";
	if ($numdero > 80)
	$numd = $numd."Y ".(unidad($numdero - 80));
	}
	else if ($numdero >= 70 && $numdero <= 79)
	{
	$numd = "SETENTA ";
	if ($numdero > 70)
	$numd = $numd."Y ".(unidad($numdero - 70));
	}
	else if ($numdero >= 60 && $numdero <= 69)
	{
	$numd = "SESENTA ";
	if ($numdero > 60)
	$numd = $numd."Y ".(unidad($numdero - 60));
	}
	else if ($numdero >= 50 && $numdero <= 59)
	{
	$numd = "CINCUENTA ";
	if ($numdero > 50)
	$numd = $numd."Y ".(unidad($numdero - 50));
	}
	else if ($numdero >= 40 && $numdero <= 49)
	{
	$numd = "CUARENTA ";
	if ($numdero > 40)
	$numd = $numd."Y ".(unidad($numdero - 40));
	}
	else if ($numdero >= 30 && $numdero <= 39)
	{
	$numd = "TREINTA ";
	if ($numdero > 30)
	$numd = $numd."Y ".(unidad($numdero - 30));
	}
	else if ($numdero >= 20 && $numdero <= 29)
	{
	if ($numdero == 20)
	$numd = "VEINTE ";
	else
	$numd = "VEINTI".(unidad($numdero - 20));
	}
	else if ($numdero >= 10 && $numdero <= 19)
	{
	switch ($numdero){
	case 10:
	{
	$numd = "DIEZ ";
	break;
	}
	case 11:
	{
	$numd = "ONCE ";
	break;
	}
	case 12:
	{
	$numd = "DOCE ";
	break;
	}
	case 13:
	{
	$numd = "TRECE ";
	break;
	}
	case 14:
	{
	$numd = "CATORCE ";
	break;
	}
	case 15:
	{
	$numd = "QUINCE ";
	break;
	}
	case 16:
	{
	$numd = "DIECISEIS ";
	break;
	}
	case 17:
	{
	$numd = "DIECISIETE ";
	break;
	}
	case 18:
	{
	$numd = "DIECIOCHO ";
	break;
	}
	case 19:
	{
	$numd = "DIECINUEVE ";
	break;
	}
	}
	}
	else
	$numd = unidad($numdero);
	return $numd;
	}

	function centena($numc){
	if ($numc >= 100)
	{
	if ($numc >= 900 && $numc <= 999)
	{
	$numce = "NOVECIENTOS ";
	if ($numc > 900)
	$numce = $numce.(decena($numc - 900));
	}
	else if ($numc >= 800 && $numc <= 899)
	{
	$numce = "OCHOCIENTOS ";
	if ($numc > 800)
	$numce = $numce.(decena($numc - 800));
	}
	else if ($numc >= 700 && $numc <= 799)
	{
	$numce = "SETECIENTOS ";
	if ($numc > 700)
	$numce = $numce.(decena($numc - 700));
	}
	else if ($numc >= 600 && $numc <= 699)
	{
	$numce = "SEISCIENTOS ";
	if ($numc > 600)
	$numce = $numce.(decena($numc - 600));
	}
	else if ($numc >= 500 && $numc <= 599)
	{
	$numce = "QUINIENTOS ";
	if ($numc > 500)
	$numce = $numce.(decena($numc - 500));
	}
	else if ($numc >= 400 && $numc <= 499)
	{
	$numce = "CUATROCIENTOS ";
	if ($numc > 400)
	$numce = $numce.(decena($numc - 400));
	}
	else if ($numc >= 300 && $numc <= 399)
	{
	$numce = "TRESCIENTOS ";
	if ($numc > 300)
	$numce = $numce.(decena($numc - 300));
	}
	else if ($numc >= 200 && $numc <= 299)
	{
	$numce = "DOSCIENTOS ";
	if ($numc > 200)
	$numce = $numce.(decena($numc - 200));
	}
	else if ($numc >= 100 && $numc <= 199)
	{
	if ($numc == 100)
	$numce = "CIEN ";
	else
	$numce = "CIENTO ".(decena($numc - 100));
	}
	}
	else
	$numce = decena($numc);

	return $numce;
	}

	function miles($nummero){
	if ($nummero >= 1000 && $nummero < 2000){
	$numm = "MIL ".(centena($nummero%1000));
	}
	if ($nummero >= 2000 && $nummero <10000){
	$numm = unidad(Floor($nummero/1000))." MIL ".(centena($nummero%1000));
	}
	if ($nummero < 1000)
	$numm = centena($nummero);

	return $numm;
	}

	function decmiles($numdmero){
	if ($numdmero == 10000)
	$numde = "DIEZ MIL";
	if ($numdmero > 10000 && $numdmero <20000){
	$numde = decena(Floor($numdmero/1000))."MIL ".(centena($numdmero%1000));
	}
	if ($numdmero >= 20000 && $numdmero <100000){
	$numde = decena(Floor($numdmero/1000))." MIL ".(miles($numdmero%1000));
	}
	if ($numdmero < 10000)
	$numde = miles($numdmero);

	return $numde;
	}

	function cienmiles($numcmero){
	if ($numcmero == 100000)
	$num_letracm = "CIEN MIL";
	if ($numcmero >= 100000 && $numcmero <1000000){
	$num_letracm = centena(Floor($numcmero/1000))." MIL ".(centena($numcmero%1000));
	}
	if ($numcmero < 100000)
	$num_letracm = decmiles($numcmero);
	return $num_letracm;
	}

	function millon($nummiero){
	if ($nummiero >= 1000000 && $nummiero <2000000){
	$num_letramm = "UN MILLON ".(cienmiles($nummiero%1000000));
	}
	if ($nummiero >= 2000000 && $nummiero <10000000){
	$num_letramm = unidad(Floor($nummiero/1000000))." MILLONES ".(cienmiles($nummiero%1000000));
	}
	if ($nummiero < 1000000)
	$num_letramm = cienmiles($nummiero);

	return $num_letramm;
	}

	function decmillon($numerodm){
	if ($numerodm == 10000000)
	$num_letradmm = "DIEZ MILLONES";
	if ($numerodm > 10000000 && $numerodm <20000000){
	$num_letradmm = decena(Floor($numerodm/1000000))."MILLONES ".(cienmiles($numerodm%1000000));
	}
	if ($numerodm >= 20000000 && $numerodm <100000000){
	$num_letradmm = decena(Floor($numerodm/1000000))." MILLONES ".(millon($numerodm%1000000));
	}
	if ($numerodm < 10000000)
	$num_letradmm = millon($numerodm);

	return $num_letradmm;
	}

	function cienmillon($numcmeros){
	if ($numcmeros == 100000000)
	$num_letracms = "CIEN MILLONES";
	if ($numcmeros >= 100000000 && $numcmeros <1000000000){
	$num_letracms = centena(Floor($numcmeros/1000000))." MILLONES ".(millon($numcmeros%1000000));
	}
	if ($numcmeros < 100000000)
	$num_letracms = decmillon($numcmeros);
	return $num_letracms;
	}

	function milmillon($nummierod){
	if ($nummierod >= 1000000000 && $nummierod <2000000000){
	$num_letrammd = "MIL ".(cienmillon($nummierod%1000000000));
	}
	if ($nummierod >= 2000000000 && $nummierod <10000000000){
	$num_letrammd = unidad(Floor($nummierod/1000000000))." MIL ".(cienmillon($nummierod%1000000000));
	}
	if ($nummierod < 1000000000)
	$num_letrammd = cienmillon($nummierod);

	return $num_letrammd;
	}

	function montoLetras($numero){
	$tempnum = explode('.',$numero);
	if ($tempnum[0] !== ""){
	$numf = milmillon($tempnum[0]);
	if ($numf == "UNO")
	{
	$numf = substr($numf, 0, -1);
	$Ps = " ".MONEDA." CON ";
	}
	else
	{
	$Ps = " ".MONEDA." CON ";
	}
	$TextEnd = $numf;
	$TextEnd .= $Ps;
	}
	if ($tempnum[1] == "" || $tempnum[1] >= 100)
	{
	$tempnum[1] = "00" ;
	}
	$TextEnd .= $tempnum[1] ;
	$TextEnd .= "/100 CENTAVOS";
	return $TextEnd;
	}




 ?>