# QueryBuilderKit

#Available functions
1.table("tablename");    //*
2.join("tablename","field1","operator","field2","jointype");  //used to join a table
//where conditions
3.where("field1","operator","value");       //used to where condition]
4.whereBetween("field1","value1","value2")
5.whereNotBetween("field1","value1","value2")
6.orWhere("field1","operator","value")
7.whereIn(field,valuearray)
//select
8.select()
9.orderby("columnname");
10.limit(value);
//aggrigative functions
11.count()
12.sum("columnname");
13.avg("columnname");
14.min("columnname");
15.max("columnname");
//fetch functions(final functions)
16.first();   //first result
17.get();   //return result (object)
18.toArray(); //return result array
19.find("primarykey");  
20.toSql();   //return query



#Example codes
$QB = new QB;
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
            
