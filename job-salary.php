<?php
/**
 * Plugin Name:     Job Salary for for WP Job Manager
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     job-salary
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Job_Salary
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants.
define( 'JOB_SALARY_VERSION', '1.34.3' );
define( 'JOB_SALARY_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'JOB_SALARY_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'JOB_SALARY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Require the main Job_Salary class.
require_once dirname( __FILE__ ) . '/includes/class-job-salary.php';
