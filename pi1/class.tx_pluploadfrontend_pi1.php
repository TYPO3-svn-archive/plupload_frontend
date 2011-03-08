<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Alexander Grein <ag@mediaessenz.eu>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Plupload' for the 'plupload_frontend' extension.
 *
 * @author	Alexander Grein <ag@mediaessenz.eu>
 * @package	TYPO3
 * @subpackage	tx_pluploadfrontend
 */
class tx_pluploadfrontend_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_pluploadfrontend_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_pluploadfrontend_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'plupload_frontend';	// The extension key.
    var $debug = false;
    var $targetMail;
    var $targetDir;

    private function log() {
        if (count($_GET)) {
            $result['get'] = $_GET;
        }
        if (count($_POST)) {
            $result['post'] = $_POST;
        }
        if (count($_FILES)) {
            $result['files'] = $_FILES;
        }
        if($this->debug){
            t3lib_div::devLog('Form parameter:', $this->extKey, 0, $result);
        }
    }

    private function getSPath($_length) {
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                '*',         // SELECT ...
                'tx_pluploadfrontend_uploads',     // FROM ...
                'ip=\''.$_SERVER['REMOTE_ADDR'].'\'',    // WHERE...
                '',            // GROUP BY...
                '',    // ORDER BY...
                ''            // LIMIT to 10 rows, starting with number 5 (MySQL compat.)
            );

        $_upload = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
        if ($_upload && !is_null($_upload)) {
            $_out = $_upload['sPath'];
        } else {
            //Check DB
            $_out = '';
            for ($i=1;$i<=$_length;$i++) {
                $_out .= rand(0,9);
            }
        }
        return $_out;
    }

    private function getUploadData() {

        $_data = '';
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                '*',         // SELECT ...
                'tx_pluploadfrontend_uploads',     // FROM ...
                'ip=\'' . $_SERVER['REMOTE_ADDR'] . '\'',    // WHERE...
                '',            // GROUP BY...
                '',    // ORDER BY...
                ''            // LIMIT to 10 rows, starting with number 5 (MySQL compat.)
            );

        while ($_upload = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $_data .= '<li><a href="' . $_upload['path'] . $_upload['name'] . '">' . $_upload['name'] . '</a></li>';
            t3lib_div::fixPermissions(PATH_site . $this->targetDir . DIRECTORY_SEPARATOR . $_upload['name']);
            if($this->debug) {
                t3lib_div::devLog('Fixes Filepath: ' . PATH_site . $this->targetDir . DIRECTORY_SEPARATOR . $_upload['name'], $this->extKey, 0);
            }
        }
        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_pluploadfrontend_uploads', 'ip=\'' . $_SERVER['REMOTE_ADDR'] . '\'');
        return $_data;
    }

    /**
     * 
     * @return void
     */
    private function isSend() {
        $this->log();

        $s_Template = $this->cObj->fileResource('EXT:' . $this->extKey . '/res/templates/mail.html');
        $s_Tmpl = $this->cObj->getSubpart($s_Template, '###MAIL_TEXT###');

        $a_Marker = array();

        $a_Marker['###INPUT_UPLOAD_HEADLINE###'] = $this->pi_getLL('mail_upload_headline');
        $a_Marker['###INPUT_NAME###'] = $this->piVars['name'];
        $a_Marker['###INPUT_NAME_LABEL###'] = $this->pi_getLL('mail_upload_name_label');
        $a_Marker['###INPUT_COMPANY###'] = $this->piVars['company'];
        $a_Marker['###INPUT_COMPANY_LABEL###'] = $this->pi_getLL('mail_upload_company_label');
        $a_Marker['###INPUT_DESCRIPTION###'] = $this->piVars['description'];
        $a_Marker['###INPUT_DESCRIPTION_LABEL###'] = $this->pi_getLL('mail_upload_description_label');
        $a_Marker['###UPLOAD_DATA###'] = $this->getUploadData();
        $a_Marker['###UPLOAD_DATA_LABEL###'] = $this->pi_getLL('mail_upload_data_label');

        $email['body'] = $this->cObj->substituteMarkerArrayCached($s_Tmpl, $a_Marker);

        $email['address'] = $this->targetEmail;

        $email['subject'] = $this->pi_getLL('mail_new_upload_available');

        $html_start='<html><head><title>' . $this->pi_getLL('mail_new_upload_available') . '</title></head><body>';
        $html_end='</body></html>';

        $from = ($this->feUserEmail != '') ? $this->feUserEmail : $this->conf['mailto'];
        $fromName = ($this->feUserName != '') ? $this->feUserName : $this->conf['mailabsender'];
        $returnPath = ($this->feUserEmail != '') ? $this->feUserEmail : $this->conf['mailto'];

        $success = false;

        if (t3lib_div::compat_version('4.5')){
            // new TYPO3 swiftmailer code
            $this->mail = t3lib_div::makeInstance('t3lib_mail_Message');
            $this->mail->setTo(array($email['address']))
                ->setFrom(array($from => $fromName))
                ->setSubject($email['subject'])
                ->setReturnPath($returnPath)
                ->setCharset($GLOBALS['TSFE']->metaCharset)
                ->addPart($email['body'], 'text/plain')
                ->setBody($html_start . $email['body'] . $html_end, 'text/html');
            $this->mail->send();
            $success = $this->mail->isSent();
            
        } else {

            $this->mail = t3lib_div::makeInstance('t3lib_htmlmail');
            $this->mail->start();
            $this->mail->recipient = $email['address'];
            $this->mail->subject = $email['subject'];
            $this->mail->from_email = $from;
            $this->mail->from_name = $fromName;
            $this->mail->returnPath = $returnPath;
            $this->mail->addPlain($email['body']);
            $this->mail->setHTML($this->mail->encodeMsg($html_start . $email['body'] . $html_end));
            $success = $this->mail->send();
        }
        
        if($this->debug && $success) {
            t3lib_div::devLog('A Email was successfully sent to ' . $email['address'], $this->extKey, 0);
            t3lib_div::devLog('Email content was: ' . $email['body'], $this->extKey, 0);
            t3lib_div::devLog('piVars:', $this->extKey, 0, $this->piVars);
        }

        $s_Redirect = t3lib_div::locationheaderUrl(
            $this->cObj->typoLink_URL(
                array('parameter' => $this->conf['redirectTo'])
            )
        );
        header('Location: ' . $s_Redirect);
        exit;

    }

    private function init() {
        //Clean DB
        $_time = date('U') - 60*60*24*7;

        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_pluploadfrontend_uploads', 'tstamp < ' . $_time);
    }
    
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
	
        $this->debug = t3lib_extMgm::isLoaded('devlog');
         $result = array();
        //$this->init();

