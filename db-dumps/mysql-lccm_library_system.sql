
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
DROP TABLE IF EXISTS `author_book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `author_book` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` bigint(20) unsigned NOT NULL,
  `book_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `author_book_author_id_foreign` (`author_id`),
  KEY `author_book_book_id_foreign` (`book_id`),
  CONSTRAINT `author_book_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `author_book_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `author_book` WRITE;
/*!40000 ALTER TABLE `author_book` DISABLE KEYS */;
/*!40000 ALTER TABLE `author_book` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `authors` WRITE;
/*!40000 ALTER TABLE `authors` DISABLE KEYS */;
/*!40000 ALTER TABLE `authors` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `book_publication_place`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_publication_place` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `publication_place_id` bigint(20) unsigned NOT NULL,
  `book_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `book_publication_place_publication_place_id_foreign` (`publication_place_id`),
  KEY `book_publication_place_book_id_foreign` (`book_id`),
  CONSTRAINT `book_publication_place_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_publication_place_publication_place_id_foreign` FOREIGN KEY (`publication_place_id`) REFERENCES `publication_places` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `book_publication_place` WRITE;
/*!40000 ALTER TABLE `book_publication_place` DISABLE KEYS */;
/*!40000 ALTER TABLE `book_publication_place` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `book_publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_publisher` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `publisher_id` bigint(20) unsigned NOT NULL,
  `book_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `book_publisher_publisher_id_foreign` (`publisher_id`),
  KEY `book_publisher_book_id_foreign` (`book_id`),
  CONSTRAINT `book_publisher_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_publisher_publisher_id_foreign` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `book_publisher` WRITE;
