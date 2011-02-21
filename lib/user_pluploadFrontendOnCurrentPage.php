<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 uploadify development team (details on http://forge.typo3.org/projects/show/extension-uploadify)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * Function user_pluploadFrontendOnCurrentPage() checks if a plupload_frontend plugin is inserted on current page
 *
 * @return	boolean		false/true
 */
function user_pluploadFrontendOnCurrentPage() {
	$pluploadfrontend = false;
	if (TYPO3_MODE == 'FE') {
		$ttContentWhere = 'AND deleted = 0 AND hidden = 0';
		if (is_array($GLOBALS['TCA']['tt_content']) && method_exists($GLOBALS['TSFE']->sys_page, 'enableFields')) {
			$ttContentWhere = $GLOBALS['TSFE']->sys_page->enableFields('tt_content');
		}

		$pid = getCorrectPageIdForPluploadFrontendOnCurrentPageUserfunc();
		$where = 'pid = ' . intval($pid) . ' AND list_type = "plupload_frontend_pi1" ' . $ttContentWhere;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('COUNT(*) AS t', 'tt_content', $where);

		if ($res !== false) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if ($row !== false) {
				$pluploadfrontend = true;
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
	}
	return $pluploadfrontend;
}

/**
 * Returns the correct Page-ID for the "ispluploadfrontendOnCurrentPage"-Check
 * This method has a check for "content_from_pid"-field of pages table
 *
 * @return	integer		Correct PageID
 */
function getCorrectPageIdForPluploadFrontendOnCurrentPageUserfunc() {
	$pid = $GLOBALS['TSFE']->id;

	// This part is copied and modified from TSpagegen::pagegenInit();
	// because we can`t call it directly. It is to early.
	if ($GLOBALS['TSFE']->page['content_from_pid'] > 0) {
		// make REAL copy of TSFE object - not reference!
		$temp_copy_TSFE = clone($GLOBALS['TSFE']);
		// Set ->id to the content_from_pid value - we are going to evaluate this pid as was it a given id for a page-display!
		$temp_copy_TSFE->id = $GLOBALS['TSFE']->page['content_from_pid'];
		$temp_copy_TSFE->getPageAndRootlineWithDomain($GLOBALS['TSFE']->tmpl->setup['config.']['content_from_pid_allowOutsideDomain'] ? 0 : $GLOBALS['TSFE']->domainStartPage);
		$pid = intval($temp_copy_TSFE->id);
		unset($temp_copy_TSFE);
	}

	return $pid;
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/plupload_frontend/lib/user_pluploadFrontendOnCurrentPage.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/plupload_frontend/lib/user_pluploadFrontendOnCurrentPage.php']);
}
?>