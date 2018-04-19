<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "kota".
 *
 * @property integer $id_kota
 * @property string $nama_kota
 */
class Kota extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kota';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_kota'], 'required'],
            [['nama_kota'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_kota' => 'Id Kota',
            'nama_kota' => 'Nama Kota',
        ];
    }
}
