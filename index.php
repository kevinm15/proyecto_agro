<?php 
    session_start();
    require_once ("header.php");
?>
	<section class="containerPage">
        <section id="pag_productos">
            <h3>TE RECOMENDAMOS</h3>
            <hr>
            <section id="listado_productos">
                <section id="contenedor_listado_productos">
                    <section id="elementos_producto">
                        <aside>
                            <h4>Categorías</h4>
                            <?php echo $ulCat; ?>
                        </aside>
                        <section class="group_p">
                            <h4 class="hCategoria"><?php echo $tituloCategoria; ?></h4>
                            <div>
                        <?php
                            if($result > 0){
                                while ($producto = mysqli_fetch_array($query)) {
                        ?>
                            <article class="item_prod">
                                <a href="<?= $base_url; ?>/producto/?art=<?php echo $producto['producto'].'_'.$producto['codproducto']; ?>">
                                    <div class="img_prod">
                                        <img src="<?php echo $base_url.'/sistema/img/uploads/'.$producto['foto'];?>" alt="Producto">
                                    </div>
                                </a>
                                <div class="info_pro">
                                    <p><?php echo $producto['producto']; ?></p>
                                    <label for="" class="precio"><?php echo SIMBOLO_MONEDA. formatCant($producto['precio']); ?></label>
                                </div>
                                <div class="add_car">
                                    <span type="button" class="btnAddCarrito" onclick="fntAddCarProd(<?= $producto['codproducto'].','.$producto['precio'].',0'; ?>)">Agregar <i class="fas fa-cart-plus"></i></span>
                                </div>
                            </article>
                        <?php
                                }//end While
                            }//end if
                            ?>
                            </div>
                        </section>
                    </section>
                </section>
            </section>
        </section>
	</section>
    <section>
        <div class="container_pag"> 
            <?php
                if($pagina != 1)
                {
             ?>
                <a href="./?<?= $cat; ?>pg=<?= $pagina-1; ?>" title=""><i class="fas fa-chevron-left"></i> Anterior</a>&nbsp;&nbsp;
            <?php }
                if($pagina != $total_paginas)
                {
            ?>
                <a href="./?<?= $cat; ?>pg=<?= $pagina+1; ?>" title="">Siguiente <i class="fas fa-chevron-right"></i></a>
            <?php } ?>
        </div>
    </section>
<?php require_once("footer.php"); ?>
