<?php


use Phinx\Migration\AbstractMigration;

class GenisInitial extends AbstractMigration
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
        $animal = $this->table('animal', ['id'=>'id_animal']);
        $animal->addColumn('nom_animal', 'char', ['limit'=>50])
                ->addColumn('sexe', 'integer', ['limit'=>1])
                ->addColumn('no_identification', 'char', ['limit'=>13,'default'=>'0000000000'])
                ->addColumn('date_naiss', 'date')
                ->addColumn('reproducteur', 'integer', ['limit'=>1])
                ->addColumn('fecondation', 'integer', ['limit'=>1])
                ->addColumn('coeff_consang', 'decimal', ['precision'=>5, 'scale'=>5])
                ->addColumn('conservatoire', 'integer', ['limit'=>1])
                ->addColumn('valide_animal', 'integer', ['limit'=>1])
                ->addColumn('code_race', 'integer', ['limit'=>10, 'null'=>true])
                ->addColumn('id_pere', 'integer', ['limit'=>11, 'null'=>true])
                ->addColumn('id_mere', 'integer', ['limit'=>11, 'null'=>true])
                ->addColumn('id_photo', 'integer', ['limit'=>11, 'null'=>true])
                ->addForeignKey('id_pere', 'animal', 'id_animal', ['constraint'=>'fk_pere', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->addForeignKey('id_mere', 'animal', 'id_animal', ['constraint'=>'fk_mere', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->create();
    }
}
