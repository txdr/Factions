<?php namespace taylor\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taylor\factions\factions\managers\members\FactionMember;
use taylor\factions\factions\managers\roles\RolePermissions;
use taylor\factions\Main;
use taylor\factions\sessions\SessionManager;

class KickSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "kick", "Kick a player from your faction.");
    }

    public function prepare() : void {

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = SessionManager::getInstance()->getSession($sender);
        if (!$sender instanceof Player) {
            $sender->sendMessage($session->getMessage("commands.mustBeInGame"));
            return;
        }
        if (!$session->isInFaction()) {
            $sender->sendMessage($session->getMessage("commands.faction.mustBeInFaction"));
            return;
        }
        if (!$session->getFactionMember()->getRole()->hasPermission(RolePermissions::KICK)) {
            $sender->sendMessage($session->getMessage("commands.faction.noFactionPermission"));
            return;
        }
        $faction = $session->getFactionObject();
        $validMembers = array_map(fn(FactionMember $member) => $member->getMemberName(), array_filter($faction->getMembersManager()->getMembers(), fn(FactionMember $member) => !$member->getRole()->hasPermission(RolePermissions::KICK)));
        $sender->sendForm(new CustomForm(
            "Kick a member",
            [new Dropdown("members", "Select a player to kick", $validMembers)],
            function(Player $player, CustomFormResponse $data) use ($faction, $validMembers) : void {
                $name = $validMembers[$data->getInt("members")];
                if (is_null($faction->getMembersManager()->getMember($name)))
            }
        ));
    }

}