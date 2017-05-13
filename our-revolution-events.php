<?php

/*

* Plugin Name: Our Revolution Events

* Description: Pulls JSON formatted events directly from the Our Revolution website via WP-Cron. File dump is used to display events.

* Version: 0.1

* Author: Ron O'Keefe

* Author URI: http://mdrising.org

*/



#wp_register_script( "OREJS", plugin_dir_url(__FILE__)."js/ore.js", array("json2","jquery"), "0.1");

#wp_register_style( 'ORECSS', plugin_directory_uri(__FILE__) . 'css/ore.css' );



function ore_scripts () {



	wp_register_style( 'ORECSS', plugin_dir_url(__FILE__) . 'css/ore.css' );



}

add_action( 'wp_enqueue_scripts', 'ore_scripts' );



// activation code

register_activation_hook( __FILE__, 'ore_activation' );

// add hook to pull data

add_action("ORE_MINER", 'ore_miner');

// add new cron schedule for 5 minutes

add_filter( 'cron_schedules', 'cron_add_5min' );

function cron_add_5min( $schedules ) {



    $schedules['5m'] = array(

        'interval' => 300,

        'display' => __( '5 Minutes' )

    );

 	return $schedules;

}

// schedule cron event to pull data

function ore_activation() {

    wp_schedule_event( time(), '5m' , 'ORE_MINER' );

}



// This is Cron executed code

function ore_miner () {



    $url = "https://go.ourrevolution.com/page/event/search_results?orderby=day&state=MD&country=US&format=json";

    #$message = "This is my IP address".$_SERVER['REMOTE_ADDR'];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_URL, $url);

    $result = curl_exec($ch);

    curl_close($ch);



    file_put_contents( plugin_dir_path(__FILE__)."data/ore_data.json", $result);

}



// This code will execute for page shortcodes to display events

function OR_Events () {



	//load style sheet

	wp_enqueue_style( 'ORECSS' );



    //open event data dump file

    $json = file_get_contents( plugin_dir_path(__FILE__)."data/ore_data.json");

    //parse json object

    $events = json_decode($json);



    foreach ($events->results as $event) {

        $date = new DateTime($event->start_dt);

        ?>

        <div class="ore-box">

            <h3 class="title"> <?php echo $event->name ?> </h3>

			<div class="date"> <?php echo date_format($date,'l F jS \a\t g:ia'); ?> </div>

			<div class="location"> <span class="venue"> <?php echo $event->venue_name ?></span><span class="city">(<?php echo $event->venue_city.",".$event->venue_state_cd ?>)</span></div>

			<div class="description"> <?php echo $event->description ?></div>

			<div class="ore-footer"><a href="<?php echo $event->url ?>" target="_blank">Learn More and RSVP &#187;</a></div>

        </div>

        <?php



    }







}



add_shortcode('ORE', "OR_Events");
