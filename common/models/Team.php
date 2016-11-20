<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "team".
 *
 * @property integer $id
 * @property string $name
 * @property integer $limit
 * @property integer $is_mech
 * @property integer $is_available
 * @property integer $is_ready
 * @property integer $score
 * @property string $media
 */
class Team extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['limit', 'is_mech', 'is_available', 'is_ready', 'score'], 'integer'],
            [['media'], 'string'],
            [['name'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'limit' => 'Limit',
            'is_mech' => 'Is Mech',
            'is_available' => 'Is Available',
            'is_ready' => 'Is Ready',
            'score' => 'Score',
            'media' => 'Media',
        ];
    }
}
