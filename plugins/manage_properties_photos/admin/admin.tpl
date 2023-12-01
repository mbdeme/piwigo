{combine_script id='jquery.ui.sortable' require='jquery.ui' load='footer' path='themes/default/js/ui/minified/jquery.ui.sortable.min.js'}
{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
{include file='include/datepicker.inc.tpl'}

{combine_script id='jquery.chosen' load='footer' path='themes/default/js/plugins/chosen.jquery.min.js'}
{combine_css path="themes/default/js/plugins/chosen.css"}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}


{footer_script}
jQuery(document).ready(function(){

{* <!-- DATEPICKER --> *}
jQuery(function(){ {* <!-- onLoad needed to wait localization loads --> *}
  jQuery('[data-datepicker]').pwgDatepicker({
    showTimepicker: true,
    cancelButton: '{'Cancel'|translate}'
  });
});

  var x = 1;
  var max_fields = $("#hideid").data('maxfield');
  
  jQuery('.eiw').delay(2000).slideUp();
 
  jQuery(".drag_button").show();
  jQuery(".categoryLi").css("cursor","move");
  jQuery(".categoryUl").sortable({
    axis: "y",
    opacity: 0.8,
    update : function() {
      jQuery("#manualOrderInfo").show();
    }
  });

  jQuery("#infoOrdering").submit(function(){
    ar = jQuery('.categoryUl').sortable('toArray');
    for(i=0;i < ar.length ;i++) {
      iord = ar[i].split('iord_');
      document.getElementsByName('infoOrd[' + iord[1] + ']')[0].value = i;
    }
  });

  jQuery("#cancelManualOrderInfo").click(function(){
    jQuery(".categoryUl").sortable("cancel");
    jQuery("#manualOrderInfo").hide();
  });
  
  jQuery('.categoryLi').mouseover(function(){
    jQuery(this).children('span').show();
  });
  jQuery('.categoryLi').mouseout(function(){
    jQuery(this).children('span').hide();
  });
  
  jQuery('#aip_sumit').click(function(){
    jQuery("#add_info_edit").toggle();
    jQuery("#leg_add").show();
    jQuery("#leg_edit").hide();
    jQuery('#aip_add').empty();
    jQuery('#aip_hide').attr('checked', false);
    jQuery('#hideid').val(0);
    jQuery("textarea[name=inserwording]").focus();
	jQuery('input[name=typ]').val(['1']);
	jQuery('#mppselect').hide();
	jQuery('#exifselect').hide();
	jQuery('#iptcselect').hide();

  });
  
  jQuery("#addinfoClose").click(function(){
    jQuery("#add_info_edit").hide();
  });
  
  jQuery('.edit_libinfo').click(function(){
    var id_prop_photo=$(this).data('id');
    var lib=$(this).data('lib');
    var hide=$(this).data('hide');
	var typ=$(this).data('typ');
	var dataprop=$(this).data('dataprop');
		if(typ=='4'){
			jQuery('#exifselect').hide();
			jQuery('#iptcselect').hide();
			jQuery('input[name=typ]').val(['4']);
			jQuery('#mppselect').show();
			jQuery('#mytext5').parent('div').remove();
			jQuery('#mytext4').parent('div').remove();
			jQuery('#mytext3').parent('div').remove();
			jQuery('#mytext2').parent('div').remove();
			x = 1
			for (key in dataprop){
				if(key==0){
					jQuery('#mytext1').val(dataprop[key]);
				}else{
					x++; //text box increment
					$(wrapper).append('<div><input type="text" id="mytext'+x+'" name="mytext[]" value="'+dataprop[key]+'"/><a href="#" class="remove_field"><span class="icon-trash"></span></a></div>');
				}
			}
		}else if(typ=='2'){
			jQuery('#iptcselect').hide();
			jQuery('#exifselect').hide();
			jQuery('input[name=typ]').val(['2']);	
			jQuery('#mppselect').hide();
		}else if(typ=='3'){
			jQuery('#iptcselect').hide();
			jQuery('#exifselect').show();
			jQuery('input[name=typ]').val(['3']);	
			jQuery('#mppselect').hide();
			jQuery('#selectexif').val(dataprop);
		}else if(typ=='6'){
			jQuery('#iptcselect').show();
			jQuery('#exifselect').hide();
			jQuery('input[name=typ]').val(['6']);	
			jQuery('#mppselect').hide();
			jQuery('#selectiptc').val(dataprop);
		}else if(typ=='5'){
			jQuery('#iptcselect').hide();
			jQuery('#exifselect').hide();
			jQuery('input[name=typ]').val(['5']);
			jQuery('#mppselect').show();
			jQuery('#mytext5').parent('div').remove();
			jQuery('#mytext4').parent('div').remove();
			jQuery('#mytext3').parent('div').remove();
			jQuery('#mytext2').parent('div').remove();
			x = 1
			for (key in dataprop){
				if(key==0){
					jQuery('#mytext1').val(dataprop[key]);
				}else{
					x++; //text box increment
					$(wrapper).append('<div><input type="text" id="mytext'+x+'" name="mytext[]" value="'+dataprop[key]+'"/><a href="#" class="remove_field"><span class="icon-trash"></span></a></div>');
				}
			}
		}else{
			jQuery('input[name=typ]').val(['1']);	
			jQuery('#mppselect').hide();
			jQuery('#exifselect').hide();
		}
    jQuery("#add_info_edit").show();
    jQuery("#leg_add").hide();
    jQuery("#leg_edit").show();
    jQuery('#hideid').val(id_prop_photo);
    jQuery('#aip_add').text(lib);
        if(hide==0){
            jQuery('#aip_hide').prop('checked', false);
        }else{
            jQuery('#aip_hide').prop('checked', true);
        }
    jQuery("textarea[name=inserwording]").focus();
  });
  jQuery('.pphide').click(function(){
    var id= $(this).data('id');
    var link= $(this).data('link2');
    $.ajax({
        method: 'POST',
        url: link,
        success: function(Datalc,textStatus,jqXHR) {
          jQuery('#pphide'+id).hide();
          jQuery('#ppshow'+id).show();
          jQuery('#iord_'+id).css("opacity","0.4");
        }
      });
    });
  jQuery('.ppshow').click(function(){
    var id= $(this).data('id');
    var link= $(this).data('link2');
    $.ajax({
        method: 'POST',
        url: link,
        success: function(Datalc,textStatus,jqXHR) {
          jQuery('#pphide'+id).show();
          jQuery('#ppshow'+id).hide();
          jQuery('#iord_'+id).css("opacity","1");
         }
      });
    });
  jQuery('.radtyp').change(function(){
	var typ=$(this).val();
		if(typ==1){
			jQuery('#mppselect').hide();
			jQuery('#exifselect').hide();
			jQuery('#wordingta').show();
			jQuery('#iptcselect').hide();
		}
		if(typ==4||typ==5){
			jQuery('#mppselect').show();
			jQuery('#mytext1').val('');
			jQuery('#mytext5').parent('div').remove();
			jQuery('#mytext4').parent('div').remove();
			jQuery('#mytext3').parent('div').remove();
			jQuery('#mytext2').parent('div').remove();
			x = 1
			jQuery('#exifselect').hide();
			jQuery('#wordingta').show();
			jQuery('#iptcselect').hide();
		}
		if(typ==3){
			jQuery('#iptcselect').hide();
			jQuery('#mppselect').hide();
			jQuery('#exifselect').show();
			var exifval=jQuery('#selectexif').val();
			jQuery('#wordingta').show();
			if(jQuery('#aip_add').val()==''){
				jQuery('#aip_add').text(exifval);
			}
		}
		if(typ==6){
			jQuery('#iptcselect').show();
			jQuery('#mppselect').hide();
			jQuery('#exifselect').hide();
			var iptcval=jQuery('#selectiptc').val();
			jQuery('#wordingta').show();
			if(jQuery('#aip_add').val()==''){
				jQuery('#aip_add').text(iptcval);
			}
		}
		if(typ==2){
			jQuery('#iptcselect').hide();
			jQuery('#mppselect').hide();
			jQuery('#exifselect').hide();
			jQuery('#wordingta').show();
		}
    });
		
		var exifval2=jQuery('#selectexif').val();
  jQuery('#selectexif').change(function(){
		var exifval=jQuery('#selectexif').val();
		if(jQuery('#aip_add').val()==''||jQuery('#aip_add').val()==exifval2){
			jQuery('#aip_add').text(exifval);
		}
		exifval2=exifval
    });
	
    var max_fields = $("#hideid").data('maxfield');
    var wrapper         = $(".input_fields_wrap");
    var add_button      = $(".add_field_button");
   
    $(add_button).click(function(e){
        e.preventDefault();
        if(x < max_fields){
            x++; //text box increment
            $(wrapper).append('<div><input id="mytext'+x+'" type="text" name="mytext[]"/><a href="#" class="remove_field"><span class="icon-trash"></a></div>');
        }
    });
   
    $(wrapper).on("click",".remove_field", function(e){
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })	
});
{/footer_script}
{html_style}
.mouse:hover{
    cursor:pointer;
}
.radtypspan{
	margin-right:40px;
}
#aip_sumit{
    border: 1px solid #D6D6D6;
    color: #5B5B5B;
	padding: 7px 15px;
	font-weight: bold;
	font-size: 11px;
}
#aip_sumit:hover{
    border: 1px solid #aaa;
    text-decoration: none;
}
.showCreatempp{
    text-align: left;
    margin: 0 1em 25px 20px;
    line-height: 22px;
}

