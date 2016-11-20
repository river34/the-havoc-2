<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "triangle".
 *
 * @property integer $id
 * @property integer $a
 * @property integer $b
 * @property integer $c
 * @property integer $team_id
 * @property string $updated_at
 * @property string $created_at
 */
class Triangle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'triangle';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['a', 'b', 'c', 'team_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
            'team_id' => 'Team ID',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
