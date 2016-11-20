<?php
namespace api\controllers;

use Yii;
use common\models\Map;
use common\models\Round;
use common\models\Player;
use common\models\Team;
use common\models\RoundTeamPlayer;
use common\models\Triangle;

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

        $last_round = Round::find()->orderBy('id DESC')->one();
        if (!$last_round || $last_round->is_end) {
            $error = false;
            try {
                Yii::$app->db->createCommand()->truncateTable('triangle')->execute();
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
                $round->save();
                // $result['data']['round'] = $round;
                $result['data']['round_id'] = $round->id;
                $result['success'] = true;
            }
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
            $round->save();
            // $result['data']['round'] = $round;

            $round->is_ready = $round->is_team_ready * $round->is_mech_ready;
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

        if (!empty($round_id)) {
            $round = Round::findOne($round_id);
        } else {
            $round = Round::find()->orderBy('id DESC')->one();
        }
        if ($round && $round->is_ready == 1) {
            $round->is_start = 1;
            $round->save();
            // $result['data']['round'] = $round;

            $round->save();
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
        // input
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];
        $towers = empty($this->params['towers'])?'':$this->params['towers'];    // grid of the destroyed tower
        $scores = empty($this->params['scores'])?'':$this->params['scores'];    // new score
        $triangles = empty($this->params['triangles'])?'':$this->params['triangles'];

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
                        Yii::$app->db->createCommand("UPDATE map SET mark = ". Yii::$app->params['mark_remain'] .", player_id = 0, team_id = 0 WHERE mark <> ".Yii::$app->params['mark_core']." AND id = ".$element['tower_id'])->execute();
                        Yii::$app->db->createCommand("UPDATE round_team_player SET resource = resource + 1 WHERE round_id = ".$round_id ." AND player_id = ".$element['player_id'])->execute();
                    } catch (Exception $e) {
                        throw new Exception("Error : ".$e);
                        $error = true;
                    }
                }
            }

            if (!empty($scores)) {
                foreach ($scores as $element) {
                    $grid = Map::findOne($element['tower_id']);
                    if ($grid) {
                        try {
                            Yii::$app->db->createCommand("UPDATE player SET score = score + ". $element['score'] ." WHERE id = ".$grid->player_id)->execute();
                            Yii::$app->db->createCommand("UPDATE round_team_player SET score = score + ". $element['score'] ." WHERE round_id = ".$round_id ." AND player_id = ".$grid->player_id)->execute();
                            Yii::$app->db->createCommand("UPDATE team SET score = score + ". $element['score']." WHERE id = ".$grid->team_id)->execute();
                            Yii::$app->db->createCommand("UPDATE map SET score = score + ". $element['score']. ", score_rate = ". $element['score']." WHERE id = ".$grid->id)->execute();
                        } catch (Exception $e) {
                            throw new Exception("Error : ".$e);
                            $error = true;
                        }
                    } else {
                        $error = true;
                    }
                }
            }

            try {
                Yii::$app->db->createCommand()->truncateTable('triangle')->execute();
            } catch (Exception $e) {
                throw new Exception("Error : ".$e);
                $error = true;
            }

            if (!empty($triangles)) {
                try {
                    foreach ($triangles as $element) {
                        Yii::$app->db->createCommand("INSERT INTO triangle (a, b, c, team_id) VALUES ('".$element['a']."', '".$element['b']."', '".$element['c']."', ".$element['team_id'].")")->execute();
                    }
                } catch (Exception $e) {
                    throw new Exception("Error : ".$e);
                    $error = true;
                }
            }

            $grids = Map::find()->where(['<>', 'mark', Yii::$app->params['mark_empty']])->andWhere(['<>', 'mark', Yii::$app->params['mark_core']])->andWhere(['is_sent' => 0])->all();
            foreach ($grids as $element) {
                try {
                    Yii::$app->db->createCommand("UPDATE map SET is_sent = 1 WHERE id = ".$element->id)->execute();
                } catch (Exception $e) {
                    throw new Exception("Error : ".$e);
                    $error = true;
                }
            }
            $result['data']['grids'] = $grids;

            if (!$error) {
                $result['success'] = true;
            }
        } else if ($round && $round->is_start && $round->is_end && $round->is_timeout) {
            $result['data']['is_timeout'] = $round->is_timeout;
        }

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
            $round->is_end = 1;
            $round->save();
            $roundTeamPlayers = RoundTeamPlayer::find()->where(['round_id'=>$round->id])->all();
            if ($roundTeamPlayers) {
                foreach ($roundTeamPlayers as $roundTeamPlayer) {
                    $roundTeamPlayer->is_win = $is_win;
                    $roundTeamPlayer->save();
                }
            }
            $result['success'] = true;
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }
}
