<?php namespace taylor\factions\factions\managers;

use pocketmine\player\Player;
use taylor\factions\factions\Faction;
use taylor\factions\factions\managers\invites\FactionInvite;

class FactionInvitesManager {

    private Faction $parent;

    /** @var array<FactionInvite> */
    private array $invites;

    public function __construct(Faction $parent, array $invites) {
        $this->parent = $parent;
        $this->invites = $invites;
    }

    public function getParent() : Faction {
        return $this->parent;
    }

    public function submitInvite(Player $player) : void {
        $this->invites[$name = $player->getName()] = new FactionInvite(
            $this,
            $name,
            $player->getUniqueId()->getBytes(),
            60 * 30,
            date('d/m/Y h:i A')
        );
    }

    public function setInvites(array $invites) : void {
        $this->invites = $invites;
    }

    public function getInvite(string|Player $playerName) : ?FactionInvite {
        if ($playerName instanceof Player) {
            $playerName = $playerName->getUniqueId()->getBytes();
        }
        $ret = $this->invites[$playerName] ?? null;
        if (!is_null($ret)) {
            return $ret;
        }
        foreach($this->invites as $invite) {
            if ($invite->getReceiverUUID() == $playerName) {
                return $invite;
            }
        }
        return null;
    }

    public function closeInvite(FactionInvite $invite) : void {
        unset($this->invites[$invite->getReceiverName()]);
    }

    public function tickInvites() : void {
        foreach($this->invites as $invite) {
            $invite->tick();
        }
    }

}