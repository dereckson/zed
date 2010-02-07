---
--- Empties the table and inserts again my flags
--- Dereckson
---

TRUNCATE `persos_flags`;

INSERT INTO `persos_flags` (`perso_id`, `flag_key`, `flag_value`)
VALUES
(5555, 'hypership.reached', '3'),
(5555, 'admin.pages.editor', '1'),
(5555, 'admin.api.keyprovider', '1'),
(1148, 'site.smartline.method', 'get'),
(5555, 'site.smartline.method', 'get');
