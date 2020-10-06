(function($) {

    $(document).ready(function() {
        $(document).on('click', '.add_to_wishlist_button', function(e) {
            
            var $this = $(this),
                product_id = $this.attr( 'data-product-id' ),
                product_wrap = $('.add-to-wishlist-' + product_id),
                data = {
                    action: WISHLIST_SCRIPTS.actions.add_to_wishlist_action,
                    context: 'frontend',
                    add_to_wishlist: product_id,
                    product_type: $this.data('product-type'),
                    wishlist_id: $this.data('wishlist-id' ),
                    fragments: product_wrap.data('fragment-options')
                };

                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: WISHLIST_SCRIPTS.ajax_url,
                    data: data,
                    success: function( response ) {
                        if(response.success) {
                            $this.replaceWith(data.fragments.already_in_wishlist_text + ' <a href="'+data.fragments.wishlist_url+'">'+data.fragments.browse_wishlist_text+'</a>');
                        }
                    },
                    error: function( response ) {
                        console.log(response);
                    }
                });
                
            


        });
    });

        /**
     * Retrieve fragments that need to be refreshed in the page
     *
     * @param search string Ref to search among all fragments in the page
     * @return object Object containing a property for each fragments that matches search
     * @since 3.0.0
     */
    function retrieve_fragments( search ) {
        var options = {},
            fragments = null;

        if( search ){
            if( typeof search === 'object' ){
                search = $.extend( {
                    fragments: null,
                    s: '',
                    container: $(document),
                    firstLoad: false
                }, search );

                if( ! search.fragments ) {
                    fragments = search.container.find('.wishlist-fragment');
                } else {
                    fragments = search.fragments;
                }

                if( search.s ){
                    fragments = fragments.not('[data-fragment-ref]').add(fragments.filter('[data-fragment-ref="' + search.s + '"]'));
                }

                if( search.firstLoad ){
                    fragments = fragments.filter( '.on-first-load' );
                }
            }
            else {
                fragments = $('.wishlist-fragment');

                if (typeof search === 'string' || typeof search === 'number') {
                    fragments = fragments.not('[data-fragment-ref]').add(fragments.filter('[data-fragment-ref="' + search + '"]'));
                }
            }
        }
        else{
            fragments = $('.wishlist-fragment');
        }

        fragments.each( function(){
            var t = $(this),
                id = t.attr( 'class' ).split( ' ' ).filter( ( val ) => { return val.length && val !== 'exists'; } ).join( WISHLIST_SCRIPTS.fragments_index_glue );

            options[ id ] = t.data('fragment-options');
        } );

        return options;
    }

})(jQuery);