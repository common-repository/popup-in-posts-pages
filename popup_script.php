<?php
define('popupclassname_PIPP', 	'hiden_popupi');
if(isset($include_script)){	
	return;
}


header("Content-type: application/javascript");
if(isset($_GET['tinymce_editor'])) {?>

//<script>
function add_new_filed(){
	counter_pipp=counter_pipp+1;
	var parent = document.getElementById("fields_holder_rh");
	var fields = document.getElementsByClassName("OnePopupBlock");
	var target_field = fields[0]; 
	var newDiv = target_field.cloneNode(true);
		newDiv.innerHTML = newDiv.innerHTML.replace(/THE_IDENTIFIER_OF_POPUP/g, counter_pipp);
	parent.appendChild(newDiv);
}

// =========== adding button in tinyMCE =========== //
var arguments=
{
	init : function(ed, url) {

			ed.addButton('Popup_Insert', {
				title : 'Insert Popup',
				image : url+'/bubble2.png',
				onclick : function() {
					var got_text= tinyMCE.activeEditor.selection.getContent({format : 'raw'});
					add_new_filed();
					ed.execCommand('mceInsertContent', false, '[popupi '+counter_pipp+']'+(got_text || "example anchor text")+'[/popupi]'  );
					jQuery('html, body').animate({
						scrollTop: jQuery("#popupblock_"+counter_pipp).offset().top
					 }, 300);
					//document.getElementById("popupblock_"+counter_pipp).scrollIntoView();
				}
			});
	},
	createControl : function(n, cm) {
		return null;
	}
}
tinymce.create('tinymce.plugins.Nlineeplg123', arguments);
tinymce.PluginManager.add('MyButtonss1_pipp', tinymce.plugins.Nlineeplg123);
// ================================================= //


<?php 
}
else 
{
?>
 
	
	// ===================== simple POPUP  ===================== https://github.com/tazotodua/useful-javascript/ =====================
	function show_my_popup(TEXTorID, AdditionalStyles ){
			TEXTorID=TEXTorID.trim(); var FirstChar= TEXTorID.charAt(0); var eName = TEXTorID.substr(1); if ('#'==FirstChar || '.'==FirstChar){	if('#'==FirstChar){var x=document.getElementById(eName);} else{var x=document.getElementsByClassName(eName)[0];}    if(x==null){var x=document.createElement('div');x.innerHTML="popup element not found";} } else { var x=document.createElement('div');x.innerHTML=TEXTorID;} var randm_id=Math.floor((Math.random()*100000000));
		var DivAA = document.createElement('div');    DivAA.id = "blkBackgr_"+randm_id;  DivAA.className = "MyJsBackg";   DivAA.setAttribute("style", 'background:black; height:5000px; left:0px; opacity:0.9; position:fixed; top:0px; width:100%; z-index:9599;'); document.body.appendChild(DivAA);      AdditionalStyles= AdditionalStyles || '';
		var DivBB = document.createElement('div');    DivBB.id = 'popupp_'+randm_id;     DivBB.className = "MyJsPopup";   DivBB.setAttribute("style",'background-color:white; border:6px solid white; border-radius:10px; display:block; min-height:1%; min-width:350px; width:auto;  max-height:80%; max-width:800px; padding:15px; overflow: scroll;  position:fixed; text-align:left; top:10%; z-index:9599; left:0px; right:0px; margin-left:auto; margin-right:auto; width:80%;'+ AdditionalStyles); 	DivBB.innerHTML = '<div style="background-color:#ff858b; border-radius:55px; padding:0 15px; font-family:arial; float:right; font-weight:700; margin:-30px -10px 0px -20px; z-index: 88; "  class="CloseButtn" ><a href="javascript:my_popup_closee('+randm_id+');" style="display:block;margin:-5px 0 0 0;font-size:1.6em;">x</a></div>'; document.body.appendChild(DivBB); z=x.cloneNode(true); z.innerHTML= '<div class="div_cont" style="overflow:auto;"><div>'+z.innerHTML+'</div></div>';  DivBB.appendChild(z); if(z.style.display=="none"){z.style.display="block";}       }               function my_popup_closee(RandomIDD) { var x=document.getElementById("blkBackgr_"+RandomIDD); x.parentNode.removeChild(x);      var x=document.getElementById('popupp_'+RandomIDD); x.parentNode.removeChild(x);
	}
	// ==============================================================================================================================

		//all divs, which has a classname "My_TARGET_Hiddens", will be automatically hidden on page load...
		function hide_popuping_divs(classnameee){
			var elmnts = document.getElementsByClassName(classnameee); var index;
			for (index = 0; index < elmnts.length; ++index) {
				elmnts[index].style.display= "none";	//elmnts[index].className = elmnts[index].className + " my_css_hide_class";
			}
		}	
		
	//
	window.onload=function(){    hide_popuping_divs('<?php echo popupclassname_PIPP;?>');     };
<?php
}	
exit;
?>