<?php


/* Quit */
defined('ABSPATH') OR exit;


/**
* Serverstate_Dashboard
*
* @since 0.1
*/

class Serverstate_Dashboard
{


	/**
	* Installation auch für MU-Blog
	*
	* @since   0.1
	* @change  0.5
	*/

	public static function init()
	{
		/* Capability check */
		if ( ! current_user_can('edit_dashboard') ) {
			return;
		}

		/* Version definieren */
		self::_define_version();

		/* Widget */
		wp_add_dashboard_widget(
			'serverstate_dashboard',
			'Serverstate',
			array(
				__CLASS__,
				'print_frontview'
			),
			array(
				__CLASS__,
				'print_backview'
			)
		);

		/* CSS laden */
		add_action(
			'admin_print_styles',
			array(
				__CLASS__,
				'add_style'
			)
		);

		/* JS laden */
		add_action(
			'admin_print_scripts',
			array(
				__CLASS__,
				'add_js'
			)
		);
	}


	/**
	* Ausgabe der Stylesheets
	*
	* @since   0.1
	* @change  0.1
	*/

	public static function add_style()
	{
		/* CSS registrieren */
		wp_register_style(
			'serverstate',
			plugins_url('css/dashboard.min.css', SERVERSTATE_FILE),
	  		array(),
	  		SERVERSTATE_VERSION
		);

	  	/* CSS ausgeben */
	  	wp_enqueue_style('serverstate');
	}


	/**
	* Ausgabe von JavaScript
	*
	* @since   0.1
	* @change  0.3
	*/

	public static function add_js() {
		/* Registrieren */
		wp_register_script(
			'serverstate',
			plugins_url('js/dashboard.min.js', SERVERSTATE_FILE),
			array(),
			SERVERSTATE_VERSION
		);
		wp_register_script(
			'google_jsapi',
			'https://www.google.com/jsapi',
			false
		);

		/* Einbinden */
		wp_enqueue_script('google_jsapi');
		wp_enqueue_script('serverstate');

		/* Übergeben */
		if ( $data = self::get_stats('cache') ) {
			wp_localize_script(
				'serverstate',
				'serverstate',
				$data
			);
		}
	}


	/**
	* Ausgabe der Frontseite
	*
	* @since   0.1
	* @change  0.1
	*/

	public static function print_frontview()
	{ ?>
		<div id="serverstate_chart">
			<noscript>Zur Darstellung der Statistik wird JavaScript benötigt.</noscript>
		</div>
	<?php }


	/**
	* Ausgabe der Backseite
	*
	* @since   0.1
	* @change  0.1
	*/

	public static function print_backview()
	{
		/* Capability check */
		if ( ! current_user_can('manage_options') ) {
			return;
		}

		/* Handle options */
		if ( ! empty($_POST['serverstate']) && is_array($_POST['serverstate']) ) {
			/* Referer check */
			check_admin_referer('_serverstate');

			/* Init data */
			$input = $_POST['serverstate'];
			$output = array();

			/* Nickname */
			if ( ! empty($input['nickname']) ) {
				$output['nickname'] = sanitize_text_field($input['nickname']);
			}

			/* Password */
			if ( ! empty($input['password']) ) {
				$output['password'] = md5(sanitize_text_field($input['password']));
			}

			/* Sensor ID */
			if ( ! empty($input['sensor_id']) ) {
				$output['sensor_id'] = (int)$input['sensor_id'];
			}

			/* Update options */
			update_option(
				'serverstate',
				$output
			);

			/* Delete cache */
			delete_transient('serverstate');
		}

		/* Get options */
		$options = wp_parse_args(
			get_option('serverstate'),
			array(
				'nickname'  => '',
				'password'  => '',
				'sensor_id' => ''
			)
		);

		/* Set nonce field */
		wp_nonce_field('_serverstate'); ?>

		<table class="form-table">
			<tr>
				<td>
					<label>Benutzername:</label>
					<input type="text" name="serverstate[nickname]" autocomplete="off" value="<?php esc_attr_e($options['nickname']) ?>" />
				</td>
				<td>
					Noch kein Serverstate-Account?
				</td>
			</tr>
			<tr>
				<td>
					<label>Passwort:</label>
					<input type="password" name="serverstate[password]" autocomplete="off" value="" />
				</td>
				<td>
					<a href="https://serverstate.de/" target="_blank" class="button-secondary">Bei Serverstate anmelden →</a>
				</td>
			</tr>
			<tr>
				<td>
					<label>Sensor ID:</label>
					<input type="text" name="serverstate[sensor_id]" autocomplete="off" value="<?php esc_attr_e($options['sensor_id']) ?>" />
				</td>
				<td>
					<em>Partnerlink. Danke.</em>
				</td>
			</tr>
		</table>

		<?php
	}


	/**
	* Rückgabe der Statistik-Werte
	*
	* @since   0.1
	* @change  0.3
	*
	* @param   string  $from  Quelle der Daten [optional]
	* @return  array   $data  Array mit Statistik- oder Fehlerwerten
	*/

