<?php
	
	$installer = $this;
	$installer->startSetup();

	$installer->run
	("
		DROP TABLE IF EXISTS `query_sales_customer_ranking`;
		CREATE TABLE `query_sales_customer_ranking`
		(
			`ranking_id` int(11) NOT NULL AUTO_INCREMENT,
			`customer_id` int(11) UNIQUE NOT NULL,
			`customer_name` VARCHAR(255) DEFAULT NULL,
			`number_of_orders` int(11) NOT NULL,
			`last_order_date` datetime DEFAULT NULL,
			`last_promotional_email_date` datetime DEFAULT NULL,
			`total_value_spent` DOUBLE NOT NULL DEFAULT 0,
			PRIMARY KEY(`ranking_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;

	");	

	$installer->endSetup();


