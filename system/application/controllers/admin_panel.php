<?php 
/**
    * Admin_panel
    *
    * @author	Martino Visintin (EWB 2010) (Email: martino.visintin@ewb-uk.org)
    *
    *
    */

if (! defined('BASEPATH')) exit('No direct script access');

class Admin_panel extends Controller {

    //php 5 constructor
    function __construct()
    {
        parent::Controller();

        $this->load->helper(array('form','url'));
        $this->load->model('admin_model', 'adm');

        // Connect to database. Connection parameters are stored in application/config/database.php
        $this->load->database();
    }
    
    function generate_city_admin_links()
    {
        // Get a list of the cities
        $cities = $this->adm->get_cities();
        
        
        if ($cities != FALSE) // if there are citeis...
        {
            $city_admin_links = NULL;
            
            foreach ($cities as $city)  // ... generate teh admin links for all the cities
            {
                $city_admin_links .= anchor('admin_panel/manage_city/'.$city->name, $city->name).'<br />';
            }
        }
        else // else show a notification
        {
            $city_admin_links = '<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;"> 
            					<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"><div style="display: inline; height: auto; position: absolute; visibility: hidden; width: auto; "><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
            					<strong>Warning!</strong></div></span>
            					<strong>Warning!</strong>There are currently no cities in the DB. You can start adding a new one below.</p>
            				</div>';
        }
        if (IS_AJAX) // if the request is done through ajax, print the links so that they can be loaded into the DOM by jQuery
        {
            echo $city_admin_links;
            return NULL;
        }
        else // otherwise return them to php for handling
        {
            return $city_admin_links;
        }
    }
    function index()
    {
        
        // City administration
        $data['city_admin_links'] = $this->generate_city_admin_links();
        
        // Field addition
        $data['tab_dropdown_options'] = $this->adm->get_tabs();

        // Dropdown administration
        $data['dropdowns'] = $this->adm->get_select_fields();
        
        // Load CI table class
        $this->load->library('table');
        
        $this->load->view('admin_panel/admin_panel', $data);
    }


    function manage_city($city)
    {
        $data['form'] = $this->adm->generate_city_admin_form($city);
        $data['city'] = $city;

        // Loads required libraries and views
        $this->load->library('table');
        $this->load->view('admin_panel/manage_city', $data);
    }
    
    function update_city_fields($city)
    {
        $city_form = NULL;

        foreach ($this->adm->get_all_ui_fields() as $field)
        {
            $city_form[$field->name] = array(
                'field_name'  => $this->input->post($field->name),
                'city'        => $city,
                );
        }

        $this->adm->update_city_fields($city,$city_form);
        redirect('/admin_panel/manage_city/'.$city.'/1', 'redirect');

    }

    function add_city() // adds a new city, used through ajax
    {
        $city = $this->input->post('city_name');
        
        $this->adm->add_city($city);
        
        return TRUE;
    }

    // loads the options for a give n field_ID and displays them in a view that adds Delete and Add functionality
    function manage_dropdown_options($field_ID) 
    {
        $data['field_ID'] = $field_ID;
        $data['dropdown_options'] = $this->adm->get_drop_down_options($field_ID);

        // Loads required libraries and views
        $this->load->library('table'); 
        $this->load->view('admin_panel/manage_dropdown_options', $data); 

    }
    function add_dropdown_option($field_ID)
    {
        $this->adm->add_dropdown_option($field_ID, $this->input->post('option_name'));
        redirect('/admin_panel/manage_dropdown_options/'.$field_ID, 'redirect');
    }
    function delete_dropdown_option($option_ID, $field_ID)
    {
        
        $this->adm->delete_dropdown_option($option_ID);
        redirect('/admin_panel/manage_dropdown_options/'.$field_ID, 'redirect');
    }

    function add_field() // Adds a new question, used through ajax
    {
        $tab_ID = $this->input->post('tab_ID');
        $label = $this->input->post('label');
        $type = $this->input->post('type');
        
        // The CI Database Forge Class is used to alter the DB structure
        $this->load->dbforge();
        
        $this->adm->add_field($tab_ID, $label, $type);
        
        return NULL;
    }
    
    function optimise_db()
    {
        $this->load->dbutil();
        
        $result = $this->dbutil->optimize_database();
        redirect('/admin_panel', 'redirect');
    }
    
    function backup_db()
    {
        // Load the DB utility class
        $this->load->dbutil();

        // Backup your entire database and assign it to a variable
        $backup =& $this->dbutil->backup(); 

        // Load the file helper and write the file to your server
        //$this->load->helper('file');
        //write_file('/path/to/mybackup.gz', $backup); 

        // Load the download helper and send the file to your desktop
        $this->load->helper('download');
        force_download('db_backup.gz', $backup);
        
        redirect('/admin_panel', 'redirect');
    }
    
    function reorder_fields()
    {
        $data['fields'] = $this->adm->get_all_ui_fields();
        $this->load->view('admin_panel/reorder_fields', $data);
    }
    
    function update_field_order()
    {
        $fields = $this->input->post('fields');
        
        foreach ($fields as $position => $field_ID)
        {
            $this->adm->update_field_position($field_ID, $position);
        }
    }
    
    function clear_charts() // empties the chart directory from all teh charts
    {
        $this->SureRemoveDir('charts', FALSE);
        redirect('/admin_panel', 'redirect');
    }
    
    // Empties the directory $dir if $DeleteMe is false, it deletes it if $deleteme is true
    // snippet from: http://snippets.dzone.com/posts/show/5004
    function SureRemoveDir($dir, $DeleteMe) { 
        
        if(!$dh = @opendir($dir)) return;
        while (false !== ($obj = readdir($dh))) {
            if($obj=='.' || $obj=='..') continue;
            if (!@unlink($dir.'/'.$obj)) SureRemoveDir($dir.'/'.$obj, true);
        }

        closedir($dh);
        if ($DeleteMe){
            @rmdir($dir);
        }
    }
    
    // function syncronise_ui_db_tables() // Quick and very dirty Hack to update the db_table field in the ui_fieds // In the future a join should be performed to get the db_table of a field
    //  {
    //      $this->db->select('*');
    //      $this->db->from('database_input_ui_fields');
    //      $this->db->join('database_input_ui_tabs', 'database_input_ui_fields.tab_ID = database_input_ui_tabs.ID');
    //      $q = $this->db->get();
    //      $ui_fields = $q->result();
    //      
    //      foreach ($ui_fields as $ui_field)
    //      {
    //          $this->db->set('db_table', $ui_field->updb_table);
    //          $this->db->where('name', $ui_field->name);
    //          $this->db->update('database_input_ui_fields');
    //      }
    //  }
    
    // function add_db_fields_from_ui_fields() // extremely dirty hack to ensure that for each entry in the ui_fields table, a corresponding field is present in the related db table.
    // {
    //     //
    //     $this->load->dbforge();
    // 
    //     $this->db->select('*');
    //     $this->db->from('database_input_ui_fields');
    //     $q = $this->db->get();
    //     $ui_fields = $q->result();
    //     $i = 1;
    //     foreach ($ui_fields as $ui_field)
    //     {
    //         $this->adm-> add_db_field($ui_field->db_table, $ui_field->name, $ui_field->type);
    //         echo '['.$i.']---'.$ui_field->db_table.'---'.$ui_field->name.'----'.$ui_field->type;
    //         $i++;
    //     }
    // }
}

/* End of file admin_panel.php */
/* Location: ./system/application/controllers/admin_panel.php */