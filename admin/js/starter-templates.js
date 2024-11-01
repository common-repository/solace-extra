(function( $ ) {
	'use strict';
    
    // const iframex = document.getElementById('solaceIframe');

    // function postMessageToIframex(data) {
    //     // Mengirim pesan ke iframe dengan data yang ditentukan
    //     iframex.contentWindow.postMessage(data, 'https://solacewp.com'); // Sesuaikan URL sesuai dengan domain iframe
    //     // iframe.contentWindow.postMessage(data, 'https://stagging-solace.djavaweb.com'); // Sesuaikan URL sesuai dengan domain iframe
    //     console.log('Post message sent to iframe:', data);

    // }

    // // DELETE ALL TAB- FROM LIVE
    // const defax = "";
    // const datax = { type: 'deleteLocal', value: defax };

    // // postMessageToIframex(datax);
    // setTimeout(() => {
    //     postMessageToIframex(datax);
    // }, 5000); // 3000 milidetik = 3 detik

    // Function to get the value of a URL parameter by name
    function getParameterByName(name, url) {
        // If URL is not provided, use the current window's URL
        if (!url) url = window.location.href;

        // Escape special characters in the parameter name
        name = name.replace(/[\[\]]/g, "\\$&");

        // Create a regular expression to match the parameter in the URL
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");

        // Execute the regular expression on the URL
        var results = regex.exec(url);

        // If no results are found, return an empty string
        if (!results) return '';

        // If the parameter is present but has no value, return an empty string
        if (!results[2]) return '';

        // Decode the URI component and return the parameter value
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }	

    // Get Data
    let demoType = getParameterByName('type');
    demoType = demoType.toLowerCase().replace(/\s+/g, '-');

    // Get Type
    $('section.page-builder .mycontainer .boxes a .mybox').on("click", function(event) {
        event.preventDefault()
        let getType = $(this).attr('data-type');
        window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-starter-templates&type=' + getType;
    });

    // Get Link
    $('section.start-templates .content-main main .mycontainer').on("click", ".demo", function() {
        let demoUrl = $(this).attr('data-url');
        let demoName = $(this).attr('data-name');
        demoName = demoName.toLowerCase().replace(/\s+/g, '-');    
        let dataUrl = demoUrl;

        // Add cookie page access.
        $.ajax({ 
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'continue-page-access',
                nonce: ajax_object.nonce,
            },
            success: function(response) {
                localStorage.setItem('solaceInfo', dataUrl);
                localStorage.setItem('solaceDemoName', demoName);
        
                let adminUrl = pluginUrl.admin_url + `admin.php?page=dashboard-step5&dataUrl=${dataUrl}&type=${demoType}&demo=${demoName}&nonce=${ajax_object.nonce}`;
                window.location.replace(adminUrl);
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });        

    });    

    // Function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }     

    // Get the 'type' parameter from the URL
    var getSolaceType = getUrlParameter('type');
    // Define the list of valid types
    var listType = ['elementor', 'gutenberg'];
    // Define the redirect URL
    var step2 = pluginUrl.admin_url + 'admin.php?page=dashboard-video';

    // Check if 'type' is empty or not in the list of valid types
    if (getSolaceType === '' || listType.indexOf(getSolaceType) === -1) {
        // Redirect to step2 if conditions are met
        window.location.href = step2;
        return;
    }

    var currentDate = new Date();

    currentDate.setTime(currentDate.getTime() - 24 * 60 * 60 * 1000);
    // Delete All LocalStorage and Cookie;
    localStorage.removeItem('solace_step5_font');
    localStorage.removeItem('solace_step5_color');
    localStorage.removeItem('solace_step5_logo');
    document.cookie = "solace_step5_font=; expires=" + currentDate.toUTCString() + "; path=/";
    document.cookie = "solace_step5_color=; expires=" + currentDate.toUTCString() + "; path=/";
    document.cookie = "solace_step5_logoid=; expires=" + currentDate.toUTCString() + "; path=/";    

	// Initialize an array to store checked checkbox values
	const checkedValues = [];

	// Ajax Checkbox
	$('.start-templates .box-checkbox input[type="checkbox"]').change(function () {
        // Get nonce
        var nonce = ajax_object.nonce;

        // clear search
        $('section.start-templates .content-main aside .mycontainer .box-search input').val('');

		// Get checkbox checked
        let checkboxes = document.querySelectorAll('section.start-templates aside .box-checkbox input[type="checkbox"]');
        let valueCheckboxes = '';
        let getCheckbox = [];
        
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                let checkboxName = checkbox.getAttribute('value');
                getCheckbox.push(checkboxName);
            }
        });

        if ( getCheckbox.length !== 0 ) {
            valueCheckboxes = getCheckbox.join(', ');
        }

        // Get All value checkbox (All Uncheck)
        if ( getCheckbox.length === 0 ) {
            let checkboxes = document.querySelectorAll('section.start-templates aside .box-checkbox input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
                if (!checkbox.checked) {
                    let checkboxName = checkbox.getAttribute('value');
                    getCheckbox.push(checkboxName);
                }
            });

            // valueCheckboxes = getCheckbox.join(', ');
            valueCheckboxes = 'show-all-demos';
        }

        // console.log(valueCheckboxes);

        // Ajax Unchecked
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'action_ajax_checkbox',
                nonce: nonce,
                checked: valueCheckboxes,
                getType: demoType,
            },
            success: function (data) {

                let createDiv = document.createElement('div');
                createDiv.innerHTML = data;

                // console.log(data);
                $('section.start-templates main .mycontainer').html('');
                $( 'section.start-templates .content-main main .mycontainer' ).append(data);

                // Remove button load more
                $('section.start-templates .content-main main .box-load-more button').css('display', 'none');

                if ( valueCheckboxes === 'show-all-demos' ) {
                    $('section.start-templates .content-main main .box-load-more button').css('display', 'block');
                }
            }
        });
	});

	// Ajax Search
	$('section.start-templates .content-main aside .mycontainer .box-search input').keyup(function () {
        // Get nonce
        var nonce = ajax_object.nonce;

		let keyword = $(this).val();
		$('section.start-templates .content-main aside .mycontainer form .box-checkbox input').prop('checked', false);
		// console.log(keyword);
        // console.log(1);
        
        // if (event.keyCode === 8) {
        //     return;
        // }

        if (keyword.length == '') {
            keyword = 'empty';
        }

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'action_ajax_search',
                nonce: nonce,
                keyword: keyword,
                getType: demoType,
            },
            success: function (data) {
                let createDiv = document.createElement('div');
                createDiv.innerHTML = data;

                if (data == 0) {
                    $('section.start-templates main .mycontainer').html('');
                    $('section.start-templates main .mycontainer').append(`<span class="not-found" style="font-size:17px;">No demo found...</span>`);
                    $('section.start-templates main .mycontainer').css('display', 'flex');
                    return;
                }

                $('section.start-templates main .mycontainer .not-found').remove();
                $('section.start-templates main .mycontainer .demo').remove();
                $('section.start-templates main .mycontainer').append(data);
                $('section.start-templates main .mycontainer').css('display', 'flex');

                // Remove button load more
                $('section.start-templates .content-main main .box-load-more button').css('display', 'none');

                if ( keyword === 'empty' ) {
                    $('section.start-templates .content-main main .box-load-more button').css('display', 'block');
                }
            }
        });

	});

    // Ajax load more
    $('section.start-templates .content-main main .box-load-more button').on("click", function(event) {
        // Get nonce
        var nonce = ajax_object.nonce;        

        let totalPosts = parseInt( $(this).attr('show-posts') );
        let totalAllPosts = parseInt( $('section.start-templates .content-main aside .mycontainer span.desc .count').text() );
        // let calc = totalPosts + totalPosts;
        let calc = totalPosts + 9;
        $('section.start-templates .content-main main .box-load-more button').addClass('active');
        $('section.start-templates .content-main main .box-load-more button dotlottie-player').show();

        $(this).addClass('active-button').css({
            'width': '200px',
            'padding-right': '85px',
            'transition': 'width 1s ease, padding-right 1s ease'
        });
        
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'action_load_more',
                nonce: nonce,
                totalPosts: totalPosts,
                getType: demoType,
            },
            success: function (data) {
                setTimeout(() => {
                    $('section.start-templates .content-main main .box-load-more button').attr('show-posts', calc);
                    if ( !(totalAllPosts > calc) ) {
                        $('section.start-templates .content-main main .box-load-more button').hide();
                    }
                    $('section.start-templates main .mycontainer').append(data);
                    $('section.start-templates .content-main main .box-load-more button').removeClass('active');
                    $('section.start-templates .content-main main .box-load-more button dotlottie-player').hide();
                    $('section.start-templates .content-main main .box-load-more button').removeClass('active-button').css({
                        'width': 'auto',
                        'padding-right': '30px',
                        'transition': 'width 1s ease, padding-right 1s ease'
                    });                    
                }, 500);
            }
        });
    }); 
})( jQuery );
