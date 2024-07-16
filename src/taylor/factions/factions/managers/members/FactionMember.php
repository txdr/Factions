<?php namespace taylor\factions\factions\managers\members;

use pocketmine\player\Player;
use pocketmine\Server;
use taylor\factions\factions\managers\FactionMembersManager;
use taylor\factions\factions\managers\roles\FactionRole;
use taylor\factions\sessions\PlayerSession;

class FactionMember {

    private FactionMembersManager $parent;

    private string $memberUUID;

    private string $memberName;

    private FactionRole $role;

    public function __construct(FactionMembersManager $parent, string $memberUUID, string $memberName, FactionRole $role) {
        $this->parent = $parent;
        $this->memberUUID = $memberUUID;
        $this->memberName = $memberName;
        $this->role = $role;
    }

    public function getParent() : FactionMembersManager {
        return $this->parent;
    }

    public function getMemberUUID() : string {
        return $this->memberUUID;
    }

    public function getMemberName() : string {
        return $this->memberName;
    }

    public function getRole() : FactionRole {
        return $this->role;
    }

    public function getPlayerObject() : ?Player {
        return Server::getInstance()->getPlayerExact($this->memberName);
    }

    public function getPlayerSession() : ?PlayerSession {

    }

    public function pack() : string {
        return json_encode([
            "memberUUID" => $this->memberUUID,
            "memberName" => $this->memberName,
            "role" => $this->role->getName()
        ]);
    }

}