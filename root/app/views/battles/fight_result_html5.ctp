<?
    define('CELL_HEIGHT', 140);
    define('CELL_WIDTH_SPACING', 135); // Distance from left edge of one cell to next
    define('CELL_HEIGHT_SPACING', 150);
?>

<style type = 'text/css'>

#AttackingFormation {
    position: absolute;
    width: 400px;
    top: 15px;
    left: 10px;
}

#DefendingFormation {
    position: absolute;
    top: 15px;
    right: 10px;
    width: 400px;
}

.FormationTable {
    width: 100%;
}

.CharacterCell {
    font-weight: bold;
}

.FormationCell, .HpBar {
    position: absolute;
    width: 120px;
    height: <?= CELL_HEIGHT; ?>px;
}

.FormationCell {
    border: 1px solid rgb(0, 0, 0);
    text-align: center;
    font-size: 80%;
}

.HpBar {
    width: 121px;
    height: <?= CELL_HEIGHT + 1; ?>px;
    border-top: 1px solid;
}

#MessageArea {
    margin-left: 10px;
    margin-top: 20px;
    background-color: rgb(0, 0, 0);
    color: rgb(255, 255, 255);
    width: 700px;
    height: 130px;
    font-family: 'tahoma';
    padding: 3px;
    padding-left: 10px;
    border: 1px dotted rgb(255, 255, 255);
    overflow: auto;
}

#MessageTitle {
    font-size: 150%;
}

#Messages {
}

#ButtonArea {
    position: absolute;
    top: 15px;
    left: 435px;
}

</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Battle', array('controller' => 'battles', 'action' => 'index')); ?> |
        Battle Result
    </div>

    <div class = 'PageContent'>

        <div id = 'ButtonArea'>
            <input type = 'button' value = '<<' id = 'PrevButton' />
            <input type = 'button' value = '>>' id = 'NextButton' />
        </div>

        <div id = 'MessageArea' class = 'rounded-corners'>
            <div id = 'MessageTitle'>
            </div>

            <div id = 'Messages'>
            </div>
        </div>
    </div>
</div>

<?
    // Preload class images
    $characters = array_merge($battleInfo['attacker'], $battleInfo['defender']);
    $icons = Set::classicExtract($characters, '{n}.icon');
    $icons = array_unique($icons);

    $icons[] = 'face';

    foreach ($icons as $icon)
        echo $html->image('/img/sprites/' . $icon . '.png', array('id' => $icon, 'style' => 'display: none'));
?>

<?= $javascript->link('battle/Three'); ?>
<?= $javascript->link('battle/Stats'); ?>
<?= $javascript->link('battle/Cube'); ?>
<?= $javascript->link('battle/Tween'); ?>

<script type = 'text/javascript'>
    <? file_get_contents('http://127.0.0.1/js/battle/battle.js'); ?>
    var messages = [<?= implode(',', $messages); ?>];
    var battleInfo = <?= $battleLog; ?>;

    var c_formationWidth = <?= FORMATION_WIDTH; ?>;
    var c_formationHeight = <?= FORMATION_HEIGHT; ?>;

