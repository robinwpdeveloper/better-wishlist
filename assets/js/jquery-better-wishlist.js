(function($) {

  function show_wishlist_modal() {
    var dialogbox = $('body').find('.added-to-wishlist-dialog-box');
    
    dialogbox.css('visibility', 'visible');
    dialogbox.css('opacity', '1');
  }

  function show_add_cart_modal() {
    var dialogbox = $('body').find('.added-to-cart-dialog-box');
    
    dialogbox.css('visibility', 'visible');
    dialogbox.css('opacity', '1');
  }

  function all_product_add_cart_modal() {
    var allCart = $('body').find('.all_product_added-to-cart-dialog-box');
    
    allCart.css('visibility', 'visible');
    allCart.css('opacity', '1');
  }

  function remove_from_wishlist_modal() {
    var removeWishlist = $('body').find('.product_remove_from_wishlist_dialog_box');
    
    removeWishlist.css('visibility', 'visible');
    removeWishlist.css('opacity', '1');
  }

  $(window).load(function () {
    var dialogbox = $('body').find('.added-to-wishlist-dialog-box')
        addToCartBox = $('body').find('.added-to-cart-dialog-box')
        allCart = $('body').find('.all_product_added-to-cart-dialog-box')
        removeWishlist = $('body').find('.product_remove_from_wishlist_dialog_box');
    $(dialogbox).click(function(){
        $(dialogbox).css('visibility', 'hidden');
        $(dialogbox).css('opacity', '0');
    });

    $(addToCartBox).click(function(){
      $(addToCartBox).css('visibility', 'hidden');
      $(addToCartBox).css('opacity', '0');
    });

    $(allCart).click(function(){
      $(allCart).css('visibility', 'hidden');
      $(allCart).css('opacity', '0');
    });

    $(removeWishlist).click(function(){
      $(removeWishlist).css('visibility', 'hidden');
      $(removeWishlist).css('opacity', '0');
    });

    $('.popupCloseButton').click(function(){
      $(dialogbox).css('visibility', 'hidden');
      $(dialogbox).css('opacity', '0');
    });

});


  $(document).ready(function() {

    $(document).on('click', '.add_to_wishlist_button', function(e) {
        
        var $this = $(this),
            product_id = $this.attr( 'data-product-id' ),
            product_wrap = $('.add-to-wishlist-' + product_id),
            data = {
                _ajax_nonce: BETTER_WISHLIST_SCRIPTS.nonce,
                action: BETTER_WISHLIST_SCRIPTS.actions.add_to_wishlist_action,
                context: 'frontend',
                add_to_wishlist: product_id,
                product_type: $this.data('product-type'),
                wishlist_id: $this.data('wishlist-id' ),
                fragments: product_wrap.data('fragment-options')
            };

            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: BETTER_WISHLIST_SCRIPTS.ajax_url,
                data: data,
                success: function( response ) {
                    console.log(response);
                    if(response.success) {

                        $this.replaceWith(data.fragments.already_in_wishlist_text + ' <a href="'+data.fragments.wishlist_url+'">'+data.fragments.browse_wishlist_text+'</a>');
                        if (response.data.redirects) {
                          window.location.replace(data.fragments.wishlist_url);
                        } else {
                          show_wishlist_modal();
                        }
                        
                    }
                },
                error: function( response ) {
                    console.log(response);
                }
            });
    });

    $(document).on('click', '.bw-multiple-products-add-to-carts', function(e) {
        e.preventDefault();
        
        var $product_ids = $(this).data('product-ids'),
            $product_ids = $product_ids.split(":");

            $.ajax({
                type: 'POST',
                url: BETTER_WISHLIST_SCRIPTS.ajax_url,
                data: {
                    _ajax_nonce: BETTER_WISHLIST_SCRIPTS.nonce,
                    action: BETTER_WISHLIST_SCRIPTS.actions.multiple_product_add_to_cart_action,
                    product_ids: $product_ids
                },
                success: function( response ) {

                  if (response.success) {
                    if ( response.data.removed ) {
                      $('.wishlist_table').remove();
                      $('.multiple-products-add-to-cart').remove();
                    }

                    if ( (response.data.redirects) != null ) {
                      window.location.replace(response.data.redirects);
                    } else {
                      all_product_add_cart_modal();
                    }
                    
                  }
                  //console.log(response);
                  // $('.wishlist_table').remove();
                  // $('.multiple-products-add-to-cart').remove();
                  // all_product_add_cart_modal();
                },
                error: function( response ) {
                    console.log(response);
                }
            });

    });


    $(document).on('click', '.remove_from_wishlist', function(e) {
        e.preventDefault();

        var $this = $(this),
            $product_id = $this.data('product_id'),
            product_row = "#wishlist-row-" + $product_id,
            wishlist_table = $('.wishlist_table');

            $.ajax({
                type: 'POST',
                url: BETTER_WISHLIST_SCRIPTS.ajax_url,
                data: {
                    _ajax_nonce: BETTER_WISHLIST_SCRIPTS.nonce,
                    action: BETTER_WISHLIST_SCRIPTS.actions.remove_from_wishlist_action,
                    product_id: $product_id
                },
                success: function( response ) {

                    if (response.success) {
                      $(product_row).remove();
                      remove_from_wishlist_modal();
                    }
                    if ( $('.wishlist_table tr.wishlist-row').length < 1 ) {
                      $('.multiple-products-add-to-cart').remove();
                      $('.wishlist_table').remove();

                      // var div = $('.no-record-message').length();

                      // if(div != 0) {
                      //   var span = $('span').text('No Records Found.');

                      // }
                    }
                },
                error: function( response ) {
                    console.log(response);
                }
          });

    });

    $(document).on('click', '.single-product-add-to-cart', function(e) {
      e.preventDefault();

      var $this = $(this),
          $product_id = $this.data('product_id');
          product_row = "#wishlist-row-" + $product_id,
          wishlist_table = $('.wishlist_table');

          $.ajax({
              type: 'POST',
              url: BETTER_WISHLIST_SCRIPTS.ajax_url,
              data: {
                  _ajax_nonce: BETTER_WISHLIST_SCRIPTS.nonce,
                  action: BETTER_WISHLIST_SCRIPTS.actions.single_product_add_to_cart_action,
                  product_id: $product_id
              },
              success: function( response ) {

                console.log(response);

                  if (response.success) {
                    if ( response.data.removed ) {
                      $(product_row).remove();
                    }
                    if ( $('.wishlist_table tr.wishlist-row').length < 1 ) {
                      $('.multiple-products-add-to-cart').remove();
                      $('.wishlist_table').remove();
                    }

                    if ( (response.data.redirects) != null ) {
                      window.location.replace(response.data.redirects);
                    } else {
                      show_add_cart_modal();
                    }
                    
                  }
              },
              error: function( response ) {
                  console.log(response);
              }
          });

     });

  });
    
})(jQuery);