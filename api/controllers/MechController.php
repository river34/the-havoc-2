<?php
namespace api\controllers;

use Yii;
use common\models\Map;
use common\models\Round;
use common\models\Player;
use common\models\Team;
use common\models\RoundTeamPlayer;
use common\models\Triangle;
use common\models\Game;

/**
 * Mech controller
 */
class MechController extends ApiController
{
    /************************************************************************
    /*
    /* API for Mech
    /*
    /***********************************************************************/

    // (unity side)
    // replace actionRestart
    // clear map data, clear team data
    // start a new round
    // output: round
    // output: round_id
    public function actionStart() {
        // output
        $result['success'] = false;
        $result['data'] = [];
        // $result['data']['round'] = [];
        $result['data']['round_id'] = 0;
        $result['data']['core_score'] = 0;

        $last_round = Round::find()->orderBy('id DESC')->one();
        if (!$last_round || $last_round->is_end) {
            $error = false;
            try {
                // Yii::$app->db->createCommand()->truncateTable('triangle')->execute();
                Yii::$app->db->createCommand("UPDATE map SET mark = 0, player_id = 0, team_id = 0, score = 0, score_rate = 0, is_sent = 0  WHERE mark <> ".Yii::$app->params['mark_core'])->execute();
                Yii::$app->db->createCommand("UPDATE team SET is_ready = 0, score = 0")->execute();
            } catch (Exception $e) {
                throw new Exception("Error : ".$e);
                $error = true;
            }

            if (!$error) {
                $round = new Round();
                /////////////////////////////////////
                //  Mech is ready at the beginning
                /////////////////////////////////////
                $round->is_mech_ready = 1;
                $round->secret = sprintf('%04d', rand(0, 9999));
                $round->save();
                // $result['data']['round'] = $round;
                $result['data']['round_id'] = $round->id;
                $result['success'] = true;
            }
        } else if ($last_round && !$last_round->is_start) {
            $last_round->is_mech_ready = 1;
            $last_round->save();
            $result['data']['round_id'] = $last_round->id;
            $result['success'] = true;
        }

        $game = Game::find()->one();
        if ($game && $result['success']) {
            $result['data']['core_score'] = $game->core_score;
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (unity side)
    // check if all parties are ready
    // input: round_id:int
    public function actionCheckReady() {
        // input
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];

        // output
        $result['success'] = false;
        $result['data'] = [];
        // $result['data']['round'] = [];
        $result['data']['is_ready'] = 0;
        $result['data']['is_start'] = 0;

        if (!empty($round_id)) {
            $round = Round::findOne($round_id);
        } else {
            $round = Round::find()->orderBy('id DESC')->one();
        }
        if ($round) {
            $result['data']['is_ready'] = $round->is_ready;
            $result['data']['is_start'] = $round->is_ready;
            $result['success'] = true;
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (unity side)
    // replace actionStartBattle
    // input: round_id:int
    public function actionReady() {
        // input
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];

        // output
        $result['success'] = false;
        $result['data'] = [];
        // $result['data']['round'] = [];

        if (!empty($round_id)) {
            $round = Round::findOne($round_id);
        } else {
            $round = Round::find()->orderBy('id DESC')->one();
        }
        if ($round && $round->is_ready == 0) {
            $round->is_mech_ready = 1;
            $round->is_ready = $round->is_team_ready * $round->is_mech_ready * $round->is_player_ready;
            $round->save();

            $result['success'] = true;
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (unity side)
    // replace actionStartBattle
    // input: round_id:int
    public function actionEnter() {
        // input
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];

        // output
        $result['success'] = false;
        $result['data'] = [];
        // $result['data']['round'] = [];
        $result['data']['is_start'] = 0;

        if (!empty($round_id)) {
            $round = Round::findOne($round_id);
        } else {
            $round = Round::find()->orderBy('id DESC')->one();
        }
        if ($round && $round->is_ready == 1) {
            $round->is_start = 1;
            // $result['data']['round'] = $round;
            $round->save();
            $round->refresh();
            $result['data']['is_start'] = $round->is_start;
            apcu_clear_cache();
            $result['success'] = true;
        }
        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (unity side)
    // replace actionGetMap
    // input: round_id:int
    // output: grids
    public function actionGetMap() {
        // input
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];

        // output
        $result['success'] = true;
        $result['data'] = [];
        $result['data']['grids'] = [];

        if (!empty($round_id)) {
            $round = Round::findOne($round_id);
        } else {
            $round = Round::find()->orderBy('id DESC')->one();
        }
        if ($round && $round->is_start && $round->is_end == 0) {
            $result['data']['grids'] = Map::find()->all();
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (unity side)
    // replace actionGetMarked
    // input: round_id:int
    // output: grids
    public function actionGetMarked() {
        // input
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];

        // output
        $result['success'] = true;
        $result['data'] = [];
        $result['data']['grids'] = [];

        if (!empty($round_id)) {
            $round = Round::findOne($round_id);
        } else {
            $round = Round::find()->orderBy('id DESC')->one();
        }
        if ($round && $round->is_start && $round->is_end == 0) {
            $result['data']['grids'] = Map::find()->where(['<>', 'mark', Yii::$app->params['mark_empty']])->all();
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (unity side)
    // replace actionUpdateMap
    // input: round_id:int
    // input: towers{tower_id:int, player_id:int}
    // input: scores{tower_id:int; score:int}
    // input: triangles{a:int; b:int; c:int; team_id:int}
    // output: (new) grids
    // output: is_timeout
    public function actionUpdate() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "havoc";
/*
        // Create connection
        $conn = new mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }
*/
        $result['start_time'] = microtime(true);
        // input
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];
        $towers = empty($this->params['towers'])?'':$this->params['towers'];    // grid of the destroyed tower
        $scores = empty($this->params['scores'])?'':$this->params['scores'];    // new score
        $triangles = empty($this->params['triangles'])?'':$this->params['triangles'];
        $core_health = empty($this->params['core'])?'':$this->params['core'];
        apcu_store('core',$core_health);
        // output
        $result['success'] = false;
        $result['data'] = [];
        // $result['data']['params'] = $this->params;
        // $result['data']['round'] = [];
        $result['data']['grids'] = [];
        $result['data']['is_timeout'] = 0;

        if (!empty($round_id)) {
            $round = Round::findOne($round_id);
        } else {
            $round = Round::find()->orderBy('id DESC')->one();
        }
        // $result['data']['round'] = $round;
        if ($round && $round->is_start && $round->is_end == 0) {
            $error = false;
            if (!empty($towers)) {
                foreach ($towers as $element) {
                    try {
                        Yii::$app->db->createCommand("UPDATE map SET mark = ". Yii::$app->params['mark_remain'] ." WHERE mark <> ".Yii::$app->params['mark_core']." AND id = ".$element['tower_id'])->execute();
                        //Yii::$app->db->createCommand("UPDATE round_team_player SET resource = resource + 1 WHERE round_id = ".$round_id ." AND player_id = ".$element['player_id'])->execute();
                    } catch (Exception $e) {
                        throw new Exception("Error : ".$e);
                        $error = true;
                    }
                    $game = Game::find()->one();
                    $roundTeamPlayer = RoundTeamPlayer::find()->where(['round_id'=>$round_id, 'player_id'=>$element['player_id']])->one();
                    if ($game && $roundTeamPlayer && $roundTeamPlayer->resource < $game->resource) {
                        $roundTeamPlayer->resource += 1;
                        $roundTeamPlayer->save();
                    }
                }
                $result['check_time_1'] = microtime(true) - $result['start_time'];
            }
            for($i=1;$i<=81;$i++){
                apcu_store('map'.$i, 0);
            }
            if (!empty($scores)) {
                foreach ($scores as $element) {
                    $grid = Map::findOne($element['tower_id']);
                    if ($grid) {
                        try {

                            /*
                            $sql = "UPDATE player SET score = score + ". $element['score'] ." WHERE id = ".$grid->player_id;
                            $conn->query($sql);
                            $sql = "UPDATE round_team_player SET score = score + ". $element['score'] ." WHERE round_id = ".$round_id ." AND player_id = ".$grid->player_id;
                            $conn->query($sql);
                            $sql = "UPDATE team SET score = score + ". $element['score']." WHERE id = ".$grid->team_id;
                            $conn->query($sql);
                            $sql = "UPDATE map SET score = score + ". $element['score']. ", score_rate = ". $element['score']." WHERE id = ".$grid->id;
                            $conn->query($sql);
                            */
                            if(!apcu_fetch('player'.$grid->player_id)){
                                apcu_store('player'.$grid->player_id, (int)$element['score']);
                            }
                            else{
                                $result['add_result']=apcu_inc('player'.$grid->player_id, (int)$element['score']);
                            }
                            if(!apcu_fetch('round_team'.$round_id.'player'.$grid->player_id)){
                                apcu_store('round_team'.$round_id.'player'.$grid->player_id, (int)$element['score']);
                            }
                            else{
                                apcu_inc('round_team'.$round_id.'player'.$grid->player_id, (int)$element['score']);
                            }
                            if(!apcu_fetch('team'.$grid->team_id)){
                                apcu_store('team'.$grid->team_id, (int)$element['score']);
                            }
                            else{
                                apcu_inc('team'.$grid->team_id, (int)$element['score']);
                            }
                            apcu_store('map'.$grid->id, (int)$element['score']);


                            // Yii::$app->db->createCommand("UPDATE player SET score = score + ". $element['score'] ." WHERE id = ".$grid->player_id)->execute();
                            // Yii::$app->db->createCommand("UPDATE round_team_player SET score = score + ". $element['score'] ." WHERE round_id = ".$round_id ." AND player_id = ".$grid->player_id)->execute();
                            // Yii::$app->db->createCommand("UPDATE team SET score = score + ". $element['score']." WHERE id = ".$grid->team_id)->execute();
                            // Yii::$app->db->createCommand("UPDATE map SET score = score + ". $element['score']. ", score_rate = ". $element['score']." WHERE id = ".$grid->id)->execute();
                        } catch (Exception $e) {
                            throw new Exception("Error : ".$e);
                            $error = true;
                        }
                    } else {
                        $error = true;
                    }
                }
                $result['check_time_2'] = microtime(true) - $result['start_time'];
            }

            // try {
            //     Yii::$app->db->createCommand()->truncateTable('triangle')->execute();
            // } catch (Exception $e) {
            //     throw new Exception("Error : ".$e);
            //     $error = true;
            // }

            // if (!empty($triangles)) {
            //     // try {
            //     //     foreach ($triangles as $element) {
            //     //         Yii::$app->db->createCommand("INSERT INTO triangle (a, b, c, team_id) VALUES ('".$element['a']."', '".$element['b']."', '".$element['c']."', ".$element['team_id'].")")->execute();
            //     //     }
            //     //     $result['check_time_3'] = microtime(true) - $result['start_time'];
            //     // } catch (Exception $e) {
            //     //     throw new Exception("Error : ".$e);
            //     //     $error = true;
            //     // }
            //
            //     apcu_store('triangles', $triangles);
            // }

            apcu_store('triangles', $triangles);

            $grids = Map::find()->where(['=', 'mark', Yii::$app->params['mark_default']])->all();
            $result['check_time_4'] = microtime(true) - $result['start_time'];
            $result['data']['grids'] = $grids;

            if (!$error) {
                $result['success'] = true;
            }
        } else if ($round && $round->is_start && $round->is_end && $round->is_timeout) {
            $result['data']['is_timeout'] = $round->is_timeout;
        }
        //$conn->close();
        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (unity side)
    // replace actionEnd
    // input: round_id:int
    // input: is_win:int
    public function actionEnd() {
        // input
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];
        $is_win = empty($this->params['is_win'])?0:$this->params['is_win'];

        // output
        $result['success'] = false;

        if (!empty($round_id)) {
            $round = Round::findOne($round_id);
        } else {
            $round = Round::find()->orderBy('id DESC')->one();
        }
        if ($round && $round->is_start && $round->is_end == 0) {
            // end this round
            $round->is_end = 1;
            $round->save();

            // save round score to each player
            // add round score to each player
            $roundTeamPlayers = RoundTeamPlayer::find()->where(['round_id'=>$round->id])->all();
            if ($roundTeamPlayers) {
                foreach ($roundTeamPlayers as $roundTeamPlayer) {
                    $roundTeamPlayer->is_win = $is_win;
                    $roundTeamPlayer->score = (int)apcu_fetch('player'.$roundTeamPlayer->player_id);
                    $roundTeamPlayer->save();
                    $player = Player::findOne($roundTeamPlayer->player_id);
                    if ($player) {
                        $player->score += (int)apcu_fetch('player'.$roundTeamPlayer->player_id);
                        $player->save();
                    }
                }
            }

            // start the next round directly
            $result = $this::actionStart();
            $result['success'] = true;
        }
        /*
         else if ($round && $round->is_start && $round->is_end == 1) { // force
            $roundTeamPlayers = RoundTeamPlayer::find()->where(['round_id'=>$round->id])->all();
            if ($roundTeamPlayers) {
                foreach ($roundTeamPlayers as $roundTeamPlayer) {
                    $roundTeamPlayer->is_win = $is_win;
                    $roundTeamPlayer->score = (int)apcu_fetch('player'.$roundTeamPlayer->player_id);
                    $roundTeamPlayer->save();
                }
            }
            $result = $this::actionStart();
            $result['success'] = true;
        }
        */

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }
}
