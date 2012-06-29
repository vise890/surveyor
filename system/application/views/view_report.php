<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link type="text/css" href="<?php echo base_url(); ?>css/ui-lightness/jquery-ui.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo base_url(); ?>css/shelter.ui.css" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.ui.js"></script>
    <title>Shelter - Slum Report</title>
</head>

<body>
    <h1>Select the slums you want to generate a report for:</h1>
    <?php

echo form_open('report/');

// City selection
$cities['NR'] = 'Please Select a City first...'; // Add a default 'NR' value to the dropdown array
$label = form_label('City', 'city'); // label for the city field
$tag = form_dropdown('city', $cities, 'NR', 'id="city"');
$this->table->add_row($label, $tag);

// Slum selection
$label = form_label('Slum', 'slum[]', array('id' => 'slum-label')); // label for slum. id used to hide/show element with jQuery
$tag = form_dropdown('slum', array(), '', 'id="slum" size="10" multiple="multiple"'); // slum dropdown . id used to interact with the element with jQ
$this->table->add_row($label, $tag);

// Question selection
$label = form_label('Select the question', 'slum', array('id' => 'field-label')); // label for field . id used to hide/show element with jQuery
$tag = form_dropdown('field', array(), '-', 'id="field"'); // field dropdown . id used to interact with the element with jQ
$this->table->add_row($label, $tag);

// Chart type selection
$options = array(
     'pie'  => "Pie Chart",
     'bar'  => "Bar Chart",
     );
$label = form_label('Select the Chart type', 'chart_type', array('id' => 'chart_type-label')); // label for field . id used to hide/show element with jQuery
$tag = form_dropdown('chart_type', $options, 'pie', 'id="chart_type"'); // field dropdown . id used to interact with the element with jQ
$this->table->add_row($label, $tag);


// Report generation trigger
$generate_button = array(
    'name'      => 'generate',
    'id'        => 'generate',
    'content'   => 'Generate Report'
    );
$tag = form_button($generate_button);
$this->table->add_row('', $tag);

// Generate the table
$tmpl = array (
    'table_open' =>  '<table border="0" cellpadding="4" align="center" cellspacing="0">',
    );
$this->table->set_template($tmpl);
echo $this->table->generate();

echo form_close();

?>
<!-- Ajax spinner -->
<img src="<?php echo base_url().'images/ajax-loader.gif'; ?>" id="spinner" style="margin-top: 100px">

<!-- Placeholder for ajax loaded content -->
<div align="center" style="text-align: center" id="report"></div>

<script type="text/javascript">
// <! [CDATA[

$(document).ready(function()
{
    // Hide the slum & field selection bit of the form...
    $("#slum").hide();
    $("#slum-label").hide();
    $("#field").hide();
    $("#field-label").hide();
    $("#chart_type").hide();
    $("#chart_type-label").hide();
    $("#generate").hide();
    
    // Hide ajax loading animation
    $("#spinner").hide();

    // when the DOM element with id="city" changes....
    $("#city").change(function(){

        // Prepare the city to pass to the ajax calls...
        var city = $("#city").val();

        // ... and issue the actual AJAX request to the PHP script
        var location = <?php echo '"'.base_url().'index.php/report/get_city_slums/"'; ?>;
        $.ajax({

            type: 'POST',

            dataType: 'json',

            url: location, // the php script location

            data: { city: city }, //data to be passed through post

            success:
            function(data) // funciton called on success
            {
                // rebuild new options based on the JSON response...
                var options;
                // it is the array of key-value pairs to turn into option value/labels...
                for (var i = 0; i < data.length; i++) 
                {
                    options += '<option value="' + data[i].option_value + '">' + data[i].option_label + '</option>';
                }

                // stick these new options in the existing select menu
                $("#slum").html(options);

                // We can now show the slum bit of the form with the updated info ...
                $("#slum").show();
                $("#slum-label").show();
            },


        }
        ); // end of slum .ajax()


        // ... and another ajax request for the field selection....
        var location = <?php echo '"'.base_url().'index.php/report/get_city_queriable_fields/"'; ?>;
        $.ajax({

            type: 'POST',

            dataType: 'json',

            url: location, // the php script location

            data: { city: city }, // data to be passed through post

            success: 
            function(data) // 3rd argument is a funciton called on success
            {
                // rebuild new options based on the JSON response...
                var options;

                // it is the array of key-value pairs to turn into option value/labels...
                for (var i = 0; i < data.length; i++) 
                {
                    options += '<option value="' + data[i].option_value + '">' + data[i].option_label + '</option>';
                }
                // stick these new options in the existing select menu
                $("#field").html(options);
                // We can now show the slum bit of the form with the updated info ...
                $("#field").show();
                $("#field-label").show();
            }
        }
        ); // end of field .ajax()
        
        // Show the chart type bit
        $("#chart_type").show();
        $("#chart_type-label").show();
        
        // show the report generation trigger button
        $("#generate").show();
    }
    ); // end 'city' changed select that triggers AJAX to load the rest of the form



    // ... when the generate button is clicked...
    $("#generate").click(function()
    {
        // Show the spinny thing
        $("#spinner").show();
        $("#report").hide();

        // .. in the meantime load the chart
        $('#report').load('report/generate_report', 
        {
            slums: $("#slum").val(),
            field: $("#field").val(),
            field_label: $("#field :selected").text(),
            chart_type: $("#chart_type").val()
        },

        hideSpinny // hide tehespinny thingy when the graph is loaded

        ); // end of load
    }
    ); // end of #generate.click trigger

    function hideSpinny()
    {
        $("#spinner").hide();
        $("#report").show();
    }
}
);
//]]>
</script>
</body>
</html>