-- MySQL 8.0+ DDL for iAudit schema
-- Recommended: CREATE DATABASE iaudit DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE iaudit;

SET NAMES utf8mb4;

-- Drop in dependency order
DROP TABLE IF EXISTS question_nc;
DROP TABLE IF EXISTS question;
DROP TABLE IF EXISTS criteria_table;
DROP TABLE IF EXISTS text_block;
DROP TABLE IF EXISTS template_reference;
DROP TABLE IF EXISTS template;
DROP TABLE IF EXISTS subheading;
DROP TABLE IF EXISTS heading;
DROP TABLE IF EXISTS category;

-- Lookups
CREATE TABLE heading (
  heading_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  UNIQUE KEY uq_heading_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE subheading (
  subheading_id INT AUTO_INCREMENT PRIMARY KEY,
  heading_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  UNIQUE KEY uq_subheading (heading_id, name),
  CONSTRAINT fk_subheading_heading
    FOREIGN KEY (heading_id) REFERENCES heading(heading_id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE category (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  UNIQUE KEY uq_category_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Templates
CREATE TABLE template (
  template_id INT AUTO_INCREMENT PRIMARY KEY,
  template_code VARCHAR(255) NOT NULL,
  report_title TEXT,
  department TEXT,
  UNIQUE KEY uq_template_code (template_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Note: using template_code (varchar) as FK so it matches the exported CSV
CREATE TABLE template_reference (
  template_ref_id INT AUTO_INCREMENT PRIMARY KEY,
  template_code VARCHAR(255) NOT NULL,
  reference_code VARCHAR(255) NOT NULL,
  UNIQUE KEY uq_template_reference (template_code, reference_code),
  CONSTRAINT fk_template_reference_template
    FOREIGN KEY (template_code) REFERENCES template(template_code)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reusable text
CREATE TABLE text_block (
  text_block_id INT AUTO_INCREMENT PRIMARY KEY,
  reference_code VARCHAR(255) NOT NULL,
  main_heading VARCHAR(255) NOT NULL,
  body TEXT,
  KEY idx_text_block_ref (reference_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Criteria tables/questions
CREATE TABLE criteria_table (
  criteria_id INT AUTO_INCREMENT PRIMARY KEY,
  reference_code VARCHAR(255) NOT NULL,
  main_heading VARCHAR(255) NOT NULL,
  table_heading VARCHAR(255) NOT NULL,
  question TEXT NOT NULL,
  KEY idx_criteria_ref (reference_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Question bank (all QTN sheets)
CREATE TABLE question (
  question_id INT AUTO_INCREMENT PRIMARY KEY,
  topic ENUM('IPM Docs','PCL','Pest Obs','EFK','RG','CRT') NOT NULL,
  reference_code VARCHAR(255),
  heading_id INT NULL,
  subheading_id INT NULL,
  category_id INT NULL,
  question_text TEXT,
  information_text TEXT,
  KEY idx_question_topic (topic),
  KEY idx_question_ref (reference_code),
  CONSTRAINT fk_question_heading
    FOREIGN KEY (heading_id) REFERENCES heading(heading_id),
  CONSTRAINT fk_question_subheading
    FOREIGN KEY (subheading_id) REFERENCES subheading(subheading_id),
  CONSTRAINT fk_question_category
    FOREIGN KEY (category_id) REFERENCES category(category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE question_nc (
  question_id INT PRIMARY KEY,
  nc_heading TEXT,
  nc_text TEXT,
  nc_rem_hd TEXT,
  nc_rem_text TEXT,
  nc_con_hd TEXT,
  nc_con_text TEXT,
  nc_usph_hd TEXT,
  nc_usph_text TEXT,
  nc_ipm_hd TEXT,
  nc_ipm_ref TEXT,
  CONSTRAINT fk_question_nc_question
    FOREIGN KEY (question_id) REFERENCES question(question_id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
