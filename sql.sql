CREATE TABLE charlotter_sp_analytics(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, whenrecorded datetime, url VARCHAR(50), type VARCHAR(10),  linktext VARCHAR(50), domain VARCHAR(100) NOT NULL, sessionid VARCHAR(29) NOT NULL, returninguser TINYINT(1) NOT NULL, parent VARCHAR(100));

DROP TABLE charlotter_sp_analytics;

TRUNCATE TABLE charlotter_sp_analytics

/* category e.g. digital-practice , practice-information */

/* subcat e.g. health review and assessment clinic, prescriptions and medicines centre, practice boundary,*/

CREATE TABLE charlotter_sp_dim_category(category_id INT AUTO_INCREMENT PRIMARY KEY, category VARCHAR(50), sub_category VARCHAR(100), link VARCHAR(200));

CREATE TABLE charlotter_sp_dim_calendar(calendar_id INT AUTO_INCREMENT PRIMARY KEY, day_num INT, month_num INT, year_num INT);

CREATE TABLE charlotter_sp_dim_practice(practice_id INT AUTO_INCREMENT PRIMARY KEY, practice_name VARCHAR(200), practice_code VARCHAR(10), sp_shortcode VARCHAR(50), list_size INT, start_date DATE, federation VARCHAR(200), ccg VARCHAR(200));

CREATE TABLE charlotter_sp_fact_clicks(click_id INT AUTO_INCREMENT PRIMARY KEY, category_id INT, calendar_id INT , practice_id INT, time_clicked TIMESTAMP, user_id VARCHAR(50) NOT NULL);

CREATE TABLE charlotter_sp_fact_exits(exit_id INT AUTO_INCREMENT PRIMARY KEY, page VARCHAR(200), calendar_id INT, practice_id INT, time_exited TIMESTAMP, user_id VARCHAR(50) NOT NULL);

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


