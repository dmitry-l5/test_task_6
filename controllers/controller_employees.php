<?php

class Employees{
    public function action_index($arg){
        global $db;
        $result['view_name']='employees_table';
        $result['data']= $db->get_employee_list();
        return $result;
    }
    public function action_error($arg){
        global $db;
        $result['view_name']='employees_validation_error';
        $result['data']= $arg;
        return $result;
    }
    public function action_delete($args){
        global $db;
        $db->get_employee_delete($args['id']);
        header('Location:/');
    }
    public function action_edit($args, $errInfo = null){
        global $db;
        $result = array();
        $result = array(
            'view_name'=>'index'
        );
        if($args&&isset($args['id'])){
            $result['data']['person_data'] = $db->get_employee_person_data($args['id']);
            $result['data']['experience_data'] = $db->get_employee_experience_data($args['id']);
        }
        $result['view_name']='employees_edit';
        return $result;
    }
    public function action_save($args){
        global $db;
        $validation_error = array();
        if(isset($_POST['deleted_experience'])){
            $deleted = json_decode($_POST['deleted_experience']);
            $db->delete_experience_arr($deleted);
        }
        if(isset($_POST['person'])){
            $e1 = $db->update_person_arr($_POST['person']);
            if($e1['status']=='fail'){
               array_push($validation_error, $e1);
            }
        }
        if(isset($_POST['experience'])){
            $e2 = $db->update_experience_arr($_POST['person']['id'], $_POST['experience']);
            if($e2['status']=='fail'){
                array_push($validation_error, $e2);
            }
        }
        if(!empty($validation_error)){
            $json_err = json_encode($validation_error);
            header('Location:error?err='.$json_err);
        }else{
            header('Location:edit?id='.$e1['id']);
        }
    }
}