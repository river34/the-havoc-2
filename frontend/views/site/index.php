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
    <canvas id="main" width="<?=$scene_width?>" height="<?=$scene_height?>"></canvas>
    <div class="loader" hidden></div>
    <div class="loader-mask" hidden></div>
    <div id="grids" hidden>
        <div id="map">
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
canvas {
    padding: 0;
    margin: auto;
    display: block;
    position: absolute;
    top: 0;
    /*bottom: 0;*/
    left: 0;
    right: 0;
    width: <?=$scene_width?>px;
    height: <?=$scene_height?>px;
}
#grids, #login-form, #keycode-form {
    padding: 0;
    margin: auto;
    display: none;
    position: absolute;
    top: 0;
    /*bottom: 0;*/
    left: 0;
    right: 0;
    width: <?=$scene_width?>px;
    height: <?=$scene_height?>px;
}
#map {
    position: absolute;
    top: 195px;
    left: 466px;
    width: <?=$map_width?>px;
    height: <?=$map_height?>px;
}
.grid {
    background-color:Transparent;
    background-repeat:no-repeat;
    border:none;
    cursor:pointer;
    overflow:hidden;
    outline:none;
    /*border:1px silver dashed;*/
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
    padding: 0;
    margin: auto;
    display: block;
    position: absolute;
    top: calc(<?=$scene_height/2?>px - 60px);
    /*bottom: calc(<?=$scene_height/2?>px - 60px);*/
    left: calc(<?=$scene_width/2?>px - 60px);
    right: calc(<?=$scene_width/2?>px - 60px);
    z-index: 2010;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.loader-mask {
    padding: 0;
    margin: auto;
    display: block;
    position: absolute;
    top: 0;
    /*bottom: 0;*/
    left: 0;
    right: 0;
    z-index: 2000;
    background-color: rgba(0, 0, 0, 1);
    width: <?=$scene_width?>px;
    height: <?=$scene_height?>px;
}
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
            "<?=Yii::$app->params['wait_0_unready']?>",
            "<?=Yii::$app->params['wait_0_ready']?>",
            "<?=Yii::$app->params['wait_0_empty']?>",
            "<?=Yii::$app->params['wait_1_unready']?>",
            "<?=Yii::$app->params['wait_1_ready']?>",
            "<?=Yii::$app->params['wait_1_empty']?>",
            "<?=Yii::$app->params['start_image']?>",
            "<?=Yii::$app->params['teamup_image']?>",
            "<?=Yii::$app->params['wait_image']?>",
            "<?=Yii::$app->params['bg_image']?>",
            "<?=Yii::$app->params['game_image_0']?>",
            "<?=Yii::$app->params['game_image_1']?>",
            "<?=Yii::$app->params['end_image']?>",
            "<?=Yii::$app->params['tutorial']?>",
            "<?=Yii::$app->params['tutorial_1']?>",
            "<?=Yii::$app->params['tutorial_2']?>",
            "<?=Yii::$app->params['tutorial_3']?>",
            "<?=Yii::$app->params['tutorial_4']?>",
            "<?=Yii::$app->params['tutorial_5']?>",
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
var main;

// params
var is_login;
var is_open;
var is_player_ready;
var is_player_in_team;
var is_mech_ready;
var is_team_ready;
var is_player_ready_to_battle;
var is_all_player_ready_to_battle;
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
var error_code;
var secret;
var check_secret;
var core_score;
var total_core_score;
var round_count;
var players;
var team_players;

// preloaded images
var team_tower_images = [];
var my_tower_images = [];
var colors = [];

// interval
var interval;

var check_status;

function init() {
    clearInterval(check_status); check_status = false;
    clearInterval(interval); interval = false;
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

    if (check_status == false) {
        check_status = setInterval(function(){
            if (not_able_to_request > 0) {
                return;
            }
            not_able_to_request = 1;
            checkStatus();
        }, <?=Yii::$app->params['refresh_rate']?>);
    }
}
function checkStatus () {
    // check if player is in game
    $.ajax({
        // method: "POST",
        url: "<?= '../../api/web/index.php?r=round/check'; ?>",
        data: {
            key: getCookie('key'),
            is_inspector: getCookie('is_inspector'),
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
                is_player_ready_to_battle = response.data.is_player_ready_to_battle;
                is_all_player_ready_to_battle = response.data.is_all_player_ready_to_battle;
                is_ready = response.data.is_ready;
                is_start = response.data.is_start;
                is_end = response.data.is_end;
                is_win = response.data.is_win;
                team_score_1 = response.data.team_score_1;
                team_score_2 = response.data.team_score_2;
                team_counts = response.data.team_counts;
                player_id = response.data.player_id;
                name = response.data.name;
                score = response.data.score;
                rank = response.data.rank;
                team_id = response.data.team_id;
                teams = response.data.teams;
                empty_slots = response.data.empty_slots;
                empty_player_slots = response.data.empty_player_slots;
                round = response.data.round;
                round_id = response.data.round_id;
                round_score = response.data.round_score;
                resource = response.data.resource;
                players = response.data.players;
                team_players = response.data.team_players;
            } else {
                is_login = 1;
                is_open = 0;
                is_player_ready = 0;
                is_player_in_team = 0;
                is_mech_ready = 0;
                is_team_ready = 0;
                is_player_ready_to_battle = 0;
                is_all_player_ready_to_battle = 0;
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
    main = new createjs.Stage("main");

    drawBackground();
}
function init_state() {
    hideScenes();

    if (is_login == 0) {
        // login
        loginGame();
        return;
    }
    if (getCookie("is_inspector") == 0 && getCookie("end") == 1) {
        endGame();
        return;
    }
    if (getCookie("is_inspector") == 0 && getCookie("leaderboard") == 1) {
        getLeaderBoard();
        return;
    }
    if (getCookie("is_inspector") == 0 && getCookie("start") == 1){
        startGame();
        return;
    }
    if (is_open && is_player_ready == 0) {
        if (getCookie("is_inspector") == 1) {
            return;
        }
        // join this round
        startGame();
        setCookie('current_tutorial', 1);
        return;
    }
    if (is_open == 0 && is_player_ready == 0) {
        if (getCookie("is_inspector") == 1) {
            return;
        }
        startGame();
        setCookie('current_tutorial', 1);
        return;
    }
    if (is_player_ready && is_player_in_team == 0) {
        TeamUpGame();
        setCookie("start_time", 0);
        setCookie("end_time", 0);
        setCookie('current_tutorial', 1);
    }
    if (is_player_ready && is_player_in_team && is_team_ready == 0) {
        // wait for other teammates / other team to get ready
        if (is_player_ready_to_battle == 0) {
            tutorialGame();
        } else {
            waitGame();
        }
        drawText('Wait for players' + '  ...  ' + empty_slots + '  left');
        setCookie("start_time", 0);
        setCookie("end_time", 0);
    }
    if (is_player_ready && is_player_in_team && is_player_ready_to_battle && is_team_ready == 0) {
        // wait for other teammates / other team to get ready
        waitGame();
        drawText('Wait for players' + '  ...  ' + empty_slots + '  left');
        setCookie("start_time", 0);
        setCookie("end_time", 0);
    }
    if (is_player_ready && is_player_in_team && is_team_ready && is_ready == 0
        || is_player_ready && is_player_in_team && is_team_ready && is_ready && is_start == 0) {
        // wait for mech to get ready
        if (is_player_ready_to_battle == 0) {
            tutorialGame();
            drawText('Get ready...');
        } else if (is_all_player_ready_to_battle == 0) {
            waitGame();
            drawText('Wait for other players');
        } else {
            waitGame();
            drawText('Wait for Mech');
        }
        setCookie("start_time", 0);
        setCookie("end_time", 0);
    }
    if (is_player_ready && is_player_in_team && is_team_ready && is_ready && is_start && is_end == 0) {
        // start timer
        if (getCookie("start_time") == 0 || getCookie("end_time") == 0) {
            setCookie("start_time", new Date().getTime());
            setCookie("end_time", new Date().getTime() + 1000*60*3);
        }
        // game starts
        clearInterval(check_status); check_status = false;
        clearInterval(interval); interval = false;
        if (interval == false) {
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
        }

        // load game scene
        showLoading();
        setTimeout(function(){
            $('#grids').show();
            hideLoading();
        }, <?=Yii::$app->params['load_scene_time']?>);
        drawGameBg();
        drawCore(41);
    }

    if (is_player_ready && is_player_in_team && is_team_ready && is_ready && is_start && is_end) {
        if (getCookie("is_inspector") == 1) {
            clearInterval(interval); interval = false;
            if (check_status == false) {
                check_status = setInterval(function(){
                    if (not_able_to_request > 0) {
                        return;
                    }
                    not_able_to_request = 1;
                    checkStatus();
                }, <?=Yii::$app->params['refresh_rate']?>);
            }
            return;
        }
        setCookie("is_started", 0);
        setCookie("start_time", 0);
        setCookie("end_time", 0);
        // game ends
        clearInterval(check_status); check_status = false;
        clearInterval(interval); interval = false;
        endGame();
    }
}
function TeamUpGame() {
    hideScenes();

    var teamup = main.getChildByName("teamup");

    if (teamup == null) {
        teamup = new createjs.Container();
        teamup.name = "teamup";
        main.addChild(teamup);
    }
    main.setChildIndex(teamup, main.getNumChildren()-1);

    if (error_code == "teamup_error") {
        //
    } else if (getCookie('is_festival') && getCookie('team_id')) {
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
            main.update();
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
                teamup.setChildIndex(rect, teamup.getNumChildren()-1);
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
                            main.update();
                        }
                        var text_1 = new createjs.Text(teams[i]['limit'], '18px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                        text_1.x = 625;
                        text_1.y = 911;
                        text_1.textAlign = 'right';
                        teamup.addChild(text_1);
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
                            main.update();
                        }
                        var text_2 = new createjs.Text(teams[i]['limit'], '18px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                        text_2.x = 502;
                        text_2.y = 740;
                        teamup.addChild(text_2);
                    }
                }
                teamup.setChildIndex(rect, teamup.getNumChildren()-1);
                rect.on("click", function(event) {
                    setCookie('team_id', this.id);
                    selectTeam(this.id);
                });
            } else {
                if (i == 0){
                    teamup.removeChild(teamup.getChildByName("select_0"));
                    if (teamup.getChildByName("select_0_un") == null) {
                        var imag_3 = new Image();
                        imag_3.src = "<?=Yii::$app->params['select_0_un']?>";
                        imag_3.onload = function() {
                            var bitmap = new createjs.Bitmap(imag_3);
                            bitmap.name = "select_0_un";
                            bitmap.x = 580;
                            bitmap.y = 667;
                            teamup.addChild(bitmap);
                            main.update();
                        }
                        var text_3 = new createjs.Text(teams[i]['limit'], '18px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                        text_3.x = 625;
                        text_3.y = 911;
                        text_3.textAlign = 'right';
                        teamup.addChild(text_3);
                    }
                }
                else if (i == 1){
                    teamup.removeChild(teamup.getChildByName("select_1"));
                    if (teamup.getChildByName("select_1_un") == null) {
                        var imag_4 = new Image();
                        imag_4.src = "<?=Yii::$app->params['select_1_un']?>";
                        imag_4.onload = function() {
                            var bitmap = new createjs.Bitmap(imag_4);
                            bitmap.name = "select_1_un";
                            bitmap.x = 0;
                            bitmap.y = 667;
                            teamup.addChild(bitmap);
                            main.update();
                        }
                        var text_4 = new createjs.Text(teams[i]['limit'], '18px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                        text_4.x = 502;
                        text_4.y = 740;
                        teamup.addChild(text_4);
                    }
                }
            }
        }
    }
    main.update();
}
function tutorialGame() {
    hideScenes();

    var tutorials = main.getChildByName("tutorials");

    if (tutorials == null) {
        tutorials = new createjs.Container();
        tutorials.name = "tutorials";
        main.addChild(tutorials);
    }
    // main.setChildIndex(tutorials, main.getNumChildren()-1);

    tutorials.removeChild(tutorials.getChildByName("previous"));
    tutorials.removeChild(tutorials.getChildByName("next"));
    tutorials.removeChild(tutorials.getChildByName("finish"));

    if (getCookie("current_tutorial") >= 1 && getCookie("current_tutorial") <= 5) {
        if (tutorials.getChildByName("tutorial_"+getCookie("current_tutorial")) == null) {
            var cont = new createjs.Container();
            cont.name = "tutorial_"+getCookie("current_tutorial");
            if (cont.getChildByName("tutorial_image") == null) {
                var image;
                if (getCookie("current_tutorial") == 1) {
                    image = new Image();
                    image.src = "<?=Yii::$app->params['tutorial_1']?>";
                } else if (getCookie("current_tutorial") == 2) {
                    image = new Image();
                    image.src = "<?=Yii::$app->params['tutorial_2']?>";
                } else if (getCookie("current_tutorial") == 3) {
                    image = new Image();
                    image.src = "<?=Yii::$app->params['tutorial_3']?>";
                } else if (getCookie("current_tutorial") == 4) {
                    image = new Image();
                    image.src = "<?=Yii::$app->params['tutorial_4']?>";
                } else if (getCookie("current_tutorial") == 5) {
                    image = new Image();
                    image.src = "<?=Yii::$app->params['tutorial_5']?>";
                }
                if (image) {
                    image.onload = function() {
                        var bitmap = new createjs.Bitmap(image);
                        bitmap.name = "tutorial_image";
                        cont.addChild(bitmap);
                        main.update();
                    }
                }
            }
            tutorials.addChild(cont);
            tutorials.setChildIndex(cont, tutorials.getNumChildren()-1);
        } else {
            var cont = tutorials.getChildByName("tutorial_"+getCookie("current_tutorial"));
            tutorials.setChildIndex(cont, tutorials.getNumChildren()-1);
        }
    }

    if (getCookie("current_tutorial") > 1 && getCookie("current_tutorial") <= 5) {
        if (tutorials.getChildByName("previous") == null) {
            var rect = new createjs.Shape();
            rect.graphics.beginFill("#ff0000").drawRect(0, 0, 100, 100);
            rect.x = 460;
            rect.y = 720;
            rect.alpha = 0.01;
            rect.name = "previous";
            rect.on("click", function(event) {
                setCookie('current_tutorial', parseInt(getCookie("current_tutorial"))-1);
            });
            tutorials.addChild(rect);
            tutorials.setChildIndex(rect, tutorials.getNumChildren()-1);
        }
    }

    if (getCookie("current_tutorial") >= 1 && getCookie("current_tutorial") < 5) {
        if (tutorials.getChildByName("next") == null) {
            var rect = new createjs.Shape();
            rect.graphics.beginFill("#ffff00").drawRect(0, 0, 100, 100);
            rect.x = 640;
            rect.y = 720;
            rect.alpha = 0.01;
            rect.name = "next";
            rect.on("click", function(event) {
                setCookie('current_tutorial', parseInt(getCookie("current_tutorial"))+1);
            });
            tutorials.addChild(rect);
            tutorials.setChildIndex(rect, tutorials.getNumChildren()-1);
        }
    }

    if (getCookie("current_tutorial") >= 5) {
        if (tutorials.getChildByName("finish") == null) {
            var rect = new createjs.Shape();
            rect.graphics.beginFill("#0000ff").drawRect(0, 0, 200, 100);
            rect.x = 260;
            rect.y = 620;
            rect.alpha = 0.01;
            rect.name = "finish";
            rect.on("click", function(event) {
                setCookie('current_tutorial', parseInt(getCookie("current_tutorial"))+1);
                finishTutorial();
            });
            tutorials.addChild(rect);
            tutorials.setChildIndex(rect, tutorials.getNumChildren()-1);
        }
    }

    main.update();
}
function waitGame() {
    hideScenes();

    var wait = main.getChildByName("wait");

    if (wait == null) {
        wait = new createjs.Container();
        wait.name = "wait";
        main.addChild(wait);
    }
    // main.setChildIndex(wait, main.getNumChildren()-1);

    if (wait.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['wait_image']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            wait.addChild(bitmap);
            wait.setChildIndex(bitmap,0);
            main.update();
        }
    }

    if (team_players && teams) {
        wait_players = wait.getChildByName("players");
        if (wait_players == null) {
            wait_players = new createjs.Container();
            wait_players.name = "players";
            wait.addChild(wait_players);
        }

        for (var i=0; i<teams.length; i++) {
            // team limits
            if (wait.getChildByName("count_"+i) == null) {
                if (teams[i]['id'] == 2) { // ice
                    var text1 = new createjs.Text(teams[i]['limit'], '18px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                    text1.x = 610;
                    text1.y = 863;
                    text1.textAlign = 'right';
                    text1.alpha = 0.8;
                    text1.name = "count_"+i;
                    wait.addChild(text1);
                    wait.setChildIndex(text1, wait.getNumChildren()-1);
                } else if (teams[i]['id'] == 3) { // fire
                    var text2 = new createjs.Text(teams[i]['limit'], '18px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                    text2.x = 495;
                    text2.y = 320;
                    text2.textAlign = 'left';
                    text2.alpha = 0.8;
                    text2.name = "count_"+i;
                    wait.addChild(text2);
                    wait.setChildIndex(text2, wait.getNumChildren()-1);
                }
            }

            // empty slots
            for (var j=0; j<teams[i]['limit']; j++) {
                if (wait_players.getChildByName("empty_"+i+"_"+j) == null
                && wait_players.getChildByName("ready_"+i+"_"+j) == null
                && wait_players.getChildByName("unready_"+i+"_"+j) == null) {
                    var image_0 = new Image();
                    image_0.team = i;
                    image_0.team_id = teams[i]['id'];
                    image_0.id = j;
                    image_0.src = "<?=Yii::$app->params['wait_0_empty']?>";
                    image_0.onload = function() {
                        var bitmap = new createjs.Bitmap(image_0);
                        bitmap.name = "empty_"+this.team+"_"+this.id;
                        wait_players.addChild(bitmap);
                        wait_players.setChildIndex(bitmap,wait_players.getNumChildren()-1);
                        if (this.team_id == 2) { // ice
                            if (this.id<3) {
                                bitmap.x = 225 + this.id*130;
                                bitmap.y = 627;
                            } else if (this.id<6) {
                                bitmap.x = 165 + (this.id-3)*130;
                                bitmap.y = 790;
                            }
                        } else if (this.team_id == 3) { // fire
                            if (this.id<3) {
                                bitmap.x = 676 + this.id*130;
                                bitmap.y = 251;
                            } else if (this.id<6) {
                                bitmap.x = 610 + (this.id-3)*130;
                                bitmap.y = 411;
                            }
                        }
                        main.update();
                    }
                }
            }

            // current players in this round
            var players = team_players[teams[i]['id']];
            if (!players) {
                continue;
            }
            for (var j=0; j<players.length; j++) {
                // console.log(players[j]);
                if (players[j]) {
                    // name
                    if (wait_players.getChildByName(i+"_name_"+j) == null) {
                        var text = new createjs.Text(players[j]['name'], '20px <?=Yii::$app->params['font2']?>', '<?=Yii::$app->params['main_text_color']?>');
                        text.textAlign = 'center';
                        text.name = i+"_name_"+j;
                        if (players[j]['team_id'] == 2) { // ice
                            if (j<=3) {
                                text.x = 275 + j*130;
                                text.y = 751;
                            } else if (j<=6) {
                                text.x = 215 + j*130;
                                text.y = 914;
                            }
                        } else if (players[j]['team_id'] == 3) { // fire
                            if (j<=3) {
                                text.x = 726 + j*130;
                                text.y = 375;
                            } else if (j<=6) {
                                text.x = 660 + j*130;
                                text.y = 535;
                            }
                        }
                        wait_players.addChild(text);
                    }

                    // icon
                    if (players[j]['is_ready'] == 1) {
                        wait_players.removeChild(wait_players.getChildByName("empty_"+i+"_"+j));
                        wait_players.removeChild(wait_players.getChildByName("unready_"+i+"_"+j));
                        if (wait_players.getChildByName("ready_"+i+"_"+j) == null) {
                            if (players[j]['team_id'] == 2) { // ice
                                var image_1 = new Image();
                                image_1.team = i;
                                image_1.id = j;
                                image_1.src = "<?=Yii::$app->params['wait_0_ready']?>";
                                image_1.onload = function() {
                                    var bitmap = new createjs.Bitmap(image_1);
                                    bitmap.name = "ready_"+this.team+"_"+this.id;
                                    wait_players.addChild(bitmap);
                                    wait_players.setChildIndex(bitmap,wait_players.getNumChildren()-1);
                                    if (this.id<3) {
                                        bitmap.x = 225 + this.id*130;
                                        bitmap.y = 627;
                                    } else if (this.id<6) {
                                        bitmap.x = 165 + (this.id-3)*130;
                                        bitmap.y = 790;
                                    }
                                    main.update();
                                }
                            } else if (players[j]['team_id'] == 3) { // fire
                                var image_2 = new Image();
                                image_2.team = i;
                                image_2.id = j;
                                image_2.src = "<?=Yii::$app->params['wait_1_ready']?>";
                                image_2.onload = function() {
                                    var bitmap = new createjs.Bitmap(image_2);
                                    bitmap.name = "ready_"+this.team+"_"+this.id;
                                    wait_players.addChild(bitmap);
                                    wait_players.setChildIndex(bitmap,wait_players.getNumChildren()-1);
                                    if (this.id<3) {
                                        bitmap.x = 676 + this.id*130;
                                        bitmap.y = 251;
                                    } else if (this.id<6) {
                                        bitmap.x = 610 + (this.id-3)*130;
                                        bitmap.y = 411;
                                    }
                                    main.update();
                                }
                            }
                        }
                    } else {
                        wait_players.removeChild(wait_players.getChildByName("empty_"+i+"_"+j));
                        if (wait_players.getChildByName("unready_"+i+"_"+j) == null) {
                            if (players[j]['team_id'] == 2) { // ice
                                var image_3 = new Image();
                                image_3.team = i;
                                image_3.id = j;
                                image_3.src = "<?=Yii::$app->params['wait_0_unready']?>";
                                image_3.onload = function() {
                                    var bitmap = new createjs.Bitmap(image_3);
                                    bitmap.name = "unready_"+this.team+"_"+this.id;
                                    wait_players.addChild(bitmap);
                                    wait_players.setChildIndex(bitmap,wait_players.getNumChildren()-1);
                                    if (this.id<3) {
                                        bitmap.x = 225 + this.id*130;
                                        bitmap.y = 627;
                                    } else if (this.id<6) {
                                        bitmap.x = 165 + (this.id-3)*130;
                                        bitmap.y = 790;
                                    }
                                    main.update();
                                }
                            } else if (players[j]['team_id'] == 3) { // fire
                                var image_4 = new Image();
                                image_4.team = i;
                                image_4.id = j;
                                image_4.src = "<?=Yii::$app->params['wait_1_unready']?>";
                                image_4.onload = function() {
                                    var bitmap = new createjs.Bitmap(image_4);
                                    bitmap.name = "unready_"+this.team+"_"+this.id;
                                    wait_players.addChild(bitmap);
                                    wait_players.setChildIndex(bitmap,wait_players.getNumChildren()-1);
                                    if (this.id<3) {
                                        bitmap.x = 676 + this.id*130;
                                        bitmap.y = 251;
                                    } else if (this.id<6) {
                                        bitmap.x = 610 + (this.id-3)*130;
                                        bitmap.y = 411;
                                    }
                                    main.update();
                                }
                            }
                        }
                    }
                }
            }
        }

    }


    if (players) {
        wait_players = wait.getChildByName("players");
        if (wait_players == null) {
            wait_players = new createjs.Container();
            wait_players.name = "players";
            wait.addChild(wait_players);
        }
        /*
        for (var i=0; i<players.length; i++) {
            if (players[i]['is_ready'] == 1) {
                if (wait_players.getChildByName("ready_"+i) == null) {
                    var image_1 = new Image();
                    image_1.id = i;
                    if (players[i]['team_id'] == 2) { // ice
                        image_1.src = "<?=Yii::$app->params['wait_0_ready']?>";
                        image_1.onload = function() {
                            var bitmap = new createjs.Bitmap(image_1);
                            bitmap.name = "ready_"+this.id;
                            wait_players.addChild(bitmap);
                            wait_players.setChildIndex(bitmap,wait_players.getNumChildren()-1);
                            if (this.id<=3) {
                                bitmap.x = 225 + this.id*130;
                                bitmap.y = 627;
                            } else if (this.id<=6) {
                                bitmap.x = 165 + (this.id-3)*130;
                                bitmap.y = 790;
                            }
                            main.update();
                        }
                    } else if (players[i]['team_id'] == 3) { // fire
                        image_1.src = "<?=Yii::$app->params['wait_1_ready']?>";
                        image_1.onload = function() {
                            var bitmap = new createjs.Bitmap(image_1);
                            bitmap.name = "ready_"+this.id;
                            wait_players.addChild(bitmap);
                            wait_players.setChildIndex(bitmap,wait_players.getNumChildren()-1);
                            if (this.id<=3) {
                                bitmap.x = 676 + this.id*130;
                                bitmap.y = 251;
                            } else if (this.id<=6) {
                                bitmap.x = 610 + (this.id-3)*130;
                                bitmap.y = 411;
                            }
                            main.update();
                        }
                    }
                }
            } else {
                if (wait_players.getChildByName("unready_"+i) == null) {
                    var image_2 = new Image();
                    image_2.id = i;
                    if (players[i]['team_id'] == 2) { // ice
                        image_2.src = "<?=Yii::$app->params['wait_0_unready']?>";
                        image_2.onload = function() {
                            var bitmap = new createjs.Bitmap(image_2);
                            bitmap.name = "unready_"+this.id;
                            wait_players.addChild(bitmap);
                            wait_players.setChildIndex(bitmap,wait_players.getNumChildren()-1);
                            if (this.id<=3) {
                                bitmap.x = 225 + this.id*130;
                                bitmap.y = 627;
                            } else if (this.id<=6) {
                                bitmap.x = 165 + (this.id-3)*130;
                                bitmap.y = 790;
                            }
                            main.update();
                        }
                    } else if (players[i]['team_id'] == 3) { // fire
                        image_2.src = "<?=Yii::$app->params['wait_1_unready']?>";
                        image_2.onload = function() {
                            var bitmap = new createjs.Bitmap(image_2);
                            bitmap.name = "unready_"+this.id;
                            wait_players.addChild(bitmap);
                            wait_players.setChildIndex(bitmap,wait_players.getNumChildren()-1);
                            if (this.id<=3) {
                                bitmap.x = 676 + this.id*130;
                                bitmap.y = 251;
                            } else if (this.id<=6) {
                                bitmap.x = 610 + (this.id-3)*130;
                                bitmap.y = 411;
                            }
                            main.update();
                        }
                    }
                }
            }
        }
        */
    }

    main.update();
}
function drawBackground() {
    var background = main.getChildByName("background");

    if (background == null) {
        background = new createjs.Container();
        background.name = "background";
        main.addChild(background);
    }
    main.setChildIndex(background, 0);

    if (background.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['bg_image']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            background.addChild(bitmap);
            background.setChildIndex(bitmap,0);
            main.update();
        }
    }

    main.update();
    $("#main").show();
}
function drawCore(id) {
    var game = main.getChildByName("game");

    if (game == null) {
        game = new createjs.Container();
        game.name = "login";
        main.addChild(game);
    }
    main.setChildIndex(game, main.getNumChildren()-1);

    var towers = game.getChildByName("towers");

    if (towers == null) {
        towers = new createjs.Container();
        towers.name = "towers";
        game.addChild(towers);
    }
    game.setChildIndex(towers, game.getNumChildren()-1);

    if (towers.getChildByName('tower_' + id) == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['core']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            var x = (id-1)%<?=$column?>;
            var y = Math.floor((id-1)/<?=$column?>);
            bitmap.x = x*<?=$grid_width?> + <?=$grid_width/2?> - image.width/2 + <?=$offset_x?>;
            bitmap.y = y*<?=$grid_height?> + <?=$grid_height/2?> - image.height/2 + <?=$offset_y?>;
            bitmap.name = "tower_"+id;
            towers.addChild(bitmap);
            towers.setChildIndex(bitmap, main.getNumChildren()-1);
            main.update();
        };
    }

    main.update();
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
                } else {
                    error_code = 'teamup_error';
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
                    resource = response.data.resource;
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
                    drawText(null, null, round_score, null, team_score_1, team_score_2, core_score, resource);
                }
            }
            if (is_end) {
                if (getCookie("is_inspector")) {
                    location.reload();
                }
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
                if (response.data.is_inspector) {
                    setCookie("is_inspector", 1);
                    loginInspector();
                    return;
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

    clearInterval(check_status); check_status = false;
    clearInterval(interval); interval = false;

    var keycode = main.getChildByName("keycode");

    if (keycode == null) {
        keycode = new createjs.Container();
        keycode.name = "keycode";
        main.addChild(keycode);
    }
    main.setChildIndex(keycode, main.getNumChildren()-1);

    if (keycode.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['keycode']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            keycode.addChild(bitmap);
            keycode.setChildIndex(bitmap, 0);
            main.update();
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
            startGame();
            // location.reload();
        });
        keycode.addChild(rect);
        keycode.setChildIndex(rect, keycode.getNumChildren()-1);
    }

    main.update();
    $('#keycode-form').show();
}

