<?php namespace taylor\factions\factions\managers;

use pocketmine\player\Player;
use taylor\factions\factions\Faction;
use taylor\factions\factions\managers\members\FactionMember;

class FactionMembersManager {

    private Faction $parent;

    /** @var array<string, FactionMember> */
    private array $members;

    public function __construct(Faction $parent, array $members) {
        $this->parent = $parent;
        $this->members = $members;
    }

    public function getParent() : Faction {
        return $this->parent;
    }

    public function getMemberCount() : int {
        return count(array_keys($this->members));
    }

    public function setMembers(array $members) : void {
        $this->members = $members;
    }

    public function addMember(Player $player) : void {
        $this->members[$player->getName()] = new FactionMember(
            $this,
            $player->getUniqueId()->getBytes(),
            $player->getName(),
            $this->parent->getRolesManager()->getRole("Recruit")
        );
    }

    /**
     * String can only be player UUID and player names.
     *
     * @param string|Player $uuid
     * @return FactionMember|null
     */
    public function getMember(string|Player $uuid) : ?FactionMember {
        if ($uuid instanceof Player) {
            $uuid = $uuid->getUniqueId()->getBytes();
        }
        if (is_null($member = $this->members[$uuid] ?? null)) {
            foreach($this->getMembers() as $member) {
                if ($member->getMemberName() == $uuid) {
                    return $member;
                }
            }
            return null;
        }
        return $member;
    }

    /*** @return array<FactionMember> */
    public function getMembers() : array {
        return array_values($this->members);
    }

    public function removeMember(FactionMember|string $member) : void {
        if (!$member instanceof FactionMember) {
            if (is_null($member = $this->getMember($member))) {
                return;
            }
        }
        if (!is_null($session = $member->getPlayerSession())) {
            $session->setFaction("");
        }
        unset($this->members[$member->getMemberUUID()]);
    }

}