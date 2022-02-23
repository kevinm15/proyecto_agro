$(document).ready(function(){

    $('.btnMenu').click(function(e) {
        e.preventDefault();
        if($('nav').hasClass('viewMenu'))
        {
            $('nav').removeClass('viewMenu');
        }else{
            $('nav').addClass('viewMenu');
        }
    });

    $('nav .principal').click(function() {
         $('nav ul li ul').slideUp();

         if($(this).children('ul').css('display') == 'block'){
            $(this).children('ul').slideUp();
         }else{
            $(this).children('ul').slideDown();
         }
    });

    if($('.copy').length)
    {
        $('.copy').append(library);
    }
    //--------------------- SELECCIONAR FOTO PRODUCTO ---------------------
    $("#foto").on("change",function(){
        var uploadFoto = document.getElementById("foto").value;
        var foto       = document.getElementById("foto").files;
        var nav = window.URL || window.webkitURL;
        var contactAlert = document.getElementById('form_alert');

            if(uploadFoto !='')
            {
                var type = foto[0].type;
                var name = foto[0].name;
                if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png')
                {
                    contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';
                    $("#img").remove();
                    $(".delPhoto").addClass('notBlock');
                    $('#foto').val('');
                    return false;
                }else{
                        contactAlert.innerHTML='';
                        $("#img").remove();
                        $(".delPhoto").removeClass('notBlock');
                        var objeto_url = nav.createObjectURL(this.files[0]);
                        $(".prevPhoto").append("<img id='img' src="+objeto_url+">");
                        $(".upimg label").remove();
                    }
              }else{
                alertify.alert("No se ha seleccionado foto.", function(){
                    alertify.message('OK');
                });
                $("#img").remove();
              }
    });

    $('.delPhoto').click(function(e){
        e.preventDefault();
        $('#foto').val('');
        $(".delPhoto").addClass('notBlock');
        $("#img").remove();
        var photo= '<img id="img" src="'+ base_url+'/sistema/img/uploads/img_producto.png" alt="Photo">';
        $('.prevPhoto').append(photo)
        if($("#foto_actual") && $("#foto_remove")){
            $("#foto_remove").val('img_producto.png');
        }

    });

    //--------------------- SELECCIONAR LOGO EMPRESA ---------------------
    $("#logo").on("change",function(){
        var uploadFoto = document.getElementById("logo").value;
        var foto       = document.getElementById("logo").files;
        var nav = window.URL || window.webkitURL;
        var contactAlert = document.getElementById('form_alert');

            if(uploadFoto !='')
            {
                var type = foto[0].type;
                var name = foto[0].name;
                if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png')
                {
                    contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';
                    $("#img").remove();
                    $(".delLogo").addClass('notBlock');
                    $('#logo').val('');
                    return false;
                }else{
                        contactAlert.innerHTML='';
                        $("#img").remove();
                        $(".delLogo").removeClass('notBlock');
                        var objeto_url = nav.createObjectURL(this.files[0]);
                        $(".prevPhoto").append("<img id='img' src="+objeto_url+">");
                        $(".upimg label").remove();
                    }
              }else{
                alertify.alert("No se ha seleccionado foto.", function(){
                    alertify.message('OK');
                });
                $("#img").remove();
              }
    });
    $('.delLogo').click(function(e){
        e.preventDefault();
        $('#logo').val('');
        $(".delLogo").addClass('notBlock');
        $("#img").remove();
        var photo= '<img id="img" src="'+ base_url+'/sistema/img/logoEmpresa.png" alt="Photo">';
        $('.prevPhoto').append(photo)
        if($("#foto_actual") && $("#foto_remove")){
            $("#foto_remove").val('img_producto.png');
        }

    });
    $('#formLogo').submit(function(e) {
        e.preventDefault();

        var data = new FormData(this);

        $.ajax({
            url: 'ajax.php',
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            async : true,
            data: data,
            beforeSend: function(){
            },
            success: function(response)
            {
                if(response=="ok")
                {
                    alertify.alert("Atención","Logotipo actualizado correctamente.", function(){
                        alertify.message('OK');
                    });
                }else{
                    alertify.alert("Error","Error en actualizar.", function(){
                        alertify.message('OK');
                    });
                }
            },
            error: function(error) {
            }
        });
    });

    $('.add_product').click(function(e) {
        e.preventDefault();
        var producto = $(this).attr('product');
        var action = 'infoProducto';

        $('.alertAddProduct').html('');

        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,producto:producto},
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(response)
                {
                    if(response != 'error')
                    {
                        var info = JSON.parse(response);
                        if(info.existencia > 0)
                        {
                            $('.bodyModal').html('<form action="" id="form_add_product" name="form_add_product" onsubmit="event.preventDefault(); sendDataProduct();">'+
                                '<h1><i class="fas fa-cart-plus" style="font-size: 30pt;"></i><br><br>Agregar producto al carrito</h1><br>'+
                                '<h2 class="nameProducto">Código: '+info.codebar+'</h2>'+
                                '<h2 class="nameProducto">'+info.producto+'</h2><br>'+
                                '<p class="">Existencia: <strong>'+info.existencia+'</strong></p>'+
                                '<p class="">Precio: <strong>'+info.precio+'</strong></p><br>'+

                                '<label>Cantidad a vender: </label><input type="number" name="cantidad" id="txtCantidad" placeholder="Cantidad" value="1" required style="width:120px;margin:auto;"><br>'+
                                '<input type="hidden" name="producto" id="producto" value="'+info.codproducto+'" required>'+
                                '<div class="alert alertAddProduct"></div>'+
                                '<input type="hidden" name="action" value="addProductoDetalle" required>'+
                                '<button type="submit" class="btn_new"><i class="fas fa-cart-plus"></i> Agregar </button>'+
                                '<button type="button" class="btn_ok closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar </button>'+
                            '</form>');
                            modalView();
                        }else{
                            alertify.alert("Atención","Existencia del producto es de 0.", function(){
                                alertify.message('OK');
                            });
                        }
                    }
                    $('.loading').hide();
                },
                error: function(error) {
                }
        });
    });

    //Modal Eliminar Producto
    $('.del_product').click(function(e) {
        e.preventDefault();
        var producto = $(this).attr('product');
        var action = 'infoProducto';

        $('.alertAddProduct').html('');

        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,producto:producto},
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(response)
                {
                    if(response != 'error')
                    {
                        var info = JSON.parse(response);
                        $('.bodyModal').html('<form action="" id="form_del_product" name="form_del_product" onsubmit="event.preventDefault(); delProduct();">'+
                            '<h1><i class="fas fa-cubes" style="font-size: 45pt;"></i> <br>Eliminar producto</h1><br>'+
                            '<h2 class="nameProducto">'+info.producto+'</h2><br>'+
                            '<input type="hidden" name="action" value="delProducto">'+
                            '<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<input type="hidden" name="action" value="delProduct" required>'+
                            '<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');

                        modalView();
                    }
                    $('.loading').hide();
                },
                error: function(error) {
                }
            });
    });

    //Agregar Productos
    $('#form_add_product').submit(function(e) {
        e.preventDefault();

        var pr = $('#producto_id').val();
        //e.stopImmediatePropagation();
        $('.alertAddProduct').html('');
        $.ajax({
            url:  '../ajax.php',
            type: "POST",
            async : true,
            data: $("#form_add_product").serialize(),

            beforeSend: function(){
                $('.loading').show();
            },

            success: function(response)
            {
                if(response == 'error'){
                    $('.alertAddProduct').html('<p style="color:red;">Error al agregar el producto.</p>');
                }else{
                    var data  = $.parseJSON(response);
                    $('.row'+pr+' .celPrecio').html(data.nuevo_precio);
                    $('.row'+pr+' .celExistencia').html(data.nueva_existencia);
                    $('#txtCantidad').val('');
                    $('#txtPrecio').val('');
                    $('.alertAddProduct').html('<p>Producto agregado correctamente.</p>');
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });

    });

    $('#search_marca').change(function(e){
        e.preventDefault();
        var sistema = getUrl();
        location.href = sistema+'buscar_productos.php?marca='+$(this).val();
    });


// ------------------------ Generar Venta -------------------------
    //Buscar Cliente
    $('#nit_cliente').keyup(function(e) {
        e.preventDefault();
        /* Act on the event */
        var cl = $(this).val();
        var action = 'searchCliente'

        if(cl != 'cf' || cl== 'CF' || cl== 0){
            $.ajax({
                url:  '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,cliente:cl},

                success: function(response)
                {
                    if(response == 0){
                        $('#idcliente').val('');
                        $('#nom_cliente').val('');
                        $('#tel_cliente').val('');
                        $('#dir_cliente').val('');
                        //Mostrar boton agregar
                        $('.btn_new_cliente').slideDown();
                    }else{
                        var data  = $.parseJSON(response);
                        $('#idcliente').val(data.idcliente);
                        $('#nom_cliente').val(data.nombre);
                        $('#tel_cliente').val(data.telefono);
                        $('#dir_cliente').val(data.direccion);
                        //Ocultar boton agregar
                        $('.btn_new_cliente').slideUp();

                        //Bloque campos
                        $('#nom_cliente').attr('disabled','disabled');
                        $('#tel_cliente').attr('disabled','disabled');
                        $('#dir_cliente').attr('disabled','disabled');

                        //Oculta boton guardar
                        $('#div_registro_cliente').slideUp();
                    }
                },
                error: function(error) {
                }
            });
        }

    });

    //Activa campos para registrar cliente
    $('.btn_new_cliente').click(function(e){
        e.preventDefault();
        $('#nom_cliente').removeAttr('disabled');
        $('#tel_cliente').removeAttr('disabled');
        $('#dir_cliente').removeAttr('disabled');

        $('#div_registro_cliente').slideDown();
    });

    //Agregar Cliente
    $('#form_new_cliente_venta').submit(function(e) {
        e.preventDefault();

        $.ajax({
            url:  '../ajax.php',
            type: "POST",
            async : true,
            data: $("#form_new_cliente_venta").serialize(),

            success: function(response)
            {
                if(response != 'error'){
                    //Agregar id a input hiden
                    $('#idcliente').val(response);
                    //Bloque campos
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');
                    $('#dir_cliente').attr('disabled','disabled');

                    //Oculta boton agregar
                    $('.btn_new_cliente').slideUp();
                    //Oculta boton guardar
                    $('#div_registro_cliente').slideUp();
                }

            },
            error: function(error) {
            }
        });
    });

    //Buscar Producto para Venta
    $('#txt_cod_producto').keyup(function(e){
        e.preventDefault();
        var barCode = $(this).val();
        var action = 'infoProductoCod';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,barCode:barCode},

            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    $('#hidCodProducto').val(info.codproducto);
                    $('#txt_descripcion').html(info.descripcion);
                    $('#txt_existencia').html(info.existencia);
                    $('#txt_cant_producto').val('1');
                    $('#txt_precio').html(info.precio);
                    $('#txt_precio_total').html(info.precio);

                    //Activar Cantidad
                    $('#txt_cant_producto').removeAttr('disabled');

                    //Mostrar botón agregar
                    $('#add_product_venta').slideDown();
                }else{
                    $('#hidCodProducto').val('');
                    $('#txt_descripcion').html('-');
                    $('#txt_existencia').html('-');
                    $('#txt_cant_producto').val('0');
                    $('#txt_precio').html('0.00');
                    $('#txt_precio_total').html('0.00');

                    //Bloquear Cantidad
                    $('#txt_cant_producto').attr('disabled','disabled');

                    //Ocultar boton agregar
                    $('#add_product_venta').slideUp();
                }
            },
            error: function(error) {
            }
        });

    });

    //Buscar Producto para Compra
    $('#txt_cod_producto_c').keyup(function(e){
        e.preventDefault();
        var barCode = $(this).val();
        var action = 'infoProductoCodCompra';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,barCode:barCode},

            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    $('#hidCodProducto').val(info.codproducto);
                    $('#txt_descripcion').html(info.descripcion);
                    $('#txt_cant_producto_c').val('1');
                    $('#txt_precio_c').val('');
                    //Activar Cantidad
                    $('#txt_cant_producto_c').removeAttr('disabled');
                    $('#txt_precio_c').removeAttr('disabled');
                    $('#txt_cant_producto_c').focus().select();

                    //Mostrar botón agregar
                    $('#add_product_venta_c').slideDown();
                }else{
                    $('#hidCodProducto').val('');
                    $('#txt_descripcion').html('-');
                    $('#txt_cant_producto_c').val('');
                    $('#txt_precio_c').val('');

                    //Bloquear Cantidad
                    $('#txt_cant_producto_c').attr('disabled','disabled');
                    $('#txt_precio_c').attr('disabled','disabled');

                    //Ocultar boton agregar
                    $('#add_product_compra').slideUp();
                }
            },
            error: function(error) {
            }
        });

    });

    //Buscar Por Nombre
    $('#txtSearchPro').keyup(function(e){
        e.preventDefault();

        var searchText = $(this).val();
        var action = 'infoProductoSearch';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,searchText:searchText},

            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    $('#tbtlProSearch').html(info);
                }else{
                    $('#tbtlProSearch').html('');
                }
            },
            error: function(error) {
            }
        });

    });
    //Buscar Por Nombre para Compra
    $('#txtSearchPro_c').keyup(function(e){
        e.preventDefault();

        var searchText = $(this).val();
        var action = 'infoProductoSearch_v';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,searchText:searchText},

            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    $('#tbtlProSearch').html(info);
                }else{
                    $('#tbtlProSearch').html('');
                }
            },
            error: function(error) {
            }
        });

    });

    //Agregar productos al detalle con enter
    $("#txt_cod_producto").keypress(function(e) {
        //mayor compatibilidad entre navegadores.
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){

            var existencia = $('#txt_existencia').html();

            //Oculta el boton agregar si la cantidad es menor que 1
            if(($('#txt_cant_producto').val() <= 0 || isNaN($(this).val())) || ($('#txt_cant_producto').val() >  existencia) ){
                alertify.alert("Error en la cantidad.", function(){
                    alertify.message('OK');
                });
            }else{
                var cantP = $('#txt_cant_producto').val();
                var idP = $('#hidCodProducto').val();
                addProductoVentaDetalle(idP,cantP);
            }

        }
    });

    //Agregar productos al detalle con enter - compra
    $("#txt_cod_producto_c").keypress(function(e) {
        //mayor compatibilidad entre navegadores.
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){

            //Oculta el boton agregar si la cantidad es menor que 1
            if(($('#txt_cant_producto_c').val() <= 0 || isNaN($(this).val())) || ($('#txt_precio_c').val() <= 0) ){
                alertify.alert("Error en la cantidad.", function(){
                    alertify.message('OK');
                });
            }else{
                var idP = $('#hidCodProducto').val();
                var cantP = $('#txt_cant_producto_c').val();
                var preP = $('#txt_precio_c').val();
                addProductoCompraDetalle(idP,cantP,preP);
            }

        }
    });

    $("#txt_cant_producto").keypress(function(e) {
        //mayor compatibilidad entre navegadores.
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){


            var existencia = parseInt($('#txt_existencia').html());

            //Oculta el boton agregar si la cantidad es menor que 1
            if(($('#txt_cant_producto').val() <= 0 || isNaN($(this).val())) || ($('#txt_cant_producto').val() >  existencia) ){
                alertify.alert("Cantidad no válida.", function(){
                    alertify.message('OK');
                });
            }else{
                var cantP = $('#txt_cant_producto').val();
                var idP = $('#hidCodProducto').val();
                addProductoVentaDetalle(idP,cantP);
            }
        }
    });

    //Cantidad producto Compra
    $("#txt_cant_producto_c").keypress(function(e) {
        //mayor compatibilidad entre navegadores.
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){

            //Oculta el boton agregar si la cantidad es menor que 1
            if(($('#txt_cant_producto_c').val() <= 0 || isNaN($(this).val())) || $('#txt_cant_producto_c').val() =='' || ($('#txt_precio_c').val() <= 0 || $('#txt_precio_c').val() == '') ){
                alertify.alert("Verifique cantidad y precio.", function(){
                    alertify.message('OK');
                });
            }else{
                var idP = $('#hidCodProducto').val();
                var cantP = $('#txt_cant_producto_c').val();
                var preP = $('#txt_precio_c').val();
                addProductoCompraDetalle(idP,cantP,preP);
            }
        }
    });
    //Precio producto Compra
    $("#txt_precio_c").keypress(function(e) {
        //mayor compatibilidad entre navegadores.
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){

            //Oculta el boton agregar si la cantidad es menor que 1
            if(($('#txt_cant_producto_c').val() <= 0 || isNaN($(this).val())) || $('#txt_cant_producto_c').val() =='' || ($('#txt_precio_c').val() <= 0 || $('#txt_precio_c').val() == '') ){
                alertify.alert("Datos guardados correctamente.", function(){
                    alertify.message('OK');
                });
            }else{
                var idP = $('#hidCodProducto').val();
                var cantP = $('#txt_cant_producto_c').val();
                var preP = $('#txt_precio_c').val();
                addProductoCompraDetalle(idP,cantP,preP);
            }
        }
    });
    //Validar Cantidad de producto antes de agregar a la venta
    $('#txt_cant_producto_c').keyup(function(e){
        e.preventDefault();

        //Oculta el boton agregar si la cantidad es menor que 1
        if($(this).val() < 1 || isNaN($(this).val()) || $(this).val() == '' || ($('#txt_precio_c').val() < 1 || isNaN($('#txt_precio_c').val()) || $('#txt_precio_c').val() == '' ) ){
            $('#add_product_compra').hide();
        }else{
            $('#add_product_compra').show();
        }

    });
    //Validar Cantidad de producto antes de agregar a la venta
    $('#txt_precio_c').keyup(function(e){
        e.preventDefault();

        //Oculta el boton agregar si la cantidad es menor que 1
        if($(this).val() < 1 || isNaN($(this).val()) || $(this).val() == '' || ($('#txt_precio_c').val() < 1 || isNaN($('#txt_precio_c').val()) || $('#txt_precio_c').val() == '' ) ){
            $('#add_product_compra').hide();
        }else{
            $('#add_product_compra').show();
        }

    });

    //Validar Cantidad de producto antes de agregar
    $('#txt_cant_producto').keyup(function(e){
        e.preventDefault();
        var precio_total = $(this).val() * $('#txt_precio').html();
        $('#txt_precio_total').html(precio_total);

        //Oculta el boton agregar si la cantidad es menor que 1
        if($(this).val() < 1 || isNaN($(this).val()) || $(this).val() == '' ){
            $('#add_product_venta').hide();
        }else{
            $('#add_product_venta').show();
        }

        var existencia = parseInt($('#txt_existencia').html());
        if($(this).val() >  existencia ){
            $('#add_product_venta').hide();
        }else{
            $('#add_product_venta').show();
        }

    });

    //Agregar producto al detalle de VENTA
    $('#add_product_venta').click(function(e){
        e.preventDefault();
        if($('#txt_cant_producto').val() > 0)
        {
            var codproducto = $('#hidCodProducto').val();
            var cantidad    = $('#txt_cant_producto').val();
            addProductoVentaDetalle(codproducto,cantidad);
        }else{
            alertify.alert("Cantidad no válida.", function(){
                alertify.message('OK');
            });
            $('#txt_cant_producto').focus();
        }
    });

    //Agregar producto al detalle de COMPRA
    $('#add_product_compra').click(function(e){
        e.preventDefault();
        if($('#txt_cant_producto_c').val() > 0 && $('#txt_precio_c').val() > 0)
        {
            var codproducto = $('#hidCodProducto').val();
            var cantidad    = $('#txt_cant_producto_c').val();
            var precio      = $('#txt_precio_c').val();
            addProductoCompraDetalle(codproducto,cantidad,precio);
        }
    });

    //Anular Venta
    $('#btn_anular_venta').click(function(e){
        e.preventDefault();

        var rows = $('#detalle_venta tr').length;
        if(rows > 0)
        {
            var action = 'anularVenta';

            $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action},
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(response)
                {
                    if(response != 'error')
                    {
                        location.reload();
                    }
                    $('.loading').hide();
                },
                error: function(error) {
                }
            });
        }else{
            alertify.alert("Atenciaón","No hay datos para anular.", function(){
                alertify.message('OK');
            });
        }

    });
    // Facturar venta
    $('#btn_facturar_venta').click(function(e){
        e.preventDefault();

        if($('#detalle_venta tr').length > 0)
        {
            var action = 'procesarPago';
            var codcliente = $('#idcliente').val();
            var tipoPago = parseInt($('#tipo_pago').val());
            var efectivo = parseFloat($('#txtPagoEfectivo').val());
            var descuento = parseFloat($('#txtDescuento').val());
            var hdTotal = parseFloat($('#txtTotal').val());
            var hddidserie = parseInt($('#hddidserie').val());
            if(isNaN(efectivo))
            {
                efectivo = 0;
            }
            if(isNaN(descuento))
            {
                descuento = 0;
            }
            if(tipoPago == 1 && efectivo < hdTotal)
            {
                alertify.alert("Error, acción invalida.", function(){
                    alertify.message('OK');
                });
                return false;
            }

            $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,codcliente:codcliente,tipoPago:tipoPago,efectivo:efectivo,descuento:descuento,idserie:hddidserie},
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(response)
                {

                    try {
                        var info = JSON.parse(response);
                        if(info.status)
                        {
                            generarTicket(info.codcliente,info.nofactura);
                            location.reload();
                        }else{
                            alertify.alert("Error",info.msg, function(){
                                alertify.message('OK');
                            });
                        }
                        $('.loading').hide();
                        return false;
                    } catch (error) {

                        alertify.alert("Error","No es posible procesar la venta, intenta de nuevo.", function(){
                            alertify.message('OK');
                        });
                        $('.loading').hide();
                    }
                },
                error: function(error) {
                }
            });

        }else{
            //console.log("No data send");
        }

    });

    //Ver factura
    $('.view_pdf').click(function(e) {
        e.preventDefault();
        var codCliente = $(this).attr('cl');
        var noFactura = $(this).attr('f');
        generarPDF(codCliente,noFactura);

    });
    //Ver Ticket
    $('.view_ticket').click(function(e) {
        e.preventDefault();
        var codCliente = $(this).attr('cl');
        var noFactura = $(this).attr('f');
        generarTicket(codCliente,noFactura);

    });
    //Ver pedido
    $('.view_pedido_pdf').click(function(e) {
        e.preventDefault();
        var pedido = $(this).attr('p');
        var key = $(this).attr('c');
        generarPedidoPDF(key,pedido);

    });
    //Enviar correo
    $('.btn_sendEmail').click(function(e) {
        e.preventDefault();
        var action = 'ajaxSendEmail';
        var codCliente = $(this).attr('cl');
        var email = $(this).attr('email');
        var noFactura = $(this).attr('f');
        var urlVenta =  base_url+'/sistema/factura/generaFactura.php?cl='+codCliente+'&f='+noFactura;

        alertify.confirm("Enviar email","Enviar documento a "+email,
        function(){
            $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,cliente:codCliente,factura:noFactura,urlVenta:urlVenta},
                beforeSend: function(){
                    $(".loading").show();
                },
                success: function(response)
                {
                    if(response == 'send')
                    {
                        alertify.success('Documento enviada...');
                    }else{
                        alertify.error('No es posible enviar el documento');
                    }
                    $(".loading").hide();
                },
                error: function(error) {
                    $(".loading").hide();
                }
            });
        },

        function(){
            alertify.error('Cancel');
        });
    });
    //Anular factura
    $('.anular_factura').click(function(e){
        e.preventDefault();

        var nofactura = $(this).attr('fac');
        var action = 'infoFactura';

        $('.alertAddProduct').html('');

        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,nofactura:nofactura},
                beforeSend: function(){
                    $(".loading").show();
                },
                success: function(response)
                {
                    if(response != 'error')
                    {
                        var info = JSON.parse(response);
                        $('.bodyModal').html('<form action="" id="form_anular_factura" name="form_anular_factura" onsubmit="event.preventDefault(); anularFactura();">'+
                            '<h1><i class="far fa-file-alt" style="font-size: 30pt;"></i> <br><br>Anular Factura</h1><br>'+
                            '<p>Realmente desea anular la factura:</p>'+
                            '<p><strong>No. '+info.nofactura+'</strong></p>'+
                            '<p><strong>Monto. '+info.totalfactura+'</strong></p>'+
                            '<p><strong>Fecha. '+info.fecha+'</strong></p>'+
                            '<input type="hidden" name="action" value="anularFactura">'+
                            '<input type="hidden" name="no_factura" id="no_factura" value="'+info.nofactura+'" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Anular</button>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }
                    $(".loading").hide();
                },
                error: function(error) {
                }
        });
    });
    //Anular pedido
    $('.estado_pedido').click(function(e){
        e.preventDefault();
        var idpedido = $(this).attr('p');
        var action = 'infoPedido';
        $('.alertAddProduct').html('');
        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,idpedido:idpedido},
                beforeSend: function(){
                    $(".loading").show();
                },
                success: function(response)
                {
                    if(response != 'error')
                    {
                        var info = JSON.parse(response);
                        $('.bodyModal').html('<form action="" id="form_anular_pedido" name="form_anular_pedido" onsubmit="event.preventDefault(); updPedido();">'+
                            '<h1>ESTADO Pedido</h1><br>'+
                            '<p>Datos pedido:</p>'+
                            '<p><strong>No. '+info.id_pedido+'</strong></p>'+
                            '<p><strong>Monto: '+info.total+'</strong></p>'+
                            '<p><strong>Fecha: '+info.fecha+'</strong></p>'+
                            '<p><strong>Estado:</strong></p><select name="selectEstado" id="selectEstado" required>'+
                                '<option value="">Seleccione...</option>'+
                                '<option value="1">Activo</option>'+
                                '<option value="2">En proceso</option>'+
                                '<option value="3">Entregado</option>'+
                                '<option value="4">Anulado</option>'+
                            '</select>'+
                            '<input type="hidden" name="action" value="anularPedido">'+
                            '<input type="hidden" name="no_pedido" id="no_pedido" value="'+info.id_pedido+'" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<button type="submit" class="btn_new"><i class="fas fa-check-circle"></i> Actualizar</button>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }
                    $(".loading").hide();
                },
                error: function(error) {
                }
        });
    });

    // ------------- Tabs
    $('.containerTabs ul li').click(function(){
        $('.containerTabs ul li').removeClass('tabSelected');
        $(this).addClass('tabSelected');

        $('.containerInfo > div').hide();
        var parent = $(this).attr('tabId');
        $('#'+parent).fadeIn();
    });

    // Change Password
    $('.newPass').keyup(function(){
        validPass();
    });

    $('#frmChangePass').submit(function(e){
        e.preventDefault();

        var passActual = $('#txtPassUser').val();
        var passNuevo  = $('#txtNewPassUser').val();
        var confirmPassNuevo = $('#txtPassConfirm').val();
        var action = "changePassword";

        if(passNuevo != confirmPassNuevo){
            $('.alertChangePass').html('<p>La nueva contraseña no coincide.</p>');
            return false;
        }
        if(passNuevo.length < 6){
            $('.alertChangePass').html('<p>La nueva contraseña debe ser de 6 caracteres como mínimo.</p>');
            return false;
        }

        $.ajax({
            url : 'ajax.php',
            type: "POST",
            async : true,
            data: {action:action,passActual:passActual,passNuevo:passNuevo},
            beforeSend: function(){
                $(".loading").show();
                $('#frmChangePass input').attr('disabled', 'disabled');
            },

            success: function(response)
            {
                var info = JSON.parse(response);

                if(info.cod == '00'){
                    $('.alertChangePass').html('<p style="color:green;">'+info.msg+'</p>');
                    $('#frmChangePass')[0].reset();
                }else{
                    $('.alertChangePass').html('<p>'+info.msg+'</p>');
                }
                $('#frmChangePass input').removeAttr('disabled');
                $('.alertChangePass').slideDown();
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
    });

    // Información del usuario
    $('.btnInfoUser').click(function(e){
        e.preventDefault();
        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idUser = arrData[1];
        var action = 'infoUsuario';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idUser:idUser},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    var btnAction;
                    var lblEstado;
                    var dpi = info.dpi;

                    if(info.estatus == 1){
                        lblEstado = 'Activo';
                    }else{
                        lblEstado = 'Inactivo';
                    }

                    $('.bodyModal').html('<form action="" id="form_activeUser" class="formModal" name="form_activeUser" onsubmit="event.preventDefault(); activeUser();">'+
                        '<h1><i class="fas fa-user" style="font-size: 45pt;"></i><br><br>Datos del usuario</h1><br>'+

                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Identificación</td><td class="textleft"><strong>'+info.dpi+'</strong></td></tr>'+
                                '<tr><td class="textright">Nombre</td><td class="textleft"><strong>'+info.nombre+'</strong></td></tr>'+
                                '<tr><td class="textright">Teléfono</td><td class="textleft"><strong>'+info.telefono+'</strong></td></tr>'+
                                '<tr><td class="textright">Correo</td><td class="textleft"><strong>'+info.correo+'</strong></td></tr>'+
                                '<tr><td class="textright">Usuario</td><td class="textleft"><strong>'+info.usuario+'</strong></td></tr>'+
                                '<tr><td class="textright">Tipo usuario</td><td class="textleft"><strong>'+info.rol+'</strong></td></tr>'+
                                '<tr><td class="textright">Estado</td><td class="textleft"><strong>'+lblEstado+'</strong></td></tr>'+
                        '</tbody></table>'+

                        '<a href="editar_usuario.php?id='+idUser+'" class="btn_new "><i class="fas fa-check-circle"></i> Editar</a>'+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });

    });

    //Form Editar Usuario
    $('.btn_updateData').click(function(){
        var action = 'infoUsuario';

        $.ajax({
            url : 'ajax.php',
            type: "POST",
            async : true,
            data: {action:action},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    $('.bodyModal').html('<form action="" id="form_updateUser" class="formModal" name="form_updateUser" onsubmit="event.preventDefault(); updateUser();">'+
                        '<input type="hidden" name="action" value="updateUser">'+
                        '<h1><i class="fas fa-user" style="font-size: 45pt;"></i><br><br>Actualizar datos</h1><br>'+
                        '<div class="alert alertForm"></div>'+
                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Identificación</td><td class="textleft"><input type="text" name="dpi" id="dpi" placeholder="Documento de Identificación" value="'+info.dpi+'" required></td></tr>'+
                                '<tr><td class="textright">Nombre</td><td class="textleft"> <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value="'+info.nombre+'" required></td></tr>'+
                                '<tr><td class="textright">Teléfono</td><td class="textleft"> <input type="text" name="telefono" id="telefono" placeholder="Número de teléfono" value="'+info.telefono+'" required> </td></tr>'+
                                '<tr><td class="textright">Correo</td><td class="textleft"> <input type="email" name="correo" id="correo" placeholder="Correo electrónico" value="'+info.correo+'" required></td></tr>'+
                                '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Guardar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
                        '</tbody></table>'+
                    '</form>');
                    modalView();
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
    });

    // Fomr Actualizar datos de la empresa
    $('#frmEmpresa').submit(function(e){
        e.preventDefault();

        var intNit         = $('#txtNit').val();
        var strNombreEmp   = $('#txtNombre').val();
        var intTelEmp      = $('#txtTelEmpresa').val();
        var strEmailEmp    = $('#txtEmailEmpresa').val();
        var strEmailRemt   = $('#txtEmailRemitente').val();
        var strDirEmp      = $('#txtDirEmpresa').val();
        var strImpuesto    = $('#txtImpuesto').val();
        var strMoneda      = $('#txtMoneda').val();
        var strSimbolo     = $('#txtSimbolo').val();
        var txtZonaHoraria = $('#txtZonaHoraria').val();
        var identificacion = $('#txtIdentificacionCliente').val();
        var idTributaria = $('#txtIdentificacionTributaria').val();

        if(intNit == '' || strNombreEmp == '' || intTelEmp == '' || strEmailEmp == '' || strEmailRemt == '' || strDirEmp == '' || strImpuesto == '' || strMoneda == '' || strSimbolo == '' || txtZonaHoraria == '' || identificacion == '' || idTributaria == ''){
            $('.alertFormEmrpresa').html('<p>Todos los campos son obligatorios.</p>');
            $('.alertFormEmrpresa').slideDown();
            return false;
        }

        if(!fntEmailValidate(strEmailEmp))
            {
                $('.alertFormEmrpresa').html('<p>El correo de la empresa no es válido.</p>');
                $('.alertFormEmrpresa').slideDown();
                return false;
            }
        if(!fntEmailValidate(strEmailRemt))
            {
                $('.alertFormEmrpresa').html('<p>El correo remitente no es válido.</p>');
                $('.alertFormEmrpresa').slideDown();
                return false;
            }

        $.ajax({
            url : 'ajax.php',
            type: "POST",
            async : true,
            data: $('#frmEmpresa').serialize(),
            beforeSend: function(){
                $(".loading").show();
                $('.alertFormEmrpresa').slideUp();
                $('.alertFormEmrpresa').html();
                $('#frmEmpresa input').attr('disabled', 'disabled');
                $('#frmEmpresa select').attr('disabled', 'disabled');
            },
            success: function(response)
            {
                if(response == 'error'){
                    $('.alertFormEmrpresa').html('<p style="color:red;">No es posible actualizar los datos.</p>');
                }else{
                    var info = JSON.parse(response);
                    if(info.cod == '00')
                    {
                        $('.alertFormEmrpresa').html('<p style="color: #23922d;">Datos actualizados correctamente.</p>');
                        $('.alertFormEmrpresa').slideDown();
                    }else{
                        $('.alertFormEmrpresa').html('<p style="color: #23922d;">No es posible actualizar los datos.</p>');
                        $('.alertFormEmrpresa').slideDown();
                    }
                    $('#frmEmpresa input').removeAttr('disabled');
                    $('#frmEmpresa select').removeAttr('disabled');
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
    });

    // Información del cliente
    $('.btnInfoCliente').click(function(e){
        e.preventDefault();
        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idCliente = arrData[1];
        var action = 'infoCliente';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idCliente:idCliente},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);

                    $('.bodyModal').html('<form action="" id="form_activeUser" class="formModal" name="form_activeUser" >'+
                        '<h1><i class="fas fa-user" style="font-size: 30pt;"></i><br><br>Datos del cliente</h1><br>'+

                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Identificación tributaria</td><td class="textleft"><strong>'+info.nit+'</strong></td></tr>'+
                                '<tr><td class="textright">Nombre</td><td class="textleft"><strong>'+info.nombre+'</strong></td></tr>'+
                                '<tr><td class="textright">Teléfono</td><td class="textleft"><strong>'+info.telefono+'</strong></td></tr>'+
                                '<tr><td class="textright">Correo</td><td class="textleft"><strong>'+info.correo+'</strong></td></tr>'+
                                '<tr><td class="textright">Dirección</td><td class="textleft"><strong>'+info.direccion+'</strong></td></tr>'+
                        '</tbody></table>'+

                        '<a href="editar_cliente.php?id='+idCliente+'" class="btn_new "><i class="fas fa-check-circle"></i> Editar</a>'+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });

    });

    // Información del proveedor
    $('.btnInfoProveedor').click(function(e){
        e.preventDefault();
        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idProveedor = arrData[1];
        var action = 'infoProveedor';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idProveedor:idProveedor},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);

                    $('.bodyModal').html('<form action="" id="form_activeUser" class="formModal" name="form_activeUser" >'+
                        '<h1><i class="fas fa-truck" style="font-size: 40pt;"></i><br><br>Datos del proveedor</h1><br>'+

                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Identificación tributaria:</td><td class="textleft"><strong>'+info.nit+'</strong></td></tr>'+
                                '<tr><td class="textright">Proveedor</td><td class="textleft"><strong>'+info.proveedor+'</strong></td></tr>'+
                                '<tr><td class="textright">Contacto</td><td class="textleft"><strong>'+info.contacto+'</strong></td></tr>'+
                                '<tr><td class="textright">Teléfono</td><td class="textleft"><strong>'+info.telefono+'</strong></td></tr>'+
                                '<tr><td class="textright">Correo</td><td class="textleft"><strong>'+info.correo+'</strong></td></tr>'+
                                '<tr><td class="textright">Dirección</td><td class="textleft"><strong>'+info.direccion+'</strong></td></tr>'+
                                '<tr><td class="textright">Fecha registro</td><td class="textleft"><strong>'+info.fecha+'</strong></td></tr>'+
                        '</tbody></table>'+

                        '<a href="editar_proveedor.php?id='+idProveedor+'" class="btn_new "><i class="fas fa-check-circle"></i> Editar</a>'+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });

    });

    //Nueva categoria
    $('.btnNewCategory').click(function(e){
        e.preventDefault();
        $('.bodyModal').html('<form action="" id="form_NewCategoria" class="formModal" name="form_NewCategoria" onsubmit="event.preventDefault(); newCategoria();">'+
            '<input type="hidden" name="action" value="newCategoria">'+
            '<h1><i class="fas fa-window-restore" style="font-size: 45pt;"></i><br><br>Crear categoría</h1><br>'+
            '<div class="alert alertForm"></div>'+
            '<table class="tblModal"><tbody>'+
                    '<tr><td class="textright">Categoría</td><td class="textleft"><input type="text" name="txtCategoria" id="txtCategoria" placeholder="Nombre de la categoría" required></td></tr>'+
                    '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcionCat" id="txtDescripcionCat" rows="5" placeholder="Descripción de la categoría" required></textarea></td></tr>'+
                    '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Guardar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
            '</tbody></table>'+
        '</form>');
        modalView();
    });

    //Editar categoria
    $('.btnEditCategoria').click(function(e){
        e.preventDefault();
        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idCategoria = arrData[1];
        var action = 'infoCategory';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idCategoria:idCategoria},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);

                    $('.bodyModal').html('<form action="" id="form_updCategoria" class="formModal" name="form_updCategoria" onsubmit="event.preventDefault(); updCategoria();">'+
                        '<input type="hidden" id="txtIdCategoria" name="txtIdCategoria" value="'+idCategoria+'">'+
                        '<input type="hidden" name="action" value="updCategoria">'+
                        '<h1 style="font-size: 18pt;"><i class="fas fa-window-restore" style="font-size: 45pt;"></i><br><br>Actualizar categoría</h1><br>'+
                        '<div class="alert alertForm"></div>'+
                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Categoría</td><td class="textleft"><input type="text" name="txtCategoria" id="txtCategoria" value="'+info.categoria+'" placeholder="Nombre de la categoría" required></td></tr>'+
                                '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcionCat" id="txtDescripcionCat" rows="5" placeholder="Descripción de la categoría" required>'+info.descripcion+'</textarea></td></tr>'+
                                '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Actualizar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
                        '</tbody></table>'+
                    '</form>');
                    modalView();
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });
    });

    //Eliminar categoria
    $('.btnDelCategoria').click(function(e) {
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idCategoria = arrData[1];
        var action = 'infoCategoryDel';

        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,idCategoria:idCategoria},
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(response)
                {

                    if(response == 'exist')
                    {
                        $('.bodyModal').html('<form action="" class="textcenter">'+
                            '<h1><i class="fas fa-cubes" style="font-size: 45pt;"></i> <br><br>Eliminar Categoría</h1><br>'+
                            '<div class="alert alertAddProduct"><p>No es posible eliminar una categoría que tiene productos asociados.</p></div>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }else if(response != 'error'){
                        var info = JSON.parse(response);
                        $('.bodyModal').html('<form action="" id="form_del_categoria" class="textcenter" name="form_del_categoria" onsubmit="event.preventDefault(); delCategoria();">'+
                            '<h1><i class="fas fa-cubes" style="font-size: 45pt;"></i> <br><br>Eliminar Categoría</h1><br>'+
                            '<h2 class="nameProducto">'+info.categoria+'</h2><br>'+
                            '<input type="hidden" name="action" value="delCategoria">'+
                            '<input type="hidden" name="categoria_id" id="categoria_id" value="'+info.idcategoria+'" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');

                        modalView();
                    }
                    $('.loading').hide();
                },
                error: function(error) {
                }
            });
    });

    //Nueva marca
    $('.btnNewMarca').click(function(e){
        e.preventDefault();
        $('.bodyModal').html('<form action="" id="form_NewMarca" class="formModal" name="form_NewMarca" onsubmit="event.preventDefault(); newMarca();">'+
            '<input type="hidden" name="action" value="newMarca">'+
            '<h1><i class="fab fa-bandcamp" style="font-size: 45pt;"></i> <br><br>Crear marca</h1><br>'+
            '<div class="alert alertForm"></div>'+
            '<table class="tblModal"><tbody>'+
                    '<tr><td class="textright">Marca</td><td class="textleft"><input type="text" name="txtMarca" id="txtMarca" placeholder="Nombre de la marca" required></td></tr>'+
                    '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcionM" id="txtDescripcionM" rows="5" placeholder="Descripción de la marca" required></textarea></td></tr>'+
                    '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Guardar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
            '</tbody></table>'+
        '</form>');
        modalView();
    });

    //Editar marca
    $('.btnEditMarca').click(function(e){
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idMarca = arrData[1];
        var action = 'infoMarca';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idMarca:idMarca},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);

                    $('.bodyModal').html('<form action="" id="form_updMarca" class="formModal" name="form_updMarca" onsubmit="event.preventDefault(); updMarca();">'+
                        '<input type="hidden" id="txtIdMarca" name="txtIdMarca" value="'+idMarca+'">'+
                        '<input type="hidden" name="action" value="updMarca">'+
                        '<h1 style="font-size: 18pt;"><i class="fab fa-bandcamp" style="font-size: 45pt;"></i><br><br>Actualizar Marca</h1><br>'+
                        '<div class="alert alertForm"></div>'+
                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Marca</td><td class="textleft"><input type="text" name="txtMarca" id="txtMarca" value="'+info.marca+'" placeholder="Nombre de la marca" required></td></tr>'+
                                '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcionM" id="txtDescripcionM" rows="5" placeholder="Descripción de la marca" required>'+info.descripcion+'</textarea></td></tr>'+
                                '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Actualizar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
                        '</tbody></table>'+
                    '</form>');
                    modalView();
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });
    });

    //Eliminar marca
    $('.btnDelMarca').click(function(e) {
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idMarca = arrData[1];
        var action = 'infoMarcaDel';

        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,idMarca:idMarca},
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(response)
                {
                    if(response == 'exist')
                    {
                        $('.bodyModal').html('<form action="" class="textcenter">'+
                            '<h1><i class="fab fa-bandcamp" style="font-size: 45pt;"></i> <br><br>Eliminar Marca</h1><br>'+
                            '<div class="alert alertAddProduct"><p>No es posible eliminar una marca que tiene productos asociados.</p></div>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }else if(response != 'error'){
                        var info = JSON.parse(response);
                        $('.bodyModal').html('<form action="" id="form_del_marca" class="textcenter" name="form_del_marca" onsubmit="event.preventDefault(); delMarca();">'+
                            '<h1><i class="fab fa-bandcamp" style="font-size: 45pt;"></i> <br><br>Eliminar Marca</h1><br>'+
                            '<h2 class="nameProducto">'+info.marca+'</h2><br>'+
                            '<input type="hidden" name="action" value="delMarca">'+
                            '<input type="hidden" name="marca_id" id="marca_id" value="'+info.idmarca+'" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');

                        modalView();
                    }
                    $('.loading').hide();
                },
                error: function(error) {
                }
            });
    });
    //Nueva ubicación
    $('.btnNewUbicacion').click(function(e){
        e.preventDefault();
        $('.bodyModal').html('<form action="" id="form_NewUbicacion" class="formModal" name="form_NewUbicacion" onsubmit="event.preventDefault(); newUbicacion();">'+
            '<input type="hidden" name="action" value="newUbicacion">'+
            '<h1><i class="fas fa-location-arrow" style="font-size: 45pt;"></i><br><br>Crear ubicación</h1><br>'+
            '<div class="alert alertForm"></div>'+
            '<table class="tblModal"><tbody>'+
                    '<tr><td class="textright">Ubicación</td><td class="textleft"><input type="text" name="txtUbicacion" id="txtUbicacion" placeholder="Nombre ubicación" required></td></tr>'+
                    '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcionU" id="txtDescripcionU" rows="5" placeholder="Descripción de ubicación" required></textarea></td></tr>'+
                    '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Guardar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
            '</tbody></table>'+
        '</form>');
        modalView();
    });
    //Editar Ubicación
    $('.btnEditUbicacion').click(function(e){
        e.preventDefault();
        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idUbicacion = arrData[1];
        var action = 'infoUbicacion';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idUbicacion:idUbicacion},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);

                    $('.bodyModal').html('<form action="" id="form_updUbicacion" class="formModal" name="form_updUbicacion" onsubmit="event.preventDefault(); updUbicacion();">'+
                        '<input type="hidden" id="txtIdUbicacion" name="txtIdUbicacion" value="'+idUbicacion+'">'+
                        '<input type="hidden" name="action" value="updUbicacion">'+
                        '<h1 style="font-size: 18pt;"><i class="fas fa-location-arrow" style="font-size: 45pt;"></i></i><br><br>Actualizar Ubicación</h1><br>'+
                        '<div class="alert alertForm"></div>'+
                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Ubicación</td><td class="textleft"><input type="text" name="txtUbicacion" id="txtUbicacion" value="'+info.ubicacion+'" placeholder="Nombre de la ubicación" required></td></tr>'+
                                '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcionU" id="txtDescripcionU" rows="5" placeholder="Descripción de la ubicación" required>'+info.descripcion+'</textarea></td></tr>'+
                                '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Actualizar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
                        '</tbody></table>'+
                    '</form>');
                    modalView();
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });
    });
    //Eliminar Ubicación
    $('.btnDelUbicacion').click(function(e) {
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idUbicacion = arrData[1];
        var action = 'infoUbicacionDel';

        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,idUbicacion:idUbicacion},
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(response)
                {
                    if(response == 'exist')
                    {
                        $('.bodyModal').html('<form action="" class="textcenter">'+
                            '<h1><i class="fab fa-bandcamp" style="font-size: 45pt;"></i> <br><br>Eliminar Ubicación</h1><br>'+
                            '<div class="alert alertAddProduct"><p>No es posible eliminar una ubicación que tiene productos asociados.</p></div>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }else if(response != 'error'){
                        var info = JSON.parse(response);
                        $('.bodyModal').html('<form action="" id="form_del_ubicacion" class="textcenter" name="form_del_ubicacion" onsubmit="event.preventDefault(); delUbicacion();">'+
                            '<h1><i class="fas fa-location-arrow" style="font-size: 45pt;"></i><br><br>Eliminar Ubicación</h1><br>'+
                            '<h2 class="nameProducto">'+info.ubicacion+'</h2><br>'+
                            '<input type="hidden" name="action" value="delUbicacion">'+
                            '<input type="hidden" name="ubicacion_id" id="ubicacion_id" value="'+info.id_ubicacion+'" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');

                        modalView();
                    }
                    $('.loading').hide();
                },
                error: function(error) {
                }
            });
    });
    //Nueva documento
    $('.btnNewDocumento').click(function(e){
        e.preventDefault();
        $('.bodyModal').html('<form action="" id="form_NewDocumento" class="formModal" name="form_NewDocumento" onsubmit="event.preventDefault(); newDocumento();">'+
            '<input type="hidden" name="action" value="newDocumento">'+
            '<h1><i class="fas fa-file-alt" style="font-size: 30pt;"></i><br><br>Nuevo Documento</h1><br>'+
            '<div class="alert alertForm"></div>'+
            '<table class="tblModal"><tbody>'+
                    '<tr><td class="textright">Documento:</td><td class="textleft"><input type="text" name="txtDocumento" id="txtDocumento" placeholder="Nombre documento" required></td></tr>'+
                    '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcion" id="txtDescripcion" rows="5" placeholder="Descripción del documento" required></textarea></td></tr>'+
                    '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Guardar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
            '</tbody></table>'+
        '</form>');
        modalView();
    });
    //Editar documento
    $('.btnEditDocumento').click(function(e){
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idDocumento = arrData[1];
        var action = 'infoDocumento';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idDocumento:idDocumento},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);

                    $('.bodyModal').html('<form action="" id="form_updDocumento" class="formModal" name="form_updDocumento" onsubmit="event.preventDefault(); updDocumento();">'+
                        '<input type="hidden" id="txtIdCategoria" name="txtIdDocumento" value="'+idDocumento+'">'+
                        '<input type="hidden" name="action" value="updDocumento">'+
                        '<h1 style="font-size: 12pt;"><i class="fas fa-window-restore" style="font-size: 30pt;"></i><br><br>Actualizar documento</h1><br>'+
                        '<div class="alert alertForm"></div>'+
                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Categoría</td><td class="textleft"><input type="text" name="txtDocumento" id="txtDocumento" value="'+info.documento+'" placeholder="Nombre de documento" required></td></tr>'+
                                '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcion" id="txtDescripcion" rows="5" placeholder="Descripción del documento" required>'+info.descripcion+'</textarea></td></tr>'+
                                '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Actualizar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
                        '</tbody></table>'+
                    '</form>');
                    modalView();
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });
    });
    //Eliminar documento
    $('.btnDelDocumento').click(function(e) {
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idDocumento = arrData[1];
        var action = 'infoDocumentoDel';

        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,idDocumento:idDocumento},
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(response)
                {
                    if(response == 'exist')
                    {
                        $('.bodyModal').html('<form action="" class="textcenter">'+
                            '<h1><i class="fas fa-file-alt" style="font-size: 30pt;"></i> <br><br>Eliminar Documento</h1><br>'+
                            '<div class="alert alertAddProduct"><p>No es posible eliminar un documento que tiene ventas o compras asociados.</p></div>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }else if(response != 'error'){
                        var info = JSON.parse(response);
                        $('.bodyModal').html('<form action="" id="form_del_documento" class="textcenter" name="form_del_documento" onsubmit="event.preventDefault(); delDocumento();">'+
                            '<h1><i class="fas fa-file-alt" style="font-size: 30pt;"></i> <br><br>Eliminar Documento</h1><br>'+
                            '<h2 class="nameProducto">'+info.documento+'</h2><br>'+
                            '<input type="hidden" name="action" value="delDocumento">'+
                            '<input type="hidden" name="documento_id" id="documento_id" value="'+info.iddocumento+'" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }
                    $('.loading').hide();
                },
                error: function(error) {
                }
            });
    });
    //Nueva Forma Pago
    $('.btnNewFormaPago').click(function(e){
        e.preventDefault();
        $('.bodyModal').html('<form action="" id="form_NewFormaPago" class="formModal" name="form_NewFormaPago" onsubmit="event.preventDefault(); newFormaPago();">'+
            '<input type="hidden" name="action" value="newFormaPago">'+
            '<h1><i class="far fa-money-bill-alt" style="font-size: 30pt;"></i><br>Nueva Forma de Pago</h1><br>'+
            '<div class="alert alertForm"></div>'+
            '<table class="tblModal"><tbody>'+
                    '<tr><td class="textright">Forma pago:</td><td class="textleft"><input type="text" name="txtFormaPago" id="txtFormaPago" placeholder="Forma de pago" required></td></tr>'+
                    '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcion" id="txtDescripcion" rows="5" placeholder="Descripción forma de pago" required></textarea></td></tr>'+
                    '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Guardar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
            '</tbody></table>'+
        '</form>');
        modalView();
    });
    //Editar Forma Pago
    $('.btnEditTipoPago').click(function(e){
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idFormaPago = arrData[1];
        var action = 'infoFormaPago';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idFormaPago:idFormaPago},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    $('.bodyModal').html('<form action="" id="form_updFormaPago" class="formModal" name="form_updFormaPago" onsubmit="event.preventDefault(); updFormaPago();">'+
                        '<input type="hidden" id="txtIdFormaPago" name="txtIdFormaPago" value="'+info.idformapago+'">'+
                        '<input type="hidden" name="action" value="updFormaPago">'+
                        '<h1 style="font-size: 12pt;"><i class="far fa-money-bill-alt" style="font-size: 30pt;"></i><br><br>Actualizar Forma Pago</h1><br>'+
                        '<div class="alert alertForm"></div>'+
                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Forma Pago</td><td class="textleft"><input type="text" name="txtFormaPago" id="txtFormaPago" value="'+info.tipo_pago+'" placeholder="Nombre forma pago" required></td></tr>'+
                                '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcion" id="txtDescripcion" rows="5" placeholder="Descripción forma pago" required>'+info.descripcion+'</textarea></td></tr>'+
                                '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Actualizar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
                        '</tbody></table>'+
                    '</form>');
                    modalView();
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });
    });
    //Eliminar Forma Pago
    $('.btnDelTipoPago').click(function(e) {
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idFormaPago = arrData[1];
        var action = 'infoTipoPagoDel';

        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,idFormaPago:idFormaPago},
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(response)
                {
                    if(response == 'exist')
                    {
                        $('.bodyModal').html('<form action="" class="textcenter">'+
                            '<h1><i class="fas fa-file-alt" style="font-size: 30pt;"></i> <br><br>Eliminar forma pago</h1><br>'+
                            '<div class="alert alertAddProduct"><p>No es posible eliminar una forma de pago que tiene ventas o compras asociados.</p></div>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }else if(response != 'error'){
                        var info = JSON.parse(response);
                        $('.bodyModal').html('<form action="" id="form_del_formapago" class="textcenter" name="form_del_formapago" onsubmit="event.preventDefault(); delFormaPago();">'+
                            '<h1><i class="fas fa-file-alt" style="font-size: 30pt;"></i> <br><br>Eliminar forma pago</h1><br>'+
                            '<h2 class="nameFormaPago">'+info.tipo_pago+'</h2><br>'+
                            '<input type="hidden" name="action" value="delFormaPago">'+
                            '<input type="hidden" name="formapago_id" id="formapago_id" value="'+info.id_tipopago+'" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }
                    $('.loading').hide();
                },
                error: function(error) {
                }
            });
    });
    //Nueva Impuesto
    $('.btnNewImpuesto').click(function(e){
        e.preventDefault();
        $('.bodyModal').html('<form action="" id="form_NewImpuesto" class="formModal" name="form_NewImpuesto" onsubmit="event.preventDefault(); newImpuesto();">'+
            '<input type="hidden" name="action" value="newImpuesto">'+
            '<h1>Nuevo Impuesto</h1><br>'+
            '<div class="alert alertForm"></div>'+
            '<table class="tblModal"><tbody>'+
                    '<tr><td class="textright">Impuesto:</td><td class="textleft"><input type="text" name="txtImpuesto" id="txtImpuesto" placeholder="Eje. 12" required></td></tr>'+
                    '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcion" id="txtDescripcion" rows="5" placeholder="Descripción impuesto" required></textarea></td></tr>'+
                    '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Guardar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
            '</tbody></table>'+
        '</form>');
        modalView();
    });
    //Editar Impuesto
    $('.btnEditImpuesto').click(function(e){
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idImpuesto = arrData[1];
        var action = 'infoImpuesto';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idImpuesto:idImpuesto},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    $('.bodyModal').html('<form action="" id="form_updImpuesto" class="formModal" name="form_updImpuesto" onsubmit="event.preventDefault(); updImpuesto();">'+
                        '<input type="hidden" id="txtIdImpuesto" name="txtIdImpuesto" value="'+info.idimpuesto+'">'+
                        '<input type="hidden" name="action" value="updImpuesto">'+
                        '<h1 style="font-size: 12pt;"></i><br><br>Actualizar Impuesto</h1><br>'+
                        '<div class="alert alertForm"></div>'+
                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Impuesto:</td><td class="textleft"><input type="text" name="txtImpuesto" id="txtImpuesto" value="'+info.impuesto+'" placeholder="Eje. 12" required></td></tr>'+
                                '<tr><td class="textright">Descripción:</td><td class="textleft"> <textarea name="txtDescripcion" id="txtDescripcion" rows="5" placeholder="Descripción impuesto" required>'+info.descripcion+'</textarea></td></tr>'+
                                '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Actualizar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
                        '</tbody></table>'+
                    '</form>');
                    modalView();
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });
    });
    //Eliminar Impuesto
    $('.btnDelImpuesto').click(function(e) {
        e.preventDefault();
        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idImpuesto = arrData[1];
        var action = 'infoImpuestoDel';
        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,idImpuesto:idImpuesto},
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(response)
                {
                    if(response == 'exist')
                    {
                        $('.bodyModal').html('<form action="" class="textcenter">'+
                            '<h1>Eliminar impuesto</h1><br>'+
                            '<div class="alert alertAddProduct"><p>No es posible eliminar un impuesto que tiene ventas o compras asociado.</p></div>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }else if(response != 'error'){
                        var info = JSON.parse(response);
                        $('.bodyModal').html('<form action="" id="form_del_impuesto" class="textcenter" name="form_del_impuesto" onsubmit="event.preventDefault(); delImpuesto();">'+
                            '<h1>Eliminar forma pago</h1><br>'+
                            '<h2 class="nameImpuesto">'+info.impuesto+'<br>'+info.descripcion+'</h2><br>'+
                            '<input type="hidden" name="action" value="delImpuesto">'+
                            '<input type="hidden" name="idimpuesto" id="idimpuesto" value="'+info.idimpuesto+'" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }
                    $('.loading').hide();
                },
                error: function(error) {
                }
            });
    });
    //Nueva Serie Factura
    $('.btnNewSerie').click(function(e){
        e.preventDefault();
        $('.bodyModal').html('<form action="" id="form_NewSerie" class="formModal" name="form_NewSerie" onsubmit="event.preventDefault(); newSerie();">'+
            '<input type="hidden" name="action" value="newSerie">'+
            '<h1><i class="fas fa-file-alt" style="font-size: 30pt;"></i><br><br>Rango facturación</h1><br>'+
            '<div class="alert alertForm"></div>'+
            '<table class="tblModal"><tbody>'+
                '<tr><td class="textright">CAI:</td><td class="textleft"><input type="text" name="txtCai" id="txtCai" placeholder="CAI" required></td></tr>'+
                '<tr><td class="textright">Prefijo Factura:</td><td class="textleft"><input type="text" name="txtPrefijoFactura" id="txtPrefijoFactura" placeholder="Prefijo facturación" required> </td></tr>'+
                '<tr><td class="textright">Periodo Inicio:</td><td class="textleft"><input type="date" name="txtPeriodoInicio" id="txtPeriodoInicio" placeholder="Fecha inicio facturación" required> </td></tr>'+
                '<tr><td class="textright">Periodo Fin:</td><td class="textleft"><input type="date" name="txtPeriodoFin" id="txtPeriodoFin" placeholder="Fecha fin facturación" required> </td></tr>'+
                '<tr><td class="textright">Rango facturación:</td><td class="textleft"><input type="text" name="txtRango" id="txtRango" placeholder="Eje. 1-100" required> </td></tr>'+
                '<tr><td class="textright">No. Ceros:</td><td class="textleft"><input type="text" name="txtCeros" id="txtCeros" value="0" placeholder="No. ceros" required> </td></tr>'+
                '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Guardar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
            '</tbody></table>'+
        '</form>');
        modalView();
    });

    //Editar Series Factura
    $('.btnEditSerie').click(function(e){
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idSerie = arrData[1];
        var action = 'infoSerie';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idSerie:idSerie},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    $('.bodyModal').html('<form action="" id="form_updSerie" class="formModal" name="form_updSerie" onsubmit="event.preventDefault(); updSerie();">'+
                        '<input type="hidden" id="txtIdCategoria" name="txtIdSerie" value="'+idSerie+'">'+
                        '<input type="hidden" name="action" value="updSerie">'+
                        '<h1 style="font-size: 12pt;"><br>Actualizar Rango facturación</h1><br>'+
                        '<div class="alert alertForm"></div>'+
                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">CAI:</td><td class="textleft"><input type="text" name="txtCai" id="txtCai" value="'+info.cai+'" placeholder="CAI" required></td></tr>'+
                                '<tr><td class="textright">Prefijo Factura:</td><td class="textleft"><input type="text" name="txtPrefijoFactura" id="txtPrefijoFactura" value="'+info.prefijo+'" placeholder="Prefijo facturación" required> </td></tr>'+
                                '<tr><td class="textright">Periodo Inicio:</td><td class="textleft"><input type="date" name="txtPeriodoInicio" id="txtPeriodoInicio" value="'+info.periodo_inicio+'" placeholder="Fecha inicio facturación" required> </td></tr>'+
                                '<tr><td class="textright">Periodo Fin:</td><td class="textleft"><input type="date" name="txtPeriodoFin" id="txtPeriodoFin" value="'+info.periodo_fin+'" placeholder="Fecha fin facturación" required> </td></tr>'+
                                '<tr><td class="textright">Rango facturación:</td><td class="textleft"><input type="text" name="txtRango" id="txtRango" value="'+info.no_inicio+'-'+info.no_fin+'" placeholder="Eje. 1-100" required> </td></tr>'+
                                '<tr><td class="textright">No. Ceros:</td><td class="textleft"><input type="text" name="txtCeros" id="txtCeros" value="'+info.ceros+'" placeholder="No. ceros" required> </td></tr>'+
                                '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Actualizar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
                        '</tbody></table>'+
                    '</form>');
                    modalView();
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });
    });
    //Eliminar Serie
    $('.btnDelSerie').click(function(e) {
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idSerie = arrData[1];
        var action = 'infoSerieDel';

        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,idSerie:idSerie},
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(response)
                {
                    if(response == 'exist')
                    {
                        $('.bodyModal').html('<form action="" class="textcenter">'+
                            '<h1><i class="fas fa-file-alt" style="font-size: 30pt;"></i> <br><br>Eliminar rango facturación</h1><br>'+
                            '<div class="alert alertAddProduct"><p>No es posible eliminar el rango de facturación que tiene facturas asociadas.</p></div>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }else if(response != 'error'){
                        var info = JSON.parse(response);
                        $('.bodyModal').html('<form action="" id="form_del_serie" class="textcenter" name="form_del_serie" onsubmit="event.preventDefault(); delSerie();">'+
                            '<h1><i class="fas fa-file-alt" style="font-size: 30pt;"></i> <br><br>Eliminar rango facturación</h1><br>'+
                            '<h2 class="namePeriodo">Del '+info.fecha_inicio+' al '+info.fecha_fin+'</h2><br>'+
                            '<h2 class="nameRango">Rango del '+info.no_inicio+' al '+info.no_fin+'</h2><br>'+
                            '<input type="hidden" name="action" value="delSerie">'+
                            '<input type="hidden" name="serie_id" id="serie_id" value="'+info.idserie+'" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }
                    $('.loading').hide();
                },
                error: function(error) {
                }
            });
    });

    //Nueva presentacion
    $('.btnNewPresentacion').click(function(e){
        e.preventDefault();
        $('.bodyModal').html('<form action="" id="form_NewPresentacion" class="formModal" name="form_NewPresentacion" onsubmit="event.preventDefault(); newPresentacion();">'+
            '<input type="hidden" name="action" value="newPresentacion">'+
            '<h1><i class="fas fa-file-alt" style="font-size: 45pt;"></i><br><br>Nuevo Presentación</h1><br>'+
            '<div class="alert alertForm"></div>'+
            '<table class="tblModal"><tbody>'+
                    '<tr><td class="textright">Presentación:</td><td class="textleft"><input type="text" name="txtPresentacion" id="txtPresentacion" placeholder="Presentación del producto" required></td></tr>'+
                    '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcion" id="txtDescripcion" rows="5" placeholder="Descripción de presentación" required></textarea></td></tr>'+
                    '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Guardar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
            '</tbody></table>'+
        '</form>');
        modalView();
    });

    //Modal Editar documento
    $('.btnEditPresentacion').click(function(e){
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idPresentacion = arrData[1];
        var action = 'infoPresentacion';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idPresentacion:idPresentacion},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    $('.bodyModal').html('<form action="" id="form_updPresentacion" class="formModal" name="form_updPresentacion" onsubmit="event.preventDefault(); updPresentacion();">'+
                        '<input type="hidden" id="txtIdPresentacion" name="txtIdPresentacion" value="'+idPresentacion+'">'+
                        '<input type="hidden" name="action" value="updPresentacion">'+
                        '<h1 style="font-size: 18pt;"><i class="fas fa-window-restore" style="font-size: 45pt;"></i><br><br>Actualizar presentacion</h1><br>'+
                        '<div class="alert alertForm"></div>'+
                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Categoría</td><td class="textleft"><input type="text" name="txtPresentacion" id="txtPresentacion" value="'+info.presentacion+'" placeholder="Presentación del producto" required></td></tr>'+
                                '<tr><td class="textright">Descripción</td><td class="textleft"> <textarea name="txtDescripcion" id="txtDescripcion" rows="5" placeholder="Descripción de presentacion" required>'+info.descripcion+'</textarea></td></tr>'+
                                '<tr><td class="textright"><button type="submit" class="btn_new"><i class="far fa-save fa-lg"></i><br>Actualizar</button></td><td class="textleft"><button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i><br>Cerrar</button></td></tr>'+
                        '</tbody></table>'+
                    '</form>');
                    modalView();
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
    });

    //Eliminar presentacion
    $('.btnDelPresentacion').click(function(e) {
        e.preventDefault();

        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idPresentacion = arrData[1];
        var action = 'infoPresentacionDel';

        $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,idPresentacion:idPresentacion},
                beforeSend: function(){
                    $(".loading").show();
                },
                success: function(response)
                {
                    if(response == 'exist')
                    {
                        $('.bodyModal').html('<form action="" class="textcenter">'+
                            '<h1><i class="fas fa-file-alt" style="font-size: 45pt;"></i> <br><br>Eliminar Presentacion</h1><br>'+
                            '<div class="alert alertAddProduct"><p>No es posible eliminar una presentación que tiene productos asociados.</p></div>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }else if(response != 'error'){
                        var info = JSON.parse(response);
                        $('.bodyModal').html('<form action="" id="form_del_presentacion" class="textcenter" name="form_del_presentacion" onsubmit="event.preventDefault(); delPresentacion();">'+
                            '<h1><i class="fas fa-file-alt" style="font-size: 45pt;"></i> <br><br>Eliminar Presentacion</h1><br>'+
                            '<h2 class="namePresentacion">'+info.presentacion+'</h2><br>'+
                            '<p class="nameDescripcion">'+info.descripcion+'</p><br>'+
                            '<input type="hidden" name="action" value="delPresentacion">'+
                            '<input type="hidden" name="presentacion_id" id="presentacion_id" value="'+info.idpresentacion+'" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>'+
                            '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</button>'+
                        '</form>');
                        modalView();
                    }
                    $(".loading").hide();
                },
                error: function(error) {
                }
            });
    });


    // Información del Producto
    $('.btnInfoProducto').click(function(e){
        e.preventDefault();
        var idRow = $(this).parent().parent().parent().parent().attr('id');
        var arrData =  idRow.split('_');
        var idProducto = arrData[1];
        var action = 'infoProducto';

        $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,producto:idProducto},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);

                    $('.bodyModal').html('<form action="" id="form_activeUser" class="formModal" name="form_activeUser" >'+
                        '<h1>'+info.producto+'</h1><br>'+
                        '<table class="tblModal"><tbody>'+
                                '<tr><td class="textright">Código:</td><td class="textleft"><strong>'+info.codebar+'</strong></td></tr>'+
                                '<tr><td class="textright">Descripción:</td><td class="textleft"><strong>'+info.descripcion+'</strong></td></tr>'+
                                '<tr><td class="textright">Precio:</td><td class="textleft"><strong>'+info.precio+'</strong></td></tr>'+
                                '<tr><td class="textright">Existancia:</td><td class="textleft"><strong>'+info.existencia+'</strong></td></tr>'+
                                '<tr><td class="textright">Existencia mínima:</td><td class="textleft"><strong>'+info.existencia_minima+'</strong></td></tr>'+
                        '<tr><td class="textright">Marca:</td><td class="textleft"><strong>'+info.marca+'</strong></td></tr>'+
                        '<tr><td class="textright">Presentación:</td><td class="textleft"><strong>'+info.presentacion+'</strong></td></tr>'+
                        '<tr><td class="textright">Categoría:</td><td class="textleft"><strong>'+info.categoria+'</strong></td></tr>'+
                        '<tr><td class="textright">Estado:</td><td class="textleft"><strong>'+info.estado+'</strong></td></tr>'+
                        '<tr><td class="textright">Fecha regsitro:</td><td class="textleft"><strong>'+info.fecha+'</strong></td></tr>'+
                        '<tr><td class="textcenter" colspan="2"><img width="200" src="'+base_url+'/sistema/'+info.foto+'" alt="'+info.descripcion+'"/>'+
                        '<img src="'+base_url+'/sistema/library/barcode/barcode.php?text='+info.codebar+'&size=50&orientation=horizontal&codetype=Code128&print=true&sizefactor=1">'+
                        '</td></tr>'+
                        '</tbody></table>'+

                        '<a href="editar_producto.php?id='+info.codproducto+'" class="btn_new "><i class="fas fa-check-circle"></i> Editar</a>'+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });

    });

    //Tipo de pago
    $('#tipo_pago').change(function(e){
        e.preventDefault();

        var efectivoPago = parseFloat($('#txtPagoEfectivo').val());
        var hidTotalPagar    = parseFloat($('#hidTotalPagar').val());

        if($(this).val() == 2)
        {
            $('#txtPagoEfectivo').val('');
            $('.spnCambio').val('');
            $('.divEfectivo').slideUp();
            if($('#detalle_venta tr').length > 0)
            {
                $('#btn_facturar_venta').show();
            }else{
                $('#btn_facturar_venta').hide();
            }

            //Mostrar / Ocultar cambio
            $('.divCambio').hide();
            $('.spnCambio').html('');
        }

        if($(this).val() == 1)
        {
            $('.divEfectivo').slideDown();
            if(efectivoPago > hidTotalPagar && hidTotalPagar != '' ){
                $('#btn_facturar_venta').show();
            }else{
                $('#btn_facturar_venta').hide();
            }

            //Mostrar / Ocultar cambio
            $('.divCambio').show();
            $('.spnCambio').html('');
        }
    });

    $('#txtPagoEfectivo').keyup(function(e){
        e.preventDefault();
        var cantEfectivo = parseFloat($('#txtPagoEfectivo').val());
        var cnatAPagar = parseFloat($('#txtTotal').val());
        var cambio = cantEfectivo - cnatAPagar;
        cambio = cambio.toFixed(2);
        $('.spnCambio').html(cambio);
        if(cantEfectivo >= cnatAPagar){
            $('#btn_facturar_venta').show();
            $('.divCambio').show();
        }else{
            $('.spnCambio').html('0');
            $('.divCambio').hide();
            $('#btn_facturar_venta').hide();
        }
    });

    $('#txtDescuento').keyup(function(e){
        e.preventDefault();

        if($('#txtDescuento').val() == "")
        {
            var cantDescuento = 0;
        }else{
            var cantDescuento = parseFloat($('#txtDescuento').val());
        }
        var cantEfectivo = parseFloat($('#txtPagoEfectivo').val());
        var cnatAPagar = parseFloat($('#hidTotalPagar').val());
        var totalDescuento = cnatAPagar - cantDescuento;
        //cambio = cambio.toFixed(2);
        $('#txtTotal').val(totalDescuento);
        $('#importeTotal').html(totalDescuento);

        cantDescuento = cantDescuento.toFixed(2);
        if(cantDescuento < $('#txtTotal').val() || $('#txtTotal').val() < cantEfectivo){
            $('#btn_facturar_venta').show();
            $('.divCambio').show();
        }else{
            $('.spnCambio').html('0');
            $('.divCambio').hide();
            $('#btn_facturar_venta').hide();
        }
    });


    // PROCESAR COMPRA
    $('#btn_facturar_compra').click(function(e){
        e.preventDefault();

        var hoy = new Date();
        var fechaFormulario = new Date($('#fecha_compra').val());

        if($('#no_documento').val() == '' || $('#fecha_compra').val() == '' || $('#proveedor_id').val() == '' )
        {
            alertify.alert("Atenciaón","Los datos de la compra no son válidos.", function(){
                alertify.message('OK');
            });
            return false;
        }

        if( fechaFormulario > hoy ){

            alertify.alert("Atenciaón","La fecha no es correcta.", function(){
                alertify.message('OK');
            });
            return false;
        }

        if($('#detalle_venta tr').length > 0)
        {
            var action = 'procesarCompra';
            var tipoPago = parseInt($('#tipopago_id').val());
            var documento = $('#documento_id').val();
            var noDocumento = $('#no_documento').val();
            var serie = $('#serie').val();
            var fechaCompra = $('#fecha_compra').val();
            var proveedor = $('#proveedor_id').val();

            $.ajax({
                url : '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,tipoPago:tipoPago,documento:documento,noDocumento:noDocumento,serie:serie,fechaCompra:fechaCompra,proveedor:proveedor},
                beforeSend: function(){
                    $(".loading").show();
                },
                success: function(response)
                {
                    if(response != 'error')
                    {
                        var info = JSON.parse(response);
                        if(info.status)
                        {
                            location.reload();
                            alertify.alert("Datos guardados correctamente.", function(){
                                alertify.message('OK');
                            });
                            viewCompra(info.compra_id);
                        }else{
                            alertify.alert("Error","No es posible realizar el proceso.", function(){
                                alertify.message('OK');
                            });
                        }
                        return false;
                    }else{
                        console.log('no data');
                    }
                     $(".loading").hide();
                },
                error: function(error) {
                    $(".loading").hide();
                }
            });

        }else{
            alertify.alert("Atenciaón","Debe agregar productos al detalle.", function(){
                alertify.message('OK');
            });
        }
    });

    $('.btnVendedoresMes').click(function(event) {
        if($('.vendedoresMes').val() != '')
        {
            var action = 'vendedoresMes';
            var fecha = $('.vendedoresMes').val();
            $.ajax({
                url : 'ajax.php',
                type: "POST",
                async : true,
                data: {action:action,fecha:fecha},
                beforeSend: function(){
                    $(".loading").show();
                },
                success: function(response)
                {
                    if(response != 'error')
                    {
                        var info = JSON.parse(response);
                        $('#tblEmpreadosMes').html(info);
                    }else{
                        console.log('no data');
                    }
                    $(".loading").hide();
                },
                error: function(error) {
                    $(".loading").hide();
                }
            });
        }
    });
    //Ventas del mes por dia
    $('.btnVentasMes').click(function(event) {
        if($('.ventasMes').val() != '')
        {
            var action = 'ventasMes';
            var fecha = $('.ventasMes').val();
            $.ajax({
                url : 'ajax.php',
                type: "POST",
                async : true,
                data: {action:action,fecha:fecha},
                beforeSend: function(){
                    $(".loading").show();
                },
                success: function(response)
                {
                    $('#graficaMes').html(response);
                    $(".loading").hide();
                },
                error: function(error) {
                    $(".loading").hide();
                }
            });
        }
    });
    //Ventas del año por mes
    $('.btnVentasAnio').click(function(event) {
        if($('.ventasAnio').val() != '')
        {
            if($('.ventasAnio').val().length == 4){
                var action = 'ventasAnio';
                var anio = $('.ventasAnio').val();
                $.ajax({
                    url : 'ajax.php',
                    type: "POST",
                    async : true,
                    data: {action:action,anio:anio},
                    beforeSend: function(){
                        $(".loading").show();
                    },
                    success: function(response)
                    {
                        $('#graficaAnio').html(response);
                        $(".loading").hide();
                    },
                    error: function(error) {
                        $(".loading").hide();
                    }
                });
            }else{
                alertify.alert("El año no es válido.", function(){
                    alertify.message('OK');
                });
            }
        }
    });

    //Grafica Compra y ventas del año por mes
    $('.btnCompraVenta').click(function(event) {
        var anio = '';
        var obj = '';
        var processAjax = false;
        var action = 'grCompraVentaAnio';
        if($(this).attr('obj') == "tabla")
        {
            obj = 'tabla';
            if($('#tblIEAnio').val() != '' && $('#tblIEAnio').val().length == 4)
            {
                anio = $('#tblIEAnio').val();
                processAjax = true;
            }
        }else{
            obj = 'grafica';
            if($('#grIEAnio').val() != '' && $('#grIEAnio').val().length == 4)
            {
                anio = $('#grIEAnio').val();
                processAjax = true;
            }
        }
        if(processAjax)
        {
            $.ajax({
                url : 'ajax.php',
                type: "POST",
                async : true,
                data: {action:action,anio:anio,objeto:obj},
                beforeSend: function(){
                    $(".loading").show();
                },
                success: function(response)
                {
                    if(obj == 'tabla')
                    {
                        $('#tblIngresosEgresos').html(response);
                    }else{
                        $('#graficaIngresosEgresos').html(response);
                    }
                    $(".loading").hide();
                },
                error: function(error) {
                }
            });
        }else{
            alertify.alert("El año no es válido.", function(){
                alertify.message('OK');
            });
        }
    });
}); //End Ready

