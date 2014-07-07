<div class="panel panel-default">
    <div class="panel-heading">Afwezigheid Aanvragen</div>
    <div class="panel-body">

        <?php echo $this->Form->create('CalendarItem', array('url' => array('controller' => 'CalendarItems', 'action' => 'add')));?>
        <div class="form-group fullwidth spaced">

                <div class="row">
                    <div class="col-md-8 formspaced-left">
                        <input type="date" class="form-control" name="data[CalendarItem][start_date]" id="CalendarItemStartDate">
                    </div>
                    <div class="col-md-4 formspaced-right">
                        <?php echo $this->Form->input('start_time', array('class' => 'form-control', 'label' => false, 'options' => array(array('name' =>'AM', 'value' => 'AM'), array('name' =>'PM', 'value' => 'PM'), array('name' => 'DAG', 'value' => 'DAG'))));?>
                    </div>
                </div>
        </div>
       <div class="form-group fullwidth spaced">
                       <div class="row">
                           <div class="col-md-8 formspaced-left">
                               <input type="date" class="form-control" name="data[CalendarItem][end_date]" id="CalendarItemEndDate">
                           </div>
                           <div class="col-md-4 formspaced-right">
                               <?php echo $this->Form->input('end_time', array('class' => 'form-control', 'label' => false, 'options' => array(array('name' =>'AM', 'value' => 'AM'), array('name' =>'PM', 'value' => 'PM'), array('name' => 'DAG', 'value' => 'DAG'))));?>
                           </div>
                       </div>
               </div>
        <?php echo $this->Form->input('replacement_id', array('class' => 'form-control spaced', 'label' => false,
            'options' => $employeesOptions))
        ;?>
        <?php echo $this->Form->input('calendar_item_type_id', array('class' => 'form-control spaced', 'label' => false,
                    'options' => $typesOptions))
                ;?>
        <?php echo $this->Form->input('note', array('class' => 'form-control spaced', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Notitie'));?>
        <?php echo $this->Form->hidden('employee_id', array('value' => $this->Session->read('Auth.Employee.Employee.id')));?>
        <?php echo $this->Form->submit('Vraag aan', array('class' => 'btn btn-primary fullwidth'));?>
    </div>
</div>
