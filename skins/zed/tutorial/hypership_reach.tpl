{if $DOJO}

{/if}

    <!-- Floating panes -->
    <script type="text/javascript" src="{#StaticContentURL#}/js/dojo/dojox/layout/FloatingPane.js"></script>
    <link rel="stylesheet" type="text/css" href="{#StaticContentURL#}/js/dojo/dojox/layout/resources/FloatingPane.css" />
    <link rel="stylesheet" type="text/css" href="{#StaticContentURL#}/js/dojo/dojox/layout/resources/ResizeHandle.css" />

    <!-- Dock -->
    <style type="text/css">
            @import "{#StaticContentURL#}/js/dojo/dijit/themes/dijit.css";
    </style>


    <!-- Help to reach the hypership -->
    <div dojoType="dojox.layout.FloatingPane" title="Join the hypership" resizable="true" id="floaterHypershipReach" class="floatingPaneTutorial" duration="300">
{if $CurrentPerso->location_global[0] == "S"}
        <p>Congratulations! You found a ship.</p>
        <p>You're aboard the {$CurrentPerso->location->ship->name}.</p>
{if $controller == "ship"}
        <p>In the  future, you will able to explore the ship, but this is not yet implemented yet in this alpha preview.</p>
{if $note == ""}
        <p>Your notes is the only information generally available about ships. For reference, you should note something like "Took the {$CurrentPerso->location->ship->name} at {get_hypership_time()} from ... to the hypership."</p>
{/if}
{/if}
{else}
        <p>{sprintf(#WhereYouAre#, $CurrentPerso->where(), lang_get($CurrentPerso->location->body_kind))}</p>
        <p>{#WhereTheHypershipIs#}</p>
        <p>{#HowToJoinIt#}</p>
{/if}
    </div>
