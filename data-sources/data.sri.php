<?php
/*
Copyright: Deux Huit Huit 2016
LICENCE: MIT http://deuxhuithuit.mit-license.org;
*/

if(!defined("__IN_SYMPHONY__")) die("<h2>Error</h2><p>You cannot directly access this file</p>");

require_once TOOLKIT . '/class.datasource.php';
require_once TOOLKIT . '/class.entrymanager.php';
require_once FACE . '/interface.datasource.php';

class datasourceSri extends DataSource
{
	/**
	 * About this data source
	 */
	public function about() {
		return array(
			'name' => __('SRI'),
			'author' => array(
				'name' => 'Deux Huit Huit',
				'website' => 'https://deuxhuithuit.com/'
			),
			'version' => '1.0.0',
			'release-date' => '2016-04-20'
		);
	}

	/**
	 * Disallow data source parsing
	 */
	public function allowEditorToParse() {
		return false;
	}
	
	/**
	 * This function generates a list of month and weekday names for each language provided.
	 */
	public function execute(array &$param_pool=NULL) {
		$result = new XMLElement('sri');
		
		return $result;
	}
}
