<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_pluploadfrontend_uploads'] = array (
	'ctrl' => $TCA['tx_pluploadfrontend_uploads']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'name,path,ip,sessid'
	),
	'feInterface' => $TCA['tx_pluploadfrontend_uploads']['feInterface'],
	'columns' => array (
		'name' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:plupload_frontend/locallang_db.xml:tx_pluploadfrontend_uploads.name',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'max' => '250',
				'eval' => 'required,trim',
			)
		),
		'path' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:plupload_frontend/locallang_db.xml:tx_pluploadfrontend_uploads.path',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'max' => '250',
				'eval' => 'required,trim',
			)
		),
		'ip' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:plupload_frontend/locallang_db.xml:tx_pluploadfrontend_uploads.ip',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'max' => '250',
				'eval' => 'required,trim',
			)
		),
		'sessid' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:plupload_frontend/locallang_db.xml:tx_pluploadfrontend_uploads.sessid',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'max' => '250',
				'eval' => 'required,trim',
			)
		),
		'sPath' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:plupload_frontend/locallang_db.xml:tx_pluploadfrontend_uploads.sPath',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'max' => '250',
				'eval' => 'required,trim',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'name;;;;1-1-1, path, ip, sessid')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>