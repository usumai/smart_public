<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php
$ass_id	= $_GET["ass_id"];


$pdata = [];
$sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id=$ass_id";
$result = $con->query($sql);
if ($result->num_rows > 0) {
 while($r = $result->fetch_assoc()) {
    $pdata["asset"] = $r;
}}

$reasoncodes = [];
$sql = "SELECT * FROM smartdb.sm15_rc ";
$result = $con->query($sql);
if ($result->num_rows > 0) {
 while($r = $result->fetch_assoc()) {
    $pdata["reasoncodes"][] = $r;
}}
$sdata = json_encode($pdata);







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
    let data = <?=$sdata?>;
    let tempData = [];

    let colGreen= "#78e090";
    let colRed  = "#FFCDD2";
    let colAmber= "#FFE0B2";

    tempData["lockSettings"] = [];
    tempData["lockSettings"] = {
        "FF": "00000 000000 000000 000000 00000",
        "NF": "11111 111111 111111 111111 11111",
        "ND": "00100 111111 000000 000000 00000",
        "AF": "00000 000000 000000 000000 00000",
    }

    tempData["valdStgs"] = [];
    tempData["valdStgs"] = {
        "default": {
            "type":"string",
            "maxlen":"250"
        },
        "CurrentNBV": {
            "type":"number",
            "maxlen":"250",
            "maxnum":"100000000000"
        },
        "AcqValue": {
            "type":"number",
            "maxlen":"250",
            "maxnum":"100000000000"
        },
        "OrigValue": {
            "type":"number",
            "maxlen":"250",
            "maxnum":"100000000000"
        },
        "ScrapVal": {
            "type":"number",
            "maxlen":"250",
            "maxnum":"100000000000"
        },
        "CapDate": {
            "type":"date",
            "maxlen":"250",
            "maxnum":"100000000000"
        },
        "LastInv": {
            "type":"date",
            "maxlen":"250",
            "maxnum":"100000000000"
        },
        "DeactDate": {
            "type":"date",
            "maxlen":"250",
            "maxnum":"100000000000"
        },
        "PlRetDate": {
            "type":"date",
            "maxlen":"250",
            "maxnum":"100000000000"
        },
        "res_comment": {
            "type":"text",
            "maxlen":"2000"
        },
    }

    console.log(data)
    console.log(tempData)
    tempData["arrRC"]=[];
    for (let rc in data["reasoncodes"]){
        let res_reason_code = data["reasoncodes"][rc]["res_reason_code"];
        let rc_desc         = data["reasoncodes"][rc]["rc_desc"];
        let rc_long_desc    = data["reasoncodes"][rc]["rc_long_desc"];
        let rc_examples     = data["reasoncodes"][rc]["rc_examples"];
        let rc_section      = data["reasoncodes"][rc]["rc_section"];
        let btnRCL = "<div class='col-2'><button class='btn btn-info rc_select' value='"+res_reason_code+"'>"+res_reason_code+"</button></div>"
        let btnRCR = "<div class='col-2'><button class='btn btn-info rc_select float-right' value='"+res_reason_code+"'>"+res_reason_code+"</button></div>"
        let rowRC  = "<div class='row rc_option rc_section"+rc_section+"'>"+btnRCL+"<div class='col-8'><b>"+rc_desc+"</b> "+rc_long_desc+" <br>Example: "+rc_examples+"</div>"+btnRCR+"</div>"
        
        tempData["arrRC"][res_reason_code]=rc_desc;
        $("#areaRCs").append(rowRC)
    }
    
	$( ".datepicker" ).datepicker({ 
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true 
	});


    function fnGetImgGallery() {
        let ImgGallery = "Images TBA"
        $.post("api.php",{
            act:    "get_ImgGallery",
            ass_id: data["asset"]["ass_id"]
        },
        function(ImgGallery, status){
            $('#areaImgGallery').html(ImgGallery);
        });
    }

    function fnInitialSetup(){
        $(".tf").each(function(){
            let fieldName = $(this).data("name");
            let fieldValue = data["asset"][fieldName];
            $(this).html(fieldValue);
            // console.log("Publishing field: "+fieldName+" with value:"+fieldValue)
        })

        $(".txy").each(function(){// Initially populate the asset fields with the current db result data - highlight changes
            let fieldName   = $(this).data("name");
            let originalFV  = data["asset"][fieldName];
            let currentFV   = data["asset"]["res_"+fieldName];
            currentFV = fieldName=="res_comment" ? data["asset"]["res_comment"] : currentFV;


            let validSettings   = (fieldName in tempData["valdStgs"]) ? tempData["valdStgs"][fieldName] : tempData["valdStgs"]["default"];
            let vldtType        = ("type" in validSettings) ? validSettings["type"] : tempData["valdStgs"]["default"]["type"];

            currentFV = vldtType=="date" ? fnCleanDate(currentFV) : currentFV
            hasBeenChanged = fnCompare(originalFV, currentFV, vldtType)

            $(this).val(currentFV);

            if(!hasBeenChanged){
                $(this).css("background-color",colGreen)
            }
        })     
        
    }

    $(".txy").change(function(){
        fnTxyEdit($(this))
    })
    $(".txy").keyup(function(){// Event fires when field is edited
        fnTxyEdit($(this))
    })

    function fnTxyEdit(thisElement){
        let fieldName       = thisElement.data("name");
        let valOriginal     = data["asset"][fieldName];
        let valCurrent      = data["asset"]["res_"+fieldName];
        let valEntered      = thisElement.val();
        if(fieldName=="res_comment"){
            valCurrent      = data["asset"]["res_comment"];
        }else{
            valEntered      = valEntered.toUpperCase()
        } 
        let validSettings   = (fieldName in tempData["valdStgs"]) ? tempData["valdStgs"][fieldName] : tempData["valdStgs"]["default"];
        let vldtType        = ("type" in validSettings) ? validSettings["type"] : tempData["valdStgs"]["default"]["type"];
        let vldtMaxLen      = ("maxlen" in validSettings) ? validSettings["maxlen"] : tempData["valdStgs"]["default"]["maxlen"];
        let vldtMaxNum      = ("maxnum" in validSettings) ? validSettings["maxnum"] : tempData["valdStgs"]["default"]["maxnum"];

        if (!tempData["validationNote"+fieldName]){//Initiate a note variable in the temp array
            tempData["validationNote"+fieldName] = true
            thisElement.after("<p id='validationNote"+fieldName+"' class='text-danger'></p>");
        }
        $("#validationNote"+fieldName).hide();

        validity = fnValidate(valEntered, vldtType, vldtMaxLen, vldtMaxNum);

        console.log(validity)

        thisElement.css("background-color","white")

        if(!validity["result"]){//Failed validation
            thisElement.css("background-color",colRed)
            $("#validationNote"+fieldName).text(validity["msg"]);
            $("#validationNote"+fieldName).show();
        }else{
            thisElement.css("background-color",colAmber)
            fieldName = fieldName=="res_comment" ? fieldName : "res_"+fieldName;
            $.post("api.php",{
                act: "save_AssetFieldValue",
                ass_id:     data["asset"]["ass_id"],
                fieldName:  fieldName,
                fieldValue: valEntered
            },
            function(res, status){
                let valConfirmed = res
                savedCorrectly = fnCompare(valEntered, valConfirmed, vldtType)

                if(savedCorrectly){//Saved successfully
                    data["asset"][fieldName] = valConfirmed
                    differsFromOrig = fnCompare(valOriginal, valConfirmed, vldtType)
                    if(differsFromOrig){// Value hasn't changed from the very original
                        thisElement.css("background-color","white")
                    }else{// Value has saved and is different from original
                        thisElement.css("background-color",colGreen)
                    }
                }else{
                    $("#validationNote"+fieldName).show();
                    $("#validationNote"+fieldName).text("This value has not been saved to the database");
                }
            });
        }
    }

    $(document).on('click', '.thumb_photo', function(){
    // $(".thumb_photo").click(function(){
        let filename = $(this).val();

        let btnPD = "	<div class='dropdown'> "
            btnPD+= "	    <button class='nav-link btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Delete</button>"
            btnPD+= "	    <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>"
            btnPD+= "	        <button class='dropdown-item btn_delete_photo' value='"+filename+"' data-dismiss='modal' >I'm sure</a>"
            btnPD+= "	    </div>"
            btnPD+= "	</div>"

        $("#imageFrame").html("<img src='images/"+filename+"' width='100%'>"+btnPD);
    });

    $(document).on('click', '.btn_delete_photo', function(){
        let filename = $(this).val();
        $.post("api.php",{
            act: "save_delete_photo",
            filename:  filename
        },
        function(res, status){
            fnGetImgGallery()
        });
    });


    $(".rcCat").click(function(){
        catSelection = $(this).val();
        noedit      = $(this).data("noedit");
        console.log("noedit")
        console.log(noedit)
        if(catSelection=="ND10"){
            data["asset"]["res_reason_code"] = "ND10"
            fnSaveReasonCode("ND10", noedit)
        }else{
            tempData["tempReasonCat"] = catSelection
            setPage()
        }
        
    });

    $(".btnCancel").click(function(){
        tempData["tempReasonCat"] = null
        setPage(data)
    });

    $("#btnTemplate").click(function(){
        $(this).hide();
        $.post("api.php",{
            act:    "save_CreateTemplateAsset",
            ass_id: data["asset"]["ass_id"]

        },
        function(res, status){
            console.log(res)
            fnDo("get_templates","LoadTemplates",1)
            $("#menuAdd").effect( "bounce", {times:4}, 500 );
        }); 
        
    });

    $(".btnClearSure").click(function(){
        data["asset"]["res_reason_code"]= null
        tempData["tempReasonCat"]       = null
        $.post("api.php",{
            act:    "save_ResetAssetResults",
            ass_id: data["asset"]["ass_id"]
        },
        function(res, status){
            data = JSON.parse(res)
            console.log(data)
            fnInitialSetup()
            setPage()
            $(".txy").css("background-color","#e9ecef")
        });        
    });

    $(".rc_select").click(function(){
        rcSelection = $(this).val();
        fnSaveReasonCode(rcSelection)
    });

    function fnSaveReasonCode(new_reason_code, noedit){
        $.post("api.php",{
            act:        "save_AssetFieldValue",
            ass_id:     data["asset"]["ass_id"],
            fieldName:  "res_reason_code",
            fieldValue: new_reason_code
        },
        function(confirmedFV, status){
            // console.log("new_reason_code:"+new_reason_code)
            console.log("confirmedFV:"+confirmedFV)
            if(new_reason_code==confirmedFV){
                // console.log("Saved successfully")
                data["asset"]["res_reason_code"] = new_reason_code
                $(".txy").css("background-color","white")
                if (noedit){
                    window.location.href = "10_stk.php";
                }else{
                    setPage()
                }
            }
        });
    }

    function setPage(){
        $(".rcCat").hide();
        $(".btnCancel").hide();
        $(".btnClear").hide();
        $("#areaRCs").hide();
        $("#areaInputs").hide();
        $(".rc_option").hide();
        $("#btnTemplate").hide();
        $(".btnDeleteFF").hide();
        $(".btnCamera").hide();
        $("#res_reason_code").text("");
        let res_reason_code = data["asset"]["res_reason_code"];
        let rc_details;
        for (let rc_no in data["reasoncodes"]){
            rc_details =  data["reasoncodes"][rc_no]["res_reason_code"]==res_reason_code ? data["reasoncodes"][rc_no]: rc_details;
        }
        if(res_reason_code){// Asset is finished!
            $("#res_reason_code").text(res_reason_code+" - "+tempData["arrRC"][res_reason_code]);
            $(".btnClear").show();
            $("#areaInputs").show();
            $(".txy").prop('disabled', false);
            $(".btnCamera").show();
            // if(res_reason_code.substring(0,2)=="FF"){
            if(rc_details["rc_section"]=="FF"){
                $("#btnTemplate").show();
                $(".btnClear").hide();
                $(".btnDeleteFF").show();
            }else if(res_reason_code=="AF20"&&data["asset"]["genesis_cat"]=="Added from RR"){
                $(".btnClear").hide();
                $(".btnDeleteFF").show();
                $("#tags").focus();

            }else if(res_reason_code=="ND10"){
                $("#tags").focus();
            }
        }else if(tempData["tempReasonCat"]=="notfound"){//Select a not found reason code
            $(".btnCancel").show();
            $("#areaRCs").show();
            $(".rc_sectionNF").show();
        }else if(tempData["tempReasonCat"]=="error"){//Select an error reason code
            $(".btnCancel").show();
            $("#areaRCs").show();
            $(".rc_sectionERR").show();
        }else{//his asset has not been assessed
            $(".txy").prop('disabled', true);
            $("#ta_comment").prop('disabled', false);
            // $(".txy").css("background-color","#e9ecef")
            $(".rcCat").show();
            $("#areaInputs").show();
        }
    }


    fnInitialSetup()
    setPage(data)
    fnGetImgGallery()
});
</script>

