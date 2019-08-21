CREATE TABLE {$prefix}sp_analytics(id INT NOT NULL AUTO-INCREMENT PRIMARY KEY, when datetime, url VARCHAR(100), text VARCHAR(100), domain VARCHAR(100) NOT NULL);

ALTER TABLE charlotter_sp_analytics 
ADD returninguser TINYINT(1) NOT NULL,
ADD sessionid VARCHAR(29) NOT NULL;