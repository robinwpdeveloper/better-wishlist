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

  $(window).load(function () {
    var dialogbox = $('body').find('.added-to-wishlist-dialog-box')
        addToCartBox = $('body').find('.added-to-cart-dialog-box');
    $(dialogbox).click(function(){
        $(dialogbox).css('visibility', 'hidden');
        $(dialogbox).css('opacity', '0');
    });

    $(addToCartBox).click(function(){
      $(addToCartBox).css('visibility', 'hidden');
      $(addToCartBox).css('opacity', '0');
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
                    if(response.success) {
                        $this.replaceWith(data.fragments.already_in_wishlist_text + ' <a href="'+data.fragments.wishlist_url+'">'+data.fragments.browse_wishlist_text+'</a>');

                        show_wishlist_modal();
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
                    action: BETTER_WISHLIST_SCRIPTS.actions.multiple_product_add_to_cart_action,
                    product_ids: $product_ids
                },
                success: function( response ) {
                    console.log(response);
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

            // console.log($('.wishlist_table tr.wishlist-row').length);

            // return false;


            $.ajax({
                type: 'POST',
                url: BETTER_WISHLIST_SCRIPTS.ajax_url,
                data: {
                    action: BETTER_WISHLIST_SCRIPTS.actions.remove_from_wishlist_action,
                    product_id: $product_id
                },
                success: function( response ) {

                    if (response.success) {
                      $(product_row).remove();
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

          $.ajax({
              type: 'POST',
              url: BETTER_WISHLIST_SCRIPTS.ajax_url,
              data: {
                  action: BETTER_WISHLIST_SCRIPTS.actions.single_product_add_to_cart_action,
                  product_id: $product_id
              },
              success: function( response ) {

                  if (response.success) {
                    show_add_cart_modal();
                  }
              },
              error: function( response ) {
                  console.log(response);
              }
          });

     });

  });
    
})(jQuery);