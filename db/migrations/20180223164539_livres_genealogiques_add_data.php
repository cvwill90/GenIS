<?php


use Phinx\Migration\AbstractMigration;

class LivresGenealogiquesAddData extends AbstractMigration
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
    public function up()
    {
        $livres = [
            [
                'id_livre'=>1,
                'lib_livre'=>'Livre Principal'
            ],
            [
                'id_livre'=>2,
                'lib_livre'=>'Livre Annexe'
            ],
            [
                'id_livre'=>3,
                'lib_livre'=>'Hors-Livre'
            ]
        ];
        
        $livre_genealogique= $this->table('livre_genealogique');
        $livre_genealogique->insert($livres)
                ->saveData();
    }
    
    public function down() {
        $this->execute('DELETE FROM livre_genealogique');
    }
}
