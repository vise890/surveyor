<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link type="text/css" href="<?=base_url();?>css/ui-lightness/jquery-ui.css" rel="stylesheet" />
        <link type="text/css" href="<?=base_url();?>css/shelter.ui.css" rel="stylesheet" />
        <link type="text/css" href="<?=base_url();?>css/jquery.notice.css" rel="stylesheet" />
        <script type="text/javascript" src="<?=base_url();?>js/jquery.js"></script>
        <script type="text/javascript" src="<?=base_url();?>js/jquery.ui.js"></script>
        <script type="text/javascript" src="<?=base_url();?>js/jquery.notice.js"></script>
        <script type="text/javascript" src="<?=base_url();?>js/jquery.validate.js"></script>
        <title>
            Admin Panel
        </title>
    </head>
    <body>
        <!-- Beginning of tabs for admin panel. Managed by jquery-ui .tabs() . docs at: http://jqueryui.com/demos/tabs/ -->
        <div id="tabs">
            <!-- tab headers and buttons are contained in an unordered list -->
            <ul>
                <li>
                    <a href="#city_admin" id="city_admin_header">City Administration</a>
                </li>
                <li>
                    <a href="#field_admin" id="field_admin_header">Field Administration</a>
                </li>
                <li>
                    <a href="#dropdown_admin">Dropdown Menu Administration</a>
                </li>
                <li>
                    <a href="#maintenance">Maintenance</a>
                </li>
            </ul>
            
            <!-- each tab is contained in its div. the div id must match the id of the tab header -->
            
            <!-- Beginning of city administration tab -->
            <div id="city_admin" style="text-align: left">
                <h2>Select the city you wish to manage:</h2>
                
                <!-- Container for the city links, up dateable through jquery .load() -->
                <div id="city_admin_links" style="text-align: left">
                    <?=$city_admin_links;?>
                    </div><br />
                <?php
                                // Add city
                                echo form_open('admin_panel/add_city/', 'id="add_city"');
                                echo form_label('Add a new city:', 'city_name');
                                echo form_input('city_name', '', 'class="required" id="city_name"');
                                echo form_submit('','Add a new city!');
                                echo form_close();
                            ?>
            </div>
            <!-- End of city admin tab -->
            
            <!-- beginning of Field administration tab -->
            <div id="field_admin">
                <div class="adminbox">
                    <h2>
                        Below you can add a new question that can be enabled for all the cities:
                    </h2><?php
                                        // Field addition form
                                        echo form_open('admin_panel/add_field/', 'id="add_q"');

                                        // Field type options
                                        $type_options = array(
                                            "date"      => "Date",
                                            "number"    => "Number (Integers only)",
                                            "decimal"   => "Number (Decimals too..)",
                                            "select"    => "Dropdown Menu",
                                            "text"      => "Simple Text",
                                            "checkbox"  => "Checkbox"
                                            );

                                        // Field's label 
                                        $this->table->add_row(form_label('Question name:', 'label'), form_input('label','', 'class="required" id="label"'));

                                        // Field's tab and automatically DB table
                                        $this->table->add_row(form_label('Question\'s tab and DB table:', 'tab_ID'), form_dropdown('tab_ID', $tab_dropdown_options, '', 'id="tab_ID"'));

                                        // Field's type (influences db field type, validation rules)
                                        $this->table->add_row(form_label('Question type:', 'type'), form_dropdown('type', $type_options, '', 'id="type"'));

                                        // Add a submit button
                                        $this->table->add_row('', form_submit('','Add a new question!'));

                                        // Put the form in a nice and tidy table
                                        $this->table->set_empty("&nbsp;");
                                        echo $this->table->generate();

                                        // close the form
                                        echo form_close();
                                    ?>
                </div>
                <div class="adminbox">
                    <!-- Rearrange fields link -->
                    <h2>
                        <?=anchor('admin_panel/reorder_fields','Rearrange Questions');?>
                        </h2>
                </div>
            </div><!-- End of field admin tab -->
            <!-- Beginning of Dropdown admin tab -->
            <div id="dropdown_admin">
                <h2>Select the dropdown menu you wish to manage:</h2>
                <?php
                    
                    // Dropdown administration links
                    foreach ($dropdowns as $dropdown)
                    {
                        echo anchor('admin_panel/manage_dropdown_options/'.$dropdown->ID, $dropdown->label).'<br />';
                    }
                ?>
            </div>
            <!-- End of Dropdown admin tab -->
            
            <!-- Beginning of maintenance tab -->
            <div id="maintenance">
                <!-- Database optimisation -->
                <h2><?=anchor('admin_panel/optimise_db','Optimise Database (To be used once every few months)');?></h2>
                <h2><?=anchor('admin_panel/clear_charts','Empty Charts directory (To be used once every few months)');?></h2>
                    
            </div>
            <!-- End of maintenance tab -->
            
        </div>
        <!-- End of #tabs div -->
        
        <!-- Dialog to confirm add city. Managed by jquery-ui .dialog(). docs at http://jqueryui.com/demos/dialog/-->
        <div id="dialog-add_city" title="Submit Confirmation">
            <p>
                Are you sure that you want to add the city '<span id="dialog-city"></span>' ?
            </p>
            <p>
                It is very important that the city name is correct since you cannot delete cities nor can you rename them once they are entered. Press 'I am sure, Add the City' if you are sure or press Cancel to return to the form
            </p>
        </div>
        
        <!-- Dialog to confirm add question. Managed by jquery-ui .dialog() . http://jqueryui.com/demos/dialog/ -->
        <div id="dialog-add_q" title="Submit Confirmation">
            Please check that all the details you have entered for the new question are correct:<br />
            <p>
                Question name: <span id="dialog-question_label"></span>
            </p>
            <p>
                Question's tab and DB table: <span id="dialog-question_tab"></span>
            </p>
            <p>
                Question's type': <span id="dialog-question_type"></span>
            </p>
            <p>
                It is very important that all the details are correct as you cannot delete nor can you edit questions once they are entered. Press 'I am sure, Add Question' if you are sure or press Cancel to return to the form
            </p>
            
            
        </div>
