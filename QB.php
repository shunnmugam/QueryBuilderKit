<?php
class QB
{
	const V_DB_SERVER = "localhost";
    const V_DB_USER = "redwebde_rwsrest";
    const V_DB_PASSWORD = "N!*eiGO_tQz7";
    const V_DB = "redwebde_rws_restaurant";
    //public $validationdb = NULL;

    public $table;
    public $tableCapture;
    public $select;
    public $selectCaptureQuery;
    public $insert;
    public $actionCapture;
    public $where;
    public $join;
    public $orderby;
    public $limit;
    public $min;
    public $max;
    public $count;
    public $avg;
    public $sum;
    public $orderArray = [];
    
	public function __construct()
    {
    	$this->validationdb = "";
    	self::validation_dbConnect(); // Initiate Database connection
        $this->errorArray = []; // Initiate errorArray
    }
    //Database connection
    public function validation_dbConnect()
    {
        $this->validationdb = new mysqli(self::V_DB_SERVER, self::V_DB_USER, self::V_DB_PASSWORD,self::V_DB);
        //$this->validationdb = mysql_connect(self::V_DB_SERVER, self::V_DB_USER, self::V_DB_PASSWORD);
         if ($this->validationdb->connect_error)
            die("Connection failed: " . $this->validationdb->connect_error);
             //mysql_select_db(self::V_DB, $this->validationdb);
    }
    //set main table
    public function table($table)
    {
        $this->tableCapture = $table;
    	$this->table = "from ".$table ." ";
        $this->orderArray[1] =  "table";
        return $this;
    }
    public function checkTable()
    {
        if($this->table == "")
        {
            return false;
        }
    }
    //select
    public function select($query)
    {
        $this->actionCapture = "select";
        $this->select = "select ".$query." ";
        $this->selectCaptureQuery = $query;
        $this->orderArray[0] =  "select";
        return $this;
    }
    //insert
    public function insert($queryarray)
    {
        $colarray   = [];
        $valuearray = [];
        $type       = 1;
        $this->actionCapture = "insert";
            if(is_array($queryarray))
            {
                foreach ($queryarray as $key => $value) {
                    if(!is_array($value))
                    {
                        $type         = 1;
                        $colarray[]   = $key;
                        $valuearray[] = $value;
                    }
                    else
                    {
                        $type         = 2;
                        foreach ($value as $key1 => $value1) {
                            $colarray[]   = $key1;
                            $valuearray[] = $value1;
                        }

                    }
                }
                if($type == 1)
                {
                    $columns = implode(",", $colarray);
                    $values  = implode(",", $valuearray);
                    $sql = "insert into $this->tableCapture ($columns)
                            VALUES ($values)";
                }
                elseif ($type == 2) {
                    $unique_column = array_unique($columns);
                }
            }
            else
                echo "insert function need array";

    }
    //where functions
        //where
    public function where($col,$operator,$value,$boolean = "and")
    {
        if(isset($this->orderArray[3]))
        {
            $this->where.= " ".$boolean." ".$col." ".$operator." '".$value."'";
        }
        else
        {
            $this->where = "where ".$col." ".$operator." '".$value."'";
        }
        $this->orderArray[3] =  "where";
        return $this;
    }
        //OR
    public function orWhere($col,$operator,$value)
    {
        $this->where($col,$operator,$value,"or");
        return $this;
    }
        //IN
    public function whereIn($col,$values)
    {
        if(isset($this->orderArray[3]))
        {
            $this->where.= " and ".$col." in (".implode(",",$values).")";
        }
        else
        {
            $this->where = "where ".$col." in (".implode(",",$values).")";
        }
        $this->orderArray[3] =  "where";
        return $this;
    }
        //BETWEEN
    public function whereBetween($col,$from,$to,$type="")
    {
        if(isset($this->orderArray[3]))
        {
            $this->where.= " and ".$col." ".$type." between '".$from."' and '".$to."' ";
        }
        else
        {
            $this->where = "where ".$col." ".$type." between  '".$from."' and '".$to."' ";
        }
        $this->orderArray[3] =  "where";
        return $this;

    }
    public function whereNotBetween($col,$from,$to)
    {
        return $this->whereBetween($col,$from,$to,"not");
    }
    //where end
    //join functions
    public function join($col,$oncol1,$operator,$oncol2,$type="")
    {
        $this->join.=  " ".$type." join ".$col." on (".$oncol1.$operator.$oncol2.") ";
        $this->orderArray[2] =  "join";
        return $this;
    }
    //last functions
    public function distinct($col)
    {
        $captureselectArray = explode(",", $this->selectCaptureQuery);
        if (in_array($col, $captureselectArray)) {
            $array_key = array_search($col,$captureselectArray);
            if($array_key!=0)
            {
                $tmpval=$captureselectArray[0];
                $captureselectArray[0] = $col;
                $captureselectArray[$array_key] = $tmpval;
                $this->select = "select distinct ".implode(",", $captureselectArray)." ";
            }
            
        }
        return $this;
    }
    public function orderby($col)
    {
        $this->orderby = " order by ".$col;
        $this->orderArray[4] =  "orderby";
        return $this;
    }
    public function limit($limit)
    {
        $this->limit = " limit ".$limit;
        $this->orderArray[4] =  "limit";
        return $this;
    }
    
//final functions non-return object functions
    public function toSql()
    {
        // print_r($this->orderArray);
        $sql = "";
        $maxkey = max(array_keys($this->orderArray));
        for($i=0;$i<=$maxkey;$i++) {
            if(isset($this->orderArray[$i])){
            $qry = $this->orderArray[$i];
            $sql.=$this->$qry;
            }
        }
        //print_r($sql);exit;
        return $sql ;
    }
    public function get()
    {
        $sql = $this->toSql();
        $result =$this->validationdb->query($sql);
        //$sql  = mysql_query($sql, $this->validationdb);
        if($result==false)
        {
            echo "Query Error: " . $this->validationdb->error;
            exit;
        }
        if ($result->num_rows > 0) {
            while($results = $result->fetch_assoc())
            {
                $re[] = $results;
            }
            return json_encode($re);
        }else{
            return  array();
        }
    }
    public function first()
    {
        $sql = $this->limit(1)->toSql();
        $sql  = $this->validationdb->query($sql);
        if($sql==false)
        {
            echo "Query Error: " . $this->validationdb->error;
            exit;
        }
        if ($sql!=false && $sql->num_rows > 0) 
            return json_encode($sql->fetch_assoc());
        else
            return  array();
    }
    public function toArray()
    {
        $sql = $this->toSql();
        $sql  = $this->validationdb->query($sql);
        if($sql==false)
        {
            echo "Query Error: " . $this->validationdb->error;
            exit;
        }
        if ($sql!=false && $sql->num_rows > 0) {
            while($results = $sql->fetch_assoc())
            {
                $re[] = $results;
            }
            return $re;
        }else{
            return  array();
        }

    }
    public function find($value)
    {
        $sql = "SHOW KEYS ".$this->table." WHERE Key_name = 'PRIMARY'";
        $sql  = $this->validationdb->query($sql);
        if($sql==false)
        {
            echo "Query Error: " . $this->validationdb->error;
            exit;
        }
        $results = $sql->fetch_assoc();
        $col = $results["Column_name"];
        return $this->select("*")->where($col,"=",$value)->get();
    }
    //aggregate functions
        //min
    public function min($col)
    {
        $this->min = " select min($col) ";
        $this->orderArray[0] =  "min";
        $obj = $this->get();
        return $this->getValueFromObject($obj,"min(".$col.")");
    }
        //max
    public function max($col)
    {
        $this->max = " select max($col) ";
        $this->orderArray[0] =  "max";
        $obj = $this->get();
        return $this->getValueFromObject($obj,"max(".$col.")");
    }
        //count
    public function count()
    {
        
        $this->count = " select * ";
        $this->orderArray[0] =  "count";
        return count($this->toArray());
        // $obj = $this->get();
        // return $this->getValueFromObject($obj,"count(".$col.")");
    }
        //avg
    public function avg($col)
    {
        $this->avg = " select avg($col) ";
        $this->orderArray[0] =  "avg";
        $obj = $this->get();
        return $this->getValueFromObject($obj,"avg(".$col.")");
    }
        //sum
    public function sum($col)
    {
        $this->sum = " select sum($col) ";
        $this->orderArray[0] =  "sum";
        $obj = $this->get();
        return $this->getValueFromObject($obj,"sum(".$col.")");
    }
//common functions
    private function getValueFromObject($obj,$col)
    {
        foreach (json_decode($obj) as $key => $value) {
            $value =  $value->$col;
         }
         return $value;
         // print_r(json_decode($obj));
         // exit;
    }
}
$QB = new QB;
//select quries
/*
$test =  $QB->table("cities as c")
            //->find(10);
            ->join("states as s","s.id","=","c.state_id","right")
             //->join("countries as c","c.id","=","s.country_id")
             ->where("s.name","like","%Tamil%")
             //->where("c.name","=","Arani")
             //->whereBetween("c.name","C","E")
            ->whereNotBetween("c.id",3600,3670)
             //->orWhere("state_id","=",2)
             //->whereIn("id",explode(",","1,2,3"))
            ->select("c.id as cid,c.name as cname,s.id as sid,s.name as sname")
            // //->orderby("name")
             ->limit(60)
            // //->count();
            //->first();
            //->sum("c.id");
            ->get();
*/

//insert quries
$data = array('name' =>'bengaluru' ,'state_id'=>17 );
$test = $QB->table("cities")
        ->insert($data);
print_r($test);
// foreach (json_decode($test) as $key => $value) {
//     echo $value->id."<br>";
// }
// print_r(json_decode($test));

?>