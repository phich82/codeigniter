<?php if (isset($tableList) && is_array($tableList)): ?>
    <?php foreach ($tableList as $table): ?>
    <?php
        $rsv = $data[$table['id']] ?? [];
        $width = function ($startTime, $endTime) {
            $stSplit = explode(':', $startTime);
            $edSplit = explode(':', $endTime);

            return (((int)$edSplit[0]*60 + (int)$edSplit[1] - ((int)$stSplit[0]*60 - (int)$stSplit[1]))/30)*80;
        };
    ?>
    <tr data-id="<?php echo $table['id']; ?>">
        <td style="height: 50px; width: 80px; border: 1px solid;">
            <?php echo $table['name']; ?>
        </td>
        <?php foreach ($timeListHeader as $time): ?>
        <td style="height: 50px; width: 80px; border: 1px solid;">
            <?php if (isset($rsv[$table['id'].'_'.str_replace(':', '', $time)])): ?>
            <?php $r = $rsv[$table['id'].'_'.str_replace(':', '', $time)]; ?>
            <div class="rowbar" style="width:<?php echo $width($r['start_time'], $r['end_time']); ?>px">
                <?php //echo $row['seat']; ?>
                <span class="btn_resize">+</span>
            </div>
            <?php endif ?>
        </td>
        <?php endforeach ?>
    </tr>
    <?php endforeach ?>
<?php endif ?>
