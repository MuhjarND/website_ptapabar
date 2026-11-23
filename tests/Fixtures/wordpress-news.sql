-- Synthetic phpMyAdmin-style data. The importer must not execute SQL.
DROP TABLE posts;
INSERT INTO `wp_terms` (`term_id`, `slug`) VALUES
(7, 'berita'), (8, 'artikel'), (9, 'pengumuman'), (20, 'rapat');
INSERT INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `parent`) VALUES
(17, 7, 'category', 0), (18, 8, 'category', 0), (19, 9, 'category', 0), (30, 20, 'category', 7);
INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`) VALUES
(100, 17), (101, 30), (102, 17), (103, 17), (104, 19), (105, 17), (105, 18), (106, 17);
INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES
(100, '_thumbnail_id', '900'), (900, '_wp_attached_file', '2024/01/Gambar Lama.jpg'),
(901, '_wp_attached_file', '2024/01/rapat.jpg'), (902, '_wp_attached_file', '2024/01/galeri.jpg'),
(903, '_wp_attached_file', '2024/01/referensi.pdf'), (904, '_wp_attached_file', '2024/01/tidak-terkait.jpg'),
(905, '_wp_attached_file', '2024/01/file-terkunci.jpg');
INSERT INTO `wp_posts` (`ID`, `post_status`, `post_type`, `post_password`, `post_title`, `post_content`, `post_date`, `post_name`, `post_excerpt`, `post_parent`) VALUES
(100, 'publish', 'post', '', 'Rapat &amp; Pelayanan', '<p style="color:red">Isi berita dengan ''kutip''.</p><script>unsafe marker</script><img src="https://example.test/image.jpg"><a href="javascript:alert(1)">Tautan</a><img src="https://i0.wp.com/pta-papuabarat.go.id/wp-content/uploads/2024/01/rapat-300x200.jpg" data-id="901" alt="Rapat"><img src="/wp-content/uploads/2024/01/inline.jpg" alt="Foto dalam berita">[gallery ids="902"]', '2024-01-10 09:00:00', 'rapat-pelayanan', '', 0),
(101, 'publish', 'post', '', 'Arsip Rapat Lanjutan', '<p>Isi lanjutan.</p><img src="http://www.pta-papuabarat.go.id/wp-content/uploads/2024/01/lanjutan.jpg" onerror="alert(1)">', '2024-01-11 09:00:00', 'arsip-rapat-lanjutan', '', 0),
(102, 'draft', 'post', '', 'Draf', 'Isi draf.', '2024-01-10 09:00:00', 'draf', '', 0),
(103, 'publish', 'post', 'secret', 'Terkunci', 'Isi privat.', '2024-01-10 09:00:00', 'terkunci', '', 0),
(104, 'publish', 'post', '', 'Pengumuman', 'Isi pengumuman.', '2024-01-10 09:00:00', 'pengumuman', '', 0),
(105, 'publish', 'post', '', 'Artikel Ganda', '<p>Isi artikel.</p><a href="/wp-content/uploads/2024/01/artikel.pdf">Buka artikel PDF</a>', '2024-01-10 09:00:00', 'artikel', '', 0),
(106, 'publish', 'page', '', 'Halaman', 'Isi halaman.', '2024-01-10 09:00:00', 'halaman', '', 0),
(901, 'inherit', 'attachment', '', 'Rapat', '', '2024-01-10 09:01:00', 'foto-rapat', 'Foto rapat', 100),
(902, 'inherit', 'attachment', '', 'Galeri', '', '2024-01-10 09:01:00', 'foto-galeri', 'Foto galeri', 0),
(903, 'inherit', 'attachment', '', 'Referensi', '', '2024-01-10 09:01:00', 'referensi', 'Referensi rapat', 100),
(904, 'inherit', 'attachment', '', 'Tidak terkait', '', '2024-01-10 09:01:00', 'tidak-terkait', '', 103),
(905, 'inherit', 'attachment', 'secret', 'Terkunci', '', '2024-01-10 09:01:00', 'file-terkunci', '', 100);
