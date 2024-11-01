(function( $ ) {
	'use strict';

    // Get nonce
    var nonce = ajax_object.nonce;    

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

    // Update Full Progress Text
    function updatePercent(percentage, bar) {
        var progress = percentage / 3;
        if ( bar === '.bar2') {
            var formattedProgress = Math.floor(progress); // Round down to the nearest whole number
            if ( formattedProgress <= 75 || formattedProgress > 75) {
                formattedProgress = 75;
            }
        } else if ( bar === '.bar4') {
            var formattedProgress = Math.floor(progress); // Round down to the nearest whole number
            if ( formattedProgress >= 75 || formattedProgress <= 75) {
                formattedProgress = 100;
            }
        } else {
            // Default Bar1
            var formattedProgress = Math.floor(progress); // Round down to the nearest whole number
            if ( formattedProgress > 25) {
                formattedProgress = 25;
            }             
        }
        $('section.progress-import .mycontainer .boxes .percent').text(formattedProgress + '%');  
    }
    
    // Update Full Progress Width
    function updateProgressFull(targetPercentage, dur, bar) {
        var progress = 0;
        var duration = dur;
        var interval = 10;
        var totalSteps = duration / interval;
        var step = (targetPercentage / totalSteps);

        // Variable to store the width style in a string
        var getWidthBar = $('section.progress-import .mycontainer .boxes .boxes-bar ' + bar + ' .progress').attr('style');

        if (typeof getWidthBar === 'undefined') {
            getWidthBar = 'width: 0%';
        }

        // console.log(getWidthBar);

        // Matching the numbers using regular expression (regex)
        var widthNumber = getWidthBar.match(/\b\d+(?:\.\d+)?(?=%)\b/);

        // If there's a match, taking the first number
        progress = widthNumber ? Math.floor(parseFloat(widthNumber[0])) : 0;

        var timer = setInterval(function() {
            if (progress < targetPercentage) {
                progress += step;
                if ( progress > 100) {
                    progress = 100;
                }

                $('section.progress-import .mycontainer .boxes .boxes-bar ' + bar + ' .progress').css('width', progress + '%');
                updatePercent(progress, bar);
            } else {
                clearInterval(timer);
            }
        }, interval);
    }

    // Function to animate progress text bars based on provided information
    var stepTextIntervals = {};
    let lastTimeTextActive = Date.now(); // Track the last time the tab was active
    function animateProgressText(barInfoArray, totalDuration) {
        var interval = 10; // Interval time for each step in milliseconds
        var totalSteps = totalDuration / interval;

        var currentBarIndex = 0;
        var startProgress = 0;
        function setIntervalStart() {
            // Iterate through barInfoArray to set intervals for each step
            barInfoArray.forEach(function(barInfo, index) {
                var step = barInfo.StagedStep;        
                // Set interval to update startProgress bars
                stepTextIntervals[step] = setInterval(function () {
                    const now = Date.now(); // Get current time
                    const timeElapsed = now - lastTimeTextActive; // Calculate elapsed time since last active
                    lastTimeTextActive = now; // Update last active time

                    // Determine how many steps to advance based on elapsed time
                    const stepsToAdvance = Math.floor(timeElapsed / interval);

                    if (currentBarIndex < barInfoArray.length) {
                        var currentBarInfo = barInfoArray[currentBarIndex];

                        if (startProgress < currentBarInfo.targetPercentage) {
                            startProgress += currentBarInfo.step * stepsToAdvance;

                            if (startProgress > 100) {
                                startProgress = 100;
                            }

                            // Update the current startProgress bar
                            if ( currentBarInfo.StagedStep === 'step1' ) {
                                $(currentBarInfo.selector).text( Math.min( 100, Math.floor( startProgress / 4 ) ) + '%' );
                            } else if ( currentBarInfo.StagedStep === 'step2' ) {
                                $(currentBarInfo.selector).text( Math.min( 100, Math.floor( startProgress / 2 + 25) ) + '%' );
                            } else if ( currentBarInfo.StagedStep === 'step4' ) {
                                $(currentBarInfo.selector).text( Math.min( 100, Math.floor( startProgress / 4 + 75 ) ) + '%' );
                            }
                        } else {
                            // If the current startProgress bar has reached its target, move to the next one
                            currentBarIndex++;
                            startProgress = 0;
                        }
                    } else {
                        // All startProgress bars have completed, stop the interval
                        clearInterval(stepTextIntervals[step]);
                    }
                }, interval);
            });
        }

        if ( barInfoArray[currentBarIndex].StagedStep === 'step2' ) {
            updateProgressFull(100, 500, '.bar1');    
            setTimeout(function() {
                setIntervalStart();
            }, 500);
        } else if ( barInfoArray[currentBarIndex].StagedStep === 'step4' ) {
            updateProgressFull(100, 500, '.bar2');    
            setTimeout(function() {
                setIntervalStart();
            }, 500);
        } else {
            setIntervalStart();
        }        
    }    

    // Event listener to reset last active time when the tab becomes active again
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            lastTimeTextActive = Date.now(); // Reset last active time
        }
    });     

    // Function to animate progress bars based on provided information
    var stepIntervals = {};
    let lastTimeProgressActive = Date.now(); // Track the last time the tab was active    
    function animateProgress(barInfoArray, totalDuration) {
        var interval = 10; // Interval time for each step in milliseconds
        var totalSteps = totalDuration / interval;

        var currentBarIndex = 0;
        var progress = 0;

        function setIntervalStart() {
            // Iterate through barInfoArray to set intervals for each step
            barInfoArray.forEach(function(barInfo, index) {
                var step = barInfo.StagedStep;

                // Set interval for current step
                stepIntervals[step] = setInterval(function () {
                    const now = Date.now(); // Get current time
                    const timeElapsed = now - lastTimeProgressActive; // Calculate elapsed time since last active
                    lastTimeProgressActive = now; // Update last active time

                    // Determine how many steps to advance based on elapsed time
                    const stepsToAdvance = Math.floor(timeElapsed / interval);                    

                    if (currentBarIndex < barInfoArray.length) {
                        var currentBarInfo = barInfoArray[currentBarIndex];

                        if (progress < currentBarInfo.targetPercentage) {
                            progress += currentBarInfo.step * stepsToAdvance;

                            if (progress > 100) {
                                progress = 100;
                            }

                            // Update the current progress bar
                            $(currentBarInfo.selector).css('width', progress + '%');
                        } else {
                            // If the current progress bar has reached its target, move to the next one
                            currentBarIndex++;
                            progress = 0;
                        }
                    } else {
                        // All progress bars have completed, stop the interval
                        clearInterval(stepIntervals[step]);
                    }
                }, interval);
            });
        }

        // console.log(barInfoArray[currentBarIndex].StagedStep);

        if ( barInfoArray[currentBarIndex].StagedStep === 'step2' ) {
            updateProgressFull(100, 500, '.bar1');    
            setTimeout(function() {
                setIntervalStart();
            }, 500);
        } else if ( barInfoArray[currentBarIndex].StagedStep === 'step4' ) {
            updateProgressFull(100, 500, '.bar2');    
            setTimeout(function() {
                setIntervalStart();
            }, 500);
        } else {
            setIntervalStart();
        }
    }

    // Event listener to reset last active time when the tab becomes active again
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            lastTimeProgressActive = Date.now(); // Reset last active time
        }
    });      

    var interval = 10; // Interval time for each step in milliseconds
    var bar1 = 60;
    var bar2 = 75;
    var bar3 = 75;
    var bar4 = 30;
    // var bar1 = 8;
    // var bar2 = 10;
    // var bar3 = 10;
    // var bar4 = 6;    

    var totalDuration = bar1 * 1000 + bar2 * 1000 + bar3 * 1000 + bar4 * 1000; // Total animation duration in milliseconds

    // Update text did you know
    update_info_did_you_know();
    function update_info_did_you_know() {
        // Make a GET request to the API endpoint
        fetch('https://solacewp.com/api/wp-json/wp/v2/info')
        .then(response => {
        // Check if the response is successful (status code 200-299)
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        // Parse the JSON data from the response
        return response.json();
        })
        .then(data => {
            // Handle the successful response
            var titles = data.map(function (item) {
                return item.title.rendered;
            });            

            // Shuffle the titles array (randomize the order)
            titles.sort(function () {
                return Math.random() - 0.5;
            });            

            // Log the shuffled titles to the console
            // console.log('Shuffled Titles:', titles);

            var currentIndex = 0;

            function updateText() {
                // Get the HTML element
                var descElement = document.querySelector('section.progress-import .mycontainer .boxes .box-did-you-know .box-desc span.desc');
                
                // Check if the element exists
                if (descElement) {
                    // Update the text with the current title
                    descElement.textContent = titles[currentIndex];
                
                    // Increment the index for the next title
                    currentIndex = (currentIndex + 1) % titles.length;
                } else {
                    console.error('Element not found. Check your HTML structure or selector.');
                }
            }
            
            // Initial update
            updateText();
            
            // Set interval to update text every 4 seconds
            setInterval(updateText, 4000);

        })
        .catch(error => {
            // Handle errors
            console.error('Error fetching API:', error);
        });
    }

    // Animations Import Step1
    function solace_extra_import_step1() {
        var step1Text = [
            { 
                targetPercentage: 100, 
                step: 100 / (bar1 * 1000 / interval), 
                selector: '.progress-import .boxes .percent',
                StagedStep: 'step1',
            },
        ];                    
        var step1 = [
            { 
                targetPercentage: 100, 
                step: 100 / (bar1 * 1000 / interval), 
                selector: '.bar1 .progress',
                StagedStep: 'step1',
            },
        ];
        animateProgressText(step1Text, totalDuration);
        animateProgress(step1, totalDuration);       
    }

    // Animations Import Step2 & Step3
    function solace_extra_import_step2_and_step3() {
        var step2Text = [
            { 
                targetPercentage: 100, 
                step: 100 / ( (bar2 + bar3) * 1000 / interval), 
                selector: '.progress-import .boxes .percent',
                StagedStep: 'step2',
            },
        ];            
        var step2 = [
            { 
                targetPercentage: 100,
                step: 100 / ( (bar2 + bar3 ) * 1000 / interval),
                selector: '.bar2 .progress',
                StagedStep: 'step2',
            },
        ];
        animateProgressText(step2Text, totalDuration);     
        animateProgress(step2, totalDuration);    
    }    

    // Animations Import Step4
    function solace_extra_import_step4() {
        var step4Text = [
            { 
                targetPercentage: 100, 
                step: 100 / (bar4 * 1000 / interval), 
                selector: '.progress-import .boxes .percent',
                StagedStep: 'step4',
            },
        ];                    
        var step4 = [
            {
                targetPercentage: 100,
                step: 100 / (bar4 * 1000 / interval),
                selector: '.bar4 .progress',
                StagedStep: 'step4',
            },
        ];
        animateProgressText(step4Text, totalDuration);
        animateProgress(step4, totalDuration);    
    }    

    remove_data_import();
    function remove_data_import() {
        let prevDemo = '';
        if ( localStorage.getItem('solaceRemoveDataDemo') ) {
            prevDemo = localStorage.getItem('solaceRemoveDataDemo');
        } else {
            prevDemo = 'blank';
        }

        solace_extra_import_step1();

        if ( localStorage.getItem('solaceRemoveImported') === "remove") {
            $.ajax({ 
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'action-delete-previously-imported',
                    nonce: nonce,
                    prevDemo: prevDemo,
                },
                success: function(response) {
                    $('section.progress-import .mycontainer .boxes span.info-import').text('');
                    $('section.progress-import .mycontainer .boxes .step-import').text('Delete Previously Imported Sites...');
                    // Remove list data demo
                    localStorage.removeItem('solaceRemoveDataDemo');
                    activate_theme();
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                    alert('Error Delete Previously Imported Sites: ' + errorThrown);
                    window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-starter-templates&type=elementor';
                }
            });
        } else {
            activate_theme();
        }
    }    

    function activate_theme() {
        $.ajax({ 
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'action-install-activate-theme',
                nonce: nonce,
            },
            success: function(response) {
                console.log(response);
                console.log ('Sukses Instal Activate Theme, Now Install & Activate Plugin');
                activate_plugin();
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
                alert('An error occurred during Theme activation: ' + errorThrown);
                window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-starter-templates&type=elementor';
            }
        });
    }

    function activate_plugin(){
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'action-install-activate-plugin',
                nonce: nonce,
                getDemo: getParameterByName('demo'),
            },
            success: function(response) {
                clearInterval(stepTextIntervals['step1']);
                clearInterval(stepIntervals['step1']);

                setTimeout(function() {
                    console.log(response);
                    console.log ('Sukses Instal Activate Plugin, Now Importing Elementor ZIP');
                    $('section.progress-import .mycontainer .boxes span.info-import').text('');
                    $('section.progress-import .mycontainer .boxes .step-import').text('Importing Content...');
                    solace_extra_import_step2_and_step3();
                    import_zip();
                    // setTimeout(function() {
                    //     window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-congratulations&timestamp=' + new Date().getTime();
                    // }, 2000);
                }, 500);
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
                alert('An error occurred during Plugin activation: ' + errorThrown);
                window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-starter-templates&type=elementor';
            }
        });
    }

    function import_zip() {
        let prevDemo = '';
        if ( localStorage.getItem('solaceRemoveDataDemo') ) {
            prevDemo = localStorage.getItem('solaceRemoveDataDemo');
        } else {
            prevDemo = 'blank';
        }        
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'action-import-zip',
                nonce: nonce,
                getDemoUrl: getParameterByName('url'),
                prevDemo: prevDemo,
            },
            success: function(response) {
                clearInterval(stepTextIntervals['step2']);
                clearInterval(stepIntervals['step2']);           

                setTimeout(function() {
                    console.log(response);
                    console.log ('Sukses Importing Elementor ZIP, Now Importing Customizer');
                    $('section.progress-import .mycontainer .boxes span.info-import').text('Importing customizer.');
                    solace_extra_import_step4();            
                    import_menu();
                }, 500);
            },
            // error: function (xhr, status, error) {
            //     console.error('Error importing Elementor ZIP:', error);
            // }
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
                alert('An error occurred during Content import: ' + errorThrown);
                window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-starter-templates&type=elementor';
            }
        });
    }

    function import_menu() {    
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'action_import_menu',
                nonce: nonce,
                getUrl: getParameterByName('url'),
                getDemo: getParameterByName('demo'),
            },
            success: function (response) {
                // console.log(response);
                import_customizer();
            },
            // error: function (xhr, status, error) {
            //     console.error('Error importing menu', error);
            // }
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
                alert('An error occurred during menu import: ' + errorThrown);
                window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-starter-templates&type=elementor';
            }
        });
    }

    function import_customizer() {
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'post',
            data: {
                action: 'action-import-customizer',
                nonce: nonce,
                getUrl: getParameterByName('url'),
            },
            success: function(response) {
                console.log(response);
                console.log ('Sukses Instal Importing Customizer, NOW Importing Widget');
                import_widgets();
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
                alert('An error occurred during customizer import: ' + errorThrown);
                window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-starter-templates&type=elementor';
            }
        }); 
    }

    function import_widgets() {
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'post',
            data: {
                action: 'action-import-widgets',
                nonce: nonce,
                getUrl: getParameterByName('url'),
            },
            success: function(response) {
                console.log(response);
                $('section.progress-import .mycontainer .boxes span.info-import').text('Final touches...');

                clearInterval(stepTextIntervals['step4']);
                clearInterval(stepIntervals['step4']);
                updateProgressFull(100, 500, '.bar4');

                setTimeout(function() {
                    window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-congratulations&timestamp=' + new Date().getTime();
                }, 2000);
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
                alert('An error occurred during widgets import: ' + errorThrown);
                window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-starter-templates&type=elementor';
            }
        });
    }
})( jQuery );
