<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m190409_074537_admin_log_init
 */
class m190409_074537_admin_log_init extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%admin_log}}', [
            'id'          => $this->primaryKey(),
            'user_id'     => $this->integer()->notNull(),
            'username'    => $this->string(20),
            'type'        => $this->tinyInteger(3),
            'table_name'  => $this->string(30),
            'description' => $this->text(),
            'route'       => $this->string(50),
            'ip'          => $this->string(20),
            'created_at'  => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->createIndex('user_id', '{{%admin_log}}', ['user_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%admin_log}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190409_074537_admin_log_init cannot be reverted.\n";

        return false;
    }
    */
}
