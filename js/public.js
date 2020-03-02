/* Add this block to the global.js file of your theme */
if($(".owl-products").length > 0 && $.fn.owlCarousel !== undefined) {
    $(".owl-products").owlCarousel(Object.assign({},owlOptions,{
        loop: false,
        responsive:{
            0:{
                items:1,
                margin: 0
            },
            480:{
                items:2,
                margin: 0
            },
            768:{
                items:2,
                margin: 30
            },
            992:{
                items:3,
                margin: 30
            },
            1200:{
                items:4,
                // slideBy:2,
                margin: 30
            }
        }
    }));
}