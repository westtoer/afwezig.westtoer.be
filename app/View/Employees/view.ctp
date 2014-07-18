<div class="row">
    <div class="col-md-9">
        <?php
            echo $this->Request->tableRequests($requests);
        ;?>
    </div>
    <div class="col-md-3">
        <div class="well flat">
            <h3 class="first"><?php echo $employee["Employee"]["name"] . ' ' . $employee["Employee"]["surname"] ;?></h3>
            <hr />
            <ul class="nulled">
                <li>Dienst: <?php echo $employee["EmployeeDepartment"]["name"];?></li>
                <li>Telefoon: <?php echo $employee["Employee"]["telephone"];?></li>
                <li>Telefoon: <?php echo $employee["Employee"]["gsm"];?></li>
                <li>Email: <?php if(strpos($employee["Employee"]["3gram"], "@") !== false){echo $employee["Employee"]["3gram"];} else {echo $employee["Employee"]["3gram"] . '@westtoer.be';}?></li>
            </ul>
        </div>
        <div class="well flat">
            <h3 class="first">Notitie</h3>
            <p><?php echo $employee["Employee"]["note"];?></p>
        </div>
        <?php if($employee["Employee"]["id"] == $this->Session->read('Auth.Employee.id')){?>
            <?php echo $this->Html->link('Aanpassen', array('controller' => 'users', 'action' => 'management'));?>
        <?php }?>
    </div>
</div>