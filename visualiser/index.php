<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml" itemscope itemtype="http://schema.org/Map">

<head>
<title>OII Network Visualisation Example</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Pragma" content="no-cache">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1,user-scalable=no" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
<link rel='stylesheet' media='screen and (max-height: 700px)' href='css/tablet.css' />

<!--[if IE]><script type="text/javascript" src="js/excanvas.js"></script><![endif]--> <!-- js/default.js -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
  <script src="js/sigma.min.js" type="text/javascript" language="javascript"></script>
  <script src="js/sigma.parseGexf.js" type="text/javascript" language="javascript"></script>
  <script src="js/jquery.fancybox.pack.js" type="text/javascript" language="javascript"></script>
  <script type="text/javascript" language="javascript">

var sigInst, canvas, $GP

function Search(a) {
    this.input = a.find("input[name=search]");
    this.state = a.find(".state");
    this.results = a.find(".results");
    this.exactMatch = !1;
    this.lastSearch = "";
    this.searching = !1;
    var b = this;
    this.input.focus(function () {
        var a = $(this);
        a.data("focus") || (a.data("focus", !0), a.removeClass("empty"));
        b.clean()
    });
    this.input.keydown(function (a) {
        if (13 == a.which) return b.state.addClass("searching"), b.search(b.input.val()), !1
    });
    this.state.click(function () {
        var a = b.input.val();
        b.searching && a == b.lastSearch ? b.close() : (b.state.addClass("searching"), b.search(a))
    });
    this.dom = a;
    this.close = function () {
        this.state.removeClass("searching");
        this.results.hide();
        this.searching = !1;
        nodeNormal()
    };
    this.clean = function () {
        this.results.empty().hide();
        this.state.removeClass("searching");
        this.input.val("")
    };
    this.search = function (a) {
        var b = !1,
            c = [],
            b = this.exactMatch ? ("^" + a + "$").toLowerCase() : a.toLowerCase(),
            g = RegExp(b);
        this.exactMatch = !1;
        this.searching = !0;
        this.lastSearch = a;
        this.results.empty();
        if (2 >= a.length) this.results.html("<i>You must search for a name with a minimum of 3 letters.</i>");
        else {
            sigInst.iterNodes(function (a) {
                g.test(a.label.toLowerCase()) && c.push({
                    id: a.id,
                    name: a.label
                })
            });
            c.length ? (b = !0, nodeActive(c[0].id)) : b = showCluster(a);
            a = ["<b>Search Results: </b>"];
            if (1 < c.length) for (var d = 0, h = c.length; d < h; d++) a.push('<a href="#' + c[d].name + '" onclick="nodeActive(\'' + c[d].id + "')\">" + c[d].name + "</a>");
            0 == c.length && !b && a.push("<i>No results found.</i>");
            1 < a.length && this.results.html(a.join(""));
           }
        if(c.length!=1) this.results.show();
        if(c.length==1) this.results.hide();   
    }
}

function Cluster(a) {
    this.cluster = a;
    this.display = !1;
    this.list = this.cluster.find(".list");
    this.list.empty();
    this.select = this.cluster.find(".select");
    this.select.click(function () {
        $GP.cluster.toggle()
    });
    this.toggle = function () {
        this.display ? this.hide() : this.show()
    };
    this.content = function (a) {
        this.list.html(a);
        this.list.find("a").click(function () {
            var a = $(this).attr("href").substr(1);
            showCluster(a)
        })
    };
    this.hide = function () {
        this.display = !1;
        this.list.hide();
        this.select.removeClass("close")
    };
    this.show = function () {
        this.display = !0;
        this.list.show();
        this.select.addClass("close")
    }
}
function showGroups(a) {
    a ? ($GP.intro.find("#showGroups").text("Hide groups"), $GP.bg.show(), $GP.bg2.hide(), $GP.showgroup = !0) : ($GP.intro.find("#showGroups").text("View Groups"), $GP.bg.hide(), $GP.bg2.show(), $GP.showgroup = !1)
}

