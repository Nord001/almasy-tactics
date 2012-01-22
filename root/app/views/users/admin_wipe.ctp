<h2>Wiping Users...</h2>

<h2>Number of Users: <?= $numIds; ?></h2>

<div id = 'OutputDiv' style = 'width: 600px; border: 1px dashed; height: 400px; overflow: auto; padding: 10px;'></div>

<script type = 'text/javascript'>
    function Wipe (startId) {
        var output = $('#OutputDiv');
        $('#OutputDiv').append('userid ' + startId + '... ');
        $.post(
            '/admin/users/wipe',
            {
                startId: startId,
            },
            function (data) {
                if (data != 'complete') {
                    $('#OutputDiv').append('success<br />');
                    var lastId = parseInt(data);
                    if (isNaN(lastId)) {
                        $('#OutputDiv').append('Error.');
                        return;
                    }
                    Wipe(lastId);
                } else {
                    $('#OutputDiv').append('COMPLETE!<br />');
                }
            }
        );

    }

    $(document).ready(function() {
        Wipe(36);
    });
</script>
