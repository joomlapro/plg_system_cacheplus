<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Cacheplus
 *
 * @author      Bruno Batista <bruno@atomtech.com.br>
 * @copyright   Copyright (C) 2013 AtomTech IT Services. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Joomla Cacheplus plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  System.Cacheplus
 * @author      Bruno Batista <bruno@atomtech.com.br>
 * @since       3.2
 */
class PlgSystemCacheplus extends JPlugin
{
	/**
	 * After the framework has rendered the application.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function onAfterRender()
	{
		// Get the application.
		$app = JFactory::getApplication('site');

		// Check that we are in the site application.
		if ($app->isAdmin())
		{
			return true;
		}

		// Register dependent classes.
		JLoader::register('Minify_HTML', __DIR__ . '/libraries/Minify/HTML.php');
		JLoader::register('Minify_CSS', __DIR__ . '/libraries/Minify/CSS.php');
		JLoader::register('JSMin', __DIR__ . '/libraries/JSMin.php');

		// Set the options.
		$options = array();
		$options['cssMinifier'] = array('Minify_CSS', 'minify');
		$options['jsMinifier']  = array('JSMin', 'minify');

		// Process the content.
		$content = JResponse::getBody();
		$start   = strlen($content);
		$content = Minify_HTML::minify($content, $options);
		$end     = strlen($content);
		$savings = round((($start - $end) / $start * 100), 3);

		// Set the body content.
		JResponse::setBody($content);

		// Show the process compression.
		JResponse::appendBody("<!-- Uncompressed size: $start bytes; Compressed size: $end bytes; $savings% savings. -->");

		return true;
	}
}
