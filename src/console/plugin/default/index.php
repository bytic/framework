<style type="text/css">
    .console-plugin-default-item {
        padding: 5px 10px !important;
        border-bottom: 1px solid #A1D3F8;
    }

    .console-plugin-default-item-last {        
        border-bottom-width: 0;
    }
</style>

<?php if ($data) { ?>
<ul id="console-plugin-default-items">
    <?php foreach ($data as $item) { ?>
    <li class="console-plugin-default-item<?php echo $item == end($data) ? ' console-plugin-default-item-last' : ''; ?>">
        <pre><?php print_r(is_bool($item) ? ($item ? 'True' : 'False') : $item); ?></pre>
    </li>
    <?php } ?>
</ul>
<?php } ?>