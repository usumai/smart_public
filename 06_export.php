
<script>
"use strict";

fetch("exportapi.php?stkm_id=1", {
    method: "POST"
})
.then(function(res){ 
    return res.text();
})
.then(function(res){ 
    var jsonData = JSON.stringify(res);
    console.log(res);
    download(jsonData, 'json.txt', 'text/plain');

})    


function download(content, fileName, contentType) {
    var a = document.createElement("a");
    var file = new Blob([content], {type: contentType});
    a.href = URL.createObjectURL(file);
    a.download = fileName;
    a.click();
}
</script>