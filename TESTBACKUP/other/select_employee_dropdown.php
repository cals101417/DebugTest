
<div class="form-group row">
   <label class="col-4" for="<?=$select_id ?>">Conducted by</label>   
    <select class="form-control form-control-sm col-8" id="<?=$select_id ?>" name="<?=$select_name ?>">
        <?php
        $employee_sql = $conn->query("SELECT `employee_id`, `firstname`, `middlename`, `lastname`
            FROM tbl_employees WHERE  `is_deleted` = 0 ORDER BY `firstname` ASC");
        $employees_qry1 = $employee_sql->fetchAll();
//        var_dump($employees_qry1);
        ?>

        <?php foreach ($employees_qry1 as $employee1): ?>
            <?php $employee_fullname = ucwords(strtolower($employee1['firstname']." ".$employee1['middlename']." ".$employee1['lastname'])); ?>

            <option value="<?=$employee1['employee_id'] ?>"><?=$employee_fullname?></option>
        <?php endforeach ?>
    </select>  
</div>
<!-- asdasdas -->