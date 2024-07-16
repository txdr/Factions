<?php namespace taylor\factions\factions\managers\roles;

interface RolePermissions {

    public const PERMISSION_TO_NAME = [
        "Build",
        "Place",
        "Interact",
        "Promote",
        "Demote",
        "Invite",
        "Kick",
        "Admin",
        "Use Faction Home"
    ];

    public const BUILD = 0;

    public const PLACE = 1;

    public const INTERACT = 2;

    public const PROMOTE = 3;

    public const DEMOTE = 4;

    public const INVITE = 5;

    public const KICK = 6;

    public const ADMIN = 7;

    public const WARP_FACTION_HOME = 8;

}