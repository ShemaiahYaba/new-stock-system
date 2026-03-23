-- Migration 025: Add bundle-tracking columns to stock_ledger for KZinc
-- Keeps existing inflow_pieces/outflow_pieces/balance_pieces intact for data validation.
-- New columns track the same movements in bundles (1 bundle = KZINC_PIECES_PER_BUNDLE pieces).

ALTER TABLE stock_ledger
    ADD COLUMN inflow_bundles  INT NOT NULL DEFAULT 0 AFTER inflow_pieces,
    ADD COLUMN outflow_bundles INT NOT NULL DEFAULT 0 AFTER outflow_pieces,
    ADD COLUMN balance_bundles INT NOT NULL DEFAULT 0 AFTER balance_pieces;
