
SET foreign_key_checks = 0;
TRUNCATE TABLE matches_form;
INSERT INTO `matches_form` (`id`, `active`, `name`, `application_alert_emails`, description)
VALUES ('1', 1, 'test', 'EEE@EEEDQEW.nls', '');

    TRUNCATE TABLE matches;
INSERT INTO `matches` VALUES
(1,1,1,'f1_Yearsofexperience657162','Years of experience', null,1,'BIGGER_THAN',1,0,0,'2023-12-07 06:12:07','2023-12-07 06:12:07'),
(2,1,1,'f1_Programminglanguages657162','Programming languages',null,1,'MULTIPLE_CHOOSE',2,0,0,'2023-12-07 06:12:07','2023-12-07 06:12:07'),
(3,1,1,'f1_Favouriteworkingday657162','Favourite working day',null, null,'MENU',3,0,0,'2023-12-07 06:12:07','2023-12-07 06:12:07')
;


INSERT INTO `matches_options` VALUES
(null, 2, 'php',1,'2023-12-07 06:24:22','2023-12-07 06:24:22'),
(null, 2,'java',2,'2023-12-07 06:24:22','2023-12-07 06:24:22'),
(null,2,'c++',3,'2023-12-07 06:24:22','2023-12-07 06:24:22'),
(null,3,'Monday',1,'2023-12-07 06:24:22','2023-12-07 06:24:22'),
(null,3,'Tuesday',2,'2023-12-07 06:24:22','2023-12-07 06:24:22'),
(null,3,'Friday',3,'2023-12-07 06:24:22','2023-12-07 06:24:22')
;
TRUNCATE TABLE matches_profile_status;
INSERT INTO `matches_profile_status` (id, `label`, `active`, `ordering`) VALUES (1, 'aangemeld', '1', '0');
INSERT INTO `matches_profile_status` (`id`, `label`, `active`, `ordering`) VALUES (2, 'actief', '1', '1');
INSERT INTO `matches_profile_status` (`id`, `label`, `active`, `ordering`) VALUES ('3', 'niet actief', '1', '2');
INSERT INTO `matches_profile_status` (`id`, `label`, `active`, `ordering`) VALUES ('4', 'geblokkeerd', '1', '3');
INSERT INTO `matches_profile_status` (`id`, `label`, `active`, `ordering`, `deleted`)
VALUES ('5', 'deleted', '1', '4', 1);

INSERT INTO `content_texts` (`id`, `text_key`, `link`, `description`, `text`) VALUES
(1, 'AANMELDEN_HOME_PAGINA', 'https://aanmelden.test.nl/', 'Welkom tekst bij het aanmelden en de formulier te kiezen', '<p><span style=\"font-size: 14pt;\"><span style=\"color: rgb(53, 152, 219);\">Demo site</span> is de plek waar de Vereniging Paardrijden Gehandicapten (VPG) haar activiteiten organiseert. Meer dan 250 valide en minder valide ruiters rijden er elke week met veel plezier paard. Dit is mogelijk dankzij de inzet van een grote groep enthousiaste vrijwilligers, die ook de mogelijkheid heeft om zelf paard te rijden. Het is een bruisende club van valide en invalide paardrij-liefhebbers. De VPG verzorgt zes dagen per week paardrijlessen, voornamelijk op de doordeweekse avonden en op woensdag en zaterdag overdag. Iedere dag heeft een eigen team dat zorgdraagt voor een onvergetelijke paardrij-ervaring!</span></p>'),
(2, 'BEDANKT VOOR HET AANMELDEN', 'https://aanmelden.test.nl/', 'Tekst na het aanmelden van het formulier om te bedanken en verdere informatie.', '<p class=\"MsoNormal\">Hartelijk dank voor je interesse in de Prins Willem Alexander m en de Vereniging Paardrijden Gehandicapten. Wij nemen contact met je op om de mogelijkheden verder te bespreken. Aangezien wij vrijwel geheel op vrijwilligers draaien, kan dit een weekje duren. Dank voor je begrip en tot snel.</p>'),
(3, 'FOOTER', 'https://aanmelden.test.nl/', 'Footer op elke pagina', '<p>Copyright Vpg - Demo site 2024</p>\n<p><span style=\"font-size: 10pt;\">m geopend: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;A: Loosdrechtdreef 9, 1108 AZ, Amsterdam Zuido</span><span style=\"font-size: 10pt;\">ost</span><br><span style=\"font-size: 10pt;\">ma. di. do. vanaf 16.00uur &nbsp; &nbsp; &nbsp; &nbsp; E: info@test.nl</span><br><span style=\"font-size: 10pt;\">wo. vr. za. vanaf 12.00uur. &nbsp; &nbsp; &nbsp; &nbsp;Tel m : 020 69 79 701</span></p>');



SET foreign_key_checks = 1;