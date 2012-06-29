<?php 
/**
    * Survey-model
    *
    * @author	Martino Visintin (EWB 2010) (Email: martino.visintin@ewb-uk.org)
    *
    * For help about the Active Records Class in Code Igniter, refer to ./user_guide/database/active_record.html
    * Variables are not escaped as this is automatically executed by the Code Igniter Active Record class
    *
    */

if (! defined('BASEPATH')) exit('No direct script access');

class Survey_model extends Model {

    //php 5 constructor
    function __construct()
    {
        parent::Model();

        // Connect to database. Connection parameters are stored in application/config/database.php
        $this->load->database();
    }

    function get_all_cities()  // Select all cities from the db
    {
        $query = $this->db->get('database_input_cities');
        return $query->result();
    }

    function get_city_slums($city) // Get all the slums in a given city. 
    {
        $this->db->select('slum_name');
        $this->db->distinct();
        $this->db->from('SurveyDetail');
        $this->db->where('city', $city);
        $this->db->order_by('slum_name', 'asc');

        $query = $this->db->get();

        return $query->result();
    }

    // Gets all the select fields which are the ones which we want to query
    // allow_query is used by the data display website so leave it alone
    // This should be updated to select only the enabled fields for a given city.
    function get_city_queriable_fields($city)
    {
        $this->db->select('name, label, db_table');
        $this->db->from('database_input_ui_fields');
        $this->db->join('database_input_city_fields', 'database_input_city_fields.field_name = database_input_ui_fields.name');
        $this->db->where('type', 'select');
        $this->db->where('city', $city);
        $this->db->order_by('position', 'asc');

        $query = $this->db->get();

        return $query->result();
    }

    // For the given $field_ID (field should be of 'select' type), a set of distinct values is retrieved
    // Foreach value, the number of rows having that value is then counted
    function generate_distinct_values_percentages($field_name, $survey_IDs)
    {
        $db_table = $this->get_field_db_table($field_name);

        $this->db->select($field_name);
        $this->db->from($db_table);
        $this->db->distinct();
        $this->db->where_in('survey_ID', $survey_IDs);
        $this->db->order_by($field_name, 'asc');

        $value_query = $this->db->get();
        $value_result = $value_query->result_array();
        
        // Count the occurrencies of the value
        $no = $value_query->num_rows();
        
        
        if ($no == 0) //if there are no values for the given field in the given slum, then exit and return false
        {
            return FALSE;
        }
        else // else go on..
        {
            // Adds a total count to generate the percentage at the end
            $total_count = 0;

            // Foreach distinct value of the field count the number of occurrences of that value
            foreach ($value_result as $row)
            {
                // get the value of the field called $field_name
                $value = $row[$field_name];
                // if you do not understand the above check out the output of print_r($row);

                // Get a number of rows for each option
                $this->db->select($field_name);
                $this->db->from($db_table);
                $this->db->where($field_name, $value);
                $this->db->where_in('survey_ID', $survey_IDs);
                $count_query = $this->db->get();

                // Count the number of rows returned
                $count = $count_query->num_rows();

                // Insert $value and count in array so that it can be easily used by pChart 
                $distinct['counts'][] = $count;
                $distinct['labels'][] = $value;
                $distinct['legend_entries'][] = $value.' ['.$count.']';

                // Increase the total count
                $total_count = $total_count+$count;

                // free memory
                $count_query->free_result();

                unset($count_query);
                unset($count);
                unset($value);
            }


            // Calculate the percentages and store tehm in the array
            foreach ($distinct['counts'] as $count)
            {
                $distinct['percentages'][] = round(($count/$total_count)*100);
            }
            unset($total_count);

            // Free memory
            $value_query->free_result();
            
            // return teh array used by the pChart library for the pie grneration and for the table generation
            return $distinct;
        } // end of else
    } // end of function

    // returns the db_table name for a given field name
    // SHOULD BE UPDATED TO JOIN ui_tabs to get the db_table
    function get_field_db_table($field_name)
    {
        $this->db->select('db_table');
        $this->db->from('database_input_ui_fields');
        $this->db->where('name', $field_name);

        $query = $this->db->get();

        $result = $query->result_array();

        return $result['0']['db_table']; // return just the name of the db table
    }

