<?php

/**
 * Fired during plugin activation
 *
 * @link       www.danielboggiano.com
 * @since      1.0.0
 *
 * @package    Cuentas
 * @subpackage Cuentas/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cuentas
 * @subpackage Cuentas/includes
 * @author     Daniel Boggiano <dev@danielboggiano.com>
 */
class Cuentas_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		global $mm_db_version;

		$charset_collate = $wpdb->get_charset_collate();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql_1 = "
		SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
		SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
		SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


		-- -----------------------------------------------------
		-- Table `mm_empresa_usuario` EMPRESA USUARIOS
		-- -----------------------------------------------------
		CREATE TABLE IF NOT EXISTS `mm_empresa_usuario` (
		
		`mm_empresa_usuario_id` INT NOT NULL AUTO_INCREMENT,
		`mm_empresa_id` INT NOT NULL,
		`mm_usuario_id` INT NOT NULL,
		
		PRIMARY KEY (`mm_empresa_usuario_id`)
		)
		
		$charset_collate
		ENGINE = InnoDB;";
		dbDelta( $sql_1 );
		
		$sql_2 = "
		-- -----------------------------------------------------
		-- Table `mm_empresa` EMPRESA
		-- -----------------------------------------------------
		CREATE TABLE IF NOT EXISTS `mm_empresa` (
		
		`mm_empresa_id` INT NOT NULL AUTO_INCREMENT,
		`mm_empresa_nombre` VARCHAR(150) NULL,
		`mm_empresa_notas` TEXT NULL,
		`mm_empresa_foto` VARCHAR(200) NULL,
		
		PRIMARY KEY (`mm_empresa_id`)
		)
		
		$charset_collate
		ENGINE = InnoDB;";
		dbDelta( $sql_2 );
		
		$sql_3 = "
		-- -----------------------------------------------------
		-- Table `mm_cuenta` CUENTA
		-- -----------------------------------------------------
		CREATE TABLE IF NOT EXISTS `mm_cuenta` (
		
		`mm_cuenta_id` INT NOT NULL AUTO_INCREMENT,
		`mm_cuenta_empresa_id` INT NOT NULL,
		`mm_cuenta_nombre` VARCHAR(150) NULL,
		`mm_cuenta_banco` VARCHAR(150) NULL,
		`mm_cuenta_moneda` VARCHAR(150) NULL,
		`mm_cuenta_numero` VARCHAR(150) NULL,
		`mm_cuenta_cci` VARCHAR(150) NULL,
		`mm_cuenta_notas` TEXT NULL,
		`mm_cuenta_foto` VARCHAR(200) NULL,
		
		PRIMARY KEY (`mm_cuenta_id`)
		)
		
		$charset_collate
		ENGINE = InnoDB;";
		dbDelta( $sql_3 );
		
		$sql_4 = "
		-- -----------------------------------------------------
		-- Table `mm_registro` REGISTRO
		-- -----------------------------------------------------
		CREATE TABLE IF NOT EXISTS `mm_registro` (
		
		`mm_registro_id` INT NOT NULL AUTO_INCREMENT,
		`mm_registro_cuenta_id` INT NOT NULL,
		`mm_registro_fecha` DATE NOT NULL,
		`mm_registro_ingreso` BOOLEAN NOT NULL,
		`mm_registro_categoria_id` INT NOT NULL,
		`mm_registro_subcategoria_id` INT NOT NULL,
		`mm_registro_descripcion` TEXT NULL,
		`mm_registro_entidad` VARCHAR(200) NULL,
		`mm_registro_operacion` VARCHAR(200) NULL,
		`mm_registro_moneda` VARCHAR(5) NULL,
		`mm_registro_monto` DECIMAL(10,2) NULL,
		`mm_registro_foto` VARCHAR(200) NULL,
		
		PRIMARY KEY (`mm_registro_id`)
		)
		
		$charset_collate
		ENGINE = InnoDB;";
		dbDelta( $sql_4 );
		
		$sql_5 = "
		-- -----------------------------------------------------
		-- Table `mm_categoria` CATEGORIA
		-- -----------------------------------------------------
		CREATE TABLE IF NOT EXISTS `mm_categoria` (
		
		`mm_categoria_id` INT NOT NULL AUTO_INCREMENT,
		`mm_categoria_cuenta_id` INT NOT NULL,
		`mm_categoria_nombre` VARCHAR(200) NOT NULL,
		
		PRIMARY KEY (`mm_categoria_id`)
		)
		
		$charset_collate
		ENGINE = InnoDB;";
		dbDelta( $sql_5 );
		
		$sql_6 = "
		-- -----------------------------------------------------
		-- Table `mm_subcategoria` SUBCATEGORIA
		-- -----------------------------------------------------
		CREATE TABLE IF NOT EXISTS `mm_subcategoria` (
		
		`mm_subcategoria_id` INT NOT NULL AUTO_INCREMENT,
		`mm_subcategoria_categoria_id` INT NOT NULL,
		`mm_subcategoria_nombre` VARCHAR(200) NOT NULL,
		
		PRIMARY KEY (`mm_subcategoria_id`)
		)
		
		$charset_collate
		ENGINE = InnoDB;";
		dbDelta( $sql_6 );
	}

}
