<style type = 'text/css'>
    .AccountDiv {
        margin: 5px;
        padding: 10px;
        border: 2px solid rgb(0, 0, 50);
        border-radius: 4px;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
    }
    
    .AccountHeader {
        font-size: 140%;
        border-bottom: 1px dotted;
    }
    
    .PageDiv {
        padding: 3px;
        border-bottom: 1px dashed;
    }
</style>

<div class = 'DividerHeader'>
    New Account Activity
</div>

<div style = 'float: left; width: 50%'>
    <?
        for ($i = 0; $i < count($accounts) / 2; $i++)
            echo $this->element('admin_new_account_activity', array('account' => $accounts[$i]));
    ?>
</div>

<div style = 'float: right; width: 50%'>
    <?
        for ($i = ceil(count($accounts) / 2); $i < count($accounts); $i++)
            echo $this->element('admin_new_account_activity', array('account' => $accounts[$i]));
    ?>
</div>
