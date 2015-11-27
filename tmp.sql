--Toutes les cartes pour lesquelles 2 a fait un enr
SELECT `carteID` FROM `enregistrement` WHERE `idOracle`='2'

--Toutes les cartes pour lesquelles 2 a fait le druide
SELECT `enregistrement`.`carteID` FROM `arbitrage`,`enregistrement` WHERE `arbitrage`.`enregistrementID` = `enregistrement`.`enregistrementID` AND `idDruide`='2'

--Toutes les cartes créées par 2
SELECT `idCarte` FROM `cartes` WHERE `idDruide`='2'

--Toutes les cartes devinées par 2
SELECT `enregistrement`.`carteID` FROM `parties`,`enregistrement` WHERE `parties`.`enregistrementID` = `enregistrement`.`enregistrementID` AND `idDevin`='3'

--NOT IN UNION Ci-dessus

SELECT * FROM `cartes` WHERE `cartes`.`idDruide`!='$idJ' AND `cartes`.`idCarte` NOT IN (
	(SELECT `carteID` FROM `enregistrement` WHERE `idOracle`='$idJ')
  UNION (SELECT `enregistrement`.`carteID` FROM `arbitrage`,`enregistrement` WHERE `arbitrage`.`enregistrementID` = `enregistrement`.`enregistrementID` AND `idDruide`='$idJ')

SELECT * FROM `cartes` WHERE `cartes`.`idDruide`!='$idJ' AND `cartes`.`idCarte` NOT IN (
	SELECT `carteID` as`cardId` FROM `enregistrement` WHERE `idOracle`='$idJ'
  UNION SELECT `enregistrement`.`carteID` as`cardId` FROM `arbitrage`,`enregistrement` WHERE `arbitrage`.`enregistrementID` = `enregistrement`.`enregistrementID` AND `idDruide`='$idJ'
);

-- TODO :
--	* créer des constantes pour les types de requêtes possibles (n'a pas créé, n'a pas joué, n'a pas deviné, arbitré)
--	* pour chaque type de requête, la requête est stockée (constante aussi, mais de la classe CARTE)
--	* on préparer une requête avec unions, qu'on compose selon les paramètres… (voir les ≠ variables idUsr, idCarte, etc.) → order by rand, etc.