function nodeNormal() {
    !0 != $GP.calculating && !1 != sigInst.detail && (showGroups(!1), $GP.calculating = !0, sigInst.detail = !0, $GP.info.delay(400).animate({width:'hide'},350),$GP.cluster.hide(), sigInst.iterEdges(function (a) {
        a.attr.color = !1;
        a.hidden = !1
    }), sigInst.iterNodes(function (a) {
        a.hidden = !1;
        a.attr.color = !1;
        a.attr.lineWidth = !1;
        a.attr.size = !1
    }), sigInst.draw(2, 2, 2, 2), sigInst.neighbors = {}, sigInst.active = !1, $GP.calculating = !1, window.location.hash = "")
}

function nodeActive(a) {
    sigInst.neighbors = {};
    sigInst.detail = !0;
    var b = sigInst._core.graph.nodesIndex[a];
    showGroups(!1);
    sigInst.iterEdges(function (b) {
        b.attr.lineWidth = !1;
        b.hidden = !0;
        if (a == b.source || a == b.target) sigInst.neighbors[a == b.target ? b.source : b.target] = {
            name: b.label,
            colour: b.color
        }, b.hidden = !1, b.attr.color = "rgba(0, 0, 0, 1)"
    });
    var f = [];
    sigInst.iterNodes(function (a) {
        a.hidden = !0;
        a.attr.lineWidth = !1;
        a.attr.color = a.color
    });
    var e = [],
        c = sigInst.neighbors,
        g;
    for (g in c) {
        var d = sigInst._core.graph.nodesIndex[g];
        d.hidden = !1;
        d.attr.lineWidth = !1;
        d.attr.color = c[g].colour;
        a != g && e.push({
            id: g,
            name: d.label,
            group: (c[g].name)? c[g].name:"",
            colour: c[g].colour
        })
    }
    e.sort(function (a, b) {
        var c = a.group.toLowerCase(),
            d = b.group.toLowerCase(),
            e = a.name.toLowerCase(),
            f = b.name.toLowerCase();
        return c != d ? c < d ? -1 : c > d ? 1 : 0 : e < f ? -1 : e > f ? 1 : 0
    });
    d = "";
    for (g in e) c = e[g], c.group != d && (d = c.group, f.push('<li class="cf" rel="' + c.colour + '"><div class=""></div><div class="">' + d + "</div></li>")), f.push('<li class="membership"><a href="#' + c.name + '" onmouseover="sigInst._core.plotter.drawHoverNode(sigInst._core.graph.nodesIndex[\'' + c.id + "'])\" onclick=\"nodeActive('" + c.id + '\')" onmouseout="sigInst.refresh()">' + c.name + "</a></li>");
    b.hidden = !1;
    b.attr.color = b.color;
    b.attr.lineWidth = 6;
    b.attr.strokeStyle = "#000000";
    sigInst.draw(2, 2, 2, 2);

    $GP.info_link.find("ul").html(f.join(""));
    $GP.info_link.find("li").each(function () {
        var a = $(this),
            b = a.attr("rel");
    });
    f = b.attr;
    if (f.attributes && 0 < f.attributes.length) {
        e = [];
        g = 0;
        for (c = f.attributes.length; g < c; g++) {
            var d = f.attributes[g].val,
                h = "";
            // switch (f.attributes[g].attr) {
            // case "biography":
			if (f.attributes[g].attr == "Image File") {
			 	// h = '<img src="' + g + '"><br/>'
			} else {
                h = '<span><strong>' + f.attributes[g].attr + ':</strong> ' + d + '</span><br/>'
			}
            // }
            e.push(h)
        }
    	$GP.info_name.html("<div><img src=\"https://graph.facebook.com/" + f.attributes[0].val + "/picture \" style=\"vertical-align:middle;max-height: 100px;max-width: 100px;\"> <span onmouseover=\"sigInst._core.plotter.drawHoverNode(sigInst._core.graph.nodesIndex['" + b.id + '\'])" onmouseout="sigInst.refresh()">' + f.attributes[1].val + "</span></div>");
        $GP.info_data.html(e.join("<br/>"))
    }
    $GP.info_data.show();
    $GP.info_p.html("Connections:");
    $GP.info.animate({width:'show'},350);
	$GP.info_donnees.hide();
	$GP.info_donnees.show();
    sigInst.active = a;
    window.location.hash = b.label;
}

