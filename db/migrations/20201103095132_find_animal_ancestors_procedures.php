<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FindAnimalAncestorsProcedures extends AbstractMigration
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
        $sql_procedure_creation_lignee = 
                "CREATE PROCEDURE getLignee( 
                    IN id_orphan_animal INT(11), 
                    OUT name_last_known_ancestor char(50) 
                ) 
                BEGIN 
                        DECLARE id_last_known_ancestor INT(11) DEFAULT id_orphan_animal; 
                    DECLARE id_last_identified_ancestor INT(11) DEFAULT id_orphan_animal; 
                    WHILE id_last_identified_ancestor!=1 DO 
                        SELECT id_pere INTO id_last_identified_ancestor FROM animal WHERE id_animal=id_last_identified_ancestor; 
                        IF id_last_identified_ancestor!=1 THEN 
                                SET id_last_known_ancestor = id_last_identified_ancestor; 
                        END IF; 
                    END WHILE; 
                    SELECT nom_animal INTO name_last_known_ancestor FROM animal WHERE id_animal=id_last_known_ancestor; 
                END";
        
        $sql_procedure_creation_famille = 
                "CREATE PROCEDURE getFamille( 
                    IN id_orphan_animal INT(11), 
                    OUT name_last_known_ancestor char(50) 
                ) 
                BEGIN 
                        DECLARE id_last_known_ancestor INT(11) DEFAULT id_orphan_animal; 
                    DECLARE id_last_identified_ancestor INT(11) DEFAULT id_orphan_animal; 
                    WHILE id_last_identified_ancestor!=2 DO 
                        SELECT id_mere INTO id_last_identified_ancestor FROM animal WHERE id_animal=id_last_identified_ancestor; 
                        IF id_last_identified_ancestor!=2 THEN 
                                SET id_last_known_ancestor = id_last_identified_ancestor; 
                        END IF; 
                    END WHILE; 
                    SELECT nom_animal INTO name_last_known_ancestor FROM animal WHERE id_animal=id_last_known_ancestor; 
                END";
        
        $this->execute($sql_procedure_creation_lignee);
        $this->execute($sql_procedure_creation_famille);
    }
    
    public function down(): void
    {
        $this->execute("DROP PROCEDURE IF EXISTS `getFamille`");
        $this->execute("DROP PROCEDURE IF EXISTS `getLignee`");
    }
}
