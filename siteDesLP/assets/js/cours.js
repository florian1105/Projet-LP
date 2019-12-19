const $ = require('jquery');
const jstree = require('jstree');

$('#arborescence').jstree({
	"core" : {
		"themes" : {
	    	"variant" : "large"
	  }
	},
    "plugins" : [ "wholerow" ]
  })

/* Activation des liens */
.bind("select_node.jstree", function (e, data) {
	var href = data.node.a_attr.href;
	if(href!="#")
		window.open(href, '_blank');
});