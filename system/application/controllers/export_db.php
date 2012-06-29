<?php 
/**
    * Export_db
    *
    * @author	Martino Visintin (EWB 2010) (Email: martino.visintin@ewb-uk.org)
    *
    *
    */

if (! defined('BASEPATH')) exit('No direct script access');

class Export_db extends Controller {

    //php 5 constructor
    function __construct()
    {
        parent::Controller();
        $this->load->model('survey_model','sm');
        $this->load->helper(array('form', 'url'));
        
        // Connect to database. Connection parameters are stored in application/config/database.php
        $this->load->database();
    }
    
    function index()
    {
        $slum = $this->input->post('slum');
        $city = $this->input->post('city');
        $GIS = $this->input->post('GIS');
        
        // If a slum was submitted, export the given slum
        if ($slum != NULL)
        {
            if ($GIS == 1) // Export for geomedia
            {
                redirect('/export_db/export_GIS_slum/'.$city.'/'.$slum);
            }
            else // Export all db tables
            {
                redirect('/export_db/export_slum/'.$slum);
            }
            
        }
        else // Show the city & slum selection screen
        {
            // Insert the cities into the array that is passed to the view to generate drop down menu
            foreach ($this->sm->get_all_cities() as $city)
            {
                $data['cities'][$city->name] = $city->name;
            }

            $this->load->view('slum_selection', $data);			
        }

    }
    
    function get_city_slums($city) // Method called by jquery ajax to create slum dorpdown once the city has been selected
    {
        // Get the slums for the city 
        $dropdown_options = $this->sm->get_city_slums($city);
        
        // .. convert them in dropdown options for CI form helper
        foreach($dropdown_options as $option)
        {
            $slum_options[] = array(
                'option_value' => $option->slum_name,
                'option_label' => $option->slum_name,
                );
        }

        // make JSON object that will populate select dropdown menu options
        if(function_exists('json_encode') && IS_AJAX) //.. only if encode is there and the request is made through ajax
        {
            // this output is what the jQuery ajax request will receive and parse in its ajax callback function
            echo json_encode($slum_options); // this puts the php array in the funny javascript array/object format so you don't have to know how to translate manually
        }
        else
        {
            die('json encode not supported by the server or not an ajax request!!!');
        }

        
        exit;
    }
   
    function export_slum($slum)
    {   
        // Load zip helper to compress and download the CSV files
        $this->load->library('zip');
        
        // a bit of a workaround to pass the slum name to get_slum_survey_IDs (has to be array to work with where_in())
        $slums[] = $slum;
        // Get all the survey_ID s belonging the given slum in SurveyDetail
        $survey_IDs = $this->sm->get_slum_survey_IDs($slums);
        unset($slums);
        
        // Export all the associated entries in all db tables
        foreach ($this->sm->get_all_db_tables() as $db_table)
        {
            // Export the entries form the current table to CSV
            $csv_export = $this->sm->export_entries_by_survey_IDs($db_table, $survey_IDs);
            
            if ($csv_export != FALSE) // Don't export empty tables to save trees. ( export_entries_by_survey_IDs() returns false if there are no rows to export)
            {
                // Adds the CSV export as file named [slum_name]-[db_table].csv to a zip archive
                $this->zip->add_data($slum.'-'.$db_table->db_table.'.csv', $csv_export);
            }

            
            unset($csv_export);
        }

        // Downloads the zip archive as a file named [slum_name].zip
        $this->zip->download($slum.'.zip');
        
        // Redirect
        redirect('/export_db');
    }
    
    function export_GIS_slum($city, $slum)  // Exports all the one-to-one table as a single csv file that can be easily attached to Geomedia
    {
        // Load download helper to download the CSV files
        $this->load->helper('download');
        
        // a bit of a workaround to pass the slum name to get_slum_survey_IDs (has to be array to work with where_in())
        $slums[] = $slum;
        // Get all the survey_ID s belonging the given slum in SurveyDetail
        $survey_IDs = $this->sm->get_slum_survey_IDs($slums);
        unset($slums);
        
        // All the one to one db tables are selected
        $db_tables = $this->sm->get_one_to_one_db_tables();
        
        // Join all the one-to-one db table using the survey_ID, then select only the relevant survey IDs and export the result to CSV
        $csv_export = $this->sm->join_and_export_tables($city, $db_tables, $survey_IDs);
        
        // Downloads the CSV export as file named GIS-[slum_name].csv to a zip archive
        force_download('GIS-'.$slum.'.csv', $csv_export);
        
        // free memory 
        unset($csv_export);
        
        // Redirect
        redirect('/export_db');
    }
}