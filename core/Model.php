<?php


abstract class Model
{
    private $conn;
    private $table;
    private $query;
    private $uniqueCol = array();
    private $whereValues = array();
    private $createdAtExist = false;
    private $createdAtPosition = null;
    private $updatedAtExist = false;
    private $updatedAtPosition = null;

    public function __construct(){
        $this->conn = DB::getInstance()->getConn();
    }

    public function setTable($table){
        $this->table = $table;
    }

    public function setUniqueCol($uniqueCol){
        $this->uniqueCol = toArray($uniqueCol);
    }

    public function tableExists($table) {
        $query = $this->conn->query("SHOW TABLES LIKE '".$table."'");
        if($query->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function getColumns(){
        $query = "SHOW COLUMNS FROM ".$this->table;
        $result = $this->conn->query($query);
        $allCol = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            array_push($allCol,$row['Field']);
        }

        //check if all unique column present in the table
        $commonArray = array_intersect($this->uniqueCol,$allCol);
        if(sizeof($commonArray) != sizeof($this->uniqueCol)){
            printError("Unique Columns not present in the table");
        }

        $this->createdAtPosition = (array_search(CREATED_AT,$allCol));
        $this->updatedAtPosition = (array_search(UPDATED_AT,$allCol));

        if($this->createdAtPosition !== false){
            $this->createdAtExist = true;
        }
        if($this->updatedAtPosition !== false){
            $this->updatedAtExist = true;
        }

//        var_dump($this->createdAtExist);
//        var_dump($this->updatedAtExist);
//        die();
        return $allCol;
    }

    private function insertInArray($values){
        if($this->createdAtExist && $this->updatedAtExist){
            if($this->createdAtPosition > $this->updatedAtPosition){
                $values = insertIntoArray($this->updatedAtPosition,$values,Date::getCurrentDateTime());
                $values = insertIntoArray($this->createdAtPosition,$values,Date::getCurrentDateTime());
            }else{
                $values = insertIntoArray($this->createdAtPosition,$values,Date::getCurrentDateTime());
                $values = insertIntoArray($this->updatedAtPosition,$values,Date::getCurrentDateTime());
            }

        }elseif($this->createdAtExist){
            $values = insertIntoArray($this->createdAtPosition,$values,Date::getCurrentDateTime());
        }elseif($this->updatedAtExist){
            insertIntoArray($this->updatedAtPosition,$values,Date::getCurrentDateTime());
        }

        return $values;
    }

    private function checkDuplicateEntry(){

    }

    public function insert($values,$columns=null, $remove_pk=true){
        $values = toArray($values);
        if(is_null($columns)){
            $columns = $this->getColumns();
        }else{
            $remove_pk = false;
        }
        if($remove_pk){
            array_shift($columns);
        }

        //$values = $this->insertInArray($values);

        $totalColumnsSize = sizeof($columns);
        //printError($totalColumnsSize);

        if($valueSize = sizeof($values) != $totalColumnsSize){
            printError("Column Size $totalColumnsSize and Value Size $valueSize mismatch");
            die();
        }

        // check for duplicate in the unique columns
        if(!is_null($this->uniqueCol)){
            $isDuplicatePresent = $this->checkDuplicateEntry();
        }

        $columnString = implode(',',$columns);
        $questionMark = "";
        while($totalColumnsSize > 0){
            $questionMark .= "?";
            if($totalColumnsSize != 1){
                $questionMark .= ",";
            }
            $totalColumnsSize--;
        }
        $this->query = "INSERT INTO ".$this->table." (".$columnString.") VALUES (".$questionMark.")";
        $this->exe($values);
    }


    // the function is used for inserting or updating or deleting
    private function exe($values){
        //printError($this->query);

        try{
            $stmt = $this->conn->prepare($this->query);
            foreach($values as $k => $v){
                $stmt->bindValue(++$k,$v);
            }
            $stmt->execute();

        }catch (Exception $e){
            printError($e->getMessage());
        }
        $stmt->close();
    }

    public function select($cols = null){
        if(is_null($cols)){
            $this->query = "SELECT * FROM ".$this->table;
        }else{
            $cols = toArray($cols);
            $this->query = "SELECT (".implode(',',$cols).") FROM ".$this->table;
        }

        return $this;
    }

    public function where($col,$val,$op = '='){
        array_push($this->whereValues,$val);
//        if where present in the query
        if(strpos($this->query,'WHERE') !== false){
            $this->query .= " AND $col $op ?";
        }else{
            $this->query .= " WHERE $col $op ?";
        }

        return $this;
    }

    public function orderBy($col,$order='ASC'){
        if(strpos($this->query,' ORDER BY ')){
            $this->query .= ", ".$col." ".$order;
        }else{
            $this->query .= "ORDER BY ".$col." ".$order." ";
        }
        return $this;
    }

    public function innerJoin($table,$ownCol,$otherCol){
        if(!empty($table) && !empty($ownCol) && !empty($otherCol)){
            $this->query .= " INNER JOIN $table ON ".$this->table.".$ownCol  = $table.$otherCol";
            return $this;
        }else{
            printError("All Parameters are not given for innerJoin");
            die();
        }
    }

    public function raw($query,$values){
        $this->query = $query;
        if(!is_array($values)){
            array_push($this->whereValues,$values);
        }else{
            $this->whereValues = array_merge($this->whereValues,$values);
        }
        return $this;
    }

    public function get(){
        $arr = array();
        if(sizeof($this->whereValues) == 0){
            try{
                $stmt = $this->conn->query($this->query);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr[] = $row;
                }
            }catch (Exception $e){
                printError($e->getMessage());
            }
        }else{
            try{

                $stmt = $this->conn->prepare($this->query);
                foreach($this->whereValues as $k => $v){
                    $stmt->bindValue(++$k,$v);
                }
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr[] = $row;
                }
            }catch (Exception $e){
                printError($e->getMessage());
            }
        }

        //printError($this->query);
        return $arr;
    }

    public function toSql(){
        printError($this->query);
    }

    private function dataType($values){
        $bindString = "";
        foreach($values as $value) {
            $valueType = gettype($value);
            if ($valueType == 'string') {
                $bindString .= 's';
            } else if ($valueType == 'integer') {
                $bindString .= 'i';
            } else if ($valueType == 'double') {
                $bindString .= 'd';
            } else {
                $bindString .= 'b';
            }
        }

        return $bindString;
    }

}