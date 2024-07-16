<?php namespace taylor\factions\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use taylor\factions\factions\managers\roles\RolePermissions;
use taylor\factions\Main;
use taylor\factions\sessions\SessionManager;
use taylor\factions\utils\PlayerUtils;

class InviteSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "invite", "Invite a player to your faction.");
    }

    public function prepare() : void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $sm = SessionManager::getInstance();
        $session = $sm->getSession($sender);
        if (!$sender instanceof Player) {
            $sender->sendMessage($session->getMessage("commands.mustBeInGame"));
            return;
        }
        if (!$session->isInFaction()) {
            $sender->sendMessage($session->getMessage("commands.faction.mustBeInFaction"));
            return;
        }
        if (!$session->hasFactionPermission(RolePermissions::INVITE)) {
            $sender->sendMessage($session->getMessage("commands.faction.noFactionPermission"));
            return;
        }
        $valid = array_map(fn(Player $player) => $player->getName(), PlayerUtils::getPlayers(fn(Player $player) => $sm->getSession($player)->getFaction() == "" && $player->getId() !== $sender->getId()));
        $sender->sendForm(new CustomForm(
            "Invite a Player",
            [new Dropdown("players", "Player to invite", $valid)],
            function(Player $player, CustomFormResponse $data) use ($session, $valid, $sm) : void {
                if (is_null($invite = Server::getInstance()->getPlayerExact($name = $valid[$data->getInt("players")]))) {
                    $player->sendMessage($session->getMessage("commands.faction.inviteOffline"));
                    return;
                }
                $im = ($faction = $session->getFactionObject())->getInvitesManager();
                if (!is_null($im->getInvite($name))) {
                    $player->sendMessage($session->getMessage("commands.faction.alreadyInvited"));
                    return;
                }
                $player->sendMessage($session->getMessage("commands.faction.invites", [["{name}"], [$name]]));
                $invite->sendMessage($sm->getSession($invite)->getMessage("commands.faction.justInvited", [["{factionName}", "{invitedName}"], [$faction->getName(), $player->getName()]]));
                $im->submitInvite($invite);
            }
        ));
    }

}