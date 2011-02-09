<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
t3lib_extMgm::addStaticFile($_EXTKEY,'static/plupload_frontend/', 'plupload_frontend');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:plupload_frontend/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

t3lib_extMgm::allowTableOnStandardPages('tx_pluploadfrontend_uploads');

$TCA['tx_pluploadfrontend_uploads'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:plupload_frontend/locallang_db.xml:tx_pluploadfrontend_uploads',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_pluploadfrontend_uploads.gif',
	),
);


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_pluploadfrontend_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_pluploadfrontend_pi1_wizicon.php';
}
?>