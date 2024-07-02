<?php namespace taylor\factions\kits;

use pocketmine\utils\SingletonTrait;

class KitsManager {

    use SingletonTrait;

    public const VALID_GROUPS = ["regular", "gkit"];

    /** @var array<string, Kit> */
    private array $kits = [];

    /** @var array<string, string> */
    private array $kitToGroup = [];

    public function __construct() {
        self::setInstance($this);
    }

    public function getKit(string $name) : ?Kit {
        return $this->kits[$name] ?? null;
    }

}