function loginGame() {
    hideScenes();

    clearInterval(check_status); check_status = false;
    clearInterval(interval); interval = false;

    var login = main.getChildByName("login");

    if (login == null) {
        login = new createjs.Container();
        login.name = "login";
        main.addChild(login);
    }
    main.setChildIndex(login, main.getNumChildren()-1);

    if (login.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['login']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            login.addChild(bitmap);
            login.setChildIndex(bitmap, 0);
            main.update();
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
        } else {
            login.getChildByName('login_hint').text = error;
        }
    }

    main.update();
    $('#login-form').show();
}

function leaderboardGame() {
    hideScenes();

    clearInterval(check_status); check_status = false;
    clearInterval(interval); interval = false;

    setCookie("leaderboard", 1);

    var leaderboard = main.getChildByName("leaderboard");

    if (leaderboard == null) {
        leaderboard = new createjs.Container();
        leaderboard.name = "leaderboard";
        main.addChild(leaderboard);
    }
    main.setChildIndex(leaderboard, main.getNumChildren()-1);

    if (leaderboard.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['leaderboard']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            leaderboard.addChild(bitmap);
            leaderboard.setChildIndex(bitmap, 0);
            main.update();
        }
    }

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
            startGame();
            // location.reload();
        });
        leaderboard.addChild(rect);
        leaderboard.setChildIndex(rect, leaderboard.getNumChildren()-1);
    }

    main.update();
    $("#main").show();
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
            } else if (getCookie('is_festival')) {
                joinGame(secret);
            } else {
                keycodeGame();
            }
        }
    });
}

