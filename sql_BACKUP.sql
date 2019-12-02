CREATE TABLE charlotter_sp_analytics(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, whenrecorded datetime, url VARCHAR(50), type VARCHAR(10),  linktext VARCHAR(50), domain VARCHAR(100) NOT NULL, sessionid VARCHAR(29) NOT NULL, returninguser TINYINT(1) NOT NULL, parent VARCHAR(100));

DROP TABLE charlotter_sp_analytics;

TRUNCATE TABLE charlotter_sp_analytics

/* category e.g. digital-practice , practice-information */

/* subcat e.g. health review and assessment clinic, prescriptions and medicines centre, practice boundary,*/

CREATE TABLE charlotter_sp_dim_category(category_id INT AUTO_INCREMENT PRIMARY KEY, category VARCHAR(50), sub_category VARCHAR(100), link VARCHAR(200));

CREATE TABLE charlotter_sp_dim_calendar(calendar_id INT AUTO_INCREMENT PRIMARY KEY, day_num INT, month_num INT, year_num INT);

CREATE TABLE charlotter_sp_dim_practice(practice_id INT AUTO_INCREMENT PRIMARY KEY, practice_name VARCHAR(200), practice_code VARCHAR(10), sp_shortcode VARCHAR(50), list_size INT, start_date DATE, pcn VARCHAR(200), federation VARCHAR(200), ccg VARCHAR(200), stp VARCHAR(200), ics VARCHAR(200));

CREATE TABLE charlotter_sp_dim_forms(form_id INT AUTO_INCREMENT PRIMARY KEY, form_name VARCHAR(200));

CREATE TABLE charlotter_sp_fact_clicks(click_id INT AUTO_INCREMENT PRIMARY KEY, category_id INT, calendar_id INT , practice_id INT, time_clicked TIMESTAMP, user_id VARCHAR(50) NOT NULL);

CREATE TABLE charlotter_sp_fact_exits(exit_id INT AUTO_INCREMENT PRIMARY KEY, page VARCHAR(200), calendar_id INT, practice_id INT, time_exited TIMESTAMP, user_id VARCHAR(50) NOT NULL);

CREATE TABLE charlotter_sp_fact_abandoned(abandoned_id INT AUTO_INCREMENT PRIMARY KEY, form_id INT, calendar_id INT, practice_id INT, time_abandoned TIMESTAMP, user_id VARCHAR(50) NOT NULL);

/* ADD FOREIGN KEY CONSTRAINTS */
/**/

#STORED PROCEDURE TO POPULATE DATE DIMENSION#
DELIMITER $$

CREATE
    /*[DEFINER = { user | CURRENT_USER }]*/
    PROCEDURE `wpdb_charlotter`.`populate_date_dimension`()
    /*LANGUAGE SQL
    | [NOT] DETERMINISTIC
    | { CONTAINS SQL | NO SQL | READS SQL DATA | MODIFIES SQL DATA }
    | SQL SECURITY { DEFINER | INVOKER }
    | COMMENT 'string'*/
	BEGIN
		DECLARE startdate DATETIME;
		DECLARE enddate DATETIME;
		DECLARE loopdate DATETIME;
		
		SET startdate = '2019-10-01';
		SET enddate = '2019-12-31';
		SET loopdate = startdate;

		WHILE loopdate <= enddate
			INSERT INTO charlotter_sp_dim_calendar(day_num, month_num, year_num) VALUES (
				DAY(loopdate),
				MONTH(loopdate),
				YEAR(loopdate)
			);
			
			SET loopdate = DATE_ADD(loopdate, 1, DAY);
		END WHILE;
	END$$

DELIMITER ;

CALL StoredProcedureName();


