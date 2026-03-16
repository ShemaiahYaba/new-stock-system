-- Migration 024: Add piece-tracking columns to stock_ledger for KZinc entries
-- Existing meters columns remain for Alusteel/Aluminium; piece columns default to 0 for those rows.

ALTER TABLE stock_ledger
    ADD COLUMN inflow_pieces  INT NOT NULL DEFAULT 0 AFTER balance_meters,
    ADD COLUMN outflow_pieces INT NOT NULL DEFAULT 0 AFTER inflow_pieces,
    ADD COLUMN balance_pieces INT NOT NULL DEFAULT 0 AFTER outflow_pieces;
