<?php if(!empty($nonActiveEmployees)):?>
    <table class="table">
    <tr><th>Naam</th><th>Trigram</th><th>Link</th></tr>
    <?php foreach($nonActiveEmployees as $nonActiveEmployee):?>
        <tr id="<?php echo $nonActiveEmployee['Employee']['id'];?>"><td><?php echo $nonActiveEmployee['Employee']['name'] . ' ' . $nonActiveEmployee['Employee']['surname'];?></td><td><?php echo $nonActiveEmployee['Employee']['3gram'];?></td><td><?php echo $this->Html->link('Link aan mijn UiTID', array('controller' => 'Employees', 'action' => 'associate', 'assoc' => $nonActiveEmployee['Employee']['id'], 'uitid' => $this->request->params['named']['uitid']));?></td></tr>
    <?php endforeach;?>
    </table>
<?php endif;?>