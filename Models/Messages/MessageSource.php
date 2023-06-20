<?php

namespace Zed\Models\Messages;

enum MessageSource {
    /**
     * Identifies a system message, like an automated notification.
     */
    case System;

    /**
     * Identifies a message sent by a being.
     */
    case Perso;
}
