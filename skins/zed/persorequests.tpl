    <!-- Javascript bits for request handling -->
    <script type="text/javascript">
    
    //The amount of requests printed on the page.
    //When it falls to 0, request_reply_callback will print a temporary exit
    //message and then redirects to the homepage.
    var requestsQuantity = {count($requests)};
    
    //Performs an AJAX call
    //  id      the request DOM element id
    //  url     the URL to query
    //
    //The reply will be handled by request_reply_callback function.
    function request_reply (id, url) {
        dojo.xhrGet({
            handleAs:       "json",
            url:            url,
            preventCache:   true,
            handle:         function (response, ioArgs) {
                request_reply_callback(response, id);
            }
        });
    }
    
    //Prints a wap message
    //  message the error message to print
    function wap (message) {
        var html = '<div class="wap">' + message + '</div><div class="clear"></div>';
        document.getElementById('RequestsWap').innerHTML = html;
    }
    
    //Prints a notify message
    //  message the warning message to print
    function notify (message) {
        var html = '<div class="notify">' + message + '</div><div class="clear"></div>';
        document.getElementById('RequestsNotify').innerHTML = html;
    }
    
    //This function is called when there isn't requests anymore.
    //It prints a close message, clears the site.requests flag and redirects to
    //Zed homepage.
    function no_more_site_requests () {
        document.getElementById('RequestsBody').style.display = 'none';
        notify("{#CallbackDone#}");
        setTimeout('document.location = "{get_url()}";', 3000);
        dojo.xhrGet({
            url:            '{get_request_url(0, 'perso' , 'site.requests', 0)}',
            preventCache:   true
        });
    }
    
    //Handles the reply
    //  reply   ajax reply ; a boolean is expected, so true or false.
    //  id      the request DOM element id
    //
    //If the reply is true      hides request id.
    //If the reply is false     outputs a WAP error.
    function request_reply_callback (reply, id) {
        if (reply == true) {
            document.getElementById(id).style.display = 'none';
            requestsQuantity--;
            if (requestsQuantity == 0) {
                no_more_site_requests();
            }
        } else {
            wap("{#CallbackError#}");
        }
    }
    </script>

    <!-- Perso requests -->
    <div id="RequestsWap"></div>
    <div id="RequestsNotify"></div>
    <div id="RequestsBody">
        <h1>{#Requests#}</h1>
        <div class="grid_16 alpha omega">
{foreach from=$requests item=request}
{$i = {counter}}
            <div id="request{$i}" class="request message {cycle values="dark,light"}">
                <p>{$request->message}</p>
                <ul>
                    <li><a onclick="request_reply('request{$i}', '{get_request_allow_url($request)}'); return false;" href="{get_request_allow_url($request)}?redirectTo={get_url()}">{#Allow#}</li>
                    <li><a onclick="request_reply('request{$i}', '{get_request_deny_url($request)}'); return false;"  href="{get_request_deny_url($request)}?redirectTo={get_url()}">{#Deny#}</a></li>
                </ul>
            </div>
{/foreach}
        </div>
        <p><a href="{get_request_url(0, 'perso', 'site.requests', 0)}?redirectTo={get_url()}">{#IgnoreAll#}</a></p>
    </div>