<?php
namespace classes\dao\super;

class SuperDAO
{
    
    //if data found it returns an array of arrays (ex:- Array[Arrays[data]]) i.e., it returns multiple records,
    protected function getMultipleRecords($conn, $query){
        $recordsArr = null;
        if(empty($query))
            return $recordsArr;
            $record = mysqli_query($conn,$query);
            if($record){
                $recordsArr = array();
                while($result = mysqli_fetch_assoc($record)){
                    array_push($recordsArr, $result);
                }
            }
            
            if(empty($recordsArr))
                return null;
                return $recordsArr;
    }
    
    //if data found it returns an array i.e., it returns one record
    protected function getARecord($conn, $query){
        $result = null;
        if(empty($query))
            return $result;
            $record = mysqli_query($conn,$query);
            if($record)
                $result = mysqli_fetch_assoc($record);
                
                return $result;
    }
    
    //this function is common for all DML operations like insertion,updation and deletions queries
    private function insertOrUpdateOrDeleteRecord($conn, $query){
        $flag = false;
        if(empty($query))
            return $flag;
            $record = mysqli_query($conn,$query);
            if(mysqli_affected_rows($conn) > 0){
                $flag = true;
            }
            
            return $flag;
    }
    
    protected function insertRecordAndGetId($conn, $query){
        $id = null;
        if(empty($query))
            return $id;
            $record = mysqli_query($conn,$query);
            if($record){
                $id = mysqli_insert_id($conn);
            }
            
            return $id;
    }
    
    protected function insertRecord($conn, $query){
        return $this -> insertOrUpdateOrDeleteRecord($conn, $query);
    }
    
    protected function updateRecord($conn, $query){
        return $this -> insertOrUpdateOrDeleteRecord($conn, $query);
    }
    
    protected function deleteRecord($conn, $query){
        return $this -> insertOrUpdateOrDeleteRecord($conn, $query);
    }
}

