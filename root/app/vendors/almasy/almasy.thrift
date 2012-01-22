namespace cpp almasy

service Almasy {
    string getBattle(1:i32 attackerFormationId, 2:i32 defenderFormationId),
    string getCharacterStats(1:i32 characterId),
    string getFormationStats(1:i32 formationId)
}