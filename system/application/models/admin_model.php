<?php 
/**
    * Admin_model
    *
    * @author	Martino Visintin (EWB 2010) (Email: martino.visintin@ewb-uk.org)
    *
    * For help about the Active Records Class in Code Igniter, refer to ./user_guide/database/active_record.html
    * Variables are not escaped as this is automatically executed by the Code Igniter Active Record class
    *
    */
if (! defined('BASEPATH')) exit('No direct script access');

class Admin_Model extends Model {

    //php 5 constructor
    function __construct() {
        parent::Model();
    }

    function get_select_fields() // Gets all the `select` type UI fields form the db
    {
        $this->db->select('*');
        $this->db->from('database_input_ui_fields');
        $this->db->where('type', 'select');

        $query = $this->db->get();

        if ($this->db->count_all_results() > 0)
            return $query->result();
        else
            return FALSE;
    }
    
    function get_tabs() // Gets all the tabs from the db as a dropdown array ready to be passed to the CI form helper
    {
        $query = $this->db->get('database_input_ui_tabs');

        foreach ($query->result() as $tab)
        {
            $drop_down_options[$tab->ID] = $tab->label.' [ DB table: '.$tab->db_table.' ]';
        }

        // Clear memory
        $query->free_result();

        return $drop_down_options;
    }
    
    function get_db_tables() // Gets all the db_tables from the db as a dropdown array ready to be passed to the CI form helper
    {
        $this->db->select('db_table');
        $this->db->from('database_input_ui_tabs');
        $this->db->distinct();
        $query = $this->db->get();

        foreach ($query->result() as $db_table)
        {
            $drop_down_options[$db_table->db_table] = $db_table->db_table;
        }

        // Clear memory
        $query->free_result();

        return $drop_down_options;
    }
    function get_all_ui_fields() // Gets all the UI fields form the db
    {
        $this->db->select('*');
        $this->db->from('database_input_ui_fields');
        $this->db->order_by('position', 'asc');

        $query = $this->db->get();

        return $query->result();
    }
    
    function update_field_position($field_ID, $position)
    {
        $this->db->set('position', $position);
        $this->db->where('ID', $field_ID);
        $this->db->update('database_input_ui_fields');
    }
    
    function get_drop_down_options($field_ID)  // Get the drop down options (for `select` inputs) for a given $field_ID. Return them in an array
    {   
        $this->db->select('*');
        $this->db->from('database_input_ui_drop_down');
        $this->db->where('field_ID', $field_ID);
        $query = $this->db->get();

        if ($this->db->count_all_results() > 0)
            return $query->result_array();
        else
            return FALSE;
    }

    function delete_dropdown_option($option_ID) // Deletes a dropdown menu option. ID is a primary key in the db so there is no need to specify the field it belongs to 
    {
        $this->db->delete('database_input_ui_drop_down', array('ID' => $option_ID));
    }

    function add_dropdown_option($field_ID, $option_name) // Adds a new dropdown option belonging to a given field id
    {
        $data = array(
            'field_ID' => $field_ID ,
            'option_name' => $option_name
            );

        $this->db->insert('database_input_ui_drop_down', $data); 
    }


    function rename_dropdown_option($option_ID) // Not implemented yet.
    {

    }

    function get_cities() // Gets all the cities from the db
    {
        $query = $this->db->get('database_input_cities');

        if ($this->db->count_all_results() > 0)
            return $query->result();
        else
            return FALSE;
    }
    function get_city_enabled_fields($city)
    {
        $this->db->select('*');
        $this->db->from('database_input_city_fields');
        $this->db->like('city', $city);
        $this->db->join('database_input_ui_fields', 'database_input_ui_fields.name = database_input_city_fields.field_name');

        $query = $this->db->get();

        return $query->result();
    }

