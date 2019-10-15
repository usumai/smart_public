<?php
// $sql = "SELECT count(*) as rowcount_rr FROM smartdb.sm12_rwr;";
// $result = $con->query($sql);
// if ($result->num_rows > 0) {
//     while($row = $result->fetch_assoc()) {
//         $rowcount_rr = $row["rowcount_rr"];
// }}
// if ($rowcount_rr>0) {
//     $status_rr = "<span class='dropdown-item'>$rowcount_rr assets</span>";
// }else{
//     $status_rr = "<span class='dropdown-item'>Not loaded</span>";
// }
// $area_rr = $drpd_div."<h6 class='dropdown-header'>Raw remainder</h6>".$status_rr;


// $menu_software = $area_last_update . $area_version_status.$btn_push_master ;





$opt_stk = '';
$sql = "SELECT stkm_id, stk_id, stk_name FROM smartdb.sm13_stk WHERE stk_include=1";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $stkm_id	= $row["stkm_id"];
        $stk_id		= $row["stk_id"];
		$stk_name	= $row["stk_name"];
		$opt_stk   .= "<option value='".$stkm_id."'>".$stk_id.". ".$stk_name."</option>";
}}

?>


<script>
$(function(){
	fnDo("get_system","SetMenu",0)
	fnDo("get_templates","LoadTemplates",0)

    $(document).on('click', '.btnInitTemplate', function(){ 
		let ass_id = $(this).val();
		$("#ass_id").val(ass_id);
		console.log(ass_id)
	})
	
    $(document).on('click', '#btnCheckForUpdates', function(e){ 
		e.stopPropagation();
		if ($('.dropdown').find('#dropdownHelp').is(":hidden")){
			$('#dropdownHelp').dropdown('toggle');
		}
		$('#areaVersionAction').html("<span class='dropdown-item text-warning'>Checking server version<br><div class='spinner-border text-center' role='status'><span class='sr-only'>Loading...</span></div></span>");
		let nextAction = fnDo("save_check_version","CheckUpdates",1)
    })

    $( "#tags" ).autocomplete({
        source: function( request, response ) {
            search_term = request.term;
            $.ajax( {
                url: "05_action.php",
                data: {
                    act: "get_asset_list",
                    search_term: request.term
                },
                success: function( data ) {
                    json = JSON.parse(data)
                    response(json);
                }
            });
        },
        select: function( event, ui ) {
            // console.log("Selected: " + ui.item.value + " aka " + ui.item )
            if(ui.item.Asset=="Raw remainder results"){
              window.location.href = "14_rr.php?search_term="+search_term;
            }else{
              window.location.href = "11_ass.php?ass_id="+ui.item.value;
            }
        }
    })
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
      if (item.Asset == "Raw remainder results"){
        row = "<div><b>Raw remainder count:</b>"+item.Subnumber+"</div>" 
      }else{
        row = "<div><b>"+item.Asset+"-"+item.Subnumber+"</b>:"+item.AssetDesc1+"<br>"+item.status_compl+" InventNo["+item.InventNo+"] Serial["+item.SNo+"] Location["+item.Location+""+item.Room+"]</div>"
      }
      return $( "<li>" )
        .append(row)
        .appendTo( ul );
    };

});
</script>


<body class="d-flex flex-column h-100">
<header>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a class="navbar-brand" href="index.php">smartM</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item initiateBTN"></li>
                <li class='nav-item' id='menuSearch' style="display:none"><div class='ui-widget'><input id='tags' class='form-control' autofocus ></div></li>
                <li class="nav-item dropdown" id='menuAdd'></li>
                <li class="nav-item dropdown" id='menuVesion'></li>
                <li class="nav-item dropdown" id='menuHelp'></li>
            </ul>
            <span class='initiateBTN'></span>
        </div>
    </nav>
</header>


<!-- Modal -->
<div class="modal fade" id="modal_confirm_update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update to latest version</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="lead">Updating to the latest version will delete all data on this device. Are you Sure you want to proceed with the update?<br><br>Please keep device connected to the internet until the update is finished.</p>     
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a type="button" class="btn btn-danger" href='05_action.php?act=sys_pull_master'>Update</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_confirm_push" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Push this version to the master</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">  
        <p class="lead">This will overwrite the existing master file. Only do this if you are a guru developer.<br><br>Please keep device connected to the internet until the update is finished.</p>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a type="button" class="btn btn-danger" href='05_action.php?act=sys_push_master'>Update</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_confirm_reset" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Delete all data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">  
        <p class="lead">Reseting SMARTm will delete all data on this device.<br><br>Are you sure you want to proceed?</p>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a class="btn btn-danger" href='05_action.php?act=sys_reset_data'>Reset</a>
        <a class="btn btn-danger" href='05_action.php?act=sys_reset_data_minus_rr'>Reset excluding RR</a>
      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="modal_confirm_reset_minus_rr" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Delete all data except for raw remainder data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">  
        <p class="lead">Reseting SMARTm will delete all data on this device.<br><br>Are you sure you want to proceed?</p>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_initiate_template" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Initiate asset template</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	  
	  	<form method='post' action='05_action.php'>
			<div class="modal-body">  
				<p class="lead">Please select a stocktake to initiate this template into</p>
				<select name='stkm_id' class='form-control'>
					<?=$opt_stk?>
				</select>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-outline-dark">Initiate</button>
				<input type='hidden' name='act' value='save_initiate_template'>
				<input type='hidden' name='ass_id' id='ass_id'>
			</div>
		</form>
    </div>
  </div>
</div>


