<div style="padding: 10px;">
    <?php if ($table) { ?>
    <table cellspacing="1" cellpadding="0" class="console-grid">
        <thead>
            <tr>
                <?php foreach ($labels as $label) { ?>
                <td><?php echo $label; ?></td>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($table as $row) { ?>
            <tr>
                <?php foreach ($labels as $label) { ?>
                <td><?php echo $row[$label]; ?></td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } ?>    
</div>