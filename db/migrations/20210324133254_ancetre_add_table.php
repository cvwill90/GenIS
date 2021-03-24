<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AncetreAddTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // Add new table: livre_genealogique
        $ancetre = $this->table('ancetre', ['id'=>false]);
        $ancetre->addColumn('id_ancetre', 'integer', ['limit'=>11])
                ->addColumn('type_ancetre', 'enum', ['values'=>['fondateur', 'titre_initial']])
                ->addColumn('pourcentage_sang_race', 'decimal', ['precision'=>4, "scale"=>3])
                ->addColumn('id_livre', 'integer', ["limit"=>1])
                ->addIndex(['id_ancetre'], ["unique"=>true])
                ->addIndex(['id_livre'])
                ->create();
        
        // Add foreign key to table: animal
        $ancetre->addForeignKey('id_livre', 'livre_genealogique', 'id_livre', ['constraint'=>'fk_ancetre_livre_genealogique', 'delete'=>'RESTRICT', 'update'=>'RESTRICT'])
                ->addForeignKey('id_ancetre', 'animal', 'id_animal', ['constraint'=>'fk_ancetre_animal', 'delete'=>'CASCADE', 'update'=>'CASCADE'])
                ->update();
    }
}