	public static function get_stats($from = 'all')
	{
		/* Capability check */
		if ( ! current_user_can('edit_dashboard') ) {
			return;
		}

		/* Read from cache */
		$data = get_transient('serverstate');

		/* Return from cache? */
		if ( $from === 'cache' ) {
			return $data;
		}

		/* If empty cache */
		if ( empty($data) ) {
			/* API Call */
			$response = self::_api_call();

			/* Array? */
			if ( is_array($response) ) {
				$data = self::_prepare_stats($response);
			} else {
				$data['error'] = $response;
			}

			/* Move into cache */
			set_transient(
			   'serverstate',
			   $data,
			   60 * 60 * 12 // = 12 hours
			 );
		}

		return $data;
	}


	/**
	* Call an die Serverstate-API
	*
	* @since   0.1
	* @change  0.5.2
	*
	* @return  mixed  $data  Array mit API-Werten oder Fehlermeldungen
	*/

	private static function _api_call()
	{
		/* Optionen */
		$options = get_option('serverstate');

		/* Init */
		$data = array(
			'day'      => array(),
			'uptime'   => array(),
			'response' => array()
		);

		/* Leer? */
		if ( empty($options['nickname']) OR empty($options['password']) OR empty($options['sensor_id']) ) {
			return sprintf(
				'Bitte Zugangsdaten im Dashboard-Widget <a href="%s">vervollständigen</a>.',
				add_query_arg(
					array(
						'edit' => 'serverstate_dashboard#serverstate_dashboard'
					),
					admin_url('/')
				)
			);
		}

		/* Tage loopen */
		for ($i = 0; $i < 30; $i ++) {
			/* URL erfragen */
			$response = wp_remote_get(
				add_query_arg(
					array(
						'nickname' => urlencode($options['nickname']),
						'password' => $options['password'],
						'sensor_id' => $options['sensor_id'],
						'day' => date('d.m.Y', strtotime('-' .$i. ' day'))
					),
					'https://serverstate.de/api/1/daily_report/'
				),
				array(
					'timeout'   => 30,
                    'sslverify' => false
				)
			);

			/* Fehler? */
			if ( is_wp_error($response) ) {
				return $response->get_error_message();
			}

			/* Body */
			$body = wp_remote_retrieve_body($response);

			/* Falsche Sensor-ID? */
			if ( $body == 'ERROR_INVALID_REQUEST' ) {
				return sprintf(
					'Bitte die Sensor-ID im Dashboard-Widget <a href="%s">überprüfen</a>.',
					add_query_arg(
						array(
							'edit' => 'serverstate_dashboard#serverstate_dashboard'
						),
						admin_url('/')
					)
				);
			}

			/* Falsche Daten? */
			if ( $body == 'ERROR_INVALID_AUTH' ) {
				return sprintf(
					'Bitte Zugangsdaten im Dashboard-Widget <a href="%s">überprüfen</a>.',
					add_query_arg(
						array(
							'edit' => 'serverstate_dashboard#serverstate_dashboard'
						),
						admin_url('/')
					)
				);
			}

			/* Dekodieren */
			$xml = simplexml_load_string($body);

			/* Fehler? */
			if ( $xml === false ) {
				return 'Houston, wir haben ein Problem: Kein XML als Rückgabe?';
			}

			/* Zuweisen */
			$day = (string) $xml->day;
			$uptime = (int) $xml->uptime_percent;
			$response = (int) $xml->response_time;

			/* Ungültig? */
			if ( $uptime === -1 or $response === -1 ) {
				continue;
			}

			/* Zusammenführen */
			array_push($data['day'], $day);
			array_push($data['uptime'], $uptime);
			array_push($data['response'], $response);
		}

		/* Nichts gesammelt? */
		if ( empty($data['day']) ) {
			return 'Aktuell sind keine Daten zur Anzeige vorhanden.';
		}

		return $data;
	}


	/**
	* Vorbereitung der Werte für JS
	*
	* @since   0.1
	* @change  0.4
	*
	* @param   array  $data  Unbehandelter Array
	* @return  array  $data  Behandelter Array
	*/

	private static function _prepare_stats($data)
	{
		/* Leer? */
		if ( empty($data) ) {
			return array();
		}

		/* Einträge binden */
		return array_map(
			array(
				__CLASS__,
				'_array_map_callback'
			),
			$data
		);
	}


	/**
	* Plugin-Version als Konstante
	*
	* @since   0.1
	* @change  0.1
	*/

	private static function _define_version()
	{
		/* Auslesen */
		$meta = get_plugin_data(SERVERSTATE_FILE);

		/* Zuweisen */
		define('SERVERSTATE_VERSION', $meta['Version']);
	}


	/**
	* Callback für array_map (PHP 5.2)
	*
	* @since   0.4
	* @change  0.4
	*
	* @param   array   $array  Array mit Werten
	* @return  string          Kommaseparierter String
	*/

	private static function _array_map_callback($array)
	{
		return implode(',', $array);
	}
}
