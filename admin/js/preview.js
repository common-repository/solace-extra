(function ($) {
    'use strict';

    // Event click Change Color Palette in preview
    $(function () {

        // Link solacewp
        const solacewp = 'https://solacewp.com/';

        // Getting the value of the 'demo' parameter using JavaScript
        var params = new URLSearchParams(window.location.search);
        var demoValue = params.get('demo');

        // Cleaning the demoValue to contain only lowercase letters, numbers, hyphens, and spaces
        demoValue = demoValue.toLowerCase().replace(/[^a-z0-9- ]/g, '');

        // Link demo
        let linkDemo = solacewp + demoValue + '/wp-json/elementor-api/v1/settings';

        // Make AJAX request to the API
        $.ajax({
            url: linkDemo,
            method: 'GET',
            success: function (response) {
                // $('body.solace_page_dashboard-step5 .colorlist a').click(function () {
                $('body.solace_page_dashboard-step5 .colorlist a').click(function () {
                    // Get index color
                    // var index = $(this).index();
                    // const colorValue = this.getAttribute('data-styles');
                    // console.log(colorValue);

                    // const jsonString = JSON.stringify(colorValue);
                    // localStorage.setItem('solace_step5_color', jsonString);

                    // // Active class
                    // $('body.solace_page_dashboard-step5 .colorlist a').removeClass('active');
                    // $(this).addClass('active');

                    // Get container preview
                    
                    // if (0 === index && typeof index === 'number' && !isNaN(index)) {
                    
                    //     console.log(colorValue);
                    //     var jsonString = JSON.stringify(colorValue);
                    //     localStorage.setItem('solace_step5_color', jsonString);

                    // } else if (0 !== index && index < 5 && typeof index === 'number' && !isNaN(index)) {
                    //     let colorsIndex = index;
                    //     let responseColors = response.color_scheme['solace_colors_elementor_' + (colorsIndex + 1)];
                    //     // console.log('BaseColor2:'+responseColors.base_color);

                    //     // BASE FONT base_color
                    //     previewElement.style.setProperty('--sol-color-base-font', responseColors.base_color);
                    //     previewElement.style.setProperty('--e-global-color-text', responseColors.base_color);
                    //     previewElement.style.setProperty('--e-global-color-solcolorbasefont', responseColors.base_color);

                    //     // HEADING 
                    //     previewElement.style.setProperty('--e-global-color-solcolorheading', responseColors.heading_color);
                    //     previewElement.style.setProperty('--sol-color-heading', responseColors.heading_color);

                    //     // LINK BUTTON
                    //     previewElement.style.setProperty('--e-global-color-primary', responseColors.link_button_color);
                    //     previewElement.style.setProperty('--e-global-color-solcolorlinkbuttoninitial', responseColors.link_button_color);
                    //     previewElement.style.setProperty('--sol-color-link-button-initial', responseColors.link_button_color);

                    //     // LINK BUTTON HOVER
                    //     previewElement.style.setProperty('--e-global-color-solcolorlinkbuttonhover', responseColors.link_button_hover_color);
                    //     previewElement.style.setProperty('--sol-color-link-button-hover', responseColors.link_button_hover_color);

                    //     // BUTTON / INITIAL
                    //     previewElement.style.setProperty('--e-global-color-solcolorbuttoninitial', responseColors.button_color);
                    //     previewElement.style.setProperty('--sol-color-button-initial', responseColors.button_color);
                    //     previewElement.style.setProperty('--e-global-color-accent', responseColors.button_color);

                    //     // BUTTON HOVER
                    //     previewElement.style.setProperty('--e-global-color-solcolorbuttonhover', responseColors.button_hover_color);
                    //     previewElement.style.setProperty('--sol-color-button-hover', responseColors.button_hover_color);

                    //     // TEXT SELECTION 
                    //     previewElement.style.setProperty('--e-global-color-solcolorselectioninitial', responseColors.text_selection_color);
                    //     previewElement.style.setProperty('--sol-color-selection-initial', responseColors.text_selection_color);

                    //     // TEXT SELECTION BACKGROUND
                    //     previewElement.style.setProperty('--e-global-color-solcolorselectionhigh', responseColors.text_selection_bg_color);
                    //     previewElement.style.setProperty('--sol-color-selection-high', responseColors.text_selection_bg_color);

                    //     // BORDER
                    //     previewElement.style.setProperty('--e-global-color-solcolorborder', responseColors.border_color);

                    //     // BACKGORUND
                    //     previewElement.style.setProperty('--e-global-color-solcolorbackground', responseColors.background_color);
                    //     previewElement.style.setProperty('--sol-color-background', responseColors.background_color);

                    //     // PAGE TITLE TEXT COLOR
                    //     previewElement.style.setProperty('--e-global-color-solcolorheadpagetitletexting', responseColors.page_title_text_color);
                    //     previewElement.style.setProperty('--sol-color-page-title-text', responseColors.page_title_text_color);
                    //     previewElement.style.setProperty('--e-global-color-solcolorpagetitletext', responseColors.page_title_text_color);

                    //     // PAGE TITLE BACKGROUND
                    //     previewElement.style.setProperty('--e-global-color-solcolorpagetitlebackground', responseColors.page_title_bg_color);
                    //     previewElement.style.setProperty('--sol-color-page-title-background', responseColors.page_title_bg_color);
                    //     previewElement.style.setProperty('--e-global-color-secondary', responseColors.page_title_bg_color);

                    //     // BACKGROUND MENU DROPDOWN
                    //     previewElement.style.setProperty('--sol-color-bg-menu-dropdown', responseColors.bg_menu_dropdown_color);
                    //     previewElement.style.setProperty('--sol-color-border', responseColors.bg_menu_dropdown_color);

                    //     var colorValue = { 
                    //         new_base_color: responseColors.base_color,
                    //         new_heading_color: responseColors.heading_color,
                    //         new_link_button_color: responseColors.link_button_color,
                    //         new_link_button_hover_color: responseColors.link_button_hover_color,
                    //         new_button_color: responseColors.button_color,
                    //         new_button_hover_color: responseColors.button_hover_color,
                    //         new_text_selection_color: responseColors.text_selection_color,
                    //         new_text_selection_bg_color: responseColors.text_selection_bg_color,
                    //         new_border_color: responseColors.border_color,
                    //         new_background_color: responseColors.background_color,
                    //         new_page_title_text_color: responseColors.page_title_text_color,
                    //         new_page_title_bg_color: responseColors.page_title_bg_color,
                    //         new_bg_menu_dropdown_color: responseColors.bg_menu_dropdown_color
                    //     };
                    //     console.log(colorValue);
                    //     var jsonString = JSON.stringify(colorValue);
                    //     localStorage.setItem('solace_step5_color', jsonString);
                    // }
                });
            },
            error: function (xhr, status, error) {
                console.error('Failed to make request to the API settings. Status:', status);
            }
        });
    });

    $(function () {
        $(window).on('load', function() {
            // Event listener for the 'load' event on the window.
            setTimeout(function() {
                // Set a timeout to execute the function after 1500ms (1.5 seconds)
                const boxPreview = $('body.solace_page_dashboard-step5 .col-right');
                if (boxPreview.length) {
                    // Get and double the width of the element
                    let boxPreviewWidth = boxPreview.width();
                    boxPreviewWidth = Math.round(boxPreviewWidth);
                    boxPreviewWidth = boxPreviewWidth * 2;

                    const imgBar = $('body.solace_page_dashboard-step5 .col-right img.urlbar');
                    const getImgBarWidth = window.getComputedStyle(imgBar[0]).width;

                    if ( '100%' === getImgBarWidth ) {
                        // Set the width of the 'preview' element based on the calculated value.
                        // console.log('Width img toolbar: ', getImgBarWidth);
                        // alert('Width img toolbar: ' + getImgBarWidth);
                        $('body.solace_page_dashboard-step5 .col-right .pre-preview > .preview').css('width', boxPreviewWidth - 700 + 'px');
                    } else {
                        // Set the width of the 'preview' element based on the calculated value.
                        $('body.solace_page_dashboard-step5 .col-right .pre-preview > .preview').css('width', boxPreviewWidth + 'px');
                    }
                }
            }, 2100);
        });

        let resizeTimeout;
        // Event listener for the 'resize' event on the window
        $(window).on('resize', function() {
            const boxPreview = $('body.solace_page_dashboard-step5 .col-right');
            if (boxPreview.length) {
                // Get and double the width of the element
                let boxPreviewWidth = boxPreview.width();
                boxPreviewWidth = Math.round(boxPreviewWidth);
                boxPreviewWidth = boxPreviewWidth * 2;

                // Set the width of the 'preview' element based on the calculated value
                $('body.solace_page_dashboard-step5 .col-right .pre-preview > .preview').css('width', boxPreviewWidth + 'px');
            }
            // Clear any existing resize timeout
            clearTimeout(resizeTimeout);
        });        
    });        

})(jQuery);