    // Gets all the survey_ID s in SurveyDetail belonging to the given slum(s).
    // Return them as an array so that they can be used with $this->db->where_in() CI Active Records method
    function get_slum_survey_IDs($slum_names) 
    {
        $this->db->select('survey_ID');
        $this->db->from('SurveyDetail');
        $this->db->where_in('slum_name', $slum_names);

        $query = $this->db->get();

        // form array for where_in()
        foreach ($query->result() as $ID)
        {
            $IDs[] = $ID->survey_ID;
        }

        if (isset($IDs))
        {
            return $IDs;
        }
    }

    // Join all the one-to-one db tables using the survey_ID, then select only the relevant survey IDs and export the result to CSV
    // Only the city enabled fields are exported otherwise this would be a big ass query
    function join_and_export_tables($city, $db_tables, $survey_ID)
    {
        // Get the enabled fields for the city
        $enabled_fields = $this->get_city_enabled_fields($city);
        $select_fields = '';
        //print_r($enabled_fields);
        
        foreach ($enabled_fields as $field)
        {
            if ($field->is_array == 0) // Don't add many to one fields
            {
                $select_fields .= ', '.$field->name;
            }
        }
        unset($field);
        //print $select_fields;
        //die();

        // Load DButil for db to CSV export
        $this->load->dbutil();

        $this->db->select('SurveyDetail.survey_ID, city'.$select_fields);
        $this->db->from('SurveyDetail');

        foreach ($db_tables as $db_table)  // Join all the other tables ...
        {
            if ($db_table->db_table != 'SurveyDetail') // .. apart from surveydetail , which is already there
            {
                $this->db->join($db_table->db_table, 'SurveyDetail.survey_ID = '.$db_table->db_table.'.survey_ID', 'LEFT OUTER');
            }
        }

        $this->db->where_in('SurveyDetail.survey_ID', $survey_ID); 

        $query = $this->db->get();

        $result = $query->result_array();

        // $str = $this->db->last_query();
        // echo $str;

        // print_r($result);

        return $this->dbutil->csv_from_result($query); // export to csv

    }

    function get_one_to_one_db_tables()
    {
        $this->db->select('db_table, class');
        $this->db->from('database_input_ui_tabs');
        $this->db->distinct();
        $this->db->where('many_to_one', '0');

        $query = $this->db->get();

        return $query->result();
    }

    function get_many_to_one_db_tables()
    {
        $this->db->select('*');
        $this->db->from('database_input_ui_tabs');
        $this->db->distinct();
        $this->db->where('many_to_one', '1');

        $query = $this->db->get();

        return $query->result();
    }
    function get_all_db_tables()
    {
        $this->db->select('db_table');
        $this->db->from('database_input_ui_tabs');
        $this->db->distinct();

        $query = $this->db->get();

        return $query->result();
    }

    function export_entries_by_survey_IDs($db_table, $survey_IDs) // returns a CSV export of all the entries matching the survey_IDs contained in the array $survey_ID in a given db table
    {
        // Load DButil for db to CSV export
        $this->load->dbutil();

        $this->db->select('*');
        $this->db->from($db_table);
        $this->db->where_in('survey_ID', $survey_IDs);

        $query = $this->db->get();

        if ($this->db->count_all_results() > 0)  // Return the result only if there are actually rows to export
        {
            return $this->dbutil->csv_from_result($query);
        }
        else // Otherwise return false
        {
            return FALSE;
        }
    }

    function get_city_enabled_fields($city) // Get all enabled fields for the given city
    {
        $this->db->select('*');
        $this->db->order_by('position', 'asc');
        $this->db->from('database_input_city_fields');
        $this->db->join('database_input_ui_fields', 'database_input_ui_fields.name = database_input_city_fields.field_name');
        $this->db->like('city', $city);
        $this->db->order_by('position', 'asc');

        $query = $this->db->get();

        return $query->result();
    }

    function get_all_tabs() // Select all the tabs and their attributes from the db. Tabs are used solely for the display of the data in the view and have no effect on the final way in which the data is input into the database
    {
        $this->db->from('database_input_ui_tabs');
        $this->db->order_by('position', 'asc');
        $query = $this->db->get();

        return $query->result();
    }

