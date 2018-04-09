drop table appuser cascade;
drop table restaurants cascade;
drop table dailyelo cascade;

create table appuser (
    id varchar(50) primary key,
    password varchar(50),
    votes int DEFAULT 0 CHECK (votes >= 0),
    fav_restaurant varchar(50) DEFAULT '',
    phone varchar(10) DEFAULT '',
    gender int DEFAULT 0 CHECK (gender >= 0 AND gender <= 2),
    bike int DEFAULT 0 CHECK (bike >= 0 AND bike <= 1),
    car int DEFAULT 0 CHECK (car >= 0 AND car <= 1),
    bus int DEFAULT 0 CHECK (bus >= 0 AND bus <= 1),
    walk int DEFAULT 0 CHECK (walk >= 0 AND walk <= 1),
    strut int DEFAULT 0 CHECK (strut >= 0 AND strut <= 1)
);

create table restaurants (
    name varchar(50) primary key,
    elo int DEFAULT 1200,
      win int DEFAULT 0,
      lost int DEFAULT 0,
      draw int DEFAULT 0
);

create table dailyelo (
    name varchar(50) primary key,
    elochange int DEFAULT 0,
    today date DEFAULT '2018-01-01'
);

insert into appuser values('auser', 'apassword', 0, '', '', 0, 0, 0, 0, 0, 0);

\COPY restaurants(name) FROM 'restaurants.txt'
\COPY dailyelo(name) FROM 'restaurants.txt'
