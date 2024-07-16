<?php namespace taylor\factions\factions\managers;

use taylor\factions\factions\Faction;
use taylor\factions\factions\managers\roles\FactionRole;

class FactionRolesManager {

    private Faction $parent;

    /** @var array<string, FactionRole> */
    private array $roles;

    public function __construct(Faction $parent, array $roles) {
        $this->parent = $parent;
        $this->roles = $roles;
    }

    public function getParent() : Faction {
        return $this->parent;
    }

    public function setRoles(array $roles) : void {
        $this->roles = $roles;
    }

    public function getRole(string $name) : ?FactionRole {
        return $this->roles[$name] ?? null;
    }

}