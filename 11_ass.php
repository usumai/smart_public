<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php
$ass_id	= $_GET["ass_id"];

$sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id=$ass_id";
$arr_asset = array();
$result = $con->query($sql);
if ($result->num_rows > 0) {
 while($r = $result->fetch_assoc()) {
     $arrsql[] = $r;
}}

$arr_asset = $arrsql[0];
// $arr_asset[0]['awesome'] = false;
$arr_asset['awesome'] 			= true;
$arr_asset['best_AssetDesc1'] 	= true;

$json_asset = json_encode($arr_asset);
// echo $json_asset;

$sql = "SELECT * FROM smartdb.sm13_stk WHERE stkm_id=".$arr_asset['stkm_id'];
$result = $con->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$stk_id			= $row['stk_id'];
		$stk_name		= $row['stk_name'];
		$journal_text	= $row['journal_text'];
}}


$btn_found_ne 	= "<button type='button' class='nav-link btn btn-success' id='btn_found_ne'	>Sighted<br>No&nbsp;Edit</button><br>";
$btn_found_e 	= "<button type='button' class='nav-link btn btn-success' id='btn_found_e' style='background-color:#78e090!important'>Sighted<br>Edit</button><br>";
$btn_notfound 	= "<button type='button' class='nav-link btn btn-warning' id='btn_notfound'	>Not<br>found</button><br>";
$btn_error 		= "<button type='button' class='nav-link btn btn-primary' id='btn_error'	>Asset<br>Error</button>";
$btnset 		= "<div class='col-12 col-md-1 col-xl-1 bd-sidebar'><nav class='nav flex-column'>".$btn_found_ne.$btn_found_e.$btn_notfound.$btn_error."</nav></div>";




$flag_status 	= "<span class='nav-link bg-danger text-center text-light'>NYC</span><br>";
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

<br><br>
<script src="includes/vue.js"></script>
<div class='container-fluid' id="asset_page">
	<div class='row'>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar'><nav class='nav flex-column'><?=$flag_status?></nav></div>
		<div class='col-10'>
			<h2  v-if="awesome">Asset:{{ Asset }}-{{ Subnumber }}: {{ AssetDesc1 }} ({{ AssetDesc2 }})</h2>
		</div>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar'><nav class='nav flex-column'><?=$flag_status?></nav></div>
	</div>

	<div class='row'>
	    <?=$btnset?>
		<!-- <div class='col-1'><?=$btnset?></div> -->
		<div class='col-10'>

			<div class='row'>
				<div class='col-4'>
					<!-- ass_id, create_date, create_user, delete_date, delete_user, stkm_id, storage_id, stk_include, Asset, Subnumber, impairment_code, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, 

						CCC_ParentName, CCC_GrandparentName, GrpCustod, CoCd, PlateNo, Vendor, Mfr, UseNo, 

						res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo, res_isq_5, res_isq_6, res_isq_7, res_isq_8, res_isq_9, res_isq_10, res_isq_13, res_isq_14, res_isq_15 -->
					<div class="form-group"><label>Asset Description</label><input type="text" v-model="best_AssetDesc1" class="form-control" :disabled="awesome"></div>
					<div class="form-group"><label>Asset Description 2</label><input type="text" v-model="AssetDesc2" class="form-control"></div>
					<div class="form-group"><label>Asset Main No Text</label><input type="text" v-model="AssetMainNoText" class="form-control"></div>
					<div class="form-group"><label>Class</label><input type="text" v-model="Class" class="form-control"></div>
					<div class="form-group"><label>Inventory</label><input type="text" v-model="Inventory" class="form-control"></div>
					<div class="form-group"><label>InventNo</label><input type="text" v-model="InventNo" class="form-control"></div>
				</div>
				<div class='col-2'>
					<div class="form-group"><label>Serial No</label><input type="text" v-model="SNo" class="form-control"></div>
					<div class="form-group"><label>accNo</label><input type="text" v-model="accNo" class="form-control"></div>
					<div class="form-group"><label>Location</label><input type="text" v-model="Location" class="form-control"></div>
					<div class="form-group"><label>Level/Room</label><input type="text" v-model="Room" class="form-control"></div>
					<div class="form-group"><label>State</label><input type="text" v-model="State" class="form-control"></div>
				</div>
				<div class='col-2'>
					<div class="form-group"><label>latitude</label><input type="text" v-model="latitude" class="form-control"></div>
					<div class="form-group"><label>longitude</label><input type="text" v-model="longitude" class="form-control"></div>
					<div class="form-group"><label>CapDate</label><input type="text" v-model="CapDate" class="form-control"></div>
					<div class="form-group"><label>LastInv</label><input type="text" v-model="LastInv" class="form-control"></div>
					<div class="form-group"><label>DeactDate</label><input type="text" v-model="DeactDate" class="form-control"></div>
					<div class="form-group"><label>PlRetDate</label><input type="text" v-model="PlRetDate" class="form-control"></div>
				</div>
				<div class='col-2'>
					<div class="form-group"><label>Quantity</label><input type="text" v-model="Quantity" class="form-control"></div>
					<div class="form-group"><label>CurrentNBV</label><input type="text" v-model="CurrentNBV" class="form-control"></div>
					<div class="form-group"><label>AcqValue</label><input type="text" v-model="AcqValue" class="form-control"></div>
					<div class="form-group"><label>OrigValue</label><input type="text" v-model="OrigValue" class="form-control"></div>
					<div class="form-group"><label>ScrapVal</label><input type="text" v-model="ScrapVal" class="form-control"></div>
				</div>
				<div class='col-2'>
					<div class="form-group"><label>CostCtr</label><input type="text" v-model="CostCtr" class="form-control"></div>
					<div class="form-group"><label>WBSElem</label><input type="text" v-model="WBSElem" class="form-control"></div>
					<div class="form-group"><label>Fund</label><input type="text" v-model="Fund" class="form-control"></div>
					<div class="form-group"><label>RspCCtr</label><input type="text" v-model="RspCCtr" class="form-control"></div>
					<div class="form-group"><label>RevOdep</label><input type="text" v-model="RevOdep" class="form-control"></div>
					<div class="form-group"><label>ValMethod</label><input type="text" v-model="ValMethod" class="form-control"></div>
				</div>
			</div>
		</div>
	    <?=$btnset?>
	</div>

</div>

<script>
new Vue({
	el: '#asset_page',
	data: <?=$json_asset?>,
	methods: {
		doSum: function() {
			this.sum = this.first + this.second;
		}
	}
})
</script>