/*        if (isset($_FILES['Filedata'])) {
            $this->isUpload();
        }*/

        $feUserObj = $GLOBALS['TSFE']->fe_user;
        $feUserId = intval($feUserObj->user['uid']);

        if($feUserId > 0){
            // Settings
            $sysconf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
            if(substr($sysconf['feuseruploadpath'],-1,1) == DIRECTORY_SEPARATOR) {
                $sysconf['feuseruploadpath'] = substr($sysconf['feuseruploadpath'],0,-1);
            }
            $this->targetDir = t3lib_div::convUmlauts($sysconf['feuseruploadpath']);

            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_pluploadfrontend_email_leader, tx_pluploadfrontend_upload_folder, name, email, company', 'fe_groups LEFT JOIN fe_users ON fe_users.usergroup = fe_groups.uid', 'fe_users.uid = ' . $feUserId . ' AND fe_users.deleted = 0 AND fe_users.disable = 0 AND fe_groups.deleted = 0 AND fe_groups.hidden = 0');
            if ($res !== false) {
                $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
                if($row !== false) {
                    $this->targetEmail = $row['tx_pluploadfrontend_email_leader'];
                    $this->targetDir .= DIRECTORY_SEPARATOR . $row['tx_pluploadfrontend_upload_folder'];
                    $this->feUserName = $row['name'];
                    $this->feUserEmail = $row['email'];
                    $this->feUserCompany = $row['company'];
                    if($this->debug) {
                        t3lib_div::devLog('targetEmail ' . (t3lib_div::validEmail($this->targetEmail) ? '(gueltige) ' : '(ungueltig) '). ' Emailadresse: ' . $this->targetEmail, $this->extKey, 0);
                        t3lib_div::devLog('targetDir: ' . $this->targetDir, $this->extKey, 0);
                    }
                }
                $GLOBALS['TYPO3_DB']->sql_free_result($res);
            }

            if($this->debug){
                 t3lib_div::devLog('piVars', $this->extKey, 0, $this->piVars);
            }

            if ($this->piVars['send'] || $this->piVars['finish']) {
                 $this->isSend();
                //return $this->isSend();
            }

            //<textarea id="log" style="width: 100%; height: 150px; font-size: 11px" spellcheck="false" wrap="off"></textarea>
            $content = '
                <form id="tx_pluploadfrontend_pi1_form" action="' . $this->pi_getPageLink($GLOBALS['TSFE']->id) . '" method="POST">
                    <div id="tx_pluploadfrontend_pi1_uploader">
                        <p>' . $this->pi_getLL('mail_upload_no_support'). '</p>
                    </div>
                    <p><label for="tx_pluploadfrontend_pi1_name">' . $this->pi_getLL('mail_upload_name_label'). '</label><input type="text" name="tx_pluploadfrontend_pi1[name]" id="tx_pluploadfrontend_pi1_name" value="' . $this->feUserName . '" placeholder="' . $this->pi_getLL('mail_upload_placeholder_name'). '" /></p>
                    <p><label for="tx_pluploadfrontend_pi1_company">' . $this->pi_getLL('mail_upload_company_label'). '</label><input type="text" name="tx_pluploadfrontend_pi1[company]" id="tx_pluploadfrontend_pi1_company" value="' . $this->feUserCompany . '" placeholder="' . $this->pi_getLL('mail_upload_placeholder_company'). '" /></p>
                    <p><label for="tx_pluploadfrontend_pi1_description">' . $this->pi_getLL('mail_upload_description_label'). '</label><textarea id="tx_pluploadfrontend_pi1_description" name="tx_pluploadfrontend_pi1[description]" placeholder="' . $this->pi_getLL('mail_upload_placeholder_description'). '"></textarea></p>
                    <input type="hidden" name="tx_pluploadfrontend_pi1[finish]" id="tx_pluploadfrontend_pi1_finish" value="0" />
                    <input type="submit" name="tx_pluploadfrontend_pi1[send]" id="tx_pluploadfrontend_pi1_send" value="' . $this->pi_getLL('mail_upload_send'). '" />
                </form>
            ';
        } else {
            $content = '<h2>' . $this->pi_getLL('mail_upload_login_first') . '</h2>';
        }


		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/plupload_frontend/pi1/class.tx_pluploadfrontend_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/plupload_frontend/pi1/class.tx_pluploadfrontend_pi1.php']);
}

?>