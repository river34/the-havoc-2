<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "player".
 *
 * @property integer $id
 * @property string $name
 * @property string $key
 * @property string $device
 * @property string $ip
 * @property integer $score
 * @property integer $mark
 * @property integer $is_active
 * @property string $updated_at
 * @property string $created_at
 */
class Player extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'player';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'key'], 'required'],
            [['score', 'mark', 'is_active'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['name', 'key', 'device', 'access_token', 'email', 'media'], 'string', 'max' => 255],
            [['ip'], 'string', 'max' => 45],
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
            'key' => 'Key',
            'device' => 'Device',
            'ip' => 'Ip',
            'score' => 'Score',
            'mark' => 'Mark',
            'access_token' => 'Access Token',
            'email' => 'Email',
            'media' => 'Media',
            'is_active' => 'Is Active',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
