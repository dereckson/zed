    <script type="text/javascript">
        //Dijit
        dojo.require("dijit.form.Form");
        dojo.require("dijit.form.ValidationTextBox");
    </script>
    <h1>Communicator</h1>
    <h2>Send a request to the hypership</h2>
    <form dojoType="dijit.form.Form" name="aid.reach" method="post">
        <div class="row">
            <label class="firstLabel" for="PostTitle">{#Title#}</label>
            <input dojoType="dijit.form.ValidationTextBox" value="{$request->title}" type="text" id="PostTitle" name="title" class="long" required="true" />
        </div>
        <p>Your request will be sent to humans.</p>
    </form>