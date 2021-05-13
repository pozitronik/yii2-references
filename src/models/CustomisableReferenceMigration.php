<?php
declare(strict_types = 1);

namespace pozitronik\references\models;

use yii\db\Migration;

/**
 * Class CustomisableReferenceMigration
 * @property string $table_name
 */
class CustomisableReferenceMigration extends Migration {

	public string $table_name;

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable($this->table_name, [
			'id' => $this->primaryKey(),
			'name' => $this->string(255)->notNull(),
			'color' => $this->string(255)->null(),
			'textcolor' => $this->string(255)->null(),
			'deleted' => $this->boolean()->notNull()->defaultValue(false)
		]);

		$this->createIndex('deleted', $this->table_name, 'deleted');
		$this->createIndex('name', $this->table_name, 'name');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable($this->table_name);
	}
}