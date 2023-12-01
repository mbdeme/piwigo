<?php
// +-----------------------------------------------------------------------+
// | Manage Properties Photos plugin for Piwigo by TEMMII                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2007-2023 ddtddt               http://temmii.com/piwigo/ |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

/* definie Typ
0 : default piwigo properties
1 : properties texte
2 : properties date
3 : properties exif
4 : properties select
5 : properties radio
6 : properties IPTC

$conf['mpp_idexif'] ** picture id for list exif
$conf['mpp_max_fields'] ** max fileds for select and radio default 5
*/


class manage_properties_photos_maintain extends PluginMaintain
{
  private $installed = false;

  function __construct($plugin_id){
    parent::__construct($plugin_id);
  }

  function install($plugin_version, &$errors=array()){
       global $prefixeTable, $conf;

if (!defined('ADD_PROP_PHOTO_TABLE')) define('ADD_PROP_PHOTO_TABLE', $prefixeTable.'add_properties_photos');
	$query = "CREATE TABLE IF NOT EXISTS ". ADD_PROP_PHOTO_TABLE ." (
id_prop_pho SMALLINT(5) UNSIGNED NOT NULL auto_increment,
wording VARCHAR(255) NOT NULL ,
orderprop SMALLINT(5) UNSIGNED NOT NULL ,
active SMALLINT(5) UNSIGNED NOT NULL ,
edit SMALLINT(5) UNSIGNED NOT NULL ,
Typ SMALLINT(5) DEFAULT 1,
dataprop LONGTEXT,
PRIMARY KEY (id_prop_pho))DEFAULT CHARSET=utf8;";
	$result = pwg_query($query);

if (!defined('ADD_PROP_PHOTO_DATA_TABLE')) define('ADD_PROP_PHOTO_DATA_TABLE', $prefixeTable.'add_properties_photos_data');
      	$query = "CREATE TABLE IF NOT EXISTS ". ADD_PROP_PHOTO_DATA_TABLE ." (
id_img SMALLINT(5) UNSIGNED NOT NULL ,
id_prop_pho SMALLINT(5) UNSIGNED NOT NULL ,
data VARCHAR(255) NOT NULL ,
PRIMARY KEY (id_img,id_prop_pho))DEFAULT CHARSET=utf8;";
	$result = pwg_query($query);
	
if (!defined('ADD_PROP_PHOTO_DATADATE_TABLE')) define('ADD_PROP_PHOTO_DATADATE_TABLE', $prefixeTable.'add_properties_photos_datadate');
	$query = "CREATE TABLE IF NOT EXISTS ". ADD_PROP_PHOTO_DATADATE_TABLE ." (
		id_img SMALLINT(5) UNSIGNED NOT NULL ,
		id_prop_pho SMALLINT(5) UNSIGNED NOT NULL ,
		datadate datetime ,
		PRIMARY KEY (id_img,id_prop_pho))DEFAULT CHARSET=utf8;";
	$result = pwg_query($query);

	
  
  $activ=unserialize($conf['picture_informations']);
  if($activ['author']==true){$activauteur=0;}else{$activauteur=1;}
  if($activ['created_on']==true){$activco=0;}else{$activco=1;}
  if($activ['posted_on']==true){$activpo=0;}else{$activpo=1;}
  if($activ['dimensions']==true){$activdim=0;}else{$activdim=1;}
  if($activ['file']==true){$activfile=0;}else{$activfile=1;}
  if($activ['filesize']==true){$activfilesize=0;}else{$activfilesize=1;}
  if($activ['tags']==true){$activtags=0;}else{$activtags=1;}
  if($activ['categories']==true){$activcategories=0;}else{$activcategories=1;}
  if($activ['visits']==true){$activvisits=0;}else{$activvisits=1;}
  if($activ['rating_score']==true){$activrs=0;}else{$activrs=1;}
  if($activ['privacy_level']==true){$activpl=0;}else{$activpl=1;}
  $q = 'INSERT INTO ' . $prefixeTable . 'add_properties_photos(id_prop_pho,wording,orderprop,active,edit,Typ)VALUES 
	(1,"author",1,'.$activauteur.',0,0),
	(2,"Created on",2,'.$activco.',0,0),
	(3,"Posted on",3,'.$activpo.',0,0),
	(4,"Dimensions",4,'.$activdim.',0,0),
	(5,"File",5,'.$activfile.',0,0),
	(6,"Filesize",6,'.$activfilesize.',0,0),
	(7,"Tags",7,'.$activtags.',0,0),
	(8,"Albums",8,'.$activcategories.',0,0),
	(9,"Visits",9,'.$activvisits.',0,0),
	(10,"Average",10,'.$activrs.',0,0),
	(11,"Who can see this photo?",11,'.$activpl.',0,0)
	;';
  pwg_query($q); 
 
