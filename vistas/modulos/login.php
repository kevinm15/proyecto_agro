<div id="back"></div>

<div class="login-box">

  
  <div class="login-logo">

  <p style=" font-size:26pt; font-style:Impact"; align="center">ServiAgro El Roble</p>
  
  </div>

  <div class="login-box-body">

    <p class="login-box-msg">Ingresar al sistema</p>

    <form method="post">

      <div class="form-group has-feedback">

        <input type="text" class="form-control" placeholder="Usuario" name="ingUsuario" required>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>

      </div>

      <div class="form-group has-feedback">

        <input type="password" class="form-control" placeholder="Contraseña" name="ingPassword" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      
      </div>

      <div class="row">
       
        <div class="col-xs-4">

          <button type="submit" class="btn btn-primary btn-block btn-flat">Ingresar</button>
        
        </div>

      </div>

      <?php

       $login = new ControladorUsuarios();
       $login -> ctrIngresoUsuario();
      ?>

    </form>

  </div>

</div>
