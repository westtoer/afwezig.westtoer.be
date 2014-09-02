<div class="row">
    <div class="col-md-3">
        <div class="alert alert-info"><p><strong>Tip! </strong>Gebruik de velden bovenaan de tabel om te zoeken. Geef een zoekterm in en druk op enter.</p></div>
    </div>
    <div class="col-md-9">
        <?php if(!empty($nonActiveEmployees)):?>
            <table class="table" id="filtertable">
                <tr><th>Naam</th><th>Trigram</th><th>Link</th></tr>
                <?php foreach($nonActiveEmployees as $nonActiveEmployee):?>
                    <tr id="<?php echo $nonActiveEmployee['Employee']['id'];?>"><td><?php echo $nonActiveEmployee['Employee']['name'] . ' ' . $nonActiveEmployee['Employee']['surname'];?></td><td><?php echo $nonActiveEmployee['Employee']['3gram'];?></td><td><?php echo $this->Html->link('Link aan mijn UiTID', array('controller' => 'Employees', 'action' => 'associate', 'assoc' => $nonActiveEmployee['Employee']['id'], 'uitid' => $this->request->params['named']['uitid'], 'email' => $this->request->params["named"]["email"]));?></td></tr>
                <?php endforeach;?>
            </table>
        <?php endif;?>

        <script language="javascript" type="text/javascript">
            setFilterGrid("filtertable");
        </script>
    </div>
</div>