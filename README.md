# Surveyor
Surveyor is a data entry software. The main features are:

- Expandable surveys
- Multiple survey sets (via cities)
- Export capabilities
- Basic graphing facilities

This is a fork of my project for [Shelter Associates](http://shelter-associates.org/) to help them inputting data in their database, but with a bit of work, it could be adopted to be more flexible.

Surveyor is quite messy at this stage. Use at your own risk :)

# If this gets published, please note:
- Login and general security are not implemented
- A more serious approach to security should be taken. Data on the server side is often not validated (admin panel, many-to-one fields..)
- Minify the jscript n use only the relevant bits of the jquery libraries (now the development, complete version of jquery is being used)
- look through all the controller methods. some of them might be dangerous if used maliciously (e.g. empty folder can empty whatever folder if permissions are right)
- ensure that validation rules check the length of the value entered. this must match the db field limit ( i haven't implemented this at all). you should do this and maybe tweak the db so that fields are only as long as they're needed.

# Next Features & Known BUGS
- Add Delete/Edit capabilities for surveys
- Add code to avoid duplicate surveys to be input. 
- Apparently a few surveys are not being exported. Investigate the problem and fix it.

## Improvements to the data entry:
- Functionality to Rename drop down menu options (Update existing entries in the db AS WELL)
- CRUD tabs and develop admin panel so that the tab a field belongs to can be changed 
- Improve the way fields are reordered (maybe by tab or something, make fields draggable to new tabs to change their tab .. etc)
(http://www.wiseguysonly.com/200/12/07/drag-and-drop-reordering-of-database-fields-sortables-with-jquery/)

- jQuery "smart" logic for validation (ie if electricity is yes then electricity_meter is required, otherwise it's NA)(Make a depends_on field in the db. A field can depend on the value of another field)
- Split views into headers footers etc.
- Add functionality to add more field types. Add more rules that create machine friendly field names. This should all be contained in the add_ui_field method in the admin_model file
- REDESIGN DATABASE PROPERLY. my belief is that there should be a good redesign of the db. a more extensive use of relational db features should be used to avoid redundancy of data.
- add some kind of alert for when there are jQuery validation errors but not in the current tab. Something like ' the field bar is empty, go check it'
- the survey_form method in the survey.php controller is hacked together. Clean it up (especially the row count)
- Survey Drafts??
- Style it up with css
- Update the system so that NRs are NULL in the db. I was forced to create a lot of code bloat to support the current standard of putting NR when a value was not recorded. I believe that as long as the validations are run correctly there is no need to waste space with NR though. NA should still be recorded as it is different.
- rewrite the methods that use names instead of table IDs (and change db structure) : city_ui_fields
- Add drop down like checkboxes
- Add functionality to add slums to a city. not just a simple dropdown
- Join ui_fields and ui_tabs tables to get the db_table of a field  (duplicated data now)
- Organise Enable/rearrange Fields by tab
- Improve Checkbox functionality (Add kind of dropdown options, a checkbox group should be a field, not one field per checkbox)
- Confirmation dialogues to prevent data loss
- validate admin panel inputs (now you can add an empty city)
- find a smart way to validate array fields on the server side. Now such fields are not validated on the server side since, if a row is added and left blank CI goes mental.
- add 2 new fields to the ui fields table. These should be depends_on, depends_on_value. This way we could generate codeigniter as well as jQuery validate rules based on the value of any other field. Also you could generate some js to disable certain fields until another one chages value (eg. electricity meter should be NA  until a household has electricity)
- Add functionality to change tabs for fields. the way i see it this should be done with a jQuery-ui/ajax interface and merged with the "rearrange field order" functionality
- Clear up the db. IDs should be used instead of names
- Add RUD functionality for surveys
- Add support for specifying a default value for fields (Now all dropdowns are set to NR which should be kept but Animal_quantity, for example, should be 0, this is now hacked together with jQuery in the view.)
- lock tables ?
- add success message for updated fields in city
- website relies quite heavily on js to solve certain issues. (i.e. to select slum name in the fields, to validate fields). their server counterpart should be added!
- piecharts with more than 8 slices fail as the cigniter class cannot generate more colors or something . i have just suppressed the notices so that the charts gets shown anyway (even if slices are pitch black after the 8th one). this should be fixed


# Oher Stuff
- Attach CSV to Geomedia: http://dominoc925.blogspot.com/2009/08/quickly-attach-xlscsvdbf-files-in.html
- Investigate the GRASS-GIS package. It is free and can be used in conjunction to a central network database (MySQL). Pratima has expressed the need for producing open format GIS files as sometimes they are preferred by the local governments. I will assess if switching to GRASS is a feasible option for Shelter.
- Investigate what systems can be used to display GIS data on the website. It seems like this could be done dynamically with a combination of GRASS-GIS and GeoServer  or with Geomajas . It is unlikely however that this kind of system will be running by the end of the placement. I will prepare a manual with the information I gather so that the next volunteer can pick up from where we left off.