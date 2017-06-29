/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function() {
	$(".product-detail-list-of-images a").fancybox({
		openEffect	: 'none',
		closeEffect	: 'none'
	});
        
        $(".product-detail-main-image img").click(function () {
            $(".product-detail-list-of-images a").first().click();
        })
});