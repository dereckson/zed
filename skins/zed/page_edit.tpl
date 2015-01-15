    <div class="content_wrapper">
        <h1>Page editor</h1>

        <!-- Page editor form -->
        <form method="post" action="{get_url('page', $page['page_code'])}">
            <p><input id="title" type="text" size="80" maxsize="255" name="title" value="{$page['page_title']}" /> ◄ <label for="title">Page title</label>
            <br />
            <input id="edit_reason" type=text size="80" maxsize="255" name="edit_reason" /> ◄ <label for="edit_reason">Edit summary</label></p>
            <textarea id="PageEditorContent" name="content" style="width: 100%" rows=20>{$page['page_content']}</textarea>
            <br />
            <input type=hidden name="code" value='{$page['page_code']}' />
            <input type=submit value='Enregistrer' />

        </form>
    </div>

    <!-- Loads FCKeditor -->
    <script>
        var oFCKeditor = new FCKeditor('content');
        oFCKeditor.BasePath = '/js/FCKeditor/';
        oFCKeditor.Config['SkinPath'] = oFCKeditor.BasePath + 'editor/skins/silver/';
        oFCKeditor.Config['BaseHref'] = 'http://zed.dereckson.be/page/';
        oFCKeditor.Height	= 480;
        oFCKeditor.ReplaceTextarea();
    </script>
