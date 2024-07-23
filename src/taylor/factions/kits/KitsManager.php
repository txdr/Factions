<?php namespace taylor\factions\kits;

use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use taylor\factions\kits\commands\KitsCommand;
use taylor\factions\kits\commands\KitsManagerCommand;
use taylor\factions\Main;
use taylor\factions\utils\ItemSerializer;

class KitsManager {

    use SingletonTrait;

    // TODO: Move this to config.yml so owners can add groups.
    public const VALID_GROUPS = ["Regular Kits", "God Kits"];

    /** @var array<string, Kit> */
    private array $kits = [];

    public function __construct() {
        self::setInstance($this);

        $db = Main::getInstance()->getDatabase();
        $db->executeGeneric("kits.init");

        Server::getInstance()->getCommandMap()->registerAll("Factions", [
            new KitsCommand(),
            new KitsManagerCommand()
        ]);

        $db->executeSelect("kits.get", [], function(array $rows) : void {
            foreach($rows as $row) {
                $this->kits[$row["kitName"]] = new Kit(
                    $row["kitName"],
                    $row["kitFancyName"],
                    ItemSerializer::unserializeMultiple(json_decode($row["kitContents"] ?? "{}", true)),
                    $row["kitCoolDown"],
                    $row["kitPermission"],
                    $row["kitType"]
                );
            }
        });
    }

    /*** @return Kit[] */
    public function getKits() : array {
        return $this->kits;
    }

    /**
     * @param string $group
     * @return array<Kit>
     */
    public function getKitsOfGroup(string $group) : array {
        return array_values(array_filter($this->kits, fn(Kit $kit) => $kit->getType() == $group));
    }

    public function getKit(string $name) : ?Kit {
        return $this->kits[$name] ?? null;
    }

    public function insertKit(
        string $name,
        string $fancyName,
        string $permission,
        int $coolDown,
        string $type,
        array $contents = []
    ) : void {
        $this->kits[$name] = new Kit($name, $fancyName, $contents, $coolDown, $permission, $type);
        Main::getInstance()->getDatabase()->executeInsert("kits.insert", [
            "kitName" => $name,
            "kitFancyName" => $fancyName,
            "kitPermission" => $permission,
            "kitType" => $type,
            "kitCoolDown" => $coolDown,
            "kitContents" => json_encode(ItemSerializer::serializeMultiple($contents))
        ]);
    }

    public function deleteKit(string $name) : void {
        unset($this->kits[$name]);
        Main::getInstance()->getDatabase()->executeGeneric("kits.delete", [":kitName" => $name]);
    }

}