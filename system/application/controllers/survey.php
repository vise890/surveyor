<?php
/**
    * Survey
    *
    * @author	Martino Visintin (EWB 2010) (Email: martino.visintin@ewb-uk.org)
    */

if (! defined('BASEPATH')) exit('No direct script access');

class Survey extends Controller {

    //php 5 constructor
    function __construct() 
    {
        parent::Controller();

        $this->load->helper(array('form','url'));
        $this->load->library('form_validation');
        $this->load->model('survey_model','sm');

        // Connect to database. Connection parameters are stored in application/config/database.php
        $this->load->database();

    }

    function index()
    {
        redirect('/survey/city_selection', 'location');
    }

    function city_selection()
    {

        $city = $this->input->post('city');
        $username = trim($this->input->post('username'));
        $username = strtolower($username);
        $usrername = str_replace(' ', '', $username);
        $usrername = str_replace('/', '_', $username);

        // If a city was submitted, redirect to the city's survey form
        if ($city != NULL)
        {
            redirect('/survey/survey_form/'.$city.'/'.$username);
        }
        else // Show the city selection screen
        {
            $data['username'] = $this->uri->segment('3');

            // Insert the cities into the array that is passed to the view to generate drop down menu
            foreach ($this->sm->get_all_cities() as $city)
            {
                $data['cities'][$city->name] = $city->name;
            }
            
            // Used by the view
            $this->load->library('table');
            
            //Load view
            $this->load->view('city_selection', $data);			
        }

    }

    function survey_form($city, $username) // Handles the display, the server side validation and the db entry of the survey entry form
    {	
        // Generate the survey form for the current city, The username is needed to generate the survey_ID
        $form = $this->sm->generate_survey_form($city, $username);

        // Set up Code Igniter (server side) validation rules
        foreach ($this->sm->get_city_enabled_fields($city) as $field) 
        {
            // An array postfix is created for array inputs.
            if ($field->is_array == TRUE)
            {
                $input_name_postfix = '[]';
            }
            if ($field->is_array != TRUE) // This is a workaround for the mess up of added rows that are left blank. A smarter way should be worked out and this condition should be removed
            {
                $this->form_validation->set_rules($field->name.@$input_name_postfix, $field->label, $field->validation);
            }
        }

        // Load the form again if validation fails
        if ($this->form_validation->run() == FALSE)
        {
            // A message is shown if there are no active fields in the current city
            if ( ! isSet($form['fields']))
            {
                $data['title'] = 'Error';
                $data['message'] = 'There are no active questions for this city, go to the admin panel and activate some!';
                $data['link'] = array(
                    'href'  => 'admin_panel/admin_panel',
                    'label' => 'Go to admin panel'
                    );

                $this->load->view('message', $data);
            }
            else // If the form is set (i.e. there are active fields for the city), the survey_form view is loaded
            {
                $this->load->library('table');
                $this->load->view('survey_form', $form);
            }
        }
        // If the validation is successful, the data is inserted into the db
        else 
        {
            // the whole thing is wrapped in a transaction so that CI can roll back changes if something happens (no incomplete data!)
            $this->db->trans_start();
            
            // Db tables in which data is stored in just one row per survey (no many to one relationships) (e.g. SurveyDetail)
            //Foreach db table, insert the relevant row into the db
            foreach($this->sm->get_one_to_one_db_tables() as $db_table)
            {
                $this->sm->insert_single_survey_row($db_table->db_table);
            }

            // Db tables in which data could be more rows per survey (many to one relationship) (e.g FamilyDetail)            
            // Foreach db table, prepare the relevant rows and insert them into the db
            foreach($this->sm->get_many_to_one_db_tables() as $db_table)
            {
                
                // Table name
                $db_table_name = $db_table->db_table;
                
                // Class of the tab the db_table corresponds to
                $tab_class = $db_table->class;
                
                // Find out how many rows were submitted.
                // This is done by counting the array from the hidden field named 'count'.$tab_class in the view <--- SMARTER WAY NEEDED
                $count_array = $this->input->post('count'.$tab_class);
                $no_of_rows = count($count_array);
                
                /// SMARTER WAY NEEDED ^^^^^^^^^^^^^^^^^
                
                $this->sm->insert_multiple_db_rows($db_table_name, $no_of_rows);
                
                unset($no_of_rows);
            }

            // Updates city in the Survey Details table in the DB <--- SMARTER WAY NEEDED ###########
            $this->db->set('city', $this->input->post('city')); 
            $this->db->where('survey_ID', $this->input->post('survey_ID'));
            $this->db->update('SurveyDetail');
            // **************************************************************************************
            
            // End of db transaction
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE)
            {
                // Show a failure message with a link to a fresh form for the current city and user
                $data['title'] = 'Failure';
                $data['message'] = 'There was a problem with the survey insertion, the data you have input has not been inserted. Hit back to try again';
                $data['link'] = array(
                    'href'  => 'survey/survey_form/'.$this->input->post('city').'/'.$username,
                    'label' => 'Add another (NEW) survey'
                    );
                $this->load->view('message', $data);
                
            }
            else
            {
                // Show a success message with a link to a fresh form for the current city and user
                $data['title'] = 'Success';
                $data['message'] = 'The survey has been successfully inserted in the database';
                $data['link'] = array(
                    'href'  => 'survey/survey_form/'.$this->input->post('city').'/'.$username,
                    'label' => 'Add another survey'
                    );
                $this->load->view('message', $data);
            }
        }
    }
}

/* End of file survey.php */
/* Location: ./system/application/controllers/survey.php */