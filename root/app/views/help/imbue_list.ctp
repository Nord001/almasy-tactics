<style type = 'text/css'>
    .DataTable {
        border-width: 1px;
        border-spacing: 2px;
        border-collapse: separate;
        padding: 5px;
    }

    .DataTable th {
        text-align: center;
        padding: 3px;
    }

    .DataTable td {
        text-align: center;
        padding: 3px;
    }

    .imbuename {
        color: rgb(142, 126, 66)
    }

    .effectname {
        color: rgb(178, 130, 74)
    }

    .alternaterow {
        background-color: rgb(220, 231, 237)
    }

</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Imbue List
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>List of Imbues</div>

        <div class = 'HelpIntro'>
            When you imbue an item, the item receives bonuses depending on which imbue you have selected (listed below as "Main Effects).  Many of the bonuses have a range, so imbuing the same item several times to get exceptional stats is a good idea. Imbued items also get 2 to 4 random effects that are not tied to the imbue.
        </div>

        <div class = 'StatHeader'>
            Main Effects - Weapons
        </div>
            These effects will be present whenever you choose the corresponding imbue.
        <div class = 'StatContent'>
            <table class = 'DataTable'>
                <tr>
                    <th>Name</th>
                    <th>Effect</th>
                </tr>
                <tr>
                    <th class= 'imbuename' )>Brawn</th>
                    <td>+15% to +30% HP<br/>
                        +20 to +30 Critical<br/>
                        -20 Critical for 3 to 5 rounds<br/>
                        +3% to +7% Regen for 5 to 15 rounds
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th class= 'imbuename' )>Desperation</th>
                    <td>+40% to +60% Damage<br/>
                        +10 to +20 Critical<br/>
                        -15% to -6% Regen<br/>
                        Spirit of Earth
                    </td>
                </tr>


                <tr>
                    <th class= 'imbuename' )>Fury</th>
                    <td>-30% to -20% Damage<br/>
                        +1 to +3 Strikes/Round<br/>
                        +25 to +40 Critical for 2 to 6 Rounds<br/>
                        -30% to -10% VIT<br/>
                        Spirit of Fire
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'imbuename' >Glory</th>
                    <td>+15% to +30% Damage<br/>
                        +5 to +10 Physical Reduction for 5 to 10 rounds<br/>
                        +5 to +10 Magical Reduction for 5 to 10 rounds<br/>
                        +10% to +20% HP<br/>
                        -5 to -3 Speed<br/>
                        Spirit of Steel
                    </td>
                </tr>


                <tr>
                    <th  class= 'imbuename' >Guilt</th>
                    <td>-80% to -70% Damage for 5 to 8 rounds<br/>
                        +50% to 90% Damage<br/>
                        -5% to -3% Regen<br/>
                        +10% to +30% HP
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'imbuename' >Liberation</th>
                    <td>+2 to +6 Speed for 5 to 10 rounds<br/>
                        +1 to +1 Strikes/Round<br/>
                        +15 to +20 Critical<br/>
                        +5 to +13 Dodge<br/>
                        Spirit of Water
                    </td>
                </tr>

                <tr>
                    <th class= 'imbuename' )>Meditation</th>
                    <td>+40% to +70% Damage<br/>
                        -4 to -2 Strikes/Round for 5 to 6 rounds<br/>
                        +20% to +30% HP<br/>
                        +2 to 5 Dodge
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th class= 'imbuename' )>Sloth</th>
                    <td>+90% to +140% Damage for 1 to 4 rounds<br/>
                        -50% to -40% Damage<br/>
                        -3.6 to -2.1 VIT per Level<br/>
                        -2 to +2 LUK per Level
                    </td>
                </tr>

                <tr>
                    <th  class= 'imbuename' >Temperance</th>
                    <td>+20% to +40% Damage<br/>
                        -1 Strike<br/>
                        +7 to +15 Critical<br/>
                        -8 to -4 Speed<br/>
                        +4% to +9% Magical Reduction
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'imbuename' >Valor</th>
                    <td>-1.9 to +3.1 VIT per Level<br/>
                        +20% to +40% STR<br/>
                        -8% to +20% Physical Reduction<br/>
                        +1% to +8% INT for 1 to 3 rounds
                    </td>
                </tr>

                <tr>
                    <th class= 'imbuename' )>Wonder</th>
                    <td>+10% to +30% Damage<br/>
                        -10% to +30% INT<br/>
                        -1.1 to +2.3 INT per Level<br/>
                        -10 to +15 Critical
                    </td>
                </tr>
            </table>
        </div>


        <div class = 'StatHeader'>
            Main Effects - Armor
        </div>
            These effects will be present whenever you choose the corresponding imbue.
        <div class = 'StatContent'>
            <table class = 'DataTable'>
                <tr>
                    <th>Name</th>
                    <th>Effect</th>
                </tr>


                <tr>
                    <th class= 'imbuename' )>Benevolence</th>
                    <td>+0.2 to +2.3 STR per Level<br/>
                        +0.2 to +2.3 VIT per Level<br/>
                        +0.2 to +2.3 INT per Level<br/>
                        +0.2 to +2.3 LUK per Level<br/>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'imbuename' >Blaze</th>
                    <td>+2 to +4 Speed for 5 to 10 rounds<br/>
                        +70% to +90% HP<br/>
                        -4 to -1.5 LUK per Level<br/>
                        -30% to -13% Regen<br/>
                        +10% to +20% Damage<br/>
                        Spirit of Fire
                    </td>
                </tr>

                <tr>
                    <th  class= 'imbuename' >Brilliance</th>
                    <td>-5 to -3 Speed<br/>
                        +10 to +20 Physical Reduction <br/>
                        +10 to +20 Magical Reduction <br/>
                        -2.3 to -1.5 LUK per level<br/>
                        Spirit of Steel
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'imbuename' >Fortitude</th>
                    <td>+1.1 to +3.5 STR per Level<br/>
                        +1.1 to +3.5 INT per Level<br/>
                        -20 to -10 Critical<br/>
                        +10% to +20% HP<br/>
                        Spirit of Earth
                    </td>
                </tr>

                <tr>
                    <th class= 'imbuename' )>Growth</th>
                    <td>-2.0 to -0.5 VIT per Level for 3 to 5 rounds<br/>
                        +7% to +13% Regen for 10 to 15 rounds<br/>
                        -5% to -3% Regen for 4 to 7 rounds<br/>
                        -5% to -3 Regen for 3 to 5 rounds<br/>
                        Spirit of Wood
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th class= 'imbuename' )>Infatuation</th>
                    <td>+35% to +45% Regen for 1 to 3 rounds<br/>
                        -20% to -10% Regen for 12 to 20 rounds<br/>
                        +20% to +40% Magical Defense
                    </td>
                </tr>

                <tr>
                    <th  class= 'imbuename' >Passion</th>
                    <td>-20% to -10% VIT<br/>
                        +10% to +15% INT<br/>
                        +0.9 to +3.8 VIT per Level<br/>
                        +30% to +80% Physical Defense for 6 to 12 rounds<br/>
                        +30% to +80% Magical Defense for 6 to 12 rounds
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th class= 'imbuename' )>Perseverance</th>
                    <td>+120% to +140% Physical Defense for 5 to 7 rounds<br/>
                        -140% to -120% Magical Defense for 5 to 7 rounds<br/>
                        +80% to +120% Magical Defense
                    </td>
                </tr>

                <tr>
                    <th  class= 'imbuename' >Pliancy</th>
                    <td>+3 to +7 Speed<br/>
                        +30% to +60% Physical Defense for 5 to 9 rounds<br/>
                        +30% to +60% Magical Defense for 5 to 9 rounds<br/>
                        +5 to +13 Dodge<br/>
                        Spirit of Water
                    </td>
                </tr>


            </table>
        </div>

        <div class = 'StatHeader'>
            Random Effects
        </div>
            Whenever you choose to imbue an item, it will also have 2 to 4 random effects along with the main effects.  The "Weight" column provides shows how common each effect is; the higher the weight, the more likely the effect will be present.
        <div class = 'StatContent'>
            <table class = 'DataTable'>
                <tr>
                    <th width=150>Effect</th>
                    <th>Minimum (Weapon)</th>
                    <th>Maximum (Weapon)</th>
                    <th>Minimum (Armor)</th>
                    <th>Maximum (Armor)</th>
                    <th>Weight</th>
                </tr>
                <tr>
                    <th  class= 'effectname' >% STR</th>
                    <td>2</td>
                    <td>15</td>
                    <td>-5</td>
                    <td>15</td>
                    <td>2</td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >% VIT</th>
                    <td>2</td>
                    <td>15</td>
                    <td>-5</td>
                    <td>15</td>
                    <td>2</td>
                    </td>
                </tr>

                <tr>
                    <th  class= 'effectname' >% INT</th>
                    <td>2</td>
                    <td>15</td>
                    <td>-5</td>
                    <td>15</td>
                    <td>2</td>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >% LUK</th>
                    <td>2</td>
                    <td>15</td>
                    <td>-5</td>
                    <td>15</td>
                    <td>2</td>
                    </td>
                </tr>

                <tr>
                    <th  class= 'effectname' >% Regen</th>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>1</td>
                    <td>2</td>
                    <td>.3</td>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >Phys. Reduct.</th>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>2</td>
                    <td>7</td>
                    <td>1</td>
                    </td>
                </tr>

                <tr>
                    <th class= 'effectname' >Phys. Defense</th>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>5</td>
                    <td>25</td>
                    <td>1</td>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >Mag. Reduct.</th>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>2</td>
                    <td>7</td>
                    <td>1</td>
                    </td>
                </tr>

                <tr>
                    <th class= 'effectname' >Mag. Defense</th>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>5</td>
                    <td>25</td>
                    <td>1</td>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >Critical</th>
                    <td>5</td>
                    <td>10</td>
                    <td>2</td>
                    <td>8</td>
                    <td>1</td>
                    </td>
                </tr>

                <tr>
                    <th class= 'effectname' >Speed</th>
                    <td>-1</td>
                    <td>4</td>
                    <td>-3</td>
                    <td>4</td>
                    <td>.3</td>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >STR per Level</th>
                    <td>0.2</td>
                    <td>1.1</td>
                    <td>0.2</td>
                    <td>1.1</td>
                    <td>1</td>
                    </td>
                </tr>

                <tr>
                    <th  class= 'effectname' >VIT per Level</th>
                    <td>0.2</td>
                    <td>1.1</td>
                    <td>0.2</td>
                    <td>1.1</td>
                    <td>1</td>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >INT per Level</th>
                    <td>0.2</td>
                    <td>1.1</td>
                    <td>0.2</td>
                    <td>1.1</td>
                    <td>1</td>
                    </td>
                </tr>

                <tr>
                    <th  class= 'effectname' >LUK per Level</th>
                    <td>0.2</td>
                    <td>1.1</td>
                    <td>0.2</td>
                    <td>1.1</td>
                    <td>1</td>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >% Damage on Earth</th>
                    <td>5</td>
                    <td>10</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>.5</td>
                    </td>
                </tr>

                <tr>
                    <th  class= 'effectname' >% Damage on Wood</th>
                    <td>5</td>
                    <td>10</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>.5</td>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >% Damage on Steel</th>
                    <td>5</td>
                    <td>10</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>.5</td>
                    </td>
                </tr>

                <tr>
                    <th  class= 'effectname' >% Damage on Fire</th>
                    <td>5</td>
                    <td>10</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>.5</td>
                    </td>
                </tr>
                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >% Damage on Water</th>
                    <td>5</td>
                    <td>10</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>.5</td>
                    </td>
                </tr>

                <tr>
                    <th  class= 'effectname' >Dodge</th>
                    <td>2</td>
                    <td>5</td>
                    <td>2</td>
                    <td>5</td>
                    <td>1</td>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >% HP</th>
                    <td>-5</td>
                    <td>10</td>
                    <td>5</td>
                    <td>15</td>
                    <td>1</td>
                    </td>
                </tr>

                <tr>
                    <th  class= 'effectname' >% Earth Resist</th>
                    <td>-10</td>
                    <td>10</td>
                    <td>5</td>
                    <td>10</td>
                    <td>.5</td>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >% Wood Resist</th>
                    <td>-10</td>
                    <td>10</td>
                    <td>5</td>
                    <td>10</td>
                    <td>.5</td>
                    </td>
                </tr>

                <tr>
                    <th  class= 'effectname' >% Steel Resist</th>
                    <td>-10</td>
                    <td>10</td>
                    <td>5</td>
                    <td>10</td>
                    <td>.5</td>
                    </td>
                </tr>

                <tr class = 'alternaterow'>
                    <th  class= 'effectname' >% Fire Resist</th>
                    <td>-10</td>
                    <td>10</td>
                    <td>5</td>
                    <td>10</td>
                    <td>.5</td>
                    </td>
                </tr>

                <tr>
                    <th  class= 'effectname' >% Water Resist</th>
                    <td>-10</td>
                    <td>10</td>
                    <td>5</td>
                    <td>10</td>
                    <td>.5</td>
                    </td>
                </tr>

            </table>
        </div>


        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>