function startGame() {
    hideScenes();

    clearInterval(interval); interval = false;
    clearInterval(check_status); check_status = false;
    setCookie("start", 1);

    var start = main.getChildByName("start");

    if (start == null) {
        start = new createjs.Container();
        start.name = "start";
        main.addChild(start);
    }
    main.setChildIndex(start, main.getNumChildren()-1);

    if (start.getChildByName("bg") == null) {
        var image = new Image();
        image.src = "<?=Yii::$app->params['start_image']?>";
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            bitmap.name = "bg";
            start.addChild(bitmap);
            start.setChildIndex(bitmap, 0);
            main.update();
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
    }

    if (start.getChildByName("logout") == null) {
        var rect = new createjs.Shape();
        rect.graphics.beginFill("#000000").drawRect(0, 0, 400, 120);
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
    }

    if (is_login && name) {
        if (start.getChildByName('login_hint') == null) {
            var text = new createjs.Text('welcome, ' + name, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            text.textAlign = 'center';
            text.name = 'login_hint';
            text.x = <?=$scene_width/2?>;
            text.y = 960;
            start.addChild(text);
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
            text.y = 930;
            start.addChild(text);
        }
    } else if (is_open) {
        if (start.getChildByName('start_hint')) {
            start.removeChild(start.getChildByName('start_hint'));
        }
    }

    main.update();
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
    $('#grids').show();
    if (getCookie("is_inspector")) {
        $('#grids').hide();
    }
}
function drawText(hint, score, round_score, rank, team_score_1, team_score_2, core, resource) {
    // hint at the bottom
    if (hint != null) {
        if (main.getChildByName("hint") == null) {
            string = new createjs.Text('Hint', '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            string.textAlign = 'center';
            string.name = 'hint';
            string.x = <?=$scene_width/2?>;
            string.y = 960;
            string.alpha = 0.8;
            main.addChild(string);
            main.setChildIndex(string, main.getNumChildren()-1);
            main.update();
        }
        main.getChildByName("hint").text = hint;
        main.setChildIndex(main.getChildByName("hint"), main.getNumChildren()-1);
    } else {
        // main.removeChild("hint");
    }

    // timer
    if (main.getChildByName("timer") == null) {
        var string = new createjs.Text(game_time, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
        string.textAlign = 'right';
        string.name = 'timer';
        string.x = 430;
        string.y = 245;
        string.alpha = 0.8;
        string.shadow = new createjs.Shadow("#000000", 0, 5, 10);
        main.addChild(string);
        main.setChildIndex(string, main.getNumChildren()-1);
        main.update();
    }
    main.getChildByName("timer").text = game_time;

    // core
    if (core_score != null && total_core_score != null) {
        if (main.getChildByName("core_score") == null) {
            var string = new createjs.Text(core_score, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
            string.textAlign = 'right';
            string.name = 'core_score';
            string.x = 430;
            string.y = 430;
            string.alpha = 0.8;
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
            var string = new createjs.Text(team_score_1, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
            string.textAlign = 'right';
            string.name = 'team_score_1';
            string.x = 430;
            string.y = 600;
            string.alpha = 0.8;
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
            var string = new createjs.Text(team_score_2, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
            string.textAlign = 'right';
            string.name = 'team_score_2';
            string.x = 430;
            string.y = 510;
            string.alpha = 0.8;
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
            string.alpha = 0.8;
            string.shadow = new createjs.Shadow("#000000", 0, 5, 10);
            main.addChild(string);
            main.setChildIndex(string, main.getNumChildren()-1);
            main.update();
        }
        main.getChildByName("round_score").text = round_score;
    }

    // resource
    if (resource != null) {
        if (main.getChildByName("resource") == null) {
            var string = new createjs.Text(resource, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
            string.textAlign = 'right';
            string.name = 'resource';
            string.x = 430;
            string.y = 685;
            string.alpha = 0.8;
            string.shadow = new createjs.Shadow("#000000", 0, 5, 10);
            main.addChild(string);
            main.setChildIndex(string, main.getNumChildren()-1);
            main.update();
        }
        main.getChildByName("resource").text = resource;
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
    var game = main.getChildByName('game');
    if (game == null) {
        var game = new createjs.Container();
        game.name = "game";
        main.addChild(game);
    }

    var main_towers = game.getChildByName('towers');
    if (main_towers == null) {
        var main_towers = new createjs.Container();
        main_towers.name = "towers";
        game.addChild(main_towers);
    }

    if (main_towers.getChildByName('remain_' + id) == null) {
        var x = (id-1)%<?=$column?>;
        var y = Math.floor((id-1)/<?=$column?>);

        var remain;
        if (team_id == 2) {
            var image = new Image();
            image.src = "<?=Yii::$app->params['team_remain_0']?>";
            image.onload = function() {
                remain = new createjs.Bitmap(image);
                remain.x = x*<?=$grid_width?> - <?=$grid_width*3/4?> + <?=$offset_x?>;
                remain.y = y*<?=$grid_height?> - <?=$grid_height*3/4?> + <?=$offset_y?>;
                remain.name = 'remain_' + id;
                main_towers.addChild(remain);
            }
        } else if (team_id == 3) {
            var image = new Image();
            image.src = "<?=Yii::$app->params['team_remain_1']?>";
            image.onload = function() {
                remain = new createjs.Bitmap(image);
                remain.x = x*<?=$grid_width?> - <?=$grid_width*3/4?> + <?=$offset_x?>;
                remain.y = y*<?=$grid_height?> - <?=$grid_height*3/4?> + <?=$offset_y?>;
                remain.name = 'remain_' + id;
                main_towers.addChild(remain);
            }
        }

        var rect = new createjs.Shape();
        rect.graphics.beginFill("<?=Yii::$app->params['main_color']?>").drawRect(0, 0, <?=$grid_width?>, <?=$grid_height?>);
        rect.alpha = 0.8;
        rect.x = x*<?=$grid_width?> + <?=$offset_x?>;
        rect.y = y*<?=$grid_height?> + <?=$offset_y?>;
        rect.name = "block";
        main_towers.addChild(rect);

        createjs.Ticker.on("tick", tick);
        createjs.Ticker.setFPS(10);
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
            main_towers.removeChild(remain);
            main_towers.removeChild(rect);
            main.update();
        }, <?=Yii::$app->params['cool_down_time']?>);
    }

    main.update();
}
function clearTower(id) {
    var game = main.getChildByName('game');
    if (game == null) {
        var game = new createjs.Container();
        game.name = "game";
        main.addChild(game);
    }

    var main_towers = game.getChildByName('towers');
    if (main_towers == null) {
        var main_towers = new createjs.Container();
        main_towers.name = "towers";
        game.addChild(main_towers);
    }

    var tower = main_towers.getChildByName('tower_' + id);
    if (tower) {
        main_towers.removeChild(tower);
    }

    var main_score_rates = game.getChildByName('score_rates');
    if (main_score_rates == null) {
        var main_score_rates = new createjs.Container();
        main_score_rates.name = "score_rates";
        game.addChild(main_score_rates);
    }

    var score_rate = main_score_rates.getChildByName('tower_' + id + '_score_rate');
    if (score_rate) {
        main_score_rates.removeChild(score_rate);
    }
    main.update();
}
function drawTower(id, team_id, tower_player_id, score_rate) {
    if (score_rate == null){
        score_rate = 0;
    }

    if (team_id < 2) {
        return;
    }

    var game = main.getChildByName('game');
    if (game == null) {
        var game = new createjs.Container();
        game.name = "game";
        main.addChild(game);
    }

    var main_towers = game.getChildByName('towers');
    if (main_towers == null) {
        var main_towers = new createjs.Container();
        main_towers.name = "towers";
        game.addChild(main_towers);
    }

    if (main_towers.getChildByName('tower_' + id) == null) {
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
        main_towers.addChild(tower);
        // bubble sorting for 1 iteration
        for (var i = 0; i < <?=$row-2?>; i++) {
            if (id+<?=$column?>*i >= <?=$column*$row?>) {
                break;
            }
            var tower_prev = main_towers.getChildByName('tower_' + (id+<?=$column?>*i));
            var tower_next = main_towers.getChildByName('tower_' + (id+<?=$column?>*(i+1)));
            if (tower_next) {
                main_towers.swapChildren(tower_prev, tower_next);
            }
        }
    }

    var main_score_rates = game.getChildByName('score_rates');
    if (main_score_rates == null) {
        var main_score_rates = new createjs.Container();
        main_score_rates.name = "score_rates";
        game.addChild(main_score_rates);
    }

    if (tower_player_id == player_id) {
        if (main_score_rates.getChildByName('tower_' + id + '_score_rate') == null) {
            var text = new createjs.Text('+ ' + score_rate, '30px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
            text.textAlign = 'center';
            text.name = 'tower_' + id + '_score_rate';
            text.x = x*<?=$grid_width?> + <?=$grid_width*0.5?> + <?=$offset_x?>;
            text.y = y*<?=$grid_height?> - <?=$grid_height*0.4?>+ <?=$offset_y?>;
            main_score_rates.addChild(text);
            main_score_rates.setChildIndex(text, main_score_rates.getNumChildren()-1);

            createjs.Ticker.on("tick", tick);
            createjs.Ticker.setFPS(10);
            createjs.Ticker.timingMode = createjs.Ticker.RAF_SYNCHED;
            function tick (event) {
                text.y -= 4;
                text.alpha -= 0.2;
                if (text.y <= y*<?=$grid_height?> - <?=$grid_height*0.8?> + <?=$offset_y?>) {
                    text.y = y*<?=$grid_height?> - <?=$grid_height*0.4?>+ <?=$offset_y?>;
                    text.alpha = 1;
                }
                main.update();
            }
        } else {
            main_score_rates.getChildByName('tower_' + id + '_score_rate').text = '+ ' + score_rate;
        }

        if (score_rate == 0) {
            main_score_rates.getChildByName('tower_' + id + '_score_rate').text = '';
        }
    }

    main.update();
}
function drawGameBg() {
    var game = main.getChildByName('towers');
    if (game == null) {
        var game = new createjs.Container();
        game.name = "game";
        main.addChild(game);
    }

    if (game.getChildByName("bg") == null) {
        if (team_id == 2) {
            var image = new Image();
            image.src = "<?=Yii::$app->params['game_image_0']?>";
            image.onload = function() {
                var bitmap = new createjs.Bitmap(image);
                bitmap.name = "bg";
                game.addChild(bitmap);
                game.setChildIndex(bitmap, 0);
                main.update();
            }
        } else {
            var image = new Image();
            image.src = "<?=Yii::$app->params['game_image_1']?>";
            image.onload = function() {
                var bitmap = new createjs.Bitmap(image);
                bitmap.name = "bg";
                game.addChild(bitmap);
                game.setChildIndex(bitmap, 0);
                main.update();
            }
        }
    }

    main.update();
}
function hideScenes() {
    $('#keycode-form').hide();
    $('#login-form').hide();
    $('#grids').hide();
}
function endGame() {
    hideScenes();

    clearInterval(interval); interval = false;
    clearInterval(check_status); check_status = false;
    setCookie("end", 1);

    var end = main.getChildByName("end");

    if (end == null) {
        end = new createjs.Container();
        end.name = "end";
        main.addChild(end);
    }
    main.setChildIndex(end, main.getNumChildren()-1);

    if (end.getChildByName("bg") == null) {
        var image1 = new Image();
        image1.src = "<?=Yii::$app->params['end_image']?>";
        image1.onload = function() {
            var bitmap = new createjs.Bitmap(image1);
            bitmap.name = "bg";
            end.addChild(bitmap);
            end.setChildIndex(bitmap, 0);
            main.update();
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
                main.update();
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

    if (end.getChildByName("restart") == null) {
        rect = new createjs.Shape();
        rect.graphics.beginFill("#000000").drawRect(0, 0, 400, 160);
        rect.x = <?=$scene_width/2 - 200?>;
        rect.y = 740;
        rect.alpha = 0.01;
        rect.name = "restart";
        rect.on("click", function(event) {
            //alert(this.id);
            is_open = 1;
            setCookie("end", 0);
            // automatically logout for festival demo devices
            if (getCookie("is_festival")) {
                logout();
            } else {
                startGame();
            }
        });
        end.addChild(rect);
        end.setChildIndex(rect, end.getNumChildren()-1);
    }

    main.update();
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

function loginInspector () {
    $.ajax({
        // method: "POST",
        url: "<?= '../../api/web/index.php?r=round/login-inspector'; ?>",
        dataType : 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                setCookie("key", response.data.player.key, 7);
                is_login = 1;
                name = response.data.player.name;
                location.reload();
            }
        }
    });
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
                startGame();
                // location.reload();
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
                startGame();
                // location.reload();
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
            round_id: round_id,
        },
        dataType : 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                is_player_ready_to_battle = response.data.is_player_ready_to_battle;
                is_all_player_ready_to_battle = response.data.is_all_player_ready_to_battle;
                if (is_player_ready_to_battle) {
                    // go to waiting scene (hide tutorial)
                    checkStatus();
                } else {
                    //
                }
            } else {
                //
            }
        }
    });
}
</script>
