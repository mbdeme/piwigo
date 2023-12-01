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

  $("input[name=check_MPP]").change(function(){
    if($(this).is(':checked')){
		$('#changeselect').hide();
		$('#changepro').hide();
		$('#changeproda').hide();
    }else{
	  $('#selectMPP').val(0).change();
    }
  }); 
  
  $('#selectMPP')
  .change(function () {
    $("input[name=check_MPP]").attr('checked', false);
	$('#dataselect').children().remove()
    var selected = $(':selected', this);
	var typ=$(':selected', this).data('typ');
	var dataprop=$(':selected', this).data('dataprop');
	$('#hidetyp').val(typ);
	if(typ=='4'){
		$('#changeselect').show();
		$('#changepro').hide();
		$('#changeproda').hide();
		for (key in dataprop){
			$('#dataselect').append(new Option(dataprop[key]));
		}
		$('#changeradio').hide();
		$('#changeradio').empty();		
	}else if(typ=='2'){
		$('#changepro').hide();
		$('#changeproda').show();
		$('#changeselect').hide();
		$('#changeradio').hide();
		$('#changeradio').empty();
	}else if(typ=='5'){
		$('#changeselect').hide();
		$('#changepro').hide();
		$('#changeproda').hide();
		$('#changeradio').empty();
		for (key in dataprop){
			$('<input type="radio" name="radioselect" value="'+dataprop[key]+'">'+dataprop[key]+'</input><span style="margin: 0 0 0 10px">').appendTo('#changeradio');
		}
		$('#changeradio').show();
	}else if(typ=='1'){
		$('#changeselect').hide();
		$('#changepro').show();
		$('#changeproda').hide();
		$('#changeradio').hide();
		$('#changeradio').empty();
	}else{
		$('#changeselect').hide();
		$('#changepro').hide();
		$('#changeproda').hide();
		$('#changeradio').hide();
		$('#changeradio').empty();		
	}
  })
  .change();
  
});
{/footer_script}
<span id="persompp">
  {'Choose a property'|@translate}
  <br>
  <br>
  <select name="IDMPP" id="selectMPP">
		
    {foreach from=$info_select item=infoselect}
		<option value="{$infoselect.IDINFOPHO}" data-typ="{$infoselect.AIPTYP}" data-dataprop='{$infoselect.AIPDATAPROP}' id="selectMPP{$infoselect.IDINFOPHO}">{$infoselect.AIPWORDING}</option>
	{/foreach}
  </select>
  <br>  
  <br>
  <input id="check_MPP" type="checkbox" name="check_MPP"> {'delete data this property'|@translate}<br />
  <input id="hidetyp" type="hidden" name="invisibleTyp" value="">
  <div id="changepro" style="display: none;" >
		<textarea rows="3" cols="100" {if $useED==1}placeholder="{'Use Extended Description tags...'|@translate}"{/if} name="dataglob"></textarea>
  </div>
  <div id="changeproda" style="display: none;" >
	  <input type="hidden" name="datadate" value="{$DATADATE}">
      <label>
        <i class="icon-calendar"></i>
        <input type="text" data-datepicker="datadate" readonly>
      </label>  
  </div>
  <div id="changeselect" style="display: none;" >
	<select name="dataselect" id="dataselect">
	</select>
  </div>
  <div id="changeradio" style="display: none;" >
  </div>
</span>