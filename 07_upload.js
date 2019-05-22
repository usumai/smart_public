let elem = document.getElementById("myBar");   
let width = 1;
let count_total_progress = 0;

function readFile(input) {
  let file 		= input.files[0];
  let reader 	= new FileReader();
  reader.readAsText(file);

	reader.onload = function() {
		let obj = JSON.parse(reader.result);
		let assets 			= obj['import']['results'];
		let count_total 	= obj['import']['count_total'];
		let total_records 	= 4;
		let fractions 		= 100/(total_records);
		elem.style.width = fractions + '%';
		console.log(fractions);


		for (let key in assets) {
			if (assets.hasOwnProperty(key)) {
				let asset = assets[key];
				let asset_clean='';
				for (let assfkey in asset) {
					assfval = asset[assfkey]
					if(assfkey=="ass_id"||assfkey=="end"){

					}else{
						if (assfval==null) {// We replace actual null with the word null for sql purposes
							asset_clean += "null,";
						}else if(assfval=='0000-00-00 00:00:00'){// We replace zero dates with the word null for sql purposes
							asset_clean += "null,";
						}else{
							asset_clean += "'"+asset[assfkey]+"',";
						}
					}
				}
				let assetvalues = asset_clean.substr(0, asset_clean.length-1);
				let assetkeys = Object.keys(asset);
				assetkeys.shift();
				assetkeys.pop();
				let sql_save = "INSERT INTO smartdb.sm14_ass ("+assetkeys+") VALUES ("+assetvalues+");";
				let data = new FormData();
				data.append("sql_save", sql_save);

				fetch("05_action.php?act=upload_file", {
					method: "POST",
        			body: data
				})
				.then(function(res){ 
					return res.text();
				})
				.then(function(res){ 
					console.log(res);
					if(res=="success"){
						width = width+fractions; 
						elem.style.width = width + '%';
						console.log("updating progress bar"+width);
					}
					count_total_progress++;
					if (count_total_progress>=total_records) {
						// window.location.href = "05_action.php?act=save_finish_upload";
					}
				})

			}
		}


	};

  reader.onerror = function() {
    console.log(reader.error);
  };

}