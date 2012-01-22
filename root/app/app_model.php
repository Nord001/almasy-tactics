<?

//--------------------------------------------------------------------------------------------
function IsValidFormationName ($name) {
    if (preg_match('/^[a-z0-9 \']*$/', strtolower($name)) == 0)
        return false;

    if (strlen($name) == 0)
        return false;

    return true;
}

//----------------------------------------------------------------------------------------------
// Ensures that a result is not false.
function G ($result, $err = false) {
    if ($result === false) {
        if ($err === false)
            IERR('Argument passed was false.');
        else
            IERR($err);
    }
    return $result;
}

//----------------------------------------------------------------------------------------------
class AppModel extends Model {

    var $recursive = -1;
    var $actsAs = array('Containable');

    var $transactionLevel = 0;

    //----------------------------------------------------------------------------------------------
    function begin () {
        if ($this->transactionLevel == 0)
            $this->query('START TRANSACTION');
        $this->transactionLevel++;
    }

    //----------------------------------------------------------------------------------------------
    function commit () {
        $this->transactionLevel--;
        if ($this->transactionLevel < 0)
            IERR('Commit called without begin.');
        if ($this->transactionLevel == 0) {
            $success = $this->query('COMMIT');
            if ($success === false)
                IERR('Commit failed.');
        }
    }

    //----------------------------------------------------------------------------------------------
    function rollback () {
        $this->transactionLevel--;
        if ($this->transactionLevel < 0)
            IERR('Rollback called without begin.');
        $this->query('ROLLBACK');
    }

    //----------------------------------------------------------------------------------------------
    function fastSave ($field, $data) {
        $saveStr = '';
        if ($data === '')
            $saveStr = 'NULL';
        else
            $saveStr = "'" . mysql_escape_string($data) . "'";

        return $this->query(sprintf("
            UPDATE
                `%s`
            SET
                `{$field}` = %s
            WHERE
                `id` = '%s'
            LIMIT 1",
            $this->table,
            $saveStr,
            $this->id
        )) !== false;
    }

    //---------------------------------------------------------------------------------------------
    // Begin on the fly model chains
    // http://www.pseudocoder.com/archives/2009/04/17/on-the-fly-model-chains-with-cakephp/
    var $__definedAssociations = array();
    var $__loadAssociations = array('Aro', 'Aco', 'Permission');

    function __construct($id = false, $table = null, $ds = null) {
        if (!in_array(get_class($this), $this->__loadAssociations)) {
            foreach($this->__associations as $association) {
                foreach($this->{$association} as $key => $value) {
                    $assocName = $key;

                    if (is_numeric($key)) {
                        $assocName = $value;
                        $value = array();
                    }

                    $value['type'] = $association;
                    $this->__definedAssociations[$assocName] = $value;
                    if (!empty($value['with'])) {
                        $this->__definedAssociations[$value['with']] = array('type' => 'hasMany');
                    }
                }

                $this->{$association} = array();
            }
        }

        parent::__construct($id, $table, $ds);

        // Load related models with no assocation
        // http://bin.cakephp.org/saved/38624
        if (!empty($this->knows)) {
          foreach ($this->knows as $alias => $modelName) {
              if (is_numeric($alias)) {
                  $alias = $modelName;
                }
                $model = array('class' => $modelName, 'alias' => $alias);
                if (PHP5) {
                    $this->{$alias} = ClassRegistry::init($model);
                } else {
                    $this->{$alias} =& ClassRegistry::init($model);
                }
            }
        }
    }

    function __isset($name) {
        return $this->__connect($name);
    }

    function __get($name) {
        return $this->__connect($name);
    }

    function __connect($name) {
        if (empty($this->__definedAssociations[$name])) {
            return false;
        }

        $this->bind($name, $this->__definedAssociations[$name]);
        return $this->{$name};
    }

    // End Code
    //---------------------------------------------------------------------------------------------

    // Added caching

    /**
     * Get Enum Values
     * Snippet v0.1.3
     * http://cakeforge.org/snippet/detail.php?type=snippet&id=112
     *
     * Gets the enum values for MySQL 4 and 5 to use in selectTag()
     * Tested with PHP 4/5 and CakePHP 1.1.8
     */
    function getEnumValues($columnName = null)
    {
        if ($columnName == null) { return array(); } //no field specified

        $cacheKey = GenerateCacheKey('enum', $this->name, $columnName);
        $cacheDuration = 'long';
        $assoc_values = Cache::read($cacheKey, $cacheDuration);

        if ($assoc_values)
            return $assoc_values;

        //Get the name of the table
        $db =& ConnectionManager::getDataSource($this->useDbConfig);
        $tableName = $db->fullTableName($this, false);


        //Get the values for the specified column (database and version specific, needs testing)
        $result = $this->query("SHOW COLUMNS FROM {$tableName} LIKE '{$columnName}'");

        //figure out where in the result our Types are (this varies between mysql versions)
        $types = null;
        if     ( isset( $result[0]['COLUMNS']['Type'] ) ) { $types = $result[0]['COLUMNS']['Type']; } //MySQL 5
        elseif ( isset( $result[0][0]['Type'] ) )         { $types = $result[0][0]['Type'];         } //MySQL 4
        else   { return array(); } //types return not accounted for

        //Get the values
        $values = explode("','", preg_replace("/(enum)\('(.+?)'\)/","\\2", $types) );

        //explode doesn't do assoc arrays, but cake needs an assoc to assign values
        $assoc_values = array();
        foreach ( $values as $value ) {
            //leave the call to humanize if you want it to look pretty
            $assoc_values[$value] = Inflector::humanize($value);
        }

        Cache::write($cacheKey, $assoc_values, $cacheDuration);

        return $assoc_values;

    } //end getEnumValues

    //---------------------------------------------------------------------------------------------
    // Loaded related models with no association
    var $knows = array();
}

?>
