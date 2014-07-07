<!-- app/View/Users/add.ctp -->
<div class="users form">
    <?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Add User'); ?></legend>
        <?php echo $this->Form->input('email');
        echo $this->Form->input('uitid');
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>