#
# Table structure for table 'tx_pluploadfrontend_uploads'
#
CREATE TABLE tx_pluploadfrontend_uploads (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	name varchar(250) DEFAULT '' NOT NULL,
	path varchar(250) DEFAULT '' NOT NULL,
	ip varchar(250) DEFAULT '' NOT NULL,
	sessid varchar(250) DEFAULT '' NOT NULL,
	sPath varchar(250) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);