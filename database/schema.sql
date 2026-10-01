-- Praxia schema (MySQL 5.7+ / MariaDB 10.3+). Run by setup.php, or import manually in phpMyAdmin.
CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(60) PRIMARY KEY,
  setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS enquiries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  source ENUM('quote','proposal','contact','popup') NOT NULL,
  name VARCHAR(120) NULL,
  company VARCHAR(160) NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(40) NULL,
  asset_type VARCHAR(80) NULL,
  asset_value VARCHAR(40) NULL,
  audience VARCHAR(80) NULL,
  message TEXT NULL,
  status ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  ip VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_status (status),
  KEY idx_ip_created (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS job_openings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(160) NOT NULL,
  department VARCHAR(80) NOT NULL,
  job_type VARCHAR(40) NOT NULL DEFAULT 'Full-time',
  description TEXT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS applications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_id INT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(40) NULL,
  message TEXT NULL,
  cv_file VARCHAR(120) NULL,
  cv_original VARCHAR(190) NULL,
  status ENUM('new','shortlisted','rejected') NOT NULL DEFAULT 'new',
  ip VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_ip_created (ip, created_at),
  CONSTRAINT fk_app_job FOREIGN KEY (job_id) REFERENCES job_openings(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS faqs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category ENUM('lease','fin','process','lender') NOT NULL,
  question VARCHAR(255) NOT NULL,
  answer TEXT NOT NULL,
  show_on_home TINYINT(1) NOT NULL DEFAULT 0,
  home_answer TEXT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS case_studies (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NULL UNIQUE,
  body MEDIUMTEXT NULL,
  summary TEXT NULL,
  client_name VARCHAR(160) NULL,
  location VARCHAR(160) NULL,
  service VARCHAR(160) NULL,
  duration VARCHAR(80) NULL,
  results TEXT NULL,
  quote_text TEXT NULL,
  quote_author VARCHAR(120) NULL,
  quote_role VARCHAR(160) NULL,
  seo_title VARCHAR(190) NULL,
  seo_desc VARCHAR(300) NULL,
  image_url VARCHAR(500) NOT NULL,
  image_alt VARCHAR(160) NOT NULL DEFAULT '',
  tag VARCHAR(160) NOT NULL,
  title VARCHAR(255) NOT NULL,
  metrics TEXT NULL COMMENT 'one per line: value|label',
  challenge TEXT NULL,
  solution TEXT NULL,
  outcome TEXT NULL,
  home_order INT NULL COMMENT 'set to show on the home page',
  home_tag VARCHAR(160) NULL,
  home_title VARCHAR(255) NULL,
  home_kpis TEXT NULL COMMENT 'one per line: value|label',
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS team_members (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tier ENUM('founder','leader') NOT NULL,
  initials VARCHAR(4) NOT NULL,
  name VARCHAR(120) NOT NULL,
  role VARCHAR(160) NOT NULL,
  photo_url VARCHAR(500) NULL,
  bio TEXT NULL COMMENT 'one bullet per line',
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS testimonials (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quote TEXT NOT NULL,
  author VARCHAR(120) NOT NULL,
  designation VARCHAR(190) NOT NULL,
  initials VARCHAR(4) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS investors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  description VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS partners (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  logo_url VARCHAR(500) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stats (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(120) NOT NULL,
  stat_value INT NOT NULL DEFAULT 0,
  prefix VARCHAR(10) NOT NULL DEFAULT '',
  suffix VARCHAR(10) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS asset_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  placement ENUM('home','product') NOT NULL,
  title VARCHAR(120) NOT NULL,
  description VARCHAR(255) NOT NULL,
  image_url VARCHAR(500) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS media (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  file_path VARCHAR(190) NOT NULL,
  original_name VARCHAR(190) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS features (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  placement ENUM('home','careers','diligence') NOT NULL,
  icon VARCHAR(30) NOT NULL DEFAULT 'card',
  title VARCHAR(120) NOT NULL,
  description VARCHAR(500) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS steps (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  placement ENUM('home','product') NOT NULL,
  title VARCHAR(120) NOT NULL,
  description VARCHAR(300) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS compare_rows (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  aspect VARCHAR(80) NOT NULL,
  loan_text VARCHAR(160) NOT NULL,
  lease_text VARCHAR(160) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS case_study_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  case_study_id INT UNSIGNED NOT NULL,
  image_url VARCHAR(500) NOT NULL,
  caption VARCHAR(190) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_csi_case FOREIGN KEY (case_study_id) REFERENCES case_studies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS menu_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  location ENUM('header','footer_company','footer_explore') NOT NULL,
  label VARCHAR(80) NOT NULL,
  url VARCHAR(300) NOT NULL,
  new_tab TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
