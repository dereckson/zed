-- Adds two accounts (copyright holders from some of the test content).
-- Login/pass are dereckson / dereckson (1148) and jeronimo / jeronimo (6200)

REPLACE INTO users
    (user_id, username, user_password, user_active)
VALUES
    (1148, 'dereckson', MD5('dereckson'), 1),
    (6200, 'jeronimo', MD5('jeronimo'), 1);

REPLACE INTO persos
    (perso_id, perso_name, perso_nickname, perso_race, perso_sex,
     user_id, location_global, location_local)
VALUES
    (1148, 'Dereckson', 'dereckson', 'human', 'M',
     1148, 'B00001001', 'T2C3'),
    (6200, 'jeronimo', 'jeronimo', 'human', 'M',
     6200, 'B00001002', '(26, -11, -12)');

-- Empties the contents table and inserts content in the repository's info

DELETE FROM content_files WHERE perso_id = 1148 OR perso_id = 6200;

-- [1148] Tower artwork
-- TODO: add T2 content from prod server (into _files and _locations tables)

-- [1148] Composition test files
REPLACE INTO content_files
    (content_path, content_title, user_id, perso_id)
VALUES
    ('users/1148/BlueMetalDrakes.png', 'Blue metal drakes', 1148, 1148),
    ('users/1148/ktor.png', 'Ktor', 1148, 1148);

-- [1148] Item test (ringbell for tower appartments)
REPLACE INTO content_files
    (content_path, content_title, user_id, perso_id)
VALUES
    ('users/1148/plate.jpg', 'DcK plate', 1148, 1148),
    ('users/1148/warning.mp3', 'Warning sound', 1148, 1148);
    
-- [6200] core Ghost Metro content
REPLACE INTO content_files
    (content_path, content_title, user_id, perso_id)
VALUES
    ('users/6200/ghostmetro-A-01.jpg', 'Ghost Metro, set A, image 1', 6200, 6200),
    ('users/6200/ghostmetro-A-02.jpg', 'Ghost Metro, set A, image 2', 6200, 6200),
    ('users/6200/ghostmetro-A-03.jpg', 'Ghost Metro, set A, image 3', 6200, 6200),
    ('users/6200/ghostmetro-A-04.jpg', 'Ghost Metro, set A, image 4', 6200, 6200),
    ('users/6200/ghostmetro-A-05-a.jpg', 'Ghost Metro, set A, image 5a', 6200, 6200),
    ('users/6200/ghostmetro-A-05-b.jpg', 'Ghost Metro, set A, image 5b', 6200, 6200),
    ('users/6200/ghostmetro-A-05-c.jpg', 'Ghost Metro, set A, image 5c', 6200, 6200),
    ('users/6200/ghostmetro-A-06.png', 'Ghost Metro, set A, image 6', 6200, 6200),
    ('users/6200/ghostmetro-A-07.jpg', 'Ghost Metro, set A, image 7', 6200, 6200),
    ('users/6200/ghostmetro-A-08.jpg', 'Ghost Metro, set A, image 8', 6200, 6200),
    ('users/6200/ghostmetro-A-09.jpg', 'Ghost Metro, set A, image 9', 6200, 6200),
    ('users/6200/ghostmetro-A-10.jpg', 'Ghost Metro, set A, image 10', 6200, 6200),
    ('users/6200/ghostmetro-A-11.jpg', 'Ghost Metro, set A, image 11', 6200, 6200);