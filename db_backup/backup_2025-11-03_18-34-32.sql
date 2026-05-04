-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: rental_portal
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL AUTO_INCREMENT,
  `fullName` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `institution` enum('ukzn','dut','unisa','richfield') NOT NULL,
  `studentId` varchar(50) NOT NULL,
  `income` decimal(10,2) NOT NULL,
  `incomeSource` enum('nsfas','parents','job','bursary','other') NOT NULL,
  `incomeProof` varchar(255) DEFAULT NULL,
  `idCopy` varchar(255) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Denied') NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`application_id`),
  KEY `property_id` (`property_id`),
  CONSTRAINT `fk_property` FOREIGN KEY (`property_id`) REFERENCES `properties1` (`property_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
INSERT INTO `applications` VALUES (1,'John Doe','0783141079','richfield','402307410',2000.00,'','uploads/Screenshot 2025-10-03 230914.png','uploads/Screenshot 2025-10-03 230716.png',7,'2025-10-23 19:16:17','Approved'),(2,'John Doe','0783141079','richfield','402307410',2000.00,'','uploads/Screenshot 2025-10-03 230914.png','uploads/Screenshot 2025-10-03 230716.png',7,'2025-10-23 19:18:33','Approved'),(3,'John Doe','0783141079','richfield','402307410',2000.00,'','uploads/Screenshot 2025-10-03 230914.png','uploads/Screenshot 2025-10-03 230716.png',7,'2025-10-23 19:19:57','Denied'),(4,'2g3','h3h4g35hg','','3h3h53h',354.00,'','uploads/Screenshot 2025-10-03 230716.png','uploads/Screenshot 2025-10-03 230914.png',6,'2025-10-23 19:21:36','Pending'),(5,'2g3','h3h4g35hg','','3h3h53h',354.00,'','uploads/Screenshot 2025-10-03 230716.png','uploads/Screenshot 2025-10-03 230914.png',6,'2025-10-23 19:22:02','Approved'),(6,'2g3','h3h4g35hg','','3h3h53h',354.00,'','uploads/Screenshot 2025-10-03 230914.png','uploads/Screenshot 2025-10-03 230914.png',4,'2025-10-27 09:38:46','Pending'),(7,'Kemmy-Leigh Moodley','0671675189','','2401232',85000.00,'','uploads/Screenshot 2025-10-03 230716.png','uploads/Screenshot 2025-10-03 230914.png',7,'2025-10-30 17:34:55','Approved'),(8,'Melaine Pillay','0810420694','','1017421',40000.00,'','uploads/Screenshot 2025-10-12 212324.png','uploads/Screenshot 2025-10-12 220410.png',4,'2025-10-30 17:53:13','Pending'),(9,'Melaine Pillay','0810420694','','1017421',40000.00,'','uploads/Screenshot 2025-10-03 230716.png','uploads/Screenshot 2025-10-13 205257.png',9,'2025-10-30 17:57:55','Denied'),(10,'Bongani Bright Bophela ','123456789','','1234',10000.00,'','uploads/Screenshot 2025-10-03 230716.png','uploads/Screenshot 2025-10-03 230716.png',6,'2025-11-03 09:29:25','Pending'),(11,'XAN','1234567891','dut','123456',2000.00,'other','uploads/Screenshot 2025-10-03 230914.png','uploads/Screenshot 2025-10-03 230716.png',10,'2025-11-03 09:50:08','Approved');
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `properties1`
--

DROP TABLE IF EXISTS `properties1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `properties1` (
  `property_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `property_type` enum('Apartment','House','Shared Room','Studio') NOT NULL,
  `rent` decimal(10,2) NOT NULL,
  `bedrooms` int(11) NOT NULL,
  `bathrooms` int(11) NOT NULL,
  `square_meters` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `area` enum('Near UKZN','Near DUT','Durban Central','Berea','Other') NOT NULL,
  `distance` decimal(5,2) NOT NULL,
  `description` text DEFAULT NULL,
  `landlord_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`property_id`),
  KEY `landlord_id` (`landlord_id`),
  CONSTRAINT `fk_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `properties1`
--

LOCK TABLES `properties1` WRITE;
/*!40000 ALTER TABLE `properties1` DISABLE KEYS */;
INSERT INTO `properties1` VALUES (1,'Three Bedroom apartment ','Apartment',2000.00,3,2,100,'123 Random Road','Durban Central',5.20,'Well-furnished apartment with a city view, Wi-Fi and laundry facilities. ',1,'2025-10-18 06:16:54'),(3,'Neat Apartmen','Apartment',2000.00,2,1,100,'123 Gotham Road','Near DUT',0.50,'A cool and neat place with all the cool stuff. ',1,'2025-10-19 17:05:45'),(4,'Cool place','House',24554.00,1,2,123,'123 Cool street','Berea',1.20,'Cool place with cool stuff',1,'2025-10-19 17:10:12'),(5,'2f322','House',1234.00,1,2,123,'1123 FNQIF road','Other',12.00,'f12g3bv3f3t45v  5y hh ',3,'2025-10-19 17:17:52'),(6,'Batcave','Studio',1000.00,10,1,200,'123 Arkham Lane','Near UKZN',1.50,'Garage for batmobile, hangar for batjet, butler included. ',10,'2025-10-23 09:45:16'),(7,'Foster\'s Home for Imaginary Friends','House',2599.00,10,5,300,'1 Cartoon Drive','Durban Central',10.00,'A friendly home for imaginary friends. There\'s a blue one, and a red tall one, and a furry monster one, and a girl one that looks like a palm tree. ',10,'2025-10-23 09:56:58'),(8,'House','House',1000.00,1,2,200,'13 Ave','Durban Central',500.00,'gfqegkqefhe',10,'2025-10-27 09:41:38'),(9,'Unicorn Villa','House',10000.00,15,7,2000,'97 Zhulye Avenue','Other',2.00,'The most magnificent dwelling abode set upon a mystical and magical land. The property is surrounded by an enchanted forest, rich in anti-ageing properties and brimming with newly sprouted flowers that tickle the toe beans of furry 4-legged creatures. Each room exudes the warmth of noon-time sun-kissed fur, boasting sky windows on the top level to absorb every drop of vitamin D. Bathrooms are safe havens of solace, relaxation and pure ecstasy - each coming equipped with a personal masseuse/asswipe(r) and self-cleaning toilets that fluff your anoos with a fresh coat of powder once done. Meals are available on days when you \"just can\'t\" - but on the days that you can - the most delicious, nutritious, fresh ingredients fill the fridges and storehouses. Call 082 xxx xxxx for more.',12,'2025-10-30 17:47:52'),(10,'WIN','Apartment',2000.00,2,1,3000,'123 OGWINI FLAT','Durban Central',1.00,'the best',15,'2025-11-03 09:48:13'),(11,'tjgn','Apartment',113.00,1,1,12,'123','Durban Central',500.00,'Description ',19,'2025-11-03 10:36:46');
/*!40000 ALTER TABLE `properties1` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_type` enum('Student/Tenant','Landlord') NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'david','pillay','dp@gmail.com','Landlord','$2y$10$7MApImRyTtSqRPERWR6Gguylp2MxeAqoYCwAr5Mtj5VP2rGbCRClG','2025-10-17 07:13:27'),(3,'Ellie','Mullah','ellie@gmail.com','Landlord','$2y$10$j/qE.oi/87nzayAUyMl92O7zyd/cfcsF5FHQann1rkFMbhZrtvjQS','2025-10-19 17:16:45'),(4,'Dave','Pillay','dp1@gmail.com','','$2y$10$oYxc6q8TIWnmqW2TpuwsoOLA4G0Gc3NKay6EHhT79hcGsvTDt3onO','2025-10-21 18:10:59'),(5,'Ribah','latif','Ribah@gmail.com','','$2y$10$BATu808z9xczPDMDXLqyJ..pWzVDJv1Gfimso6Jh4zooK3W/ixUs2','2025-10-22 08:34:01'),(6,'XAN','ALI','XANALI@GMAIL.COM','','$2y$10$y5iPmRigIU03z2ojfZXPyeiIsgDrmNZcQ.8v97thll.a0Ibruf9DG','2025-10-23 07:59:44'),(7,'XAN2','ALI','XAN2ALI@GMAIL.COM','Landlord','$2y$10$xaCQWUPQtYMvkxf2z7EAPejNeHiCu9/ADQ0KpIOdwfXyblL3HFkN2','2025-10-23 08:03:34'),(8,'John','Doe','johndoestudent@gmail.com','','$2y$10$hxtaab2ky6S2Fw1vIfAxuObeOO45C2gAhp6y4fIzPKS1JPu7.PaVW','2025-10-23 09:37:31'),(10,'john','doe','johndoeowner@gmail.com','Landlord','$2y$10$1SRiK8M/MlXTSlCiEyGNKu6QSAIUZY5bfqBuxYChTiJcjaj2/jOB6','2025-10-23 09:41:52'),(11,'Kemmy-Leigh','Moodley','keighmoodley@gmail.com','','$2y$10$oj1EGCDj3xA/CcWyJ9hHO.SSR6L1omK73IrUCZjWKK/2Grl9J3mPm','2025-10-30 18:30:19'),(12,'Kuku','Nintu','teenytinybabydoo@sleepykitty.com','Landlord','$2y$10$uwXzsdzVi3bkr6l9Xuf3s.FNQK42TBJTNmKuHew9TXCE/UXt4JOTa','2025-10-30 18:39:52'),(13,'Melaine','Pillay','melainep23@gmail.com','','$2y$10$2LauchzkB40RVp2Z0/y1lexcSKAW8ZnfgM6yItMJYLacyA39hsFNK','2025-10-30 18:49:49'),(14,'xolisa','alister','xolisaalister@gmail.com','','$2y$10$yTBrx2IhXVWK4GOSKu9qpeylxSvbbE1c7wo.F/xwRCPKVg85wLwuK','2025-11-03 10:46:13'),(15,'xolisa','alister','xolisaalister01@gmail.com','Landlord','$2y$10$QR89QcvEGDMCB5UovKVYw.geoF3sbhL0XXIo5YGj1hDWnSKSu3j0m','2025-11-03 10:46:48'),(18,'Jane','Doe','janedoe@gmail','','$2y$10$IoquedYdWxAZiLptr1UhMOgjlkcstjEyBPmPbQ7FocBJpwfTy7xgK','2025-11-03 11:26:32'),(19,'Jane','Doe','janedoe@gmail.com','Landlord','$2y$10$YGl9P6NeB0qwX6Gp/qMqnu2nUUlFGVYGBVYlJ6YIqzm9vpM0an3ee','2025-11-03 11:36:04');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-03 19:34:32
