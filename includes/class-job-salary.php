<?php
/**
 * Job_Salary setup
 *
 * @package Job_Salary
 */

namespace BengalStudio\Job_Salary;

defined( 'ABSPATH' ) || exit;

/**
 * Main Job_Salary Class.
 */
final class Job_Salary {

	/**
	 * The single instance of the class.
	 *
	 * @var Job_Salary
	 */
	protected static $_instance = null; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Main Job_Salary Instance.
	 * Ensures only one instance of Job_Salary is loaded or can be loaded.
	 *
	 * @return Job_Salary - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Job_Salary Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
	}

	/**
	 * Define CJS Constants.
	 */
	private function define_constants() {
		define( 'JOB_SALARY_VERSION', '0.1.0' );
		define( 'JOB_SALARY_ABSPATH', dirname( JOB_SALARY_PLUGIN_FILE ) . '/' );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 * e.g. include_once JOB_SALARY_ABSPATH . 'includes/foo.php';
	 */
	private function includes() {
		include_once JOB_SALARY_ABSPATH . 'includes/util.php';
	}

	/**
	 * Get the URL for the Job_Salary plugin directory.
	 *
	 * @return string URL
	 */
	public static function plugin_url() {
		return untrailingslashit( plugins_url( '/', JOB_SALARY_PLUGIN_FILE ) );
	}
}

Job_Salary::instance();