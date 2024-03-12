jQuery(function($){

    function updateCartItemCount() {

        $.ajax({
            url: ajax_object.ajaxurl, 
            type: 'POST',
            data: {
                action: 'get_cart_item_count' 
            },
            success: function(response) {
                $('.cart-number').text(response);
            }
        }); 
    }
    updateCartItemCount();

    $(document.body).on('updated_wc_div', function(){
        //updateCartItemCount();
        console.log("clike");
    });

    // Ajax request for adding to cart
    $('.add-to-cart-btn').on('click', function(e){
        e.preventDefault();

        var product_id = $(this).data('product-id');

        $.ajax({
            type: 'POST',
            url: ajax_object.ajaxurl,
            data: {
                action: 'add_to_cart_ajax',
                product_id: product_id
            },
            success: function(response){
                // Handle the response as needed
                // console.log(response);
                $('.cart-number').text(response);
            }
        });
    });

    function updateCartItems() {
        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_cart_items_html'
            },
            success: function (response) {
                $('.cart-inner-wrapper').html(response);
            }
        });
    }

    // Trigger the updateCartItems function when the page loads
    updateCartItems();

    $(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
        updateCartItems();
    });

    // Listen for clicks on "Add to Cart" buttons and trigger the AJAX request
    $('.add-to-cart-btn').on('click', function (e) {
        e.preventDefault();

        var $thisbutton = $(this);

        // Perform the necessary logic to add the product to the cart
        var data = {
            action: 'add_to_cart',
            product_id: $thisbutton.data('product-id'),
            quantity: $thisbutton.data('quantity') || 1,
        };

        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: data,
            success: function (response) {
                // Trigger the 'added_to_cart' event
                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
            }
        });
    });

    function updateCartItems() {
        // Make AJAX request to update cart items
        $.ajax({
            type: 'POST',
            url: ajax_object.ajaxurl,
            data: { action: 'get_cart_items_html' },
            success: function (response) {
                // Update the cart items on the page
                $('.cart-inner-wrapper').html(response);
            }
        });
    }
    updateCartItems();

    $(".cart-menu-hover").hide();
    $(".cart-menu").hover(
        function() {
            
            $(".cart-menu-hover").fadeIn(500);
        },
        function() {
            // Hover out: hide the element
            $(".cart-menu-hover").fadeOut(500);
        }
    );

    $(".cart-menu-hover").hover(
        function() {
            // Hover out: hide the element
            $(this).show();
        }
    );

    // $('button.update-cart-button').on('click', function () {
    //     e.preventDefault();
    //     $.ajax({
    //             url: ajax_object.ajaxurl, 
    //             type: 'POST',
    //             data: {
    //                 action: 'get_cart_item_count' 
    //             },
    //             success: function(response) {
    //                 $('.cart-number').text(response);
    //             }
    //         }); 
    //     updateCartItemCount();
    // });
    // $(document.body).on('updated_wc_div', function(){
    //     updateCartItemCount();
    // });

    // function updateCartItems() {
    //     $.ajax({
    //         url: ajax_object.ajaxurl,
    //         type: 'POST',
    //         data: {
    //             action: 'get_cart_items_html'
    //         },
    //         success: function (response) {
    //             $('.cart-inner-wrapper').html(response);
    //         }
    //     });
    // }

    

    // // Handle update cart button click
    // $('.update-cart-button').on('click', function () {
    //     // Make AJAX request to update cart items
    //     $.ajax({
    //         type: 'POST',
    //         url: ajax_object.ajaxurl,
    //         data: { action: 'get_cart_items_html' },
    //         success: function (response) {
    //             // Update the cart items on the page
    //             $('.cart-inner-wrapper').html(response);
    //         }
    //     });
    // });

    
});