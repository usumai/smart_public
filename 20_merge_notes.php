<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php


?>


<script>

//     arR = JSON.parse(arR);
// console.log(arR);

let merge = {
    1:{ config:[1,0,1,0],act:"1",
        desc:"Neither stocktake records have a recorded result for this item"        
    },
    2:{ config:[1,0,1,1],act:"2",
        desc:"Both stocktake records have this item, but only stocktake 2 has a result for it"
    },
    3:{ config:[1,0,0,1],act:"NA",
        desc:"One stocktake has a storage record of this, but the other stocktake doesn't, it would imply that these two stocktakes are not the same, we also wouldn't get a match for these two records."
    },
    4:{ config:[1,0,0,0],act:"NA",
        desc:"One stocktake has a storage record of this, but the other stocktake doesn't, it would imply that these two stocktakes are not the same, we also wouldn't get a match for these two records."
    },
    5:{ config:[1,1,1,0],act:"1",
        desc:"Both stocktake records have this item, but only stocktake 1 has a result for it"
    },
    6:{ config:[1,1,1,1],act:"Compare",
        desc:"	Both stocktakes have a record of this, and both have a result for it. <br> E.1: Both fingerprints are the same = Use stocktake 1 (Match was made in a previous merge)<br> E.2: Fingerprints are different = Offer the user the chance to deconflict"
    },
    7:{ config:[1,1,0,1],act:"NA",
        desc:"One stocktake has a storage record of this, but the other stocktake doesn't, it would imply that these two stocktakes are not the same, we also wouldn't get a match for these two records."
    },
    8:{ config:[1,1,0,0],act:"NA",
        desc:"One stocktake has a storage record of this, but the other stocktake doesn't, it would imply that these two stocktakes are not the same, we also wouldn't get a match for these two records."
    },
    9:{ config:[0,1,1,0],act:"NA",
        desc:"One stocktake has a storage record of this, but the other stocktake doesn't, it would imply that these two stocktakes are not the same, we also wouldn't get a match for these two records."
    },
    10:{ config:[0,1,1,1],act:"NA",
        desc:"One stocktake has a storage record of this, but the other stocktake doesn't, it would imply that these two stocktakes are not the same, we also wouldn't get a match for these two records."
    },
    11:{ config:[0,1,0,1],act:"1",
        desc:"Both stocktakes have a matching fingerprint. (They inherently match or else they wouldn't be paired)"
    },
    12:{ config:[0,1,0,0],act:"1",
        desc:"Stocktake one has a first found"
    },
    13:{ config:[0,0,1,0],act:"NA",
        desc:"One stocktake has a storage record of this, but the other stocktake doesn't, it would imply that these two stocktakes are not the same, we also wouldn't get a match for these two records."
    },
    14:{ config:[0,0,1,1],act:"NA",
        desc:"One stocktake has a storage record of this, but the other stocktake doesn't, it would imply that these two stocktakes are not the same, we also wouldn't get a match for these two records."
    },
    15:{ config:[0,0,0,1],act:"2",
        desc:"Stocktake two has a first found"
    },
    16:{ config:[0,0,0,0],act:"NA",
        desc:"Why are you even here?"
    }
}

let rws;
for (let ocm in merge) {

    score1 = merge[ocm]['config'][0] ? 1 : 0;
    score2 = merge[ocm]['config'][1] ? 2 : 0;
    score3 = merge[ocm]['config'][2] ? 4 : 0;
    score4 = merge[ocm]['config'][3] ? 8 : 0;
    score  = score1+score2+score3+score4;
    rws+="<tr>"
    rws+="<td>"+ocm+"</td>"
    rws+="<td>"+fnFmt(merge[ocm]['config'][0])+"</td>"
    rws+="<td>"+fnFmt(merge[ocm]['config'][1])+"</td>"
    rws+="<td>"+fnFmt(merge[ocm]['config'][2])+"</td>"
    rws+="<td>"+fnFmt(merge[ocm]['config'][3])+"</td>"
    rws+="<td>"+score+"</td>"
    rws+="<td>"+fnFmtRes(merge[ocm]['act'])+"</td>"
    rws+="<td>"+merge[ocm]['desc']+"</td>"
    rws+="</tr>"
}

function fnFmt(fv){
    if(fv=='1'){
        return "<span class='badge badge-success'>Yes</span>";
    }else if(fv=='0'){
        return "<span class='badge badge-danger'>No</span>";
    }
}

function fnFmtRes(fv){
    if(fv=='1'){
        return "<span class='badge badge-primary'>One</span>";
    }else if(fv=='2'){
        return "<span class='badge badge-info'>Two</span>";
    }else if(fv=='Compare'){
        return "<span class='badge badge-dark'>Compare</span>";
    }else{
        return fv;
    }
}

$(document).ready(function() {
    
    $('#mainTable').append(rws);
});
</script>



























<br><br><br>
<div class='row'>
    <div class='col'>
        <table class='table' id='mainTable'>
            <tr>
                <td></td>
                <td colspan='2'><span class='badge badge-primary'>Stocktake 1</span></td>
                <td colspan='2'><span class='badge badge-info'>Stocktake 2</span></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>StorageID exists</td>
                <td>Fingerprint exists</td>
                <td>StorageID exists</td>
                <td>Fingerprint exists</td>
                <td>Score</td>
                <td>Result</td>
                <td>Result</td>
            </tr>
            <tr>
                <td>Value</td>
                <td>1</td>
                <td>2</td>
                <td>4</td>
                <td>8</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
</div>


<?php include "04_footer.php"; ?>