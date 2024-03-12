<?php 
/*Template Name: Customize Product*/ 

$params = array('posts_per_page' => 6, 'post_type' => 'product');
$wc_query = new WP_Query($params);

if ($wc_query->have_posts()) : ?>
	<div class="product-main-wrap"> 
		<?php while ($wc_query->have_posts()) : $wc_query->the_post();
			$product = wc_get_product( get_the_id() );
			$product_title = $product->get_title();
			$product_image = $product->get_image();
			$product_price = $product->get_price();
			// $product_add_cart = $product->add_to_cart_url();
			$product_id = $product->get_id();
		?>
		<div class="product-wrap">
			<div class="product-img"> <?php echo $product_image ?> </div>
			<h2> <?php echo $product_title; ?> </h2>
			<p>₱ <?php echo $product_price; ?> </p>
			<!-- <a href="<?php  $product_add_cart; ?>" title="">Add to cart</a> -->
			<button class="add-to-cart-btn" data-product-id="<?php echo $product_id; ?>">Add to cart</button>
		</div>

		<?php endwhile; 
		 	  wp_reset_postdata(); 
		 	  else: ?>

		<p>
		<?php _e( 'No Products'); ?>
		</p>
<?php endif; ?>