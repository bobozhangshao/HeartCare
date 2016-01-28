<?php
defined('_JEXEC') or die('Restricted acccess');

/**
 * Script file of HeartCare component
 */
class Com_HeartcareInstallerScript
{
	/**
	 * method to install the component
	 * create devices folder
	 * @return void
	 */
	function install($parent)
	{
		$path = JPATH_SITE . '/images/healthcare';
		if (!JFolder::exists($path)) {
			JFolder::create($path);
		}

		$path1 = JPATH_SITE . '/images/healthcare/devices';
		if (!JFolder::exists($path1)) {
			JFolder::create($path1);
		}

		$path2 = JPATH_SITE . '/images/healthcare/news';
		if (!JFolder::exists($path2)) {
			JFolder::create($path2);
		}

	}
}