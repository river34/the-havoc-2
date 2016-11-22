<?php
namespace api\controllers;

use Yii;
use common\models\Map;
use common\models\Player;
use common\models\Round;
use common\models\MechTrack;
use common\models\Bomb;
use common\models\RoundTeamPlayer;
use common\models\Triangle;
use common\models\Team;

/**
 * Map controller
 */
class MapController extends ApiController
{
    // (mobile side)
    public function actionMark() {
        // input
        $id = empty($this->params['id'])?'':$this->params['id'];
        $key = empty($this->params['key'])?'':$this->params['key'];
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];

        // output
        $result['success'] = false;
        $result['data'] = [];
        $result['data']['player'] = [];
        $result['data']['round'] = [];
        $result['data']['grid'] = [];
        $result['data']['roundTeamPlayer'] = [];
        $result['data']['resource'] = 0;

        // handshake
        $key = $this->handshake($key);
        $player = Player::findOne(['key'=>$key]);
        $result['data']['player'] = $player;
        if ($player) {
            $round = Round::findOne($round_id);
            $result['data']['round'] = $round;
            if ($round && $round->is_start && $round->is_end == 0) {
                $roundTeamPlayer = RoundTeamPlayer::find()->where(['round_id'=>$round->id])->andWhere(['player_id'=>$player->id])->one();
                if ($roundTeamPlayer && $roundTeamPlayer->resource > 0) {
                    $grid = Map::findOne(['id'=>$id, 'mark'=>Yii::$app->params['mark_empty']]);
                    if ($grid) {
                        $grid->mark = Yii::$app->params['mark_default'];
                        $grid->player_id = $player->id;
                        $grid->team_id = $roundTeamPlayer->team_id;
                        $grid->save();
                        $result['data']['grid'] = $grid;

                        $roundTeamPlayer->resource -= 1;
                        $roundTeamPlayer->save();
                        $result['data']['roundTeamPlayer'] = $roundTeamPlayer;
                        $result['data']['resource'] = $roundTeamPlayer->resource;
                        $result['success'] = true;
                    }
                }
            }
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }

    // (mobile side)
    // call for every 0.5 sec
    public function actionGetMap() {
        // input
        $key = empty($this->params['key'])?'':$this->params['key'];
        $round_id = empty($this->params['round_id'])?'':$this->params['round_id'];
        $team_id = empty($this->params['team_id'])?'':$this->params['team_id'];

        // output
        $result['success'] = false;
        $result['data'] = [];
        $result['data']['player'] = [];
        $result['data']['round'] = [];
        $result['data']['grids'] = [];
        $result['data']['remains'] = [];
        $result['data']['triangles'] = [];
        $result['data']['is_start'] = 0;
        $result['data']['is_end'] = 0;
        $result['data']['rank'] = '-';
        $result['data']['score'] = 0;
        $result['data']['round_score'] = 0;
        $result['data']['team_score_1'] = 0;
        $result['data']['team_score_2'] = 0;
        $result['data']['resource'] = 0;

        // handshake
        $key = $this->handshake($key);
        $player = Player::findOne(['key'=>$key]);
        $result['data']['player'] = $player;
        if ($player) {
            $result['data']['rank'] = Player::find()->where(['>', 'score', $player->score])->count()+1;
            $result['data']['score'] = $player->score;
            $round = Round::findOne($round_id);
            $result['data']['round'] = $round;
            if ($round) {
                $result['data']['is_start'] = $round->is_start;
                $result['data']['is_end'] = $round->is_end;
                $roundTeamPlayer = RoundTeamPlayer::find()->where(['round_id'=>$round->id, 'player_id'=>$player->id])->one();
                if ($roundTeamPlayer) {
                    $result['data']['round_score'] = $roundTeamPlayer->score;
                    $result['data']['resource'] = $roundTeamPlayer->resource;
                }
            }
            if ($round && $round->is_start && $round->is_end == 0) {
                $grids = Map::find()->where(['mark'=>Yii::$app->params['mark_default']])->all();
                foreach ($grids as $element) {
                    $element->score_rate = (int)apcu_fetch('map'.$element->id);
                    $result['data']['grids'][] = $element;
                }

                $start_time = date('Y-m-d H:i:s', strtotime(Yii::$app->params['destroy_time'], time()));
                $remains = Map::find()->where(['mark'=>Yii::$app->params['mark_remain']])->all();
                foreach ($remains as $element) {
                    if ($element->updated_at > $start_time) {
                        $result['data']['remains'][] = $element;
                    } else {
                        $element->mark = Yii::$app->params['mark_empty'];
                        $element->player_id = 0;
                        $element->team_id = 0;
                        $element->save();
                    }
                }

                // $result['data']['triangles'] = Triangle::find()->all();
                $result['data']['core'] = (int)apcu_fetch('core');
                $result['data']['triangles'] = apcu_fetch('triangles');
                $result['data']['round_score'] = (int)apcu_fetch('player'.$player->id);

                $result['success'] = true;
            }
        }
        //$team_1 = Team::findOne(2);
        $result['data']['team_score_1'] = (int)apcu_fetch('team2');
        //$team_2 = Team::findOne(3);
        $result['data']['team_score_2'] = (int)apcu_fetch('team3');

        $result['data']['round_score'] = (int)apcu_fetch('player'.$player->id);

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }
}
