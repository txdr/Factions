<?php namespace taylor\factions;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use taylor\factions\kits\KitsManager;
use taylor\factions\sessions\SessionManager;
use taylor\factions\utils\LanguageManager;

class Main extends PluginBase {

    use SingletonTrait;

    private DataConnector $connector;

    /*** @throws HookAlreadyRegistered */
    public function onEnable() : void {
        self::setInstance($this);
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

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
        new KitsManager();
        new SessionManager();
        $this->connector->waitAll();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getServer()->getLogger()->notice("[CORE] Everything loaded correctly.");
    }

    public function getDatabase() : DataConnector {
        return $this->connector;
    }

}