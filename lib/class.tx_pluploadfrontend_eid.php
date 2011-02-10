<?php
if (!defined('PATH_typo3conf')) die ('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "You have to login first."}, "id" : "id"}');

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

require_once(PATH_t3lib . 'class.t3lib_befunc.php');
require_once(PATH_t3lib . 'stddb/tables.php');
require_once(t3lib_extMgm::extPath('cms', 'ext_tables.php'));


class tx_pluploadfrontend_eID {

    function main() {
        $extKey = 'plupload_frontend';

        $this->debug = t3lib_extMgm::isLoaded('devlog');
        
        // Initialize FE user object
        $feUserObj = tslib_eidtools::initFeUser();

        //Connect to database
        tslib_eidtools::connectDB();

        if($this->debug) {
            t3lib_div::devLog(t3lib_div::testInt($feUserObj->user['uid']) ? 'Eingeloggt als ' . intval($feUserObj->user['uid']) : 'Ausgeloggt', $extKey, 0);
        }

        // Check if user is logged in
        if (t3lib_div::testInt($feUserObj->user['uid']) === true) {
            header('Content-type: text/plain; charset=UTF-8');
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");

            // Settings
            $sysconf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extKey]);
            if(substr($sysconf['feuseruploadpath'],-1,1) == DIRECTORY_SEPARATOR) {
                $sysconf['feuseruploadpath'] = substr($sysconf['feuseruploadpath'],0,-1);
            }
            $targetDir = t3lib_div::convUmlauts($sysconf['feuseruploadpath']);

            $uid = intval($feUserObj->user['uid']);

            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_pluploadfrontend_email_leader, tx_pluploadfrontend_upload_folder', 'fe_groups LEFT JOIN fe_users ON fe_users.usergroup = fe_groups.uid', 'fe_users.uid = ' . $uid . ' AND fe_users.deleted = 0 AND fe_users.disable = 0 AND fe_groups.deleted = 0 AND fe_groups.hidden = 0');
            if ($res !== false) {
                $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
                if($row !== false) {
                    $targetEmail = $row['tx_pluploadfrontend_email_leader'];
                    $targetDir .= DIRECTORY_SEPARATOR . $row['tx_pluploadfrontend_upload_folder'];
                    if($this->debug) {
                        t3lib_div::devLog('targetEmail ' . (t3lib_div::validEmail($targetEmail) ? '(gueltige) ' : '(ungueltig) '). ' Emailadresse: ' . $targetEmail, $extKey, 0);
                        t3lib_div::devLog('targetDir: ' . $targetDir, $extKey, 0);
                    }
                }
                $GLOBALS['TYPO3_DB']->sql_free_result($res);
            }


            //$targetDir = ini_get('upload_tmp_dir') . DIRECTORY_SEPARATOR . 'plupload';
            $cleanupTargetDir = false; // Remove old files
            $maxFileAge = 60 * 60; // Temp file age in seconds

            // 10 minutes execution time
            @set_time_limit(10 * 60);

            // Uncomment this one to fake upload time
            // usleep(5000);

            // Get parameters
            $chunk = t3lib_div::_GP('chunk');
            $chunks = t3lib_div::_GP('chunks');
            $fileName = t3lib_div::_GP('name');
            $queued = t3lib_div::_GP('queued');

            // Clean the fileName for security reasons
            $fileName = preg_replace('/[^\w\._]+/', '', t3lib_div::convUmlauts($fileName));


            if($this->debug) {
                t3lib_div::devLog('fileName: ' . $fileName, $extKey, 0);
                t3lib_div::devLog('chunk: ' . $chunk, $extKey, 0);
                t3lib_div::devLog('chunks: ' . $chunks, $extKey, 0);
            }

