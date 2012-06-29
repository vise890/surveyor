<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link type="text/css" href="<?php echo base_url(); ?>css/ui-lightness/jquery-ui.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo base_url(); ?>css/shelter.ui.css" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.ui.js"></script>
    <title>Shelter - Reorder fields</title>
    <style type="text/css">
    #field_list { list-style-type: none; margin: 0; padding: 0; width: 30%; }
    #field_list li { margin: 0 1px 1px 1px; padding: 0.2em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
    #field_list li span { position: absolute; margin-left: -1.3em; }
    </style>

</head>

<body>
    <h2>Drag and drop the fields in the order you want them to appear in the form</h2>
    <ul id="field_list" >
        <?php
    foreach ($fields as $field)
    {
        echo '<li class="ui-state-default" id="fields_'.$field->ID.'"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$field->label.'</li>';
    }

    ?>
</ul>


<script type="text/javascript">
// <! [CDATA[

$(document).ready(function()
{
// Disables text selection on the ul items. this prevents messing up with the otherwise graceful drag n drop
$("#field_lists").disableSelection();

// The location of the php script is created
var location = <?php echo '"'.base_url().'index.php/admin_panel/update_field_order/"'; ?>;

// The list is made stoppable and an ajax call is initialised for when a user stops reordering a field
$("#field_list").sortable(
    {
        stop:function(i)
        {
            var positions = $("#field_list").sortable("serialize");
            $.post(location, positions);
        }
    });
});
//]]>
</script>
</body>
</html>