    function add_city($city_name)
    {
        $data = array( 'name' => $city_name );
        $this->db->insert('database_input_cities', $data);
    }
    function generate_city_admin_form($city) // Generates data to view an unsorted administration panel in which single fields can be enabled/disabled for a given city.
    {
        // Initiate the variable `$form`. This will be a multidimensional array used to store the entire form
        $form = NULL;
        
        // Gets all the fields from the db and  populates the form array with all the  fields
        foreach ($this->get_all_ui_fields() as $field)
        {
                // Update the `$form['fields']` array with the field's attributes.
                $fields[$field->name] = array(
                    'name'              => $field->name,
                    'label'             => $field->label,
                    'enabled'			=> '0'
                    );
        }
        
        // For each enabled field, changes the enabled attribute to 1
        foreach ($this->get_city_enabled_fields($city) as $city_enabled_field)  
        {
            $fields[$city_enabled_field->field_name]['enabled'] = '1';
        }

        // Gets all the fields from the db and finishes populating the form array with the disabled fields
        foreach ($this->get_all_ui_fields() as $field)
        {
            if ( ! isSet($fields[$field->name]))
            {
                // Update the `$form['fields']` array with the field's attributes.
                $fields[$field->name] = array(
                    'name'              => $field->name,
                    'label'             => $field->label,
                    'enabled'			=> '0'
                    );
            }
        }

        return $fields;

    }
    function update_city_fields ($city, $city_form) // Updates the list of enabled fields in the database_input_city_fields table in the db
    {
        // Delete all the current enabled fields for the city               <---- I feel this should be done in a smarter way ##########
        $this->db->delete('database_input_city_fields', array('city' => $city));

        // Reenter all the enabled fields in the table
        foreach ($city_form as $field)
        {
            if ($field['field_name'] != NULL)
                $this->db->insert('database_input_city_fields', $field);
        }	    
    }
    function create_validation_rules($type) // Creates a validation that is used for CI serverside validation
    {
        switch ($type) 
        {
            case 'text':
            $validation['CI'] = 'required|trim';
            $validation['js'] = 'required';
            break;
            case 'select':
            $validation['CI'] = 'required';
            $validation['js'] = 'required';
            break;
            case 'date':
            $validation['CI'] = 'required';
            $validation['js'] = 'required date';
            break;
            case 'number':
            $validation['CI'] = 'trim|required|integer';
            $validation['js'] = 'required digits';
            break;
            case 'decimal':
            $validation['CI'] = 'trim|required|numeric';
            $validation['js'] = 'required number';
            break;
            case 'checkbox':
            $validation['CI'] = '';
            $validation['js'] = '';
            break;            
            default:
            die('Cannot create validation rules for current field: wrong field type');
            break;
        }

        return $validation;
    }
    // Depending on the field type, returns an array with a DB friendly field type and eventually a constraint and a default value
    // This array can be passed directly to the CI Database Forge Class to add a field named $field_name
    function get_db_field_type($field_name, $field_type) 
    {
        switch ($field_type) 
        {
            case 'text':
            $field[$field_name]['type'] = 'VARCHAR';
            $field[$field_name]['constraint'] = 255;
            $field[$field_name]['null'] = FALSE;
            break;
            case 'select':
            $field[$field_name]['type'] = 'VARCHAR';
            $field[$field_name]['constraint'] = 255;
            $field[$field_name]['null'] = FALSE;
            break;
            case 'date':
            $field[$field_name]['type'] = 'DATE';
            $field[$field_name]['null'] = FALSE;
            break;
            case 'number':
            $field[$field_name]['type'] = 'MEDIUMINT';
            $field[$field_name]['constraint'] = 11;
            $field[$field_name]['null'] = FALSE;
            break;
            case 'decimal':
            $field[$field_name]['type'] = 'FLOAT';
            $field[$field_name]['constraint'] = 11;
            $field[$field_name]['null'] = FALSE;
            break;
            case 'checkbox':
            $field[$field_name]['type'] = 'BOOL';
            $field[$field_name]['default'] = 0;
            $field[$field_name]['null'] = FALSE;
            break;
            default:
            die('Cannot create db type for this field: illegal field type');
            break;
        }
        
        return $field;
    }
    
    // Depending on the fields type, a NR value is created 
    function create_empty_value($type) 
    {
        switch ($type) 
        {
            case 'text':
            $empty_value = 'NR';
            break;
            case 'select':
            $empty_value = 'NR';
            break;
            case 'date':
            $empty_value = '0000-00-00'; // Equivalent to NR
            break;
            case 'number':
            $empty_value = '0'; // Equivalent to NR
            break;
            case 'decimal':
            $empty_value = '0';
            break;
            case 'checkbox':
            $empty_value = '0'; // Equivalent to NR ???
            break;
            default:
            die('Cannot create empty field value to update old records: wrong field type');
            break;
        }
        return $empty_value;
    }
    
