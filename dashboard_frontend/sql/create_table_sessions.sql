CREATE TABLE IF NOT EXISTS sessions (
   id varchar(32) NOT NULL,
   access int(10) unsigned DEFAULT NULL,
   data text,
   PRIMARY KEY (id)
);