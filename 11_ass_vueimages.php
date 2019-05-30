<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php
$ass_id	= $_GET["ass_id"];

$sql = "SELECT *, 
			CASE WHEN res_AssetDesc1 IS NULL THEN AssetDesc1 ELSE res_AssetDesc1 END AS best_AssetDesc1,
			CASE WHEN res_AssetDesc2 IS NULL THEN AssetDesc2 ELSE res_AssetDesc2 END AS best_AssetDesc2,
			CASE WHEN res_AssetMainNoText IS NULL THEN AssetMainNoText ELSE res_AssetMainNoText END AS best_AssetMainNoText,
			CASE WHEN res_Inventory IS NULL THEN Inventory ELSE res_Inventory END AS best_Inventory,
			CASE WHEN res_InventNo IS NULL THEN InventNo ELSE res_InventNo END AS best_InventNo,

			CASE WHEN res_SNo IS NULL THEN SNo ELSE res_SNo END AS best_SNo,
			CASE WHEN res_Location IS NULL THEN Location ELSE res_Location END AS best_Location,
			CASE WHEN res_Room IS NULL THEN Room ELSE res_Room END AS best_Room,
			CASE WHEN res_State IS NULL THEN State ELSE res_State END AS best_State,
			CASE WHEN res_latitude IS NULL THEN latitude ELSE res_latitude END AS best_latitude,
			CASE WHEN res_longitude IS NULL THEN longitude ELSE res_longitude END AS best_longitude,

			CASE WHEN res_Class IS NULL THEN Class ELSE res_Class END AS best_Class,
			CASE WHEN res_accNo IS NULL THEN accNo ELSE res_accNo END AS best_accNo,
			CASE WHEN res_CapDate IS NULL THEN CapDate ELSE res_CapDate END AS best_CapDate,
			CASE WHEN res_LastInv IS NULL THEN LastInv ELSE res_LastInv END AS best_LastInv,
			CASE WHEN res_DeactDate IS NULL THEN DeactDate ELSE res_DeactDate END AS best_DeactDate,
			CASE WHEN res_PlRetDate IS NULL THEN PlRetDate ELSE res_PlRetDate END AS best_PlRetDate,

			CASE WHEN res_Quantity IS NULL THEN Quantity ELSE res_Quantity END AS best_Quantity,
			CASE WHEN res_CurrentNBV IS NULL THEN CurrentNBV ELSE res_CurrentNBV END AS best_CurrentNBV,
			CASE WHEN res_AcqValue IS NULL THEN AcqValue ELSE res_AcqValue END AS best_AcqValue,
			CASE WHEN res_OrigValue IS NULL THEN OrigValue ELSE res_OrigValue END AS best_OrigValue,
			CASE WHEN res_ScrapVal IS NULL THEN ScrapVal ELSE res_ScrapVal END AS best_ScrapVal,
			CASE WHEN res_ValMethod IS NULL THEN ValMethod ELSE res_ValMethod END AS best_ValMethod,

			CASE WHEN res_CostCtr IS NULL THEN CostCtr ELSE res_CostCtr END AS best_CostCtr,
			CASE WHEN res_WBSElem IS NULL THEN WBSElem ELSE res_WBSElem END AS best_WBSElem,
			CASE WHEN res_Fund IS NULL THEN Fund ELSE res_Fund END AS best_Fund,
			CASE WHEN res_RspCCtr IS NULL THEN RspCCtr ELSE res_RspCCtr END AS best_RspCCtr,
			CASE WHEN res_RevOdep IS NULL THEN RevOdep ELSE res_RevOdep END AS best_RevOdep,

			CASE WHEN res_GrpCustod IS NULL THEN GrpCustod ELSE res_GrpCustod END AS best_GrpCustod,
			CASE WHEN res_CoCd IS NULL THEN CoCd ELSE res_CoCd END AS best_CoCd,
			CASE WHEN res_PlateNo IS NULL THEN PlateNo ELSE res_PlateNo END AS best_PlateNo,
			CASE WHEN res_Vendor IS NULL THEN Vendor ELSE res_Vendor END AS best_Vendor,
			CASE WHEN res_Mfr IS NULL THEN Mfr ELSE res_Mfr END AS best_Mfr,
			CASE WHEN res_UseNo IS NULL THEN UseNo ELSE res_UseNo END AS best_UseNo



		FROM smartdb.sm14_ass 
		WHERE ass_id=$ass_id";
$arr_asset = array();
$result = $con->query($sql);
if ($result->num_rows > 0) {
 while($r = $result->fetch_assoc()) {
     $arrsql[] = $r;
}}

$arr_asset = $arrsql[0];
$arr_asset['ar.lock_all']					= true;
$arr_asset['ar.lock_limited']				= true;

