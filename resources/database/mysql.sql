-- #!mysql
-- #{ init_topstats
CREATE TABLE IF NOT EXISTS topstats (
    xuid VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    block_break INT DEFAULT 0,
    block_place INT DEFAULT 0,
    change_skin INT DEFAULT 0,
    chat INT DEFAULT 0,
    consume INT DEFAULT 0,
    crafting INT DEFAULT 0,
    damage_dealt INT DEFAULT 0,
    damage_received INT DEFAULT 0,
    death INT DEFAULT 0,
    drop_item INT DEFAULT 0,
    emote INT DEFAULT 0,
    enchant INT DEFAULT 0,
    farm INT DEFAULT 0,
    heal INT DEFAULT 0,
    item_pickup INT DEFAULT 0,
    jump INT DEFAULT 0,
    kick INT DEFAULT 0,
    money INT DEFAULT 0,
    online_time INT DEFAULT 0,
    xp INT DEFAULT 0
);
-- #}
-- #{ insert_stats
-- #    :xuid string
-- #    :name string
INSERT INTO topstats (xuid, name)
VALUES (:xuid, :name)
ON DUPLICATE KEY UPDATE
    name = VALUES(name);
-- #}
-- #{ insert_or_update_stats
-- #    :xuid string
-- #    :name string
-- #    :block_break int
-- #    :block_place int
-- #    :change_skin int
-- #    :chat int
-- #    :consume int
-- #    :crafting int
-- #    :damage_dealt int
-- #    :damage_received int
-- #    :death int
-- #    :drop_item int
-- #    :emote int
-- #    :enchant int
-- #    :farm int
-- #    :heal int
-- #    :item_pickup int
-- #    :jump int
-- #    :kick int
-- #    :money int
-- #    :online_time int
-- #    :xp int
INSERT INTO topstats (
    xuid, name, block_break, block_place, change_skin, chat, consume, crafting,
    damage_dealt, damage_received, death, drop_item, emote, enchant, farm, heal,
    item_pickup, jump, kick, money, online_time, xp
) VALUES (
    :xuid, :name, :block_break, :block_place, :change_skin, :chat, :consume, :crafting,
    :damage_dealt, :damage_received, :death, :drop_item, :emote, :enchant, :farm, :heal,
    :item_pickup, :jump, :kick, :money, :online_time, :xp
)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    block_break = VALUES(block_break),
    block_place = VALUES(block_place),
    change_skin = VALUES(change_skin),
    chat = VALUES(chat),
    consume = VALUES(consume),
    crafting = VALUES(crafting),
    damage_dealt = VALUES(damage_dealt),
    damage_received = VALUES(damage_received),
    death = VALUES(death),
    drop_item = VALUES(drop_item),
    emote = VALUES(emote),
    enchant = VALUES(enchant),
    farm = VALUES(farm),
    heal = VALUES(heal),
    item_pickup = VALUES(item_pickup),
    jump = VALUES(jump),
    kick = VALUES(kick),
    money = VALUES(money),
    online_time = VALUES(online_time),
    xp = VALUES(xp);
-- #}
-- #{ update_stats
-- #    :xuid string
-- #    :block_break int
-- #    :block_place int
-- #    :change_skin int
-- #    :chat int
-- #    :consume int
-- #    :crafting int
-- #    :damage_dealt int
-- #    :damage_received int
-- #    :death int
-- #    :drop_item int
-- #    :emote int
-- #    :enchant int
-- #    :farm int
-- #    :heal int
-- #    :item_pickup int
-- #    :jump int
-- #    :kick int
-- #    :money int
-- #    :online_time int
-- #    :xp int
UPDATE topstats
SET 
    block_break = :block_break,
    block_place = :block_place,
    change_skin = :change_skin,
    chat = :chat,
    consume = :consume,
    crafting = :crafting,
    damage_dealt = :damage_dealt,
    damage_received = :damage_received,
    death = :death,
    drop_item = :drop_item,
    emote = :emote,
    enchant = :enchant,
    farm = :farm,
    heal = :heal,
    item_pickup = :item_pickup,
    jump = :jump,
    kick = :kick,
    money = :money,
    online_time = :online_time,
    xp = :xp
WHERE xuid = :xuid;
-- #}
-- #{ select_stats
-- #    :xuid string
SELECT * FROM topstats WHERE xuid = :xuid;
-- #}
-- #{ select_all_stats
SELECT * FROM topstats;
-- #}