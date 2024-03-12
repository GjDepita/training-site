<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

	wp_enqueue_script( 'js', get_stylesheet_directory_uri() . '/js/script.js', array('astra-theme-js', 'jquery'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

	// Localize the script with the AJAX URL
    wp_localize_script('js', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));

    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.6.4.min.js', array(), '3.6.4', true);
    wp_enqueue_script('jquery');
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

function shop_products_func(){
	ob_start();
    include( get_stylesheet_directory() .'/shortcodes/wc-shop-products.php' );
    return ob_get_clean();
}
add_shortcode( 'shop-product', 'shop_products_func' );

function get_cart_item_count() {
    echo WC()->cart->get_cart_contents_count();
    die();
}
add_action('wp_ajax_get_cart_item_count', 'get_cart_item_count');
add_action('wp_ajax_nopriv_get_cart_item_count', 'get_cart_item_count');

function add_to_cart_ajax_callback(){
    $product_id = intval($_POST['product_id']);

    if($product_id > 0){
        WC()->cart->add_to_cart($product_id);
        // echo json_encode(array('status' => 'success', 'message' => 'Product added to cart'));
        echo WC()->cart->get_cart_contents_count();
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Invalid product ID'));
    }
    wp_die(); // this is required to terminate immediately and return a proper response
}
add_action('wp_ajax_add_to_cart_ajax', 'add_to_cart_ajax_callback');
add_action('wp_ajax_nopriv_add_to_cart_ajax', 'add_to_cart_ajax_callback');

function get_cart_items_html_ajax() {
    // Check if it's an AJAX request
    if (defined('DOING_AJAX') && DOING_AJAX) {
        // Get the cart contents
        $cart_contents = WC()->cart->get_cart();

        // Output cart items
        if ($cart_contents) {
            ob_start(); // Start output buffering
            ?>
            <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                <thead>
                    <tr>
                        <th class="product-remove"><span class="screen-reader-text"><?php esc_html_e( 'Remove item', 'woocommerce' ); ?></span></th>
                        <th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e( 'Thumbnail image', 'woocommerce' ); ?></span></th>
                        <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                        <th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
                        <th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
                        <th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                    <?php
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                        /**
                         * Filter the product name.
                         *
                         * @since 2.1.0
                         * @param string $product_name Name of the product in the cart.
                         * @param array $cart_item The product in the cart.
                         * @param string $cart_item_key Key for the product in the cart.
                         */
                        $product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            ?>
                            <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                                <td class="product-remove">
                                    <?php
                                        echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            'woocommerce_cart_item_remove_link',
                                            sprintf(
                                                '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                                esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                                /* translators: %s is the product name */
                                                esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
                                                esc_attr( $product_id ),
                                                esc_attr( $_product->get_sku() )
                                            ),
                                            $cart_item_key
                                        );
                                    ?>
                                </td>

                                <td class="product-thumbnail">
                                <?php
                                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                                if ( ! $product_permalink ) {
                                    echo $thumbnail; // PHPCS: XSS ok.
                                } else {
                                    printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                                }
                                ?>
                                </td>

                                <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
                                <?php
                                if ( ! $product_permalink ) {
                                    echo wp_kses_post( $product_name . '&nbsp;' );
                                } else {
                                    /**
                                     * This filter is documented above.
                                     *
                                     * @since 2.1.0
                                     */
                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                }

                                do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

                                // Meta data.
                                echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

                                // Backorder notification.
                                if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
                                }
                                ?>
                                </td>

                                <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
                                    <?php
                                        echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                                    ?>
                                </td>

                                <td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
                                    <?php echo esc_html( $cart_item['quantity'] ); ?>
                                </td>

                                <td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
                                    <?php
                                        echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                </tbody>
            </table>
            <!-- <button type="button" class="button update-cart-button"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button> -->

            <?php
            $output = ob_get_clean(); // Get the buffered output and clean the buffer

            echo $output;
        } else {
            echo 'Cart is empty';
        }

        wp_die();
    }
}
add_action('wp_ajax_get_cart_items_html', 'get_cart_items_html_ajax');
add_action('wp_ajax_nopriv_get_cart_items_html', 'get_cart_items_html_ajax');

function add_to_cart_ajax() {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($product_id > 0) {
        $result = WC()->cart->add_to_cart($product_id, $quantity);

        if ($result) {
            // Get updated cart fragments
            $fragments = WC_AJAX::get_refreshed_fragments();

            // Send the response back
            wp_send_json_success(array('fragments' => $fragments));
        }
    }

    wp_send_json_error();
}
add_action('wp_ajax_add_to_cart', 'add_to_cart_ajax');
add_action('wp_ajax_nopriv_add_to_cart', 'add_to_cart_ajax');

function cart_popout_hover() {
    ?>
    <div class="cart-menu-hover">
        <div class="cart-inner-wrapper">
            
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'cart_popout_hover');









