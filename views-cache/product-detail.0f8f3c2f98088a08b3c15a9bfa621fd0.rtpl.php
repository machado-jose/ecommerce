<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2><?php echo htmlspecialchars( $product["desproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-content-right">
                    <div class="product-breadcroumb">
                        <a href="/">Home</a>
                        <a href=""><?php echo htmlspecialchars( $product["desproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?></a>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="product-images">
                                <div class="product-main-img">
                                    <img src="<?php echo htmlspecialchars( $product["desphoto"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="product-inner">
                                <h2 class="product-name"><?php echo htmlspecialchars( $product["desproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?></h2>
                                <div class="product-inner-price">
                                    <ins>R$ <?php echo formatPrice($product["vlprice"]); ?></ins>
                                </div>    
                                
                                <form action="/cart/<?php echo htmlspecialchars( $product["idproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?>/add" class="cart" method="POST">
                                    <div class="quantity">
                                        <input type="number" size="4" class="input-text qty text" title="Qty" value="1" name="qtd" min="1" step="1">
                                    </div>
                                    <button class="add_to_cart_button" type="submit">Adicionar no carrinho</button>
                                </form>   
                                
                                <div class="product-inner-category">
                                    <p>Categorias:<?php $counter1=-1;  if( isset($categories) && ( is_array($categories) || $categories instanceof Traversable ) && sizeof($categories) ) foreach( $categories as $key1 => $value1 ){ $counter1++; ?> <a href="/categories/<?php echo htmlspecialchars( $value1["idcategory"], ENT_COMPAT, 'UTF-8', FALSE ); ?>/1"><?php echo htmlspecialchars( $value1["descategory"], ENT_COMPAT, 'UTF-8', FALSE ); ?></a><?php } ?>

                                </div> 
                                
                                <div role="tabpanel">
                                    <ul class="product-tab" role="tablist">
                                        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Descrição</a></li>
                                        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Avaliações</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane fade in active" id="home">
                                            <h2 class="text-center">Descrição do Produto</h2>
                                            <?php echo htmlspecialchars_decode($product["description"]); ?>     
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade" id="profile">
                                            <h2>Reviews</h2>
                                            <div class="submit-review">
                                                <p><label for="name">Name</label> <input name="name" type="text"></p>
                                                <p><label for="email">Email</label> <input name="email" type="email"></p>
                                                <div class="rating-chooser">
                                                    <p>Your rating</p>

                                                    <div class="rating-wrap-post">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                </div>
                                                <p><label for="review">Your review</label> <textarea name="review" id="" cols="30" rows="10"></textarea></p>
                                                <p><input type="submit" value="Submit"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>                               
                           </div>
                    </div>                   
                </div>                    
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <h2 class="text-center">Ficha Técnica</h2>
        <table class="table table-sm table-striped table-bordered table-hover">
            <tbody>
                <tr>
                    <td class="text-center">Peso</td>
                    <td class="text-center">
                        <?php echo htmlspecialchars( $product["vlweight"], ENT_COMPAT, 'UTF-8', FALSE ); ?> g
                    </td>            
                </tr>
                <tr">
                    <td class="text-center">Altura</td>
                    <td class="text-center">
                        <?php echo htmlspecialchars( $product["vlheight"], ENT_COMPAT, 'UTF-8', FALSE ); ?> cm
                    </td>
                </tr>
                <tr>
                    <td class="text-center">Largura</td>
                    <td class="text-center">
                        <?php echo htmlspecialchars( $product["vlwidth"], ENT_COMPAT, 'UTF-8', FALSE ); ?> cm
                    </td>
                </tr>
                <tr>
                    <td class="text-center">Comprimento</td>
                    <td class="text-center">
                        <?php echo htmlspecialchars( $product["vllength"], ENT_COMPAT, 'UTF-8', FALSE ); ?> cm
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</div>