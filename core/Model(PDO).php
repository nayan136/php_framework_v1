<?php


abstract class Model
{

    public function create() {
        $db = Database::getInstance();
        $mysqli = $db->getConnection();

        $attributes = $this->sanitized_attributes();

        $tableName = static::$table_name;

        $columnNames = array();
        $placeHolders = array();
        $values = array();

        foreach($attributes as $key=>$val)
        {
            // skip identity field
            if ($key == static::$identity)
                continue;
            $columnNames[] = '`' . $key. '`';
            $placeHolders[] = '?';
            $values[] = $val;
        }

        $sql = "Insert into `{$tableName}` (" . join(',', $columnNames) . ") VALUES (" . join(',', $placeHolders) . ")";

        $statement = $mysqli->stmt_init();
        if (!$statement->prepare($sql)) {
            die("Error message: " . $mysqli->error);
            return;
        }

        $bindString = array();
        $bindValues = array();

        // build bind mapping (ssdib) as an array
        foreach($values as $value) {
            $valueType = gettype($value);

            if ($valueType == 'string') {
                $bindString[] = 's';
            } else if ($valueType == 'integer') {
                $bindString[] = 'i';
            } else if ($valueType == 'double') {
                $bindString[] = 'd';
            } else {
                $bindString[] = 'b';
            }

            $bindValues[] = $value;
        }

        // prepend the bind mapping (ssdib) to the beginning of the array
        array_unshift($bindValues, join('', $bindString));

        // convert the array to an array of references
        $bindReferences = array();
        foreach($bindValues as $k => $v) {
            $bindReferences[$k] = &$bindValues[$k];
        }

        // call the bind_param function passing the array of referenced values
        call_user_func_array(array($statement, "bind_param"), $bindReferences);

        $statement->execute();
        $statement->close();

        return true;
    }
}