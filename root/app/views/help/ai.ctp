<style type = 'text/css'>
    .Script {
        background-color: rgb(240, 255, 240);
        white-space: pre;
        font-family: monospace;
        padding: 5px;
        border: 1px dashed;
        width: 500px;
        overflow: auto;
        padding-left: 10px;
        margin-top: 10px;
        text-align: left;
    }

    .StatContent div {
        text-align: justify;
    }

    .PropertyTable tr td {
        border-bottom: 1px dotted;
        padding: 3px;
        padding-left: 5px;
        padding-right: 5px;
    }

    .PropertyTable .PropertyTd {
        border-right: 1px dotted;
        font-weight: bold;
    }

</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | AI
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Using AI for Strategy</div>
        <div class = 'HelpIntro'>
             Almasy lets you customize AI for each of your characters! This allows you to design strategies for your teams to target certain characters first, rather than relying on the default AI to choose a target for you! Now you can have characters that target people based on their <b>threat</b> to your team, or target <b>easier to kill</b> characters, or target <b>certain classes</b> that you know to be troublesome!  Tinkering with your AI is not necessary, but can really help your team make the right decisions in battle to win!
        </div>

        <div class = 'StatHeader'>
            Getting Started
        </div>
        <div class = 'StatContent'>
             <ol>
                <li>Go to the Formations page and click on Strategy under a formation.</li>
                <li>You'll see a list of AI scripts on the left. You can edit or delete existing ones and create new scripts. These scripts are shared among all of your formations.</li>
                <li>Once you've created a script, you can assign it on the right to your characters by using the dropdown for each character to select the AI they will use. If you select none, they will use the default AI.</li>
                <li>Click Save to save your configuration.</li>
                <li>You're good to go!</li>
             </ol>
        </div>

        <div class = 'StatHeader'>
            Writing an AI
        </div>
        <div class = 'StatContent'>
            AI scripts are written in a programming language called Lua, which is really simple and easy to use. You can think of it as writing mathematical equations, like "x = 5". To read more about Lua, try <a href = 'http://www.luxinia.de/index.php/Tutorials/Lua'>this link</a>. But keep in mind that you don't need to know everything about Lua to write a simple script. It's really not that hard!
        </div>

        <div class = 'StatHeader'>
            Your First Script
        </div>
        <div class = 'StatContent'>
            <div>
                Let's look at a simple script.
            </div>

            <div class = 'Script'>priority = 1</div>

            <div>
                What's happening here? There's this priority that's being given the value 1. The priority is a value that represents how much the AI wants to attack a particular character. Whenever it's a character's turn to attack, the system will run your AI on each potential target and choose the target with the <b>highest</b> priority. In this case, it gives every target the same priority, so it won't attack any particular character earlier than another. But your eventual goal is to set priorities for each target that let your AI make good decisions about attacking targets.
            </div>

            <div>
                Let's look at another script.
            </div>

            <div class = 'Script'>priority = target.level</div>

            <div>
                What's happening here? There is a new thing here: target.level. When you're writing your script, you get access to certain values which give you information about the character you're using and the potential target. In this case, target.level represents the level of the target. So you're assigning a priority to each target based on their level. The <b>higher</b> the level, the <b>higher</b> the priority. The lower the level, the lower the priority. So when the AI decides who to attack, it will always try to attack the target with the highest level, because you told the AI to prioritize higher-leveled characters.
            </div>

            <div>
                You get access to other information too, about the character whom the AI is controlling and the current state of battle. <b>target</b> contains a lot of information about the potential target. <b>battle</b> contains information about the current state of battle. <b>me</b> contains information about the character the AI is controlling. For example, <b>me.level</b> contains the level of the character using the AI. <b>target.str</b> contains the strength of the potential target. A complete list is below. Here are some of the important ones:
                <ul>
                    <li><b>target.estimatedDamage</b>: The amount of damage the target can deal in general. This is an estimate of the target's threat level and not an accurate indication of how much damage they actually will deal to any character, because it does not take into account defense or elemental resistances (or anything else) that the character might have.</li>
                    <li><b>me.estimatedDamageToTarget</b>: The amount of damage the AI thinks it can deal to the target with one strike. This takes into account elemental bonuses, defense, resists, etc, but does not take into account chance-based things like dodge and critical.</li>
                    <li><b>target.class</b>: The name of the class of the target. You can use this to target certain classes.</li>
                </ul>
            </div>
        </div>

        <div class = 'StatHeader'>
            Another Script
        </div>
        <div class = 'StatContent'>
            <div>
                Here's another script.
            </div>

            <div class = 'Script'>priority = me.estimatedDamageToTarget / target.hp</div>

            <div>
                This one's a little more complicated, but don't worry! This script takes your estimated damage to the target and the target's current HP and calculates a priority. This gives higher priority to classes that would take a higher portion of their HP as damage if they were attacked. For example, if you wcould deal 40 damage to a 60 hp character (Crysello) and 50 damage to a 120 hp character (Erik), it would decide to attack Crysello first because Crysello has a priority of 40 / 60 = 0.666 while Erik has a priority = 50 / 120 = 0.416. Even though you could hurt Erik more, you can deal a larger blow to Crysello. This is great for your strategy because it means that you will attack targets that will die faster rather than targets that take a long time to kill.
            </div>
        </div>

        <div class = 'StatHeader'>
            Yet Another Script
        </div>
        <div class = 'StatContent'>
            <div>
                Here's another script!
            </div>

            <div class = 'Script'>if target.class == "Cardinal" then
     priority = 100
