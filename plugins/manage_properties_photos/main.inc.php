<?php
/*
Plugin Name: Manage Properties Photos
Version: 13.0.a
Description: Add properties on photo page and organize this
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=783
Author: ddtddt
Author URI: http://temmii.com/piwigo/
Has Settings: webmaster
*/

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


if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $prefixeTable;

define('ADD_PROP_PHOTO_DIR' , basename(dirname(__FILE__)));
define('ADD_PROP_PHOTO_PATH' , PHPWG_PLUGINS_PATH . ADD_PROP_PHOTO_DIR . '/');
if (!defined('ADD_PROP_PHOTO_TABLE')) define('ADD_PROP_PHOTO_TABLE', $prefixeTable.'add_properties_photos');
if (!defined('ADD_PROP_PHOTO_DATA_TABLE')) define('ADD_PROP_PHOTO_DATA_TABLE', $prefixeTable.'add_properties_photos_data');
if (!defined('ADD_PROP_PHOTO_DATADATE_TABLE')) define('ADD_PROP_PHOTO_DATADATE_TABLE', $prefixeTable.'add_properties_photos_datadate');
define('ADD_PROP_PHOTO_ADMIN',get_root_url().'admin.php?page=plugin-'.ADD_PROP_PHOTO_DIR);

include_once(ADD_PROP_PHOTO_PATH . 'include/function.aip.inc.php');

add_event_handler('loading_lang', 'manage_properties_photos_loading_lang');	  
function manage_properties_photos_loading_lang(){
  load_language('plugin.lang', ADD_PROP_PHOTO_PATH);
}

 // Plugin on picture page
if (script_basename() == 'picture'){
  include_once(dirname(__FILE__).'/initpicture.php');
}

  // Plugin for admin
if (script_basename() == 'admin'){
  include_once(dirname(__FILE__).'/initadmin.php');
}

/*delete photo*/
$datedelete = pwg_db_fetch_assoc(pwg_query("SELECT id_prop_pho FROM " . ADD_PROP_PHOTO_TABLE . " WHERE dataprop = 'DeletePhoto' AND Typ = 2 LIMIT 1;"));
  if ($datedelete != NULL){
	$photodelete = pwg_query('
	  SELECT id_img
	  FROM '.ADD_PROP_PHOTO_DATADATE_TABLE.'
	  WHERE datadate <= NOW()
	  AND id_prop_pho = '.$datedelete['id_prop_pho'].'
	;');
	if (pwg_db_num_rows($photodelete)) {
	  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
	  while ($delete_photos = pwg_db_fetch_assoc($photodelete)) {
		$tab_delete_photos[]=$delete_photos['id_img'];
	  }
	  global $user,$conf; 
	  $user['id']=$conf['webmaster_id'];
	  delete_elements($tab_delete_photos, true);
	  pwg_query('DELETE FROM '.ADD_PROP_PHOTO_DATA_TABLE.' WHERE id_img IN ('.implode(',', $tab_delete_photos).');');
	  pwg_query('DELETE FROM '.ADD_PROP_PHOTO_DATADATE_TABLE.' WHERE id_img IN ('.implode(',', $tab_delete_photos).');');
	  invalidate_user_cache();
	  unset($user);
	}
  }

/*end delete photo*/

?>