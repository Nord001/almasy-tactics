<div style = 'position: absolute; top: 8px; right: 10px; font-size: 70%'>
    <input type = 'button' value = 'Store' id = 'StoreButton' />
    <script type = 'text/javascript'>
    var storeUrl = '<?= $html->url(array('controller' => 'items', 'action' => 'store')); ?>';
    $('#StoreButton').click(function(event) {
        event.preventDefault();

        window.location = storeUrl;
    });
    </script>
</div>