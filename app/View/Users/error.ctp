
<h2>Er liep iets mis...</h2>

<?php if(!isset($this->request->params["named"]["error"])){?>
<p>Bij het aanmelden met UiTID is er iets misgelopen. Probeer het aanmelden nogmaals!</p>

<ul>
    <li>Er is iets misgelopen bij UiTID</li>
    <li>Je gebruikersaccount is nog niet actief</li>
</ul>
<?php } else{
    switch ($this->request->params["named"]["error"]){
        case 1:
            ?>
            <p>Dit email adres heeft al een gebruiker, en kan dus niet aan een gebruiker gekoppelt worden. Om dit te verhelpen neemt u contact om met iemand van HR of de systeemadministrator</p>
<?php
        break;
    }
}
?>