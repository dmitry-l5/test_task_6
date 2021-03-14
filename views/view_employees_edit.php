<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
    <br>
        <?php
            $person = static::getData('data', 'person_data');
            $valid_err = static::getData('data', 'validation_error');
            if($valid_err){
                echo($valid_err);
            }
        ?>
        <div class="container">
        <form class="edit_form" onsubmit='form_prepare(event)' action="/employees/save" method="post">
            <div>
                <input type="hidden" name="person[id]" value=<?=($person&&(isset($person['id_employees'])))?($person['id_employees']):('')?>><br>
                <div class="w-100 text-centered">Имя</div>
                <input type="text" name="person[name]" value=<?=($person&&(isset($person['name'])))?($person['name']):('')?>><br>
                <div class="w-100 text-centered">Фамилия</div>
                <input type="text" name="person[surname]" value=<?=($person&&(isset($person['surname'])))?($person['surname']):('')?>><br>
                <div class="w-100 text-centered">Отчество</div>
                <input type="text" name="person[middle_name]" value=<?=($person&&(isset($person['middle_name'])))?($person['middle_name']):('')?>><br>
                <div class="w_100 text-centered"> День рождения</div>
                <input class="w-100" type="date" name="person[date_of_birth]" value=<?=($person&&(isset($person['date_of_birth'])))?($person['date_of_birth']):('')?>><br>
                <div class="w-100 text-centered">Пол</div>
                <div class="gender_panel">
                    <div>
                        <label for="gender_m">Муж</label>
                        <input id='gender_m' type="radio" name="person[gender]" value="male"   <?=($person&&(isset($person['gender']))&&($person['gender']==1) )?('checked'):('')?>><br>
                    </div>
                    <div>
                        <label for="gender_f">Жен</label>
                        <input id='gender_f' type="radio" name="person[gender]" value="female" <?=($person&&(isset($person['gender']))&&($person['gender']==0) )?('checked'):('')?>><br>
                    </div>
                </div>
                <button class="w-100" type="submit">сохранить</button>
            </div>
            <div>
            <h3>опыт работы</h3>
                <div id='experience_dashboard'>
                    <input id="deleted_experience" type="hidden" name="deleted_experience" value="">
                    <div class="experiance_board" id='experience_template' style='display:none;'>
                        <input type='hidden' name='id' disabled  mark='id'><br>
                        <div>Организация</div>
                        <input type='text'   name='entity' disabled><br>
                        <div>Выполняемые обязанности</div>
                        <input type='text'   name='description' disabled><br>
                        <div class="experiance_board_subpanel">
                            <div>С</div>
                            <input type='date'   name='begin_date' disabled><br>
                            <div>По</div>
                            <input type='date'   name='end_date' disabled><br>
                            <button onclick='delete_experience(event)'>Удалить организацию</button>
                        </div>
                    </div>
                    <?php 
                        $exp_data = static::getData('data', 'experience_data'); 
                        $counter = 0;
                    ?>
                    <?php if($exp_data):?>
                        <?php foreach($exp_data as $exp):?>
                            <div class="experiance_board">
                                <input type='hidden' name='experience[<?=$counter?>][id]' value=<?=$exp['id_experience']?> mark='id'><br>
                                <div>Организация</div>
                                <input type='text'   name='experience[<?=$counter?>][entity]' value="<?=$exp['entity']?>"><br>
                                <div>Выполняемые обязанности</div>
                                <input type='text'   name='experience[<?=$counter?>][description]' value="<?=$exp['description']?>"><br>
                                <div class="experiance_board_subpanel">
                                    <div>С</div>
                                    <input type='date'   name='experience[<?=$counter?>][begin_date]' value=<?=$exp['begin_date']?>><br>
                                    <div>По</div>
                                    <input type='date'   name='experience[<?=$counter?>][end_date]' value=<?=$exp['end_date']?>><br>
                                    <button onclick='delete_experience(event)'>Удалить организацию</button>
                                </div>
                            </div>
                            <?php $counter++;?>
                        <?php endforeach?>
                    <?php endif?>
                </div>
                <button onclick='add_experience(event)'>Добавить организацию</button>
                <script>
                    var counter = <?=$counter;?>;
                    var deleted = [];
                    function form_prepare(event){
                        deleted_experience.value = JSON.stringify(deleted); ;
                    }
                    function delete_experience(event){
                        event.preventDefault();
                        let id = event.target.parentNode.parentNode.querySelector("[mark=id]").value;
                        if(id&&id>0){
                            deleted.push(id);
                        }
                        event.target.parentNode.parentNode.remove();
                    }
                    function add_experience(event){
                        let newNode = experience_template.cloneNode(true);
                        newNode.removeAttribute('id');
                        let childs = newNode.querySelectorAll("input");
                        newNode.removeAttribute('style');
                        for(let i = 0; i < childs.length; i++){
                            childs[i].removeAttribute('disabled');
                            if(childs[i].hasAttribute('name')){
                                childs[i].setAttribute('name', `experience[${counter}][${childs[i].getAttribute('name')}]`)
                            }
                        }
                        counter++;
                        experience_dashboard.append(newNode);
                        event.preventDefault();
                    }
                </script>
            </div>
        </form>
    </body>
</html>