		<nav class="nav_admin">
			<ul>
				<li><a href="<?php echo $base_url; ?>/sistema/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
			<?php
				if($_SESSION['rol'] == 1){
			 ?>
				<li class="principal">

					<a href="#"><i class="fas fa-users"></i> Usuarios <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="<?php echo $base_url; ?>/sistema/usuarios/registro_usuario.php"><i class="fas fa-user-plus"></i> Nuevo Usuario</a></li>
						<li><a href="<?php echo $base_url; ?>/sistema/usuarios/"><i class="fas fa-users"></i> Lista de Usuarios</a></li>
					</ul>
				</li>
			<?php } ?>
				<li class="principal">
					<a href="#"><i class="fas fa-user"></i> Clientes <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="<?php echo $base_url; ?>/sistema/clientes/registro_cliente.php"><i class="fas fa-user-plus"></i> Nuevo Cliente</a></li>
						<li><a href="<?php echo $base_url; ?>/sistema/clientes/"><i class="far fa-list-alt"></i> Lista de Clientes</a></li>
					</ul>
				</li>
			<?php
				if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){
			 ?>
				<li class="principal">
					<a href="#"><i class="fas fa-truck"></i> Proveedores <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="<?php echo $base_url; ?>/sistema/proveedores/registro_proveedor.php"><i class="fas fa-plus"></i> Nuevo Proveedor</a></li>
						<li><a href="<?php echo $base_url; ?>/sistema/proveedores/"><i class="far fa-list-alt"></i> Lista de Proveedores</a></li>
					</ul>
				</li>
			<?php } ?>
				<li class="principal">
					<a href="#"><i class="fas fa-cubes"></i> Productos <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="<?php echo $base_url; ?>/sistema/productos/"><i class="fas fa-cube"></i> Lista de Productos</a></li>
						<?php
							if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){
						 ?>
							<li><a href="<?php echo $base_url; ?>/sistema/productos/registro_producto.php"><i class="fas fa-plus"></i> Nuevo Producto</a></li>
						<?php }
							if($_SESSION['rol'] == 1){
						 ?>
							<li><a href="<?php echo $base_url; ?>/sistema/marcas/"><i class="fab fa-bandcamp"></i> Marcas</a></li>
							<li><a href="<?php echo $base_url; ?>/sistema/categorias/"><i class="fas fa-check-square"></i> Categorías</a></li>
							<li><a href="<?php echo $base_url; ?>/sistema/presentacion/"><i class="fas fa-file-alt"></i> Presentación Producto</a></li>
							<li><a href="<?php echo $base_url; ?>/sistema/ubicacion/"><i class="fas fa-location-arrow"></i> Ubicacion</a></li>
						<?php } ?>
					</ul>
				</li>
			<?php if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){ ?>
				<li class="principal">
					<a href="#"><i class="fas fa-shopping-basket"></i> Compras <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="<?php echo $base_url; ?>/sistema/compras/nueva_compra.php"><i class="fas fa-plus"></i> Nueva compra </a></li>
						<li><a href="<?php echo $base_url; ?>/sistema/compras/"><i class="fas fa-shopping-basket"></i> Compras </a></li>
					</ul>
				</li>
			<?php
				}
			?>
				<li class="principal">
					<a href="#"><i class="fas fa-cart-plus"></i> Ventas <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="<?php echo $base_url; ?>/sistema/ventas/nueva_venta.php"><i class="fas fa-plus"></i> Nuevo Venta</a></li>
						<li><a href="<?php echo $base_url; ?>/sistema/ventas/"><i class="far fa-newspaper"></i> Ventas</a></li>
					</ul>
				</li>
			<?php
				if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){
			 ?>
				<li><a href="<?php echo $base_url; ?>/sistema/pedidos"><i class="fas fa-box"></i> Pedidos</a></li>
			<?php } ?>
			<?php if($_SESSION['rol'] == 1){ ?>
				<li class="principal">
					<a href="#"><i class="fas fa-cog"></i> Opciones <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="<?php echo $base_url; ?>/sistema/documentos/"><i class="fas fa-file-alt"></i> Documentos</a></li>
						<li><a href="<?php echo $base_url; ?>/sistema/forma_pago/"><i class="far fa-money-bill-alt"></i> Forma Pago</a></li>
						<li><a href="<?php echo $base_url; ?>/sistema/impuestos/"><i class="fas fa-percent"></i> Impuestos </a></li>
						<li><a href="<?php echo $base_url; ?>/sistema/facturas/"><i class="fas fa-file-alt"></i> Rango facturas</a></li>
					</ul>
				</li>
			<?php
				}
			?>
			</ul>
		</nav>