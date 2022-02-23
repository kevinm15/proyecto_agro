jQuery(document).ready(function($) {

	$('.infoProducto').click(function(e) {
		e.preventDefault();
		var action = 'infoProducto';
		var producto = $(this).attr('prod_id');
        $.ajax({
                url : base_url+'/sistema/ajax.php',
                type: "POST",
                async : true,
                data: {action:action,producto:producto},
                beforeSend: function(){
                },
                success: function(response)
                {
                    if(response != 'error')
                    {
                        var info = JSON.parse(response);
                        var htmlModal = '<div class="colseModal"><span id="btnCloseModal">X</span></div>'+
					                '<div class="containerInfoProducto">'+
					                    '<div class="imgProducto">'+
					                        '<img src="'+base_url+'/sistema/'+info.foto+'" alt="'+info.producto+'">'+
					                    '</div>'+
					                    '<div class="desciption">'+
					                		'<h2 class="titleNombreProducto">'+info.producto+'</h2>'+
					                		'<div>'+
					                			'<p>'+info.descripcion+'</p>'+
					                		'</div>'+
					                		'<span class="spanPrecio">Precion: <strong>'+info.precio+'</strong></span>'+
					                		'<input type="number" id="txtCantidad" name="txtCantidad" value="1" min="1">'+
					               			'<button id="btnCotizar" onclick="fntAddCarProd('+info.codproducto+','+info.precio+','+'1)"><i class="fas fa-shopping-cart"></i> Agregar</button>'+
                                            '<div class="alertModal"></div>'+
					                    '</div>'+
					                '</div>';
					    $('.bodyModalStore').html(htmlModal);
                        $('.modalStore').fadeIn();
					    var btnClose = document.getElementById('btnCloseModal');
					    btnClose.addEventListener("click",fntClosModal);
                    }
                },
                error: function(error) {
                }
        });
	});

    if($('#formPedido').length)
    {
        $('#formPedido').submit(function(e){
            e.preventDefault();

            if(!fntEmailValidate($('#email_cliente').val()))
            {
                $('.alertForm').html('<p style="color:red;"><strong>El correo electrónico no es válido.</strong></p>');
                return false;
            }

            $.ajax({
                url : base_url+'/sistema/ajax.php',
                type: "POST",
                async : true,
                data: $('#formPedido').serialize(),
                beforeSend: function(){
                    $('.alertForm').html('');
                    $('#formPedido select').attr('disabled','disabled');
                    $('#formPedido input').attr('disabled','disabled');
                },
                success: function(response)
                {
                    if(response == 'errorDetalle' || response == 'errorPedido' || response =='errorContacto')
                    {
                    	alertify.alert("Atención","En este momento no es posible procesar tu pedido.", function(){
		                    alertify.message('OK');
		                });
                        $('#formPedido select').removeAttr('disabled');
                        $('#formPedido input').removeAttr('disabled');
                    }else{
                    	var confirm = `
	                    <div class="sendOk textcenter">
		            		<img src="`+base_url+`/sistema/img/check.png" alt="Éxito">
		            		<br>
		            		<h2>Tu pedido ha sido <br> realizado</h2>
		            		<br>
		            		<h2><strong>No. Pedido: `+response+`</strong></h2>
		            		<br>
		            		<button class="btnGreen" type="button" onclick="window.location=base_url"> Cerrar </button>
		            	</div>`;
                    	$('#contentFormCotizador').html(confirm);
                    }
                },
                error: function(error) {
                }
            });
        });
    }

    if($('.cantProducto').length)
    {
    	$('.cantProducto').keyup(function() {
    		var cant = $(this).val();
    		var producto = $(this).attr('producto_id');
    		updateCant(producto,cant);
    	});
    	$('.cantProducto').change(function() {
    		/* Act on the event */
    		var cant = $(this).val();
    		var producto = $(this).attr('producto_id');
    		updateCant(producto,cant);
    	});

    }

	$('.btnCloseModal').click(function(e) {
		e.preventDefault();
		$('.modalStore').fadeOut();
	});

});

