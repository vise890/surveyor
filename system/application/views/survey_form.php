<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Shelter - Survey Entry Form</title>
    <link type="text/css" href="<?php echo base_url(); ?>css/ui-lightness/jquery-ui.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo base_url(); ?>css/shelter.ui.css" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.ui.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.validate.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.addrow.js"></script>
</head>
<body>

    <div id="dialog-form_invalid" title="Incomplete Data">
        <!-- A dialog for an invalid form is prepared below -->
        <h3>The data you have entered is incomplete. Please go through the tabs and complete the fields highlighted in red.</h3>
        TIP: If you do not have a value, you should input either NA (Not Applicable) or NR (Not Recorded). If the question is a number you should put 0.
    </div>

    <?php

$attributes = array('id' => 'survey_form');
echo form_open('survey/survey_form/'.$city.'/'.$username, $attributes);

echo form_hidden('survey_ID', $survey_ID);
echo form_hidden('city', $city);

?>  
<div id="tabs">
    <ul>
        <?php
    // Print a list of tabs in a list and link each item to the div element with the tab's class  
    foreach ($tabs as $tab_button)
    {           
        print ("<li>");
        print ('<a href="#'.$tab_button['class'].'">'.$tab_button['label']."</a>");
        print ("</li>");
    }
    ?>
</ul>
<?php
// Prints a div element containing the relevant fields for each tab
foreach ($tabs as $tab)
{   
    // Print a div element with the tab's class (used by jQuery)
    print ('<div id="'.$tab['class'].'">');

    $tab_class = $tab['class']; // Just for syntactic purposes

    $i = 0; // For the counter

    // prepare the html tag needed to generate the input for each field in the current tab
    // set_value($field['name']) repopulates the form after an unsuccessful CI serverside validation
    // the class attribute is used by jQuery validate plugin to validate the fields on the client side.
    foreach ($fields[$tab_class] as $field)
    {   
        // An array postfix is created for inputs. This is used for fields in the family detail tab that need to be duplicated (many family members for one survey)
        if ($field['is_array'] == TRUE)
        {
            $input_name_postfix = '[]';
        }
        
        // Prepare a field label and a field name with its eventual array postfix
        $field_label = form_label($field['label'], $field['name']);
        $field_name = $field['name'].@$input_name_postfix;
        unset($input_name_postfix);
        
        // prepare the html input tag based on the field type. Fields are repopulated after an unsuccessful CI validation and class information is added to work with the jQuery validate plugin
        switch ($field['type'])
        {
            case 'text':
            $field_tag = form_input($field_name, set_value($field_name), 'class="'.$field['js_validation'].'"');
            break;
            case 'select':
            $field_tag = form_dropdown($field_name, $field['drop_down_options'], 'NR', 'class="'.$field['js_validation'].'"');
            break;
            case 'number':
            $field_tag = form_input($field_name, set_value($field_name), 'class="'.$field['js_validation'].'"');
            break;
            case 'decimal':
            $field_tag = form_input($field_name, set_value($field_name), 'class="'.$field['js_validation'].'"');
            break;
            case 'date': // A `date` field is a normal html text field. The only difference is that it is given a `date` id so that it inherits datepicker functionality from jQuery.
            $field_attributes = array(
                'name'        => $field_name,
                'value'       => set_value($field_name),
                'style'       => 'width:200',
                'class'       => $field['js_validation']
                );
            $field_tag = form_input($field_attributes);
            break;
            case 'checkbox':
            $field_tag = form_checkbox($field_name, 1, FALSE);
            break;
            default:
                die('Cannot produce field tag: illegal field type');
            break;
        }

        // The Code Igniter Validation error message is assigned to a variable
        $field_error = strip_tags(form_error($field_name));

        // If the error is present then it is cleaned so that only the actual error message is preserved (remove <p> </p>) and it is put into a label element
        if ($field_error != NULL)
        {
            $field_error = form_label($field_error, $field_name, 'class="error"');
        }

        // If the table needs to have a js funcion to add rows of fields (e.g. for Family Details where there could be more than one family member) add the field label as a table header and the field's tag and error
        if ($tab['many_to_one'] == TRUE)
        {
            $field_labels[$i] = $field_label;
            $field_tags[$i] = $field_tag.$field_error;
            $i = $i+1;
        }
        else
        {
            // Adds a row in the table that contains the fieds for the current tab; The field label, the actual field ($field_tag) and the eventual $field_error (generated by CI) are contained in 3 separate columns
            $this->table->add_row($field_label, $field_tag, $field_error);
        }
    }

    // If the table needs to have a js funcion to add rows of fields, create a heading with the field labels and add the first row with the actual fields(and eventual ci validation errors)
    if ($tab['many_to_one'] == TRUE)
    {
        // An add and a delete button are added in the last two columns
        $field_labels[$i] = 'Add';
        $field_labels[$i+1] = 'Delete';
        $add_button = array(
            'class' => 'addRow',
            'value' => 'Add',
            'content' => 'Add',
            'type' => 'button'

            );
        $delete_button = array(
            'class' => 'delRow',
            'value' => 'Delete',
            'content' => 'Delete',
            'type' => 'button'
            );

        $field_tags[$i] =  form_button($add_button).form_hidden('count'.$tab_class.'[]', 'class="count"');
        $field_tags[$i+1] = form_button($delete_button);

        // Set the headings from the field names array (+ add and delete labels)
        $this->table->set_heading($field_labels);
        // Add the first row with the actual fields and eventual CI validation error messages (+ add and delete row buttons)
        $this->table->add_row($field_tags);  
        
        // Destroy the variables so that they can be used in the next tab
        unset($field_labels);
        unset($field_tags);
        unset($field_error);
    }

    unset($tab_class);
    
    // Set the value for empty cells and generate the table
    $this->table->set_empty("&nbsp;");
    echo $this->table->generate();

    // Table is cleared so that a new one can be generated for the next tab
    $this->table->clear();

    // Close tab's div element
    print ("</div>");

}
?>
</div>
<?php
echo form_submit('survey_form','Submit Survey');
echo form_close();
?>

<script type="text/javascript">
// <! [CDATA[

$(document).ready(function()
{
    // All the jquey plugins are located in the js/ folder. Their documentation can be found on their website.
    
    // Prepares a dialog displayed when validation errors are present (uses jQuery UI)
    $("#dialog-form_invalid").dialog({
        autoOpen: false,
        resizable: false,
        width:500,
        height:230,
        modal: true,
        draggable: false,
        buttons:
        {
            'OK': function()
            {
                $(this).dialog('close');
            }
        }
    });

    // Datepicker is initialised for fields of `date` type (which have a `date` class) (uses jQuery UI)
    $(".date").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });

    //tabs are initiated for #tabs div (uses jQuery UI)
    $("#tabs").tabs();

    // The Add Row and Delete Row buttons are initialised (uses Query Table addRow plugin)
    $(".addRow").btnAddRow();
    $(".delRow").btnDelRow();

    // Validation is set up (uses jQuery validate plugin)
    $("#survey_form").validate({
        invalidHandler: function(form, validator) 
        {
            var errors = validator.numberOfInvalids();
            if (errors)
            {
                $('#dialog-form_invalid').dialog('open');
            } 

        }
    });

});

//]]>
</script>
</body>
</html>