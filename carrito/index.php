<?php
	session_start();
	$arrCarrito = array();
	if(isset($_SESSION['arrProductos']))
	{
		$arrCarrito = $_SESSION['arrProductos'];
	}
	include "../productos_carrito.php";
    include "../header.php"; 
 ?>
    <div class="modalStore" style="display: none;">
        <div class="bodyModalStore">
            <div class="colseModal"><span id="btnCloseModal">X</span></div>
            <div id="contentFormCotizador">
            	<div>
            		<div>
	            		<h2>Información de Contacto</h2>
	            		<p>Email: <strong><?php echo EMAIL_EMPRESA; ?></strong></p>
	            		<p>Tel: <strong><?php echo TELEFONO_EMPRESA; ?></strong></p>
            		</div>
            	</div>
            	<div>
            		<div>
		                <h3>DATOS DE CONTACTO</h3>
		                <form id="formPedido" action="" method="post" class="form">
			                <div class="wd50">
			                    <label for="nombre">Nombre completo</label>
			                    <input type="text" id="nombre_cliente" name="nombre_cliente" value="" required>
			                </div>
			                <div class="wd50">
			                    <label for="tel_cliente">Teléfono</label>
			                    <input type="text" id="tel_cliente" name="tel_cliente" value="" onkeypress="return controlTag(event);" required>
			                </div>
			                <div class="wd100">
			                    <label for="email_cliente">Email</label>
			                    <input type="text" id="email_cliente" name="email_cliente" value="" required>
			                </div>
			                <div class="wd100">
			                	<br>
			                	<h3>DATOS DE PEDIDO</h3>
			                </div>
			                <div class="wd30">
			                    <label for="nit"><?= IDENTIFICACION_TRIBUTARIA; ?></label>
			                    <input type="text" id="nit" name="nit" value="" required>
			                </div>
			                <div class="wd60">
			                    <label for="nombrefiscal">Nombre fiscal</label>
			                    <input type="text" id="nombrefiscal" name="nombrefiscal" value="" required>
			                </div>
			                <div class="wd100">
			                    <label for="ireccion">Dirección</label>
			                    <input type="text" id="direccion" name="direccion" value="" required>
			                </div>
			                <div class="wd100">
			                    <label for="ireccion">Tipo pago</label>
			                    <select name="tipopago" id="tipopago">
			                    	<option value="1" selected>Efectivo</option>
			                    	<option value="2">Tarjeta</option>
			                    </select>
			                </div>
			                <br>
                            <div class="alertForm"></div>
			                <div class="textcenter wd100">
                                <br>
			                    <button id="btnSendMsg" type="submit"><i class="far fa-envelope"></i> Enviar pedido</button>
			                    <input type="hidden" name="action" value="sendPedido">
			                </div>
		                </form>
            		</div>
            	</div>
            </div>
        </div>
    </div>
	<section class="containerPage">
        <div class="barrProductos">
            <h1>Mi carrito</h1>
        </div>

        <div class="container_carrito">
        <?php
            if(count($arrCarrito) > 0 )
            {
         ?>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col" colspan="2">Producto</th>
                        <th class="textcenter" scope="col">Precio</th>
                        <th class="textcenter" scope="col">Cantidad</th>
                        <th class="textright" scope="col">Total</th>

                    </tr>
                </thead>
                <tbody id="detalleCarrito">
                    <?php
                        $montoTotal = 0;
                        for ($p=0; $p < count($arrCarrito); $p++) {
                        	# code...
                            $total = $arrCarrito[$p]['precio'] * $arrCarrito[$p]['cantidad'];
                            $montoTotal += $total;
                     ?>
                    <tr id="row_<?php echo $arrCarrito[$p]['codproducto'] ; ?>" >
                        <th scope="row" class="textcenter"><span class="btnDelDetalle" onclick="delProdCarrito(<?php echo $arrCarrito[$p]['codproducto']; ?>);" ><i class="far fa-trash-alt" ></i></span></th>
                        <td><img class="imgDetalleCarrito" src="<?php echo $base_url ;?>/sistema/img/uploads/<?php echo $arrCarrito[$p]['foto'] ; ?>" alt="<?php echo $arrCarrito[$p]['producto']; ?>"></td>
                        <td><?php echo $arrCarrito[$p]['producto'] ; ?></td>
                        <td class="textcenter"><?php echo SIMBOLO_MONEDA.'. '. formatCant($arrCarrito[$p]['precio']) ; ?></td>
                        <td class="textcenter">
                        	<input type="number" min="1" name="cantProducto" id="prod_<?php echo $arrCarrito[$p]['codproducto']; ?>" class="cantProducto" value="<?php echo $arrCarrito[$p]['cantidad'] ; ?>" producto_id="<?php echo $arrCarrito[$p]['codproducto']; ?>" onkeypress="return controlTag(event);" required>
                        </td>
                        <td class="textright subTotal"><?php echo SIMBOLO_MONEDA.'. '. formatCant($total); ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="textright" colspan="5">Monto Total:</td>
                        <td id="totalCarrito" class="textright"><?php echo SIMBOLO_MONEDA.'. '. $montoTotal; ?></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <div class="containerBtnPago textright">
                <button type="button" id="btnCotizar" class="btn btn-primary" onclick="sendPedido();"><i class="fas fa-box"></i>&nbsp;&nbsp; Realiza pedido &nbsp;&nbsp;</button>
            </div>
        <?php
            }else{
         ?>
            <p>No hay productos en el carrito, <a href="<?= $base_url; ?>">Ver Productos</a></p>
        <?php } ?>

        </div>
	</section>
<?php include ("../footer.php"); ?>