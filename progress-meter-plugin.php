<?php
/**
 * Plugin Name:  Progress Meter
 * Plugin URI:   https://github.com/asianlegacylibrary/progress-meter-plugin
 * Description:  Create progress meters.
 * Version:      1.0.0
 * Author:       Asian Legacy Library
 * Author URI:   https://asianlegacylibrary.org/
 * License:      GPL2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  progress-meter
 * Domain Path:  /languages/
 *
 * @package     ProgressMeter
 * @author      Asian Legacy Library
 * @copyright   Copyright (c) 2022, Asian Legacy Library
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main Progress_Meter Class
 *
 * @since 1.0.0
 */
class Progress_Meter
{
    /**
     * @var string Plugin Name
     */
    const PROGRESS_METER_WRAPPER = 'progress-meter-wrapper';

    /**
     * @var string Plugin Name
     */
    public static $NAME = 'Progress Meter';

    /**
     * @var string Plugin Version
     */
    public static $VER = '1.0.0';

    /**
     * @var Progress_Meter The one true Progress_Meter
     */
    private static $instance;

    /**
     * Main instance
     *
     * @return Progress_Meter
     */
    public static function instance()
    {
        error_log(__METHOD__ . ' +' . __LINE__ . PHP_EOL);

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Popup_Maker ) ) {
            self::$instance = new Progress_Meter;
            self::$instance->init();
        }

        return self::$instance;
    }

    public function init()
    {
        error_log(__METHOD__ . ' +' . __LINE__ . PHP_EOL);

        // This adds support for a "progress-meter" shortcode
        add_shortcode( 'progress-meter', [$this, 'progressMeterWidget'] );
    }

    public function progressMeterWidget()
    {
        error_log(__METHOD__ . ' +' . __LINE__ . PHP_EOL);

        wp_enqueue_script("progressMeterScript",  plugin_dir_url( __FILE__ ) . "assets/js/progress-meter.js",[], self::$VER, true);
        wp_enqueue_style( "progressMeterStyle",  plugin_dir_url( __FILE__ ) . "assets/css/progress-meter.css", [], self::$VER );

        $html = <<<EOH
<div id="progress-meter-wrapper">
	<div id="progress-meter-thermo-heading"></div>
	<div id="progress-meter-termometer">
		<div id="progress-meter-temperature" style="height:0" data-value="$0.00"></div>
		<div id="graduations"></div>
	</div>
</div>
EOH;
        echo $html;
    }
}

/**
 * The main function responsible for returning the one true Progress_Meter
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @return Progress_Meter
 * @since      1.8.0
 *
 */
function pm()
{
    error_log(__METHOD__ . ' +' . __LINE__ . PHP_EOL);
    return Progress_Meter::instance();
}

function pm_init()
{
    error_log(__METHOD__ . ' +' . __LINE__ . PHP_EOL);
    pm();
}

// Get Progress Meter running
add_action( 'plugins_loaded', 'pm_init', 9 );