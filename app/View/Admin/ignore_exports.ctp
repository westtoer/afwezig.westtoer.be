<div class="row">
    <div class="col-md-3">
        <?php echo $this->element('admin/base_admin_menu');?>
    </div>
    <div class="col-md-9">
        <table class="table">
            <tr><th>Datum</th><th>Maand</th><th>Jaar</th><th>JSON</th><th>CSV</th><th>Acties</th></tr>

            <?php
            foreach($exports as $export){
                $html = '<tr>';
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
        <p></p>
    </div>
</div>