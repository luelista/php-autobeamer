<?php

$ip = $_SERVER["REMOTE_ADDR"];
$prefix = "config.";
$conffile = $prefix.$ip;
if (!file_exists($conffile)) $conffile = $prefix."default";

$tab = explode("\n", file_get_contents($conffile));
$params = array();
$rows = array();
foreach($tab as $line) {
    $line=trim($line);
    if ($line=="") continue;
    if ($line{0}=="#") {
        list($key,$val)=explode(":",$line);
        $params[substr($key,1)] = $val;
    } else {
        list($timeout, $url) = explode(";", trim($line));
        $rows[] = ["timeout" => $timeout, "url" => $url];
    }
}

if (isset($_GET["checkmod"])) {
    if (file_exists("debugfile")) $params["debug"] = file_get_contents("debugfile");
    if(!isset($params["debug"]))$params["debug"]=0;
    if (filemtime("checkfile") > intval($_GET["checkmod"])) {
         die("{'modified':1,'debug':$params[debug]}");
    } else {
         die("{'modified':0,'debug':$params[debug]}");
    }
}

?><!doctype html>
<html><head>

<title>BEAMER - <?= $params['title']?> </title>
<style>
html,body{margin:0;padding:0;height:100%;}
iframe{width:100%;height:100%;border:0;}
.progressBar {
    position: fixed; top: 0; left: 0; width: 100%; height: 9px; background: #0091EA; z-index: 101;
}
@keyframes slide {
    0% { width: 0%; }
    100% { width: 100%; }
}
@-webkit-keyframes slide {
    0% { width: 0%; }
    100% { width: 100%; }
}
#debug{position:absolute; bottom:0;right:0;background:green;color:white;font-size:90%;padding:5px 20px;}
#debug b{font-size:170%;}
</style>
<script>
var index=0;
var remaining=0;
var iframes;
function nextframe() {
    for(var i=0;i<iframes.length;i++) iframes[i].style.display = (index!=i)?"none":"block";
    remaining = iframes[index].getAttribute("data-timeout");
    setTimeout(nextframe, 1000*remaining);
    index = (index+1) % iframes.length;
    var pb = document.getElementById("progressBar");
    var pb2 = pb.cloneNode(true);
    pb.parentNode.replaceChild(pb2, pb);
    pb2.style.animation = "slide "+remaining+"s linear forwards";
    pb2.style.webkitAnimation = "slide "+remaining+"s linear forwards";
}
window.onload=function(){
    iframes = document.getElementsByTagName("iframe");
    if (iframes.length > 1)
        nextframe();
}
setInterval(function(){
    document.getElementById("debug").style.display="none";
    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            console.log(xmlhttp.responseText);
            var data = eval("("+xmlhttp.responseText+")");
            if (data.modified) location=location;
            if (data.debug) document.getElementById("debug").style.display="block";
        }
    }
    xmlhttp.open("GET","?checkmod="+<?=filemtime("checkfile")?>,true);
    xmlhttp.send();
},10000);
</script>
</head>
<body>
<div class="progressBar" id="progressBar"></div>
<div id="debug">
<b><?= $params['title']?></b> &bull; <?= $ip ?> &bull; <?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?> &bull; github.com/max-weller/php-autobeamer</div>

<?php foreach($rows as $d):
       ?>
<iframe src="<?= $d['url'] ?>" data-timeout="<?= $d['timeout'] ?>"></iframe>

<?php endforeach; ?>

</body>
</html>