if (!defined('ADD_PROP_PHOTO_DATADATE_TABLE')) define('ADD_PROP_PHOTO_DATADATE_TABLE', $prefixeTable.'add_properties_photos_datadate');
      	$query = "CREATE TABLE IF NOT EXISTS ". ADD_PROP_PHOTO_DATADATE_TABLE ." (
id_img SMALLINT(5) UNSIGNED NOT NULL ,
id_prop_pho SMALLINT(5) UNSIGNED NOT NULL ,
datadate datetime ,
PRIMARY KEY (id_img,id_prop_pho))DEFAULT CHARSET=utf8;";
	$result = pwg_query($query);
 
  }

  function activate($plugin_version, &$errors=array()){
 	global $prefixeTable, $pwg_loaded_plugins;
	if (!defined('ADD_PROP_PHOTO_TABLE')) define('ADD_PROP_PHOTO_TABLE', $prefixeTable.'add_properties_photos'); 
	/*Update to manage Typ select date*/
	$col = pwg_db_fetch_assoc(pwg_query("SHOW COLUMNS FROM " . ADD_PROP_PHOTO_TABLE . " LIKE 'Typ';"));
     if ($col == NULL){
		pwg_query('ALTER TABLE '. ADD_PROP_PHOTO_TABLE.' ADD COLUMN `Typ` SMALLINT(5) DEFAULT 1;');
		pwg_query('ALTER TABLE '. ADD_PROP_PHOTO_TABLE.' ADD COLUMN `dataprop` LONGTEXT;');
		pwg_query('UPDATE '. ADD_PROP_PHOTO_TABLE.' set Typ = 0 where edit =0;');
	 }
	
	/*Update for manage date*/
	if (!defined('ADD_PROP_PHOTO_DATADATE_TABLE')) define('ADD_PROP_PHOTO_DATADATE_TABLE', $prefixeTable.'add_properties_photos_datadate');
	$datatable= pwg_db_fetch_assoc(pwg_query("SHOW TABLES LIKE '" . ADD_PROP_PHOTO_DATADATE_TABLE . "';"));
	 if ($datatable == NULL){
		$query = "CREATE TABLE IF NOT EXISTS ". ADD_PROP_PHOTO_DATADATE_TABLE ." (
		id_img SMALLINT(5) UNSIGNED NOT NULL ,
		id_prop_pho SMALLINT(5) UNSIGNED NOT NULL ,
		datadate datetime ,
		PRIMARY KEY (id_img,id_prop_pho))DEFAULT CHARSET=utf8;";
		$result = pwg_query($query);
	 } 
  }

  function update($old_version, $new_version, &$errors=array()){
	global $prefixeTable, $pwg_loaded_plugins;
	if (!defined('ADD_PROP_PHOTO_TABLE')) define('ADD_PROP_PHOTO_TABLE', $prefixeTable.'add_properties_photos'); 
	/*Update to manage Typ select date*/
	$col = pwg_db_fetch_assoc(pwg_query("SHOW COLUMNS FROM " . ADD_PROP_PHOTO_TABLE . " LIKE 'Typ';"));
     if ($col == NULL){
		pwg_query('ALTER TABLE '. ADD_PROP_PHOTO_TABLE.' ADD COLUMN `Typ` SMALLINT(5) DEFAULT 1;');
		pwg_query('ALTER TABLE '. ADD_PROP_PHOTO_TABLE.' ADD COLUMN `dataprop` LONGTEXT;');
		pwg_query('UPDATE '. ADD_PROP_PHOTO_TABLE.' set Typ = 0 where edit =0;');
	 }
	
	/*Update for manage date*/
	if (!defined('ADD_PROP_PHOTO_DATADATE_TABLE')) define('ADD_PROP_PHOTO_DATADATE_TABLE', $prefixeTable.'add_properties_photos_datadate');
	$datatable= pwg_db_fetch_assoc(pwg_query("SHOW TABLES LIKE '" . ADD_PROP_PHOTO_DATADATE_TABLE . "';"));
	 if ($datatable == NULL){
		$query = "CREATE TABLE IF NOT EXISTS ". ADD_PROP_PHOTO_DATADATE_TABLE ." (
		id_img SMALLINT(5) UNSIGNED NOT NULL ,
		id_prop_pho SMALLINT(5) UNSIGNED NOT NULL ,
		datadate datetime ,
		PRIMARY KEY (id_img,id_prop_pho))DEFAULT CHARSET=utf8;";
		$result = pwg_query($query);
	 }
  }
  
  function deactivate(){
  }

  function uninstall(){
	  global $prefixeTable;
    pwg_query('DROP TABLE ' . $prefixeTable . 'add_properties_photos;');
    pwg_query('DROP TABLE ' . $prefixeTable . 'add_properties_photos_data;');
	pwg_query('DROP TABLE ' . $prefixeTable . 'add_properties_photos_datadate;');
  }
}
?>
