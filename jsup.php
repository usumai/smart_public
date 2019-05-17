<input type="file" onchange="readFile(this)">

<script>
function readFile(input) {
  let file = input.files[0];
  let reader = new FileReader();
  reader.readAsText(file);

	reader.onload = function() {
		let obj = JSON.parse(reader.result);
		let assets = obj['import']['results'];
		for (var key in assets) {
			if (assets.hasOwnProperty(key)) {
				var asset = assets[key];
				let asset_clean='';
				for (var assfkey in asset) {
					assfval = asset[assfkey]
					if(assfkey=="ass_id"){

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
				var assetvalues = asset_clean.substr(0, asset_clean.length-1);
				// console.log(assetvalues) 
				var assetkeys = Object.keys(asset);
				assetkeys.shift();
				// var assetvalues = Object.values(asset);
				let sql = "INSERT INTO smartdb.sm14_ass ("+assetkeys+") VALUES ("+assetvalues+");";
				console.log(sql);

				// let data = {element: "barium"};

				fetch("save.php", {
				  method: "POST", 
				  sql: sql
				})
				// .then(res => {
				// 	// console.log("Request complete! response:", res.body);
				// 	console.log(JSON.stringify( res ));
    //   				// return res.json();
				// })

			.then(function(res){ 
				// return res.json(); 
				console.log(res.json());
			})
			.then(function(data){ alert( JSON.stringify( data ) ) })
				// .then((data) =>{
			 //    });

			}
		}


	};

  reader.onerror = function() {
    console.log(reader.error);
  };

}




</script>