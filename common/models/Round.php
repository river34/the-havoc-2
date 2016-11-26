<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "round".
 *
 * @property integer $id
 * @property integer $is_team_ready
 * @property integer $is_mech_ready
 * @property integer $is_ready
 * @property integer $is_start
 * @property integer $is_end
 * @property integer $is_timeout
 * @property string $secret
 * @property string $updated_at
 * @property string $created_at
 */
class Round extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'round';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_team_ready', 'is_mech_ready', 'is_ready', 'is_start', 'is_end', 'is_timeout'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['secret'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_team_ready' => 'Is Team Ready',
            'is_mech_ready' => 'Is Mech Ready',
            'is_ready' => 'Is Ready',
            'is_start' => 'Is Start',
            'is_end' => 'Is End',
            'is_timeout' => 'Is Timeout',
            'secret' => 'Secret',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