$arr_asset['ar.show_btnset']				= true;
$arr_asset['ar.show_fieldset']				= true;
$arr_asset['ar.show_second_choice_nf']		= false;
$arr_asset['ar.show_second_choice_err']		= false;
$arr_asset['ar.show_impaired_curr']			= false;
$arr_asset['ar.show_impaired_prev']			= false;
$arr_asset['ar.show_clear_btn']				= false;

$arr_asset['ar.selected_reason_code']		= false;
$arr_asset['ar.show_nyc']					= false;
$arr_asset['ar.show_complete']				= false;
$arr_asset['ar.show_incomplete_impaired']	= false;
$arr_asset['ar.loaded_image']				= 'includes/favicon.ico';





$arr_asset['rcs'] = [];
$sql = "SELECT * FROM smartdb.sm15_rc ";
$result = $con->query($sql);
if ($result->num_rows > 0) {
 while($row = $result->fetch_assoc()) {
    $list_res_reason_code 	= $row['res_reason_code'];
    $list_rc_desc 			= $row['rc_desc'];
    $rc_long_desc 			= $row['rc_long_desc'];
    $rc_examples 			= $row['rc_examples'];

	$arr_asset['rcs'][$list_res_reason_code]['rc_desc'] 		= $list_rc_desc;
	$arr_asset['rcs'][$list_res_reason_code]['rc_long_desc'] 	= $rc_long_desc;
	$arr_asset['rcs'][$list_res_reason_code]['rc_examples'] 	= $rc_examples;
}}


$arr_asset['completeness'] 	= false;
if($arr_asset['res_completed']==1){
	$arr_asset['completeness'] 	= true;
}
// echo $json_asset;

$sql = "SELECT * FROM smartdb.sm13_stk WHERE stkm_id=".$arr_asset['stkm_id'];
$result = $con->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$stk_id			= $row['stk_id'];
		$stk_name		= $row['stk_name'];
		$journal_text	= $row['journal_text'];
}}
$btn_found_ne = '<br><br><br><br>';
if (empty($arrsql[0]['impairment_code'])) {
	$btn_found_ne 	= "<a class='nav-link btn btn-success' href='05_action.php?act=save_asset_noedit&ass_id=$ass_id'>Sighted<br>No&nbsp;Edit</a><br>";	
}
$btn_found_e 	= "<button type='button' class='nav-link btn' v-on:click='save_reason_code(`ND10`)' style='background-color:#78e090!important'>Sighted<br>Edit</button><br>";

$btn_notfound 	= "<button type='button' class='nav-link btn btn-warning' v-on:click='stateful(`Not Found`)' >Not<br>found</button><br>";
$btn_error 		= "<button type='button' class='nav-link btn btn-primary' v-on:click='stateful(`Asset Error`)' >Asset<br>Error</button><br>";
// $btnset 		= "<div class='col-12 col-md-1 col-xl-1 bd-sidebar'  ><nav class='nav flex-column'>".$btn_found_ne.$btn_found_e.$btn_notfound.$btn_error."</nav></div>";
$btnset 		= $btn_found_ne.$btn_found_e.$btn_notfound.$btn_error;

$btn_clear = "	<div class='dropdown' v-if='ar.show_clear_btn'>
				    <button class='nav-link btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Clear</button>
				    <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
				        <a class='dropdown-item bg-danger text-light' href='05_action.php?act=save_clear_results&ass_id=$ass_id'>I'm sure</a>
				    </div>
				</div>";

$btn_cancel 	= "<button type='button' class='btn btn-danger' v-if='ar.show_cancel_btn' v-on:click='stateful(null)' >Cancel</button>";
$btn_deleteff 	= "	<div class='dropdown' v-if='ar.show_delete_btn'>
					    <button class='nav-link btn btn-danger dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Delete</button>
					    <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
					        <a class='dropdown-item bg-danger text-light' href='05_action.php?act=save_delete_first_found&ass_id=$ass_id'>I'm sure</a>
					    </div>
					</div>";;

$flag_status 	= "	<span class='nav-link bg-danger  text-center text-light' v-if='ar.show_nyc'>NYC</span>
					<span class='nav-link bg-success text-center text-light' v-if='ar.show_complete'>Complete</span>
					<a class='nav-link bg-danger text-center text-light' href='#impairment' v-if='ar.show_incomplete_impaired'>Impaired!</a><br>";


$Asset 		= $arrsql[0]['Asset'];
$Subnumber 	= $arrsql[0]['Subnumber'];
$a 			= scandir("images/");
$img_list 	= "";
$images 	= "";
$arr_img = [];
foreach ($a as $key => $value) {
    if (substr($value, 0,8)=="$Asset-$Subnumber")  {

		$arr_img[] = $value;
		$clean_val = str_replace("-", "", $value);
		$clean_val = str_replace(".jpg", "", $clean_val);
		$clean_val = str_replace("_", "", $clean_val);
		$img_list .= "<button type='button' class='btn thumb_photo' value='".$clean_val."' data-toggle='modal' data-target='#modal_show_pic'><img src='images/".$value."' width='200px'></button>"; 
		$images .= "<img class='btn_photo' id='".$clean_val."' src='images/".$value."' width='100%'>"; 
    }
}
$arr_asset['img'] = $arr_img;
$img_list = "<div class='row'><div class='col-12'><div class='form-group'><h2>Images</h2>$img_list</div></div></div>";

