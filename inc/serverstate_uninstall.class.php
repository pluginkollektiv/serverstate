<?php


/* Quit */
defined('ABSPATH') OR exit;


/**
* Serverstate_Uninstall
*
* @since 0.1
*/

class Serverstate_Uninstall
{


	/**
	* Uninstallation auch für MU-Blog
	*
	* @since   0.1
	* @change  0.1
	*
	* @param   integer  ID des Blogs
	*/

	public static function init($id)
	{
		/* Global */
		global $wpdb;

		/* Neuer MU-Blog */
		if ( !empty($id) ) {
			/* Im Netzwerk? */
			if ( !is_plugin_active_for_network(SERVERSTATE_BASE) ) {
				return;
			}

			/* Wechsel */
			switch_to_blog( (int)$id );

			/* Installieren */
			self::_apply();

			/* Wechsel zurück */
			restore_current_blog();

			/* Raus */
			return;
		}

		/* Multisite & Network */
		if ( is_multisite() && !empty($_GET['networkwide']) ) {
			/* Alter Blog */
			$old = $wpdb->blogid;

			/* Blog-IDs */
			$ids = $wpdb->get_col("SELECT blog_id FROM `$wpdb->blogs`");

			/* Loopen */
			foreach ($ids as $id) {
				switch_to_blog($id);
				self::_apply();
			}

			/* Wechsel zurück */
			switch_to_blog($old);

			/* Raus */
			return;
		}

		/* Single-Blog */
		self::_apply();
	}


	/**
	* Löschung der Daten
	*
	* @since   0.1
	* @change  0.1
	*/

	private static function _apply()
	{
		/* Option */
		delete_option('serverstate');

		/* Transient */
		delete_transient('serverstate');
	}
}