function showCluster(a) {
    var b = sigInst.clusters[a];
    if (b && 0 < b.length) {
        showGroups(!1);
        sigInst.detail = !0;
        b.sort();
        sigInst.iterEdges(function (a) {
            a.hidden = !1;
            a.attr.lineWidth = !1;
            a.attr.color = !1
        });
        sigInst.iterNodes(function (a) {
            a.hidden = !0
        });
        for (var f = [], e = [], c = 0, g = b.length; c < g; c++) {
            var d = sigInst._core.graph.nodesIndex[b[c]];
            !0 == d.hidden && (e.push(b[c]), d.hidden = !1, d.attr.lineWidth = !1, d.attr.color = d.color, f.push('<li class="membership"><a href="#'+d.label+'" onmouseover="sigInst._core.plotter.drawHoverNode(sigInst._core.graph.nodesIndex[\'' + d.id + "'])\" onclick=\"nodeActive('" + d.id + '\')" onmouseout="sigInst.refresh()">' + d.label + "</a></li>"))
        }
        sigInst.clusters[a] = e;
        sigInst.draw(2, 2, 2, 2);
        $GP.info_name.html("<b>" + a + "</b>");
        $GP.info_data.hide();
        $GP.info_p.html("Group Members:");
        $GP.info_link.find("ul").html(f.join(""));
        $GP.info.animate({width:'show'},350);
        resize();
        $GP.search.clean();
		$GP.cluster.hide();
        return !0
    }
    return !1
}

function init() {
    var a = sigma.init(document.getElementById("sigma-canvas")).drawingProperties({
        defaultLabelColor: "#000",
        defaultLabelSize: 14,
        defaultLabelBGColor: "#ddd",
        defaultHoverLabelBGColor: "#002147",
        defaultLabelHoverColor: "#fff",
        labelThreshold: 10,
        defaultEdgeType: "curve",
        hoverFontStyle: "bold",
        fontStyle: "bold",
        activeFontStyle: "bold"
    }).graphProperties({
        minNodeSize: 1,
        maxNodeSize: 7,
        minEdgeSize: 0.2,
        maxEdgeSize: 0.5
    }).mouseProperties({
        minRatio: 0.75, // How far can we zoom out?
        maxRatio: 20, // How far can we zoom in?
    });
    sigInst = a;
    a.active = !1;
    a.neighbors = {};
    a.detail = !1;
    a.parseGexf("<?php echo "../output/".$_SERVER['QUERY_STRING']; ?>");

	var greyColor = '#ccc';
	 sigInst.bind('overnodes',function(event){
	   var nodes = event.content;
	   var neighbors = {};
	   sigInst.iterEdges(function(e){
	     if(nodes.indexOf(e.source)<0 && nodes.indexOf(e.target)<0){
	       if(!e.attr['grey']){
	         e.attr['true_color'] = e.color;
	         e.color = greyColor;
	         e.attr['grey'] = 1;
	       }
	     }else{
	       e.color = e.attr['grey'] ? e.attr['true_color'] : e.color;
	       e.attr['grey'] = 0;

	       neighbors[e.source] = 1;
	       neighbors[e.target] = 1;
	     }
	   }).iterNodes(function(n){
	     if(!neighbors[n.id]){
	       if(!n.attr['grey']){
	         n.attr['true_color'] = n.color;
	         n.color = greyColor;
	         n.attr['grey'] = 1;
	       }
	     }else{
	       n.color = n.attr['grey'] ? n.attr['true_color'] : n.color;
	       n.attr['grey'] = 0;
	     }
	   }).draw(2,2,2);
	 }).bind('outnodes',function(){
	   sigInst.iterEdges(function(e){
	     e.color = e.attr['grey'] ? e.attr['true_color'] : e.color;
	     e.attr['grey'] = 0;
	   }).iterNodes(function(n){
	     n.color = n.attr['grey'] ? n.attr['true_color'] : n.color;
	     n.attr['grey'] = 0;
	   }).draw(2,2,2);
	 });


    gexf = sigmaInst = null;
    a.clusters = {};

	    /*a.iterEdges(
		function (b) {
	        	// a.clusters[b.label] || (a.clusters[b.label] = []);
	        	// a.clusters[b.label].push(b.source);
	        	// a.clusters[b.label].push(b.target)
	    	}
	);*/
	
	a.iterNodes(
		function (b) { //This is where we populate the array used for the group select box
			
			// alert(b.attr.attributes[0].val);
			// a.clusters[b.attr.attributes[0].val] || (a.clusters[b.attr.attributes[0].val] = []);
			// 				a.clusters[b.attr.attributes[0].val].push(b.label);
			a.clusters[b.color] || (a.clusters[b.color] = []);
			// a.clusters[b.color].push(b.label)
			a.clusters[b.color].push(b.id);//SAH: push id not label

		}
		
	);
	
    a.bind("upnodes", function (a) {
        nodeActive(a.content[0])
    });
    a.draw()
}

