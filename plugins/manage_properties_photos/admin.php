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

if (!defined('PHPWG_ROOT_PATH'))
    die('Hacking attempt!');
global $template, $conf, $user, $pwg_loaded_plugins;
include_once(PHPWG_ROOT_PATH . 'admin/include/tabsheet.class.php');
load_language('plugin.lang', ADD_PROP_PHOTO_PATH);
$my_base_url = get_admin_plugin_menu_link(__FILE__);

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

//-------------------------------------------------------- sections definitions

$template->assign(array(
  'ADD_PROP_PHOTO_PATH'=> get_root_url() . ADD_PROP_PHOTO_PATH,
  ));

/*download_counter*/
if (isset($pwg_loaded_plugins['download_counter'])){
  $row = pwg_db_fetch_assoc(pwg_query('SELECT dataprop FROM '. ADD_PROP_PHOTO_TABLE .' where dataprop ="plugdownload_counter";'));
	if(empty($row)){
	  $row = pwg_db_fetch_assoc(pwg_query('SELECT MAX(orderprop) FROM '. ADD_PROP_PHOTO_TABLE ));
	  $or = ($row['MAX(orderprop)'] + 1);
	  pwg_query('INSERT INTO ' . $prefixeTable . 'add_properties_photos(wording,orderprop,active,edit,Typ,dataprop)VALUES ("Download Counter","' . $or . '","0",0,"1","plugdownload_counter");');
	}
}else{
  pwg_query('DELETE FROM ' . ADD_PROP_PHOTO_TABLE . ' where dataprop ="plugdownload_counter";'); 
}
/*end*/
/*Color Palette*/
if (isset($pwg_loaded_plugins['ColorPalette'])){
  $row = pwg_db_fetch_assoc(pwg_query('SELECT dataprop FROM '. ADD_PROP_PHOTO_TABLE .' where dataprop ="plugColorPalette";'));
	if(empty($row)){
	  $row = pwg_db_fetch_assoc(pwg_query('SELECT MAX(orderprop) FROM '. ADD_PROP_PHOTO_TABLE ));
	  $or = ($row['MAX(orderprop)'] + 1);
	  pwg_query('INSERT INTO ' . $prefixeTable . 'add_properties_photos(wording,orderprop,active,edit,Typ,dataprop)VALUES ("ColorPalette","' . $or . '","0",0,"1","plugColorPalette");');
	}
}else{
  pwg_query('DELETE FROM ' . ADD_PROP_PHOTO_TABLE . ' where dataprop ="plugColorPalette";'); 
}
/*end*/
/*Expiry Date*/
if (isset($pwg_loaded_plugins['expiry_date'])){
  $row = pwg_db_fetch_assoc(pwg_query('SELECT dataprop FROM '. ADD_PROP_PHOTO_TABLE .' where dataprop ="plugexpiry_date";'));
	if(empty($row)){
	  $row = pwg_db_fetch_assoc(pwg_query('SELECT MAX(orderprop) FROM '. ADD_PROP_PHOTO_TABLE ));
	  $or = ($row['MAX(orderprop)'] + 1);
	  pwg_query('INSERT INTO ' . $prefixeTable . 'add_properties_photos(wording,orderprop,active,edit,Typ,dataprop)VALUES ("expiry_date","' . $or . '","0",0,"1","plugexpiry_date");');
	}
}else{
  pwg_query('DELETE FROM ' . ADD_PROP_PHOTO_TABLE . ' where dataprop ="plugexpiry_date";'); 
}
/*end*/
/*Copyrights*/
if (isset($pwg_loaded_plugins['Copyrights'])){
  $row = pwg_db_fetch_assoc(pwg_query('SELECT dataprop FROM '. ADD_PROP_PHOTO_TABLE .' where dataprop ="plugCopyrights";'));
	if(empty($row)){
	  $row = pwg_db_fetch_assoc(pwg_query('SELECT MAX(orderprop) FROM '. ADD_PROP_PHOTO_TABLE ));
	  $or = ($row['MAX(orderprop)'] + 1);
	  pwg_query('INSERT INTO ' . $prefixeTable . 'add_properties_photos(wording,orderprop,active,edit,Typ,dataprop)VALUES ("Copyrights","' . $or . '","0",0,"1","plugCopyrights");');
	}
}else{
  pwg_query('DELETE FROM ' . ADD_PROP_PHOTO_TABLE . ' where dataprop ="plugCopyrights";'); 
}
/*end*/
/*added by*/
if (isset($pwg_loaded_plugins['Photo_add_by'])){
  $row = pwg_db_fetch_assoc(pwg_query('SELECT dataprop FROM '. ADD_PROP_PHOTO_TABLE .' where dataprop ="plugPhoto_add_by";'));
	if(empty($row)){
	  $row = pwg_db_fetch_assoc(pwg_query('SELECT MAX(orderprop) FROM '. ADD_PROP_PHOTO_TABLE ));
	  $or = ($row['MAX(orderprop)'] + 1);
	  pwg_query('INSERT INTO ' . $prefixeTable . 'add_properties_photos(wording,orderprop,active,edit,Typ,dataprop)VALUES ("addedby","' . $or . '","0",0,"1","plugPhoto_add_by");');
	}
}else{
  pwg_query('DELETE FROM ' . ADD_PROP_PHOTO_TABLE . ' where dataprop ="plugPhoto_add_by";'); 
}
/*end*/

