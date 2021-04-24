<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AnimalRemoveColumns extends AbstractMigration
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
    
    public function up(): void
    {
        $animal = $this->table('animal');
        $animal->dropForeignKey('id_livre')
                ->removeColumn('id_livre')
                ->removeColumn('pourcentage_sang_race')
                ->removeColumn('fondateur')
                ->save();
    }
    
    public function down(): void
    {
        $animal = $this->table('animal');
        $animal->addColumn('fondateur', 'boolean', ['after'=>'reproducteur', 'default'=>0, 'comment'=>'Specifie si l\'animal est fondateur (1) ou non (0)'])
                ->addColumn('pourcentage_sang_race', 'decimal', ['after'=>'coeff_consang', 'precision'=>4, 'scale'=>3, 'default'=>NULL, 'null'=>true, 'comment'=>'Indique le pourcentage de sang de race de l\'animal. La valeur doit etre comprise entre 0 et 1 et doit avoir une precision de 3 apres la virgule.'])
                ->addColumn('id_livre', 'integer', ['limit'=>1, 'null'=>true, 'after'=>'date_naiss'])
                ->addIndex(['id_livre'])
                ->addForeignKey('id_livre', 'livre_genealogique', 'id_livre', ['constraint'=>'fk_livre', 'delete'=>'RESTRICT', 'update'=>'RESTRICT'])
                ->save();
    }
}
