-- #!sqlite
-- #{ init_topstats
CREATE TABLE IF NOT EXISTS topstats (
    xuid TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    block_break INTEGER DEFAULT 0,
    block_place INTEGER DEFAULT 0,
    change_skin INTEGER DEFAULT 0,
    chat INTEGER DEFAULT 0,
    consume INTEGER DEFAULT 0,
    crafting INTEGER DEFAULT 0,
    damage_dealt INTEGER DEFAULT 0,
    damage_received INTEGER DEFAULT 0,
    death INTEGER DEFAULT 0,
    drop_item INTEGER DEFAULT 0,
    emote INTEGER DEFAULT 0,
    enchant INTEGER DEFAULT 0,
    farm INTEGER DEFAULT 0,
    heal INTEGER DEFAULT 0,
    item_pickup INTEGER DEFAULT 0,
    jump INTEGER DEFAULT 0,
    kick INTEGER DEFAULT 0,
    money INTEGER DEFAULT 0,
    online_time INTEGER DEFAULT 0,
    xp INTEGER DEFAULT 0
);
-- #}
-- #{ insert_stats
-- #    :xuid string
-- #    :name string
INSERT INTO topstats (xuid, name)
VALUES (:xuid, :name)
ON CONFLICT(xuid) DO UPDATE SET
    name = excluded.name;
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
ON CONFLICT(xuid) DO UPDATE SET
    name = excluded.name,
    block_break = excluded.block_break,
    block_place = excluded.block_place,
    change_skin = excluded.change_skin,
    chat = excluded.chat,
    consume = excluded.consume,
    crafting = excluded.crafting,
    damage_dealt = excluded.damage_dealt,
    damage_received = excluded.damage_received,
    death = excluded.death,
    drop_item = excluded.drop_item,
    emote = excluded.emote,
    enchant = excluded.enchant,
    farm = excluded.farm,
    heal = excluded.heal,
    item_pickup = excluded.item_pickup,
    jump = excluded.jump,
    kick = excluded.kick,
    money = excluded.money,
    online_time = excluded.online_time,
    xp = excluded.xp;
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
UPDATE topstats SET
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