if (!isset($_GET['tab']))
    $page['tab'] = 'define_properties';
else
    $page['tab'] = $_GET['tab'];


if ($page['tab'] != 'iap') {
    $tabsheet = new tabsheet();
    $tabsheet->add('addip', l10n('Property'), ADD_PROP_PHOTO_ADMIN . '-define_properties');
	$tabsheet->add('config', l10n('Configuration'), ADD_PROP_PHOTO_ADMIN . '-config');
    $tabsheet->select($page['tab']);
    $tabsheet->assign();
} else if ($_GET['tab'] == 'iap') {

    $page['active_menu'] = get_active_menu('photo'); // force oppening "Photos" menu block

    /* Basic checks */
    check_status(ACCESS_ADMINISTRATOR);

    check_input_parameter('image_id', $_GET, false, PATTERN_ID);
    $id_img = $_GET['image_id'];
    $admin_photo_base_url = get_root_url() . 'admin.php?page=photo-' . $_GET['image_id'];

    $page['tab'] = 'iap';

    $tabsheet = new tabsheet();
    $tabsheet->set_id('photo');
    $tabsheet->select('iap');
    $tabsheet->assign();

    $template->assign(
            'gestionD', array(
        'A' => 'a'
    ));
    
	if (isset($pwg_loaded_plugins['ExtendedDescription'])){
        add_event_handler('AP_render_content', 'get_user_language_desc');
                $template->assign('useED',1);
    }else{
        $template->assign('useED',0);
    }

    $tab_add_info_one_photo = tab_add_info_by_photo($_GET['image_id']);
    if (pwg_db_num_rows($tab_add_info_one_photo)) {
        while ($info_photos = pwg_db_fetch_assoc($tab_add_info_one_photo)) {
			if($info_photos['Typ']==2){
				$d = data_info_photosdate($id_img, $info_photos['id_prop_pho']);
				$row = pwg_db_fetch_assoc($d);

				$items = array(
					'IDPHO' => $_GET['image_id'],
					'IDINFOPHO' => $info_photos['id_prop_pho'],
					'AIPWORDING' => trigger_change('AP_render_content',$info_photos['wording']),
					//'AIPDATA' => $row['datadate'],
					'AIPTYP' => $info_photos['Typ'],
				);
				if(isset($row['datadate'])){
					$items['AIPDATA']=$row['datadate'];	
				}else{
					$items['AIPDATA']="";	
				}
				if($info_photos['wording']=="**delpho**"){
					$items['AIPWORDING'] =l10n('Delete photo');
				}
			}else{
				$d = data_info_photos($id_img, $info_photos['id_prop_pho']);
				$row = pwg_db_fetch_assoc($d);

				$items = array(
					'IDPHO' => $_GET['image_id'],
					'IDINFOPHO' => $info_photos['id_prop_pho'],
					'AIPWORDING' => trigger_change('AP_render_content',$info_photos['wording']),
					'AIPTYP' => $info_photos['Typ'],
				);
				if(isset($row['data'])){
				  $items['AIPDATA']=$row['data'];	
				}else{
				  $items['AIPDATA']="";	
				}
				if($info_photos['Typ']==4||$info_photos['Typ']==5){
					$items['AIPSELECT']=unserialize($info_photos['dataprop']);
					foreach ($items['AIPSELECT'] as $key => $dataselect){
						$items['AIPSELECTTRANS'][] = trigger_change('AP_render_content',$dataselect);
					}
				}
			}
            $template->append('info_photosI', $items);
        }
    }

    if (isset($_POST['submitaddinfoimg'])) {
        foreach ($_POST['data'] AS $id_prop_pho => $data) {
            $q = 'SELECT 1 FROM ' . ADD_PROP_PHOTO_DATA_TABLE . ' WHERE id_img=' . $id_img . ' AND id_prop_pho=' . $id_prop_pho;
            $test = pwg_query($q);
            $row = pwg_db_fetch_assoc($test);
            if (!empty($row)) {
                if ($data != '') {
                    $query = 'UPDATE ' . $prefixeTable . 'add_properties_photos_data SET data="' . $data . '" WHERE id_img=' . $id_img . ' AND id_prop_pho=' . $id_prop_pho;
                    pwg_query($query);
                } else {
                    $query = 'DELETE FROM ' . $prefixeTable . 'add_properties_photos_data WHERE id_img=' . $id_img . ' AND id_prop_pho=' . $id_prop_pho;
                    pwg_query($query);
                }
            } else if ($data != '') {
                $query = 'INSERT ' . $prefixeTable . 'add_properties_photos_data(id_img,id_prop_pho,data) VALUES (' . $id_img . ',' . $id_prop_pho . ',"' . $data . '");';
                pwg_query($query);
            }    
        }
		        foreach ($_POST['datadate'] AS $id_prop_pho => $data) {
            $q = 'SELECT 1 FROM ' . ADD_PROP_PHOTO_DATADATE_TABLE . ' WHERE id_img=' . $id_img . ' AND id_prop_pho=' . $id_prop_pho;
            $test = pwg_query($q);
            $row = pwg_db_fetch_assoc($test);
            if (!empty($row)) {
                if ($data != '') {
                    $query = 'UPDATE ' . $prefixeTable . 'add_properties_photos_datadate SET datadate="' . $data . '" WHERE id_img=' . $id_img . ' AND id_prop_pho=' . $id_prop_pho;
                    pwg_query($query);
                } else {
                    $query = 'DELETE FROM ' . $prefixeTable . 'add_properties_photos_datadate WHERE id_img=' . $id_img . ' AND id_prop_pho=' . $id_prop_pho;
                    pwg_query($query);
                }
            } else if ($data != '') {
                $query = 'INSERT ' . $prefixeTable . 'add_properties_photos_datadate(id_img,id_prop_pho,datadate) VALUES (' . $id_img . ',' . $id_prop_pho . ',"' . $data . '");';
                pwg_query($query);
            }   
        }
        $redirect_url = ADD_PROP_PHOTO_ADMIN . '-iap&amp;image_id=' . $id_img;
        $_SESSION['page_infos'] = array(l10n('Properties update'));
        redirect($redirect_url);
    }
}

