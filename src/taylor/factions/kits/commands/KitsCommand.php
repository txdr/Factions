<?php namespace taylor\factions\kits\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taylor\factions\kits\KitsManager;
use taylor\factions\Main;
use taylor\factions\sessions\SessionManager;

class KitsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "kit", "Equip a kit!", ["kits"]);
        $this->setPermission("commands.kits");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("kit", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command must be used in-game.");
            return;
        }
        $session = SessionManager::getInstance()->getSession($sender);
        if (isset($args["kit"])) {
            if (is_null($kit = KitsManager::getInstance()->getKit($args["kit"]))) {
                $sender->sendMessage($session->getMessage("commands.kits.cantfindkit"));
                return;
            }
            $kit->equip($sender);
            return;
        }
    }

}