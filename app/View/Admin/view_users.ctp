<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <?php
        if(!empty($usersActive)){?>
        <h2 id="active" class="first">Actieve gebruikers</h2>
        <?php
            echo $this->Admin->tableUsers($usersActive, 'active');
        }
        ?>


        <?php
        if(!empty($usersPending)){?>
        <h2 id="pending" class="first">Registraties</h2>
        <?php
            echo $this->Admin->tableUsers($usersPending, 'pending');
        }
        ?>

        <?php
        if(!empty($usersPending)){?>
            <h2 id="pending" class="first">Geweigerde gebruikers</h2>
            <?php
            echo $this->Admin->tableUsers($usersDenied, 'denied');
        }
        ?>
    </div>
</div>