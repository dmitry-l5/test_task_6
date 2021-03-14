<?php
class DBManager{
    static private $instance = null;
    static private $pdo;
    private function __construct(){}
    static public function init($config = null){
        if(!self::$instance){
            try {
                if($config){
                    self::$pdo = new PDO("mysql:host=".$config['host'].";dbname=".$config['database'], $config['user'], $config['password']);
                }
            } catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
            }
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function get_employee_delete($id){
        $id = $this->id_validation($id);
        var_dump($id);
        if($id){
            $experiance = $this->get_employee_experience_data($id);
            foreach($experiance as $exp){
                var_dump($exp);echo("<br>");
                static::$pdo->query(
                    "DELETE FROM experience WHERE id_experience = ".$exp['id_experience'].";"
                );
            }
            static::$pdo->query(
                "DELETE FROM employees WHERE id_employees = ".$id.";"
            );
        }
    }
    public function get_employee_list(){
        $stmt = static::$pdo->prepare("select id_employees, surname, name, middle_name, date_of_birth, gender from rp_test.employees");
        if($stmt->execute()){
            $result = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                array_push($result, $row);
            }
            return $result;
        }
        return null;
    }
    public function get_employee_person_data($id){
        if(intval($id)){
            $stmt = static::$pdo->prepare("select id_employees, surname, name, middle_name, date_of_birth, gender from rp_test.employees WHERE id_employees=".intval($id));
            if($stmt->execute()){
                return $row = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
        return null;
    }
    public function get_employee_experience_data($id){
        if(intval($id)){
            $result = array();
            $stmt = static::$pdo->prepare("select id_experience, id_employees, begin_date, end_date, entity, description from rp_test.experience WHERE id_employees=".intval($id)." ORDER BY begin_date asc");
            if($stmt->execute()){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $result[] = $row;
                  }
            }
            return $result;
        }
        return null;
    }
    public function update_person_arr($data_arr){
        //validation - begin
        $isValid = true;
        preg_match("~([0-9]{4})-([0-9]{2})-([0-9]{2})~",$data_arr['date_of_birth'], $match);
        $item = array();
        if(!isset($data_arr['name'])||empty($data_arr['name'])){$isValid = false; array_push($item, "name"); }
        if(!isset($data_arr['middle_name'])||empty($data_arr['middle_name'])){$isValid = false; array_push($item, "middle_name");  }
        if(!isset($data_arr['surname'])||empty($data_arr['surname'])){$isValid = false; array_push($item, "surname");  }
        if(!isset($data_arr['date_of_birth'])||empty($match)){$isValid = false; array_push($item, "date_of_birth");  }
            else{ $data_arr['date_of_birth'] = $match[1]."-".$match[2]."-".$match[3];  }
        if(!isset($data_arr['gender'])||!($data_arr['gender']==='male'||$data_arr['gender']==='female')){$isValid = false; array_push($item, "gender");}
        if(!$isValid){
            return array(
                'status'=>'fail',
                'description'=>'validation failed ',
                'look_for'=>$item,
                'id'=>$data_arr['id'],
            );
        }
        //validation - end

        try{
            $last_id = $data_arr['id'];
            $isUpdate = false;
            if(static::$pdo->query("SELECT COUNT(*) FROM employees WHERE id_employees = ".$data_arr['id'])){
                //update
                //echo("<br>update<br>");
                $stmt = static::$pdo->prepare(
                    "UPDATE employees SET surname=:surname, name=:name, middle_name=:middle_name, date_of_birth=:date_of_birth, gender=:gender  where id_employees=".$data_arr['id'].";"
                );
                $isUpdate = true;
            }else{
                //insert
                //echo("<br>insert<br>");
                $stmt = static::$pdo->prepare(
                    "INSERT INTO employees ( surname, name, middle_name, date_of_birth, gender) 
                    VALUES ( :surname, :name, :middle_name, :date_of_birth, :gender)"
                );
            }
            $stmt->bindParam(':surname', $data_arr['surname']);
            $stmt->bindParam(':name', $data_arr['name']);
            $stmt->bindParam(':middle_name', $data_arr['middle_name']);
            $stmt->bindParam(':date_of_birth', $data_arr['date_of_birth']);
            $gender = (($data_arr['gender']==='male')?1:0);
            $stmt->bindParam(':gender', $gender);
            $stmt->execute();
            $isUpdate?($last_id = $data_arr['id']):($last_id = static::$pdo->lastInsertId());
        }catch(Exception $e){
            echo("\n error: ".$e->getMessage()."\n");
        }
        return array(
            'status'=>'success',
            'id'=>$last_id
        );
    }
    public function update_experience_arr($id, $experience){
        //validation - begin
        $item = array();
        $isValid = true;
        for($i = 0; $i<count($experience); $i++){
            if(isset($experience[$i]['id'])&&!empty($experience[$i]['id'])){
                $experience[$i]['id'] = $this->id_validation($experience[$i]['id']);
                if($experience[$i]['id']==null){
                    $isValid = false; array_push($item, "wrong : id - ".json_encode($experience[$i])); 
                }
            }else{ $experience[$i]['id'] = null; }
            if(!isset($experience[$i]['entity'])||empty($experience[$i]['entity'])){$isValid = false; array_push($item, "wrong : entity"); }
            if(!isset($experience[$i]['begin_date'])||($this->date_validation($experience[$i]['begin_date'])==null)){$isValid = false;  array_push($item, "wrong : begin_date"); }
                else{ $experience[$i]['begin_date'] = $this->date_validation($experience[$i]['begin_date']);}
            if(isset($experience[$i]['end_date'])&&!empty($experience[$i]['end_date'])){
                $experience[$i]['end_date'] = $this->date_validation($experience[$i]['end_date']);
                //if($experience[$i]['end_date'] == null){ $isValid = false; array_push($item, "end_date".json_encode($experience[$i])); }
            }else{
                $experience[$i]['end_date'] = null;
            }
            if(!isset($experience[$i]['description'])){ $experience[$i]['description'] = '';}
        }
        if(!$isValid){
            return array(
                'status'=>'fail',
                'description'=>'validation failed ',
                'look_for'=>$item,
                'id'=>$id,
            );
        }
        //validation - end

        $insert_stmt = static::$pdo->prepare(
                "INSERT INTO experience 
                    ( id_employees,  begin_date, end_date,  entity, description) 
                VALUES 
                    (:id_employees, :begin_date, :end_date, :entity, :description);"
        );
        $update_stmt = static::$pdo->prepare(
                "UPDATE experience SET 
                    begin_date=:begin_date,
                    end_date=:end_date, 
                    entity=:entity,
                    description=:description
                WHERE id_experience=:id_experience;"
        );

        $id_employees = $id;
        $insert_stmt->bindParam(':id_employees', $id_employees);
        $insert_stmt->bindParam(':begin_date', $begin_date);
        $insert_stmt->bindParam(':end_date', $end_date);
        $insert_stmt->bindParam(':entity', $entity);
        $insert_stmt->bindParam(':description', $description);
        
        $update_stmt->bindParam(':begin_date', $begin_date);
        $update_stmt->bindParam(':end_date', $end_date);
        $update_stmt->bindParam(':entity', $entity);
        $update_stmt->bindParam(':description', $description);
        $update_stmt->bindParam(':id_experience', $id_experience);
        foreach($experience as $exp){
            $id_experience = $exp['id'];
            $begin_date = $exp['begin_date'];
            $end_date = $exp['end_date'];
            $entity = $exp['entity'];
            $description = $exp['description'];
            if($id_experience){
                //echo("<br>update eperience<br>");
                //echo("---");var_dump($exp);echo("<br>");
                $update_stmt->execute();
            }else{
                //echo("<br>insert eperience<br>");
                //echo("---");var_dump($exp);echo("<br>");
                $insert_stmt->execute();
            }
        }
        return array(
            'status'=>'success',
        );
    }
    public function delete_experience_arr($id_arr){
        echo("<br>".$id_arr."<br>");
        $delete_stmt = static::$pdo->prepare("DELETE FROM experience WHERE id_experience=:id_experience");
        $delete_stmt->bindParam(":id_experience", $true_id); 
        foreach($id_arr as $id){
            $true_id = $this->id_validation($id);
            echo($id."<br>");
            echo($true_id ."<br>");
            if($true_id){
                $delete_stmt->execute();
            }
        }
        return null;
    }
    private function date_validation($data_str){
        preg_match("~([0-9]{4})-([0-9]{2})-([0-9]{2})~",$data_str, $match);
        if(!empty($match)){
            return $match[1]."-".$match[2]."-".$match[3]; 
        }else{
            return null;
        }
    }
    private function id_validation($data_str){
        preg_match("~(^\d+$)~",$data_str, $match);
        if(!empty($match)){
            return $match[0]; 
        }else{
            return null;
        }
    }

}