<script type="text/javascript">
    PHPConsole.Plugin.Logger = {};
    PHPConsole.Plugin.Logger.showBacktrace = function (id) {
        var labels      = PHPConsole.Utility.getElementsByClassName('logger-event-data');
        var backtraces  = PHPConsole.Utility.getElementsByClassName('logger-event-backtrace');

        var parent    = document.getElementById(id + '-data');
        var backtrace = document.getElementById(id + '-backtrace');

        if (backtrace.style.display == 'none') {
            parent.className = parent.className + ' logger-event-data-selected';
            backtrace.style.display = 'block';
        } else {
            parent.className = 'logger-event-data';
            backtrace.style.display = 'none';
        }
    };
</script>
<style type="text/css">
    .logger-event {
        padding: 5px 10px !important;
        border-bottom: 1px solid #A1D3F8;
    }

    .logger-event-last {
        border-bottom-width: 0;
    }
</style>

<?php if ($events) { ?>
<ul id="console-plugin-logger">
    <?php $i = 0; ?>
    <?php foreach ($events as $event) { ?>
    <li class="logger-event logger-event-<?php echo $event->getType(); ?><?php echo $event == end($events) ? ' logger-event-last' : ''; ?>">
        <div class="logger-event-data" id="logger-event-<?php echo $i; ?>-data" onclick="PHPConsole.Plugin.Logger.showBacktrace('logger-event-<?php echo $i; ?>')">
            <?php
                $data = $event->getData();
                if (is_string($data)) {
                    echo $data;
                } else {
                    echo "<pre>";
                    print_r($data);
                    echo "</pre>";
                }
            ?>
        </div>
        <div class="logger-event-backtrace" id="logger-event-<?php echo $i; ?>-backtrace" style="display:none;">
            <table cellspacing="1" cellpadding="0" class="console-grid" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <td>File</td>
                        <td>Line</td>
                        <td>Function</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($event->getBacktrace() as $step) { ?>
                    <tr>
                        <td><?php echo $step['file']; ?></td>
                        <td><?php echo $step['line']; ?></td>
                        <td><?php echo $step['function']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </li>
        <?php $i++; ?>
    <?php } ?>
</ul>
<?php } ?>