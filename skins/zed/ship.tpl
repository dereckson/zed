    <!-- Ship CSS -->
    <style>
    .content textarea {
        background-color: inherit;
        color: white;
        border: dashed #343434 2px;
        width: 100%;
        background-image: url("/img/zed/opaque_20.png");
    }
    </style>
       
    <!-- Ship content -->
    <div class="content_wrapper">
        <h1>{$ship->name}</h1>
        <div class="content">
            <p>Lorem ipsum dolor</p>
            <h2>{#PersonalNotes#}</h2>
            <form method="POST">
                <input type="hidden" name="action" value="ship.setnote">
                <textarea id="ShipNote" name="note" rows="8" onfocus="set_opacity(this.id, 20)" onBlur="set_opacity(this.id, 0)">{$note}</textarea>
                <br />
                <input type="submit" value="{#SaveNote#}" />
            </form>
        </div>
    </div>
    
    <script>
        //Sets the focus on note textarea
        document.getElementById('ShipNote').focus();
    </script>