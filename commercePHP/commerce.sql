-- Creazione del database
CREATE DATABASE commerce;
USE commerce;

-- Tabella dei prodotti
CREATE TABLE products (
    name VARCHAR(255) PRIMARY KEY,
    price VARCHAR(255),
    description VARCHAR(1024),
    mainImg VARCHAR(255),
    typeP VARCHAR(255)
);

-- Tabella dei colori dei prodotti
CREATE TABLE productsColors (
    color VARCHAR(255),
    name VARCHAR(255),
    img VARCHAR(255),
    FOREIGN KEY (name) REFERENCES products(name),
    PRIMARY KEY (color, name)
);

-- Tabella delle taglie per i pantaloni (Slacks)
CREATE TABLE Slacksize (
    typeS VARCHAR(255) PRIMARY KEY,
    lengthS VARCHAR(255),
    inseam VARCHAR(255),
    waist VARCHAR(255),
    thigh_width VARCHAR(255),
    hem VARCHAR(255)
);

-- Tabella per i pantaloni (Slacks)
CREATE TABLE Slacks (
    color VARCHAR(255),
    name VARCHAR(255),
    typeS VARCHAR(255),
    quantity INT,
    FOREIGN KEY (color) REFERENCES productsColors(color),
    FOREIGN KEY (name) REFERENCES products(name),
    FOREIGN KEY (typeS) REFERENCES Slacksize(typeS),
    PRIMARY KEY(color, name, typeS)
);

-- Tabella delle taglie per le camicie (Shirts)
CREATE TABLE ShirtSize (
    typeS VARCHAR(255) PRIMARY KEY,
    chest VARCHAR(255),
    shoulder VARCHAR(255),
    sleeve VARCHAR(255)
);

-- Tabella delle taglie per le scarpe (Shoes)
CREATE TABLE ShoesSize (
    typeS VARCHAR(255) PRIMARY KEY,
    lengthS VARCHAR(255),
    widthS VARCHAR(255)
);

-- Tabella delle taglie per le borse (Bags)
CREATE TABLE BagSize (
    typeS VARCHAR(255) PRIMARY KEY,
    height VARCHAR(255),
    width VARCHAR(255),
    depth VARCHAR(255)
);

-- Tabella per le scarpe (Shoes)
CREATE TABLE Shoes (
    color VARCHAR(255),
    name VARCHAR(255),
    typeS VARCHAR(255),
    quantity INT,
    FOREIGN KEY (color) REFERENCES productsColors(color),
    FOREIGN KEY (name) REFERENCES products(name),
    FOREIGN KEY (typeS) REFERENCES ShoesSize(typeS),
    PRIMARY KEY(color, name, typeS)
);

-- Tabella per le borse (Bags)
CREATE TABLE Bags (
    color VARCHAR(255),
    name VARCHAR(255),
    typeS VARCHAR(255),
    quantity INT,
    FOREIGN KEY (color) REFERENCES productsColors(color),
    FOREIGN KEY (name) REFERENCES products(name),
    FOREIGN KEY (typeS) references BagSize(typeS),
    PRIMARY KEY(color, name, typeS)
);

-- Tabella per le camicie (Shirts)
CREATE TABLE Shirts (
    color VARCHAR(255),
    name VARCHAR(255),
    typeS VARCHAR(255),
    quantity INT,
    FOREIGN KEY (color) REFERENCES productsColors(color),
    FOREIGN KEY (name) REFERENCES products(name),
    FOREIGN KEY (typeS) references ShirtSize(typeS),
    PRIMARY KEY(color, name, typeS)
);



-- Tabella degli utenti (solo username e password)
CREATE TABLE users (
    username VARCHAR(255) PRIMARY KEY,
    password_hash VARCHAR(255) NOT NULL
);

