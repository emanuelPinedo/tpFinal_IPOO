CREATE DATABASE bdviajes; 

CREATE TABLE empresa(
    idempresa bigint AUTO_INCREMENT,
    enombre varchar(150),
    edireccion varchar(150),
    PRIMARY KEY (idempresa)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE persona (
    nombre varchar(150),
    apellido varchar(150),
    documento int(15),
    PRIMARY KEY (documento)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE responsable (
    rnumeroempleado bigint UNIQUE,
    rnumerolicencia bigint UNIQUE,
	rdocumento int(15),
    PRIMARY KEY (rdocumento),
    FOREIGN KEY (rdocumento) REFERENCES persona (documento)
    ON UPDATE CASCADE
    ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
CREATE TABLE viaje (
    idviaje bigint AUTO_INCREMENT, /*codigo de viaje*/
	vdestino varchar(150),
    vcantmaxpasajeros int,
	idempresa bigint,
    rdocumento int (15),
    vimporte float,
    PRIMARY KEY (idviaje),
    FOREIGN KEY (idempresa) REFERENCES empresa (idempresa),
	FOREIGN KEY (rdocumento) REFERENCES responsable (rdocumento)
    ON UPDATE CASCADE
    ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT = 1;
	
CREATE TABLE pasajero (
    pdocumento int(15),
	ptelefono int, 
	idviaje bigint,
    PRIMARY KEY (pdocumento),
    FOREIGN KEY (pdocumento) REFERENCES persona (documento)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
	FOREIGN KEY (idviaje) REFERENCES viaje (idviaje)	
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;