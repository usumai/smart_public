<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<br><br>
<?php

$sql = "SELECT *, CASE WHEN impairment_code IS NOT NULL THEN 'IS~' ELSE NULL END AS impairment_flag, CASE WHEN res_reason_code IS NULL THEN 'NYC' ELSE res_reason_code END AS asset_status  FROM smartdb.sm14_ass WHERE stk_include=1 ";
$sql .= " LIMIT 10 ";
$result = $con->query($sql);
if ($result->num_rows > 0) {
 while($r = $result->fetch_assoc()) {
     $arrsql[] = $r;
}}
// $arr_json["items"]   = $arrsql;
// $arr_json["maker"]    = "Lucas";
// $arr_json["showModal"]  = false;
// $json_asset     = json_encode($arr_json);
$json_asset     = json_encode($arrsql);



?>
<script src="includes/vue.js"></script>
<script src="includes/vuetify.js"></script>
<link rel="stylesheet" href="includes/vuetify.min.css">
<link rel="stylesheet" href="includes/googlefonts/materialfonts.css">





<style type="text/css">
label {
    font-weight: bold;
}
</style>


<script type="text/javascript">
    
$('#myTab a').on('click', function (e) {
  e.preventDefault()
  $(this).tab('show')
})  
</script>

<nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Home</a>
    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Profile</a>
    <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</a>
  </div>
</nav>
<div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">qwe</div>
  <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">asd</div>
  <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">zxc</div>
</div>



<div id="app">
    <v-app id="inspire">
        <v-toolbar flat color="white">
            <v-dialog v-model="dialog" max-width="90%" max-height="90%">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{ editedItem.AssetDesc1 }}</span>
                    </v-card-title>

                   <!--  <div class='row'>
                        <div class='col'>

                            <input outline v-model="editedItem.AssetDesc1" class='form-control'>
                        </div>
                        <div class='col'>
                            
                        </div>
                    </div> -->


            <div class='container-fluid'>







                <div class='row'>
                    <div class='col-12'>

                    </div>
                </div>

                <div class='row'>
                    <div class='col-4'>
                        <!-- ass_id, create_date, create_user, delete_date, delete_user, stkm_id, storage_id, stk_include, Asset, Subnumber, impairment_code, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, 

                            CCC_ParentName, CCC_GrandparentName, GrpCustod, CoCd, PlateNo, Vendor, Mfr, UseNo, 

                            res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo, res_isq_5, res_isq_6, res_isq_7, res_isq_8, res_isq_9, res_isq_10, res_isq_13, res_isq_14, res_isq_15 -->
                        <div class="form-group"><label>Asset Description</label><input type="text" v-model="editedItem.AssetDesc1" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>Asset Description 2</label><input type="text" v-model="editedItem.AssetDesc2" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>Asset Main No Text</label><input type="text" v-model="editedItem.AssetMainNoText" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>Class</label><input type="text" v-model="editedItem.Class" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>Inventory</label><input type="text" v-model="editedItem.Inventory" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>InventNo</label><input type="text" v-model="editedItem.InventNo" class="form-control" :readonly="lock_all"></div>
                    </div>
                    <div class='col-2'>
                        <div class="form-group"><label>Serial No</label><input type="text" v-model="editedItem.SNo" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>accNo</label><input type="text" v-model="editedItem.accNo" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>Location</label><input type="text" v-model="editedItem.Location" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>Level/Room</label><input type="text" v-model="editedItem.Room" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>State</label><input type="text" v-model="editedItem.State" class="form-control" :readonly="lock_all"></div>
                    </div>
                    <div class='col-2'>
                        <div class="form-group"><label>latitude</label><input type="text" v-model="editedItem.latitude" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>longitude</label><input type="text" v-model="editedItem.longitude" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>CapDate</label><input type="text" v-model="editedItem.CapDate" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>LastInv</label><input type="text" v-model="editedItem.LastInv" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>DeactDate</label><input type="text" v-model="editedItem.DeactDate" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>PlRetDate</label><input type="text" v-model="editedItem.PlRetDate" class="form-control" :readonly="lock_all"></div>
                    </div>
                    <div class='col-2'>
                        <div class="form-group"><label>Quantity</label><input type="text" v-model="editedItem.Quantity" class="form-control text-right" :readonly="lock_all"></div>
                        <div class="form-group"><label>CurrentNBV</label><input type="text" v-model="editedItem.CurrentNBV" class="form-control text-right" :readonly="lock_all"></div>
                        <div class="form-group"><label>AcqValue</label><input type="text" v-model="editedItem.AcqValue" class="form-control text-right" :readonly="lock_all"></div>
                        <div class="form-group"><label>OrigValue</label><input type="text" v-model="editedItem.OrigValue" class="form-control text-right" :readonly="lock_all"></div>
                        <div class="form-group"><label>ScrapVal</label><input type="text" v-model="editedItem.ScrapVal" class="form-control text-right" :readonly="lock_all"></div>
                    </div>
                    <div class='col-2'>
                        <div class="form-group"><label>CostCtr</label><input type="text" v-model="editedItem.CostCtr" class="form-control text-right" :readonly="lock_all"></div>
                        <div class="form-group"><label>WBSElem</label><input type="text" v-model="editedItem.WBSElem" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>Fund</label><input type="text" v-model="editedItem.Fund" class="form-control text-right" :readonly="lock_all"></div>
                        <div class="form-group"><label>RspCCtr</label><input type="text" v-model="editedItem.RspCCtr" class="form-control" :readonly="lock_all"></div>
                        <div class="form-group"><label>RevOdep</label><input type="text" v-model="editedItem.RevOdep" class="form-control text-right" :readonly="lock_all"></div>
                        <div class="form-group"><label>ValMethod</label><input type="text" v-model="editedItem.ValMethod" class="form-control" :readonly="lock_all"></div>
                    </div>
                </div>
            </div>












                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat @click="close">Cancel</v-btn>
                        <v-btn color="blue darken-1" flat @click="save">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-toolbar>