    function get_outdated_rows($db_table, $field_name) // Gets all the rows in $db_table for which the field named $field_name is empty
    {
        // Select the survey_ID of the entries for which the new field is empty (NULL). ie select all the entries that were already there
        $this->db->select('survey_ID');
        $this->db->from($db_table);
        $this->db->where($field_name, '');
        $query = $this->db->get();

        $old_rows = $query->result();
        $query->free_result();
        
        return $old_rows;
    }
    // Gets the info of the given tab
    function get_tab_info($tab_ID)
    {
        $this->db->select('*');
        $this->db->from('database_input_ui_tabs');
        $this->db->where('ID', $tab_ID);
        $query = $this->db->get();
        
        return $query->result_array();
    }
    function add_ui_field($field_details) // adds a new field to the database_input_ui_fields table
    {
        $this->db->insert('database_input_ui_fields', $field_details);
    }
    
    function add_db_field($db_table, $field_name, $field_type) // Adds a field to the $db_table named $field_name onf type $field_type
    {
        // The CI Database Forge Class is used to alter the DB structure (doc at: ./user_guide/database/forge.html)
        $db_field = $this->get_db_field_type($field_name, $field_type); // Get the field type and attributes formatted in db syntax
        $this->dbforge->add_column($db_table, $db_field);
    }
    
    function add_field($tab_ID, $label, $type) // Adds a new field that can be then used for every city
    {
        //Get the tab attributes
        $tab = $this->get_tab_info($tab_ID); 
        $db_table = $tab['0']['db_table'];
        
        
        // For tables that contain many to one relationships, the field is an array
        $is_array = $tab['0']['many_to_one'];

        // Querying on the display website is only functional for tables with a one-row-per-surey structure and for certain field types
        if (( ! $is_array) && ($type == 'select' || $type == 'number' || $type == 'decimal' || $type == 'checkbox'))
        {
            $allow_query = 1;
        }
        else
        {
            $allow_query = 0;
        }
        
        // Creates a htmlfriendly name for the field, (MORE RULES SHOULD BE ADDED!!!)
        $name = trim($label);
        $name = strtolower($name);
        $name = str_replace(' / ', '-', $name);
        $name = str_replace(' ', '_', $name);
        
        // Create validation rules
        $validation = $this->create_validation_rules($type);
        
        // Creates an array with the field's details so that this can be inserted in the database_input_ui_fields table
        $field_details = array(
            'tab_ID'    => $tab_ID,
            'db_table'  => $db_table,
            'name'      => $name,
            'type'      => $type,
            'is_array'  => $is_array,
            'allow_query'   => $allow_query,
            'label'         => $label,
            'validation'    => $validation['CI'],
            'js_validation' => $validation['js']
            );
        
        // Prepares an empty value (NR or equivalent) for the field based on its type
        $empty_value = $this->create_empty_value($type);
        
        //
        // Commit the changes to the database
        //
        
        // Start transaction. If any of the queries below fails, everything will be rolled back (docs: ./user_guide/database/transactions.html)
        $this->db->trans_start();
        
        // Create an entry in the table database_input_ui_fields. This is used later for the generation of the survey form and to set up the validation rules
        $this->add_ui_field($field_details);
        
        // Create a new field in the selected table so that the data can be inserted in it.
        $this->add_db_field($db_table, $name, $type);
        
        // For each existing outdated record, set the field that has just been added to NR (or its equivalent)
        foreach ($this->get_outdated_rows($db_table, $name) as $row)
        {
            $this->db->where('survey_ID', $row->survey_ID);
            $this->db->set($name, $empty_value); // Set the NR value (prev. determined on the field type) for the addedd field
            $this->db->update($db_table);
        }
        
        // End transaction
        $this->db->trans_complete();
        
        // Provide some hackish msg when transaction fails
        if ($this->db->trans_status() === FALSE)
        {
            die('Error, field not added (transaction failed)');
        }
        
    }
}

/* End of file admin_model.php */
/* Location: ./system/application/models/admin_model.php */