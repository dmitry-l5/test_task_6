<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="container">
            <?php
                var_dump(Page::getData('data'));
                $data = Page::getData('data');
            ?>

            <div class="toolbar">
                <a class="add_button" href="employees/edit">
                    <button>Добавить сотрудника</button>
                </a>
            </div>
            <table class="employees">
                <tr>
                    <td class='thead'>ФИО сотрудника</td>
                    <td class='thead'>Дата рождения</td>
                    <td class='thead'>Пол</td>
                    <td class='thead'>Редактировать</td>
                    <td class='thead'>Удалить</td>
                </tr>
                <?php if(isset($data)):?>
                    <?php foreach($data as $row):?>
                        <tr>
                            <td><?= $row['surname'].' '.$row['name'].' '.$row['middle_name'] ?></td>
                            <td><?=$row['date_of_birth']?></td>
                            <td><?=$row['gender']?"М":"Ж"?></td>
                            <td><a href="edit?id=<?=$row['id_employees']?>">Редактировать</a></td>
                            <td><a href="delete?id=<?=$row['id_employees']?>">Удалить</a></td>
                        </tr>
                    <?php endforeach?>
                <?php endif;?>
            </table>
        </div>
    </body>
</html>