<!-- {"ass_id":"12465","create_date":"2019-05-22 00:00:00","create_user":"lucas.taulealeausuma","delete_date":null,"delete_user":null,"stkm_id":"5","storage_id":"94840","stk_include":"1","Asset":"823776","Subnumber":"0","impairment_code":"impaired_curr","genesis_cat":"original storage asset","first_found_flag":null,"rr_id":null,"fingerprint":null,"res_create_date":null,"res_create_user":null,"res_reason_code":"ND10","res_reason_code_desc":null,"res_impairment_completed":null,"res_completed":"1","res_comment":null,"AssetDesc1":"1408\/A0133 Piping and Valves","AssetDesc2":null,"AssetMainNoText":"1408\/A0133 Piping and Valves","Class":"5200","classDesc":null,"assetType":"OPE","Inventory":"IMP-EIG-181030","Quantity":"1","SNo":null,"InventNo":"516","accNo":"1408","Location":"1408\/A0133","Room":"TANKRM","State":"NSW","latitude":null,"longitude":null,"CurrentNBV":"689309.35","AcqValue":"716500.00","OrigValue":"820580.00","ScrapVal":"40420.00","ValMethod":"FCIV","RevOdep":"-27190.7","CapDate":"2014-06-01 00:00:00","LastInv":"2016-04-18 00:00:00","DeactDate":null,"PlRetDate":null,"CCC_ParentName":"ESTATE AND INFRASTRUCTURE GROUP","CCC_GrandparentName":null,"GrpCustod":"CSIG","CostCtr":"681524","WBSElem":null,"Fund":"99998","RspCCtr":null,"CoCd":"1000","PlateNo":null,"Vendor":null,"Mfr":null,"UseNo":"31","res_AssetDesc1":"He said \"I'm allabout it\" Yeah!","res_AssetDesc2":null,"res_AssetMainNoText":null,"res_Class":null,"res_classDesc":null,"res_assetType":null,"res_Inventory":null,"res_Quantity":null,"res_SNo":null,"res_InventNo":null,"res_accNo":null,"res_Location":null,"res_Room":null,"res_State":null,"res_latitude":null,"res_longitude":null,"res_CurrentNBV":null,"res_AcqValue":null,"res_OrigValue":null,"res_ScrapVal":null,"res_ValMethod":null,"res_RevOdep":null,"res_CapDate":null,"res_LastInv":null,"res_DeactDate":null,"res_PlRetDate":null,"res_CCC_ParentName":null,"res_CCC_GrandparentName":null,"res_GrpCustod":null,"res_CostCtr":null,"res_WBSElem":null,"res_Fund":null,"res_RspCCtr":null,"res_CoCd":null,"res_PlateNo":null,"res_Vendor":null,"res_Mfr":null,"res_UseNo":null,"res_isq_5":null,"res_isq_6":null,"res_isq_7":null,"res_isq_8":null,"res_isq_9":null,"res_isq_10":null,"res_isq_13":null,"res_isq_14":null,"res_isq_15":null} -->
    



        <v-data-table :headers="headers" :items="assets" class="elevation-1">
            <template v-slot:items="props">
                <td class="text-xs-left"><v-icon small class="mr-2" @click="editItem(props.item)"> edit </v-icon></td>
                <td>{{ props.item.Asset }}-{{ props.item.Subnumber }}<br>-c{{ props.item.Class }}</td>
                <td>{{ props.item.Location }}<br>{{ props.item.Room }}</td>
                <td nowrap>{{ props.item.AssetDesc1 }}<br>{{ props.item.AssetDesc2 }}</td>
                <td>{{ props.item.InventNo }}<br>{{ props.item.SerialNo }}</td>
                <td>{{ props.item.impairment_flag }}</td>
                <td class="text-xs-right">{{ props.item.CurrentNBV }}</td>
                <td class="text-xs-right">{{ props.item.asset_status }}</td>
                <td class="text-xs-right"><v-icon small class="mr-2" @click="editItem(props.item)"> edit </v-icon></td>
            </template>
        </v-data-table>



    </v-app>
