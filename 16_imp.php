<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<?php

// apply ajax calls to update db
// create split cats
// apply date picker
// status maker
// arming clear switch
// Clear breaks




$auto_storageID = $_GET["auto_storageID"];

$arrSample = array();
$sql = "SELECT * FROM smartdb.sm18_impairment WHERE auto_storageID = $auto_storageID";
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $auto_storageID     = $row['auto_storageID'];    
        $storageID          = $row['storageID'];
        $rowNo              = $row['rowNo'];
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];
        $WHOUSE_ID          = $row['WHOUSE_ID'];
        $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];
        $SC_ACCOUNT_TYPE    = $row['SC_ACCOUNT_TYPE'];
        $STOCK_CODE         = $row['STOCK_CODE'];
        $ITEM_NAME          = $row['ITEM_NAME'];
        $BIN_CODE           = $row['BIN_CODE'];
        $INVENT_CAT         = $row['INVENT_CAT'];
        $TRACKING_IND       = $row['TRACKING_IND'];
        $SOH                = $row['SOH'];
        $TRACKING_REFERENCE = $row['TRACKING_REFERENCE'];
        $STK_DESC           = $row['STK_DESC'];
        $sampleFlag         = $row['sampleFlag'];
        $res_create_date    = $row['res_create_date'];
        $res_update_user    = $row['res_update_user'];
        $findingID          = $row['findingID'];
        $res_comment        = $row['res_comment'];
        $res_unserv_date    = $row['res_unserv_date'];
        $LAST_MOD_DATE      = $row['LAST_MOD_DATE'];

        $arrSample[] = $row;
}}


if($findingID>0){  
    $getBackBtn = "<div class='text-center complete'><div class='dropdown'><button class='btn btn-outline-danger complete dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='dispBtnClear'>Delete</button><div class='dropdown-menu bg-danger' aria-labelledby='dropdownMenuButton'>
    <a class='dropdown-item bg-danger text-light' href='05_action.php?act=save_clear_msi_bin&auto_storageID=".$auto_storageID."&storageID=$storageID'>I'm sure</a></div></div></div>";
}else{
    $getBackBtn = "<div class='text-center complete'><button class='btn btn-outline-dark' id='btnClear'>Back</button></div>";  
}


if ($findingID==11){
    $splityTotal = 0;
    $maxSplity = 0;
    $sql = "SELECT auto_storageID, findingID AS splityResult, res_create_date, SOH AS splityCount, res_unserv_date AS splityDate FROM smartdb.sm18_impairment WHERE res_parent_storageID = $storageID";   
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) { 
            $splitty_auto_storageID     = $row['auto_storageID']; 
            if($splitty_auto_storageID>$maxSplity){
                $maxSplity = $splitty_auto_storageID;
            }
            $arrSample['splitys'][] = $row;
    }}
    $arrSample['maxSplity']     = $maxSplity;

}else{ 
    $arrSample['splitys']       = [];
    $arrSample['maxSplity']     = 0;
}
 

$sql = "SELECT * FROM smartdb.sm19_result_cats";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $arrSample['rl'][] = $row;
}}

$arrSample = json_encode($arrSample);
?>











<script type="text/javascript">
let arS = '<?=$arrSample?>'
    arS = JSON.parse(arS);
let fID = arS[0]['findingID'];
let rl  = arS['rl'];
console.log(rl)

//Declare other global variables
let dispQtrack,dispStrack,complete;

