<?php if (isset($data) && is_array($data)): ?>
    <?php foreach ($data as $k => $row): ?>
    <tr>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['age']; ?></td>
        <td>
            <div class="rowbar">
                <?php echo $row['seat']; ?>
                <span class="btn_resize">+</span>
            </div>
        </td>
    </tr>
    <?php endforeach ?>
<?php endif ?>