switch ($page['tab']) {
    case 'define_properties':
        $admin_base_url = ADD_PROP_PHOTO_ADMIN . '-define_properties';
        $template->assign(
            'addinfotemplate', array(
				'addinfo' => l10n('addinfo'),
		));
		$maxfields = (isset($conf['mpp_max_fields'])) ? $conf['mpp_max_fields'] : '5';
        $template->assign('MPPMAXFIELD',$maxfields);
		
    if (isset($pwg_loaded_plugins['ExtendedDescription'])){
        add_event_handler('AP_render_content', 'get_user_language_desc');
                $template->assign('useED',1);
    }else{
        $template->assign('useED',0);
    }
        
        $admin_base_url = ADD_PROP_PHOTO_ADMIN . '-define_properties';
        $tab_info_photos = tab_info_photos();

        if (pwg_db_num_rows($tab_info_photos)) {
            while ($info_photos = pwg_db_fetch_assoc($tab_info_photos)) {
				$items = array(
                    'IDINFOPHO' => $info_photos['id_prop_pho'],
                    'AIPORDER' => $info_photos['orderprop'],
                    'AIPACTIVE' => $info_photos['active'],
                    'AIPEDIT' => $info_photos['edit'],
                    'U_HIDE' => $admin_base_url . '&amp;hide=' . $info_photos['id_prop_pho'],
                    'U_SHOW' => $admin_base_url . '&amp;show=' . $info_photos['id_prop_pho']
				);
                if($info_photos['id_prop_pho']==1){
				    $items['AIPWORDING'] = l10n('Author');
                }else if($info_photos['id_prop_pho']==2){
					$items['AIPWORDING'] = l10n('Created on');
                }else if($info_photos['id_prop_pho']==3){
                    $items['AIPWORDING'] = l10n('Posted on');
                }else if($info_photos['id_prop_pho']==4){
                    $items['AIPWORDING'] = l10n('Dimensions');
                }else if($info_photos['id_prop_pho']==5){
                    $items['AIPWORDING'] = l10n('File');
                }else if($info_photos['id_prop_pho']==6){
                    $items['AIPWORDING'] = l10n('Filesize');
                }else if($info_photos['id_prop_pho']==7){
                    $items['AIPWORDING'] = l10n('Tags');
                }else if($info_photos['id_prop_pho']==8){
                    $items['AIPWORDING'] = l10n('Albums');
                }else if($info_photos['id_prop_pho']==9){
                    $items['AIPWORDING'] = l10n('Visits');
                }else if($info_photos['id_prop_pho']==10){
					$items['AIPWORDING'] = l10n('Rating score');
                }else if($info_photos['id_prop_pho']==11){
                    $items['AIPWORDING'] = l10n('Who can see this photo?');
                }else{
					if($info_photos['dataprop']=="DeletePhoto" and $info_photos['wording'] ="**delpho**"){
						$items['AIPWORDING'] = l10n('Delete photo');
					}else if($info_photos['dataprop']=="movedescription"){
						$items['AIPWORDING'] = l10n('Description');
					}else if($info_photos['dataprop']=="showid"){
						$items['AIPWORDING'] = l10n('Image id');
					}else if($info_photos['dataprop']=="plugPhoto_add_by"){
						$items['AIPWORDING'] = l10n('Plugins')." - ".l10n('Photo added by');
					}else if($info_photos['dataprop']=="plugdownload_counter"){
						$items['AIPWORDING'] = l10n('Plugins')." - ".l10n('Downloads');
					}else if($info_photos['dataprop']=="plugColorPalette"){
						$items['AIPWORDING'] = l10n('Plugins')." - ".l10n('Palette');
					}else if($info_photos['dataprop']=="plugexpiry_date"){
						$items['AIPWORDING'] = l10n('Plugins')." - ".l10n('Expiry date');
					}else if($info_photos['dataprop']=="plugCopyrights"){
						$items['AIPWORDING'] = l10n('Plugins')." - ".l10n('Copyrights');
					}else{
						$items['AIPWORDING'] = trigger_change('AP_render_content',$info_photos['wording']);	
					}
                    $items['AIPWORDING2'] =  $info_photos['wording'];
					$items['U_DELETE'] =  $admin_base_url . '&amp;delete=' . $info_photos['id_prop_pho'];
					$items['AIPTYP'] =  $info_photos['Typ'];
					if($info_photos['Typ']==4||$info_photos['Typ']==5){
						$items['AIPDATAPROP'] =  json_encode(unserialize($info_photos['dataprop']));
					}else{
						$items['AIPDATAPROP'] = "";
					}
					if($info_photos['Typ']==3||$info_photos['Typ']==6){
							$items['AIPDATAPROP'] =  $info_photos['dataprop'];
					}					
                }        
                $template->append('info_photos', $items);
            }
			
			$idexifone = pwg_db_fetch_assoc(pwg_query("SELECT MIN(id) as id FROM " . IMAGES_TABLE .";"));
			$idexif = (isset($conf['mpp_idexif'])) ? $conf['mpp_idexif'] : $idexifone['id'];
			$filename = pwg_db_fetch_assoc(pwg_query("SELECT path FROM " . IMAGES_TABLE . " WHERE id = '".$idexif."';"));
		  if(empty($filename['path'])){
			  $_SESSION['page_errors'] = array(l10n('reference photo id doesn\'t exist please update'));
		  }else if(exif_imagetype($filename['path']) != IMAGETYPE_JPEG and exif_imagetype($filename['path']) != IMAGETYPE_TIFF_II and exif_imagetype($filename['path']) != IMAGETYPE_TIFF_MM){
			  $_SESSION['page_errors'] = array(l10n('reference photo type doesn\'t use metadata please update')); 
		  }else{	
			$exif = @exif_read_data($filename['path']);
				foreach ($exif as $key => $section){
					if(is_array($section)){
						$i=0;
					foreach ($section as $name => $value){
						if($i==0){
							$items['RM_AFF'] = $key;
						}else{
							$items['RM_AFF'] = '';
						}
						$items['RM_SECTION'] = $key;
						$items['RM_KEY'] = $name;
						$template->append('rm_exif', $items);
						$i++;
					}
				}else{
					$items['RM_SECTION'] = '1';
					$items['RM_KEY'] = $key;
					$items['RM_VALUE'] = $section;
					$template->append('rm_exif', $items);
				}
			}
			$iptc_result = array();
			$imginfo = array();
			getimagesize($filename['path'], $imginfo);
			if (isset ($imginfo['APP13'])){
			 $iptc = iptcparse($imginfo['APP13']);
			  if (is_array($iptc)){
				foreach (array_keys($iptc) as $iptc_key){
					if (isset($iptc[$iptc_key][0])){
						$iptc_result[$iptc_key] = $value;
					}
				}
			  }
			  $keys = array_keys($iptc_result);
			  sort($keys);
			  foreach ($keys as $key){
				$items['RM_KEY'] = $key;
				$template->append('rm_iptc', $items);
			  }
			}
		  }
        }
        
        if (isset($_POST['submitManualOrderInfo'])){
            
            asort($_POST['infoOrd'], SORT_NUMERIC);
            
            $data = array();
            foreach ($_POST['infoOrd'] as $id =>$val){
            
            $data[] = array('id_prop_pho' => $id, 'orderprop' => $val+1);
            }
            $fields = array('primary' => array('id_prop_pho'), 'update' => array('orderprop'));
            mass_updates(ADD_PROP_PHOTO_TABLE, $fields, $data);

          $page['infos'][] = l10n('Properties manual order was saved');
          redirect($admin_base_url);
        }

        if (isset($_POST['submitaddAIP'])) {
			if ($_POST['inserwording'] == '') {
			$_SESSION['page_errors'] = array(l10n('Wording isn\'t empty'));
			redirect($admin_base_url);
			}
			if ($_POST['typ'] == '4'||$_POST['typ'] == '5') {
			$select= serialize($_POST['mytext']);
			}
			if ($_POST['typ'] == '3') {
				if($_POST['selectexif']==''){
					$_SESSION['page_errors'] = array(l10n('exif field can\'t be empty'));
					redirect($admin_base_url);
				}
			$select= $_POST['selectexif'];
			}
			if ($_POST['typ'] == '6') {
				if($_POST['selectiptc']==''){
					$_SESSION['page_errors'] = array(l10n('IPTC field can\'t be empty'));
					redirect($admin_base_url);
				}
			$select= $_POST['selectiptc'];
			}
            if (!isset($_POST['inseractive'])) {
                $_POST['inseractive'] = 0;
            }
            if ($_POST['invisibleID'] == 0) {
                $result = pwg_query('SELECT MAX(orderprop) FROM '. ADD_PROP_PHOTO_TABLE );
                $row = pwg_db_fetch_assoc($result);
                $or = ($row['MAX(orderprop)'] + 1);
					if ($_POST['typ'] == '4'||$_POST['typ'] == '3'||$_POST['typ'] == '5'||$_POST['typ'] == '6') {
						$q = 'INSERT INTO ' . $prefixeTable . 'add_properties_photos(wording,orderprop,active,edit,Typ,dataprop)VALUES ("' . $_POST['inserwording'] . '","' . $or . '","' . $_POST['inseractive'] . '",1,"' . $_POST['typ'] . '",\''. $select .'\');';
						pwg_query($q);
					}else{
						$q = 'INSERT INTO ' . $prefixeTable . 'add_properties_photos(wording,orderprop,active,edit,Typ)VALUES ("' . $_POST['inserwording'] . '","' . $or . '","' . $_POST['inseractive'] . '",1,"' . $_POST['typ'] . '");';
						pwg_query($q);
					}
                $_SESSION['page_infos'] = array(l10n('Property photo add'));
            } else {
					if ($_POST['typ'] == '4'||$_POST['typ'] == '3'||$_POST['typ'] == '5'||$_POST['typ'] == '6') {
					  $q = '
					  UPDATE ' . $prefixeTable . 'add_properties_photos'
                        . ' set wording ="' . $_POST['inserwording'] . '" '
                        . ' ,active =' . $_POST['inseractive']
						. ' ,Typ =' . $_POST['typ']
						. ' ,dataprop =\'' . $select . '\' '
                        . ' WHERE id_prop_pho=' . $_POST['invisibleID'] . ';';
					}else{
					  $q = '
					  UPDATE ' . $prefixeTable . 'add_properties_photos'
                        . ' set wording ="' . $_POST['inserwording'] . '" '
                        . ' ,active=' . $_POST['inseractive']
						. ' ,Typ =' . $_POST['typ']
                        . ' WHERE id_prop_pho=' . $_POST['invisibleID'] . ';';
					}
                pwg_query($q);
                $_SESSION['page_infos'] = array(l10n('Property photo update'));
            }
            redirect($admin_base_url);
        }

        if (isset($_GET['delete'])) {
            check_input_parameter('delete', $_GET, false, PATTERN_ID);
            $query = 'DELETE FROM ' . ADD_PROP_PHOTO_TABLE . ' WHERE id_prop_pho = ' . $_GET['delete'] . ';';
            pwg_query($query);
            $query = 'DELETE FROM ' . ADD_PROP_PHOTO_DATA_TABLE . ' WHERE id_prop_pho = ' . $_GET['delete'] . ';';
            pwg_query($query);
			$query = 'DELETE FROM ' . ADD_PROP_PHOTO_DATADATE_TABLE . ' WHERE id_prop_pho = ' . $_GET['delete'] . ';';
            pwg_query($query);

            $_SESSION['page_infos'] = array(l10n('Property delete'));
            redirect($admin_base_url);
        }
        
        if (isset($_GET['hide'])) {
            check_input_parameter('hide', $_GET, false, PATTERN_ID);
            $query = 'UPDATE ' . ADD_PROP_PHOTO_TABLE . ' SET active = 1 WHERE id_prop_pho=' . $_GET['hide'] . ';';
            pwg_query($query);
        }
        
        if (isset($_GET['show'])) {
            check_input_parameter('show', $_GET, false, PATTERN_ID);
            $query = 'UPDATE ' . ADD_PROP_PHOTO_TABLE . ' SET active = 0 WHERE id_prop_pho=' . $_GET['show'] . ';';
            pwg_query($query);
        }

        break;
		case 'config':
		$maxfields = (isset($conf['mpp_max_fields'])) ? $conf['mpp_max_fields'] : '5';
        $template->assign('MPPMAXFIELD',$maxfields);
		$idexif = (isset($conf['mpp_idexif'])) ? $conf['mpp_idexif'] : '';
        $template->assign('MPPIDEXIF',$idexif);
        $admin_base_url = ADD_PROP_PHOTO_ADMIN . '-config';
			$template->assign(
				'mppconfig', array(
					'a' => 'a',
			));
				
		$delpho = pwg_db_fetch_assoc(pwg_query('SELECT dataprop FROM ' . ADD_PROP_PHOTO_TABLE.' WHERE dataprop="DeletePhoto";'));
		if(empty($delpho)){
			$template->assign(
				'mppconfig', array(
					'ADDPHOAC' => 'actiondelphoadd',
			));
		}
		if (isset($_POST['submitadddelpho'])){
			$result = pwg_query('SELECT MAX(orderprop) FROM '. ADD_PROP_PHOTO_TABLE );
			$row = pwg_db_fetch_assoc($result);
			$or = ($row['MAX(orderprop)'] + 1);
			$q = 'INSERT INTO ' . $prefixeTable . 'add_properties_photos(wording,orderprop,active,edit,Typ,dataprop)VALUES ("**delpho**","' . $or . '","1",1,"2","DeletePhoto");';
			pwg_query($q);
			$_SESSION['page_infos'] = array(l10n('Information data registered in database'));
			redirect(ADD_PROP_PHOTO_ADMIN . '-config');
		}
		if (isset($_POST['submitremovedelpho'])){
			$iddelpho = pwg_db_fetch_assoc(pwg_query('SELECT id_prop_pho FROM '. ADD_PROP_PHOTO_TABLE.' where dataprop="DeletePhoto";'));
			pwg_query('DELETE FROM ' . ADD_PROP_PHOTO_TABLE . ' where dataprop ="DeletePhoto";');
			$query = 'DELETE FROM ' . ADD_PROP_PHOTO_DATADATE_TABLE . ' WHERE id_prop_pho = ' . $iddelpho['id_prop_pho'] . ';';
            pwg_query($query);
			$_SESSION['page_infos'] = array(l10n('Information data registered in database'));
			redirect(ADD_PROP_PHOTO_ADMIN . '-config');
		}
		$descpho = pwg_db_fetch_assoc(pwg_query('SELECT dataprop FROM ' . ADD_PROP_PHOTO_TABLE.' WHERE dataprop="movedescription";'));
		if(empty($descpho)){
			$vars['MOVEDESC']='actionmovedesc';
			$template->append('mppconfig', $vars, true);
		}
		if (isset($_POST['submitmovedesc'])){
			$result = pwg_query('SELECT MAX(orderprop) FROM '. ADD_PROP_PHOTO_TABLE );
			$row = pwg_db_fetch_assoc($result);
			$or = ($row['MAX(orderprop)'] + 1);
			$q = 'INSERT INTO ' . $prefixeTable . 'add_properties_photos(wording,orderprop,active,edit,Typ,dataprop)VALUES ("Description","' . $or . '","0",0,"1","movedescription");';
			pwg_query($q);
			$_SESSION['page_infos'] = array(l10n('Information data registered in database'));
			redirect(ADD_PROP_PHOTO_ADMIN . '-config');
		}
		if (isset($_POST['submitdefaultdesc'])){
			pwg_query('DELETE FROM ' . ADD_PROP_PHOTO_TABLE . ' where dataprop ="movedescription";');
			$_SESSION['page_infos'] = array(l10n('Information data registered in database'));
			redirect(ADD_PROP_PHOTO_ADMIN . '-config');
		}
		if (isset($_POST['submitmppoption'])){
			conf_update_param('mpp_max_fields', $_POST['mppmaxfield']);
			conf_update_param('mpp_idexif', $_POST['mppitidexif']);
			$_SESSION['page_infos'] = array(l10n('Information data registered in database'));
			redirect(ADD_PROP_PHOTO_ADMIN . '-config');
		}
		$mppshid = pwg_db_fetch_assoc(pwg_query('SELECT dataprop FROM ' . ADD_PROP_PHOTO_TABLE.' WHERE dataprop="showid";'));
		if(empty($mppshid)){
			$vars['MPPSHID']='actionschowid';
			$template->append('mppconfig', $vars, true);
		}
		if (isset($_POST['submitmppsid'])){
			$row = pwg_db_fetch_assoc(pwg_query('SELECT MAX(orderprop) FROM '. ADD_PROP_PHOTO_TABLE ));
			$or = ($row['MAX(orderprop)'] + 1);
			$q = 'INSERT INTO ' . $prefixeTable . 'add_properties_photos(wording,orderprop,active,edit,Typ,dataprop)VALUES ("ID","' . $or . '","0",0,"1","showid");';
			pwg_query($q);
			$_SESSION['page_infos'] = array(l10n('Information data registered in database'));
			redirect(ADD_PROP_PHOTO_ADMIN . '-config');
		}
		if (isset($_POST['submitmpphid'])){
			pwg_query('DELETE FROM ' . ADD_PROP_PHOTO_TABLE . ' where dataprop ="showid";');
			$_SESSION['page_infos'] = array(l10n('Information data registered in database'));
			redirect(ADD_PROP_PHOTO_ADMIN . '-config');
		}
		break;
 }


$template->set_filenames(array('plugin_admin_content' => dirname(__FILE__) . '/admin/admin.tpl'));
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
?>