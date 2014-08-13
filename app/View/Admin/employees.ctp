<div class="row">
    <div class="col-md-3"><?php echo $this->element('admin/base_admin_menu')?></div>
    <div class="col-md-9">
        <h2 class="first">Medewerkers</h2>
        <?php echo $this->Employee->tableEmployees($employees);?></div>
</div>