function updateCant(producto,cant){
	if(cant <= 0)
	{
		alertify.error('Ingrese cantidad...');
		$('#btnCotizar').hide();
		$('#btnCotizar').removeAttr('onclick');
		return false;
	}else{
		$('#btnCotizar').show();
		$('#btnCotizar').attr('onclick','sendPedido();');
	}
	var action = "updCantidadCarrito";
	$.ajax({
        url : base_url+'/sistema/ajax.php',
        type: "POST",
        async : true,
        data: {action:action,coproducto:producto,cantidad:cant},
        beforeSend: function(){
            $('.alertModal').slideUp();
        },
        success: function(response)
        {
        	if(response == 'errorData')
        	{
        		alertify.success('No es posible actualizar la cantidad...');
        		return false;
        	}
        	var cantCarrito = 0;
            var info = JSON.parse(response);
            var productos = info.productos;

            for (var i = 0; i < productos.length; i++) {
            	cantCarrito += parseInt(productos[i].cantidad);
            }
            $('.cantCarrito').html(cantCarrito);
            $('#totalCarrito').html(info.total);
            $('#row_'+producto+' .subTotal').html(info.subtotal);
        },
        error: function(error) {
        }
    });

}

function fntAddCarProd(producto,precio,op){
	var action = 'addCarrito';
	var coproducto = producto;
    var precio = precio;
	var cantidad = 1;
	if(op == 1)
	{
		cantidad = $('#txtCantidad').val();
	}

	$.ajax({
        url : base_url+'/sistema/ajax.php',
        type: "POST",
        async : true,
        data: {action:action,coproducto:coproducto,precio:precio,cantidad:cantidad},
        beforeSend: function(){
            $('.alertModal').slideUp();
        },
        success: function(response)
        {
        	var cantCarrito = 0;
            var info = JSON.parse(response);
            for (var i = 0; i < info.length; i++) {
            	cantCarrito += parseInt(info[i].cantidad);
            }

            if($('.containerInfoProducto').length)
            {
                $('.alertModal').html('<p>Producto agregado!</p>'); 
                $('.alertModal').slideDown();
            }else{
                alertify.success('Producto agregado...');
            }
            $('.cantCarrito').html(cantCarrito);
        },
        error: function(error) {
        }
    });
}

function delProdCarrito(codproducto)
{
    var codproducto = codproducto;
    var action = "delItemCarrito";

    $.ajax({
        url : base_url+'/sistema/ajax.php',
        type: "POST",
        async : true,
        data: {action:action,codproducto:codproducto},
        beforeSend: function(){
            $('.alertModal').slideUp();
        },
        success: function(response)
        {
            var cantCarrito = 0;
            var info = JSON.parse(response);

            alertify.success('Producto eliminado');
            $('.container_carrito').html(info.htmlTable);
            $('.cantCarrito').html(info.cant);
            $('.cantProducto').keyup(function() {
	    		var cant = $(this).val();
	    		var producto = $(this).attr('producto_id');
	    		updateCant(producto,cant);
	    	});
	    	$('.cantProducto').change(function() {
	    		/* Act on the event */
	    		var cant = $(this).val();
	    		var producto = $(this).attr('producto_id');
	    		updateCant(producto,cant);
	    	});
        },
        error: function(error) {
        }
    });
}

function sendPedido()
{
    $('.modalStore').fadeIn();
    var btnClose = document.getElementById('btnCloseModal');
    btnClose.addEventListener("click",fntClosModal);
    $('#nombre_cliente').focus();
}
function controlTag(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla==8) return true;
    else if (tecla==0||tecla==9)  return true;
    patron =/[0-9\s]/;
    te = String.fromCharCode(tecla);
    return patron.test(te); 
}
function fntClosModal(){
    $('.modalStore').fadeOut();
	//$('.bodyModalStore').html('');
}

//===================== SUMAR AL CARRITO ===========================
function sumaCant(){
    var cant = document.getElementById("txtCantidad").value;
    var cantidad = parseInt(cant) + 1;
    document.getElementById("txtCantidad").value = cantidad;
}
function restaCant(){
    var cant = document.getElementById("txtCantidad").value;

    if(cant == 1){
        document.getElementById("txtCantidad").value = 1;
    }else{
    var cantidad = parseInt(cant) - 1;
    document.getElementById("txtCantidad").value = cantidad;
    }
}