DROP TABLE IF EXISTS charlotter_sp_dim_category;
DROP TABLE IF EXISTS charlotter_sp_dim_forms;
DROP TABLE IF EXISTS charlotter_sp_fact_clicks;
DROP TABLE IF EXISTS charlotter_sp_fact_exits;
DROP TABLE IF EXISTS charlotter_sp_fact_abandoned;

CREATE TABLE charlotter_sp_dim_category(category_id INT AUTO_INCREMENT PRIMARY KEY, category VARCHAR(50), sub_category VARCHAR(100), link VARCHAR(200));

CREATE TABLE charlotter_sp_dim_forms(form_id INT AUTO_INCREMENT PRIMARY KEY, form_name VARCHAR(200));

CREATE TABLE charlotter_sp_fact_clicks(click_id INT AUTO_INCREMENT PRIMARY KEY, category_id INT, calendar_id INT, time_clicked TIMESTAMP, user_id VARCHAR(50) NOT NULL);

CREATE TABLE charlotter_sp_fact_exits(exit_id INT AUTO_INCREMENT PRIMARY KEY, page VARCHAR(200), calendar_id INT, time_exited TIMESTAMP, user_id VARCHAR(50) NOT NULL);

CREATE TABLE charlotter_sp_fact_abandoned(abandoned_id INT AUTO_INCREMENT PRIMARY KEY, form_id INT, calendar_id INT, time_abandoned TIMESTAMP, user_id VARCHAR(50) NOT NULL);