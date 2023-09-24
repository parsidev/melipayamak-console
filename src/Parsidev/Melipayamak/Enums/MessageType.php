<?php

namespace Parsidev\Melipayamak\Enums;

enum MessageType: string
{
    case All = "all";
    case Inbox = "in";
    case Outbox = "out";

}
