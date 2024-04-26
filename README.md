J'ai fait un ManyToMany pour Album/Artist car plusieurs artistes peuvent être sur un album et plusieurs albums peuvent appartenir à un artiste


Données utilisées pour l'eval :

INSERT INTO album (name, year) VALUES
  ('A Foreigner Affair', 1981),
  ('A Night at the Opera', 1975),
  ('Imagine', 1971),
  ('Thriller', 1982),
  ('Like a Virgin', 1984);
INSERT INTO artist (name) VALUES
  ('Foreigner'),
  ('Queen'),
  ('John Lennon'),
  ('Michael Jackson'),
  ('Madonna');
INSERT INTO album_artist (album_id, artist_id) VALUES
  (1, 1),  -- Foreigner Affair - Foreigner
  (2, 2),  -- A Night at the Opera - Queen
  (3, 3),  -- Imagine - John Lennon
  (4, 4),  -- Thriller - Michael Jackson
  (5, 5);  -- Like a Virgin - Madonna
INSERT INTO track (album_id, title, duration) VALUES
  (1, 'My Sharona', 235),
  (1, 'Hotel California', 330),
  (1, 'Heartache Tonight', 300),
  (2, 'Bohemian Rhapsody', 579),
  (2, 'We Will Rock You', 201),
  (2, 'We Are the Champions', 292),
  (3, 'Imagine', 308),
  (3, 'Let It Be', 208),
  (3, 'Yesterday', 209),
  (4, 'Billie Jean', 457),
  (4, 'Thriller', 525),
  (4, 'Beat It', 300),
  (5, 'Like a Virgin', 357),
  (5, 'Material Girl', 242),
  (5, 'Vogue', 534);

