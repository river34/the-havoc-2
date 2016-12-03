<?php

/* @var $this yii\web\View */
use yii\helpers\Url;

$this->title = Yii::$app->params['title'];
$grid_width = Yii::$app->params['grid_width'];
$grid_height = Yii::$app->params['grid_height'];
$row = Yii::$app->params['row'];
$column = Yii::$app->params['column'];
$map_width = Yii::$app->params['map_width'];
$map_height = Yii::$app->params['map_height'];
$scene_width = Yii::$app->params['scene_width'];
$scene_height = Yii::$app->params['scene_height'];
$offset_x = Yii::$app->params['offset_x'];
$offset_y = Yii::$app->params['offset_y'];
$mark_empty = Yii::$app->params['mark_empty'];
$mark_default = Yii::$app->params['mark_default'];

header("Cache-Control: max-age=2592000");
header("Connection: Keep-alive");
?>
<body onload="init();">
    <!-- start of mobile game UI -->
    <canvas id="text" width="<?=$scene_width?>" height="<?=$scene_height?>" style="position:absolute;top:0;left:0;" hidden></canvas>
    <canvas id="login" width="<?=$scene_width?>" height="<?=$scene_height?>" style="position:absolute;top:0;left:0;" hidden></canvas>
    <canvas id="keycode" width="<?=$scene_width?>" height="<?=$scene_height?>" style="position:absolute;top:0;left:0;" hidden></canvas>
    <canvas id="leaderboard" width="<?=$scene_width?>" height="<?=$scene_height?>" style="position:absolute;top:0;left:0;" hidden></canvas>
    <canvas id="start" width="<?=$scene_width?>" height="<?=$scene_height?>" style="position:absolute;top:0;left:0;" hidden></canvas>
    <canvas id="end" width="<?=$scene_width?>" height="<?=$scene_height?>" style="position:absolute;top:0;left:0;" hidden></canvas>
    <canvas id="teamup" width="<?=$scene_width?>" height="<?=$scene_height?>" style="position:absolute;top:0;left:0;" hidden></canvas>
    <canvas id="info" width="<?=$scene_width?>" height="<?=$scene_height?>" style="position:absolute;top:0;left:0;" hidden></canvas>
    <canvas id="main" width="<?=$scene_width?>" height="<?=$scene_height?>" style="position:absolute;top:0;left:0;" hidden></canvas>
    <canvas id="background" width="<?=$scene_width?>" height="<?=$scene_height?>" style="position:absolute;top:0;left:0;"></canvas>
    <div class="loader" hidden></div>
    <div class="loader-mask" hidden></div>
    <div class="map" id="grids" hidden>
    <?php
    for ($y=0; $y<$column; $y++)
    {
        for ($x=0; $x<$row; $x++)
        {
            $id = $x*$column+$y+1;
            echo '<div id="grid_'.$id.'" class="grid" onclick="clickGrid('.$id.');" style="position:absolute;top:'.($x*$grid_width).'px;left:'.($y*$grid_height).'px;width:'.$grid_width.'px;height:'.$grid_height.'px;"></div>';
        }
    }
    ?>
    </div>
    <!-- end of mobile game UI -->

    <!-- start of login form -->
    <div id="login-form" class="input-form" hidden>
        <div id="login-name" class="input-field">
            <label class="title">Name</label>
            <input class="input" type="text" id="name" name="name" >
        </div>
        <div id="login-password" class="input-field">
            <label class="title">Password</label>
            <input class="input" type="password" id="password" name="password" >
        </div>
        <div id="login-submit" class="input-field">
            <input class="submit" type="submit" value="" onclick="loginSubmit();">
        </div>
        <div id="login-close" onclick="location.reload();"></div>
        <!-- <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" /> -->
    </div>
    <!-- end of login form -->

    <!-- start of secret form -->
    <div id="keycode-form" class="input-form" hidden>
        <div id="keycode-secret" class="input-field">
            <label class="title">Secret Code</label>
            <input class="input" type="text" id="secret" name="secret" >
        </div>
        <div id="keycode-submit" class="input-field">
            <input class="submit" type="submit" value="" onclick="keycodeSubmit();">
        </div>
        <div id="keycode-close" onclick="location.reload();"></div>
    </div>
    <!-- end of secret form -->

</body>

<style>
.grid {
    background-color:Transparent;
    background-repeat:no-repeat;
    border:none;
    cursor:pointer;
    overflow:hidden;
    outline:none;
    /* border:1px silver dashed; */
}
.marked {
    background-color: #fffccc;
}
.scene {
    position: absolute;;
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
#background {
    position: fixed;
    z-index: -4000;
}
#main {
    position: fixed;
    z-index: 0;
}
#teamup {
    position: fixed;
    z-index: 2000;
}
#info {
    position: fixed;
    z-index: 2000;
}
#text {
    position: fixed;
    z-index: -100;
}
#start {
    position: fixed;
    z-index: 2000;
}
#end {
    position: fixed;
    z-index: 2000;
}
#login {
    position: fixed;
    z-index: 2000;
}
#leaderboard {
    position: fixed;
    z-index: 2000;
}
#keycode {
    position: fixed;
    z-index: 2000;
}
.input-form {
    position: absolute;
    width: <?=$scene_width?>px;
    height: <?=$scene_height?>px;
    z-index: 2100;
    font-size: 38px;
}
.input-form .input-field {
    position: absolute;
    width: 700px;
    left: 360px;
}
.input-form .input-field .title {
    position: absolute;
    width: 150px;
    left: 0;
    display: none;
    color: <?=Yii::$app->params['main_text_color']?>;
}
.input-form .input-field .input {
    position: absolute;
    width: 400px;
    left: 200px;
    padding: 0 10px;
    border: none;
    background: rgba(0, 0, 0, 0);
    color: <?=Yii::$app->params['main_text_color']?>;
}
.input-form .input-field .submit {
    position: absolute;
    width: 600px;
    height: 140px;
    left: 0;
    border: none;
    background: rgba(0, 0, 0, 0);
    color: <?=Yii::$app->params['main_text_color']?>;
}
#login-form #login-name {
    position: absolute;
    top: 394px;
}
#login-form #login-password {
    position: absolute;
    top: 544px;
}
#login-form #login-submit {
    position: absolute;
    top: 660px;
}
#login-form #login-close {
    position: absolute;
    top: 0;
    left: 0;
    width: 300px;
    height: 150px;
    background-color: rgba(0, 0, 0, 0);
}
#keycode-form #keycode-secret {
    position: absolute;
    top: 485px;
}
#keycode-form #keycode-submit {
    position: absolute;
    top: 600px;
}
#keycode-form #keycode-close {
    position: absolute;
    top: 0;
    left: 0;
    width: 300px;
    height: 150px;
    background-color: rgba(0, 0, 0, 0);
}
#keycode-form .input-field .title {
    width: 200px;
}
#keycode-form .input-field .input {
    width: 400px;
    left: 50px;
}
*:focus {
    outline: none;
}
body {
    font-family:<?=Yii::$app->params['font']?>;
    background-color: black;
}
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
    background-color: rgba(0, 0, 0, 1);
}
.meter {
	height: 20px;  /* Can be anything */
	position: relative;
	background: #555;
	-moz-border-radius: 25px;
	-webkit-border-radius: 25px;
	border-radius: 25px;
	padding: 10px;
	box-shadow: inset 0 -1px 1px rgba(255,255,255,0.3);
}
.meter > span {
  display: block;
  height: 100%;
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
  border-top-left-radius: 20px;
  border-bottom-left-radius: 20px;
  background-color: rgb(43,194,83);
  background-image: linear-gradient(
    center bottom,
    rgb(43,194,83) 37%,
    rgb(84,240,84) 69%
  );
  box-shadow:
    inset 0 2px 9px  rgba(255,255,255,0.3),
    inset 0 -2px 6px rgba(0,0,0,0.4);
  position: relative;
  overflow: hidden;
}
#loading-bar {
    width: 300px;
    height: 300px;
    display:flex;
    align-items: center;
    z-index: 3000;
    position: absolute;
    top: <?=$offset_y + $map_height/2 - 150?>px;
    left: <?=$offset_x + $map_width/2 - 150?>px;
}
@keyframes barAnim {
    0%, 100% {
        height: 50px;
    }
    50% {
        height: 250px;
    }
}
.bar {
    width:50px;
    height:200px;
    display:inline-block;
    background-color:orange;
    margin:2px;
    animation: barAnim 1.3s infinite ease-in-out;
}
.bar1 { animation-duration: 1.2s; }
.bar2 { animation-duration: 1.8s; }
.bar3 { animation-duration: 1.5s; }
.bar4 { animation-duration: 2.1s; }
.bar5 { animation-duration: 1.6s; }
.bar6 { animation-duration: 1.1s; }
</style>
<script type="text/javascript">
    <!--//--><![CDATA[//><!--
        var images = new Array()
        function preload() {
            for (i = 0; i < preload.arguments.length; i++) {
                images[i] = new Image()
                images[i].src = preload.arguments[i]
            }
        }
        preload(
            "<?=Yii::$app->params['core']?>",
            "<?=Yii::$app->params['team_tower_0']?>",
            "<?=Yii::$app->params['team_tower_1']?>",
            "<?=Yii::$app->params['my_tower_0']?>",
            "<?=Yii::$app->params['my_tower_1']?>",
            "<?=Yii::$app->params['team_remain_0']?>",
            "<?=Yii::$app->params['team_remain_1']?>",
            "<?=Yii::$app->params['team_mech']?>",
            "<?=Yii::$app->params['team_0']?>",
            "<?=Yii::$app->params['team_1']?>",
            "<?=Yii::$app->params['select_0']?>",
            "<?=Yii::$app->params['select_0_un']?>",
            "<?=Yii::$app->params['select_1']?>",
            "<?=Yii::$app->params['select_1_un']?>",
            "<?=Yii::$app->params['start_image']?>",
            "<?=Yii::$app->params['teamup_image']?>",
            "<?=Yii::$app->params['bg_image']?>",
            "<?=Yii::$app->params['game_image_0']?>",
            "<?=Yii::$app->params['game_image_1']?>",
            "<?=Yii::$app->params['end_image']?>",
            "<?=Yii::$app->params['tutorial']?>",
            "<?=Yii::$app->params['keycode']?>",
            "<?=Yii::$app->params['leaderboard']?>",
            "<?=Yii::$app->params['login']?>",
            "<?=Yii::$app->params['core_bar']?>"
        )
    //--><!]]>