function resize() {
    var a = $("body");
    500 > a.width() ? ($GP.intro.hide(), $GP.mini.show()) : ($GP.intro.show(), $GP.mini.hide());
    $GP.minifier.hide();
    a = a.height() - 120;
}
$(document).ready(function () {
    var a = $;
    $GP = {
        calculating: !1,
        showgroup: !1
    };
    $GP.intro = a("#intro");
    $GP.minifier = $GP.intro.find("#minifier");
    $GP.mini = a("#minify");
    $GP.info = a("#attributepane");
    $GP.info_donnees = $GP.info.find(".nodeattributes");
    $GP.info_name = $GP.info.find(".name");
    $GP.info_link = $GP.info.find(".link");
    $GP.info_data = $GP.info.find(".data");
    $GP.info_close = $GP.info.find(".returntext");
    $GP.info_close2 = $GP.info.find(".close");
    $GP.info_p = $GP.info.find(".p");
    $GP.info_close.click(nodeNormal);
    $GP.info_close2.click(nodeNormal);
    $GP.form = a("#mainpanel").find("form");
    $GP.search = new Search($GP.form.find("#search"));
    $GP.cluster = new Cluster($GP.form.find("#attributeselect"));
    init();
    resize();
    $GP.bg = a(sigInst._core.domElements.bg);
    $GP.bg2 = a(sigInst._core.domElements.bg2);
    var a = [],
        b,x=1;
    for (b in sigInst.clusters) a.push('<div style="line-height:12px"><a href="#' + b + '"><div style="width:40px;height:10px;border:1px solid #fff;background:' + b + ';display:inline-block"></div> Group ' + (x++) + ' (' + sigInst.clusters[b].length + ' members)</a></div>');
    //a.sort();
    $GP.cluster.content(a.join(""));
    b = {
        minWidth: 400,
        maxWidth: 800,
        maxHeight: 600
    };//        minHeight: 300,
    $("a.fb").fancybox(b);
    $("#zoom").find("div.z").each(function () {
        var a = $(this),
            b = a.attr("rel");
        a.click(function () {
			if (b == "center") {
				sigInst.position(0,0,1).draw();
			} else {
		        var a = sigInst._core;
	            sigInst.zoomTo(a.domElements.nodes.width / 2, a.domElements.nodes.height / 2, a.mousecaptor.ratio * ("in" == b ? 1.5 : 0.5));		
			}

        })
    });
    $GP.mini.click(function () {
        $GP.mini.hide();
        $GP.intro.show();
        $GP.minifier.show()
    });
    $GP.minifier.click(function () {
        $GP.intro.hide();
        $GP.minifier.hide();
        $GP.mini.show()
    });
    $GP.intro.find("#showGroups").click(function () {
        !0 == $GP.showgroup ? showGroups(!1) : showGroups(!0)
    });
    a = window.location.hash.substr(1);
    if (0 < a.length) switch (a) {
    case "Groups":
        showGroups(!0);
        break;
    case "information":
        $.fancybox.open($("#information"), b);
        break;
    default:
        $GP.search.exactMatch = !0, $GP.search.search(a)
		$GP.search.clean();
    }
});
$(window).resize(resize);

