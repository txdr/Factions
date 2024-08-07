<?php namespace taylor\factions\kits\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use taylor\factions\kits\KitsManager;
use taylor\factions\Main;
use taylor\factions\sessions\SessionManager;

class DeleteKitSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "deletekit", "Delete a kit.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("kit"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $km = KitsManager::getInstance();
        $session = SessionManager::getInstance()->getSession($sender);
        if (is_null($km->getKit($name = $args["kit"]))) {
            $sender->sendMessage($session->getMessage("commands.kitsmgr.kitNotExists"));
            return;
        }
        $km->deleteKit($name);
        $sender->sendMessage($session->getMessage("commands.kitsmgr.kitDeleted", [["{name}"], [$name]]));
    }

}