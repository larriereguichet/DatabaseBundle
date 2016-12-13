CREATE DATABASE lag_database_bundle_test;

USE lag_database_bundle_test;

CREATE TABLE entity (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO entity (name)
VALUES ('test'),
  ('lol'),
  ('ours');
