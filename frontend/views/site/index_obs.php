<?php

/* @var $this yii\web\View */
use yii\helpers\Url;

$this->title = 'The Havoc';

$row = Yii::$app->params['row'];
$column = Yii::$app->params['column'];
$map_width = Yii::$app->params['map_width'];
$map_height = Yii::$app->params['map_height'];
$scene_width = Yii::$app->params['scene_width'];
$scene_height = Yii::$app->params['scene_height'];
$offset_x = Yii::$app->params['offset_x'];
$offset_y = Yii::$app->params['offset_y'];
?>
<!doctype html>
<head>
    <meta charset="utf-8">
    <title>Hello World</title>
    <link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
    <script src="https://code.createjs.com/easeljs-0.8.2.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
    // canvas
    var background;
    var main;

    // params
    var is_mech_ready;
    var is_team_ready;
    var is_ready;
    var is_start;
    var is_end;
    var resource;

    // canvas params
    var map_width;
    var map_height;
    var grid_width;
    var grid_height;


    function init () {
        background = new createjs.Stage("background");
        main = new createjs.Stage("main");
        map_width = 900;
        map_height = 900;
        grid_width = map_width/<?=$column?>;
        grid_height = map_height/<?=$row?>;

        checkStatus();
        drawGrids();
        drawCore(42);
    }
    function drawGrids () {
        var grids = new createjs.Container();
        for (var y=0; y<<?=$column?>; y++) {
            for (var x=0; x<<?=$row?>; x++) {
                var id = x+<?=$column?>*y+1;
                var squ = new createjs.Shape();
                var odd = id%2 ? 1 : 0;
                if (odd == 1) {
                    squ.graphics.beginFill("rgba(255, 0, 0, 0.05)");
                } else {
                    squ.graphics.beginFill("rgba(0, 0, 0, 0)");
                }
                squ.graphics.drawRect(0, 0, grid_width, grid_height);
                squ.x = x*grid_width;
                squ.y = y*grid_height;
                squ.name = id;
                squ.on("click", function(event) {
                    alert("id:"+this.name);
                    //clickGrid(this.name);
                });
                grids.addChild(squ);
            }
        }
        main.addChild(grids);
        main.update();
    }
    function drawCore (id) {
        var image = new Image();
        image.src = 'images/towers/icon_coretower.png';
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            var x = (id-1)%<?=$column?>;
            var y = Math.floor((id-1)/<?=$column?>)+1;
            bitmap.x = x*grid_width - image.width/2;
            bitmap.y = y*grid_height - image.height/2;
            bitmap.name = "core";
            background.addChild(bitmap);
            background.update();
        };
    }
    function checkStatus() {
        $.ajax({
            url: "<?= '../../api/web/index.php?r=round/check'; ?>",
            data: {
                key: getCookie('key'),
            },
            dataType : 'json',
            success: function(response) {
                if (response.success) {
                    setCookie("key", response.data.player.key, 7);
                    resource = response.data.roundTeamPlayer.resource;
                    is_mech_ready = response.data.round.is_mech_ready;
                    is_team_ready = 0;
                    is_ready = response.data.round.is_ready;
                    is_start = response.data.round.is_start;
                    is_end = response.data.round.is_end;
                } else {
                    is_mech_ready = 0;
                    is_team_ready = 0;
                    is_ready = 0;
                    is_start = 0;
                    is_end = 0;
                }
            }
        });
    }
    function clickGrid (id) {
        if (is_start && resource > 0) {
            $('.loader').show();
            $('.loader-mask').show();
            $.ajax({
                // method: "POST",
                url: "<?= '../../api/web/index.php?r=map/mark'; ?>",
                data: {
                    id: id,
                    key: getCookie('key'),
                },
                dataType : 'json',
                success: function(response) {
                    $('.loader').hide();
                    $('.loader-mask').hide();
                    if (response.success) {
                        setCookie("key", response.data.player.key, 7);
                        setCookie("resource", response.data.player.current_resource);
                        markMyTower(id);
                        drawText();
                    }
                }
            });
        }
    }
    // Hide method from for-in loops
    Object.defineProperty(Array.prototype, "equals", {enumerable: false});
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length,c.length);
            }
        }
        return "";
    }
    </script>
</head>
<body onload="init();">
    <div id="game">
        <canvas class="scene" id="background"></canvas>
        <canvas class="map" id="main"></canvas>
    </div>
</body>

<style>
#game {
    width: <?=$scene_width?>px;
    height: <?=$scene_height?>px;
}
.grid {
    background-color:Transparent;
    background-repeat:no-repeat;
    border:none;
    cursor:pointer;
    overflow:hidden;
    outline:none;
    border:1px silver dashed;
}
.grid.odd {
    background-color: rgba(0, 0, 0, 0.05);
}
.grid.even {

}
.scene {
    position: absolute;
    left: 0px;
    top: 0px;
    width: <?=$scene_width?>px;
    height: <?=$scene_height?>px;
}
.map {
    position: absolute;
    left: <?=$offset_x?>px;
    top: <?=$offset_y?>px;
    width: <?=$map_width?>px;
    height: <?=$map_height?>px;
}
.clear-button {
    position: absolute;
    left: <?=$offset_x?>px;
    top: <?=$offset_y + $map_height?>px;
    width: <?=$map_width?>px;
    background-color: #999999;
    text-align: center;
    font-size: 30px;
}
.round-button {
    display:block;
    width:50px;
    height:50px;
    line-height:50px;
    border: 5px solid #f5f5f5;
    border-radius: 50%;
    color:#f5f5f5;
    text-align:center;
    text-decoration:none;
    background: #464646;
    box-shadow: 0 0 3px gray;
    font-size:20px;
    font-weight:bold;
}
.round-button:hover {
    color: #515151;
}
#restart-button:hover {
    color: <?=Yii::$app->params['white_color']?>;
}
#restart-button {
    position: absolute;
    left: 0px;
    top: 0;
    color: #999999;
    width: <?=$scene_width?>px;
    height: <?=$scene_height?>px;
    z-index: 3000;
}
#enter-button:hover {
    color: <?=Yii::$app->params['white_color']?>;
}
#enter-button {
    position: absolute;
    left: 0px;
    top: 0;
    color: #999999;
    width: <?=$scene_width?>px;
    height: <?=$scene_height?>px;
    z-index: 3000;
}
#background {
    position: fixed;
    z-index: -4000;
}
#main {
    position: fixed;
    z-index: 0;
}
#start {
    position: fixed;
    z-index: 2000;
}
#end {
    position: fixed;
    z-index: 2000;
}
body{font-family:<?=Yii::$app->params['font']?>;}
.loader {
    border: 16px solid #f3f3f3; /* Light grey */
    border-top: 16px solid <?=Yii::$app->params['main_color']?>;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
    position: fixed;
    top: <?=$scene_height/2-60?>px;
    left: <?=$scene_width/2-60?>px;
    z-index: 3000;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.loader-mask {
    position: fixed;
    z-index: 2000;
    width: <?=$scene_width?>px;
    height: <?=$scene_height?>px;
    background-color: rgba(255, 255, 255, 0.5);
}
</style>
