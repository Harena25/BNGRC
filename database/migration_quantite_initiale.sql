-- Migration: Add quantite_initiale column to bn_besoin
-- This stores the original requested quantity (never changes after creation)
-- quantite = current remaining, quantite_initiale = original request

USE bngrc;

-- Add the column if it doesn't exist
ALTER TABLE bn_besoin 
ADD COLUMN IF NOT EXISTS quantite_initiale INT NOT NULL DEFAULT 0 AFTER quantite;

-- For existing records, recalculate quantite_initiale = quantite + sum(distributions)
UPDATE bn_besoin b
SET quantite_initiale = b.quantite + COALESCE(
    (SELECT SUM(d.quantite_distribuee) 
     FROM bn_distribution d 
     WHERE d.besoin_id = b.id), 
    0
);

-- Verify the update
SELECT id, ville_id, article_id, quantite, quantite_initiale, status_id
FROM bn_besoin
WHERE quantite_initiale != quantite
LIMIT 10;
