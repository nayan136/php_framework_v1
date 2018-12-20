<?php


abstract class Model
{
    private $conn;
    private $table;
    private $query;

    public function __construct(){
        $this->conn = DB::getInstance()->getConn();
    }

    public function setTable($table){
        $this->table = $table;
    }

    public function tableExists($table) {
        $result = $this->conn->query("SHOW TABLES LIKE '".$table."'");
        if($result->num_rows > 0){
            return true;
        }else{
            return false;
        }
    }

    public function getAll(){
        return $this->select()->get();
    }

    public function getUniqueCol(){
        if(isset($this->uniqueCol)){
            return $this->uniqueCol;
        }else{
            return null;
        }
    }

    public function insert($values,$columns=null, $cus_pk=false){
        $uniqueCol=$this->getUniqueCol();
        $duplicate = false;
        $query = "SHOW columns FROM ".$this->table;
        $result = $this->conn->query($query);
        $allCol = [];
        while($row = $result->fetch_array()){
            array_push($allCol,$row['Field']);
        }
        if(is_null($columns)){
            $columns = $allCol;
            if(!$cus_pk){
                array_shift($columns);
            }
        }else{
            $columns = toArray($columns);
        }
        $values = toArray($values);

        if(!is_null($uniqueCol)){
            $duplicate = $this->checkDuplicate($columns,$values,$uniqueCol);
        }
//        return $duplicate;
        if(count(array_diff($columns,$allCol))===0){
            if(!$duplicate){
                $col = implode(',',$columns);
                $val="";
                foreach ($values as $v){
                    $val .= "'".$v."',";
                }
                $val = rtrim($val,',');
                $statement = "INSERT INTO ".$this->table." (".$col.") VALUES (".$val.")";
                $result = $this->conn->query($statement);
                if($result){
                    $last_id = $this->conn->insert_id;
                    return $last_id;
                    //$last_record = $this->select()->where("id",$last_id)->getData();
//                    return status(OK,$last_id);
                }else{
                    return "Error: Not Found";
//                    return status(NOT_FOUND);
                }
            }else{
//                return status(EXIST);
                return "Error: Duplicate";
            }
        }else{
            echo "Columns Not found: ".implode(',',array_diff($columns, $allCol));
        }

    }

    public function delete($id){
        $statement = "DELETE FROM ".$this->table." WHERE id=".$id;
        if($this->conn->query($statement)){
//            return status(OK,$id);
            return $id;
        }else{
//            return status(ERROR);
            return "Error: Delete Failed";
        }
    }

    public function update($col,$val,$id){
        $col = toArray($col);
        $val = toArray($val);
        if(count($col) === count($val)){
            $string="";
            for($i=0;$i<count($col);$i++){
                $string .= $col[$i]."= '".$val[$i]."', ";
            }
            $string = rtrim($string,', ');
            $statement = "UPDATE ".$this->table." SET ".$string." WHERE id=".$id;
            $result = $this->conn->query($statement);
            $count = $this->conn->affected_rows;
            if($result){
                if($count>0){
                    return $id;
//                    return status(OK,$id);
                }else{
                    return "No Change Occured";
//                    return status(EXIST);
                }
            }else{
                return "Error";
//                return status(ERROR);
            }
        }else{
            echo "coulumn and value mismatched";
        }
    }

//    start method chaining
    public function select($cols='*',$from=null){
        if($from !== null){
            $this->query = "SELECT ".$cols." FROM ".$this->table.",".$from." ";
        }else{
            $this->query = "SELECT ".$cols." FROM ".$this->table." ";
        }

        return $this;
    }

    public function where($col,$val,$oper='='){
        if(strpos($this->query,' WHERE ')){
            $this->query .= "AND ".$col.$oper."'".$val."' ";
        }else{
            $this->query .= "WHERE ".$col.$oper."'".$val."' ";
        }
        return $this;
    }

    public function whereIn($col,$val){
        if(is_array($val)){
            $val = implode($val,',');
        }
        if(strpos($this->query,' WHERE ')){
            $this->query .= "AND ".$col." IN (".$val.") ";
        }else{
            $this->query .= "WHERE ".$col." IN (".$val.") ";
        }
        return $this;
    }

    public function whereNotIn($col,$val){
        if(is_array($val)){
            $val = implode($val,',');
        }
        if(strpos($this->query,' WHERE ')){
            $this->query .= "AND ".$col." NOT IN (".$val.") ";
        }else{
            $this->query .= "WHERE ".$col." NOT IN (".$val.") ";
        }
        return $this;
    }

    public function whereJoin($col,$val,$oper='='){
        if(strpos($this->query,' WHERE ')){
            $this->query .= "AND ".$col.$oper.$val." ";
        }else{
            $this->query .= "WHERE ".$col.$oper.$val." ";
        }
        return $this;
    }

    public function whereBetween($col,$values){
        if(strpos($this->query,' WHERE ')){
            $this->query .= "AND ".$col." BETWEEN ".$values[0]." AND ".$values[1]." ";
        }else{
            $this->query .= "WHERE ".$col." BETWEEN ".$values[0]." AND ".$values[1]." ";
        }
        return $this;
    }

    public function query($statement){
        $this->query .= $statement." ";
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

    public function chunk($start, $range){
        $this->query .= "LIMIT ".$start.", ".$range." ";
        return $this;
    }

    public function join($tableName, $current='id', $foreign='id')
    {
        $this->query .= "JOIN ".$tableName." ON ".$this->table.".".$current."=".$tableName.".".$foreign." ";
        return $this;
    }

    public function groupBy($col){
        $this->query .="GROUP BY ".$col." ";
        return $this;
    }

    public function get(){
//        echo $this->query;
        $res = $this->conn->query($this->query);
        $result = array();
        if($res){
            if($res->num_rows >0 ){
                while($row = $res->fetch_assoc()){
                    array_push($result, $row);
                }
                return $result;
            }else{
                return "Error: Not Found";
//                return status(NOT_FOUND);
            }

        }else{
            return "Error";
//            return status(ERROR);
        }
    }

    public function count(){
        $res = $this->conn->query($this->query);
        return $res->num_rows;
    }

    private function getData(){
        $res = $this->conn->query($this->query);
        return (array)$res->fetch_assoc();
    }

    public function toSql(){
        echo $this->query;
    }

    private function checkDuplicate($columns,$values,$uniqueCol){
        $duplicate = false;
        $found = array_search($uniqueCol,$columns);
        if($found !== false){
            $val = $values[$found];
            if($this->select()->where($uniqueCol, $val)->count()){
                $duplicate = true;
            }
        }
        // echo $this->select()->where($uniqueCol, $val)->toSql();
        // var_dump($this->select()->where($uniqueCol, $val)->count());
        // var_dump($duplicate);
        return $duplicate;
//        if(!$duplicate){
//            return true;
//        }else{
//            return false;
//        }
    }
}