/*
    var curMessage = 0;

    var characters = [];

    var fireballs = [];

    var attackerView = new Formation();
    var defenderView = new Formation();

    function GetCharacterFromFormations (id) {
        var character = attackerView.GetCharacterById(id);
        if (character == null)
            character = defenderView.GetCharacterById(id);
        return character;
    }

    function Update () {
        var message = messages[curMessage];

        $('#MessageTitle').html(message.title);

        $('#Messages').html('');
        var subMessages = message.messages;
        for (subMessageIndex in subMessages)
            $('#Messages').append(subMessages[subMessageIndex] + '<br />');

        if (message['attackingCharId'] != null) {
            var attackingCharId = message['attackingCharId'];
            var targetCharId = message['targetCharId']

            var attackingChar = GetCharacterFromFormations(attackingCharId);
            var targetChar = GetCharacterFromFormations(targetCharId);

            if (attackingChar != null)
                attackingChar.Attack(targetChar);
        }

        var hps = message.hps;
        for (characterId in hps) {
            var character = GetCharacterFromFormations(characterId);

            if (character == null)
                continue;

            var hpStr = hps[characterId];
            var hpSplit = hpStr.split('/');
            if (hpSplit.length == 2) {
                var hp = hpSplit[0];
                var maxHp = hpSplit[1];
                character.hp = hp;
                character.maxHp = maxHp;
            }
        }
    }
    */
    var camera = null;
    var renderer = null;
    var scene = null;

    var secondsPerFrame = 30;

    function Update () {
        //setTimeout(Update, secondsPerFrame);

    }

    var isBenchmarking = false;
    function Render () {
        setTimeout(Render, secondsPerFrame);

        if (isBenchmarking)
            console.time('render');

        /*
        ctx.clearRect(0, 0, 850, 600);

        attackerView.Display();
        defenderView.Display();

        for (var index in fireballs) {
            fireballs[index].Update(secondsPerFrame * 0.001);
            fireballs[index].Display();
        }*/
        TWEEN.update();

        renderer.render(scene, camera);
        stats.update();


        if (isBenchmarking)
            console.timeEnd('render');
    }

    function createSprite() {

        var canvas = document.createElement( 'canvas' );
        canvas.width = 16;
        canvas.height = 16;

        var context = canvas.getContext( '2d' );
        var gradient = context.createRadialGradient( canvas.width / 2, canvas.height / 2, 0, canvas.width / 2, canvas.height / 2, canvas.width / 2 );
        gradient.addColorStop( 0, 'rgba(255,255,255,1)' );
        gradient.addColorStop( 0.2, 'rgba(255,255,0,1)' );
        gradient.addColorStop( 0.4, 'rgba(50,0,0,1)' );
        gradient.addColorStop( 1, 'rgba(0,0,0,0)' );

        context.fillStyle = gradient;
        context.fillRect( 0, 0, canvas.width, canvas.height );

        return canvas;

    }
            function initParticle( particle, delay ) {

                var particle = this instanceof THREE.Particle ? this : particle;
                var delay = delay !== undefined ? delay : 0;

                particle.position.x = 0;
                particle.position.y = 0;
                particle.position.z = 0;
                particle.scale.x = particle.scale.y = 1;

                new TWEEN.Tween( particle )
                    .delay( delay )
                    .to( {}, 2000 )
                    .onComplete( initParticle )
                    .start();

                new TWEEN.Tween( particle.position )
                    .delay( delay )
                    .easing(TWEEN.Easing.Quadratic.EaseOut)
                    .to( { x: Math.random() * 60 - 20, y: Math.random() * 60 - 5, z: Math.random() * 80 - 20 }, 500 )
                    .start();

                new TWEEN.Tween( particle.scale )
                    .delay( delay )
                    .easing(TWEEN.Easing.Quadratic.EaseIn)
                    .to( { x: 0, y: 0 }, 500 )
                    .start();

            }

    var WINDOW_WIDTH = 850;
    var WINDOW_HEIGHT = 600;

    $(document).ready(function() {
        renderer = new THREE.CanvasRenderer();
        renderer.setSize(WINDOW_WIDTH, WINDOW_HEIGHT);

        container = $('.PageContent');
        container.prepend(renderer.domElement);

        scene = new THREE.Scene();
        var geo = new Cube(30, 30, 30);
        var cube = new THREE.Mesh(geo, new THREE.MeshBasicMaterial({ color: 0xaa0000}));
        //scene.addObject(cube);


        camera = new THREE.Camera( 70, WINDOW_WIDTH / WINDOW_HEIGHT, 0.0001, 10000 );
        camera.position.x = 30;
        camera.position.y = 40;
        camera.position.z = 60;

        var geometry = new THREE.Geometry();
        geometry.vertices.push( new THREE.Vertex( new THREE.Vector3( - 100, 0, 0 ) ) );
        geometry.vertices.push( new THREE.Vertex( new THREE.Vector3( 100, 0, 0 ) ) );


            var material = new THREE.ParticleBasicMaterial( { map: createSprite(), blending: THREE.AdditiveBlending } );

            for ( var i = 0; i < 10; i++ ) {

                particle = new THREE.Particle( material );

                initParticle( particle, i * 10 );

                scene.addObject( particle );
            }

                    for ( var i = 0; i <= 20; i ++ ) {

                        var line = new THREE.Line( geometry, new THREE.LineBasicMaterial( { color: 0x000000, opacity: 0.2 } ) );
                        line.position.z = ( i * 50 ) - 50;
                        scene.addObject( line );

                        var line = new THREE.Line( geometry, new THREE.LineBasicMaterial( { color: 0x000000, opacity: 0.2 } ) );
                        line.position.x = ( i * 50 ) - 50;
                        line.rotation.y = 90 * Math.PI / 180;
                        scene.addObject( line );

        }

        //var ambientLight = new THREE.AmbientLight( 0xffffff );
        //scene.addLight( ambientLight );

        stats = new Stats();
        stats.domElement.style.position = 'absolute';
        stats.domElement.style.top = '0px';
        container.append(stats.domElement);

        Render();
        Update();
    });
</script>