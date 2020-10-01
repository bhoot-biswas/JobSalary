<?php
/**
 * Plugin Name:     Job Salary
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

defined( 'ABSPATH' ) || exit;

// Define JOB_SALARY_SEARCH_PLUGIN_FILE.
if ( ! defined( 'JOB_SALARY_SEARCH_PLUGIN_FILE' ) ) {
	define( 'JOB_SALARY_SEARCH_PLUGIN_FILE', __FILE__ );
}

// Include the main Custom_Job_Search class.
if ( ! class_exists( 'Custom_Job_Search' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-job-salary.php';
}