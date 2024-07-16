<?php namespace taylor\factions\kits;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
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

    public function editItems(Player $player) : void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->send($player, "Edit " . $this->getName() . " Items");
        $menu->getInventory()->setContents($this->getContents());
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) : void {
             $this->contents = $inventory->getContents();
             $player->sendMessage(SessionManager::getInstance()->getSession($player)->getMessage("messages.kitsmgr.successfulEdit"));
             $this->sync();
        });
    }

    public function sync() : void {
        KitsManager::getInstance()->insertKit(
            $this->getName(),
            $this->getFancyName(),
            $this->getPermission(),
            $this->getCoolDown(),
            $this->getType(),
            $this->getContents()
        );
    }

    public function getFormSubTitle(Player $player) : string {
        $session = SessionManager::getInstance()->getSession($player);
        if ($session->getRemainingKitCoolDown($this->getName(), $this->getCoolDown()) > 0) {
            return "&cOn Cool Down!";
        }
        if ($this->getPermission() !== "NO_PERMISSION" && !$player->hasPermission($this->getPermission())) {
            return "&cNo Permission!";
        }
        return "&aReady to Equip!";
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