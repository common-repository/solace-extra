(function( $ ) {
	'use strict';

    // Remove cookie
    $(window).load(function() {

        // Get the value of the "page" parameter from the URL
        var urlParams = new URLSearchParams(window.location.search);
        var page = urlParams.get('page');

        // Check if the "page" parameter exists in the URL
        if(!page) {
            console.log("The 'page' parameter is not found in the URL.");
        }

        if ( 'dashboard-congratulations' === page ) {
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'remove-cookie-continue-page-access',
                    nonce: ajax_object.nonce,
                    mypage: page,
                },
                success: function(response) {
                    // console.log({response});
                    // alert('success!');
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        }

    });
})( jQuery );

jQuery(document).ready(function($) {
    if ($('body').hasClass('toplevel_page_solace')) {
        $('.mycontainer .box-menu ul li:nth-child(1)').addClass('active');
    }

    if ($('body').hasClass('solace_page_dashboard')) {
        $('.mycontainer .box-menu ul li:nth-child(1)').addClass('active');
    }

    if ($('body').hasClass('solace_page_dashboard-starter-templates')) {
        $('.mycontainer .box-menu ul li:nth-child(3)').addClass('active');
    }
});

jQuery(document).ready(function($) {
    function setLogoURL(logo_url) {
        // alert(logo_url);
        // AJAX request to save the logo URL
        $.ajax({
			url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'update_logo2',
                logo_url: logo_url
            },
            success: function(response) {
                console.log('Logo URL updated successfully.');
            },
            error: function(error) {
                console.error('Error updating logo URL:', error);
            }
        });
    }

    // $(".logo_default").on("click", function(e) {
    //     e.preventDefault(); 

    //     $(".preview .site-logo .brand img.sol_logo_new").remove();

    //     $(".preview .site-logo .brand img.neve-site-logo").css("display", "block");
    //     $(".logo_default").css("display", "none");
    //     $(".logo-buttons input[type='submit']").val("Upload Your Logo");
    //     $(".palette-buttons img.logo").css("display", "none");
    // });


});
