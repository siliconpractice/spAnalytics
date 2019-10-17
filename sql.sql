CREATE TABLE charlotter_sp_analytics(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, whenrecorded datetime, url VARCHAR(50), type VARCHAR(10),  linktext VARCHAR(50), domain VARCHAR(100) NOT NULL, sessionid VARCHAR(29) NOT NULL, returninguser TINYINT(1) NOT NULL, parent VARCHAR(100));

DROP TABLE charlotter_sp_analytics;

TRUNCATE TABLE charlotter_sp_analytics

/* category e.g. digital-practice , practice-information */

/* subcat e.g. health review and assessment clinic, prescriptions and medicines centre, practice boundary,*/

CREATE TABLE charlotter_an_dim_category(category_id INT AUTO_INCREMENT PRIMARY KEY, category VARCHAR(50), sub_category VARCHAR(100), link VARCHAR(200));

CREATE TABLE charlotter_an_dim_calendar(calendar_id INT AUTO_INCREMENT PRIMARY KEY, day_num INT, month_num INT, year_num INT);

CREATE TABLE charlotter_an_dim_practice(practice_id INT AUTO_INCREMENT PRIMARY KEY, practice_name VARCHAR(200), practice_code VARCHAR(10), list_size INT, start_date DATE, federation VARCHAR(200), ccg VARCHAR(200), stp VARCHAR(200));

CREATE TABLE charlotter_an_fact_clicks(category_id INT, calendar_id INT , practice_id INT, time_clicked TIMESTAMP,  user_id VARCHAR() NOT NULL);

INSERT INTO charlotter_an_dim_practice(practice_name, practice_code, list_size, start_date, federation, ccg, stp) VALUES ();