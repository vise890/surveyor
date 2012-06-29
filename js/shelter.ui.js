//tabs are initiated for #tabs elements
$(function() {
	$("#tabs").tabs();
});

// Datepicker is initialised for fields of `date` type
$(function() {
	$("#date").datepicker({
		//  showOn: 'button',
		//  buttonImage: 'ui-icon-document',
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
	});
});



// Buttons are defined and their icon is specified below
$(function() {
	$("#new_entry").button({
		icons: {
			primary: 'ui-icon-document'
		},
	});

});

$(function() {
	$("#edit_entry").button({
		icons: {
			primary: 'ui-icon-pencil'
		},
	});

});

$(function() {
	$("#search_entry").button({
		icons: {
			primary: 'ui-icon-search'
		},
	});

});

$(function() {
	$("#save").button({
		icons: {
			primary: 'ui-icon-disk'
		},
	});

});
$(function() {
	$("#delete").button({
		icons: {
			primary: 'ui-icon-trash'
		},
	});

});
$(function() {
	$("#cancel").button({
		icons: {
			primary: 'ui-icon-close'
		},
	});

});


// A dialog for unsaved form data is initialised below
$(function() {

	$("#dialog-confirm").dialog({
		autoOpen: false,
		resizable: false,
		width:500,
		height:230,
		modal: true,
		buttons: {
			'Discard Changes': function() {
				$(this).dialog('close');
			},
			'Cancel': function() {
				$(this).dialog('close');
			}
		}
	});     
});


// If unsaved form data is present, a confirmation dialog is shown. ######## Not implemented yet!!!
$(function() {

	$('#edit_entry').click(function()
		{
			$('#dialog-confirm').dialog('open');
			return false;
		});
});

$(function() {

	$('#new_entry').click(function() {
		$('#dialog-confirm').dialog('open');
		return false;
	});
});

$(function() {

	$('#search_entry').click(function() {
		$('#dialog-confirm').dialog('open');
		return false;
	});
});


function delete_confirm()
{
 var where_to= confirm("Do you really want to delete this option?");
 if (where_to== true)
 {
   window.location="http://yourplace.com/yourpage.htm";
 }
 else
 {
  window.location="#";
  }
}