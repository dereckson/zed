    <!-- DIJIT -->
    <script type="text/javascript">
        dojo.require("dijit.form.Form");
        dojo.require("dijit.form.ValidationTextBox");
        dojo.require("dijit.form.Button");
    </script>
    
    <!-- Request form: aid.reach -->
    <h1>{#Communicator#}</h1>
    <h2>{#SendRequestToHyperShip#}</h2>
    <form dojoType="dijit.form.Form" name="aid.reach" method="post">
        <div class="row">
            <label class="firstLabel" for="PostTitle">{#Title#}</label>
            <input dojoType="dijit.form.ValidationTextBox" value="{$request->title}" type="text" id="PostTitle" name="title" class="long" required="true" />
        </div>
        <div class="row">
            <label class="firstLabel" for="PostMessage">{#Message#}</label>
            <textarea id="PostMessage" name="message" cols="80" rows="8"></textarea>
        </div>
        <div class="row">
            <button dojoType="dijit.form.Button" iconClass="dijitEditorIcon dijitEditorIconSave" type="submit" value="Save" />{#Send#}</button>
        </div>
        <p><strong>{#Warning#}</strong> {#RequestHandledByHumans#}</p>
    </form>