li.categoryLi{
  list-style:none;
  background: #ddd;
  padding: 5px;
  margin-bottom: 5px;
  border-radius:5px;
}

{/html_style}


<div class="titrePage">
  <h2>{'Manage properties photos'|@translate}</h2>
</div>
{if isset ($addinfotemplate)}
        <p class="showCreatempp">
            <a href="#" id="aip_sumit" class="icon-plus">{'Create new Property photo'|@translate} </a>
        </p>
    <div id="add_info_edit" style="display: none;">
        <form method="post" >
            <fieldset class="with-border">
                <legend><span id="leg_add">{'Create new Property photo'|@translate}</span><span id="leg_edit">{'Edit Property photo'|@translate}</span></legend>
                <input id="hideid" data-maxfield="{$MPPMAXFIELD}" type="hidden" name="invisibleID" value=""></input>
				<input class="radtyp" id="radtyptex" type="radio" name="typ" value="1"> <span class="radtypspan">{'Text'|@translate}</span></input>
				<input class="radtyp" type="radio" name="typ" value="4"><span class="radtypspan"> {'select'|@translate}</span></input>
				<input class="radtyp" type="radio" name="typ" value="5"><span class="radtypspan"> {'radio'|@translate}</span></input>
				<input class="radtyp" type="radio" name="typ" value="2"><span class="radtypspan"> {'date'|@translate}</span></input>
				<input class="radtyp" type="radio" name="typ" value="3"><span class="radtypspan"> {'exif'|@translate}</span></input>
				<input class="radtyp" type="radio" name="typ" value="6"><span class="radtypspan"> {'IPTC'|@translate}</span></input>
				
				
                <p id="wordingta" class="input">
                    <label for="inserwording"><strong>{'Wording'|@translate}</strong></label><br />
                    <textarea {if $useED==1}placeholder="{'Use Extended Description tags...'|@translate}"{/if} style="margin-left:50px" rows="5" cols="50" class="description" name="inserwording" id="aip_add"></textarea>
                    {if $useED==1}
                    <a href="{$ROOT_URL}admin/popuphelp.php?page=extended_desc" onclick="popuphelp(this.href); return false;" title="{'Use Extended Description tags...'|translate}" style="vertical-align: middle; border: 0; margin: 0.5em;"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/help.png" class="button" alt="{'Use Extended Description tags...'|translate}'"></a>
                    {/if} 
                </p>
                <p class="input" style="width: 700px;">
                    <label for="inseractive">{'Hide'|@translate}</label>
                    <input id="aip_hide" type="checkbox" name="inseractive" value="1">
                </p>
				<div id="mppselect" class="input_fields_wrap">
					<Span class="selectlabel">{'Option List'|@translate}</span><br>
					<button class="add_field_button">{'Add Field'|@translate}</button>
					<div><input type="text" id="mytext1" name="mytext[]"></div>
				</div>
				<div id="exifselect" class="exifselect">
				  {if empty ($rm_exif)}<span style="margin: 0 0 0 20px">{'The reference photo doesn\'t have exif'|@translate}
				  {else}
					<select name="selectexif" id="selectexif">
						<option value="">-----</option>
					  {foreach from=$rm_exif item=rm}
						{if isset($rm.RM_AFF) and $rm.RM_AFF!='1' and $rm.RM_AFF!=''}<optgroup label="{$rm.RM_AFF}"></optgroup>{/if}
						<option value="{if isset ($rm.RM_SECTION) and ($rm.RM_SECTION)!='1'}{$rm.RM_SECTION}:{/if}{$rm.RM_KEY}">{if isset ($rm.RM_SECTION) and ($rm.RM_SECTION)!='1'}&nbsp;&nbsp;&nbsp;{/if}{$rm.RM_KEY}</option>
					  {/foreach}
					</select>
				  {/if}
				</div>
				<div id="iptcselect" class="iptcselect">
				  {if empty ($rm_iptc)}<span style="margin: 0 0 0 20px">{'The reference photo doesn\'t have IPTC'|@translate}
				  {else}
					<select name="selectiptc" id="selectiptc">
						<option value="">-----</option>
					    {foreach from=$rm_iptc item=rm}
						  <option value="{$rm.RM_KEY}">{$rm.RM_KEY}</option>
						{/foreach}
					</select>
				  {/if}
				</div>
				<p class="actionButtons">
					<button name="submitaddAIP" type="submit" class="buttonLike">
						<i class="icon-plus-circled"></i> {'Create'|translate}
					</button>
                    <a href="#" id="addinfoClose" class="icon-cancel-circled">{'Cancel'|@translate}</a>
                </p>
            </fieldset>
        </form>
    </div>
    <form id="infoOrdering" method="post" >
        <p id="manualOrderInfo" style="display:none; text-align: left">
          <input class="submit" name="submitManualOrderInfo" type="submit" value="{'Save order'|@translate}">
          {'... or '|@translate} <a href="#" id="cancelManualOrderInfo">{'cancel manual order'|@translate}</a>
        </p>
	<fieldset>
	<legend>{'Properties List'|@translate}</legend>
          <ul class="categoryUl">
            {foreach from=$info_photos item=infophoto}
              <li {if ($infophoto.AIPACTIVE==0)}style="opacity: 1;"{else}style="opacity: 0.4;"{/if}class="categoryLi{if ($infophoto.AIPEDIT==1)} virtual_cat{/if}" id="iord_{$infophoto.IDINFOPHO}">
                <img src="{$themeconf.admin_icon_dir}/cat_move.png" class="drag_button" style="display:none;" alt="{'Drag to re-order'|@translate}" title="{'Drag to re-order'|@translate}">
                {$infophoto.AIPWORDING}
                <input type="hidden" name="infoOrd[{$infophoto.IDINFOPHO}]" value="{$infophoto.AIPORDER}">
                <br />
                <span class="actiononphoto" style="display: none">
                    <span id="pphide{$infophoto.IDINFOPHO}" {if ($infophoto.AIPACTIVE==1)}style="display: none"{/if}class="graphicalCheckbox icon-check-empty mouse pphide" data-id="{$infophoto.IDINFOPHO}" data-link2="{$infophoto.U_HIDE}">{'Hide'|@translate}</span>
                    <span id="ppshow{$infophoto.IDINFOPHO}" {if ($infophoto.AIPACTIVE==0)}style="display: none"{/if}class="graphicalCheckbox icon-check mouse ppshow" data-id="{$infophoto.IDINFOPHO}" data-link2="{$infophoto.U_SHOW}">{'Hide'|@translate}</span>
                    {if ($infophoto.AIPEDIT==1)}
					| <span class="edit_libinfo mouse icon-pencil" data-id="{$infophoto.IDINFOPHO}" data-lib="{$infophoto.AIPWORDING2}" data-hide="{$infophoto.AIPACTIVE}" data-typ="{$infophoto.AIPTYP}" data-dataprop='{$infophoto.AIPDATAPROP}' />{'Edit'|@translate}</span>
					| <a href="{$infophoto.U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');"><span class="icon-trash"></span>{'delete'|@translate}</a>
                    {/if}
                </span>
                <br />
              </li>
            {/foreach}
          </ul>
        </fieldset>
    </form>
	                    {if $useED==1}
                        <a href="{$ROOT_URL}admin/popuphelp.php?page=extended_desc" onclick="popuphelp(this.href); return false;" title="{'Use Extended Description tags...'|translate}" style="vertical-align: middle; border: 0; margin: 0.5em;"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/help.png" class="button" alt="{'Use Extended Description tags...'|translate}'"></a>
                    {/if}
{/if}
{if isset ($gestionD)}
<div>
    <form method="post" >
	<fieldset>
	<legend>{'Properties additionals'|@translate}</legend>
        <table>
            {foreach from=$info_photosI item=infophoto}
            <tr>
                <td style="width: 100px;"><span style="font-weight: bold; text-align: right;" >{$infophoto.AIPWORDING}</span></td>
                {if $infophoto.AIPTYP==1}
					<td><input data-typ="{$infophoto.AIPTYP}" type="text" size="150" maxlength="250" {if $useED==1}placeholder="{'Use Extended Description tags...'|@translate}"{/if} name="data[{$infophoto.IDINFOPHO}]" value="{$infophoto.AIPDATA}" /></td>
				{/if}
				{if $infophoto.AIPTYP==2}
					<td>
					  <input type="hidden" name="datadate[{$infophoto.IDINFOPHO}]" value="{$infophoto.AIPDATA}">
					  <label>
						<i class="icon-calendar"></i>
						<input type="text" data-datepicker="datadate[{$infophoto.IDINFOPHO}]" readonly>
					  </label>
					</td>
				{/if}
				{if $infophoto.AIPTYP==4}
					<td>{html_options name="data[{$infophoto.IDINFOPHO}]" values=$infophoto.AIPSELECT output=$infophoto.AIPSELECTTRANS selected="{$infophoto.AIPDATA}"}</td>
				{/if}
				{if $infophoto.AIPTYP==5}
					<td>{html_radios name="data[{$infophoto.IDINFOPHO}]" values=$infophoto.AIPSELECT output=$infophoto.AIPSELECTTRANS selected="{$infophoto.AIPDATA}" separator='<span style="margin: 0 0 0 10px"></span>'}</td>
				{/if}
			</tr>  
            {/foreach}
            <tr style="text-align: right;">
                <td colspan="2">
                    {if $useED==1}
                        <a href="{$ROOT_URL}admin/popuphelp.php?page=extended_desc" onclick="popuphelp(this.href); return false;" title="{'Use Extended Description tags...'|translate}" style="vertical-align: middle; border: 0; margin: 0.5em;"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/help.png" class="button" alt="{'Use Extended Description tags...'|translate}'"></a>
                    {/if}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input class="submit" name="submitaddinfoimg" type="submit" value="{'Save'|@translate}" />
                </td>
           </tr>
           </table>
  	</fieldset>
    </form>
