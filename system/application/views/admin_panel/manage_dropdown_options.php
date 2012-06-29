<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link type="text/css" href="<?php echo base_url(); ?>css/shelter.ui.css" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.confirm.js"></script>
    <title>Admin - Manage Dropdown Options</title>
</head>

<body>
    <div class="adminbox">
        <h2>Below you can edit the drop down options for the field you selected:</h2>
<?php
	if ($dropdown_options == FALSE) // If the result set is empty, the model returns FALSE
	{
	    echo 
            '<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em; width: 30%;"> 
        		<p>
            		<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;">
                		<div style="display: inline; height: auto; position: absolute; visibility: hidden; width: auto; ">
                		    <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
                		</div>
            		</span>
            		There is currently no data about this question for the selected slum. Try selecting another question or another slum.
        		</p>
        	</div>';
	}
	else
	{
		$this->table->set_heading(array('Option Name', 'Delete')); // Prepare table headings

		foreach ($dropdown_options as $option) // Create table rows with option name and a `delete` link
		{
			$delete = anchor('admin_panel/delete_dropdown_option/'.$option['ID'].'/'.$field_ID, 'Delete', 'class="delete"');
			$this->table->add_row($option['option_name'], $delete);
		}
		
        $tmpl = array ( 'table_open'  => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl);
        
		echo $this->table->generate();
	}
	echo '<br />';
	
	// Form to add dropdown options
	echo '<div style="text-align:left">';
	echo form_open('admin_panel/add_dropdown_option/'.$field_ID);
	print("\t\t\t<label>Add a new option:</label>\n");
	echo form_input('option_name','', 'id="add_option"');
	echo form_submit('','Add option!');
	echo form_close();
	echo '<br />';
	echo '</div>';
	
	// Return to admin panel
	echo anchor('admin_panel', 'Back to Admin Panel');
	
?>
<script type="text/javascript">
// <! [CDATA[
    //$('input[type="submit"]').confirm();
    $('a[class="delete"]').confirm();
    
    //Focus on the new option input
    $('#add_option').focus();
//]]>
</script>
</div>
</body>
</html>
