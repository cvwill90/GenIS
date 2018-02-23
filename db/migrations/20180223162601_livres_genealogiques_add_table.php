<?php


use Phinx\Migration\AbstractMigration;

class LivresGenealogiquesAddTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        // Add new table: livre_genealogique
        $livre_genealogique = $this->table('livre_genealogique', ['id'=>false]);
        $livre_genealogique->addColumn('id_livre', 'integer', ['limit'=>1])
                ->addColumn('lib_livre', 'char', ['limit'=>15])
                ->addIndex(['id_livre'], ['unique'=>true])
                ->create();
        
        // Add new column to table: animal
        $animal = $this->table('animal');
        $animal->addColumn('id_livre', 'integer', ['limit'=>1, 'null'=>true, 'after'=>'date_naiss'])
                ->addIndex(['id_livre'])
                ->update();
        
        // Add foreign key to table: animal
        $animal->addForeignKey('id_livre', 'livre_genealogique', 'id_livre', ['constraint'=>'fk_livre', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->update();
    }
}