</script>
<script type="text/javascript">
// only send request when you get response
var not_able_to_request = 0;

// canvas
var text;
var background;
var main;
var teamup;
var info;
var start;
var end;
var login;
var keycode;

// params
var is_login;
var is_open;
var is_player_ready;
var is_player_in_team;
var is_mech_ready;
var is_team_ready;
var is_ready;
var is_start;
var is_end;
var is_win;
var player_id;
var resource;
var score;
var round_score;
var rank;
var ranks;
var team_id;
var round_id;
var teams;
var team_score_1;
var team_score_2;
var game_time;
var empty_slots;
var name;
var error;
var secret;
var check_secret;
var core_score;
var total_core_score;
var is_inspector;
var round_count;

// preloaded images
var team_tower_images = [];
var my_tower_images = [];
var colors = [];

// interval
var interval;

var check_status;

function init() {
    clearInterval(check_status);
    clearInterval(interval);
    $('#main').hide();

    showLoading();

    var image = new Image();
    image.src = "<?=Yii::$app->params['team_tower_0']?>";
    team_tower_images[0] = image;

    image = new Image();
    image.src = "<?=Yii::$app->params['team_tower_1']?>";
    team_tower_images[1] = image;

    image = new Image();
    image.src = "<?=Yii::$app->params['my_tower_0']?>";
    my_tower_images[0] = image;

    image = new Image();
    image.src = "<?=Yii::$app->params['my_tower_1']?>";
    my_tower_images[1] = image;

    colors[0] = "<?=Yii::$app->params['team_background_color_0']?>";
    colors[1] = "<?=Yii::$app->params['team_background_color_1']?>";

    init_draw();

    check_status = setInterval(function(){
        if (not_able_to_request > 0) {
            return;
        }
        not_able_to_request = 1;
        checkStatus();
    }, <?=Yii::$app->params['refresh_rate']?>);
}
function checkStatus () {
    // check if player is in game
    $.ajax({
        // method: "POST",
        url: "<?= '../../api/web/index.php?r=round/check'; ?>",
        data: {
            key: getCookie('key'),
        },
        dataType : 'json',
        success: function(response) {
            not_able_to_request = 0;
            hideLoading();
            if (response.success) {
                setCookie("key", response.data.player.key, 7);
                is_login = 1;
                is_open = response.data.is_open;
                is_player_ready = response.data.is_player_ready;
                is_player_in_team = response.data.is_player_in_team;
                is_mech_ready = response.data.is_mech_ready;
                is_team_ready = response.data.is_team_ready;
                is_ready = response.data.is_ready;
                is_start = response.data.is_start;
                is_end = response.data.is_end;
                is_win = response.data.is_win;
                rank = response.data.rank;
                team_score_1 = response.data.team_score_1;
                team_score_2 = response.data.team_score_2;
                team_counts = response.data.team_counts;
                round_score = response.data.round_score;
                score = response.data.score;
                team_id = response.data.team_id;
                teams = response.data.teams;
                empty_slots = response.data.empty_slots;
                empty_player_slots = response.data.empty_player_slots;
                is_inspector = response.data.is_inspector;
                if (is_player_ready == 0 && is_start) { // player is not in current game
                    player_id = response.data.player.id;
                    name = response.data.player.name;
                }
                if (is_player_ready && is_start) {
                    player_id = response.data.player.id;
                    name = response.data.player.name;
                    resource = response.data.roundTeamPlayer.resource;
                    score = response.data.player.score;
                    round_score = response.data.roundTeamPlayer.score;
                    round_id = response.data.round.id;
                    team_id = response.data.roundTeamPlayer.team_id;
                }
                if (is_player_ready && is_player_in_team == 0) {
                    player_id = response.data.player.id;
                    name = response.data.player.name;
                    teams = response.data.teams;
                    round_id = response.data.round.id;
                }
            } else {
                setCookie("key", 0);
                is_login = 1;
                is_open = 0;
                is_player_ready = 0;
                is_player_in_team = 0;
                is_mech_ready = 0;
                is_team_ready = 0;
                is_ready = 0;
                is_start = 0;
                is_end = 0;
                is_win = 0;
                name = "";

                if (response.data.error == 'not_login') {
                    is_login = 0;
                    loginGame();
                } else {
                    name = response.data.player.name;
                }
            }
            init_state();
        }
    });
}
function init_draw() {
    // load stage
    text = new createjs.Stage("text");
    main = new createjs.Stage("main");
    teamup = new createjs.Stage("teamup");
    info = new createjs.Stage("info");
    background = new createjs.Stage("background");
    start = new createjs.Stage("start");
    end = new createjs.Stage("end");
    login = new createjs.Stage("login");
    leaderboard = new createjs.Stage("leaderboard");
    keycode = new createjs.Stage("keycode");

    drawBackground();
    // drawText("<?=Yii::$app->params['title']?>");
}
function init_state() {
    hideScenes();

    // if (getCookie("start") == 1){
    //     startGame();
    //     return;
    // }
    //

    if (is_login == 0) {
        // login
        loginGame();
        return;
    }
    if (getCookie("end") == 1) {
        endGame();
        return;
    }
    if (getCookie("leaderboard") == 1) {
        getLeaderBoard();
        return;
    }
    if (getCookie("start") == 1){
        startGame();
        return;
    }
    // special cases for is_inspector account
    if (is_inspector && is_ready && is_start == 0) {
        startGameForInspector();
        return;
    }
    if (is_inspector && is_start && is_end == 0) {
        if (getCookie("start_time") == 0 || getCookie("end_time") == 0) {
            setCookie("start_time", new Date().getTime());
            setCookie("end_time", new Date().getTime() + 1000*60*3);
        }
        $("#grids").hide();
        // game starts
        clearInterval(check_status);
        clearInterval(interval);
        // drawInfo('enter');
        interval = setInterval(function(){
            var time = getCookie("end_time") - new Date().getTime();
            if (time < 0){
                time = 0;
            }
            game_time = Math.floor(time/1000/60) + ":" + Math.floor(time/1000%60);
            //game_time = Math.round(100 * (getCookie("end_time") - getCookie("start_time"))/1000/60)/100;
            if (not_able_to_request > 0) {
                return;
            }
            not_able_to_request = 1;
            updateMap();
        }, <?=Yii::$app->params['refresh_rate']?>);

        // load game scene
        showLoading();
        setTimeout(function(){
            $('#info').hide(); $('#main').show(); $('#grids').show(); hideLoading();
        }, <?=Yii::$app->params['load_scene_time']?>);
        drawGameBg();
    }

    if (is_inspector && is_start && is_end) {
        setCookie("is_started", 0);
        setCookie("start_time", 0);
        setCookie("end_time", 0);
        // game ends
        clearInterval(check_status);
        clearInterval(interval);
        endGameForInspector();
    }

    // normal cases
    if (is_open && is_player_ready == 0) {
        // join this round
        startGame();
        return;
    }
    if (is_open == 0 && is_player_ready == 0) {
        startGame();
        return;
    }
    if (is_player_ready && is_player_in_team == 0) {
        TeamUpGame();
        // drawText("<?=Yii::$app->params['title']?>", 'Join in a Team');
        setCookie("start_time", 0);
        setCookie("end_time", 0);
    }
    if (is_player_ready && is_player_in_team && is_team_ready == 0) {
        // wait for other teammates / other team to get ready
        drawInfo('tutorial');
        drawText("<?=Yii::$app->params['title']?>", 'Wait for players' + ' ... ' + empty_slots);
        setCookie("start_time", 0);
        setCookie("end_time", 0);
    }
    if (is_player_ready && is_player_in_team && is_team_ready && is_ready == 0){
        // wait for mech to get ready
        drawInfo('tutorial');
        drawText("<?=Yii::$app->params['title']?>", 'Wait for Mech');
        setCookie("start_time", 0);
        setCookie("end_time", 0);
    }
    if (is_player_ready && is_player_in_team && is_team_ready && is_ready && is_start == 0) {
        // wait for mech to get ready
        drawInfo('tutorial');
        drawText("<?=Yii::$app->params['title']?>", 'Wait for Mech');
        setCookie("start_time", 0);
        setCookie("end_time", 0);
    }
    if (is_player_ready && is_player_in_team && is_team_ready && is_ready && is_start && is_end == 0) {
        if (getCookie("start_time") == 0 || getCookie("end_time") == 0) {
            setCookie("start_time", new Date().getTime());
            setCookie("end_time", new Date().getTime() + 1000*60*3);
        }
        // game starts
        clearInterval(check_status);
        clearInterval(interval);
        // drawInfo('enter');
        interval = setInterval(function(){
            var time = getCookie("end_time") - new Date().getTime();
            if (time < 0){
                time = 0;
            }
            game_time = Math.floor(time/1000/60) + ":" + Math.floor(time/1000%60);
            //game_time = Math.round(100 * (getCookie("end_time") - getCookie("start_time"))/1000/60)/100;
            if (not_able_to_request > 0) {
                return;
            }
            not_able_to_request = 1;
            updateMap();
        }, <?=Yii::$app->params['refresh_rate']?>);

        // load game scene
        showLoading();
        setTimeout(function(){
            $('#info').hide(); $('#main').show(); $('#grids').show(); hideLoading();
        }, <?=Yii::$app->params['load_scene_time']?>);
        drawGameBg();
    }

    if (is_player_ready && is_player_in_team && is_team_ready && is_ready && is_start && is_end) {
        setCookie("is_started", 0);
        setCookie("start_time", 0);
        setCookie("end_time", 0);
        // game ends
        clearInterval(check_status);
        clearInterval(interval);
        endGame();
        // drawText("<?=Yii::$app->params['title']?>", 'Game Ends');
    }
}
function TeamUpGame() {
    hideScenes();

    if (getCookie('is_festival') && getCookie('team_id')) {
        selectTeam(getCookie('team_id'));
        return;
    }

    if (teamup.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['teamup_image']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            teamup.addChild(bitmap);
            teamup.setChildIndex(bitmap, 0);
            teamup.update();
        }
    }
    if (teams != null) {
        var odd;
        var color;
        for (var i=0; i<teams.length; i++) {
            var rect = teamup.getChildByName("team_"+teams[i]['id']);
            if (rect == null) {
                color = colors[i];
                rect = new createjs.Shape();
                rect.graphics.beginFill(color).drawRect(0, 0, <?=$scene_width?>/teams.length, <?=$scene_height/2?>);
                rect.x = <?=$scene_width/2?> - i*<?=$scene_width?>/teams.length;
                rect.y = <?=$scene_height/2?>;
                rect.alpha = 0.01;
                rect.name = "team_"+teams[i]['id'];
                rect.id = teams[i]['id'];
                teamup.addChild(rect);
            }
            if (teams[i]['is_ready'] == 0) {
                if (i == 0){
                    if (teamup.getChildByName("select_0") == null) {
                        var image_1 = new Image();
                        image_1.src = "<?=Yii::$app->params['select_0']?>";
                        image_1.onload = function() {
                            var bitmap = new createjs.Bitmap(image_1);
                            bitmap.name = "select_0";
                            bitmap.x = 580;
                            bitmap.y = 667;
                            teamup.addChild(bitmap);
                            teamup.update();
                        }
                        var text_1 = new createjs.Text(teams[i]['limit']-team_counts[i], '18px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                        text_1.x = 625;
                        text_1.y = 911;
                        text_1.textAlign = 'right';
                        teamup.addChild(text_1);
                        teamup.update();
                    }
                }
                else if (i == 1){
                    if (teamup.getChildByName("select_1") == null) {
                        var image_2 = new Image();
                        image_2.src = "<?=Yii::$app->params['select_1']?>";
                        image_2.onload = function() {
                            var bitmap = new createjs.Bitmap(image_2);
                            bitmap.name = "select_1";
                            bitmap.x = 0;
                            bitmap.y = 667;
                            teamup.addChild(bitmap);
                            teamup.update();
                        }
                        var text_2 = new createjs.Text(teams[i]['limit']-team_counts[i], '18px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                        text_2.x = 502;
                        text_2.y = 740;
                        teamup.addChild(text_2);
                        teamup.update();
                    }
                }
                rect.on("click", function(event) {
                    setCookie('team_id', this.id);
                    selectTeam(this.id);
                });
            } else {
                if (i == 0){
                    if (teamup.getChildByName("select_0_un") == null) {
                        var imag_3 = new Image();
                        imag_3.src = "<?=Yii::$app->params['select_0_un']?>";
                        imag_3.onload = function() {
                            var bitmap = new createjs.Bitmap(imag_3);
                            bitmap.name = "select_0_un";
                            bitmap.x = 580;
                            bitmap.y = 667;
                            teamup.addChild(bitmap);
                            teamup.update();
                        }
                        var text_3 = new createjs.Text(teams[i]['limit']-team_counts[i], '18px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                        text_3.x = 625;
                        text_3.y = 911;
                        text_3.textAlign = 'right';
                        teamup.addChild(text_3);
                        teamup.update();
                    }
                }
                else if (i == 1){
                    if (teamup.getChildByName("select_1_un") == null) {
                        var imag_4 = new Image();
                        imag_4.src = "<?=Yii::$app->params['select_1_un']?>";
                        imag_4.onload = function() {
                            var bitmap = new createjs.Bitmap(imag_4);
                            bitmap.name = "select_1_un";
                            bitmap.x = 0;
                            bitmap.y = 667;
                            teamup.addChild(bitmap);
                            teamup.update();
                        }
                        var text_4 = new createjs.Text(teams[i]['limit']-team_counts[i], '18px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                        text_4.x = 502;
                        text_4.y = 740;
                        teamup.addChild(text_4);
                        teamup.update();
                    }
                }
            }
            teamup.update();
        }
    }
    teamup.update();
    $('#teamup').show();
}
function drawInfo(index) {
    if (index == 'tutorial') {
        var cont = info.getChildByName("tutorial");
        if (cont == null) {
            var cont = new createjs.Container();
            cont.name = "tutorial";

            /*
            for (var i=0; i<4; i++) {
                var x = i%2;
                var y = Math.floor(i/2);

                if (cont.getChildByName("rect_"+i) == null) {
                    var rect = new createjs.Shape();
                    rect.graphics.beginStroke('#AAAAAA');
                    rect.graphics.moveTo(0, 0)
                    .lineTo(0, 200)
                    .lineTo(200, 200)
                    .lineTo(200, 0)
                    .lineTo(0, 0);
                    rect.graphics.setStrokeDash([50, 50], 0);
                    rect.x = <?=$scene_width/2?> -200 + x*200;
                    rect.y = <?=$scene_height/2?> -100 + y*200;
                    rect.name = "rect_"+i;
                    cont.addChild(rect);
                }

                if (cont.getChildByName("cir_"+i) == null) {
                    if (i!=1) {
                        var cir = new createjs.Shape();
                        cir.graphics.beginStroke('<?=Yii::$app->params['main_color_2']?>');
                        cir.graphics.beginFill("<?=Yii::$app->params['main_color_2']?>").drawCircle(0, 0, 25);
                        cir.x = <?=$scene_width/2?> - 100 + x*200;
                        cir.y = <?=$scene_height/2?> + y*200;
                        cir.name = "cir_"+i;
                        cont.addChild(cir);

                        var cir = new createjs.Shape();
                        cir.graphics.beginStroke('<?=Yii::$app->params['main_color_2']?>');
                        cir.graphics.beginFill("<?=Yii::$app->params['main_color_2']?>").drawCircle(0, 0, 50);
                        cir.alpha = 0.2;
                        cir.x = <?=$scene_width/2?> - 100 + x*200;
                        cir.y = <?=$scene_height/2?> + y*200;
                        cir.name = "cir_"+i;
                        cont.addChild(cir);
                    }
                }
            }

            if (cont.getChildByName("tri") == null) {
                var tri = new createjs.Shape();
                tri.graphics.beginStroke("<?=Yii::$app->params['main_text_color_2']?>").setStrokeStyle(10,"round");
                //tri.graphics.beginFill("<?=Yii::$app->params['main_text_color_2']?>");
                tri.alpha = 0.4;
                tri.graphics.moveTo(0, 0)
                .lineTo(0, 200)
                .lineTo(200, 200)
                .lineTo(0, 0);
                tri.x = <?=$scene_width/2?> - 100;
                tri.y = <?=$scene_height/2?>;
                tri.nmae = "tri";
                cont.addChild(tri);
            }

            if (cont.getChildByName("hint") == null) {
                var string = new createjs.Text('Isosceles Right Triangle makes you powerful', '36px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color_2']?>');
                string.textAlign = 'center';
                string.name = 'hint';
                string.x = <?=$scene_width/2?>;
                string.y = 320;
                cont.addChild(string);
            }

            info.addChild(cont);

            createjs.Ticker.on("tick", tick);
            createjs.Ticker.setFPS(2);
            createjs.Ticker.timingMode = createjs.Ticker.RAF_SYNCHED;
            function tick (event) {
                if (tri.alpha == 0.4) {
                    tri.alpha = 0.2;
                } else {
                    tri.alpha = 0.4;
                }
                info.update();
            }
            */

            if (cont.getChildByName("tutorial_image") == null) {
                var image = new Image();
                image.src = "<?=Yii::$app->params['tutorial']?>";
                image.onload = function() {
                    var bitmap = new createjs.Bitmap(image);
                    bitmap.name = "tutorial_image";
                    bitmap.x = <?=$scene_width/2?> - image.width/2;
                    bitmap.y = 350;
                    cont.addChild(bitmap);
                    info.update();
                }
            }

            info.addChild(cont);
        }
        info.setChildIndex(cont, info.getNumChildren()-1);
        info.update();
    } else if (index == 'enter'){
        var cont = info.getChildByName("enter");
        if (cont == null) {
            var cont = new createjs.Container();
            cont.name = "enter";

            // $('#info').html('<div class="meter"><span style="width: 25%"></span></div>');
            // $('#info').after('<div id="loading-bar">  <span class="bar bar1"></span>  <span class="bar bar2"></span>  <span class="bar bar3"></span>  <span class="bar bar4"></span>  <span class="bar bar5"></span>  <span class="bar bar6"></span></div>');

            info.addChild(cont);
        }

        // load game scene
        showLoading();
        setTimeout(function(){
            $('#info').hide(); $('#main').show(); $('#grids').show(); hideLoading();
        }, <?=Yii::$app->params['load_scene_time']?>);

        info.setChildIndex(cont, info.getNumChildren()-1);
        info.update();
    }

    $('#info').show();
}
function drawBackground() {
    // preload background
    // if (background.getChildByName("bg") == null) {
    //     var rect = new createjs.Shape();
    //     rect.graphics.beginFill("<?=Yii::$app->params['canvas_background_color']?>").drawRect(0, 0, <?=$scene_width?>, <?=$scene_height?>);
    //     rect.name = "bg";
    //     background.addChild(rect);
    //     background.update();
    // }
    //$("#background").after(bg_image.src);
    if (background.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['bg_image']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            background.addChild(bitmap);
            background.setChildIndex(bitmap,-4000);
            background.update();
        }
    }

    // if (background.getChildByName("left_border") == null) {
    //     var rect = new createjs.Shape();
    //     rect.graphics.beginFill("<?=Yii::$app->params['main_color']?>").drawRect(<?=$offset_x?>, <?=$offset_y?>, 5, <?=$map_height?>);
    //     rect.name = "left_border";
    //     background.addChild(rect);
    //     background.update();
    // }
    //
    // if (background.getChildByName("right_border") == null) {
    //     var rect = new createjs.Shape();
    //     rect.graphics.beginFill("<?=Yii::$app->params['main_color']?>").drawRect(<?=$map_width-5+$offset_x?>, <?=$offset_y?>, 5, <?=$map_height?>);
    //     rect.name = "right_border";
    //     background.addChild(rect);
    //     background.update();
    // }
    //
    // if (background.getChildByName("gradient") == null) {
    //     var gradient = new createjs.Shape();
    //     gradient.graphics.beginLinearGradientFill(["rgba(255,255,255,0)","rgba(255,211,150,125)"], [0, 1], <?=$scene_width?>, <?=$offset_y?>, 0, <?=$offset_y?>).drawRect(<?=$offset_x+5?>, <?=$offset_y?>, <?=$map_width-5?>, <?=$map_height?>);
    //     gradient.name = "gradient";
    //     background.addChild(gradient);
    //     background.update();
    // }
    $("#background").show();
}
function drawCore(id) {
    var image = new Image();
    image.src = "<?=Yii::$app->params['core']?>";
    image.onload = function() {
        var bitmap = new createjs.Bitmap(image);
        var x = (id-1)%<?=$column?>;
        var y = Math.floor((id-1)/<?=$column?>);
        bitmap.x = x*<?=$grid_width?> + <?=$grid_width/2?> - image.width/2 + <?=$offset_x?>;
        bitmap.y = y*<?=$grid_height?> + <?=$grid_height/2?> - image.height/2 + <?=$offset_y?>;
        bitmap.name = "core";
        main.addChild(bitmap);
        main.setChildIndex(bitmap, main.getNumChildren()-1);
        main.update();
    };
}
function showLoading() {
    $('.loader').show();
    $('.loader-mask').show();
    // setTimeout(function(){ $('#mask').hide(); $('.loader').hide();}, 3000);
}
function hideLoading() {
    $('.loader').hide();
    $('.loader-mask').hide();
}
function selectTeam(team_id) {
    if (is_player_in_team == 0) {
        showLoading();
        $.ajax({
            // method: "POST",
            url: "<?= '../../api/web/index.php?r=round/team-up'; ?>",
            data: {
                key: getCookie('key'),
                round_id: round_id,
                team_id: team_id,
            },
            dataType : 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    setCookie("key", response.data.player.key, 7);
                    team_id = response.data.roundTeamPlayer.team_id;
                    is_player_in_team = 1;
                    $('#teamup').hide();
                }
            }
        });
    }
}
function clickGrid(id) {
    if (is_start == 1 && resource > 0) {
        showLoading();
        $.ajax({
            // method: "POST",
            url: "<?= '../../api/web/index.php?r=map/mark'; ?>",
            data: {
                id: id,
                key: getCookie('key'),
                round_id: round_id,
                team_id: team_id,
            },
            dataType : 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    setCookie("key", response.data.player.key, 7);
                    resource = response.data.roundTeamPlayer.resource;
                    drawTower(id, team_id, player_id);
                }
            }
        });
    }
}
function updateMap() {
    $.ajax({
        // method: "POST",
        url: "<?= '../../api/web/index.php?r=map/get-map'; ?>",
        data: {
            key: getCookie('key'),
            round_id: round_id,
            team_id: team_id,
        },
        dataType : 'json',
        success: function(response) {
            not_able_to_request = 0;
            name = response.data.player.name;
            is_start = response.data.is_start;
            is_end = response.data.is_end;
            is_win = response.data.is_win;
            score = response.data.score;
            round_score = response.data.round_score;
            rank = response.data.rank;
            team_score_1 = response.data.team_score_1;
            team_score_2 = response.data.team_score_2;
            resource = response.data.resource;
            core_score = response.data.core;
            total_core_score = response.data.total_core_score;
            if (response.success) {
                setCookie("key", response.data.player.key, 7);
                if (is_start && is_end==0) {
                    drawGame(response.data.grids, response.data.remains, response.data.triangles);
                    drawText("", null, null, round_score, null, team_score_1, team_score_2, core_score);
                }
            }
            if (is_end) {
                showLoading();
                setTimeout(function(){
                    endGame();
                    hideLoading();
                }, 1000);
            }
        }
    });
}
function joinGame(shadow) {
    showLoading();
    $.ajax({
        // method: "POST",
        url: "<?= '../../api/web/index.php?r=round/start'; ?>",
        data: {
            key: getCookie('key'),
            secret: $('#secret').val(),
            shadow: shadow,
        },
        dataType : 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                setCookie("key", response.data.player.key, 7);
                if (response.data.is_festival) {
                    setCookie("is_festival", 1);
                }
                location.reload();
            } else {
                if (response.data.error == 'wrong_secret') {
                    error = "wrong secret";
                }
                keycodeGame();
            }
        }
    });
}

