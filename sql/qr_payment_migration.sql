-- ============================================================
-- MIGRACIÓN: Sistema de Pago por QR
-- Ejecutar sobre la base de datos 'ecommerce'
-- ============================================================

USE `ecommerce`;

CREATE TABLE IF NOT EXISTS `qr_pagos` (
  `id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `token`       VARCHAR(64)   NOT NULL,
  `ciCliente`   VARCHAR(20)   NOT NULL,
  `carrito`     JSON          NOT NULL,
  `estado`      ENUM('pendiente','confirmado','completado','expirado') NOT NULL DEFAULT 'pendiente',
  `nroVenta`    INT(11)       DEFAULT NULL,
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at`  TIMESTAMP     NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_token` (`token`),
  KEY `fk_qrpagos_cliente` (`ciCliente`),
  KEY `idx_estado` (`estado`),
  CONSTRAINT `fk_qrpagos_cliente`
    FOREIGN KEY (`ciCliente`) REFERENCES `cliente` (`ci`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;