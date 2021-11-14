(function(){

	if (typeof window.nfExtendProductsRepo == "undefined"){
		window.nfExtendProductsRepo = {};
	}
	window.nfExtendProducts = {


        //window.location.protocol + "//" + window.location.hostname + "/"+ `rest/default/V1/extendproducts/get/?match=sku&matchValue=24-WG080&attr=sku,description,has_options,activity&frontValues=1&options=1`

        //PARAMS: match, matchValue, attr = ["sku"], frontValues = 1, options = 0

		find:function(params, callback){
			var _this = this;

			var url = _this.buildURL(params.match, params.matchValue, params.attr, params.frontValues, params.options);

            var urlHash = _this.hash(url);

            if (typeof window.nfExtendProductsRepo[urlHash] == "undefined") {

            	var xmlHttp = new XMLHttpRequest();
                	xmlHttp.onreadystatechange = function() { 
                	    if (xmlHttp.readyState == 4 && xmlHttp.status == 200){

                	    	var dataArr = JSON.parse(xmlHttp.responseText);
                	    	var parsed = [];
                	    	for(var i = 0; i < dataArr.length;i++){

                	    		parsed.push(JSON.parse(dataArr[i]));

                	    	}
                            
                            window.nfExtendProductsRepo[urlHash] = parsed;
                	        callback(window.nfExtendProductsRepo[urlHash]);
                	    }
                	}
                	xmlHttp.open("GET", url, true); // true for asynchronous 
                	xmlHttp.setRequestHeader('Accept', "application/json");
                	xmlHttp.send(null);

            } else {
            	callback(window.nfExtendProductsRepo[urlHash]);
            }




		},


		buildURL: function(match, matchValue, attr, frontValues, options) {

			var _this = this;
            
            return window.location.protocol + "//" + window.location.hostname + "/"+ `rest/default/V1/extendproducts/get/?match=`+ match + `&matchValue=`+matchValue.join(',')+`&attr=`+attr.join(',')+`&frontValues=`+frontValues+`&options=`+options;

		},


		hash: function(str) {
    		var hash = 0;
    		if (str.length == 0) {
    		    return hash;
    		}
    		for (var i = 0; i < str.length; i++) {
    		    var char = str.charCodeAt(i);
    		    hash = ((hash<<5)-hash)+char;
    		    hash = hash & hash; // Convert to 32bit integer
    		}
    		return hash;
		}

	}

	return window.nfExtendProducts;
})()

