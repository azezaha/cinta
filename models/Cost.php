<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cost".
 *
 * @property integer $id
 * @property integer $origin
 * @property integer $destination
 * @property string $courier
 * @property string $jenis
 * @property double $cost
 * @property integer $durasi
 */
class Cost extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cost';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin', 'destination', 'courier', 'jenis', 'cost', 'durasi'], 'required'],
            [['origin', 'destination', 'durasi'], 'integer'],
            [['cost'], 'number'],
            [['courier'], 'string', 'max' => 11],
            [['jenis'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin' => 'Origin',
            'destination' => 'Destination',
            'courier' => 'Courier',
            'jenis' => 'Jenis',
            'cost' => 'Cost',
            'durasi' => 'Durasi',
        ];
    }
}
