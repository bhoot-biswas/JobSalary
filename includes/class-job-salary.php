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
		$this->init();
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
	 * Init hooks.
	 * @return [type] [description]
	 */
	private  function init() {
		add_filter( 'submit_job_form_fields', array( $this, 'frontend_add_salary_fields' ) );
		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'admin_add_salary_field' ) );
		add_action( 'single_job_listing_meta_end', array( $this, 'display_job_salary_data' ) );
		add_filter( 'wpjm_get_job_listing_structured_data', array( $this, 'add_basesalary_data' ) );
		add_action( 'job_manager_job_filters_search_jobs_end', array( $this, 'filter_by_salary_field' ) );
		add_filter( 'job_manager_get_listings', array( $this, 'filter_by_salary_field_query_args' ), 10, 2 );
		add_filter( 'manage_edit-job_listing_columns', array( $this, 'retrieve_salary_column' ) );
		add_filter( 'manage_job_listing_posts_custom_column', array( $this, 'display_salary_column' ) );
	}

	/**
	 * Add frontend salary fields.
	 * @param [type] $fields [description]
	 */
	public function frontend_add_salary_fields( $fields ) {
		$fields['job']['job_salary_pay_scale'] = array(
			'label'    => __( 'Salary Pay Scale', 'job-salary' ),
			'type'     => 'select',
			'required' => false,
			'priority' => 8,
			'options'  => $this->get_salary_pay_scale_options(),
		);

		$fields['job']['job_salary_minimum'] = array(
			'label'       => __( 'Salary Minimum ($)', 'job-salary' ),
			'type'        => 'text',
			'required'    => false,
			'placeholder' => 'e.g. 10000',
			'priority'    => 9,
		);

		$fields['job']['job_salary_maximum'] = array(
			'label'       => __( 'Salary Maximum ($)', 'job-salary' ),
			'type'        => 'text',
			'required'    => false,
			'placeholder' => 'e.g. 20000',
			'priority'    => 10,
		);
		return $fields;
	}

	/**
	 * Add admin salary field.
	 * @param [type] $fields [description]
	 */
	public function admin_add_salary_field( $fields ) {
		$fields['_job_salary_pay_scale'] = array(
			'label'       => __( 'Salary Pay Scale', 'job-salary' ),
			'type'        => 'select',
			'description' => '',
			'options'     => $this->get_salary_pay_scale_options(),
		);
		$fields['_job_salary_minimum']   = array(
			'label'       => __( 'Salary Minimum ($)', 'job-salary' ),
			'type'        => 'text',
			'placeholder' => 'e.g. 10000',
			'description' => '',
		);
		$fields['_job_salary_maximum']   = array(
			'label'       => __( 'Salary Maximum ($)', 'job-salary' ),
			'type'        => 'text',
			'placeholder' => 'e.g. 10000',
			'description' => '',
		);
		return $fields;
	}

	/**
	 * Display job salary data.
	 * @return [type] [description]
	 */
	public function display_job_salary_data() {
		global $post;

		$salary = get_post_meta( $post->ID, '_job_salary', true );

		if ( $salary ) {
			echo '<li>' . __( 'Salary:' ) . ' $' . esc_html( $salary ) . '</li>';
		}
	}

	/**
	 * Add Google structured data.
	 * @param [type] $data [description]
	 */
	public function add_basesalary_data( $data ) {
		global $post;

		$data['baseSalary']                      = array();
		$data['baseSalary']['@type']             = 'MonetaryAmount';
		$data['baseSalary']['currency']          = 'USD';
		$data['baseSalary']['value']             = array();
		$data['baseSalary']['value']['@type']    = 'QuantitativeValue';
		$data['baseSalary']['value']['value']    = get_post_meta( $post->ID, '_job_salary', true );
		$data['baseSalary']['value']['unitText'] = 'YEAR';

		return $data;
	}

	/**
	 * This can either be done with a filter (below) or the field can be added directly to the job-filters.php template file!
	 * @return [type] [description]
	 */
	public function filter_by_salary_field() {
		?>
		<div class="search_salary">
			<label for="search_salary"><?php _e( 'Salary', 'job-salary' ); ?></label>
			<select name="filter_by_salary" class="job-manager-filter">
				<option value=""><?php _e( 'Any Salary', 'job-salary' ); ?></option>
				<option value="upto20"><?php _e( 'Up to $20,000', 'job-salary' ); ?></option>
				<option value="20000-40000"><?php _e( '$20,000 to $40,000', 'job-salary' ); ?></option>
				<option value="40000-60000"><?php _e( '$40,000 to $60,000', 'job-salary' ); ?></option>
				<option value="over60"><?php _e( '$60,000+', 'job-salary' ); ?></option>
			</select>
		</div>
		<?php
	}

	/**
	 * This code gets your posted field and modifies the job search query
	 * @param  [type] $query_args [description]
	 * @param  [type] $args       [description]
	 * @return [type]             [description]
	 */
	public function filter_by_salary_field_query_args( $query_args, $args ) {
		if ( isset( $_POST['form_data'] ) ) {
			parse_str( $_POST['form_data'], $form_data );

			// If this is set, we are filtering by salary
			if ( ! empty( $form_data['filter_by_salary'] ) ) {
				$selected_range = sanitize_text_field( $form_data['filter_by_salary'] );
				switch ( $selected_range ) {
					case 'upto20':
						$query_args['meta_query'][] = array(
							'key'     => '_job_salary',
							'value'   => '20000',
							'compare' => '<',
							'type'    => 'NUMERIC',
						);
						break;
					case 'over60':
						$query_args['meta_query'][] = array(
							'key'     => '_job_salary',
							'value'   => '60000',
							'compare' => '>=',
							'type'    => 'NUMERIC',
						);
						break;
					default:
						$query_args['meta_query'][] = array(
							'key'     => '_job_salary',
							'value'   => array_map( 'absint', explode( '-', $selected_range ) ),
							'compare' => 'BETWEEN',
							'type'    => 'NUMERIC',
						);
						break;
				}

				// This will show the 'reset' link
				add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );
			}
		}
		return $query_args;
	}

	/**
	 * Sets the job_salary metadata as a new $column that can be used in the back-end.
	 * @param  [type] $columns [description]
	 * @return [type]          [description]
	 */
	public function retrieve_salary_column( $columns ) {
		$columns['job_salary'] = __( 'Salary', 'job-salary' );
		return $columns;
	}

	/**
	 * Display salary column in the back-end.
	 * @param  [type] $column [description]
	 * @return [type]         [description]
	 */
	public function display_salary_column( $column ) {
		global $post;

		switch ( $column ) {
			case 'job_salary':
				$salary = get_post_meta( $post->ID, '_job_salary', true );

				if ( ! empty( $salary ) ) {
					echo $salary;
				} else {
					echo '-';
				}
				break;
		}

		return $column;
	}

	/**
	 * Get salary pay scale options.
	 * @var [type]
	 */
	public function get_salary_pay_scale_options() {
		$job_salary_pay_scales        = $this->get_salary_pay_scales();
		$job_salary_pay_scale_options = array(
			'' => __( 'Please select an option', 'job-salary' ),
		);

		for ( $i = 1; $i <= 20; $i++ ) {
			if ( isset( $job_salary_pay_scales[ $i ][1] ) ) {
				$job_salary_pay_scale_options[ $i ] = \sprintf( __( 'Grade %1$d (%2$d - %3$d)', 'job-salary' ), $i, $job_salary_pay_scales[ $i ][0], $job_salary_pay_scales[ $i ][1] );
			} else {
				$job_salary_pay_scale_options[ $i ] = \sprintf( __( 'Grade %1$d (%2$d)', 'job-salary' ), $i, $job_salary_pay_scales[ $i ][0] );
			}
		}

		return $job_salary_pay_scale_options;
	}

	/**
	 * [get_salary_pay_scales description]
	 * @return [type] [description]
	 */
	public function get_salary_pay_scales() {
		return array(
			array(),
			array( 78000 ),
			array( 66000, 76490 ),
			array( 56500, 74400 ),
			array( 50000, 71200 ),
			array( 43000, 69850 ),
			array( 35500, 67010 ),
			array( 29000, 63410 ),
			array( 23000, 55460 ),
			array( 22000, 53060 ),
			array( 16000, 38640 ),
			array( 12500, 32240 ),
			array( 11300, 27300 ),
			array( 11000, 26590 ),
			array( 10200, 24680 ),
			array( 9700, 23490 ),
			array( 9300, 22490 ),
			array( 9000, 21800 ),
			array( 8800, 21310 ),
			array( 8500, 20570 ),
			array( 8250, 20010 ),
		);
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
