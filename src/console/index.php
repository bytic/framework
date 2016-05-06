<script type="text/javascript">
    PHPConsole         = {};
    PHPConsole.Plugin  = {};
    PHPConsole.Utility = {};

    PHPConsole.Browser = {
		IE : !!(window.attachEvent && navigator.userAgent.indexOf('Opera') === -1),
		Opera : navigator.userAgent.indexOf('Opera') > -1,
		WebKit : navigator.userAgent.indexOf('AppleWebKit/') > -1,
		Gecko : navigator.userAgent.indexOf('Gecko') > -1 && navigator.userAgent.indexOf('KHTML') === -1
    };

	PHPConsole.Utility.getViewportWidth = function() {
        return PHPConsole.browser.IE ?
            // IE Cases
            // Test for IE 5-7 Quirks and IE 4
            (!(document.documentElement.clientWidth)
            || (document.documentElement.clientWidth === 0)) ?
            // IE 5-7 Quirks and IE 4 case
            document.body.clientWidth :
            //IE 6+ Strict Case
            document.documentElement.clientWidth:
            // Gecko and Other DOM compliant case
            window.innerWidth;
    };

    PHPConsole.Utility.getViewportHeight = function() {
        return PHPConsole.Browser.IE ?
            // IE Cases
            // Test for IE 5-7 Quirks and IE 4
            (!(document.documentElement.clientHeight)
            || (document.documentElement.clientHeight === 0)) ?
            // IE 5-7 Quirks and IE 4 case
            document.body.clientHeight :
            //IE 6+ Strict Case
            document.documentElement.clientHeight:
            // Gecko and Other DOM compliant case
            window.innerHeight;
    };

    PHPConsole.Utility.getElementsByClassName = function(clsName) {
        var retVal = new Array();
        var elements = document.getElementsByTagName("*");
        for (var i = 0;i < elements.length;i++) {
            if (elements[i].className.indexOf(" ") >= 0) {
                var classes = elements[i].className.split(" ");
                for(var j = 0;j < classes.length;j++) {
                    if (classes[j] == clsName) {
                        retVal.push(elements[i]);
                    }
                }
            } else if (elements[i].className == clsName) {
                retVal.push(elements[i]);
            }
        }
        return retVal;
    };

    PHPConsole.Utility.hasClassName = function (node, className) {
        className = className.toUpperCase();
        if (node.className) {
            var classNames = node.className.split(' ');
            for (i = 0; i < classNames.length; i++) {
                if (classNames[i].toUpperCase() == className) {
                    return true;
                }
            }
        }

        return false;
    };

    PHPConsole.Utility.showPlugin = function (id) {
        var labels  = PHPConsole.Utility.getElementsByClassName('console-plugin-label');
        var plugins = PHPConsole.Utility.getElementsByClassName('console-plugin-container');

        for (i = 0; i < labels.length; i++) {
            labels[i].className      = 'console-plugin-label';
            plugins[i].style.display = 'none';
        }

        var parent = document.getElementById(id + '-label');

        parent.className = parent.className + ' console-plugin-label-selected';
        document.getElementById(id).style.display = 'block';
        
        if (document.getElementById('console-plugin-container').style.height == '0px') {
            PHPConsole.Utility.resize('normal');
        }

        PHPConsole.Utility.setCookie('console-plugin', id, 1, "/");
    };

    PHPConsole.Utility.setCookie = function (name, value, expires, path, domain, secure) {
        var today = new Date();
        today.setTime( today.getTime() );

        if (expires) {
            expires = expires * 1000 * 60 * 60 * 24;
        }
        var expires_date = new Date(today.getTime() + (expires));

        document.cookie = name + "=" +escape(value) +
        (expires ? ";expires=" + expires_date.toGMTString() : "") +
        (path ?    ";path=" + path : "") +
        (domain ?  ";domain=" + domain : "") +
        (secure ?  ";secure" : "");
    };

    PHPConsole.Utility.resize = function (size) {
        var container = document.getElementById('console-plugin-container');
        switch (size) {
            case 'small':
                container.style.height = '0';
                container.style.marginTop = '-1px';
                break;
            case 'normal':
                container.style.height = '250px';
                container.style.marginTop = '0';
                break;
            case 'full':
                container.style.height = PHPConsole.Utility.getViewportHeight() - 50 + 'px';
                container.style.marginTop = '0';
                break;
        }

        PHPConsole.Utility.setCookie('console-size', size, 1, "/");
    };
</script>
<style type="text/css">
    <?php include('style.css'); ?>
</style>

<div id="console-container">
    <div id="console-heading">
        <div id="console-size">
            <a href="javascript:" title="Maximize" onclick="PHPConsole.Utility.resize('full');">[ ]</a>
            <a href="javascript:" title="Normal" onclick="PHPConsole.Utility.resize('normal');">+</a>
            <a href="javascript:" title="Minimize" onclick="PHPConsole.Utility.resize('small');">_</a>
        </div>
        <ul id="console-labels">
        <?php
            $i = 0;
            foreach ($plugins as $plugin) {
                $selected = false;
                if ($activePlugin) {
                    if ($activePlugin == 'console-plugin-' . $i) {
                        $selected = true;
                    }
                } else {
                    if ($plugin == reset($plugins)) {
                        $selected = true;
                    }
                }

                $plugin->selected = $selected;
        ?>
            <li class="console-plugin-label<?php echo $selected ? ' console-plugin-label-selected' : ''; ?>" id="console-plugin-<?php echo $i; ?>-label">
                <a href="javascript:" onclick="PHPConsole.Utility.showPlugin('console-plugin-<?php echo $i; ?>')" title="<?php echo $label; ?>" rel="console-plugin-<?php echo $i; ?>">
                    <?php echo $plugin->getLabel(); ?>
                </a>
            </li>
        <?php
                $i++;
            }
        ?>
        </ul>
    </div>
    <?php
        $size = $_COOKIE['console-size'];
        switch ($size) {
            case 'small':
                $style = "height: 0; margin-top: -1px;";
                break;
            case 'normal':
            default:
                $style = 'height: 250px; margin-top: 0;';
                break;
        }
    ?>
    <div id="console-plugin-container" <?php echo $style ? ' style="'.$style.'"' : ''; ?>>
        <script type="text/javascript">
            PHPConsole.Utility.resize('<?php echo $size; ?>');
        </script>
        <?php
            $i = 0;
            foreach ($plugins as $label => $plugin) {
        ?>
        <div class="console-plugin-container"<?php echo !$plugin->selected ? ' style="display: none;"' : ''; ?> id="console-plugin-<?php echo $i; ?>">
            <?php echo $plugin->output(); ?>
        </div>
        <?php
                $i++;
            }
        ?>
    </div>
</div>