CREATE TABLE charlotter_sp_analytics(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, whenrecorded datetime, url VARCHAR(50), type VARCHAR(10),  linktext VARCHAR(50), domain VARCHAR(100) NOT NULL, sessionid VARCHAR(29) NOT NULL, returninguser TINYINT(1) NOT NULL);

DROP TABLE charlotter_sp_analytics;