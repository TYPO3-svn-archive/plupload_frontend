<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
include_once(t3lib_extMgm::extPath($_EXTKEY) . 'lib/user_pluploadFrontendOnCurrentPage.php'); // Conditions for JS including
include_once(t3lib_extMgm::extPath($_EXTKEY) . 'lib/user_pluploadFrontendCheckT3jquery.php'); // Conditions for Check if t3jquery is loaded or not
include_once(t3lib_extMgm::extPath($_EXTKEY) . 'lib/user_pluploadFrontendCheckT3jqueryCDNMode.php'); // Conditions for Check if t3jquery is in CDN Mode

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_pluploadfrontend_pi1.php', '_pi1', 'list_type', 0);
?>