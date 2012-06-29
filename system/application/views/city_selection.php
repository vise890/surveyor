<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link type="text/css" href="<?php echo base_url(); ?>css/ui-lightness/jquery-ui.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo base_url(); ?>css/shelter.ui.css" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.ui.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.validate.js"></script>
    <title>Shelter - City Selection</title>
</head>

<body>
    <h2>Please input your name and select the city you wish to work on below:</h2>
    <?php
        // Open city selection form
        echo form_open('survey/city_selection/');
        
        // City selection
        $label = form_label('City: ', 'city');
        $tag = form_dropdown('city', $cities);
        $this->table->add_row($label, $tag);
        
        // Username input
        $label = form_label('Data Input by:', 'username');
        $tag = form_input('username', @$username, 'id="username"');
        $this->table->add_row($label, $tag);
        
        // Sumbit
        $this->table->add_row('', form_submit('','OK'));
        
        // Show table
        $tmpl = array (
                            'table_open'          => '<table border="0" align="center" cellpadding="4" cellspacing="0">',
                      );

        $this->table->set_template($tmpl);
        echo $this->table->generate();

        // Close the form
        echo form_close(); 
    ?>
    
    <script type="text/javascript">
    // <! [CDATA[

    // Validation is set up through the jQuery validate plugin
    $(document).ready(function()
    {
        // add a validator method for just letters and spaces (with regex)
        $.validator.addMethod(
            "username",
            function(value,element) {
                return this.optional(element) || /^[a-zA-Z]*$/i.test(value);
            },
            "Only letters are allowed (No spaces or special characters)"
        );

        $('form').validate({
            rules: {
                username: "required username",
            }
        });
        
        // Focus on the username field
        $("#username").focus();

    });
    //]]>
    </script>
</body>
</html>
