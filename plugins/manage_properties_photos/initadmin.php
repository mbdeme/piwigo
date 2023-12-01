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

//add admin menu
/*
add_event_handler('get_admin_plugin_menu_links', 'add_info_photo_admin_menu');
function add_info_photo_admin_menu($menu){
  if (is_webmaster()){
    load_language('plugin.lang', ADD_PROP_PHOTO_PATH);
    $menu[] = array(
        'NAME' => l10n('Manage properties photos'),
        'URL' => ADD_PROP_PHOTO_ADMIN,
    );
  }
     return $menu;
}
*/
add_event_handler('tabsheet_before_select', 'aip_tabsheet_before_select',
    EVENT_HANDLER_PRIORITY_NEUTRAL);

function aip_tabsheet_before_select($sheets, $id){
  global $template, $page;
  if ($id == 'photo'){
    $sheets['iap'] = array(
      'caption' => l10n('Properties additionals'),
      'url' => ADD_PROP_PHOTO_ADMIN.'-iap&amp;image_id='.$_GET['image_id'],
      );
  }
  return $sheets;
}

//add manage by batch Manager
add_event_handler('loc_end_element_set_global', 'MPP_loc_end_element_set_global');
add_event_handler('element_set_global_action', 'MPP_element_set_global_action', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);
  
function MPP_loc_end_element_set_global(){
  global $template, $pwg_loaded_plugins;
  
  $template->assign(array(
  'ADD_PROP_PHOTO_PATH'=> get_root_url() . ADD_PROP_PHOTO_PATH,
  )); 
  
  $q = 'SELECT 1 FROM ' . ADD_PROP_PHOTO_TABLE . ' WHERE edit=1';
  $test = pwg_query($q);
  $row = pwg_db_fetch_assoc($test);
  if (!empty($row)){
	$propertieslist = array();
	$propertieslist2 = tab_add_info_by_photo();
	if (isset($pwg_loaded_plugins['ExtendedDescription'])){
		add_event_handler('AP_render_content', 'get_user_language_desc');
		$template->assign('useED',1);
      }else{
        $template->assign('useED',0);
      }
		$template->assign('DATADATE', date('Y-m-d').' 10:05:05');
	while ($info_select = pwg_db_fetch_assoc($propertieslist2)){
		$items = array(
			'IDINFOPHO' => $info_select['id_prop_pho'],
			'AIPWORDING' => trigger_change('AP_render_content',$info_select['wording']),
			'AIPTYP' => $info_select['Typ'],
		);
		if($info_select['Typ']==4||$info_select['Typ']==5){
			$items['AIPDATAPROP'] =  json_encode(unserialize($info_select['dataprop']));
		}else{
			$items['AIPDATAPROP'] = "";
		}		
		if($info_select['wording']=="**delpho**"){
			$items['AIPWORDING'] =l10n('Delete photo');
		}
		$template->append('info_select', $items);
	}
	
    $template->set_filename('MMPP', realpath(ADD_PROP_PHOTO_PATH.'mmp.tpl'));
    $template->assign('propertieslist', $propertieslist);
    $template->append('element_set_global_plugins_actions', array(
      'ID' => 'MPP', 
      'NAME' => l10n('Change photos properties'), 
      'CONTENT' => $template->parse('MMPP', true)
	));
  }
}
 
function MPP_element_set_global_action($action, $collection){
  if ($action == 'MPP'){
	global $page,$template,$prefixeTable;
    $id_prop_pho= $_POST['IDMPP'];
	$Typ=$_POST['invisibleTyp'];
	if($Typ==2){
		$data= $_POST['datadate'];
	}else if($Typ==4){
		$data= $_POST['dataselect'];
	}else if($Typ==5){
		$data= $_POST['radioselect'];
	}else{
		$data= $_POST['dataglob'];
	}
	
  if($Typ==2){
	if (!empty($_POST['check_MPP'])){
	  foreach ($collection as $image_id){
		$query = 'DELETE FROM ' . $prefixeTable . 'add_properties_photos_datadate WHERE id_img=' . $image_id . ' AND id_prop_pho=' . $id_prop_pho;
		pwg_query($query);
      }
    }else{
      foreach ($collection as $image_id){
	    $q = 'SELECT 1 FROM ' . ADD_PROP_PHOTO_DATADATE_TABLE . ' WHERE id_img=' . $image_id . ' AND id_prop_pho=' . $id_prop_pho;
        $test = pwg_query($q);
        $row = pwg_db_fetch_assoc($test);
        if (!empty($row)) {
		  if ($data != '') {
			$query = 'UPDATE ' . $prefixeTable . 'add_properties_photos_datadate SET datadate="' . $data . '" WHERE id_img=' . $image_id . ' AND id_prop_pho=' . $id_prop_pho;
			pwg_query($query);
		  }else{
			$query = 'DELETE FROM ' . $prefixeTable . 'add_properties_photos_datadate WHERE id_img=' . $image_id . ' AND id_prop_pho=' . $id_prop_pho;
			pwg_query($query);
		  }
        }else if ($data != ''){
            $query = 'INSERT ' . $prefixeTable . 'add_properties_photos_datadate(id_img,id_prop_pho,datadate) VALUES (' . $image_id . ',' . $id_prop_pho . ',"' . $data . '");';
            pwg_query($query);
        }
      }
    }
  }else{
	if (!empty($_POST['check_MPP'])){
	  foreach ($collection as $image_id){
		$query = 'DELETE FROM ' . $prefixeTable . 'add_properties_photos_data WHERE id_img=' . $image_id . ' AND id_prop_pho=' . $id_prop_pho;
		pwg_query($query);
      }
    }else{
      foreach ($collection as $image_id){
	    $q = 'SELECT id_img FROM ' . ADD_PROP_PHOTO_DATA_TABLE . ' WHERE id_img=' . $image_id . ' AND id_prop_pho=' . $id_prop_pho;
        $test = pwg_query($q);
        $row = pwg_db_fetch_assoc($test);
        if (!empty($row)) {
		  if ($data != '') {
			$query = 'UPDATE ' . $prefixeTable . 'add_properties_photos_data SET data="' . $data . '" WHERE id_img=' . $image_id . ' AND id_prop_pho=' . $id_prop_pho;
			pwg_query($query);
		  }else{
			$query = 'DELETE FROM ' . $prefixeTable . 'add_properties_photos_data WHERE id_img=' . $image_id . ' AND id_prop_pho=' . $id_prop_pho;
			pwg_query($query);
		  }
        }else if ($data != ''){
            $query = 'INSERT ' . $prefixeTable . 'add_properties_photos_data(id_img,id_prop_pho,data) VALUES (' . $image_id . ',' . $id_prop_pho . ',"' . $data . '");';
            pwg_query($query);
        }
      }
    }
  }
  }
}
 
add_event_handler('loc_begin_admin_page', 'mpp_change_admin_show');
function mpp_change_admin_show(){
  global $template;
  $template->set_prefilter('config', 'mpp_change_admin_show_prefilter');
}

function mpp_change_admin_show_prefilter($content){
  $search = '#(<fieldset id="pictureInfoConf">).*</fieldset>#ms';
  return preg_replace($search, '
  <fieldset id="pictureInfoConf">
    <legend>{\'Photo Properties\'|@translate}</legend> 
	  <a href="'.ADD_PROP_PHOTO_ADMIN.'"><span class="icon-arrows-cw"></span>{\'Manage properties photos\'|@translate}</a>
  </fieldset>
  ', $content);
}