<div class="container">
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="logo navbar-btn pull-left" href="/" title="Home">
                    <img src="http://intranet.westtoer.be/sites/default/files/logo_0.png" alt="Home">
                </a>
                <a class="navbar-brand" href="/" style="margin-left: 6px;">Afwezig</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
                <strong>
                    <ul class="nav navbar-nav navbar-right">
                            <?php
                            $employee = $this->Session->read('Auth.Employee');
                            if($this->Session->read('Auth.Role.id') == 1 OR  $this->Session->read('Auth.Role.id') == 2){
                                echo '<li>' . $this->Html->link('Administratie', array('controller' => 'Admin', 'action' => 'index')) . '</li>';
                            }
                            if($isSupervisor == true){
                                echo '<li>' . $this->Html->link('Verlof Goedkeuren', array('controller' => 'Requests', 'action' => 'index')) . '</li>';
                            }
                            ?>
                            <?php
                            if(!empty($employee)){
                                echo '<li>' . $this->Html->link('Mijn afwezigheidsblad', array('controller' => 'employees', 'action' => 'view', 'me')) . '</li>';
                                echo '<li>' . $this->Html->link('Uitloggen', array('controller' => 'users', 'action' => 'logout')) . '</li>';
                            }
                            ;?>
                    </ul>
                </strong>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</div>