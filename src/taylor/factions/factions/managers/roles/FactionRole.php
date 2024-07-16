<?php namespace taylor\factions\factions\managers\roles;

use taylor\factions\factions\managers\FactionRolesManager;

class FactionRole {

    private FactionRolesManager $parent;

    private string $name;

    private array $permissions;

    public function __construct(FactionRolesManager $parent, string $name, array $permissions) {
        $this->parent = $parent;
        $this->name = $name;
        $this->permissions = $permissions;
    }

    public function getParent() : FactionRolesManager {
        return $this->parent;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getPermissions() : array {
        return $this->permissions;
    }

    public function hasPermission(int $permission) : bool {
        return in_array($permission, $this->permissions);
    }

    public function setPermissions(array $new) : void {
        $this->permissions = $new;
    }

    public function pack() : string {
        return json_encode([
            "name" => $this->name,
            "permissions" => $this->permissions
        ]);
    }

}