else
     priority = 0
end</div>

            <div>
                This one introduces something new: the if statement. The if allows you to change what you want to do based on some test. In this case, we are telling the priority to be 100 if the target's class is Cardinal, and 0 otherwise. Note the <b>then</b>, <b>end</b>, <b>==</b> and the quotes around Cardinal - all are very important. Your script will not work the way you want unless you have these.
            </div>

            <div>
                This script is really useful for targeting certain classes! You know that certain classes like Cardinals give amazing buffs to their team, so it would be a good idea to attack them first. This allows you to do that! This AI will attack Cardinals first and everyone else in no particular order, because it assigns the priority 100 to Cardinals and the priority 0 to everyone else. If it can possibly attack a Cardinal, the AI will always go for it because 100 is higher priority than 0.
            </div>
        </div>

        <div class = 'StatHeader'>
            One More Script
        </div>
        <div class = 'StatContent'>
            <div>
                Let's say you want to add some more code to your Cardinal-attacking code. You want it to attack Cardinals first, but if there are no cardinals, then attack the target that you can deal the most damage to. Here's what you might try at first:
            </div>

            <div class = 'Script' style = 'background-color: rgb(255, 240, 240)'>if target.class == "Cardinal" then
     priority = 100
else
     priority = 0
end
priority = me.estimatedDamageToTarget
</div>

            <div>
                <b>This is wrong!</b> If you follow the flow of execution as the code runs, you'll see that the priority gets set to either 100 or 0, based on the if statement. But immediately after that, priority is <u>reset</u> to <b>me.estimatedDamageToTarget</b>, which makes it completely lose the value it had before. Therefore, your code to attack Cardinals will no longer work, and your AI will only attack in order of targets that will take the most damage. This is the fixed version:
            </div>

            <div class = 'Script'>if target.class == "Cardinal" then
     priority = 100000
else
     priority = me.estimatedDamageToTarget
end
</div>

            <div>
                This code works. It will set the priority to a really high value for any Cardinals, so that it will always target Cardinals first no matter what. But if there are no Cardinals, then it will prioritize in order of the estimated damage to the target, and will attack the target it can deal the most damage to. This is why that line goes into the else part of the code and not afterwards.
            </div>
        </div>

        <div class = 'StatHeader'>
            Good luck!
        </div>
        <div class = 'StatContent'>
            Good luck with AI! I'm excited to see what AIs people write! Hint: A good AI probably tries to kill characters that might be dangerous to your team - like classes that deal a lot of damage (Virtuoso), or classes that heal (Cardinals), or classes that have really good abilities (Mastersmith).
        </div>

        <div class = 'StatHeader'>
            Character Property List
        </div>
        <div class = 'StatContent'>
            <div>
                The following properties are available for both <b>me</b> and <b>target</b>. So you can use <b>me.class</b> and <b>target.class</b>.
            </div>

            <table class = 'PropertyTable'>
                <tr>
                    <td class = 'PropertyTd'>class</td>
                    <td>The name of the class of the character. You can test if a character is a particular class using this property. For example,

                        <div class = 'Script'>if target.class == "Cardinal" then
    priority = 100
