<?= $form->create('GrowthCalc', array('id' => 'GrowthCalcForm', 'url' => '/admin/c_classes/growth'));?>
<!--form method = "post" id = 'GrowthCalcForm' action = "/admin/c_classes/growth"-->
    <fieldset>
        <legend>Growth Calculator</legend>
    <?php
        echo $form->input('base_growth_str', array('label' => 'Base Growth (STR)'));
        echo $form->input('base_growth_vit', array('label' => 'Base Growth (VIT)'));
        echo $form->input('base_growth_int', array('label' => 'Base Growth (INT)'));
        echo $form->input('base_growth_luk', array('label' => 'Base Growth (LUK)'));

        echo $form->input('first_class_id', array(
            'options' => $promotions[1],
        ));
        echo $form->input('second_class_id', array(
            'type' => 'select',
            'style' => 'display: none',
        ));
        echo $form->input('third_class_id', array(
            'type' => 'select',
            'style' => 'display: none',
        ));
        echo $form->input('fourth_class_id', array(
            'type' => 'select',
            'style' => 'display: none',
        ));
        echo $form->input('fifth_class_id', array(
            'type' => 'select',
            'style' => 'display: none',
        ));
        echo $form->input('sixth_class_id', array(
            'type' => 'select',
            'style' => 'display: none',
        ));
        echo $form->input('seventh_class_id', array(
            'type' => 'select',
            'style' => 'display: none',
        ));
        echo $form->input('eighth_class_id', array(
            'type' => 'select',
            'style' => 'display: none',
        ));
    ?>
    </fieldset>

    <input type = 'hidden' name = 'data[GrowthCalc][form_selected_ids]' id = 'FormSelectVals' />

    <div class = "submit">
        <input type = "submit" value = "Submit" id = 'SubmitButton' />
    </div>
 </form>

<script type = 'text/javascript'>

    // Pass in selected ids to reset when the form's been submitted.
    var selectedIds = [<?= $this->data['GrowthCalc']['form_selected_ids']; ?>];
    //---------------------------------------------------------------------------------------------
    $(document).ready(function() {
        $('#SubmitButton').click(function() {
            $('#FormSelectVals').val(
                $('#GrowthCalcFirstClassId').val() + ',' +
                $('#GrowthCalcSecondClassId').val() + ',' +
                $('#GrowthCalcThirdClassId').val() + ',' +
                $('#GrowthCalcFourthClassId').val() + ',' +
                $('#GrowthCalcFifthClassId').val() + ',' +
                $('#GrowthCalcSixthClassId').val() + ',' +
                $('#GrowthCalcSeventhClassId').val() + ',' +
                $('#GrowthCalcEighthClassId').val()
            );
            $('#GrowthCalcForm').submit();
        })
    });

    var promotions = <?= json_encode($promotions); ?>;

    //---------------------------------------------------------------------------------------------
    function OperatePromoteSelect (classSelectId, promoteSelectId, index) {
        var classSelect = $(classSelectId);

        // Start off hidden
        var val = classSelect.val();
        var promoteSelect = $(promoteSelectId);
        promoteSelect.html('');
        promoteSelect.hide();

        // Add options based on class select
        var numPromotions = 0;
        for (promotionClass in promotions[val]) {
            numPromotions++;
            promoteSelect.append(
                $('<option></option>').val(promotionClass).html(promotions[val][promotionClass])
            );
        }

        // Only show if there are things to show
        if (numPromotions > 0) {
            promoteSelect.show();
            promoteSelect.val(selectedIds[index]);
        }

        // Trigger a change so it cascades down
        promoteSelect.trigger('change');
    }

    //---------------------------------------------------------------------------------------------
    function BindPromoteSelect (classSelectId, promoteSelectId, index) {
        OperatePromoteSelect(classSelectId, promoteSelectId, index);
        $(document).ready(function() {
            var classSelect = $(classSelectId);
            classSelect.change(function() {
                OperatePromoteSelect(classSelectId, promoteSelectId, index);
            });
        });
    }

    BindPromoteSelect('#GrowthCalcFirstClassId', '#GrowthCalcSecondClassId', 1);
    BindPromoteSelect('#GrowthCalcSecondClassId', '#GrowthCalcThirdClassId', 2);
    BindPromoteSelect('#GrowthCalcThirdClassId', '#GrowthCalcFourthClassId', 3);
    BindPromoteSelect('#GrowthCalcFourthClassId', '#GrowthCalcFifthClassId', 4);
    BindPromoteSelect('#GrowthCalcFifthClassId', '#GrowthCalcSixthClassId', 5);
    BindPromoteSelect('#GrowthCalcSixthClassId', '#GrowthCalcSeventhClassId', 6);
    BindPromoteSelect('#GrowthCalcSeventhClassId', '#GrowthCalcEighthClassId', 7);
</script>

<? if (isset($stats)): ?>
    <div>
        <h2>Results</h2>

        <div id = 'Graph' style = 'width: 700px; height: 350px'></div>

        <?= $javascript->link('jquery.flot.pack.js'); ?>

        <script type = 'text/javascript'>
            $.plot(
                $('#Graph'),
                <?=
                    // An array of four data series. array_keys($stats) are the levels.
                    json_encode(array(
                        array(
                            'label' => 'STR',
                            'data' => ArraysToKeyValuePair(array_keys($stats), Set::classicExtract($stats, '{n}.str')),
                        ),
                        array(
                            'label' => 'VIT',
                            'data' => ArraysToKeyValuePair(array_keys($stats), Set::classicExtract($stats, '{n}.vit')),
                        ),
                        array(
                            'label' => 'INT',
                            'data' => ArraysToKeyValuePair(array_keys($stats), Set::classicExtract($stats, '{n}.int')),
                        ),
                        array(
                            'label' => 'LUK',
                            'data' => ArraysToKeyValuePair(array_keys($stats), Set::classicExtract($stats, '{n}.luk'))
                        ),
                    ));
                ?>,
                {
                    legend: {
                        show: true,
                        noColumns: 1,
                    }
                }
            );
        </script>

        <table class = 'data'>
            <tr>
                <th>Level</th>
                <th>STR</th>
                <th>VIT</th>
                <th>INT</th>
                <th>LUK</th>
            </tr>
            <? $i = 0; ?>
            <? foreach ($stats as $level => $stat): ?>
                <? $class = $i % 2 == 0 ? 'altrow' : ''; ?>
                <? $style = $i % 20 == 0 ? 'font-weight: bold;' : ''; ?>
                <? $i++; ?>
                <tr class = '<?= $class; ?>' style = '<?= $style; ?>'>
                    <td><?= $level; ?></td>
                    <td><?= sprintf("%d (%+d)", $stat['str'], $stat['growth_str']); ?></td>
                    <td><?= sprintf("%d (%+d)", $stat['vit'], $stat['growth_vit']); ?></td>
                    <td><?= sprintf("%d (%+d)", $stat['int'], $stat['growth_int']); ?></td>
                    <td><?= sprintf("%d (%+d)", $stat['luk'], $stat['growth_luk']); ?></td>
                </tr>
            <? endforeach; ?>
        </table>
    </div>
<? endif; ?>