$(document).ready(function() {
    
    //Copy the menu to the other side of the page
    let menuright = $('#menuleft').html();
    $('#menuright').html(menuright);

    //Create the splity option menu
    let rls = [1,2,3,4,5,6,7];
    let splityOptions = "";
    for (let key in rls) {
        splityOptions += "<option "
        splityOptions += "value='"+rls[key]+"'  "
        splityOptions += "class='list-group-item-"+arS['rl'][rls[key]-1]['color']+"'>"
        splityOptions += arS['rl'][rls[key]-1]['findingName']
        splityOptions += "</option>";
    }
    $('#splityResult').html(splityOptions);



    //Initialise the page
    setPage()

    function setPage(){  
        // console.log(arS[0]['findingID'])
        if (arS[0]['findingID']){
            complete        = true;
        }else{
            complete        = false;
        }

        // determine if the menu should be shown at all
        dispQtrack = complete ? false : arS[0]['TRACKING_IND']=="Q"
        dispStrack = complete ? false : arS[0]['TRACKING_IND']=="S"
        $('.dispQtrack').toggle(dispQtrack);
        $('.dispStrack').toggle(dispStrack);
        $('.complete').toggle(complete);

        if(arS[0]['findingID']){
            let fID = arS[0]['findingID']

            //Update the form values
            $('#findingID').val(arS[0]['findingID']);   

            // Publish display elements
            $('#areaDate').toggle(arS['rl'][fID-1]['reqDate']==1);
            $('#areaSplit').toggle(arS['rl'][fID-1]['reqSplit']==1);
            $("#resultSelection").html("<b>"+arS['rl'][fID-1]['findingName']+"</b>");
            $("#resultSelection").addClass('list-group-item-'+arS['rl'][fID-1]['color']);

            //Disprove submission validation
            $('#btnSubmit').show();

            //Check if date required, date is set
            let res_unserv_date = $('#res_unserv_date').val();
            if(arS['rl'][fID-1]['reqDate']==1&& res_unserv_date.length<=0){
                $('#btnSubmit').hide();
            }

            //Check if comment required, comment is set
            let res_comment = $('#res_comment').val();
            if(arS['rl'][fID-1]['reqComment']==1&& res_comment.length<=5){
                $('#btnSubmit').hide();
            }

            //Check if splity values all add up sufficient to submit.
            if(fID==11){


                let splityRows      = "";
                let totalSplitySOH  = 0;
                let splityHidden    = "";
                $('.splityRow').remove();
                for (let key in arS['splitys']) {
                    
                    let splityCount     = arS['splitys'][key]['splityCount']
                    let splityDate      = arS['splitys'][key]['splityDate']
                    let splityResult    = arS['splitys'][key]['splityResult']
                    let btnRemoveSplity = "<button type='button' class='btn btn-outline-dark btnRemoveSplity' value='"+key+"'><i class='fas fa-minus'></i></button>"


                    if(!splityDate){
                        splityDate = ''
                    }
                    
                    splityRows += "<tr class='splityRow'><td>"+splityCount+"</td><td>"+arS['rl'][splityResult-1]['findingName']+"</td><td>"+splityDate+"</td><td>"+btnRemoveSplity+"</td></tr>"

                    splityHidden+="<input type='hidden' name='splityRecord[]' value='"+key+"'>"
                    splityHidden+="<input type='hidden' name='splityCount["+key+"]' value='"+splityCount+"'>"
                    splityHidden+="<input type='hidden' name='splityResult["+key+"]' value='"+splityResult+"'>"
                    splityHidden+="<input type='hidden' name='splityDate["+key+"]' value='"+splityDate+"'>"

                    totalSplitySOH += Number(splityCount);
                }
                $('#splityTable tr:last').before(splityRows)
                $('#splityTotal').text(totalSplitySOH);
                $('#splityLanding').html(splityHidden)

                console.log('totalSplitySOH:'+totalSplitySOH);
                console.log('SOH:'+arS[0]['SOH']);
                if(totalSplitySOH<arS[0]['SOH']){
                    $('#btnSubmit').hide();
                }else{
                    $('#btnSubmit').show();
                }
                
            }
        }else{
            $('#areaDate').toggle(false);
            $('#areaSplit').toggle(false);
            $("#resultSelection").html('&nbsp;');
            $("#resultSelection").removeClass('list-group-item-success');
            $("#resultSelection").removeClass('list-group-item-warning');
            $("#resultSelection").removeClass('list-group-item-danger');
        }
    }


    //Events
    $('.dispStrack').click(function(){
        arS[0]['findingID'] = $(this).val();
        setPage()
    });

    $('.dispQtrack').click(function(){
        arS[0]['findingID'] = $(this).val();
        setPage()
    });

    $(document).on('keyup', "#res_comment", function(){
        setPage()
    });

    $('body').on('click', '#btnClear', function() {
        arS[0]['findingID'] = false;
        setPage()
    });

    $(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
    $(".datepicker").change(function(){
        setPage()
    })






//SPLITY SECTION
//     validateSplity()
    $(document).on('change', ".splity", function(){
        validateSplity()
    });

    $(document).on('keyup', ".splity", function(){
        validateSplity()
    });

    
    // splityId=0;
    $("#addSplity").click(function(){
        arS['maxSplity']++;
        newMaxS             = arS['maxSplity'];
        let splityCount     = $('#splityCount').val();
        let splityResult    = $('#splityResult').val();
        let splityDate      = $('#splityDate').val();
        
        arS['splitys'][newMaxS] = {
            splityCount,
            splityResult,
            splityDate
        }
        setPage()


        $('#splityCount').val('');
        $('#splityResult').val(1);
        $('#splityDate').val('');
        $("#addSplity").prop('disabled', true);
    })

    $(document).on('click', ".btnRemoveSplity", function(){
        let splityId = $(this).val()
        delete arS['splitys'][splityId]
        setPage()
    });

    validateSplity()
    function validateSplity(){
        let splityCount     = $('#splityCount').val();
        let splityResult    = $('#splityResult').val();
        let splityDate      = $('#splityDate').val();
        $("#addSplity").prop('disabled', false);

        if(isNaN(splityCount)){
            $("#addSplity").prop('disabled', true);
        }
        if(splityCount<=0){
            $("#addSplity").prop('disabled', true);
        }

        if(splityResult){
            if(arS['rl'][splityResult-1]['reqDate']==1&& splityDate.length<=0){
                $("#addSplity").prop('disabled', true);
            }
        }
    }





//     function checkSplityAllGood(){
//         $('#btnSubmit').show()

//         if(splityTotal<SOH){
//             $('#btnSubmit').hide();
//         }
//         if(isNaN(splityTotal)){
//             $('#btnSubmit').hide();
//             splityTotal = 0
//         }
//         let res_comment = $('#res_comment').val();
//         if(resultOptions[resultSelection]['reqComment']&& res_comment.length<=5){
//             $('#btnSubmit').hide();
//         }

//     }







});
</script>


<style>
.list-group-item{
    margin-bottom:10px;
}
</style>



<br><br>

<div class='container-fluid'>

<div class='row'>
    <div class='col'>
        <h1 class='display-4'>Bin impairment</h1>
    </div>
</div>



<div class='row'>

    <div class='col-3 lead' id='menuleft'>
        


            

        <?=$getBackBtn?>
    

        <ul class="list-group list-group-flush text-center">

            <li class="list-group-item dispStrack"><b>Item sighted</b></li>
            <button class="list-group-item list-group-item-action list-group-item-success dispStrack" value='1'>Serviceable</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispStrack" value='2'>Unserviceable&nbsp;-&nbsp;with&nbsp;date</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispStrack" value='3'>Unserviceable&nbsp;-&nbsp;no&nbsp;date</button>

            <li class="list-group-item dispStrack"><b>Item not sighted, evidence provided</b></li>
            <button class="list-group-item list-group-item-action list-group-item-warning dispStrack" value='4'>Serviceable</button>
            <button class="list-group-item list-group-item-action list-group-item-warning dispStrack" value='5'>Unserviceable&nbsp;-&nbsp;with&nbsp;date</button>
            <button class="list-group-item list-group-item-action list-group-item-warning dispStrack" value='6'>Unserviceable&nbsp;-&nbsp;no&nbsp;date</button>

            <li class="list-group-item dispStrack"><b>No items found, no evidence provided</b></li>
            <button class="list-group-item list-group-item-action list-group-item-danger dispStrack" value='7'>Not in count</button>

            <li class="list-group-item dispStrack"><b>In progress</b></li>
            <button class="list-group-item list-group-item-action list-group-item-info dispStrack" value='13'>Come back to it later</button>



            <li class="list-group-item dispQtrack"><b>Sighted&nbsp;or&nbsp;found&nbsp;evidence&nbsp;of&nbsp;all&nbsp;items</b></li>
            <button class="list-group-item list-group-item-action list-group-item-success dispQtrack" value='8'>Serviceable</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispQtrack" value='9'>None&nbsp;serviceable&nbsp;-&nbsp;with&nbsp;date</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispQtrack" value='10'>None&nbsp;serviceable&nbsp;-&nbsp;no&nbsp;date</button>

            <li class="list-group-item dispQtrack"><b>Split&nbsp;category</b></li>
            <button class="list-group-item list-group-item-action list-group-item-warning dispQtrack" value='11'>One, some or all of the following:
                <br>-Not all items were found 
                <br>-Items were in different categories 
                <br>-Found more than original quantity
            </button>

            <li class="list-group-item dispQtrack"><b>No items found, no evidence provided</b></li>
            <button class="list-group-item list-group-item-action list-group-item-danger dispQtrack" value='12'>Not in count</button>

            <li class="list-group-item dispQtrack"><b>In progress</b></li>
            <button class="list-group-item list-group-item-action list-group-item-info dispQtrack" value='13'>Come back to it later</button>
        </ul>
    </div>

    <div class='col-6 lead'>

    <form action='05_action.php' method='POST'>
        <table class='table table-sm'>
            <tr><td colspan='2' id='resultSelection'>&nbsp;</td></tr>
            <tr><td><b>District</b></td><td><?=$DSTRCT_CODE?></td></tr>
            <tr><td><b>Warehouse</b></td><td><?=$WHOUSE_ID?></td></tr>
            <tr><td><b>SCA</b></td><td><?=$SUPPLY_CUST_ID?></td></tr>
            <tr><td><b>Bin</b></td><td><?=$BIN_CODE?></td></tr>
            <tr><td><b>SOH</b></td><td><?=$SOH?></td></tr>
            <tr><td nowrap><b>SC Account type</b></td><td><?=$SC_ACCOUNT_TYPE?></td></tr>
            <tr><td nowrap><b>Tracking indicator</b></td><td><?=$TRACKING_IND?></td></tr>
            <tr><td nowrap><b>Tracking reference</b></td><td><?=$TRACKING_REFERENCE?></td></tr>
            <tr><td nowrap><b>Last Mod Date</b></td><td><?=$LAST_MOD_DATE?></td></tr>
            <tr><td colspan='2' class='complete'><b>Comments</b><textarea class='form-control' rows='5' name='res_comment' id='res_comment'><?=$res_comment?></textarea></td></tr>
            <tr id='areaDate'><td><b>Date</b></td><td><input type='text' class='form-control datepicker' name='res_unserv_date' id='res_unserv_date' value='<?=$res_unserv_date?>' readonly></td></tr>
            <tr id='areaSplit'><td colspan='2'>
                <b>Split area</b><br>
                <table class='table' id='splityTable'>
                    <tr>
                        <td width='20%'>Count</td>
                        <td>Sighted</td>
                        <td>Date</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><input type='text' class='form-control splity' name='splityCount' id='splityCount'></td>
                        <td>
                            <select class='form-control splity' name='splityResult' id='splityResult'>
                                <option value='1'></option>
                            </select>
                        </td>
                        <td><input type='text' class='form-control datepicker splity' name='splityDate' id='splityDate' readonly></td>
                        <td><button type='button' class='btn btn-outline-dark float-right' id='addSplity'><i class='fas fa-plus'></i></button></td>
                    </tr>
                    <tr><td id='splityTotal'></td><td>Total</td><td></td><td></td></tr>
                </table>
            </td></tr>
            <tr><td colspan='2'>

                    <span id='splityLanding'></span>

                    <input type='hidden' name='act' value='save_msi_bin_stk'>
                    <input type='hidden' name='findingID' id='findingID' value='<?=$findingID?>'>
                    <input type='hidden' name='auto_storageID' value='<?=$auto_storageID?>'>
                    <input type='hidden' name='storageID' id='storageID' value='<?=$storageID?>'>
                    <input type='submit' id='btnSubmit' value='Save' class='btn btn-outline-dark float-right complete' >
            </td></tr>
        </table>
        </form>
    </div>

    
    <div class='col-3 lead' id='menuright'></div>

</div>


<?php include "04_footer.php"; ?>