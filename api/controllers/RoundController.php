<?php
namespace api\controllers;

use Yii;
use common\models\Map;
use common\models\Round;
use common\models\Player;
use common\models\Team;
use common\models\RoundTeamPlayer;

/**
 * Round controller
 */
class RoundController extends ApiController
{
    // (mobile side)
    // start a new round
    // output: player
    // output: roundTeamPlayer
    // output: teams
    public function actionStart() {
        // input
        $key = empty($this->params['key'])?'':$this->params['key'];

        // output
        $result['success'] = false;
        $result['data'] = [];
        $result['data']['player'] = [];
        $result['data']['roundTeamPlayer'] = [];
        $result['data']['teams'] = [];
        $result['data']['empty_player_slots'] = 0;

        // handshake
        $key = $this->handshake($key);

        $player = Player::findOne(['key'=>$key]);
        $result['data']['player'] = $player;
        $teams = Team::find()->where(['is_available'=>1])->all();
        $result['data']['teams'] = $teams;
        $total_num = 0;
        foreach ($result['data']['teams'] as $team) {
            $result['data']['empty_player_slots'] += $team->limit;
        }
        if ($player) {
            $round = Round::find()->orderBy('id DESC')->one();
            if (!$round) {
                MechController::actionStart();
                $round = Round::find()->orderBy('id DESC')->one();
            }
            if ($round) {
                $current_count = RoundTeamPlayer::find()->where(['round_id'=>$round->id])->count();
                $result['data']['empty_player_slots'] -= $current_count;
                if ($result['data']['empty_player_slots'] > 0) {
                    $roundTeamPlayer = RoundTeamPlayer::find()->where(['round_id'=>$round->id])->andWhere(['player_id'=>$player->id])->one();
                    if (!$roundTeamPlayer) {
                        $roundTeamPlayer = new RoundTeamPlayer();
                        $roundTeamPlayer->round_id = $round->id;
                        $roundTeamPlayer->player_id = $player->id;
                        $roundTeamPlayer->resource = Yii::$app->params['resource'];
                        $roundTeamPlayer->is_mech = 0;
                        $roundTeamPlayer->save();
                    }
                    $result['data']['roundTeamPlayer'] = $roundTeamPlayer;
                    $result['success'] = true;
                }
            }
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (mobile side)
    // input: round_id
    // input: team_id
    public function actionTeamUp() {
        // input
        $key = empty($this->params['key'])?'':$this->params['key'];
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];
        $team_id = empty($this->params['team_id'])?'':$this->params['team_id'];

        // output
        $result['success'] = false;
        $result['data'] = [];
        $result['data']['params'] = $this->params;
        $result['data']['roundTeamPlayer'] = [];
        $result['data']['player'] = [];
        $result['data']['round'] = [];
        $result['data']['team'] = [];
        $result['data']['count'] = '';

        // handshake
        $key = $this->handshake($key);
        $player = Player::findOne(['key'=>$key]);
        $round = Round::findOne($round_id);
        $team = Team::findOne($team_id);
        $result['data']['player'] = $player;
        $result['data']['round'] = $round;
        $result['data']['team'] = $team;
        if ($player && $round && $team) {
            $roundTeamPlayer = RoundTeamPlayer::find()->where(['round_id'=>$round->id, 'player_id'=>$player->id])->one();
            if ($roundTeamPlayer && $team->is_ready == 0) {
                $roundTeamPlayer->round_id = $round->id;
                $roundTeamPlayer->team_id = $team->id;
                $roundTeamPlayer->player_id = $player->id;
                $roundTeamPlayer->save();
                $result['data']['roundTeamPlayer'] = $roundTeamPlayer;

                $count = RoundTeamPlayer::find()->where(['round_id'=>$round->id, 'team_id'=>$team->id])->count();
                $result['data']['count'] = $count;
                if ($count == $team->limit) {
                    $team->is_ready = 1;
                    $team->save();
                }
                $teams = Team::find()->where(['is_available'=>1])->all();
                if ($teams) {
                    $round->is_team_ready = 1;
                    foreach ($teams as $team) {
                        $round->is_team_ready *= $team->is_ready;
                    }
                    $round->is_ready = $round->is_team_ready * $round->is_mech_ready;
                    $round->save();
                }

                $result['success'] = true;
            }
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (mobile side)
    // check if the player is in the game
    // last active round = player's current round
    public function actionCheck() {
        // input
        $key = empty($this->params['key'])?'':$this->params['key'];

        // output
        $result['success'] = false;
        $result['data'] = [];
        $result['data']['player'] = [];
        $result['data']['round'] = [];
        $result['data']['roundTeamPlayer'] = [];
        $result['data']['team'] = [];
        $result['data']['teams'] = [];
        $result['data']['team_counts'] = [];
        $result['data']['is_open'] = 0;
        $result['data']['is_player_ready'] = 0;
        $result['data']['is_player_in_team'] = 0;
        $result['data']['is_mech_ready'] = 0;
        $result['data']['is_team_ready'] = 0;
        $result['data']['is_ready'] = 0;
        $result['data']['is_start'] = 0;
        $result['data']['is_end'] = 0;
        $result['data']['is_win'] = 0;
        $result['data']['rank'] = '-';
        $result['data']['score'] = 0;
        $result['data']['round_score'] = 0;
        $result['data']['team_score_1'] = 0;
        $result['data']['team_score_2'] = 0;
        $result['data']['empty_slots'] = 0;
        $result['data']['empty_player_slots'] = 0;

        // handshake
        $key = $this->handshake($key);
        $player = Player::findOne(['key'=>$key]);
        $result['data']['player'] = $player;
        $result['data']['teams'] = Team::find()->where(['is_available'=>1])->all();
        foreach ($result['data']['teams'] as $team) {
            $result['data']['empty_slots'] += $team->limit;
            $result['data']['empty_player_slots'] += $team->limit;
        }
        if ($player) {
            $result['data']['score'] = $player->score;
            $result['data']['rank'] = Player::find()->where(['>', 'score', $player->score])->count()+1;
            $round = Round::find()->orderBy('id DESC')->one();
            $result['data']['round'] = $round;
            if (!$round) {
                MechController::actionStart();
                $round = Round::find()->orderBy('id DESC')->one();
            }
            if ($round) {
                $result['data']['is_team_ready'] = $round->is_team_ready;
                $result['data']['is_mech_ready'] = $round->is_mech_ready;
                $result['data']['is_ready'] = $round->is_ready;
                $result['data']['is_start'] = $round->is_start;
                $result['data']['is_end'] = $round->is_end;

                $current_count = RoundTeamPlayer::find()->where(['round_id'=>$round->id])->andWhere(['<>', 'team_id', 0])->count();
                $result['data']['empty_slots'] -= $current_count;
                $current_count = RoundTeamPlayer::find()->where(['round_id'=>$round->id])->count();
                $result['data']['empty_player_slots'] -= $current_count;
                $roundTeamPlayer = RoundTeamPlayer::find()->where(['round_id'=>$round->id])->andWhere(['player_id'=>$player->id])->one();
                $result['data']['roundTeamPlayer'] = $roundTeamPlayer;
                if ($roundTeamPlayer) {
                    $result['data']['round_score'] = $roundTeamPlayer->score;
                    $result['data']['is_player_ready'] = 1;
                    if (!empty($roundTeamPlayer->team_id)) {
                        $result['data']['is_player_in_team'] = 1;
                    }
                    $team = Team::find()->where(['id'=>$roundTeamPlayer->team_id])->one();
                    $result['data']['team'] = $team;
                    $teams = Team::find()->where(['is_available'=>1])->all();
                    $result['data']['teams'] = $teams;
                    if ($teams) {
                        $round->is_team_ready = 1;
                        foreach ($teams as $index=>$team) {
                            $round->is_team_ready *= $team->is_ready;
                            $result['data']['team_counts'][$index] = RoundTeamPlayer::find()->where(['round_id'=>$round->id, 'team_id'=>$team->id])->count();
                        }
                        $round->is_ready = $round->is_team_ready * $round->is_mech_ready;
                        $round->save();
                    }
                    $result['data']['is_win'] = $roundTeamPlayer->is_win;
                }
            }
            $result['data']['round_score'] = (int)apcu_fetch('player'.$player->id);
            $result['success'] = true;
        }
        // $team_1 = Team::findOne(2);
        // $result['data']['team_score_1'] = $team_1->score;
        // $team_2 = Team::findOne(3);
        // $result['data']['team_score_2'] = $team_2->score;
        $result['data']['team_score_1'] = (int)apcu_fetch('team2');
        $result['data']['team_score_2'] = (int)apcu_fetch('team3');

        if ($round && !$result['data']['is_ready'] && !$result['data']['is_player_in_team'] && !$result['data']['is_player_ready'] && $result['data']['empty_player_slots'] > 0) {
            $result['data']['is_open'] = 1;
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (mobile side)
    // check if mech and team are ready
    public function actionReady() {
        // input
        $key = empty($this->params['key'])?'':$this->params['key'];
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];

        // output
        $result['success'] = false;
        $result['data'] = [];
        $result['data']['player'] = [];
        $result['data']['round'] = [];

        // handshake
        $key = $this->handshake($key);
        $player = Player::findOne(['key'=>$key]);
        $result['data']['player'] = $player;
        if ($player) {
            $round = Round::findOne($round_id);
            $result['data']['round'] = $round;
            if ($round && $round->is_start && $round->is_end == 0) {
                $result['success'] = true;
            }
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (mobile side)
    public function actionGetRoundResult() {
        // input
        $key = empty($this->params['key'])?'':$this->params['key'];
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];

        // output
        $result['success'] = false;
        $result['data'] = [];
        $result['data']['player'] = [];
        $result['data']['roundTeamPlayer'] = [];

        // handshake
        $key = $this->handshake($key);
        $player = Player::findOne(['key'=>$key]);
        $result['data']['player'] = $player;
        if ($player) {
            $roundTeamPlayer = RoundTeamPlayer::find()->where(['round_id'=>$round_id, 'player_id'=>$player->id])->one();
            if ($roundTeamPlayer) {
                $result['data']['roundTeamPlayer'] = $roundTeamPlayer;
                $result['success'] = true;
            }
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }
}