    function get_all_fields() // Select all the available fields from the db.
    {
        $this->db->select('name');
        $this->db->order_by('position', 'asc');
        $this->db->from('database_input_ui_fields');

        $query = $this->db->get();

        return $query->result();
    }
    function get_all_db_table_fields($db_table) // Select all the fields belonging to a specific table from the db.
    {
        $this->db->select('*');
        $this->db->from('database_input_ui_fields');
        $this->db->where('db_table', $db_table);

        $query = $this->db->get();

        if ($this->db->count_all_results() > 0)
        {
            return $query->result();
        }
        else
        {
            return FALSE;
        }
    }

    function get_tab_fields($city,$tab_ID)   // Select all fields in the current tab that are required by the current city
    {
        $this->db->select('*');
        $this->db->order_by('position', 'asc');
        $this->db->from('database_input_city_fields');
        $this->db->join('database_input_ui_fields', 'database_input_city_fields.field_name = database_input_ui_fields.name');
        $this->db->like('city', $city);
        $this->db->where('tab_ID', $tab_ID);

        $query = $this->db->get();

        return $query->result();
    }

    function get_drop_down_options($field_ID)  // Get the drop down options (for `select` inputs) for a given $field_ID
    {
        $this->db->select('option_name');
        $this->db->order_by('option_name', 'asc');
        $this->db->from('database_input_ui_drop_down');
        $this->db->where('field_ID', $field_ID);

        $query = $this->db->get();

        foreach ($query->result() as $option)
        {
            $drop_down_options[$option->option_name] = $option->option_name;
        };

        // Add NA and NR at the end
        $drop_down_options['NA'] = 'NA';
        $drop_down_options['NR'] = 'NR';

        // Clear memory
        $query->free_result();

        return $drop_down_options;
    }

    function get_empty_value($type) // Returns an empty value for db insertion basing on a field type
    {
        switch ($type) 
        {
            case 'text':
            $empty_v = 'NR';
            break;
            case 'select':
            $empty_v = 'NR';
            break;
            case 'date':
            $empty_v = '0000-00-00'; // Equivalent to NR
            break;
            case 'number':
            $empty_v = '0'; // Equivalent to NR
            break;
            case 'decimal':
            $empty_v = '0';
            break;
            case 'checkbox':
            $empty_v = '0'; // Equivalent to NR ???
            break;
            default:
            die('Cannot create empty field value: illegal field type');
            break;
        }

        return $empty_v;
    }
    
    // Checks if the current field is explicitly `empty` (NR specified in the submitted form)
    // This is done so that $db_table_used does not become true. If all the fields for a particular table were explicitly declared empty, then no row is inserted in that table.
    // e.g. if there are no animals, the row is not inserted
    function submitted_field_is_full($field_name, $array_index)
    {
        
        // If the array index was supplied the field was an array . set $val to the value corresponding to the array index
        if ($array_index != -1)
        {
            $field_array = $this->input->post($field_name);
            $val = $field_array[$array_index];
        }
        // Else set $val to the submitted value
        else
        {
            $val = $this->input->post($field_name);
        }

        if ($val != 'NR' && $val != '0000-00-00' && $val != '0')
        {
            $field_full = TRUE;
        }
        else
        {
            $field_full = FALSE;
        }

        return $field_full;
    }
    
    // Checks wether or not there is a house with a particular $house_no in a given $city and $slum
    // Used to prevent duplicate insertions
    function duplicate_check($city, $slum, $house_no)
    {
        $this->db->select('*');
        $this->db->from('SurveyDetail');
        $this->db->where('city', $city);
        $this->db->where('slum', $slum);
        $this->db->where('house_no', $house_no);
        
        $query = $this->db->get();
        
        $no = $query->num_rows();
        
        if ($no == 0) {
            // nothing to report...
            return TRUE;
        } else {
            // Duplicate found!
            return FALSE;
        }
    }
    
