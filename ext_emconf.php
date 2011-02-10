<?php

########################################################################
# Extension Manager/Repository config file for ext "plupload_frontend".
#
# Auto generated 09-02-2011 19:30
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Plupload Frontend',
	'description' => 'Uses Plupload (http://www.plupload.com/) to enable frontend user to upload mutiple files to a specific directory.
After upload an automatical generated email with links to the uploaded files will be send to a specified email address.',
	'category' => 'plugin',
	'author' => 'Alexander Grein',
	'author_email' => 'ag@mediaessenz.eu',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
    'createDirs' => 'uploads/tx_pluploadfrontend',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
			't3jquery' => '1.9.0-0.0.0',
		),
	),
	'_md5_values_when_last_written' => 'a:50:{s:9:"ChangeLog";s:4:"fa51";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"54c9";s:14:"ext_tables.php";s:4:"a650";s:14:"ext_tables.sql";s:4:"8f1d";s:28:"ext_typoscript_constants.txt";s:4:"93aa";s:24:"ext_typoscript_setup.txt";s:4:"fa9f";s:13:"locallang.xml";s:4:"a7c6";s:16:"locallang_db.xml";s:4:"975e";s:12:"t3jquery.txt";s:4:"a6b5";s:7:"tca.php";s:4:"3f89";s:19:"doc/wizard_form.dat";s:4:"e286";s:20:"doc/wizard_form.html";s:4:"f17b";s:42:"lib/user_pluploadFrontendCheckT3jquery.php";s:4:"1dd3";s:49:"lib/user_pluploadFrontendCheckT3jqueryCDNMode.php";s:4:"e6ce";s:42:"lib/user_pluploadFrontendOnCurrentPage.php";s:4:"1ba6";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:37:"pi1/class.tx_pluploadfrontend_pi1.php";s:4:"0b12";s:45:"pi1/class.tx_pluploadfrontend_pi1_wizicon.php";s:4:"a624";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"5985";s:30:"res/css/jquery.ui.plupload.css";s:4:"61da";s:26:"res/css/plupload.queue.css";s:4:"3347";s:23:"res/img/backgrounds.gif";s:4:"cffe";s:28:"res/img/buttons-disabled.png";s:4:"8c98";s:19:"res/img/buttons.png";s:4:"a346";s:18:"res/img/delete.gif";s:4:"c717";s:16:"res/img/done.gif";s:4:"75ef";s:17:"res/img/error.gif";s:4:"0451";s:23:"res/img/plupload-bw.png";s:4:"d957";s:20:"res/img/plupload.png";s:4:"1134";s:20:"res/img/throbber.gif";s:4:"c366";s:20:"res/img/transp50.png";s:4:"6579";s:20:"res/js/gears_init.js";s:4:"428c";s:35:"res/js/jquery.plupload.queue.min.js";s:4:"c9ab";s:32:"res/js/jquery.ui.plupload.min.js";s:4:"5dd4";s:34:"res/js/plupload.browserplus.min.js";s:4:"e8b9";s:28:"res/js/plupload.flash.min.js";s:4:"1dde";s:25:"res/js/plupload.flash.swf";s:4:"3059";s:27:"res/js/plupload.full.min.js";s:4:"4e69";s:28:"res/js/plupload.gears.min.js";s:4:"f9e8";s:28:"res/js/plupload.html4.min.js";s:4:"99d5";s:28:"res/js/plupload.html5.min.js";s:4:"749b";s:22:"res/js/plupload.min.js";s:4:"2cea";s:34:"res/js/plupload.silverlight.min.js";s:4:"f7c3";s:31:"res/js/plupload.silverlight.xap";s:4:"92f3";s:23:"res/templates/mail.html";s:4:"5562";s:38:"static/plupload_frontend/constants.txt";s:4:"d41d";s:34:"static/plupload_frontend/setup.txt";s:4:"de88";}',
	'suggests' => array(
	),
);

?>