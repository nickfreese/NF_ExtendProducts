# NF ExtendProducts

 - This module provides an API endpoint for retreiving product data on the frontend.  
 - The attributes which it may return can be defined within the admin.



## Usage

Endpoint: `rest/default/V1/extendproducts/get`

API Parameters:

 - `match` defines an attribute to match for example SKU
 - `matchValue` defines a comman seperated list of the values to search for on the "match" attribute
 - `attr` a comma separated list of attributes to retreive.  These are limited to those defined on the admin config.
 - `frontValues` passing `1`, will convert attribute values to their frontend value.  This makes dropdown and multiselect attributes human readable.
 - `options` passing `1` renders all custom and bundle options data. It includes all required info to construct a complete add to cart form.



### JS module usage

```


require(['nfExtendProducts'], function(nfExtendProducts){


    window.nfExtendProducts.find({
        match:"sku",
        matchValue: ["24-WG080","MP01"],
        attr:["sku", "description"],
        frontValues: 1,
        options:1
    }, 
    function(data){
          console.log(data)
    });


  });

```

  



### Direct api integration with Jquery.

```
require([
    'jquery'
], function($){
    'use strict';

    $.ajax({
      url: window.location.protocol + "//" + window.location.hostname + "/"+ `rest/default/V1/extendproducts/get/?match=sku&matchValue=24-WG080,MP01&attr=sku,description&frontValues=1&options=1`,
      method: "GET",
  
      dataType: "json",
      success: function(data){

          var parsed = [];
          for(var i = 0; i < data.length;i++){
              parsed.push(JSON.parse(data[i]));
          }

          console.log(parsed);

      },
      error: function (data) {
        console.log(data);
      }

    });


});

```