INSERT INTO charlotter_sp_dim_practice(practice_name, practice_code, sp_shortcode, list_size, start_date, pcn, federation, ccg, stp, ics) 
VALUES 
	('Drayton Medical Practice', 'D82029', 'draytonff', 18094, '2019-09-16', NULL, NULL, 'North Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Parish Fields Practice', 'D82031', 'parishff', 7930, '2019-10-15', NULL, NULL, 'South Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Magdalen Medical Practice', 'D82012', 'magdalenff', 13688, '2019-10-08', NULL, NULL, 'Norwhich CCG', 'Norfolk and Waveney', NULL),
	('Beccles Medical Centre', 'D82029', 'becclesff', 18094, '2019-09-16', NULL, NULL, 'Great Yarmouth & Waveney CCG', 'Norfolk and Waveney', NULL),
	('Feltwell Surgery', 'D82079', 'feltwellff', 14008, '2019-10-02', NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Thorpewood Medical Group', 'D82048', 'thorpewoodff', 5172, '2019-10-08', NULL, NULL, 'NHS Norwhich CCG', 'Norfolk and Waveney', NULL),
	('Boughton Surgery', 'D82604', 'boughtonff', 3006, '2019-09-26', NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Holt Medical Practice', 'D82001', 'holtff', 13882, '2019-10-22', NULL, NULL, 'North Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Beechcroft Surgery', 'Y03595', 'beechcroftff', 6923, NULL, NULL, NULL, 'Norwich CCG', 'Norfolk and Waveney', NULL),
	('Old Palace Medical Practice', 'Y03595', '', 6923, NULL, NULL, NULL, 'Norwich CCG', 'Norfolk and Waveney', NULL),
	('Mundesley Medical Practice', 'D820531', 'mundesleyff', 5723, NULL, NULL, NULL, 'North Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Grove Surgery', 'D82002', 'grovesurgeryff', 13326, '2019-10-16', NULL, NULL, 'South Norfolk CCG', 'Norfolk and Waveney', NULL),
	('School Lane Surgery', 'D82041', 'schoollaneff', 11571, '2019-10-16', NULL, NULL, 'South Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Acle Medical Centre', 'D82104', 'acleff', 9284, '2019-11-25', NULL, NULL, 'North Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Fakenham Medical Practice', 'D82054', 'fakenhamff', 15022, '2019-10-25', NULL, NULL, 'North Norfolk CCG', 'Norfolk and Waveney', NULL),
	('East Harling & Kenninghall Medical Practice', 'D82042', 'eastharlingff', 8360, '2019-11-01', NULL, NULL, 'South Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Prospect Medical Centre', 'D82087', 'prospectmedicalff', 6879, '2019-10-28', NULL, NULL, 'Norwich CCG', 'Norfolk and Waveney', NULL),
	('The Market Surgery', 'D82016', 'marketsurgeryff', 9713, '2019-11-07', NULL, NULL, 'North Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Plowright Medical Centre', 'D82621', 'plowrightff', 5936, '2019-11-28', NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Lakenham Surgery', 'D82026', 'lakenhamff', 8406, NULL, NULL, NULL, 'Norwich CCG', 'Norfolk and Waveney', NULL),
	('Hoveton & Wroxham Medical Centre', 'D82025', 'hovetonwroxhamff', 9013, '2019-11-11', NULL, NULL, 'North Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Attleborough Surgeries', 'D82034', '', 18294, NULL, NULL, NULL, 'South Norfolk CCG', 'Norfolk and Waveney', NULL),
	('Sheringham Medical Practice', 'D82005', 'sheringhamff', 9380, NULL, NULL, NULL, 'North Norfolk CCG', 'Norfolk and Waveney', NULL),
	("St James' House Surery", 'D82051', '', 16675, NULL, NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	("Rosedale Surgery", 'D83047', 'rosedaleff', 14623, '2019-11-18', NULL, NULL, 'Great Yarmouth & Waveney CCG', 'Norfolk and Waveney', NULL),
	("Wymondham Medical Centre", 'D82045', 'wymondhamff', 18811, '2019-11-28', NULL, NULL, 'South Norfolk CCG', 'Norfolk and Waveney', NULL),
	("Alexandra Road Surgery", 'D83002', 'alexandracrestviewff', 15517, '2019-12-23', NULL, NULL, 'Great Yarmouth & Waveney CCG', 'Norfolk and Waveney', NULL),
	("Crestview Medical Centre", 'D83002', 'alexandracrestviewff', 15517, '2019-12-23', NULL, NULL, 'Great Yarmouth & Waveney CCG', 'Norfolk and Waveney', NULL),
	("Carole Brown Health Centre", 'D82044', '', 32254, '2020-01-06', NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	("Kirkley Mill Health Centre", 'D83030', 'kirkleymillff', 6311, '2019-11-28', NULL, NULL, 'Great Yarmouth & Waveney CCG', 'Norfolk and Waveney', NULL),
	("High Street Surgery", 'D83023', 'highstreetsurgerylowestoftff', 12742, '2020-02-17', NULL, NULL, 'Great Yarmouth & Waveney CCG', 'Norfolk and Waveney', NULL),
	("Chet Valley Medical Practice", 'D82006', 'chetvalleyff', 8697, '2020-03-09', NULL, NULL, 'South Norfolk CCG', 'Norfolk and Waveney', NULL),
	("The Bridge Street Surgery", 'D82015', 'bridgestreetff', 8704, '2020-03-30', NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	("Elmham Surgery", 'D82056', '', 9843, NULL, NULL, NULL, 'South Norfolk CCG', 'Norfolk and Waveney', NULL),
	("Southgates Medical Centre", 'D82099', '', 16996, '2020-06-01', NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	("The Woottons", 'D82618', '', 16996, '2020-06-01', NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	("Upwell Health Centre", 'D82035', '', 10446, NULL, NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	("Gt Massingham Surgery", 'D82070', '', 6458, NULL, NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	("Manor Farm Medical Centre", 'D82065', '', 7130, NULL, NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	("St Clements Surgery", 'D82105', '', 6173, NULL, NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	("The Cringleford Surgery", 'D82064', '', 20235, NULL, NULL, NULL, 'Norwich CCG', 'Norfolk and Waveney', NULL),
	("Waltington Medical Centre", 'D82043', '', 6684, NULL, NULL, NULL, 'West Norfolk CCG', 'Norfolk and Waveney', NULL),
	("Watton Medical Practice", 'D82063', '', 12693, NULL, NULL, NULL, 'South Norfolk CCG', 'Norfolk and Waveney', NULL),
	("Charlotte's GP Surgery", 'T0TEST', 'charlotter', 10000, '2019-10-24', NULL, NULL, 'Testshire CCG', 'Testshire and Testerton', NULL),
	("krishnaveni's GP Surgery", "T1TEST", 'krishnaveni', 12000, '2019-11-21', NULL, NULL, 'Testshire CCG', 'Testshire and Testerton', NULL);
	