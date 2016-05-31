CREATE  TABLE IF NOT EXISTS `elementos` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(255) NULL ,
  `orden` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `orden_UNIQUE` (`orden` ASC) )
ENGINE = InnoDB;
INSERT INTO  `elementos` (`id`, `nombre`, `orden`)
VALUES 
	(NULL ,  'Comprar pasteles',  '1'), 
	(NULL ,  'Comprar rosas',  '2'), 
	(NULL ,  'Comprar bombones',  '3'), 
	(NULL ,  'Comprar peluches',  '4'), 
	(NULL ,  'Comprar tarjeta',  '5');