function keycodeGame() {
    hideScenes();

    clearInterval(check_status);
    clearInterval(interval);

    if (keycode.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['keycode']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            keycode.addChild(bitmap);
            keycode.setChildIndex(bitmap, 0);
            keycode.update();
        }
    }

    if (error) {
        if (keycode.getChildByName('keycode_hint') == null) {
            var text = new createjs.Text(error, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            text.textAlign = 'center';
            text.name = 'keycode_hint';
            text.x = <?=$scene_width/2?>;
            text.y = 960;
            keycode.addChild(text);
            keycode.setChildIndex(text, keycode.getNumChildren()-1);
            keycode.update();
        } else {
            keycode.getChildByName('keycode_hint').text = error;
        }
    }

    // close button
    if (keycode.getChildByName("close") == null) {
        var rect = new createjs.Shape();
        rect.graphics.beginFill("#ff0000").drawRect(0, 0, 300, 150);
        rect.x = 0;
        rect.y = 0;
        rect.alpha = 0.01;
        rect.name = "close";
        rect.on("click", function(event) {
            // close keycode = back to start game
            // startGame();
            location.reload();
        });
        keycode.addChild(rect);
        keycode.setChildIndex(rect, keycode.getNumChildren()-1);
        keycode.update();
    }

    keycode.update();
    $('#keycode').show();
    $('#keycode-form').show();
}

