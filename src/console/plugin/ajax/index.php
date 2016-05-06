<style type="text/css">
    #console-plugin-ajax-requests li {
        padding: 5px 10px;
        border-bottom: 1px solid #A1D3F8;
    }

    .console-plugin-ajax-response {
        margin-top: 10px !important;
        line-height: 16px;
        padding: 5px !important;
        background-color: #E5F0F8;
        border: 0;
        width: 100%;
        height: 100px;
    }
</style>
<div id="console-plugin-ajax">
    <ul id="console-plugin-ajax-requests"></ul>
    <script type="text/javascript">
        <?php include("XMLHttpRequest.js"); ?>
        <?php include("Encoder.js"); ?>
    </script>    
    <script type="text/javascript">
        PHPConsole.Plugin.Ajax = {};
        PHPConsole.Plugin.Ajax.readyStateChange = function (request) {
            var element = document.getElementById(request.getID());
            switch (request.readyState) {
                case 4:
                    element.className = '';
                    element.innerHTML = '<a href="javascript:">'+ element.innerHTML +'</a>';
                    element.innerHTML += '<textarea class="console-plugin-ajax-response" id='+element.id+'-response style="display:none;">'+Encoder.htmlEncode(request.responseText)+'</textarea>';
                    element.getElementsByTagName('a')[0].onclick = function () {
                        var response = document.getElementById(element.id + '-response');
                        response.style.display = response.style.display == 'none' ? 'block' : 'none';
                    };
                    break;
            }
        };

        PHPConsole.Plugin.Ajax.open = function (request, method, url) {
            var container   = document.getElementById('console-plugin-ajax-requests');
            var item        = document.createElement('li');
            item.id         = request.getID();
            item.className  = 'console-plugin-ajax-loading';
            item.innerHTML  = method + ' | ' + url;

            container.appendChild(item);
        };

        PHPConsole.Plugin.Ajax.send = function (request, data) {
            document.getElementById(request.getID()).innerHTML += ' | ' + Encoder.htmlEncode(decodeURIComponent(data));
        };

        XMLHttpRequest.prototype.getID = function() {
            if (!this.id) {
                this.id = 'request-' + Math.floor(Math.random( ) * 1000);
            }
            return this.id;
        };

        (function(open) {
            XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
                PHPConsole.Plugin.Ajax.open(this, method, url);

                this.addEventListener("readystatechange", function() {
                    PHPConsole.Plugin.Ajax.readyStateChange(this);
                }, false);

                open.call(this, method, url, async, user, pass);
            };
        })(XMLHttpRequest.prototype.open);

        (function(send) {
            XMLHttpRequest.prototype.send = function(data) {
                PHPConsole.Plugin.Ajax.send(this, data);
                
                send.call(this, data);
            };
        })(XMLHttpRequest.prototype.send);
    </script>
</div>