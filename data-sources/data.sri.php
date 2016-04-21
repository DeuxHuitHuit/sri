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
	public $dsFilesPath;

	public function __construct() {
		$this->dsFilesPath = MANIFEST . '/sri.xml';
	}

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
		try {
			$files = $this->getFiles();
			foreach ($files as $file) {
				$xmlfile = new XMLElement('file');
				$filepath = DOCROOT . '/' . $file['file'];
				$filecontent = @file_get_contents($filepath);
				if (!$filecontent) {
					$xmlfile->appendChild(new XMLElement('error', 'Could not read `' . $filepath . '`'));
				}
				else {
					$integrity = $this->computeIntegrity($file['hash'], $filecontent);
					$xmlfile->setAttribute('filename', basename($file['file']));
					$xmlfile->setAttribute('hash', $file['hash']);
					$xmlfile->setAttribute('integrity', $integrity);
					$xmlfile->setValue($file['file']);
				}
				$result->appendChild($xmlfile);
			}
		}
		catch (Exception $ex) {
			Symphony::Logs()->pushExceptionToLog($ex, true);
			$result->appendChild(new XMLElement('error', $ex->getMessage()));
		}
		return $result;
	}

	private function getFiles() {
		if (!@file_exists($this->dsFilesPath)) {
			return array();
		}
		$files = array();
		$xml = @simplexml_load_file($this->dsFilesPath);
		if (!$xml) {
			throw new Exception('Could not load xml file');
		}
		$defaultHash = (string)current($xml->xpath('/*/@hash'));
		if (empty($defaultHash)) {
			$defaultHash = 'sha384';
		}
		$xmlfiles = $xml->xpath('/*/file');
		foreach ($xmlfiles as $file) {
			$fileHash = (string)current($file->xpath('@hash'));
			if (empty($fileHash)) {
				$fileHash = $defaultHash;
			}
			$files[] = array(
				'file' => (string)$file,
				'hash' => $fileHash,
			);
		}
		return $files;
	}

	private function computeIntegrity($hash, $value) {
		return $hash . '-' . base64_encode(hash($hash, $value, true));
	}
}