</script>

  <link rel="stylesheet" type="text/css" href="js/jquery.fancybox.css"/>

</head>


<body>
  <div class="sigma-parent">
    <div class="sigma-expand" id="sigma-canvas"></div>
  </div>

<div id="mainpanel">


  <div class="col">
	<div id="maintitle"><div style="line-height:70px"><img src="../images/namegen.png" style="width:70px;float:left"><h1 >NameGenWeb</h1></div></div>
    <div id="title"><h2>Your Facebook Network</h2></div>
    <div id="titletext">
		This interactive visualisation shows your Facebook network from your perspective. Each circle (node) represents a Facebook friend, and each line represents a friendship. Use the controls on this page to explore your network.
	</div>
    <div class="info cf">
      <dl>
        <dt class="moreinformation"></dt>
        <dd class="line"><a href="#information" class="line fb">More about this visualisation</a></dd>
      </dl>
    </div>
<div id="legend">
	<div class="box">
		<h2>Legend:</h2>
		<dl>
		<dt class="node"></dt>
		<dd>A Facebook friend.</dd>
		<dt class="edge"></dt>
		<dd>A mutual friendship between two people.</dd>
		</dl>
	</div>
</div> 
   
    <div class="b1">
    <form>
      <div id="search" class="cf"><h2>Search:</h2>
        <input type="text" name="search" value="Search by name" class="empty"/><div class="state"></div>
        <div class="results"></div>
      </div>

      <div class="cf" id="attributeselect"><h2>Group Selector:</h2>
        <div class="select">Select Group</div>
	<div class="list cf"></div>
      </div>
    </form>
    </div>

  </div>



  <div id="information">
	<h2>Information about this Visualisation</h2>
	<p>This section has yet to be written. NameGenWeb is in active development, so please check back soon.</p>
	</div>

</div>
<div id="zoomcontainer">
	<div id="zoom">
  		<div class="z" rel="in"></div> <div class="z" rel="out"></div> <div class="z" rel="center"></div>
		<div style="clear:both"></div>
	</div>
	<div id="copyright">
		<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/"><img alt="Creative Commons License" style="border-width:0" src="images/CC.png" /></a></div>
	</div>
</div>

<div id="attributepane">
<div class="text">
	<div title="Close" class="left-close returntext"><div class="c cf"><span>Return to the full network</span></div></div>	
<div class="headertext">
	<span>Information Pane</span>
</div>	
  <div class="nodeattributes">
    <div class="name"></div>
	<div class="data"></div>
    <div class="p">Connections:</div>
    <div class="link">
      <ul>
      </ul>
    </div>
  </div>
	</div>
</div>

<div id="developercontainer">
	<a href="http://www.oii.ox.ac.uk" title="Oxford Internet Institute"><div id="oii"><span>OII</span></div></a>
	<a href="http://jisc.ac.uk" title="JISC"><div id="jisc"><span>JISC</span></div></a>	
</div>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-21293169-4']);
  _gaq.push(['_setDomainName', 'none']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
