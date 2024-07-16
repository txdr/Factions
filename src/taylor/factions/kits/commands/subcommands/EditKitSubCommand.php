<?php namespace taylor\factions\kits\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taylor\factions\kits\KitsManager;
use taylor\factions\Main;
use taylor\factions\sessions\SessionManager;

class EditKitSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "editkit", "Edit a kit.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("kit"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = SessionManager::getInstance()->getSession($sender);
        if (!$sender instanceof Player) {
            $sender->sendMessage($session->getMessage("commands.mustBeInGame"));
            return;
        }
        if (is_null($kit = KitsManager::getInstance()->getKit($args["kit"]))) {
            $sender->sendMessage($session->getMessage("commands.kitsmgr.kitNotExists"));
            return;
        }
        $kit->editItems($sender);
    }

}