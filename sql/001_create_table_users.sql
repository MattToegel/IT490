CREATE TABLE IF NOT EXISTS `Users` (
    `id` INT NOT NULL AUTO_INCREMENT
    ,`fname` VARCHAR(60) NOT NULL
    ,`lname` VARCHAR(60) NOT NULL
    ,`email` VARCHAR(100) NOT NULL
    ,`username` VARCHAR(60) NOT NULL
    ,`bday` DATE NOT NULL
    ,`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ,`modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ,`is_active` TINYINT(1)
    ,`password` VARCHAR(60) NOT NULL
    ,PRIMARY KEY(`id`)
    ,UNIQUE (`email`)
    ,UNIQUE (`username`)
)