    // Inserts the relevant survey row in the specified table
    // Supports a second, optional parameter to be passed if the field names are arrays. This is used in the insert_multiple_db_rows() method below
    function insert_single_survey_row($db_table, $array_index = -1)
    {

        // Prepare a variable for storing all the survey data belonging to the current table
        $survey_row = NULL;

        // The db table is initially considered unused
        $db_table_used = FALSE;

        // Get all the db table fields and prepare their values for db insert
        foreach ($this->get_all_db_table_fields($db_table) as $field)
        {
            // If the array index was supplied the field was an array . set $field_value to the value corresponding to the array index
            if ($array_index != -1)
            {
                $field_array = $this->input->post($field->name);
                $field_value = $field_array[$array_index];
            }
            // Else set $field_value to the submitted value
            else
            {
                $field_value = $this->input->post($field->name);
            }

            // Field is checked to see if it was explicitly set to NR
            $field_full = $this->submitted_field_is_full($field->name, $array_index);

            // If a value for a given form was subitted and it wasn't NR then the submitted value is input into the array db insertion
            // This does not include blank fields as CI would pick required blank fields up during validation
            if ($field_value != '' && $field_full) 
            {
                $survey_row[$field->name] = $field_value;

                // If there is a submitted field, the db table should be enabled
                $db_table_used = TRUE;
            }
            // If no value was submitted, the current field is not active for the current city or it was explicitly empty so the field's value should be NR.
            // In case of date or number, an NR equivalent is inserted (see get_empty_value() function)
            else
            {
                $survey_row[$field->name] = $this->get_empty_value($field->type);
            }
        }

        // Fetches the the survey_ID. This is the primary key in all the data tables and all are referenced to the survey_ID in SurveyDetail
        $survey_row['survey_ID'] = $this->input->post('survey_ID');

        // Insert the row into the db only if there is more than one active field in the survey form. (The survey_ID is always present but it shouldn't be inserted by itself)
        // Also if all the fields belonging the current db table were explicitly set to NR (i.e. if $db_table_used == FALSE), no data is inserted
        if (count($survey_row) > 1 && $db_table_used == TRUE)
        {
            $this->db->insert($db_table, $survey_row); 
            return TRUE;
        }
        else
        {
            return FALSE;
        }

        // Unset the $survey_row array
        unset($survey_row);
    }

    function insert_multiple_db_rows($db_table, $no_of_rows)
    {
        // repeats the insert_single_survey_row() for the given number of rows
        // $n is passed to the function to create an array postfix that can be attached to retrieve the correct value from the submitted form
        for ($i = 1; $i <= $no_of_rows; $i++)
        {
            $array_index = $i - 1;
            $this->insert_single_survey_row($db_table, $array_index);
        }
    }
    function generate_survey_form($city, $username)
    {
        // Initiate the variable `$form`. This will be a multidimensional array used to store the entire form (fields and their attributes organised by tabs)
        $form = NULL;

        // Create an unique surveyid. This is the primary key in the db and prevents duplicate etries to be entered and joining of tables
        $form['survey_ID'] = $city.$username.time();

        $form['username'] = $username;

        // Prepare so that city can be passed to the view and used to generate the form opening
        $form['city'] = $city;

        // For each tab
        foreach ($this->get_all_tabs() as $tab)
        {
            // Create a variable $tab_enabled. The initial value is FALSE (Tab is empty). If at least one field is found to be active within the tab, the value will be se to TRUE below (tab is not empty).
            $tab_enabled = FALSE;

            // Store field attributes in $form['fields'] for evey field in the current tab
            foreach ($this->get_tab_fields($city, $tab->ID) as $field)
            {

                // Update the `$form['fields']` array with the field's info.
                $form['fields'][$tab->class][$field->ID] = array(
                    'name'              => $field->name,
                    'label'             => $field->label,
                    'type'              => $field->type,
                    'is_array'          => $field->is_array,
                    'js_validation'     => $field->js_validation
                    );

                // If the field is a drop down menu, the drop down options are fetched and added to the array
                if ($field->type == 'select')
                {
                    $drop_down_options = $this->get_drop_down_options($field->ID);
                    $form['fields'][$tab->class][$field->ID]['drop_down_options'] = $drop_down_options;
                    unset($drop_down_options);
                }

                // Change $tab_enabled to TRUE. This means that the tab is not empty (i.e. there is at least one enabled field in it)
                $tab_enabled = TRUE;

            }

            // If the current tab is enabled, then store tab and its attributes in the $form['tabs'] array. This is used to generate tabs in the view
            if ($tab_enabled == TRUE)
            {
                $form['tabs'][$tab->ID] = array(
                    'class' => $tab->class,
                    'label' => $tab->label,
                    'many_to_one' => $tab->many_to_one
                    );
            }

        }
        return $form;
    }

}

/* End of file survey_model.php */
/* Location: ./system/application/models/survey_model.php */