<?php namespace taylor\factions\factions\managers\invites;

use taylor\factions\factions\managers\FactionInvitesManager;

class FactionInvite {

    private FactionInvitesManager $parent;

    private string $receiverName;

    private string $receiverUUID;

    private int $timeLeft;

    private string $startedAt;

    public function __construct(
        FactionInvitesManager $parent,
        string $receiverName,
        string $receiverUUID,
        int $timeLeft,
        string $startedAt
    ) {
        $this->parent = $parent;
        $this->receiverName = $receiverName;
        $this->receiverUUID = $receiverUUID;
        $this->timeLeft = $timeLeft;
        $this->startedAt = $startedAt;
    }

    public function getParent() : FactionInvitesManager {
        return $this->parent;
    }

    public function getReceiverName() : string {
        return $this->receiverName;
    }

    public function getReceiverUUID() : string {
        return $this->receiverUUID;
    }

    public function getTimeLeft() : int {
        return $this->timeLeft;
    }

    public function getStartedAt() : string {
        return $this->startedAt;
    }

    public function tick() : void {
        $this->timeLeft--;
        if ($this->timeLeft < 1) {
            $this->parent->closeInvite($this);
        }
    }

    public function pack() : string {
        return json_encode([
            "receiverName" => $this->receiverName,
            "receiverUUID" => $this->receiverUUID,
            "timeLeft" => $this->timeLeft,
            "startedAt" => $this->startedAt
        ]);
    }

}

