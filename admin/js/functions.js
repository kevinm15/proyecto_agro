jQuery(document).ready(function($) {
	//Login
	$('#formLogin').submit(function(e){
		e.preventDefault();
		var usuario = $('#usuario').val();
		var clave   = $('#clave').val();
		var action  = 'login';

		$.ajax({
            url:  '../sistema/ajax.php',
            type: "POST",
            async : true,
            data: {usuario:usuario,clave:clave,action:action},

            beforeSend: function(){
                $(".loading").show();
                $('#formLogin .alert').html('');
            },

            success: function(response)
            {
            	//var infoData = $.parseJSON('json');
				var infoData = JSON.parse(response);
				if(infoData.cod == '00'){
					location.reload();
				}else{
					$('#formLogin .alert').html('<p style="color:#f01515;">'+infoData.msg+'</p>');
					$('#clave').val('');
					$('#clave').focus();
                    $(".loading").hide();
                }
            },
            error: function(error) {
            }
        });
	});


    $('.closeModal').click(function(e) {
        e.preventDefault();
        $('.modalLogin').slideUp();
        $('#txtEmail').val('');
        $('.alertSolicitud').html('');
    });

    $('#linkRecoveryPass').click(function(e) {
        e.preventDefault();
        $('.modalLogin').fadeIn();
        $('#txtEmail').val('');
        $('.alertSolicitud').html('');
    });

    $('#btnRecoveryPass').click(function(e){
        e.preventDefault();
        if( $('#txtEmail').val() != ''){
            var email = $('#txtEmail').val();
            var action = "recoveryPass";

            if(!fntEmailValidate($('#txtEmail').val()))
            {
                $('.alertSolicitud').html('<p style="color:#f01515;">El correo electrónico no es válido.</p>');
                $('.alertSolicitud').show();
                return false;
            }

            $.ajax({
                url:  '../sistema/ajax.php',
                type: "POST",
                async : true,
                data: {email:email,action:action},

                beforeSend: function(){
                    $(".loading").show();
                    $('.alertSolicitud').html('');
                    $('.alertSolicitud').hide();
                },

                success: function(response)
                {
                    try {
                        var infoData = JSON.parse(response);
                        var alert = '';
                        if(infoData.cod == '00'){
                            alert = '<p style="color:#3d88a6;">'+infoData.msg+'</p>';
                            $('#txtEmail').val('');
                        }else{
                            alert = '<p style="color:#f01515;">'+infoData.msg+'</p>';
                        }
                    } catch (e) {
                            alert = '<p style="color:#f01515;">No es posible enviar el email.</p>';
                    }
                    $('.alertSolicitud').html(alert);
                    $('.alertSolicitud').slideDown();
                    $(".loading").hide();
                },
                error: function(error) {
                }
            });
        }
    });

    //Actualizar password
    $('#formChangePassword').submit(function(e){
        e.preventDefault();

        var pass1 = $('#pass1').val();
        var pass2 = $('#pass2').val();

        if(pass1 ==''){
            $('.alert').html('<p style="color:#f01515;">Escriba nueva contraseña.</p>');
            $('.alert').slideDown();
            return false;
        }

        if($('#pass1').val().length < 5){
            $('.alert').html('<p style="color:#f01515;">La contraseña debe tener 5 caractereas mínimo.</p>');
            $('.alert').slideDown();
            return false;
        }

        if(pass1 != pass2)
        {
            $('.alert').html('<p style="color:#f01515;">Las contraseñas no son iguales.</p>');
            $('.alert').slideDown();
            return false;
        }
        $.ajax({
            url:  '../sistema/ajax.php',
            type: "POST",
            async : true,
            data: $('#formChangePassword').serialize(),

            beforeSend: function(){
                $(".loading").show();
                $('.alert').hide();
            },

            success: function(response)
            {
                var infoData = JSON.parse(response);
                var alert = '';
                if(infoData.cod == '00'){
                    alert = '<p style="color:#44d290;">'+infoData.msg+'</p><br><a href="index.php" style="display:block;margin-bottom:10px;color:#24367d;font-size: 12pt;">Iniciar sesión</a><br> ';
                    $('#txtEmail').val('');
                    $('#btnChangePass').hide();
                    $('#pass1').hide();
                    $('#pass2').hide();
                }else{
                    alert = '<p style="color:#f01515;">'+infoData.msg+'</p>';
                }
                $('.alert').html(alert);
                $('.alert').slideDown();
                $(".loading").hide();
            },
            error: function(error) {
            }
        });

    });
});

function fntEmailValidate(email){
    var stringEmail = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
    if (stringEmail.test(email) == false){
        return false;
    }else{
        return true;
    }
}