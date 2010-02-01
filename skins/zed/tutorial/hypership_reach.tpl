    <!-- Floating panes -->
    <script type="text/javascript" src="{#StaticContentURL#}/js/dojo/dojox/layout/FloatingPane.js"></script>
    <link rel="stylesheet" type="text/css" href="{#StaticContentURL#}/js/dojo/dojox/layout/resources/FloatingPane.css" />
    <link rel="stylesheet" type="text/css" href="{#StaticContentURL#}/js/dojo/dojox/layout/resources/ResizeHandle.css" />

    <!-- Dock -->
    <style type="text/css">
            @import "{#StaticContentURL#}/js/dojo/dojo/resources/dojo.css";
            @import "{#StaticContentURL#}/js/dojo/dijit/themes/dijit.css";
            @import "{#StaticContentURL#}/js/dojo/dijit/themes/tundra/tundra.css";
    </style>

    <!-- Help to reach the hypership -->
    <div dojoType="dojox.layout.FloatingPane" title="Join the hypership" resizable="true" id="floaterHypershipReach" class="floatingPaneTutorial" duration="300">
        <p>{sprintf(#WhereYouAre#, $CurrentPerso->where(), lang_get($CurrentPerso->location->body_kind))}</p>
        <p>{#WhereTheHypershipIs#}</p>
        <p>{#HowToJoinIt#}</p>
    </div>