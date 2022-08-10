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

require __DIR__ . '/vendor/autoload.php';


/**
 * Main Progress_Meter Class
 *
 * @since 1.0.0
 */
class Progress_Meter
{
    /**
     * @var string Plugin Options
     */
    const PROGRESS_METER_OPTIONS = 'progress_meter_options';

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

        // Delete plugin options from database
        // $this->deleteOptions();

        // This adds support for a "progress-meter" shortcode
        add_shortcode( 'progress-meter', [$this, 'progressMeterWidget'] );
    }

    public function readSpreadSheetData()
    {
        error_log(__METHOD__ . ' +' . __LINE__ . PHP_EOL);

        $pluginOptions = get_option(self::PROGRESS_METER_OPTIONS, false);
        if (($pluginOptions !== false) && is_array($pluginOptions) && !empty($pluginOptions)) {
            error_log(__METHOD__ . ' +' . __LINE__ . ' Got saved $pluginOptions: ' . var_export($pluginOptions, true) . PHP_EOL);
            // TDOD: if we already have saved data do not make a request?
            echo '<script type="text/javascript">' . PHP_EOL .
                ' let config = {};' . PHP_EOL .
                ' console.log("+' . __LINE__ . ' config: ", config);' . PHP_EOL .
                ' config.campaignName = "' . $pluginOptions[0] . '";' . PHP_EOL .
                ' config.targetAmount = "' . $pluginOptions[1] . '";' . PHP_EOL .
                ' config.currentAmount = "' . $pluginOptions[2] . '";' . PHP_EOL .
                ' config.startingAmount = "' . $pluginOptions[3] . '";' . PHP_EOL .
                ' config.currency = "' . $pluginOptions[4] . '";' . PHP_EOL .
                ' console.log("+' . __LINE__ . ' config: ", config);' . PHP_EOL .
                '</script>';

            return true;
        }

        // Reading data from spreadsheet.
        $client = new \Google_Client();
        $client->setApplicationName('ALL Dev');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig(__DIR__ . '/credentials.json');
        $service = new Google_Service_Sheets($client);
        $spreadsheetId = '1qyjOXkxijm5cPXSwhKt6RKDwvEfDKU1OMwqNxDiUhPw'; // It is present in your URL
        // https://docs.google.com/spreadsheets/d/1qyjOXkxijm5cPXSwhKt6RKDwvEfDKU1OMwqNxDiUhPw/edit#gid=0
        $getRange = 'A2:E2';
        // Request to get data from spreadsheet.
        $response = $service->spreadsheets_values->get($spreadsheetId, $getRange);
        $values = $response->getValues();
        // error_log(__METHOD__ . ' +' . __LINE__ . ' $values: ' . var_export($values, true) . PHP_EOL);

        if (isset($values[0]) && is_array($values[0]) && !empty($values[0])) {
            if ($pluginOptions === false) {
                add_option(self::PROGRESS_METER_OPTIONS, $values[0]);
                // error_log(__METHOD__ . ' +' . __LINE__ . ' Added ' . self::PROGRESS_METER_OPTIONS . ' $values[0]: ' . var_export($values[0], true) . PHP_EOL);
            } else {
                update_option(self::PROGRESS_METER_OPTIONS, $values[0]);
                // error_log(__METHOD__ . ' +' . __LINE__ . ' Updated ' . self::PROGRESS_METER_OPTIONS . ' $values[0]: ' . var_export($values[0], true) . PHP_EOL);
            }

            echo '<script type="text/javascript">' . PHP_EOL .
                ' let config = {};' . PHP_EOL .
                ' console.log("+' . __LINE__ . ' config: ", config);' . PHP_EOL .
                ' config.campaignName = "' . $values[0][0] . '";' . PHP_EOL .
                ' config.targetAmount = "' . $values[0][1] . '";' . PHP_EOL .
                ' config.currentAmount = "' . $values[0][2] . '";' . PHP_EOL .
                ' config.startingAmount = "' . $values[0][3] . '";' . PHP_EOL .
                ' config.currency = "' . $values[0][4] . '";' . PHP_EOL .
                ' console.log("+' . __LINE__ . ' config: ", config);' . PHP_EOL .
                '</script>';

            return true;
        }
    }

    public function deleteOptions()
    {
        delete_option(self::PROGRESS_METER_OPTIONS);

        return true;
    }

    public function progressMeterWidget()
    {
        error_log(__METHOD__ . ' +' . __LINE__ . PHP_EOL);

        wp_enqueue_script("progressMeterScript",  plugin_dir_url( __FILE__ ) . "assets/js/progress-meter.js", [], self::$VER, true);
        wp_enqueue_style("progressMeterStyle",  plugin_dir_url( __FILE__ ) . "assets/css/progress-meter.css", [], self::$VER);

        // Read Google Spreadsheet data
        $this->readSpreadSheetData();

        $html = <<<EOH
<div class="progress-meter-wrapper">
	<div class="progress-meter-thermo-heading"></div>
	<div class="progress-meter-termometer">
		<div class="progress-meter-temperature" style="height:0" data-value="$0.00"></div>
		<div class="graduations"></div>
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
 * @since      1.0.0
 *
 */
function pm()
{
    return Progress_Meter::instance();
}

function pm_init()
{
    pm();
}

// Get Progress Meter running
add_action( 'plugins_loaded', 'pm_init', 9 );