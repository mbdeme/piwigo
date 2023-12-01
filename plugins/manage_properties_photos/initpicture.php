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

//Ajout du prefiltre
add_event_handler('loc_begin_picture', 'add_info_photo_pre', 05);
add_event_handler('loc_end_picture', 'add_info_photo_pre2', 95);

function add_info_photo_pre() {
    global $template;
	$template->set_prefilter('picture', 'add_info_photo_preT');
}

function add_info_photo_pre2() {
	global $template,$pwg_loaded_plugins;
	if (isset($pwg_loaded_plugins['ColorPalette'])){
	  $template->set_prefilter('picture', 'color_palette_picture_prefilter',0);
	}
	if (isset($pwg_loaded_plugins['Copyrights'])){
	  $template->set_prefilter('picture', 'copyrights_add_to_pic_info',0);
	}
}

function add_info_photo_preT($content) {

    global $conf, $user;

   $replastandard='<dl id="standard" class="imageInfoTable">{strip}
{foreach from=$add_info_photos item=addinfophotos}
    {if $addinfophotos.AIPID == 1 and isset($INFO_AUTHOR)}
    <div id="Author" class="imageInfo">
		<dt>{\'Author\'|@translate}</dt>
		<dd>{$INFO_AUTHOR}</dd>
	</div>
    {else if $addinfophotos.AIPID == 2 and isset($INFO_CREATION_DATE)}
    <div id="datecreate" class="imageInfo">
		<dt>{\'Created on\'|@translate}</dt>
		<dd>{$INFO_CREATION_DATE}</dd>
	</div>
    {else if $addinfophotos.AIPID == 3 and isset($INFO_POSTED_DATE)}
	<div id="datepost" class="imageInfo">
		<dt>{\'Posted on\'|@translate}</dt>
		<dd>{$INFO_POSTED_DATE}</dd>
	</div>
    {else if $addinfophotos.AIPID == 4 and isset($INFO_DIMENSIONS)}
	<div id="Dimensions" class="imageInfo">
		<dt>{\'Dimensions\'|@translate}</dt>
		<dd>{$INFO_DIMENSIONS}</dd>
	</div>
    {else if $addinfophotos.AIPID == 5}
	<div id="File" class="imageInfo">
		<dt>{\'File\'|@translate}</dt>
		<dd>{$INFO_FILE}</dd>
	</div>
    {else if $addinfophotos.AIPID == 6 and isset($INFO_FILESIZE)}
	<div id="Filesize" class="imageInfo">
		<dt>{\'Filesize\'|@translate}</dt>
		<dd>{$INFO_FILESIZE}</dd>
	</div>
    {else if $addinfophotos.AIPID == 7 and isset($related_tags)}
    	<div id="Tags" class="imageInfo">
        <dt>{\'Tags\'|@translate}</dt>
		<dd>
		{foreach from=$related_tags item=tag name=tag_loop}{if !$smarty.foreach.tag_loop.first}, {/if}<a href="{$tag.URL}">{$tag.name}</a>{/foreach}
		</dd>
	</div>
    {else if $addinfophotos.AIPID == 8 and isset($related_categories)}
	<div id="Categories" class="imageInfo">
            <dt>{\'Albums\'|@translate}</dt>
            <dd>
                <ul>
                    {foreach from=$related_categories item=cat}
                        <li>{$cat}</li>
                    {/foreach}
                </ul>
            </dd>
	</div>
    {else if $addinfophotos.AIPID == 9}
	<div id="Visits" class="imageInfo">
		<dt>{\'Visits\'|@translate}</dt>
		<dd>{$INFO_VISITS}</dd>
	</div>
    {else if $addinfophotos.AIPID == 10 and isset($rate_summary)}
	<div id="Average" class="imageInfo">
		<dt>{\'Rating score\'|@translate}</dt>
		<dd>
		{if $rate_summary.count}
			<span id="ratingScore">{$rate_summary.score}</span> <span id="ratingCount">({$rate_summary.count|@translate_dec:\'%d rate\':\'%d rates\'})</span>
		{else}
			<span id="ratingScore">{\'no rate\'|@translate}</span> <span id="ratingCount"></span>
		{/if}
		</dd>
	</div>
        {if isset($rating)}
	<div id="rating" class="imageInfo">
		<dt>
			<span id="updateRate">{if isset($rating.USER_RATE)}{\'Update your rating\'|@translate}{else}{\'Rate this photo\'|@translate}{/if}</span>
		</dt>
		<dd>
			<form action="{$rating.F_ACTION}" method="post" id="rateForm" style="margin:0;">
			<div>
			{foreach from=$rating.marks item=mark name=rate_loop}
			{if isset($rating.USER_RATE) && $mark==$rating.USER_RATE}
				<input type="button" name="rate" value="{$mark}" class="rateButtonSelected" title="{$mark}">
			{else}
				<input type="submit" name="rate" value="{$mark}" class="rateButton" title="{$mark}">
			{/if}
			{/foreach}
			{strip}{combine_script id=\'core.scripts\' load=\'async\' path=\'themes/default/js/scripts.js\'}
			{combine_script id=\'rating\' load=\'async\' require=\'core.scripts\' path=\'themes/default/js/rating.js\'}
			{footer_script}
				var _pwgRatingAutoQueue = _pwgRatingAutoQueue||[];
				_pwgRatingAutoQueue.push( {ldelim}rootUrl: \'{$ROOT_URL}\', image_id: {$current.id},
					onSuccess : function(rating) {ldelim}
						var e = document.getElementById("updateRate");
						if (e) e.innerHTML = "{\'Update your rating\'|@translate|@escape:\'javascript\'}";
						e = document.getElementById("ratingScore");
						if (e) e.innerHTML = rating.score;
						e = document.getElementById("ratingCount");
						if (e) {ldelim}
							if (rating.count == 1) {ldelim}
								e.innerHTML = "({\'%d rate\'|@translate|@escape:\'javascript\'})".replace( "%d", rating.count);
							} else {ldelim}
								e.innerHTML = "({\'%d rates\'|@translate|@escape:\'javascript\'})".replace( "%d", rating.count);
              }
						{rdelim}
					{rdelim}{rdelim} );
			{/footer_script}
			{/strip}
			</div>
			</form>
		</dd>
	</div>
        {/if}
    {else if $addinfophotos.AIPID == 11 and $display_info.privacy_level and isset($available_permission_levels)}
	<div id="Privacy" class="imageInfo">
		<dt>{\'Who can see this photo?\'|@translate}</dt>
		<dd>
			<div>
				<a id="privacyLevelLink" href>{$available_permission_levels[$current.level]}</a>
			</div>
{combine_script id=\'core.scripts\' load=\'async\' path=\'themes/default/js/scripts.js\'}
{footer_script require=\'jquery\'}{strip}
function setPrivacyLevel(id, level){
(new PwgWS(\'{$ROOT_URL}\')).callService(
	"pwg.images.setPrivacyLevel", { image_id:id, level:level},
	{
		method: "POST",
		onFailure: function(num, text) { alert(num + " " + text); },
		onSuccess: function(result) {
			  jQuery(\'#privacyLevelBox .switchCheck\').css(\'visibility\',\'hidden\');
				jQuery(\'#switchLevel\'+level).prev(\'.switchCheck\').css(\'visibility\',\'visible\');
				jQuery(\'#privacyLevelLink\').text(jQuery(\'#switchLevel\'+level).text());
		}
	}
	);
}
(SwitchBox=window.SwitchBox||[]).push("#privacyLevelLink", "#privacyLevelBox");
{/strip}{/footer_script}
			<div id="privacyLevelBox" class="switchBox" style="display:none">
				{foreach from=$available_permission_levels item=label key=level}
					<span class="switchCheck"{if $level != $current.level} style="visibility:hidden"{/if}>&#x2714; </span>
					<a id="switchLevel{$level}" href="javascript:setPrivacyLevel({$current.id},{$level})">{$label}</a><br>
				{/foreach}
			</div>
		</dd>
	</div>
    {else if $addinfophotos.AIPWORDING == \'Description\' and isset($COMMENT_IMG)}
	<div id="Description" class="imageInfo">
            <dt>{\'Description\'|@translate}</dt>
            <dd>
                {$COMMENT_IMG}
            </dd>
	</div>
        {footer_script}
            jQuery(document).ready(function(){
              jQuery(".imageComment").hide();
            });
        {/footer_script}
	{else if $addinfophotos.AIPWORDING == \'ID\'}
	<div id="ImageId" class="imageInfo">
        <dt>{\'Image id\'|@translate}</dt>
        <dd>{$addinfophotos.AIPDATA}</dd>
	</div>
	{else if $addinfophotos.AIPWORDING == \'addedby\'}
	<div id="pab1" class="imageInfo">
		<dt>{\'Photo added by\'|@translate}</dt>
		<dd>{$PAB}</dd>
	</div>
	{else if $addinfophotos.AIPWORDING == \'Download Counter\'}
	<div id="DownloadCounter" class="imageInfo">
		<dt>{\'Downloads\'|@translate}</dt>
		<dd>{$DOWNLOAD_COUNTER}</dd>
	</div>
	{else if $addinfophotos.AIPWORDING == \'ColorPalette\'}
		{$INFO_PALETTE}
	{else if $addinfophotos.AIPWORDING == \'expiry_date\'}
	{if isset($expiry_date)}
	<div id="expd_expiry_date" class="imageInfo">
		<dt>{\'Expiry date\'|@translate}</dt>
		<dd>{\'%s, in %s days\'|@translate:$expiry_date:$expd_days}</dd>
	</div>
	{/if}
	{if isset($expired_on_date)}
	<div id="expd_expired_on_date" class="imageInfo">
		<dt>{\'expired on\'|@translate}</dt>
		<dd>{$expired_on_date}</dd>
	</div>
	{/if}
	{else if $addinfophotos.AIPWORDING == \'Copyrights\'}
	<div id="Copyrights_name" class="imageInfo">
		<dt>{\'Copyright\'|@translate}</dt>
		<dd>
		{if $CR_INFO_NAME}
			<a target="_blanc" href="{$CR_INFO_URL}" title="{$CR_INFO_NAME}: {$CR_INFO_DESCR}">{$CR_INFO_NAME}</a>
		{else}
			{\'N/A\'|@translate}
		{/if}
		</dd>
	</div>	
    {else if $addinfophotos.AIPDATA}
        <div id="add_info" class="imageInfo">
          <dt class="label">{$addinfophotos.AIPWORDING}</dt>
          <dd class="value">{$addinfophotos.AIPDATA}</dd>
        </div>
    {/if}
{/foreach}
{/strip}
</dl>
{if isset($metadata)}';

	$repladarkroomcards='
  <div id="infopanel-left" class="col-lg-6 col-12">
      <!-- Picture infos -->
      <div id="card-informations" class="card mb-2">
        <div class="card-body">
          <h5 class="card-title">{\'Information\'|@translate}</h5>
          <div id="info-content" class="d-flex flex-column">
{foreach from=$add_info_photos item=addinfophotos}
    {if $addinfophotos.AIPID == 1 and isset($INFO_AUTHOR)}
    <div id="Author" class="imageInfo">
		<dl class="row mb-0">
            <dt class="col-sm-5">{\'Author\'|@translate}</dt>
			<dd class="col-sm-7">{$INFO_AUTHOR}</dd>
        </dl>
	</div>
    {else if $addinfophotos.AIPID == 2 and isset($INFO_CREATION_DATE)}
    <div id="datecreate" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Created on\'|@translate}</dt>
			<dd class="col-sm-7">{$INFO_CREATION_DATE}</dd>
        </dl>
	</div>
    {else if $addinfophotos.AIPID == 3 and isset($INFO_POSTED_DATE)}
	<div id="datepost" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Posted on\'|@translate}</dt>
			<dd class="col-sm-7">{$INFO_POSTED_DATE}</dd>
		</dl>
	</div>
    {else if $addinfophotos.AIPID == 4 and isset($INFO_DIMENSIONS)}
	<div id="Dimensions" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Dimensions\'|@translate}</dt>
			<dd class="col-sm-7">{$INFO_DIMENSIONS}</dd>
		</dl>
	</div>
    {else if $addinfophotos.AIPID == 5}
	<div id="File" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'File\'|@translate}</dt>
			<dd class="col-sm-7">{$INFO_FILE}</dd>
		</dl>
	</div>
    {else if $addinfophotos.AIPID == 6 and isset($INFO_FILESIZE)}
	<div id="Filesize" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Filesize\'|@translate}</dt>
			<dd class="col-sm-7">{$INFO_FILESIZE}</dd>
        </dl>
	</div>
    {else if $addinfophotos.AIPID == 7 and isset($related_tags)}
		{if $TAGAFF == 0}		
    <div id="Tags" class="imageInfo">
        <dl class="row mb-0">
			<dt class="col-sm-5">{\'Tags\'|@translate}</dt>
			<dd class="col-sm-7">
				{foreach from=$related_tags item=tag name=tag_loop}{if !$smarty.foreach.tag_loop.first}, {/if}<a href="{$tag.URL}">{$tag.name}</a>{/foreach}
				</dd>
		</dl>
	</div>
	    {/if}
		{if $TAGAFF == 1}
 		 </div>
        </div>
      </div>
     <div id="card-tags" class="card mb-2">
        <div class="card-body">
          <h5 class="card-title">{\'Tags\'|@translate}</h5>
            <div id="Tags" class="imageInfo">
              {foreach from=$related_tags item=tag name=tag_loop}<a class="btn btn-primary btn-raised mr-1" href="{$tag.URL}">{$tag.name}</a>{/foreach}
            </div>
        </div>
      </div>
		{/if}
    {else if $addinfophotos.AIPID == 8 and isset($related_categories)}
	<div id="Categories" class="imageInfo">
        <dl class="row mb-0">
            <dt class="col-sm-5">{\'Albums\'|@translate}</dt>
            <dd class="col-sm-7">
                {foreach from=$related_categories item=cat name=cat_loop}
                {if !$smarty.foreach.cat_loop.first}<br />{/if}{$cat}
                {/foreach}
            </dd>
		</dl>
	</div>
    {else if $addinfophotos.AIPID == 9}
	<div id="Visits" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Visits\'|@translate}</dt>
			<dd class="col-sm-7">{$INFO_VISITS}</dd>
		</dl>
	</div>
    {else if $addinfophotos.AIPID == 10 and isset($rate_summary)}
	<div id="Average" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Rating score\'|@translate}</dt>
			<dd class="col-sm-7">
			  {if $rate_summary.count}
				<span id="ratingScore">{$rate_summary.score}</span> <span id="ratingCount">({$rate_summary.count|@translate_dec:\'%d rate\':\'%d rates\'})</span>
			  {else}
				<span id="ratingScore">{\'no rate\'|@translate}</span> <span id="ratingCount"></span>
			  {/if}
			</dd>
		</dl>
	</div>
	{if isset($rating)}
		<div id="rating" class="imageInfo">
			<dl class="row mb-0">
                <dt class="col-sm-5" id="updateRate">{if isset($rating.USER_RATE)}{\'Update your rating\'|@translate}{else}{\'Rate this photo\'|@translate}{/if}</dt>
                <dd class="col-sm-7">
                  <form action="{$rating.F_ACTION}" method="post" id="rateForm" style="margin:0;">
                    <div>
                      {foreach from=$rating.marks item=mark name=rate_loop}
                      {if isset($rating.USER_RATE) && $mark==$rating.USER_RATE}
                      <span class="rateButtonStarFull" data-value="{$mark}"></span>
                      {else}
                      <span class="rateButtonStarEmpty" data-value="{$mark}"></span>
                      {/if}
                      {/foreach}
                      {strip}{combine_script id=\'core.scripts\' path=\'themes/default/js/scripts.js\' load=\'async\'}
                      {combine_script id=\'rating\' require=\'core.scripts\' path=\'themes/bootstrap_darkroom/js/rating.js\' load=\'async\'}
                      {footer_script require=\'jquery\'}
                           var _pwgRatingAutoQueue = _pwgRatingAutoQueue||[];
                           _pwgRatingAutoQueue.push( {ldelim}rootUrl: \'{$ROOT_URL}\', image_id: {$current.id},
                                    onSuccess : function(rating) {ldelim}
                                           var e = document.getElementById("updateRate");
                                           if (e) e.innerHTML = "{\'Update your rating\'|@translate|@escape:\'javascript\'}";
                                           e = document.getElementById("ratingScore");
                                           if (e) e.innerHTML = rating.score;
                                           e = document.getElementById("ratingCount");
                                           if (e) {ldelim}
                                                   if (rating.count == 1) {ldelim}
                                                           e.innerHTML = "({\'%d rate\'|@translate|@escape:\'javascript\'})".replace( "%d", rating.count);
                                                   {rdelim} else {ldelim}
                                                           e.innerHTML = "({\'%d rates\'|@translate|@escape:\'javascript\'})".replace( "%d", rating.count);
                                                   {rdelim}
                                           {rdelim}
                                           $(\'#averageRate\').find(\'span\').each(function() {ldelim}
                                                   $(this).addClass(rating.average > $(this).data(\'value\') - 0.5 ? \'rateButtonStarFull\' : \'rateButtonStarEmpty\');
                                                   $(this).removeClass(rating.average > $(this).data(\'value\') - 0.5 ? \'rateButtonStarEmpty\' : \'rateButtonStarFull\');
                                           {rdelim});
                                   {rdelim}
                           {rdelim});
                      {/footer_script}
                      {/strip}
                    </div>
                  </form>
                </dd>
			</dl>
		</div>
	{/if}
    {else if $addinfophotos.AIPID == 11 and $display_info.privacy_level and isset($available_permission_levels)}
	<div id="Privacy" class="imageInfo">
		<dl class="row mb-0">
                <dt class="col-sm-5">{\'Who can see this photo?\'|@translate}</dt>
		<dd class="col-sm-7">
			<div>
				<a id="privacyLevelLink" href>{$available_permission_levels[$current.level]}</a>
			</div>
{combine_script id=\'core.scripts\' load=\'async\' path=\'themes/default/js/scripts.js\'}
{footer_script require=\'jquery\'}{strip}
function setPrivacyLevel(id, level){
(new PwgWS(\'{$ROOT_URL}\')).callService(
	"pwg.images.setPrivacyLevel", { image_id:id, level:level},
	{
		method: "POST",
		onFailure: function(num, text) { alert(num + " " + text); },
		onSuccess: function(result) {
			  jQuery(\'#privacyLevelBox .switchCheck\').css(\'visibility\',\'hidden\');
				jQuery(\'#switchLevel\'+level).prev(\'.switchCheck\').css(\'visibility\',\'visible\');
				jQuery(\'#privacyLevelLink\').text(jQuery(\'#switchLevel\'+level).text());
		}
	}
	);
}
(SwitchBox=window.SwitchBox||[]).push("#privacyLevelLink", "#privacyLevelBox");
{/strip}{/footer_script}
			<div id="privacyLevelBox" class="switchBox" style="display:none">
				{foreach from=$available_permission_levels item=label key=level}
					<span class="switchCheck"{if $level != $current.level} style="visibility:hidden"{/if}>&#x2714; </span>
					<a id="switchLevel{$level}" href="javascript:setPrivacyLevel({$current.id},{$level})">{$label}</a><br>
				{/foreach}
			</div>
		</dd>
              </dl>
	</div>
    {else if $addinfophotos.AIPWORDING == \'Description\' and isset($COMMENT_IMG)}
	<div id="Description" class="imageInfo">
            <dl class="row mb-0">
                <dt class="col-sm-5">{\'Description\'|@translate}</dt>
            <dd class="col-sm-7">
                {$COMMENT_IMG}
            </dd>
        </dl>
	</div>
        {footer_script}
            jQuery(document).ready(function(){
              jQuery(".imageComment").hide();
            });
        {/footer_script}
	{else if $addinfophotos.AIPWORDING == \'ID\'}
	<div id="ImageId" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Image id\'|@translate}</dt>
			<dd class="col-sm-7">{$addinfophotos.AIPDATA}</dd>
		</dl>
	</div>
	{else if $addinfophotos.AIPWORDING == \'addedby\'}
	<div id="pab1" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Photo added by\'|@translate}</dt>
			<dd class="col-sm-7">{$PAB}</dd>
		</dl>
	</div>
	{else if $addinfophotos.AIPWORDING == \'Download Counter\'}
	<div id="DownloadCounter" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Downloads\'|@translate}</dt>
			<dd class="col-sm-7">{$DOWNLOAD_COUNTER}</dd>
		</dl>
	</div>
	{else if $addinfophotos.AIPWORDING == \'ColorPalette\'}
	{strip}
	{combine_css id="colorpalette.paletteinfo_css" path=$COLOR_PALETTE_PATH|cat:"template/palette_info.css"}
	{combine_script id="colorpalette.paletteinfo_js" require="jquery" load="async" path=$COLOR_PALETTE_PATH|cat:"template/palette_info.js"}
	{footer_script}
		var paletteUrl = \'{$palette_url}\';
	{/footer_script}
	<div id="color_palette" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Palette\'|@translate}</dt>
			<dd class="col-sm-7" id="palette_colors">
				{foreach
				  from=$palette_colors item=color name=color_loop}
					<div class="color_palette_item"
						 style="background-color: #{$color.hex};"
						 data-color="{$color.rgb}"
						 onclick="paletteItemClick(this);"
						 title="#{$color.hex}"></div>
				{/foreach}
				&nbsp;<a id="palette_search" href="#" style="display: none;">{\'Search\'|@translate}</a>
				<div style="clear: both"/>
			</dd>
		</dl>
	</div>
	{/strip}
	{else if $addinfophotos.AIPWORDING == \'expiry_date\'}
	{if isset($expiry_date)}
	<div id="expd_expiry_date" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Expiry date\'|@translate}</dt>
			<dd class="col-sm-7">{\'%s, in %s days\'|@translate:$expiry_date:$expd_days}</dd>
		</dl>
	</div>
	{/if}
	{if isset($expired_on_date)}
	<div id="expd_expired_on_date" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'expired on\'|@translate}</dt>
			<dd class="col-sm-7">{$expired_on_date}</dd>
		</dl>
	</div>
	{/if}
	{else if $addinfophotos.AIPWORDING == \'Copyrights\'}
	<div id="Copyrights_name" class="imageInfo">
		<dl class="row mb-0">
			<dt class="col-sm-5">{\'Copyright\'|@translate}</dt>
			<dd class="col-sm-7">
			{if $CR_INFO_NAME}
				<a target="_blanc" href="{$CR_INFO_URL}" title="{$CR_INFO_NAME}: {$CR_INFO_DESCR}">{$CR_INFO_NAME}</a>
			{else}
				{\'N/A\'|@translate}
			{/if}
			</dd>
		</dl>
	</div>	
    {else if $addinfophotos.AIPDATA}
        <div id="add_info" class="imageInfo">
		<dl class="row mb-0">
          <dt class="label col-sm-5">{$addinfophotos.AIPWORDING}</dt>
          <dd class="value col-sm-7">{$addinfophotos.AIPDATA}</dd>
              </dl>
        </div>
    {/if}
{/foreach}
	{if $TAGAFF == 0}
      </div></div></div>
	{/if}  
	  </div>
  {if isset($metadata) || (isset($comment_add) || $COMMENT_COUNT > 0)}
    <div id="infopanel-right" class="col-lg-6 col-12">
    <!-- metadata -->
{if isset($metadata)}
{if isset($loaded_plugins[\'exif_view\'])}
{assign var="exif_make" value="{\'exif_field_Make\'|@translate}"}
{assign var="exif_model" value="{\'exif_field_Model\'|@translate}"}
{assign var="exif_lens" value="{\'exif_field_UndefinedTag:0xA434\'|@translate}"}
{assign var="exif_fnumber" value="{\'exif_field_FNumber\'|@translate}"}
{assign var="exif_iso" value="{\'exif_field_ISOSpeedRatings\'|@translate}"}
{assign var="exif_focal_length" value="{\'exif_field_FocalLength\'|@translate}"}
{assign var="exif_flash" value="{\'exif_field_Flash\'|@translate}"}
{assign var="exif_exposure_time" value="{\'exif_field_ExposureTime\'|@translate}"}
{assign var="exif_exposure_bias" value="{\'exif_field_ExposureBiasValue\'|@translate}"}
{else}
{assign var="exif_make" value="Make"}
{assign var="exif_model" value="Model"}
{assign var="exif_lens" value="UndefinedTag:0xA434"}
{assign var="exif_fnumber" value="FNumber"}
{assign var="exif_iso" value="ISOSpeedRatings"}
{assign var="exif_focal_length" value="FocalLength"}
{assign var="exif_flash" value="Flash"}
{assign var="exif_exposure_time" value="ExposureTime"}
{assign var="exif_exposure_bias" value="ExposureBiasValue"}
{/if}

      <div id="card-metadata" class="card mb-2">
        <div class="card-body">
          <h5 class="card-title">{\'EXIF Metadata\'|@translate}</h5>
          <div id="metadata">
            {if is_array($metadata.0.lines) && (array_key_exists("{$exif_make}", $metadata.0.lines) || array_key_exists("{$exif_model}", $metadata.0.lines))}
            <div class="row" style="line-height: 40px">
              <div class="col-12">
                <span class="camera-compact fa-3x mr-3" title="{$exif_make} &amp; {$exif_model}"></span>
                {if is_array($metadata.0.lines) && (array_key_exists("{$exif_make}", $metadata.0.lines))}{$metadata.0.lines[{$exif_make}]}{/if}
                {if is_array($metadata.0.lines) && (array_key_exists("{$exif_model}", $metadata.0.lines))}{$metadata.0.lines[{$exif_model}]}{/if}
              </div>
            </div>
            {/if}
            {if is_array($metadata.0.lines) && (array_key_exists("{$exif_lens}", $metadata.0.lines))}
            <div class="row" style="line-height: 40px">
              <div class="col-12">
                <span class="camera-lens-h fa-3x mr-3" title="{$exif_lens}"></span>
                    {$metadata.0.lines[{$exif_lens}]}
              </div>
            </div>
            {/if}
            <div class="row">
              <div class="col-12{if $theme_config->fluid_width} col-xl-10{/if}">
                <div class="row">
                  {if is_array($metadata.0.lines) && (array_key_exists("{$exif_fnumber}", $metadata.0.lines))}
                  <div class="col-6 col-sm-4">
                    <span class="camera-aperture fa-2x pr-2" title="{$exif_fnumber}"></span> f/{$metadata.0.lines[{$exif_fnumber}]}
                  </div>
                  {/if}
                  {if is_array($metadata.0.lines) && (array_key_exists("{$exif_focal_length}", $metadata.0.lines))}
                  <div class="col-6 col-sm-4">
                    <span class="camera-focal-length fa-2x pr-2" title="{$exif_focal_length}"></span> {$metadata.0.lines[{$exif_focal_length}]}
                  </div>
                  {/if}
                  {if is_array($metadata.0.lines) && (array_key_exists("{$exif_exposure_time}", $metadata.0.lines))}
                  <div class="col-6 col-sm-4">
                    <span class="camera-shutter-speed fa-2x pr-2" title="{$exif_exposure_time}"></span> {$metadata.0.lines[{$exif_exposure_time}]}
                  </div>
                  {/if}
                  {if is_array($metadata.0.lines) && (array_key_exists("{$exif_iso}", $metadata.0.lines))}
                  <div class="col-6 col-sm-4">
                    <span class="camera-iso fa-2x pr-2" title="{$exif_iso}"></span> {$metadata.0.lines[{$exif_iso}]}
                  </div>
                  {/if}
                  {if is_array($metadata.0.lines) && (array_key_exists("{$exif_exposure_bias}", $metadata.0.lines))}
                  <div class="col-6 col-sm-4">
                    <span class="camera-exposure fa-2x pr-2" title="{$exif_exposure_bias}"></span> {$metadata.0.lines[{$exif_exposure_bias}]}
                  </div>
                  {/if}
                  {if is_array($metadata.0.lines) && (array_key_exists("{$exif_flash}", $metadata.0.lines))}
                  <div class="col-6 col-sm-4">
                    <span class="camera-flash fa-2x pr-2 float-left h-100" title="{$exif_flash}"></span><div> {$metadata.0.lines[{$exif_flash}]}</div>
                  </div>
                  {/if}
                </div>
              </div>
            </div>
          </div>
          <button id="show_exif_data" class="btn btn-primary btn-raised mt-1" style="text-transform: none;"><i class="fas fa-info mr-1"></i> {\'Show EXIF data\'|@translate}</button>
{footer_script require=\'jquery\'}
$(\'#show_exif_data\').on(\'click\', function() {
  if ($(\'#full_exif_data\').hasClass(\'d-none\')) {
    $(\'#full_exif_data\').addClass(\'d-flex\').removeClass(\'d-none\');
    $(\'#show_exif_data\').html(\'<i class="fas fa-info mr-1"></i> {"Hide EXIF data"|@translate}\');
  } else {
    $(\'#full_exif_data\').addClass(\'d-none\').removeClass(\'d-flex\');
    $(\'#show_exif_data\').html(\'<i class="fas fa-info mr-1"></i> {"Show EXIF data"|@translate}\');
  }
});
{/footer_script}
          <div id="full_exif_data" class="d-none flex-column mt-2">
{foreach from=$metadata item=meta}
{foreach from=$meta.lines item=value key=label}
            <div>
              <dl class="row mb-0">
                <dt class="col-sm-6">{$label}</dt>
                <dd class="col-sm-6">{$value}</td>
                  </tr>
              </dl>
            </div>
{/foreach}
{/foreach}
          </div>
        </div>
      </div>
{/if}
      <div id="card-comments" class="ml-2">
        {include file=\'picture_info_comments.tpl\'}
      </div>
    </div>
{/if}
  ';
  
 $repladarkroomtabs='
     <div id="infopanel" class="col-lg-8 col-md-10 col-12 mx-auto">
      <!-- Nav tabs -->
      <ul class="nav nav-tabs nav-justified flex-column flex-sm-row" role="tablist">
{if $theme_config->picture_info == \'tabs\' || (get_device() != \'desktop\' && $theme_config->picture_info != \'disabled\')}
        <li class="nav-item"><a class="flex-sm-fill text-sm-center nav-link active" href="#tab_info" aria-controls="tab_info" role="tab" data-toggle="tab">{\'Information\'|@translate}</a></li>
{if isset($metadata)}
        <li class="nav-item"><a class="flex-sm-fill text-sm-center nav-link" href="#tab_metadata" aria-controls="tab_metadata" role="tab" data-toggle="tab">{\'EXIF Metadata\'|@translate}</a></li>
{/if}
{/if}
{if isset($comment_add) || $COMMENT_COUNT > 0}
        <li class="nav-item{if $theme_config->picture_info == \'disabled\' || ($theme_config->picture_info != \'tabs\' && get_device() == \'desktop\')} active{/if}"><a class="flex-sm-fill text-sm-center nav-link" href="#tab_comments" aria-controls="tab_comments" role="tab" data-toggle="tab">{\'Comments\'|@translate} <span class="badge badge-secondary">{$COMMENT_COUNT}</span></a></li>
{/if}
      </ul>

      <!-- Tab panes -->
      <div class="tab-content d-flex justify-content-center">
{if $theme_config->picture_info === \'tabs\' || (get_device() != \'desktop\' && $theme_config->picture_info != \'disabled\')}
        <div role="tabpanel" class="tab-pane active" id="tab_info">
          <div id="info-content" class="info">
            <div class="table-responsive">
              <table class="table table-sm">
                <colgroup>
                  <col class="w-50">
                  <col class="w-50">
                </colgroup>
                <tbody>
 <!--mpp -->
 {foreach from=$add_info_photos item=addinfophotos}
    {if $addinfophotos.AIPID == 1 and isset($INFO_AUTHOR)}
    <div id="Author" class="imageInfo">
		<tr>
            <th scope="row">{\'Author\'|@translate}</th>
			<td>{$INFO_AUTHOR}</td>
        </tr>
	</div>
    {else if $addinfophotos.AIPID == 2 and isset($INFO_CREATION_DATE)}
    <div id="datecreate" class="imageInfo">
		<tr>
            <th scope="row">{\'Created on\'|@translate}</th>
			<td>{$INFO_CREATION_DATE}</td>
        </tr>
	</div>
    {else if $addinfophotos.AIPID == 3 and isset($INFO_POSTED_DATE)}
	<div id="datepost" class="imageInfo">
		<tr>
            <th scope="row">{\'Posted on\'|@translate}</th>
			<td>{$INFO_POSTED_DATE}</td>
        </tr>
	</div>
    {else if $addinfophotos.AIPID == 4 and isset($INFO_DIMENSIONS)}
	<div id="Dimensions" class="imageInfo">
		<tr>
			<th scope="row">{\'Dimensions\'|@translate}</th>
			<td>{$INFO_DIMENSIONS}</td>
        </tr>
	</div>
    {else if $addinfophotos.AIPID == 5}
	<div id="File" class="imageInfo">
		<tr>
			<th scope="row">{\'File\'|@translate}</th>
			<td>{$INFO_FILE}</td>
        </tr>
	</div>
    {else if $addinfophotos.AIPID == 6 and isset($INFO_FILESIZE)}
	<div id="Filesize" class="imageInfo">
		<tr>
            <th scope="row">{\'Filesize\'|@translate}</th>
			<td>{$INFO_FILESIZE}</td>
        </tr>
	</div>
    {else if $addinfophotos.AIPID == 7 and isset($related_tags)}
    	<div id="Tags" class="imageInfo">
        <tr>
			<th scope="row">{\'Tags\'|@translate}</th>
			<td>
		{foreach from=$related_tags item=tag name=tag_loop}{if !$smarty.foreach.tag_loop.first}, {/if}<a href="{$tag.URL}">{$tag.name}</a>{/foreach}
			</td>
        </tr>
	</div>
    {else if $addinfophotos.AIPID == 8 and isset($related_categories)}
	<div id="Categories" class="imageInfo">
        <tr>
            <th scope="row">{\'Albums\'|@translate}</th>
            <td>
				{foreach from=$related_categories item=cat name=cat_loop}
					{if !$smarty.foreach.cat_loop.first}<br />{/if}{$cat}
				{/foreach}
            </td>
        </tr>
	</div>
    {else if $addinfophotos.AIPID == 9}
	<div id="Visits" class="imageInfo">
		<tr>
            <th scope="row">{\'Visits\'|@translate}</th>
			<td>{$INFO_VISITS}</td>
        </tr>
	</div>
    {else if $addinfophotos.AIPID == 10 and isset($rate_summary)}
	<div id="Average" class="imageInfo">
		<tr>
            <th scope="row">{\'Rating score\'|@translate}</th>
			<td>
		{if $rate_summary.count}
			<span id="ratingScore">{$rate_summary.score}</span> <span id="ratingCount">({$rate_summary.count|@translate_dec:\'%d rate\':\'%d rates\'})</span>
		{else}
			<span id="ratingScore">{\'no rate\'|@translate}</span> <span id="ratingCount"></span>
		{/if}
			</td>
        </tr>
	</div>
        {if isset($rating)}
	<div id="rating" class="imageInfo">
		<tr>
			<th scope="row" id="updateRate">{if isset($rating.USER_RATE)}{\'Update your rating\'|@translate}{else}{\'Rate this photo\'|@translate}{/if}</th>
			<td>
			  <div id="rating" class="imageInfo">
			  <form action="{$rating.F_ACTION}" method="post" id="rateForm" style="margin:0;">
				<div>
				{foreach from=$rating.marks item=mark name=rate_loop}
					{if isset($rating.USER_RATE) && $mark==$rating.USER_RATE}
						<span class="rateButtonStarFull" data-value="{$mark}"></span>
					{else}
						<span class="rateButtonStarEmpty" data-value="{$mark}"></span>
					{/if}
				{/foreach}
				{strip}{combine_script id=\'core.scripts\' path=\'themes/default/js/scripts.js\' load=\'async\'}
				{combine_script id=\'rating\' require=\'core.scripts\' path=\'themes/bootstrap_darkroom/js/rating.js\' load=\'async\'}
				{footer_script require=\'jquery\'}
					var _pwgRatingAutoQueue = _pwgRatingAutoQueue||[];
					_pwgRatingAutoQueue.push( {ldelim}rootUrl: \'{$ROOT_URL}\', image_id: {$current.id},
					onSuccess : function(rating) {ldelim}
					var e = document.getElementById("updateRate");
					if (e) e.innerHTML = "{\'Update your rating\'|@translate|@escape:\'javascript\'}";
					e = document.getElementById("ratingScore");
					if (e) e.innerHTML = rating.score;
					e = document.getElementById("ratingCount");
					if (e) {ldelim}
						if (rating.count == 1) {ldelim}
							e.innerHTML = "({\'%d rate\'|@translate|@escape:\'javascript\'})".replace( "%d", rating.count);
							{rdelim} else {ldelim}
								e.innerHTML = "({\'%d rates\'|@translate|@escape:\'javascript\'})".replace( "%d", rating.count);
							{rdelim}
							{rdelim}
								$(\'#averageRate\').find(\'span\').each(function() {ldelim}
								$(this).addClass(rating.average > $(this).data(\'value\') - 0.5 ? \'rateButtonStarFull\' : \'rateButtonStarEmpty\');
								$(this).removeClass(rating.average > $(this).data(\'value\') - 0.5 ? \'rateButtonStarEmpty\' : \'rateButtonStarFull\');
							{rdelim});
							{rdelim}
							{rdelim});
				{/footer_script}
				{/strip}
			  </div>
			  </form>
			</div>
			</td>
		</tr>
	</div>
        {/if}
    {else if $addinfophotos.AIPID == 11 and $display_info.privacy_level and isset($available_permission_levels)}
	<div id="Privacy" class="imageInfo">
		<tr>
			<th scope="row">{\'Who can see this photo?\'|@translate}</th>
			<td>
			<div>
				<a id="privacyLevelLink" href>{$available_permission_levels[$current.level]}</a>
			</div>
{combine_script id=\'core.scripts\' load=\'async\' path=\'themes/default/js/scripts.js\'}
{footer_script require=\'jquery\'}{strip}
function setPrivacyLevel(id, level){
(new PwgWS(\'{$ROOT_URL}\')).callService(
	"pwg.images.setPrivacyLevel", { image_id:id, level:level},
	{
		method: "POST",
		onFailure: function(num, text) { alert(num + " " + text); },
		onSuccess: function(result) {
			  jQuery(\'#privacyLevelBox .switchCheck\').css(\'visibility\',\'hidden\');
				jQuery(\'#switchLevel\'+level).prev(\'.switchCheck\').css(\'visibility\',\'visible\');
				jQuery(\'#privacyLevelLink\').text(jQuery(\'#switchLevel\'+level).text());
		}
	}
	);
}
(SwitchBox=window.SwitchBox||[]).push("#privacyLevelLink", "#privacyLevelBox");
{/strip}{/footer_script}
			<div id="privacyLevelBox" class="switchBox" style="display:none">
				{foreach from=$available_permission_levels item=label key=level}
					<span class="switchCheck"{if $level != $current.level} style="visibility:hidden"{/if}>&#x2714; </span>
					<a id="switchLevel{$level}" href="javascript:setPrivacyLevel({$current.id},{$level})">{$label}</a><br>
				{/foreach}
			</div>
			</td>
        </tr>
	</div>
    {else if $addinfophotos.AIPWORDING == \'Description\' and isset($COMMENT_IMG)}
	<div id="Description" class="imageInfo">
        <tr>
			<th scope="row">{\'Description\'|@translate}</th>
            <td>
                {$COMMENT_IMG}
            </td>
        </tr>
	</div>
        {footer_script}
            jQuery(document).ready(function(){
              jQuery(".imageComment").hide();
            });
        {/footer_script}
	{else if $addinfophotos.AIPWORDING == \'ID\'}
	<div id="ImageId" class="imageInfo">
		<tr>
			<th scope="row">{\'Image id\'|@translate}</th>
			<td>{$addinfophotos.AIPDATA}</td>
		</tr>
	</div>
	{else if $addinfophotos.AIPWORDING == \'addedby\'}
	<div id="pab1" class="imageInfo">
		<tr>
			<th scope="row">{\'Photo added by\'|@translate}</th>
			<td>{$PAB}</td>
		</tr>
	</div>
	{else if $addinfophotos.AIPWORDING == \'Download Counter\'}
	<div id="DownloadCounter" class="imageInfo">
		<tr>
			<th scope="row">{\'Downloads\'|@translate}</th>
			<td>{$DOWNLOAD_COUNTER}</td>
		</tr>
	</div>
	{else if $addinfophotos.AIPWORDING == \'ColorPalette\'}
	{strip}
	{combine_css id="colorpalette.paletteinfo_css" path=$COLOR_PALETTE_PATH|cat:"template/palette_info.css"}
	{combine_script id="colorpalette.paletteinfo_js" require="jquery" load="async" path=$COLOR_PALETTE_PATH|cat:"template/palette_info.js"}
	{footer_script}
	var paletteUrl = \'{$palette_url}\';
	{/footer_script}
	<div id="color_palette" class="imageInfo">
		<tr>
			<th scope="row">{\'Palette\'|@translate}</th>
			<td id="palette_colors">
				{foreach
				  from=$palette_colors item=color name=color_loop}
					<div class="color_palette_item"
						 style="background-color: #{$color.hex};"
						 data-color="{$color.rgb}"
						 onclick="paletteItemClick(this);"
						 title="#{$color.hex}"></div>
				{/foreach}
				&nbsp;<a id="palette_search" href="#" style="display: none;">{\'Search\'|@translate}</a>
				<div style="clear: both"/>
			</td>
</div>
{/strip}
	{else if $addinfophotos.AIPWORDING == \'expiry_date\'}
	{if isset($expiry_date)}
	<div id="expd_expiry_date" class="imageInfo">
		<tr>
			<th scope="row">{\'Expiry date\'|@translate}</th>
			<td>{\'%s, in %s days\'|@translate:$expiry_date:$expd_days}</td>
		</tr>
	</div>
	{/if}
	{if isset($expired_on_date)}
	<div id="expd_expired_on_date" class="imageInfo">
		<tr>
			<th scope="row">{\'expired on\'|@translate}</th>
			<td>{$expired_on_date}</td>
		</tr>
	</div>
	{/if}
	{else if $addinfophotos.AIPWORDING == \'Copyrights\'}
	<div id="Copyrights_name" class="imageInfo">
		<tr>
			<th>{\'Copyright\'|@translate}</th>
			<td>
			{if $CR_INFO_NAME}
				<a target="_blanc" href="{$CR_INFO_URL}" title="{$CR_INFO_NAME}: {$CR_INFO_DESCR}">{$CR_INFO_NAME}</a>
			{else}
				{\'N/A\'|@translate}
			{/if}
			</td>
		</tr>
	</div>
    {else if $addinfophotos.AIPDATA}
	<div id="add_info" class="imageInfo">
		<tr>
			<th scope="row label">{$addinfophotos.AIPWORDING}</th>
			<td class="value">{$addinfophotos.AIPDATA}</td>
		</tr>
	</div>
    {/if}
{/foreach}
 <!--and mpp -->
                 </tbody>
              </table>
            </div>
          </div>
        </div>
         <!-- metadata -->
{if isset($metadata)}
        <div role="tabpanel" class="tab-pane" id="tab_metadata">
          <div id="metadata" class="info">
            <div class="table-responsive">
              <table class="table table-sm">
                <colgroup>
                  <col class="w-50">
                  <col class="w-50">
                </colgroup>
                <tbody>
{foreach from=$metadata item=meta}
{foreach from=$meta.lines item=value key=label}
                  <tr>
                    <th scope="row">{$label}</th>
                    <td>{$value}</td>
                  </tr>
{/foreach}
{/foreach}
                </tbody>
              </table>
            </div>
          </div>
        </div>
{/if}
{/if}

        <!-- comments -->
{if isset($comment_add) || $COMMENT_COUNT > 0}
        <div role="tabpanel" class="tab-pane" id="tab_comments">
          {include file=\'picture_info_comments.tpl\'}
        </div>
{/if}
      </div>
    </div>

 ';
 
 $replasmart='
 <ul data-role="listview" data-inset="true" id="PictureInfo">
{strip}
 <!--mpp -->
{foreach from=$add_info_photos item=addinfophotos}
    {if $addinfophotos.AIPID == 1 and isset($INFO_AUTHOR)}
    <li id="Author" class="imageInfo">
		<dt>{\'Author\'|@translate}</dt>
		<dd>{$INFO_AUTHOR}</dd>
	</li>
    {else if $addinfophotos.AIPID == 2 and isset($INFO_CREATION_DATE)}
    <li id="datecreate" class="imageInfo">
		<dt>{\'Created on\'|@translate}</dt>
		<dd>{$INFO_CREATION_DATE}</dd>
	</li>
    {else if $addinfophotos.AIPID == 3 and isset($INFO_POSTED_DATE)}
	<li id="datepost" class="imageInfo">
		<dt>{\'Posted on\'|@translate}</dt>
		<dd>{$INFO_POSTED_DATE}</dd>
	</li>
    {else if $addinfophotos.AIPID == 4 and isset($INFO_DIMENSIONS)}
	<li id="Dimensions" class="imageInfo">
		<dt>{\'Dimensions\'|@translate}</dt>
		<dd>{$INFO_DIMENSIONS}</dd>
	</li>
    {else if $addinfophotos.AIPID == 5}
	<li id="File" class="imageInfo">
		<dt>{\'File\'|@translate}</dt>
		<dd>{$INFO_FILE}</dd>
	</li>
    {else if $addinfophotos.AIPID == 6 and isset($INFO_FILESIZE)}
	<li id="Filesize" class="imageInfo">
		<dt>{\'Filesize\'|@translate}</dt>
		<dd>{$INFO_FILESIZE}</dd>
	</li>
    {else if $addinfophotos.AIPID == 7 and isset($related_tags)}
    	<li id="Tags" class="imageInfo">
        <dt>{\'Tags\'|@translate}</dt>
		<dd>
		{foreach from=$related_tags item=tag name=tag_loop}{if !$smarty.foreach.tag_loop.first}, {/if}<a href="{$tag.URL}">{$tag.name}</a>{/foreach}
		</dd>
	</li>
    {else if $addinfophotos.AIPID == 8 and isset($related_categories)}
	<li id="Categories" class="imageInfo">
            <dt>{\'Albums\'|@translate}</dt>
            <dd>
                <ul>
                    {foreach from=$related_categories item=cat}
                        <li>{$cat}</li>
                    {/foreach}
                </ul>
            </dd>
	</li>
    {else if $addinfophotos.AIPID == 9}
	<li id="Visits" class="imageInfo">
		<dt>{\'Visits\'|@translate}</dt>
		<dd>{$INFO_VISITS}</dd>
	</li>
    {else if $addinfophotos.AIPID == 10 and isset($rate_summary)}
	<li id="Average" class="imageInfo">
		<dt>{\'Rating score\'|@translate}</dt>
		<dd>
		{if $rate_summary.count}
			<span id="ratingScore">{$rate_summary.score}</span> <span id="ratingCount">({$rate_summary.count|@translate_dec:\'%d rate\':\'%d rates\'})</span>
		{else}
			<span id="ratingScore">{\'no rate\'|@translate}</span> <span id="ratingCount"></span>
		{/if}
		</dd>
	</li>
        {if isset($rating)}
	<li id="rating" class="imageInfo">
		<dt>
			<span id="updateRate">{if isset($rating.USER_RATE)}{\'Update your rating\'|@translate}{else}{\'Rate this photo\'|@translate}{/if}</span>
		</dt>
		<dd>
			<form action="{$rating.F_ACTION}" method="post" id="rateForm" style="margin:0;">
			<div>
			{foreach from=$rating.marks item=mark name=rate_loop}
			{if isset($rating.USER_RATE) && $mark==$rating.USER_RATE}
				<input type="button" name="rate" value="{$mark}" class="rateButtonSelected" title="{$mark}">
			{else}
				<input type="submit" name="rate" value="{$mark}" class="rateButton" title="{$mark}">
			{/if}
			{/foreach}
			{strip}{combine_script id=\'core.scripts\' load=\'async\' path=\'themes/default/js/scripts.js\'}
			{combine_script id=\'rating\' load=\'async\' require=\'core.scripts\' path=\'themes/default/js/rating.js\'}
			{footer_script}
				var _pwgRatingAutoQueue = _pwgRatingAutoQueue||[];
				_pwgRatingAutoQueue.push( {ldelim}rootUrl: \'{$ROOT_URL}\', image_id: {$current.id},
					onSuccess : function(rating) {ldelim}
						var e = document.getElementById("updateRate");
						if (e) e.innerHTML = "{\'Update your rating\'|@translate|@escape:\'javascript\'}";
						e = document.getElementById("ratingScore");
						if (e) e.innerHTML = rating.score;
						e = document.getElementById("ratingCount");
						if (e) {ldelim}
							if (rating.count == 1) {ldelim}
								e.innerHTML = "({\'%d rate\'|@translate|@escape:\'javascript\'})".replace( "%d", rating.count);
							} else {ldelim}
								e.innerHTML = "({\'%d rates\'|@translate|@escape:\'javascript\'})".replace( "%d", rating.count);
              }
						{rdelim}
					{rdelim}{rdelim} );
			{/footer_script}
			{/strip}
			</div>
			</form>
		</dd>
	</li>
        {/if}
    {else if $addinfophotos.AIPID == 11 and $display_info.privacy_level and isset($available_permission_levels)}
	<li id="Privacy" class="imageInfo">
		<dt>{\'Who can see this photo?\'|@translate}</dt>
		<dd>
			<div>
				<a id="privacyLevelLink" href>{$available_permission_levels[$current.level]}</a>
			</div>
{combine_script id=\'core.scripts\' load=\'async\' path=\'themes/default/js/scripts.js\'}
{footer_script require=\'jquery\'}{strip}
function setPrivacyLevel(id, level){
(new PwgWS(\'{$ROOT_URL}\')).callService(
	"pwg.images.setPrivacyLevel", { image_id:id, level:level},
	{
		method: "POST",
		onFailure: function(num, text) { alert(num + " " + text); },
		onSuccess: function(result) {
			  jQuery(\'#privacyLevelBox .switchCheck\').css(\'visibility\',\'hidden\');
				jQuery(\'#switchLevel\'+level).prev(\'.switchCheck\').css(\'visibility\',\'visible\');
				jQuery(\'#privacyLevelLink\').text(jQuery(\'#switchLevel\'+level).text());
		}
	}
	);
}
(SwitchBox=window.SwitchBox||[]).push("#privacyLevelLink", "#privacyLevelBox");
{/strip}{/footer_script}
			<div id="privacyLevelBox" class="switchBox" style="display:none">
				{foreach from=$available_permission_levels item=label key=level}
					<span class="switchCheck"{if $level != $current.level} style="visibility:hidden"{/if}>&#x2714; </span>
					<a id="switchLevel{$level}" href="javascript:setPrivacyLevel({$current.id},{$level})">{$label}</a><br>
				{/foreach}
			</div>
		</dd>
	</li>
    {else if $addinfophotos.AIPWORDING == \'Description\' and isset($COMMENT_IMG)}
	<li id="Description" class="imageInfo">
            <dt>{\'Description\'|@translate}</dt>
            <dd>
                {$COMMENT_IMG}
            </dd>
	</li>
        {footer_script}
            jQuery(document).ready(function(){
              jQuery(".imageComment").hide();
            });
        {/footer_script}
	{else if $addinfophotos.AIPWORDING == \'ID\'}
	<li id="ImageId" class="imageInfo">
        <dt>{\'Image id\'|@translate}</dt>
        <dd>{$addinfophotos.AIPDATA}</dd>
	</li>
	{else if $addinfophotos.AIPWORDING == \'addedby\'}
	<li id="pab1" class="imageInfo">
		<dt>{\'Photo added by\'|@translate}</dt>
		<dd>{$PAB}</dd>
	</li>
	{else if $addinfophotos.AIPWORDING == \'Download Counter\'}
	<li id="DownloadCounter" class="imageInfo">
		<dt>{\'Downloads\'|@translate}</dt>
		<dd>{$DOWNLOAD_COUNTER}</dd>
	</li>
	{else if $addinfophotos.AIPWORDING == \'ColorPalette\'}
		{$INFO_PALETTE}
	{else if $addinfophotos.AIPWORDING == \'expiry_date\'}
	{if isset($expiry_date)}
	<li id="expd_expiry_date" class="imageInfo">
		<dt>{\'Expiry date\'|@translate}</dt>
		<dd">{\'%s, in %s days\'|@translate:$expiry_date:$expd_days}</dd>
	</li>
	{/if}
	{if isset($expired_on_date)}
	<li id="expd_expired_on_date" class="imageInfo">
		<dt>{\'expired on\'|@translate}</dt>
		<dd>{$expired_on_date}</dd>
	</li>
	{/if}
	{else if $addinfophotos.AIPWORDING == \'Copyrights\'}
	<li id="Copyrights_name" class="imageInfo">
		<dt>{\'Copyright\'|@translate}</dt>
		<dd>
		{if $CR_INFO_NAME}
			<a target="_blanc" href="{$CR_INFO_URL}" title="{$CR_INFO_NAME}: {$CR_INFO_DESCR}">{$CR_INFO_NAME}</a>
		{else}
			{\'N/A\'|@translate}
		{/if}
		</dd>
	</li>
    {else if $addinfophotos.AIPDATA}
    <li id="add_info" class="imageInfo">
        <dt class="label">{$addinfophotos.AIPWORDING}</dt>
        <dd class="value">{$addinfophotos.AIPDATA}</dd>
    </li>
    {/if}
{/foreach}
 <!--and mpp -->
{if isset($metadata)}
 ';

   $repladarkroom='<div id="theImageInfos" class="row justify-content-center">
{if $theme_config->picture_info == \'cards\'}'
	.$repladarkroomcards.'    
{elseif $theme_config->picture_info == \'tabs\'}'
	.$repladarkroomtabs.'   
{elseif $theme_config->picture_info == \'sidebar\' || $theme_config->picture_info == \'disabled\'}
    <div class="col-lg-8 col-md-10 col-12 mx-auto">
      {include file=\'picture_info_comments.tpl\'} 
    </div>
{/if}
  </div>

{if !empty($PLUGIN_PICTURE_AFTER)}{$PLUGIN_PICTURE_AFTER}{/if}
 ';
  if ($user['theme'] == 'bootstrap_darkroom'){
	  $themeconfig = new \BootstrapDarkroom\Config();
		if($themeconfig->picture_info=='sidebar'){
			$search = '/(<dl id="standard" class="imageInfoTable">).*({if isset\(\$metadata\)})/is';
			return preg_replace($search, $replastandard , $content);
		}else{
			$search = '/(<div id="theImageInfos" class="row justify-content-center">).*({if \!empty\(\$PLUGIN_PICTURE_AFTER\)}{\$PLUGIN_PICTURE_AFTER}{\/if})/is';	
			return preg_replace($search, $repladarkroom , $content);
		}
	}else if ($user['theme'] == 'smartpocket'){
	  $search = '/(<ul data-role="listview" data-inset="true" id="PictureInfo">).*({if isset\(\$metadata\)})/is';
	   return preg_replace($search, $replasmart , $content);
	}else{
	  $search = '/(<dl id="standard" class="imageInfoTable">).*({if isset\(\$metadata\)})/is';
	  return preg_replace($search, $replastandard , $content);
  }
}

add_event_handler('loc_begin_picture', 'add_InfoT');

function add_InfoT() {
    global $conf, $page, $template, $lang, $pwg_loaded_plugins,$user;

    if (!empty($page['image_id'])) {

	  if ($user['theme'] == 'bootstrap_darkroom'){
	  $themeconfig = new \BootstrapDarkroom\Config();
		if($themeconfig->picture_info=='cards'){
			$tagaff = pwg_db_fetch_assoc(pwg_query('SELECT wording FROM '. ADD_PROP_PHOTO_TABLE.' ORDER BY orderprop DESC LIMIT 1'));
				if($tagaff['wording']=='Tags'){
					$template->assign('TAGAFF',1);
				}else{
					$template->assign('TAGAFF',0);
				}
		}
	}


 	  if (isset($pwg_loaded_plugins['ExtendedDescription'])){add_event_handler('AP_render_content', 'get_user_language_desc');}
	  		
        $tab_add_info_one_photo = tab_add_info_by_photo_show();
		
		$query = 'select path FROM ' . IMAGES_TABLE . ' WHERE id = \''.$page['image_id'].'\';';
		$result = pwg_query($query);
		$row = pwg_db_fetch_assoc($result);
		$filename=$row['path'];
		$exif = @exif_read_data($filename);
		$imginfo = array();
		getimagesize($filename, $imginfo);
		if (isset ($imginfo['APP13'])){$iptc = iptcparse($imginfo['APP13']);}
        if (pwg_db_num_rows($tab_add_info_one_photo)) {
            while ($info_photos = pwg_db_fetch_assoc($tab_add_info_one_photo)) {
				if($info_photos['Typ']==2){
					$d = data_info_photosdate($page['image_id'], $info_photos['id_prop_pho']);
                }else{
					$d = data_info_photos($page['image_id'], $info_photos['id_prop_pho']);
				}
				$row = pwg_db_fetch_assoc($d);
                $items = array(
                    'AIPID' => $info_photos['id_prop_pho'],
                    'AIPORDER' => $info_photos['orderprop'],
                    'AIPWORDING' => trigger_change('AP_render_content', $info_photos['wording']),
                );
				if($info_photos['Typ']==2){
				    if(isset($row['datadate'])){ 	
					  $items['AIPDATA']=format_date($row['datadate'],array('day_name', 'day', 'month', 'year','time'));
				    }
					if($info_photos['wording']=="**delpho**"){
						$items['AIPWORDING'] =l10n('Delete photo');
					}
                }else if($info_photos['Typ']==3){
				  if(strpos($info_photos['dataprop'],':')!==false){
				    $exiftab = explode(':', $info_photos['dataprop']);
					if (isset($exif[$exiftab[0]][$exiftab[1]])){
						if (isset($pwg_loaded_plugins['exif_view'])){
							$items['AIPDATA']=exif_key_translation($exif[$exiftab[0]][$exiftab[1]], $exif[$exiftab[0]][$exiftab[1]]);
						}else{
							$items['AIPDATA']= $exif[$exiftab[0]][$exiftab[1]];
						}
						if ($info_photos['wording']!=$info_photos['dataprop']){
						}else if (isset($lang['exif_field_'.$exiftab[1]])){
							$items['AIPWORDING']= $lang['exif_field_'.$exiftab[1]];
						}else{
							$items['AIPWORDING']= $exiftab[1];
						}
					}
				  }else{
					if (isset($exif[$info_photos['dataprop']])){
						if (isset($pwg_loaded_plugins['exif_view'])){
							$items['AIPDATA']=exif_key_translation($info_photos['dataprop'], $exif[$info_photos['dataprop']]);
						}else{
							$items['AIPDATA']= $exif[$info_photos['dataprop']];
						}
						if ($info_photos['wording']!=$info_photos['dataprop']){
						}else if (isset($lang['exif_field_'.$info_photos['dataprop']])){
							$items['AIPWORDING']= $lang['exif_field_'.$info_photos['dataprop']];
						}else{
							$items['AIPWORDING']= $info_photos['dataprop'];
						}
					}
				  }
                }else if($info_photos['Typ']==6){
					$items['AIPWORDING']=trigger_change('AP_render_content', $info_photos['wording']);
					if (isset($iptc[$info_photos['dataprop']])){
					$items['AIPDATA']=implode(", ", $iptc[$info_photos['dataprop']]);
					}
				}else if($info_photos['Typ']==1 AND $info_photos['dataprop']=='showid'){
					$items['AIPDATA']=$page['image_id'];
				}else{
				  if(isset($row['data'])){	
					$items['AIPDATA']=trigger_change('AP_render_content', $row['data']);
				  }else{
					$items['AIPDATA']='';  
				  }
                }
				

                $template->append('add_info_photos', $items);
            }
        }

        $template->assign(
                array(
                    'A' => 'a'
        ));
    }
}

?>