function loginGame() {
    hideScenes();

    clearInterval(check_status);
    clearInterval(interval);

    if (login.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['login']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            login.addChild(bitmap);
            login.setChildIndex(bitmap, 0);
            login.update();
        }
    }

    if (is_login == 0 && error) {
        if (login.getChildByName('login_hint') == null) {
            var text = new createjs.Text(error, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            text.textAlign = 'center';
            text.name = 'login_hint';
            text.x = <?=$scene_width/2?>;
            text.y = 960;
            login.addChild(text);
            login.setChildIndex(text, login.getNumChildren()-1);
            login.update();
        } else {
            login.getChildByName('login_hint').text = error;
        }
    }

    $('#login').show();
    $('#login-form').show();
}

function leaderboardGame() {
    hideScenes();

    clearInterval(check_status);
    clearInterval(interval);

    setCookie("leaderboard", 1);

    /////////////////////////////////////////////
    ////   need to check the leaderboard
    /////////////////////////////////////////////

    if (leaderboard.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['leaderboard']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            leaderboard.addChild(bitmap);
            leaderboard.setChildIndex(bitmap, 0);
            leaderboard.update();
        }
    }

    // if (leaderboard.getChildByName("title") == null) {
    //     var text = new createjs.Text('Leaderboard', '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
    //     text.textAlign = 'center';
    //     text.name = 'title';
    //     text.x = <?=$scene_width/2?>;
    //     text.y = 200;
    //     leaderboard.addChild(text);
    //     leaderboard.setChildIndex(text, leaderboard.getNumChildren()-1);
    //     leaderboard.update();
    // }

    if (ranks) {
        if (leaderboard.getChildByName("list") == null) {
            var list = new createjs.Container();
            list.name = "list";
            list.x = 0;
            list.y = 174;

            for (var i=0; i<ranks.length; i++) {
                // var text1 = new createjs.Text(ranks[i]['rank'], '32px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                // text1.textAlign = 'left';
                // text1.x = 50;
                // text1.y = i*50;

                var text2 = new createjs.Text(ranks[i]['name'], '32px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                text2.textAlign = 'left';
                text2.x = 230;
                text2.y = i*71;
                text2.alpha = 0.8;

                var text3 = new createjs.Text(ranks[i]['score'], '32px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                text3.textAlign = 'right';
                text3.x = 800;
                text3.y = i*71;
                text3.alpha = 0.8;

                var text4 = new createjs.Text(ranks[i]['round_count'] + "    BATTLES", '32px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                text4.textAlign = 'right';
                text4.x = 1035;
                text4.y = i*71;
                text4.alpha = 0.8;

                // var border = new createjs.Shape();
                // border.graphics.beginFill("<?=Yii::$app->params['main_color']?>").drawRect(0, 0, 500, 1);
                // border.x = 50;
                // border.y = i*50 + 40;
                // border.alpha = 0.5;

                list.addChild(text2, text3, text4);
            }

            if (rank) {
                var text = new createjs.Text(rank, '32px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                text.textAlign = 'left';
                text.x = 360;
                text.y = 905;
                text.alpha = 0.8;
                leaderboard.addChild(text);
                leaderboard.setChildIndex(text, leaderboard.getNumChildren()-1);
            }

            if (score == 0 || score) {
                var text = new createjs.Text(score, '32px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                text.textAlign = 'left';
                text.x = 605;
                text.y = 905;
                text.alpha = 0.8;
                leaderboard.addChild(text);
                leaderboard.setChildIndex(text, leaderboard.getNumChildren()-1);
            }

            if (round_count == 0 || round_count) {
                var text = new createjs.Text(round_count, '32px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                text.textAlign = 'left';
                text.textAlign = 'right';
                text.x = 1035;
                text.y = 905;
                text.alpha = 0.8;
                leaderboard.addChild(text);
                leaderboard.setChildIndex(text, leaderboard.getNumChildren()-1);
            }

            leaderboard.addChild(list);
            leaderboard.setChildIndex(list, leaderboard.getNumChildren()-1);
            leaderboard.update();
        }
    }

    // close button
    if (leaderboard.getChildByName("close") == null) {
        var rect = new createjs.Shape();
        rect.graphics.beginFill("#ff0000").drawRect(0, 0, 300, 150);
        rect.x = 0;
        rect.y = 0;
        rect.alpha = 0.01;
        rect.name = "close";
        rect.on("click", function(event) {
            // close leader board = back to start game
            setCookie("leaderboard", 0);
            // startGame();
            location.reload();
        });
        leaderboard.addChild(rect);
        leaderboard.setChildIndex(rect, leaderboard.getNumChildren()-1);
        leaderboard.update();
    }

    leaderboard.update();
    $("#leaderboard").show();
}

function logout() {
    showLoading();
    $.ajax({
        // method: "POST",
        url: "<?= '../../api/web/index.php?r=round/logout'; ?>",
        data: {
            key: getCookie('key'),
        },
        dataType : 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                is_login = 0;
                loginGame();
            } else {
                //
            }
        }
    });
}

function getLeaderBoard() {
    showLoading();
    $.ajax({
        // method: "POST",
        url: "<?= '../../api/web/index.php?r=round/get-ranks'; ?>",
        data: {
            key: getCookie('key'),
            limit: 10,
        },
        dataType : 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                setCookie("key", response.data.player.key, 7);
                rank = response.data.rank;
                ranks = response.data.ranks;
                round_count = response.data.round_count;
                leaderboardGame();
            } else {
                if (response.data.error == 'not_login') {
                    is_login = 0;
                    loginGame();
                }
            }
        }
    });
}

function checkSecret() {
    showLoading();
    $.ajax({
        // method: "POST",
        url: "<?= '../../api/web/index.php?r=round/get-secret'; ?>",
        data: {
            key: getCookie('key'),
        },
        dataType : 'json',
        success: function(response) {
            hideLoading();
            var secret;
            if (response.success) {
                secret = response.data.secret;
                check_secret = response.data.check_secret;
            } else {
                secret = '';
                check_secret = 0;
            }
            if (check_secret == 0) {
                joinGame();
            } else if (getCookie('is_festival') || is_inspector) {
                joinGame(secret);
            } else {
                keycodeGame();
            }
        }
    });
}

function startGameForInspector() {
    hideScenes();

    if (start.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['start_image']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            start.addChild(bitmap);
            start.setChildIndex(bitmap, 0);
            start.update();
        }
    }

    if (start.getChildByName("leaderboard") == null) {
        var rect = new createjs.Shape();
        rect.graphics.beginFill("#ff0000").drawRect(0, 0, 400, 120);
        rect.name = "leaderboard";
        rect.alpha = 0.01;
        rect.x = <?=$scene_width/2?> - 200;
        rect.y = <?=$scene_height/2?> + 160;
        rect.on("click", function(event) {
            setCookie("start", 0);
            getLeaderBoard();
        });
        start.addChild(rect);
        start.setChildIndex(rect, start.getNumChildren()-1);
        start.update();
    }

    start.update();
    $('#start').show();
}