/*!40000 ALTER TABLE `book_publisher` DISABLE KEYS */;
/*!40000 ALTER TABLE `book_publisher` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `books` (
  `id` int(10) unsigned NOT NULL,
  `copy` int(11) NOT NULL DEFAULT 1,
  `available_copy` int(11) NOT NULL DEFAULT 1,
  `amount` decimal(11,2) NOT NULL DEFAULT 0.00,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `publication_year` varchar(255) DEFAULT NULL,
  `edition` varchar(255) DEFAULT NULL,
  `call` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `acquisition_method` enum('donated','purchased') DEFAULT NULL,
  `type` enum('book','e-book') DEFAULT NULL,
  `dewey_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `books_dewey_id_foreign` (`dewey_id`),
  CONSTRAINT `books_dewey_id_foreign` FOREIGN KEY (`dewey_id`) REFERENCES `deweys` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES (1,9021,1,90.11,'Iure Aut Eaque Eum Et.','Error Nesciunt Non Quia Eaque Recusandae Amet.',NULL,'1','LAUDANTIUM','Ducimus consectetur ad nesciunt atque nam harum aperiam eos. Voluptate in ullam iusto est delectus fugit maxime. Quia rerum impedit fugit ut numquam. Mollitia voluptatum natus et.','Dolorem quo porro nobis ipsam.','purchased','e-book',500,'2023-05-13 03:28:20','2023-05-20 00:12:30',NULL),(2,8772,0,59.92,'Sit ad odit maiores.','Expedita fugit recusandae ad fugiat cupiditate ea.',NULL,'6','eum','Dolorem aut alias deleniti et. Quia consequuntur nobis blanditiis et facilis. Magni corrupti reiciendis expedita quaerat quisquam rem.','Aspernatur nam et dicta ea eos totam.','donated','book',700,'2023-05-13 03:28:20','2023-05-19 23:51:03',NULL),(3,8483,0,99.16,'Non ea ratione hic distinctio nisi et.','Sit sint voluptas est delectus.',NULL,'9','necessitatibus','Et recusandae ab veritatis nulla voluptatem est et quia. Dolorem neque delectus quia dolorem aut quasi assumenda. Ut odit qui omnis omnis qui consequatur.','Et nostrum quod et impedit quia soluta voluptatum.','donated','e-book',800,'2023-05-13 03:28:20','2023-05-19 23:51:03',NULL),(4,128,1,10.65,'Cumque aut dolores qui et et.','Unde aut ea cumque sunt et qui.',NULL,'4','consequuntur','Est et expedita quas. Voluptatem vel aut dolorum quod nostrum qui aut. Dolores vel maiores iste molestias. Ipsam tempore at a veritatis quia enim quos. Est ducimus illo impedit adipisci aut cupiditate illo.','Qui dolores vel necessitatibus.','donated','book',600,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(5,1124,1,55.54,'Distinctio molestiae rerum voluptatem eos ut nam.','Et quibusdam iure perspiciatis veritatis quod laborum.',NULL,'3','in','Laborum dolores aliquid nam excepturi provident sed sed. Sequi non magnam ut repudiandae quas quasi. Sint eveniet quod quisquam facilis quia eos. Blanditiis dolorum vel et expedita deleniti vitae molestias.','Rerum et est porro ea.','purchased','book',200,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(6,5524,1,30.55,'Fugiat quae non commodi nam natus et.','Ut illum ad excepturi suscipit sit iure.',NULL,'9','odio','Expedita id et eum ut. Nam voluptas nisi ducimus quam est. Quidem qui rerum minus quia ea in aut.','Nulla voluptatem est accusantium nemo voluptatem excepturi.','purchased','book',100,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(7,9998,1,79.32,'Ad magni possimus ducimus perferendis illum eveniet laborum.','Voluptatem dolores cum dolores unde velit qui minima.',NULL,'4','qui','Dolorem ut eos asperiores odit unde dolores. Et non fugiat autem quisquam. Quo laborum sit ut ut aperiam. Placeat pariatur voluptatem sit.','Eum error nesciunt enim porro error aliquid inventore.','donated','e-book',300,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(8,2332,1,89.00,'Aut et ipsa perferendis aut magnam ea.','Explicabo voluptas commodi eos et.',NULL,'1','voluptas','Quis assumenda vel ea voluptates repudiandae. Et voluptatum nam voluptatem dolor. Aspernatur vero delectus fuga quibusdam repellat voluptas nam.','Sit atque dolorem et et.','donated','book',600,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(9,8538,1,67.13,'Culpa labore itaque sapiente id molestiae ea minus.','Iure quae dolores enim temporibus.',NULL,'8','et','Consectetur est aut dolorem ut velit vel rem. Expedita nihil cum fuga non ipsa quisquam quis. Laboriosam non aut ad eius voluptatem.','Similique magni unde ipsum sint.','purchased','e-book',200,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(10,7335,1,39.70,'Quibusdam dolores quod distinctio ipsam possimus.','Quis ab nostrum consequatur id atque et adipisci assumenda.',NULL,'9','accusamus','Rerum et doloremque aut eligendi animi iure esse. Et hic vero illo consequatur quae. Est aut rerum et cupiditate voluptatem voluptas.','Ipsum et itaque omnis molestiae reiciendis.','donated','e-book',200,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(11,8821,1,61.19,'Ipsum deserunt suscipit saepe deserunt explicabo odio.','Provident quo autem accusantium hic.',NULL,'1','laboriosam','Officiis possimus quas laboriosam sit et minus illum. Et eos veritatis quis omnis soluta labore consequatur. Expedita tempore quis nulla ratione autem nostrum. Suscipit dignissimos qui animi voluptatem est.','Nihil iusto unde accusantium.','purchased','e-book',600,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(12,9790,1,59.11,'Laborum velit accusantium commodi.','Est consequatur ut aut deserunt id.',NULL,'8','fuga','Amet rem maiores est occaecati vel porro. Porro exercitationem voluptatibus necessitatibus amet. Et voluptas architecto praesentium voluptate eius dolor rerum.','Modi reprehenderit rerum nemo reiciendis et occaecati similique.','purchased','book',600,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(13,3742,1,24.54,'Natus mollitia quis iusto rerum in.','Quisquam consequatur praesentium eum dolorem aut magni voluptas.',NULL,'10','inventore','Ratione sunt aut et temporibus. Tenetur nesciunt ullam et ut. Quia ea suscipit magni et omnis assumenda ipsa. Totam dolor dicta sed nam.','Amet facilis molestiae esse dolore mollitia.','donated','book',500,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(14,2485,1,83.47,'Commodi ipsum expedita rerum omnis nobis illum.','Dolore et sed beatae dolores quo voluptas veritatis atque.',NULL,'2','a','Qui necessitatibus natus at. Ut quisquam omnis ab voluptas. Natus similique eius dolor est ea.','Alias voluptas sunt impedit voluptas nulla tempora.','donated','book',200,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(15,3393,1,22.39,'Voluptatem sapiente ut minima.','Rerum modi repellat et doloremque eligendi.',NULL,'7','et','Aut nobis sint eaque dolor illum. Consequatur omnis dolorum ut non qui in eveniet. Quis et aperiam ut magnam dolorum enim velit.','Velit aut vero sequi qui ex nam non.','donated','book',300,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(16,9843,1,76.82,'Exercitationem voluptatem aspernatur molestiae consequatur sed.','Error officia perspiciatis explicabo iste.',NULL,'5','consequatur','Doloribus ea qui ut nihil. Quis rem iste soluta debitis nemo expedita sint. Nobis necessitatibus et ut sequi qui sunt.','Doloribus rerum velit dolorum tenetur.','purchased','e-book',300,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(17,4380,1,23.97,'Quis aliquam sit dicta labore iure.','Sit odit modi accusantium explicabo eveniet et sed.',NULL,'6','dolore','Quisquam veritatis molestiae corrupti corporis magnam deleniti. Eum dolores vel pariatur est aut labore explicabo. Aut modi et in. Libero et soluta dignissimos debitis amet corporis rerum. Occaecati eum officia maiores qui eaque consequatur.','Voluptatem eius assumenda ducimus incidunt nemo.','purchased','book',300,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(18,1500,1,59.08,'Ut ea ea earum vel voluptas vero et.','Praesentium aut soluta amet soluta modi tenetur sit.',NULL,'9','quo','Laboriosam voluptas iusto porro dolor quam veritatis ex. Facilis quia commodi et id. Eos voluptas rerum voluptates quas. Reprehenderit et exercitationem aut incidunt quia accusantium.','Temporibus velit ut ipsum eveniet earum distinctio.','purchased','book',400,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(19,5523,1,70.61,'Quisquam repudiandae est incidunt enim.','Nihil ex reiciendis aut eveniet voluptatem.',NULL,'2','velit','Deleniti quidem aliquam vel soluta eveniet. Qui ipsa excepturi cupiditate eos qui. Ex illum qui ea.','Repellendus debitis facilis nihil velit autem.','donated','e-book',800,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(20,1637,1,32.50,'Natus tempore hic et sed optio assumenda omnis.','Officia asperiores et nulla facere aspernatur.',NULL,'9','rerum','Accusantium voluptatem autem iusto quisquam et fuga. Molestias fuga quia et. Nobis quod molestiae nostrum.','Perferendis velit ea omnis sunt labore fuga earum accusamus.','donated','e-book',800,'2023-05-13 03:28:20','2023-05-13 03:28:20',NULL);
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `collectibles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collectibles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` bigint(20) unsigned NOT NULL,
  `collectible_id` bigint(20) unsigned NOT NULL,
  `collectible_type` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `collectibles_collection_id_foreign` (`collection_id`),
  CONSTRAINT `collectibles_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `collectibles` WRITE;
/*!40000 ALTER TABLE `collectibles` DISABLE KEYS */;
/*!40000 ALTER TABLE `collectibles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `items_count` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `collections` WRITE;
/*!40000 ALTER TABLE `collections` DISABLE KEYS */;
/*!40000 ALTER TABLE `collections` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `deweys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deweys` (
  `id` int(10) unsigned NOT NULL,
  `classification` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `deweys` WRITE;
/*!40000 ALTER TABLE `deweys` DISABLE KEYS */;
INSERT INTO `deweys` VALUES (100,'Philosophy','2023-05-13 03:28:19','2023-05-13 03:28:19'),(200,'Religion','2023-05-13 03:28:19','2023-05-13 03:28:19'),(300,'Social Sciences','2023-05-13 03:28:19','2023-05-13 03:28:19'),(400,'Language','2023-05-13 03:28:19','2023-05-13 03:28:19'),(500,'Science','2023-05-13 03:28:19','2023-05-13 03:28:19'),(600,'Technology','2023-05-13 03:28:19','2023-05-13 03:28:19'),(700,'Arts and Recreation','2023-05-13 03:28:19','2023-05-13 03:28:19'),(800,'Literature','2023-05-13 03:28:19','2023-05-13 03:28:19'),(900,'History and Geography','2023-05-13 03:28:19','2023-05-13 03:28:19');
/*!40000 ALTER TABLE `deweys` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `imageable_id` int(11) NOT NULL,
  `imageable_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `available_quantity` int(11) NOT NULL DEFAULT 1,
  `amount` decimal(11,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `materials` WRITE;
/*!40000 ALTER TABLE `materials` DISABLE KEYS */;
INSERT INTO `materials` VALUES (1,'Map','','ebooks',NULL,NULL,5,0,0.00,'2023-05-13 03:28:21','2023-05-20 00:12:30',NULL),(2,'Newspaper','','artifacts',NULL,NULL,1,0,0.00,'2023-05-13 03:28:21','2023-05-20 00:12:30',NULL),(3,'Globe','','','','',1,1,0.00,'2023-05-13 03:28:21','2023-05-13 03:28:21',NULL);
/*!40000 ALTER TABLE `materials` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2023_02_25_071207_create_images_table',1),(6,'2023_02_25_100821_create_permission_tables',1),(7,'2023_02_26_053652_create_deweys_table',1),(8,'2023_02_26_073433_create_books_table',1),(9,'2023_02_27_052233_create_authors_table',1),(10,'2023_02_27_052527_create_author_book_table',1),(11,'2023_02_27_055035_create_publication_places_table',1),(12,'2023_02_27_055145_create_book_publication_place_table',1),(13,'2023_02_27_055640_create_publishers_table',1),(14,'2023_02_27_060323_create_book_publisher_table',1),(15,'2023_02_28_085546_create_materials_table',1),(16,'2023_03_05_062600_create_reservations_table',1),(17,'2023_03_05_064101_create_reservation_items_table',1),(18,'2023_03_05_070312_create_statuses_table',1),(19,'2023_03_13_091301_create_transactions_table',1),(20,'2023_03_14_073421_create_transaction_items_table',1),(21,'2023_03_14_073711_create_transaction_fees_table',1),(22,'2023_04_14_062611_create_collections_table',1),(23,'2023_04_15_061511_create_collectibles_table',1),(24,'2023_05_13_080046_create_shelves_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(3,'App\\Models\\User',3),(4,'App\\Models\\User',4),(5,'App\\Models\\User',5);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `publication_places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publication_places` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `publication_places` WRITE;
/*!40000 ALTER TABLE `publication_places` DISABLE KEYS */;
/*!40000 ALTER TABLE `publication_places` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `publishers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publishers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `publishers` WRITE;
/*!40000 ALTER TABLE `publishers` DISABLE KEYS */;
/*!40000 ALTER TABLE `publishers` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `reservation_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` bigint(20) unsigned NOT NULL,
  `reservation_itemable_id` bigint(20) unsigned NOT NULL,
  `reservation_itemable_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservation_items_reservation_id_foreign` (`reservation_id`),
  CONSTRAINT `reservation_items_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `reservation_items` WRITE;
/*!40000 ALTER TABLE `reservation_items` DISABLE KEYS */;
INSERT INTO `reservation_items` VALUES (1,2,1,'App\\Models\\Book','2023-05-19 22:49:55','2023-05-19 23:48:46','2023-05-19 23:48:46'),(2,2,1,'App\\Models\\Material','2023-05-19 22:49:55','2023-05-19 23:48:46','2023-05-19 23:48:46'),(3,3,1,'App\\Models\\Book','2023-05-19 22:57:25','2023-05-19 23:48:47','2023-05-19 23:48:47'),(4,3,1,'App\\Models\\Material','2023-05-19 22:57:25','2023-05-19 23:48:47','2023-05-19 23:48:47'),(5,5,4,'App\\Models\\Book','2023-05-19 23:09:08','2023-05-19 23:48:47','2023-05-19 23:48:47'),(6,6,5,'App\\Models\\Book','2023-05-19 23:09:50','2023-05-19 23:48:47','2023-05-19 23:48:47'),(7,8,3,'App\\Models\\Material','2023-05-19 23:29:22','2023-05-19 23:48:47','2023-05-19 23:48:47'),(8,9,2,'App\\Models\\Material','2023-05-19 23:29:55','2023-05-19 23:48:47','2023-05-19 23:48:47'),(9,10,1,'App\\Models\\Book','2023-05-19 23:49:47','2023-05-19 23:49:47',NULL),(10,10,1,'App\\Models\\Material','2023-05-19 23:49:47','2023-05-19 23:49:47',NULL),(11,10,2,'App\\Models\\Material','2023-05-19 23:49:47','2023-05-19 23:49:47',NULL),(12,11,1,'App\\Models\\Material','2023-05-19 23:50:45','2023-05-19 23:50:45',NULL),(13,11,3,'App\\Models\\Book','2023-05-19 23:50:45','2023-05-19 23:50:45',NULL),(14,11,2,'App\\Models\\Book','2023-05-19 23:50:45','2023-05-19 23:50:45',NULL);
/*!40000 ALTER TABLE `reservation_items` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `cancel_by` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservations_user_id_foreign` (`user_id`),
  CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,1,NULL,'2023-05-19 22:24:08','2023-05-19 22:52:23','2023-05-19 22:52:23'),(2,1,NULL,'2023-05-19 22:49:55','2023-05-19 23:48:46','2023-05-19 23:48:46'),(3,1,NULL,'2023-05-19 22:57:25','2023-05-19 23:48:47','2023-05-19 23:48:47'),(4,1,NULL,'2023-05-19 23:07:12','2023-05-19 23:10:14','2023-05-19 23:10:14'),(5,1,NULL,'2023-05-19 23:09:08','2023-05-19 23:48:47','2023-05-19 23:48:47'),(6,1,NULL,'2023-05-19 23:09:50','2023-05-19 23:48:47','2023-05-19 23:48:47'),(7,1,NULL,'2023-05-19 23:28:00','2023-05-19 23:48:47','2023-05-19 23:48:47'),(8,1,NULL,'2023-05-19 23:29:22','2023-05-19 23:48:47','2023-05-19 23:48:47'),(9,1,NULL,'2023-05-19 23:29:55','2023-05-19 23:48:47','2023-05-19 23:48:47'),(10,1,NULL,'2023-05-19 23:49:47','2023-05-19 23:49:47',NULL),(11,1,NULL,'2023-05-19 23:50:45','2023-05-19 23:50:45',NULL);
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','2023-05-13 03:28:19','2023-05-13 03:28:19'),(2,'librarian','web','2023-05-13 03:28:19','2023-05-13 03:28:19'),(3,'faculty','web','2023-05-13 03:28:19','2023-05-13 03:28:19'),(4,'employee','web','2023-05-13 03:28:19','2023-05-13 03:28:19'),(5,'student','web','2023-05-13 03:28:19','2023-05-13 03:28:19');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `shelves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shelves` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `amount` decimal(11,2) NOT NULL DEFAULT 0.00,
  `shelfable_id` bigint(20) unsigned NOT NULL,
  `shelfable_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shelves_user_id_foreign` (`user_id`),
  CONSTRAINT `shelves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `shelves` WRITE;
/*!40000 ALTER TABLE `shelves` DISABLE KEYS */;
/*!40000 ALTER TABLE `shelves` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statuses_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `statuses_user_id_foreign` (`user_id`),
  CONSTRAINT `statuses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES (1,'pending',1,NULL,'App\\Models\\Reservation',1,'2023-05-19 22:24:08','2023-05-19 22:24:08'),(2,'pending',1,NULL,'App\\Models\\Reservation',2,'2023-05-19 22:49:55','2023-05-19 22:49:55'),(3,'pending',1,NULL,'App\\Models\\Reservation',3,'2023-05-19 22:57:25','2023-05-19 22:57:25'),(4,'pending',1,NULL,'App\\Models\\Reservation',4,'2023-05-19 23:07:12','2023-05-19 23:07:12'),(5,'pending',1,NULL,'App\\Models\\Reservation',5,'2023-05-19 23:09:08','2023-05-19 23:09:08'),(6,'pending',1,NULL,'App\\Models\\Reservation',6,'2023-05-19 23:09:50','2023-05-19 23:09:50'),(7,'pending',1,NULL,'App\\Models\\Reservation',7,'2023-05-19 23:28:00','2023-05-19 23:28:00'),(8,'pending',1,NULL,'App\\Models\\Reservation',8,'2023-05-19 23:29:22','2023-05-19 23:29:22'),(9,'pending',1,NULL,'App\\Models\\Reservation',9,'2023-05-19 23:29:55','2023-05-19 23:29:55'),(10,'for pick-up',1,NULL,'App\\Models\\Transaction',1,'2023-05-19 23:47:43','2023-05-19 23:47:43'),(11,'accepted',1,NULL,'App\\Models\\ReservationItem',3,'2023-05-19 23:47:43','2023-05-19 23:47:43'),(12,'accepted',1,NULL,'App\\Models\\ReservationItem',4,'2023-05-19 23:47:43','2023-05-19 23:47:43'),(13,'accepted',1,NULL,'App\\Models\\Reservation',3,'2023-05-19 23:47:43','2023-05-19 23:47:43'),(14,'pending',1,NULL,'App\\Models\\Reservation',10,'2023-05-19 23:49:47','2023-05-19 23:49:47'),(15,'pending',1,NULL,'App\\Models\\Reservation',11,'2023-05-19 23:50:45','2023-05-19 23:50:45'),(16,'for pick-up',1,NULL,'App\\Models\\Transaction',2,'2023-05-19 23:51:03','2023-05-19 23:51:03'),(17,'accepted',1,NULL,'App\\Models\\ReservationItem',12,'2023-05-19 23:51:03','2023-05-19 23:51:03'),(18,'accepted',1,NULL,'App\\Models\\ReservationItem',13,'2023-05-19 23:51:03','2023-05-19 23:51:03'),(19,'accepted',1,NULL,'App\\Models\\ReservationItem',14,'2023-05-19 23:51:03','2023-05-19 23:51:03'),(20,'accepted',1,NULL,'App\\Models\\Reservation',11,'2023-05-19 23:51:03','2023-05-19 23:51:03'),(21,'for pick-up',1,NULL,'App\\Models\\Transaction',3,'2023-05-19 23:51:16','2023-05-19 23:51:16'),(22,'accepted',1,NULL,'App\\Models\\ReservationItem',9,'2023-05-19 23:51:16','2023-05-19 23:51:16'),(23,'declined',1,NULL,'App\\Models\\ReservationItem',10,'2023-05-19 23:51:16','2023-05-19 23:51:16'),(24,'accepted',1,NULL,'App\\Models\\ReservationItem',11,'2023-05-19 23:51:16','2023-05-19 23:51:16'),(25,'partially accepted',1,NULL,'App\\Models\\Reservation',10,'2023-05-19 23:51:17','2023-05-19 23:51:17'),(26,'pending',1,NULL,'App\\Models\\Reservation',10,'2023-05-19 23:51:41','2023-05-19 23:51:41'),(27,'pending',1,NULL,'App\\Models\\Reservation',11,'2023-05-20 00:01:54','2023-05-20 00:01:54'),(28,'for pick-up',1,NULL,'App\\Models\\Transaction',4,'2023-05-20 00:09:41','2023-05-20 00:09:41'),(29,'accepted',1,NULL,'App\\Models\\ReservationItem',12,'2023-05-20 00:09:41','2023-05-20 00:09:41'),(30,'declined',1,NULL,'App\\Models\\ReservationItem',13,'2023-05-20 00:09:41','2023-05-20 00:09:41'),(31,'declined',1,NULL,'App\\Models\\ReservationItem',14,'2023-05-20 00:09:41','2023-05-20 00:09:41'),(32,'partially accepted',1,NULL,'App\\Models\\Reservation',11,'2023-05-20 00:09:41','2023-05-20 00:09:41'),(33,'pending',1,NULL,'App\\Models\\Reservation',11,'2023-05-20 00:09:56','2023-05-20 00:09:56'),(34,'for pick-up',1,NULL,'App\\Models\\Transaction',5,'2023-05-20 00:12:14','2023-05-20 00:12:14'),(35,'accepted',1,NULL,'App\\Models\\ReservationItem',12,'2023-05-20 00:12:14','2023-05-20 00:12:14'),(36,'declined',1,NULL,'App\\Models\\ReservationItem',13,'2023-05-20 00:12:14','2023-05-20 00:12:14'),(37,'declined',1,NULL,'App\\Models\\ReservationItem',14,'2023-05-20 00:12:14','2023-05-20 00:12:14'),(38,'partially accepted',1,NULL,'App\\Models\\Reservation',11,'2023-05-20 00:12:14','2023-05-20 00:12:14'),(39,'pending',1,NULL,'App\\Models\\Reservation',11,'2023-05-20 00:12:22','2023-05-20 00:12:22'),(40,'for pick-up',1,NULL,'App\\Models\\Transaction',6,'2023-05-20 00:12:30','2023-05-20 00:12:30'),(41,'accepted',1,NULL,'App\\Models\\ReservationItem',9,'2023-05-20 00:12:30','2023-05-20 00:12:30'),(42,'accepted',1,NULL,'App\\Models\\ReservationItem',10,'2023-05-20 00:12:30','2023-05-20 00:12:30'),(43,'accepted',1,NULL,'App\\Models\\ReservationItem',11,'2023-05-20 00:12:30','2023-05-20 00:12:30'),(44,'accepted',1,NULL,'App\\Models\\Reservation',10,'2023-05-20 00:12:30','2023-05-20 00:12:30');
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `transaction_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_fees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(11,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_fees_transaction_id_foreign` (`transaction_id`),
  CONSTRAINT `transaction_fees_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `transaction_fees` WRITE;
/*!40000 ALTER TABLE `transaction_fees` DISABLE KEYS */;
/*!40000 ALTER TABLE `transaction_fees` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `transaction_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) unsigned NOT NULL,
  `transaction_itemable_id` bigint(20) unsigned NOT NULL,
  `transaction_itemable_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_items_transaction_id_foreign` (`transaction_id`),
  CONSTRAINT `transaction_items_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `transaction_items` WRITE;
/*!40000 ALTER TABLE `transaction_items` DISABLE KEYS */;
INSERT INTO `transaction_items` VALUES (1,1,1,'App\\Models\\Book','2023-05-19 23:47:43','2023-05-19 23:47:43',NULL),(2,1,1,'App\\Models\\Material','2023-05-19 23:47:43','2023-05-19 23:47:43',NULL),(10,6,1,'App\\Models\\Book','2023-05-20 00:12:30','2023-05-20 00:12:30',NULL),(11,6,1,'App\\Models\\Material','2023-05-20 00:12:30','2023-05-20 00:12:30',NULL),(12,6,2,'App\\Models\\Material','2023-05-20 00:12:30','2023-05-20 00:12:30',NULL);
/*!40000 ALTER TABLE `transaction_items` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `balance` decimal(11,2) NOT NULL DEFAULT 0.00,
  `fee` decimal(11,2) NOT NULL DEFAULT 0.00,
  `type` enum('reserved','on-site') NOT NULL,
  `date_due` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_reservation_id_foreign` (`reservation_id`),
  KEY `transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `transactions_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,3,1,0.00,0.00,'reserved',NULL,NULL,'2023-05-19 23:47:43','2023-05-19 23:47:43'),(6,10,1,0.00,0.00,'reserved',NULL,NULL,'2023-05-20 00:12:30','2023-05-20 00:12:30');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Andrea','Magsumbol','andimags@example.com','$2y$10$KqiPAqGH4BBafVobvCffIOrYDrPYLsc755lfUvbsh56IZHO29kxba','2023-05-13 03:28:20','2023-05-13 03:28:20',NULL),(2,'Ailsa Danielle','Bajen','danielle.bajen@example.com','$2y$10$zpjoEQrqh1hURSs/8UN31.I/iB.Z.DSc1Dt6WyjflLMGHiZFBuovK','2023-05-13 03:28:21','2023-05-13 03:28:21',NULL),(3,'Catherine Sophie','Fajardo','cath.fajardo@example.com','$2y$10$PnRJTmcS3fYNETsPtTGoeeKC8NKvKhOWjO67WsfboD7JWrWIBTx0S','2023-05-13 03:28:21','2023-05-13 03:28:21',NULL),(4,'Kathleen','Ponce','ponce.kath@example.com','$2y$10$FicA798d1w6/PZz7vDa4ae3XvwkDcbucBpgplzqD/nuKoP4FmNjS.','2023-05-13 03:28:21','2023-05-13 03:28:21',NULL),(5,'Gherie Lynne','Pascual','chinchin@example.com','$2y$10$hw55Y8toueyaTBO2/rda9.JJnu1deN7THW8AzI8hN7T5H9jSqmeDW','2023-05-13 03:28:21','2023-05-13 03:28:21',NULL);
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

