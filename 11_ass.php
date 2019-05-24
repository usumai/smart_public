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
// $arr_asset[0]['ar.lock_all'] = false;
$arr_asset['ar.lock_all']		= true;
$arr_asset['ar.lock_limited']	= true;
// $arr_asset['vis_reason_code_buttons'] 		= true;
$arr_asset['best_AssetDesc1'] 	= true;
$arr_asset['best_AssetDesc2'] 	= true;
$arr_asset['best_Inventory'] 	= true;
$arr_asset['best_InventNo'] 	= true;
$arr_asset['best_SNo'] 			= true;
$arr_asset['best_Location'] 	= true;
$arr_asset['best_Room'] 		= true;
$arr_asset['best_State'] 		= true;
$arr_asset['best_latitude'] 	= true;
$arr_asset['best_longitude'] 	= true;




$arr_asset['completeness'] 	= false;
if($arr_asset['res_completed']==1){
	$arr_asset['completeness'] 	= true;
}
$ar['ar'] = $arr_asset;
$json_asset = json_encode($ar);
// echo $json_asset;

$sql = "SELECT * FROM smartdb.sm13_stk WHERE stkm_id=".$arr_asset['stkm_id'];
$result = $con->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$stk_id			= $row['stk_id'];
		$stk_name		= $row['stk_name'];
		$journal_text	= $row['journal_text'];
}}


$btn_found_ne 	= "<button type='button' class='nav-link btn btn-success' v-if='ar.vis_reason_code_buttons' value='hello' v-on:click='select_nd10' id='btn_found_ne'	>Sighted<br>No&nbsp;Edit</button><br>";
$btn_found_e 	= "<button type='button' class='nav-link btn btn-success' v-if='ar.vis_reason_code_buttons' id='btn_found_e' style='background-color:#78e090!important'>Sighted<br>Edit</button><br>";
$btn_notfound 	= "<button type='button' class='nav-link btn btn-warning' v-if='ar.vis_reason_code_buttons' id='btn_notfound'	>Not<br>found</button><br>";
$btn_error 		= "<button type='button' class='nav-link btn btn-primary' v-if='ar.vis_reason_code_buttons' id='btn_error'	>Asset<br>Error</button>";
$btnset 		= "<div class='col-12 col-md-1 col-xl-1 bd-sidebar'  ><nav class='nav flex-column'>".$btn_found_ne.$btn_found_e.$btn_notfound.$btn_error."</nav></div>";




$flag_status 	= "<span class='nav-link bg-danger text-center text-light' v-if='!ar.res_completed'>NYC</span><span class='nav-link bg-success text-center text-light' v-if='ar.res_completed'>Complete</span><br>";
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
			<h2>Asset:{{ ar.Asset }}-{{ ar.Subnumber }}: {{ ar.AssetDesc1 }} ({{ ar.AssetDesc2 }})</h2>{{ ar.res_reason_code }}
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
					<div class="form-group"><label>Asset Description</label><input type="text" v-model="ar.AssetDesc1" class="form-control" :disabled="ar.lock_limited"  v-on:change="sync_data"></div>
					<div class="form-group"><label>Asset Description 2</label><input type="text" v-model="ar.AssetDesc2" class="form-control" :disabled="ar.lock_limited"></div>
					<div class="form-group"><label>Asset Main No Text</label><input type="text" v-model="ar.AssetMainNoText" class="form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>Inventory</label><input type="text" v-model="ar.Inventory" class="form-control" :disabled="ar.lock_limited"></div>
					<div class="form-group"><label>InventNo</label><input type="text" v-model="ar.InventNo" class="form-control" :disabled="ar.lock_limited"></div>
				</div>
				<div class='col-2'>
					<div class="form-group"><label>Serial No</label><input type="text" v-model="ar.SNo" class="form-control" :disabled="ar.lock_limited"></div>
					<div class="form-group"><label>Location</label><input type="text" v-model="ar.Location" class="form-control" :disabled="ar.lock_limited"></div>
					<div class="form-group"><label>Level/Room</label><input type="text" v-model="ar.Room" class="form-control" :disabled="ar.lock_limited"></div>
					<div class="form-group"><label>State</label><input type="text" v-model="ar.State" class="form-control" :disabled="ar.lock_limited"></div>
					<div class="form-group"><label>latitude</label><input type="text" v-model="ar.latitude" class= "form-control" :disabled="ar.lock_limited"></div>
					<div class="form-group"><label>longitude</label><input type="text" v-model="ar.longitude" class= "form-control" :disabled="ar.lock_limited"></div>
				</div>
				<div class='col-2'>
					<div class="form-group"><label>Class</label><input type="text" v-model="ar.Class" class="form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>accNo</label><input type="text" v-model="ar.accNo" class="form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>CapDate</label><input type="text" v-model="ar.CapDate" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>LastInv</label><input type="text" v-model="ar.LastInv" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>DeactDate</label><input type="text" v-model="ar.DeactDate" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>PlRetDate</label><input type="text" v-model="ar.PlRetDate" class= "form-control" :disabled="ar.lock_all"></div>
				</div>
				<div class='col-2'>
					<div class="form-group"><label>Quantity</label><input type="text" v-model="ar.Quantity" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>CurrentNBV</label><input type="text" v-model="ar.CurrentNBV" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>AcqValue</label><input type="text" v-model="ar.AcqValue" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>OrigValue</label><input type="text" v-model="ar.OrigValue" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>ScrapVal</label><input type="text" v-model="ar.ScrapVal" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>ValMethod</label><input type="text" v-model="ar.ValMethod" class= "form-control" :disabled="ar.lock_all"></div>
				</div>
				<div class='col-2'>
					<div class="form-group"><label>CostCtr</label><input type="text" v-model="ar.CostCtr" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>WBSElem</label><input type="text" v-model="ar.WBSElem" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>Fund</label><input type="text" v-model="ar.Fund" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>RspCCtr</label><input type="text" v-model="ar.RspCCtr" class= "form-control" :disabled="ar.lock_all"></div>
					<div class="form-group"><label>RevOdep</label><input type="text" v-model="ar.RevOdep" class= "form-control" :disabled="ar.lock_all"></div>
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
		sync_data: function (event) {

		},
		select_nd10: function (event) {
			// alert(this.ass_id)
			this.ar.res_reason_code = "ND10";
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


      		this.stateful()
		},

		stateful: function (event) {
			this.ar.lock_limited 			= true;
			this.ar.lock_all 				= true;
			this.ar.vis_reason_code_buttons	= true;

			if (this.ar.res_reason_code) {
				this.ar.vis_reason_code_buttons 		= false;

			}
			if (this.ar.res_completed==1) {

			}
			if (["ND10","ND10","ND10"].includes(this.ar.res_reason_code)) {
				this.ar.lock_limited 	= false;
				this.ar.lock_all 		= true;

			}
		}





	},
	beforeMount: function(){
	    this.stateful()
	 }
})
</script>