$ar['ar'] = $arr_asset;
$json_asset = json_encode($ar);
?>


<style type="text/css">
	label{
		margin-bottom:0px;
		font-weight: bold;
	}
	.form-group{
		margin-bottom:5px;
	}
</style>



<script>
$( function() {
	$( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });

	$('.thumb_photo').click(function(){
		let filename = $(this).val();
		$('.btn_photo').hide();
		$('#'+filename).show();
	});
});
</script>






<script src="includes/vue.js"></script>
<script src="includes/vuejs-datepicker.min.js"></script>
<br><br>
<div class='container-fluid' id="asset_page">
	<div class='row'>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar'><nav class='nav flex-column'><?=$flag_status?></nav></div>
		<div class='col-10'>
			<h2>Asset:{{ ar.Asset }}-{{ ar.Subnumber }}: {{ ar.AssetDesc1 }} ({{ ar.AssetDesc2 }})</h2>
			<p v-if='ar.res_reason_code'>{{ ar.res_reason_code }}: {{ ar.selected_rc_details }}</p>
		</div>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar'><nav class='nav flex-column'><?=$flag_status?></nav></div>
	</div>

	<div class='row'>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar'  >
			<nav class='nav flex-column'>
				<span  v-if='ar.show_btnset'><?=$btnset?></span>
			</nav>
			<?=$btn_clear?>
			<?=$btn_cancel?>
			<?=$btn_deleteff?>

			<a href="13_camera.php?ass_id=<?=$ass_id?>" class="btn btn-secondary text-center" ><span class='octicon octicon-device-camera' style='font-size:30px'></span></a>
		</div>
		<!-- <div class='col-1'><?=$btnset?></div> -->
		<div class='col-10' >

			<span v-if="ar.show_second_choice_nf">
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('NF10')">NF10</button></div>
					<div class='col-8'>
						<b>Asset Not Found - Project Disposal.</b> Asset not found - Disposal under National Project	Asset disposed under a National Project not communicated to DFG, not removed from the asset register/system. (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('NF10')">NF10</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('NF15')">NF15</button></div>
					<div class='col-8'>
						<b>Asset Not Found - Local Disposal.</b> Asset not found - Locally disposed asset.	Asset disposal, failed to advise DFG of disposal, not removed from the asset register/system. (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('NF15')">NF15</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('NF20')">NF20</button></div>
					<div class='col-8'>
						<b>Asset Not Found - Trade in.</b> Asset not found - Procurement Trade-In	Asset used as `Traded-in` in the procurement process, asset owner failed to follow correct disposal process, not communicated to DFG, not removed from the asset register/system. (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('NF20')">NF20</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('NF25')">NF25</button></div>
					<div class='col-8'>
						<b>Asset Not Found - Local Estate Works.</b> Asset not found - Disposal under Local Estate Works	Asset disposed under a local works, not communicated to DFG, not removed from the asset register/system. (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('NF25')">NF25</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('NF30')">NF30</button></div>
					<div class='col-8'>
						<b>Asset Not Found - Unexplained.</b> Asset not found - Unexplained	Asset owner cannot provide information as to its whereabouts. (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('NF30')">NF30</button></div>
				</div>
			</span>
			<span v-if="ar.show_second_choice_err">
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('NC10')">NC10</button></div>
					<div class='col-8'>
						<b>Not In Count</b>	Assets excluded from count.	Asset where the site is inaccessible, i.e. remote locality or project construction areas. (NIC report)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('NC10')">NC10</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('AF10')">AF10</button></div>
					<div class='col-8'>
						<b>Asset Found - Ownership	Asset ownership error.</b>	The asset management system to be updated to reflect correct owners.	Asset found with incorrect Cost Centre Code. (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('AF10')">AF10</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('AF15')">AF15</button></div>
					<div class='col-8'>
						<b>Asset Found - Incorrect Register.</b> Asset found - asset accounted for in the incorrect asset register/system.	An asset found that should be accounted for in MILIS and not ROMAN. (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('AF15')">AF15</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('AF20')">AF20</button></div>
					<div class='col-8'>
						<b>Asset Found - Location Transfers.</b> Asset found, however, asset register indicates the asset resides in another base/site.	Demountable moved between Defence properties without asset transfer documentation sent to DFG. (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('AF20')">AF20</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('PE10')">PE10</button></div>
					<div class='col-8'>
						<b>Prior Stocktake Error.</b> Stocktake Adjustment error in the asset register/ system, where the error has occurred as a direct result of a previous or current stocktake adjustment.	Reversal of a `write-on` action from a previous stocktake. AFF that should not have been created. (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('PE10')">PE10</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('RE10')">RE10</button></div>
					<div class='col-8'>
						<b>Asset Duplication - Different Register.</b> Errors found for the same asset record in separate registers/ systems/company codes where the error is a direct result of register actions by DFG Register Authority.	Duplication: assets recorded and financially accounted for in multiple register/ systems (ROMAN and MILIS), or in multiple Company Codes, (1000 and 4100). (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('RE10')">RE10</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('RE15')">RE15</button></div>
					<div class='col-8'>
						<b>Asset Duplication - Same Register.</b> Errors found for the same asset record in same asset register/ system, where the error is a direct result of register actions by the Register Authority	Duplication: assets recorded twice for the same physical asset. Assets created as a result of revaluation adjustments. (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('RE15')">RE15</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('FF99')">FF99</button></div>
					<div class='col-8'>
						<b>DLIAA excluded adjustments.</b> Authorised SAV discrepancies forward to DFG for ROMAN action advice. Adjustments to be conducted by DFG.	ROMAN adjustments relating to Project rollouts. (NIC)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('FF99')">FF99</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('RE20')">RE20</button></div>
					<div class='col-8'>
						<b>Asset register Error.</b> General non-financial related errors.	Simple record updates such as, location data, barcode updates, transcription, spelling errors, description i.e. asset description not in UPPER CASE. (ND)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('RE20')">RE20</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('RE25')">RE25</button></div>
					<div class='col-8'>
						<b>Asset Split.</b> Errors relating to assets that may form part of Merge/Split process.	A Split error is where a single asset record may have been initially created, however the assets characteristics distinctly display two separate physical assets (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('RE25')">RE25</button></div>
				</div>
				<div class='row'>
					<div class='col-2'><button class="btn btn-info" v-on:click="save_reason_code('RE30')">RE30</button></div>
					<div class='col-8'>
						<b>Asset Merge.</b> Errors relating to assets that may form part of Merge/Split process.	A Merge error is where two asset records may have been initially created, when it should have been a single asset record (SAV)
					</div>
					<div class='col-2 text-right'><button class="btn btn-info" v-on:click="save_reason_code('RE30')">RE30</button></div>
				</div>
			</span>

			<span v-if="ar.show_fieldset">
				<div class='row'>
					<div class='col-4'>
						<div class="form-group"><label>Asset Description</label><input type="text" v-model="ar.best_AssetDesc1" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>Asset Description 2</label><input type="text" v-model="ar.best_AssetDesc2" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>Asset Main No Text</label><input type="text" v-model="ar.best_AssetMainNoText" class="form-control" :disabled="ar.lock_all" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>Inventory</label><input type="text" v-model="ar.best_Inventory" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>InventNo</label><input type="text" v-model="ar.best_InventNo" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data"></div>
					</div>
					<div class='col-2'>
						<div class="form-group"><label>Serial No</label><input type="text" v-model="ar.best_SNo" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>Location</label><input type="text" v-model="ar.best_Location" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>Level/Room</label><input type="text" v-model="ar.best_Room" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>State</label><input type="text" v-model="ar.best_State" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>latitude</label><input type="text" v-model="ar.best_latitude" class= "form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data" v-bind:class="{'text-danger': isNaN(ar.best_latitude)}"></div>
						<div class="form-group"><label>longitude</label><input type="text" v-model="ar.best_longitude" class= "form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data" v-bind:class="{'text-danger': isNaN(ar.best_longitude)}"></div>
					</div>
					<div class='col-2'>
						<div class="form-group"><label>Class</label><input type="text" v-model="ar.best_Class" class="form-control" :disabled="ar.lock_all" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>accNo</label><input type="text" v-model="ar.best_accNo" class="form-control" :disabled="ar.lock_all" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>CapDate</label><input type="text" v-model="ar.best_CapDate" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data" ></div>
						<div class="form-group"><label>LastInv (YYYY-MM-DD)</label><input type="text" v-model="ar.best_LastInv" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data" ></div>
						<div class="form-group"><label>DeactDate</label><input type="text" v-model="ar.best_DeactDate" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data" ></div>
						<div class="form-group"><label>PlRetDate</label><input type="text" v-model="ar.best_PlRetDate" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data" ></div>
						<!-- <div class="form-group"><label>DeactDate</label><vuejs-datepicker  v-model="ar.best_DeactDate"  v-on:change="sync_data"></vuejs-datepicker></div> -->
							<!-- <input type="text" class= "form-control datepicker" :disabled="ar.lock_all" readonly> -->
					</div>
					<div class='col-2'>
						<div class="form-group"><label>Quantity</label><input type="text" v-model="ar.best_Quantity" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data" v-bind:class="{'text-danger': isNaN(ar.best_Quantity)}"></div>
						<div class="form-group"><label>CurrentNBV</label><input type="text" v-model="ar.best_CurrentNBV" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data" v-bind:class="{'text-danger': isNaN(ar.best_CurrentNBV)}"></div>
						<div class="form-group"><label>AcqValue</label><input type="text" v-model="ar.best_AcqValue" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data" v-bind:class="{'text-danger': isNaN(ar.best_AcqValue)}"></div>
						<div class="form-group"><label>OrigValue</label><input type="text" v-model="ar.best_OrigValue" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data" v-bind:class="{'text-danger': isNaN(ar.best_OrigValue)}"></div>
						<div class="form-group"><label>ScrapVal</label><input type="text" v-model="ar.best_ScrapVal" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data" v-bind:class="{'text-danger': isNaN(ar.best_ScrapVal)}"></div>
						<div class="form-group"><label>ValMethod</label><input type="text" v-model="ar.best_ValMethod" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data"></div>
					</div>
					<div class='col-2'>
						<div class="form-group"><label>CostCtr</label><input type="text" v-model="ar.best_CostCtr" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>WBSElem</label><input type="text" v-model="ar.best_WBSElem" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>Fund</label><input type="text" v-model="ar.best_Fund" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>RspCCtr</label><input type="text" v-model="ar.best_RspCCtr" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data"></div>
						<div class="form-group"><label>RevOdep</label><input type="text" v-model="ar.best_RevOdep" class= "form-control" :disabled="ar.lock_all" v-on:keyup="sync_data"></div>
					</div>
				</div>

				<span v-for="photo_file in ar.img">
					<button type='button' class='btn' data-toggle='modal' data-target='#modal_show_pic' v-on:click="update_picture(photo_file)" v-if="'ar.img.'+photo_file"><img :src="'images/'+photo_file" width="200px"></button>
				</span>



				<div class='row'>
					<div class='col-12'>
						<div class="form-group"><h2>Comments</h2>
							<!-- <input type="text"> -->
							<textarea v-model="ar.res_comment" class= "form-control" v-on:keyup="sync_data" rows='5'></textarea>
						</div>
					</div>
				</div>
			</span>


			<div class='row' v-if="ar.show_impaired_curr||ar.show_impaired_prev">
				<div class='col-12'>
					<h2>Impairment</h2>
				</div>
			</div>

			<span v-if="ar.show_impaired_curr">
				<div class='row'>
					<div class='col-2'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_5!=1, 'btn-dark': ar.res_isq_5==1}"  v-on:click="save_is_result('res_isq_5',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_5!=0, 'btn-dark': ar.res_isq_5==0}"  v-on:click="save_is_result('res_isq_5',0)">No</button>
					</div>
					<div class='col-8'>
						<label><b>Is there any evidence available of obsolescence or physical damage to the asset? (AASB Ref:136.12(e); 21.27(c))</b><br><small>Example: Property can no longer be used for its original purpose due to changes in technology and/or for example, an armoured vehicle has been driven into the building resulting in damage.</small></label>
					</div>
					<div class='col-2 text-right'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_5!=1, 'btn-dark': ar.res_isq_5==1}"  v-on:click="save_is_result('res_isq_5',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_5!=0, 'btn-dark': ar.res_isq_5==0}"  v-on:click="save_is_result('res_isq_5',0)">No</button>	
					</div>
				</div>

				<div class='row'>
					<div class='col-2'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_6!=1, 'btn-dark': ar.res_isq_6==1}"  v-on:click="save_is_result('res_isq_6',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_6!=0, 'btn-dark': ar.res_isq_6==0}"  v-on:click="save_is_result('res_isq_6',0)">No</button>
					</div>
					<div class='col-8'>
						<label><b>Are there any significant changes with an adverse effect on the entity that have taken place during the period, or are expected to take place in the near future, in the extent to which, or manner in which, an asset is used or is expected to be used? (AASB Ref:136.12(f); 21.27(d))</b><br><small>Example: Defence plans to close the base and/or buildings in the next 12 months.</small></label>
					</div>
					<div class='col-2 text-right'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_6!=1, 'btn-dark': ar.res_isq_6==1}"  v-on:click="save_is_result('res_isq_6',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_6!=0, 'btn-dark': ar.res_isq_6==0}"  v-on:click="save_is_result('res_isq_6',0)">No</button>	
					</div>
				</div>

				<div class='row'>
					<div class='col-2'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_7!=1, 'btn-dark': ar.res_isq_7==1}"  v-on:click="save_is_result('res_isq_7',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_7!=0, 'btn-dark': ar.res_isq_7==0}"  v-on:click="save_is_result('res_isq_7',0)">No</button>
					</div>
					<div class='col-8'>
						<label><b>Is there any evidence available from internal reporting that indicates the economic performance of an asset is, or will be, worse than expected? (AASB Ref:136.12(g); 21.27(f))</b><br><small>Example: Defence planned to use the building for the entire year, but due to malfunctioning lights the building is only able to be used during daylight hours or it has been declared unsafe & cannot be used.</small></label>
					</div>
					<div class='col-2 text-right'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_7!=1, 'btn-dark': ar.res_isq_7==1}"  v-on:click="save_is_result('res_isq_7',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_7!=0, 'btn-dark': ar.res_isq_7==0}"  v-on:click="save_is_result('res_isq_7',0)">No</button>	
					</div>
				</div>

				<div class='row'>
					<div class='col-2'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_8!=1, 'btn-dark': ar.res_isq_8==1}"  v-on:click="save_is_result('res_isq_8',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_8!=0, 'btn-dark': ar.res_isq_8==0}"  v-on:click="save_is_result('res_isq_8',0)">No</button>
					</div>
					<div class='col-8'>
						<label><b>Has there been a cessation, or near cessation, of the demand or need for services provided by the asset? (AASB Ref:21.27(a))</b><br><small>Example: Building has been vacated by the unit using it and/or the building is no longer used to service aircraft or as a officer's mess. This could also include a tennis court being turned into a carpark.</small></label>
					</div>
					<div class='col-2 text-right'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_8!=1, 'btn-dark': ar.res_isq_8==1}"  v-on:click="save_is_result('res_isq_8',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_8!=0, 'btn-dark': ar.res_isq_8==0}"  v-on:click="save_is_result('res_isq_8',0)">No</button>	
					</div>
				</div>

				<div class='row'>
					<div class='col-2'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_9!=1, 'btn-dark': ar.res_isq_9==1}"  v-on:click="save_is_result('res_isq_9',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_9!=0, 'btn-dark': ar.res_isq_9==0}"  v-on:click="save_is_result('res_isq_9',0)">No</button>
					</div>
					<div class='col-8'>
						<label><b>Has there been a decision to halt the construction of the asset before it is complete or in a useable condition? (AASB Ref:21.27(e))</b><br><small>Example: Defence is building a building, but ceases construction before it is finished (for example, a shell with no windows/doors). As a result Defence will be unable to use it.</small></label>
					</div>
					<div class='col-2 text-right'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_9!=1, 'btn-dark': ar.res_isq_9==1}"  v-on:click="save_is_result('res_isq_9',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_9!=0, 'btn-dark': ar.res_isq_9==0}"  v-on:click="save_is_result('res_isq_9',0)">No</button>	
					</div>
				</div>

				<div class='row'>
					<div class='col-2'>
						<input type="text" class="form-control text-center" v-model="ar.res_isq_10" readonly>
						<input type="range" min="1" max="100" class="slider form-control" v-model="ar.res_isq_10" v-on:change="save_is_slider">
						<button class="btn btn-outline-dark" v-on:click="save_is_result('res_isq_10',null)" v-if='ar.res_isq_10'>Clear</button>
					</div>
					<div class='col-8'>
						<label><b>If an impairment indicator is present please indicate to what extent the impairment has occurred (i.e 50%, 100%)?</b><br><small>Example: If the building has ten rooms and only five can be used, than it is 50% impaired or if the building has been damaged by an explosion and is now unusable, then it is 100% impaired.</small></label>
					</div>
					<div class='col-2 text-right'>
						<input type="text" class="form-control text-center" v-model="ar.res_isq_10" readonly>
						<input type="range" min="1" max="100" class="slider form-control" v-model="ar.res_isq_10" v-on:change="save_is_slider" >
						<button class="btn btn-outline-dark" v-on:click="save_is_result('res_isq_10',null)" v-if='ar.res_isq_10'>Clear</button>
					</div>
				</div><br>
			</span>

			<span v-if="ar.show_impaired_prev">
				<div class='row'>
					<div class='col-12'>
						<div>Impairment</div>
					</div>
				</div>
				<div class='row'>
					<div class='col-2'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_13!=1, 'btn-dark': ar.res_isq_13==1}"  v-on:click="save_is_result('res_isq_13',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_13!=0, 'btn-dark': ar.res_isq_13==0}"  v-on:click="save_is_result('res_isq_13',0)">No</button>
					</div>
					<div class='col-8'>
						<label><b>Does this asset still exist?</b></label>
					</div>
					<div class='col-2 text-right'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_13!=1, 'btn-dark': ar.res_isq_13==1}"  v-on:click="save_is_result('res_isq_13',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_13!=0, 'btn-dark': ar.res_isq_13==0}"  v-on:click="save_is_result('res_isq_13',0)">No</button>	
					</div>
				</div><br>

				<div class='row'>
					<div class='col-2'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_14!=1, 'btn-dark': ar.res_isq_14==1}"  v-on:click="save_is_result('res_isq_14',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_14!=0, 'btn-dark': ar.res_isq_14==0}"  v-on:click="save_is_result('res_isq_14',0)">No</button>
					</div>
					<div class='col-8'>
						<label><b>Is this asset still impaired?</b></label>
					</div>
					<div class='col-2 text-right'>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_14!=1, 'btn-dark': ar.res_isq_14==1}"  v-on:click="save_is_result('res_isq_14',1)">Yes</button>
						<button class="btn" v-bind:class="{'btn-outline-dark': ar.res_isq_14!=0, 'btn-dark': ar.res_isq_14==0}"  v-on:click="save_is_result('res_isq_14',0)">No</button>	
					</div>
				</div><br>

				<div class='row'>
					<div class='col-2'>
						<input type="text" v-model="ar.res_isq_15" class= "form-control" v-on:keyup="sync_data" readonly>
						<button class="btn btn-outline-dark" v-on:click="save_is_result('res_isq_15',null)" v-if='ar.res_isq_10'>Clear</button>
					</div>
					<div class='col-8'>
						<label><b>When will this asset be repaired/remediated?</b></label>
					</div>
					<div class='col-2 text-right'>
						<input type="text" v-model="ar.res_isq_15" class= "form-control" v-on:keyup="sync_data" readonly>
						<button class="btn btn-outline-dark" v-on:click="save_is_result('res_isq_15',null)" v-if='ar.res_isq_10'>Clear</button>
					</div>
				</div>
			</span>





