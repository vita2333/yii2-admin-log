<?php
/**
 * Author: vita2333
 * Date: 2019/4/9
 */

namespace AdminLog\models;

/**
 *
 * This is the model class for table "{{%admin_log}}" .
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $username
 * @property int    $type
 * @property string $table_name
 * @property string $description
 * @property string $route
 * @property string $ip
 * @property string $created_at
 */
class AdminLog extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['description'], 'string'],
            [['created_at'], 'safe'],
            [['username', 'ip'], 'string', 'max' => 20],
            [['table_name'], 'string', 'max' => 30],
            [['route'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'user_id'     => 'User ID',
            'username'    => 'Username',
            'type'        => 'Type',
            'table_name'  => 'Table Name',
            'description' => 'Description',
            'route'       => 'Route',
            'ip'          => 'Ip',
            'created_at'  => 'Created At',
        ];

    }
}