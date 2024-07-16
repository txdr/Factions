<?php namespace taylor\factions\factions;

use pocketmine\player\Player;
use taylor\factions\factions\managers\FactionInvitesManager;
use taylor\factions\factions\managers\FactionMembersManager;
use taylor\factions\factions\managers\FactionRolesManager;
use taylor\factions\factions\managers\invites\FactionInvite;
use taylor\factions\factions\managers\members\FactionMember;
use taylor\factions\factions\managers\roles\FactionRole;
use taylor\factions\factions\managers\roles\RolePermissions;

class Faction {

    private string $name;

    private string $description;

    private string $payoutLink;

    private string $creationDate;

    private string $ownerName;

    private FactionMembersManager $membersManager;

    private FactionRolesManager $rolesManager;

    private FactionInvitesManager $invitesManager;

    public function __construct(
        string $name,
        string $description,
        string $payoutLink,
        string $creationDate,
        string $ownerName,
        string $members,
        string $roles,
        string $invites,
        bool $new = false,
        ?Player $owner = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->payoutLink = $payoutLink;
        $this->creationDate = $creationDate;
        $this->ownerName = $ownerName;
        $this->rolesManager = new FactionRolesManager($this, []);
        $this->membersManager = new FactionMembersManager($this, []);
        $this->invitesManager = new FactionInvitesManager($this, []);
        if ($new) {
            $this->rolesManager->setRoles([
                $ownerRole = new FactionRole($this->rolesManager, "Owner", [RolePermissions::ADMIN]),
                new FactionRole($this->rolesManager, "Captain", [
                    RolePermissions::BUILD, RolePermissions::PLACE, RolePermissions::INTERACT,
                    RolePermissions::DEMOTE, RolePermissions::INVITE, RolePermissions::PROMOTE,
                    RolePermissions::KICK, RolePermissions::WARP_FACTION_HOME
                ]),
                new FactionRole($this->rolesManager, "Member", [
                    RolePermissions::BUILD, RolePermissions::PLACE, RolePermissions::INTERACT,
                    RolePermissions::WARP_FACTION_HOME
                ]),
                new FactionRole($this->rolesManager, "Recruit", [])
            ]);
            $this->membersManager->setMembers([$uuid = $owner->getUniqueId()->getBytes() => new FactionMember($this->membersManager, $uuid, $owner->getName(), $ownerRole)]);
            return;
        }
        $this->rolesManager->setRoles(
            array_merge(
                ...array_map(fn(array $role) => [$role["name"] => new FactionRole(
                    $this->rolesManager,
                    $role["name"],
                    $role["permissions"]
                )], json_decode($roles, true))
            )
        );
        $this->membersManager->setMembers(
            array_merge(
                ...array_map(fn(array $member) => [$member["memberName"] => new FactionMember(
                    $this->membersManager,
                    $member["memberUUID"],
                    $member["memberName"],
                    $this->rolesManager->getRole($member["role"])
                )], json_decode($members, true))
            )
        );
        $this->invitesManager->setInvites([
            array_merge(
                ...array_map(fn(array $invite) => [$invite["receiverName"] => new FactionInvite(
                    $this->invitesManager,
                    $invite["receiverName"],
                    $invite["receiverUUID"],
                    $invite["timeLeft"],
                    $invite["startedAt"]
                )], json_decode($invites, true))
            )
        ]);
    }

    public function getName() : string {
        return $this->name;
    }

    public function getMembersManager() : FactionMembersManager {
        return $this->membersManager;
    }

    public function getInvitesManager() : FactionInvitesManager {
        return $this->invitesManager;
    }

    public function getRolesManager() : FactionRolesManager {
        return $this->rolesManager;
    }

}