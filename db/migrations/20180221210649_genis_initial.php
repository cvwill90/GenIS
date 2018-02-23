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
        
        ///////////////////
        // Create tables //
        ///////////////////
        
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
                ->addIndex(['code_race', 'id_photo'])
                ->create();
        
        $attribut = $this->table('attribut', ['id'=>'id_attribut']);
        $attribut->addColumn('lib_attribut', 'char', ['limit'=>50])
                ->addColumn('id_espece', 'integer', ['limit'=>11])
                ->create();
        
        $caractere_genetique = $this->table('caractere_genetique', ['id'=>'id_caractere']);
        $caractere_genetique->addColumn('lib_caractere', 'char', ['limit'=>50])
                ->addColumn('comm_caractere', 'char', ['limit'=>200])
                ->create();
        
        $commune = $this->table('commune', ['id'=>'id_commune']);
        $commune->addColumn('lib_commune', 'char', ['limit'=>50])
                ->addColumn('cp_commune', 'char', ['limit'=>10])
                ->addColumn('no_dpt', 'integer', ['limit'=>11])
                ->create();
        
        $compte = $this->table('compte', ['id'=>false, 'primary_key'=>'id_compte']);
        $compte->addColumn('id_compte', 'integer', ['limit'=>15, 'identity'=>true])
                ->addColumn('identifiant', 'char', ['limit'=>30])
                ->addColumn('mdp', 'char', ['limit'=>30])
                ->addColumn('id_privilege', 'integer', ['limit'=>2, 'signed'=>false])
                ->create();
        
        $contact = $this->table('contact', ['id'=>'id_contact']);
        $contact->addColumn('nom', 'char', ['limit'=>50, 'null'=>true])
                ->addColumn('prenom', 'char', ['limit'=>50, 'null'=>true])
                ->addColumn('adresse', 'char', ['limit'=>200, 'null'=>true])
                ->addColumn('adresse2', 'char', ['limit'=>200, 'null'=>true])
                ->addColumn('tel', 'char', ['limit'=>255, 'null'=>true])
                ->addColumn('tel2', 'char', ['limit'=>255, 'null'=>true])
                ->addColumn('mail', 'char', ['limit'=>200, 'null'=>true])
                ->addColumn('id_commune', 'integer', ['limit'=>11, 'null'=>true])
                ->addColumn('notes', 'char', ['limit'=>250, 'null'=>true])
                ->addColumn('id_elevage', 'integer', ['limit'=>11, 'null'=>true])
                ->create();
        
        $departement = $this->table('departement', ['id'=>'no_dpt']);
        $departement->addColumn('lib_dpt', 'char', ['limit'=>255])
                ->addColumn('no_region', 'integer', ['limit'=>2, 'default'=>25])
                ->create();
        
        $elevage = $this->table('elevage', ['id'=>'id_elevage']);
        $elevage->addColumn('nom_elevage', 'char', ['limit'=>100])
                ->addColumn('no_elevage', 'char', ['limit'=>15, 'default'=>0])
                ->addIndex(['id_elevage'])
                ->create();
        
        $espece = $this->table('espece', ['id'=>'id_espece']);
        $espece->addColumn('lib_espece', 'char', ['limit'=>50])
                ->create();
        
        $ligne_caractere = $this->table('ligne_caractere', ['id'=>false]);
        $ligne_caractere->addColumn('id_animal', 'integer', ['limit'=>11])
                ->addColumn('id_caractere', 'integer', ['limit'=>11])
                ->create();
        
        $link_race_elevage = $this->table('link_race_elevage', ['id'=>false]);
        $link_race_elevage->addColumn('id_elevage', 'integer', ['limit'=>11])
                ->addColumn('code_race', 'integer', ['limit'=>10])
                ->create();
        
        $periode = $this->table('periode', ['id'=>'id_periode']);
        $periode->addColumn('date_entree', 'date', ['null'=>true])
                ->addColumn('date_sortie', 'date', ['null'=>true])
                ->addColumn('valide_periode', 'integer', ['limit'=>1])
                ->addColumn('id_animal', 'integer', ['limit'=>11])
                ->addColumn('id_elevage', 'integer', ['limit'=>11, 'null'=>true])
                ->addColumn('id_type', 'integer', ['limit'=>11])
                ->create();
        
        $privilege = $this->table('privilege', ['id'=>false, 'primary_key'=>'id_privilege']);
        $privilege->addColumn('id_privilege', 'integer', ['limit'=>2, 'signed'=>false, 'identity'=>true])
                ->addColumn('lib_privilege', 'char', ['limit'=>20])
                ->create();
        
        $race = $this->table('race', ['id'=>false, 'primary_key'=>'code_race']);
        $race->addColumn('code_race', 'integer', ['limit'=>10, 'identity'=>true])
                ->addColumn('lib_race', 'char', ['limit'=>30])
                ->addColumn('abbrev', 'char', ['limit'=>255])
                ->addColumn('id_espece', 'integer', ['limit'=>11, 'default'=>11])
                ->create();
        
        $region = $this->table('region', ['id'=>false, 'primary_key'=>'no_region']);
        $region->addColumn('no_region', 'integer', ['limit'=>2, 'identity'=>true])
                ->addColumn('lib_region', 'char', ['limit'=>255])
                ->create();
        
        $type_periode = $this->table('type_periode', ['id'=>'id_type']);
        $type_periode->addColumn('lib_type', 'char', ['limit'=>15])
                ->create();
        
        $valeur_attribut = $this->table('valeur_attribut', ['id'=>'id_valeur_attribut']);
        $valeur_attribut->addColumn('valeur', 'char', ['limit'=>10])
                ->addColumn('id_animal', 'integer', ['limit'=>11])
                ->addColumn('id_attribut', 'integer', ['limit'=>11])
                ->create();

        //////////////////////
        // Add foreign keys //
        //////////////////////
        
        $animal->addForeignKey('id_pere', 'animal', 'id_animal', ['constraint'=>'fk_pere', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->addForeignKey('id_mere', 'animal', 'id_animal', ['constraint'=>'fk_mere', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->update();
        
        $attribut->addForeignKey('id_espece', 'espece', 'id_espece', ['constraint'=>'fk_index', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->update();
        
        $commune->addForeignKey('no_dpt', 'departement', 'no_dpt', ['constraint'=>'fk_departement', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->update();
        
        $compte->addForeignKey('id_privilege', 'privilege', 'id_privilege', ['constraint'=>'fk_privilege', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->update();
        
        $contact->addForeignKey('id_commune', 'commune', 'id_commune', ['constraint'=>'fk_commune', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->addForeignKey('id_elevage', 'elevage', 'id_elevage', ['constraint'=>'fk_elevage2', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->update();
        
        $departement->addForeignKey('no_region', 'region', 'no_region', ['constraint'=>'departement_ibfk_2', 'delete'=>'RESTRICT', 'update'=>'RESTRICT'])
                ->update();
        
        $ligne_caractere->addForeignKey('id_caractere', 'caractere_genetique', 'id_caractere', ['constraint'=>'ligne_caractere_ibfk_2', 'delete'=>'RESTRICT', 'update'=>'RESTRICT'])
                ->addForeignKey('id_animal', 'animal', 'id_animal', ['constraint'=>'ligne_caractere_ibfk_3', 'delete'=>'RESTRICT', 'update'=>'RESTRICT'])
                ->update();
        
        $link_race_elevage->addForeignKey('id_elevage', 'elevage', 'id_elevage', ['constraint'=>'fk_elevage_1', 'delete'=>'RESTRICT', 'update'=>'RESTRICT'])
                ->addForeignKey('code_race', 'race', 'code_race', ['constraint'=>'fk_race', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->update();
        
        $periode->addForeignKey('id_type', 'type_periode', 'id_type', ['constraint'=>'periode_ibfk_1', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->addForeignKey('id_animal', 'animal', 'id_animal', ['constraint'=>'periode_ibfk_2', 'delete'=>'RESTRICT', 'update'=>'RESTRICT'])
                ->addForeignKey('id_elevage', 'elevage', 'id_elevage', ['constraint'=>'periode_ibfk_3', 'delete'=>'RESTRICT', 'update'=>'RESTRICT'])
                ->update();
        
        $race->addForeignKey('id_espece', 'espece', 'id_espece', ['constraint'=>'fk_espece', 'delete'=>'NO_ACTION', 'update'=>'NO_ACTION'])
                ->update();
        
        $valeur_attribut->addForeignKey('id_attribut', 'attribut', 'id_attribut', ['constraint'=>'valeur_attribut_ibfk2', 'delete'=>'RESTRICT', 'update'=>'RESTRICT'])
                ->addForeignKey('id_animal', 'animal', 'id_animal', ['constraint'=>'valeur_attribut_ibfk3', 'delete'=>'RESTRICT', 'update'=>'RESTRICT'])
                ->update();
    }
}
