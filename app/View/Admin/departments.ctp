<div class="row">
    <?php foreach($departments as $department){?>
        <div class="col-md-4">
            <div class="well flat">
                <div class="row">
                    <div class="col-md-4">
                        <h2 class="first"><?php echo $department["EmployeeDepartment"]["name"];?></h2>
                        <a href="<?php echo $this->here;?>?action=delete&id=<?php echo $department["EmployeeDepartment"]["id"];?>">Verwijder <?php echo $department["EmployeeDepartment"]["name"];?></a>
                    </div>
                    <div class="col-md-8">
                        <ul class="nulled">
                            <?php foreach($department["Employees"] as $employee){
                                echo '<li>'. $employee["name"] . ' ' . $employee["surname"] . '</li>';
                            }?>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>