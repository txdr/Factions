<?php namespace taylor\factions\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taylor\factions\factions\FactionsManager;
use taylor\factions\factions\managers\invites\FactionInvite;
use taylor\factions\Main;
use taylor\factions\sessions\SessionManager;

class JoinFactionSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "join", "Join a faction!");
    }

    public function prepare() : void {

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = SessionManager::getInstance()->getSession($sender);
        if (!$sender instanceof Player) {
            $sender->sendMessage($session->getMessage("commands.mustBeInGame"));
            return;
        }
        if ($session->isInFaction()) {
            $sender->sendMessage($session->getMessage("commands.faction.cannotBeInFaction"));
            return;
        }
        $possible = FactionsManager::getInstance()->getInvitesForPlayer($sender);
        $sender->sendForm(new CustomForm("Join a faction", [
            new Dropdown(
                "factionDropdown",
                "You have a invite from the following factions, select one and submit the form to join, or close form to not join any.",
                array_map(fn(FactionInvite $invite) => $invite->getParent()->getParent()->getName(), $possible)
            )
        ], function(Player $player, CustomFormResponse $data) use ($possible, $session) : void {
            /** @var FactionInvite $chosen */
            $chosen = $possible[$data->getInt("factionDropdown")];
            if ($chosen->getTimeLeft() < 1) {
                $player->sendMessage($session->getMessage("commands.faction.inviteExpired"));
                return;
            }
            $faction = $chosen->getParent()->getParent();
            if ($faction->getMembersManager()->getMemberCount() >= Main::getInstance()->getConfig()->get("max-faction-size")) {
                $player->sendMessage($session->getMessage("commands.faction.fullFaction"));
                return;
            }
            $name = $faction->getName();
            $session->setFaction($name);
            $faction->getMembersManager()->addMember($player);
            $player->sendMessage($session->getMessage("commands.faction.joined", [["{name}"], [$name]]));
            $faction->getInvitesManager()->closeInvite($chosen);
        }));
    }

}