function startGame() {
    hideScenes();

    clearInterval(check_status);
    clearInterval(interval);

    setCookie("start", 1);

    if (start.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['start_image']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            start.addChild(bitmap);
            start.setChildIndex(bitmap, 0);
            start.update();
        }
    }

    if (start.getChildByName("start") == null) {
        var rect = new createjs.Shape();
        rect.graphics.beginFill("#000000").drawRect(0, 0, 400, 120);
        rect.name = "start";
        rect.alpha = 0.01;
        rect.x = <?=$scene_width/2?> - 200;
        rect.y = <?=$scene_height/2?>;
        if (is_open) {
            rect.on("click", function(event) {
                setCookie("start", 0);
                //joinGame();
                checkSecret();
            });
        } else if (is_open == 0) {
            // rect.alpha = 0.1;
        }
        start.addChild(rect);
        start.setChildIndex(rect, start.getNumChildren()-1);
        start.update();
    }

    if (start.getChildByName("leaderboard") == null) {
        var rect = new createjs.Shape();
        rect.graphics.beginFill("#ff0000").drawRect(0, 0, 400, 120);
        rect.name = "leaderboard";
        rect.alpha = 0.01;
        rect.x = <?=$scene_width/2?> - 200;
        rect.y = <?=$scene_height/2?> + 160;
        rect.on("click", function(event) {
            setCookie("start", 0);
            getLeaderBoard();
        });
        start.addChild(rect);
        start.setChildIndex(rect, start.getNumChildren()-1);
        start.update();
    }

    if (start.getChildByName("logout") == null) {
        var rect = new createjs.Shape();
        rect.graphics.beginFill("#00ff00").drawRect(0, 0, 400, 120);
        rect.name = "leaderboard";
        rect.alpha = 0.01;
        rect.x = <?=$scene_width/2?> - 200;
        rect.y = <?=$scene_height/2?> + 320;
        rect.on("click", function(event) {
            setCookie("start", 0);
            logout();
        });
        start.addChild(rect);
        start.setChildIndex(rect, start.getNumChildren()-1);
        start.update();
    }

    if (is_login && name) {
        if (start.getChildByName('login_hint') == null) {
            var text = new createjs.Text('welcome, ' + name, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            text.textAlign = 'center';
            text.name = 'login_hint';
            text.x = <?=$scene_width/2?>;
            text.y = 960;
            start.addChild(text);
            start.update();
        } else {
            start.getChildByName('login_hint').text = 'welcome, ' + name;
        }
    }

    if (is_open == 0) {
        if (start.getChildByName('start_hint') == null) {
            var text = new createjs.Text('wait for the current round to stop', '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            text.textAlign = 'center';
            text.name = 'start_hint';
            text.x = <?=$scene_width/2?>;
            text.y = 750;
            start.addChild(text);
            start.update();
        }
    } else if (is_open) {
        if (start.getChildByName('start_hint')) {
            start.removeChild(start.getChildByName('start_hint'));
        }
    }

    start.update();
    $('#start').show();
}
function drawGame(grids, remains, triangles) {
    for (var i=0; i<grids.length; i++) {
        drawTower(grids[i]['id'], grids[i]['team_id'], grids[i]['player_id'], grids[i]['score_rate']);
    }
    for (var i=0; i<remains.length; i++) {
        clearTower(remains[i]['id']);
        drawRemain(remains[i]['id'], remains[i]['team_id']);
    }
    clearTriangkes();
    for (var i=0; i<triangles.length; i++) {
        drawTriangle(triangles[i]['a'], triangles[i]['b'], triangles[i]['c'], triangles[i]['team_id']);
    }
    $('#info').hide();
    $('#grids').show();
}
function drawText(title, hint, score, round_score, rank, team_score_1, team_score_2, core) {
    if (title != null) {
        if (main.getChildByName("title") == null) {
            var string = new createjs.Text(title, '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
            string.textAlign = 'center';
            string.name = 'title';
            string.x = <?=$scene_width/2?>;
            string.y = 60;
            main.addChild(string);
            main.setChildIndex(string, main.getNumChildren()-1);
            main.update();
        }
        main.getChildByName("title").text = title;
    }

    if (hint != null) {
        if (main.getChildByName("hint") == null) {
            string = new createjs.Text('Hint', '48px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            string.textAlign = 'center';
            string.name = 'hint';
            string.x = <?=$scene_width/2?>;
            string.y = 220;
            main.addChild(string);
            main.setChildIndex(string, main.getNumChildren()-1);
            main.update();
        }
        main.getChildByName("hint").text = hint;
    } else {
        main.removeChild(main.getChildByName("hint"));
    }

    // timer
    if (main.getChildByName("timer") == null) {
        var string = new createjs.Text(game_time, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
        string.textAlign = 'right';
        string.name = 'timer';
        string.x = 430;
        string.y = 245;
        string.shadow = new createjs.Shadow("#000000", 0, 5, 10);
        main.addChild(string);
        main.setChildIndex(string, main.getNumChildren()-1);
        main.update();
    }
    main.getChildByName("timer").text = game_time;

    // core
    if (core_score != null && total_core_score != null) {
        if (main.getChildByName("core_score") == null) {
            var string = new createjs.Text(core_score, '30px <?=Yii::$app->params['font']?>', '#ffff00');
            string.textAlign = 'right';
            string.name = 'core_score';
            string.x = 430;
            string.y = 430;
            string.shadow = new createjs.Shadow("#000000", 0, 5, 10);
            main.addChild(string);
            main.setChildIndex(string, main.getNumChildren()-1);
            main.update();
        } if (main.getChildByName("core_bar") == null) {
            var image = new Image();
            image.src = "<?=Yii::$app->params['core_bar']?>";
            image.onload = function() {
                var bitmap = new createjs.Bitmap(image);
                bitmap.name = "core_bar";
                bitmap.x = 321 + (1-core_score/total_core_score) * 108; // 108 = image.width
                bitmap.y = 413;
                bitmap.scaleX = core_score/total_core_score;
                main.addChild(bitmap);
                main.setChildIndex(bitmap, main.getNumChildren()-1);
                main.update();
            }
        } else {
            main.getChildByName("core_score").text = core_score;
            main.getChildByName("core_bar").scaleX = core_score/total_core_score;
            main.getChildByName("core_bar").x = 321 + (1-core_score/total_core_score) * 108;
        }
    }

    // ice team
    if (team_score_1 != null) {
        if (main.getChildByName("team_score_1") == null) {
            var string = new createjs.Text(team_score_1, '30px <?=Yii::$app->params['font']?>', '#0000ff');
            string.textAlign = 'right';
            string.name = 'team_score_1';
            string.x = 430;
            string.y = 600;
            string.shadow = new createjs.Shadow("#000000", 0, 5, 10);
            main.addChild(string);
            main.setChildIndex(string, main.getNumChildren()-1);
            main.update();
        }
        main.getChildByName("team_score_1").text = team_score_1;
    }

    // fire team
    if (team_score_2 != null) {
        if (main.getChildByName("team_score_2") == null) {
            var string = new createjs.Text(team_score_2, '30px <?=Yii::$app->params['font']?>', '#ff0000');
            string.textAlign = 'right';
            string.name = 'team_score_2';
            string.x = 430;
            string.y = 510;
            string.shadow = new createjs.Shadow("#000000", 0, 5, 10);
            main.addChild(string);
            main.setChildIndex(string, main.getNumChildren()-1);
            main.update();
        }
        main.getChildByName("team_score_2").text = team_score_2;
    }

    // score for this round
    if (round_score != null) {
        if (main.getChildByName("round_score") == null) {
            var string = new createjs.Text(round_score, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
            string.textAlign = 'right';
            string.name = 'round_score';
            string.x = 430;
            string.y = 335;
            string.shadow = new createjs.Shadow("#000000", 0, 5, 10);
            main.addChild(string);
            main.setChildIndex(string, main.getNumChildren()-1);
            main.update();
        }
        main.getChildByName("round_score").text = round_score;
    }

    main.update();
    $("#main").show();
}
function clearTriangkes() {
    var cont = main.getChildByName('triangles');
    if (cont != null) {
        cont.removeAllChildren();
    }
}
function drawTriangle(a, b, c, team_id) {
    if (team_id < 2) {
        return;
    }

    var cont = main.getChildByName('triangles');

    if (cont == null) {
        cont = new createjs.Container();
        cont.name = 'triangles';
        main.addChild(cont);
        main.setChildIndex(cont, main.getNumChildren()-1);
    }

    if (cont.getChildByName('triangle_' + a + '_' + b + '_' + c) == null) {
        var inner_cont = new createjs.Container();
        inner_cont.name = 'triangle_' + a + '_' + b + '_' + c;

        var a_x = (a-1)%<?=$column?>;
        var a_y = Math.floor((a-1)/<?=$column?>);
        var b_x = (b-1)%<?=$column?>;
        var b_y = Math.floor((b-1)/<?=$column?>);
        var c_x = (c-1)%<?=$column?>;
        var c_y = Math.floor((c-1)/<?=$column?>);

        // var cir = new createjs.Shape();
        // cir.graphics.beginStroke('<?=Yii::$app->params['main_color_2']?>');
        // cir.graphics.beginFill("<?=Yii::$app->params['main_color_2']?>").drawCircle(a_x*<?=$grid_width?>, a_y*<?=$grid_height?>, <?=$grid_width/8?>);
        // cir.x = <?=$grid_width/2 + $offset_x?>;
        // cir.y = <?=$grid_height/2 + $offset_y?>;
        // inner_cont.addChild(cir);
        //
        // var cir = new createjs.Shape();
        // cir.graphics.beginStroke('<?=Yii::$app->params['main_color_2']?>');
        // cir.graphics.beginFill("<?=Yii::$app->params['main_color_2']?>").drawCircle(b_x*<?=$grid_width?>, b_y*<?=$grid_height?>, <?=$grid_width/8?>);
        // cir.x = <?=$grid_width/2 + $offset_x?>;
        // cir.y = <?=$grid_height/2 + $offset_y?>;
        // inner_cont.addChild(cir);
        //
        // var cir = new createjs.Shape();
        // cir.graphics.beginStroke('<?=Yii::$app->params['main_color_2']?>');
        // cir.graphics.beginFill("<?=Yii::$app->params['main_color_2']?>").drawCircle(c_x*<?=$grid_width?>, c_y*<?=$grid_height?>, <?=$grid_width/8?>);
        // cir.x = <?=$grid_width/2 + $offset_x?>;
        // cir.y = <?=$grid_height/2 + $offset_y?>;
        // inner_cont.addChild(cir);

        // var rect = new createjs.Shape();
        // rect.graphics.beginFill(colors[team_id-2]).drawRect(0, 0, <?=$grid_width?>, <?=$grid_height?>);
        // rect.alpha = 0.4;
        // rect.x = a_x*<?=$grid_width?> + <?=$offset_x?>;
        // rect.y = a_y*<?=$grid_height?> + <?=$offset_y?>;
        // inner_cont.addChild(rect);
        //
        // var rect = new createjs.Shape();
        // rect.graphics.beginFill(colors[team_id-2]).drawRect(0, 0, <?=$grid_width?>, <?=$grid_height?>);
        // rect.alpha = 0.4;
        // rect.x = b_x*<?=$grid_width?> + <?=$offset_x?>;
        // rect.y = b_y*<?=$grid_height?> + <?=$offset_y?>;
        // inner_cont.addChild(rect);
        //
        // var rect = new createjs.Shape();
        // rect.graphics.beginFill(colors[team_id-2]).drawRect(0, 0, <?=$grid_width?>, <?=$grid_height?>);
        // rect.alpha = 0.4;
        // rect.x = c_x*<?=$grid_width?> + <?=$offset_x?>;
        // rect.y = c_y*<?=$grid_height?> + <?=$offset_y?>;
        // inner_cont.addChild(rect);

        var tri = new createjs.Shape();
        tri.graphics.beginStroke(colors[team_id-2]).setStrokeStyle(10,"round");
        tri.alpha = 0.4;
        tri.graphics.moveTo(a_x*<?=$grid_width?>, a_y*<?=$grid_height?>)
        .lineTo(b_x*<?=$grid_width?>, b_y*<?=$grid_height?>)
        .lineTo(c_x*<?=$grid_width?>, c_y*<?=$grid_height?>)
        .lineTo(a_x*<?=$grid_width?>, a_y*<?=$grid_height?>);
        tri.x = <?=$grid_width/2 + $offset_x?>;
        tri.y = <?=$grid_height/2 + $offset_y?>;
        tri.name = 'triangle_' + a + '_' + b + '_' + c;
        inner_cont.addChild(tri);

        cont.addChild(inner_cont);
        cont.setChildIndex(inner_cont, 0);
    }

    main.update();
}
function drawRemain(id, team_id) {
    if (main.getChildByName('remain_' + id) == null) {
        var x = (id-1)%<?=$column?>;
        var y = Math.floor((id-1)/<?=$column?>);
        // var remain = new createjs.Shape();
        // remain.graphics.beginFill("<?=Yii::$app->params['main_color_2']?>").drawCircle(0, 0, <?=$grid_width/4?>);
        // remain.x = x*<?=$grid_width?> + <?=$grid_width/2?> + <?=$offset_x?>;
        // remain.y = y*<?=$grid_height?> + <?=$grid_height/2?> + <?=$offset_y?>;
        // remain.name = 'remain_' + id;
        // main.addChild(remain);

        var remain;
        if (team_id == 2) {
            var image = new Image();
            image.src = "<?=Yii::$app->params['team_remain_0']?>";
            image.onload = function() {
                remain = new createjs.Bitmap(image);
                remain.x = x*<?=$grid_width?> - <?=$grid_width*3/4?> + <?=$offset_x?>;
                remain.y = y*<?=$grid_height?> - <?=$grid_height*3/4?> + <?=$offset_y?>;
                remain.name = 'remain_' + id;
                main.addChild(remain);
            }
        } else if (team_id == 3) {
            var image = new Image();
            image.src = "<?=Yii::$app->params['team_remain_1']?>";
            image.onload = function() {
                remain = new createjs.Bitmap(image);
                remain.x = x*<?=$grid_width?> - <?=$grid_width*3/4?> + <?=$offset_x?>;
                remain.y = y*<?=$grid_height?> - <?=$grid_height*3/4?> + <?=$offset_y?>;
                remain.name = 'remain_' + id;
                main.addChild(remain);
            }
        }

        var rect = new createjs.Shape();
        rect.graphics.beginFill("<?=Yii::$app->params['main_color']?>").drawRect(0, 0, <?=$grid_width?>, <?=$grid_height?>);
        rect.alpha = 0.8;
        rect.x = x*<?=$grid_width?> + <?=$offset_x?>;
        rect.y = y*<?=$grid_height?> + <?=$offset_y?>;
        rect.name = "block";
        main.addChild(rect);

        createjs.Ticker.on("tick", tick);
        createjs.Ticker.setFPS(20);
        createjs.Ticker.timingMode = createjs.Ticker.RAF_SYNCHED;
        function tick (event) {
            //rect.scaleY -= 0.01;
            rect.alpha -= 0.01;
            if (remain != null) {
                remain.alpha -= 0.01;
            }
            main.update();
        }

        // load game scene
        setTimeout(function(){
            main.removeChild(remain);
            main.removeChild(rect);
            main.update();
        }, <?=Yii::$app->params['cool_down_time']?>);
    }
}
function clearTower(id) {
    tower = main.getChildByName('tower_' + id);
    score_rate = main.getChildByName('tower_' + id + '_score_rate');
    if (tower != null) {
        main.removeChild(tower);
        main.update();
    }
    if (score_rate != null) {
        main.removeChild(score_rate);
        main.update();
    }
}
function drawTower(id, team_id, tower_player_id, score_rate) {
    if (score_rate == null){
        score_rate = 0;
    }

    if (team_id < 2) {
        return;
    }

    if (main.getChildByName('tower_' + id) == null) {
        if (tower_player_id == player_id) {
            image = my_tower_images[team_id-2];
        } else {
            image = team_tower_images[team_id-2];
        }

        var x = (id-1)%<?=$column?>;
        var y = Math.floor((id-1)/<?=$column?>);
        var tower = new createjs.Bitmap(image);
        tower.x = x*<?=$grid_width?> + <?=$grid_width/2?> - image.width/2 + <?=$offset_x?>;
        tower.y = y*<?=$grid_height?> + <?=$grid_height/2?> - image.height/2 + <?=$offset_y?>;
        tower.name = 'tower_' + id;
        main.addChild(tower);
        main.update();
    }

    if (tower_player_id == player_id) {
        if (main.getChildByName('tower_' + id + '_score_rate') == null) {
            var text = new createjs.Text('+ ' + score_rate, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
            text.textAlign = 'center';
            text.name = 'tower_' + id + '_score_rate';
            text.x = x*<?=$grid_width?> + <?=$grid_width*0.5?> + <?=$offset_x?>;
            text.y = y*<?=$grid_height?> - <?=$grid_height*0.4?>+ <?=$offset_y?>;
            main.addChild(text);
            main.setChildIndex(text, main.getNumChildren()-1);
            // main.update();

            createjs.Ticker.on("tick", tick);
            createjs.Ticker.setFPS(20);
            createjs.Ticker.timingMode = createjs.Ticker.RAF_SYNCHED;
            function tick (event) {
                text.y -= 2;
                text.alpha -= 0.1;
                if (text.y <= y*<?=$grid_height?> - <?=$grid_height*0.8?> + <?=$offset_y?>) {
                    text.y = y*<?=$grid_height?> - <?=$grid_height*0.4?>+ <?=$offset_y?>;
                    text.alpha = 1;
                }
                main.update();
            }
        } else {
            main.getChildByName('tower_' + id + '_score_rate').text = '+ ' + score_rate;
        }

        if (score_rate == 0) {
            main.getChildByName('tower_' + id + '_score_rate').text = '';
        }
    }
}
function drawGameBg() {
    if (main.getChildByName("bg") == null) {
        if (team_id == 2) {
            var image = new Image();
            image.src = "<?=Yii::$app->params['game_image_0']?>";
            image.onload = function() {
                var bitmap = new createjs.Bitmap(image);
                main.name = "bg";
                main.addChild(bitmap);
                main.setChildIndex(bitmap, 0);
                main.update();
            }
        } else {
            var image = new Image();
            image.src = "<?=Yii::$app->params['game_image_1']?>";
            image.onload = function() {
                var bitmap = new createjs.Bitmap(image);
                main.name = "bg";
                main.addChild(bitmap);
                main.setChildIndex(bitmap, 0);
                main.update();
            }
        }
    }
    drawCore(41);
}
function hideScenes() {
    $('#login').hide();
    $('#start').hide();
    $('#teamup').hide();
    $('#leaderboard').hide();
    $('#info').hide();
    $('#main').hide();
    $('#end').hide();
    $('#keycode').hide();
    $('#keycode-form').hide();
    $('#login-form').hide();
}
function endGameForInspector() {
    hideScenes();

    clearInterval(check_status);
    clearInterval(interval);

    setCookie("end", 1);

    if (end.getChildByName("bg") == null) {
        // var rect = new createjs.Shape();
        // rect.graphics.beginFill("<?=Yii::$app->params['background_color']?>").drawRect(0, 0, <?=$scene_width?>, <?=$scene_height?>);
        // rect.name = "bg";
        // end.addChild(rect);
        var image1 = new Image();
        image1.src = "<?=Yii::$app->params['end_image']?>";
        image1.onload = function() {
            var bitmap = new createjs.Bitmap(image1);
            bitmap.name = "bg";
            end.addChild(bitmap);
            end.setChildIndex(bitmap, 0);
            end.update();
        }
    }
    var image;
    if (end.getChildByName("winner") == null) {
        if (is_win == 0) {
            image = new Image();
            image.src = "<?=Yii::$app->params['team_mech']?>";
        } else if (is_win == 1) {
            if (team_score_1 > team_score_2) {
                image = new Image();
                image.src = "<?=Yii::$app->params['team_0']?>";
            } else if (team_score_1 <= team_score_2) {
                image = new Image();
                image.src = "<?=Yii::$app->params['team_1']?>";
            }
        }
        if (image != null) {
            image.onload = function() {
                var bitmap = new createjs.Bitmap(image);
                bitmap.name = "winner";
                bitmap.x = 260;
                bitmap.y = 176;
                end.addChild(bitmap);
                end.setChildIndex(bitmap, end.getNumChildren()-1);
                end.update();
            }
        }
    }

    if (end.getChildByName("team_score_1") == null) {
        var text = new createjs.Text(team_score_1, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
        text.textAlign = 'left';
        text.name = "team_score_1";
        text.x = 600;
        text.y = 453;
        text.alpha = 0.8;
        end.addChild(text);
        end.setChildIndex(text, end.getNumChildren()-1);
    } else {
        end.getChildByName("team_score_1").text = team_score_1;
    }

    if (end.getChildByName("team_score_2") == null) {
        var text = new createjs.Text(team_score_2, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
        text.textAlign = 'left';
        text.name = "team_score_2";
        text.x = 600;
        text.y = 540;
        text.alpha = 0.8;
        end.addChild(text);
        end.setChildIndex(text, end.getNumChildren()-1);
    } else {
        end.getChildByName("team_score_2").text = team_score_2;
    }

    end.update();
    $('#end').show();
}
function endGame() {
    hideScenes();

    clearInterval(check_status);
    clearInterval(interval);

    setCookie("end", 1);

    if (end.getChildByName("bg") == null) {
        // var rect = new createjs.Shape();
        // rect.graphics.beginFill("<?=Yii::$app->params['background_color']?>").drawRect(0, 0, <?=$scene_width?>, <?=$scene_height?>);
        // rect.name = "bg";
        // end.addChild(rect);
        var image1 = new Image();
        image1.src = "<?=Yii::$app->params['end_image']?>";
        image1.onload = function() {
            var bitmap = new createjs.Bitmap(image1);
            bitmap.name = "bg";
            end.addChild(bitmap);
            end.setChildIndex(bitmap, 0);
            end.update();
        }
    }
    var image;
    if (end.getChildByName("winner") == null) {
        if (is_win == 0) {
            image = new Image();
            image.src = "<?=Yii::$app->params['team_mech']?>";
        } else if (is_win == 1) {
            if (team_score_1 > team_score_2) {
                image = new Image();
                image.src = "<?=Yii::$app->params['team_0']?>";
            } else if (team_score_1 < team_score_2) {
                image = new Image();
                image.src = "<?=Yii::$app->params['team_1']?>";
            } else {
                if (team_id == 2) {
                    image = new Image();
                    image.src = "<?=Yii::$app->params['team_0']?>";
                } else {
                    image = new Image();
                    image.src = "<?=Yii::$app->params['team_1']?>";
                }
            }
        }
        if (image != null) {
            image.onload = function() {
                var bitmap = new createjs.Bitmap(image);
                bitmap.name = "winner";
                bitmap.x = 260;
                bitmap.y = 176;
                end.addChild(bitmap);
                end.setChildIndex(bitmap, end.getNumChildren()-1);
                end.update();
            }
        }
    }

    if (end.getChildByName("team_score_1") == null) {
        var text = new createjs.Text(team_score_1, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
        text.textAlign = 'left';
        text.name = "team_score_1";
        text.x = 600;
        text.y = 453;
        text.alpha = 0.8;
        end.addChild(text);
        end.setChildIndex(text, end.getNumChildren()-1);
    } else {
        end.getChildByName("team_score_1").text = team_score_1;
    }

    if (end.getChildByName("team_score_2") == null) {
        var text = new createjs.Text(team_score_2, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
        text.textAlign = 'left';
        text.name = "team_score_2";
        text.x = 600;
        text.y = 540;
        text.alpha = 0.8;
        end.addChild(text);
        end.setChildIndex(text, end.getNumChildren()-1);
    } else {
        end.getChildByName("team_score_2").text = team_score_2;
    }

    // if (end.getChildByName("score") == null) {
    //     var text = new createjs.Text(score, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
    //     text.textAlign = 'left';
    //     text.name = "score";
    //     text.x = 600;
    //     text.y = 627;
    //     end.addChild(text);
    //     end.setChildIndex(text, end.getNumChildren()-1);
    // } else {
    //     end.getChildByName("score").text = score;
    // }

    if (end.getChildByName("round_score") == null) {
        var text = new createjs.Text(round_score, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
        text.textAlign = 'left';
        text.name = "round_score";
        text.x = 600;
        text.y = 627;
        text.alpha = 0.8;
        end.addChild(text);
        end.setChildIndex(text, end.getNumChildren()-1);
    } else {
        end.getChildByName("round_score").text = round_score;
    }

    // if (end.getChildByName("the_end") == null) {
    //     var text = new createjs.Text('The End', '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
    //     text.textAlign = 'center';
    //     text.name = "the_end";
    //     text.x = <?=$scene_width/2?>;
    //     text.y = <?=$scene_height/2?> - 360;
    //     end.addChild(text);
    // }
    //
    // if (end.getChildByName("is_win") == null) {
    //     if (is_win == 1) {
    //         var text = new createjs.Text('We won!', '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
    //     } else {
    //         var text = new createjs.Text('We lost...', '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
    //     }
    //     text.textAlign = 'center';
    //     text.name = "is_win";
    //     text.x = <?=$scene_width/2?>;
    //     text.y = <?=$scene_height/2?> - 220;
    //     end.addChild(text);
    // } else {
    //     if (is_win == 1) {
    //         end.getChildByName("is_win").text = 'We won!';
    //     } else {
    //         end.getChildByName("is_win").text = 'We lost...';
    //     }
    // }
    //
    // if (end.getChildByName("score") == null) {
    //     var text = new createjs.Text('Score: ' + score, '128px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
    //     text.textAlign = 'center';
    //     text.name = "score";
    //     text.x = <?=$scene_width/2?>;
    //     text.y = <?=$scene_height/2?> - 140;
    //     end.addChild(text);
    // } else {
    //     end.getChildByName("score").text = 'Score: ' + score;
    // }
    //
    // if (end.getChildByName("round_score") == null) {
    //     var text = new createjs.Text('this round: ' + round_score, '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
    //     text.textAlign = 'center';
    //     text.name = "round_score";
    //     text.x = <?=$scene_width/2?>;
    //     text.y = <?=$scene_height/2?>;
    //     end.addChild(text);
    // } else {
    //     end.getChildByName("round_score").text = 'this round: ' + round_score;
    // }
    //
    // for (var i=0; i<teams.length; i++) {
    //     if (end.getChildByName("teams_score_"+i) == null) {
    //
    //     }
    // }

    if (end.getChildByName("restart") == null) {
        rect = new createjs.Shape();
        rect.graphics.beginFill("#000000").drawRect(0, 0, <?=$scene_width?>, <?=$scene_height/2?>);
        rect.x = 0;
        rect.y = <?=$scene_height/2?>;
        rect.alpha = 0.01;
        rect.name = "restart";
        rect.on("click", function(event) {
            //alert(this.id);
            is_open = 1;
            setCookie("end", 0);
            //////////////////////////////////
            // ? do it or not
            // to do: automatically logout if using demo device
            // otherwise startGame()
            /////////////////////////////////
            startGame();
        });
        end.addChild(rect);
        end.setChildIndex(rect, end.getNumChildren()-1);
    }

    end.update();
    $('#end').show();
}
// Warn if overriding existing method
if(Array.prototype.equals)
    console.warn("Overriding existing Array.prototype.equals. Possible causes: New API defines the method, there's a framework conflict or you've got double inclusions in your code.");
// attach the .equals method to Array's prototype to call it on any array
Array.prototype.equals = function (array) {
    // if the other array is a falsy value, return
    if (!array)
        return false;

    // compare lengths - can save a lot of time
    if (this.length != array.length)
        return false;

    for (var i = 0, l=this.length; i < l; i++) {
        // Check if we have nested arrays
        if (this[i] instanceof Array && array[i] instanceof Array) {
            // recurse into the nested arrays
            if (!this[i].equals(array[i]))
                return false;
        }
        else if (this[i] != array[i]) {
            // Warning - two different object instances will never be equal: {x:20} != {x:20}
            return false;
        }
    }
    return true;
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

// login
function loginSubmit () {
    $('#login-form').hide();
    showLoading();

    $.ajax({
        // method: "POST",
        url: "<?= '../../api/web/index.php?r=round/login'; ?>",
        data: {
            name: $('#name').val(),
            password: $('#password').val(),
        },
        dataType : 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                setCookie("key", response.data.player.key, 7);
                $('#login-form').hide();
                is_login = 1;
                name = response.data.player.name;
                location.reload();
            } else {
                is_login = 0;
                if (response.data.error == 'new_player') {
                    signupSubmit();
                } else if (response.data.error == 'empty') {
                    error = "username and password cannot be empty";
                    loginGame();
                } else if (response.data.error == 'wrong_password') {
                    error = "wrong password";
                    loginGame();
                } else {
                    loginGame();
                }
            }
        }
    });
}

// signup
function signupSubmit () {
    $('#login-form').hide();
    showLoading();

    $.ajax({
        // method: "POST",
        url: "<?= '../../api/web/index.php?r=round/sign-up'; ?>",
        data: {
            name: $('#name').val(),
            password: $('#password').val(),
        },
        dataType : 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                setCookie("key", response.data.player.key, 7);
                $('#login-form').hide();
                is_login = 1;
                name = response.data.player.name;
                location.reload();
            } else {
                is_login = 0;
                if (response.data.error == "name_invalid") {
                    error = "name is taken";
                }
                loginGame();
            }
        }
    });
}

// secret
function keycodeSubmit () {
    $('#keycode-form').hide();

    joinGame();
}

// finish tutorial
function finishTutorial () {
    showLoading();

    $.ajax({
        // method: "POST",
        url: "<?= '../../api/web/index.php?r=round/player-ready'; ?>",
        data: {
            key: getCookie("key"),
        },
        dataType : 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                //
            } else {
                //
            }
        }
    });
}
</script>
