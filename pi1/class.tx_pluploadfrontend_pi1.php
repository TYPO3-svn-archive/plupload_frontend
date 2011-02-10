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

    private function log() {
        $result['time'] = date('r');
        $result['addr'] = substr_replace(gethostbyaddr($_SERVER['REMOTE_ADDR']), '******', 0, 6);
        $result['agent'] = $_SERVER['HTTP_USER_AGENT'];

        if (count($_GET)) {
            $result['get'] = $_GET;
        }
        if (count($_POST)) {
            $result['post'] = $_POST;
        }
        if (count($_FILES)) {
            $result['files'] = $_FILES;
        }

        // we kill an old file to keep the size small
        if (file_exists('script.log') && filesize('script.log') > 102400) {
            unlink('script.log');
        }

        $log = @fopen('script.log', 'a');
        if ($log) {
            fputs($log, print_r($result, true) . "\n---\n");
            fclose($log);
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

    private function isUpload() {

                // HTTP headers for no cache etc

        /*
        $_uPath = $this->conf['uploadPath'];
        $_dirLength = $this->conf['filedirLength'];

        $absFilePath = t3lib_div::getFileAbsFileName($_uPath);

        $_sPath = $this->getSPath($_dirLength);

        $error = 0;

        try {
            mkdir($absFilePath . $_sPath);

            $_replace = array(
                ' '	=>	'_',
            );

            $_FILES['Filedata']['name'] = str_replace(array_keys($_replace),array_values($_replace),$_FILES['Filedata']['name']);
            $_FILES['Filedata']['name'] = preg_replace('/[^\w\._]+/', '', $_FILES['Filedata']['name']);

            move_uploaded_file($_FILES['Filedata']['tmp_name'], $absFilePath . $_sPath . '/' . $_FILES['Filedata']['name']);

            $return = array(
                'status' => '1',
                'name' => $_FILES['Filedata']['name']
            );


            $_url = t3lib_div::locationheaderUrl(
                $this->cObj->typoLink_URL(
                    array('parameter' => $_uPath.$_sPath.'/' . $_FILES['Filedata']['name'])
                )
            );
            $insertArray = array(
                'pid' 		=> 	$this->conf['storageId'],
                'tstamp' 	=> 	date('U'),
                'crdate'	=>	date('U'),
                'name'		=>	$_FILES['Filedata']['name'],
                'path'		=>	$_url,//$absFilePath . $_sPath . '/' . $_FILES['Filedata']['name'],
                'ip'		=>	$_SERVER['REMOTE_ADDR'],
                'sessid'	=>	session_id(),
                'sPath'		=>	$_sPath,
            );

            $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_pluploadfrontend_uploads', $insertArray);

        } catch (Exception $e) {
            $return = array(
                'status' => '0',
                'name' => $_FILES['Filedata']['name']
            );
            $error = 1;
        }


        if (!$error) {
            $return['hash'] = md5_file($absFilePath . $_sPath . '/' .$_FILES['Filedata']['name']);
        }
        echo json_encode($return);
        die();
    }

    private function getUploadData() {

        $_data = '';
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                '*',         // SELECT ...
                'tx_pluploadfrontend_uploads',     // FROM ...
                'ip=\''.$_SERVER['REMOTE_ADDR'].'\'',    // WHERE...
                '',            // GROUP BY...
                '',    // ORDER BY...
                ''            // LIMIT to 10 rows, starting with number 5 (MySQL compat.)
            );

        while ($_upload = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $_data .= '<div>
            Filename: '.$_upload['name'].'<br/>
            Filepath: '.$_upload['path'].'<br/>
            </div>';
        }
        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_pluploadfrontend_uploads', 'ip=\''.$_SERVER['REMOTE_ADDR'].'\'');
        return $_data;
         */
    }
    private function isSend() {
        $this->log();

        $s_Template = $this->cObj->fileResource('EXT:'.$this->extKey.'/res/template/mail.html');
        $s_Tmpl = $this->cObj->getSubpart($s_Template, '###MAIL_TEXT###');

        $a_Marker = array();

        $a_Marker['###UPLOAD_DATA###'] = $this->getUploadData();


        $a_Marker['###INPUT_NAME###'] = $_POST['name'];
        $a_Marker['###INPUT_INHALT###'] = $_POST['inhalt'];
        $email['body'] = $this->cObj->substituteMarkerArrayCached($s_Tmpl, $a_Marker);

        $email['address']= $this->conf['mailto'];

        $email['subject']='Neuer Upload auf Ihrem Server';

        $html_start='<html><head><title>Neuer Upload auf Ihrem Server</title></head><body>';
        $html_end='</body></html>';

        $this->htmlMail = t3lib_div::makeInstance('t3lib_htmlmail');
        $this->htmlMail->start();
        $this->htmlMail->recipient = $email['address'];
        $this->htmlMail->subject = $email['subject'];
        $this->htmlMail->from_email = $this->conf['mailto'];
        $this->htmlMail->from_name = $this->conf['mailabsender'];
        $this->htmlMail->returnPath = $this->conf['mailto'];
        $this->htmlMail->addPlain($email['body']);
        $this->htmlMail->setHTML($this->htmlMail->encodeMsg($html_start.$email['body'].$html_end));
        $this->htmlMail->send($email['address']);

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

        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_pluploadfrontend_uploads', 'tstamp < '.$_time);
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
	
        $result = array();
        $this->init();

        if (isset($_FILES['Filedata'])) {
            $this->isUpload();
        }
        if (isset($_POST['send']) && $_POST['send']==1) {
            return $this->isSend();
        }

		$content='
            <form action="' . $this->pi_getPageLink($GLOBALS['TSFE']->id) . '" method="POST">
                <div id="uploader">
                    <p>You browser doesn\'t have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
                </div>
                <input type="submit" value="Send" />
            </form>
		';
	
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/plupload_frontend/pi1/class.tx_pluploadfrontend_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/plupload_frontend/pi1/class.tx_pluploadfrontend_pi1.php']);
}

?>