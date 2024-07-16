-- #!mysql
-- #{ players
-- #{ init
CREATE TABLE IF NOT EXISTS players(xuid VARCHAR(16));
-- #}
-- #}

-- #{ kits
-- #{ init
CREATE TABLE IF NOT EXISTS kits(
    kitName VARCHAR(64) PRIMARY KEY,
    kitFancyName VARCHAR(64),
    kitPermission VARCHAR(64),
    kitType VARCHAR(64),
    kitCoolDown INTEGER,
    kitContents JSON
);
-- # }
-- # { insert
-- #    :kitName string
-- #    :kitFancyName string
-- #    :kitPermission string
-- #    :kitType string
-- #    :kitCoolDown int
-- #    :kitContents string
INSERT INTO kits(kitName, kitFancyName, kitPermission, kitType, kitCoolDown, kitContents)
VALUES(:kitName, :kitFancyName, :kitPermission, :kitType, :kitCoolDown, kitContents)
ON DUPLICATE KEY UPDATE kitFancyName = :kitFancyName, kitPermission = :kitPermission,
                        kitType = :kitType, kitCoolDown = :kitCoolDown, kitContents = :kitContents;
-- # }
-- # { delete
-- #    :kitName string
DELETE FROM kits WHERE kitName = :kitName;
-- # }
-- # { get
SELECT * FROM kits;
-- # }
-- # }