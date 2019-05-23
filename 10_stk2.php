<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<br><br>
<?php

$sql = "SELECT * FROM smartdb.sm14_ass WHERE stk_include=1 ";
$sql .= " LIMIT 10 ";
$result = $con->query($sql);
if ($result->num_rows > 0) {
 while($r = $result->fetch_assoc()) {
     $arrsql[] = $r;
}}
$arr_json["assets"] 	= $arrsql;
$arr_json["maker"] 		= "Lucas";
$arr_json["showModal"] 	= false;
$json_asset 		= json_encode($arr_json);



?>
<script src="includes/vue.js"></script>


<style type="text/css">
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}
.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}
.modal-container {
  width: 90%;
  height: 90%;
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
  transition: all .3s ease;
  font-family: Helvetica, Arial, sans-serif;
}
.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}
.modal-body {
  margin: 20px 0;
}
.modal-default-button {
  float: right;
}
.modal-enter {
  opacity: 0;
}
.modal-leave-active {
  opacity: 0;
}
.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>
ass_id, create_date, create_user, delete_date, delete_user, stkm_id, storage_id, stk_include, Asset, Subnumber, impairment_code, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo, res_isq_5, res_isq_6, res_isq_7, res_isq_8, res_isq_9, res_isq_10, res_isq_13, res_isq_14, res_isq_15









<div class='container-fluid' id="asset_page">
	<table class="table">
		<tr v-for="x in assets">
			<td><button href='' class='btn btn-outline-dark'>Action</button></td>
			<td>{{ x.Asset }}-{{ x.Subnumber }}</td>
			<td>{{ x.AssetDesc1 }}</td>
			<td>{{ x.AssetDesc2 }}</td>
			<td>{{ x.Class }}</td>
			<td>{{ x.ass_id }}</td>
			<td><button href='' class='btn btn-outline-dark float-right'  id="show-modal" @click="showModal = true">Action</button></td>
		</tr>	
	</table>



	<modal v-if="showModal" @close="showModal = false">
		<h3 slot="header">custom header</h3>
	</modal>


</div>







<script type="text/javascript">
Vue.component('modal', {
	template: '#modal-template'
})
new Vue({
	el: '#asset_page',
	data: <?=$json_asset?>
})
</script>




<script type="text/x-template" id="modal-template">
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">

          <div class="modal-header">
            <slot name="header">
              default header
            </slot>
          </div>

          <div class="modal-body">
            <slot name="body">
              default body
            </slot>
          </div>

          <div class="modal-footer">
            <slot name="footer">
              default footer
              <button class="modal-default-button" @click="$emit('close')">
                OK
              </button>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</script>


<div id="app">
</div>