</div>




<script>
new Vue({
  el: '#app',
  data: () => ({
    dialog: false,
    headers: [
      { text: 'Action',         sortable: false},
      { text: 'AssetID',        value: 'calories' },
      { text: 'Location',       value: 'fat' },
      { text: 'Description',    value: 'carbs' },
      { text: 'InventNo',       value: 'inventNo' },
      { text: 'IS',             value: 'is' },
      { text: '$NBV',           value: 'nbv' },
      { text: 'Status',         value: 'status' },
      { text: 'Action',         align: 'right', sortable: false }
    ],
    assets: [],

    lock_all: true,


    editedIndex: -1,
    editedItem: {
      name: '',
      calories: 0,
      fat: 0,
      carbs: 0,
      protein: 0
    },
    defaultItem: {
      name: '',
      calories: 0,
      fat: 0,
      carbs: 0,
      protein: 0
    }
  }),

  computed: {
    formTitle () {
      return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
    }
  },

  watch: {
    dialog (val) {
      val || this.close()
    }
  },

  created () {
    this.initialize()
  },

  methods: {
    initialize () {


        this.assets = <?=$json_asset?>;
      // this.assets = [
      //   {
      //     name: 'Frozen Yogurt',
      //     calories: 159,
      //     fat: 6.0,
      //     carbs: 24,
      //     protein: 4.0
      //   },
      //   {
      //     name: 'Ice cream sandwich',
      //     calories: 237,
      //     fat: 9.0,
      //     carbs: 37,
      //     protein: 4.3
      //   }
      // ]
    },

    editItem (item) {
      this.editedIndex = this.assets.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem (item) {
      const index = this.assets.indexOf(item)
      confirm('Are you sure you want to delete this item?') && this.assets.splice(index, 1)
    },

    close () {
      this.dialog = false
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      }, 300)
    },

    save () {
      if (this.editedIndex > -1) {
        Object.assign(this.assets[this.editedIndex], this.editedItem)
      } else {
        this.assets.push(this.editedItem)
      }
      this.close()
    }
  }
})
</script>