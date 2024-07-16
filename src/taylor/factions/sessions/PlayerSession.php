<?php namespace taylor\factions\sessions;

use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use taylor\factions\factions\Faction;
use taylor\factions\factions\FactionsManager;
use taylor\factions\factions\managers\members\FactionMember;
use taylor\factions\groups\GroupsManager;
use taylor\factions\Main;
use taylor\factions\utils\LanguageManager;

class PlayerSession {

    public const BALANCE_COINS = 0;
    public const BALANCE_MOBCOINS = 1;
    public const BALANCE_EXPERIENCE = 2;
    public const BALANCE_TO_STRING = [
        self::BALANCE_COINS => "Coins",
        self::BALANCE_MOBCOINS => "Mob Coins",
        self::BALANCE_EXPERIENCE => "Experience"
    ];

    private Player $player;

    private array $balances;

    private string $language;

    private array $kitCoolDowns;

    private array $groups;

    private array $individualPermissions;

    /** @var array<PermissionAttachment> */
    private array $attachments = [];

    private string $faction;

    public function __construct(?Player $player) {
        $this->language = "en";
        if (is_null($player)) {
            return;
        }

        $this->player = $player;
        $this->balances = [0, 0, 0];
        $this->kitCoolDowns = [];
        $this->groups = ["Guest"];
        $this->individualPermissions = [];
        $this->faction = "";

        $this->sortGroups();
        $this->reloadPermissions();
    }

    public function close() : void {

    }

    public function addGroup(string $group) : void {
        $this->groups[] = $group;
        $this->reloadPermissions();
        $this->sortGroups();
    }

    public function removeGroup(string $group) : void {
        $this->groups = array_diff($this->groups, [$group]);
        $this->reloadPermissions();
        $this->sortGroups();
    }

    public function hasGroup(string $name) : bool {
        return in_array($name, $this->groups);
    }

    public function addIndividualPermission(string $permission) : void {
        $this->individualPermissions[] = $permission;
        $this->reloadPermissions();
    }

    public function removeIndividualPermission(string $permission) : void {
        $this->individualPermissions = array_diff($this->individualPermissions, [$permission]);
        $this->reloadPermissions();
    }

    public function hasIndividualPermission(string $permission) : bool {
        return in_array($permission, $this->individualPermissions);
    }

    public function sortGroups() : void {
        $new = [];
        foreach($this->groups as $group) {
            $group = GroupsManager::getInstance()->getGroup($group);
            $new[$group->getName()] = $group->getPriority();
        }
        arsort($new);
        $this->groups = array_keys($new);
    }

    public function getGroupObjects() : array {
        $gm = GroupsManager::getInstance();
        return array_map(fn(string $group) => $gm->getGroup($group), $this->groups);
    }

    public function reloadPermissions() : void {
        foreach($this->attachments as $attachment) {
            $this->player->removeAttachment($attachment);
        }
        $this->attachments = [];
        $permissions = $this->individualPermissions;
        foreach($this->getGroupObjects() as $group) {
            foreach($group->getPermissions() as $permission) {
                if (in_array($permission, $permissions)) {
                    continue;
                }
                $permission[] = $permission;
            }
        }
        foreach($permissions as $permission) {
            $this->attachments[] = $this->player->addAttachment(Main::getInstance(), $permission, $permission);
        }
    }

    public function addToBalance(int $amount, int $type) : void {
        $this->balances[$type] += $amount;
    }

    public function takeFromBalance(int $amount, int $type) : void {
        $this->balances[$type] -= $amount;
    }

    public function getBalance(int $type) : int {
        return $this->balances[$type];
    }

    public function getMessage(string $name, array $replace = [[], []]) : string {
        return LanguageManager::getInstance()->getTranslation($this->language, $name, $replace);
    }

    public function setKitCoolDown(string $kit) : void {
        $this->kitCoolDowns[$kit] = time();
    }

    public function getRemainingKitCoolDown(string $kit, int $coolDown) : int {
        if (is_null($result = $this->kitCoolDowns[$kit] ?? null)) {
            return -1;
        }
        return $coolDown - (time() - $result);
    }

    public function getPlayer() : Player {
        return $this->player;
    }

    public function getFaction() : string {
        return $this->faction;
    }

    public function getFactionObject() : ?Faction {
        return FactionsManager::getInstance()->getFaction($this->faction);
    }

    public function getFactionMember() : FactionMember {
        return $this->getFactionObject()->getMembersManager()->getMember($this->player);
    }

    public function hasFactionPermission(int $permission) : bool {
        return $this->getFactionMember()->getRole()->hasPermission($permission);
    }

    public function isInFaction() : bool {
        return $this->faction !== "";
    }

    public function setFaction(string $faction) : void {
        $this->faction = $faction;
    }

}