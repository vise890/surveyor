<?php 
/**
    * Report
    *
    * @author	Martino Visintin (EWB 2010) (Email: martino.visintin@ewb-uk.org)
    * 
    * Uses the codeigniter library 'Charts' (docs: http://192.168.1.100/houselevel/user_guide/libraries/charts.html) (OR http://www.bluecardassistance.net/lib/pChart/user_guide/libraries/charts.html)
* which is a port of pCharts (docs: http://pchart.sourceforge.net/documentation.php)
*
    */

if (! defined('BASEPATH')) exit('No direct script access');

class Report extends Controller {

    //php 5 constructor
    function __construct()
    {
        parent::Controller();
        $this->load->model('survey_model','sm');
        $this->load->helper(array('form', 'url'));

        // Load up the 'Charts' library to make charts!
        $this->load->library('charts');

        // Connect to database. Connection parameters are stored in application/config/database.php
        $this->load->database();
    }

    function index()
    {
        // Insert the cities into the array that is passed to the view to generate drop down menu
        foreach ($this->sm->get_all_cities() as $city)
        {
            $data['cities'][$city->name] = $city->name;
        }

        // Used by the view to arrange the form in a table. AND NO I DON'T CARE ABOUT W3 silly specifications. Google does it as well on his freaking homepage
        $this->load->library('table');

        // Show the city, slum(s) & field selection screen
        $this->load->view('view_report', $data);
    }

    function get_city_slums() // Method called by jquery ajax .post() to create slum dorpdown once the city has been selected
    {
        $city = $this->input->post('city');

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

    function get_city_queriable_fields() // Method called by jquery ajax .post() to create field dorpdown once the city has been selected
    {
        $city = $this->input->post('city');

        // Get the enabled, queriable fields for the city 
        $dropdown_options = $this->sm->get_city_queriable_fields($city);

        // .. convert them in dropdown options for CI form helper
        foreach($dropdown_options as $option)
        {
            $field_options[] = array(
                'option_value' => $option->name,
                'option_label' => $option->label,
                );
        }

        // make JSON object that will populate select dropdown menu options
        if(function_exists('json_encode') && IS_AJAX) //.. only if encode is there and the request is made through ajax
        {
            echo json_encode($field_options); 
        }
        else
        {
            die('json encode not supported by the server or not an ajax request!!!');
        }

        exit;
    }

    // Generates a pie chart with the chart library.
    // 150 is the default radius of the pie. It automatically influences the size of the image, but don't go mad, large images take up space and resources!
    function generate_pie_chart($percentages, $labels, $chart_title, $pie_radius = 150)
    {
        if (count($labels) == 1) { $splice_distance = 0; } else {$splice_distance = 13; }// Do not split graphs with only one series

        $config = array(
            'SpliceDistance'    => $splice_distance, // For exploded pie
            'Skew'              => 65, // For 3D awesomeness
            'TitleFontSize'     => 16,
            'TitleBGR'          => 255, // Background Red component
            'TitleBGG'          => 255, // BG Green component
            'TitleBGB'          => 255, // BG Blue component
            );
        $image = $this->charts->pieChart($pie_radius, $percentages, $labels, '', $chart_title, '',$config);

        return $image;
    }

    // Generates a bar chart with the chart library.
    function generate_bar_chart($percentages, $labels, $chart_title)
    {
        $config = array(
            'TitleFontSize'     => 16,
            // 'TitleBGR'          => 255, // Background Red component
            // 'TitleBGG'          => 255, // BG Green component
            // 'TitleBGB'          => 255, // BG Blue component
            // 'Textbox'           => $chart_title,
            'Ylabel'            => 'percentage(%)',
            'Xlabel'            => $chart_title,
            // "ImgR"              => 255,
            // "ImgB"              => 255,
            // "ImgG"              => 255,
            );
            
        // the barChart method should be improved and used instead. this is ugly as hell
        $image = $this->charts->cartesianChart('bar', $labels, $percentages, 500, 250, '',$config);
        
        // the barChart method should be improved and used instead. this is ugly as hell

        return $image;
    }

    // Generates a report (now this is only a chart), only usable through ajax
    function generate_report()
    {
        error_reporting(E_ALL ^ E_NOTICE); // Workaround to hide notices when there are more than 8 data points in pie charts
        // Should be updated, pChart fails to create new colorus for the pie when there are more than 8 datapoints
        // Another charting library should be used or you should look into fixing the library charts.php (which i believe is the cause)
        
        
        $slums = $this->input->post('slums'); // from view input
        $field_name = $this->input->post('field'); // from view input
        $chart_type = $this->input->post('chart_type');
    
        // If slum is not suelected, show a notice .. NOT WORKING !!!???!!!
        if ( count($slums) == 0 )
        {
            echo '<p class="notice" align="center"> Select a slum!</p>';
            die();
        }

        // Get the survey IDs for the selected slums
        $survey_IDs = $this->sm->get_slum_survey_IDs($slums);

        // For the selected field and slums (survey_IDs), generate an array containing the data series to pass to the chart library
        // the array contains labels, absolute values (counts) and percentages
        $chart = $this->sm->generate_distinct_values_percentages($field_name, $survey_IDs);
        
        // if $chart is false, that means there were no values for the given question in the given slum
        // .. so exit with a notice
        if ($chart == FALSE)
        {
            echo 
                '<div class="ui-state-highlight ui-corner-all" align="center" style="margin-top: 20px; padding: 0 .7em; width: 100%;"> 
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
        else // otherwise , go on ..
        {
            // The title of the chart is the label of the queried field
            $chart_title = $field = $this->input->post('field_label');

            // Depending on the selected chart type, a chart is generated
            switch ($chart_type)
            {
                case 'pie':
                    $image = $this->generate_pie_chart($chart['percentages'], $chart['legend_entries'], $chart_title);
                    break;
                case 'bar':
                    $image = $this->generate_bar_chart($chart['percentages'], $chart['legend_entries'], $chart_title);
                    break;
            }


            // Generate a table with the data
            $this->load->library('table');
            $this->table->set_heading($chart_title, 'Number', 'Percentage (%)');

            $array_length = count($chart['labels']);
            for ($i = 0; $i < $array_length; $i++)
            {
                $this->table->add_row($chart['labels'][$i], $chart['counts'][$i], $chart['percentages'][$i].'%');
            }

            $tmpl = array (
                                'table_open'          => '<table border="0" cellpadding="4" align="center" cellspacing="0">',
                          );

            $this->table->set_template($tmpl);

            if (IS_AJAX)
            {
                // print the image tag containing the pie chart
                echo '<img src="'.base_url().$image['name'].'" width="'.$image['w'].'" height="'.$image['h'].'" /><br />';

                // print the table
                echo $this->table->generate();
            }
        } // end of else
    }

}