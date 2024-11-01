(function ($) {
    'use strict';

    // Get paramter in URL.
    function getParameterByName(name) {
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(window.location.search);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }    

    // Button submit.
    $('#submit-button').on('click', function () {
        const firstname = $('.firstname').val();
        const email = $('.email').val();
        const agreement = $('#agreement').is(':checked') ? 1 : 0;
        const listId = 'EWbwOPUKmvf0mfrH6W8o7w';
        const apiKey = 'UrMQxMjGtuRSR9RCfUYr';

        if ( firstname.length === 0 || email.length === 0 || agreement === 0 ) {
            swal({
                title: "Error!",
                text: "Please complete all fields in the form.",
                icon: "error"
            });
        } else {
            sendDataToSendy(firstname, email, agreement, listId, apiKey);
            localStorage.setItem('solaceStep6', 'success');

            const demoType = getParameterByName('type');
            const demoUrl = getParameterByName('url');
            const demoName = getParameterByName('demo');

            // Function to get the current time
            function getCurrentTime() {
                var now = new Date();
                return now.toLocaleString(); // Using local date and time format
            }

            if (localStorage.getItem('solaceDuration')) {
                // If it exists, remove it first
                localStorage.removeItem('solaceDuration');
            }

            // Get the time when the button is clicked
            var currentTime = getCurrentTime();

            // Save the time value to local storage with the name 'solaceDuration'
            localStorage.setItem('solaceDuration', currentTime);

            // Check if the 'solaceListDemo' key already exists in local storage
            if (localStorage.getItem('solaceRemoveDataDemo')) {
                // Retrieve the existing value
                let existingValue = localStorage.getItem('solaceRemoveDataDemo');

                // Combine the existing value with the new data, separated by a comma
                let combinedValue = existingValue + ',' + demoName;

                // Save the combined value back to local storage
                localStorage.setItem('solaceRemoveDataDemo', combinedValue);
            } else {
                // If 'solaceRemoveDataDemo' doesn't exist in local storage, create it and store the new data
                localStorage.setItem('solaceRemoveDataDemo', demoName);
            }                

            window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-progress&type=' + demoType + '&url=' + demoUrl +'&demo=' + demoName + '&timestamp=' + new Date().getTime();            
        }

    });

    $('button.skip-this-step').on('click', function () {

        const demoType = getParameterByName('type');
        const demoUrl = getParameterByName('url');
        const demoName = getParameterByName('demo');

        window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-progress&type=' + demoType + '&url=' + demoUrl +'&demo=' + demoName + '&timestamp=' + new Date().getTime();         
    });

    function sendDataToSendy(firstname, email, agreement, listId, apiKey) {
        const demoType = getParameterByName('type');
        const demoUrl = getParameterByName('url');
        const demoName = getParameterByName('demo');
        var sendy_url = 'https://mailist.detheme.com/subscribe';

        var data = {
            name: firstname,
            email: email,
            list: listId,
            api_key: apiKey
        };

        $.ajax({
            type: 'POST',
            url: sendy_url,
            data: data,
            success: function (response) {
                if (response == '1') {
                    window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-progress&type=' + demoType + '&url=' + demoUrl + '&demo=' + demoName + '&timestamp=' + new Date().getTime();
                } else {
                    window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-progress&err=1&type=' + demoType + '&url=' + demoUrl + '&demo=' + demoName + '&timestamp=' + new Date().getTime();
                }
            },
            error: function (xhr, status, error) {
                window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-progress&err=1&type=' + demoType + '&url=' + demoUrl + '&demo=' + demoName + '&timestamp=' + new Date().getTime();
                // console.error(xhr.responseText);
            }
        });
    }

    // function sendDataToSendy(firstname, email, agreement, listId, apiKey) {
    //     var sendy_url = 'https://mailist.detheme.com/subscribe';

    //     var data = {
    //         name: firstname,
    //         email: email,
    //         list: listId, 
    //         api_key: apiKey 
    //     };

    //     $.ajax({
    //         type: 'POST',
    //         url: sendy_url,
    //         data: data,
    //         success: function(response) {
    //             if (response == '1') {
    //                 window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-progress';
    //             } else {
    //                 window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-progress&err=1';
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             console.error('AJAX error:', error);
    //             window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-progress&err=1';
    //         }
    //     });
    // }
})( jQuery );
