<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="container-fluid">
	<div class="row" style="float:right;">
		<form action="/products">
          <div class="input-group input-group-sm" style="width: 170px;">
            <input type="text" name="search" class="form-control pull-right" placeholder="Ache o seu produto" value="<?php echo htmlspecialchars( $search, ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            <div class="input-group-btn">
              <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
            </div>
          </div>
        </form>
	</div>
</div>