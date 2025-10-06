CREATE DATABASE IF NOT EXISTS ambulance_alert;
USE ambulance_alert;

CREATE TABLE IF NOT EXISTS detections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  camera_id VARCHAR(100),
  timestamp DATETIME,
  image_path VARCHAR(255),
  confidence FLOAT,
  bbox VARCHAR(100), -- store as "x1,y1,x2,y2"
  lat DOUBLE NULL,
  lng DOUBLE NULL,
  alerted TINYINT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS intersections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  lat DOUBLE,
  lng DOUBLE,
  controller_endpoint VARCHAR(255) -- where to send signal commands
);

CREATE TABLE IF NOT EXISTS alerts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  detection_id INT,
  sent_at DATETIME,
  method VARCHAR(50), -- "sms","email","signal"
  status VARCHAR(50),
  details TEXT,
  FOREIGN KEY (detection_id) REFERENCES detections(id)
);