INSERT INTO charlotter_sp_dim_practice(practice_name, practice_code, sp_shortcode, list_size, start_date, federation, ccg) 
VALUES 
	('Drayton Medical Practice', 'D82029', 'draytonff', 18094, '2019-09-16', NULL, 'North Norfolk CCG'),
	('Parish Fields Practice', 'D82031', 'parishff', 7930, '2019-10-15', NULL, 'South Norfolk CCG'),
	('Magdalen Medical Practice', 'D82012', 'magdalenff', 13688, '2019-10-08', NULL, 'Norwhich CCG'),
	('Beccles Medical Centre', 'D82029', 'becclesff', 18094, '2019-09-16', NULL, 'Great Yarmouth & Waveney CCG'),
	('Feltwell Surgery', 'D82079', 'feltwellff', 14008, '2019-10-02', NULL, 'West Norfolk CCG'),
	('Thorpewood Medical Group', 'D82048', 'thorpewoodff', 5172, '2019-10-08', NULL, 'NHS Norwhich CCG'),
	('Boughton Surgery', 'D82604', 'boughtonff', 3006, '2019-09-26', NULL, 'West Norfolk CCG'),
	('Holt Medical Practice', 'D82001', 'holtff', 13882, '2019-10-22', NULL, 'North Norfolk CCG'),
	('Beechcroft Surgery', 'Y03595', 'beechcroftff', 6923, NULL, NULL, 'Norwich CCG'),
	('Old Palace Medical Practice', 'Y03595', '', 6923, NULL, NULL, 'Norwich CCG'),
	('Mundesley Medical Practice', 'D820531', 'mundesleyff', 5723, NULL, NULL, 'North Norfolk CCG'),
	('Grove Surgery', 'D82002', 'grovesurgeryff', 13326, '2019-10-16', NULL, 'South Norfolk CCG'),
	('School Lane Surgery', 'D82041', 'schoollaneff', 11571, '2019-10-16', NULL, 'South Norfolk CCG'),
	('Acle Medical Centre', 'D82104', 'acleff', 9284, '2019-11-25', NULL, 'North Norfolk CCG'),
	('Fakenham Medical Practice', 'D82054', 'fakenhamff', 15022, '2019-10-25', NULL, 'North Norfolk CCG'),
	('East Harling & Kenninghall Medical Practice', 'D82042', 'eastharlingff', 8360, '2019-11-01', NULL, 'South Norfolk CCG'),
	('Prospect Medical Centre', 'D82087', 'prospectmedicalff', 6879, '2019-10-28', NULL, 'Norwich CCG'),
	('The Market Surgery', 'D82016', 'marketsurgeryff', 9713, '2019-11-07', NULL, 'North Norfolk CCG'),
	('Plowright Medical Centre', 'D82621', 'plowrightff', 5936, '2019-11-28', NULL, 'West Norfolk CCG'),
	('Lakenham Surgery', 'D82026', 'lakenhamff', 8406, NULL, NULL, 'Norwich CCG'),
	('Hoveton & Wroxham Medical Centre', 'D82025', 'hovetonwroxhamff', 9013, '2019-11-11', NULL, 'North Norfolk CCG'),
	('Attleborough Surgeries', 'D82034', '', 18294, NULL, NULL, 'South Norfolk CCG'),
	('Sheringham Medical Practice', 'D82005', 'sheringhamff', 9380, NULL, NULL, 'North Norfolk CCG'),
	("St James' House Surery", 'D82051', '', 16675, NULL, NULL, 'West Norfolk CCG'),
	("Rosedale Surgery", 'D83047', 'rosedaleff', 14623, '2019-11-18', NULL, 'Great Yarmouth & Waveney CCG'),
	("Wymondham Medical Centre", 'D82045', 'wymondhamff', 18811, '2019-11-28', NULL, 'South Norfolk CCG'),
	("Alexandra Road Surgery", 'D83002', 'alexandracrestviewff', 15517, '2019-12-23', NULL, 'Great Yarmouth & Waveney CCG'),
	("Crestview Medical Centre", 'D83002', 'alexandracrestviewff', 15517, '2019-12-23', NULL, 'Great Yarmouth & Waveney CCG'),
	("Carole Brown Health Centre", 'D82044', '', 32254, '2020-01-06', NULL, 'West Norfolk CCG'),
	("Kirkley Mill Health Centre", 'D83030', 'kirkleymillff', 6311, '2019-11-28', NULL, 'Great Yarmouth & Waveney CCG'),
	("High Street Surgery", 'D83023', 'highstreetsurgerylowestoftff', 12742, '2020-02-17', NULL, 'Great Yarmouth & Waveney CCG'),
	("Chet Valley Medical Practice", 'D82006', 'chetvalleyff', 8697, '2020-03-09', NULL, 'South Norfolk CCG'),
	("The Bridge Street Surgery", 'D82015', 'bridgestreetff', 8704, '2020-03-30', NULL, 'West Norfolk CCG'),
	("Elmham Surgery", 'D82056', '', 9843, NULL, NULL, 'South Norfolk CCG'),
	("Southgates Medical Centre", 'D82099', '', 16996, '2020-06-01', NULL, 'West Norfolk CCG'),
	("The Woottons", 'D82618', '', 16996, '2020-06-01', NULL, 'West Norfolk CCG'),
	("Upwell Health Centre", 'D82035', '', 10446, NULL, NULL, 'West Norfolk CCG'),
	("Gt Massingham Surgery", 'D82070', '', 6458, NULL, NULL, 'West Norfolk CCG'),
	("Manor Farm Medical Centre", 'D82065', '', 7130, NULL, NULL, 'West Norfolk CCG'),
	("St Clements Surgery", 'D82105', '', 6173, NULL, NULL, 'West Norfolk CCG'),
	("The Cringleford Surgery", 'D82064', '', 20235, NULL, NULL, 'Norwich CCG'),
	("Waltington Medical Centre", 'D82043', '', 6684, NULL, NULL, 'West Norfolk CCG'),
	("Watton Medical Practice", 'D82063', '', 12693, NULL, NULL, 'South Norfolk CCG'),
	("Charlotte's GP Surgery", 'T0TEST', 'charlotter', 10000, '2019-10-24', NULL, 'Testshire CCG');
	
/*	
INSERT INTO charlotter_sp_dim_category(category, sub_category, link) VALUES ("Test category", "Test subcategory", "Test link");
INSERT INTO charlotter_sp_fact_clicks (category_id, calendar_id, practice_id, time_clicked, user_id) 
		VALUES (
			(SELECT cat.category_id FROM charlotter_sp_dim_category cat WHERE cat.link = "Test link"),
			(SELECT cal.calendar_id FROM charlotter_sp_dim_calendar cal WHERE cal.day_num = 30 && cal.month_num = 10 && cal.year_num = 2019),
			(SELECT p.practice_id FROM charlotter_sp_dim_practice p WHERE p.sp_shortcode = "charlotter"),
			NOW(), "TESTUSER");
			*/