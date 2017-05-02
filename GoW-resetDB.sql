TRUNCATE `arbitrage` ;
TRUNCATE `cartes` ;
TRUNCATE `enregistrement` ;
TRUNCATE `mots_interdits`;
TRUNCATE `notif` ;
TRUNCATE `parties` ;
TRUNCATE `sanctionCarte` ;
UPDATE `stats` SET `nbJeux_oracle` = '0', `nbAbandons_oracle` = '0', `nbEnregistrements_oracle` = '0', `nbErreurs_oracle` = '0', `nbLectures_oracle` = '0', `nbSucces_oracle` = '0', `score_oracle` = '0', `nbCartes_druide` = '0', `nbArbitrages_druide` = '0', `nbErrArbitrage_druide` = '0', `score_druide` = '0', `nbEnregistrements_devin` = '0', `nbMotsTrouves_devin` = '0', `score_devin` = '0' WHERE *;
TRUNCATE `themes` ;
TRUNCATE `themes_cartes` ;
--
-- TRUNCATE `langues` ;
-- TRUNCATE `user` ;
-- TRUNCATE `user_niveau` ;
--