<script type="text/javascript">
// <! [CDATA[

		$(document).ready(function()
	{
        
        // Initialise tabs .... 
		$("#tabs").tabs();
		
		// Focus on the city name field when the document is ready ..
		$('#city_name').focus();

        // .. and also when its tab is clicked
        $('#city_admin_header').click( function(){
            $('#city_name').focus();
        });

        // .. and when the field admin tab is clicked, focus on the field name
        $('#field_admin_header').click( function(){
            $('#label').focus();
        });

		// Prepares a dialog displayed to confirm the submission of the #add_city form
        $("#dialog-add_city").dialog({
            autoOpen: false,
            resizable: false,
            width:500,
            height:250,
            modal: true,
            draggable: false,
            buttons:
            {
                'Cancel': function()
                {
                    $(this).dialog('close');
                },
                'I am sure, Add the City': function()
                {
                    var city = $('#city_name').val();
                    var location = <?="'".base_url()."'"?>+'index.php/admin_panel/add_city';
                    
                    // Submit the data through ajax
                    $.ajax({
                        type: "POST",
                        url: location,
                        data: { city_name: city },
                        success: function() {
                            //add a notification through jquery.notice()
                            $.noticeAdd({ text: 'The city '+city+' was successfully added!' });
                            // Refresh the div containing the cities
                            $('#city_admin_links').load(<?="'".base_url()."'"?>+'index.php/admin_panel/generate_city_admin_links');
                            // Clear the field
                            $('#city_name').val("");
                        } // end of success funciton
                    }); // end of ajax
                    // Close the dialog
                    $(this).dialog('close');
                } // end of second button
            } // end of buttons
        }); // end of dialog

		// Prepares a dialog displayed to confirm the submission of the #add_q form
		$("#dialog-add_q").dialog({
			autoOpen: false,
			resizable: false,
			width:500,
			height:360,
			modal: true,
			draggable: false,
			buttons: {
				'Cancel': function() {
					$(this).dialog('close');
				},
				'I am sure, Add Question': function() {

					// Initialise the variables with the values needed for submission
					var label = $('input[name="label"]').val();
					var tab_ID = $('select#tab_ID').val();
					var type = $('select#type').val();

					// Submit the data through ajax
					$.ajax({
					    type: "POST",
					    url: <?="'".base_url()."'"?>+'index.php/admin_panel/add_field',
					    data: {
					        label: label,
							tab_ID: tab_ID,
							type: type,
					    },
					    success: function() {
							//Add a little notice through the jQuery notice plugin
							$.noticeAdd({ text: 'The question &#x27;'+label+'&#x27; was successfully added!' });
							
							// Reset the form
							$('#add_q')[0].reset();
						} // end of success funciton
					}); // end of ajax
				    // Close the dialog
					$(this).dialog('close');
					} // end of submit button
				} // end of buttons
			}); // end of dialog
		
		// add a validator method for just letters and spaces (with regex)
		$.validator.addMethod(
			"db_field",
			function(value,element) {
				return this.optional(element) || /^[a-zA-Z ]*$/i.test(value);
			},
			"Only letters and spaces are allowed"
		);


		// Validation of the #add_city form is set up through the jQuery validate plugin
		$("form#add_city").validate({
			rules: {
				city_name: "required db_field",
			},
			submitHandler: function(form)
			{
				// Gets the submitted values to pass them to the dialog
				$("span#dialog-city").html($('input[name="city_name"]').val());
				$('#dialog-add_city').dialog('open');
			}
		});


		// Validation of the #add_q form is set up through the jQuery validate plugin
		$("form#add_q").validate({
			rules: {
				label: "required db_field",
			},
			submitHandler: function(form)
			{
				// Gets the submitted values and load them in the dialog
				$("span#dialog-question_label").html($('input[name="label"]').val());
				$("span#dialog-question_tab").html($('select#tab_ID :selected').text());
				$("span#dialog-question_type").html($('select#type :selected').text());


				$('#dialog-add_q').dialog('open');
			}
		});

	}); // end of document ready
//]]>
</script>
    </body>
</html>
