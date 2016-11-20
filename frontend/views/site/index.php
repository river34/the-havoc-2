<?php

/* @var $this yii\web\View */
use yii\helpers\Url;

$this->title = 'The Havoc';

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
?>
<!doctype html>
<head>
    <meta charset="utf-8">
    <title>Hello World</title>
    <link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
    <script src="https://code.createjs.com/easeljs-0.8.2.min.js"></script>
    <script src="https://code.createjs.com/createjs-2015.11.26.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
    // canvas
    var text;
    var background;
    var main;
    var teamup;
    var info;
    var start;
    var end;

    // params
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
    var team_id;
    var round_id;
    var teams;

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

        init_draw();

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

        check_status = setInterval(function(){
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
                hideLoading();
                setCookie("key", response.data.player.key, 7);
                if (response.success) {
                    is_open = response.data.is_open;
                    is_player_ready = response.data.is_player_ready;
                    is_player_in_team = response.data.is_player_in_team;
                    is_mech_ready = response.data.is_mech_ready;
                    is_team_ready = response.data.is_team_ready;
                    is_ready = response.data.is_ready;
                    is_start = response.data.is_start;
                    is_end = response.data.is_end;
                    is_win = response.data.is_win;
                    if (is_player_ready == 0 && is_start) { // player is not in current game
                        player_id = response.data.player.id;
                    }
                    if (is_player_ready && is_start) {
                        player_id = response.data.player.id;
                        resource = response.data.roundTeamPlayer.resource;
                        score = response.data.player.score;
                        round_score = response.data.roundTeamPlayer.score;
                        round_id = response.data.round.id;
                        team_id = response.data.roundTeamPlayer.team_id;
                    }
                    if (is_player_ready && is_player_in_team == 0) {
                        player_id = response.data.player.id;
                        teams = response.data.teams;
                        round_id = response.data.round.id;
                    }
                } else {
                    is_open = 0;
                    is_player_ready = 0;
                    is_player_in_team = 0;
                    is_mech_ready = 0;
                    is_team_ready = 0;
                    is_ready = 0;
                    is_start = 0;
                    is_end = 0;
                    is_win = 0;
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

        drawBackground();
        drawCore(41);
        drawText();
    }
    function init_state() {
        $('#end').hide();
        $('#start').hide();

        if (getCookie("end") == 1) {
            // go to end screen
            clearInterval(check_status);
            clearInterval(interval);
            endGame();
            return;
        }
        if (getCookie("start") == 1) {
            // go to start screen
            clearInterval(check_status);
            clearInterval(interval);
            startGame();
            return;
        }
        if (getCookie("restart") == 1 && is_end == 1) {
            // wait for mech to start a new round
            drawInfo();
            drawText();
        }
        if (is_open == 0) {
            // wait for mech to start a new round
            drawInfo('tutorial');
        }
        if (is_open && is_mech_ready) {
            drawInfo('tutorial');
        }
        if (is_open && is_player_ready == 0 && is_start == 0) {
            // join this round
            startGame();
        }
        if (is_open && is_player_ready == 0 && is_start && is_end == 0) {
            // wait for current round to end
        }
        if (is_open && is_player_ready == 0 && is_start && is_end) {
            // current round is end
        }
        if (is_open && is_player_ready && is_player_in_team == 0) {
            drawTeams();
        }
        if (is_open && is_player_ready && is_player_in_team && is_team_ready == 0) {
            // wait for other teammates / other team to get ready
            drawInfo('tutorial');
        }
        if (is_open && is_player_ready && is_team_ready && is_ready == 0){
            // wait for mech to get ready
            drawInfo('tutorial');
        }
        if (is_open && is_player_ready && is_team_ready && is_ready && is_start == 0) {
            // wait for mech to get ready
            drawInfo('tutorial');
        }

        if (is_open && is_player_ready && is_start && is_end == 0) {
            // game starts
            clearInterval(check_status);
            clearInterval(interval);
            drawInfo('enter');
            $('#main').show();
            interval = setInterval(function(){
                updateMap();
            }, <?=Yii::$app->params['refresh_rate']?>);
        }

        if (is_open && is_player_ready && is_start && is_end && getCookie("restart") == 0) {
            // game ends
            clearInterval(check_status);
            clearInterval(interval);
            endGame();
        }
        if (is_open && is_player_ready && is_start && is_end && getCookie("restart") == 1) {
            //
        }

        drawText();
    }
    function drawTeams() {
        if (teams != null) {
            var odd;
            var color;
            for (var i=0; i<teams.length; i++) {
                var rect = teamup.getChildByName("team_"+teams[i]['id']);
                if (rect == null) {
                    color = colors[i];
                    rect = new createjs.Shape();
                    rect.graphics.beginFill(color).drawRect(0, 0, <?=$map_width?>/teams.length, <?=$map_height?>);
                    rect.x = i*<?=$map_width?>/teams.length + <?=$offset_x?>;
                    rect.y = <?=$offset_y?>;
                    rect.name = "team_"+teams[i]['id'];
                    rect.id = teams[i]['id'];
                    teamup.addChild(rect);
                }
                if (teams[i]['is_ready'] == 0) {
                    rect.on("click", function(event) {
                        // alert(this.id);
                        selectTeam(this.id);
                    });
                } else {
                    rect.graphics.beginFill('<?=Yii::$app->params['grey_color']?>').drawRect(0, 0, <?=$map_width?>/teams.length, <?=$map_height?>);
                    // rect.alpha = 0;
                }
                teamup.update();
            }
        }
        $('#teamup').show();
        $('#info').hide();
    }
    function drawInfo(index) {
        drawText();
        var rect = info.getChildByName("background");
        if (rect == null) {
            var rect = new createjs.Shape();
            rect.graphics.beginFill("<?=Yii::$app->params['background_color']?>").drawRect(0, 0, <?=$map_width?>, <?=$map_height?>);
            rect.x = <?=$offset_x?>;
            rect.y = <?=$offset_y?>;
            rect.name = "background";
            info.addChild(rect);
        }
        info.setChildIndex(rect, info.getNumChildren()-1);
        info.update();
        if (index == 'new_round') {
            var cont = info.getChildByName("new_round");
            if (cont == null) {
                var cont = new createjs.Container();
                cont.name = "new_round";

                //

                info.addChild(cont);
            }
            info.setChildIndex(cont, info.getNumChildren()-1);
            info.update();
        }
        else if (index == 'tutorial') {
            var cont = info.getChildByName("tutorial");
            if (cont == null) {
                var cont = new createjs.Container();
                cont.name = "tutorial";

                for (var i=0; i<4; i++) {
                    var x = i%2;
                    var y = Math.floor(i/2);

                    if (cont.getChildByName("rect_"+i) == null) {
                        var rect = new createjs.Shape();
                        rect.graphics.beginStroke('#AAAAAA');
                        rect.graphics.moveTo(0, 0)
                        .lineTo(0, 2*<?=$grid_height?>)
                        .lineTo(2*<?=$grid_width?>, 2*<?=$grid_height?>)
                        .lineTo(2*<?=$grid_width?>, 0)
                        .lineTo(0, 0);
                        rect.graphics.setStrokeDash([50, 50], 0);
                        rect.x = <?=$map_width/2?> + <?=$offset_x?> - 2*<?=$grid_width?> + 2*x*<?=$grid_width?>;
                        rect.y = <?=$map_height/2?> + <?=$offset_y?> - 2*<?=$grid_height?> + 2*y*<?=$grid_height?>;
                        rect.name = "rect_"+i;
                        cont.addChild(rect);
                    }

                    if (cont.getChildByName("cir_"+i) == null) {
                        if (i!=1) {
                            var cir = new createjs.Shape();
                            cir.graphics.beginStroke('<?=Yii::$app->params['main_color_2']?>');
                            cir.graphics.beginFill("<?=Yii::$app->params['main_color_2']?>").drawCircle(0, 0, <?=$grid_width/4?>);
                            cir.x = <?=$map_width/2?> + <?=$offset_x?> - <?=$grid_width?> + 2*x*<?=$grid_width?>;
                            cir.y = <?=$map_height/2?> + <?=$offset_y?> - <?=$grid_height?> + 2*y*<?=$grid_height?>;
                            cir.name = "cir_"+i;
                            cont.addChild(cir);

                            var cir = new createjs.Shape();
                            cir.graphics.beginStroke('<?=Yii::$app->params['main_color_2']?>');
                            cir.graphics.beginFill("<?=Yii::$app->params['main_color_2']?>").drawCircle(0, 0, <?=$grid_width/2?>);
                            cir.alpha = 0.2;
                            cir.x = <?=$map_width/2?> + <?=$offset_x?> - <?=$grid_width?> + 2*x*<?=$grid_width?>;
                            cir.y = <?=$map_height/2?> + <?=$offset_y?> - <?=$grid_height?> + 2*y*<?=$grid_height?>;
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
                    .lineTo(0, 2*<?=$grid_height?>)
                    .lineTo(2*<?=$grid_width?>, 2*<?=$grid_height?>)
                    .lineTo(0, 0);
                    tri.x = <?=$map_width/2?> + <?=$offset_x?> - <?=$grid_width?>;
                    tri.y = <?=$map_height/2?> + <?=$offset_y?> - <?=$grid_height?>;
                    tri.nmae = "tri";
                    cont.addChild(tri);
                }

                if (cont.getChildByName("hint") == null) {
                    var string = new createjs.Text('Isosceles Right Triangle makes you powerful', '36px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color_2']?>');
                    string.textAlign = 'center';
                    string.name = 'hint';
                    string.x = <?=$map_width/2?> + <?=$offset_x?>;
                    string.y = <?=$offset_y?> + 120;
                    cont.addChild(string);
                }

                info.addChild(cont);

                createjs.Ticker.on("tick", tick);
    			createjs.Ticker.setFPS(2);
                function tick (event) {
                    if (tri.alpha == 0.4) {
                        tri.alpha = 0.2;
                    } else {
                        tri.alpha = 0.4;
                    }
                    info.update();
        		}
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
                showLoading();

                info.addChild(cont);
            }

            // load game scene
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
        if (background.getChildByName("bg") == null) {
            var rect = new createjs.Shape();
            rect.graphics.beginFill("<?=Yii::$app->params['canvas_background_color']?>").drawRect(0, 0, <?=$scene_width?>, <?=$scene_height?>);
            rect.name = "bg";
            background.addChild(rect);
            background.update();
        }

        if (background.getChildByName("left_border") == null) {
            var rect = new createjs.Shape();
            rect.graphics.beginFill("<?=Yii::$app->params['main_color']?>").drawRect(<?=$offset_x?>, <?=$offset_y?>, 5, <?=$map_height?>);
            rect.name = "left_border";
            background.addChild(rect);
            background.update();
        }

        if (background.getChildByName("right_border") == null) {
            var rect = new createjs.Shape();
            rect.graphics.beginFill("<?=Yii::$app->params['main_color']?>").drawRect(<?=$map_width-5+$offset_x?>, <?=$offset_y?>, 5, <?=$map_height?>);
            rect.name = "right_border";
            background.addChild(rect);
            background.update();
        }

        if (background.getChildByName("gradient") == null) {
            var gradient = new createjs.Shape();
            gradient.graphics.beginLinearGradientFill(["rgba(255,255,255,0)","rgba(255,211,150,125)"], [0, 1], <?=$scene_width?>, <?=$offset_y?>, 0, <?=$offset_y?>).drawRect(<?=$offset_x+5?>, <?=$offset_y?>, <?=$map_width-5?>, <?=$map_height?>);
            gradient.name = "gradient";
            background.addChild(gradient);
            background.update();
        }
    }
    function drawCore(id) {
        var image = new Image();
        image.src = 'images/towers/icon_coretower.png';
        image.onload = function() {
            var bitmap = new createjs.Bitmap(image);
            var x = (id-1)%<?=$column?>;
            var y = Math.floor((id-1)/<?=$column?>);
            bitmap.x = x*<?=$grid_width?> + <?=$grid_width/2?> - image.width/2 + <?=$offset_x?>;
            bitmap.y = y*<?=$grid_height?> + <?=$grid_height/2?> - image.height/2 + <?=$offset_y?>;
            bitmap.name = "core";
            main.addChild(bitmap);
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
                        drawText();
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
                setCookie("key", response.data.player.key, 7);
                is_start = response.data.is_start;
                is_end = response.data.is_end;
                if (response.success) {
                    if (is_start && is_end==0) {
                        drawGame(response.data.grids, response.data.remains, response.data.triangles);
                    }
                    drawText();
                }
                if (is_end) {
                    endGame();
                }
            }
        });
    }
    function startGame() {
        showLoading();
        $.ajax({
            // method: "POST",
            url: "<?= '../../api/web/index.php?r=round/start'; ?>",
            data: {
                key: getCookie('key'),
            },
            dataType : 'json',
            success: function(response) {
                hideLoading();
                setCookie("key", response.data.player.key, 7);
                if (response.success) {
                    is_open = response.data.is_open;
                    if (is_open == 0) {
                        drawStart();
                    }
                }
            }
        });
    }
    function drawStart() {
        $('#end').hide();
        if (start.getChildByName("bg") == null) {
            var rect = new createjs.Shape();
            rect.graphics.beginFill("<?=Yii::$app->params['background_color']?>").drawRect(0, 0, <?=$scene_width?>, <?=$scene_height?>);
            rect.name = "bg";
            start.addChild(rect);
        }

        if (start.getChildByName("title") == null) {
            var text = new createjs.Text('The Havoc', '120px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
            text.textAlign = 'center';
            text.name = "title";
            text.x = <?=$scene_width/2?>;
            text.y = <?=$scene_height/2?> - 160;
            start.addChild(text);
        }

        if (start.getChildByName("start") == null) {
            var text = new createjs.Text('Start', '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            text.textAlign = 'center';
            text.name = "start";
            text.x = <?=$scene_width/2?>;
            text.y = <?=$scene_height/2?> + 200;
            text.on("click", function(event) {
                setCookie("start", 0);
                setCookie("restart", 1);
                location.reload();
            });
            start.addChild(text);
        }

        start.update();
        $('#start').show();
    }
    function drawGame(grids, remains, triangles) {
        for (var i=0; i<grids.length; i++) {
            drawTower(grids[i]['id'], grids[i]['team_id'], grids[i]['player_id'], grids[i]['score_rate']);
        }
        for (var i=0; i<remains.length; i++) {
            clearTower(grids[i]['id']);
            drawRemain(grids[i]['id']);
        }
        clearTriangkes();
        for (var i=0; i<triangles.length; i++) {
            drawTriangle(triangles[i]['a'], triangles[i]['b'], triangles[i]['c'], triangles[i]['team_id']);
        }
    }
    function drawText() {
        if (background.getChildByName("hint") == null) {
            var string = new createjs.Text('Hint', '48px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            string.textAlign = 'center';
            string.name = 'hint';
            string.x = <?=$map_width/2?> + <?=$offset_x?>;
            string.y = 220;
            background.addChild(string);
        }

        if (getCookie("restart") == 1 && is_end == 1) {
            background.getChildByName("hint").text = 'Wait for VR';
            background.update();
            return;
        }
        if (is_open == 0) {
            background.getChildByName("hint").text = 'Wait for VR';
            background.update();
            return;
        }
        if (is_open && is_player_ready == 0 && is_start && is_end == 0) {
            background.getChildByName("hint").text = 'Wait for current round to end';
        }
        if (is_open && is_player_ready == 0 && is_start && is_end) {
            background.getChildByName("hint").text = 'Current round is end';
        }
        if (is_open && is_player_ready && is_player_in_team == 0) {
            background.getChildByName("hint").text = 'Join in a Team';
        }
        if (is_open && is_player_ready && is_player_in_team && is_team_ready == 0) {
            background.getChildByName("hint").text = 'Wait for Team';
        }
        if (is_open && is_player_ready && is_team_ready && is_ready == 0) {
            background.getChildByName("hint").text = 'Wait for Mech';
        }
        if (is_open && is_player_ready && is_team_ready && is_ready && is_start == 0) {
            background.getChildByName("hint").text = 'Wait for Mech';
        }

        if (is_open && is_player_ready && is_start && is_end == 0) {
            background.getChildByName("hint").text = 'Place tower to steal power';
            if (background.getChildByName("score") == null) {
                var string = new createjs.Text('Score: ' + score, '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                string.textAlign = 'center';
                string.name = 'score';
                string.x = <?=$map_width/2?> + <?=$offset_x?>;
                string.y = 60;
                background.addChild(string);
            } else {
                background.getChildByName("score").text = 'Score: ' + score;
            }
            if (background.getChildByName("round_score") == null) {
                var string = new createjs.Text('this round: ' + round_score, '42px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
                string.textAlign = 'center';
                string.name = 'round_score';
                string.x = <?=$map_width/2?> + <?=$offset_x?>;
                string.y = 160;
                background.addChild(string);
            } else {
                background.getChildByName("round_score").text = 'this round: ' + round_score;
            }
        }

        if (is_open && is_player_ready && is_start && is_end) {
            // alert("here");
            background.getChildByName("hint").text = 'Game Ends';
        }

        background.update();
        $("background").show();
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
            main.setChildIndex(cont, 0);
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
    function drawRemain(id) {
        if (main.getChildByName('remain_' + id) == null) {
            var x = (id-1)%<?=$column?>;
            var y = Math.floor((id-1)/<?=$column?>);
            var remain = new createjs.Shape();
            remain.graphics.beginFill("<?=Yii::$app->params['main_color_2']?>").drawCircle(0, 0, <?=$grid_width/4?>);
            remain.x = x*<?=$grid_width?> + <?=$grid_width/2?> + <?=$offset_x?>;
            remain.y = y*<?=$grid_height?> + <?=$grid_height/2?> + <?=$offset_y?>;
            remain.name = 'remain_' + id;
            main.addChild(remain);

            var rect = new createjs.Shape();
            rect.graphics.beginFill("<?=Yii::$app->params['grey_color']?>").drawRect(0, 0, <?=$grid_width?>, <?=$grid_height?>);
            rect.alpha = 0.8;
            rect.x = x*<?=$grid_width?> + <?=$offset_x?>;
            rect.y = y*<?=$grid_height?> + <?=$offset_y?>;
            rect.name = "block";
            main.addChild(rect);

            createjs.Ticker.on("tick", tick);
            createjs.Ticker.setFPS(20);
            function tick (event) {
                rect.scaleY -= 0.01;
                rect.alpha -= 0.01;
                remain.alpha -= 0.01;
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
    function drawTower(id, team_id, tower_player_id, score_rate = 0) {
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
        }
    }
    function endGame() {
        setCookie("end", 1);
        clearInterval(check_status);
        clearInterval(interval);

        if (end.getChildByName("bg") == null) {
            var rect = new createjs.Shape();
            rect.graphics.beginFill("<?=Yii::$app->params['background_color']?>").drawRect(0, 0, <?=$scene_width?>, <?=$scene_height?>);
            rect.name = "bg";
            end.addChild(rect);
        }

        if (end.getChildByName("the_end") == null) {
            var text = new createjs.Text('The End', '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
            text.textAlign = 'center';
            text.name = "the_end";
            text.x = <?=$scene_width/2?>;
            text.y = <?=$scene_height/2?> - 360;
            end.addChild(text);
        }

        if (end.getChildByName("is_win") == null) {
            if (is_win == 1) {
                var text = new createjs.Text('We won!', '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            } else {
                var text = new createjs.Text('We lost...', '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            }
            text.textAlign = 'center';
            text.name = "is_win";
            text.x = <?=$scene_width/2?>;
            text.y = <?=$scene_height/2?> - 220;
            end.addChild(text);
        } else {
            if (is_win == 1) {
                end.getChildByName("is_win").text = 'We won!';
            } else {
                end.getChildByName("is_win").text = 'We lost...';
            }
        }

        if (end.getChildByName("score") == null) {
            var text = new createjs.Text('Score: ' + score, '128px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_text_color']?>');
            text.textAlign = 'center';
            text.name = "score";
            text.x = <?=$scene_width/2?>;
            text.y = <?=$scene_height/2?> - 140;
            end.addChild(text);
        } else {
            end.getChildByName("score").text = 'Score: ' + score;
        }

        if (end.getChildByName("round_score") == null) {
            var text = new createjs.Text('this round: ' + round_score, '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            text.textAlign = 'center';
            text.name = "round_score";
            text.x = <?=$scene_width/2?>;
            text.y = <?=$scene_height/2?>;
            end.addChild(text);
        } else {
            end.getChildByName("round_score").text = 'this round: ' + round_score;
        }

        if (end.getChildByName("restart") == null) {
            var text = new createjs.Text('Restart', '84px <?=Yii::$app->params['font']?>', '<?=Yii::$app->params['main_color']?>');
            text.textAlign = 'center';
            text.name = "restart";
            text.x = <?=$scene_width/2?>;
            text.y = <?=$scene_height/2?> + 200;
            text.on("click", function(event) {
                setCookie("end", 0);
                setCookie("start", 1);
                startGame();
            });
            end.addChild(text);
        }

        end.update();
        $('#end').show();
    }

    (createjs.Graphics.Polygon = function(x, y, points) {
        this.x = x;
        this.y = y;
        this.points = points;
    }).prototype.exec = function(ctx) {
        // Start at the end to simplify loop
        var end = this.points[this.points.length - 1];
        ctx.moveTo(end.x, end.y);
        this.points.forEach(function(point) {
            ctx.lineTo(point.x, point.y);
        });
    };
    createjs.Graphics.prototype.drawPolygon = function(x, y, args) {
        var points = [];
        if (Array.isArray(args)) {
            args.forEach(function(point) {
                point = Array.isArray(point) ? {x:point[0], y:point[1]} : point;
                points.push(point);
            });
        } else {
            args = Array.prototype.slice.call(arguments).slice(2);
            var px = null;
            args.forEach(function(val) {
                if (px === null) {
                    px = val;
                } else {
                    points.push({x: px, y: val});
                    px = null;
                }
            });
        }
        return this.append(new createjs.Graphics.Polygon(x, y, points));
    };
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
    </script>
</head>
<body onload="init();">
    <?php
    // echo "Hello World!";
    ?>
    <canvas id="text" width="<?=$scene_width?>" height="<?=$scene_height?>" style="position:absolute;top:0;left:0;" hidden></canvas>
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
</body>

<style>
.grid {
    background-color:Transparent;
    background-repeat:no-repeat;
    border:none;
    cursor:pointer;
    overflow:hidden;
    outline:none;
    border:1px silver dashed;
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
#start-button:hover {
    color: <?=Yii::$app->params['white_color']?>;
}
#start-button {
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
    background-color: rgba(255, 255, 255, 1);
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
