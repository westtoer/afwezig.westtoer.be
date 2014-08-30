<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">

        <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#active" role="tab" data-toggle="tab">Actief</a></li>
            <li><a href="#ignored" role="tab" data-toggle="tab">Genegeerd</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="active">
                <br />
                <table class="table">
                    <tr><th>Datum</th><th>Maand</th><th>Jaar</th><th>JSON</th><th>CSV</th><th>Acties</th></tr>

                    <?php
                    $html = '';
                    foreach($exports as $export){
                        $html .= '<tr>';
                        $html .= '<td>' . $export["Export"]["timestamp"] . '</td>';
                        $html .= '<td>' . date('m', strtotime($export["Export"]["start_date"])) . '</td>';
                        $html .= '<td>' . date('Y', strtotime($export["Export"]["start_date"])) . '</td>';
                        $html .= '<td>' . str_replace('/var/www/html/afwezig.westtoer.be/', '', $export["Export"]["json_path"]) . '</td>';
                        $html .= '<td>' . str_replace('/var/www/html/afwezig.westtoer.be/', '', $export["Export"]["xls_path"]) . '</td>';
                        $html .= '<td><a href="' . $this->here . '?ignore=1&id=' . $export["Export"]["id"] . '">Negeer</a></td>';
                        $html .= '</tr>';
                    }
                    if(isset($html)){
                        echo $html;
                    }
                    ?>
                </table>
            </div>
            <div class="tab-pane" id="ignored">
                <br />
                <table class="table">
                    <tr><th>Datum</th><th>Maand</th><th>Jaar</th><th>JSON</th><th>CSV</th></tr>

                    <?php
                    $html = '';
                    foreach($exportsIgnored as $export){
                        $html .= '<tr>';
                        $html .= '<td>' . $export["Export"]["timestamp"] . '</td>';
                        $html .= '<td>' . date('m', strtotime($export["Export"]["start_date"])) . '</td>';
                        $html .= '<td>' . date('Y', strtotime($export["Export"]["start_date"])) . '</td>';
                        $html .= '<td>' . str_replace('/var/www/html/afwezig.westtoer.be/', '', $export["Export"]["json_path"]) . '</td>';
                        $html .= '<td>' . str_replace('/var/www/html/afwezig.westtoer.be/', '', $export["Export"]["xls_path"]) . '</td>';
                        $html .= '</tr>';
                    }
                    if(isset($html)){
                        echo $html;
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>