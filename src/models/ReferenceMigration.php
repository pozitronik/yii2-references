<?php
declare(strict_types = 1);

namespace pozitronik\references\models;

use yii\db\Migration;

/**
 * Class ReferenceMigration
 * @property string $table_name
 */
class ReferenceMigration extends Migration {

	public string $table_name;

	/**
	 * {@inheritdoc}
	 */
	public function safeUp():void {
		$this->createTable($this->table_name, [
			'id' => $this->primaryKey(),
			'name' => $this->string(255)->notNull(),
			'deleted' => $this->boolean()->notNull()->defaultValue(false)
		]);

		$this->createIndex($this->table_name.'_deleted', $this->table_name, 'deleted');
		$this->createIndex($this->table_name.'_name', $this->table_name, 'name');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown():void {
		$this->dropTable($this->table_name);
	}
}