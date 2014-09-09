<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu')?>
    </div>
    <div class="col-md-9">
        <h2 class="first">Een stramien aanpassen</h2>
        <?php echo $this->Form->create('Stream', array('url' => '/Admin/editStream'));?>
        <!-- Controls -->
        <div class="well flat">
            <div class="row">
                <div class="col-md-6 formspaced-left">
                    <h4>Stramien aanpassen voor <?php echo $employee["Employee"]["name"];?> <?php echo $employee["Employee"]["surname"];?> </h4>
                </div>
                <div class="col-md-6 formspaced-right">
                    <a OnClick="toggleAssymetric()" id="assymetry" class="btn btn-primary fullwidth">Tweewekelijks</a>
                </div>

            </div>
        </div>

        <!-- Form -->

        <?php echo $this->Stream->addStream($calendaritemtypes);?>
        <?php echo $this->Form->Submit('Opslaan', array('class' => 'btn btn-success fullwidth'));?>
        <?php echo $this->Form->end();?>
    </div>
</div>

<?php echo $this->Html->script('Admin/newStream');?>
<script>
    var prev = [
        <?php $keys = array_keys($streams);?>
    <?php foreach($streams as $key => $stream){;?>
    {'element':'<?php echo $stream["element"];?>', 'calendar_item_type':'<?php echo $stream["calendar_item_type_id"];?>'}<?php if($key != $keys[(count($keys) - 1)]){ echo ',';};?><?php echo PHP_EOL;?>
        <?php } ;?>
    ]

</script>
<?php echo $this->Html->script('Admin/editStream');?>