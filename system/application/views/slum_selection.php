<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link type="text/css" href="<?php echo base_url(); ?>css/ui-lightness/jquery-ui.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo base_url(); ?>css/shelter.ui.css" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.ui.js"></script>
    <title>Shelter - Slum Selection</title>
</head>

<body>
    <h2>Please select the slum you want to export below:</h2>
    <?php

echo form_open('export_db/');

echo form_label('City', 'city'); // label for the city field
$cities['NR'] = 'Please Select a City first...'; // Add a default 'NR' value to the dropdown array
echo form_dropdown('city', $cities, 'NR', 'id="city"');
echo '<br />';

$attributes = array('id' => 'slum-label'); // attributes for the slum label. id used to hide/show element with jQuery
echo form_label('Slum', 'slum', $attributes); // label for slum
echo form_dropdown('slum', array( 'NA' => "Please Select a City first..."), '', 'id="slum"'); // slum dropdown . id used to interact with the element with jQ
echo '<br />';

// GIS cehckbox
$attributes = array('id' => 'GIS-label');
echo form_label('Export for Geomedia?', 'GIS', $attributes);
echo form_checkbox('GIS', '1', FALSE, 'id="GIS"');


echo '<br />';
echo form_submit('','EXPORT');
echo form_close(); 
?>

<script type="text/javascript">
// <! [CDATA[

$(document).ready(function()
{
    // Hide the slum & GIS selection bit of the form...
    $("#slum").hide();
    $("#slum-label").hide();
    $("#GIS").hide();
    $("#GIS-label").hide();
    
    
    // .getJSON is probably not the most appropriate or elegant jQuery method to do this !!! update to .post() or .load() !!!
    
    // The script below is an adapted version of the tutorial found at: http://www.ajaxlines.com/ajax/stuff/article/use_jquery_ajax_to_create_options_in_a_dropdown_menu.php (MV)
    // when the DOM element with id="city" changes....
    $("#city").change(function() {

        // Prepare the location to pass to the getJSON call...
        // The URL also includes the city. This is passed to the code igniter method 'get_city_slums' as a paramenter.
        var city = $("#city").val();
        var location = <?php echo '"'.base_url().'index.php/export_db/get_city_slums/"'; ?> + city;

        // ... and issue an AJAX request to the PHP script
        // getJSON is a method that expects a JSON-encoded data structure to be returned
        // there are other AJAX methods too. See http://docs.jquery.com/Ajax
        $.getJSON(

            // 1st arg to getJSON is the URI of the script. contained in the 'location' variable
            location, 

            // 2nd arg to getJSON is an array of key-value pairs to send 
            // to the script. As many as you want.
            // left-side is the GET variable name as seen by the target
            // script, right-side is the value that will be sent.
            // THIS is however not used since we already passed the city through the URL (and CI does not use GET)
            {
            },

            // 3rd arg is the callback function for the AJAX response
            // The CI method responds in a JSON format so jQuery can
            // understand the data structure natively, without you 
            // writing awful parsing of your own:
            function(j)
            {
                // erase all OPTIONs from existing select menu on the page
                // $("#slum").remove(); 

                // You will rebuild new options based on the JSON response...
                var options = '<option value="">Choose the slum...</option>';
                // "j" is the json object that was output by your PHP script
                // it is the array of key-value pairs to turn 
                // into option value/labels...
                for (var i = 0; i < j.length; i++) 
                {
                    options += '<option value="' + j[i].option_value + '">' + j[i].option_label + '</option>';
                }
                // stick these new options in the existing select menu
                $("#slum").html(options);
                // now your select menu is rebuilt with dynamic info
            } 

            ); // end getJSON 

            // We can now show the slum & GIS bit of the form with the updated info ...
            $("#slum").show();
            $("#slum-label").show();
            $("#GIS").show();
            $("#GIS-label").show();

            }); // end changed select to trigger AJAX


        });
        //]]>
        </script>
    </body>
    </html>