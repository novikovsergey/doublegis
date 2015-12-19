<?php

use yii\db\Schema;
use yii\db\Migration;

class m151219_084700_create_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('company', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'building_id' => $this->integer()->notNull()
        ]);



        $this->createTable('building', [
            'id' => $this->primaryKey(),
            'address' => $this->string(255)->notNull(),
            'location' =>  'GEOMETRY(POINT, 4326) NOT NULL',
        ]);

        $this->createTable('company_phone', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull(),
            'phone' => $this->string(32)->notNull(),

        ]);



        $this->createTable('rubric', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'parent_id' => $this->integer()->defaultValue(NULL)
        ]);

        $this->createTable('company_rubric',[
            'rubric_id' => $this->integer()->notNull(),
            'company_id' => $this->integer()->notNull(),
            'PRIMARY KEY(rubric_id, company_id)'
        ]);

        $this->addForeignKey('company_building_foreign_idx','company','building_id','building','id','CASCADE','CASCADE');
        $this->addForeignKey('company_phone_company_foreign_idx','company_phone','company_id','company','id','CASCADE','CASCADE');
        $this->addForeignKey('company_rubric_rubric_foreign_idx', 'company_rubric', 'rubric_id', 'rubric', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('company_rubric_company_foreign_idx', 'company_rubric', 'company_id', 'company', 'id', 'CASCADE', 'CASCADE');
        $this->execute('CREATE INDEX building_location_idx ON building USING GIST(location)');

    }

    public function safeDown()    {

        $this->execute('DROP INDEX building_location_idx');
        $this->dropForeignKey('company_building_foreign_idx','company');
        $this->dropForeignKey('company_phone_company_foreign_idx', 'company_phone');
        $this->dropForeignKey('company_rubric_rubric_foreign_idx', 'company_rubric');
        $this->dropForeignKey('company_rubric_company_foreign_idx', 'company_rubric');

        $this->dropTable('company');
        $this->dropTable('building');
        $this->dropTable('company_phone');
        $this->dropTable('rubric');
        $this->dropTable('company_rubric');
    }
}
