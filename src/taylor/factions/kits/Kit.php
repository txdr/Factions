<?php namespace taylor\factions\kits;

use pocketmine\item\Item;
use pocketmine\player\Player;
use taylor\factions\sessions\SessionManager;
use taylor\factions\utils\PlayerUtils;

class Kit {

    private string $name;

    private string $fancyName;

    /** @var array<Item> */
    private array $contents;

    private int $coolDown;

    private string $permission;

    private string $type;

    public function __construct(string $name, string $fancyName, array $contents, int $coolDown, string $permission, string $type) {
        $this->name = $name;
        $this->fancyName = $fancyName;
        $this->contents = $contents;
        $this->coolDown = $coolDown;
        $this->permission = $permission;
        $this->type = $type;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getFancyName() : string {
        return $this->fancyName;
    }

    /*** @return array|Item[] */
    public function getContents() : array {
        return $this->contents;
    }

    public function getCoolDown() : int {
        return $this->coolDown;
    }

    public function getPermission() : string {
        return $this->permission;
    }

    public function getType() : string {
        return $this->type;
    }

    public function equip(Player $player) : void {
        $session = SessionManager::getInstance()->getSession($player);
        $player->sendMessage($session->getMessage("module.kits.equipKit", [["{fancyName}"], [$this->getFancyName()]]));
        $session->setKitCoolDown($this->name);
        $dropped = false;
        foreach($this->contents as $item) {
            if (!PlayerUtils::safeGive($player, $item)) {
                $dropped = true;
            }
        }
        if ($dropped) {
            $player->sendMessage($session->getMessage("module.kits.droppedGround"));
        }
    }

}