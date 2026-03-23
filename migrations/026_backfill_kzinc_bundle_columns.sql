-- Migration 026: Backfill inflow_bundles / outflow_bundles / balance_bundles
-- for existing KZinc stock ledger rows that were recorded before migration 025.
-- Converts piece values to bundles using KZINC_PIECES_PER_BUNDLE = 15.

UPDATE stock_ledger
SET
    inflow_bundles  = FLOOR(inflow_pieces  / 15),
    outflow_bundles = FLOOR(outflow_pieces / 15),
    balance_bundles = FLOOR(balance_pieces / 15)
WHERE inflow_pieces > 0
   OR outflow_pieces > 0
   OR balance_pieces > 0;
