-- db_klinik: struktur database Klinik (rawat jalan) + 1 user admin.
-- Kompatibel MySQL 8 / MariaDB 10.x. Login awal: code = admin, password = admin123 (segera ganti).
CREATE DATABASE IF NOT EXISTS `db_klinik` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_klinik`;
SET FOREIGN_KEY_CHECKS=0;
SET NAMES utf8mb4;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bhp_usage_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bhp_usage_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_name` varchar(200) NOT NULL,
  `qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(30) DEFAULT NULL,
  `price` decimal(14,2) NOT NULL DEFAULT 0.00,
  `disc_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `ppn_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `ppn_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `expired_date` date DEFAULT NULL,
  `usage_rule` varchar(200) DEFAULT NULL,
  `shelf_no` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_usage` (`bhp_usage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bhp_usages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usage_no` varchar(30) NOT NULL,
  `usage_date` date NOT NULL,
  `usage_time` time DEFAULT NULL,
  `depo` varchar(100) DEFAULT NULL,
  `queue_no` int(11) NOT NULL DEFAULT 0,
  `buyer_type` varchar(10) NOT NULL DEFAULT 'pasien',
  `unit_name` varchar(100) DEFAULT NULL,
  `registration_id` int(10) unsigned DEFAULT NULL,
  `registration_no` varchar(40) DEFAULT NULL,
  `medical_record_no` varchar(30) DEFAULT NULL,
  `patient_name` varchar(150) DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `class_name` varchar(50) DEFAULT NULL,
  `doctor_name` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subtotal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `discount_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `tax_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `is_create` tinyint(1) NOT NULL DEFAULT 1,
  `is_update` tinyint(1) NOT NULL DEFAULT 0,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` char(1) NOT NULL DEFAULT 'Y',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usage_no` (`usage_no`),
  KEY `idx_usage_date` (`usage_date`),
  KEY `idx_registration` (`registration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_address` varchar(255) DEFAULT NULL,
  `business_fields` varchar(255) DEFAULT NULL,
  `company_telp` varchar(255) DEFAULT NULL,
  `company_fax` varchar(255) DEFAULT NULL,
  `company_npwp` varchar(255) DEFAULT NULL,
  `company_director` varchar(255) DEFAULT NULL,
  `company_director_1` varchar(255) DEFAULT NULL,
  `sign_title_1` varchar(255) DEFAULT NULL,
  `company_director_2` varchar(255) DEFAULT NULL,
  `sign_title_2` varchar(255) DEFAULT NULL,
  `company_director_3` varchar(255) DEFAULT NULL,
  `sign_title_3` varchar(255) DEFAULT NULL,
  `company_director_4` varchar(255) DEFAULT NULL,
  `sign_title_4` varchar(255) DEFAULT NULL,
  `warehouse_address` varchar(255) DEFAULT NULL,
  `warehouse_telp` varchar(255) DEFAULT NULL,
  `warehouse_fax` varchar(255) DEFAULT NULL,
  `manifest_number` varchar(255) DEFAULT NULL,
  `invoice_code` varchar(255) DEFAULT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `invoice_number_include` varchar(255) DEFAULT NULL,
  `invoice_evidence_email` varchar(255) DEFAULT NULL,
  `faktur_pajak_number` varchar(255) DEFAULT NULL,
  `berita_acara_number` varchar(255) DEFAULT NULL,
  `permit_number` varchar(255) DEFAULT NULL,
  `offer_number` varchar(255) DEFAULT NULL,
  `offer_year` varchar(255) DEFAULT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `norek` varchar(255) DEFAULT NULL,
  `an` varchar(255) DEFAULT NULL,
  `bank_name_1` varchar(255) DEFAULT NULL,
  `bank_norek_1` varchar(255) DEFAULT NULL,
  `bank_an_1` varchar(255) DEFAULT NULL,
  `bank_name_2` varchar(255) DEFAULT NULL,
  `bank_norek_2` varchar(255) DEFAULT NULL,
  `bank_an_2` varchar(255) DEFAULT NULL,
  `bank_name_3` varchar(255) DEFAULT NULL,
  `bank_norek_3` varchar(255) DEFAULT NULL,
  `bank_an_3` varchar(255) DEFAULT NULL,
  `bank_name_4` varchar(255) DEFAULT NULL,
  `bank_norek_4` varchar(255) DEFAULT NULL,
  `bank_an_4` varchar(255) DEFAULT NULL,
  `bank_name_5` varchar(255) DEFAULT NULL,
  `bank_norek_5` varchar(255) DEFAULT NULL,
  `bank_an_5` varchar(255) DEFAULT NULL,
  `va_bank_an` varchar(255) DEFAULT NULL,
  `va_bank_name` varchar(255) DEFAULT NULL,
  `va_bank_branch` varchar(255) DEFAULT NULL,
  `va_bank_norek` varchar(255) DEFAULT NULL,
  `wa_invoice` varchar(255) DEFAULT NULL,
  `city_invoice` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `emergency_call` varchar(255) DEFAULT NULL,
  `offer_perihal` varchar(255) DEFAULT NULL,
  `offer_description` text DEFAULT NULL,
  `offer_info` varchar(255) DEFAULT NULL,
  `manifest_type` varchar(50) DEFAULT NULL,
  `manifest_code` varchar(50) DEFAULT NULL,
  `offer_code` varchar(100) DEFAULT NULL,
  `sign_1` varchar(100) DEFAULT NULL,
  `sign_2` varchar(100) DEFAULT NULL,
  `sign_3` varchar(100) DEFAULT NULL,
  `sign_4` varchar(100) DEFAULT NULL,
  `mou_code` varchar(100) DEFAULT NULL,
  `transporter_id` int(11) DEFAULT NULL,
  `header` varchar(100) DEFAULT NULL,
  `footer` varchar(100) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `is_create` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `is_update` varchar(50) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `is_delete` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `is_active` enum('Y','N') DEFAULT 'Y',
  `is_log` text DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctor_schedule_quota_histories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_schedule_id` int(11) NOT NULL,
  `cycle_date` date NOT NULL COMMENT 'Tanggal praktik pada minggu tsb (mis. Senin 20-07-2026)',
  `quota` int(11) NOT NULL DEFAULT 0,
  `quota_used` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_schedule_cycle` (`doctor_schedule_id`,`cycle_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctor_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL DEFAULT '0',
  `doctor_id` int(11) NOT NULL,
  `day` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `quota` int(11) DEFAULT 0,
  `estimated_minutes` int(11) DEFAULT NULL,
  `quota_used` int(11) DEFAULT NULL,
  `quota_reset_at` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_create` int(11) DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') DEFAULT 'Y',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL DEFAULT '',
  `region` varchar(100) DEFAULT NULL,
  `nik_login` varchar(50) DEFAULT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `doctor_name` varchar(255) NOT NULL DEFAULT '',
  `specialist` varchar(255) DEFAULT NULL,
  `polyclinic_name` varchar(255) DEFAULT NULL,
  `religion` varchar(255) DEFAULT NULL,
  `sip_number` varchar(100) DEFAULT NULL,
  `str_number` varchar(100) DEFAULT NULL,
  `str_issue_date` date DEFAULT NULL,
  `str_expiry_date` varchar(50) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_doctors_code` (`code`),
  KEY `idx_doctors_nik` (`nik`),
  KEY `idx_doctors_nik_login` (`nik_login`),
  KEY `idx_doctors_doctor_name` (`doctor_name`),
  KEY `idx_doctors_polyclinic_name` (`polyclinic_name`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `health_facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL DEFAULT '0',
  `code_int` varchar(50) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `code_ppk` varchar(50) DEFAULT '',
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `insurances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL DEFAULT '0',
  `region` varchar(50) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `bank_account_number` varchar(50) NOT NULL,
  `bank_account_name` varchar(50) NOT NULL,
  `phone_number_rs` varchar(50) NOT NULL,
  `email_rs` varchar(50) NOT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_request_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_request_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_name` varchar(200) NOT NULL,
  `qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(30) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_request` (`item_request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_no` varchar(30) NOT NULL,
  `request_date` date NOT NULL,
  `from_depo` varchar(100) NOT NULL,
  `to_depo` varchar(100) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'diajukan',
  `is_create` tinyint(1) NOT NULL DEFAULT 1,
  `is_update` tinyint(1) NOT NULL DEFAULT 0,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` char(1) NOT NULL DEFAULT 'Y',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_request_no` (`request_no`),
  KEY `idx_request_date` (`request_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_examination_results` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lab_examination_id` int(10) unsigned NOT NULL,
  `parameter_name` varchar(150) NOT NULL,
  `result` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `reference_range` varchar(100) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lab_results_exam` (`lab_examination_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_examinations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lab_no` varchar(30) NOT NULL,
  `registration_id` int(10) unsigned DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `patient_id` int(10) unsigned DEFAULT NULL,
  `medical_record_no` varchar(30) DEFAULT NULL,
  `patient_name` varchar(150) NOT NULL,
  `examination_name` varchar(255) NOT NULL,
  `patient_type` varchar(50) DEFAULT NULL,
  `sender_doctor` varchar(150) DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `status` enum('received','processing','done') NOT NULL DEFAULT 'received',
  `integration_status` tinyint(1) NOT NULL DEFAULT 0,
  `is_create` tinyint(1) NOT NULL DEFAULT 1,
  `is_update` tinyint(1) NOT NULL DEFAULT 0,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` char(1) NOT NULL DEFAULT 'Y',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lab_no` (`lab_no`),
  KEY `idx_received_at` (`received_at`),
  KEY `idx_rm` (`medical_record_no`),
  KEY `idx_registration` (`registration_id`),
  KEY `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_internal_memos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `registration_no` varchar(50) DEFAULT NULL,
  `medical_record_no` varchar(50) DEFAULT NULL,
  `patient_name` varchar(150) DEFAULT NULL,
  `memo` text NOT NULL,
  `memo_date` datetime NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lab_memos_date` (`memo_date`),
  KEY `idx_lab_memos_rm` (`medical_record_no`),
  KEY `idx_lab_memos_noreg` (`registration_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_normal_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `master_laboratory_id` int(10) unsigned NOT NULL,
  `test_name` varchar(150) NOT NULL,
  `normal_result` varchar(255) DEFAULT NULL,
  `is_active` char(1) NOT NULL DEFAULT 'Y',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_master_lab_tpl` (`master_laboratory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_normal_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `master_laboratory_id` int(10) unsigned NOT NULL,
  `machine_code` varchar(50) DEFAULT NULL,
  `parameter_name` varchar(150) NOT NULL,
  `unit` varchar(30) DEFAULT NULL,
  `general_min` decimal(12,3) DEFAULT NULL,
  `general_max` decimal(12,3) DEFAULT NULL,
  `male_min` decimal(12,3) DEFAULT NULL,
  `male_max` decimal(12,3) DEFAULT NULL,
  `female_min` decimal(12,3) DEFAULT NULL,
  `female_max` decimal(12,3) DEFAULT NULL,
  `child_min` decimal(12,3) DEFAULT NULL,
  `child_max` decimal(12,3) DEFAULT NULL,
  `text_male` varchar(150) DEFAULT NULL,
  `text_female` varchar(150) DEFAULT NULL,
  `decimals` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_create` tinyint(1) NOT NULL DEFAULT 1,
  `is_update` tinyint(1) NOT NULL DEFAULT 0,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` char(1) NOT NULL DEFAULT 'Y',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_master_lab` (`master_laboratory_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_requisition_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lab_requisition_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_name` varchar(200) NOT NULL,
  `qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(30) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_requisition_items` (`lab_requisition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_requisitions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_no` varchar(30) NOT NULL,
  `request_date` date NOT NULL,
  `from_dept` varchar(100) NOT NULL,
  `to_dept` varchar(100) NOT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'diajukan',
  `item_count` int(11) NOT NULL DEFAULT 0,
  `is_active` char(1) NOT NULL DEFAULT 'Y',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_requisition_no` (`request_no`),
  KEY `idx_requisition_date` (`request_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_usage_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lab_usage_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_name` varchar(200) NOT NULL,
  `qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(30) DEFAULT NULL,
  `price` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total` decimal(14,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_usage` (`lab_usage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_usages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usage_no` varchar(30) NOT NULL,
  `usage_date` date NOT NULL,
  `warehouse` varchar(100) DEFAULT NULL,
  `cost_centre` varchar(100) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `charge_to_patient` tinyint(1) NOT NULL DEFAULT 0,
  `registration_id` int(10) unsigned DEFAULT NULL,
  `registration_no` varchar(40) DEFAULT NULL,
  `medical_record_no` varchar(30) DEFAULT NULL,
  `patient_name` varchar(150) DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `grand_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `is_create` tinyint(1) NOT NULL DEFAULT 1,
  `is_update` tinyint(1) NOT NULL DEFAULT 0,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` char(1) NOT NULL DEFAULT 'Y',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usage_no` (`usage_no`),
  KEY `idx_usage_date` (`usage_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `logistic_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `unit` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_item_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `logistic_stocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_code` varchar(30) NOT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `item_code` varchar(50) NOT NULL,
  `item_name` varchar(200) NOT NULL,
  `unit` varchar(30) DEFAULT NULL,
  `last_so_date` date DEFAULT NULL,
  `so_qty` decimal(14,2) NOT NULL DEFAULT 0.00,
  `adjustment` decimal(14,2) NOT NULL DEFAULT 0.00,
  `received` decimal(14,2) NOT NULL DEFAULT 0.00,
  `issued` decimal(14,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(14,2) NOT NULL DEFAULT 0.00,
  `min_stock` decimal(14,2) NOT NULL DEFAULT 0.00,
  `max_stock` decimal(14,2) NOT NULL DEFAULT 0.00,
  `is_valid` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wh_item` (`warehouse_code`,`item_code`),
  KEY `idx_item` (`item_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `logistic_warehouses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `is_active` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wh_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_icd10` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) NOT NULL,
  `nama` text NOT NULL,
  `is_active` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kode` (`kode`),
  FULLTEXT KEY `ft_nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=18544 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_icd9` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) NOT NULL,
  `nama` text NOT NULL,
  `is_active` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kode` (`kode`),
  FULLTEXT KEY `ft_nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=4627 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_laboratories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `tariff` decimal(15,2) DEFAULT 0.00,
  `jasa_rs` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jasa_dr` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jasa_perawat` decimal(15,2) NOT NULL DEFAULT 0.00,
  `category` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `lab_group` varchar(100) DEFAULT NULL,
  `lab_subgroup` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_patient_referrers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `referral_origin` varchar(255) DEFAULT NULL,
  `doctor_sender` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `is_active` char(1) DEFAULT 'Y',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_penjamins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `is_active` char(1) DEFAULT 'Y',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_radiologies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `tariff` decimal(15,2) DEFAULT 0.00,
  `category` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_tenaga_medis` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(30) NOT NULL COMMENT 'Kode pegawai, contoh: PWR0000101, PWT-00025, DRT-028',
  `nama` varchar(150) NOT NULL COMMENT 'Nama lengkap tenaga kesehatan',
  `status` varchar(50) NOT NULL COMMENT 'Jenis/profesi: PERAWAT/SUSTER, BIDAN, UMUM, PENUNJANG MEDIS, ANALYST LABORATORIUM, dst',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_master_tenaga_medis_kode` (`kode`),
  KEY `idx_master_tenaga_medis_nama` (`nama`),
  KEY `idx_master_tenaga_medis_status` (`status`),
  KEY `idx_master_tenaga_medis_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master data tenaga kesehatan untuk pengisian CPPT';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_tindakan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) NOT NULL COMMENT 'Kode unik tindakan',
  `nama` varchar(200) NOT NULL COMMENT 'Nama tindakan',
  `kategori` varchar(50) DEFAULT NULL,
  `harga_umum` decimal(12,2) NOT NULL DEFAULT 0.00,
  `persen_dokter_umum` decimal(5,2) NOT NULL DEFAULT 0.00,
  `harga_bpjs` decimal(12,2) NOT NULL DEFAULT 0.00,
  `persen_dokter_bpjs` decimal(5,2) NOT NULL DEFAULT 0.00,
  `harga_reimbes` decimal(12,2) NOT NULL DEFAULT 0.00,
  `persen_dokter_reimbes` decimal(5,2) NOT NULL DEFAULT 0.00,
  `applies_rawat_jalan` tinyint(1) NOT NULL DEFAULT 1,
  `applies_rawat_inap` tinyint(1) NOT NULL DEFAULT 0,
  `applies_igd` tinyint(1) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `lab_group` varchar(100) DEFAULT NULL,
  `lab_subgroup` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kode` (`kode`),
  KEY `idx_kategori` (`kategori`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master tindakan & tarif (Konsultasi, Tindakan Medis, Administrasi)';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_treatment_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(15,2) DEFAULT 0.00,
  `is_active` char(1) DEFAULT 'Y',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_record_diagnoses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medical_record_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_record_documents` (
  `id` int(11) NOT NULL DEFAULT 0,
  `medical_record_id` int(11) NOT NULL,
  `document_name` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_record_examinations` (
  `id` int(11) NOT NULL DEFAULT 0,
  `medical_record_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `payload_json` longtext DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `history_no` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_record_icares` (
  `id` int(11) NOT NULL DEFAULT 0,
  `medical_record_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_record_integrations` (
  `id` int(11) NOT NULL DEFAULT 0,
  `medical_record_id` int(11) NOT NULL,
  `noted_at` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `health_worker_name` varchar(255) DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `doctor_name` varchar(255) DEFAULT NULL,
  `soap_sbar` text DEFAULT NULL,
  `instruction` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `payload_json` longtext DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_record_laboratories` (
  `id` int(11) NOT NULL DEFAULT 0,
  `medical_record_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_record_prescriptions` (
  `id` int(11) NOT NULL DEFAULT 0,
  `medical_record_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_record_radiologies` (
  `id` int(11) NOT NULL DEFAULT 0,
  `medical_record_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_record_referrals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medical_record_id` int(11) NOT NULL,
  `referral_no` varchar(100) DEFAULT NULL,
  `referral_type` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `idx_medical_record_id` (`medical_record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL DEFAULT 0,
  `registration_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `outpatient_visit_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `medical_record_no` varchar(50) DEFAULT NULL,
  `service_type` varchar(50) DEFAULT NULL,
  `allergy` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_resumes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `outpatient_visit_id` int(11) DEFAULT NULL,
  `registration_id` int(10) unsigned DEFAULT NULL,
  `patient_id` int(10) unsigned DEFAULT NULL,
  `doctor1_id` int(10) unsigned DEFAULT NULL,
  `doctor2_name` varchar(150) DEFAULT NULL,
  `doctor3_name` varchar(150) DEFAULT NULL,
  `doctor4_name` varchar(150) DEFAULT NULL,
  `consultant1_name` varchar(150) DEFAULT NULL,
  `consultant2_name` varchar(150) DEFAULT NULL,
  `summary_history` text DEFAULT NULL,
  `inpatient_indication` text DEFAULT NULL,
  `physical_exam` text DEFAULT NULL,
  `supporting_exam` text DEFAULT NULL,
  `therapy_during_treatment` text DEFAULT NULL,
  `consultation_result` text DEFAULT NULL,
  `admission_diagnosis` text DEFAULT NULL,
  `lab_unfinished` text DEFAULT NULL,
  `diet` text DEFAULT NULL,
  `education_instruction` text DEFAULT NULL,
  `primary_diagnosis` text DEFAULT NULL,
  `secondary_diagnosis` text DEFAULT NULL,
  `medical_action` text DEFAULT NULL,
  `condition_on_discharge` varchar(100) DEFAULT NULL,
  `prognosis` varchar(100) DEFAULT NULL,
  `follow_up_treatment` varchar(100) DEFAULT NULL,
  `control_date` date DEFAULT NULL,
  `control_note` text DEFAULT NULL,
  `control_plan` text DEFAULT NULL,
  `return_reason` text DEFAULT NULL,
  `letter_type` varchar(30) DEFAULT NULL,
  `print_classic` tinyint(1) NOT NULL DEFAULT 0,
  `send_digital_claim` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_outpatient_visit_id` (`outpatient_visit_id`),
  KEY `idx_registration_id` (`registration_id`),
  KEY `idx_patient_id` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicine_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 0,
  `code` varchar(50) DEFAULT NULL COMMENT 'Kode IFRS',
  `name` varchar(255) DEFAULT NULL COMMENT 'Nama Produk',
  `type` varchar(255) DEFAULT NULL COMMENT 'Jenis',
  `description` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL COMMENT 'path untuk gambar',
  `qty_unit` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah satuan',
  `unit` varchar(100) DEFAULT NULL COMMENT 'Satuan',
  `qty_packaging` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah kemasan',
  `packaging` varchar(100) DEFAULT NULL COMMENT 'Kemasan',
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_print` int(11) NOT NULL DEFAULT 0,
  `printed` datetime DEFAULT NULL,
  `is_active` enum('Y','N') DEFAULT 'Y' COMMENT 'Y aktif, N tidak aktif',
  `is_log` text DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='Group Obat';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicine_merks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 0,
  `code` varchar(50) DEFAULT NULL COMMENT 'Kode',
  `name` varchar(255) DEFAULT NULL COMMENT 'Nama',
  `type` varchar(255) DEFAULT NULL COMMENT 'Jenis',
  `description` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL COMMENT 'path untuk gambar',
  `qty_unit` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah satuan',
  `unit` varchar(100) DEFAULT NULL COMMENT 'Satuan',
  `qty_packaging` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah kemasan',
  `packaging` varchar(100) DEFAULT NULL COMMENT 'Kemasan',
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_print` int(11) NOT NULL DEFAULT 0,
  `printed` datetime DEFAULT NULL,
  `is_active` enum('Y','N') DEFAULT 'Y' COMMENT 'Y aktif, N tidak aktif',
  `is_log` text DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='Merk Obat';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicine_stocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `medicine_id` int(11) NOT NULL,
  `last_so_date` date DEFAULT NULL COMMENT 'Tgl SO terakhir',
  `hasil_so` decimal(15,2) NOT NULL DEFAULT 0.00,
  `adjustment` decimal(15,2) NOT NULL DEFAULT 0.00,
  `terima` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keluar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `saldo_akhir` decimal(15,2) NOT NULL DEFAULT 0.00,
  `max_stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_valid` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'Status validasi stock',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_branch_medicine` (`branch_id`,`medicine_id`),
  KEY `fk_medicine_stocks_medicine` (`medicine_id`),
  CONSTRAINT `fk_medicine_stocks_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='Informasi stock per obat, relasi ke master medicines';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 0,
  `code` varchar(50) DEFAULT NULL COMMENT 'Kode',
  `code_kfa` varchar(50) DEFAULT NULL COMMENT 'Kode KFA',
  `name` varchar(255) DEFAULT NULL COMMENT 'Nama Produk',
  `generic_name` varchar(255) DEFAULT NULL,
  `inventory_category` varchar(255) DEFAULT NULL,
  `unit_1` varchar(100) DEFAULT NULL,
  `unit_2` varchar(100) DEFAULT NULL,
  `unit_2_qty` int(11) DEFAULT 0,
  `unit_3` varchar(100) DEFAULT NULL,
  `unit_3_qty` int(11) DEFAULT 0,
  `code_ifrs` varchar(255) DEFAULT NULL COMMENT 'Kode IFRS',
  `gol` varchar(255) DEFAULT NULL,
  `therapy_class` varchar(255) DEFAULT NULL,
  `golongan` varchar(255) DEFAULT NULL,
  `pabrik` varchar(255) DEFAULT NULL,
  `sediaan` varchar(255) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `lead_time` int(11) DEFAULT 0,
  `depkes_link` varchar(100) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL COMMENT 'Jenis',
  `img` varchar(255) DEFAULT NULL COMMENT 'path untuk gambar',
  `hna` decimal(15,2) DEFAULT 0.00,
  `ppn_status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `hna_ppn` decimal(15,2) DEFAULT 0.00,
  `jenis_harga` enum('PROSENTASE','MANUAL') NOT NULL DEFAULT 'PROSENTASE',
  `harga_jual` decimal(15,2) DEFAULT 0.00,
  `harga_jual_jaminan` decimal(15,2) DEFAULT 0.00,
  `nilai_persediaan_satuan` decimal(15,2) DEFAULT 0.00,
  `minimum_stock` decimal(12,2) DEFAULT 0.00,
  `reorder_level` decimal(12,2) DEFAULT 0.00,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_print` int(11) NOT NULL DEFAULT 0,
  `printed` datetime DEFAULT NULL,
  `is_active` enum('Y','N') DEFAULT 'Y' COMMENT 'Y aktif, N tidak aktif',
  `is_log` text DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='Obat';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_laboratories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `master_tindakan_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `examination_name` varchar(255) NOT NULL,
  `price` decimal(14,2) NOT NULL DEFAULT 0.00,
  `approval_note` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order_laboratories_order_id` (`order_id`),
  KEY `idx_order_laboratories_master_tindakan_id` (`master_tindakan_id`),
  CONSTRAINT `fk_order_laboratories_master_tindakan` FOREIGN KEY (`master_tindakan_id`) REFERENCES `master_tindakan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_order_laboratories_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_prescriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `medicine_unit` varchar(100) DEFAULT NULL,
  `is_kronis` tinyint(1) DEFAULT 0,
  `jml_per_hari` decimal(8,2) DEFAULT 0.00,
  `qty_min` decimal(8,2) DEFAULT 0.00,
  `jml_hari` int(11) DEFAULT 0,
  `qty_satuan` decimal(8,2) DEFAULT 0.00,
  `aturan_pakai` varchar(100) DEFAULT NULL,
  `harga` decimal(12,2) DEFAULT 0.00,
  `total_harga` decimal(12,2) DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_order_prescriptions_order_id` (`order_id`) USING BTREE,
  KEY `idx_order_prescriptions_medicine_id` (`medicine_id`) USING BTREE,
  CONSTRAINT `fk_order_prescriptions_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_radiologies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `master_tindakan_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `examination_name` varchar(255) NOT NULL,
  `price` decimal(14,2) NOT NULL DEFAULT 0.00,
  `approval_note` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order_radiologies_order_id` (`order_id`),
  KEY `idx_order_radiologies_master_tindakan_id` (`master_tindakan_id`),
  CONSTRAINT `fk_order_radiologies_master_tindakan` FOREIGN KEY (`master_tindakan_id`) REFERENCES `master_tindakan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_order_radiologies_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `warna` varchar(20) DEFAULT NULL COMMENT 'hex color untuk badge UI, ex: #28a745',
  `urutan` int(11) DEFAULT 0 COMMENT 'urutan tampil di dropdown',
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_order` varchar(30) NOT NULL COMMENT 'generate otomatis, ex: ORD/2026/06/00001',
  `registration_id` int(11) NOT NULL COMMENT 'FK ke tabel registrations',
  `patient_id` int(11) NOT NULL COMMENT 'FK ke tabel patients',
  `doctor_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel doctors',
  `created_by` int(11) DEFAULT NULL COMMENT 'FK ke tabel users',
  `status_id` int(11) NOT NULL DEFAULT 1 COMMENT 'FK ke order_statuses',
  `tgl_order` datetime NOT NULL COMMENT 'tanggal & jam order dibuat',
  `lokasi_depo` varchar(50) DEFAULT NULL COMMENT 'ex: FARMASI 2 RAJAL DEPO',
  `pesan` varchar(100) DEFAULT NULL COMMENT 'pesan',
  `catatan` text DEFAULT NULL,
  `laboratory_note` text DEFAULT NULL,
  `radiology_note` text DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_order` (`no_order`),
  KEY `status_id` (`status_id`),
  KEY `idx_orders_registration` (`registration_id`),
  KEY `idx_orders_patient` (`patient_id`),
  KEY `idx_orders_tgl` (`tgl_order`),
  KEY `idx_orders_no` (`no_order`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `order_statuses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `outpatient_actions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `outpatient_visit_id` int(10) unsigned NOT NULL,
  `action_code` varchar(50) DEFAULT NULL,
  `action_name` varchar(255) NOT NULL,
  `action_category` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `performed_by` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_outpatient_visit_id` (`outpatient_visit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `outpatient_billing_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `billing_id` int(10) unsigned NOT NULL,
  `item_type` enum('tindakan','obat','laboratorium','radiologi','administrasi','lainnya') NOT NULL DEFAULT 'tindakan',
  `item_code` varchar(50) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit` varchar(20) DEFAULT 'kali',
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `final_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `is_covered` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=ditanggung asuransi/BPJS',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_delete` tinyint(1) DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_billing` (`billing_id`),
  CONSTRAINT `fk_billing_items_billing` FOREIGN KEY (`billing_id`) REFERENCES `outpatient_billings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rincian item tagihan rawat jalan';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `outpatient_billing_payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `billing_id` int(10) unsigned NOT NULL,
  `payment_no` varchar(30) NOT NULL COMMENT 'Nomor transaksi pembayaran',
  `payment_method` enum('tunai','transfer','bpjs','asuransi','debit','kredit','qris','lainnya') NOT NULL DEFAULT 'tunai',
  `bank_name` varchar(50) DEFAULT NULL COMMENT 'Nama bank jika transfer/debit/kredit',
  `account_no` varchar(50) DEFAULT NULL COMMENT 'No rekening / no kartu (masked)',
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` datetime DEFAULT NULL,
  `verified_by` int(10) unsigned DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_payment_no` (`payment_no`),
  KEY `idx_billing` (`billing_id`),
  CONSTRAINT `fk_billing_payments_billing` FOREIGN KEY (`billing_id`) REFERENCES `outpatient_billings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Riwayat pembayaran tagihan rawat jalan';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `outpatient_billings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `billing_no` varchar(30) NOT NULL COMMENT 'Nomor tagihan, contoh: BILL-20260613-00001',
  `receipt_no` varchar(30) DEFAULT NULL COMMENT 'No. Kwitansi',
  `outpatient_visit_id` int(10) unsigned NOT NULL,
  `registration_id` int(10) unsigned DEFAULT NULL,
  `patient_id` int(10) unsigned NOT NULL,
  `doctor_id` int(10) unsigned DEFAULT NULL,
  `visit_date` date NOT NULL,
  `polyclinic_name` varchar(100) DEFAULT NULL,
  `insurance_type` varchar(50) DEFAULT 'Umum' COMMENT 'Umum, BPJS, Asuransi, dll',
  `insurance_partner_name` varchar(100) DEFAULT NULL COMMENT 'Nama Rekanan, cth. BPJS Kesehatan',
  `policy_no` varchar(50) DEFAULT NULL COMMENT 'No. Polis / No. SEP',
  `insurance_no` varchar(50) DEFAULT NULL COMMENT 'No kartu BPJS / asuransi',
  `status` enum('draft','waiting_payment','paid','cancelled','void') NOT NULL DEFAULT 'draft',
  `total_action` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total biaya tindakan',
  `total_medicine` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total biaya obat/resep',
  `total_lab` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total biaya laboratorium',
  `total_radiology` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total biaya radiologi',
  `total_other` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Biaya lain-lain',
  `admin_fee` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Adm Rp',
  `inacbg_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Inacbg Rp',
  `cbg_code` varchar(20) DEFAULT NULL COMMENT 'Kode Briging CBG',
  `guarantor2_name` varchar(100) DEFAULT NULL COMMENT 'Nama Rekanan (2)',
  `guarantor2_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total dijamin Rekanan (2)',
  `guarantor3_name` varchar(100) DEFAULT NULL COMMENT 'Nama Rekanan (3)',
  `guarantor3_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total dijamin Rekanan (3)',
  `uncollectible_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Selisih Tak Tertagih',
  `transfer_card_no` varchar(100) DEFAULT NULL,
  `transfer_bank_auth_no` varchar(100) DEFAULT NULL,
  `transfer_card_type` varchar(20) DEFAULT NULL,
  `transfer_admin_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `transfer_bank_dest` varchar(100) DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` enum('nominal','percent') DEFAULT 'nominal',
  `discount_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Hasil kalkulasi diskon',
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('tunai','transfer','bpjs','asuransi','piutang','lainnya') DEFAULT 'tunai',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Jumlah yang dibayarkan',
  `change_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Kembalian',
  `paid_at` datetime DEFAULT NULL,
  `cashier_id` int(10) unsigned DEFAULT NULL COMMENT 'User kasir yang memproses',
  `cashier_name` varchar(100) DEFAULT NULL,
  `paid_by` varchar(100) DEFAULT NULL COMMENT 'Dibayar Oleh (nama kasir)',
  `is_referred` tinyint(1) NOT NULL DEFAULT 0,
  `referred_to` varchar(100) DEFAULT NULL,
  `is_inpatient` tinyint(1) NOT NULL DEFAULT 0,
  `ready_to_transfer` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Sudah siap ditransfer ke rawat inap',
  `inpatient_referral_no` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `void_reason` varchar(255) DEFAULT NULL,
  `voided_at` datetime DEFAULT NULL,
  `voided_by` int(10) unsigned DEFAULT NULL,
  `is_create` tinyint(1) DEFAULT 1,
  `is_update` tinyint(1) DEFAULT 0,
  `is_delete` tinyint(1) DEFAULT 0,
  `is_active` char(1) DEFAULT 'Y',
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `created_by` int(10) unsigned DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `reopen_reason` text DEFAULT NULL,
  `reopened_at` datetime DEFAULT NULL,
  `reopened_by` int(11) DEFAULT NULL,
  `reopen_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_billing_no` (`billing_no`),
  KEY `idx_visit` (`outpatient_visit_id`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_billing_no` (`billing_no`),
  KEY `idx_status` (`status`),
  KEY `idx_visit_date` (`visit_date`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tagihan rawat jalan per kunjungan';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `outpatient_cppt_notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `outpatient_visit_id` int(10) unsigned NOT NULL,
  `registration_id` int(10) unsigned DEFAULT NULL,
  `patient_id` int(10) unsigned DEFAULT NULL,
  `doctor_id` int(10) unsigned DEFAULT NULL,
  `noted_at` datetime DEFAULT NULL,
  `health_worker_name` varchar(100) DEFAULT NULL,
  `doctor_name` varchar(100) DEFAULT NULL,
  `nurse_name` varchar(100) DEFAULT NULL,
  `assessment_text` text DEFAULT NULL,
  `instruction` text DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_outpatient_visit_id` (`outpatient_visit_id`),
  KEY `idx_registration_id` (`registration_id`),
  KEY `idx_patient_id` (`patient_id`),
  KEY `idx_doctor_id` (`doctor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `outpatient_display_devices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device_code` varchar(50) NOT NULL,
  `device_name` varchar(100) DEFAULT NULL,
  `polyclinic_name` varchar(150) NOT NULL,
  `is_active` char(1) NOT NULL DEFAULT 'Y',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_outpatient_display_device_code` (`device_code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `outpatient_initial_medical_assessments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `outpatient_visit_id` int(11) NOT NULL,
  `registration_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `assessment_no` varchar(50) DEFAULT NULL,
  `assessment_date` date DEFAULT NULL,
  `assessment_time` time DEFAULT NULL,
  `data_dasar` longtext DEFAULT NULL,
  `subjektif` longtext DEFAULT NULL,
  `observasi` longtext DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_oima_outpatient_visit_id` (`outpatient_visit_id`),
  KEY `registration_id` (`registration_id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `outpatient_letters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `outpatient_visit_id` int(11) NOT NULL,
  `registration_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `letter_type` varchar(30) NOT NULL DEFAULT 'sakit',
  `letter_no` varchar(50) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `color_blind` varchar(20) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `bpjs_card_no` varchar(50) DEFAULT NULL,
  `leave_days` int(11) DEFAULT NULL,
  `leave_from` date DEFAULT NULL,
  `leave_to` date DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `consultation_answer` longtext DEFAULT NULL,
  `is_normal` tinyint(1) DEFAULT 1,
  `examined_at` date DEFAULT NULL,
  `anamnesa` text DEFAULT NULL,
  `diagnosis_primary_text` text DEFAULT NULL,
  `diagnosis_secondary_text` text DEFAULT NULL,
  `action_primary_text` text DEFAULT NULL,
  `action_secondary_text` text DEFAULT NULL,
  `supporting_diagnosis` text DEFAULT NULL,
  `is_autopsy` tinyint(1) NOT NULL DEFAULT 0,
  `special_actions` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `advice_notes` text DEFAULT NULL,
  `discharged_by_doctor_id` int(10) unsigned DEFAULT NULL,
  `completing_doctor_id` int(10) unsigned DEFAULT NULL,
  `referral_doctor_id` int(11) DEFAULT NULL,
  `referral_doctor_name` varchar(150) DEFAULT NULL,
  `referral_doctor_specialist` varchar(150) DEFAULT NULL,
  `patient_or_family_name` varchar(150) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outpatient_visit_id` (`outpatient_visit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `outpatient_visit_emr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `outpatient_visit_id` int(10) unsigned NOT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `pulse` int(10) unsigned DEFAULT NULL,
  `temperature` decimal(4,1) unsigned DEFAULT NULL,
  `respiratory_rate` int(10) unsigned DEFAULT NULL,
  `oxygen_saturation` int(10) unsigned DEFAULT NULL,
  `weight` decimal(5,2) unsigned DEFAULT NULL,
  `height` decimal(5,2) unsigned DEFAULT NULL,
  `bmi` decimal(5,2) unsigned DEFAULT NULL,
  `chief_complaint` text DEFAULT NULL,
  `illness_history` text DEFAULT NULL,
  `admission_diagnosis` varchar(255) DEFAULT NULL,
  `allergy` varchar(255) DEFAULT NULL,
  `pain_scale` int(10) unsigned DEFAULT NULL,
  `diagnosis_primary` varchar(100) DEFAULT NULL,
  `diagnosis_secondary` varchar(255) DEFAULT NULL,
  `diagnosis_desc` text DEFAULT NULL,
  `icd10_items` text DEFAULT NULL,
  `icd9_items` text DEFAULT NULL,
  `anatomy_front` varchar(255) DEFAULT NULL,
  `anatomy_back` varchar(255) DEFAULT NULL,
  `action_notes` text DEFAULT NULL,
  `action_notes_secondary` text DEFAULT NULL,
  `doctor_notes` text DEFAULT NULL,
  `prescription_notes` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `referral_to` varchar(100) DEFAULT NULL,
  `referral_reason` text DEFAULT NULL,
  `payment_method` varchar(30) DEFAULT NULL,
  `total_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_outpatient_visit_emr_visit` (`outpatient_visit_id`),
  CONSTRAINT `fk_emr_outpatient_visit` FOREIGN KEY (`outpatient_visit_id`) REFERENCES `outpatient_visits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `outpatient_visits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `registration_id` int(10) unsigned NOT NULL,
  `registration_no` varchar(50) NOT NULL,
  `patient_id` int(10) unsigned NOT NULL,
  `doctor_id` int(10) unsigned DEFAULT NULL,
  `queue_id` int(10) unsigned DEFAULT NULL,
  `queue_number` int(11) DEFAULT NULL,
  `polyclinic_name` varchar(100) DEFAULT NULL,
  `visit_date` date NOT NULL,
  `visit_time` time DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'waiting',
  `billing_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke outpatient_billings.id',
  `billing_status` varchar(30) DEFAULT NULL COMMENT 'Cache status billing: draft/waiting_payment/paid',
  `called_at` datetime DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `bmi_category` varchar(50) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `updated_by` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_outpatient_visits_registration_id` (`registration_id`),
  KEY `idx_outpatient_visits_patient_id` (`patient_id`),
  KEY `idx_outpatient_visits_doctor_id` (`doctor_id`),
  KEY `idx_outpatient_visits_queue_id` (`queue_id`),
  KEY `idx_outpatient_visits_visit_date` (`visit_date`),
  KEY `idx_outpatient_visits_status` (`status`),
  KEY `idx_outpatient_visits_registration_no` (`registration_no`),
  KEY `idx_outpatient_visits_patient_visit` (`patient_id`,`visit_date`),
  KEY `idx_billing_id` (`billing_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient_histories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `idx_medical_records_registration_id` (`registration_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient_history_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_history_id` int(11) NOT NULL,
  `document_name` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `idx_medical_record_id` (`patient_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient_history_examinations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_history_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `payload_json` longtext DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `idx_medical_record_id` (`patient_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient_history_icares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_history_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `idx_medical_record_id` (`patient_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient_history_integrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_history_id` int(11) NOT NULL,
  `noted_at` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `health_worker_name` varchar(255) DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `doctor_name` varchar(255) DEFAULT NULL,
  `soap_sbar` text DEFAULT NULL,
  `instruction` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `payload_json` longtext DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `idx_medical_record_id` (`patient_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient_history_laboratories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_history_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `idx_medical_record_id` (`patient_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient_history_prescriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_history_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `idx_medical_record_id` (`patient_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient_history_radiologies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_history_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `note` text DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `idx_medical_record_id` (`patient_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `patients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `code_hash` char(64) DEFAULT NULL,
  `old_medical_record_no` varchar(50) DEFAULT NULL COMMENT 'No. Rekam Medis lama (sistem sebelumnya)',
  `title` varchar(20) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL DEFAULT '',
  `identity_type` varchar(50) DEFAULT NULL,
  `identity_number` varchar(50) DEFAULT NULL,
  `insurance_card_no` varchar(30) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `allergy` text DEFAULT NULL COMMENT 'Riwayat alergi pasien',
  `birth_date` date DEFAULT NULL,
  `age` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `citizenship` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `subdistrict` varchar(100) DEFAULT NULL,
  `id_prov` varchar(100) DEFAULT NULL,
  `id_kab` varchar(100) DEFAULT NULL,
  `id_kec` varchar(100) DEFAULT NULL,
  `id_kel` varchar(100) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `ethnic_group` varchar(100) DEFAULT NULL,
  `marital_status` varchar(50) DEFAULT NULL,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `is_log` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_patients_medical_record_no` (`old_medical_record_no`),
  UNIQUE KEY `uq_patients_code_hash` (`code_hash`),
  KEY `idx_patients_identity_number` (`identity_number`),
  KEY `idx_patients_full_name` (`full_name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_responsibles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_id` int(11) NOT NULL DEFAULT 0 COMMENT 'fk ke registration',
  `name` varchar(255) NOT NULL COMMENT 'nama',
  `identity_type` varchar(255) NOT NULL COMMENT 'jenis identitas',
  `identity_no` varchar(255) NOT NULL COMMENT 'no identitas',
  `relation` varchar(50) NOT NULL COMMENT 'hubungan',
  `age` int(11) NOT NULL COMMENT 'umur',
  `job` varchar(100) NOT NULL COMMENT 'pekerjaan',
  `address` text NOT NULL COMMENT 'alamat',
  `phone` varchar(20) NOT NULL COMMENT 'nomor telepon',
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='penanggung jawab pasien';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pharmacy_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL DEFAULT '0',
  `region` varchar(50) NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `purchase_unit` varchar(50) NOT NULL,
  `sale_unit` varchar(50) NOT NULL,
  `qjb` decimal(18,2) DEFAULT 0.00,
  `therapy_class` varchar(255) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `producer` varchar(255) NOT NULL,
  `medicine_type` varchar(255) NOT NULL,
  `cbg_rate` decimal(18,2) DEFAULT 0.00,
  `formula_name` varchar(255) NOT NULL,
  `formula_percentage` decimal(10,2) DEFAULT 0.00,
  `hpp` decimal(18,2) DEFAULT 0.00,
  `sale_price` decimal(18,2) DEFAULT 0.00,
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `polyclinic_queues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL DEFAULT '0',
  `source` varchar(255) NOT NULL COMMENT 'sumber pra regist',
  `booking_code` varchar(100) DEFAULT NULL COMMENT 'Kode booking simulasi Mobile JKN',
  `bpjs_number` varchar(50) DEFAULT NULL COMMENT 'No kartu BPJS simulasi Mobile JKN',
  `identity_number` varchar(50) DEFAULT NULL COMMENT 'NIK pasien simulasi Mobile JKN',
  `visit_date` date DEFAULT NULL COMMENT 'Tanggal periksa simulasi Mobile JKN',
  `checkin_status` varchar(50) DEFAULT 'Belum Check-in' COMMENT 'Status check-in Mobile JKN',
  `checkin_time` datetime DEFAULT NULL COMMENT 'Waktu check-in Mobile JKN',
  `call_status` varchar(50) DEFAULT 'Menunggu' COMMENT 'Status panggilan antrian',
  `called_time` datetime DEFAULT NULL COMMENT 'Waktu terakhir nomor dipanggil',
  `call_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah nomor dipanggil ulang',
  `poly_name` varchar(255) NOT NULL COMMENT 'nama poli',
  `patients_name` varchar(255) DEFAULT NULL COMMENT 'Nama pasien',
  `patient_id` int(11) DEFAULT NULL,
  `medical_record_no` varchar(50) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `doctor_name` varchar(255) DEFAULT NULL,
  `no_telp` varchar(255) DEFAULT NULL COMMENT 'No. HP',
  `queue_number` int(11) NOT NULL COMMENT 'nomor antrian',
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_polyclinic_queues_call_status` (`call_status`),
  KEY `idx_polyclinic_queues_called_time` (`called_time`)
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='antrian poli';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pra_registrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_code` varchar(30) NOT NULL,
  `patient_id` int(10) unsigned DEFAULT NULL COMMENT 'nanti di-FK ke patients.id',
  `medical_record_no` varchar(20) DEFAULT NULL COMMENT 'No. RM',
  `patient_name` varchar(150) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `doctor_id` int(10) unsigned DEFAULT NULL COMMENT 'nanti di-FK ke doctors.id / doctor_schedules.id',
  `doctor_name` varchar(150) NOT NULL,
  `poly_name` varchar(100) NOT NULL COMMENT 'Tujuan / Pelayanan',
  `appointment_date` date NOT NULL COMMENT 'Tanggal',
  `appointment_day` varchar(15) DEFAULT NULL COMMENT 'Hari, disimpan biar gampang ditampilkan',
  `appointment_time` time NOT NULL COMMENT 'Jam',
  `patient_queue_number` int(10) unsigned DEFAULT NULL COMMENT 'Nomor Antri Pasien',
  `guarantor` varchar(100) DEFAULT NULL COMMENT 'Penjamin, kosong = tunai',
  `planned_action` varchar(255) DEFAULT NULL COMMENT 'Rencana Tindakan',
  `referral_type` enum('rujukan_fktp','rujukan_internal','kontrol','rujukan_antar_rs') DEFAULT NULL,
  `card_number` varchar(30) DEFAULT NULL COMMENT 'No Kartu (BPJS dll)',
  `referral_number` varchar(50) DEFAULT NULL COMMENT 'No Rujukan / Kontrol',
  `channel` enum('telepon','sms','website','poli_spesialis','wa') NOT NULL DEFAULT 'wa' COMMENT 'Melalui',
  `notes` varchar(255) DEFAULT NULL COMMENT 'Keterangan',
  `registration_status` enum('belum_diproses','diteruskan','sudah_diproses','batal') DEFAULT 'belum_diproses',
  `polyclinic_queue_id` int(11) DEFAULT NULL,
  `registration_id` int(11) DEFAULT NULL,
  `registered_at` datetime DEFAULT NULL COMMENT 'diisi saat tombol Daftarkan ditekan',
  `created` datetime NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pra_registrations_reservation_code` (`reservation_code`),
  KEY `idx_pra_registrations_appointment_date` (`appointment_date`),
  KEY `idx_pra_registrations_status` (`registration_status`),
  KEY `idx_pra_registrations_patient_name` (`patient_name`),
  KEY `idx_pra_registrations_registration_id` (`registration_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `radiology_examination_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `examination_id` int(10) unsigned NOT NULL,
  `order_item_id` int(11) DEFAULT NULL COMMENT 'id baris order radiologi',
  `code` varchar(50) DEFAULT NULL,
  `examination_name` varchar(255) NOT NULL,
  `exam_type` varchar(50) DEFAULT NULL COMMENT 'Jenis',
  `tariff` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty` int(11) NOT NULL DEFAULT 1,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `approval_note` varchar(255) DEFAULT NULL,
  `result_text` text DEFAULT NULL COMMENT 'hasil / expertise',
  `conclusion` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL COMMENT 'file foto / DICOM',
  `radiologist_name` varchar(150) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'received',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `photo_no` varchar(30) DEFAULT NULL COMMENT 'No. Foto',
  `doctor_fee` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Jasa Dokter',
  `incentive` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Insentif',
  `insurance_cover` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Askes/dicover',
  `jpk` decimal(15,2) NOT NULL DEFAULT 0.00,
  `disc_rs` decimal(15,2) NOT NULL DEFAULT 0.00,
  `disc_dr` decimal(15,2) NOT NULL DEFAULT 0.00,
  `outside_doctor_fee` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'dokter luar',
  PRIMARY KEY (`id`),
  KEY `idx_examination` (`examination_id`),
  CONSTRAINT `fk_radexamitems_exam` FOREIGN KEY (`examination_id`) REFERENCES `radiology_examinations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `radiology_examination_supplies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `examination_id` int(10) unsigned NOT NULL,
  `item_id` int(11) DEFAULT NULL COMMENT 'pharmacy_items.id',
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `qty` decimal(12,2) NOT NULL DEFAULT 1.00,
  `unit` varchar(30) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `location` varchar(100) DEFAULT NULL COMMENT 'LOK/UNIT',
  `charged_to_patient` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_examination` (`examination_id`),
  CONSTRAINT `fk_radsupplies_exam` FOREIGN KEY (`examination_id`) REFERENCES `radiology_examinations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `radiology_examinations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rad_no` varchar(30) NOT NULL COMMENT 'No Tr, contoh R202600000003743',
  `patient_origin` varchar(10) NOT NULL DEFAULT 'rs' COMMENT 'rs | luar',
  `order_id` int(11) DEFAULT NULL COMMENT 'orders.id (sumber entry)',
  `no_order` varchar(30) DEFAULT NULL,
  `registration_id` int(11) DEFAULT NULL,
  `registration_no` varchar(30) DEFAULT NULL COMMENT 'No Reg, contoh RJ2026V64860',
  `patient_id` int(11) DEFAULT NULL,
  `medical_record_no` varchar(30) DEFAULT NULL,
  `patient_name` varchar(150) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `doctor_name` varchar(150) DEFAULT NULL,
  `patient_type` varchar(30) DEFAULT NULL COMMENT 'Rawat Jalan / Rawat Inap / IGD',
  `doctor_message` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'received' COMMENT 'received|processing|done',
  `received_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `processed_by` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` varchar(1) DEFAULT NULL COMMENT 'L | P',
  `class_name` varchar(50) DEFAULT NULL COMMENT 'Kelas',
  `reader_doctor_id` int(11) DEFAULT NULL COMMENT 'Dr. Pembaca',
  `sender_doctor_id` int(11) DEFAULT NULL COMMENT 'Pengirim (dokter RS)',
  `sender_name` varchar(150) DEFAULT NULL COMMENT 'Pengirim (teks bebas / dokter luar)',
  `officer_name` varchar(100) DEFAULT NULL COMMENT 'Petugas',
  `request_type` varchar(30) NOT NULL DEFAULT 'dikirim_dr' COMMENT 'dikirim_dr | atas_permintaan_sendiri',
  `system_type` varchar(20) NOT NULL DEFAULT 'rs' COMMENT 'rs | dirujuk',
  `eorder_note` text DEFAULT NULL,
  `barcode_label` varchar(60) DEFAULT NULL,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rad_no` (`rad_no`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_mr` (`medical_record_no`),
  KEY `idx_received` (`received_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `radiology_normal_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tindakan_id` int(10) unsigned NOT NULL,
  `normal_value` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_radiology_normal_tindakan` (`tindakan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipient_payers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 0,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `is_active` enum('Y','N') DEFAULT NULL,
  `is_log` text DEFAULT NULL,
  `is_create` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `NewIndex1` (`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `registrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `registration_no` varchar(50) NOT NULL,
  `patient_id` int(10) unsigned NOT NULL,
  `patient_title` varchar(10) DEFAULT NULL,
  `registration_date` date NOT NULL,
  `admission_time` time DEFAULT NULL,
  `service_type` varchar(255) NOT NULL,
  `doctor_id` int(10) unsigned DEFAULT NULL,
  `doctor_schedule_id` int(10) unsigned DEFAULT NULL,
  `polyclinic_queue_id` int(10) unsigned DEFAULT NULL,
  `queue_number` varchar(50) DEFAULT NULL,
  `polyclinic_name` varchar(255) DEFAULT NULL,
  `room_name` varchar(100) DEFAULT NULL,
  `room_id` int(10) unsigned DEFAULT NULL,
  `bed_id` int(10) unsigned DEFAULT NULL,
  `class_name` varchar(255) DEFAULT NULL,
  `entry_process` varchar(255) NOT NULL,
  `entry_method` varchar(255) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `insurance_type` varchar(255) DEFAULT NULL,
  `insurance_card_no` varchar(30) DEFAULT NULL,
  `refferal_no` varchar(30) DEFAULT NULL,
  `skdp_no` varchar(50) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `sep_no` varchar(50) DEFAULT NULL,
  `bpjs_nik` varchar(30) DEFAULT NULL,
  `bpjs_name` varchar(255) DEFAULT NULL,
  `bpjs_group` varchar(100) DEFAULT NULL,
  `bpjs_type` varchar(100) DEFAULT NULL,
  `bpjs_class` varchar(50) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `person_responsible_id` int(10) unsigned DEFAULT NULL,
  `treatment_type` varchar(50) NOT NULL DEFAULT 'BIASA',
  `referral_type` varchar(20) DEFAULT NULL,
  `referral_doctor` varchar(255) DEFAULT NULL,
  `referral_origin` varchar(255) DEFAULT NULL,
  `referral_name` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'aktif',
  `is_create` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `is_update` int(11) NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `deleted` datetime DEFAULT NULL,
  `delete_log` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_registrations_registration_no` (`registration_no`),
  KEY `idx_registrations_patient_id` (`patient_id`),
  KEY `idx_registrations_doctor_id` (`doctor_id`),
  KEY `idx_registrations_registration_date` (`registration_date`),
  KEY `idx_registrations_service_type` (`service_type`),
  KEY `idx_registrations_status` (`status`),
  KEY `idx_registrations_polyclinic_queue_id` (`polyclinic_queue_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_balances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `last_so_date` date DEFAULT NULL,
  `so_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `adjustment_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `received_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `issued_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_stock_balance` (`warehouse_id`,`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(40) NOT NULL,
  `name` varchar(200) NOT NULL,
  `unit` varchar(30) DEFAULT NULL,
  `min_stock` decimal(15,2) NOT NULL DEFAULT 0.00,
  `max_stock` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_stock_items_code` (`code`),
  KEY `idx_stock_items_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_warehouses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `validated_at` datetime DEFAULT NULL,
  `validated_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_stock_wh_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` varchar(255) NOT NULL DEFAULT '0',
  `hrga_employee_id` varchar(255) NOT NULL DEFAULT '0',
  `user_team` varchar(255) NOT NULL DEFAULT '0',
  `radius_id` varchar(255) NOT NULL DEFAULT '0',
  `telegram_chat_id` varchar(255) DEFAULT NULL,
  `zona` varchar(255) NOT NULL DEFAULT 'WIB',
  `working_hours` tinyint(3) unsigned DEFAULT NULL,
  `code` varchar(20) DEFAULT '0',
  `staff` varchar(20) DEFAULT NULL,
  `face` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT '0',
  `password` varchar(255) DEFAULT '0',
  `password_new` varchar(255) DEFAULT '0',
  `help_hint_pwd` varchar(255) DEFAULT '0',
  `telp` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `devision` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `project` varchar(255) DEFAULT NULL COMMENT 'Project UBS',
  `ubs` varchar(255) DEFAULT NULL COMMENT 'UBS SCM',
  `marketing_code` varchar(255) DEFAULT NULL,
  `project_status` enum('Y','N') DEFAULT 'N' COMMENT 'Akses project UBS',
  `approved` enum('Y','N') DEFAULT 'N',
  `approved_acc` enum('Y','N') DEFAULT 'N',
  `approved_ceo` enum('Y','N') DEFAULT 'N',
  `payment` enum('Y','N') DEFAULT 'N',
  `pettycash_full_access` enum('Y','N') DEFAULT 'N',
  `hris_approved` enum('Y','N') DEFAULT 'N',
  `hris_knowing` enum('Y','N') DEFAULT 'N',
  `hris_full_access` enum('Y','N') DEFAULT 'N',
  `rule` text DEFAULT NULL,
  `rules` text DEFAULT NULL COMMENT 'Hak akses terbaru',
  `rules_acc` text DEFAULT NULL COMMENT 'Hak akses Acc terbaru',
  `rule_acc` text DEFAULT NULL,
  `jurnal_type_acc` varchar(255) DEFAULT NULL,
  `change_password` enum('y','n') DEFAULT NULL,
  `change_password_date` datetime DEFAULT NULL,
  `reset` int(11) DEFAULT NULL,
  `reset_date` datetime DEFAULT NULL,
  `is_create` varchar(15) DEFAULT NULL,
  `is_update` varchar(15) DEFAULT NULL,
  `is_delete` varchar(15) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `is_active` enum('Y','N') DEFAULT 'Y',
  `is_log` text DEFAULT NULL,
  `who_create` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `who_update` varchar(50) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `status` varchar(5) DEFAULT '0',
  `level` varchar(5) DEFAULT '0',
  `visited` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=146 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_stock_summary` AS SELECT
 1 AS `warehouse_id`,
  1 AS `item_id`,
  1 AS `item_code`,
  1 AS `item_name`,
  1 AS `unit`,
  1 AS `last_so_date`,
  1 AS `so_qty`,
  1 AS `adjustment_qty`,
  1 AS `received_qty`,
  1 AS `issued_qty`,
  1 AS `closing_qty`,
  1 AS `min_stock`,
  1 AS `max_stock` */;
SET character_set_client = @saved_cs_client;
/*!50001 DROP VIEW IF EXISTS `v_stock_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013  SQL SECURITY INVOKER */
/*!50001 VIEW `v_stock_summary` AS select `b`.`warehouse_id` AS `warehouse_id`,`b`.`item_id` AS `item_id`,`i`.`code` AS `item_code`,`i`.`name` AS `item_name`,`i`.`unit` AS `unit`,`b`.`last_so_date` AS `last_so_date`,`b`.`so_qty` AS `so_qty`,`b`.`adjustment_qty` AS `adjustment_qty`,`b`.`received_qty` AS `received_qty`,`b`.`issued_qty` AS `issued_qty`,`b`.`so_qty` + `b`.`adjustment_qty` + `b`.`received_qty` - `b`.`issued_qty` AS `closing_qty`,`i`.`min_stock` AS `min_stock`,`i`.`max_stock` AS `max_stock` from (`stock_balances` `b` join `stock_items` `i` on(`i`.`id` = `b`.`item_id`)) where `i`.`is_active` = 1 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

SET FOREIGN_KEY_CHECKS=1;

INSERT INTO `users` (`id`,`code`,`name`,`password`,`password_new`,`rules`,`level`,`status`,`is_active`,`created`,`modified`)
VALUES (1,'admin','Administrator','$2y$10$yziYdNj97jn/x4PO0yIoOuXBxx3M7Vzf0iZbTDBP8VbsguNNHMjLe','$2y$10$yziYdNj97jn/x4PO0yIoOuXBxx3M7Vzf0iZbTDBP8VbsguNNHMjLe','bhpusages=index:1,add:1,view:1,delete:1,searchItems:1,searchRegistration:1;branches=index:1,view:1,add:1,edit:1,delete:1,xls:1;dashboard=index:1;doctors=searchModal:1,index:1,excel:1,view:1,add:1,edit:1,searchByPolyclinic:1,delete:1,listActive:1;doctorschedules=index:1,excel:1,view:1,add:1,edit:1,delete:1;healthfacilities=index:1,excel:1,view:1,add:1,edit:1,delete:1;insurances=index:1,excel:1,view:1,add:1,edit:1,delete:1;itemrequests=index:1,add:1,view:1,delete:1,searchItems:1;kartustok=index:1,searchMedicine:1;labexaminations=index:1,proses:1,tindakan:1;labhistories=index:1,searchPatient:1,detail:1;labmemos=index:1,add:1,delete:1;labnormalvalues=index:1,addValue:1,deleteTemplates:1,save:1,deleteValue:1,addTemplate:1,deleteTemplate:1,deleteChecked:1,copyFrom:1;labofficers=index:1;labrequisitions=index:1,add:1,view:1,delete:1,searchItems:1;labroresults=get:1;labsenderdoctors=index:1,save:1,delete:1;labstocks=index:1,warehouses:1,stock:1,validateDepot:1;labusages=index:1,add:1,view:1,delete:1,searchItems:1,searchRegistration:1;logisticstocks=index:1,warehouses:1,stocks:1,save:1,validateStock:1,searchItems:1;mastericd10=index:1,excel:1,view:1,add:1,import:1,edit:1,toggleActive:1,delete:1,search:1;mastericd9=index:1,excel:1,add:1,import:1,edit:1,view:1,toggleActive:1,delete:1,search:1;masterlaboratories=index:1,view:1,add:1,edit:1,delete:1;masterpatientreferrers=index:1,view:1,add:1,edit:1,delete:1,excel:1,toggleActive:1;masterpenjamins=index:1,view:1,add:1,edit:1,delete:1,excel:1,toggleActive:1;masterradiologies=index:1,view:1,add:1,edit:1,delete:1,toggleActive:1;mastertenagamedis=index:1,excel:1,toggleActive:1,view:1,add:1,edit:1,delete:1,searchModal:1,nextKode:1;mastertindakan=index:1,excel:1,view:1,add:1,edit:1,getNextKode:1,toggleActive:1,delete:1,search:1,searchDebug:1;mastertreatmentpackages=index:1,excel:1,view:1,add:1,edit:1,delete:1,toggleActive:1;medicalrecorddiagnoses=index:1,view:1,add:1,edit:1,delete:1;medicalrecords=index:1,searchPatients:1,backfillNoRm:1;medicalresumes=index:1,view:1,add:1,edit:1,delete:1;medicinegroups=index:1,view:1,add:1,edit:1,delete:1;medicinemerks=index:1,view:1,add:1,edit:1,delete:1;medicines=index:1,view:1,add:1,edit:1,delete:1,getGroupInfo:1;medicinestocks=index:1,toggleValid:1;orderlaboratories=index:1,view:1,add:1,edit:1,delete:1;orderprescriptions=index:1,view:1,add:1,edit:1,delete:1;orderradiologies=index:1,view:1,add:1,edit:1,delete:1;orders=index:1,antrianFarmasi:1,proses:1,form:1,detail:1,historyPasien:1,historyOrderDetail:1,cariPasien:1,cariObat:1,cariLaboratorium:1,kategoriLaboratorium:1,cariRadiologi:1;orderstatuses=index:1,view:1,add:1,edit:1,delete:1;outpatientactions=index:1,view:1,add:1,edit:1,delete:1;outpatientbillingitems=index:1,view:1,add:1,edit:1,delete:1;outpatientbillingpayments=index:1,view:1,add:1,edit:1,delete:1;outpatientbillings=index:1,kasir:1,kwitansi:1,tambahItem:1,hapusItem:1,toggleReadyTransfer:1,prosesBayar:1,reopen:1;outpatientcpptnotes=index:1,view:1,add:1,edit:1,delete:1;outpatientletters=index:1,view:1,add:1,edit:1,delete:1,getSuratSakit:1,simpanSuratSakit:1,getSuratKonsultasi:1,simpanSuratKonsultasi:1,getSuratJawabanKonsultasi:1,simpanSuratJawabanKonsultasi:1,cetakSuratSakit:1,cetakSuratKonsultasi:1,getSuratPengantarRawat:1,simpanSuratPengantarRawat:1,cetakSuratPengantarRawat:1,getSuratBuktiRajal:1,simpanSuratBuktiRajal:1,cetakSuratBuktiRajal:1,getSuratSkdp:1,simpanSuratSkdp:1,cetakSuratSkdp:1,getSuratKontrolBpjs:1,simpanSuratKontrolBpjs:1,cetakSuratKontrolBpjs:1,getSuratSehat:1,simpanSuratSehat:1,cetakSuratSehat:1;outpatientvisitemr=emr:1,saveVitalSigns:1,createReferralQueue:1,saveAnatomy:1,kajianAwal:1,simpanKajianAwal:1;outpatientvisits=display:1,displaySettings:1,doctorQueue:1,excel:1,index:1,view:1,add:1,edit:1,cppt:1,addCppt:1,deleteCppt:1,selesai:1,panggil:1,datang:1,batal:1,getSummary:1,resumeMedis:1,emr:1,searchIcd10:1,searchIcd9:1,delete:1,selesaiPeriksa:1,hasilPemeriksaan:1,getSuratSakit:1,simpanSuratSakit:1;patientadmissions=index:1;patienthistories=index:1,searchPatients:1,periksa:1,history:1;patients=index:1,excel:1,view:1,add:1,edit:1,delete:1;paymenthistories=index:1,searchPatients:1;personresponsibles=index:1,view:1,add:1,edit:1,delete:1;pharmacyitems=index:1,excel:1,view:1,add:1,edit:1,delete:1;polyclinicqueues=index:1,excel:1,display:1,view:1,pdf:1,add:1,getPolyclinicSchedule:1,searchPatients:1,mobileJkn:1,checkIn:1,callQueue:1,edit:1,delete:1,kiosk:1,kioskNew:1,createReferralQueue:1,searchPoly:1,searchPolyQueue:1;praregistrations=index:1,view:1,add:1,edit:1,delete:1,register:1,cancel:1;queuepolys=index:1,view:1,add:1,edit:1,delete:1;radiology=index:1,proses:1,tindakan:1,saveTindakan:1,biaya:1,searchMaster:1,searchItems:1,searchRegistration:1;radiologynormalvalues=index:1,save:1;registrations=index:1,getSummary:1,excel:1,view:1,add:1,addTemp:1,edit:1,delete:1,searchPatients:1,getKabupatens:1,getKecamatans:1,getKelurahans:1,previewRegistrationNo:1,getDoctorSchedules:1;users=login:1,logout:1,index:1,view:1,add:1,edit:1,changePassword:1,resetPassword:1,access:1,delete:1,addFace:1,resetFace:1,getSelect:1,getUsers:1,templateExcel:1',2,1,'Y',NOW(),NOW());