//Ver compra
function viewCompra(idcompra){
    var ancho = 930;
    var alto = 600;
    //Calcular posicion x,y para centrar la ventana
    var x = parseInt((window.screen.width/2) - (ancho / 2));
    var y = parseInt((window.screen.height/2) - (alto / 2));

    $url = base_url+'/sistema/compras/ver_compra.php?cmp='+idcompra;
    window.open($url,"Compra","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");
}
function modalView()
{
    $('.modal').fadeIn();
    $('body').css({'overflow':'hidden'})
}

function cantProductoSearch(id){

    var cantProSR = $('#prodSrcAll_'+id+' .txtCantProd').val();
    var existencia = parseInt($('#prodSrcAll_'+id+' .existSR').html());

    if((cantProSR > existencia) || (cantProSR == '') || (cantProSR < 0) || (isNaN(cantProSR)) ){
        $('#prodSrcAll_'+id+' .carAdd').hide();
    }else{
        $('#prodSrcAll_'+id+' .carAdd').show();
    }
}
//Valida si hay cantidad y precio para la compra
function fntActionPress(id){

    var cantProSR = $('#prodSrcAll_'+id+' .txtCantProd_c').val();
    var preProSR = $('#prodSrcAll_'+id+' .txtPreProd_c').val();

     //Oculta el boton agregar si la cantidad es menor que 1
    if(cantProSR <= 0 || isNaN(cantProSR) || cantProSR == '' || (preProSR <= 0 || isNaN(preProSR) || preProSR == '' ) ){
        $('#prodSrcAll_'+id+' .carAdd').hide();
    }else{
        $('#prodSrcAll_'+id+' .carAdd').show();
    }
}
function addProductVenta(id){
    var cantProAdd = $('#prodSrcAll_'+id+' .txtCantProd').val();
    var existencia = parseInt($('#prodSrcAll_'+id+' .existSR').html());
    if(existencia > cantProAdd || cantProAdd != '' ){
        addProductoVentaDetalle(id,cantProAdd);
    }
}

