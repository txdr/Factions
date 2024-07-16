<?php namespace taylor\factions\groups\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use taylor\factions\Main;
use taylor\factions\sessions\SessionManager;

class RemovePermissionSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "remove", "Remove a player's permission.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new RawStringArgument("permission"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = SessionManager::getInstance()->getSession($sender);
        if (is_null($player = Server::getInstance()->getPlayerExact($args["player"]))) {
            $sender->sendMessage($session->getMessage("commands.cantFindPlayer"));
            return;
        }
        $playerSession = SessionManager::getInstance()->getSession($player);
        if (!$playerSession->hasIndividualPermission($permission = $args["permission"])) {
            $sender->sendMessage($session->getMessage("commands.permissions.doesntHavePermission"));
            return;
        }
        $playerSession->removeIndividualPermission($permission);
        $sender->sendMessage($session->getMessage("commands.permissions.permissionsRemoved", [["{permission}", "{player}"], [$permission, $player->getName()]]));
    }

}