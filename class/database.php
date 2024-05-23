<?php // <-- Fichier class/database.php --> 
// <!-- Fichier Classes Database requete BDD + connexion à la base de données --> 
class Database{ 
    private $connection; 
    private $permission; 

    /** 
     * Fonction pour se connecter à la base de données 
     */ 
    public function __construct() { 
        try{ 
            $this->connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD); 
            $this->connection->exec("set names utf8mb4"); 
        }catch(PDOException $exception){ 
            echo "Erreur de connexion : " . $exception->getMessage(); 
            exit; 
        }        
    } 
    /*  
    *   Fonction pour exécuter une requête SQL  
    */  
    public function select($table = null, $indexColumn = null, $where = null , $option = null , $orderBy = null, $groupby = null, $having = null , $paramsFiltered = null) {  
        $where = (array) $where;  
        $k = 0;  
        // détermine si $where existe  
        if(isset($where) ){  
            $hasLikeKey = false;  
            // on vérifie si la clé contient le préfixe 'like_'  
            foreach ($where as $key => $value) {  
                if (strpos($key, 'like_') === 0) {  
                    $hasLikeKey = true;  
                    break;  
                }  
            }  
            // si il y a des préfixe like_ on compte le nombre de conditions LIKE  
            if ($hasLikeKey) {  
                $likeCount = array_count_values(array_map(function($key) {  
                    return substr($key, 0, 5);  
                }, array_keys($where)))['like_'];  
            }      
        }  
        // Définit la clause SQL WHERE  
        if(!isset($indexColumn)){  
            $query = "SELECT * FROM " . $table . " ";  
        }else{     

            // si index column array
            if(is_array($indexColumn)){
                $query = "SELECT " . implode(", ", $indexColumn) . " FROM " . $table . " ";  
            }else{
                $query = "SELECT " . $indexColumn . " FROM " . $table . " ";
            }
        }  
        if (isset($where) && !empty($where)) {  
            $query .= "WHERE ";  
            $i = 0;  
            // Parcours le tableau de données  
            foreach ($where as $key => $value) {  
                // Si $value est un tableau, on utilise IN pour la clause SQL  
                if (is_array($value)) {  
                    $query .= $key . " IN (";  
                    $j = 0;  
                    foreach ($value as $val) {  
                        $query .= $val;  
                        $j++;  
                        if ($j < count($value)) {  
                            $query .= ",";  
                        }  
                    }  
                    $query .= ")";  
                } else {  
                    // vérifie si la chaîne de caractères like_ est au début de la clé  
                    if (strpos($key, 'like_') === 0) {                         
                        // si on rentre pour la 1ère fois on ajout une '('  
                        if ($k == 0) {  
                            $query .= "(";  
                        }   
                        $k++;  
                        // Supprime le préfixe "like_" de la clé  
                        $column = substr($key, 5);  
                        $query .= $column . " LIKE '%" . $value . "%'";  
                        // On défini l'option de la clause SQL pour les LIKE  
                        $option = 'OR';  
                        // Si on est à la dernière condition LIKE on ajoute une ')'  
                        if ($k == $likeCount) {  
                            $query .= ")";  
                        }  
                    } else {  
                        $query .= $key . " = '" . $value ."'";  
                    }  
                }  
                $i++;  
                // Ajoute un AND/OR si il y a plusieurs conditions  
                if ($i < count($where)) {  
                    $query .= $option == null ? ' AND ': ' '.$option.' '  . " ";  
                }  
 
            }              
        } 
 
        // Définit la clause SQL GROUP BY 
        if ($groupby != null) { 
            $query .= " GROUP BY ".$groupby[0]." "; 
        } 
        // Définit la clause SQL HAVING 
        if ($having != null) { 
            $query .= " HAVING ".$having." "; 
        } 
        // Définit la clause SQL ORDER BY  
        if ($orderBy != null) {  
            $column = $orderBy[0]['column'] + 1;  
            $query .= " ORDER BY ".$column." ".$orderBy[0]['dir']." ";  
        }  
        if($paramsFiltered != null){  
            $query .= " limit " . $paramsFiltered['start'] . ", " . $paramsFiltered['length'] . "";  
        }  
 
        try{  
 
            $statement = $this->connection->prepare($query);  
            $statement->execute();  
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);  
            return $result;  
        }  
        catch(PDOException $exception){  
            echo "Erreur de connexion : " . $exception->getMessage();  
        }  
    }  
 
      /** 
     *  Insertion dans une table 
     *  @param string $table 
     * @param array $insert ex: array('username' => '🍏✨.', 'password' => '123456') 
     */ 
    public function insert($table,$insert){ 
        $query = "INSERT INTO ".$table." ("; 
        $i = 0; 
        foreach($insert as $key => $value){ 
            $query .= $key; 
            $i++; 
            if($i < count($insert)){ 
                $query .= ", "; 
            } 
        } 
        $query .= ") VALUES ("; 
        $i = 0; 
        foreach($insert as $key => $value){ 
            $query .= "'".$value."'"; 
            $i++; 
            if($i < count($insert)){ 
                $query .= ", "; 
            } 
        } 
        $query .= ");"; 
        $statement = $this->connection->prepare($query); 
        $statement->execute(); 
        $result = $this->connection->lastInsertId(); 
        return $result; 
    } 
    /** 
     * Update dans une table 
     * @param string $table 
     * @param array $values ex: array('id' => '1', 'username' => '🍏✨.', 'password'=>'1234') 
     * @param array $where ex: array('id' => '1')
     */ 

    public function update($table, $values, $where){ 
        if(!$this->permission->hasPerm()){ 
            return ['error' => 'Vous n\'avez pas les droits pour effectuer cette action']; 
        } 
        $query = "UPDATE ".$table." SET "; 
        $i = 0; 
        foreach($values as $key => $value){ 
            $query .= $key." = '".$value."'"; 
            $i++; 
            if($i < count($values)){ 
                $query .= ", "; 
            } 
        } 
        $query .= " WHERE "; 
        $i = 0; 
        foreach($where as $key => $value){ 
            $query .= $key." = '".$value."'"; 
            $i++; 
            if($i < count($where)){ 
                $query .= " AND "; 
            } 
        } 
        $query .= ";"; 
        $statement = $this->connection->prepare($query); 
        $statement->execute(); 
        $result = $statement->rowCount(); 
        return $result; 
    }

 
} 
?> 