<div class="modal fade" id="modal_show_pic" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Photo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">  


		<span v-model="ar.loaded_image"></span>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
		<div class='dropdown'>
		    <button class='nav-link btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Delete</button>
		    <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
		        <button class='dropdown-item bg-danger text-light' v-on:click="delete_photo">I'm sure</button>
		    </div>
		</div>
      </div>
    </div>
  </div>
</div>

<!-- 
Need to add camera
Add raw remainder
Add fix me portal
Add merge
Add history
Add user login
 -->



		</div>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar text-right'  >
			<nav class='nav flex-column'>
				<span  v-if='ar.show_btnset'><?=$btnset?></span>
				<?=$btn_clear?>
				<?=$btn_cancel?>
				<?=$btn_deleteff?>
			</nav>
		</div>
	    











	</div><!-- End main page row -->



<br><br><br><br><br><br><br><br><br><br><br>



</div>

<br><br><br>
<script>
new Vue({
	el: '#asset_page',
	data: <?=$json_asset?>,
	components: {
		vuejsDatepicker
	},
	methods: {
		sync_data: function (event) {

			let data = new FormData();
			data.append("act", 				"save_asset_field");
			data.append("ass_id", 			this.ar.ass_id);
			// console.log(this.ar);
			let asset_vals = Object.entries(this.ar);
			asset_vals = JSON.stringify(asset_vals);
			console.log(asset_vals);
			data.append("asset_vals", 	asset_vals);
			fetch("05_action.php", {
				method: "POST",
    			body: data
			})
			.then(function(res){ 
				return res.text();
			})
			.then(function(res){ 
				console.log(res);
				if(res=="success"){
				}
			})
		},
		isValidDate(d) {
			return d instanceof Date && !isNaN(d);
			console.log(d)
		},
		save_is_result(isq,isq_res){
			this.ar[isq] 						= isq_res;
			this.ar.res_completed 				= 0;
			this.ar.res_impairment_completed 	= 0;
			console.log(this.ar.impairment_code)
			if (this.ar.impairment_code=="impaired_curr"&&this.ar.res_isq_5!=null&&this.ar.res_isq_6!=null&&this.ar.res_isq_7!=null&&this.ar.res_isq_8!=null&&this.ar.res_isq_9!=null) {
				this.ar.res_impairment_completed 	= 1;
				this.ar.res_completed 				= 1;
			}else if (this.ar.impairment_code=="impaired_prev"&&this.ar.res_isq_13!=null&&this.ar.res_isq_14!=null&&this.ar.res_isq_15!=null) {
				this.ar.res_impairment_completed 	= 1;
				this.ar.res_completed 				= 1;
			}


			let data = new FormData();
			data.append("act",						"save_asset_isq");
			data.append("ass_id",					this.ar.ass_id);
			data.append("isq",						isq);
			data.append("isq_res",					isq_res);
			data.append("res_impairment_completed",	this.ar.res_impairment_completed);
			data.append("res_completed", 			this.ar.res_completed);

			fetch("05_action.php", {
				method: "POST",
    			body: data
			})
			.then(function(res){ 
				return res.text();
			})
			.then(function(res){ 
				console.log(res);
				if(res=="success"){
				}
			})
			this.stateful(this.ar.res_reason_code);
		},
		save_is_slider(){
			let data = new FormData();
			data.append("act",		"save_asset_isq");
			data.append("ass_id",	this.ar.ass_id);
			data.append("isq",		"res_isq_10");
			data.append("isq_res",	this.ar.res_isq_10);
			
			fetch("05_action.php", {
				method: "POST",
    			body: data
			})
			.then(function(res){ 
				return res.text();
			})
			.then(function(res){ 
				console.log(res);
				if(res=="success"){
				}
			})
		},

		save_reason_code(res_reason_code){
			this.ar.res_reason_code = res_reason_code;
			console.log(this.ar.impairment_code);
			if(this.ar.impairment_code){
				this.ar.res_completed 	= null;
			}else{
				this.ar.res_completed 	= 1;
			}
			console.log(this.ar.res_completed);
			let data = new FormData();
			data.append("act", 				"save_asset_edit");
			data.append("ass_id", 			this.ar.ass_id);
			data.append("res_reason_code", 	this.ar.res_reason_code);
			data.append("res_completed", 	this.ar.res_completed);
			fetch("05_action.php", {
				method: "POST",
    			body: data
			})
			.then(function(res){ 
				return res.text();
			})
			.then(function(res){ 
				console.log(res);
				if(res=="success"){
				}
			})
      		this.stateful(this.ar.res_reason_code)
		},
		update_picture(photo_filename){
			this.ar.loaded_image 			= photo_filename;
			// this.ar.loaded_image 			= "<img src='images/"+photo_filename+"'>";
			console.log("this.ar.loaded_image: "+this.ar.loaded_image);
		},
		delete_photo(){
			console.log("this.ar.loaded_image: "+this.ar.loaded_image);

			this.ar.img.pop;

			// let data = new FormData();
			// data.append("act", 				"save_asset_edit");
			// data.append("ass_id", 			this.ar.ass_id);
			// data.append("res_reason_code", 	this.ar.res_reason_code);
			// data.append("res_completed", 	this.ar.res_completed);
			// fetch("05_action.php", {
			// 	method: "POST",
   //  			body: data
			// })
			// .then(function(res){ 
			// 	return res.text();
			// })
			// .then(function(res){ 
			// 	console.log(res);
			// 	if(res=="success"){
			// 	}
			// })
		},
		test_method(){
			// alert("hello")
			console.log(this.ar.img)
		},
		stateful(res_reason_code){
			this.ar.res_reason_code 		= res_reason_code;
			this.ar.lock_limited 			= true;
			this.ar.lock_all 				= true;
			this.ar.show_btnset				= true;
			this.ar.show_fieldset			= true;
			this.ar.show_second_choice 		= false;
			this.ar.show_impaired_curr		= false;
			this.ar.show_impaired_prev		= false;
			this.ar.show_second_choice_nf	= false;
			this.ar.show_second_choice_err	= false;
			this.ar.show_clear_btn			= false;
			this.ar.show_cancel_btn			= false;
			this.ar.show_delete_btn			= false;

			this.ar.show_nyc				= false;
			this.ar.show_complete			= false;
			this.ar.show_incomplete_impaired= false;

			console.log(this.ar.rcs['ND10']);

			console.log("this.ar.res_reason_code:"+this.ar.res_reason_code);
			if (this.ar.res_reason_code) {
				this.ar.show_btnset			= false;

				if (["FF10","FF15","FF20","FF25"].includes(this.ar.res_reason_code)) {
					this.ar.show_complete		= true;
					this.ar.lock_limited 		= false;
					this.ar.lock_all 			= false;
					this.ar.show_cancel_btn		= false;
					this.ar.show_delete_btn		= true;
				}else if (this.ar.res_reason_code=='Not Found') {
					this.ar.show_nyc				= true;
					this.ar.show_btnset=false;
					this.ar.show_fieldset=false;
					this.ar.show_second_choice_nf=true;
					this.ar.show_cancel_btn		= true;
				}else if(this.ar.res_reason_code=='Asset Error'){
					this.ar.show_nyc				= true;
					this.ar.show_btnset=false;
					this.ar.show_fieldset=false;
					this.ar.show_second_choice_err=true;
					this.ar.show_cancel_btn		= true;
				}else{
					this.ar.show_complete		= true;
					// $arr_asset['ar.reason_codes'][$list_res_reason_code]['rc_desc'] 		= $list_rc_desc;
					this.ar.selected_rc_details	= this.ar.rcs[this.ar.res_reason_code]['rc_desc']+" - "+this.ar.rcs[this.ar.res_reason_code]['rc_long_desc'];
					this.ar.lock_limited 		= true;
					this.ar.lock_all 			= true;
					this.ar.show_clear_btn		= true;
					if (this.ar.res_reason_code=='ND10') {
						this.ar.lock_limited 	= false;
					}

					if(this.ar.impairment_code=="impaired_curr"){
						this.ar.show_impaired_curr	= true;	
					}else if(this.ar.impairment_code=="impaired_prev"){
						this.ar.show_impaired_prev	= true;
					}
				}
				if(this.ar.impairment_code=="impaired_curr"&&this.ar.res_impairment_completed!=1||this.ar.impairment_code=="impaired_prev"&&this.ar.res_impairment_completed!=1){
					this.ar.show_nyc				= false;
					this.ar.show_complete			= false;
					this.ar.show_incomplete_impaired= true;
				}



			}else{
				this.ar.show_nyc				= true;
				this.ar.show_complete			= false;
				this.ar.show_incomplete_impaired= false;
			}
		}
	},

	beforeMount: function(){
	    this.stateful(this.ar.res_reason_code)
	}
})
</script>




<!-- Modal -->











<?php include "04_footer.php"; ?>




