<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link type="text/css" href="<?php echo base_url(); ?>css/ui-lightness/jquery-ui.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo base_url(); ?>css/shelter.ui.css" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.ui.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.validate.js"></script>
    <title>Admin - Manage City</title>
</head>

<body>
<div class="adminbox">
    <h2>Below you can enable or disable fields for <?php echo $city ?>:</h2><br />
    <?php
    
        echo form_open('admin_panel/update_city_fields/'.$city);
        
        // Check all buttn
        echo form_button('checkall', 'Check All', 'id="checkall"');
        echo '<br />';
        
        $this->table->set_heading(array('Field Name', 'Enabled?')); // Prepare table headings

        foreach ($form as $row)
        {
            $checkbox = form_checkbox($row['name'], $row['name'], $row['enabled']);
            $this->table->add_row($row['label'], $checkbox);
        }
        
        $tmpl = array ( 'table_open'  => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl);
        
        echo $this->table->generate();
        
        echo '<br />';
        echo form_submit('','Update city Fields!');
        echo form_close();
        
        echo '<br /> <br />';
        echo anchor('admin_panel', 'Back to Admin Panel');
    
    ?>
</div>
<script type="text/javascript">
// <! [CDATA[
$(document).ready(function()
{
    $('#checkall').click(function () {
    		$('input[type="checkbox"]').attr('checked', true);
    	});
    
    // Always select slum name (check it and disable it)! 
    $('input[name="slum_name"]').attr('checked', true);
});
//]]>
</script>
</body>
</html>
