-- Generado por Oracle SQL Developer Data Modeler 21.2.0.183.1957
--   en:        2023-05-07 20:08:11 CEST
--   sitio:      Oracle Database 11g
--   tipo:      Oracle Database 11g



-- predefined type, no DDL - MDSYS.SDO_GEOMETRY

-- predefined type, no DDL - XMLTYPE

CREATE TABLE abogado (
    numero_colegiado   INTEGER NOT NULL,
    nombre             VARCHAR2(20),
    partidojudicial_id INTEGER NOT NULL,
    latitud            FLOAT,
    longitiud          FLOAT,
    pass               VARCHAR2(20)
);

ALTER TABLE abogado ADD CONSTRAINT abogado_pk PRIMARY KEY ( numero_colegiado );

CREATE TABLE amigo (
    id                       INTEGER NOT NULL,
    abogado_numero_colegiado INTEGER
);

ALTER TABLE amigo ADD CONSTRAINT amigo_pk PRIMARY KEY ( id );

CREATE TABLE asistencia (
    id                       INTEGER NOT NULL,
    fecha_inicio             DATE,
    fecha_fin                DATE,
    abogado_numero_colegiado INTEGER NOT NULL,
    usuario_id               INTEGER NOT NULL
);

ALTER TABLE asistencia ADD CONSTRAINT asistencia_pk PRIMARY KEY ( id );

CREATE TABLE partidojudicial (
    id     INTEGER NOT NULL,
    nombre VARCHAR2(50)
);

ALTER TABLE partidojudicial ADD CONSTRAINT partidojudicial_pk PRIMARY KEY ( id );

CREATE TABLE telefono (
    numero                   INTEGER NOT NULL,
    abogado_numero_colegiado INTEGER NOT NULL
);

ALTER TABLE telefono ADD CONSTRAINT telefono_pk PRIMARY KEY ( numero );

CREATE TABLE usuario (
    id     INTEGER NOT NULL,
    nombre VARCHAR2(20),
    pass   VARCHAR2 
--  ERROR: VARCHAR2 size not specified 

);

ALTER TABLE usuario ADD CONSTRAINT usuario_pk PRIMARY KEY ( id );

ALTER TABLE abogado
    ADD CONSTRAINT abogado_partidojudicial_fk FOREIGN KEY ( partidojudicial_id )
        REFERENCES partidojudicial ( id );

ALTER TABLE amigo
    ADD CONSTRAINT amigo_abogado_fk FOREIGN KEY ( abogado_numero_colegiado )
        REFERENCES abogado ( numero_colegiado );

ALTER TABLE asistencia
    ADD CONSTRAINT asistencia_abogado_fk FOREIGN KEY ( abogado_numero_colegiado )
        REFERENCES abogado ( numero_colegiado );

ALTER TABLE asistencia
    ADD CONSTRAINT asistencia_usuario_fk FOREIGN KEY ( usuario_id )
        REFERENCES usuario ( id );

ALTER TABLE telefono
    ADD CONSTRAINT telefono_abogado_fk FOREIGN KEY ( abogado_numero_colegiado )
        REFERENCES abogado ( numero_colegiado );



-- Informe de Resumen de Oracle SQL Developer Data Modeler: 
-- 
-- CREATE TABLE                             6
-- CREATE INDEX                             0
-- ALTER TABLE                             11
-- CREATE VIEW                              0
-- ALTER VIEW                               0
-- CREATE PACKAGE                           0
-- CREATE PACKAGE BODY                      0
-- CREATE PROCEDURE                         0
-- CREATE FUNCTION                          0
-- CREATE TRIGGER                           0
-- ALTER TRIGGER                            0
-- CREATE COLLECTION TYPE                   0
-- CREATE STRUCTURED TYPE                   0
-- CREATE STRUCTURED TYPE BODY              0
-- CREATE CLUSTER                           0
-- CREATE CONTEXT                           0
-- CREATE DATABASE                          0
-- CREATE DIMENSION                         0
-- CREATE DIRECTORY                         0
-- CREATE DISK GROUP                        0
-- CREATE ROLE                              0
-- CREATE ROLLBACK SEGMENT                  0
-- CREATE SEQUENCE                          0
-- CREATE MATERIALIZED VIEW                 0
-- CREATE MATERIALIZED VIEW LOG             0
-- CREATE SYNONYM                           0
-- CREATE TABLESPACE                        0
-- CREATE USER                              0
-- 
-- DROP TABLESPACE                          0
-- DROP DATABASE                            0
-- 
-- REDACTION POLICY                         0
-- 
-- ORDS DROP SCHEMA                         0
-- ORDS ENABLE SCHEMA                       0
-- ORDS ENABLE OBJECT                       0
-- 
-- ERRORS                                   1
-- WARNINGS                                 0
