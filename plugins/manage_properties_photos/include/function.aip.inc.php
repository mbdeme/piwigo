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

/*picture*/
function tab_add_info_one_photo($id_img){
$query = '
    SELECT aip.id_prop_pho,aip.wording,aipd.data,aip.orderprop,aip.Typ
    FROM ' . ADD_PROP_PHOTO_TABLE . ' AS aip
    LEFT JOIN ' . ADD_PROP_PHOTO_DATA_TABLE . ' AS aipd ON aip.id_prop_pho=aipd.id_prop_pho
    WHERE aipd.id_img = \'' . $id_img . '\' 
    AND aip.active = 0 
    ORDER BY aip.orderprop ASC
    ;';

return pwg_query($query);
}
/*
function tab_add_info_one_photo2($id_img){
$query = '
    SELECT aip.id_prop_pho,aip.wording,aipd.data,aip.orderprop
    FROM ' . ADD_PROP_PHOTO_DATA_TABLE . ' AS aipd
    RIGHT JOIN ' . ADD_PROP_PHOTO_TABLE . ' AS aip ON aip.id_prop_pho=aipd.id_prop_pho
    WHERE aipd.id_img = \'' . $id_img . '\' 
    AND aip.active = 0 
    ORDER BY aip.orderprop ASC
    ;';

return pwg_query($query);
}
*/
/*Admin*/
function tab_add_info_by_photo(){
$query = '
    SELECT id_prop_pho,wording,Typ,dataprop
    FROM ' . ADD_PROP_PHOTO_TABLE . ' 
    WHERE edit = 1
	AND typ != 3
	AND typ != 6
    ORDER BY orderprop ASC
    ;';
return pwg_query($query);
}


/*initpicture*/
function tab_add_info_by_photo_show(){
$query = '
    SELECT id_prop_pho,wording,orderprop,Typ,dataprop
    FROM ' . ADD_PROP_PHOTO_TABLE . ' 
    WHERE active = 0 
    ORDER BY orderprop ASC
    ;';
return pwg_query($query);
}

/*initpicture et admin*/
function data_info_photos($id_img=null,$id_prop_pho=NULL){
$query = '
    SELECT data
    FROM ' . ADD_PROP_PHOTO_DATA_TABLE;
    $wa='WHERE';
    if($id_img!=null){
        $query .=' '.$wa.' id_img='.$id_img;$wa='AND';
    }
    if($id_prop_pho!=null){
        $query .=' '.$wa.' id_prop_pho='.$id_prop_pho;$wa='AND';
    }
$query .= ';';
return pwg_query($query);
}

function data_info_photosdate($id_img=null,$id_prop_pho=NULL){
$query = '
    SELECT datadate
    FROM ' . ADD_PROP_PHOTO_DATADATE_TABLE;
    $wa='WHERE';
    if($id_img!=null){
        $query .=' '.$wa.' id_img='.$id_img;$wa='AND';
    }
    if($id_prop_pho!=null){
        $query .=' '.$wa.' id_prop_pho='.$id_prop_pho;$wa='AND';
    }
$query .= ';';
return pwg_query($query);
}

/*Admin*/
function tab_info_photos($id_prop_pho=NULL){
$query = '
    SELECT id_prop_pho,wording,orderprop,active,edit,Typ,dataprop
    FROM ' . ADD_PROP_PHOTO_TABLE;
    if($id_prop_pho!=null){
        $query .= ' WHERE id_prop_pho='.$id_prop_pho;
    }

$query .= ' ORDER BY orderprop ASC
    ;';
return pwg_query($query);
}

?>