else
    priority = 0
end</div>
                    </td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>column</td>
                    <td>The column of the character in the formation. This is a number between 0 and 3. 0 is the left column.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>row</td>
                    <td>The row of the character in the formation. This is a number between 0 and 2. 0 is the front row.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>level</td>
                    <td>The level of the character.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>speed</td>
                    <td>The speed of the character.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>minRange</td>
                    <td>The minimum range of the character.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>maxRange</td>
                    <td>The maximum range of the character.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>attackType</td>
                    <td>Whether or not the character uses physical attacks or magical attacks. This is either "Physical" or "Magical". Example: A script that attacks mages first.
                                <div class = 'Script'>if target.attackType == "Magical" then
    priority = 100
else
    priority = 0
end</div>
                    </td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>str</td>
                    <td>The STR of the character.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>vit</td>
                    <td>The VIT of the character.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>int</td>
                    <td>The INT of the character.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>hp</td>
                    <td>The HP of the character.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>maxHp</td>
                    <td>The maximum HP of the character.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>critical</td>
                    <td>The critical rate of the character, in %. If it has the value 40, then the character has a 40% critical rate.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>criticalShield</td>
                    <td>The critical shield of the character, in %.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>dodge</td>
                    <td>The dodge rate of the character, in %.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>hpRegen</td>
                    <td>The HP regen of the character, in %.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>strikes</td>
                    <td>The number of strikes the character has.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>physicalReduction</td>
                    <td>The physical reduction of the character, in %.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>physicalDefense</td>
                    <td>The physical defense of the character, in %.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>magicalReduction</td>
                    <td>The magical reduction of the character, in %.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>magicalDefense</td>
                    <td>The magical defense of the character, in %.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>fireHate<br/>
                    steelHate<br/>
                    woodHate<br/>
                    earthHate<br/>
                    waterHate</td>
                    <td>The amount of bonus % damage this character inflicts on targets of that particular element. For example, an item that gives +20% damage to Water would show up as a value of 20 for waterHate.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>fireResist<br/>
                    steelResist<br/>
                    woodResist<br/>
                    earthResist<br/>
                    waterResist</td>
                    <td>The defensive counterpart to hate. This is the amount of resistance a character has to a particular element.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>damageToAoe</td>
                    <td>The amount of damage that this character inflicts as area of effect damage, in %.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>attacksAll</td>
                    <td>Whether or not the character attacks all targets. This is 1 if it does and 0 if it doesn't.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>estimatedDamage</td>
                    <td>The estimated amount of damage this character is capable of inflicting in one round.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>affinity</td>
                    <td>The affinity of the target. Example: A script that attacks fire types first.
                                <div class = 'Script'>if target.affinity == "Fire" then
    priority = 100
else
    priority = 0
end</div></td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>attackingAffinity</td>
                    <td>The element that the character deals damage with.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>defendingAffinity</td>
                    <td>The element that the character defends with.</td>
                </tr>
            </table>
        </div>

      <div class = 'StatHeader'>
            Battle Property List
        </div>
        <div class = 'StatContent'>
            <div>
                The following properties are available under <b>battle</b>. So you can use <b>battle.roundNumber</b>.
            </div>

            <table class = 'PropertyTable'>
                <tr>
                    <td class = 'PropertyTd'>roundNumber</td>
                    <td>The round number of the battle. The first round is round 1.</td>
                </tr>
                <tr>
                    <td class = 'PropertyTd'>turnNumber</td>
                    <td>The turn number of the battle. The first turn in a round is turn 1.</td>
                </tr>
            </table>
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>