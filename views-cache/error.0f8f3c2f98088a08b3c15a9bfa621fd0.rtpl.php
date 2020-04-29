<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="container-fluid">
	<div class="row">
		<p>Error</p>
	    <?php if( $errorCart != '' ){ ?>

	    <div class="alert alert-danger scroll-menu-custom">
	        <h1 class="text-center">Desculpe, ocorreu um problema.</h1>
	        <h2 class="text-center"><?php echo htmlspecialchars( $errorCart, ENT_COMPAT, 'UTF-8', FALSE ); ?></h2>
	    </div>
	    <?php } ?>

	    <?php if( $errorAddress != '' ){ ?>

	    <div class="alert alert-danger scroll-menu-custom">
	        <h1 class="text-center">Desculpe, ocorreu um problema.</h1>
	        <h2 class="text-center"><?php echo htmlspecialchars( $errorAddress, ENT_COMPAT, 'UTF-8', FALSE ); ?></h2>
	    </div>
	    <?php } ?>

	</div>
</div>
