<?php namespace taylor\factions;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use taylor\factions\factions\FactionsManager;
use taylor\factions\groups\GroupsManager;
use taylor\factions\kits\KitsManager;
use taylor\factions\sessions\SessionManager;
use taylor\factions\utils\LanguageManager;

class Main extends PluginBase {

    use SingletonTrait;

    private DataConnector $connector;

    public function onLoad() : void {
        $this->getServer()->getLogger()->notice("[CORE] Loading.");
    }

    /*** @throws HookAlreadyRegistered */
    public function onEnable() : void {
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        self::setInstance($this);
        $this->saveResource("config.yml");

        $this->connector = libasynql::create(
            $this, [
                "type" => "mysql",
                "mysql" => [
                    "host" => "127.0.0.1",
                    "username" => "root",
                    "password" => "root",
                    "schema" => "factions"
                ],
                "worker-limit" => 2
            ],
            ["mysql" => "mysql.sql"]
        );

        new LanguageManager();
        new FactionsManager();
        new GroupsManager();
        new KitsManager();
        new SessionManager();
        $this->connector->waitAll();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getServer()->getLogger()->notice("[CORE] Successfully started. Rest in peace Carson Capik.");
    }

    public function getDatabase() : DataConnector {
        return $this->connector;
    }

}