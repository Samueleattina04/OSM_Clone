-- Modulo Valutazioni: raccolta del feedback dei clienti al termine di un intervento

CREATE TABLE IF NOT EXISTS `in_valutazioni` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_intervento` int(11) NOT NULL,
    `token` varchar(64) NOT NULL,
    `voto` tinyint(1) DEFAULT NULL,
    `commento` text DEFAULT NULL,
    `richiesta_at` datetime DEFAULT NULL,
    `risposta_at` datetime DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `in_valutazioni_token_unique` (`token`),
    KEY `in_valutazioni_id_intervento_index` (`id_intervento`),
    CONSTRAINT `in_valutazioni_intervento_fk` FOREIGN KEY (`id_intervento`) REFERENCES `in_interventi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Registrazione del modulo
INSERT INTO `zz_modules` (`name`, `directory`, `attachments_directory`, `options`, `options2`, `icon`, `version`, `compatibility`, `order`, `parent`, `default`, `enabled`) VALUES
('Valutazioni', 'valutazioni', 'valutazioni', 'SELECT |select| FROM `in_valutazioni` INNER JOIN `in_interventi` ON `in_interventi`.`id` = `in_valutazioni`.`id_intervento` LEFT JOIN `an_anagrafiche` ON `an_anagrafiche`.`idanagrafica` = `in_interventi`.`idanagrafica` WHERE 1=1 AND `in_valutazioni`.`deleted_at` IS NULL HAVING 2=2', '', 'fa fa-star', '2.11.0', '2.11.0', 1, (SELECT `id` FROM (SELECT `id` FROM `zz_modules` WHERE `name` = 'Interventi') AS `t`), 1, 1);

INSERT INTO `zz_modules_lang` (`id_lang`, `id_record`, `title`, `meta_title`) VALUES
(1, LAST_INSERT_ID(), 'Valutazioni', 'Valutazione - {codice}'),
(2, LAST_INSERT_ID(), 'Ratings', 'Rating - {codice}');

-- Colonne della lista
INSERT INTO `zz_views` (`id_module`, `name`, `query`, `order`, `search`, `slow`, `visible`, `default`) VALUES
((SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni'), 'id', 'in_valutazioni.id', 0, 0, 0, 0, 0),
((SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni'), 'codice', 'in_interventi.codice', 1, 1, 0, 1, 1),
((SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni'), 'cliente', 'an_anagrafiche.ragione_sociale', 2, 1, 0, 1, 1),
((SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni'), 'voto', 'in_valutazioni.voto', 3, 1, 0, 1, 1),
((SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni'), 'richiesta_at', 'in_valutazioni.richiesta_at', 4, 1, 0, 1, 1),
((SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni'), 'risposta_at', 'in_valutazioni.risposta_at', 5, 1, 0, 1, 1);

INSERT INTO `zz_views_lang` (`id_lang`, `id_record`, `title`) VALUES
(1, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'id'), '#'),
(2, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'id'), '#'),
(1, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'codice'), 'Intervento'),
(2, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'codice'), 'Job'),
(1, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'cliente'), 'Cliente'),
(2, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'cliente'), 'Customer'),
(1, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'voto'), 'Voto'),
(2, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'voto'), 'Rating'),
(1, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'richiesta_at'), 'Richiesta il'),
(2, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'richiesta_at'), 'Requested at'),
(1, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'risposta_at'), 'Risposto il'),
(2, (SELECT `id` FROM `zz_views` WHERE `id_module` = (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni') AND `name` = 'risposta_at'), 'Answered at');

-- Widget dashboard: valutazione media dei clienti
INSERT INTO `zz_widgets` (`name`, `type`, `id_module`, `location`, `class`, `query`, `bgcolor`, `icon`, `print_link`, `more_link`, `more_link_type`, `php_include`, `enabled`, `order`, `help`) VALUES
('Valutazione media clienti', 'stats', (SELECT `id` FROM `zz_modules` WHERE `name` = 'Valutazioni'), 'controller_top', 'col-md-3', 'SELECT CONCAT(COALESCE(ROUND(AVG(`voto`), 1), 0), \' / 5\') AS dato FROM `in_valutazioni` WHERE 1=1 AND `voto` IS NOT NULL AND `deleted_at` IS NULL HAVING 2=2', 'warning', 'fa fa-star', '', '', 'javascript', '', 1, 1, NULL);

INSERT INTO `zz_widgets_lang` (`id_lang`, `id_record`, `title`, `text`) VALUES
(1, LAST_INSERT_ID(), 'Valutazione media clienti', 'Valutazione media clienti'),
(2, LAST_INSERT_ID(), 'Average customer rating', 'Average customer rating');
