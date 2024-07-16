<?php namespace taylor\factions\groups;

class Group {

    private string $name;

    private string $fancyName;

    private int $priority;

    private array $permissions;

    public function __construct(string $name, string $fancyName, int $priority, array $permissions) {
        $this->name = $name;
        $this->fancyName = $fancyName;
        $this->priority = $priority;
        $this->permissions = $permissions;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getFancyName() : string {
        return $this->fancyName;
    }

    public function getPriority() : int {
        return $this->priority;
    }

    public function getPermissions() : array {
        return $this->permissions;
    }

}