<style>
.hdz{
    display:none
}
.txy{
    text-transform: uppercase
}
.btnCamera{
    margin-top:40px;
}
</style>


<br><br>

<div class='container-fluid' id="asset_page">
	<div class='row'>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar'><nav class='nav flex-column'><span class='assStatus'></span></nav></div>
		<div class='col-10'>
            <h2>

                Asset:<span class='tf' data-name='Asset'></span>-<span class='tf' data-name='Subnumber'></span>: 
                <span class='tf' data-name='AssetDesc1'></span> (<span class='tf' data-name='AssetDesc2'></span>)
            </h2>
            <p><span class='tf' id='res_reason_code' data-name='res_reason_code'></p>
		</div>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar'><nav class='nav flex-column'><span class='assStatus'></span></nav></div>
	</div>

	<div class='row'>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar'  >
			<nav class='nav flex-column'>
                <span class='btnTrough'>
                    <button type='button' value='ND10'   class='rcCat nav-link btn hdz' style='background-color:#78e090!important;display:none'>Sighted<br>Edit</button><br>
                    <button type='button' value='ND10' data-noedit="1"  class='rcCat nav-link btn hdz' style='background-color:#78e090!important;display:none'>Sighted<br>No Edit</button><br>
                    <button type='button' value='notfound'  class='rcCat nav-link btn btn-warning hdz'>Not<br>found</button><br>
                    <button type='button' value='error'     class='rcCat nav-link btn btn-primary hdz'>Asset<br>Error</button><br>
                    <div class='dropdown btnClear hdz'>
                        <button class='nav-link btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Clear</button>
                        <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                            <button type='button' class='dropdown-item bg-danger text-light btnClearSure'>I'm sure</button>
                        </div>
                    </div>
                    <button type='button' class='btn btn-danger btnCancel hdz'>Cancel</button>
                    <div class='dropdown btnDeleteFF hdz'>
                        <button class='nav-link btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Delete</button>
                        <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                            <a href='05_action.php?act=save_delete_first_found&ass_id=<?=$ass_id?>' class='dropdown-item bg-danger text-light '>I'm sure</a>
                        </div>
                    </div>

                    <a href='13_camera.php?ass_id=<?=$ass_id?>' class='btn btn-secondary text-center btnCamera'><span class='octicon octicon-device-camera' style='font-size:30px'></span></a>

                </span>
            </nav>
