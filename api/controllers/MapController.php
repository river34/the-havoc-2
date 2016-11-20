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

        // handshake
        $key = $this->handshake($key);
        $player = Player::findOne(['key'=>$key]);
        $result['data']['player'] = $player;
        if ($player) {
            $round = Round::findOne($round_id);
            $result['data']['round'] = $round;
            if ($round) {
                $result['data']['is_start'] = $round->is_start;
                $result['data']['is_end'] = $round->is_end;
            }
            if ($round && $round->is_start && $round->is_end == 0) {
                $grids = Map::find()->where(['mark'=>Yii::$app->params['mark_default']])->all();
                foreach ($grids as $element) {
                    $result['data']['grids'][] = $element;
                }

                $start_time = date('Y-m-d H:i:s', strtotime(Yii::$app->params['destroy_time'], time()));
                $remains = Map::find()->where(['mark'=>Yii::$app->params['mark_remain']])->all();
                foreach ($remains as $element) {
                    if ($element->updated_at > $start_time) {
                        $result['data']['remains'][] = $element;
                    } else {
                        $element->mark = Yii::$app->params['mark_empty'];
                        $element->save();
                    }
                }

                $triangles = Triangle::find()->all();
                foreach ($triangles as $element) {
                    if ($element->team_id != 0) {
                        $result['data']['triangles'][] = $element;
                    }
                }

                $result['success'] = true;
            }
        }

        $result['query_time'] = microtime(true) - $this->ini_time;
        return $result;
    }
}
