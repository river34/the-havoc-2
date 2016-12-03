<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "game".
 *
 * @property integer $id
 * @property integer $resource
 * @property integer $core_score
 * @property string $updated_at
 * @property string $created_at
 */
class Game extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'game';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['resource', 'core_score'], 'required'],
            [['resource', 'core_score'], 'integer'],
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
            'resource' => 'Resource',
            'core_score' => 'Core Score',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