-- Tabella dei carrelli (collegata all'utente)
CREATE TABLE carts (
    cart_id INT auto_increment,
    username VARCHAR(255) NOT NULL,
    primary key (cart_id, username),
    FOREIGN KEY (username) REFERENCES users(username)
);

CREATE TABLE cart_items (
    cart_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    color VARCHAR(255),
    size VARCHAR(255),
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (cart_id) REFERENCES carts(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (product_name) REFERENCES products(name),
    PRIMARY KEY(cart_id, product_name, color, size)
);


-- Inserimento nella tabella 'products'
INSERT INTO products (name, price, description, mainImg, typeP) VALUES
('DALTON SLACKS (W/L)', '$681.00', 
'Pantaloni realizzati con un tessuto misto di lana tropicale e lino, ideali per un look raffinato e leggero.<br>Finitura pressata che conferisce un aspetto elegante e professionale.<br>Dotati di una cerniera personalizzata per garantire una maggiore durata e resistenza.<br>Bottoni in corno di bufalo, per un tocco di classe e robustezza.<br>Composizione: 76% lana, 24% lino per una combinazione perfetta di comfort e stile.<br>Si prega di notare che potrebbero esserci lievi variazioni di colore o motivo rispetto alle immagini mostrate, a causa delle caratteristiche uniche del tessuto. Inoltre, è possibile che il prodotto subisca un leggero restringimento dopo il primo lavaggio.',
'images/DALTON SLACKS/S_black.jpg', 'Slacks'),
('MAVCAT II (M)', '$1206.00',
'Borsa per casco realizzata con un mix di cotone, lino e nylon personalizzato, perfetta per chi cerca un accessorio resistente e dal design unico.<br>Design con finali danneggiati per un look vintage e autentico.<br>Nastro militare personalizzato e chiusura lampo, per un tocco di stile funzionale e pratico.<br>Composizione: 73% cotone, 17% lino, 10% nylon per garantire durabilità e comfort.<br>Si prega di notare che potrebbero esserci lievi variazioni di colore o motivo rispetto alle immagini a causa della natura del tessuto. Inoltre, il prodotto potrebbe subire un lieve restringimento dopo il lavaggio.',
'images/MAVCAT/M_khaki.jpg', 'Bags'),
('COBDA-FOLK', '$1491.00',
'Scarpe realizzate con pelle di mucca francese combinata con pelle di struzzo giapponese.<br>Fodera in pelle senza pigmenti e suola interna in sughero naturale.<br>Suola intermedia in EVA e suola esterna in gomma sostituibile.<br>Costruzione cucita a mano per una maggiore durata e stile.<br>Si prega di notare che le dimensioni e la vestibilità possono variare a seconda dello stile della scarpa e del materiale utilizzato. Inoltre, le pelli naturali possono presentare variazioni di colore o motivo rispetto alle immagini mostrate.',
'images/COBDA-FOLK/C_black.jpg', 'Shoes'),
('WILDCATTER SHIRT L/S', '$880.00',
'Camicia a maniche lunghe in tela di rayon personalizzata.<br>Finitura pressata per un aspetto elegante.<br>Bottoni in osso personalizzati e bottoni a pressione americani.<br>Dettaglio ricamato "Visvim" e cuciture fatte a mano.<br>Composizione: 100% rayon.<br>Si prega di notare che potrebbero esserci lievi variazioni di colore o motivo rispetto alle immagini mostrate. Potrebbe verificarsi un leggero restringimento dopo il primo lavaggio.',
'images/WILDCATTER/S_green.jpg', 'Shirts');

-- Inserimento colori nella tabella 'productsColors'
-- DALTON SLACKS
INSERT INTO productsColors (color, name, img) VALUES
('BLACK', 'DALTON SLACKS (W/L)', 'images/DALTON SLACKS/S_black.jpg'),
('CHARCOAL', 'DALTON SLACKS (W/L)', 'images/DALTON SLACKS/S_charcoal.jpg'),
('KHAKI', 'DALTON SLACKS (W/L)', 'images/DALTON SLACKS/S_khaki.jpg'),

-- MAVCAT II
('KHAKI', 'MAVCAT II (M)', 'images/MAVCAT/M_khaki.jpg'),
('BLACK', 'MAVCAT II (M)', 'images/MAVCAT/M_black.jpg'),
('OLIVE', 'MAVCAT II (M)', 'images/MAVCAT/M_olive.jpg'),

-- COBDA-FOLK
('BLACK', 'COBDA-FOLK', 'images/COBDA-FOLK/C_black.jpg'),
('NAVY', 'COBDA-FOLK', 'images/COBDA-FOLK/C_navy.jpg'),
('BLUE', 'COBDA-FOLK', 'images/COBDA-FOLK/C_blue.jpg'),

-- WILDCATTER SHIRT L/S
('GREEN', 'WILDCATTER SHIRT L/S', 'images/WILDCATTER/S_green.jpg'),
('BLACK', 'WILDCATTER SHIRT L/S', 'images/WILDCATTER/S_black.jpg'),
('WHITE', 'WILDCATTER SHIRT L/S', 'images/WILDCATTER/S_white.jpg');

-- Inserimento taglie per pantaloni (Slacks) nella tabella 'Slacksize'
INSERT INTO Slacksize (typeS, lengthS, inseam, waist, thigh_width, hem) VALUES
('1', '98.5', '70.2', '80', '31.3', '17.5'),
('2', '101', '72.2', '85', '32.5', '18.7'),
('3', '103.5', '74.2', '90', '33.8', '20'),
('4', '106', '76.2', '95', '35', '21.2'),
('5', '108.5', '78.2', '100', '36.3', '22.5');

-- Inserimento taglie per scarpe (Shoes) nella tabella 'ShoesSize'
INSERT INTO ShoesSize (typeS, lengthS, widthS) VALUES
('M8', '26.5', '9.5'),
('M9', '27', '9.7'),
('M10', '27.5', '10'),
('M11', '28', '10.2'),
('M12', '28.5', '10.5');

-- Inserimento taglie per borse (Bags) nella tabella 'BagSize'
INSERT INTO BagSize (typeS, height, width, depth) VALUES
('ONE SIZE', '51', '47', 'N/A');

-- Inserimento taglie per camicie (Shirts) nella tabella 'ShirtSize'
INSERT INTO ShirtSize (typeS, chest, shoulder, sleeve) VALUES
('1', '121', '47', '62.5'),
('2', '125', '48.5', '64'),
('3', '129', '50', '63.5'),
('4', '134', '52', '67'),
('5', '139', '54', '63.5');

-- Popolamento dello stock di pantaloni (Slacks)
INSERT INTO Slacks (color, name, typeS, quantity) VALUES
('BLACK', 'DALTON SLACKS (W/L)', '1', 10),
('BLACK', 'DALTON SLACKS (W/L)', '2', 15),
('BLACK', 'DALTON SLACKS (W/L)', '3', 20),
('BLACK', 'DALTON SLACKS (W/L)', '4', 25),
('BLACK', 'DALTON SLACKS (W/L)', '5', 30),
('CHARCOAL', 'DALTON SLACKS (W/L)', '1', 8),
('CHARCOAL', 'DALTON SLACKS (W/L)', '2', 12),
('CHARCOAL', 'DALTON SLACKS (W/L)', '3', 18),
('CHARCOAL', 'DALTON SLACKS (W/L)', '4', 22),
('CHARCOAL', 'DALTON SLACKS (W/L)', '5', 28),
('KHAKI', 'DALTON SLACKS (W/L)', '1', 5),
('KHAKI', 'DALTON SLACKS (W/L)', '2', 10),
('KHAKI', 'DALTON SLACKS (W/L)', '3', 15),
('KHAKI', 'DALTON SLACKS (W/L)', '4', 20),
('KHAKI', 'DALTON SLACKS (W/L)', '5', 25);

-- Popolamento dello stock di scarpe (Shoes)
INSERT INTO Shoes (color, name, typeS, quantity) VALUES
('BLACK', 'COBDA-FOLK', 'M8', 5),
('BLACK', 'COBDA-FOLK', 'M9', 10),
('BLACK', 'COBDA-FOLK', 'M10', 12),
('BLACK', 'COBDA-FOLK', 'M11', 8),
('BLACK', 'COBDA-FOLK', 'M12', 6),
('NAVY', 'COBDA-FOLK', 'M8', 5),
('NAVY', 'COBDA-FOLK', 'M9', 7),
('NAVY', 'COBDA-FOLK', 'M10', 10),
('NAVY', 'COBDA-FOLK', 'M11', 12),
('NAVY', 'COBDA-FOLK', 'M12', 8),
('BLUE', 'COBDA-FOLK', 'M8', 4),
('BLUE', 'COBDA-FOLK', 'M9', 6),
('BLUE', 'COBDA-FOLK', 'M10', 9),
('BLUE', 'COBDA-FOLK', 'M11', 11),
('BLUE', 'COBDA-FOLK', 'M12', 7);

-- Popolamento dello stock di borse (Bags)
INSERT INTO Bags (color, name, typeS, quantity) VALUES
('KHAKI', 'MAVCAT II (M)', 'ONE SIZE', 10),
('BLACK', 'MAVCAT II (M)', 'ONE SIZE', 12),
('OLIVE', 'MAVCAT II (M)', 'ONE SIZE', 15);

-- Popolamento dello stock di camicie (Shirts)
INSERT INTO Shirts (color, name, typeS, quantity) VALUES
('GREEN', 'WILDCATTER SHIRT L/S', '1', 0),
('GREEN', 'WILDCATTER SHIRT L/S', '2', 12),
('GREEN', 'WILDCATTER SHIRT L/S', '3', 15),
('GREEN', 'WILDCATTER SHIRT L/S', '4', 18),
('GREEN', 'WILDCATTER SHIRT L/S', '5', 22),
('BLACK', 'WILDCATTER SHIRT L/S', '1', 10),
('BLACK', 'WILDCATTER SHIRT L/S', '2', 14),
('BLACK', 'WILDCATTER SHIRT L/S', '3', 17),
('BLACK', 'WILDCATTER SHIRT L/S', '4', 20),
('BLACK', 'WILDCATTER SHIRT L/S', '5', 25),
('WHITE', 'WILDCATTER SHIRT L/S', '1', 6),
('WHITE', 'WILDCATTER SHIRT L/S', '2', 9),
('WHITE', 'WILDCATTER SHIRT L/S', '3', 13),
('WHITE', 'WILDCATTER SHIRT L/S', '4', 16),
('WHITE', 'WILDCATTER SHIRT L/S', '5', 18);