             // Make sure the fileName is unique but only if chunking is disabled
            if ($chunks < 2 && file_exists(PATH_site . $targetDir . DIRECTORY_SEPARATOR . $fileName)) {
                $ext = strrpos($fileName, '.');
                $fileName_a = substr($fileName, 0, $ext);
                $fileName_b = substr($fileName, $ext);
                $count = 1;
                while (file_exists(PATH_site . $targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                    $count++;

                $fileName = $fileName_a . '_' . $count . $fileName_b;
            }

            // Create target dir
            if (!file_exists($targetDir))
                t3lib_div::mkdir(PATH_site . $targetDir);

            // Remove old temp files
            if (is_dir(PATH_site . $targetDir) && ($dir = opendir(PATH_site . $targetDir))) {
                while (($file = readdir($dir)) !== false) {
                    $filePath = PATH_site . $targetDir . DIRECTORY_SEPARATOR . $file;

                    // Remove temp files if they are older than the max age
                    if (preg_match('/\\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
                        @unlink($filePath);
                }

                closedir($dir);
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');

            // Look for the content type header
            if (isset($_SERVER['HTTP_CONTENT_TYPE']))
                $contentType = $_SERVER['HTTP_CONTENT_TYPE'];

            if (isset($_SERVER['CONTENT_TYPE']))
                $contentType = $_SERVER['CONTENT_TYPE'];

            // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
            if (strpos($contentType, 'multipart') !== false) {
                if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                    // Open temp file
                    $out = fopen(PATH_site . $targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? 'wb' : 'ab');
                    if ($out) {
                        // Read binary input stream and append it to temp file
                        $in = fopen($_FILES['file']['tmp_name'], 'rb');

                        if ($in) {
                            while ($buff = fread($in, 4096))
                                fwrite($out, $buff);
                        } else
                            die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                        fclose($in);
                        fclose($out);
                        @unlink($_FILES['file']['tmp_name']);
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            } else {
                // Open temp file
                $out = fopen(PATH_site . $targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? 'wb' : 'ab');
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = fopen('php://input', 'rb');

                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

                    fclose($in);
                    fclose($out);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }

            if($chunk == $chunks - 1){

                if($this->debug) {
                    t3lib_div::devLog('POST/GET Vars: ' . $queued, $extKey, 0, $_REQUEST);
                }

                $insertArray = array(
                    'pid' 		=> 	0,
                    'tstamp' 	=> 	date('U'),
                    'crdate'	=>	date('U'),
                    'name'		=>	$fileName,
                    'path'		=>	t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $targetDir . DIRECTORY_SEPARATOR,
                    'ip'		=>	$_SERVER['REMOTE_ADDR'],
                    'sessid'	=>	$chunk,
                    'sPath'		=>	'',
                );

                $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_pluploadfrontend_uploads', $insertArray);

            }

            if(t3lib_div::_GP('send')) {
                $email = array();

                $email['body'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $targetDir . DIRECTORY_SEPARATOR . $fileName;

                $email['address'] =  $targetEmail;

                $email['subject'] = 'Neuer Upload auf Ihrem Server';

                $html_start = '<html><head><title>Neuer Upload auf Ihrem Server</title></head><body>';
                $html_end = '</body></html>';

                $this->htmlMail = t3lib_div::makeInstance('t3lib_htmlmail');
                $this->htmlMail->start();
                $this->htmlMail->recipient = $email['address'];
                $this->htmlMail->subject = $email['subject'];
                $this->htmlMail->from_email = 'ag@t3wa.de';
                $this->htmlMail->from_name = 'Webseite';
                $this->htmlMail->returnPath = 'ag@t3wa.de';
                $this->htmlMail->addPlain($email['body']);
                $this->htmlMail->setHTML($this->htmlMail->encodeMsg($html_start . $email['body'] . $html_end));
                $this->htmlMail->send($email['address']);
                die('{"jsonrpc" : "2.0", "result" : true, "id" : "id"}');
            }

            // Return JSON-RPC response
            die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

        } else {
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "You have to login first."}, "id" : "id"}');
        }
    }
}

$SOBE = t3lib_div::makeInstance('tx_pluploadfrontend_eID');
$SOBE->main();
?>