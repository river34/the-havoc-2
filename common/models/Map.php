<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "map".
 *
 * @property integer $id
 * @property integer $mark
 * @property integer $player_id
 * @property integer $team_id
 * @property integer $is_sent
 * @property integer $score
 * @property integer $score_rate
 * @property string $updated_at
 */
class Map extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mark', 'player_id', 'team_id', 'is_sent', 'score', 'score_rate'], 'integer'],
            [['updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mark' => 'Mark',
            'player_id' => 'Player ID',
            'team_id' => 'Team ID',
            'is_sent' => 'Is Sent',
            'score' => 'Score',
            'score_rate' => 'Score Rate',
            'updated_at' => 'Updated At',
        ];
    }
}