</div>
{/if}

{if isset($mppconfig)}
  <fieldset>
	<div style="text-align:left">
	<form method="post">
	  {if isset($mppconfig.ADDPHOAC)}
		<span style="margin: 0 0 0 20px"><input class="submit" name="submitadddelpho" type="submit" value="{'Add field for automatic deletion\'s photos on a given date'|@translate}" />
	  {else}
		<span style="margin: 0 0 0 20px"><input class="submit" name="submitremovedelpho" type="submit" value="{'Remove Field for automatic deletion\'s photos on a given date and all date save'|@translate}" />
	  {/if}
	  <br>
	  <br>
	  {if isset($mppconfig.MOVEDESC)}
		<span style="margin: 0 0 0 20px"><input class="submit" name="submitmovedesc" type="submit" value="{'Move description in info table'|@translate}" />
	  {else}
	    <span style="margin: 0 0 0 20px"><input class="submit" name="submitdefaultdesc" type="submit" value="{'Return description in default location'|@translate}" />
	  {/if}
	  <br>
	  <br>
	  {if isset($mppconfig.MPPSHID)}
		<span style="margin: 0 0 0 20px"><input class="submit" name="submitmppsid" type="submit" value="{'Show Image Id'|@translate}" />
	  {else}
	    <span style="margin: 0 0 0 20px"><input class="submit" name="submitmpphid" type="submit" value="{'Hide Image Id'|@translate}" />
	  {/if}
	  <br>
	  
	</div>
	</form>
	<form method="post">
	  <br>
	  <span style="margin: 0 0 0 20px">{'Max field for select or radio option'|@translate}&nbsp;:<br>
	  <span style="margin: 0 0 0 20px"><input type="number" name="mppmaxfield" value="{$MPPMAXFIELD}" >
	  <br>
	  <br>
	  <br>
	  <span style="margin: 0 0 0 20px">{'The reference photo for exif and IPTC'|@translate}&nbsp;:<br>
	  <span style="margin: 0 0 0 20px"><input type="number" name="mppitidexif" value="{$MPPIDEXIF}" >
	  <br>
	  <p>
        <input class="submit" type="submit" name="submitmppoption" value="{'Submit'|@translate}">
      </p>
	</form>
  </fieldset>
{/if}