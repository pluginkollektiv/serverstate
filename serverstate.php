<?php
/*
Plugin Name: Serverstate
Description: Server Monitoring für WordPress. Dashboard-Widget mit Reaktionszeiten und Erreichbarkeitsmessungen der Website. Setzt einen Serverstate-Account voraus.
Author:      pluginkollektiv
Author URI:  http://pluginkollektiv.org
Plugin URI:  https://wordpress.org/plugins/serverstate/
License:     GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Version:     0.5.3
*/

/*
Copyright (C)  2012-2015 Sergej Müller

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


/* Quit */
defined('ABSPATH') OR exit;


/* Konstanten */
define('SERVERSTATE_FILE', __FILE__);
define('SERVERSTATE_DIR', dirname(__FILE__));
define('SERVERSTATE_BASE', plugin_basename(__FILE__));


/* Hooks */
add_action(
	'plugins_loaded',
	array(
		'Serverstate',
		'instance'
	)
);
register_activation_hook(
	__FILE__,
	array(
		'Serverstate_Install',
		'init'
	)
);
register_uninstall_hook(
	__FILE__,
	array(
		'Serverstate_Uninstall',
		'init'
	)
);


/* Autoload Init */
spl_autoload_register('serverstate_autoload');

/* Autoload Funktion */
function serverstate_autoload($class) {
	if ( in_array($class, array('Serverstate', 'Serverstate_Dashboard', 'Serverstate_Install', 'Serverstate_Uninstall')) ) {
		require_once(
			sprintf(
				'%s/inc/%s.class.php',
				SERVERSTATE_DIR,
				strtolower($class)
			)
		);
	}
}
