-- ---------------------------------------------------------------------------
-- DDL for table ACTIVITY_LOGS (MySQL / MariaDB / TOAD for MySQL)
-- ---------------------------------------------------------------------------

CREATE TABLE activity_logs (
  id              BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id     INT NOT NULL,                     -- from token / user
  user_id         VARCHAR(64) NULL,
  branch_id       INT NULL,
  device_id       VARCHAR(128) NULL,
  app_version     VARCHAR(32) NULL,
  platform        VARCHAR(16) NULL,                 -- android / ios
  client_log_id   BIGINT NOT NULL,                  -- device row id
  log_type        VARCHAR(40) NOT NULL,
  category        VARCHAR(80) NULL,
  source          VARCHAR(255) NULL,
  message         TEXT NULL,
  error_details   TEXT NULL,
  stack_trace     LONGTEXT NULL,
  status_code     INT NULL,
  extra_data      JSON NULL,
  event_at        DATETIME(3) NOT NULL,             -- from client created_at
  received_at     DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

  UNIQUE KEY uq_dedup (user_id, device_id, client_log_id),
  KEY ix_user     (user_id),
  KEY ix_branch   (branch_id),
  KEY ix_type     (log_type),
  KEY ix_event_at (event_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