function addProductoVentaDetalle(idProducto,cantidad){

    var action = 'addProductoDetalle';
    $.ajax({
        url : '../ajax.php',
        type: "POST",
        async : true,
        data: {action:action,producto:idProducto,cantidad:cantidad},
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response == 'error' || response == 'errorCantidad')
            {
                alertify.alert("Atención","No es posible agregar el producto, verifique cantidad existente.", function(){
                    alertify.message('OK');
                });

            }else{
                var info = JSON.parse(response);
                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);
                $('#txtTotal').val(info.total);                

                //Limpiar datos del producto agregado y ocultar boton agregar
                $('#txt_cod_producto').val('');
                $('#add_product_venta').val('');
                $('#txt_descripcion').html('-');
                $('#txt_existencia').html('-');
                $('#txt_cant_producto').val('0');
                $('#txt_precio').html('0');
                $('#txt_precio_total').html('0');

                //Bloquear Cantidad
                $('#txt_cant_producto').attr('disabled','disabled');

                //Ocultar boton agregar
                $('#add_product_venta').slideUp();

                //Mostrar / Ocultar Botón Procesar
                if($('#tipo_pago').val() == 1)
                {
                    if(parseFloat($('#txtPagoEfectivo').val()) >= parseFloat($('#hidTotalPagar').val())){
                        $('#btn_facturar_venta').show();
                    }else{
                        $('#btn_facturar_venta').hide();
                    }
                }
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
//COMPRAR PRODUCTO
function addProductCompra(id){
    var cantProAdd = $('#prodSrcAll_'+id+' .txtCantProd_c').val();
    var preProd = $('#prodSrcAll_'+id+' .txtPreProd_c').val();
    if((cantProAdd > 0 && cantProAdd != '' && !isNaN(cantProAdd) ) && (preProd > 0 && preProd != '' && !isNaN(preProd)) ){
        addProductoCompraDetalle(id,cantProAdd,preProd);
    }else{
        alertify.alert("Verifique precio y cantidad.", function(){
            alertify.message('OK');
        });
    }
}
function addProductoCompraDetalle(idProducto,cantidad,precio){

    var action = 'addProductoDetalleCompra';
    $.ajax({
        url : '../ajax.php',
        type: "POST",
        async : true,
        data: {action:action,producto:idProducto,cantidad:cantidad,precio:precio},
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response != 'error')
            {
                var info = JSON.parse(response);
                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);

                //Limpiar datos del producto agregado y ocultar boton agregar
                $('#txt_cod_producto_c').val('');
                $('#hidCodProducto').val('');
                $('#txt_descripcion').html('-');
                $('#txt_cant_producto_c').val('0');
                $('#txt_precio_c').html('0.00');

                //Bloquear Cantidad
                $('#txt_cant_producto_c').attr('disabled','disabled');
                $('#txt_precio_c').attr('disabled','disabled');

                //Ocultar boton agregar
                $('#add_product_compra').slideUp();

                //Mostrar / Ocultar Botón Procesar
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

// Nueva presentación
function newPresentacion(){
    var strPresentacion = $('#txtPresentacion').val();
    var strDesc      = $('#txtDescripcion').val();

    $('.alertForm').hide();

    if(strDesc == '' || strPresentacion == '' )
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        $('.alertForm').slideDown();
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_NewPresentacion").serialize(),

        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_NewPresentacion input').attr('disabled', 'disabled');
            $('#form_NewPresentacion textarea').attr('disabled', 'disabled');
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible guardar el documento.</p>');
            }else{
                    $('.alertForm').html('<p>Presentación guardada correctamente.</p>');
                    $('.alertForm').slideDown();
                    $('#form_NewPresentacion')[0].reset();
                    $('#txtDescripcion').focus();
                    $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_NewPresentacion input').removeAttr('disabled');
            $('#form_NewPresentacion textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

// Update presentacion
function updPresentacion(){
    var strPresentacion = $('#txtPresentacion').val();
    var strDescripcion  = $('#txtDescripcion').val();
    var intIdPresentacion = $('#txtIdPresentacion').val();

    if(strPresentacion == '' || strDescripcion == '' || intIdPresentacion == '')
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_updPresentacion").serialize(),

        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_updPresentacion input').attr('disabled', 'disabled');
            $('#form_updPresentacion textarea').attr('disabled', 'disabled');
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible guardar los cambios.</p>');
            }else{
                $('.alertForm').html('<p>Datos actualizada correctamente.</p>');
                $('#item_'+intIdPresentacion+' .rowPresentaion').html($('#txtPresentacion').val());
                $('#item_'+intIdPresentacion+' .rowDescripcion').html($('#txtDescripcion').val());
                $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_updPresentacion input').removeAttr('disabled');
            $('#form_updPresentacion textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Eliminar presentacion
function delPresentacion(){

    var pre = $('#presentacion_id').val();
    $('.alertAddProduct').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_del_presentacion").serialize(),

        beforeSend: function(){
            $(".loading").hide();
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color:red;">Error al eliminar la presentaión.</p>');

            }else{
                $('#item_'+pre).remove();
                $('#form_del_presentacion .btn_ok').remove();
                $('.alertAddProduct').html('<p>Presentación eliminada.</p>');
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

//Modal Activar | Desactivar Presentación
function modalActivePresentacion(id){
    var intEstado  = $('#item'+id+' estado span').attr('estado');
    var idPresentacion= id;
    var action     = 'infoPreentacion';

    $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idPresentacion:idPresentacion},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    var btnAction;
                    var lblEstado;
                    if(info.estatus == 1){
                        btnAction = '<button type="submit" class="btn_ok btnActionForm"><i class="fas fa-ban"></i> Desactivar</button>';
                        lblEstado = 'Activo';
                    }else{
                        btnAction = '<button type="submit" class="btn_new btnActionForm"><i class="fas fa-check-circle"></i> Activar</button>';
                        lblEstado = 'Inactivo';
                    }

                    $('.bodyModal').html('<form action="" id="form_activePresentacion" class="formModal" name="form_activePresentacion" onsubmit="event.preventDefault(); activePresentacion();">'+
                        '<h1><i class="fas fa-file-alt" style="font-size: 45pt;"></i> <br><br>Estado de presentacion</h1><br>'+
                        '<p>Nombre: <strong>'+info.presentacion+'</strong></p>'+
                        '<p>Estado: <strong>'+lblEstado+'</strong></p>'+
                        '<input type="hidden" name="action" id="action" value="changeEstadoPresentacion" required>'+
                        '<input type="hidden" name="idPresentacion" id="idPresentacion" value="'+info.idPresentacion+'" required>'+
                        '<div class="alert alertForm"></div>'+
                        btnAction+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
}

//Active Active | Inactive presentacion
function activePresentacion(){
    var idPresentacion = $('#idPresentacion').val();
    var msgAlert;
    var btnEstado;
    $('.alertForm').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_activePresentacion").serialize(),
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible cambiar el estado.</p>');
            }else{

                if(response == 1)
                {
                    btnEstado = '<span title="Desactivar presentación" class="pagada activeUser" estado="1" onclick="modalActivePresentacion('+idPresentacion+');">Activo</span>';
                    msgAlert = '<p>Documento Activada</p>';
                }else{
                    btnEstado = '<span title="Activar presentación" class="anulada activeUser" estado="0" onclick="modalActivePresentacion('+idPresentacion+');">Inactivo</span>';
                    msgAlert = '<p>Documento Desactivada</p>';
                }

                $('#item_'+idPresentacion+' .rowEstado').html(btnEstado);
                $('#form_activePresentacion .btnActionForm').remove();
                $('.alertForm').html(msgAlert);
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

// Nuevo documento
function newDocumento(){
    var strDocumento = $('#txtDocumento').val();
    var strDesc      = $('#txtDescripcion').val();

    $('.alertForm').hide();

    if(strDesc == '' || strDocumento == '' )
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        $('.alertForm').slideDown();
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_NewDocumento").serialize(),

        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_NewDocumento input').attr('disabled', 'disabled');
            $('#form_NewDocumento textarea').attr('disabled', 'disabled');
        },
        success: function(response)
        {
            //console.log(response);
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible guardar el documento.</p>');
            }else{
                    $('.alertForm').html('<p>Documento guardado correctamente.</p>');
                    $('.alertForm').slideDown();
                    $('#form_NewDocumento')[0].reset();
                    $('#txtDescripcion').focus();
                    $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_NewDocumento input').removeAttr('disabled');
            $('#form_NewDocumento textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Update documento
function updDocumento(){
    var strDocumento    = $('#txtDocumento').val();
    var strDescripcion  = $('#txtDescripcion').val();
    var intIdDoc        = $('#txtIdDocumento').val();

    if(strDocumento == '' || strDescripcion == '' || intIdDoc == '')
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_updDocumento").serialize(),
        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_updDocumento input').attr('disabled', 'disabled');
            $('#form_updDocumento textarea').attr('disabled', 'disabled');
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible guardar los cambios.</p>');
            }else{
                $('.alertForm').html('<p>Documento actualizada correctamente.</p>');
                $('#item_'+intIdDoc+' .rowDocumento').html($('#txtDocumento').val());
                $('#item_'+intIdDoc+' .rowDescripcion').html($('#txtDescripcion').val());
                $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_updDocumento input').removeAttr('disabled');
            $('#form_updDocumento textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Eliminar documento
function delDocumento(){

    var doc = $('#documento_id').val();
    $('.alertAddProduct').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_del_documento").serialize(),

        beforeSend: function(){
            $(".loading").show();
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color:red;">Error al eliminar el documento.</p>');

            }else{
                $('#item_'+doc).remove();
                $('#form_del_documento .btn_ok').remove();
                $('.alertAddProduct').html('<p>Documento eliminada correctamente.</p>');
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Nuevo Forma Pago
function newFormaPago(){
    var strFormaPago = $('#txtFormaPago').val();
    var strDesc      = $('#txtDescripcion').val();

    $('.alertForm').hide();

    if(strFormaPago == '' || strDesc == '' )
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        $('.alertForm').slideDown();
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_NewFormaPago").serialize(),

        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_NewFormaPago input').attr('disabled', 'disabled');
            $('#form_NewFormaPago textarea').attr('disabled', 'disabled');
        },
        success: function(response)
        {
            //console.log(response);
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible guardar los datos.</p>');
            }else{
                    $('.alertForm').html('<p>Datos guardado correctamente.</p>');
                    $('.alertForm').slideDown();
                    $('#form_NewFormaPago')[0].reset();
                    $('#txtDescripcion').focus();
                    $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_NewFormaPago input').removeAttr('disabled');
            $('#form_NewFormaPago textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Update Forma Pago
function updFormaPago(){
    var strFormaPago    = $('#txtFormaPago').val();
    var strDescripcion  = $('#txtDescripcion').val();
    var intIdFormaPago        = $('#txtIdFormaPago').val();

    if(strFormaPago == '' || strDescripcion == '' || intIdFormaPago == '')
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_updFormaPago").serialize(),
        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_updFormaPago input').attr('disabled', 'disabled');
            $('#form_updFormaPago textarea').attr('disabled', 'disabled');
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible actualizar los datos.</p>');
            }else{
                $('.alertForm').html('<p>Datos actualizados correctamente.</p>');
                $('#item_'+intIdFormaPago+' .rowDocumento').html($('#txtFormaPago').val());
                $('#item_'+intIdFormaPago+' .rowDescripcion').html($('#txtDescripcion').val());
                $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_updFormaPago input').removeAttr('disabled');
            $('#form_updFormaPago textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Eliminar Forma Pago
function delFormaPago(){
    var formaPago = $('#formapago_id').val();
    $('.alertAddProduct').html('');
    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_del_formapago").serialize(),
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color:red;">Error al eliminar el registro.</p>');
            }else{
                $('#item_'+formaPago).remove();
                $('#form_del_formapago .btn_ok').remove();
                $('.alertAddProduct').html('<p>Datos eliminada correctamente.</p>');
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
//Modal Activar | Desactivar Forma Pago
function modalActiveTipoPago(id){
    var intEstado  = $('#item'+id+' estado span').attr('estado');
    var idFormaPago= id;
    var action     = 'infoFormaPago';

    $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idFormaPago:idFormaPago},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    var btnAction;
                    var lblEstado;
                    if(info.estatus == 1){
                        btnAction = '<button type="submit" class="btn_ok btnActionForm"><i class="fas fa-ban"></i> Desactivar</button>';
                        lblEstado = 'Activo';
                    }else{
                        btnAction = '<button type="submit" class="btn_new btnActionForm"><i class="fas fa-check-circle"></i> Activar</button>';
                        lblEstado = 'Inactivo';
                    }

                    $('.bodyModal').html('<form action="" id="form_activeFormaPago" class="formModal" name="form_activeFormaPago" onsubmit="event.preventDefault(); activeFormaPago();">'+
                        '<h1><i class="fas fa-file-alt" style="font-size: 30pt;"></i> <br><br>Estado forma pago</h1><br>'+
                        '<p>Nombre: <strong>'+info.tipo_pago+'</strong></p>'+
                        '<p>Estado: <strong>'+lblEstado+'</strong></p>'+
                        '<input type="hidden" name="action" id="action" value="changeEstadoFormaPago" required>'+
                        '<input type="hidden" name="idFormaPago" id="idFormaPago" value="'+info.idformapago+'" required>'+
                        '<div class="alert alertForm"></div>'+
                        btnAction+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
}
//Active Active | Inactive Forma Pago
function activeFormaPago(){
    var idFormaPago = $('#idFormaPago').val();
    var msgAlert;
    var btnEstado;
    $('.alertForm').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_activeFormaPago").serialize(),
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible cambiar el estado.</p>');
            }else{

                if(response == 1)
                {
                    btnEstado = '<span title="Desactivar forma pago" class="pagada activeUser" estado="1" onclick="modalActiveTipoPago('+idFormaPago+');">Activo</span>';
                    msgAlert = '<p>Forma pago activado</p>';
                }else{
                    btnEstado = '<span title="Activar forma pago" class="anulada activeUser" estado="0" onclick="modalActiveTipoPago('+idFormaPago+');">Inactivo</span>';
                    msgAlert = '<p>Forma pago desactivado</p>';
                }

                $('#item_'+idFormaPago+' .rowEstado').html(btnEstado);
                $('#form_activeFormaPago .btnActionForm').remove();
                $('.alertForm').html(msgAlert);
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Nuevo Forma Pago
function newImpuesto(){
    var strImpuesto = $('#txtImpuesto').val();
    var strDesc      = $('#txtDescripcion').val();

    $('.alertForm').hide();

    if(strImpuesto == '' || strDesc == '' )
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        $('.alertForm').slideDown();
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_NewImpuesto").serialize(),

        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_NewImpuesto input').attr('disabled', 'disabled');
            $('#form_NewImpuesto textarea').attr('disabled', 'disabled');
        },
        success: function(response)
        {
            //console.log(response);
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible guardar los datos.</p>');
                $('.alertForm').slideDown();
            }else if(response == 'exist'){
                $('.alertForm').html('<p style="color:red;">El impuesto ya está registrado.</p>');
                $('.alertForm').slideDown();
            }else{
                    $('.alertForm').html('<p>Datos guardado correctamente.</p>');
                    $('.alertForm').slideDown();
                    $('#form_NewImpuesto')[0].reset();
                    $('#txtDescripcion').focus();
                    $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_NewImpuesto input').removeAttr('disabled');
            $('#form_NewImpuesto textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Update Impuesto
function updImpuesto(){
    var strImpuesto    = $('#txtImpuesto').val();
    var strDescripcion  = $('#txtDescripcion').val();
    var intIdImpuesto   = $('#txtIdImpuesto').val();

    if(strImpuesto == '' || strDescripcion == '' || intIdImpuesto == '')
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        return false;
    }
    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_updImpuesto").serialize(),
        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_updImpuesto input').attr('disabled', 'disabled');
            $('#form_updImpuesto textarea').attr('disabled', 'disabled');
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible actualizar los datos.</p>');
            }else if(response == 'exist'){
                $('.alertForm').html('<p style="color:red;">El impuesto ya existe.</p>');
            }else{
                $('.alertForm').html('<p>Datos actualizados correctamente.</p>');
                $('#item_'+intIdImpuesto+' .rowImpuesto').html($('#txtImpuesto').val());
                $('#item_'+intIdImpuesto+' .rowDescripcion').html($('#txtDescripcion').val());
                $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_updImpuesto input').removeAttr('disabled');
            $('#form_updImpuesto textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Eliminar Impuesto
function delImpuesto(){
    var impuesto = $('#idimpuesto').val();
    $('.alertAddProduct').html('');
    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_del_impuesto").serialize(),
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color:red;">Error al eliminar el registro.</p>');
            }else{
                $('#item_'+impuesto).remove();
                $('#form_del_impuesto .btn_ok').remove();
                $('.alertAddProduct').html('<p>Datos eliminada correctamente.</p>');
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
//Modal Activar | Desactivar Impuesto
function modalActiveImpuesto(id){
    var intEstado  = $('#item'+id+' estado span').attr('estado');
    var idImpuesto= id;
    var action     = 'infoImpuesto';

    $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idImpuesto:idImpuesto},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    var btnAction;
                    var lblEstado;
                    if(info.status == 1){
                        btnAction = '<button type="submit" class="btn_ok btnActionForm"><i class="fas fa-ban"></i> Desactivar</button>';
                        lblEstado = 'Activo';
                    }else{
                        btnAction = '<button type="submit" class="btn_new btnActionForm"><i class="fas fa-check-circle"></i> Activar</button>';
                        lblEstado = 'Inactivo';
                    }
                    $('.bodyModal').html('<form action="" id="form_activeImpuesto" class="formModal" name="form_activeImpuesto" onsubmit="event.preventDefault(); activeImpuesto();">'+
                        '<h1>Estado impuesto</h1><br>'+
                        '<p>Impuesto: <strong>'+info.impuesto+'</strong></p>'+
                        '<p>Estado: <strong>'+lblEstado+'</strong></p>'+
                        '<input type="hidden" name="action" id="action" value="changeEstadoImpuesto" required>'+
                        '<input type="hidden" name="idImpuesto" id="idImpuesto" value="'+info.idimpuesto+'" required>'+
                        '<div class="alert alertForm"></div>'+
                        btnAction+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
}
//Active Active | Inactive Forma Pago
function activeImpuesto(){
    var idImpuesto = $('#idImpuesto').val();
    var msgAlert;
    var btnEstado;
    $('.alertForm').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_activeImpuesto").serialize(),
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible cambiar el estado.</p>');
            }else{

                if(response == 1)
                {
                    btnEstado = '<span title="Desactivar impuesto" class="pagada activeUser" estado="1" onclick="modalActiveImpuesto('+idImpuesto+');">Activo</span>';
                    msgAlert = '<p>Impuesto activado</p>';
                }else{
                    btnEstado = '<span title="Activar impuesto" class="anulada activeUser" estado="0" onclick="modalActiveImpuesto('+idImpuesto+');">Inactivo</span>';
                    msgAlert = '<p>Impuesto desactivado</p>';
                }

                $('#item_'+idImpuesto+' .rowEstado').html(btnEstado);
                $('#form_activeImpuesto .btnActionForm').remove();
                $('.alertForm').html(msgAlert);
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Nuev serie Factura
function newSerie(){
    var strCai = $('#txtCai').val();
    var strPrefijo = $('#txtPrefijoFactura').val();
    var strFechaini = $('#txtPeriodoInicio').val();
    var strFechafin = $('#txtPeriodoFin').val();
    var strRango = $('#txtRango').val();
    var strCeros = $('#txtCeros').val();

    if(Date.parse(strFechaini) > Date.parse(strFechafin)){
        $('.alertForm').html('<p style="color:red;">La fecha de inicio debe ser menor a la fecha final.</p>');
        $('.alertForm').slideDown();
        return false; 
    }else{
        $('.alertForm').hide();
    }

    var arrRango = strRango.split("-");
    if(!isInteger(arrRango[0]) || !isInteger(arrRango[1]))
    {
        $('.alertForm').html('<p style="color:red;">El rango no es válido, ej. 1-100</p>');
        $('.alertForm').slideDown();
        return false; 
    }else{
        $('.alertForm').hide();
    }
    if(arrRango[0] > arrRango[1])
    {
        $('.alertForm').html('<p style="color:red;">El rango no es válido, ej. 1-100</p>');
        $('.alertForm').slideDown();
        return false; 
    }else{
        $('.alertForm').hide();
    }

    if(strCai == '' || strPrefijo == '' )
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        $('.alertForm').slideDown();
        return false;
    }
    if(!isInteger(strCeros) || strCeros < 0 ){
        $('.alertForm').html('<p style="color:red;">Cantidad de 0 incorrecto.</p>');
        $('.alertForm').slideDown();
        return false;
    }
    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_NewSerie").serialize(),
        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_NewSerie input').attr('disabled', 'disabled');
            $('#form_NewSerie date').attr('disabled', 'disabled');
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible guardar los datos.</p>');
            }else{
                    $('.alertForm').html('<p>Datos guardados correctamente.</p>');
                    $('.alertForm').slideDown();
                    $('#form_NewSerie')[0].reset();
                    $('#txtDescripcion').focus();
                    $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_NewSerie input').removeAttr('disabled');
            $('#form_NewSerie date').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Update Serie
function updSerie(){
    var strCai    = $('#txtCai').val();
    var strPrefijo = $('#txtPrefijoFactura').val();
    var intIdSerie = $('#txtIdSerie').val();
    var strFechaini = $('#txtPeriodoInicio').val();
    var strFechafin = $('#txtPeriodoFin').val();
    var strRango = $('#txtRango').val();
    var strCeros = $('#txtCeros').val();    

    if(Date.parse(strFechaini) > Date.parse(strFechafin)){
        $('.alertForm').html('<p style="color:red;">La fecha de inicio debe ser menor a la fecha final.</p>');
        $('.alertForm').slideDown();
        return false; 
    }else{
        $('.alertForm').hide();
    }

    var arrRango = strRango.split("-");
    if(!isInteger(arrRango[0]) || !isInteger(arrRango[1]))
    {
        $('.alertForm').html('<p style="color:red;">El rango no es válido, ej. 1-100</p>');
        $('.alertForm').slideDown();
        return false; 
    }else{
        $('.alertForm').hide();
    }
    if(arrRango[0] > arrRango[1])
    {
        $('.alertForm').html('<p style="color:red;">El rango no es válido, ej. 1-100</p>');
        $('.alertForm').slideDown();
        return false; 
    }else{
        $('.alertForm').hide();
    }

    if(strCai == '' || strPrefijo == '' )
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        $('.alertForm').slideDown();
        return false;
    }
    if(strCai == '' || strPrefijo == '' || intIdSerie == '')
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        return false;
    }
    if(!isInteger(strCeros) || strCeros < 0 ){
        $('.alertForm').html('<p style="color:red;">Cantidad de 0 incorrecto.</p>');
        $('.alertForm').slideDown();
        return false;
    }
    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_updSerie").serialize(),
        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_updSerie input').attr('disabled', 'disabled');
        },

        success: function(response)
        {
            if(response == 'ok'){
                $('.alertForm').html('<p>Datos actualizados correctamente.</p>');
                $('.alertForm').show();
                $('.btn_cancel').attr('onclick','refreshPage();');
            }else{
                $('.alertForm').html('<p style="color:red;">No es posible guardar los cambios.</p>');
                $('.alertForm').show();
            }
            $('#form_updSerie input').removeAttr('disabled');
            $('#form_updSerie textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
//Modal Activar | Desactivar Serie
function modalActiveSerie(id){
    var intEstado  = $('#item'+id+' estado span').attr('estado');
    var idSerie= id;
    var action     = 'infoSerie';

    $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idSerie:idSerie},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    var btnAction;
                    var lblEstado;
                    if(info.status == 1){
                        btnAction = '<button type="submit" class="btn_ok btnActionForm"><i class="fas fa-ban"></i> Desactivar</button>';
                        lblEstado = 'Activo';
                    }else{
                        btnAction = '<button type="submit" class="btn_new btnActionForm"><i class="fas fa-check-circle"></i> Activar</button>';
                        lblEstado = 'Inactivo';
                    }
                    $('.bodyModal').html('<form action="" id="form_activeSerie" class="formModal" name="form_activeSerie" onsubmit="event.preventDefault(); activeSerie();">'+
                        '<h1><i class="fas fa-file-alt" style="font-size: 30pt;"></i> <br><br>Estado rango facturación</h1><br>'+
                        '<p>Nombre: <strong>CAI: '+info.cai+'</strong></p>'+
                        '<p>Estado: <strong>'+lblEstado+'</strong></p>'+
                        '<input type="hidden" name="action" id="action" value="changeEstadoSerie" required>'+
                        '<input type="hidden" name="idSerie" id="idSerie" value="'+info.idserie+'" required>'+
                        '<div class="alert alertForm"></div>'+
                        btnAction+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
}
// Eliminar Serie
function delSerie(){

    var serie = $('#serie_id').val();
    $('.alertAddProduct').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_del_serie").serialize(),

        beforeSend: function(){
            $(".loading").show();
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color:red;">Error al eliminar el registro.</p>');
            }else{
                $('#item_'+serie).remove();
                $('#form_del_serie .btn_ok').remove();
                $('.alertAddProduct').html('<p>Datos eliminados.</p>');
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
//Active Active | Inactive Series
function activeSerie(){
    var idSerie = $('#idSerie').val();
    var msgAlert;
    var btnEstado;
    $('.alertForm').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_activeSerie").serialize(),
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible cambiar el estado.</p>');
            }else{
                if(response == 1)
                {
                    btnEstado = '<span title="Desactivar" class="pagada activeUser" estado="1" onclick="modalActiveDocumento('+idSerie+');">Activo</span>';
                    msgAlert = '<p>Rango facturación Activado</p>';
                }else{
                    btnEstado = '<span title="Activar" class="anulada activeUser" estado="0" onclick="modalActiveDocumento('+idSerie+');">Inactivo</span>';
                    msgAlert = '<p>Rango facturación Desactivado</p>';
                }
                $('#item_'+idSerie+' .rowEstado').html(btnEstado);
                $('#form_activeSerie .btnActionForm').remove();
                $('.alertForm').html(msgAlert);
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Nueva categoría
function newCategoria(){
    var strCategoria    = $('#txtCategoria').val();
    var strDescCat      = $('#txtDescripcionCat').val();

    if(strCategoria == '' || strDescCat == '' )
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_NewCategoria").serialize(),

        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_NewCategoria input').attr('disabled', 'disabled');
            $('#form_NewCategoria textarea').attr('disabled', 'disabled');
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible crear la categoría.</p>');
            }else{
                    $('.alertForm').html('<p>Categoría creada correctamente.</p>');
                    $('#form_NewCategoria')[0].reset();
                    $('#txtCategoria').focus();
                    $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_NewCategoria input').removeAttr('disabled');
            $('#form_NewCategoria textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
// Update categoría
function updCategoria(){
    var strCategoria    = $('#txtCategoria').val();
    var strDescCat      = $('#txtDescripcionCat').val();
    var intIdCat        = $('#txtIdCategoria').val();

    if(strCategoria == '' || strDescCat == '' )
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_updCategoria").serialize(),

        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_updCategoria input').attr('disabled', 'disabled');
            $('#form_updCategoria textarea').attr('disabled', 'disabled');
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible crear la categoría.</p>');
            }else{
                $('.alertForm').html('<p>Categoría actualizada correctamente.</p>');
                $('#item_'+intIdCat+' .rowCategoria').html($('#txtCategoria').val());
                $('#item_'+intIdCat+' .rowDescripcion').html($('#txtDescripcionCat').val());
            }
            $('#form_updCategoria input').removeAttr('disabled');
            $('#form_updCategoria textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}
//Modal Activar | Desactivar Documento
function modalActiveDocumento(id){
    var intEstado  = $('#item'+id+' estado span').attr('estado');
    var idDocumento= id;
    var action     = 'infoDocumento';

    $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idDocumento:idDocumento},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    var btnAction;
                    var lblEstado;
                    if(info.estatus == 1){
                        btnAction = '<button type="submit" class="btn_ok btnActionForm"><i class="fas fa-ban"></i> Desactivar</button>';
                        lblEstado = 'Activo';
                    }else{
                        btnAction = '<button type="submit" class="btn_new btnActionForm"><i class="fas fa-check-circle"></i> Activar</button>';
                        lblEstado = 'Inactivo';
                    }

                    $('.bodyModal').html('<form action="" id="form_activeDocumento" class="formModal" name="form_activeDocumento" onsubmit="event.preventDefault(); activeDocumento();">'+
                        '<h1><i class="fas fa-file-alt" style="font-size: 30pt;"></i> <br><br>Estado de documento</h1><br>'+
                        '<p>Nombre: <strong>'+info.documento+'</strong></p>'+
                        '<p>Estado: <strong>'+lblEstado+'</strong></p>'+
                        '<input type="hidden" name="action" id="action" value="changeEstadoDocumento" required>'+
                        '<input type="hidden" name="idDocumento" id="idDocumento" value="'+info.idDocumento+'" required>'+
                        '<div class="alert alertForm"></div>'+
                        btnAction+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
}
//Active Active | Inactive documento
function activeDocumento(){
    var idDocumento = $('#idDocumento').val();
    var msgAlert;
    var btnEstado;
    $('.alertForm').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_activeDocumento").serialize(),
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible cambiar el estado del documento.</p>');
            }else{

                if(response == 1)
                {
                    btnEstado = '<span title="Desactivar documento" class="pagada activeUser" estado="1" onclick="modalActiveDocumento('+idDocumento+');">Activo</span>';
                    msgAlert = '<p>Documento Activada</p>';
                }else{
                    btnEstado = '<span title="Activar documento" class="anulada activeUser" estado="0" onclick="modalActiveDocumento('+idDocumento+');">Inactivo</span>';
                    msgAlert = '<p>Documento Desactivada</p>';
                }

                $('#item_'+idDocumento+' .rowEstado').html(btnEstado);
                $('#form_activeDocumento .btnActionForm').remove();
                $('.alertForm').html(msgAlert);
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

// Nueva marca
function newMarca(){
    var strMarca    = $('#txtMarca').val();

    if(strMarca == '' )
    {
        $('.alertForm').html('<p style="color:red;">Escribe el nombre de la marca.</p>');
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_NewMarca").serialize(),

        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_NewMarca input').attr('disabled', 'disabled');
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible crear la marca.</p>');
            }else{
                    $('.alertForm').html('<p>Marca creada correctamente.</p>');
                    $('#form_NewMarca')[0].reset();
                    $('#txtMarca').focus();
                    $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_NewMarca input').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

// Update marca
function updMarca(){
    var strMarca    = $('#txtMarca').val();
    var strDescM  = $('#txtDescripcionM').val();
    var intIdMarca    = $('#txtIdMarca').val();

    if(strMarca == '' || strDescM == '' )
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_updMarca").serialize(),

        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_updMarca input').attr('disabled', 'disabled');
            $('#form_updMarca textarea').attr('disabled', 'disabled');
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible actualizar la categoría.</p>');
            }else{
                $('.alertForm').html('<p>Marca actualizada correctamente.</p>');
                $('#item_'+intIdMarca+' .rowMarca').html($('#txtMarca').val());
                $('#item_'+intIdMarca+' .rowDescripcion').html($('#txtDescripcionM').val());
            }
            $('#form_updMarca input').removeAttr('disabled');
            $('#form_updMarca textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

// Nueva Ubicación
function newUbicacion(){
    var strUbicacion    = $('#txtUbicacion').val();

    if(strUbicacion == '' )
    {
        $('.alertForm').html('<p style="color:red;">Escribe el nombre de la ubicación.</p>');
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_NewUbicacion").serialize(),

        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_NewUbicacion input').attr('disabled', 'disabled');
        },

        success: function(response)
        {
            //console.log(response);
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible crear la ubicación.</p>');
            }else{
                    $('.alertForm').html('<p>Ubicación creada correctamente.</p>');
                    $('#form_NewUbicacion')[0].reset();
                    $('#txtUbicacion').focus();
                    $('.btn_cancel').attr('onclick','refreshPage();');
            }
            $('#form_NewUbicacion input').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

// Update Ubicacion
function updUbicacion(){
    var strUbicacion    = $('#txtUbicacion').val();
    var strDescU  = $('#txtDescripcionU').val();
    var intIdMarca    = $('#txtIdUbicacion').val();

    if(strUbicacion == '' || strDescU == '' )
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        return false;
    }

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_updUbicacion").serialize(),
        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_updUbicacion input').attr('disabled', 'disabled');
            $('#form_updUbicacion textarea').attr('disabled', 'disabled');
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible actualizar la ubicación.</p>');
            }else{
                $('.alertForm').html('<p>Ubicación actualizada correctamente.</p>');
                $('#item_'+intIdMarca+' .rowUbicacion').html($('#txtUbicacion').val());
                $('#item_'+intIdMarca+' .rowDescripcion').html($('#txtDescripcionU').val());
            }
            $('#form_updUbicacion input').removeAttr('disabled');
            $('#form_updUbicacion textarea').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

// Eliminar Ubicacion
function delUbicacion(){

    var cat = $('#ubicacion_id').val();
    $('.alertAddProduct').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_del_ubicacion").serialize(),

        beforeSend: function(){
            $(".loading").show();
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color:red;">Error al eliminar la ubicación.</p>');

            }else{
                $('#item_'+cat).remove();
                $('#form_del_ubicacion .btn_ok').remove();
                $('.alertAddProduct').html('<p>Ubicación eliminada correctamente.</p>');
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

//Actualizar Usuario
function updateUser(){

    var intDpi     = $('#dpi').val();
    var strNombre  = $('#nombre').val();
    var intTel     = $('#telefono').val();
    var strEmail   = $('#correo').val();

    if(intDpi == '' || strNombre == '' || intTel == '' || strEmail == '')
    {
        $('.alertForm').html('<p style="color:red;">Todos los campos son obligatorios.</p>');
        return false;
    }

    $.ajax({
        url:  'ajax.php',
        type: "POST",
        async : true,
        data: $("#form_updateUser").serialize(),

        beforeSend: function(){
            $(".loading").show();
            $('.alertForm').html('');
            $('#form_updateUser input').attr('disabled', 'disabled');
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible actualizar sus datos.</p>');
            }else{
                var info = JSON.parse(response);
                if(info.cod == '00')
                {
                    $('.alertForm').html('<p>Datos actualizados correctamente.</p>');
                    $('#usDpi').html('<strong>'+intDpi+'</strong>');
                    $('#usNombre').html('<strong>'+strNombre+'</strong>');
                    $('#usTel').html('<strong>'+intTel+'</strong>');
                    $('#usEmail').html('<strong>'+strEmail+'</strong>');
                }else{
                    $('.alertForm').html('<p style="color:red">'+info.msg+'</p>');
                }
            }
            $('#form_updateUser input').removeAttr('disabled');
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

//Activar | Desactivar usuario
function modalActiveUser(id){
    var intEstado  = $('#item'+id+' estado span').attr('estado');
    var idUser     = id;
    var action     = 'infoUsuario';

    $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idUser:idUser},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    var btnAction;
                    var lblEstado;
                    if(info.estatus == 1){
                        btnAction = '<button type="submit" class="btn_ok btnActionForm"><i class="fas fa-ban"></i> Desactivar</button>';
                        lblEstado = '<strong style="color:green;">Activo</strong>';
                    }else{
                        btnAction = '<button type="submit" class="btn_new btnActionForm"><i class="fas fa-check-circle"></i> Activar</button>';
                        lblEstado = '<strong style="color:red;">Inactivo</strong>';
                    }

                    $('.bodyModal').html('<form action="" id="form_activeUser" class="formModal" name="form_activeUser" onsubmit="event.preventDefault(); activeUser();">'+
                        '<h1><i class="fas fa-user" style="font-size: 45pt;"></i><br><br>Cambiar estado del usuario</h1><br>'+
                        '<p>Nombre: <strong>'+info.nombre+'</strong></p>'+
                        '<p>Usuario: <strong>'+info.usuario+'</strong></p>'+
                        '<p>Tipo Usuario. <strong>'+info.rol+'</strong></p>'+
                        '<p>Estado: <strong>'+lblEstado+'</strong></p>'+
                        '<input type="hidden" name="action" id="action" value="changeEstadoUser" required>'+
                        '<input type="hidden" name="idUser" id="idUser" value="'+info.idusuario+'" required>'+
                        '<div class="alert alertForm"></div>'+
                        btnAction+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $('.loading').hide();
            },
            error: function(error) {
            }
        });

}

//Active Active | Inactive User
function activeUser(){
    var us = $('#idUser').val();
    var msgAlert;
    var btnEstado;
    $('.alertForm').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_activeUser").serialize(),

        beforeSend: function(){
            $('.loading').show();
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible cambiar el estado del usuario.</p>');

            }else{

                if(response == 1)
                {
                    btnEstado = '<span title="Desactivar usuario" class="pagada activeUser" estado="1" onclick="modalActiveUser('+us+');">Activo</span>';
                    msgAlert = '<p>El usuario se ha Activado</p>';
                }else{
                    btnEstado = '<span title="Activar usuario" class="anulada activeUser" estado="0" onclick="modalActiveUser('+us+');">Inactivo</span>';
                    msgAlert = '<p>El usuario se ha Desactivado</p>';
                }

                $('#item_'+us+' .estado').html(btnEstado);
                $('#form_activeUser .btnActionForm').remove();
                $('.alertForm').html(msgAlert);
            }
            $('.loading').hide();

        },
        error: function(error) {
        }
    });
}

//Activar | Desactivar marca
function modalActiveMarca(id){
    var intEstado  = $('#item'+id+' estado span').attr('estado');
    var idMarca     = id;
    var action     = 'infoMarca';

    $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idMarca:idMarca},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    var btnAction;
                    var lblEstado;
                    if(info.estatus == 1){
                        btnAction = '<button type="submit" class="btn_ok btnActionForm"><i class="fas fa-ban"></i> Desactivar</button>';
                        lblEstado = 'Activo';
                    }else{
                        btnAction = '<button type="submit" class="btn_new btnActionForm"><i class="fas fa-check-circle"></i> Activar</button>';
                        lblEstado = 'Inactivo';
                    }

                    $('.bodyModal').html('<form action="" id="form_activeMarca" class="formModal" name="form_activeMarca" onsubmit="event.preventDefault(); activeMarca();">'+
                        '<h1><i class="fab fa-bandcamp" style="font-size: 45pt;"></i><br><br>Estado de marca</h1><br>'+
                        '<p>Nombre: <strong>'+info.marca+'</strong></p>'+
                        '<p>Estado: <strong>'+lblEstado+'</strong></p>'+
                        '<input type="hidden" name="action" id="action" value="changeEstadoMarca" required>'+
                        '<input type="hidden" name="idMarca" id="idMarca" value="'+info.idmarca+'" required>'+
                        '<div class="alert alertForm"></div>'+
                        btnAction+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
}

//Active Active | Inactive Marca
function activeMarca(){
    var idMarca = $('#idMarca').val();
    var msgAlert;
    var btnEstado;
    $('.alertForm').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_activeMarca").serialize(),
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible cambiar el estado de la marca.</p>');

            }else{

                if(response == 1)
                {
                    btnEstado = '<span title="Desactivar marca" class="pagada activeUser" estado="1" onclick="modalActiveMarca('+idMarca+');">Activo</span>';
                    msgAlert = '<p>Marca Activada</p>';
                }else{
                    btnEstado = '<span title="Activar marca" class="anulada activeUser" estado="0" onclick="modalActiveMarca('+idMarca+');">Inactivo</span>';
                    msgAlert = '<p>Marca Desactivada</p>';
                }

                $('#item_'+idMarca+' .rowEstado').html(btnEstado);
                $('#form_activeMarca .btnActionForm').remove();
                $('.alertForm').html(msgAlert);
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

//Activar | Desactivar usuario
function modalActiveCategoria(id){
    var intEstado  = $('#item'+id+' estado span').attr('estado');
    var idCategoria= id;
    var action     = 'infoCategory';

    $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idCategoria:idCategoria},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    var btnAction;
                    var lblEstado;
                    if(info.estatus == 1){
                        btnAction = '<button type="submit" class="btn_ok btnActionForm"><i class="fas fa-ban"></i> Desactivar</button>';
                        lblEstado = 'Activo';
                    }else{
                        btnAction = '<button type="submit" class="btn_new btnActionForm"><i class="fas fa-check-circle"></i> Activar</button>';
                        lblEstado = 'Inactivo';
                    }

                    $('.bodyModal').html('<form action="" id="form_activeCategoria" class="formModal" name="form_activeCategoria" onsubmit="event.preventDefault(); activeCategoria();">'+
                        '<h1><i class="fas fa-window-restore" style="font-size: 45pt;"></i> <br><br>Estado de categoria</h1><br>'+
                        '<p>Nombre: <strong>'+info.categoria+'</strong></p>'+
                        '<p>Estado: <strong>'+lblEstado+'</strong></p>'+
                        '<input type="hidden" name="action" id="action" value="changeEstadoCategoria" required>'+
                        '<input type="hidden" name="idCategoria" id="idCategoria" value="'+info.idcategoria+'" required>'+
                        '<div class="alert alertForm"></div>'+
                        btnAction+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
}

//Active Active | Inactive User
function activeCategoria(){
    var idCategoria = $('#idCategoria').val();
    var msgAlert;
    var btnEstado;
    $('.alertForm').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_activeCategoria").serialize(),
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible cambiar el estado de la categoria.</p>');
            }else{

                if(response == 1)
                {
                    btnEstado = '<span title="Desactivar categoria" class="pagada activeUser" estado="1" onclick="modalActiveCategoria('+idCategoria+');">Activo</span>';
                    msgAlert = '<p>Marca Activada</p>';
                }else{
                    btnEstado = '<span title="Activar categoria" class="anulada activeUser" estado="0" onclick="modalActiveCategoria('+idCategoria+');">Inactivo</span>';
                    msgAlert = '<p>Marca Desactivada</p>';
                }

                $('#item_'+idCategoria+' .rowEstado').html(btnEstado);
                $('#form_activeCategoria .btnActionForm').remove();
                $('.alertForm').html(msgAlert);
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

//Activar | Desactivar Ubicación
function modalActiveUbicacion(id){
    var intEstado  = $('#item'+id+' estado span').attr('estado');
    var idUbicacion     = id;
    var action     = 'infoUbicacion';

    $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,idUbicacion:idUbicacion},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response != 'error')
                {
                    var info = JSON.parse(response);
                    var btnAction;
                    var lblEstado;
                    if(info.status == 1){
                        btnAction = '<button type="submit" class="btn_ok btnActionForm"><i class="fas fa-ban"></i> Desactivar</button>';
                        lblEstado = 'Activo';
                    }else{
                        btnAction = '<button type="submit" class="btn_new btnActionForm"><i class="fas fa-check-circle"></i> Activar</button>';
                        lblEstado = 'Inactivo';
                    }

                    $('.bodyModal').html('<form action="" id="form_activeUbicacion" class="formModal" name="form_activeUbicacion" onsubmit="event.preventDefault(); activeUbicacion();">'+
                        '<h1><i class="fas fa-location-arrow" style="font-size: 45pt;"></i>'+
                        '</i><br><br>Estado de ubicacion</h1><br>'+
                        '<p>Nombre: <strong>'+info.ubicacion+'</strong></p>'+
                        '<p>Estado: <strong>'+lblEstado+'</strong></p>'+
                        '<input type="hidden" name="action" id="action" value="changeEstadoUbicacion" required>'+
                        '<input type="hidden" name="idUbicacion" id="idUbicacion" value="'+info.id_ubicacion+'" required>'+
                        '<div class="alert alertForm"></div>'+
                        btnAction+
                        '<button type="button" class="btn_cancel closeModal" onclick="closeModal();"><i class="fas fa-sign-out-alt"></i> Cerrar</button>'+
                    '</form>');
                    modalView();
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
}

//Active Active | Inactive Ubicación
function activeUbicacion(){
    var idUbicacion = $('#idUbicacion').val();
    var msgAlert;
    var btnEstado;
    $('.alertForm').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_activeUbicacion").serialize(),
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response == 'error'){
                $('.alertForm').html('<p style="color:red;">No es posible cambiar el estado de la ubicación.</p>');

            }else{

                if(response == 1)
                {
                    btnEstado = '<span title="Desactivar ubicación" class="pagada activeUser" estado="1" onclick="modalActiveUbicacion('+idUbicacion+');">Activo</span>';
                    msgAlert = '<p>Marca Activada</p>';
                }else{
                    btnEstado = '<span title="Activar ubicación" class="anulada activeUser" estado="0" onclick="modalActiveUbicacion('+idUbicacion+');">Inactivo</span>';
                    msgAlert = '<p>Marca Desactivada</p>';
                }

                $('#item_'+idUbicacion+' .rowEstado').html(btnEstado);
                $('#form_activeUbicacion .btnActionForm').remove();
                $('.alertForm').html(msgAlert);
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}


//Validar Clave
function validPass(){
    var passNuevo  = $('#txtNewPassUser').val();
    var confirmPassNuevo = $('#txtPassConfirm').val();
    if(passNuevo != confirmPassNuevo){
        $('.alertChangePass').html('<p>Las contraeñas no son iguales.</p>');
        $('.alertChangePass').slideDown();
        return false;
    }

    if(passNuevo.length < 6 ){
        $('.alertChangePass').html('<p>La nueva contraseña debe ser de 6 caracteres como mínimo.</p>');
        $('.alertChangePass').slideDown();
        return false;
    }

    $('.alertChangePass').html('');
    $('.alertChangePass').slideUp();
}

//Anular factura
function anularFactura(){
    var noFactura  = $('#no_factura').val();
    var action     = 'anularFactura';

    $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,noFactura:noFactura},
            beforeSend: function(){
                $(".loading").show();
            },
            success: function(response)
            {
                if(response == 'error'){
                    $('.alertAddProduct').html('<p style="color:red;">Error al anular la factura.</p>');
                }else{
                    $('#row_'+noFactura+' .estado').html('<span class="anulada">Anulada</span>');
                    $('#form_anular_factura .btn_ok').remove();
                    $('#row_'+noFactura+' .div_factura').html('<button type="button" class="btn_anular inactive" ><i class="fas fa-ban"></i></button>');
                    $('.alertAddProduct').html('<p>Factura anulada.</p>');
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
}

//Anular pedido
function updPedido(){
    var noPedido  = $('#no_pedido').val();
    var estado  = $('#selectEstado').val();
    var action     = 'updPedido';
    var strEstado='';

    if(noPedido == "" || estado == "")
    {
        $('.alertAddProduct').html('<p style="color:red;">Seleccione estado.</p>');
    }
    $.ajax({
            url : '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,noPedido:noPedido,estado:estado},
            beforeSend: function(){
                $(".loading").show();
                $('.alertAddProduct').html('');
            },
            success: function(response)
            {
                if(response == 'error'){
                    $('.alertAddProduct').html('<p style="color:red;">Error al anular el pedido.</p>');
                }else if(response == 'ok'){

                    if(estado == 1){
                        strEstado='<span class="bkA">Activo</span>';
                        strEstadoPedido = "<p>Pedido activo</p>";
                    }else if(estado == 2){
                        strEstado='<span class="bkY">En proceso</span>';
                        strEstadoPedido = "<p>Pedido en proceso</p>";
                    }else if(estado == 3){
                        strEstado='<span class="bkG">Entregado</span>';
                        strEstadoPedido = "<p>Pedido entregado</p>";
                    }else if(estado == 4){
                        strEstado='<span class="bkR">Anulado</span>';
                        strEstadoPedido = "<p>Pedido anulado</p>";
                    }
                    $('#row_'+noPedido+' .estado').html(strEstado);
                    $('#form_anular_factura .btn_ok').remove();
                    //$('#row_'+noPedido+' .div_pedido').html('<button type="button" class="btn_anular inactive" ><i class="fas fa-ban"></i></button>');
                    $('.alertAddProduct').html(strEstadoPedido);
                }
                $(".loading").hide();
            },
            error: function(error) {
            }
        });
}

//Extraer productos para el detalle al cargar la página
function serchForDetalle(id,operacion)
{
    var action = 'serchForDetalle';
    var user = id;

    $.ajax({
        url : '../ajax.php',
        type: "POST",
        async : true,
        data: {action:action,user:id,operacion:operacion},
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response != 'error')
            {
                var info = JSON.parse(response);

                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);
                $('#txtTotal').val(info.total);

                if(operacion == 0)
                {
                    if($('#detalle_venta tr').length > 0){
                        $('#btn_facturar_compra').show();
                    }else{
                        $('#btn_facturar_compra').hide();
                    }
                }
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

//Eliminar producto del detalle
function del_product_detalle(id,op){

    var id_detalle  = id;
    var action      = 'delProductoDetalle';
    $.ajax({
        url : '../ajax.php',
        type: "POST",
        async : true,
        data: {action:action,id_detalle:id_detalle,operacion:op},
        beforeSend: function(){
            $(".loading").show();
        },
        success: function(response)
        {
            if(response != 'error')
            {
                var info = JSON.parse(response);
                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);
                $('#txtTotal').val(info.total);
                //Limpiar datos del producto agregado y ocultar boton agregar
                $('#txt_cod_producto').val('');
                $('#txt_descripcion').html('-');
                $('#txt_existencia').html('-');
                $('#txt_cant_producto').val('0');
                $('#txt_precio').html('0');
                $('#txt_precio_total').html('0');
                //Bloquear Cantidad
                $('#txt_cant_producto').attr('disabled','disabled');
                //Ocultar boton agregar
                $('#add_product_venta').slideUp();
            }else{
                $('#detalle_venta').html('');
                $('#detalle_totales').html('');
                $('#txtTotal').val('');
            }
            //Valida si es detalle venta
            if(op == 1)
            {
                //Mostrar / Ocultar Botón Procesar
                if($('#detalle_venta tr').length > 0){
                    if($('#tipo_pago').val() == 1 )
                    {
                        if(parseFloat($('#txtPagoEfectivo').val()) >= parseFloat($('#hidTotalPagar').val())){
                            $('#btn_facturar_venta').show();
                        }else{
                            $('#btn_facturar_venta').hide();
                        }
                    }
                }else{
                    $('#btn_facturar_venta').hide();
                }
            }

            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

//Mostrar/Ocultar boton procesar
function viewProcesar(){
    //alert($('#detalle_venta tr').length);
    if($('#detalle_venta tr').length > 0)
    {
        $('#btn_facturar_venta').show();
    }else{
        $('#btn_facturar_venta').hide();
    }
}

function getUrl() {
    var loc = window.location;
    var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
    return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
}

// Eliminar producto
function delProduct(){
    var pr = $('#producto_id').val();
    $('.alertAddProduct').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_del_product").serialize(),

        beforeSend: function(){
            $(".loading").show();
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color:red;">Error al eliminar el producto.</p>');

            }else{
                $('.row'+pr).remove();
                $('#form_del_product .btn_ok').remove();
                $('.alertAddProduct').html('<p>Producto eliminado correctamente.</p>');
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

// Eliminar Categoria
function delCategoria(){

    var cat = $('#categoria_id').val();
    $('.alertAddProduct').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_del_categoria").serialize(),

        beforeSend: function(){
            $(".loading").show();
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color:red;">Error al eliminar la categoría.</p>');

            }else{
                $('#item_'+cat).remove();
                $('#form_del_categoria .btn_ok').remove();
                $('.alertAddProduct').html('<p>Categoría eliminada correctamente.</p>');
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

// Eliminar Marca
function delMarca(){

    var cat = $('#marca_id').val();
    $('.alertAddProduct').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_del_marca").serialize(),

        beforeSend: function(){
            $(".loading").show();
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color:red;">Error al eliminar la marca.</p>');

            }else{
                $('#item_'+cat).remove();
                $('#form_del_marca .btn_ok').remove();
                $('.alertAddProduct').html('<p>Marca eliminada correctamente.</p>');
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

function sendDataProduct(){

    var pr = $('#producto_id').val();
    $('.alertAddProduct').html('');

    $.ajax({
        url:  '../ajax.php',
        type: "POST",
        async : true,
        data: $("#form_add_product").serialize(),

        beforeSend: function(){
            $(".loading").show();
        },

        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color:red;">Error al agregar el producto.</p>');

            }else{
                $('#txtCantidad').val('');
                $('.alertAddProduct').html('<p>Producto agregado correctamente.</p>');
            }
            $(".loading").hide();
        },
        error: function(error) {
        }
    });
}

function closeModal()
{
    $('.modal').fadeOut();
    $('#txtCantidad').val('');
    $('#txtPrecio').val('');
    $('body').css({'overflow':''});
    //$('.alertAddProduct').html('');

}
function refreshPage(){
    location.reload();
}

function generarPDF(cliente,factura){
    //alert(cliente+','+factura);
    //Obtenemos el alto y ancho de la pantalla
        var ancho = 1000;
        var alto = 800;
        //Calcular posicion x,y para centrar la ventana
        var x = parseInt((window.screen.width/2) - (ancho / 2));
        var y = parseInt((window.screen.height/2) - (alto / 2));
        $url = base_url+'/sistema/factura/generaFactura.php?cl='+cliente+'&f='+factura;
        window.open($url,"Factura","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");
}
function generarTicket(cliente,factura){
    //alert(cliente+','+factura);
    //Obtenemos el alto y ancho de la pantalla
        var ancho = 400;
        var alto = 600;
        //Calcular posicion x,y para centrar la ventana
        var x = parseInt((window.screen.width/2) - (ancho / 2));
        var y = parseInt((window.screen.height/2) - (alto / 2));
        $url = base_url+'/sistema/factura/generarTicket.php?cl='+cliente+'&f='+factura;
        window.open($url,"Factura","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");
}
function generarPedidoPDF(key,pedido){
    //alert(cliente+','+factura);
    //Obtenemos el alto y ancho de la pantalla
        var ancho = 1000;
        var alto = 800;
        //Calcular posicion x,y para centrar la ventana
        var x = parseInt((window.screen.width/2) - (ancho / 2));
        var y = parseInt((window.screen.height/2) - (alto / 2));
        $url = base_url+'/sistema/pedidos/pedido.php?p='+pedido+'&c='+key;
        // https://abelosh.com/sistema_ventas//sistema/pedidos/pedido.php?p=rdKvwM7gz5eP&c=mKWjqpaZqaWUbGo=
        window.open($url,"Pedido","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");
}
function controlTag(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla==8) return true; // para la tecla de retroseso
    else if (tecla==0||tecla==9)  return true; //<-- PARA EL TABULADOR-> su keyCode es 9 pero en tecla se esta transformando a 0 asi que porsiacaso los dos
    patron =/[0-9\s]/;// -> solo letras
   // patron =/[0-9\s]/;// -> solo numeros
    te = String.fromCharCode(tecla);
    return patron.test(te); 
}

function fntEmailValidate(email){
    var stringEmail = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
    if (stringEmail.test(email) == false){
        return false;
    }else{
        return true;
    }
}
function isInteger (numero){
    if(numero % 1 == 0){
        return true;
    }else{
        return false;
    }
}