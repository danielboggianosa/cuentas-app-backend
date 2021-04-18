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
	 * Creación de las tablas necesarias para que funcione el plugin.
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

		/***********************************
				Table `cu_empresas`
		************************************/
		$sql = "CREATE TABLE IF NOT EXISTS `cu_empresas` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `nombre` VARCHAR(100) NULL DEFAULT NULL,
		  `notas` TEXT NULL DEFAULT NULL,
		  `image_url` TEXT NULL DEFAULT NULL,
		  PRIMARY KEY (`id`))
		ENGINE = InnoDB;";
		dbDelta( $sql );		
		
		/* ***********************************
		-- Table `cu_users_has_empresas`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_users_has_empresas` (
		  `user_id` BIGINT(20) UNSIGNED NOT NULL,
		  `empresa_id` INT NOT NULL,
		  PRIMARY KEY (`user_id`, `empresa_id`),
		  INDEX `fk_wp_users_has_cu_empresas_cu_empresas1_idx` (`empresa_id` ASC) ,
		  INDEX `fk_wp_users_has_cu_empresas_wp_users_idx` (`user_id` ASC) ,
		  CONSTRAINT `fk_wp_users_has_cu_empresas_wp_users`
			FOREIGN KEY (`user_id`)
			REFERENCES `wp_users` (`ID`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_wp_users_has_cu_empresas_cu_empresas1`
			FOREIGN KEY (`empresa_id`)
			REFERENCES `cu_empresas` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";
		dbDelta( $sql );		
		
		/* ***********************************
		-- Table `cu_cuentas`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_cuentas` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `nombre` VARCHAR(150) NULL DEFAULT NULL,
		  `banco` VARCHAR(150) NULL DEFAULT NULL,
		  `moneda` VARCHAR(20) NULL DEFAULT NULL,
		  `codigo` VARCHAR(150) NULL DEFAULT NULL,
		  `cci` VARCHAR(150) NULL DEFAULT NULL,
		  `notas` TEXT NULL DEFAULT NULL,
		  `imagen_url` TEXT NULL DEFAULT NULL,
		  `empresa_id` INT NOT NULL,
		  PRIMARY KEY (`id`),
		  INDEX `fk_cu_cuentas_cu_empresas1_idx` (`empresa_id` ASC) ,
		  CONSTRAINT `fk_cu_cuentas_cu_empresas1`
			FOREIGN KEY (`empresa_id`)
			REFERENCES `cu_empresas` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";
		dbDelta( $sql );		
		
		/* ***********************************
		-- Table `cu_categorias`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_categorias` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `nombre` VARCHAR(200) NOT NULL,
		  `cuenta_id` INT NOT NULL,
		  PRIMARY KEY (`id`),
		  INDEX `fk_cu_categorias_cu_cuentas1_idx` (`cuenta_id` ASC) ,
		  CONSTRAINT `fk_cu_categorias_cu_cuentas1`
			FOREIGN KEY (`cuenta_id`)
			REFERENCES `cu_cuentas` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";
		dbDelta( $sql );		
		
		/* ***********************************
		-- Table `cu_subcategorias`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_subcategorias` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `nombre` VARCHAR(200) NOT NULL,
		  `categoria_id` INT NOT NULL,
		  PRIMARY KEY (`id`),
		  INDEX `fk_cu_subcategorias_cu_categorias1_idx` (`categoria_id` ASC) ,
		  CONSTRAINT `fk_cu_subcategorias_cu_categorias1`
			FOREIGN KEY (`categoria_id`)
			REFERENCES `cu_categorias` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";
		dbDelta( $sql );		
		
		/* ***********************************
		-- Table `cu_registros`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_registros` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `fecha` DATE NULL DEFAULT NULL,
		  `ingreso` TINYINT NULL DEFAULT NULL,
		  `descripcion` TEXT NULL DEFAULT NULL,
		  `entidad` VARCHAR(200) NULL DEFAULT NULL,
		  `operacion` VARCHAR(200) NULL DEFAULT NULL,
		  `monto` DECIMAL(10,2) NULL DEFAULT NULL,
		  `imagen_url` TEXT NULL DEFAULT NULL,
		  `cuenta_id` INT NOT NULL,
		  `categoria_id` INT NOT NULL,
		  `subcategoria_id` INT NOT NULL,
		  PRIMARY KEY (`id`),
		  INDEX `fk_cu_registros_cu_cuentas1_idx` (`cuenta_id` ASC) ,
		  INDEX `fk_cu_registros_cu_categorias1_idx` (`categoria_id` ASC) ,
		  INDEX `fk_cu_registros_cu_subcategorias1_idx` (`subcategoria_id` ASC) ,
		  CONSTRAINT `fk_cu_registros_cu_cuentas1`
			FOREIGN KEY (`cuenta_id`)
			REFERENCES `cu_cuentas` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_cu_registros_cu_categorias1`
			FOREIGN KEY (`categoria_id`)
			REFERENCES `cu_categorias` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_cu_registros_cu_subcategorias1`
			FOREIGN KEY (`subcategoria_id`)
			REFERENCES `cu_subcategorias` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";
		dbDelta( $sql );		
		
		/* ***********************************
		-- Table `cu_roles`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_roles` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `nombre` VARCHAR(100) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) )
		ENGINE = InnoDB;";
		dbDelta( $sql );		
		
		/* ***********************************
		-- Table `cu_permisos`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_permisos` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `accion` VARCHAR(45) NOT NULL,
		  `objeto` VARCHAR(150) NOT NULL,
		  `propio` TINYINT NOT NULL,
		  `equipo` TINYINT NOT NULL,
		  `otros` TINYINT NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE INDEX `accion_UNIQUE` (`accion` ASC) )
		ENGINE = InnoDB;";
		dbDelta( $sql );
				
		/* ***********************************
		-- Table `cu_roles_has_permisos`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_roles_has_permisos` (
		  `rol_id` INT NOT NULL,
		  `permiso_id` INT NOT NULL,
		  PRIMARY KEY (`rol_id`, `permiso_id`),
		  INDEX `fk_cu_roles_has_cu_permisos_cu_permisos1_idx` (`permiso_id` ASC) ,
		  INDEX `fk_cu_roles_has_cu_permisos_cu_roles1_idx` (`rol_id` ASC) ,
		  CONSTRAINT `fk_cu_roles_has_cu_permisos_cu_roles1`
			FOREIGN KEY (`rol_id`)
			REFERENCES `cu_roles` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_cu_roles_has_cu_permisos_cu_permisos1`
			FOREIGN KEY (`permiso_id`)
			REFERENCES `cu_permisos` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";
		dbDelta( $sql );
				
		/* ***********************************
		-- Table `cu_users_has_roles`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_users_has_roles` (
		  `user_id` BIGINT(20) UNSIGNED NOT NULL,
		  `rol_id` INT NOT NULL,
		  PRIMARY KEY (`user_id`, `rol_id`),
		  INDEX `fk_wp_users_has_cu_roles_cu_roles1_idx` (`rol_id` ASC) ,
		  INDEX `fk_wp_users_has_cu_roles_wp_users1_idx` (`user_id` ASC) ,
		  CONSTRAINT `fk_wp_users_has_cu_roles_wp_users1`
			FOREIGN KEY (`user_id`)
			REFERENCES `wp_users` (`ID`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_wp_users_has_cu_roles_cu_roles1`
			FOREIGN KEY (`rol_id`)
			REFERENCES `cu_roles` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";
		dbDelta( $sql );		
		
		/* ***********************************
		-- Table `cu_presupuestos`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_presupuestos` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `nombre` VARCHAR(150) NULL DEFAULT NULL,
		  `inicio` DATE NULL DEFAULT NULL,
		  `fin` DATE NULL DEFAULT NULL,
		  `empresa_id` INT NOT NULL,
		  `cuenta_id` INT NULL DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  INDEX `fk_cu_presupuestos_cu_empresas1_idx` (`empresa_id` ASC) ,
		  INDEX `fk_cu_presupuestos_cu_cuentas1_idx` (`cuenta_id` ASC) ,
		  CONSTRAINT `fk_cu_presupuestos_cu_empresas1`
			FOREIGN KEY (`empresa_id`)
			REFERENCES `cu_empresas` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_cu_presupuestos_cu_cuentas1`
			FOREIGN KEY (`cuenta_id`)
			REFERENCES `cu_cuentas` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";
		dbDelta( $sql );		
		
		/* ***********************************
		-- Table `cu_presupuestos_has_categorias`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_presupuestos_has_categorias` (
		  `presupuesto_id` INT NOT NULL,
		  `categoria_id` INT NOT NULL,
		  `meta` DECIMAL(10,2) NULL DEFAULT NULL,
		  `gasto` DECIMAL(10,2) NULL DEFAULT NULL,
		  `diferencia` DECIMAL(10,2) NULL DEFAULT NULL,
		  PRIMARY KEY (`presupuesto_id`, `categoria_id`),
		  INDEX `fk_cu_presupuestos_has_cu_categorias_cu_categorias1_idx` (`categoria_id` ASC) ,
		  INDEX `fk_cu_presupuestos_has_cu_categorias_cu_presupuestos1_idx` (`presupuesto_id` ASC) ,
		  CONSTRAINT `fk_cu_presupuestos_has_cu_categorias_cu_presupuestos1`
			FOREIGN KEY (`presupuesto_id`)
			REFERENCES `cu_presupuestos` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_cu_presupuestos_has_cu_categorias_cu_categorias1`
			FOREIGN KEY (`categoria_id`)
			REFERENCES `cu_categorias` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";
		dbDelta( $sql );		
		
		/* ***********************************
		-- Table `cu_presupuestos_has_categorias_has_subcategorias`
		*********************************** */
		$sql = "CREATE TABLE IF NOT EXISTS `cu_presupuestos_has_categorias_has_subcategorias` (
		  `presupuesto_id` INT NOT NULL,
		  `categoria_id` INT NOT NULL,
		  `subcategoria_id` INT NOT NULL,
		  `meta` DECIMAL(10,2) NOT NULL,
		  `gasto` DECIMAL(10,2) NULL DEFAULT NULL,
		  `diferencia` DECIMAL(10,2) NULL DEFAULT NULL,
		  PRIMARY KEY (`presupuesto_id`, `categoria_id`, `subcategoria_id`),
		  INDEX `fk_cu_presupuestos_has_categorias_has_cu_subcategorias_cu_s_idx` (`subcategoria_id` ASC) ,
		  INDEX `fk_cu_presupuestos_has_categorias_has_cu_subcategorias_cu_p_idx` (`presupuesto_id` ASC, `categoria_id` ASC) ,
		  CONSTRAINT `fk_cu_presupuestos_has_categorias_has_cu_subcategorias_cu_pre1`
			FOREIGN KEY (`presupuesto_id` , `categoria_id`)
			REFERENCES `cu_presupuestos_has_categorias` (`presupuesto_id` , `categoria_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_cu_presupuestos_has_categorias_has_cu_subcategorias_cu_sub1`
			FOREIGN KEY (`subcategoria_id`)
			REFERENCES `cu_subcategorias` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;		
		
		SET SQL_MODE=@OLD_SQL_MODE;
		SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
		SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;";
		dbDelta( $sql );
	}

}