<!-- $btn_deleteff $btn_camera -->

                


		</div>
		<div class='col-10'>
            <div id="areaRCs"></div>

			<span id='areaInputs'>
				<div class='row'>
					<div class='col-4'>
                        <div class="form-group"><label>Asset Description</label>
                            <input type="text" class="form-control txy" data-name="AssetDesc1" data-vld="string">
                        </div>
                        <div class="form-group"><label>Asset Description 2</label>
                            <input type="text" class="form-control txy" data-name="AssetDesc2" data-vld="string">
                        </div>
						<div class="form-group"><label>Asset Main No Text</label>
                            <input type="text" class="form-control txy" data-name="AssetMainNoText" data-vld="string">
                        </div>
						<div class="form-group"><label>Inventory</label>
                            <input type="text" class="form-control txy" data-name="Inventory" data-vld="string">
                        </div>
						<div class="form-group"><label>InventNo</label>
                            <input type="text" class="form-control txy" data-name="InventNo" data-vld="string">
                        </div>
						<div class="form-group"><label>Manufacturer</label>
                            <input type="text" class="form-control txy" data-name="Mfr" data-vld="string">
                        </div>
					</div>
					<div class='col-2'>
						<div class="form-group"><label>Serial No</label>
                            <input type="text" class="form-control txy" data-name="SNo" data-vld="string">
                        </div>
						<div class="form-group"><label>Location</label>
                            <input type="text" class="form-control txy" data-name="Location" data-vld="string">
                        </div>
						<div class="form-group"><label>Level/Room</label>
                            <input type="text" class="form-control txy" data-name="Room" data-vld="string">
                        </div>
						<div class="form-group"><label>State</label>
                            <input type="text" class="form-control txy" data-name="State" data-vld="string">
                        </div>
						<div class="form-group"><label>latitude</label>
                            <input type="text" class="form-control txy" data-name="latitude" data-vld="string">
                        </div>
						<div class="form-group"><label>longitude</label>
                            <input type="text" class="form-control txy" data-name="longitude" data-vld="string">
                        </div>
					</div>
					<div class='col-2'>
						<div class="form-group"><label>Class</label>
                            <input type="text" class="form-control txy" data-name="Class" data-vld="string">
                        </div>
						<div class="form-group"><label>GrpCustod</label>
                            <input type="text" class="form-control txy" data-name="GrpCustod" data-vld="string">
                        </div>
						<div class="form-group"><label>CapDate</label>
                            <input type="text" class="form-control txy datepicker" data-name="CapDate" data-vld="date" readonly>
                        </div>
						<div class="form-group"><label>LastInv (YYYY-MM-DD)</label>
                            <input type="text" class="form-control txy datepicker" data-name="LastInv" data-vld="date" readonly>
                        </div>
						<div class="form-group"><label>DeactDate</label>
                            <input type="text" class="form-control txy datepicker" data-name="DeactDate" data-vld="date" readonly>
                        </div>
						<div class="form-group"><label>PlRetDate</label>
                            <input type="text" class="form-control txy datepicker" data-name="PlRetDate" data-vld="date" readonly>
                        </div>
					</div>
					<div class='col-2'>
						<div class="form-group"><label>Quantity</label>
                            <input type="text" class="form-control txy" data-name="Quantity" data-vld="string">
                        </div>
						<div class="form-group"><label>CurrentNBV</label>
                            <input type="text" class="form-control txy" data-name="CurrentNBV" data-vld="string">
                        </div>
						<div class="form-group"><label>AcqValue</label>
                            <input type="text" class="form-control txy" data-name="AcqValue" data-vld="string">
                        </div>
						<div class="form-group"><label>OrigValue</label>
                            <input type="text" class="form-control txy" data-name="OrigValue" data-vld="string">
                        </div>
						<div class="form-group"><label>ScrapVal</label>
                            <input type="text" class="form-control txy" data-name="ScrapVal" data-vld="string">
                        </div>
						<div class="form-group"><label>ValMethod</label>
                            <input type="text" class="form-control txy" data-name="ValMethod" data-vld="string">
                        </div>
					</div>
					<div class='col-2'>
						<div class="form-group"><label>CostCtr</label>
                            <input type="text" class="form-control txy" data-name="CostCtr" data-vld="string">
                        </div>
						<div class="form-group"><label>WBSElem</label>
                            <input type="text" class="form-control txy" data-name="WBSElem" data-vld="string">
                        </div>
						<div class="form-group"><label>Fund</label>
                            <input type="text" class="form-control txy" data-name="Fund" data-vld="string">
                        </div>
						<div class="form-group"><label>RspCCtr</label>
                            <input type="text" class="form-control txy" data-name="RspCCtr" data-vld="string">
                        </div>
						<div class="form-group"><label>RevOdep</label>
                            <input type="text" class="form-control txy" data-name="RevOdep" data-vld="string">
                        </div>
                        <br><button type='button' id='btnTemplate' class='btn btn-outline-dark' data-toggle='modal' data-target='#modal_copy'>Add to template</button>
					</div>
				</div>

                
                <div class='row'>
                    <div class='col-12'>
                        <div class='form-group'>
                            <h2>Images</h2>
                            <div id='areaImgGallery'></div>
                        </div>
                    </div>
                </div>



				<div class='row'>
					<div class='col-12'>
						<div class="form-group"><h2>Comments</h2>
							<textarea class="form-control txy" id='ta_comment'  data-name="res_comment" rows='5'></textarea>
						</div>
					</div>
				</div>
			</span>
		</div>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar text-right'  >
			<nav class='nav flex-column'>
                <span class='btnTrough'>
                    <button type='button' value='ND10'   class='rcCat nav-link btn hdz' style='background-color:#78e090!important;display:none'>Sighted<br>Edit</button><br>
                    <button type='button' value='ND10' data-noedit="1"  class='rcCat nav-link btn hdz' style='background-color:#78e090!important;display:none'>Sighted<br>No Edit</button><br>
                    <button type='button' value='notfound'  class='rcCat nav-link btn btn-warning hdz'>Not<br>found</button><br>
                    <button type='button' value='error'     class='rcCat nav-link btn btn-primary hdz'>Asset<br>Error</button><br>
                    <div class='dropdown btnClear hdz'>
                        <button class='nav-link btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Clear</button>
                        <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                            <button type='button' class='dropdown-item bg-danger text-light btnClearSure'>I'm sure</button>
                        </div>
                    </div>
                    <button type='button' class='btn btn-danger btnCancel hdz'>Cancel</button>
                    <div class='dropdown btnDeleteFF hdz mr-0 float-right'>
                        <button class='nav-link btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Delete</button>
                        <div class='dropdown-menu dropdown-menu-right' aria-labelledby='dropdownMenuButton'>
                            <a href='05_action.php?act=save_delete_first_found&ass_id=<?=$ass_id?>' class='dropdown-item bg-danger text-light '>I'm sure</a>
                        </div>
                    </div>

                    <a href='13_camera.php?ass_id=<?=$ass_id?>' class='btn btn-secondary text-center btnCamera float-right'><span class='octicon octicon-device-camera' style='font-size:30px'></span></a>
                </span>
            </nav>
		</div>
	    



<!-- 
DPN export is called json_update
Add history
Add fix me portal - inherent in the install file - what about delete db?
Add merge
Add user login

The DPN upload process is working, but it isn't doing the supernumery things to clean up and finalise the upload
 -->







	</div><!-- End main page row -->
</div>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br>

<div class="modal" id="modal_show_pic" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Photo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">  
                <div id='imageFrame'></div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
            </div>
        </div>
    </div>
</div>








<?php include "04_footer.php"; ?>




